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
 * Notes ltool define js.
 * @package   ltool_note
 * @category  Classes - autoloading
 * @copyright 2021, bdecent gmbh bdecent.de
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery', 'core/modal_factory', 'core/str', 'core/fragment', 'core/modal_events', 'core/ajax', 'core/notification'],
 function($, ModalFactory, Str, Fragment, ModalEvents, Ajax, notification) {

    function learningToolInviteAction(params) {
        showModalInvitetool(params);
    }

    function showModalInvitetool(params) {
        var inviteinfo = document.querySelector(".ltoolinvite-info #ltoolinvite-action");
        if (inviteinfo) {
            inviteinfo.addEventListener("click", function() {
                ModalFactory.create({
                    title:  params.strinviteusers + getListInviteUsers(params),
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
                });
            });
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
        console.log(formData);
        params = JSON.stringify(params);
        var listurl = M.cfg.wwwroot + "/local/learningtools/ltool/invite/list.php?id="+params.userid
        Ajax.call([{
            methodname: 'ltool_invite_inviteusers',
            args: {params: params, formdata: formData},
            done: function(response) {
                modal.hide();
                var successinfo = Str.get_string('successinviteusers', 'local_learningtools', 'test');
                $.when(successinfo).done(function(localizedEditString) {
                    notification.addNotification({
                        message: localizedEditString,
                        type: "success"
                    });
                });
            }
        }]);
    }

    function getInviteAction(params) {
        return Fragment.loadFragment('ltool_invite', 'get_inviteusers_form', params.contextid, params);
    }

    function getListInviteUsers(params) {
        return '';
    }


    return {
        init: function(params) {
            learningToolInviteAction(params);
        }
    };
 });