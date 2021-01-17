<?php

namespace spark\drivers\Auth;

use \Requests;

/**
* Google ReCaptcha Driver
*
* @package spark
*/
class GoogleReCaptcha
{
    const ENDPOINT = "https://www.google.com/recaptcha/api/siteverify";

    public function verify($userInput)
    {
        if (empty($userInput)) {
            return false;
        }

        $headers = ['Accept' => 'application/json'];
        $data = [
            'secret' => get_option('google_recaptcha_secret_key'),
            'response' => $userInput,
            'remoteip' => app()->request->getIp()
        ];

        try {
            $request = Requests::post(static::ENDPOINT, $headers, $data);
        } catch (\Exception $e) {
            return false;
        }

        $response = json_decode($request->body, true);

        if (isset($response['success']) && $response['success'] === true) {
            return true;
        }

        return false;
    }
}
