<?php

namespace spark\controllers\Dashboard;

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\models\ContentModel;

/**
* DashboardGalleryController
*
* @package spark
*/
class DashboardGalleryController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();



        if (!current_user_can('access_gallery|manage_gallery')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.gallery', __('Gallery'), url_for('dashboard.gallery'));

        view_set('gallery__active', 'active');


    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        // Load form validator
        sp_enqueue_script('dropzone-js', 2);
        sp_enqueue_script('jquery-inview', 2, ['jquery']);

        $app = app();

        // Model instance
        $contentModel = new ContentModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = $app->config('dashboard.gallery_items_per_page');


        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$contentModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }

        $mime = $app->request->get('type', null);

        $sortRules = $contentModel->getAllowedSorting();
        $mimeRules = $contentModel->mimeSortType;
        array_unshift($mimeRules, 'everything');

        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        $filters['where'][] = ['content_type', '=', 'attachment'];


        if ($mime && $contentModel->isValidSortMimeType($mime)) {
            $mimeRegex = $contentModel->getMimeTypeRegex($mime);
            $filters['where'][] = ['content_mimetype', 'REGEXP', $mime];
        } else {
            $mime = 'everything';
        }


        // Total item count
        $totalCount = $contentModel->countRows(null, $filters);

        $queryStr = request_build_query(['page', 'sort', 'type']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}&type={$mime}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // List entries
        $entries = $contentModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

        foreach ($entries as $key => $_entry) {
            $entry = $_entry;
            $entry['content_ext'] = pathinfo($_entry['content_path'], PATHINFO_EXTENSION);
            $entry['content_file_type'] = $contentModel->getFileType($entry['content_ext'], $_entry['content_mimetype']);
            $entry['content_url'] = uploads_uri($_entry['content_path']);
            $entry['content_rel_path'] = trailingslashit(UPLOADS_DIR) . $_entry['content_path'];
            $entry['content_readable_size'] = format_size_units($_entry['content_size']);
            $entry['content_filename'] = $_entry['content_title'] . '.' . $entry['content_ext'];

            if (strtolower($entry['content_ext'] == 'ico')) {
                $entry['content_thumbnail'] = $entry['content_url'];
            } elseif (preg_match('#^image/#i', $_entry['content_mimetype'])) {
                $entry['content_thumbnail'] = sp_thumbnail_uri($entry['content_rel_path']);
            } else {
                $entry['content_thumbnail'] = site_uri("assets/img/media/{$entry['content_file_type']}.png");
            }

            $entries[$key] = $entry;
        }

        $presentItemsCount = count($entries);

        if (is_ajax()) {
            $html = '';
            foreach ($entries as $item) {
                $html .= view_fetch('admin::gallery/partials/gallery_loop.php', ['item' => $item]);
            }

            return json(['html' => $html, 'present_items_count' => $presentItemsCount]);
        }

        // Template data
        $data = [
            'list_entries'    => $entries,
            'max_upload_size' => format_bytes(get_max_upload_size(), 'M', 0),
            'allowed_filetypes' => $contentModel->getAllowedFileTypes(),
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'present_items_count' => $presentItemsCount,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'mime_type'       => $mime,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'mime_rules'      => $mimeRules,
            'query_str'       => $queryStr
        ];
        return view('admin::gallery/index.php', $data);
    }


    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (!current_user_can('manage_gallery')) {
            response_status(403);
            return response_body(__("You don't have enough permissions to perform this action."));
        }

        if (is_demo()) {
            response_status(403);
            return response_body($GLOBALS['_SPARK_I18N']['demo_mode']);
        }

        $uploadPath = uploadspath(date('Y/M/d'));

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $app = app();

        $nonAbsoluteUploadPath = date('Y/M/d');

        $contentModel = new ContentModel;

        $storage = new FileSystem($uploadPath);

        $allowedExtensions = $contentModel->getAllowedFileTypes();

        $maxSize = format_bytes(get_max_upload_size(), 'M') . 'M';

        $file = new File('file', $storage);

        $originalFileName = $file->getName();
        $fileName = $originalFileName;

        $i = 1;
        while (file_exists($uploadPath . '/' . $fileName . '.' . $file->getExtension())) {
            $i++;
            $fileName = $originalFileName . " ({$i})";
        }

        $file->setName($fileName);

        $fileData = [
            'name'       => $file->getNameWithExtension(),
            'rawName'    => $file->getName(),
            'extension'  => $file->getExtension(),
            'mime'       => $file->getMimetype(),
            'size'       => $file->getSize(),
            'dimension'  => $file->getDimensions(),
        ];

        $file->addValidations([
            new Extension($allowedExtensions),
            new Size($maxSize)
        ]);

        try {
            $file->upload();
        } catch (\Exception $e) {
            $errors = $file->getErrors();
            response_status(403);
            return response_body(join("\n", $errors));
        }

        $filePath = trailingslashit($nonAbsoluteUploadPath) . $fileData['name'];

        $data = [
            'content_title' => $fileData['rawName'],
            'content_size' => $fileData['size'],
            'content_mimetype' => $fileData['mime'],
            'content_meta'    => $fileData['dimension'],
        ];

        $contentID = $contentModel->addAttachment($filePath, $data);

        $jsonData = $data;
        $jsonData['success'] = true;
        $jsonData['content_url'] = uploads_uri($filePath);
        $jsonData['content_relative_url'] = leadingslashit(UPLOADS_DIR) . leadingslashit($filePath);
        $jsonData['html'] = null;

        $mode = strtolower($app->request->post('mode'));

        // For gallery uploads return the new html
        if ($mode === 'gallery') {
            $content = $contentModel->read($contentID);

            if ($content) {
                $content['content_ext'] = pathinfo($content['content_path'], PATHINFO_EXTENSION);
                $content['content_file_type'] = $contentModel->getFileType($content['content_ext'], $content['content_mimetype']);
                $content['content_url'] = uploads_uri($content['content_path']);
                $content['content_rel_path'] = trailingslashit(UPLOADS_DIR) . $content['content_path'];
                $content['content_readable_size'] = format_size_units($content['content_size']);
                $content['content_filename'] = $content['content_title'] . '.' . $content['content_ext'];

                if (preg_match('#^image/#i', $content['content_mimetype'])) {
                    $content['content_thumbnail'] = sp_thumbnail_uri($content['content_rel_path']);
                } else {
                    $content['content_thumbnail'] = site_uri("assets/img/media/{$content['content_file_type']}.png");
                }

                $jsonData['html'] = view_fetch('admin::gallery/partials/gallery_loop.php', ['item' => $content]);
            }
        }


        return json($jsonData);
    }

    /**
     * Delete entry action
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (!current_user_can('manage_gallery')) {
            return sp_not_permitted();
        }

        $json = ['id' => (int) $id, 'success' => false, 'message' => null];


        if (is_demo()) {
            $json['message'] = $GLOBALS['_SPARK_I18N']['demo_mode'];
            return json($json);
        }

        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_type', '=', 'attachment'];
        $galleryItem = $contentModel->read($id, ['content_path'], $filters);

        if (!$galleryItem) {
            $json['message'] = __('No such gallery item found');
            return json($json);
        }

        $contentModel->deleteAttachment($id);

        $json['message'] = __('Deleted successfully.');
        $json['success'] = true;
        return json($json);
    }
}
