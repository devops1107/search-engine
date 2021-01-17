<?php

namespace spark\models;

use spark\models\OAuthModel;

/**
* ProviderModel
*
*/
class ProviderModel extends Model
{
    protected static $table = 'providers';

    protected $queryKey = 'provider_id';

    protected $autoTimestamp = true;

    protected $providers = [
    'Amazon' =>
    [
    'is_openid' => false,
    ],
    'Authentiq' =>
    [
    'is_openid' => false,
    ],
    'BitBucket' =>
    [
    'is_openid' => false,
    ],
    'Blizzard' =>
    [
    'is_openid' => false,
    ],
    'BlizzardAPAC' =>
    [
    'is_openid' => false,
    ],
    'BlizzardEU' =>
    [
    'is_openid' => false,
    ],
    'Discord' =>
    [
    'is_openid' => false,
    ],
    'Disqus' =>
    [
    'is_openid' => false,
    ],
    'Dribbble' =>
    [
    'is_openid' => false,
    ],
    'Facebook' =>
    [
    'is_openid' => false,
    ],
    'Foursquare' =>
    [
    'is_openid' => false,
    ],
    'GitHub' =>
    [
    'is_openid' => false,
    ],
    'GitLab' =>
    [
    'is_openid' => false,
    ],
    'Google' =>
    [
    'is_openid' => false,
    ],
    'Instagram' =>
    [
    'is_openid' => false,
    ],
    'LinkedIn' =>
    [
    'is_openid' => false,
    ],
    'Mailru' =>
    [
    'is_openid' => false,
    ],
    'MicrosoftGraph' =>
    [
    'is_openid' => false,
    ],
    'ORCID' =>
    [
    'is_openid' => false,
    ],
    'Odnoklassniki' =>
    [
    'is_openid' => false,
    ],
    'Patreon' =>
    [
    'is_openid' => false,
    ],
    'Paypal' =>
    [
    'is_openid' => false,
    ],
    'QQ' =>
    [
    'is_openid' => false,
    ],
    'Reddit' =>
    [
    'is_openid' => false,
    ],
    'Slack' =>
    [
    'is_openid' => false,
    ],
    'Spotify' =>
    [
    'is_openid' => false,
    ],
    'StackExchange' =>
    [
    'is_openid' => false,
    ],
    'StackExchangeOpenID' =>
    [
    'is_openid' => true,
    ],
    'Steam' =>
    [
    'is_openid' => false,
    ],
    'SteemConnect' =>
    [
    'is_openid' => false,
    ],
    'Strava' =>
    [
    'is_openid' => false,
    ],
    'Telegram' =>
    [
    'is_openid' => false,
    ],
    'Tumblr' =>
    [
    'is_openid' => false,
    ],
    'TwitchTV' =>
    [
    'is_openid' => false,
    ],
    'Twitter' =>
    [
    'is_openid' => false,
    ],
    'Vkontakte' =>
    [
    'is_openid' => false,
    ],
    'WeChat' =>
    [
    'is_openid' => false,
    ],
    'WeChatChina' =>
    [
    'is_openid' => false,
    ],
    'WindowsLive' =>
    [
    'is_openid' => false,
    ],
    'WordPress' =>
    [
    'is_openid' => false,
    ],
    'Yahoo' =>
    [
    'is_openid' => false,
    ],
    'YahooOpenID' =>
    [
    'is_openid' => true,
    ],
    'Yandex' =>
    [
    'is_openid' => false,
    ],
    ];

    protected $sortRules = [
        'newest'         => ['created_at' => 'DESC'],
        'oldest'         => ['created_at' => 'ASC'],
        'enabled-first'  => ['provider_enabled' => 'DESC'],
        'disabled-first' => ['provider_enabled' => 'ASC'],
    ];

    public function listProviders()
    {
        return $this->providers;
    }

    public function getProviders($fields = ['*'])
    {
        return $this->select($fields)
                    ->where('provider_enabled', '=', 1)
                    ->execute()
                    ->fetchAll();
    }

    public function getProvider($key)
    {
        return $this->hasProvider($key) ? $this->providers[$key] : false;
    }

    public function hasProvider($key)
    {
        return isset($this->providers[$key]);
    }
}
