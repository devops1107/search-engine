<?php

namespace spark\controllers\Site;

use spark\controllers\Controller;
use spark\drivers\Auth\ReservedUsernames;
use spark\models\UserModel;

/**
* AjaxController
*/
class AjaxController extends Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

       /**
     * Checks if a email exists or not
     *
     * Http status 200 if exists or 404
     *
     * @return
     */
    public function emailCheck()
    {
        $app = app();
        $email = $app->request->get('email', '');
        $except = trim($app->request->get('except'));
        $exceptUser = trim($app->request->get('except_user'));
        $userModel = new UserModel;

        $filters['where'][] = ['email', '=', $email];

        if ($except) {
            $filters['where'][] = ['email', '!=', $except];
        } elseif ($exceptUser) {
            if (is_logged()) {
                $filters['where'][] = ['email', '!=', current_user_field('email')];
            }
        }

        $count = (bool) $userModel->countRows(null, $filters);

        if ($count) {
            return response_status(200);
        }

        return response_status(404);
    }

    /**
     * Checks if a username exists or not
     *
     * Http status 200 if exists or 404
     *
     * @return
     */
    public function usernameCheck()
    {
        $app = app();
        $username = $app->request->get('username', '');
        $except = trim($app->request->get('except'));
        $exceptUser = trim($app->request->get('except_user'));
        $userModel = new UserModel;

        // Don't allow reserved keywords as username
        if (ReservedUsernames::isReserved($username)) {
            return response_status(200);
        }


        $filters['where'][] = ['username', '=', $username];

        if ($except) {
            $filters['where'][] = ['username', '!=', $except];
        } elseif ($exceptUser) {
            if (is_logged()) {
                $filters['where'][] = ['username', '!=', current_user_field('username')];
            }
        }

        $count = (bool) $userModel->countRows(null, $filters);

        if ($count) {
            return response_status(200);
        }

        return response_status(404);
    }
}
