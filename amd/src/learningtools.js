define([], function() {
    /**
     * Controls learningtools action.
     * @param {bool} loggedin
     *
     */
    function learning_tools_action(loggedin) {
        // Add fab button.
        if (loggedin) {
          var pagewrapper = document.getElementById("page-footer");
          pagewrapper.insertAdjacentHTML("beforebegin", fabbuttonhtml);
        }

        var toolaction = document.getElementById("tool-action-button");
        toolaction.addEventListener("click", function() {
            var listclass = document.getElementsByClassName("list-learningtools")[0];
            if (listclass.classList.contains('show')) {
                listclass.classList.remove('show');
            } else {
                listclass.classList.add('show');
            }
        });
    }

    return {
        init: function(loggedin) {
            learning_tools_action(loggedin);
        }
    };
});