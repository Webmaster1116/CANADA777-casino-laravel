var EGT = {};

EGT.loadersCount = 0;

EGT.scripts = [
	"libs/library.js",
	"platform/js/Balance.js",
	"platform/js/GamePlatform.js",
	"platform/js/Socket.js",
	"platform/js/Utils.js",
	"platform/js/ViewportHandler.js",
	"platform/js/Device.js",
	"platform/js/gameProxies/GameProxy.js",
	"platform/js/gameProxies/SlotProxy.js",
	"platform/js/SessionKeeper.js",
	"platform/js/PlatformSettings.js",
	"platform/js/LoadingHandler.js"
];

EGT.initMobile = function(params) {
	
	EGT.params = params;
	
	var style = $("<link>", {
		rel: 'stylesheet',
		type: 'text/css',
		href: 'platform/css/style.css?v=1.7'
	});

	style[0].onload = function() {
		EGT.loadNextScript();
	};
	
	style.appendTo("head");

	var base = $("<div id=\"Loading\">\n" +
        "        <img src=\"platform/images/logo.jpg\" alt=\"EGT\" class=\"logo\" /><br />\n" +
        "        <span class=\"progress\">\n" +
        "            <span class=\"fill\"></span>\n" +
        "            <span class=\"percent\"></span>\n" +
        "            <span class=\"text\">LOADING</span>\n" +
        "\t\t\t<img src=\"platform/images/loadingStar.png\" alt=\"star\" class=\"star\" />\n" +
        "        </span><br />\n" +
        "        <img src=\"platform/images/touch.jpg\" alt=\"Touch\" class=\"touch\" />\n" +
        "\t</div>\n" +
        "\t<div id=\"Game\">\n" +
        "\t    <div id=\"GameViewport\" class=\"gameViewport\">\n" +
        "\t    </div>\n" +
        "\t    <div id=\"FullScreen\">\n" +
        "\t        <img class=\"hand_slide\" src=\"platform/images/hand_slide.gif\" alt=\"Slide!\" />\n" +
        "\t    </div>\n" +
        "\t    <div id=\"Waiting\">\n" +
        "\t        <img src=\"platform/images/loading.gif\" alt=\"Loading\" />\n" +
        "\t    </div>\n" +
        "\t    <div id=\"Error\">\n" +
        "\t    \t<div class=\"middle\">\n" +
        "\t\t\t    <div class=\"inner\">\n" +
        "\t\t\t        <div class=\"message\"></div>\n" +
        "\t\t\t        <span class=\"rightButton\"></span>\n" +
        "\t\t\t        <span class=\"leftButton\"></span>\n" +
        "\t\t\t    </div>\n" +
        "\t    \t</div>\n" +
        "\t    </div>\n" +
        "    </div>\n" +
        "    <div id=\"Orientation\">\n" +
        "        <img src=\"platform/images/rotate.gif\" alt=\"Rotate!\" />\n" +
        "    </div>");
    $("head").append($("<meta name=\"viewport\" content=\"width=device-width,height = device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, minimal-ui\">"));
	$("body").append(base);
	
	$("body").hide();
}

EGT.loadNextScript = function() {
	$.ajax({
		type: "GET",
		crossDomain: "false",
		url: EGT.scripts[EGT.loadersCount] + "?v=1.13",
		dataType: "script",
		cache: true,
		success: EGT.onLoadingComplete
	});
}

EGT.getParameterByName = function(sParam)
{
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++)
	{
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] == sParam)
		{
			return decodeURIComponent(sParameterName[1]);
		}
	}

	return "";
}

EGT.onLoadingComplete = function () {
	EGT.loadersCount++;
	if(EGT.loadersCount == EGT.scripts.length) {
		$("body").show();
		EGT.params.tcpPort = "443"
        if(EGT.params.lang == "it")
            EGT.params.lang = "en";
		new GamePlatform(EGT.params);
	} else {
		EGT.loadNextScript();
	}
}

$(document).on('game.reload', function(){
	window.focus();
	document.location.reload();
});

$(document).on('game.close', function(){
	window.focus();
	
	var closeUrl = EGT.getParameterByName("closeurl");
	if(closeUrl == "com.egt-bg.postMessage")
		window.parent.postMessage({ command: 'com.egt-bg.exit' }, '*');
	else if(closeUrl == "")
		window.close();
	else
		document.location.href = closeUrl;
});
