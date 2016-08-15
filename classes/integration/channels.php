<?php

namespace local_rocketchat\integration;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/group/externallib.php');

class channels 
{
    public $errors = array();
    
    private $client;

    function __construct($client) {
        $this->client = $client;
    }

    public function create($rocketchatcourse) {
        global $CFG, $DB;

        $course = $DB->get_record('course', array("id" => $rocketchatcourse->course));
        $groups = $DB->get_records('groups', array("courseid" => $rocketchatcourse->course));

        foreach ($groups as $group) {
            if($this->_group_requires_rocketchat_channel($group)) {
                $channelname = $course->shortname . "-" . str_replace(" ", "_", $group->name);
                $this->_create($channelname);
            }
        }
    }

    public function get_channel($name) {
        $url = $this->client->host . '/api/v2/rooms?filter[name]=' . $name;

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());

        $response = $request->get($url);
        $response = json_decode($response);           

        if (count($response->groups) == 1) {
            return $response->groups[0];
        } 
    }

    private function _create($channel) {
        if(!$this->_channel_exists($channel)) {
            $this->_create_channel($channel);
        }
    }

    private function _channel_exists($channelname) {
        foreach ($this->_existing_channels() as $channel) {
            if($channel->name == $channelname) {
                return true;
            }
        }

        return false;
    }

    private function _existing_channels() {
        global $CFG;

        $url = $this->client->host . '/api/v2/rooms/private';

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));

        $response = $request->get($url);
        $response = json_decode($response);        

        return $response->rooms;
    }

    private function _create_channel($channel) {
        global $CFG;

        $url = $this->client->host . "/api/v2/rooms";
        $data = array("name"=>$channel, "type"=>"p");

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));

        $response = $request->post($url, json_encode($data));
        $response = json_decode($response); 

        if(!$response->success) {
            $object = new \stdClass();
            $object->code = 'Rocket.Chat Integration - channel creation';
            $object->error = $response->error;

            array_push($this->errors, $object);
        }
    }

    private function _group_requires_rocketchat_channel($group) {
        global $CFG;

        $groupregextext = get_config('local_rocketchat', 'groupregex');
        $groupregexs = explode("\r\n", $groupregextext);


        foreach ($groupregexs as $regex) {
            if (preg_match($regex, $group->name, $match)) {
                return true;
            }
        }

        return false;
    }
}