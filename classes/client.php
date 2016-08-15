<?php

namespace local_rocketchat;

require_once($CFG->libdir . '/filelib.php');

class client
{
    public $authenticated = false;
    public $host;

    private $authtoken;
    private $userid;
    private $username;
    private $password;

    function __construct() {
        $this->host = get_config('local_rocketchat','host');
        $this->username = get_config('local_rocketchat','username');
        $this->password = get_config('local_rocketchat','password');
        $this->authenticate();
    }

    public function authentication_headers() {
        return array("X-Auth-Token: " . $this->authtoken, 
            "X-User-Id: " . $this->userid);
    }

    private function authenticate() {
        $response = $this->request_login_credentials();
        
        if($response->status == 'success') {
            $this->store_credentials($response->data);            
            $this->authenticated = true;
        }
    }

    private function request_login_credentials() {
        
        $data = "user=" . $this->username . "&" . "password=" . $this->password;
        $url = $this->host . '/api/login';

        $request = new \curl();        
        $request->setHeader("content-type: application/x-www-form-urlencoded");

        $response = $request->post($url, $data);
        $response = json_decode($response);        

        return $response;
    }

    private function store_credentials($data) {
        if(isset($data->authToken) && isset($data->userId)) {
            $this->authtoken = $data->authToken;
            $this->userid = $data->userId;
        }
    }
}