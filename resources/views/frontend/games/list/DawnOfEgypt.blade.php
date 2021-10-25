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
            
            function Logout(reason){
                document.location.href='../../';
            }
         </script>
    <iframe id='game' style="position:absolute;top:0px;margin:0px;border:0px;width:100%;height:100vh;" src='/games/{{ $game->name }}/GameLoader/Casino/IframedView.html?pid=2&gid=dawnofegypt&gameId=407&lang=en_GB&practice=1&channel=@if($is_mobile==true){{"mobile"}}@else{{"desktop"}}@endif&div=flashobject&width=100%&height=100%&user=&password=&ctx=&demo=0&brand=&lobby=&rccurrentsessiontime=0&rcintervaltime=0&rcaccounthistoryurl=&rccontinueurl=&rcexiturl=&rchistoryurlmode=&autoplaylimits=0&autoplayreset=0&callback=flashCallback&rcmga=&resourcelevel=0&hasjackpots=False&country=&pauseplay=&playlimit=&selftest=&sessiontime=&coreweburl=/&showpoweredby=True' allowfullscreen>
</iframe>




</body>


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
    var gm=document.getElementById("game");			
        
    function FormatViewport(){
        

        
        gm.style['height']=window.innerHeight+'px';	
        gm.style['top']=window.scrollY+'px';	
        
    }
        
        
    window.onresize=FormatViewport;	

setInterval(function(){
	

	


FormatViewport();		
	
	
},500);

FormatViewport();	
	
	
</script>

</html>

