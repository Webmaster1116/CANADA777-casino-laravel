<!DOCTYPE html>
<html>
<head>
    <title>{{ $game->title }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
<body style="margin: 0px;">
    <div class="back" id="backDiv">
        <!-- GAMES - BEGIN -->
        <img class="back img"/>
        <div class="play" onClick="doPlay();"></div>
        <div class="btnBack" onClick="history.back();"></div>
    </div>
    <iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;display:none" src='/games/TurnYourFortuneNET/games/turnyourfortune-client/game/turnyourfortune-client.xhtml?flashParams.bgcolor=000000&gameId=turnyourfortune-client&mobileParams.lobbyURL=&server=&lang=en&sessId=DEMO-3901711636-EUR&lang=en&sessId=&operatorId=netent' allowfullscreen>


</iframe>
</body>
<link rel="stylesheet" type="text/css" href="https://static.canada777.com/frontend/Default//css/init.css">
<script src="/frontend/Default/js/screenfull.js"></script>
<script>
if( !sessionStorage.getItem('sessionId') ){
    sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
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
document.location.href='../../';	
}

});


function ResizeHandler(){

var frm=document.getElementById('game');	

frm.style['height']=window.innerHeight+'px';	

}	

addEventListener('resize',ResizeHandler);	
addEventListener('orientationchange',ResizeHandler);
function fullScreen() {
    var docElm = document.documentElement;
    if (docElm.requestFullscreen) {
        docElm.requestFullscreen();
    }
    else if (docElm.mozRequestFullScree) {
        docElm.mozRequestFullScreen();
    }
    else if (docElm.webkitRequestFullScreen) {
        docElm.webkitRequestFullScreen();
    }
}
function doPlay()
{
    document.getElementById("backDiv").style["display"]= 'none';
    document.getElementById("game").style["display"]= 'block';    
    screenfull.request(document.getElementById('game'));
    // fullScreen();
}
</script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/addon.js"></script>
</html>
