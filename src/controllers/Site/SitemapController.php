<?php

namespace spark\controllers\Site;

use spark\controllers\Controller;
use spark\models\QueryModel;

/**
* SitemapController
*
* @package spark
*/
class SitemapController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        view_set('changefreq', 'daily');
    }

    /**
     * Callback for the sitemap index
     *
     */
    public function sitemapIndex()
    {
        $app = app();
        $data = [];
        $queryModel = new QueryModel;
        $total = $queryModel->countRows();


        // Even though Google's rate is 50,000 per page,
        // but its better to split up because of the crawling rate
        $sitemapItemsPerPage = (int) get_option('sitemap_links_per_page', 1000);

        $numberOfSiteMaps = ceil($total / $sitemapItemsPerPage);

        // So only one it is
        if ($numberOfSiteMaps < 1) {
            $numberOfSiteMaps = 1;
        }

        $data['total_sitemaps'] = $numberOfSiteMaps;

        $app->response->headers->set('content-type', 'application/xml');
        return view('sitemap/sitemap-index.php', $data);
    }


    /**
     * Sitemap
     *
     * @return
     */
    public function sitemap($page = 1)
    {
        $app = app();
        $data = [];

        $app->response->headers->set('content-type', 'application/xml');

        $queryModel    = new QueryModel;

        $itemsPerPage = (int) get_option('sitemap_links_per_page', 1000);
        $offset = ($page - 1) * $itemsPerPage;


        $filter['sort'] = 'oldest';

        $items = $queryModel->readMany(
            ['query_term', 'updated_at'],
            $offset,
            $itemsPerPage,
            $filter
        );

        $entries = [];

        // Add homepage URL for the first one
        if ($page == 1) {
            $entries[] = [
                'url' => base_uri(),
                'updated_at' => time(),
            ];
        }

        foreach ($items as $key => $item) {
            $entries[] = [
                'url' => url_for('site.search') . "?q=" . urlencode($item['query_term']),
                'updated_at' => $item['updated_at'],
            ];
        }


        $data['entries'] = $entries;

        return view('sitemap/sitemap-single.php', $data);
    }
}
