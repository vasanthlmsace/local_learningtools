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
 * ltool plugin "Learning Tools notes" - library file.
 *
 * @package   ltool_note
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use core_user\output\myprofile\tree;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot. '/local/learningtools/lib.php');

/**
 * Define notes form.
 */
class editorform extends moodleform {
    /**
     * Adds element to form
     */
    public function definition() {
        $mform = $this->_form;
        $course = $this->_customdata['course'];
        $contextlevel = $this->_customdata['contextlevel'];
        $pagetype = $this->_customdata['pagetype'];
        $pageurl = $this->_customdata['pageurl'];
        $user = $this->_customdata['user'];
        $popoutaction = isset($this->_customdata['popoutaction']) ?
        $this->_customdata['popoutaction'] : '';

        $mform->addElement('editor', 'ltnoteeditor', '', array('autosave' => false));
        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);
        $mform->setDefault('course', $course);
        $mform->addElement('hidden', 'contextlevel');
        $mform->setDefault('contextlevel', $contextlevel);
        $mform->setType('contextlevel', PARAM_INT);

        $mform->addElement('hidden', 'pagetype');
        $mform->setDefault('pagetype', $pagetype);
        $mform->setType('pagetype', PARAM_TEXT);

        $mform->addElement('hidden', 'pageurl');
        $mform->setDefault('pageurl', $pageurl);
        $mform->setType('pageurl', PARAM_RAW);

        $mform->addElement('hidden', 'user');
        $mform->setDefault('user', $user);
        $mform->setType('user', PARAM_INT);

        if ($popoutaction) {
            $this->add_action_buttons();
        }

    }
}

/**
 * Define user edit the notes form.
 */
