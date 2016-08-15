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
        global $CFG, $DB;

        $course = $DB->get_record('course', array("id" => $rocketchatcourse->course));
        $users =  \core_enrol_external::get_enrolled_users($course->id);
        $users = json_decode(json_encode($users), FALSE );

        foreach ($users as $user) {
            $this->_create($user);
        }
    }

    private function _create($user) {
        if(!$this->_user_exists($user))
        {
            $response = $this->_create_user($user);
        } 
    }

    private function _user_exists($user) {
        foreach ($this->_existingusers() as $existinguser) {
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

    private function _existingusers() {
        global $CFG;

        $url = $this->client->host . '/api/v2/users';

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));
        
        $response = $request->get($url);
        $response = json_decode($response);        

        return $response->users;
    }

    private function _create_user($user) {
        global $CFG;

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
}