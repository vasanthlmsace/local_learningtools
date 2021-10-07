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
 * List of the email tool reports.
 *
 * @package   ltool_email
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../../config.php');
require_once($CFG->dirroot. '/local/learningtools/lib.php');
require_once(dirname(__FILE__).'/lib.php');
require_login();

$teacher = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$context = context_system::instance();
$title = get_string('sentemailuserslist', 'local_learningtools');
$PAGE->set_context($context);
$PAGE->set_url('/local/learningtools/ltool/email/list.php', array('id' => $teacher,
    'courseid' => $courseid));
$PAGE->set_title($title);
$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
$sqlconditions = 'teacher=:teacher';
$sqlparams = array('teacher' => $teacher);
if ($courseid) {
    $sqlconditions .= "AND course = :courseid";
    $sqlparams['courseid'] = $courseid;
}
$table = new \ltool_email\emailtool_table('datatable-emailtool', $courseid, $teacher);
$table->set_sql('*', '{learningtools_email}', $sqlconditions, $sqlparams);
$table->define_baseurl($PAGE->url);
$table->out(10, true);
echo $OUTPUT->footer();

