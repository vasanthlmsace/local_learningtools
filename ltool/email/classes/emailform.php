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
 * Display course participants email form.
 *
 * @package   ltool_email
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ltool_email;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir."/forms
lib.php");

class emailform extends \moodleform  {

    /**
     * Add elements to form
     */
    public function definition() {
        global $DB;
        $mform = $this->_form;
        $userid = $this->_customdata['user'];
        $courseid = $this->_customdata['course'];
        $strheading = get_string("sentemailparticipants", "local_learningtools");
        $mform->addElement('header', 'moodle', $strheading);
        // Subject.
        $mform->addElement('text', 'subject', get_string('subject', 'local_learningtools'),
            array('size'=>'40'));
        $mform->setType('subject', PARAM_RAW);
        $mform->addRule('subject', get_string('subjecterr', 'local_learningtools'), 'required', 'client');

        // Message.
        $mform->addElement('editor', 'message', get_string('message', 'local_learningtools'));
        $mform->addRule('message', get_string('messageerr', 'local_learningtools'), 'required', 'client');

        // Recipients 
        $coursecontext = \context_course::instance($courseid);
        $contextroles = role_fix_names(get_roles_used_in_context($coursecontext));
        $roles = array();
        if (!empty($contextroles)) {
            foreach($contextroles as $role) {
                $roles[$role->id] = $role->localname;
            }
        }
        $options = array(
            'multiple' => true,
            'required' => true,
        );
        $mform->addElement('autocomplete', 'recipients', get_string('recipients', 'local_learningtools'), $roles, $options);
        $mform->addRule('recipients', get_string('recipientserr', 'local_learningtools'), 'required', 'client');

        // Attachements.
        $mform->addElement('filepicker', 'attachments', get_string('attachments', 'local_learningtools'), null,
                    array('maxfiles' => 50));
        $this->add_action_buttons();
    }
}
