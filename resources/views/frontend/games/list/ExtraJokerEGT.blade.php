

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>{{ $game->title }}</title>
	<meta name="viewport" content="width=device-width,height = device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<base href="/games/ExtraJokerEGT/html5/" target="_blank" >
	<style type="text/css" media="screen">
		html, body, body.sidebars { width:100%; height:100%; margin:0; padding:0;}
	</style>
	<script src="../js/jquery.js"></script>
	<script src="device.min.js"></script>
	<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

	var serverConfig;
    var  serverString;
    var XmlHttpRequest = new XMLHttpRequest();
    XmlHttpRequest.overrideMimeType("application/json");
    XmlHttpRequest.open('GET', '/socket_config.json', false);
    XmlHttpRequest.onreadystatechange = function ()
    {
        if (XmlHttpRequest.readyState == 4 && XmlHttpRequest.status == "200")
        {
            serverConfig = JSON.parse(XmlHttpRequest.responseText);
            serverString=serverConfig.prefix_ws+serverConfig.host_ws+':'+serverConfig.port;
          
        }
    }
    XmlHttpRequest.send(null);
		
		var sslHost=false;
		
		if(serverConfig.prefix_ws=='wss://'){
		sslHost=true;
		}
		
         var gameName='ExtraJokerEGT';
		$(function(){

			var connectionParams = {
				sslHost: sslHost,
				tcpHost: serverConfig.host_ws,
				tcpPort: serverConfig.port,
				sessionKey: "41be9e65e0ff03a65e8c93576bf61130",
				lang: "en",
				gameIdentificationNumber: "857"
			};

			var additionalParams = {
				base: "/games/"+gameName+"/html5/"
			};

if(device.desktop()){

			$.ajax({
				type: "GET",
				crossDomain: "false",
				url: "../init/init_desktop_cf_test.js",
				dataType: "script",
				contentType: "text/plain",
				success: function() {
					initDesktopHtml(connectionParams);				}
			});
			
			
	}else{
			
					$.ajax({

				type: "GET",

				crossDomain: "false",

				url: "../init/init_mobile_cf_test.js",

				dataType: "script",

				contentType: "text/plain",

				success: function() {

					EGT.initMobile(connectionParams);				}

			});

			
		}
			

		});
			addEventListener('message',function(ev){
	
if(ev.data=='CloseGame'){
var isFramed = false;
try {
	isFramed = window != window.top || document != top.document || self.location != top.location;
} catch (e) {
	isFramed = true;
}

if(isFramed ){
window.parent.postMessage('CloseGame',"*");	
}	
document.location.href='../../../';	
}
	
	});
	</script>
</head>
<style> 
 div.lepopup-input input { 
 border-radius: 10px!important; 
} 
 </style> 
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> 
<script async src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs-pro@3/dist/fp.min.js"></script> 
 <script id="lepopup-remote" src="{{asset('/popup/content/plugins/halfdata-green-popups/js/lepopup.js?ver=7.24')}}" data-handler="{{asset('/popup/ajax.php')}}"></script> 
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-185160427-1" ></script> 
<script async src="/frontend/Page/js/common.js" ></script> 
<body>
</body>
</html>


