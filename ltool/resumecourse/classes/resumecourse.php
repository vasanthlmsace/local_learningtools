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
 * The class defines the Resume course ltool.
 *
 * @package   ltool_resumecourse
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltool_resumecourse;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/learningtools/lib.php');

require_once(dirname(__DIR__).'/lib.php');

/**
 *  The class defines the resumecourse ltool
 */
class resumecourse extends \local_learningtools\learningtools {

    /**
     * Tool shortname.
     *
     * @var string
     */
    public $shortname = 'resumecourse';

    /**
     * Tool context level
     * @var string
     */
    public $contextlevel = 'system';

    /**
     * resumecourse name
     * @return string name
     *
     */
    public function get_tool_name() {
        return get_string('resumecourse', 'local_learningtools');
    }

    /**
     * resumecourse icon
     */
    public function get_tool_icon() {

        return 'fa fa-backward';
    }

    /**
     * resumecourse icon background color
     */
    public function get_tool_iconbackcolor() {

        return '#343a40';
    }

    /**
     * Load the required javascript files for resumecourse.
     *
     * @return void
     */
    public function load_js() {
        // Load note tool js configuration.
        load_resumecourse_js_config();
    }

    /**
     * Get the resumecourse tool  content.
     *
     * @return string display tool resumecourse plugin html.
     */
    public function get_tool_records() {
        $data = [];
        $data['name'] = $this->get_tool_name();
        $data['icon'] = $this->get_tool_icon();
        $data['ltoolresumecourse'] = true;
        $data['resumecoursehovername'] = get_string('resumecourse', 'local_learningtools');
        $data['iconbackcolor'] = get_config("ltool_{$this->shortname}", "{$this->shortname}iconbackcolor");
        $data['iconcolor'] = get_config("ltool_{$this->shortname}", "{$this->shortname}iconcolor");
        ltool_resumecourse_store_user_access_data();
        return $data;
    }

    /**
     * Return the template of resumecourse fab button.
     *
     * @return string resumecourse tool fab button html.
     */
    public function render_template() {
        $data = $this->get_tool_records();
        return ltool_resumecourse_render_template($data);
    }
}

