<?php

namespace local_rocketchat\events\observers;

class group_member_removed {
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if(self::_is_event_based_sync($data['courseid'])) {
            self::_remove_subscription($data);
        }        
    } 

    private static function _is_event_based_sync($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));
        return $rocketchatcourse ? $rocketchatcourse->eventbasedsync : false;
    }

    private static function _remove_subscription($data) {
        global $DB;

        $course = $DB->get_record('course', array('id'=>$data['courseid']));
        $group = $DB->get_record('groups', array('id'=>$data['objectid']));
        $user = $DB->get_record('user', array('id'=>$data['relateduserid']));

        $client = new \local_rocketchat\client();

        if($client->authenticated) {        
            $subscriptionapi = new \local_rocketchat\integration\subscriptions($client);
            $subscriptionapi->remove_subscription_for_user($user, $group); 
        }
    }
}