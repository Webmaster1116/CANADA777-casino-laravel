 <!DOCTYPE html>
<html>
<head>
    <title>{{ $game->title }}</title>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
      <style>
         body,
         html {
         position: fixed;
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

<script>

document.cookie = 'phpsessid=; Max-Age=0; path=/; domain=' + location.host; 
document.cookie = 'PHPSESSID=; Max-Age=0; path=/; domain=' + location.host;

 window.console={ log:function(){}, error:function(){} };       
 window.onerror=function(){return true};

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
document.location.href='../../../';	
}
	
	});
	
</script>

<body style="margin:0px;width:100%;background-color:black;overflow:hidden">



<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/AztecGoldMegawaysISB/pulse_aztec_gold.html?name=255,fun&password=fun&lang=en&currency=&funmode=true&rulesUrl=&skinid=200203&channelautodetection=ON&allowFullScreen=true&cachebuster=cef08a523e42f0feba6271b0d0152677c9016bd9&enableConsole=false&newSkinIDFormat=true' allowfullscreen>


</iframe>




</body>
<script rel="javascript" type="text/javascript" src="/games/{{ $game->name }}/device.js"></script>

<script>
if(!device.desktop()){

window.location.replace(document.getElementById('game').src);

	
}
</script>
</html>

