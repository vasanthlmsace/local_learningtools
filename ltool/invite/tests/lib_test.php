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
 * Invite ltool lib test cases defined.
 *
 * @package   ltool_invite
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL') || die(' No direct access ');

/**
 * Invite subplugin for learningtools phpunit test cases defined.
 */
class ltool_invite_testcase extends advanced_testcase {

    /**
     * Create custom page instance and set admin user as loggedin user.
     *
     * @return void
     */
    public function setup(): void {
        global $DB, $PAGE, $CFG;
        require_once($CFG->dirroot.'/local/learningtools/ltool/invite/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->generator = $this->getDataGenerator();
        $this->student = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacher = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->course = $this->generator->create_course();
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->useremail = "testuser1@gmail.com";
    }

    /**
     * Test the invite create user method.
     */
    public function test_ltool_invite_create_user(): void {
        global $DB, $CFG;
        $newuserid = ltool_invite_create_user($this->useremail);
        $newuser = $DB->count_records('user', array('id' => $newuserid));
        $this->assertEquals(1, $newuser);
        $prefername = "auth_forcepasswordchange";
        $newuserprefer = $DB->get_record('user_preferences', array('userid' => $newuserid, 'name' => $prefername));
        $this->assertEquals(1, $newuserprefer->value);
    }

    public function test_invite_users_action(): void {
        global $CFG;
        $studentdata = new stdClass;
        $studentdata->email = $this->useremail;
        $studentuser = $this->generator->create_user($studentdata);
        $teacheruser = $this->generator->create_user();
        $data = new stdClass;
        $params = array();
        $params['inviteusers'] = array($studentuser->email);
        $data->user = $studentuser->id;
        $data->course = $this->course->id;
        invite_users_action($data, $params);
        $enrolstatus = is_enrolled($this->coursecontext, $studentuser);
        $this->assertTrue($enrolstatus);
    }
}
