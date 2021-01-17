<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\EngineModel;

/**
* DashboardEnginesController
*
* @package spark
*/
class DashboardEnginesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        if (!current_user_can('manage_engines')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.engines', __('Engines'), url_for('dashboard.engines'));
        view_set('engines__active', 'active');
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
        $engineModel = new EngineModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $engineModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$engineModel->isSortAllowed($sort)) {
            $sort = 'order-first';
        }

        $sortRules = $engineModel->getAllowedSorting();

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
        $entries = $engineModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

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
        return view('admin::engines/index.php', $data);
    }


    /**
     * Reorder engines
     *
     * @return
     */
    public function reorder()
    {
        // Load form validator
        sp_enqueue_script('sortable-js', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.reorder', __('Reorder Engines'));

        $engineModel = new EngineModel;

        $engines = $engineModel->select(['engine_name', 'engine_id', 'engine_order'])
                               ->orderBy('engine_order', 'ASC')
                               ->execute()
                               ->fetchAll();

        $order = [];

        foreach ($engines as $e) {
            $order[] = $e['engine_id'];
        }

        $data = [
            'engines' => $engines,
            'engine_order' => $order,
        ];
        return view('admin::engines/reorder.php', $data);
    }

    public function reorderPOST()
    {
        $app = app();

        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to_current_route();
        }

        $order = json_decode($app->request->post('order'), true);

        if (!is_array($order) || empty($order)) {
            flash('engines-danger', __('Invalid order data.'));
            return redirect_to_current_route();
        }

        $engineModel = new EngineModel;

        foreach ($order as $key => $id) {
            $key = $key + 1;
            $id = (int) $id;

            if (!$id) {
                continue;
            }

            $engineModel->update($id, ['engine_order' => $key]);
        }

        flash('engines-success', __('Engines order updated successfully.'));
        return redirect_to_current_route();
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
        breadcrumb_add('dashboard.engines.create', __('Create Engine'));

        $data = [];
        return view('admin::engines/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'engine_name' => trim($req->post('engine_name')),
            'engine_cse_id' => trim($req->post('engine_cse_id')),
            'engine_is_image' => sp_int_bool($req->post('engine_is_image')),
            'engine_show_thumb' => sp_int_bool($req->post('engine_show_thumb')),
            'engine_show_ads' => sp_int_bool($req->post('engine_show_ads')),
            'engine_log_search' => sp_int_bool($req->post('engine_log_search')),
        ];

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'engine_name', 200)
          ->rule('lengthMax', 'engine_cse_id', 200)
          ->rule('required', [
                "engine_name",
                "engine_cse_id",
                "engine_is_image",
                "engine_show_thumb",
                "engine_show_ads",
                "engine_log_search"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('engines-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }



        $engineModel = new EngineModel;
        $i = 0;
        $order = $engineModel->select(['engine_order'])
                             ->orderBy('engine_order', 'DESC')
                             ->limit(1, 0)
                             ->execute()
                             ->fetch();

        if (isset($order['engine_order'])) {
            $i = (int) $order['engine_order'];
        }

        $data['engine_name'] = sp_strip_tags($data['engine_name']);
        $data['engine_cse_id'] = sp_strip_tags($data['engine_cse_id']);
        $data['engine_order'] = $i + 1;
        $id = $engineModel->create($data);

        flash('engines-success', __('Engine was created successfully'));


        return redirect_to('dashboard.engines');
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

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.update', __('Update Engine'));

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        $data = [
            'engine' => $engine,
        ];

        return view('admin::engines/update.php', $data);
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
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'engine_name' => trim($req->post('engine_name')),
            'engine_cse_id' => trim($req->post('engine_cse_id')),
            'engine_is_image' => sp_int_bool($req->post('engine_is_image')),
            'engine_show_thumb' => sp_int_bool($req->post('engine_show_thumb')),
            'engine_show_ads' => sp_int_bool($req->post('engine_show_ads')),
            'engine_log_search' => sp_int_bool($req->post('engine_log_search')),
        ];

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'engine_name', 200)
          ->rule('lengthMax', 'engine_cse_id', 200)
          ->rule('required', [
                "engine_name",
                "engine_cse_id",
                "engine_is_image",
                "engine_show_thumb",
                "engine_show_ads",
                "engine_log_search"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('engines-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $data['engine_name'] = sp_strip_tags($data['engine_name']);
        $data['engine_cse_id'] = sp_strip_tags($data['engine_cse_id']);

        $engineModel->update($id, $data);

        flash('engines-success', __('Engine was updated successfully'));
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
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.engines.update', __('Delete Engine'));

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        $data = [
            'engine' => $engine,
        ];
        return view('admin::engines/delete.php', $data);
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
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.engines');
        }


        if (get_option('default_engine') == $engine['engine_id']) {
            flash('engines-danger', __('The default engine can\'t be deleted.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.engines');
        }


        $engineModel->delete($id);

        flash('engines-success', __('Engine was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.engines');
    }


    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function setDefaultEngine($id)
    {
        if (is_demo()) {
            flash('engines-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.engines');
        }

        $engineModel = new EngineModel;

        $engine = $engineModel->read($id, ['engine_id']);

        if (!$engine) {
            flash('engines-danger', __('No such engine found.'));
            return redirect_to('dashboard.engines');
        }

        set_option('default_engine', $engine['engine_id']);


        flash('engines-success', __('Default engine set successfully.'));
        return redirect_to('dashboard.engines');
    }
}
