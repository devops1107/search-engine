<?php

namespace spark\models;

/**
* ReportModel
*
*/
class ReportModel extends Model
{
    /**
     * Report status closed
     */
    const STATUS_CLOSED = 0;

    /**
     * Report status open
     */
    const STATUS_OPEN = 1;

    /**
     * Report type user
     */
    const TYPE_USER = 'USER';

    protected $typeMap = [
        self::TYPE_USER => 'UserModel',
    ];

    protected static $table = 'reports';

    protected $queryKey = 'report_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'open-first'   => ['report_status' => 'DESC'],
        'closed-first' => ['report_status' => 'ASC'],
        'newest' => ['created_at' => 'DESC'],
        'oldest' => ['created_at' => 'ASC'],
    ];

    public function getModel($type)
    {
        return isset($this->typeMap[$type]) ? $this->typeMap[$type] : false;
    }

    public function generateLink($type, $targetID)
    {
        if ($type == static::TYPE_USER) {
            return url_for('dashboard.users.update', ['id' => $targetID]);
        }

        return false;
    }
}
