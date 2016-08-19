<?php

require_once('../../../config.php');

require_login();
require_capability('local/rocketchat:view', context_system::instance());

$PAGE->requires->jquery();
$PAGE->requires->js_call_amd('local_rocketchat/rocketchat', 'init');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Rocket.Chat");

$PAGE->set_url($CFG->wwwroot.'/local/rocketchat/settings/course_integration.php');

$rocketchatenabledcourses = \local_rocketchat\utilities::get_courses();

echo $OUTPUT->header();
echo $OUTPUT->heading('Course Integration');

echo html_writer::tag('p', 'Manage integration between Moodle and Rocket.Chat. Specify which users and courses require Rocket.Chat integration and manually trigger sync.');

$courses = get_courses();

echo html_writer::tag('h3', 'Integrated courses');

echo html_writer::start_tag('table', array('class' => 'admintable generaltable', 'id'=>'integrated-courses'));

echo html_writer::start_tag('thead');
echo html_writer::tag('th', 'Course');
echo html_writer::tag('th', 'Pending Sync');
echo html_writer::tag('th', 'Last Sync Date');
echo html_writer::end_tag('thead');

echo html_writer::start_tag('tbody');

foreach ($courses as $course) {
    $isrocketchatcourse = false;
    $rocketchatcourse = null;

    foreach ($rocketchatenabledcourses as $rocketchatcourse) {
        if ($course->id == $rocketchatcourse->courseid) {
            echo html_writer::start_tag('tr');
            echo html_writer::start_tag('td');
            $courseurl =  new moodle_url($CFG->wwwroot . '/course/view.php', array('id'=>$course->id));
            echo html_writer::tag('a', $course->fullname, array('href' => $courseurl));
            echo html_writer::end_tag('td');

            echo html_writer::start_tag('td');
            echo html_writer::checkbox('pendingsync', null, $rocketchatcourse->pendingsync, '', array('data-courseid'=> $course->id));
            echo html_writer::end_tag('td');

            echo html_writer::start_tag('td');

            if($rocketchatcourse->lastsync) {
                $alert = ($rocketchatcourse->error) ? 'alert-danger' : 'alert-success';

                echo html_writer::start_tag('div', array('style' => 'margin-bottom: 0', 'class'=>'alert ' . $alert));
                echo userdate($rocketchatcourse->lastsync, '%Y/%m/%d, %H:%M');
                
                if($rocketchatcourse->error) {
                    echo html_writer::tag('span', ' ...', array('title' => $rocketchatcourse->error));
                } 

                echo html_writer::end_tag('div');
            }

            echo html_writer::end_tag('td');        
            echo html_writer::start_tag('td');
            echo html_writer::tag("button", "Manual Sync", 
                array("type"=>"button", 
                    "class"=>"btn btn-default btn-xs", 
                    "id"=>"manual-sync", 
                    "data-courseid"=>$course->id,
                    "style"=>"margin-bottom: 0"));
            echo html_writer::end_tag('td');

            echo html_writer::end_tag('tr');
        }
    }

}
echo html_writer::end_tag('tbody');

echo html_writer::end_tag('table');
echo html_writer::tag('p', '* Courses pending sync will be sync\'d to rocket chat via the cron in the background</p>', array("class", "form-description"));
echo html_writer::tag('p', '* Manual execution will be run immediately',  array('class' => 'form-description'));

echo $OUTPUT->footer();