class edit_noteinfo extends moodleform {
    /**
     * Adds element to form
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;
        $noteid = $this->_customdata['id'];
        $courseid = $this->_customdata['courseid'];
        $returnurl = $this->_customdata['returnurl'];

        $note = $DB->get_record('learningtools_note', array('id' => $noteid));
        $usernote = !empty($note->note) ? $note->note : '';
        $mform->addElement('editor', 'noteeditor', get_string('note',
            'local_learningtools'))->setValue( array('text' => $usernote));
        $mform->setType('noteeditor', PARAM_RAW);
        $mform->addElement('hidden', 'edit');
        $mform->setType('edit', PARAM_INT);
        $mform->setDefault('edit', $noteid);
        if ($courseid) {
            $mform->addElement('hidden', 'courseid');
            $mform->setType('courseid', PARAM_INT);
            $mform->setDefault('courseid', $courseid);
        }

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl');
            $mform->setType('returnurl', PARAM_RAW);
            $mform->setDefault('returnurl', $returnurl);
        }
        $mform->addElement('hidden', 'sesskey');
        $mform->setType('sesskey', PARAM_RAW);
        $mform->setDefault('sesskey', sesskey());
        $this->add_action_buttons();
    }
}
/**
 * Defines the ltool notes nodes for my profile navigation tree.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser is the user viewing profile, current user ?
 * @param stdClass $course course object
 *
 * @return bool
 */
function ltool_note_myprofile_navigation(tree $tree, $user, $iscurrentuser, $course) {
    global $PAGE, $USER, $DB;
    $userid = optional_param('id', 0, PARAM_INT);
    $context = context_system::instance();
    if (is_note_status()) {
        if ($iscurrentuser) {
            if (!empty($course)) {
                $coursecontext = context_course::instance($course->id);
                if (has_capability('ltool/note:viewnote', $coursecontext)) {
                    $noteurl = new moodle_url('/local/learningtools/ltool/note/userslist.php', array('courseid' => $course->id));
                    $notenode = new core_user\output\myprofile\node('learningtools', 'note',
                        get_string('coursenotes', 'local_learningtools'), null, $noteurl);
                    $tree->add_node($notenode);
                } else {
                    $noteurl = new moodle_url('/local/learningtools/ltool/note/list.php',
                        array('courseid' => $course->id, 'userid' => $userid));
                    $notenode = new core_user\output\myprofile\node('learningtools', 'note',
                        get_string('coursenotes', 'local_learningtools'), null, $noteurl);
                    $tree->add_node($notenode);
                }
            } else {

                if (has_capability('ltool/note:viewownnote', $context)) {
                    $noteurl = new moodle_url('/local/learningtools/ltool/note/list.php');
                    $notenode = new core_user\output\myprofile\node('learningtools', 'note',
                        get_string('note', 'local_learningtools'), null, $noteurl);
                    $tree->add_node($notenode);
                }
            }
        } else {

            if (is_parentforchild($user->id, 'ltool/note:viewnote')) {
                $params = ['userid' => $user->id];
                $title = get_string('note', 'local_learningtools');
                if (!empty($course)) {
                    $params['courseid'] = $course->id;
                    $title = get_string('coursenotes', 'local_learningtools');
                }
                $noteurl = new moodle_url('/local/learningtools/ltool/note/list.php', $params);
                $notenode = new core_user\output\myprofile\node('learningtools', 'note', $title, null, $noteurl);
                $tree->add_node($notenode);
                return true;
            } else if (!empty($course)) {
                $coursecontext = context_course::instance($course->id);
                if (has_capability('ltool/note:viewnote', $coursecontext)) {
                    $noteurl = new moodle_url('/local/learningtools/ltool/note/userslist.php', array('courseid' => $course->id));
                    $notenode = new core_user\output\myprofile\node('learningtools', 'note',
                        get_string('coursenotes', 'local_learningtools'), null, $noteurl);
                    $tree->add_node($notenode);
                }
            }

        }
    }
    return true;
}

/**
 * Load the user page notes form
 * @param array $args page arguments
 * @return string Display the html note editor form.
 */
function ltool_note_output_fragment_get_note_form($args) {

    global $PAGE, $COURSE, $USER, $CFG;

    require_once($CFG->dirroot.'/lib/form/editor.php');
    require_once($CFG->dirroot . '/lib/editorlib.php');
    $editorhtml = '';
    $editor = editors_get_preferred_editor();
    $editor->use_editor("usernotes", array('autosave' => false));

    $editorhtml .= html_writer::start_tag('div', array('class' => 'ltoolusernotes'));
    $editorhtml .= html_writer::start_tag('form', array('method' => 'post', 'action' => $args['pageurl'], 'class' => 'mform'));

    $editorhtml .= html_writer::tag('textarea', '',
    array('id' => "usernotes", 'name' => 'ltnoteeditor', 'class' => 'form-group', 'rows' => 20, 'cols' => 100));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'course',
        'value' => $args['course'],
    ));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'contextid',
        'value' => $args['contextid'],
    ));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'contextlevel',
        'value' => $args['contextlevel'],
    ));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'pagetype',
        'value' => $args['pagetype'],
    ));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'pageurl',
        'value' => $args['pageurl'],
    ));

    $editorhtml .= html_writer::tag('input', '', array(
        'type' => 'hidden',
        'name' => 'user',
        'value' => $args['user'],
    ));

    $editorhtml .= html_writer::end_tag('form');
    $editorhtml .= html_writer::end_tag('div');
    $editorhtml .= load_context_notes($args);
    return $editorhtml;
}

/**
 * Page user exist notes info.
 * @param array $args page arguments.
 * @return string display the html exist notes list.
 */
function load_context_notes($args) {
    $editorhtml = '';
    $context = context_system::instance();
    if (get_userpage_countnotes($args) && has_capability('ltool/note:viewownnote', $context)) {
        $editorhtml .= html_writer::start_tag('div', array('class' => 'list-context-existnotes'));
        $editorhtml .= get_contextuser_notes($args);
        $editorhtml .= html_writer::end_tag('div');
    }
    return $editorhtml;
}

/**
 * Get the user in page context notes info.
 * @param array $args page arguments list.
 * @return string return to html the user notes.
 */
