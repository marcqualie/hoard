<?php

namespace Hoard;
use Model\User;
use MongoId;

class Auth
{

    private $app;

    public $cookie = 'user';
    public $id = null;
    public $user;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function check ()
    {

        $this->cookie = 'u' . crc32($this->cookie . '.' . COOKIE_DOMAIN);

        $cookie = isset($_COOKIE[$this->cookie]) ? $_COOKIE[$this->cookie] : '';
        if (! $cookie) {
            return;
        }
        $data = $this->decrypt($cookie);
        list ($id, $token) = explode(':', $data);
        if (! $id || ! $token) {
            return;
        }
        $user = User::findById(new MongoId($id));
        if (! $user) {
            return;
        }
        if ($user->token !== $token) {
            return;
        }

        // Populate User Data
        $this->id = $user->id;
        $this->user = $user;

    }

    /**
     * Logging in and out
     */
    public function login ($email, $password)
    {
        $user = User::findOne(array(
            'email' => $email
        ));
        if (! $user) {
            return array('error' => 404, 'message' => 'No such user');
        }
        if ($user->password !== $this->password($password)) {
            return array('error' => 401, 'message' => 'Invalid Password');
        }
        $this->login_apply((String) $user->id, $user->token);

        return array(
            'message' => 'Login Success'
        );
    }
    public function login_apply ($uid, $token)
    {
        $data = $this->encrypt($uid . ':' . $token);
        $_COOKIE[$this->cookie] = $data;
        setcookie($this->cookie, $data, 0, '/', COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTP);
    }
    public function logout ()
    {
        unset($_COOKIE[$this->cookie]);
        setcookie($this->cookie, false, time() / 2, '/', COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTP);
    }

    /**
     * Encryption
     */
    public function encrypt ($str)
    {
        $token = base64_encode($str);

        return $token;
    }
    public function decrypt ($token)
    {
        $str = base64_decode($token);

        return $str;
    }

    /**
     * Verify application keys when writing and reading data
     */
    public static function verify_appkey ($appkey, $token, $sig)
    {
        return false;
    }

    /**
     * Password generator
     */
    public static function password ($str)
    {
        return sha1(md5(sha1(md5($str))));
    }

    /**
     * Permissions
     */
    public function isAdmin ()
    {
        return $this->user->admin === 1 ? true : false;
    }

}
