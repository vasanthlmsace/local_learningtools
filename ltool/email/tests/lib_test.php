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
 * email ltool lib test cases defined.
 *
 * @package   ltool_email
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined( 'MOODLE_INTERNAL') || die(' No direct access ');

/**
 * email subplugin for learningtools phpunit test cases defined.
 */
class ltool_email_testcase extends advanced_testcase {

    /**
     * Create custom page instance and set admin user as loggedin user.
     *
     * @return void
     */
    public function setup(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->user = $this->getDataGenerator()->create_user();
        $this->user1 = $this->getDataGenerator()->create_course();
        $this->course = $this->getDataGenerator()->create_course();
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->coursecontext = context_course::instance($this->course->id);
    }

    /**
     * Test case for get_user_for_roleids method
     */
    public function test_get_user_for_roleids() {

        $this->getDataGenerator()->role_assign($this->studentrole->id, $this->user->id,
        $this->coursecontext->id);
        $this->getDataGenerator()->role_assign($this->teacherrole->id, $this->user->id,
        $this->coursecontext->id);
        $roleids = [$this->studentrole->id];
        $userinfo = get_user_for_roleids($roleids, $this->coursecontext);
        if (!empty($userinfo)) {
            foreach ($userinfo as $userinfo) {
                $this->assertEquals($userinfo->id, $this->user->id);
                $this->assertNotEquals($userinfo->id, $this->user1->id);
            }
        }
    }

    /**
     * Test case for ltool_email_sent_email_to_users method
     */
    public function test_ltool_email_sent_email_to_users() {

        $testuser1 = $this->getDataGenerator()->create_user(array('maildisplay' => 1, 'mailformat' => 0));
        $supportuser = \core_user::get_support_user();
        $this->getDataGenerator()->role_assign($this->studentrole->id, $testuser1->id,
            $this->coursecontext->id);
        set_config('allowedemaildomains', "example.com\r\nmoodle.org");

        $subject = 'Demo subject';
        $messagetext = '<b> Demo message text </b>';
        $data = new stdClass();
        $data->subject = $subject;
        $data->message['text'] = $messagetext;
        $data->recipients = array($this->studentrole->id);
        $data->attachement = '';
        // Close the default email sink.
        $sink = $this->redirectEmails();
        ltool_email_sent_email_to_users($data, $this->coursecontext, $this->course->id);
        $this->assertSame(1, $sink->count());
        $result = $sink->get_messages();
        $this->assertCount(1, $result);
        $sink->close();

        $this->assertSame($subject, $result[0]->subject);
        $this->assertSame($messagetext, trim($result[0]->body));
        $this->assertSame($testuser1->email, $result[0]->to);
        $this->assertSame($supportuser->email, $result[0]->from);
    }
}
