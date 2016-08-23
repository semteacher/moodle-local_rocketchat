<?php

namespace local_rocketchat;

class utilities
{
    public static function set_rocketchat_course_sync($courseid, $pendingsync=0) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));

        if($rocketchatcourse) {
            $rocketchatcourse->pendingsync = $pendingsync;
            $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
        } 
        else {
            $$rocketchatcourse = array();
            $rocketchatcourse['course'] = $courseid;
            $rocketchatcourse['pendingsync'] = $pendingsync;
            $DB->insert_record('local_rocketchat_courses', $rocketchatcourse);
        }
    }

    public static function set_rocketchat_role_sync($roleid, $requiresync=0) {
        global $DB;
        $rocketchatrole = $DB->get_record('local_rocketchat_roles', array('role'=>$roleid));

        if($rocketchatrole) {
            $rocketchatrole->requiresync = $requiresync;
            $DB->update_record('local_rocketchat_roles', $rocketchatrole);
        } else {
            $$rocketchatrole = array();
            $rocketchatrole['role'] = $roleid;
            $rocketchatrole['requiresync'] = $requiresync;
            $DB->insert_record('local_rocketchat_roles', $rocketchatrole);
        }
    }

    public static function set_rocketchat_event_based_sync($courseid, $eventbasedsync=0) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course'=>$courseid));

        if($rocketchatcourse) {
            $rocketchatcourse->eventbasedsync = $eventbasedsync;
            $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
        } 
        else {
            $$rocketchatcourse = array();
            $rocketchatcourse['course'] = $courseid;
            $rocketchatcourse['eventbasedsync'] = $eventbasedsync;
            $DB->insert_record('local_rocketchat_courses', $rocketchatcourse);
        }
    }    

    public static function get_courses(array $query=array()) {
        global $DB;

        $courses = $DB->get_records_sql("
            SELECT
                c.id courseid,
                case when lrc.id is null then 0 else lrc.eventbasedsync end eventbasedsync,
                case when lrc.id is null then 0 else lrc.pendingsync end pendingsync,
                lrc.lastsync,
                lrc.error
            FROM
                mdl_course c

            LEFT JOIN mdl_local_rocketchat_courses lrc on
                lrc.course = c.id;");

        return $courses;
    }

    public static function get_roles() {
        global $DB;

        $roles = $DB->get_records_sql("
            SELECT
            r.id roleid,
            CASE WHEN lrr.id IS NULL THEN 0 ELSE lrr.requiresync END requiresync
            FROM
            mdl_role r

            LEFT JOIN mdl_local_rocketchat_roles lrr ON
            lrr.role = r.id;");

        return $roles;
    }

    public static function access_protected($obj, $prop) {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}
