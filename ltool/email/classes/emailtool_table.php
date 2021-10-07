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
 * List of the sent email reports.
 *
 * @package   ltool_email
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltool_email;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/tablelib.php');

/**
 * Class for the displaying the sent email reports info table.
 *
 * @package    ltool_email
 * @copyright  2bdecent GmbH 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emailtool_table extends \table_sql {
    /**
     * Sets up the table.
     *
     * @param string $tableid The id of the table
     * @param int $courseid course id
     * @param int $teacher teacher id
     */
    public function __construct($tableid, $courseid, $teacher) {
        parent::__construct($tableid);
        $this->courseid = $courseid;
        $this->teacher = $teacher;

         // Define the headers and columns.
        $columns = array();
        $headers = array();

        $columns[] = 'profile';
        $headers[] = get_string('firstname').'/'. get_string('lastname');

        $columns[] = 'subject';
        $headers[] = get_string('subject', 'local_learningtools');

        $columns[] = 'message';
        $headers[] = get_string('message', 'local_learningtools');

        $columns[] = 'receivedusers';
        $headers[] = get_string('receivedusers', 'local_learningtools');

        $columns[] = 'attachments';
        $headers[] = get_string('attachments', 'local_learningtools');

        $columns[] = 'timeaccess';
        $headers[] = get_string('timeaccess', 'local_learningtools');

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->no_sorting(true);
    }

    /**
     * Generate the profile column.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the column
     */
    public function col_profile($row) {
        global $OUTPUT;
        $user = $this->get_user($row->teacher);
        return $OUTPUT->user_picture($user, array('size' => 35, 'courseid' => $this->courseid, 'includefullname' => true));
    }

    /**
     * Generate the status subject.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the subject
     */
    public function col_subject($row) {
        return $row->subject;
    }

    /**
     * Generate the status message.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the message
     */
    public function col_message($row) {
        return $row->message;
    }

    /**
     * Generate the status message.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the message
     */
    public function col_attachments($row) {
        $fileinfo = file_get_drafarea_files($row->attachementdraft);
        $attachmenthtml = '';
        if (isset($fileinfo->list) && isset($fileinfo->list[0]) &&
        !empty($fileinfo->list[0]) ) {
            $attachementurl = $fileinfo->list[0]->url;
            $attachementname = $fileinfo->list[0]->filename;
            $attachmenthtml .= \html_writer::link($attachementurl, $attachementname, array("id" => "attachement-link"));
        }
        return $attachmenthtml;
    }

    /**
     * Generate the status message.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the message
     */
    public function col_receivedusers($row) {
        global $OUTPUT;
        $userids = json_decode($row->tousers);
        $userlisthtml = '';
        $userlisthtml .= \html_writer::start_tag("ul");
        if (!empty($userids)) {
            foreach ($userids as $userid) {
                $user = $this->get_user($userid);
                $userlisthtml .= \html_writer::start_tag("li");
                $userlisthtml .= $OUTPUT->user_picture($user, array('size' => 35,
                    'courseid' => $this->courseid, 'includefullname' => true));
                $userlisthtml .= \html_writer::end_tag("li");
            }
        }
        $userlisthtml .= \html_writer::end_tag("ul");
        return $userlisthtml;
    }

    /**
     * Generate the timeaccess column.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the column
     */
    public function col_timeaccess($row) {
        return userdate($row->timecreated, '%B %d, %Y, %I:%M %p', '', false);
    }

    /**
     * Get the user record.
     * @param int $userid user id
     * @return object user object
     */
    public function get_user($userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));
        return $user;
    }
}
