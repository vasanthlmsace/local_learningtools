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
 * Behat Learning Tools related steps definitions.
 *
 * @package   ltool_forceactivity
 * @copyright 2021, bdecent gmbh bdecent.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

/**
 * Test cases custom function for force activity tool.
 *
 * @package   ltool_forceactivity
 * @copyright 2021, bdecent gmbh bdecent.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_forceactivity extends behat_base {

    /**
     * Check the forceactivity.
     *
     * @When /^I visit forrceactivity page"(?P<course full name>(?:[^"]|\\")*)" "(?P<quiz name>[^"]*)"$/
     * @throws coding_exception
     * @param string $coursefullname The course full name of the course.
     * @param string $quizname quiz name.
     * @return void
     */
    public function i_visit_forceactivity_page($coursefullname, $quizname) {
        global $DB;
        $course = $DB->get_record("course", array("fullname" => $coursefullname), 'id', MUST_EXIST);
        $quiz = $DB->get_record("quiz", array("name" => $quizname));
        $module = $DB->get_record("modules", array("name" => "quiz"));
        $cm = $DB->get_record("course_modules", array("course" => $course->id, "module" => $module->id,
            'instance' => $quiz->id));
        $url = new moodle_url('/mod/quiz/view.php', ['id' => $cm->id]);
        $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        $this->locate_path($courseurl->out_as_local_url(false));
        $this->getSession()->visit($this->locate_path($url->out_as_local_url(false)));
    }
}
