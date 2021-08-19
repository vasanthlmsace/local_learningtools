define(['jquery', 'core/modal_factory', 'core/str', 'core/fragment', 'core/modal_events', 'core/ajax', 'core/notification'],
    function($, ModalFactory, String, Fragment, ModalEvents, Ajax, notification){

    function learning_tool_note_action(contextid, params) {
        setTimeout(function() {
            show_modal_lttool(contextid, params);
        }, 3000);

        var sorttypefilter = document.querySelector(".ltnote-sortfilter i#notessorttype");
        if (sorttypefilter) {
            sorttypefilter.addEventListener("click", function() {
                var sorttype = this.getAttribute('data-type');
                note_sort_action_page(sorttype);
            });
        }

    }

    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts= url.split('?');
        if (urlparts.length>=2) {

            var prefix= encodeURIComponent(parameter)+'=';
            var pars= urlparts[1].split(/[&;]/g);

            //reverse iteration as may be destructive
            for (var i= pars.length; i-- > 0;) {
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    pars.splice(i, 1);
                }
            }

            url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
            return url;
        } else {
            return url;
        }
    }

    function show_modal_lttool(contextid, params) {

        var notesinfo = document.querySelector(".ltnoteinfo #ltnote-action");
        notesinfo.addEventListener("click", function() {
            var newnote = String.get_string('newnote', 'local_learningtools');
            
            $.when(newnote).done(function(localizedEditString) {
                // add class.
                var ltoolnotebody  = document.getElementsByTagName('body')[0];
                if (!ltoolnotebody.classList.contains('learningtool-note')) {
                    ltoolnotebody.classList.add('learningtool-note')
                }

                ModalFactory.create({
                    title: localizedEditString + get_popout_action(params),
                    type: ModalFactory.types.SAVE_CANCEL,
                    body: getnoteaction(contextid, params),
                    large: true
                }).then(function(modal){

                    modal.show();
                    
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        modal.destroy();
                    });
        
                    modal.getRoot().on(ModalEvents.save, function(e) { 

                        e.preventDefault();
                        submitForm(modal);
                    });

                    document.querySelector("#popout-action").addEventListener('click', function() {
                        var url = M.cfg.wwwroot+"/local/learningtools/ltool/note/popout.php?contextid="+
                        params.contextid+"&pagetype="+params.pagetype+"&contextlevel="+params.contextlevel+
                        "&course="+params.course+"&user="+params.user+"&pageurl="+params.pageurl+"&title="+params.title
                        +"&heading="+params.heading; 
                        modal.hide();
                        window.open(url, '_blank');
                    });

                    document.body.onsubmit = function (e) {
                        e.preventDefault();
                        submitFormData(modal, contextid, params)
                    };
                });
            });

        });
    }

    function note_sort_action_page(sorttype) {

        var pageurl = window.location.href;
        pageurl = removeURLParameter(pageurl, 'sorttype');

        if(sorttype == 'asc') {
            sorttype = 'desc';
        } else if (sorttype == 'desc') {
            sorttype = 'asc';
        }
        
        if (pageurl.indexOf('?') > -1) {
            var para = '&';
        } else {
            var para = '?';
        }

        pageurl = pageurl+para+'sorttype='+ sorttype;
        window.open(pageurl, '_self');
    }

    function get_popout_action(params) {
        var popouthtml = "<div class='popout-block'><button type='submit' id='popout-action' name='popoutsubmit'>Pop out</button><i class='fa fa-window-restore'></i></div>";
        return popouthtml;
    }

    function submitForm(modal) {
        modal.getRoot().submit();
    }

    function submitFormData(modal, contextid) {
        
        var modalform = document.querySelector('.ltoolusernotes form');
        var formData = serialize(modalform);
        var notesuccess = String.get_string('successnotemessage', 'local_learningtools');
        Ajax.call([{
            methodname: 'ltool_note_save_usernote',
            args: {contextid: contextid, formdata: formData},
            done: function(response) {
                // insert data into notes badge
                if (response) {
                    var noteinfo = document.querySelector(".ltnoteinfo span");
                    if (!noteinfo.classList.contains('ticked')) {
                        noteinfo.classList.add('ticked');
                    }
                    noteinfo.innerHTML = response;
                }
                
                modal.hide();
               // window.location.reload();
                $.when(notesuccess).done(function(localizedEditString){
                    notification.addNotification({
                        message: localizedEditString,
                        type: "success"
                    });
                }); 

                if (ltools.disappertimenotify != 0) {
                    setTimeout(function () {
                        document.querySelector("span.notifications").innerHTML = "";
                    }, ltools.disappertimenotify);
                }  
            },
            fail: handleFailedResponse()
        }]); 
    }

    function handleFailedResponse() {

    }

    function getnoteaction(contextid, params) {
        params.contextid = contextid;
        return Fragment.loadFragment('ltool_note', 'get_note_form', contextid, params);
    }

    function serialize(form) {
        if (!form || form.nodeName !== "FORM") {
                return;
        }
        var i, j, q = [];
        for (i = form.elements.length - 1; i >= 0; i = i - 1) {
            if (form.elements[i].name === "") {
                continue;
            }
            switch (form.elements[i].nodeName) {
                case 'INPUT':
                    switch (form.elements[i].type) {
                        case 'text':
                        case 'tel':
                        case 'email':
                        case 'hidden':
                        case 'password':
                        case 'button':
                        case 'reset':
                        case 'submit':
                            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                            break;
                        case 'checkbox':
                        case 'radio':
                            if (form.elements[i].checked) {
                                    q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                            }                                               
                            break;
                    }
                    break;
                    case 'file':
                    break; 
                case 'TEXTAREA':
                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                        break;
                case 'SELECT':
                    switch (form.elements[i].type) {
                        case 'select-one':
                            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                            break;
                        case 'select-multiple':
                            for (j = form.elements[i].options.length - 1; j >= 0; j = j - 1) {
                                if (form.elements[i].options[j].selected) {
                                        q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].options[j].value));
                                }
                            }
                            break;
                    }
                    break;
                case 'BUTTON':
                    switch (form.elements[i].type) {
                        case 'reset':
                        case 'submit':
                        case 'button':
                            q.push(form.elements[i].name + "=" + encodeURIComponent(form.elements[i].value));
                            break;
                    }
                    break;
                }
            }
        return q.join("&");
    }


    return {
        init: function(contextid, params) {
            learning_tool_note_action(contextid, params);
        }
    };
});