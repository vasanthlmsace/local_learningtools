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

 define(['jquery', 'core/modal_factory', 'core/str', 'core/fragment', 'core/modal_events', 'core/ajax', 'core/notification'],
 function($, ModalFactory, Str, Fragment, ModalEvents, Ajax, notification) {

    /**
     * Controls bookmarks tool action.
     * @param {object} params
     */
    function learningToolInviteAction(params) {
        showModalInvitetool(params);
    }

    /**
     * Display the modal to invite user emails.
     * @param {object} params
     */
    function showModalInvitetool(params) {
        var inviteinfo = document.querySelector(".ltoolinvite-info #ltoolinvite-action");
        if (inviteinfo) {
            inviteinfo.addEventListener("click", function() {
                // Strinviteusers.
                ModalFactory.create({
                    title: getListInviteUsers(params),
                    type: ModalFactory.types.SAVE_CANCEL,
                    body: getInviteAction(params),
                    large: true
                }).then(function(modal) {
                    modal.setSaveButtonText(Str.get_string('invitenow', 'local_learningtools'));
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
            var invitehovercolor = inviteinfo.getAttribute("data-hovercolor");
            if (invitehovercolor) {
                inviteinfo.addEventListener("mouseover", function() {
                    document.querySelector('#ltoolinvite-action p').style.background = invitehovercolor;
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
        var modalform = document.querySelectorAll('#invite-users-area form')[0];
        var formData = new URLSearchParams(new FormData(modalform)).toString();
        var jsonparams = JSON.stringify(params);
        Ajax.call([{
            methodname: 'ltool_invite_inviteusers',
            args: {params: jsonparams, formdata: formData},
            done: function(response) {
                modal.hide();
                if (response) {
                    var successinfo = Str.get_string('successinviteusers', 'local_learningtools');
                    $.when(successinfo).done(function(localizedEditString) {
                        notification.addNotification({
                            message: localizedEditString,
                            type: "success"
                        });
                    });
                    var listurl = M.cfg.wwwroot + "/local/learningtools/ltool/invite/list.php?id=" + params.user +
                    "&courseid=" + params.course;
                    //window.open(listurl, '_self');
                }
            }
        }]);
    }

    /**
     * Get invite user emails form.
     * @param {object} params
     * @return {string} textarea html
     */
    function getInviteAction(params) {
        return Fragment.loadFragment('ltool_invite', 'get_inviteusers_form', params.contextid, params);
    }
    /**
     * Display the list of invite users link.
     * @param {object} params
     * @returns {string} list of invite users link.
     */
    function getListInviteUsers(params) {
        var listaction = "<p>" + params.strinviteusers + "</p>";
        var listurl = M.cfg.wwwroot + "/local/learningtools/ltool/invite/list.php?id=" + params.user +
        "&courseid=" + params.course;
        listaction += "<div id='list-action-url'><a href='" + listurl + "' target='_blank'>" + params.strinvitelist + "</a></div>";
        return listaction;
    }

    return {
        init: function(params) {
            learningToolInviteAction(params);
        }
    };
 });