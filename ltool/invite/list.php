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
 * List of the invite users.
 *
 * @package   ltool_invite
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../../config.php');
require_once($CFG->dirroot. '/local/learningtools/lib.php');
require_once(dirname(__FILE__).'/lib.php');
require_login();

$teacher = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$title = get_string('inviteuserslist', 'local_learningtools');
if ($courseid) {
    $setcontext = context_course::instance($courseid);
    $courseelement = get_course($courseid);
    $courselistelement = new core_course_list_element($courseelement);
    $PAGE->set_course($courseelement);
    $heading = $courselistelement->get_formatted_name();
    require_capability('moodle/course:update', $setcontext);
} else {
    $setcontext = context_system::instance();
    $heading = $SITE->fullname;
    require_capability('moodle/site:config', $setcontext);
}
$PAGE->set_context($setcontext);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->set_url('/local/learningtools/ltool/invite/list.php');

echo $OUTPUT->header();
$sqlconditions = 'teacher=:teacher';
$sqlparams = array('teacher' => $teacher);
if ($courseid) {
    $sqlconditions .= "AND course = :courseid";
    $sqlparams['courseid'] = $courseid;
}

$table = new \ltool_invite\invitetool_table('datatable-invitetool', $courseid, $teacher);
$table->set_sql('*', '{learningtools_invite}', $sqlconditions, $sqlparams);
$table->define_baseurl($PAGE->url);
$table->out(10, true);
echo $OUTPUT->footer();


