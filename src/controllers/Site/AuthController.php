<?php

namespace spark\controllers\Site;

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Auth\Auth;
use spark\drivers\Auth\ReservedUsernames;
use spark\drivers\Http\Http;
use spark\drivers\Mail\Mailer;
use spark\helpers\UrlSlug;
use spark\models\AttemptModel;
use spark\models\TokenModel;
use spark\models\UserModel;

/**
* AuthController
*
* @package spark
*/
class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();



        view_set('body_class', 'auth');

        $app = app();

        $redirectTo = $app->request->get('redirect_to', null);

        if ($redirectTo) {
            $app->session->set('redirect_to', urldecode($redirectTo));
        }

        $query = request_build_query(['via-ajax'], null);

        if ($query) {
            $query = "?{$query}";
        }

        view_set('query_string', $query);


        $logoURL = sp_logo_uri();

        if (registry_read('darkmode')) {
            $logoURL = ensure_abs_url(get_option('dark_logo'));
        }


        view_set('auth_logo_url', $logoURL);
    }

    /**
     * Sign In Page
     *
     * @return
     */
    public function signIn()
    {
        if (is_logged()) {
            $redirectURI = get_redirect_to_uri(url_for('site.home'));

            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectURI]);
            }

            return redirect($redirectURI);
        }

        $app = app();

        $app->session->set('social_connect_referer', 'signin');


        $auth = new Auth;
        $blockedTime = $auth->getSignInAttemptBlockedTime();

        $data = [
            'hide_header' => true,
            'hide_footer' => true,
            'title' => __('sign-in', _T),
            'form_countdown' => $blockedTime,
        ];

        return ajax_view('auth/sign_in.php', $data);
    }

    /**
     * Handles sign in proccess
     *
     * @return
     */
    public function signInPOST()
    {
        $redirectURI = get_redirect_to_uri(url_for('site.home'));

        $signInURI = url_for('auth.signin');

        if (is_logged()) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' =>  $redirectURI]);
            }

            return redirect($redirectURI);
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'       => $req->post('email'),
            'password'    => $req->post('password'),
            'remember_me' => (int) $req->post('remember_me'),
        ];

        $auth = new Auth;

        $blockedTime = $auth->getSignInAttemptBlockedTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);
            $blockedMsg = __('login-limit', _T, ['time' => $timeLeftStr]);

            if (is_ajax()) {
                return ajax_form_json(['message' => $blockedMsg]);
            }

            flash(
                'account-danger',
                $blockedMsg
            );

            sp_store_post($data);
            return redirect($signInURI);
        }

        if (!sp_verify_recaptcha('auth.signin')) {
            $errors = __('invalid-captcha', _T);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect($signInURI);
        }

        $v = new Validator($data);
        $v->labels([
            'email' => __("email", _T),
            'password' => __("password", _T),
        ])->rule('required', ['email', 'password'])
          ->rule('email', 'email')
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'));

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        try {
            $userID = $auth->attempt($data['email'], $data['password']);
        } catch (\Exception $e) {
            $attemptModel = new AttemptModel;
            $attemptModel->logSignInAttempt();

            if (is_ajax()) {
                return ajax_form_json(['message' => $e->getMessage()]);
            }

            flash('account-danger', $e->getMessage());

            sp_store_post($data);
            return redirect($signInURI);
        }

        // Everything all right?
        $auth->buildSession($userID, $data['remember_me']);

        // Reset session based ones
        session_set('redirect_to', null);

        if (is_ajax()) {
            return ajax_form_json([
                'type' => 'success',
                'message' => __('login-success', _T),
                'redirect' => $redirectURI,
            ]);
        }

        return redirect($redirectURI);
    }

    /**
     * Registration page
     *
     * @return
     */
    public function register()
    {
        if (is_logged()) {
            $redirectURI = get_redirect_to_uri(url_for('site.home'));

            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectURI]);
            }

            return redirect($redirectURI);
        }


        if (!config('site.registration_enabled', true)) {
            $errors =  __('registration-disabled', _T);
            flash('account-danger', $errors);

            if (is_ajax()) {
                return ajax_json(['redirect' => url_for('auth.signin')]);
            }

            return redirect(url_for('auth.signin'));
        }

        $app = app();

        $app->session->set('social_connect_referer', 'register');


        $data = [
            'title'      => __('register', _T),
            'hide_header' => true,
            'hide_footer' => true,
            'body_class' => 'auth register',
        ];

        return ajax_view('auth/register.php', $data);
    }

    /**
     * Handles registration
     *
     * @return
     */
    public function registerPOST()
    {
        $redirectURI = get_redirect_to_uri(url_for('site.home'));
        $registerURI = url_for('auth.register');

        if (is_logged()) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' =>  $redirectURI]);
            }

            return redirect($redirectURI);
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'       => $req->post('email'),
            'password'    => $req->post('password'),
            'gender'      => (int) $req->post('gender'),
            'full_name'   => sp_strip_tags($req->post('full_name'), true),
        ];

        if (!config('site.registration_enabled', true)) {
            $errors =  __('registration-disabled', _T);

            flash('account-danger', $errors);

            if (is_ajax()) {
                return ajax_json(['redirect' => url_for('auth.signin')]);
            }

            return redirect(url_for('auth.signin'));
        }

        if (!sp_verify_recaptcha('auth.register')) {
            $errors = __('invalid-captcha', _T);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect($registerURI);
        }

        $v = new Validator($data);

        $v->labels([
            'email'     => __('email', _T),
            'password'  => __('password', _T),
            'full_name' => __('full-name', _T),
        ])->rule('required', ['email', 'password', 'full_name'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email')
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect($registerURI);
        }

        $userModel = new UserModel;
        $auth = new Auth;
        $filters = [];
        $filters['where'][] = ['user_ip', '=', $req->getIp()];
        $accountsUnderThisIP = $userModel->countRows(null, $filters);
        $maxAccountsPerIP = (int) config('auth.max_account_per_ip', 0);

        if ($maxAccountsPerIP && $accountsUnderThisIP >= $maxAccountsPerIP) {
            $errors = __('max-account-per-ip-reached', _T, ['num' => $maxAccountsPerIP]);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            sp_store_post($data);
            flash('account-danger', $errors);
            return redirect($registerURI);
        }

        $meta = [
        ];


        $urlSlug = new UrlSlug(['delimiter' => '_', 'limit' => 15]);
        $username = $urlSlug->generate($data['full_name']);

        // Don't allow reserved keywords as username
        // Instead add few random characters at the end
        if (ReservedUsernames::isReserved($username)) {
            $username .= str_random(5, 'abcdefghijklmnopqrstuvwxyz0123456789');
        }


        $username = ensure_unique_value($userModel, 'username', $username, false, '');


        $data['username'] = $username;

        $userID = $userModel->addUser($data['email'], $data['password'], $data, $meta);

        if (!$userID) {
            $errors = __('critical-db-error', _T);
            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            sp_store_post($data);
            flash('account-danger', $errors);
            return redirect($registerURI);
        }

        $auth->buildSession($userID, true);

        try {
            $auth->sendActivationEmail($data['email'], $userID, $data);
        } catch (\Exception $e) {
            logger()->critical($e);
        }

        if (is_ajax()) {
            return ajax_form_json(['redirect' => $redirectURI]);
        }

        return redirect($redirectURI);
    }


    /**
     * Email Activation
     *
     * @return
     */
    public function emailActivation()
    {
        $activationURI = url_for('auth.activation');
        $redirectURI = url_for('auth.signin') . "?redirect_to={$activationURI}";

        $redirectTo = get_redirect_to_uri(url_for('site.home'));

        if (!is_logged()) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectURI]);
            }

            return redirect($redirectURI);
        }

        // Already verified
        if (current_user_field('is_verified')) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectTo]);
            }

            return redirect($redirectTo);
        }

        $app = app();
        $auth = new Auth;
        $blockedTime = $auth->getEmailTokenRequestWaitingTime();

        $data = [
            'title'   => __('email-activation', _T),
            'hide_header' => true,
            'hide_footer' => true,
            'user' => $app->user->getAllFields(),
            'form_countdown' => $blockedTime,
        ];

        return ajax_view('auth/email_activation.php', $data);
    }

    /**
     * Handles email activation token request
     *
     * @return
     */
    public function emailActivationPOST()
    {
        $activationURI = url_for('auth.activation');
        $redirectURI = url_for('auth.signin') . "?redirect_to={$activationURI}";

        $redirectTo = get_redirect_to_uri(url_for('site.home'));

        if (!is_logged()) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectURI]);
            }

            return redirect($redirectURI);
        }

        // Already verified
        if (current_user_field('is_verified')) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectTo]);
            }

            return redirect($redirectTo);
        }

        $user = get_logged_user();
        $auth = new Auth;
        $blockedTime = $auth->getEmailTokenRequestWaitingTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);
            $errors = __('email-activation-limit', _T, ['time' => $timeLeftStr]);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            return redirect($activationURI);
        }

        $data = $user->getAllFields();

        try {
            $auth->sendActivationEmail($data['email'], $data['user_id'], $data);
        } catch (\Exception $e) {
            $errors = __('mailer-error') . $e->getMessage();

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            return redirect($activationURI);
        }

        $msg = __('email-activation-mail-sent', _T, ['email' => $data['email']]);


        if (is_ajax()) {
            return ajax_form_json(['message' => $msg, 'type' => 'success']);
        }

        flash('account-danger', $msg);
        return redirect($activationURI);
    }

    /**
     * Email verification page
     *
     * @param  string $token
     * @return
     */
    public function emailVerifyAction($token)
    {
        $app = app();
        $app->response->headers->set('X-Robots-Tag', 'nofollow, noindex');

        $redirectTo = get_redirect_to_uri(url_for('site.home'));

        if (is_logged() && current_user_field('is_verified')) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => $redirectTo]);
            }

            return redirect($redirectTo);
        }

        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_EMAIL);

        $invalid = true;


        if (!$dbToken || $dbToken['token_expires'] < time()) {
            $invalid = true;
        } else {
            $invalid = false;
            $userModel = new UserModel;
            $userModel->updateUser($dbToken['user_id'], ['is_verified' => 1]);
            $tokenModel->deleteTokens($tokenModel::TYPE_EMAIL, $dbToken['user_id']);
        }


        $data = [
            'title'  => __('email-activation', _T),
            'hide_header' => true,
            'hide_footer' => true,
            'invalid' => $invalid,
        ];

        return ajax_view('auth/email_verify_action.php', $data);
    }

    /**
     * Forgot password page
     *
     * @return
     */
    public function forgotPass()
    {
        $app = app();
        $auth = new Auth;
        $blockedTime = $auth->getForgotPassTokenRequestWaitingTime();

        $data = [
            'title' => __('forgot-password', _T),
            'form_countdown' => $blockedTime,
            'hide_header' => true,
            'hide_footer' => true,
        ];

        return ajax_view('auth/forgot_pass.php', $data);
    }

    /**
     * Processes Forgot Password Request
     *
     * @return
     */
    public function forgotPassPOST()
    {
        if (is_demo()) {
            if (is_ajax()) {
                return ajax_form_json(['message' => $GLOBALS['_SPARK_I18N']['demo_mode']]);
            }

            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('auth.forgotpass');
        }

        $app = app();
        $req = $app->request;

        $email = trim($req->post('email'));


        if (!sp_verify_recaptcha('auth.forgotpass')) {
            $errors = __('invalid-captcha', _T);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post(['email' => $email]);
            return redirect_to('auth.forgotpass');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors = __('invalid-email', _T);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post(['email' => $email]);
            return redirect_to('auth.forgotpass');
        }

        $auth = new Auth;

        $blockedTime = $auth->getForgotPassTokenRequestWaitingTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);

            $errors = __('forgotpass-limit', _T, ['time' => $timeLeftStr]);


            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            sp_store_post(['email' => $email]);
            return redirect_to('auth.forgotpass');
        }

        if (!is_logged()) {
            $userModel = new UserModel;
            $user = $userModel->fetchRow('email', $email, ['full_name', 'user_id', 'email']);
        } else {
            $user = get_logged_user()->getAllFields();
        }


        $msg = __('forgotpass-success', _T, ['email' => $email]);

        if (!$user) {
            if (is_ajax()) {
                return ajax_form_json(['message' => $msg, 'type' => 'success']);
            }

            flash('account-success', $msg);
            return redirect_to('auth.forgotpass');
        }

        $auth = new Auth;

        try {
            $auth->sendForgotPassEmail($user['email'], $user['user_id'], $user);
        } catch (\Exception $e) {
            $errors = __('mailer-error', _T) . $e->getMessage();

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            return redirect_to('auth.forgotpass');
        }


        if (is_ajax()) {
            return ajax_form_json(['message' => $msg, 'type' => 'success']);
        }

        flash('account-success', $msg);
        return redirect_to('auth.forgotpass');
    }

    /**
     * Reset password page
     *
     * @param  string $token
     * @return
     */
    public function resetPass($token)
    {
        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_FORGOT_PASS);
        $invalid = false;

        if (!$dbToken || $dbToken['token_expires'] < time()) {
            $invalid = true;
        } else {
            $userModel = new UserModel;
            $user = $userModel->read($dbToken['user_id'], ['email']);
            if (!$user) {
                $invalid = true;
            }
        }

        $app = app();

        $app->response->headers->set('X-Robots-Tag', 'nofollow, noindex');


        $data = [
            'title'   => __('reset-password', _T),
            'token'   => $token,
            'invalid' => $invalid,
            'hide_header' => true,
            'hide_footer' => true,
        ];

        return ajax_view('auth/reset_pass.php', $data);
    }

    /**
     * Reset password request processing
     *
     * @param  string $token
     * @return
     */
    public function resetPassPOST($token)
    {
        $redirectURI = url_for('auth.resetpass', ['token' => $token]);

        $app = app();
        $app->response->headers->set('X-Robots-Tag', 'nofollow, noindex');

        if (is_demo()) {
            if (is_ajax()) {
                return ajax_form_json(['message' => $GLOBALS['_SPARK_I18N']['demo_mode']]);
            }

            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect($redirectURI);
        }


        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_FORGOT_PASS);


        if (!$dbToken || $dbToken['token_expires'] < time()) {
            if (is_ajax()) {
                return ajax_form_json(['redirect' => url_for('auth.resetpass')]);
            }

            return redirect($redirectURI);
        }

        $req = $app->request;

        $data = [
            'password'         => $req->post('password'),
            'confirm_password' => $req->post('confirm_password'),
        ];


        $v = new Validator($data);

        $v->labels([
            'password'  => __('new-password', _T),
            'confirm_password'  => __('confirm-password', _T),
        ])->rule('required', ['password', 'confirm_password'])
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMin', 'confirm_password', (int) config('internal.password_minlength'))
          ->rule('equals', 'password', 'confirm_password')
          ->message(__("password-not-match", _T));

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            return redirect($redirectURI);
        }

        $userModel = new UserModel;
        $user = $userModel->read($dbToken['user_id'], ['email']);

        if (!$user) {
            $tokenModel->deleteTokens($tokenModel::TYPE_FORGOT_PASS, $dbToken['user_id']);

            $errors = __('no-such-user', _T);

            if (is_ajax()) {
                return ajax_form_json(['message' => $errors]);
            }

            flash('account-danger', $errors);
            return redirect($redirectURI);
        }

        // finally some peace of mind

        $userModel->updateUser($dbToken['user_id'], ['password' => $data['password']]);

        $tokenModel = new TokenModel;
        $tokenModel->deleteTokens($tokenModel::TYPE_FORGOT_PASS, $dbToken['user_id']);

        $auth = new Auth;
        $auth->logOut();

        $msg = __('password-updated', _T);

        // Make an exception, because there will be redirect
        flash('account-success', $msg);

        if (is_ajax()) {
            return ajax_form_json(['message' => $msg, 'type' => 'success', 'redirect' => url_for('auth.signin')]);
        }

        return redirect_to('auth.signin');
    }

    /**
     * Account settings page
     *
     * @return
     */
    public function accountSettings()
    {

        breadcrumb_add('dashboard.account.setings', __('Account Settings'));

        $app = app();

        $data = [
            'user' => $app->user->getAllFields(),
            'account__active' => 'active'
        ];

        return view('auth/account_settings.php', $data);
    }

    /**
     * Processes user account settings
     *
     * @return
     */
    public function accountSettingsPOST()
    {
        if (is_demo()) {
            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to_current_route();
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'     => $req->post('email'),
            'password'  => $req->post('password'),
            'old_password'  => $req->post('old_password'),
            'username'  => $req->post('username'),
            'full_name' => sp_strip_tags($req->post('full_name'), true),
            'gender'    => (int) $req->post('gender')
        ];


        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'username'  => __('Username'),
            'full_name' => __('Full Name'),
        ])->rule('required', ['email'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email', current_user_field('email'))
          ->rule('uniqueUsername', 'username', current_user_field('username'))
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200)
          ->rule('username', 'username');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        // Password changing logic
        if (!empty($data['password'])) {
            // You need the old password
            if (empty($data['old_password'])) {
                flash('account-danger', __("You must provide your current password in order to change your password"));
                sp_store_post($data);
                return redirect_to_current_route();
            }

            // And that should be correct
            if (!password_verify($data['old_password'], current_user_field('password'))) {
                flash('account-danger', __("Your current password is incorrect"));
                sp_store_post($data);
                return redirect_to_current_route();
            }
        } else {
            unset($data['password']);
        }

        $forceGravatar = (bool) $req->post('force_gravatar');
        $currentAvatar = current_user_field('avatar');

        // Let's handle avatar upload
        if (!empty($_FILES['avatar']['name']) && !$forceGravatar) {
            $dir = sitepath('avatars');
            $storage = new FileSystem($dir, true);
            $file = new File('avatar', $storage);
            $fileName = md5(current_user_ID()) . uniqid();

            $file->setName($fileName);

            $file->addValidations([
                new Extension(['jpg', 'jpeg', 'png', 'gif']),
                new Size(config('internal.avatar_maxsize'))
            ]);

            try {
                $file->upload();
                $data['avatar'] = trailingslashit(SITE_DIR) . 'avatars/' . $fileName . '.' . $file->getExtension();

                if (is_file($currentAvatar)) {
                    @unlink($currentAvatar);
                }
            } catch (\Exception $e) {
                $errors = join($file->getErrors(), "<br>");
                flash('account-warning', sprintf(__("Failed to change avatar. Reason: %s"), $errors));
            }
        }

        if ($forceGravatar) {
            if (is_file($currentAvatar)) {
                @unlink($currentAvatar);
            }

            $data['avatar'] = null;
        }


        unset($data['old_password']);

        // if the email is a new one, mark the user as unverified
        if ($data['email'] !== current_user_field('email')) {
            $data['is_verified'] = 0;
        }

        $userModel = new UserModel;
        $userModel->updateUser(current_user_ID(), $data);

        flash('account-success', __("Your account was updated successfully"));
        return redirect_to_current_route();
    }

    /**
     * Handles log out process
     *
     * @return
     */
    public function logOut()
    {
        $auth = new Auth;
        $auth->logOut();

        $redirectURI = get_redirect_to_uri(url_for('auth.signin'));

        if (is_ajax()) {
            return ajax_form_json(['redirect' => $redirectURI]);
        }

        return redirect($redirectURI);
    }
}
