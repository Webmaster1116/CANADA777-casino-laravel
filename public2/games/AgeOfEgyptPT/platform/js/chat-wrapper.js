;
var MWS = MWS || {};
var chat = MWS.chat || {};

(function () {
    
    function ChatWrapper() {
    
    }
    
    var getScript = function (url, onLoad, onError) {
        var head = document.getElementsByTagName('head')[ 0 ];
        var script = document.createElement('script');
        var on = { load: onLoad, error: onError };
        var getHandler = function (type) {
            return function () {
                on[ type ] && on[ type ]();
                head.removeChild(script);
            };
        };
        script.type = 'text/javascript';
        script.src = url;
        script.onload = getHandler('load');
        script.onerror = getHandler('error');
        head.appendChild(script);
    };
    
    function init() {
        MWS.chat.onInitChat(loginAfterInit);
        MWS.chat.init();
    }
    
    function loginAfterInit() {
        if (callbacks) {
            MWS.chat.onOpenChat(callbacks.onOpenChat);
            MWS.chat.onCloseChat(callbacks.onCloseChat);
            MWS.chat.onHideChat(callbacks.onHideChat);
        }
        MWS.chat.login(userName, token);        
        MWS.chat.changeLanguage(lang);
        
        onInitCallback.call(null);
    }
    
    var chatConfigURL, chatURL, userName, token, lang, onInitCallback;
    
    ChatWrapper.initChat = function (params, onInit) {
        chatConfigURL = params.chatConfigURL;
        chatURL = params.chatURL;
        userName = params.userName;
        token = params.token;
        lang = params.lang;
        onInitCallback = onInit;
        
        getScript(chatConfigURL + '?' + Date.now(), function () {
            getScript(chatURL + '?' + Date.now(), init);
        });
    };
    
    ChatWrapper.logout = function () {
        MWS.chat.logout();
    };
    
    ChatWrapper.changeLanguage = function (lang) {
        MWS.chat.changeLanguage(lang);
    };
    
    ChatWrapper.showChat = function () {
        MWS.chat.showChat();
    };
    
    ChatWrapper.showIcon = function () {
        MWS.chat.showIcon();
    };
    
    var callbacks;
    ChatWrapper.registerCallbacks = function(chatCallbacks) {
        callbacks = chatCallbacks;
    };
    
    window.ChatWrapper = ChatWrapper;
}());