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
 * Course resume ltool lib test cases defined.
 *
 * @package   ltool_resume
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL') || die(' No direct access ');

/**
 * Note subplugin for learningtools phpunit test cases defined.
 */
class ltool_resume_testcase extends advanced_testcase {

    /**
     * Create custom page instance and set admin user as loggedin user.
     *
     * @return void
     */
    public function setup(): void {
        global $PAGE, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generator = $this->getDataGenerator();
        $this->user = $this->generator->create_user();
        $course = $this->generator->create_course();
        $cm = $this->generator->create_module('quiz', array('course' => $course->id));
        $cmcontext = \context_module::instance($cm->cmid);
        $PAGE->set_context($cmcontext);
        $PAGE->set_course($course);
        $PAGE->set_cm($cm);
        $PAGE->set_title('Course 1: Quiz test 1');
        $PAGE->set_pagelayout('standard');
        $PAGE->set_url(new moodle_url('/mod/quiz/view.php', ['id' => $cm->id]));
    }

    /**
     * Test store_user_access_data
     */
    public function test_ltool_resumecourse_store_user_access_data() {
        global $DB;
        $this->setUser($this->user);
        ltool_resumecourse_store_user_access_data();
        $userrecord = $DB->count_records('learningtools_resumecourse', array('userid' => $this->userid));
        $this->assertEquals(1, $userrecord);
    }

    public function test_lastaccess_activity_action() {
        global $PAGE;
        $userrecord = new stdClass();
        $userrecord->userid = $this->user->id;
        $useraccessurl = lastaccess_activity_action($userrecord);
        $this->assertEquals($PAGE->url->out(false), $useraccessurl);
    }
}