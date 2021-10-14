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
 * Invite ltool define js.
 * @package   ltool_invite
 * @category  Classes - autoloading
 * @copyright 2021, bdecent gmbh bdecent.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'core/ajax'],
 function($, Ajax) {

    /**
     * Controls resume course tool action.
     * @param {object} params
     */
    function learningToolResumeCourseAction(params) {
        var resumecourseinfo = document.querySelector(".ltoolresumecourse-info #ltoolresumecourse-action");
        if (resumecourseinfo) {
            resumecourseinfo.addEventListener("click", function() {
                params = JSON.stringify(params);
                 Ajax.call([{
                    methodname: 'ltool_resumecourse_lastaccess_activity',
                    args: {params: params},
                    done: function(response) {
                        if (response) {
                            window.open(response, '_self');
                        }
                    }
                }]);
            });

            // Hover color.
            var resumecoursehovercolor = resumecourseinfo.getAttribute("data-hovercolor");
            if (resumecoursehovercolor) {
                resumecourseinfo.addEventListener("mouseover", function() {
                    document.querySelector('#ltoolresumecourse-action p').style.background = resumecoursehovercolor;
                });
            }
        }
    }

    return {
        init: function(params) {
            learningToolResumeCourseAction(params);
        }
    };

 });