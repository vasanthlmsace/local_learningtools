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
 * List of the user bookmarks.
 *
 * @package   ltool_bookmarks
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../../config.php');
require_once($CFG->dirroot. '/local/learningtools/lib.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot. '/course/classes/list_element.php');
require_login();

$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$pageurl = new moodle_url('/local/learningtools/ltool/email/sentemail.php',
array('courseid' => $courseid, 'userid' => $userid));
$returnurl = new moodle_url('/course/view.php', array('id' => $courseid));
$PAGE->set_url($pageurl);
$title = get_string('coursebookmarks', 'local_learningtools');
$course= get_course($courseid);
$courselistelement = new core_course_list_element($course);
$PAGE->set_course($course);
$PAGE->set_heading($courselistelement->get_formatted_name());
$setcontext = context_course::instance($course->id);
$PAGE->set_context($setcontext);

$mform = new ltool_email\emailform($pageurl, array('course' => $courseid, 'user' => $userid));
if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    ltool_email_sent_email_to_users($formdata, $setcontext, $courseid);
    redirect($returnurl, get_string('msgemailsent','local_learningtools'), null, \core\output\notification::NOTIFY_SUCCESS);
} else {
    echo $OUTPUT->header();
    $mform->display();
}

echo $OUTPUT->footer();