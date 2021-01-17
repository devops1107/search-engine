<?php

namespace spark\drivers\Auth;

use spark\drivers\Mail\Mailer;
use spark\models\AttemptModel;
use spark\models\TokenModel;
use spark\models\UserModel;

/**
* Authentication Driver
*
* @package spark
*/
class Auth
{
    /**
     * Session Identifier Key for User
     */
    const SESSION_KEY = '__spark_user_session';

    /**
     * Auto login cookie name
     */
    const AUTH_COOKIE_KEY = '__spark_auth_token';

    /**
     * Attempt to login to an account
     *
     * Returns the user ID on success FALSE otherwise
     *
     * @param  string $emailOrUsername
     * @param  string $password
     * @return boolean|integer
     */
    public function attempt($emailOrUsername, $password)
    {
        $userModel = new UserModel;
        $fields = ['user_id', 'password', 'is_blocked'];
        $user = $userModel->select($fields)
                          ->where('email', '=', $emailOrUsername)
                          ->orWhere('username', '=', $emailOrUsername)
                          ->execute()
                          ->fetch();

        // No user found? Fine. we'll still run the checks to prevent timing attacks
        // Y u no die timing attack? -_-
        if (!$user) {
            password_verify($password, sha1(time() . uniqid() . 'timing attack can suck ass :3'));
            throw new \LogicException(__('invalid-email', _T));
            return false;
        }

        if ($user['is_blocked']) {
            password_verify($password, sha1(time() . uniqid() . 'timing attack can suck ass :3'));
            throw new \LogicException(__('account-blocked', _T));
            return false;
        }

        // So there's an actual user with that identifier
        // now lets check if it belongs to you or not
        if (password_verify($password, $user['password'])) {
            // Congrats, successful attempt!
            return $user['user_id'];
        } else {
            throw new \LogicException(__('incorrect-password', _T));
        }

        // Oh, fuck off -_-
        return false;
    }

    /**
     * Builds a user session
     *
     * @param  integer  $userID
     * @param  boolean $remember
     *
     * @return boolean
     */
    public function buildSession($userID, $remember = false)
    {
        // Set up user session
        session_set(static::SESSION_KEY, $userID);
        // Early return here if we don't need auto login
        if (!$remember) {
            return true;
        }

        $tokenModel = new TokenModel;
        $token = $tokenModel->createCookieToken($userID);

        if (!$token) {
            return false;
        }

        set_cookie(static::AUTH_COOKIE_KEY, $token['token_value'], $token['token_expires']);

        return true;
    }

    /**
     * Attempts to auto login the user via the auth cookie
     *
     * @return integer|boolean
     */
    public function attemptAutoLogin()
    {
        $clientToken = get_cookie(static::AUTH_COOKIE_KEY);
        // Early return is good :|
        if (!is_string($clientToken)) {
            return false;
        }

        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($clientToken, TokenModel::TYPE_COOKIE);

        // Does it exists on database?
        if (!$dbToken) {
            // Lol
            delete_cookie(static::AUTH_COOKIE_KEY);
            return false;
        }

        if ($dbToken['token_expires'] < time()) {
            // Sorry the token expired
            delete_cookie(static::AUTH_COOKIE_KEY);

            // delete from database as well
            $tokenModel->delete($token['token_id']);
            return false;
        }

        // Build the session
        $this->buildSession($dbToken['user_id'], false);
        return $dbToken['user_id'];
    }

    /**
     * Returns the seconds to wait before current user can try to sign in again
     *
     * @return integer
     */
    public function getSignInAttemptBlockedTime()
    {
        $attemptModel = new AttemptModel;
        $time = time();
        $timespan = (int) config('auth.login_block_timespan', 600);
        $interval = $time - $timespan;
        $req = app()->request;

        $attemptCount = $attemptModel->getSignInAttemptCount($interval, $req->getIp());

        $maxAllowedAttempts = (int) config('auth.max_failed_login_attempt', 3);

        if (!$maxAllowedAttempts || $attemptCount <= $maxAllowedAttempts) {
            return 0;
        }

        $lastAttempt = $attemptModel->getLastAttempt(
            AttemptModel::TYPE_SIGN_IN,
            $interval,
            $req->getIp(),
            null,
            ['attempt_time']
        );

        if (empty($lastAttempt['attempt_time'])) {
            return 0;
        }

        $timeLeft = $time - $lastAttempt['attempt_time'];
        return  (int) $timespan - $timeLeft;
    }

