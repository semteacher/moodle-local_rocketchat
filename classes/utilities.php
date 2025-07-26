<?php
// This file is part of Moodle - http://moodle.org/
//
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
 * Helper functions for Rocket.Chat integration
 *
 * @package     local_rocketchat
 * @copyright   2016 GetSmarter {@link http://www.getsmarter.co.za}
 * @author      2019 Adrian Perez <me@adrianperez.me> {@link https://adrianperez.me}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_rocketchat;

/**
 * Class with general helper methods.
 */
class utilities {

    /**
     * The API client instance.
     *
     * @var client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param $client
     */
    public function __construct($client) {
        $this->client = $client;
    }

    /**
     * Update helper entry with pending sync status.
     *
     * @param $courseid
     * @param int $pendingsync
     * @throws \dml_exception
     */
    public static function set_rocketchat_course_sync($courseid, $pendingsync = 0) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', ['course' => $courseid]);

        if ($rocketchatcourse) {
            $rocketchatcourse->pendingsync = $pendingsync;
            $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
        } else {
            $$rocketchatcourse = [];
            $rocketchatcourse['course'] = $courseid;
            $rocketchatcourse['pendingsync'] = $pendingsync;
            $DB->insert_record('local_rocketchat_courses', $rocketchatcourse);
        }
    }

    /**
     * Update helper entry with role sync status.
     *
     * @param $roleid
     * @param int $requiresync
     * @throws \dml_exception
     */
    public static function set_rocketchat_role_sync($roleid, $requiresync=0) {
        global $DB;
        $rocketchatrole = $DB->get_record('local_rocketchat_roles', ['role' => $roleid]);

        if ($rocketchatrole) {
            $rocketchatrole->requiresync = $requiresync;
            $DB->update_record('local_rocketchat_roles', $rocketchatrole);
        } else {
            $$rocketchatrole = [];
            $rocketchatrole['role'] = $roleid;
            $rocketchatrole['requiresync'] = $requiresync;
            $DB->insert_record('local_rocketchat_roles', $rocketchatrole);
        }
    }

    /**
     * Update helper entry with event based sync status.
     *
     * @param $courseid
     * @param int $eventbasedsync
     * @throws \dml_exception
     */
    public static function set_rocketchat_event_based_sync($courseid, $eventbasedsync = 0) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', ['course' => $courseid]);

        if ($rocketchatcourse) {
            $rocketchatcourse->eventbasedsync = $eventbasedsync;
            $DB->update_record('local_rocketchat_courses', $rocketchatcourse);
        } else {
            $$rocketchatcourse = [];
            $rocketchatcourse['course'] = $courseid;
            $rocketchatcourse['eventbasedsync'] = $eventbasedsync;
            $DB->insert_record('local_rocketchat_courses', $rocketchatcourse);
        }
    }

    /**
     * Get all courses to process.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_courses() {
        global $DB;

        $query = '
            SELECT
                c.id courseid,
                CASE WHEN lrc.id IS NULL THEN 0 ELSE lrc.eventbasedsync END eventbasedsync,
                CASE WHEN lrc.id IS NULL THEN 0 ELSE lrc.pendingsync END pendingsync,
                lrc.lastsync,
                lrc.error
            FROM
                {course} c

            LEFT JOIN {local_rocketchat_courses} lrc ON
                lrc.course = c.id
        ';

        $courses = $DB->get_records_sql($query);

        return $courses;
    }

    /**
     * Get all roles to process.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_roles() {
        global $DB;

        $query = '
            SELECT
                r.id roleid,
                CASE WHEN lrr.id IS NULL THEN 0 ELSE lrr.requiresync END requiresync
            FROM
                {role} r

            LEFT JOIN {local_rocketchat_roles} lrr ON
                lrr.role = r.id;
        ';

        $roles = $DB->get_records_sql($query);

        return $roles;
    }

    /**
     * Map data from event to be accessible.
     *
     * @param $obj
     * @param $prop
     * @return mixed
     * @throws \ReflectionException
     */
    public static function access_protected($obj, $prop) {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    /**
     * Run API request.
     *
     * @param $url
     * @param $api
     * @param string $method
     * @param null $data
     * @param null $header
     * @return bool|mixed
     * @throws \dml_exception
     */
    public static function make_request($url, $api, $method, $data = null, $header = null) {
        $request = new \curl();

        if (!empty($header)) {
            $request->setHeader($header);
        }

        $url = $url . $api;

        if (isset($data)) {
            $data = json_encode($data);
        };

        if ($method == 'post') {
            if (isset($data)) {
                $response = $request->post($url, $data);
            } else {
                $response = $request->post($url);
            }
        } else if ($method == 'get') {
            if (isset($data)) {
                $response = $request->get($url, $data);
            } else {
                $response = $request->get($url);
            }
        } else {
            $response = $request->delete($url);
        }

        $response = json_decode($response);

        return $response;
    }

    /**
     * Checks if users can link their Rocket.Chat account.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_external_connection_allowed(): bool {
        if (get_config('local_rocketchat', 'allowexternalconnection')) {
            return true;
        }

        return false;
    }

    /**
     * Gets all channels and status from user data.
     *
     * @param $data
     * @return array
     * @throws \dml_exception
     */
    public static function get_user_and_group_by_event_data($data): array {
        global $DB;

        $user = $DB->get_record('user', [
                'id' => $data['relateduserid'],
        ]);

        $group = $DB->get_record('groups', [
                'id' => $data['objectid'],
        ]);

        return [$user, $group];
    }
}
