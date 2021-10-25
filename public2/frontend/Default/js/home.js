var page_hot = 0;
var page_new = 0;
var page_game = 0;

$(document).ready(function() {
    $(window).scroll(function () {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            fn_loadmore('GAME',$("#currentListTitleInput").val());
        }
    });
});

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
                                                        <a href="javascript:fn_playreal_auth()">Play For Fun</a>\
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
        error:()=>{
            alert("error");
        }
    });
}
