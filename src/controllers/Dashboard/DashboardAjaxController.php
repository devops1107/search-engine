<?php

namespace spark\controllers\Dashboard;

use spark\controllers\Dashboard\DashboardController;
use spark\models\ContentModel;
use spark\models\UserModel;

/**
* DashboardAjaxController
*
* For handling generic mainstream AJAX Requests
*
* @package spark
*/
class DashboardAjaxController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();




    }


    public function suggestMenuItems()
    {
        $app = app();
        $q = trim($app->request->get('q'));

        $data = [];

        if (empty($q)) {
            return json($data);
        }

        $contentModel = new ContentModel;

        $pages = $contentModel->select(['content_title', 'content_slug'])
        ->where('content_title', 'LIKE', "%{$q}%")
        ->where('content_type', '=', ContentModel::TYPE_PAGE)
        ->limit(5, 0)
        ->execute()
        ->fetchAll();

        foreach ($pages as $page) {
            $data[] = [
                'item_label' => $page['content_title'],
                'item_url' => url_for('site.page', ['identifier' => $page['content_slug']], true)
            ];
        }

        return json($data);
    }
}
