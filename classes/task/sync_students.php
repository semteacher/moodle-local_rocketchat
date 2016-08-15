<?php

namespace local_rocketchat\task;

class sync_students extends \core\task\scheduled_task {

    private $currenttime = 0;

    public function get_name() {
        return get_string('syncstudents', 'local_rocketchat');
    }

    public function execute() {
        global $DB;

        $this->currenttime = time();

        if($this->get_last_run_time() > 0) {
            $sync = new \local_rocketchat\sync();
            $sync->sync_pending_courses();
        }

        $this->set_last_run_time($this->currenttime);
    }
}
