
<!DOCTYPE html>

<html>

	<head>

	<base href="/games/{{ $game->name }}/">
         <title>{{ $game->title }}</title>

		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

		<meta name="apple-mobile-web-app-capable" content="yes" />

		<meta name="format-detection" content="telephone=no" />

		<meta name="viewport" content="width=device-width, initial-scale=1.0001, minimum-scale=1.0001, maximum-scale=1.0001, user-scalable=no" />

		

		<link rel="stylesheet" crossorigin="anonymous" href="/games/TropicalVacationKenoGV/keno/games/tropical_vacation/main.min.css?1583684696569" />

		          <script type="text/javascript">



    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
	var serverString='';

    var XmlHttpRequest = new XMLHttpRequest();
    XmlHttpRequest.overrideMimeType("application/json");
    XmlHttpRequest.open('GET', '/socket_config.json', false);
    XmlHttpRequest.onreadystatechange = function ()
    {
        if (XmlHttpRequest.readyState == 4 && XmlHttpRequest.status == "200")
        {
            var serverConfig = JSON.parse(XmlHttpRequest.responseText);
            serverString=serverConfig.prefix_ws+serverConfig.host_ws+':'+serverConfig.port;
          
        }
    }
    XmlHttpRequest.send(null);


</script>

		<script type="text/javascript">var base = '/games/TropicalVacationKenoGV/keno/games/tropical_vacation/';</script>

		<script type="text/javascript" src="/games/TropicalVacationKenoGV/socket.io/socket.io.js?1583660520587"></script>

		<script type="text/javascript" src="/games/TropicalVacationKenoGV/keno/games/tropical_vacation/main.min.js?1583684696569"></script>

		

		<link rel="stylesheet" crossorigin="anonymous" href="/games/TropicalVacationKenoGV/connector/connector.css?1583660520587" />

		<script type="text/javascript" src="/games/TropicalVacationKenoGV/connector/connector.js?1583660520587"></script>

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



<div id="game" data-engine="keno" data-game="tropical_vacation" data-version="2.0.0-20160520T162016"></div>


	</body>

</html>

