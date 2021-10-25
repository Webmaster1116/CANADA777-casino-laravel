<?php
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    use malkusch\lock\mutex\FlockMutex;
    class GamesController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request, $category1 = '', $category2 = '')
        {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('admin')){
                return redirect()->route('backend.dashboard');
            }

/*            $checked = new \VanguardLTE\Lib\LicenseDK();
            $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
            if( $license_notifications_array['notification_case'] != 'notification_license_ok' )
            {
                return redirect()->route('frontend.page.error_license');
            }
            if( !$this->security() )
            {
                return redirect()->route('frontend.page.error_license');
            }*/
            /*
            if( \Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->hasRole('user') )
            {
                return redirect()->route('backend.dashboard');
            }
            if( !\Illuminate\Support\Facades\Auth::check() )
            {
                return redirect()->route('frontend.auth.login');
            }
            */

            $start = microtime(true);

            $search_game = $request->search_game;
            $login_result = $request->login;
            $register_result = $request->register;
            $forgotpassword_result = $request->forgotpassword;
            $resetpassword_result = $request->resetpassword;
            $categories = [];
            $game_ids = [];
            $cat1 = false;
            $title = trans('app.games');
            $body = '';
            $keywords = '';
            $description = '';
            $games_count = 0;
            $apigames_count = 0;
            $games_loadmore = "nomore";
            $apigames_loadmore = "nomore";
            $apigamesbycategory = 0;
            $api_games = [];
            $api_hotgames = [];
            $api_newgames = [];

            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->shop_id : 0);
            $shop = \VanguardLTE\Shop::find($shop_id);
            $games = \VanguardLTE\Game::where([
                'view' => 1,
                'shop_id' => $shop_id
            ]);

            $frontend = 'Default';
            if( $shop_id && $shop )
            {
                $frontend = $shop->frontend;
            }
            if( $category1 == '' )
            {
                if( $currentCategory = $request->cookie('currentCategory') )
                {
                    $category = \VanguardLTE\Category::where([
                        'href' => $currentCategory,
                        'shop_id' => $shop_id
                    ])->first();
                    if( $category )
                    {
                        $category1 = $category->href;
                        return redirect()->route('frontend.game.list.category', [
                            'category1' => $category1,
                            'page' => $request->cookie('currentPage')
                        ]);
                    }
                }
                if( settings('use_all_categories') )
                {
                    return redirect('/home');
                    /*return redirect()->route('frontend.game.list.category', [
                        'category1' => 'all',
                        'page' => $request->cookie('currentPage')
                    ]);*/
                }
                $category = \VanguardLTE\Category::where([
                    'parent' => 0,
                    'shop_id' => $shop_id
                ])->orderBy('position')->first();
                if( $category )
                {
                    $category1 = $category->href;
                    return redirect()->route('frontend.game.list.category', $category1);
                }
            }
            \Illuminate\Support\Facades\Cookie::queue('currentCategory', $category1, 2678400);
            if( $category1 != '' )
            {
                $cat1 = \VanguardLTE\Category::where([
                    'href' => $category1,
                    'shop_id' => $shop_id
                ])->first();
                if( !$cat1 && $category1 != 'all' )
                {
                    abort(404);
                }
                if( $category2 != '' )
                {
                    $cat2 = \VanguardLTE\Category::where([
                        'href' => $category2,
                        'parent' => $cat1->id,
                        'shop_id' => $shop_id
                    ])->first();
                    if( !$cat2 )
                    {
                        abort(404);
                    }
                    $categories[] = $cat2->id;
                }
                else if( $category1 != 'all' )
                {
                    $categories = \VanguardLTE\Category::where([
                        'parent' => $cat1->id,
                        'shop_id' => $shop_id
                    ])->pluck('id')->toArray();
                    $categories[] = $cat1->id;
                }
                else
                {
                    $categories = \VanguardLTE\Category::where([
                        'parent' => 0,
                        'shop_id' => $shop_id
                    ])->pluck('id')->toArray();
                }
                if( $frontend == 'Amatic' )
                {
                    $Amatic = \VanguardLTE\Category::where([
                        'title' => 'Amatic',
                        'shop_id' => $shop_id
                    ])->first();
                    if( $Amatic )
                    {
                        $categories = \VanguardLTE\Category::where([
                            'parent' => $Amatic->id,
                            'shop_id' => $shop_id
                        ])->pluck('id')->toArray();
                        $categories[] = $Amatic->id;
                    }
                }
                if( $frontend == 'NetEnt' )
                {
                    $Amatic = \VanguardLTE\Category::where([
                        'title' => 'NetEnt',
                        'shop_id' => $shop_id
                    ])->first();
                    if( $Amatic )
                    {
                        $categories = \VanguardLTE\Category::where([
                            'parent' => $Amatic->id,
                            'shop_id' => $shop_id
                        ])->pluck('id')->toArray();
                        $categories[] = $Amatic->id;
                    }
                }
                $game_ids = \VanguardLTE\GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();

                if( count($game_ids) > 0 )
                {
                    $games = $games->whereIn('id', $game_ids);
                }
                else
                {
                    $games = $games->where('id', 0);
                }
            }

            $newgames = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
                ->leftJoin('categories','categories.id','=','game_categories.category_id')
                ->orderBy('games.new_order', 'ASC')
                ->where('categories.Title','New')
                ->where('games.new_order', "!=", NULL);

            $hotgames = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
                ->leftJoin('categories','categories.id','=','game_categories.category_id')
                ->orderBy('games.hot_order', 'ASC')
                ->where('categories.Title','Hot')
                ->where('games.hot_order', "!=", NULL);



            $detect = new \Detection\MobileDetect();
            $devices = [];
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $games = $games->whereIn('device', [
                    0,
                    2
                ]);
                $newgames = $newgames->whereIn('device', [
                    0,
                    2
                ]);
                $hotgames = $hotgames->whereIn('device', [
                    0,
                    2
                ]);
                $devices = [
                    0,
                    2
                ];
            }
            else
            {
                $games = $games->whereIn('device', [
                    1,
                    2
                ]);
                $newgames = $newgames->whereIn('device', [
                    1,
                    2
                ]);
                $hotgames = $hotgames->whereIn('device', [
                    1,
                    2
                ]);
                $devices = [
                    1,
                    2
                ];
            }
            if($search_game){
                if($category1 == 'hot'){
                    $games = $games->where('name','like','%'.$search_game.'%')->where('games.hot_order', "!=", NULL)->orderBy('games.hot_order', 'ASC')->take(10)->get();
                }else if($category1 == 'new'){
                    $games = $games->where('name','like','%'.$search_game.'%')->where('games.new_order', "!=", NULL)->orderBy('games.new_order', 'ASC')->take(10)->get();
                }else{
                    $games = $games->where('name','like','%'.$search_game.'%')->orderBy('games.order', 'ASC')->take(10)->get();
                }
            }else{
                if($category1 == 'hot'){
                    $games_count = $games->where('games.hot_order', "!=", NULL)->count();
                    $games = $games->where('games.hot_order', "!=", NULL)->orderBy('games.hot_order', 'ASC')->take(10)->get();
                }else if($category1 == 'new'){
                    $games_count = $games->where('games.new_order', "!=", NULL)->count();
                    $games = $games->where('games.new_order', "!=", NULL)->orderBy('games.new_order', 'ASC')->take(10)->get();
                }else{
                    $games_count = $games->count();
                    $games = $games->orderBy('games.order', 'ASC')->take(10)->get();
                }

                if($games_count <= 20) {
                    $games_loadmore = "nomore";
                }else {
                    $games_loadmore = "more";
                }
            }
            $hotgames = $hotgames->get();
            $newgames = $newgames->get();

            $jpgs = \VanguardLTE\JPG::get();
            $categories = false;
            $currentSliderNum = -1;
            $currentListTitle = "";
            if( $games )
            {
                $cat_ids = \VanguardLTE\GameCategory::whereIn('game_id', \VanguardLTE\Game::where([
                    'view' => 1,
                    'shop_id' => $shop_id
                ])->pluck('id'))->groupBy('category_id')->pluck('category_id');
                if( count($cat_ids) )
                {

                    $categories = \VanguardLTE\Category::whereIn('id', $cat_ids)->orwhere(['href'=> 'table', 'type' => 1])->where('shop_id', $shop_id)->orderBy('position','ASC')->get();
                    if( $category1 != '' )
                    {
                        foreach( $categories as $index => $cat )
                        {
                            if( $cat->href == $category1 )
                            {
                                $currentSliderNum = $cat->href;
                                $currentListTitle = $cat->title;
                                break;
                            }
                        }
                    }
                }
            }

            $current_shop_id =  $shop_id;
            $game_gamehub_page = 0;
            $cur_date = date("Y-m-d");
            $cur_api_games = [];
            $apigames_count = \VanguardLTE\ApiGames::where('created_at', $cur_date)->count();

            if( $apigames_count == 0) {
                $game_gamehub_api = new \VanguardLTE\Lib\games_Api;

                if($shop_id == 0){
                    $games_gamehub = $game_gamehub_api->getGameList(['currency' => 'USD']);
                }else{
                    $games_gamehub = $game_gamehub_api->getGameList(['currency' => $shop->currency]);
                }

			    if( $games_gamehub && $games_gamehub['error'] == 0 && count($games_gamehub['response']) > 0 ){

                    foreach ($games_gamehub['response'] as $key => $val) {
                        $exist_game = \VanguardLTE\ApiGames::where('game_id', (int)$val['id'])->first();
                        $api_label = '';
                        if($val['new'] == 1){
                            $api_label = 'new';
                        }
                        if(!$exist_game){
                            $model = \VanguardLTE\ApiGames::create(
                                [
                                    'game_id' => $val['id'],
                                    'name' => $val['name'],
                                    'category' => $val['category'],
                                    'subcategory' => $val['subcategory'],
                                    'new' => $val['new'],
                                    'system' => $val['system'],
                                    'position' => $val['position'],
                                    'type' => $val['type'],
                                    'image' => $val['image'],
                                    'image_preview' => $val['image_preview'],
                                    'image_filled' => $val['image_filled'],
                                    'mobile' => $val['mobile'],
                                    'play_for_fun_supported' => $val['play_for_fun_supported'],
                                    'label' => $api_label,
                                    'order' => $val['position']
                                ]
                            );
                        }else {
                            $exist_game->update(['created_at' => $cur_date, 'updated_at' => $cur_date]);
                        }
                    }
                }else {
                    $up = \VanguardLTE\ApiGames::where('created_at', '!=', $cur_date)->update(['created_at' => $cur_date, 'updated_at' => $cur_date]);
                }
            }else {
                $up = \VanguardLTE\ApiGames::where('created_at', '!=', $cur_date)->update(['created_at' => $cur_date, 'updated_at' => $cur_date]);
            }

            if( $detect->isMobile() || $detect->isTablet() ) {
                if($category1 != 'all'){
                    if($category1 == 'livecasino' || $category1 == 'jackpot' || $category1 == 'table' || strtolower($category1) == 'hot' || strtolower($category1) == 'new' || strtolower($category1) == 'pragmatic' || strtolower($category1) == 'casino-technology'){
                        if($category1 == 'livecasino'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'LIKE', '%live%casino%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'jackpot') {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('name', 'LIKE', '%'.$category1.'%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'table'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'table-games')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'hot'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'new'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'pragmatic'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('category', 'like', '%pragmatic%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_ct_gaming')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }
                    }else {
                        $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_'.$category1)->where('mobile', 1)->orderBy('order', 'ASC');
                        $apigames_count = $apigamesbycategory->count();
                        $api_games = $apigamesbycategory->take(10)->get();
                    }
                }else {
                    $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('mobile', 1)->orderBy('order', 'ASC');
                    $apigames_count = $apigamesbycategory->count();
                    $api_games = $apigamesbycategory->take(10)->get();
                }
                $api_newgames = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC')->get();
                $api_hotgames = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC')->get();
            }else {
                if($category1 != 'all'){
                    if($category1 == 'livecasino' || $category1 == 'jackpot' || $category1 == 'table' || strtolower($category1) == 'hot' || strtolower($category1) == 'new' || strtolower($category1) == 'pragmatic' || strtolower($category1) == 'casino-technology'){
                        if($category1 == 'livecasino'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'LIKE', '%live%casino%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'jackpot') {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('name', 'LIKE', '%'.$category1.'%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'table'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'table-games')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'hot'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'new'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'pragmatic'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('category', 'like', '%pragmatic%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_ct_gaming')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }
                    }else {
                        $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_'.$category1)->where('mobile', 0)->orderBy('order', 'ASC');
                        $apigames_count = $apigamesbycategory->count();
                        $api_games = $apigamesbycategory->take(10)->get();
                    }
                }else {
                    $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('mobile', 0)->orderBy('order', 'ASC');
                    $apigames_count = $apigamesbycategory->count();
                    $api_games = $apigamesbycategory->take(10)->get();
                }
                $api_newgames = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC')->get();
                $api_hotgames = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC')->get();
            }

            if($apigames_count <= 20){
                $apigames_loadmore = "nomore";
            }else {
                $apigames_loadmore = "more";
            }
            // if( settings('user_all_categories') && $category1 == 'all' )
            if( $category1 == 'all' )
            {
                $currentSliderNum = 'all';
                $currentListTitle = 'All';
            }

            $countrys =  \VanguardLTE\Country::orderBy('ranking','ASC')
                                            ->orderBy('country','ASC')->get();
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $provinces = \VanguardLTE\Province::where('country', 'CND')
                                            ->orderBy('name','ASC')->get();
            $states = \VanguardLTE\Province::where('country', 'USA')
                                            ->orderBy('name','ASC')->get();
            $realBalance = 0;
            $bonusBalance = 0;

            $time_elapsed_secs = microtime(true) - $start;
            // dd($time_elapsed_secs);

            return view('frontend.' . $frontend . '.games.list', compact('games', 'api_games', 'hotgames', 'newgames','category1', 'cat1', 'categories', 'currentSliderNum', 'currentListTitle','title', 'body', 'keywords', 'description', 'jpgs', 'devices', 'countrys', 'currencys','search_game','login_result','register_result','forgotpassword_result','resetpassword_result', 'games_loadmore', 'apigames_loadmore', 'current_shop_id', 'provinces', 'states', 'api_newgames', 'api_hotgames'));
        }

        public function loadmore(\Illuminate\Http\Request $request){
            $games_loadmore = "";
            $newgames_loadmore = "";
            $hotgames_loadmore = "";
            $apigames_loadmore = "";
            $games_count = 0;
            $newgames_count = 0;
            $hotgames_count = 0;
            $apigames_count = 0;
            $apigamesbycategory = 0;
            $api_games = [];

            $gametype = $request->type;
            $category = $request->category;

            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->shop_id : 0);
            $shop = \VanguardLTE\Shop::find($shop_id);

            $games = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
                                      ->leftJoin('categories','categories.id','=','game_categories.category_id')
                                      ->orderBy('games.order', 'ASC')
                                      ->where('games.shop_id', $shop_id);

            $detect = new \Detection\MobileDetect();
            $devices = [];

            if($gametype == "HOT"){
                $page = $request->pagehot;
                $hotgames_count = $games->where('categories.title', 'Hot')->count();
                $games = $games->where('categories.title', 'Hot')->skip($page*10)->take(10);

                if( $hotgames_count <= ($page + 1) * 20 ) {
                    $games_loadmore = "nomore";
                }else{
                    $games_loadmore = "more";
                }
                $api_games = [];
            }
            else if($gametype == "NEW"){
                $page = $request->pagenew;
                $newgames_count = $games->where('categories.title', 'New')->count();
                $games = $games->where('categories.title','New')->skip($page*10)->take(10);
                if( $newgames_count <= ($page + 1) * 20 ) {
                    $games_loadmore = "nomore";
                }else{
                    $games_loadmore = "more";
                }
                $api_games = [];
            }
            else if($gametype == "GAME"){
                $page = $request->pagegame;

                if($category == "All" || $category == "all"){
                    $games_count = $games->groupBy('games.id')->count();
                    $games = $games->groupBy('games.id')->skip($page*10)->take(10);
                    if( $detect->isMobile() || $detect->isTablet() ) {
                        $api_games = \VanguardLTE\ApiGames::where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                        $apigames_count = \VanguardLTE\ApiGames::where('mobile', 1)->orderBy('order', 'ASC')->count();
                    }else {
                        $api_games = \VanguardLTE\ApiGames::where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                        $apigames_count = \VanguardLTE\ApiGames::where('mobile', 0)->orderBy('order', 'ASC')->count();
                    }
                }else{
                    if( $category == 'table'){
                        $games_count = $games->where('categories.href', $category)->orwhere('href', 'card')->orwhere('href', 'roulette')->count();
                        $games = $games->where('categories.href', $category)->orwhere('href', 'card')->orwhere('href', 'roulette')->orderBy('order', 'ASC')->skip($page*10)->take(10);
                    }else {
                        $games_count = $games->where('categories.href', $category)->count();
                        $games = $games->where('categories.href', $category)->orderBy('order', 'ASC')->skip($page*10)->take(10);
                    }
                    if( $detect->isMobile() || $detect->isTablet() ) {
                        if($category == 'livecasino' || $category == 'jackpot' || $category == 'table' || strtolower($category) == 'hot' || strtolower($category) == 'new' || strtolower($category) == 'pragmatic' || strtolower($category) == 'casino-technology'){
                            if($category == 'livecasino'){
                                $api_games = \VanguardLTE\ApiGames::where('type', 'LIKE', '%live%casino%')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('type', 'LIKE', '%live%casino%')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else if($category == 'jackpot') {
                                $api_games = \VanguardLTE\ApiGames::where('name', 'LIKE', '%'.strtolower($category).'%')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('name', 'LIKE', '%'.strtolower($category).'%')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else if($category == 'table') {
                                $api_games = \VanguardLTE\ApiGames::where('type', 'table-games')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('name', 'table-games')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'hot') {
                                $api_games = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'new') {
                                $api_games = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'pragmatic'){
                                $api_games = \VanguardLTE\ApiGames::where('category', 'like', '%pragmatic%')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('category', 'like', '%pragmatic%')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }else {
                                $api_games = \VanguardLTE\ApiGames::where('subcategory', '_ct_gaming')->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('subcategory', '_ct_gaming')->where('mobile', 1)->orderBy('order', 'ASC')->count();
                            }
                        }else {
                            $api_games = \VanguardLTE\ApiGames::where('subcategory', '_'.strtolower($category))->where('mobile', 1)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                            $apigames_count = \VanguardLTE\ApiGames::where('subcategory', '_'.strtolower($category))->where('mobile', 1)->orderBy('order', 'ASC')->count();
                        }

                    }else {
                        if($category == 'livecasino' || $category == 'jackpot' || $category == 'table' || strtolower($category) == 'hot' || strtolower($category) == 'new' || strtolower($category) == 'pragmatic' || strtolower($category) == 'casino-technology'){
                            if($category == 'livecasino'){
                                $api_games = \VanguardLTE\ApiGames::where('type', 'LIKE', '%live%casino%')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('type', 'LIKE', '%live%casino%')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else if($category == 'jackpot') {
                                $api_games = \VanguardLTE\ApiGames::where('name', 'LIKE', '%'.strtolower($category).'%')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('name', 'LIKE', '%'.strtolower($category).'%')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else if($category == 'table'){
                                $api_games = \VanguardLTE\ApiGames::where('type', 'table-games')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('type', 'table-games')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'hot') {
                                $api_games = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'new') {
                                $api_games = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else if(strtolower($category) == 'pragmatic'){
                                $api_games = \VanguardLTE\ApiGames::where('category', 'like', '%pragmatic%')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('category', 'like', '%pragmatic%')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }else {
                                $api_games = \VanguardLTE\ApiGames::where('subcategory', '_ct_gaming')->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                                $apigames_count = \VanguardLTE\ApiGames::where('subcategory', '_ct_gaming')->where('mobile', 0)->orderBy('order', 'ASC')->count();
                            }
                        }else {
                            $api_games = \VanguardLTE\ApiGames::where('subcategory', '_'.strtolower($category))->where('mobile', 0)->orderBy('order', 'ASC')->skip($page*10)->take(10)->get();
                            $apigames_count = \VanguardLTE\ApiGames::where('subcategory', '_'.strtolower($category))->where('mobile', 0)->orderBy('order', 'ASC')->count();
                        }

                    }
                }
                if( $games_count <= ( $page + 1 ) * 20 ) {
                    $games_loadmore ="nomore";
                }else{
                    $games_loadmore ="more";
                }

                if( $apigames_count < ($page + 1) * 20){
                    $apigames_loadmore = "nomore";
                }else {
                    $apigames_loadmore = "more";
                }
            }

            if( $detect->isMobile() || $detect->isTablet() )
            {
                $games = $games->whereIn('device', [
                    0,
                    2
                ]);
                $devices = [
                    0,
                    2
                ];
            }
            else
            {
                $games = $games->whereIn('device', [
                    1,
                    2
                ]);
                $devices = [
                    1,
                    2
                ];
            }
            $games = $games->get();

	        return response(json_encode([
                'type' => $gametype,
                'api_games' => $api_games,
                'current_category' => strtolower($category),
                'games' => $games,
                'games_loadmore' => $games_loadmore,
                'apigames_loadmore' => $apigames_loadmore,
            ]));
        }
        public function setpage(\Illuminate\Http\Request $request)
        {
            $cookie = cookie('currentPage', $request->page, 2678400);
            return response()->json([
                'success' => true,
                'page' => $request->page
            ])->cookie($cookie);
        }

        public function searchgame(\Illuminate\Http\Request $request){
            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->shop_id : 0);
            if( $shop_id )
            {
                $shop = \VanguardLTE\Shop::find($shop_id);
            }
            $query = (isset($request->keyword) ? $request->keyword : '');
            $games = \VanguardLTE\Game::where('view', 1);
            $games = $games->where('shop_id', $shop_id);

            $games = $games->where(function ($games) use ($query) {
                $games = $games->where('name', 'like', '%' . $query . '%')->orWhere('title', 'like', '%' . $query . '%');
            });

            $detect = new \Detection\MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $games = $games->whereIn('device', [
                    0,
                    2
                ]);
            }
            else
            {
                $games = $games->whereIn('device', [
                    1,
                    2
                ]);
            }
            $games = $games->orderBy('name', 'ASC');
            $games = $games->get();

            /* search api games */
            $apigames = \VanguardLTE\ApiGames::where('name', 'like', '%' . $query . '%');
            $detect = new \Detection\MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $apigames = $apigames->where('mobile', 1);
            }
            else
            {
                $apigames = $apigames->where('mobile',  0);
            }
            $apigames = $apigames->orderBy('name', 'ASC')->get();
            /* --- */
            return response(json_encode([
                'games' => $games,
                'apigames' => $apigames
            ]));
        }

        public function search(\Illuminate\Http\Request $request)
        {
            if( \Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->hasRole('user') )
            {
                return redirect()->route('backend.dashboard');
            }
            if( !\Illuminate\Support\Facades\Auth::check() )
            {
                return redirect()->route('frontend.auth.login');
            }
            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->shop_id : 0);
            $frontend = 'Default';
            if( $shop_id )
            {
                $shop = \VanguardLTE\Shop::find($shop_id);
                if( $shop )
                {
                    $frontend = $shop->frontend;
                }
            }
            $query = (isset($request->q) ? $request->q : '');
            $games = \VanguardLTE\Game::where([
                'view' => 1,
                'shop_id' => $shop_id
            ]);

            $categories = [];
            $categories = \VanguardLTE\Category::where([
                'parent' => 0,
                'shop_id' => $shop_id
            ])->pluck('id')->toArray();
            $game_ids = \VanguardLTE\GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();
            if( count($game_ids) > 0 )
            {
                $games = $games->whereIn('id', $game_ids);
            }
            else
            {
                $games = $games->where('id', 0);
            }

            $games = $games->where('name', 'like', '%' . $query . '%');
            $detect = new \Detection\MobileDetect();
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $games = $games->whereIn('device', [
                    0,
                    2
                ]);
            }
            else
            {
                $games = $games->whereIn('device', [
                    1,
                    2
                ]);
            }
            $games = $games->orderBy('name', 'ASC');
            $games = $games->get();
            return view('frontend.' . $frontend . '.games.search', compact('games'));
        }
        public function init(\Illuminate\Http\Request $request)
        {
            $game = $request->game;
            $prego = 0;
            if (isset($request->prego)){
                $prego = $request->prego;
            }
            return view('frontend.Default.games.init', compact('game', 'prego'));
        }
        public function go(\Illuminate\Http\Request $request, $game, $prego='')
        {
            if($prego == 'realgo'){
                if( \Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->hasRole('user') )
                {
                    return redirect()->route('backend.dashboard');
                }
                if( !\Illuminate\Support\Facades\Auth::check() )
                {
                    return redirect()->route('frontend.game.list');
                }
                $userId = \Illuminate\Support\Facades\Auth::id();
                $shopId = \Illuminate\Support\Facades\Auth::user()->shop_id;
                $request->session()->put('freeUserID', 0);
                $gameMode = "go";
            }else if($prego == 'prego') {
                $freeShopID = 1;
                $freeUser = \VanguardLTE\User::where('shop_id', $freeShopID)->orderBy('last_login', 'asc')->first();
                if(!isset($freeUser)){
                    $userId = 1;
                }else{
                    $freeUser->update([
                        'balance' => 10000,
                        'count_balance' => 10000,
                        'last_login' => new \DateTime("now", new \DateTimeZone("UTC")),
                        'session' => ''
                    ]);
                    $userId = $freeUser->id;
                }
                $request->session()->put('freeUserID', $userId);
                $shopId = $freeShopID;
                $gamemode = "prego";
            }

            $detect = new \Detection\MobileDetect();
            $object = '\VanguardLTE\Games\\' . $game . '\SlotSettings';
            $slot = new $object($game, $userId);
            $game = \VanguardLTE\Game::where([
                'name' => $game,
                'shop_id' => $shopId
            ]);
            $is_mobile = false;
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $is_mobile = true;
                $game = $game->whereIn('device', [
                    0,
                    2
                ]);
            }
            else
            {
                $game = $game->whereIn('device', [
                    1,
                    2
                ]);
            }
            $game = $game->first();
            if( !$game )
            {
                return redirect()->route('frontend.game.list');
            }
            if( !$game->view )
            {
                return redirect()->route('frontend.game.list');
            }
            $is_api = false;
            return view('frontend.games.list.' . $game->name, compact('slot', 'game', 'is_api', 'is_mobile'));
        }
        public function apigame(\Illuminate\Http\Request $request, $game, $type){
            $play_for_fun = 0;
            $frontend = 'Default';
            $detect = new \Detection\MobileDetect();
            $games = new \VanguardLTE\Lib\games_Api;

            if($type == "api_go"){
                $play_for_fun = 0;
                if (!\Illuminate\Support\Facades\Auth::check()) {
                    return redirect()->route('frontend.auth.login');
                }

                $users = \Auth::user();
                $Shop = \VanguardLTE\Shop::find($users->shop_id);

                if( $Shop === null){
                    $currency = 'USD' ;
                }else {
                    $currency = $Shop->currency;
                }
                $datetime = Date("Y-m-d-H-m-i");
                $tionApi = \VanguardLTE\UsersRegistrationApi::where('user_id', $users->id)->first();
                if ($tionApi === null) {
                    $password = password_hash($users->username.$datetime, PASSWORD_DEFAULT);

                    $tionApi = new \VanguardLTE\UsersRegistrationApi();
                    $tionApi->user_id = $users->id;
                    $tionApi->password = $password;
                    $tionApi->usersname = $users->username;
                    $tionApi->currency = $Shop->currency ? $Shop->currency : 'EUR';
                    $tionApi->save();

                    $createPlayer = $games->createPlayer(
                        [
                            "user_username" => $users->username, //should be unique - you can use your internal ID for this parameter
                            "user_password" => $password,
                            "user_nickname" => $users->username, //optional - non unique nickname of a player that is showed in some providers. If not passed user_username is used
                            "currency" => $Shop->currency ? $Shop->currency : 'EUR'
                        ]
                    );
                    if ($createPlayer['error'] == 0) {
                        return redirect()->route('frontend.game.apigame', ['game' => $game, 'type' => $type]);
                    }
                }

                $play = $games->getGame(
                    [
                        'lang' => $users->language,
                        'user_username' => $tionApi->usersname, //not required for fun mode
                        'user_password' => $tionApi->password, //not required for fun mode
                        'game_id' => $game, // you can also use game hash from getGameList to start a game - for example ne#ne-jingle-spin
                        'homeurl' => "https://canada777.com/",
                        'method'  => 'getGame',
                        'play_for_fun' => 0,
                        'currency' => $tionApi->currency
                    ]
                );

                if ($play['error'] == 0) {
                    if ($type == 'api_go') {

                        $GamesessionID = new \VanguardLTE\UserGamesessionID();
                        $GamesessionID->user_id = $users->id;
                        $GamesessionID->session_id = $play['gamesession_id'];
                        $GamesessionID->game_id = $game;
                        $GamesessionID->save();
                    }
                    return view('frontend.' . $frontend . '.games.index', compact('play', 'play_for_fun'));
                } else {
                    return redirect()->route('frontend.game.list');
                }
            }else {
                $play_for_fun = 1;
                $play = $games->getGameDemo(
                    [
                        'lang' => 'en',
                        'game_id' => $game, // you can also use game hash from getGameList to start a game - for example ne#ne-jingle-spin
                        'homeurl' => "https://canada777.com/",
                        'method'  => 'getGame',
                        'play_for_fun' => 1,
                        'currency' => "EUR"
                    ]
                );
                if ($play['error'] == 0) {
                    return view('frontend.' . $frontend . '.games.index', compact('play', 'play_for_fun'));
                } else {
                    return redirect()->route('frontend.game.list');
                }
            }

        }
        public function callback_gamehub(\Illuminate\Http\Request $request)
		{
            // $mutex = new FlockMutex(fopen(base_path()."/public/gamehub_lock.txt", "r"));
            $mutex = new FlockMutex(fopen(__FILE__, "r"));
            header('Content-Type: application/json');

            $data = $_GET;
            $mutex->synchronized(function() use ($data) {
                $salt = "g34AQqFyq";
                if (!isset($_GET['action'])) {
                    exit();
                }
                $key = $data['key'];
                unset($data['key']);
                $hash = sha1($salt.http_build_query($data));
                if( $key != $hash ){
                    echo json_encode(
                        [
                            'status' => 500,
                            "msg" => "wrong salt key"
                        ]
                    );
                    return true;
                }

                //Number of credits
                if ($_GET['action'] == 'balance') {

                    if(!isset($_GET['username'])|| $_GET['username'] == null ||  $_GET['username'] == ""){
                        echo json_encode(
                            [
                                "status" => 500,
                                "msg" => "internal error"
                            ]
                        );
                        return true;
                    }
                    // $player = \VanguardLTE\UsersRegistrationApi::where('usersname', $_GET['username'])->first();

                    $gamesession_id = $_GET['gamesession_id'];

                    // $tionApi = \VanguardLTE\UserGamesessionID::where('session_id', $gamesession_id)->where('user_id', $player->user_id)->first();
                    // if ($tionApi === null) {
                    // 	return response()->json(
                    // 		[
                    // 			"status" => 500,
                    // 		],
                    // 		200
                    // 	);
                    // }
                    // $users = \VanguardLTE\User::where('id', '=', $tionApi->user_id)->get();
                    $users = \VanguardLTE\User::where('username', '=', $_GET['username'])->lockForUpdate()->get();
                    if ($users[0] === null) {
                        echo json_encode(
                            [
                                "status" => 500,
                            ]
                        );
                        return true;
                    }
                    echo json_encode(
                        [
                            'status' => 200,
                            'balance' => floor($users[0]->balance * 100) / 100
                        ]
                    );
                    return true;
                }
                //Withdrawing credits
                if ($_GET['action'] == 'debit') {

                    if(!isset($_GET['gamesession_id']) || !isset($_GET['game_id']) || !isset($_GET['username']) || !isset($_GET['transaction_id']) || !isset($_GET['amount']) || $_GET['gamesession_id'] == null || $_GET['game_id'] == null || $_GET['username'] == null || $_GET['transaction_id'] == null || $_GET['amount'] == null || $_GET['gamesession_id'] == "" || $_GET['game_id'] == "" || $_GET['username'] == "" || $_GET['transaction_id'] == "" || $_GET['amount'] == "" ){
                        echo json_encode(
                            [
                                "status" => 500,
                            ]
                        );
                        return true;
                    }

                    // $player = \VanguardLTE\UsersRegistrationApi::lockForUpdate()->where('usersname', $_GET['username'])->first();

                    $gamesession_id = $_GET['gamesession_id'];

                    // $tionApi = \VanguardLTE\UserGamesessionID::where('session_id', $gamesession_id)->where('user_id', $player->user_id)->first();
                    // if ($tionApi === null) {
                    // 	return json_encode(
                    // 		[
                    // 			"status" => 500,
                    // 		],
                    // 		200
                    // 	);
                    // }

                    // $user = \VanguardLTE\User::lockForUpdate()->find($tionApi->user_id);
                    $user = \VanguardLTE\User::where('username', $_GET['username'])->lockForUpdate()->first();
                    if ($user === null) {
                        echo json_encode(
                            [
                                "status" => 500,
                            ]
                        );
                        return true;
                    }

                    $trans_exist = \VanguardLTE\UserGamesLog::where('user_id', $user->id)->lockForUpdate();
                    $trans_exist = $trans_exist->where('transaction_id', $_GET['transaction_id'])->first();
                    if ($trans_exist) {
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100
                            ]
                        );
                        return true;
                    }

                    if($_GET['amount'] < 0 ) {
                        echo json_encode(
                            [
                                "status" => 500,
                                "msg" => "Negative amount not allowed!"
                            ]
                        );
                        return true;
                    }

                    $game_id = (int)$_GET['game_id'];
                    $amount = $_GET['amount'];
                    $session_id = $_GET['session_id'] ? $_GET['session_id'] : "";
                    $remote_id = $_GET['remote_id'] ? $_GET['remote_id'] : "";
                    $provider = $_GET['provider'] ? $_GET['provider'] : "";
                    // $original_session_id = $_GET['original_session_id'];
                    $transaction_id = $_GET['transaction_id'];

                    if ($user->balance >= $amount) {
                        $model = \VanguardLTE\UserGamesLog::create(
                            [
                                'amount' => $amount,
                                'no_money_left' => $user->balance,
                                'there_was_money' => $user->balance - $amount,
                                'session_id' => $session_id,
                                'user_id' => $user->id,
                                'remote_id' => $remote_id,
                                'action' => 'debit',
                                'game_id' => $game_id,
                                'provider' => $provider,
                                // 'original_session_id' => $original_session_id,
                                'transaction_id' => $transaction_id,
                                'status' => 'NOT_ROLLBACKED'
                            ]
                        );
                        if ($user->wager < 0) {
                            $user->update(
                                [
                                    'wager' => 0,
                                    'bonus' => 0
                                ]
                            );
                        } else {
                            $user->update(
                                [
                                    'wager' => $user->wager - $amount,
                                    'bonus' => $user->bonus - $amount
                                ]
                            );
                        }
                        $user->update(
                            [
                                'balance' => $user->balance - $amount
                            ]
                        );
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100,
                            ]
                        );
                        return true;
                    } else {
                        echo json_encode(
                            [
                                "status" => 403,
                                "balance" => floor($user->balance * 100) / 100,
                                "msg" => "Insufficient funds"
                            ]
                        );
                        return true;
                    }
                }
                //Accrual of loans
                if ($_GET['action'] == 'credit') {

                    if(!isset($_GET['gamesession_id']) || !isset($_GET['game_id']) || !isset($_GET['username']) || !isset($_GET['transaction_id']) || !isset($_GET['amount']) || $_GET['gamesession_id'] == null || $_GET['game_id'] == null || $_GET['username'] == null || $_GET['transaction_id'] == null || $_GET['amount'] == null || $_GET['gamesession_id'] == "" || $_GET['game_id'] == "" || $_GET['username'] == "" || $_GET['transaction_id'] == "" || $_GET['amount'] == "" ){
                        echo json_encode(
                            [
                                "status" => 500,
                            ]
                        );
                        return true;
                    }

                    // $player = \VanguardLTE\UsersRegistrationApi::lockForUpdate()->where('usersname', $_GET['username'])->first();

                    $gamesession_id = $_GET['gamesession_id'];

                    // $tionApi = \VanguardLTE\UserGamesessionID::where('session_id', $gamesession_id)->where('user_id', $player->user_id)->first();
                    // if ($tionApi === null) {
                    // 	return json_encode(
                    // 		[
                    // 			"status" => 500,
                    // 		],
                    // 		200
                    // 	);
                    // }

                    // $user = \VanguardLTE\User::lockForUpdate()->find($tionApi->user_id);
                    $user = \VanguardLTE\User::where('username', $_GET['username'])->lockForUpdate()->first();
                    if ($user === null) {
                        echo json_encode(
                            [
                                "status" => 500,
                            ]
                        );
                        return true;
                    }

                    $trans_exist = \VanguardLTE\UserGamesLog::where('user_id', $user->id)->lockForUpdate();
                    $trans_exist = $trans_exist->where('transaction_id', $_GET['transaction_id'])->first();
                    if ($trans_exist) {
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100
                            ]
                        );
                        return true;
                    }
                    if($_GET['amount'] < 0 ) {
                        echo json_encode(
                            [
                                "status" => 500,
                                "msg" => "Negative amount not allowed!"
                            ]
                        );
                        return true;
                    }

                    $game_id = (int)$_GET['game_id'];
                    $amount = $_GET['amount'];
                    $session_id = $_GET['session_id'] ? $_GET['session_id'] : "";
                    $remote_id = $_GET['remote_id'] ? $_GET['remote_id'] : "";
                    $provider = $_GET['provider'] ? $_GET['provider'] : "";
                    // $original_session_id = $_GET['original_session_id'];
                    $transaction_id = $_GET['transaction_id'];

                    $model = \VanguardLTE\UserGamesLog::create(
                        [
                            'amount' => $amount,
                            'no_money_left' => $user->balance,
                            'there_was_money' => $user->balance + $amount,
                            'session_id' => $session_id,
                            'user_id' => $user->id,
                            'remote_id' => $remote_id,
                            'action' => 'credit',
                            'game_id' => $game_id,
                            'provider' => $provider,
                            // 'original_session_id' => $original_session_id,
                            'transaction_id' => $transaction_id,
                            'status' => 'NOT_ROLLBACKED'
                        ]
                    );

                    $user->update(
                        [
                            'balance' => $user->balance + $amount
                        ]
                    );
                    echo json_encode(
                        [
                            'status' => 200,
                            'balance' => floor($user->balance * 100) / 100,
                        ]
                    );
                    return true;
                }

                if ($_GET['action'] == 'rollback') {

                    if(!isset($_GET['transaction_id']) || !isset($_GET['username']) || $_GET['transaction_id'] == null || $_GET['username'] == null || $_GET['transaction_id'] == "" || $_GET['username'] == "" ){
                        echo json_encode(
                            [
                                "status" => 500,
                                "msg" => "internal error"
                            ]
                        );
                        return true;
                    }

                    // $player = \VanguardLTE\User::lockForUpdate()->where('usersname', $_GET['username'])->first();
                    $user = \VanguardLTE\User::where('username', $_GET['username'])->lockForUpdate()->first();
                    if ($user === null) {
                        echo json_encode(
                            [
                                'status' => 500,
                                "msg" => "internal error"
                            ]
                        );
                        return true;
                    }

                    $transaction_id = $_GET['transaction_id'];
                    $tionApi = \VanguardLTE\UserGamesLog::where('user_id', $user->id)->where('transaction_id', $transaction_id)->first();
                    if ($tionApi === null) {
                        echo json_encode(
                            [
                                'status' => 404,
                                "msg" => "TRANSACTION_NOT_FOUND"
                            ]
                        );
                        return true;
                    }
                    $amount = $tionApi->amount;

                    if($tionApi->status == "ROLLBACKED"){
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100,
                                'transaction_id' => $transaction_id
                            ]
                        );
                        return true;
                    }

                    if($tionApi->action == "debit"){

                        $tionApi->update(
                            [
                                'status' => "ROLLBACKED"
                            ]
                        );
                        $user->update(
                            [
                                'balance' => $user->balance + $amount
                            ]
                        );
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100,
                                'transaction_id' => $transaction_id
                            ]
                        );
                        return true;

                    }else if($tionApi->action == "credit") {
                        $tionApi->update(
                            [
                                'status' => "ROLLBACKED"
                            ]
                        );
                        $user->update(
                            [
                                'balance' => $user->balance - $amount
                            ]
                        );
                        echo json_encode(
                            [
                                'status' => 200,
                                'balance' => floor($user->balance * 100) / 100,
                                'transaction_id' => $transaction_id
                            ]
                        );
                        return true;
                    }
                }
            });
        }
        public function server(\Illuminate\Http\Request $request, $game)
        {
            $GLOBALS['rgrc'] = config('app.salt');
            if($request->session()->get('freeUserID', 0) == 0){
                if( \Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->hasRole('user') )
                {
                    echo '{"responseEvent":"error","responseType":"start","serverResponse":"Wrong User"}';
                    exit();
                }
                if( !\Illuminate\Support\Facades\Auth::check() )
                {
                }
                $userId = \Illuminate\Support\Facades\Auth::id();
            }else{
                $userId = $request->session()->get('freeUserID', 0);
            }
//            echo '{"responseEvent":"error","responseType":"error","userid":'.$userId.'}';
            $object = '\VanguardLTE\Games\\' . $game . '\Server';
            $server = new $object();
            echo $server->get($request, $game, $userId);
        }
        public function check_freemodal(\Illuminate\Http\Request $request) {
            $status = 0;
            if(!\Illuminate\Support\Facades\Auth::check() )
            {
                $visiterId = $request->post('visiterId');
                $user_count = \VanguardLTE\UserFun::where('visitor_id', $visiterId)->count();
                if($user_count == 0){
                    $user_count = \VanguardLTE\User::where('visitor_id', $visiterId)->count();
                    if($user_count == 0){
                        $status =  1;
                    }
                }
            }

            return response(json_encode([
                'status' => $status
            ]));
        }
        public function check_email(\Illuminate\Http\Request $request) {
            $status = 0;
            if(!\Illuminate\Support\Facades\Auth::check() )
            {
                $email = $request->post('email');
                $visiterId = $request->post('visiterId');
                $user_count = \VanguardLTE\UserFun::where('email', $email)->count();
                if($user_count > 0){
                    $status = 1;
                }else{
                    $newRecord = new \VanguardLTE\UserFun;
                    $newRecord->email = $email;
                    $newRecord->visitor_id = $visiterId;
                    $newRecord->save();

                    $data = ['email' => $request->email];
                    $automizy_api = new \VanguardLTE\Lib\automizy_Api;
                    $lists = $automizy_api->getAllLists();
                    $smart_lists = $lists['smartLists'];
                    if(count($lists['smartLists']) > 0){
                        foreach($smart_lists as $val) {
                            if(str_contains(strtolower($val['name']), 'all')){
                                $list_id = $val['id'];
                                $automizy_api->addContactsByList($data, $list_id);
                            }
                        }
                    }
                }
            }

            return response(json_encode([
                'status' => $status
            ]));
        }
