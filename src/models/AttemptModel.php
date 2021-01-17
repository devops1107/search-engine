<?php

namespace spark\models;

/**
* Model for Managing User Attempts
*
* @package spark
*/
class AttemptModel extends Model
{
    const TYPE_FORGOT_PASS = 'FORGOT_PASS';
    const TYPE_SIGN_IN = 'SIGN_IN';
    const TYPE_EMAIL_VERIFY = 'TYPE_EMAIL_VERIFY';

    protected static $table = 'attempts';

    protected $queryKey = 'attempt_id';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Log an attempt
     *
     * @param  string   $type
     * @param  integer  $time
     * @param  mixed    $userIP
     * @param  integer  $userID
     * @return boolean
     */
    public function logAttempt($type, $time = null, $userIP = null, $userID = 0)
    {
        if (!$time) {
            $time = time();
        }

        if (!$userIP) {
            $userIP = app()->request->getIp();
        }

        return $this->create([
            'attempt_type' => $type,
            'attempt_ip' => $userIP,
            'attempt_time' => $time,
            'user_id' => (int) $userID
        ]);
    }

    /**
     * Log sign in attempt
     *
     * @param  integer  $time
     * @param  mixed    $userIP
     * @return boolean
     */
    public function logSignInAttempt($time = null, $userIP = null)
    {
        return $this->logAttempt(static::TYPE_SIGN_IN, $time, $userIP);
    }

    /**
     * Log forgot pass request attempt
     *
     * @param  integer  $time
     * @param  mixed    $userIP
     * @param  integer  $userID
     * @return boolean
     */
    public function logForgotPassAttempt($time, $userIP = null, $userID = 0)
    {
        return $this->logAttempt(static::TYPE_FORGOT_PASS, $time, $userIP, $userID);
    }

    /**
     * Log email verification token request attempt
     *
     * @param  integer  $time
     * @param  mixed    $userIP
     * @param  integer  $userID
     * @return boolean
     */
    public function logEmailVerificationAttempt($time, $userIP = null, $userID = 0)
    {
        return $this->logAttempt(static::TYPE_EMAIL_VERIFY, $time, $userIP, $userID);
    }

    /**
     * Get last attempt
     *
     * @param  string   $type
     * @param  integer  $interval
     * @param  mixed    $userIP
     * @param  integer  $userID
     * @param  array    $fields
     * @return array
     */
    public function getLastAttempt(
        $type,
        $interval = null,
        $userIP = null,
        $userID = null,
        array $fields = ['*']
    ) {
        $sql = $this->select($fields)
               ->where('attempt_type', '=', $type)
               ->orderBy('attempt_time', 'DESC')
               ->limit(1, 0);

        if (is_string($userIP)) {
            $sql = $sql->where('attempt_ip', '=', $userIP);
        }

        if ($interval) {
            $sql = $sql->where('attempt_time', '>', $interval);
        }

        if (is_int($userID)) {
            $sql = $sql->where('user_id', '=', $userID);
        }

        $stmt = $sql->execute();
        return $stmt->fetch();
    }

    /**
     * Get attempt count
     *
     * @param  string  $type
     * @param  integer $interval
     * @param  mixed   $userIP
     * @param  integer $userID
     * @return integer
     */
    public function getAttemptCount($type, $interval, $userIP = null, $userID = null)
    {
        $sql = $this->select(["COUNT(*) AS total"])
               ->where('attempt_type', '=', $type)
               ->where('attempt_time', '>', $interval);

        if (is_int($userID)) {
            $sql = $sql->where('user_id', '=', $userID);
        }

        if (is_string($userIP)) {
            $sql = $sql->where('attempt_ip', '=', $userIP);
        }

        $stmt = $sql->execute();
        $count = $stmt->fetch();

        return (int) $count['total'];
    }

    /**
     * Get forgot password attempt count
     *
     * @param  integer $interval
     * @param  mixed   $userIP
     * @param  integer $userID
     * @return integer
     */
    public function getForgotPassAttemptCount($interval, $userIP = null, $userID = null)
    {
        return $this->getAttemptCount(static::TYPE_FORGOT_PASS, $interval, $userIP, $userID);
    }

    /**
     * Get sign in attempt count
     *
     * @param  integer $interval
     * @param  mixed   $userIP
     * @param  integer $userID
     * @return integer
     */
    public function getSignInAttemptCount($interval, $userIP = null, $userID = null)
    {
        return $this->getAttemptCount(static::TYPE_SIGN_IN, $interval, $userIP, $userID);
    }

    /**
     * Get email verification attempt count
     *
     * @param  integer $interval
     * @param  mixed   $userIP
     * @param  integer $userID
     * @return integer
     */
    public function getEmailVerificationAttemptCount($interval, $userIP = null, $userID = null)
    {
        return $this->getAttemptCount(static::TYPE_EMAIL_VERIFY, $interval, $userIP, $userID);
    }
}
