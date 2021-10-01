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
 * @package   ltool_note
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltool_invite;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/tablelib.php');

class invitetool_table extends \table_sql {

    public function __construct($tableid, $courseid, $teacher) {
       
        parent::__construct($tableid);

        $this->courseid = $courseid;
        $this->teacher = $teacher;

        $columns = array();
        $headers = array();

        $columns[] = 'profile';
        $headers[] = get_string('firstname').'/'. get_string('lastname');

        $columns[]= 'email';
        $headers[]= get_string('email');

        $columns[]= 'status';
        $headers[]= get_string('status');

        $columns[] = 'timeaccess';
        $headers[] = get_string('timeaccess', 'local_learningtools');


        $this->define_columns($columns);
        $this->define_headers($headers); 
        $this->no_sorting(true);
    }

    public function col_email(\stdclass $row) {
        $user = $this->get_user($row->userid);
        return $user->email;

    }

    public function col_profile(\stdclass $row) {
        global $OUTPUT;
        $user = $this->get_user($row->userid);
        return $OUTPUT->user_picture($user, array('size' => 35, 'courseid' => $this->courseid, 'includefullname' => true));
    }

    public function col_status(\stdclass $row) {
        return get_string($row->status, 'local_learningtools');
    }

    public function col_timeaccess(\stdclass $row) {
        return userdate($row->timecreated, '%B %d, %Y, %I:%M %p', '', false);
    }

    public function get_user($userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));
        return $user;
    }
}