window.MESSENGER_VAR = "MESSENGER"; // messenger's global variable name

// initialize messenger to specified global variableName
function initializeMessenger(variableName) {

    var messenger = window[variableName] = new Object();
    messenger.request = function (message, callback) {
        var response = {
            classifier: "IError", code: -1,
            message: variableName + ".request() no yet initialized. Game should initialize it immediately on start"
        };
        console.error(response.message);

        callback.done(JSON.stringify(response));
    }
}

// execute JS --->
if (typeof window[MESSENGER_VAR] == "undefined") {
    initializeMessenger(MESSENGER_VAR);
    console.log("Messenger object has been created. See window['" + MESSENGER_VAR + "']");
} else {
    console.error("Cannot initialize MESSENGER because variable window['" + MESSENGER_VAR + "'] already defined.");
}