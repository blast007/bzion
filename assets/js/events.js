$(document).ready(function() {

    if (config.websocket) {
        var conn = new WebSocket('ws://' + window.location.hostname + ':' + config.websocket.port);

        conn.onmessage = function(e) {
            var data = JSON.parse(e.data).event;
            notify(data.message, 'success');
        };
    }
});