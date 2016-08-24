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
* Web service local plugin template external functions and service definitions.
*
* @package    local_rocketchat
* @copyright  2016 GetSmarter {@link http://www.getsmarter.co.za}
* @license    MIT License
*/
$observers = array(
    array(
        'eventname'   => '\core\event\user_enrolment_updated',
        'callback'    => '\local_rocketchat\events\observers\user_enrolment_updated::call'
        ),
    array(
        'eventname'   => '\core\event\group_member_added',
        'callback'    => '\local_rocketchat\events\observers\group_member_added::call'
        ),
    array(
        'eventname'   => '\core\event\group_member_removed',
        'callback'    => '\local_rocketchat\events\observers\group_member_removed::call'
        ),
    array(
        'eventname'   => '\core\event\user_enrolment_created',
        'callback'    => '\local_rocketchat\events\observers\user_enrolment_created::call'
        )
    );

