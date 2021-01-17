<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Filter\Xss;
use spark\drivers\Nav\Pagination;
use spark\models\RoleModel;
use spark\models\UserModel;

/**
* DashboardUsersController
*
* @package spark
*/
class DashboardUsersController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();



        breadcrumb_add('dashboard.users', __('Users'), url_for('dashboard.users'));


        view_set('users__active', 'active');


    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        if (!current_user_can('add_user|edit_user|delete_user')) {
            sp_not_permitted();
        }

        $app = app();

        // Model instance
        $userModel = new UserModel;
        // Role model instance
        $roleModel = new RoleModel;


        $usersTable = $userModel->getTable();
        $rolesTable = RoleModel::getTable();

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Search Query
        $search = $app->request->get('s', null);

        // Items per page
        $itemsPerPage = (int) $app->config('dashboard.items_per_page');

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$userModel->isSortAllowed($sort)) {
            $sort = 'oldest';
        }

        $sortRules = $userModel->getAllowedSorting();


        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        // Based on role ID
        $roleID = (int) $app->request->get('role_id', 0);

        // Prepare dummy data for current role
        $role = [
            'role_id'     => $roleID,
            'role_name'   => null,
            'users_count' => 0
        ];

        // If we have a valid role ID move to next step
        if ($roleID) {
            // Query to find out if such role exists or not
            $roleQuery = $roleModel->read($roleID, ['role_name']);
            // No?
            if (!$roleQuery) {
                // Then reset to zero
                $roleID = 0;
            } else {
                // Replace the dummy data
                $role['role_name'] = $roleQuery['role_name'];
                // Add role ID to the filters array
                $filters['where'][] = ["{$usersTable}.role_id", '=', $roleID];
            }
        }

        // Build count for search
        if ($search) {
            $filters['where'][] = ["{$usersTable}.full_name", 'LIKE', "%$search%"];
            $filters['where'][] = ["{$usersTable}.email", 'LIKE', "%$search%", 'OR'];
            $filters['where'][] = ["{$usersTable}.username", 'LIKE', "%$search%", 'OR'];
            $filters['where'][] = ["{$usersTable}.user_ip", 'LIKE', "%$search%", 'OR'];
        }

        // Total item count
        $totalCount = $userModel->countRows(null, $filters);

        // Add this to current role's user's count
        // Because we will query protected roles later
        $role['users_count'] = $totalCount;

        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();


        // Fields to query
        $fields[] = "{$usersTable}.*";
        $fields[] = "{$rolesTable}.role_name";

        // Query to fetch the users and their respective role names
        $sql = $userModel->select($fields)
        ->leftJoin(
            $rolesTable,
            "{$usersTable}.role_id",
            '=',
            "{$rolesTable}.role_id"
        );

        // Limit
        $sql = $sql->limit($itemsPerPage, $offset);
        // Apply Filters
        $sql = $userModel->applyModelFilters($sql, $filters);

        $stmt = $sql->execute();

        // List entries
        $entries = $stmt->fetchAll();


        $roleList = [];

        // First role list item will be all users
        $roleList[] = [
            'role_id' => 0,
            'users_count' => $userModel->countRows(),
            'role_name' => __('All'),
        ];

        // Query protected roles
        $protectedRoles = $roleModel
                         ->select(['role_id', 'role_name'])
                         ->where('is_protected', '=', 1)
                         ->execute()
                         ->fetchAll();

        // Determiner to check if currently active role is protected
        $currentRoleIsProtected = false;

        foreach ($protectedRoles as $key => $_role) {
            // Get user's count for each protected roles
            $_role['users_count'] = $roleModel->getUsersCountUnderRole($_role['role_id']);
            $roleList[] = $_role;

            // Make sure we mark $currentRoleIsProtected as true if current role is protected
            if ($roleID === $_role['role_id']) {
                $currentRoleIsProtected = true;
            }
        }

        // Based on $currentRoleIsProtected and roleID we will add the custom role's information to the role list
        if (!$currentRoleIsProtected && $roleID) {
            $roleList[] = $role;
        }

        // Template data
        $data = [
            'page_subheading' => __('Manage users'),
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'query_str'       => $queryStr,
            'role'            => $role,
            'search'          => $search,
            'role_list'       => $roleList
        ];

        if ($search) {
            $data['page_heading'] = sprintf(__('Search results for:<small> <em>%s</em>'), e($search)) . '</small><a href="?"  class="close">
    <span aria-hidden="true">×</span></a>';
        }
        return view('admin::users/index.php', $data);
    }

    public function indexPOST()
    {
        if (!current_user_can('add_user|edit_user|delete_user')) {
            sp_not_permitted();
        }

        $app = app();
        $req = $app->request;

        $action = $req->post('action');
        $postedIDs = (array) $req->post('item_multi', []);
        $userIDs = [];

        foreach ($postedIDs as $value) {
            if ((int) $value === current_user_ID()) {
                continue;
            }

            $userIDs[] = $value;
        }

        if (empty($userIDs)) {
            flash('users-warning', __('No users were selected.'));
            return redirect_to_current_route();
        }

        $userModel = new UserModel;

        switch ($action) {
            case 'delete':
                if (!current_user_can('delete_user')) {
                    sp_not_permitted();
                }

                $i = 0;

                foreach ($userIDs as $id) {
                    $userModel->deleteUser($id);
                    $i++;
                }

                flash('users-success', sprintf(__('%d user(s) were deleted successfully'), $i));
                return redirect_to_current_route();
                break;

            case 'verify':
                if (!current_user_can('edit_user')) {
                    sp_not_permitted();
                }

                $i = 0;

                foreach ($userIDs as $id) {
                    $userModel->update($id, ['is_verified' => 1]);
                    $i++;
                }

                flash('users-success', sprintf(__('%d user(s) were verified successfully'), $i));
                return redirect_to_current_route();

            case 'unverify':
                if (!current_user_can('edit_user')) {
                    sp_not_permitted();
                }

                $i = 0;

                foreach ($userIDs as $id) {
                    $userModel->update($id, ['is_verified' => 0]);
                    $i++;
                }

                flash('users-success', sprintf(__('%d user(s) were unverified successfully'), $i));
                return redirect_to_current_route();

            case 'block':
                if (!current_user_can('change_user_status')) {
                    sp_not_permitted();
                }

                $i = 0;

                foreach ($userIDs as $id) {
                    $userModel->update($id, ['is_blocked' => 1]);
                    $i++;
                }

                flash('users-success', sprintf(__('%d user(s) were blocked successfully'), $i));
                return redirect_to_current_route();

            case 'unblock':
                if (!current_user_can('change_user_status')) {
                    sp_not_permitted();
                }

                $i = 0;

                foreach ($userIDs as $id) {
                    $userModel->update($id, ['is_blocked' => 0]);
                    $i++;
                }

                flash('users-success', sprintf(__('%d user(s) were un-blocked successfully'), $i));
                return redirect_to_current_route();

            default:
                flash('users-warning', __('Invalid action. Please try again'));
                return redirect_to_current_route();
        }
    }

    /**
     * Create new entry
     *
     * @return
     */
    public function create()
    {
        if (!current_user_can('add_user')) {
            sp_not_permitted();
        }

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.users.create', __('Create User'));

        $roles = [];

        if (current_user_can('change_user_role')) {
            $roleModel = new RoleModel;
            $roles = $roleModel->readMany(['role_id', 'role_name'], 0, 50);
        }

        $data = [
            'role_list' => $roles
        ];
        return view('admin::users/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (!current_user_can('add_user')) {
            sp_not_permitted();
        }

        if (is_demo()) {
            flash('users-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.users');
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'     => $req->post('email'),
            'password'  => $req->post('password'),
            'username'  => $req->post('username'),
            'role_id'   => $req->post('role_id'),
            'full_name' => $req->post('full_name'),
            'gender'    => (int) $req->post('gender'),
            'user_ip'   => $req->post('user_ip'),
        ];

        $userModel = new UserModel;

        $roleModel = new RoleModel;

        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'username'  => __('Username'),
            'full_name' => __('Full Name'),
            'user_ip'   => __('User IP'),
        ])->rule('required', ['email', 'password'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email')
          ->rule('uniqueUsername', 'username')
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200)
          ->rule('ip', 'user_ip')
          ->rule('username', 'username');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('users-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        if (!current_user_can('change_user_role') || !$roleModel->exists($data['role_id'])) {
            $data['role_id'] = RoleModel::TYPE_USER;
        }

        $data['full_name'] = sp_strip_tags($data['full_name'], true);
        $data['is_verified'] = 1;

        if (!$id = $userModel->addUser(
            $data['email'],
            $data['password'],
            $data
        )) {
            flash('users-danger', UserModel::DB_ERROR);
            return redirect_to_current_route();
        }

        flash('users-success', __("New user was created successfully"));

        return redirect_to('dashboard.users');
    }

    /**
     * Update entry page
     *
     * @param mixed $id
     * @return
     */
    public function update($id)
    {
        if (!current_user_can('edit_user')) {
            sp_not_permitted();
        }

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.users.update', __('Update User'));

        $userModel = new UserModel;

        $user = $userModel->read($id);

        if (!$user) {
            flash('users-danger', __('No such user found.'));
            return redirect_to('dashboard.users');
        }

        if ((int) $user['user_id'] === current_user_ID()) {
            flash('users-danger', __('You can\'t edit your account from here, please use the account settings menu.'));
            return redirect_to('dashboard.users');
        }

        $roleModel = new RoleModel;

        $roleName = $roleModel->read($user['role_id'], ['role_name'])['role_name'];
        $user['role_name'] = $roleName;

        $roles = [];

        if (current_user_can('change_user_role')) {
            $roleModel = new RoleModel;
            $roles = $roleModel->readMany(['role_id', 'role_name'], 0, 50);
        }

        $data = [
            'role_list' => $roles,
            'user' => $user
        ];

        return view('admin::users/update.php', $data);
    }

    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function updatePOST($id)
    {
        if (!current_user_can('edit_user')) {
            sp_not_permitted();
        }


        if (is_demo()) {
            flash('users-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.users');
        }


        $userModel = new UserModel;

        $roleModel = new RoleModel;

        $app = app();
        $req = $app->request;

        $user = $userModel->read($id, ['user_id', 'username', 'email', 'role_id']);

        if (!$user) {
            flash('users-danger', __('No such user found.'));
            return redirect_to('dashboard.users');
        }


        if ((int) $user['user_id'] === current_user_ID()) {
            flash('users-danger', __('You can\'t edit your account from here, please use the account settings menu.'));
            return redirect_to('dashboard.users');
        }

        $data = [
            'email'     => $req->post('email'),
            'password'  => $req->post('password'),
            'username'  => $req->post('username'),
            'role_id'   => $req->post('role_id', 0),
            'full_name' => $req->post('full_name'),
            'gender'    => (int) $req->post('gender'),
            'user_ip'   => $req->post('user_ip'),
            'is_blocked'   =>  sp_int_bool($req->post('is_blocked')),
            'is_verified'   => sp_int_bool($req->post('is_verified')),
        ];

        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'username'  => __('Username'),
            'full_name' => __('Full Name'),
            'user_ip'   => __('User IP'),
        ])->rule('required', ['email', 'user_ip'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email', $user['email'])
          ->rule('uniqueUsername', 'username', $user['username'])
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200)
          ->rule('ip', 'user_ip')
          ->rule('username', 'username');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('users-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }


        if (current_user_can('change_user_role')) {
            if (!$roleModel->exists($data['role_id'])) {
                unset($data['role_id']);
            }
        } else {
            unset($data['role_id']);
        }

        if (!current_user_can('change_user_status')) {
            unset($data['is_blocked']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['full_name'] = sp_strip_tags($data['full_name'], true);

        $userModel->updateUser($id, $data);

        flash('users-success', __('User was updated successfully!'));
        return redirect_to_current_route();
    }

    /**
     * Delete entry page
     *
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        if (!current_user_can('delete_user')) {
            sp_not_permitted();
        }

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.users.delete', __('Delete User'));

        $userModel = new UserModel;

        $user = $userModel->read($id, ['full_name', 'email', 'avatar', 'last_seen', 'user_id', 'created_at']);

        if (!$user) {
            flash('users-danger', __('No such user found.'));
            return redirect_to('dashboard.users');
        }

        if ((int) $user['user_id'] === current_user_ID()) {
            flash('users-danger', __("You can't delete your own account"));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.users');
        }

        $data = [
            'user' => $user
        ];
        return view('admin::users/delete.php', $data);
    }

    /**
     * Delete entry action
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (!current_user_can('delete_user')) {
            sp_not_permitted();
        }


        if (is_demo()) {
            flash('users-info', $GLOBALS['_SPARK_I18N']['demo_mode']);

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.users');
        }


        $userModel = new UserModel;

        $user = $userModel->read($id, ['user_id']);

        if (!$user) {
            flash('users-danger', __('No such user found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.users');
        }

        if ((int) $user['user_id'] === current_user_ID()) {
            flash('users-danger', __("You can't delete your own account"));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.users');
        }


        $userModel->delete($id);

        flash('users-success', __('User was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.users');
    }
}
