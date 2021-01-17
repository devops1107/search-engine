<?php

namespace spark\models;

/**
* SessionModel
*
*/
class SessionModel extends Model
{
    const TYPE_APP = 'app';

    const TYPE_WEB = 'web';


    protected static $table = 'sessions';

    protected $queryKey = 'session_id';

    protected $autoTimestamp = false;

    protected $sortRules = [
    ];
}