function get_contextuser_notes($args) {
    global $DB, $OUTPUT;
    $context = context_system::instance();
    $reports = [];
    $template = [];
    $listrecords = [];
    $sql = "SELECT * FROM {learningtools_note}
        WHERE userid = :userid AND contextid = :contextid ORDER BY timecreated DESC";

    $params = ['userid' => $args['user'], 'contextid' => $args['contextid']];
    $records = $DB->get_records_sql($sql, $params);
    $cnt = 1;
    if (!empty($records)) {
        foreach ($records as $record) {
            $time = floor($record->timecreated/86400);
            if (isset($listrecords[$time])) {
                $listrecords[$time]['notesgroup'][] = $record->id;
            } else {
                $listrecords[$time]['notesgroup'] = array($record->id);
            }
        }
        foreach ($listrecords as $time => $listrecord) {
            $res = [];
            $notes = [];
            if (isset($listrecord['notesgroup'])) {
                list($dbsql, $dbparam) = $DB->get_in_or_equal($listrecord['notesgroup'], SQL_PARAMS_NAMED);
                $notesrecords = $DB->get_records_sql("SELECT * FROM {learningtools_note}
                WHERE id $dbsql ORDER BY timecreated desc", $dbparam);
                if (!empty($notesrecords)) {
                    foreach ($notesrecords as $note) {
                        $list['note'] = !empty($note->note) ? $note->note : '';
                        $list['time'] = userdate(($note->timecreated), '%B %d, %Y, %I:%M', '', false);
                        if (has_capability('ltool/note:manageownnote', $context)) {
                            $returnparams = array('returnurl' => $args['pageurl']);
                            $list['delete'] = delete_note_record($note, $returnparams);
                            $list['edit'] = edit_note_record($note, $returnparams);
                        }
                        $notes[] = $list;
                    }
                }
                $res['notes'] = $notes;
                $res['title'] = userdate(($time * 86400), '%B, %dth %Y', '', false);
                $res['range'] = $cnt.'-block';
                $res['active'] = ($cnt == 1) ? true : false;
            }
            $reports[] = $res;
            $cnt++;
        }
    }
    $template['records'] = $reports;
    $template['usernotes'] = true;
    return $OUTPUT->render_from_template('ltool_note/usernotes', $template);
}
/**
 * Save the user notes.
 * @param int $contextid contextid
 * @param array $data page data
 * @return int save notes status
 */
function user_save_notes($contextid, $data) {
    global $DB, $PAGE;
    $context = context::instance_by_id($contextid, MUST_EXIST);
    $PAGE->set_context($context);

    if (confirm_sesskey()) {
        $record = new stdclass();
        $record->userid = $data['user'];
        $record->course = $data['course'];
        $record->contextlevel = $data['contextlevel'];
        $record->contextid = $contextid;
        if ($record->contextlevel == 70) {
            $record->coursemodule = get_coursemodule_id($record);
        } else {
            $record->coursemodule = 0;
        }

        $record->pagetype = $data['pagetype'];
        $record->pageurl = $data['pageurl'];
        $record->note = format_text($data['ltnoteeditor'], FORMAT_HTML);
        $record->timecreated = time();

        $notesrecord = $DB->insert_record('learningtools_note', $record);
        // Add event to user create the note.
        $event = \ltool_note\event\ltnote_created::create([
            'objectid' => $notesrecord,
            'courseid' => $data['course'],
            'context' => $context,
            'other' => [
                'pagetype' => $data['pagetype'],
            ]
        ]);
        $event->trigger();
        $pageusernotes = $DB->count_records('learningtools_note', array('contextid' =>
            $contextid, 'pagetype' => $data['pagetype'], 'userid' => $data['user']));
        return $pageusernotes;
    }
}


/**
 * Get notes edit records
 * @param object $row record
 * @param array $params page url params
 * @return string edit note html
 */
function edit_note_record($row, $params = []) {
    global $OUTPUT;
    $stredit = get_string('edit');
    $buttons = [];
    $returnurl = new moodle_url('/local/learningtools/ltool/note/editlist.php');
    $optionyes = array('edit' => $row->id, 'sesskey' => sesskey());
    $optionyes = array_merge($optionyes, $params);
    $url = new moodle_url($returnurl, $optionyes);
    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/edit', $stredit));
    $buttonhtml = implode(' ', $buttons);
    return $buttonhtml;

}

/**
 * Get notes delete records
 * @param object $row note record
 * @param array $params page url params
 * @return string delete note html
 */
function delete_note_record($row, $params = []) {

    global $OUTPUT;
    $strdelete = get_string('delete');
    $buttons = [];
    $returnurl = new moodle_url('/local/learningtools/ltool/note/deletelist.php');
    $optionyes = array('delete' => $row->id, 'sesskey' => sesskey());
    $optionyes = array_merge($optionyes, $params);
    $url = new moodle_url($returnurl, $optionyes);
    $buttons[] = html_writer::link($url, $OUTPUT->pix_icon('t/delete', $strdelete));
    $buttonhtml = implode(' ', $buttons);
    return $buttonhtml;
}
/**
 * Access the delete note user capability.
 * @param int $id note id.
 * @return bool|string return status.
 */
function require_deletenote_cap($id) {
    global $DB, $USER;

    $context = context_system::instance();
    $returnurl = new moodle_url('/my');
    $currentrecord = $DB->get_record('learningtools_note', array('id' => $id));
    if (!empty($currentrecord)) {
        if ($currentrecord->userid == $USER->id) {
            if (has_capability('ltool/note:manageownnote', $context)) {
                return true;
            }
        } else {
            if (has_capability('ltool/note:managenote', $context)) {
                return true;
            }
        }
    }
    return redirect($returnurl);
}

/**
 * Get the user pagenotes
 * @param array $args page info
 * @return int page user notes.
 */
function get_userpage_countnotes($args) {
    global $DB;
    return $DB->count_records('learningtools_note', array('contextid' => $args['contextid'],
        'pagetype' => $args['pagetype'], 'userid' => $args['user']));

}

/**
 * Check capability to show notes.
 * @return bool notes status
 */
function check_view_notes() {
    $viewnote = false;
    $context = context_system::instance();
    if (has_capability('ltool/note:viewownnote', $context) && is_note_status()) {
        $viewnote = true;
    }
    return $viewnote;
}

/**
 * Load notes js files.
 * @return void
 */
function load_notes_js_config() {
    global $COURSE, $PAGE, $USER;

    $params['course'] = $COURSE->id;
    $params['contextlevel'] = $PAGE->context->contextlevel;
    $params['pagetype'] = $PAGE->pagetype;
    $params['pageurl'] = $PAGE->url->out(false);
    $params['user'] = $USER->id;
    $params['contextid'] = $PAGE->context->id;
    $params['title'] = $PAGE->title;
    $params['heading'] = $PAGE->heading;
    $PAGE->requires->js_call_amd('ltool_note/learningnote', 'init', array($PAGE->context->id, $params));
}

/**
 * Learning tools template function.
 * @param array $templatecontent template content
 * @return string display html content.
 */
function ltool_note_render_template($templatecontent) {
    global $OUTPUT;
    return $OUTPUT->render_from_template('ltool_note/note', $templatecontent);
}


/**
 * Check the note status.
 * @return bool
 */
function is_note_status() {
    global $DB;
    $noterecord = $DB->get_record('local_learningtools_products', array('shortname' => 'note'));
    if (isset($noterecord->status) && !empty($noterecord->status)) {
        return true;
    }
    return false;
}
/**
 * Check the note view capability.
 * @return bool|redirect status
 */
function require_note_status() {
    if (!is_note_status()) {
        $url = new moodle_url('/my');
        redirect($url);
    }
    return true;
}


/**
 * Delete the course notes.
 * @param int $courseid course id.
 */
function delete_course_note($courseid) {
    global $DB;
    if ($DB->record_exists('learningtools_note', array('course' => $courseid))) {
        $DB->delete_records('learningtools_note', array('course' => $courseid));
    }
}

/**
 * Delete the course notes.
 * @param int $module course moudleid
 */
function delete_module_note($module) {
    global $DB;

    if ($DB->record_exists('learningtools_note', array('coursemodule' => $module))) {
        $DB->delete_records('learningtools_note', array('coursemodule' => $module));
    }
}

