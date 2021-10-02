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
 * List of the invite users table.
 *
 * @package   ltool_invite
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltool_invite;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/tablelib.php');

/**
 * Class for the displaying the invite users info table.
 *
 * @package    ltool_invite 
 * @copyright  2bdecent GmbH 2021
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invitetool_table extends \table_sql {
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

        $columns[] = 'email';
        $headers[] = get_string('email');

        $columns[] = 'status';
        $headers[] = get_string('status');

        $columns[] = 'timeaccess';
        $headers[] = get_string('timeaccess', 'local_learningtools');

        $this->define_columns($columns);
        $this->define_headers($headers);
    }

    /**
     * Generate the email column.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the column
     */
    public function col_email($row) {
        $user = $this->get_user($row->userid);
        return $user->email;

    }

    /**
     * Generate the profile column.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the column
     */
    public function col_profile($row) {
        global $OUTPUT;
        $user = $this->get_user($row->userid);
        return $OUTPUT->user_picture($user, array('size' => 35, 'courseid' => $this->courseid, 'includefullname' => true));
    }

    /**
     * Generate the status column.
     *
     * @param \stdClass $row Data for the current row
     * @return string Content for the column
     */
    public function col_status($row) {
        return get_string($row->status, 'local_learningtools');
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
