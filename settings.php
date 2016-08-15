<?php

defined('MOODLE_INTERNAL') || die;

// Add a category to the Site Admin menu
$ADMIN->add('root', new admin_category('local_rocketchat', get_string('pluginname', 'local_rocketchat')));

// Add Rocket.Chat API settings
$settingspage = new admin_settingpage('local_rocketchat_general',  'Settings', 'moodle/site:config');

$settingspage->add(new admin_setting_heading('local_rocketchat/heading', 'Rocket.Chat API', ''));
$settingspage->add(new admin_setting_configtext('local_rocketchat/host', 'Host', 'Host url', ''));
$settingspage->add(new admin_setting_configtext('local_rocketchat/username', 'Username', 'Username for accessing the API', ''));
$settingspage->add(new admin_setting_configpasswordunmask('local_rocketchat/password', 'Password', 'Password for accessing the API', ''));
$settingspage->add(new admin_setting_configtextarea('local_rocketchat/groupregex', "Group Regex Filters", "Used for matching which groups to sync to Rocket.Chat 
    <br> Refer to https://regex101.com for regex matching
    <br> e.g. <br> /coach group [a-z][0-9]/
    <br>/example project group [0-9][0-9]/", ''));

$ADMIN->add('local_rocketchat', $settingspage);

$ADMIN->add('local_rocketchat', new admin_externalpage('local_rocketchat_course_integration',  'Course Integration', '/local/rocketchat/settings/course_integration.php', 'local/rocketchat:view'));
$ADMIN->add('local_rocketchat', new admin_externalpage('local_rocketchat_role_integration',  'Role Integration', '/local/rocketchat/settings/role_integration.php', 'local/rocketchat:view'));
