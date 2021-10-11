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
 * ltool plugin "Learning Tools Information" - library file.
 *
 * @package   ltool_information
 * @copyright bdecent GmbH 2021
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot. '/local/learningtools/lib.php');

/**
 * Learning tools information template function.
 * @param array $templatecontent template content
 * @return string display html content.
 */
function ltool_information_render_template($templatecontent) {
    global $OUTPUT;
    return $OUTPUT->render_from_template('ltool_information/information', $templatecontent);
}
/**
 * Load js for information tool
 *
 * @return void
 */
function load_information_js_config() {
    global $PAGE, $USER;
    $params = [];
    if (!empty($PAGE->course->id)) {
        $params['course'] = $PAGE->course->id;
        $params['contextid'] = $PAGE->context->id;
        $PAGE->requires->js_call_amd('ltool_information/information', 'init', array($params));
    }
}

/**
 * Get the course info.
 *
 * @param [array] $args
 * @return [string] display the info html
 */
function ltool_information_output_fragment_get_courseinformation($args) {
    global $DB;
    $content = '';
    $course = $DB->get_record('course', array('id' => $args['course']));
    $summary = ltool_information_get_coursesummary($course);
    $courseelement = new core_course_list_element($course);
    $courseimg = ltool_information_get_courseimage($courseelement);
    $coursename = $courseelement->get_formatted_fullname();
    $content .= html_writer::start_tag("div", array("id" => 'ltool-information-course-info'));
    if (!empty($summary) || !empty($courseimg)) {
        if ($summary) {
            $content .= html_writer::start_tag("div", array("id" => 'summary-block'));
                $content .= html_writer::tag("p", $summary);
            $content .= html_writer::end_tag("div");
        }
        if ($courseimg) {
            $content .= html_writer::start_tag("div", array("id" => 'image-block'));
                $content .= html_writer::empty_tag('img', array('src' => $courseimg));
            $content .= html_writer::end_tag("div");
        }
    } else {
        $content .= html_writer::tag('p', $coursename);
    }
    $content .= html_writer::end_tag('div');
    return $content;
}
/**
 * Get course summary.
 *
 * @param [object] $course
 * @return string course summary
 */
function ltool_information_get_coursesummary($course) {
    return $course->summary;
}

/**
 * Get course image.
 *
 * @param [object] $course
 * @return string course image.
 */
function ltool_information_get_courseimage($course) {
    global $CFG, $OUTPUT;
    if (!empty($course)) {
        $i = 0;
        $data = [];
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                $data[] = !empty($imgurl) ? $imgurl : '';
                $i++;
            }
        }
        return (!empty($data) && isset($data[0])) ? $data[0] : '';
    }
}
