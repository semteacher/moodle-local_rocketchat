<?php

namespace local_rocketchat;

class sync
{
    private $client;
    private $errors = array();

    function __construct() {
        $this->client = new \local_rocketchat\client();
    } 

    public function sync_pending_courses() {
        global $DB;

        $rocketchatcourses = $DB->get_records('local_rocketchat_courses', array("pendingsync"=>true), '', '*');

        foreach ($rocketchatcourses as $rocketchatcourse) {
            $this->sync_pending_course($rocketchatcourse->course);
        }
    }

    public function sync_pending_course($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array("course" => $courseid));

        if(!$rocketchatcourse) {
            $rocketchatcourseid = $this->create_rocketchat_course($courseid);
            $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array("id" => $rocketchatcourseid));            
        }

        $this->_run_sync($rocketchatcourse);
        $this->_record_result($rocketchatcourse);
    }

    private function create_rocketchat_course($courseid) {
        global $DB;

        $rocketchatcourse = array();
        $rocketchatcourse['course'] = $courseid;
        $rocketchatcourse['pendingsync'] = true;
        $rocketchatcourseid = $DB->insert_record('local_rocketchat_courses', $rocketchatcourse);

        return $rocketchatcourseid;
    }
    
    private function _run_sync($rocketchatcourse) {
        error_log("running sync for  " . $rocketchatcourse->course);
        // if($this->client->authenticated) {
        //     $channelapi = new \local_rocketchat\integration\channels($this->client);
        //     $channelapi->create($rocketchatcourse);
        //     $this->errors = array_merge($this->errors, $channelapi->errors);

        //     $userapi = new \local_rocketchat\integration\users($this->client);
        //     $userapi->create($rocketchatcourse);
        //     $this->errors = array_merge($this->errors, $userapi->errors);

        //     $subscriptionapi = new \local_rocketchat\integration\subscriptions($this->client);
        //     $subscriptionapi->create($rocketchatcourse);
        //     $this->errors = array_merge($this->errors, $subscriptionapi->errors);
        // } 
        // else {
        //     $object = new \stdClass();
        //     $object->code = 'Rocket.Chat Integration - authentication failure';
        //     $object->error = 'Failed to establish a client connection with the Rocket.Chat server';
        //     array_push($this->errors, $object);
        // }
    }

    private function _record_result($rocketchatcourse) {
        if(count($this->errors) == 0) {
            $this->_pass_sync($rocketchatcourse);
        } 
        else {
            $this->_fail_sync($rocketchatcourse);
        }

        $this->_reset_errors();
    }

    private function _pass_sync($rocketchatcourse) {
        global $DB;

        $rocketchatcourse->pendingsync = 0;
        $rocketchatcourse->lastsync = time();
        $rocketchatcourse->error = null;

        $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
    }

    private function _fail_sync($rocketchatcourse) {
        global $DB;

        $errorstring = "";
        foreach ($this->errors as $error) {
            $errorstring = $errorstring . "[" . $error->code  . "] " . $error->error . "\r\n";
        }

        $rocketchatcourse->pendingsync = 0;
        $rocketchatcourse->lastsync = time();
        $rocketchatcourse->error = $errorstring;

        $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
    }

    private function _reset_errors() {
        $this->errors = array();
    }
}