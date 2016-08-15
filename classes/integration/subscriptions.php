<?php

namespace local_rocketchat\integration;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/grouplib.php');

class subscriptions
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

            $channelname = $course->shortname . "-" . str_replace(" ", "_", $group->name);
            $channelapi = new \local_rocketchat\integration\channels($this->client);        
            $rocketchatchannel = $channelapi->get_channel($channelname);
            $roomid = $rocketchatchannel->_id;
            $members = groups_get_members($group->id);
            $members = json_decode(json_encode($members), FALSE );

            foreach ($members as $member) {
                $this->_create_subscription($member, $roomid);
            }
        }
    }

    private function _create_subscription($user, $roomid) {
        global $CFG;

        $url = $this->client->host . "/api/v2/subscriptions";
        $data = array("username" => explode('@',$user->email)[0], 
            "rid" => $roomid);

        $request = new \curl();        

        $request->setHeader($this->client->authentication_headers());
        $request->setHeader(array('Content-Type: application/json'));

        $response = $request->post($url, json_encode($data));
        $response = json_decode($response);        

        if(!$response->success) {
            $object = new \stdClass();
            $object->code = 'Rocket.Chat Integration - subscription creation';
            $object->error = $response->error;

            array_push($this->errors, $object);
        }
    }
}