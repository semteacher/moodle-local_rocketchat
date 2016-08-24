<?php

namespace local_rocketchat\events\observers;

class user_enrolment_created {
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if(self::_is_event_based_sync($data['courseid'])) {
            self::_sync_user($data['relateduserid']); 
        }        
    } 

    private static function _is_event_based_sync($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));
        return $rocketchatcourse->eventbasedsync;
    }

    private static function _sync_user($userid) {
        global $DB;

        $client = new \local_rocketchat\client();

        if($client->authenticated) {        
            $user = $DB->get_record('user', array("id" => $userid));

            $userapi = new \local_rocketchat\integration\users($client);

            if(!$userapi->user_exists($user)) {
                $userapi->create_user($user);
            }
        }
    }
}