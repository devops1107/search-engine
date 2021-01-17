<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\QueryModel;

/**
* DashboardQueriesController
*
* @package spark
*/
class DashboardQueriesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        if (!current_user_can('manage_engines')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.queries', __('Queries'), url_for('dashboard.queries'));
        view_set('queries__active', 'active');
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
        $queryModel = new QueryModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $queryModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$queryModel->isSortAllowed($sort)) {
            $sort = 'recently-searched';
        }

        $sortRules = $queryModel->getAllowedSorting();

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
        $entries = $queryModel->readMany(
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
        return view('admin::queries/index.php', $data);
    }


    public function indexPOST()
    {
        if (is_demo()) {
            flash('queries-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.queries');
        }

        $app = app();
        $req = $app->request;

        $action = $req->post('action');
        $postedIDs = (array) $req->post('item_multi', []);
        $queryIDs = [];

        foreach ($postedIDs as $value) {
            $queryIDs[] = $value;
        }

        if (empty($queryIDs)) {
            flash('queries-warning', __('No queries were selected.'));
            return redirect_to('dashboard.queries');
        }

        $queryModel = new QueryModel;

        switch ($action) {
            case 'delete':
                $i = 0;

                foreach ($queryIDs as $id) {
                    $queryModel->delete($id);
                    $i++;
                }

                flash('queries-success', sprintf(__('%d query(s) were deleted successfully'), $i));
                return redirect_to('dashboard.queries');
                break;
            default:
                flash('queries-warning', __('Invalid action. Please try again'));
                return redirect_to('dashboard.queries');
        }
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
        breadcrumb_add('dashboard.queries.update', __('Delete Query'));

        $queryModel = new QueryModel;

        $query = $queryModel->read($id);

        if (!$query) {
            flash('queries-danger', __('No such query found.'));
            return redirect_to('dashboard.queries');
        }

        $data = [
            'query' => $query,
        ];
        return view('admin::queries/delete.php', $data);
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
            flash('queries-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.queries');
        }

        $queryModel = new QueryModel;

        $query = $queryModel->read($id);

        if (!$query) {
            flash('queries-danger', __('No such query found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.queries');
        }


        $queryModel->delete($id);

        flash('queries-success', __('Query was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.queries');
    }
}
