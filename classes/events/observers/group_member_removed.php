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
 * Observer to remove subscription for Rocket.Chat integration.
 *
 * @package     local_rocketchat
 * @copyright   2016 GetSmarter {@link http://www.getsmarter.co.za}
 * @author      2019 Adrian Perez <p.adrian@gmx.ch> {@link https://adrianperez.me}
 * @license     MIT License
 */

namespace local_rocketchat\events\observers;

defined('MOODLE_INTERNAL') || die;

class group_member_removed {

    /**
     * @param $event
     * @throws \ReflectionException
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if (self::_is_event_based_sync($data['courseid'])) {
            self::_remove_subscription($data);
        }
    }

    /**
     * @param $courseid
     * @return bool
     * @throws \dml_exception
     */
    private static function _is_event_based_sync($courseid) {
        global $DB;

        $rocketchatcourse = $DB->get_record('local_rocketchat_courses', array('course' => $courseid));
        return $rocketchatcourse ? $rocketchatcourse->eventbasedsync : false;
    }

    /**
     * @param $data
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private static function _remove_subscription($data) {
        global $DB;

        $group = $DB->get_record('groups', array('id' => $data['objectid']));
        $user = $DB->get_record('user', array('id' => $data['relateduserid']));

        $client = new \local_rocketchat\client();

        if ($client->authenticated) {
            $subscriptionapi = new \local_rocketchat\integration\subscriptions($client);
            $subscriptionapi->remove_subscription_for_user($user, $group);
        }
    }
}
