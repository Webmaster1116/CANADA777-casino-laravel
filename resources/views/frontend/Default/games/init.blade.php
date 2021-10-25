<body style="margin: 0px;" onClick="fullScreen();">
    <div class="back">
        <!-- GAMES - BEGIN -->
        <img class="back img"/>
        <div class="play" onClick="location.href='/game/{{$game}}{{$prego == '1' ? '/prego' : '/realgo'}}';"></div>
        <div class="btnBack" onClick="history.back();"></div>
{{--        <div class="btnBack" onClick="location.href='/categories/all';"></div>--}}
    </div>
</body>
<style>
    .back {
        width : 100%;
        height : 100%;
    }
    .play {
        position: absolute;
        background-size: 100%;
        background-image: url(/frontend/Page/image/btn_continue.png);
        background-repeat: no-repeat;
    }
    .btnBack {
        position: absolute;
        background-size: 100%;
        background-image: url(/frontend/Page/image/btn_arrow.png);
        background-repeat: no-repeat;
    }
@media screen and (orientation:portrait) {
    .img {
        content: url(/frontend/Page/image/portrait_background.png);
    }
    .play {
        left: 26%;
        top: 83%;
        width: 50%;
        height: 17%;
    }
    .btnBack {
        left: 5%;
        top: 5%;
        width: 5%;
        height: 5%;
    }
}
/* Landscape */
@media screen and (orientation:landscape) {
    .img {
        content: url(/frontend/Page/image/background.png);
    }
    .play {
        left: 34%;
        top: 81%;
        width: 30%;
        height: 19%;
    }
    .btnBack {
        left: 5%;
        top: 5%;
        width: 3%;
        height: 7%;
    }
}
</style>
<script>
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
function fnc_fullscreen()
{
    window.moveTo(0,0);
    window.resizeTo(screen.availWidth,screen.availHeight);
}
</script>
