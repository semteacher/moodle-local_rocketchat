<?php

namespace local_rocketchat\events\observers;

class user_enrolment_updated {
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if(self::_is_event_based_sync($data['courseid'])) {
            self::_sync_enrolment_status($data['objectid']); 
        }        
    } 

    private static function _is_event_based_sync($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));
        return $rocketchatcourse ? $rocketchatcourse->eventbasedsync : false;
    }

    private static function _sync_enrolment_status($userenrolmentid) {
        global $DB;

        $client = new \local_rocketchat\client();

        if($client->authenticated) {        
            $userenrolment = $DB->get_record('user_enrolments', array("id" => $userenrolmentid));

            $userapi = new \local_rocketchat\integration\users($client);

            if($userenrolment->status == "1") {
                $userapi->activate_user($userenrolment->userid);
            } 
            else {
                $userapi->deactivate_user($userenrolment->userid);
            }
        }
    }
}