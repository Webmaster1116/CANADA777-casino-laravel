<!DOCTYPE HTML>
<html lang="en">
<head>
 <title>{{ $game->title }}</title>
<base href="/games/{{ $game->name }}/amarent/">
<script>

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }





if(document.location.href.split("?")[1]==undefined){
		document.location.href=document.location.href+'/?curr=CAD&lang=en&w=&lang=en';	
	}
		
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



	<meta charset="UTF-8"/>
	<meta http-equiv="Cache-Control" content="no-transform" />
	<meta http-equiv="expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
	<link media="screen" href="fixed_4.css" type= "text/css" rel="stylesheet" />
	<script src="./src/webgl-2d.js" type="text/javascript"></script>
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
    <div id="gameArea">
		<canvas id="canvas2"></canvas>
		<canvas id="canvas"></canvas>
		<div id="gameOverlay">
			<div id="jurisdictionDiv">
				<button id="btnsp" class="buttonPause"></button>
				<button id="btnsl" class="buttonLimit"></button>
				<button id="btnst" class="buttonTest"></button>
			</div>
			<div id="notificationDiv">
				<p id="notificationTitle"></p>
				<p id="notificationText"></p>
				<div id="notificationIcon">
					<p id="notificationCounter"></p>
				</div>
			</div>
			<div id="messageOverlay">
				<div id="messagePanel">
					<h3 id="messageTitle"></h3>
					<p id="messageText"></p>
					<button id="btne" class="messageTopbutton"></button>
					<button id="btn1" class="messageButton"></button>
					<button id="btn2" class="messageButton"></button>
					<button id="btn3" class="messageButton"></button>
					<button id="btn4" class="messageButton"></button>
				</div>
			</div>
		</div>
	</div>
	<div id="slideUpOverlay">
		<div id="slideUp">
			<div id="slideElem1"></div>
			<div id="slideElem2"></div>
		</div>
	</div>
	<div id="rotateOverlay">
		<div id="rotatePanel">
			<div id="rotate">
			</div>
			<div id="rotateInfo">
			</div>
		</div>
	</div>
	<script type="text/javascript" src="./src/royalunicornloader_00466940.js"></script>
</body>
</html>
