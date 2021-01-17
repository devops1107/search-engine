<?php

namespace spark\models;

/**
* QueryModel
*
*/
class QueryModel extends Model
{
    protected static $table = 'queries';

    protected $queryKey = 'query_id';

    protected $autoTimestamp = false;

    protected $sortRules = [
        'recently-searched' => ['updated_at' => 'DESC'],
        'newest'            => ['created_at' => 'DESC'],
        'oldest'            => ['created_at' => 'ASC'],
        'most-searched'     => ['query_count' => 'DESC'],
        'least-searched'    => ['query_count' => 'ASC'],
    ];
}
