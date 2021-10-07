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
 * @package   ltool_resumecourse
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL') || die(' No direct access ');

/**
 * Resume course subplugin for learningtools phpunit test cases defined.
 */
class ltool_resume_testcase extends advanced_testcase {

    /**
     * Create custom page instance and set admin user as loggedin user.
     *
     * @return void
     */
    public function setup(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generator = $this->getDataGenerator();
        $course = $this->generator->create_course();
        $options = array('course' => $course->id);
        $quiz = $this->getDataGenerator()->create_module('quiz', $options);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $this->cm = $cm;
        $quizcontext = \context_module::instance($cm->id);
        $PAGE = new \moodle_page();
        $PAGE->set_course($course);
        $PAGE->set_context($quizcontext);
        $PAGE->set_cm($cm);
        $PAGE->set_title('Course 1: Quiz test 1');
        $PAGE->set_url(new moodle_url('/mod/quiz/view.php', ['id' => $cm->id]));
        $this->user = $this->generator->create_user();
    }

    /**
     * Test store_user_access_data
     */
    public function test_ltool_resumecourse_store_user_access_data() {
        global $DB, $PAGE;
        $PAGE->set_url(new moodle_url('/mod/quiz/view.php', ['id' => $this->cm->id]));
        $this->setUser($this->user);
        ltool_resumecourse_store_user_access_data();
        $userrecord = $DB->count_records('learningtools_resumecourse', array('userid' => $this->user->id));
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
