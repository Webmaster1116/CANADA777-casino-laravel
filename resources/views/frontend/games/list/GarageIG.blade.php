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



<body style="margin:0px;width:100%;background-color:black;overflow:hidden">
<script>

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }
	
addEventListener('message',function(ev){
	
if(ev.data=='CloseGame'){

console.log(window != window.top || document != top.document || self.location != top.location);
	
if(window != window.top || document != top.document || self.location != top.location){
var isFramed = false;
try {
	isFramed = window != window.top || document != top.document || self.location != top.location;
} catch (e) {
	isFramed = true;
}

if(isFramed ){
window.parent.postMessage('CloseGame',"*");	
}		
window.parent.postMessage({
    'func': 'parentFunc',
    'message': 'close'
}, "*");
	
}else{
document.location.href='../../';	
}	
	
	
}
	
	});
</script>


<iframe id='game' style="margin:0px;border:0px;width:100%;height:100vh;" src='/games/GarageIG/index.html?curr=CAD&lang=en' allowfullscreen>


</iframe>




</body>
<script>
	function	FormatViewport(){
	
var gm=document.getElementById("game");	

gm.style['height']=window.innerHeight+'px';	
	
}
	
	
window.onresize=FormatViewport;	
FormatViewport();	
</script>
</html>
