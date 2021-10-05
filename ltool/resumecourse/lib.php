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
 * ltool plugin "Learning Tools Resume course" - library file.
 *
 * @package   ltool_resumecourse
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot. '/local/learningtools/lib.php');

/**
 * Learning tools resumecourse template function.
 * @param array $templatecontent template content
 * @return string display html content.
 */
function ltool_resumecourse_render_template($templatecontent) {
    global $OUTPUT;
    return $OUTPUT->render_from_template('ltool_resumecourse/resumecourse', $templatecontent);
}

/**
 * Load resume course js.
 * @return void
 */
function load_resumecourse_js_config() {
    global $PAGE, $USER;
    $params = [];
    $params['userid'] = $USER->id;
    $params['contextid'] = $PAGE->context->id;
    $PAGE->requires->js_call_amd('ltool_resumecourse/resumecourse', 'init', array($params));
}

/**
 * Save the user access data
 * @return void
 */
function ltool_resumecourse_store_user_access_data() {
    global $DB, $PAGE, $USER;
    if ($PAGE->context->contextlevel == 70) {
        $userrecord = $DB->get_record('learningtools_resumecourse', array('userid' => $USER->id));
        if (empty($userrecord)) {
            $userrecord = new stdClass();
            $userrecord->userid = $USER->id;
            $userrecord->contexid = $PAGE->context->id;
            $userrecord->pageurl = $PAGE->url->out(false);
            $userrecord->timecreated = time();
            $DB->insert_record('learningtools_resumecourse', $userrecord);
        } else {
            $userrecord->contextid = $PAGE->context->id;
            $userrecord->pageurl = $PAGE->url->out(false);
            $userrecord->timemodified = time();
            $DB->update_record('learningtools_resumecourse', $userrecord);
        }
    }
}

/**
 * Get the user last access the module url
 * @param object $params
 * @return string module url
 */
function lastaccess_activity_action($params) {
    global $DB;
    $userrecord = $DB->get_record('learningtools_resumecourse', array('userid' => $params->userid));
    if (!empty($userrecord)) {
        return $userrecord->pageurl;
    }
    $my = new moodle_url('/my');
    return $my->out(false);
}
