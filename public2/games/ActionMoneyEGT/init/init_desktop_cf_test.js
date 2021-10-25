var gptsInit = {
    loadScript: function (src, onComplete, query) {
        // with script injection, async is true by default
        var script = document.createElement('script');
        script.onload = onComplete
        script.setAttribute('src', src + this.getQueryString(query));
        var head = document.getElementsByTagName('head')[0]
        head.appendChild(script);
    },
    loadQueue: function (srcs, onComplete) {
        var length = srcs.length
        var loadedCount = 0
        for (var i = 0; i < length; i++) {
            if (typeof srcs[i] == 'string')
                srcs[i] = {src: srcs[i]}
            gptsInit.loadScript(srcs[i].src, function () {
                loadedCount++
                if (loadedCount == length) onComplete()
            }, srcs[i].query)
        }
    },
    getParameterBy: function (name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    loadCSS: function (src, onComplete, query) {
        var css = document.createElement('link');
        css.onload = onComplete
        css.setAttribute('href', src + this.getQueryString(query));
        css.setAttribute("rel", "stylesheet")
        css.setAttribute("type", "text/css")
        var head = document.getElementsByTagName('head')[0]
        head.appendChild(css);
    },
    getQueryString: function (query) {
        if (query == undefined)
            return ''
        var queryString = '?'
        for (var name in query)
            queryString += name + '=' + query[name] + '&'
        return queryString.substr(0, queryString.length - 1)
    },
    loadQueues: function (srcs, onComplete) {
        var length = srcs.length
        var counter = 0
        var that = this

        function onCompleteLoadingQueue() {
            if (counter == length)
                onComplete()
            else that.loadQueue(srcs[counter], onCompleteLoadingQueue)
            counter++
        }

        onCompleteLoadingQueue()
    }
}

function closePopup() {
    window.parent.postMessage({ command: 'com.egt-bg.exit' }, '*')

    if (window.parent && window.parent['onExitGamePlatformEGT'])
        window.parent['onExitGamePlatformEGT'].call()
    else {
        // Executes the default logic on exit, which uses popup instead of an iframe
        top.window.close()
    }
}

function initDesktopHtml(params) {

	var base = $("<div id=\"content\">\n" +
        "    <div id=\"gpts\"></div>\n" +
        "    <div id=\"sessionClosed\">\n" +
        "        <div id=\"sessionClosedLabel\"></div>\n" +
        "    </div>\n" +
        "    <div class=\"alert\"></div>\n" +
        "</div>");
	$("head").append($("<meta name=\"viewport\" content=\"width=device-width,height = device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, minimal-ui\">"));
	$("body").append(base);

    var mainTag = $("#gpts");
    mainTag.on("click", function() {window.focus();});
    window.focus();

	// TODO: the schema should be provided by the server
	
	
	
	if(params.sslHost ){
		
	var wsPref="wss://";	
		
	}else{
	
var wsPref="ws://";	
		
	}
	
	params.tcpHost = wsPref + params.tcpHost;
	

    gptsInit.loadScript('options.js', function () {
        gptsInit.loadCSS('platform.css', function () {
            gptsInit.loadQueues(gptsOptions.queues, function () {
                gpts.start(function () {
                    // Place here socket options, etc.
                    new gpts.Main(params);
                })
            })
        }, {build: gptsOptions.build})
    }, {gyCacheBusterID: new Date().getTime()})
}
