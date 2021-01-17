<?php

namespace spark\controllers\Dashboard;

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use Valitron\Validator;
use spark\controllers\Dashboard\DashboardController;
use spark\drivers\Auth\Auth;
use spark\drivers\Mail\Mailer;
use spark\models\UserModel;

/**
* DashboardAccountController
*
* @package spark
*/
class DashboardAccountController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        // robots may crawl the auth pages
        view_set('allow_robots', true);

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
    }


    /**
     * Account settings page
     *
     * @return
     */
    public function accountSettings()
    {
        // but not this one!
        view_set('allow_robots', false);

        breadcrumb_add('dashboard.account.setings', __('Account Settings'));

        $app = app();

        $data = [
            'user' => $app->user->getAllFields(),
            'account__active' => 'active'
        ];

        return view('admin::account/account_settings.php', $data);
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
            'full_name' => sp_strip_tags($req->post('full_name'), true),
            'gender'    => (int) $req->post('gender')
        ];


        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'full_name' => __('Full Name'),
        ])->rule('required', ['email'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email', current_user_field('email'))
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200);

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

        /*/ if the email is a new one, mark the user as unverified
        if ($data['email'] !== current_user_field('email')) {
            $data['is_verified'] = 0;
        }*/

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

        return follow_redirect_to_uri(url_for('dashboard.account.signin'));
    }
}
