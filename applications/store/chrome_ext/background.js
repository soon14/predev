var fix_win_id = -1;
var mscreen_win = false;
var connected_port = false;
var create_member_screen = function(options) {
    if (fix_win_id > 0) {
        return;
    }
    chrome.windows.create(options, function(win) {
        fix_win_id = win.id;
        mscreen_win = win;
    });
};
chrome.extension.onRequest.addListener(
    function(request, sender, sendResponse) {
        if (request && request.ms_options) {
            create_member_screen(request.ms_options);
        }
    });
chrome.windows.onRemoved.addListener(function(winid) {
    if (winid == fix_win_id) {
        fix_win_id = -1;
        if(connected_port){
            connected_port.postMessage('mscreen_removed');
        }
    }
});

chrome.extension.onConnect.addListener(function(port) {
    connected_port = port;
    if (port && port.name == 'to_mscreen') {
        port.onMessage.addListener(function(msg) {
            var script_str = "";
            script_str+="var customEvent = document.createEvent('Event');";
            script_str+="customEvent.initEvent('onpointOfSale', true, true);";
            script_str+="var opea = document.getElementById('on_pointofsale_event_area');";
            script_str+="opea.innerText = '"+msg+"';";
            script_str+="opea.dispatchEvent(customEvent);";
            chrome.tabs.executeScript(mscreen_win.tabs[0].id, {
                code: script_str
            });
        });
    }
});
