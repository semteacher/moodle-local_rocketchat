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
 * Observer to sync subscription status for Rocket.Chat integration.
 *
 * @package     local_rocketchat
 * @copyright   2016 GetSmarter {@link http://www.getsmarter.co.za}
 * @author      2019 Adrian Perez <me@adrianperez.me> {@link https://adrianperez.me}
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_rocketchat\events\observers;

defined('MOODLE_INTERNAL') || die;

class user_enrolment_updated {

    /**
     * @param $event
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function call($event) {
        $data = \local_rocketchat\utilities::access_protected($event, 'data');

        if (\local_rocketchat\sync::is_event_based_sync_on_course($data['courseid'])) {
            self::sync_enrolment_status($data['objectid']);
        }
    }

    /**
     * @param $userenrolmentid
     * @throws \dml_exception
     */
    private static function sync_enrolment_status($userenrolmentid) {
        global $DB;

        $client = new \local_rocketchat\client();
        if (!$client->authenticated) {
            return;
        }

        $userapi = new \local_rocketchat\integration\users($client);
        $userenrolment = $DB->get_record('user_enrolments', ['id' => $userenrolmentid]);

        $isactive = false;
        if ($userenrolment->status !== '1') {
            $isactive = true;
        }

        $userapi->update_user_activity($userenrolment->userid, $isactive);
    }
}
