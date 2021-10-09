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
 * Forceactivity ltool lib test cases defined.
 *
 * @package   ltool_forceactivity
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL') || die(' No direct access ');

/**
 * Forceactivity subplugin for learningtools phpunit test cases defined.
 */
class ltool_forceactivity_testcase extends advanced_testcase {

    /**
     * Create custom page instance and set admin user as loggedin user.
     *
     * @return void
     */
    public function setup(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $options = ['course' => $this->course->id];
        $this->quiz = $this->getDataGenerator()->create_module('quiz', $options);
        $this->cm = get_coursemodule_from_instance('quiz', $this->quiz->id);
        $this->context = context_course::instance($this->course->id);
    }

    public function test_ltool_forceactivity_get_array_of_activities() {
        $activitiesinfo = ltool_forceactivity_get_array_of_activities($this->course->id);
        $name = $activitiesinfo[$this->cm->id];
        $this->assertEquals($this->quiz->name, $name);
    }

    public function test_force_activity() {
        global $DB;
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->role_assign($studentrole->id, $student->id, $this->context);
        $this->getDataGenerator()->role_assign($teacherrole->id, $teacher->id, $this->context);
        $params = new stdClass;
        $params->course = $this->course->id;
        $params->user = $teacher->id;
        $data = [];
        $data['forceactivity'] = $this->cm->id;
        $messagetext = "Test message text";
        $data['messageinfo']['text'] = $messagetext;
        ltool_forceactivity_activityaction($params, $data);
        $records = $DB->count_records('learnigntools_forceactivity',
            array('courseid' => $this->course->id));
        $this->assertEquals(1, $records);
        load_forceactivity_action_coursepage();
        $sink = $this->redirectMessages();
        $this->assertCount(1, $sink->get_messages());
        $sink->close();
    }

}
