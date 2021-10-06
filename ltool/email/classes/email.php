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
 * The class defines the email ltool.
 *
 * @package   ltool_email
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltool_email;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/learningtools/lib.php');

require_once(dirname(__DIR__).'/lib.php');

/**
 *  The class defines the email ltool
 */
class email extends \local_learningtools\learningtools {

    /**
     * Tool shortname.
     *
     * @var string
     */
    public $shortname = 'email';

    /**
     * email name
     * @return string name
     *
     */
    public function get_tool_name() {
        return get_string('email', 'local_learningtools');
    }

    /**
     * email icon
     */
    public function get_tool_icon() {

        return 'fa fa-envelope-o';
    }

    /**
     * email icon background color
     */
    public function get_tool_iconbackcolor() {

        return '#343a40';
    }

    /**
     * Load the required javascript files for email.
     *
     * @return void
     */
    public function load_js() {
        // Load note tool js configuration.
        //load_email_js_config();
    }

    /**
     * Get the email tool  content.
     *
     * @return string display tool email plugin html.
     */
    public function get_tool_records() {
        global $PAGE, $USER;
        $data = [];
        $data['name'] = $this->get_tool_name();
        $data['icon'] = $this->get_tool_icon();
        $data['ltoolemail'] = true;
        $data['emailhovername'] = get_string('email', 'local_learningtools');
        $data['iconbackcolor'] = get_config("ltool_{$this->shortname}", "{$this->shortname}iconbackcolor");
        $data['iconcolor'] = get_config("ltool_{$this->shortname}", "{$this->shortname}iconcolor");
        $data['course'] = $PAGE->course->id;
        $data['user'] = $USER->id;
        $data['sentmailurl'] = new \moodle_url('/local/learningtools/ltool/email/sentemail.php');
        return $data;
    }

    /**
     * Return the template of email fab button.
     *
     * @return string email tool fab button html.
     */
    public function render_template() {
        global $PAGE, $SITE;
        if (!empty($PAGE->course->id) && $PAGE->course->id != $SITE->id) {
            $coursecontext = \context_course::instance($PAGE->course->id);
            if(has_capability("ltool/invite:viewinvite", $coursecontext)) {
                $data = $this->get_tool_records();
                return ltool_email_render_template($data);
            }
        }
        return '';
    }

}
