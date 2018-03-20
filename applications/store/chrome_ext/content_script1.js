var open_mscreen = function(){
    chrome.extension.sendRequest({
        ms_options: {
            url: document.getElementById('mscreen_url').value,
            type: "popup",
            state: "fullscreen",
        }
    }, function(response) {

    });
};
open_mscreen();

var port = chrome.extension.connect({
    name: 'to_mscreen'
});
document.getElementById('pointofsale_event_area').addEventListener('pointOfSale', function() {
    port.postMessage(document.getElementById('pointofsale_event_area').innerText);
});
port.onMessage.addListener(function(msg) {
    if(msg == 'mscreen_removed' && document.getElementById('pointofsale_event_area')){
        open_mscreen();
    }
});
