@extends('frontend.Default.layouts.app')
@section('slider')
<section id="hero-section">
    <iframe class="d-sm-block" style="overflow:hidden !important; height:450px; padding:0px !important; margin:0px !important; border: none !important;" width="100%" src="http://canada777.com/slides/slide.php" allowfullscreen scrolling="no"></iframe>
</section>
@endsection
@section('content')
<section id="game-list">
    <!-- GAMES - BEGIN -->

    <div class="section-title">
        <h3>{{$currentListTitle}} Games</h3>
        <input type="hidden" id="currentListTitleInput" value="{{$currentSliderNum}}">
    </div>
    <div class="game-category-section">
        <div class="section-content" id="section-game">
        @if ($games && count($games) > 0)
            @foreach ($games as $key=>$game)
            <div class="game-item">
                <img
                    class="lazyload"
                    data-src="https://static.canada777.com/frontend/Default/ico/{{$game->name.'.jpg'}}"
                    {{-- src="https://static.canada777.com/frontend/Default/ico/{{$game->name.'.jpg'}}" --}}
                />
                <div class="game-overlay">
                    @if(Auth::check())
                        @if (strpos($game->name, 'NET') !== false)
                        <a href="{{ route('frontend.game.go.prego', ['game'=>$game->name, 'prego'=>'realgo']) }}">Play For Real</a>
                        @else
                        <a href="{{ route('frontend.game.init', ['game'=>$game->name]) }}">Play For Real</a>
                        @endif
                    @else
                    <a href="javascript:fn_playreal_auth()">Play For Real</a>
                    @endif

                    @if (strpos($game->name, 'NET') !== false)
                    <a href="{{ route('frontend.game.go.prego', ['game'=>$game->name, 'prego'=>'prego']) }}">Play For Fun</a>
                    @else
                    <a href="{{ route('frontend.game.init', ['game'=>$game->name, 'prego'=>'1']) }}">Play For Fun</a>
                    @endif
                </div>
                @if($game->label)
                    @if ($game->label == "hot" || $game->label == "new" || $game->label == "exclusive" || $game->label == "top")
                        <div class="bage-game-item"><div class="bage-label bage-label-{{$game->label}}">{{$game->label}}</div></div>
                    @endif
                @endif
            </div>
            @endforeach
        @endif

        @if ($api_games && count($api_games) > 0)
        @foreach ($api_games as $key=> $api_game)
            <div class="game-item api-game-item">
                <img class="lazyload" data-src="{{ $api_game['name'] ? $api_game['image_filled'] : '' }}" src="{{ $api_game['name'] ? $api_game['image_filled'] : '' }}" alt="{{ $api_game['name'] }}" />
                <div class="game-overlay">
                    @if(Auth::check())
                    <a href="{{ route('frontend.game.apigame', ['game'=>$api_game['game_id'], 'type'=>'api_go']) }}">Play For Real</a>
                        @if($api_game['play_for_fun_supported'] == 1)
                            <a href="{{ route('frontend.game.apigame', ['game'=>$api_game['game_id'], 'type'=>'demo_go']) }}" >Play For Fun</a>
                        @endif
                    @else
                    <a href="javascript:fn_playreal_auth()">Play For Real</a>
                        @if($api_game['play_for_fun_supported'] == 1)
                            <a href="{{ route('frontend.game.apigame', ['game'=>$api_game['game_id'], 'type'=>'demo_go']) }}" >Play For Fun</a>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        @endif
        </div>
    </div>
    @if(!$search_game)
        @if($category1 == "hot" || $category1 == "new")
            @if($games_loadmore == "more")
            <div style="text-align: center; margin: 20px;">
                <button id="btn_loadmore_game" onclick="fn_loadmore('GAME','{{$currentSliderNum}}')" class="btn btn-outline-secondary btn-lg">Load More</button>
            </div>
            @endif
        @else
            @if($games_loadmore == "more" || $apigames_loadmore == "more")
            <div style="text-align: center; margin: 20px;">
                <button id="btn_loadmore_game" onclick="fn_loadmore('GAME','{{$currentSliderNum}}')" class="btn btn-outline-secondary btn-lg">Load More</button>
            </div>
            @endif
        @endif
    @endif
