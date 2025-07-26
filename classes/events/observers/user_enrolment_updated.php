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

use local_rocketchat\client;
use local_rocketchat\integration\users;
use local_rocketchat\sync;
use local_rocketchat\utilities;

/**
 * Handles when user enrolment is updated.
 */
class user_enrolment_updated {

    /**
     * Main method call.
     *
     * @param $event
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function call($event) {
        $data = utilities::access_protected($event, 'data');

        if (sync::is_event_based_sync_on_course($data['courseid'])) {
            self::sync_enrolment_status($data['objectid']);
        }
    }

    /**
     * Update user.
     *
     * @param $userenrolmentid
     * @throws \dml_exception
     */
    private static function sync_enrolment_status($userenrolmentid) {
        $client = new client();
        if (!$client->authenticated) {
            return;
        }

        $userapi = new users($client);
        $userapi->update_user_activity($userenrolmentid);
    }
}