    /**
     * Returns the seconds to wait before current user can request a new email verification token again
     *
     * @return integer
     */
    public function getEmailTokenRequestWaitingTime()
    {
        $attemptModel = new AttemptModel;
        $time = time();
        $timespan = (int) config('auth.email_verify_wait_timespan', 600);

        if (!$timespan) {
            return 0;
        }

        $interval = $time - $timespan;

        $lastAttempt = $attemptModel->getLastAttempt(
            AttemptModel::TYPE_EMAIL_VERIFY,
            $interval,
            null,
            current_user_ID(),
            ['attempt_time']
        );

        if (empty($lastAttempt['attempt_time'])) {
            return 0;
        }

        $timeLeft = $time - $lastAttempt['attempt_time'];
        return  (int) $timespan - $timeLeft;
    }

    /**
     * Returns the seconds to wait before current user can request a new forgot password token again
     *
     * @return integer
     */
    public function getForgotPassTokenRequestWaitingTime()
    {
        $attemptModel = new AttemptModel;
        $time = time();
        $timespan = (int) config('auth.forgotpass_wait_timespan', 600);

        $interval = $time - $timespan;

        if (!$timespan) {
            return 0;
        }

        $lastAttempt = $attemptModel->getLastAttempt(
            AttemptModel::TYPE_FORGOT_PASS,
            $interval,
            null,
            null,
            ['attempt_time']
        );

        if (empty($lastAttempt['attempt_time'])) {
            return 0;
        }

        $timeLeft = $time - $lastAttempt['attempt_time'];
        return  (int) $timespan - $timeLeft;
    }

    /**
     * Logs out user
     *
     * @return boolean
     */
    public function logOut()
    {
        $clientToken = get_cookie(static::AUTH_COOKIE_KEY);

        // If the auto login cookie exists, make sure we delete it from DB as well
        if ($clientToken) {
            $tokenModel = new TokenModel;
            $tokenModel->deleteRow('token_value', $clientToken);
        }

        delete_cookie(self::AUTH_COOKIE_KEY);
        // Delete the existing session
        session_delete(static::SESSION_KEY);
        // Regenarate the session id
        session_identifier(true);
        // oh :|
        return true;
    }

    /**
     * Send activation email to a user
     *
     * @param  string $email
     * @param  integer $userID
     * @param  array  $data
     *
     * @return boolean
     */
    public function sendActivationEmail($email, $userID, array $data)
    {
        $tokenModel = new TokenModel;
        $attemptModel = new AttemptModel;

        // delete existing tokens
        $tokenModel->deleteTokens($tokenModel::TYPE_EMAIL, $userID);

        $token = $tokenModel->createEmailToken($userID);
        $data['action_url'] = url_for('auth.verify_action', ['token' => $token['token_value']]);
        $mailer = new Mailer;

        $status = $mailer->send(
            $mailer::TYPE_EMAIL,
            $email,
            sprintf(__('Please verify your account | %s'), get_option('site_name')),
            $data
        );

        if ($status) {
            $attemptModel->logEmailVerificationAttempt(time(), null, $userID);
        }

        return $status;
    }

    /**
     * Send password reset mail to a user
     *
     * @param  string $email
     * @param  integer $userID
     * @param  array  $data
     *
     * @return boolean
     */
    public function sendForgotPassEmail($email, $userID, array $data)
    {
        $tokenModel = new TokenModel;
        $attemptModel = new AttemptModel;

        // delete existing tokens
        $tokenModel->deleteTokens($tokenModel::TYPE_FORGOT_PASS, $userID);

        $token = $tokenModel->createForgotPassToken($userID);
        $data['action_url'] = url_for('auth.resetpass', ['token' => $token['token_value']]);
        $mailer = new Mailer;

        $status = $mailer->send(
            $mailer::TYPE_FORGOT_PASS,
            $email,
            sprintf(__('Reset Password | %s'), get_option('site_name')),
            $data
        );

        if ($status) {
            $attemptModel->logForgotPassAttempt(time(), app()->request->getIp(), $userID);
        }

        return $status;
    }
}
