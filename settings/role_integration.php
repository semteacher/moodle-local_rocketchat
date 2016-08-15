<?php

require_once('../../../config.php');

require_login();
require_capability('local/rocketchat:view', context_system::instance());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_rocketchat/rocketchat', 'init');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Rocket.Chat");

$PAGE->set_url($CFG->wwwroot.'/local/rocketchat/settings/role_integration.php');

$rocketchatroles = \local_rocketchat\utilities::get_roles();

echo $OUTPUT->header();
echo $OUTPUT->heading('Role Integration');

echo html_writer::tag('h3', 'Roles included in sync');
echo html_writer::start_tag('table', array('class' => 'admintable generaltable', 'id'=>'integrated-roles'));

echo html_writer::start_tag('thead');
echo html_writer::tag('th', 'Course');
echo html_writer::tag('th', 'Requires Sync');
echo html_writer::end_tag('thead');

$systemcontext = context_system::instance();
$roles = role_fix_names(get_all_roles(), $systemcontext, ROLENAME_ORIGINAL);

echo html_writer::start_tag('tbody');

foreach ($roles as $role) {
    foreach ($rocketchatroles as $rocketchatrole) {
        if ($role->id == $rocketchatrole->roleid) {
            echo html_writer::start_tag('tr');
            echo html_writer::tag('td', $role->localname);
            echo html_writer::tag('td',
                html_writer::checkbox('requiresync', null, $rocketchatrole->requiresync, '', array('data-roleid'=> $role->id)));
            echo html_writer::end_tag('tr');
        }
    }
}
echo html_writer::end_tag('tbody');

echo html_writer::end_tag('table');
echo html_writer::tag('p', '* Checked roles will be included in sync. Removing a role will not remove users already uploaded to Rocket.Chat.', array("class" => "form-description"));

echo $OUTPUT->footer();
