<?php

namespace local_rocketchat\events\observers;

class user_enrolment_updated {
    public static function call($event) {
        $reflection = new \ReflectionClass($event);
        $property = $reflection->getProperty('data');
        $property->setAccessible(true);
        $data = $property->getValue($event);
        
        if($data && $data['objecttable'] == "user_enrolments") {
            $userenrolmentid = $data['objectid'];
            $sync = new \local_rocketchat\sync($userenrolmentid);
            $sync->sync_enrolment_status($userenrolmentid); 
        }
    } 
}