<?php

namespace spark\drivers\Views;

/**
* Local instant answers
*/
class InstantAnswer
{
    public function boot($query)
    {
        $query = trim($query);

        $ipRegEx = $this->getTriggers('ia_ip');

        if (preg_match('/^'.$ipRegEx.'$/ui', $query)) {
            return $this->getUserIp();
        }

        $uaRegex = $this->getTriggers('ia_browser');

        if (preg_match('/^'.$uaRegex.'$/ui', $query)) {
            return $this->getUserAgent();
        }

        $timeRegex = $this->getTriggers('ia_time');

        if (preg_match('/^'.$timeRegex.'$/ui', $query)) {
            return $this->getUserTime();
        }


        $dateRegex = $this->getTriggers('ia_date');

        if (preg_match('/^'.$dateRegex.'$/ui', $query)) {
            return $this->getUserDate();
        }

        $resRegex = $this->getTriggers('ia_resolution');

        if (preg_match('/^'.$resRegex.'$/ui', $query)) {
            return $this->getUserResolution();
        }

        $coinRegex = $this->getTriggers('ia_flipcoin');

        if (preg_match('/^'.$coinRegex.'$/ui', $query)) {
            return $this->getFlipCoin();
        }

        $md5Regex = $this->getTriggers('ia_md5');

        if (preg_match('/^'.$md5Regex.'\s(.+)$/ui', $query, $matches)) {
            return $this->getMd5($matches[2]);
        }

        $qrRegex = $this->getTriggers('ia_qr');

        if (preg_match('/^'.$qrRegex.'\s(.+)$/ui', $query, $matches)) {
            return $this->getQrCode($matches[2]);
        }

        $base64EncodeRegex = $this->getTriggers('ia_base64_encode');

        if (preg_match('/^'.$base64EncodeRegex.'\s(.+)$/ui', $query, $matches)) {
            return $this->getBase64Encode($matches[2]);
        }


        $base64DecodeRegex = $this->getTriggers('ia_base64_decode');

        if (preg_match('/^'.$base64DecodeRegex.'\s(.+)$/ui', $query, $matches)) {
            return $this->getBase64Decode($matches[2]);
        }
    }

    public function getBase64Encode($term)
    {
        return [
            'view' => 'base64_encode.php',
            'data' => base64_encode($term),
            'extra' => ['ia_term' => $term],
        ];
    }

    public function getBase64Decode($term)
    {
        return [
            'view' => 'base64_decode.php',
            'data' => base64_decode($term),
            'extra' => ['ia_term' => $term],
        ];
    }

    public function getMd5($term)
    {
        return [
            'view' => 'md5.php',
            'data' => md5($term),
            'extra' => ['ia_term' => $term],
        ];
    }

    public function getUserIp()
    {
        return [
            'view' => 'user_ip.php',
            'data' => app()->request->getIp(),
            'extra' => [],
        ];
    }

    public function getQrCode($term)
    {
        return [
            'view' => 'qr_code.php',
            'data' => 'https://chart.apis.google.com/chart?chs=230x230&chld=L|1&choe=UTF-8&cht=qr&chl=' . e_attr(urlencode($term)),
            'extra' => ['ia_term' => $term],
        ];
    }

    public function getFlipCoin()
    {
        return [
            'view' => 'flip_coin.php',
            'data' => mt_rand(0, 1),
            'extra' => [],
        ];
    }

    public function getUserResolution()
    {
        return [
            'view' => 'user_resolution.php',
            'data' => '',
            'extra' => [],
        ];
    }

    public function getUserTime()
    {
        return [
            'view' => 'current_time.php',
            'data' => '',
            'extra' => [],
        ];
    }

    public function getUserDate()
    {
        return [
            'view' => 'current_date.php',
            'data' => '',
            'extra' => [],
        ];
    }

    public function getUserAgent()
    {
        return [
            'view' => 'user_agent.php',
            'data' => app()->request->getUserAgent(),
            'extra' => [],
        ];
    }

    /**
     * Merges the triggers for regex compatibility
     *
     * @param  string $key
     * @return string|boolean
     */
    public function getTriggers($key)
    {
        $value = (array) __($key, _T);

        if (empty($value)) {
            return false;
        }

        array_walk($value, 'preg_quote');

        return '(' . implode('|', $value) . ')';
    }
}
