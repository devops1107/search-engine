<?php

namespace spark\models;

/**
* MenuModel
*
*/
class MenuModel extends Model
{
    protected static $table = 'menus';

    protected $queryKey = 'menu_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'          => ['created_at' => 'DESC'],
        'oldest'          => ['created_at' => 'ASC'],
    ];
}
