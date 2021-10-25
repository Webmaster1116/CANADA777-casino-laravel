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

<body style="margin:0px;background-color:black;overflow:hidden">

    <script src="/games/RumpelWildspinsGT/js/lib/pixi.min.js"></script>
    <script src="/games/RumpelWildspinsGT/js/lib/createjs.min.js"></script>

    <script src="/games/RumpelWildspinsGT/js/classes/GameButton.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameBack.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameUI.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameView.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameReels.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameLines.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameCounters.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/classes/GameRules.js" type="text/javascript"></script>
	
	
	@if ($slot->slotGamble)
		<script src="/games/RumpelWildspinsGT/js/classes/GameGamble.js" type="text/javascript"></script>
	@endif
	
	@if ($slot->slotBonus)
		<script src="/games/RumpelWildspinsGT/js/classes/GameBonus.js" type="text/javascript"></script>
	@endif
	
    <script src="/games/RumpelWildspinsGT/js/classes/GameMessages.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/core.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/utils.js" type="text/javascript"></script>
    <script src="/games/RumpelWildspinsGT/js/loader.js" type="text/javascript"></script>

    <script src="/games/RumpelWildspinsGT/js/classes/Sounds.js" type="text/javascript"></script>


    <script>
        var isFontLoaded = false;

    if( !sessionStorage.getItem('sessionId') ){
        sessionStorage.setItem('sessionId', parseInt(Math.random() * 1000000));
    }

        window.WebFontConfig = {
            google: {
                families: ['Verdana Regular', 'Verdana Bold', 'Arial Regular', 'Arial Bold', 'Roboto Bold', 'Roboto', 'Roboto Light']
            },
            active: function() {
                isFontLoaded = true;
                InitializeGame();
            }
        };

        (function() {
            var wf = document.createElement('script');
            wf.src = '/games/RumpelWildspinsGT/js/lib/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'false';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);

        })();
    </script>
</body>

</html>
