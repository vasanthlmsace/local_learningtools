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
 * External functions definition and returns.
 *
 * @package   ltool_forceactivity
 * @copyright bdecent GmbH 2021
 * @category  event
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace ltool_forceactivity;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/externallib.php');
/**
 * Define external class.
 */
class external extends \external_api {

    /**
     * Parameters defintion to forceactivity users.
     *
     * @return array list of option parameters
     */
    public static function forceactivityaction_parameters() {

        return new \external_function_parameters(
            array(
                'params' => new \external_value(PARAM_RAW, 'The parameters of info'),
                'formdata' => new \external_value(PARAM_RAW, 'The data for the forceactivity')
            )
        );
    }

    /**
     * forceactivity users actions
     * @param mixed $params context id
     * @param mixed $formdata user data
     * @return bool status
     */
    public static function forceactivityaction($params, $formdata) {
        global $CFG;
        require_once($CFG->dirroot."/local/learningtools/ltool/forceactivity/lib.php");
        // Parse serialize form data.
        parse_str($formdata, $data);
        $params = json_decode($params);
        return ltool_forceactivity_activityaction($params, $data);
    }

    /**
     * Return parameters define for forceactivity users status.
     */
    public static function forceactivityaction_returns() {
        return new \external_value(PARAM_BOOL, 'forceactivity status');
    }
}