</section>
@endsection
@section('page_bottom')
<script>
    // window.addEventListener('scroll', function() {
    //     var element = document.querySelector('#main_footer');
    //     var position = element.getBoundingClientRect();
    //
    //     // checking for partial visibility
    //     if(position.top < window.innerHeight && position.bottom >= 0) {
    //         fn_loadmore('GAME', $("#currentListTitleInput").val());
    //     }
    // });
    var page_hot = 0;
    var page_new = 0;
    var page_game = 0;
    fn_playreal_auth=()=>{
        $("#signin-modal").modal({
            fadeDuration: 300
        });
    }

    fn_loadmore=(type, category)=>{
        if(type == "HOT"){
            page_hot++;
        }
        else if(type == "NEW"){
            page_new++;
        }
        else if(type == "GAME"){
            page_game++;
        }
        $.ajax({
            url:"{{ route('frontend.loadmore.game') }}",
            type:"GET",
            data:{
                pagehot:page_hot,
                pagenew:page_new,
                pagegame:page_game,
                type:type,
                category:category
            },
            dataType:"JSON",
            success:(data)=>{
                var games = data.games;
                var apigames = data.api_games;
                var section_game = "";
                var games_loadmore = data.games_loadmore;
                var apigames_loadmore = data.apigames_loadmore;
                var cur_category = data.current_category;

                switch (data.type) {
                    case "HOT":
                        if(games_loadmore == "nomore"){
                            $("#btn_loadmore_hot").hide();
                        }
                        break;
                    case "NEW":
                        if(games_loadmore == "nomore"){
                            $("#btn_loadmore_new").hide();
                        }
                    case "GAME":
                        if(cur_category == "hot" || cur_category == "new"){
                            if(games_loadmore == "nomore"){
                                $("#btn_loadmore_game").hide();
                            }
                        }else {
                            if(games_loadmore == "nomore" && apigames_loadmore == "nomore"){
                                $("#btn_loadmore_game").hide();
                            }
                        }
                        break;
                    default:
                        break;
                }

                if(games.length == 0){
                    switch (data.type) {
                        case "HOT":
                            $("#btn_loadmore_hot").hide();
                            break;
                        case "NEW":
                            $("#btn_loadmore_new").hide();
                            break;
                        default:
                            break;
                    }
                }

                if(games.length == 0 && Object.keys(apigames).length == 0){
                    if(data.type == "GAME"){
                        $("#btn_loadmore_game").hide();
                        return;
                    }
                }
                if( $("#auth_status").val() != "1" ){
                    if(games.length > 0){
                        for(var i=0;i<games.length;i++) {
                            section_game+=  '<div class="game-item">\
                                                    <img src="/frontend/Default/ico/'+games[i].name+'.jpg" data-original="/frontend/Default/ico/'+games[i].name+'.jpg" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="javascript:fn_playreal_auth()">Play For Real</a>\
                                                        <a href="/game/'+games[i].name+'/prego">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                        }
                    }
                    if(Object.keys(apigames).length > 0){
                        for( val in apigames) {
                            if(apigames[val].play_for_fun_supported == 1){
                            section_game+=  '<div class="game-item api-game-item">\
                                                    <img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="javascript:fn_playreal_auth()">Play For Real</a>\
                                                        <a href="/apigame/'+apigames[val].game_id+'/demo_go">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                            }else{
                            section_game+=  '<div class="game-item api-game-item">\
                                                <img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                <div class="game-overlay">\
                                                    <a href="javascript:fn_playreal_auth()">Play For Real</a>\
                                                </div>\
                                            </div>';
                            }
                        }
                    }
                }else {
                    if(games.length > 0){
                        for(var i=0;i<games.length;i++) {
                            section_game+=  '<div class="game-item">\
                                                    <img src="/frontend/Default/ico/'+games[i].name+'.jpg" data-original="/frontend/Default/ico/'+games[i].name+'.jpg" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="/game/'+games[i].name+'/realgo">Play For Real</a>\
                                                        <a href="/game/'+games[i].name+'/prego">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                        }
                    }
                    if(Object.keys(apigames).length > 0){
                        for( val in apigames) {
                            if(apigames[val].play_for_fun_supported == 1){
                            section_game+=  '<div class="game-item api-game-item">\
                                                    <img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                    <div class="game-overlay">\
                                                        <a href="/apigame/'+apigames[val].game_id+'/api_go">Play For Real</a>\
                                                        <a href="/apigame/'+apigames[val].game_id+'/demo_go">Play For Fun</a>\
                                                    </div>\
                                                </div>';
                            }else{
                            section_game+=  '<div class="game-item api-game-item">\
                                                <img src="'+apigames[val].image_filled+'" data-original="'+apigames[val].image_filled+'" data-image-blur-on-load-update-occured="true" style="filter: opacity(1);"/>\
                                                <div class="game-overlay">\
                                                    <a href="/apigame/'+apigames[val].game_id+'/api_go">Play For Real</a>\
                                                </div>\
                                            </div>';
                            }
                        }
                    }
                }

                switch (data.type) {
                    case "HOT":
                        $("#section-hot").append(section_game);
                        break;
                    case "NEW":
                        $("#section-new").append(section_game);
                        break;
                    case "GAME":
                        $("#section-game").append(section_game);
                        break;
                    default:
                        break;
                }


            },
            error:(error)=>{
                console.log(error);
            }
        });
    }


</script>
{{-- <script type="text/javascript" src="{{asset('frontend/Page/js/jquery.bxslider.min.js')}}"></script> --}}
{{-- <script type="text/javascript" src="{{asset('frontend/Page/js/websiting.main.js')}}"></script> --}}
@endsection
