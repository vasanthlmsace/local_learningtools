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
 * forceactivity ltool define js.
 * @package   ltool_forceactivity
 * @category  Classes - autoloading
 * @copyright 2021, bdecent gmbh bdecent.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'core/modal_factory', 'core/str', 'core/fragment', 'core/modal_events', 'core/ajax', 'core/notification'],
 function($, ModalFactory, Str, Fragment, ModalEvents, Ajax, notification) {

    /**
     * Controls bookmarks tool action.
     * @param {object} params
     */
    function learningToolforceactivityAction(params) {
        showModalforceactivitytool(params);
    }

    /**
     * Display the modal to forceactivity user emails.
     * @param {object} params
     */
    function showModalforceactivitytool(params) {
        var forceactivityinfo = document.querySelector(".ltoolforceactivity-info #ltoolforceactivity-action");
        if (forceactivityinfo) {
            forceactivityinfo.addEventListener("click", function() {
                // Strforceactivityusers.
                ModalFactory.create({
                    title: Str.get_string('forceactivity', 'local_learningtools'),
                    type: ModalFactory.types.SAVE_CANCEL,
                    body: getForceActivityModal(params),
                    large: true
                }).then(function(modal) {
                    modal.show();
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
                    modal.getRoot().on(ModalEvents.save, function(e) {
                        e.preventDefault();
                        submitFormData(modal, params);
                        modal.getRoot().submit();
                    });
                    return modal;
                }).catch(notification.exception);
            });

            // hover color
            var forceactivityhovercolor = forceactivityinfo.getAttribute("data-hovercolor");
            if (forceactivityhovercolor) {
                forceactivityinfo.addEventListener("mouseover", function() {
                    document.querySelector('#ltoolforceactivity-action p').style.background = forceactivityhovercolor;
                });
            }
        }
    }

    /**
     * Submit the modal data form.
     * @param {object} modal object
     * @param {array} params  list of parameters
     * @return {void} ajax respoltoolsnse.
     */
    function submitFormData(modal, params) {
        var modalform = document.querySelectorAll('#forceactivity-modalinfo form')[0];
        var formData = new URLSearchParams(new FormData(modalform)).toString();
        params = JSON.stringify(params);
        Ajax.call([{
            methodname: 'ltool_forceactivity_forceactivityaction',
            args: {params: params, formdata: formData},
            done: function(response) {
                modal.hide();
                if (response) {
                    var successinfo = Str.get_string('successforceactivityusers', 'local_learningtools');
                    $.when(successinfo).done(function(localizedEditString) {
                        notification.addNotification({
                            message: localizedEditString,
                            type: "success"
                        });
                    });
                }
            }
        }]);
    }
    /**
     * Get forceactivity Modal info.
     * @param {object} params
     * @return {string} textarea html
     */
    function getForceActivityModal(params) {
        return Fragment.loadFragment('ltool_forceactivity', 'get_forceactivitymodal_form', params.contextid, params);
    }

    return {
        init: function(params) {
            learningToolforceactivityAction(params);
        }
    };
 });