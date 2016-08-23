<?php

namespace local_rocketchat\integration;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/enrol/externallib.php');

class users
{
    public $errors = array();

    private $client;

    function __construct($client) {
        $this->client = $client;
    }

    public function create($rocketchatcourse) {
        $users =  \core_enrol_external::get_enrolled_users($rocketchatcourse->course);
        $users = json_decode(json_encode($users), FALSE );

        foreach ($users as $user) {
            if(!$this->_user_exists($user))
            {
                $this->_create_user($user);
            } 
        }
    }

    public function deactivate_user($userid) {
        $this->_update_user_activity($userid, true);
    }

    public function activate_user($userid) {
        $this->_update_user_activity($userid, false);
    }

    public function get_user($user) {
        $rocketchatuser = $this->_get_user($user->id);
        return $rocketchatuser;
    }

    private function _user_exists($user) {
        foreach ($this->_existing_users() as $existinguser) {
            $username = $user->username;
            if(count(explode('@',$user->email)) > 1) {
                $username = explode('@',$user->email)[0];
            }

            if($username == $existinguser->username) {
                return true;
            }
        }

        return false;
    }

    private function _existing_users() {
        $url = $this->client->host . '/api/v2/users';

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));

        $response = $request->get($url);
        $response = json_decode($response);        

        return $response->users;
    }

    public function _get_user($userid) {
        global $DB;

        $user = $DB->get_record('user', array("id" => $userid));
        
        $username = $user->username;

        if(count(explode('@',$user->email)) > 1) {
            $username = explode('@',$user->email)[0];
        }
        
        $url = $this->client->host . '/api/v2/users?filter[username]=' . $username;
        
        $request = new \curl();        
        $request->setHeader($this->client->authentication_headers());
        
        $response = $request->get($url);
        $response = json_decode($response);        
        
        $rocketchatuser = null;

        if($response->users) {
            $rocketchatuser = $response->users[0];
        }
        return $rocketchatuser;
    }

    private function _create_user($user) {
        $url = $this->client->host . "/api/v2/users";

        $data = array(
            "name" => $user->firstname . " " . $user->lastname,
            "username" => explode('@',$user->email)[0],
            "email" => $user->email,
            "verified" => true,
            "password" => substr(str_shuffle(MD5(microtime())), 0, 6),
            "requirePasswordChange" => true,
            "joinDefaultChannels" => false,
            "sendWelcomeEmail" => true,
            "role" => 'user');

        $request = new \curl();        
        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));
        
        $response = $request->post($url, json_encode($data));
        $response = json_decode($response);        

        if(!$response->success) {
            $object = new \stdClass();
            $object->code = 'Rocket.Chat Integration - user creation';
            $object->error = "[ user_id - " . $user->id . " | email - " . $user->email . "]" . $response->error;

            array_push($this->errors, $object);
        }
    }

    private function _update_user_activity($userid, $isactive) {
        $rocketchatuser = $this->_get_user($userid);

        if($rocketchatuser) {
            $url = $this->client->host . "/api/v2/users/" . $rocketchatuser->_id;
            $data = array("active" => $isactive);

            $request = new \curl();        
            $request->setHeader($this->client->authentication_headers());
            $request->setHeader(array('Content-Type: application/json'));

            $response = $request->post($url, json_encode($data));
            $response = json_decode($response);     
        }
    }
}