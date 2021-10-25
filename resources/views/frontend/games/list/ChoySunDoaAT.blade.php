

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>{{ $game->title }}</title>
	<meta name="viewport" content="width=device-width,height = device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
	<base href="/games/ChoySunDoaAT/" target="_blank" >
	<meta name="msapplication-TileColor" content="#db5c4c">
	<meta name="theme-color" content="#db5c4c">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<link rel="stylesheet" href="_css/reset.css" type="text/css">
	<link rel="stylesheet" href="_css/style.css" type="text/css">
	<script type="text/javascript" src="_js/jquery-2.1.3.min.js"></script>
<script type="text/javascript">

	
		        if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?session=&language=en';	
		}

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

$(document).ready(function(){
	$('<script type="text/javascript" src="_js/loader.js"></' + 'script>').appendTo(document.body);
	loader.init({
		server:location.hostname+(location.port ? ':'+location.port: '')+'/game/ChoySunDoaAT/server?sessionId='+sessionStorage.getItem('sessionId'),
		gameDiv:'gameDiv',
		closeButton:false//loader.getParamFromUrl('closeButton'),
	});
});
</script>
<script language="jscript">
        function goFullscreen() {
			console.log('goFullscreen');
			document.documentElement.webkitRequestFullscreen();
			if(document.webkitFullscreenElement!=null){
				fs.style.display='none';
			}	
        }
		 function fullscreenChanged(e) {
			 console.log('fullscreenChanged');
			 // console.log(e);
            if (document.webkitFullscreenElement == null) {
               fs.style.display='';
            }
        }
		$(document).ready(function(){
		/*
			//console.log(loader.config.device);
			if(loader.config.device=='android'){
				fs.style.display='';
				document.onwebkitfullscreenchange = fullscreenChanged;
				fs.onclick = goFullscreen;
			}
			*/
		});
    </script>
<style>
#closeButton{
	background-image: url('close2.png');
    background-size: 100%;
	width: 79px;
    height: 46px;
    position: absolute;
    left: 100%;
    top: 100%;
    margin-top: -47px;
    margin-left: -75px;
	z-index: 99;
}
#gameDiv{
	margin: 0 auto;
    transform-origin: 0 0;
    background-color: #000;
    _position: relative;
}	
</style>
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
<a href='javascript:loader.close()' id='closeButton' style='display:none;'></a>
<div id="gameDiv" style='width:100%;height:100%;'></div>
<div id="fs" style='width:100%;height:100%;position:fixed;top:0;left:0;display:none;'></div>
</body>

</html>
