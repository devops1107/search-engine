<?php

namespace spark\models;

/**
* ContentModel
*
* @package spark
*/
class ContentModel extends Model
{
    const TYPE_PAGE = 'page';
    const TYPE_ATTACHMENT = 'attachment';
    const TYPE_POST = 'post';

    /**
     * @var string Table name
     */
    protected static $table = 'content';

    protected $queryKey = 'content_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'          => ['created_at' => 'DESC'],
        'oldest'          => ['created_at' => 'ASC'],
        'a2z'             => ['content_title'  => 'ASC'],
        'z2a'             => ['content_title'  => 'DESC'],
    ];


    protected $mimeSortType = [
        'audio', 'video',
        'text', 'image', 'document'
    ];

    protected static $allowedFileTypes = [
        'jpg', 'png', 'gif', 'webp', 'bmp', 'svg', 'psd', 'zip', 'rar', '7z', 'txt',
        'sql', 'ini', 'mp4', '3gp', '3gpp', 'avi', 'mkv', 'wmv', 'mp3', 'wav', 'amr',
        'docx', 'doc', 'ppt', 'apk', 'ico', 'pdf'
    ];

    public function addAttachment($path, array $data)
    {
        $data['content_type'] = 'attachment';
        $data['content_path'] = $path;
        $data['content_body'] = '';

        if (isset($data['content_meta']) && is_array($data['content_meta'])) {
            $data['content_meta'] = json_encode($data['content_meta']);
        } else {
        }

        if (empty($data['content_author'])) {
            $data['content_author'] = current_user_ID();
        }

        if (isset($data['content_slug'])) {
            unset($data['content_slug']);
        }

        return $this->create($data);
    }

    public function deleteAttachment($contentID)
    {
        $filters = [];
        $filters['where'][] = ['content_type', '=', 'attachment'];
        $attachment = $this->read($contentID, ['content_path'], $filters);
        if (!$attachment) {
            return false;
        }


        $this->delete($contentID);

        $file = uploadspath($attachment['content_path']);
        return @unlink($file);
    }

    /**
     * Get MySQL RegEx for certain filetype
     *
     * @param  string $fileType
     * @return string
     */
    public function getMimeTypeRegex($fileType)
    {
        switch ($fileType) {
            case 'audio':
                $fileType = "^audio/";
                break;
            case 'video':
                $fileType = "^video/";
                break;
            case 'text':
                $fileType = "^text/";
                break;
            case 'image':
                $fileType = "^image/";
                break;
            case 'document':
                $fileType = "spreadsheet|excel|presentation|powerpoint|officedocument";
                break;
            case 'everything':
            default:
                $fileType = null;
                break;
        }

        return $fileType;
    }

    public static function addAllowedFileTypes(array $fileTypes)
    {
        static::$allowedFileTypes = array_merge(static::$allowedFileTypes, $fileTypes);
        return true;
    }

    public static function getAllowedFileTypes()
    {
        return static::$allowedFileTypes;
    }

    /**
     * Get file type from extension and mimetype
     *
     * @param  string $ext
     * @param  string $mimetype
     * @return string
     */
    public static function getFileType($ext, $mimetype)
    {
        if (preg_match('#spreadsheet|excel#i', $mimetype)) {
            return 'spreadsheet';
        }
        if (preg_match('#.presentation|powerpoint#i', $mimetype)) {
            return 'interactive';
        }
        if (preg_match('#officedocument#i', $mimetype)) {
            return 'document';
        }
        if (preg_match('#^text/#i', $mimetype)) {
            return 'text';
        }

        if (preg_match('#^image/#i', $mimetype)) {
            return 'image';
        }

        if (preg_match('#^video/#i', $mimetype)) {
            return 'video';
        }

        if (preg_match('#^audio/#i', $mimetype)) {
            return 'audio';
        }

        $ext = mb_strtolower($ext);
        switch ($ext) {
            // Archives
            case 'zip':
            case 'rar':
            case 'tar':
            case '7z':
            case 'tar.gz':
            case 'arj':
            case 'deb':
            case 'rpm':
            case 'z':
            case 'gz':
                return 'archive';
            // Executables
            case 'apk':
            case 'bat':
            case 'bin':
            case 'cgi':
            case 'com':
            case 'exe':
            case 'gadget':
            case 'jar':
            case 'py':
            case 'wsf':
                return 'executable';
            default:
                return 'file';
            break;
        }
    }

    /**
     * If sort type is valid content mimetype
     *
     * @param  string  $type
     * @return boolean
     */
    public function isValidSortMimeType($type)
    {
        return in_array(mb_strtolower($type), $this->mimeSortType);
    }
}
