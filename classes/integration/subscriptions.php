<?php

namespace local_rocketchat\integration;

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/grouplib.php');

class subscriptions
{
    public $errors = array();

    private $client;
    private $channelapi;

    function __construct($client) {
        $this->client = $client;
        $this->channelapi = new \local_rocketchat\integration\channels($this->client);
        $this->userapi = new \local_rocketchat\integration\users($this->client);
    }

    public function add_subscriptions_for_course($course) {
        global $DB;

        $groups = $DB->get_records('groups', array("courseid" => $course->id));

        foreach ($groups as $group) {
            $this->add_subscriptions_for_group($group, $course);
        }
    }

    public function add_subscriptions_for_group($group, $course) {
        $users = groups_get_members($group->id);
        $users = json_decode(json_encode($users), FALSE );

        foreach ($users as $user) {
            $this->add_subscription_for_user($user, $group, $course);
        }
    }

    public function add_subscription_for_user($user, $group) {
        $rocketchatchannel = $this->channelapi->get_channel_for_group($group);
        $rocketchatuser = $this->userapi->get_user($user);

        $subscription = $this->get_subscription($user, $group);

        if($rocketchatuser && !$subscription && $rocketchatchannel) {
            $url = $this->client->host . "/api/v2/subscriptions";
            $data = array("username" => explode('@',$user->email)[0], 
                "rid" => $rocketchatchannel->_id);

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

    public function remove_subscription_for_user($user, $group) {
        $subscription = $this->get_subscription($user, $group);

        if($subscription) {
            $url = $this->client->host . "/api/v2/subscriptions/" . $subscription->_id;

            $request = new \curl();        
            $request->setHeader($this->client->authentication_headers());
            
            $response = $request->delete($url);
            $response = json_decode($response);        

            if(!$response->success) {
                $object = new \stdClass();
                $object->code = 'Rocket.Chat Integration - subscription creation';
                $object->error = $response->error;

                array_push($this->errors, $object);
            }
        }
    }

    public function get_subscription($user, $group) {

        $rocketchatchannel = $this->channelapi->get_channel_for_group($group);
        $rocketchatuser = $this->userapi->get_user($user);

        $rocketchatsubscription = null;

        if($rocketchatchannel && $rocketchatuser) {
            $url = $this->client->host . "/api/v2/subscriptions?filter[userId]=" . $rocketchatuser->_id . "&filter[roomId]=" . $rocketchatchannel->_id;

            $request = new \curl();        
            $request->setHeader($this->client->authentication_headers());
            $response = $request->get($url);
            $response = json_decode($response);        

            if($response->subscriptions) {
                $rocketchatsubscription = $response->subscriptions[0];
            }
        }
        
        return $rocketchatsubscription;
    }
}