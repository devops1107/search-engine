<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\RoleModel;
use spark\models\UserModel;

/**
* Controller for Roles CRUD
*
* @package spark
*/
class DashboardRolesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();




        breadcrumb_add('dashboard.roles', __('Roles'), url_for('dashboard.roles'));

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        view_set('roles__active', 'active');
        view_set('auth__active', 'active');


    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        if (!current_user_can('add_role|edit_role|delete_role')) {
            sp_not_permitted();
        }

        $app = app();

        // Model instance
        $roleModel = new RoleModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) $app->config('dashboard.items_per_page');

        // Total item count
        $totalCount = $roleModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$roleModel->isSortAllowed($sort)) {
            $sort = 'protected';
        }

        $sortRules = $roleModel->getAllowedSorting();

        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // List entries
        $entries = $roleModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

        // Add permissions
        foreach ($entries as $key => $entry) {
            $entries[$key]['permissions'] = array_keys($roleModel->getRolePerms($entry['role_id']));
        }

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'query_str'       => $queryStr
        ];
        return view('admin::roles/index.php', $data);
    }

    /**
     * Create new entry
     *
     * @return
     */
    public function create()
    {
        if (!current_user_can('add_role')) {
            sp_not_permitted();
        }

        // Set breadcrumb trails
        breadcrumb_add('dashboard.roles.create', __('Create Role'));

        $roleModel = new RoleModel;
        $permissions = $roleModel->getAllPermissions();

        $data = [
            'permissions' => $permissions,
        ];
        return view('admin::roles/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (!current_user_can('add_role')) {
            sp_not_permitted();
        }


        if (is_demo()) {
            flash('roles-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.roles');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'role_name' => $req->post('role_name'),
            'permissions' => (array) $req->post('permissions', []),
        ];

        $v = new Validator($data);

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v->labels([
            'role_name' => __('Role Name'),
            'permissions' => __('Permissions')
        ])
        ->rule('required', ['role_name'])
        ->rule('lengthBetween', 'role_name', 3, 200)
        ->rule('alphaNum', 'role_name');

        if (!$v->validate()) {
            sp_store_post($data);
            $errors = sp_valitron_errors($v->errors());
            flash('roles-danger', $errors);
            return redirect_to_current_route();
        }

        $roleModel = new RoleModel;

        // Fetch all permissions first
        $allPermissions = $roleModel->getAllPermissions();
        // Unique values only
        $perms = array_unique($data['permissions']);

        // Make sure the provided permissions actually exists
        foreach ($perms as $_permID) {
            // Need to strip out invalid values from user input cliche * ding *
            if (!isset($allPermissions[$_permID])) {
                $perms = array_remove_value($perms, $_permID);
            }
        }

        $roleID = $roleModel->create(['role_name' => strip_tags($data['role_name'])]);

        if (!$roleID) {
            flash('roles-danger', $roleModel::DB_ERROR);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        // Insert the new permissions
        foreach ($perms as $permID) {
            $roleModel->insertPerm($roleID, $permID);
        }

        flash('roles-success', __('Role was updated successfully.'));

        return redirect_to('dashboard.roles');
    }

    /**
     * Update entry page
     *
     * @param mixed $id
     * @return
     */
    public function update($id)
    {
        if (!current_user_can('edit_role')) {
            sp_not_permitted();
        }

        // Set breadcrumb trails
        breadcrumb_add('dashboard.roles.update', __('Update Role'));

        $roleModel = new RoleModel;

        $role = $roleModel->read($id, ['role_id', 'role_name', 'is_protected']);

        if (!$role) {
            flash('roles-danger', __('No such role found'));
            return redirect_to('dashboard.roles');
        }

        $permissions = $roleModel->getAllPermissions();

        $rolePerms = $roleModel->getRolePerms($id);

        $finalPerms = [];

        foreach ($permissions as $permID => $permLabel) {
            $state = false;

            if (isset($rolePerms[$permLabel])) {
                $state = true;
            }

            $finalPerms[$permID] = ['label' => $permLabel, 'state' => $state];
        }

        $role['permissions'] = $finalPerms;


        $data = [
            'role' => $role,
        ];
        return view('admin::roles/update.php', $data);
    }

    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function updatePOST($id)
    {
        if (!current_user_can('edit_role')) {
            sp_not_permitted();
        }


        if (is_demo()) {
            flash('roles-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.roles');
        }


        $app = app();
        $req = $app->request;

        $roleModel = new RoleModel;
        $role = $roleModel->read($id, ['role_name']);

        // Naha, you don't!
        if (!$role) {
            flash('roles-danger', __('No such role found'));
            return redirect_to('dashboard.roles');
        }

        $v = new Validator($req->post());

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v->labels([
            'role_name' => __('Role Name'),
            'permissions' => __('Permissions')
        ])
        ->rule('required', ['role_name'])
        ->rule('lengthBetween', 'role_name', 3, 200)
        ->rule('alphaNum', 'role_name');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('roles-danger', $errors);
            return redirect_to_current_route();
        }

        // Fetch all permissions first
        $allPermissions = $roleModel->getAllPermissions();
        // Unique values only
        $perms = array_unique($req->post('permissions', []));

        // Make sure the provided permissions actually exists
        foreach ($perms as $_permID) {
            // Need to strip out invalid values from user input cliche * ding *
            if (!isset($allPermissions[$_permID])) {
                $perms = array_remove_value($perms, $_permID);
            }
        }

        // Get permissions for currently working role
        $currentRolePerms = array_values($roleModel->getRolePerms($id));

        // If the values are not same then proceed
        if (!array_identical_values($currentRolePerms, $perms)) {
            // Delete all the existing perms for this role ID first
            // seriously what kinda approch is this Miraz?
            // u mad?
            // well, no bitch, i'm depressed af and this this the quickest way i can think of right now
            $roleModel->deleteRolePerms($id);

            // Insert the new permissions
            foreach ($perms as $permID) {
                $roleModel->insertPerm($id, $permID);
            }
        }

        $roleName = strip_tags($req->post('role_name'));
        $roleModel->update($id, ['role_name' => $roleName]);

        flash('roles-success', __('Role was updated successfully.'));

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
        if (!current_user_can('delete_role')) {
            sp_not_permitted();
        }

        // Set breadcrumb trails
        breadcrumb_add('dashboard.roles.delete', __('Delete Role'));

        $roleModel = new RoleModel;
        $role = $roleModel->read($id, ['is_protected', 'role_name']);

        if (!$role) {
            flash('roles-danger', __('No such role found'));
            return redirect_to('dashboard.roles');
        }

        if ($role['is_protected']) {
            flash('roles-danger', __('You can\'t delete protected roles'));
            return redirect_to('dashboard.roles');
        }

        $data = [
            'role' => $role,
        ];
        return view('admin::roles/delete.php', $data);
    }

    /**
     * Delete entry action
     *
     * Since we use a delete confirmation window most of the time, we'd have a fallback for non ajax request as well.
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (!current_user_can('delete_role')) {
            sp_not_permitted();
        }


        if (is_demo()) {
            flash('roles-info', $GLOBALS['_SPARK_I18N']['demo_mode']);

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.roles');
        }


        $roleModel = new RoleModel;
        $role = $roleModel->read($id, ['is_protected']);

        if (!$role) {
            flash('roles-danger', __('No such role found'));

            if (is_ajax()) {
                return;
            }

            return redirect_to_current_route();
        }

        if ($role['is_protected']) {
            flash('roles-danger', __('You can\'t delete protected roles'));

            if (is_ajax()) {
                return;
            }

            return redirect_to_current_route();
        }

        $usersWithRoleCount = $roleModel->getUsersCountUnderRole($id);

        if ($usersWithRoleCount > 0) {
            flash('roles-danger', sprintf(__('%d user(s) are still assigned to this role. Make sure no user is assigned to this role before deleting it.'), $usersWithRoleCount));

            if (is_ajax()) {
                return;
            }

            return redirect_to_current_route();
        }

        $roleModel->deleteRole($id);

        flash('roles-success', __('Role was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.roles');
    }
}
