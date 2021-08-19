define(['core/str', 'core/ajax', 'core/notification'],
    function(String, Ajax, notification){

    function learning_tool_bookmarks_action(contextid, params) {

        setTimeout(function() {

            var bookmarkmarked = document.getElementById('bookmarks-marked');
            if (bookmarkmarked) {
                if (pagebookmarks) {
                    bookmarkmarked.classList.add('marked');
                } else {
                    bookmarkmarked.classList.remove('marked');
                }
            }

            var bookmarksform = document.getElementById('ltbookmarks-action');
            if (bookmarksform) {
                bookmarksform.addEventListener("click", function(e) {
                    e.preventDefault();
                    submitFormdata(contextid, params);
                });
            }
            
        }, 3000);
       
        var bookmarkssorttype = document.getElementById("bookmarkssorttype");

        if (bookmarkssorttype) {
            bookmarkssorttype.addEventListener("click", function() {
                var sorttype = this.getAttribute("data-type");
                bookmarks_sort_action_page(sorttype);
            });
        }

    }

    function bookmarks_sort_action_page(sorttype) {

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

    function submitFormdata(contextid, formData) {

        var Formdata = JSON.stringify(formData);
        Ajax.call([{
            methodname: 'ltool_bookmarks_save_userbookmarks',
            args: {contextid: contextid, formdata: Formdata},
            done: function(response) { 
                
                notification.addNotification({
                    message: response.bookmarksmsg,
                    type: response.notificationtype
                });

                let bookmarkmarked = document.getElementById('bookmarks-marked');
                if (response.bookmarksstatus) {
                    bookmarkmarked.classList.add('marked');
                } else {
                    bookmarkmarked.classList.remove('marked');
                }

                if (ltools.disappertimenotify != 0) {
                    setTimeout(function () {
                        var notifications = document.querySelector("span.notifications").innerHTML = "";
                    }, ltools.disappertimenotify);
                } 

            },
            fail: handleFailedResponse()
        }]);
    }


    function handleFailedResponse() {

    }
    return {
        init: function(contextid, params) {
            learning_tool_bookmarks_action(contextid, params);
        }
    };
});
