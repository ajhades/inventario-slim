<?php

class TokenAuth extends \Slim\Middleware {

    public function __construct() {
        
    }

    /**
     * Deny Access
     *
     */
    public function deny_access() {
        $res = $this->app->response();
        $res->status(401);
    }

    /**
     * Check against the DB if the token is valid
     * 
     * @param string $token
     * @return bool
     */
    public function authenticate($token) {
        return validateToken($token);
    }

    /**
     * Call
     *
     */
    public function call() {
        $app = \Slim\Slim::getInstance();
        //Get the token sent from jquery
        $tokenAuth = $app->request()->headers()->get('Authorization');
        
        //Check if our token is valid
        if ($this->authenticate($tokenAuth)) {
            //Get the user and make it available for the controller
           $usrObj = getUserByToken($tokenAuth);
            $this->app->auth_user = $usrObj;
            //Update token's expiration
            keepTokenAlive($tokenAuth);
            //Continue with execution
            $this->next->call();
        } else {
            $this->deny_access();
        }
    }

}