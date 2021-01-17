<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\MenuModel;
use spark\models\MenuRelModel;

/**
* DashboardMenusController
*
* @package spark
*/
class DashboardMenusController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();



        if (!current_user_can('manage_menus')) {
            sp_not_permitted();
        }


        breadcrumb_add('dashboard.appearance', __('Appearance'), '#appearance');
        breadcrumb_add('dashboard.menus', __('Menus'), url_for('dashboard.menus'));
        view_set('menus__active', 'active');
    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        $app = app();

        // Model instance
        $menuModel = new MenuModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $menuModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$menuModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }

        $sortRules = $menuModel->getAllowedSorting();

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
        $entries = $menuModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'menu_locations'  => get_registered_nav_menus(),
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'query_str'       => $queryStr
        ];
        return view('admin::menus/index.php', $data);
    }

    /**
     * Create new entry
     *
     * @return
     */
    public function create()
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.menus.create', __('Create Menu'));

        $data = [];
        return view('admin::menus/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('menus-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.menus');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'menu_name' => trim($req->post('menu_name')),
        ];

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'menu_name', 200)
          ->rule('required', [
                "menu_name"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('menus-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $menuModel = new MenuModel;
        $data['menu_name'] = sp_strip_tags($data['menu_name']);


        $id = $menuModel->create($data);

        flash('menus-success', __('Menu was created successfully'));
        return redirect_to('dashboard.menus.update', ['id' => $id]);
    }

    /**
     * Update entry page
     *
     * @param mixed $id
     * @return
     */
    public function update($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
        sp_enqueue_script('jquery-nestable', 2, ['jquery']);
        sp_enqueue_script('jquery-autocomplete', 2, ['jquery']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.menus.update', __('Update Menu'));

        $menuModel = new MenuModel;

        $menuLocations = get_registered_nav_menus();

        $menu = $menuModel->read($id);

        if (!$menu) {
            flash('menus-danger', __('No such menu found.'));
            return redirect_to('dashboard.menus');
        }

        $menuRelModel = new MenuRelModel;

        $menuItems = $menuRelModel->select(['*'])
                                  ->where('menu_id', '=', $id)
                                  ->orderBy('sort')
                                  ->execute();

        $ref   = [];
        $items = [];

        while ($item = $menuItems->fetch()) {
            $thisRef = &$ref[$item['item_id']];

            $thisRef['parent_id'] = $item['parent_id'];
            $thisRef['item_label'] = $item['item_label'];
            $thisRef['item_url'] = $item['item_url'];
            $thisRef['item_id'] = $item['item_id'];
            $thisRef['item_class'] = $item['item_class'];
            $thisRef['item_icon'] = $item['item_icon'];

            if ((int) $item['parent_id'] === 0) {
                $items[$item['item_id']] = &$thisRef;
            } else {
                $ref[$item['parent_id']]['child'][$item['item_id']] = &$thisRef;
            }
        }

        $data = [
            'menu' => $menu,
            'menu_html' => $this->getMenu($items),
            'menu_locations' => $menuLocations,
        ];

        return view('admin::menus/update.php', $data);
    }

    public function getMenu(array $items, $class = 'dd-list')
    {
        $html = "<ol class=\"".$class."\">";
        foreach ($items as $key => $value) {
            $html .= $this->makeMenuItem($value);
            if (array_key_exists('child', $value)) {
                $html .= $this->getMenu($value['child'], 'child');
            }
            $html .= "</li>";
        }

        $html .= "</ol>";

        return $html;
    }

    public function makeMenuItem($value, $closeLi = false)
    {
        $deleteURL = url_for('dashboard.menus.delete_menu_post', ['id' => $value['item_id']]);

        $html = '<li class="dd-item" data-id="' . e_attr($value['item_id']) . '" id="menu-item-' . e_attr($value['item_id']) . '">
        <div class="dd-handle">
        <span class="float-left d-inline-block text-truncate menu-label" id="menu-item-label-' . e_attr($value['item_id']) . '">' . e($value['item_label']) . '</span> <span class="d-none d-md-inline-block text-truncate float-right text-muted menu-url" id="menu-item-url-' . e_attr($value['item_id']) . '">' . e_attr($value['item_url']) . '</span>
        </div>
        <div class="py-1 px-2 mb-3">
        <a href="javascript:void(0);" data-id="' . e_attr($value['item_id']) . '" data-class="' . e_attr($value['item_class']) . '" data-url="' . e_attr($value['item_url']) . '" data-label="' . e_attr($value['item_label']) . '" data-icon="' . e_attr($value['item_icon']) . '" class="edit-modal">' . __('Edit') . '</a> &middot;
        <a href="javascript:void(0);" data-endpoint="' . $deleteURL . '" class="text-danger delete-entry" data-id="' . e_attr($value['item_id']) . '">' . __('Remove') . '</a>
        </div>';

        if ($closeLi) {
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function updatePOST($id)
    {
        if (is_demo()) {
            flash('menus-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.menus');
        }

        $menuModel = new MenuModel;

        $menu = $menuModel->read($id);

        if (!$menu) {
            flash('menus-danger', __('No such menu found.'));
            return redirect_to('dashboard.menus');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'menu_name' => trim($req->post('menu_name')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'menu_name', 200)
          ->rule('required', [
                "menu_name"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('menus-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $data['menu_name'] = sp_strip_tags($data['menu_name']);
        $menuModel->update($id, $data);


        $registeredMenus = get_registered_nav_menus();

        $locations = (array) $app->request->post('menu_locations', []);

        foreach ($registeredMenus as $key => $value) {
            // Detect uncheck
            $current = (int) get_active_menu_id($key, 0);

            // Remove unchecked ones
            if ($current === (int) $menu['menu_id'] && !isset($locations[$key])) {
                set_active_menu_id($key, 0);
            }

            // ignore if the location isn't present
            if (!isset($locations[$key])) {
                continue;
            }

            set_active_menu_id($key, $menu['menu_id']);
        }

        flash('menus-success', __('Menu was updated successfully'));

        $app->cache->clear("parsedMenu/{$id}");

        return redirect_to_current_route();
    }

    public function editMenuPOST($id)
    {
        $json = [
            'success'     => false,
            'message'     => null,
            'type'        => 'warning',
            'dismissable' => true,
            'html'        => null,
            'data'        => [],
        ];

        $app = app();

        if (is_demo()) {
            $json['message'] = $GLOBALS['_SPARK_I18N']['demo_mode'];
            return json($json);
        }


        $menuRelModel = new MenuRelModel;

        $item = $menuRelModel->exists($id);

        if (!$item) {
            $json['message'] = __('No such menu item found.');
            return json($json);
        }


        $data = [
            'item_label' => sp_strip_tags($app->request->post('item_label'), true),
            'item_url' => sp_strip_tags($app->request->post('item_url')),
            'item_class' => sp_strip_tags($app->request->post('item_class'), true),
            'item_icon' => sp_strip_tags($app->request->post('item_icon'), true)
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'item_label', 200)
          ->rule('lengthMax', 'item_icon', 200)
          ->rule('required', [
                "item_label", "item_url"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            $json['message'] = $errors;
            return json($json);
        }

        $menuRelModel->update($id, $data);

        $app->cache->clear("parsedMenu/{$id}");

        $json['message'] = __('Menu item was updated successfully.');
        $json['type'] = 'success';
        $json['success'] = true;

        // Provide the data as well for updating
        $data['item_id'] = $id;
        $json['data'] = $data;
        return json($json);
    }

    public function addMenuPOST($id)
    {
        $json = [
            'success'     => false,
            'message'     => null,
            'type'        => 'warning',
            'dismissable' => true,
            'html'        => null,
        ];

        if (is_demo()) {
            $json['message'] = $GLOBALS['_SPARK_I18N']['demo_mode'];
            return json($json);
        }

        $menuModel = new MenuModel;

        $menu = $menuModel->read($id);

        if (!$menu) {
            $json['message'] = __('No such menu found.');
            return json($json);
        }

        $app = app();

        $data = [
            'parent_id' => (int) $app->request->post('parent_id', 0),
            'item_label' => sp_strip_tags($app->request->post('item_label'), true),
            'item_url' => sp_strip_tags($app->request->post('item_url')),
            'item_class' => sp_strip_tags($app->request->post('item_class'), true),
            'item_icon' => sp_strip_tags($app->request->post('item_icon'), true),
        ];

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'item_label', 200)
          ->rule('required', [
                "item_label", "item_url"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            $json['message'] = $errors;
            return json($json);
        }

        $data['menu_id'] = $id;

        $menuRelModel = new MenuRelModel;

        $sort = $menuRelModel->select(['sort'])
                             ->where('menu_id', '=', $id)
                             ->orderBy('sort', 'DESC')
                             ->execute()
                             ->fetch();

        // Make sure it stays at the last
        if (!empty($sort['sort']) && (int) $sort['sort']) {
            $data['sort'] = $sort['sort'] + 1;
        }
        $itemID = $menuRelModel->create($data);


        $app->cache->clear("parsedMenu/{$id}");

        $data['item_id'] = $itemID;

        $json['success'] = true;
        $json['type'] = 'success';
        $json['message'] = __('Menu entry was added successfully.');
        $json['html']  = $this->makeMenuItem($data, true);
        return json($json);
    }

    public function parseJSON($jsonArray, $parentID = 0)
    {
        $return = [];
        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = [];
            if (isset($subArray['children'])) {
                $returnSubSubArray = $this->parseJSON($subArray['children'], $subArray['id']);
            }
            $return[] = ['item_id' => $subArray['id'], 'parent_id' => $parentID];
            $return = array_merge($return, $returnSubSubArray);
        }

        return $return;
    }

    public function orderMenuPOST($id)
    {
        $json = [
            'success'     => false,
            'message'     => __('No data found.'),
            'type'        => 'warning',
            'dismissable' => true,
        ];

        if (is_demo()) {
            $json['message'] = $GLOBALS['_SPARK_I18N']['demo_mode'];
            return json($json);
        }

        $app = app();
        $data = json_decode($app->request->post('data'), true);

        if (!$data) {
            return json($json);
        }

        $finalOrder = $this->parseJSON($data);

        $menuRelModel = new MenuRelModel;


        $i = 0;
        foreach ($finalOrder as $item) {
            $i++;
            $menuRelModel->update($item['item_id'], ['sort' => $i, 'parent_id' => $item['parent_id']]);
        }

        $json['success'] = true;
        $json['message'] = __('Menu order was saved successfully.');
        $json['type']   = 'success';


        $app->cache->clear("parsedMenu/{$id}");

        return json($json);
    }

    public function deleteMenuPOST($id)
    {
        $app = app();

        $json = [
            'success'     => false,
            'message'     => null,
            'type'        => 'warning',
            'dismissable' => true,
        ];

        if (is_demo()) {
            $json['message'] = $GLOBALS['_SPARK_I18N']['demo_mode'];
            return json($json);
        }

        $menuRelModel = new MenuRelModel;
        $menuRelModel->deleteNested($id);

        $json['message'] = __('Item was removed successfully.');
        $json['success'] = true;
        $json['type'] = 'success';


        $app->cache->clear("parsedMenu/{$id}");

        return json($json);
    }

    /**
     * Delete entry page
     *
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.menus.update', __('Delete Menu'));

        $menuModel = new MenuModel;

        $menu = $menuModel->read($id);

        if (!$menu) {
            flash('menus-danger', __('No such menu found.'));
            return redirect_to('dashboard.menus');
        }

        $data = [
            'menu' => $menu,
        ];
        return view('admin::menus/delete.php', $data);
    }

    /**
     * Delete entry action
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (is_demo()) {
            if (is_ajax()) {
                return;
            }
            flash('menus-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.menus');
        }

        $menuModel = new MenuModel;

        $menu = $menuModel->read($id);

        if (!$menu) {
            flash('menus-danger', __('No such menu found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.menus');
        }

        $menuModel->delete($id);

        $app = app();

        $app->cache->clear("parsedMenu/{$id}");

        flash('menus-success', __('Menu was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.menus');
    }
}
