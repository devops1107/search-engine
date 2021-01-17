<?php

namespace spark\models;

/**
* OAuthModel
*
*/
class OAuthModel extends Model
{
    protected static $table = 'oauth';

    protected $queryKey = 'id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'          => ['created_at' => 'DESC'],
        'oldest'          => ['created_at' => 'ASC'],
    ];
}
