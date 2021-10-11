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
 * ltool plugin "Learning Tools Force activity" - library file.
 *
 * @package   ltool_forceactivity
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot. '/local/learningtools/lib.php');

/**
 * Learning tools forceactivity template function.
 * @param array $templatecontent template content
 * @return string display html content.
 */
function ltool_forceactivity_render_template($templatecontent) {
    global $OUTPUT;
    return $OUTPUT->render_from_template('ltool_forceactivity/forceactivity', $templatecontent);
}

/**
 * Load js data
 */
function load_forceactivity_js_config() {
    global $PAGE, $USER;
    $params = [];
    if (!empty($PAGE->course->id)) {
        $params['course'] = $PAGE->course->id;
        $params['user'] = $USER->id;
        $params['contextid'] = $PAGE->context->id;
        $PAGE->requires->js_call_amd('ltool_forceactivity/forceactivity', 'init', array($params));
    }
}

/**
 * Load the force activity form
 * @param array $args page arguments
 * @return string Display the html invite users form.
 */
function ltool_forceactivity_output_fragment_get_forceactivitymodal_form($args) {
    global $DB;
    $forceactivitydata = $DB->get_record('learningtools_forceactivity', array('courseid' => $args['course']));
    if (!empty($forceactivitydata)) {
        $args['cmid'] = $forceactivitydata->cmid;
        $args['message'] = $forceactivitydata->message;
    }
    $inviteform = new ltool_forceactivity_modalform(null, $args);
    $formhtml = html_writer::start_tag('div', array('id' => 'forceactivity-modalinfo'));
    $formhtml .= $inviteform->render();
    $formhtml .= html_writer::end_tag('div');
    return $formhtml;
}

/**
 * Get force activities list in course
 * @param int $courseid
 * @return array list of force activities
 */
function ltool_forceactivity_get_array_of_activities($courseid) {
    global $DB;
    $data = [];
    $data[] = get_string("noactivity", 'local_learningtools');
    $activities = get_array_of_activities($courseid);
    if (!empty($activities)) {
        foreach ($activities as $activity) {
            $coursemodule = $DB->get_record('course_modules', array('id' => $activity->cm));
            if ($coursemodule->completion) {
                $data[$activity->cm]  = $activity->name;
            }
        }
    }
    return $data;
}

/**
 * store the Force activity info
 *
 * @param [object] $params
 * @param [array] $data
 * @return bool status
 */
function ltool_forceactivity_activityaction($params, $data) {
    global $DB;
    if (isset($data['forceactivity'])) {
        $record = new stdclass;
        $record->courseid = $params->course;
        $record->cmid = $data['forceactivity'];
        $record->teacher = $params->user;
        $messageinfo = !empty($data['messageinfo']['text']) ? $data['messageinfo']['text'] : "";
        $record->message = trim($messageinfo);
        $existrecord = $DB->get_record('learningtools_forceactivity', array('courseid' => $params->course));
        if (empty($existrecord)) {
            $record->timecreated = time();
            $DB->insert_record('learningtools_forceactivity', $record);
        } else {
            $record->id = $existrecord->id;
            $record->timemodified = time();
            $DB->update_record('learningtools_forceactivity', $record);
        }
        return true;
    }
    return false;
}

/**
 * Redirect the Force activity.
 *
 * @return void
 */
function load_forceactivity_action_coursepage($courseid) {
    global $PAGE, $DB, $USER;
    $record = $DB->get_record('learningtools_forceactivity', array('courseid' => $courseid));
    if (!empty($record)) {
        if (!$DB->record_exists('course_modules_completion', array('coursemoduleid' => $record->cmid,
        'userid' => $USER->id, 'completionstate' => 1))) {
            if (!empty($record->cmid)) {
                $modinfo = new stdClass();
                $modinfo->coursemodule = $record->cmid;
                $modname = get_module_name($modinfo, true);
                $forceurl = "/mod/".$modname."/view.php";
                $forceurl = new moodle_url($forceurl, ['id' => $record->cmid]);
                redirect($forceurl, $record->message, null, \core\output\notification::NOTIFY_WARNING);
            }
        }
    }
}

/**
 * Display invite user email textarea
 */
class ltool_forceactivity_modalform extends moodleform {

    /**
     * Add elements to form.
     */
    public function definition() {
        $mform = $this->_form;
        $courseid = $this->_customdata['course'];
        $cmid = !empty($this->_customdata['cmid']) ? $this->_customdata['cmid'] : 0;
        $message = !empty($this->_customdata['message']) ? $this->_customdata['message'] : '';
        $courseactivites = ltool_forceactivity_get_array_of_activities($courseid);
        $mform->addElement('select', 'forceactivity', get_string('courseactivity', 'local_learningtools'), $courseactivites);
        $mform->setDefault('forceactivity', $cmid);
        $mform->addElement('editor', 'messageinfo', get_string('message', 'local_learningtools'),
            array('autosave' => false))->setValue(array('text' => $message));
    }
}