/*        public function security()
        {
            if( config('LicenseDK.APL_INCLUDE_KEY_CONFIG') != 'wi9qydosuimsnls5zoe5q298evkhim0ughx1w16qybs2fhlcpn' )
            {
                return false;
            }
            if( md5_file(base_path() . '/app/Lib/LicenseDK.php') != '3c5aece202a4218a19ec8c209817a74e' )
            {
                return false;
            }
            if( md5_file(base_path() . '/config/LicenseDK.php') != '951a0e23768db0531ff539d246cb99cd' )
            {
                return false;
            }
            return true;
        }*/
        public function SEO(\Illuminate\Http\Request $request)
        {
            $category1 = "all";
            $category2 = "";
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole('admin')){
                return redirect()->route('backend.dashboard');
            }

            /*            $checked = new \VanguardLTE\Lib\LicenseDK();
                        $license_notifications_array = $checked->aplVerifyLicenseDK(null, 0);
                        if( $license_notifications_array['notification_case'] != 'notification_license_ok' )
                        {
                            return redirect()->route('frontend.page.error_license');
                        }
                        if( !$this->security() )
                        {
                            return redirect()->route('frontend.page.error_license');
                        }*/
            /*
            if( \Illuminate\Support\Facades\Auth::check() && !\Illuminate\Support\Facades\Auth::user()->hasRole('user') )
            {
                return redirect()->route('backend.dashboard');
            }
            if( !\Illuminate\Support\Facades\Auth::check() )
            {
                return redirect()->route('frontend.auth.login');
            }
            */

            $search_game = $request->search_game;
            $login_result = $request->login;
            $register_result = $request->register;
            $forgotpassword_result = $request->forgotpassword;
            $resetpassword_result = $request->resetpassword;
            $categories = [];
            $game_ids = [];
            $cat1 = false;
            $title = trans('app.games');
            $body = '';
            $keywords = '';
            $description = '';
            $hotgames_count = 0;
            $newgames_count = 0;
            $games_count = 0;
            $apigames_count = 0;
            $hotgames_loadmore = "nomore";
            $newgames_loadmore = "nomore";
            $games_loadmore = "nomore";
            $apigames_loadmore = "nomore";
            $apigamesbycategory = 0;
            $api_games = [];
            $api_hotgames = [];
            $api_newgames = [];

            $shop_id = (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->shop_id : 0);
            $shop = \VanguardLTE\Shop::find($shop_id);
            $games = \VanguardLTE\Game::where([
                'view' => 1,
                'shop_id' => $shop_id
            ]);

            $newgames = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
                ->leftJoin('categories','categories.id','=','game_categories.category_id')
                ->orderBy('games.new_order', 'ASC')
                ->where('categories.Title','New')
                ->where('games.new_order', "!=", NULL);
