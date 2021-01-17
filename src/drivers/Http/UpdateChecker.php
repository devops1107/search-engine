<?php

namespace spark\drivers\Http;

use spark\drivers\Http\Http;

/**
* Update checker
*
* @package spark
*/
class UpdateChecker
{
    /**
     * Endpoint to check for updates
     *
     * @var string
     */
    protected static $source = "https://raw.githubusercontent.com/MirazMac/mi-version-cdn/master/mi-tube.json";

    public function check()
    {
        $http = Http::getSession();
        $pool = app()->cache;
        $item = $pool->getItem('updates');
        $data = $item->get();

        if ($item->isMiss()) {
            try {
                $request = $http->get(static::$source . '?_=' . time());
            } catch (\Exception $e) {
                logger()->error($e);
                return false;
            }

            $data = json_decode($request->body, true);


            if (!isset($data['latestVersion'])) {
                return false;
            }

            $item->set($data);
            $item->expiresAfter(config('site.update_check_interval', 3600));
            $pool->save($item);
        }

        $result = [
            'available'               => false,
            'latest_version'          => $data['latestVersion'],
            'latest_version_codename' => $data['latestVersionCodeName'],
            'updated_at'              => $data['lastUpdateTime'],
            'previous_version'        => $data['previousVersion'],
            'download_page'           => $data['downloadPage'],
            'download_uri'            => $data['directDownloadUri'],
        ];

        if ($data['latestVersion'] > APP_VERSION) {
            $result['available'] = true;
        }

        return $result;
    }
}
