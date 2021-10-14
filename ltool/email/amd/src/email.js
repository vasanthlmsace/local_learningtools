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

 define([], function() {

    /**
     * Controls resume course tool action.
     */
    function learningToolResumeCourseAction() {
        var emailinfo = document.querySelector(".ltoolemail-info #ltoolemail-action");
        if (emailinfo) {
            // Hover color.
            var emailhovercolor = emailinfo.getAttribute("data-hovercolor");
            if (emailhovercolor) {
                emailinfo.addEventListener("mouseover", function() {
                    document.querySelector('#ltoolemail-action p').style.background = emailhovercolor;
                });
            }
        }
    }

    return {
        init: function() {
            learningToolResumeCourseAction();
        }
    };

 });