<?php 
namespace VanguardLTE\Http\Controllers\Web\Frontend
{
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Http\Client\Response;
    use Illuminate\Http\Client\RequestException;

    class BonusController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function index(\Illuminate\Http\Request $request)
        {
            $countrys =  \VanguardLTE\Country::get();
            $currencys =  \VanguardLTE\Currency::get();
            $welcomepackages = \VanguardLTE\WelcomePackage::leftJoin('games', function ($join)
                                                                {
                                                                    $join->on('games.original_id','=','welcomepackages.game_id');
                                                                    $join->on('games.id','=','games.original_id');
                                                                })->select('welcomepackages.*', 'games.name')->get();
            return view('frontend.Default.bonus.bonus', compact('countrys', 'currencys', 'welcomepackages'));
        }
        public function term(\Illuminate\Http\Request $request)
        {
            $countrys =  \VanguardLTE\Country::get();
            $currencys =  \VanguardLTE\Currency::get();
            return view('frontend.Default.bonus.term', compact('countrys', 'currencys'));
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
