(function() {
    var jsonEditor = null;

    function replaceTabWithSpaces(e) {
        let TABKEY = 9;
        let txtArea = e.target;
        if(e.keyCode !== TABKEY) {
            return;
        }
        if(e.preventDefault) {
            e.preventDefault();
        }
        let start = txtArea.selectionStart;
        let end = txtArea.selectionEnd;

        txtArea.value = txtArea.value.substring(0, start) + "  " + txtArea.value.substring(end);
        txtArea.selectionStart = txtArea.selectionEnd = start + 2;
        return false;
    }

    function onDocLoaded() {
        
            let txtArea = document.querySelector('#content');
            if (!txtArea || !txtArea.value) {
                return;
            }
            let postStatusInfo = document.getElementById("post-status-info");
            postStatusInfo.style.display = "none";
            txtArea.style.display = "none";
            
            let txtAreaContainer = document.querySelector('#wp-content-editor-container');
            let postForm = document.getElementById("post");
            let jsonEditorWrapper = document.createElement("div");

            jsonEditorWrapper.id = "jsonEditorWrapper";
            txtAreaContainer.appendChild(jsonEditorWrapper);
    
            jsonEditor =  new JsonEditor('#jsonEditorWrapper', JSON.parse(txtArea.value));

            postForm.onsubmit = function() {
                let jsonAsTxt = JSON.stringify(jsonEditor.get());
                if (jsonAsTxt) {
                    txtArea.value = jsonAsTxt;
                }
                return true;
            }
            

    }

    document.addEventListener('DOMContentLoaded', onDocLoaded, false);
    
})();

(function() {
    
    
})();
