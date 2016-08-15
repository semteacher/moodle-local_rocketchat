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

//We defined the web service functions to install.
$functions = array(
    'local_rocketchat_set_rocketchat_course_sync' => array(
        'classname'   => 'local_rocketchat_external',
        'methodname'  => 'set_rocketchat_course_sync',
        'classpath'   => 'local/rocketchat/externallib.php',
        'description' => 'Sets whether or not a course should integrate with rocket chat.',
        'type'        => 'update'
        ),
    'local_rocketchat_set_rocketchat_role_sync' => array(
        'classname'   => 'local_rocketchat_external',
        'methodname'  => 'set_rocketchat_role_sync',
        'classpath'   => 'local/rocketchat/externallib.php',
        'description' => 'Sets whether or not a role should integrate with rocket chat.',
        'type'        => 'update'
        ),
    'local_rocketchat_manually_trigger_sync' => array(
        'classname'   => 'local_rocketchat_external',
        'methodname'  => 'manually_trigger_sync',
        'classpath'   => 'local/rocketchat/externallib.php',
        'description' => 'Syncs course with rocket chat.',
        'type'        => 'update'
        )
    );

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Rocket Chat Web Services' => array(
        'functions' => array (
            'local_rocketchat_set_rocketchat_course_sync',
            'local_rocketchat_set_rocketchat_role_sync',
            'local_rocketchat_manually_trigger_sync'),
        'restrictedusers' => 0,
        'enabled'=>1
        )
    );