//            $newgames_count = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
//                ->leftJoin('categories','categories.id','=','game_categories.category_id')
//                ->orderBy('games.new_order', 'ASC')
//                ->where('categories.Title','New')
//                ->where('games.new_order', "!=", NULL)
//                ->count();

//            if($newgames_count <= 20) {
//                $newgames_loadmore = "nomore";
//            }else{
//                $newgames_loadmore = "more";
//            }
            $hotgames = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
                ->leftJoin('categories','categories.id','=','game_categories.category_id')
                ->orderBy('games.hot_order', 'ASC')
                ->where('categories.Title','Hot')
                ->where('games.hot_order', "!=", NULL);
//            $hotgames_count = \VanguardLTE\Game::leftJoin('game_categories','game_categories.game_id','=','games.id')
//                ->leftJoin('categories','categories.id','=','game_categories.category_id')
//                ->orderBy('games.hot_order', 'ASC')
//                ->where('categories.Title','Hot')
//                ->where('games.hot_order', "!=", NULL)
//                ->count();
//            if($hotgames_count <= 20) {
//                $hotgames_loadmore = "nomore";
//            }else{
//                $hotgames_loadmore = "more";
//            }

            $frontend = 'Default';
            if( $shop_id && $shop )
            {
                $frontend = $shop->frontend;
            }
            if( $category1 == '' )
            {
                if( $currentCategory = $request->cookie('currentCategory') )
                {
                    $category = \VanguardLTE\Category::where([
                        'href' => $currentCategory,
                        'shop_id' => $shop_id
                    ])->first();
                    if( $category )
                    {
                        $category1 = $category->href;
                        return redirect()->route('frontend.game.list.category', [
                            'category1' => $category1,
                            'page' => $request->cookie('currentPage')
                        ]);
                    }
                }
                if( settings('use_all_categories') )
                {
                    return redirect('/home');
                    /*return redirect()->route('frontend.game.list.category', [
                        'category1' => 'all',
                        'page' => $request->cookie('currentPage')
                    ]);*/
                }
                $category = \VanguardLTE\Category::where([
                    'parent' => 0,
                    'shop_id' => $shop_id
                ])->orderBy('position')->first();
                if( $category )
                {
                    $category1 = $category->href;
                    return redirect()->route('frontend.game.list.category', $category1);
                }
            }
            \Illuminate\Support\Facades\Cookie::queue('currentCategory', $category1, 2678400);
            if( $category1 != '' )
            {
                $cat1 = \VanguardLTE\Category::where([
                    'href' => $category1,
                    'shop_id' => $shop_id
                ])->first();
                if( !$cat1 && $category1 != 'all' )
                {
                    abort(404);
                }
                if( $category2 != '' )
                {
                    $cat2 = \VanguardLTE\Category::where([
                        'href' => $category2,
                        'parent' => $cat1->id,
                        'shop_id' => $shop_id
                    ])->first();
                    if( !$cat2 )
                    {
                        abort(404);
                    }
                    $categories[] = $cat2->id;
                }
                else if( $category1 != 'all' )
                {
                    $categories = \VanguardLTE\Category::where([
                        'parent' => $cat1->id,
                        'shop_id' => $shop_id
                    ])->pluck('id')->toArray();
                    $categories[] = $cat1->id;
                }
                else
                {
                    $categories = \VanguardLTE\Category::where([
                        'parent' => 0,
                        'shop_id' => $shop_id
                    ])->pluck('id')->toArray();
                }
                if( $frontend == 'Amatic' )
                {
                    $Amatic = \VanguardLTE\Category::where([
                        'title' => 'Amatic',
                        'shop_id' => $shop_id
                    ])->first();
                    if( $Amatic )
                    {
                        $categories = \VanguardLTE\Category::where([
                            'parent' => $Amatic->id,
                            'shop_id' => $shop_id
                        ])->pluck('id')->toArray();
                        $categories[] = $Amatic->id;
                    }
                }
                if( $frontend == 'NetEnt' )
                {
                    $Amatic = \VanguardLTE\Category::where([
                        'title' => 'NetEnt',
                        'shop_id' => $shop_id
                    ])->first();
                    if( $Amatic )
                    {
                        $categories = \VanguardLTE\Category::where([
                            'parent' => $Amatic->id,
                            'shop_id' => $shop_id
                        ])->pluck('id')->toArray();
                        $categories[] = $Amatic->id;
                    }
                }
                $game_ids = \VanguardLTE\GameCategory::whereIn('category_id', $categories)->groupBy('game_id')->pluck('game_id')->toArray();
                if( count($game_ids) > 0 )
                {
                    $games = $games->whereIn('id', $game_ids);
                    $newgames = $newgames->whereIn('games.id', $game_ids);
                    $hotgames = $hotgames->whereIn('games.id', $game_ids);
                }
                else
                {
                    $games = $games->where('id', 0);
                    $newgames = $newgames->where('games.id', 0);
                    $hotgames = $hotgames->where('games.id', 0);
                }
            }

            if($newgames_count <= 20) {
                $newgames_loadmore = "nomore";
            }else{
                $newgames_loadmore = "more";
            }

            if($hotgames_count <= 20) {
                $hotgames_loadmore = "nomore";
            }else{
                $hotgames_loadmore = "more";
            }

            $detect = new \Detection\MobileDetect();
            $devices = [];
            if( $detect->isMobile() || $detect->isTablet() )
            {
                $games = $games->whereIn('device', [
                    0,
                    2
                ]);
                $newgames = $newgames->whereIn('device', [
                    0,
                    2
                ]);
                $hotgames = $hotgames->whereIn('device', [
                    0,
                    2
                ]);
                $devices = [
                    0,
                    2
                ];
            }
            else
            {
                $games = $games->whereIn('device', [
                    1,
                    2
                ]);
                $newgames = $newgames->whereIn('device', [
                    1,
                    2
                ]);
                $hotgames = $hotgames->whereIn('device', [
                    1,
                    2
                ]);
                $devices = [
                    1,
                    2
                ];
            }
            if($search_game){
                if($category1 == 'hot'){
                    $games = $games->where('name','like','%'.$search_game.'%')->where('games.hot_order', "!=", NULL)->orderBy('games.hot_order', 'ASC')->take(10)->get();
                }else if($category1 == 'new'){
                    $games = $games->where('name','like','%'.$search_game.'%')->where('games.new_order', "!=", NULL)->orderBy('games.new_order', 'ASC')->take(10)->get();
                }else{
                    $games = $games->where('name','like','%'.$search_game.'%')->orderBy('games.order', 'ASC')->take(10)->get();
                }
            }else{
                if($category1 == 'hot'){
                    $games_count = $games->where('games.hot_order', "!=", NULL)->count();
                    $games = $games->where('games.hot_order', "!=", NULL)->orderBy('games.hot_order', 'ASC')->take(10)->get();
                }else if($category1 == 'new'){
                    $games_count = $games->where('games.new_order', "!=", NULL)->count();
                    $games = $games->where('games.new_order', "!=", NULL)->orderBy('games.new_order', 'ASC')->take(10)->get();
                }else{
                    $games_count = $games->count();
                    $games = $games->orderBy('games.order', 'ASC')->take(10)->get();
                }

                if($games_count <= 20) {
                    $games_loadmore = "nomore";
                }else {
                    $games_loadmore = "more";
                }
            }
            $hotgames = $hotgames->get();
            $newgames = $newgames->get();

            $jpgs = \VanguardLTE\JPG::get();
            $categories = false;
            $currentSliderNum = -1;
            $currentListTitle = "";
            if( $games )
            {
                $cat_ids = \VanguardLTE\GameCategory::whereIn('game_id', \VanguardLTE\Game::where([
                    'view' => 1,
                    'shop_id' => $shop_id
                ])->pluck('id'))->groupBy('category_id')->pluck('category_id');
                if( count($cat_ids) )
                {
                    $categories = \VanguardLTE\Category::whereIn('id', $cat_ids)->orWhere('type', 1)->where('shop_id', $shop_id)->orderBy('position','ASC')->get();
                    if( $category1 != '' )
                    {
                        foreach( $categories as $index => $cat )
                        {
                            if( $cat->href == $category1 )
                            {
                                $currentSliderNum = $cat->href;
                                $currentListTitle = $cat->title;
                                break;
                            }
                        }
                    }
                }
            }

            $game_gamehub_page = 0;
            $cur_date = date("Y-m-d");
            $cur_api_games = [];
            $apigames_count = \VanguardLTE\ApiGames::where('created_at', $cur_date)->count();

            if( $apigames_count == 0) {
                $game_gamehub_api = new \VanguardLTE\Lib\games_Api;

                if($shop_id == 0){
                    $games_gamehub = $game_gamehub_api->getGameList(['currency' => 'USD']);
                }else{
                    $games_gamehub = $game_gamehub_api->getGameList(['currency' => $shop->currency]);
                }

			    if( $games_gamehub && $games_gamehub['error'] == 0 && count($games_gamehub['response']) > 0 ){

                    foreach ($games_gamehub['response'] as $key => $val) {
                        // $exist_game = \VanguardLTE\ApiGames::where('game_id', $val['id'])->first();
                        $exist_game = \VanguardLTE\ApiGames::where('game_id', (int)$val['id'])->first();
                        $api_label = '';
                        if($val['new'] == 1){
                            $api_label = 'new';
                        }
                        if(!$exist_game){
                            $model = \VanguardLTE\ApiGames::create(
                                [
                                    'game_id' => $val['id'],
                                    'name' => $val['name'],
                                    'category' => $val['category'],
                                    'subcategory' => $val['subcategory'],
                                    'new' => $val['new'],
                                    'system' => $val['system'],
                                    'position' => $val['position'],
                                    'type' => $val['type'],
                                    'image' => $val['image'],
                                    'image_preview' => $val['image_preview'],
                                    'image_filled' => $val['image_filled'],
                                    'mobile' => $val['mobile'],
                                    'play_for_fun_supported' => $val['play_for_fun_supported'],
                                    'label' => $api_label,
                                    'order' => $val['position']
                                ]
                            );
                        }else {
                            $exist_game->update(['created_at' => $cur_date, 'updated_at' => $cur_date]);
                        }
                    }
                }
            }
            $old_games = \VanguardLTE\ApiGames::where('created_at', '!=', $cur_date)->delete();

            if( $detect->isMobile() || $detect->isTablet() ) {
                if($category1 != 'all'){
                    if($category1 == 'livecasino' || $category1 == 'jackpot' || $category1 == 'table' || strtolower($category1) == 'hot' || strtolower($category1) == 'new' || strtolower($category1) == 'pragmatic' || strtolower($category1) == 'casino-technology'){
                        if($category1 == 'livecasino'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'LIKE', '%live%casino%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'jackpot') {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('name', 'LIKE', '%'.$category1.'%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'table'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'table-games')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'hot'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'new'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'pragmatic'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('category', 'like', '%pragmatic%')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_ct_gaming')->where('mobile', 1)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }
                    }else {
                        $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_'.$category1)->where('mobile', 1)->orderBy('order', 'ASC');
                        $apigames_count = $apigamesbycategory->count();
                        $api_games = $apigamesbycategory->take(10)->get();
                    }
                }else {
                    $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('mobile', 1)->orderBy('order', 'ASC');
                    $apigames_count = $apigamesbycategory->count();
                    $api_games = $apigamesbycategory->take(10)->get();
                }
                $api_newgames = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 1)->orderBy('order', 'ASC')->get();
                $api_hotgames = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 1)->orderBy('order', 'ASC')->get();
            }else {
                if($category1 != 'all'){
                    if($category1 == 'livecasino' || $category1 == 'jackpot' || $category1 == 'table' || strtolower($category1) == 'hot' || strtolower($category1) == 'new' || strtolower($category1) == 'pragmatic' || strtolower($category1) == 'casino-technology'){
                        if($category1 == 'livecasino'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'LIKE', '%live%casino%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'jackpot') {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('name', 'LIKE', '%'.$category1.'%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'table'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('type', 'table-games')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'hot'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'new'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else if($category1 == 'pragmatic'){
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('category', 'like', '%pragmatic%')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }else {
                            $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_ct_gaming')->where('mobile', 0)->orderBy('order', 'ASC');
                            $apigames_count = $apigamesbycategory->count();
                            $api_games = $apigamesbycategory->take(10)->get();
                        }
                    }else {
                        $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('subcategory', '_'.$category1)->where('mobile', 0)->orderBy('order', 'ASC');
                        $apigames_count = $apigamesbycategory->count();
                        $api_games = $apigamesbycategory->take(10)->get();
                    }
                }else {
                    $apigamesbycategory = \VanguardLTE\ApiGames::where('created_at', $cur_date)->where('mobile', 0)->orderBy('order', 'ASC');
                    $apigames_count = $apigamesbycategory->count();
                    $api_games = $apigamesbycategory->take(10)->get();
                }
                $api_newgames = \VanguardLTE\ApiGames::where('label', 'new')->where('mobile', 0)->orderBy('order', 'ASC')->get();
                $api_hotgames = \VanguardLTE\ApiGames::where('label', 'hot')->where('mobile', 0)->orderBy('order', 'ASC')->get();
            }

            if($apigames_count <= 20){
                $apigames_loadmore = "nomore";
            }else {
                $apigames_loadmore = "more";
            }
            // if( settings('user_all_categories') && $category1 == 'all' )
            if( $category1 == 'all' )
            {
                $currentSliderNum = 'all';
                $currentListTitle = 'All';
            }

            $countrys =  \VanguardLTE\Country::orderBy('ranking','ASC')->get();
            $currencys =  \VanguardLTE\Currency::orderBy('ranking','ASC')->get();
            $realBalance = 0;
            $bonusBalance = 0;
            return view('frontend.' . $frontend . '.games.list', compact('games', 'api_games', 'hotgames', 'newgames','category1', 'cat1', 'categories', 'currentSliderNum', 'currentListTitle','title', 'body', 'keywords', 'description', 'jpgs', 'devices', 'countrys', 'currencys','search_game','login_result','register_result','forgotpassword_result','resetpassword_result','games_loadmore', 'hotgames_loadmore', 'newgames_loadmore', 'apigames_loadmore', 'api_hotgames', 'api_newgames'));
        }
    }

}
namespace
{
    function onkXppk3PRSZPackRnkDOJaZ9()
    {
        return 'OkBM2iHjbd6FHZjtvLpNHOc3lslbxTJP6cqXsMdE4evvckFTgS';
    }

}
