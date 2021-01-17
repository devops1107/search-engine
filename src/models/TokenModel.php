<?php

namespace spark\models;

/**
* Handles all Token Related Data
*
* @version 0.1
* @author MirazMac <mirazmac@gmail.com>
* @link https://mirazmac.info <Author Homepage>
*/
class TokenModel extends Model
{
    /**
     * Cookie Token Identifier
     *
     * @var string
     */
    const TYPE_COOKIE = 'COOKIE';

    /**
     * E-mail Token Identifier
     *
     * @var string
     */
    const TYPE_EMAIL = 'EMAIL';

    /**
     * Forgot password Token Identifier
     *
     * @var string
     */
    const TYPE_FORGOT_PASS = 'FORGOT_PASS';

    /**
     * Cookie token length
     *
     * @var integer
     */
    const COOKIE_TOKEN_LENGTH = 30;

    /**
     * Email token length
     *
     * @var integer
     */
    const EMAIL_TOKEN_LENGTH = 20;

    /**
     * Forgot password token length
     *
     * @var integer
     */
    const FORGOT_PASS_TOKEN_LENGTH = 10;

    protected static $table = 'tokens';

    protected $queryKey = 'token_id';

    public function createCookieToken($userID)
    {
        $expires = strtotime(config('auth.cookie_token_lifespan', '+2 Months'));
        return $this->createToken(
            $userID,
            static::TYPE_COOKIE,
            $expires,
            static::COOKIE_TOKEN_LENGTH
        );
    }

    public function createEmailToken($userID)
    {
        $expires = strtotime(config('auth.mail_verify_token_lifespan', '+1 Days'));
        return $this->createToken(
            $userID,
            static::TYPE_EMAIL,
            $expires,
            static::EMAIL_TOKEN_LENGTH
        );
    }

    public function createForgotPassToken($userID)
    {
        $expires = strtotime(config('auth.forgot_pass_token_lifespan', '+1 Days'));
        return $this->createToken(
            $userID,
            static::TYPE_FORGOT_PASS,
            $expires,
            static::FORGOT_PASS_TOKEN_LENGTH
        );
    }

    /**
     * Create a new token
     *
     * @param  intger|null  $userID
     * @param  string       $type
     * @param  mixed        $expires
     * @param  integer      $length
     * @return boolean|array
     */
    public function createToken($userID, $type, $expires, $length = 30)
    {
        $token = str_random_secure($length);

        if ($this->getToken($token, $type, null, ['token_id'])) {
            // wtf! you're not unique watson!
            // Go and try again
            return $this->createToken($userID, $type, $expires, $length);
        }

        // So you're unique eh?
        $data = [
            'user_id' => $userID,
            'token_type' => $type,
            'token_value' => $token,
            'token_expires' => $expires,
            'session_id'   => session_id(),
        ];

        if ($this->create($data)) {
            return $data;
        }

        // you probably won't reach here
        return false;
    }

    public function getToken($token, $type = null, $userID = null, array $fields = ['*'])
    {
        $sql = $this->select($fields)->where('token_value', '=', $token);

        if (is_string($type)) {
            $sql = $sql->where('token_type', '=', $type);
        }

        if ($userID) {
            $sql = $sql->where('user_id', '=', $userID);
        }

        $sql = $sql->orderBy('token_expires', 'DESC')->limit(1, 0);
        $stmt = $sql->execute();
        return $stmt->fetch();
    }

    /**
     * Delete all existing tokens of a certain type for a user
     *
     * @param  string $type
     * @param  integer $userID
     *
     * @return boolean
     */
    public function deleteTokens($type, $userID)
    {
        $db = app()->db;
        $table = $this->getTable();
        $sql = $db->delete()
                  ->from($table)
                  ->where('token_type', '=', $type)
                  ->where('user_id', '=', $userID);
        return $sql->execute();
    }

    /**
     * Delete a token by it's value and type
     *
     * @param  [type] $token
     * @param  [type] $type
     * @param  [type] $userID
     * @param  [type] $sessID
     * @return [type]
     */
    public function deleteToken($token, $type, $userID = null, $sessID = null)
    {
        $db = app()->db;
        $table = $this->getTable();
        $sql = $db->delete()
                  ->from($table)
                  ->where('token_value', '=', $token)
                  ->where('token_type', '=', $type);

        if ($userID) {
            $sql = $sql->where('user_id', '=', $userID);
        }

        if ($sessID) {
            $sql = $sql->where('session_id', '=', $sessID);
        }

        return $sql->execute();
    }

    public function clearExpiredTokens($type = null)
    {
        $db = app()->db;
        $table = $this->getTable();
        $sql = $db->delete()->from($table)->where('token_expires', '<', time());

        if (is_string($type)) {
            $sql = $sql->where('token_type', '=', $type);
        }

        return $sql->execute();
    }
}
