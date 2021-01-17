<?php

namespace spark\drivers\Http;

/**
* DuckDuckGo Instant Answer API Consumer
*/
class DuckDuckGoInstantAnswer
{
    const ENDPOINT = 'https://api.duckduckgo.com/';

    public function getAnswer($query)
    {
        if (filter_var($query, FILTER_VALIDATE_URL)) {
            return false;
        }

        $query = mb_strtolower(trim($query));
        $requestURI = static::ENDPOINT . "?q={$query}&format=json&skip_disambig=1&t=Based";

        $pool = app()->cache;

        $handle = base64_encode($query);

        $item = $pool->getItem("answers/{$handle}");
        $data = $item->get();


        if ($item->isHit()) {
            return $data;
        }

        $http = Http::getSession();

        try {
            $request = $http->get($requestURI);
            $response = json_decode($request->body, true);
        } catch (\Exception $e) {
            return false;
        }

        if (!empty($response['Abstract'])) {
            $item->set($response);
            $item->expiresAfter(strtotime('+1 Year'));
            $pool->save($item);
        }

        return $response;
    }
}
