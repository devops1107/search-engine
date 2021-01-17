<?php

namespace spark\models;

/**
* EngineModel
*
*/
class EngineModel extends Model
{
    protected static $table = 'engines';

    protected $queryKey = 'engine_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'        => ['created_at' => 'DESC'],
        'oldest'        => ['created_at' => 'ASC'],
        'order-first'  => ['engine_order' => 'ASC'],
        'order-last' => ['engine_order' => 'DESC'],
    ];
}
