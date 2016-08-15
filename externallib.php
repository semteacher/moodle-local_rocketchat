<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
* External Web Service Template
*
* @package    getsmarter
* @copyright  2016 GetSmarter {@link http://www.getsmarter.co.za}
* @license    MIT License
*/
require_once($CFG->libdir . "/externallib.php");

class local_rocketchat_external extends external_api {
    /**
    * Returns description of method parameters
    * @return external_function_parameters
    */
    public static function set_rocketchat_course_sync_parameters() {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_TEXT, 'The course id', VALUE_DEFAULT, NULL),
                'pendingsync' => new external_value(PARAM_BOOL, 'Highlights if a course is pending sync', VALUE_DEFAULT, false))
            );
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function set_rocketchat_course_sync($courseid, $pendingsync) {
        $params = self::validate_parameters(self::set_rocketchat_course_sync_parameters(),
            array('courseid' => $courseid, 'pendingsync' => $pendingsync)
            );

        \local_rocketchat\utilities::set_rocketchat_course_sync($courseid, $pendingsync);

        return "The courseid is - {$courseid}  / The pendingsync {$pendingsync}";
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function set_rocketchat_course_sync_returns() {
        return new external_value(PARAM_TEXT, 'Whether or not the update was successful or not');
    }

    /**
    * Can this function be called directly from ajax?
    *
    * @return boolean
    * @since Moodle 2.9
    */
    public static function set_rocketchat_course_sync_is_allowed_from_ajax() {
        return true;
    }

    /**
    * Returns description of method parameters
    * @return external_function_parameters
    */

    public static function set_rocketchat_role_sync_parameters() {
        return new external_function_parameters(
            array('roleid' => new external_value(PARAM_TEXT, 'The role id', VALUE_DEFAULT, NULL),
                'requiresync' => new external_value(PARAM_BOOL, 'The requires sync boolean', VALUE_DEFAULT, false))
            );
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function set_rocketchat_role_sync($roleid, $requiresync) {
        $params = self::validate_parameters(self::set_rocketchat_role_sync_parameters(),
            array('roleid' => $roleid, 'requiresync' => $requiresync)
            );

        \local_rocketchat\utilities::set_rocketchat_role_sync($roleid, $requiresync);

        return "The roleid is - {$roleid}  / The requiresync {$requiresync}";
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function set_rocketchat_role_sync_returns() {
        return new external_value(PARAM_TEXT, 'Whether or not the update was successful or not');
    }

    /**
    * Can this function be called directly from ajax?
    *
    * @return boolean
    * @since Moodle 2.9
    */
    public static function set_rocketchat_role_sync_is_allowed_from_ajax() {
        return true;
    }

    /**
    * Returns description of method parameters
    * @return external_function_parameters
    */    
    public static function manually_trigger_sync_parameters() {
        return new external_function_parameters(array('courseid' => new external_value(PARAM_TEXT, 'The course id', VALUE_DEFAULT, NULL)));
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function manually_trigger_sync($courseid) {
        $params = self::validate_parameters(self::manually_trigger_sync_parameters(),
            array('courseid' => $courseid)
            );
        $sync = new \local_rocketchat\sync();
        $sync->sync_pending_course($courseid);

        return "success";
    }

    /**
    * Returns description of method result value
    * @return external_description
    */
    public static function manually_trigger_sync_returns() {
        return new external_value(PARAM_TEXT, 'Whether or not the update was successful or not');
    }

    /**
    * Can this function be called directly from ajax?
    *
    * @return boolean
    * @since Moodle 2.9
    */
    public static function manually_trigger_sync_is_allowed_from_ajax() {
        return true;
    }
}
