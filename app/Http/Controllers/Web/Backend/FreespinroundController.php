<?php

namespace VanguardLTE\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class FreespinroundController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $freespinrounds = \VanguardLTE\Freespinround::orderBy('created_at', 'DESC')->get();
        
        return view('backend.freespinround.list', compact('freespinrounds'));
    }
    public function add(\Illuminate\Http\Request $request)
    {
        $cur_date = date("Y-m-d");
        $savedPlayer = [];
        $savedGame = [];
        $savedApiGame = [];
        if ($request->isMethod('get')){
            $games = \VanguardLTE\Game::where('shop_id', auth()->user()->shop_id)->where('view', 1)->orderBy('order', 'ASC')->pluck('id', 'name');
            $apigames = \VanguardLTE\ApiGames::where('created_at', $cur_date)->orderBy('order', 'ASC')->pluck('game_id', 'name');
            $players = \VanguardLTE\User::pluck('id', 'username');
            return view('backend.freespinround.add', compact('games', 'players', 'games', 'apigames', 'savedPlayer', 'savedGame', 'savedApiGame'));
        }
        if ($request->isMethod('post')){
            $games_list = '';
            $apigames_list = '';
            $players_list = '';
            $all_player = '';
            $all_game = '';
            $all_apigame = '';
            if(count($request->games) > 0){
                foreach($request->games as $key => $val){
                    if($val == "all"){
                        $all_game = "all"; 
                    }
                    if($key < count($request->games)-1){
                        $games_list .= $val.'-';
                    }else{
                        $games_list .= $val;
                    } 
                }
            }
            if(count($request->apigames) > 0){
                foreach($request->apigames as $key => $val){
                    if($val == "all"){
                        $all_apigame = "all"; 
                    }
                    if($key < count($request->apigames)-1){
                        $apigames_list .= $val.'-';
                    }else{
                        $apigames_list .= $val;
                    } 
                }
            }
            if(count($request->players) > 0){
                foreach($request->players as $key => $val){
                    if($val == "all"){
                        $all_player = "all"; 
                    }
                    if($key < count($request->players)-1){
                        $players_list .= $val.'-';
                    }else{
                        $players_list .= $val;
                    } 
                }
            }
            if( $all_player == "all"){
                $players_list = 'all';
            }
            if( $all_game == "all"){
                $games_list = 'all';
            }
            if( $all_apigame == "all"){
                $apigames_list = 'all';
            }
            if(!isset($request->notify)){
                $notify = 0;
            }else{
                $notify = 1;
            }
            $freespinround = new \VanguardLTE\Freespinround;
            $freespinround->title = $request->title;
            $freespinround->free_rounds = $request->free_rounds;
            $freespinround->bet_type = $request->bet_type;
            $freespinround->valid_from = $request->valid_from;
            $freespinround->valid_to = $request->valid_to;
            $freespinround->players = $players_list;
            $freespinround->games = $games_list;
            $freespinround->apigames = $apigames_list;
            $freespinround->notify = $notify;
            $freespinround->active = $request->active;
            $freespinround->save();
            return redirect()->route('backend.freespinround.list');
        }
    }
    public function edit(\Illuminate\Http\Request $request, $id)
    {
        $cur_date = date("Y-m-d");
        $freespinround = \VanguardLTE\Freespinround::where('id', $id)->first();
        if ($request->isMethod('get')){
            $games = \VanguardLTE\Game::where('shop_id', auth()->user()->shop_id)->where('view', 1)->orderBy('order', 'ASC')->pluck('id', 'name');
            $apigames = \VanguardLTE\ApiGames::where('created_at', $cur_date)->orderBy('order', 'ASC')->pluck('game_id', 'name');
            $players = \VanguardLTE\User::pluck('id', 'username');
            $savedPlayer = [];
            $savedGame = [];
            $savedApiGame = [];
            if($freespinround) {
                if( $freespinround['players'] != 'all'){
                    $savedPlayer = explode("-", $freespinround['players']);
                }else{
                    $savedPlayer = $freespinround['players'];
                }
                if( $freespinround['games'] != 'all'){
                    $savedGame = explode("-", $freespinround['games']);
                }else{
                    $savedGame = $freespinround['games'];
                }
                if( $freespinround['apigames'] != 'all'){
                    $savedApiGame = explode("-", $freespinround['apigames']);
                }else{
                    $savedApiGame = $freespinround['apigames'];
                }
            }
            
            return view('backend.freespinround.edit', compact('freespinround', 'players', 'games', 'apigames', 'savedPlayer', 'savedGame', 'savedApiGame'));
        }
        if ($request->isMethod('post')){
            $games_list = '';
            $apigames_list = '';
            $players_list = '';
            $all_player = '';
            $all_game = '';
            $all_apigame = '';
            if(count($request->games) > 0){
                foreach($request->games as $key => $val){
                    if($val == "all"){
                        $all_game = "all"; 
                    }
                    if($key < count($request->games)-1){
                        $games_list .= $val.'-';
                    }else{
                        $games_list .= $val;
                    } 
                }
            }
            if(count($request->apigames) > 0){
                foreach($request->apigames as $key => $val){
                    if($val == "all"){
                        $all_apigame = "all"; 
                    }
                    if($key < count($request->apigames)-1){
                        $apigames_list .= $val.'-';
                    }else{
                        $apigames_list .= $val;
                    } 
                }
            }
            if(count($request->players) > 0){
                foreach($request->players as $key => $val){
                    if($val == "all"){
                        $all_player = "all"; 
                    }
                    if($key < count($request->players)-1){
                        $players_list .= $val.'-';
                    }else{
                        $players_list .= $val;
                    } 
                }
            }
            if( $all_player == "all"){
                $players_list = 'all';
            }
            if( $all_game == "all"){
                $games_list = 'all';
            }
            if( $all_apigame == "all"){
                $apigames_list = 'all';
            }
            if(!isset($request->notify)){
                $notify = 0;
            }else{
                $notify = 1;
            }
            $freespinround->title = $request->title;
            $freespinround->free_rounds = $request->free_rounds;
            $freespinround->bet_type = $request->bet_type;
            $freespinround->valid_from = $request->valid_from;
            $freespinround->valid_to = $request->valid_to;
            $freespinround->players = $players_list;
            $freespinround->games = $games_list;
            $freespinround->apigames = $apigames_list;
            $freespinround->notify = $notify;
            $freespinround->active = $request->active;
            $freespinround->save();
            return redirect()->route('backend.freespinround.list');
        }
    }
    public function delete(\Illuminate\Http\Request $request, $id)
    {
        $freespinround = \VanguardLTE\Freespinround::where('id', $id)->delete();
        return redirect()->route('backend.freespinround.list');
    }
}
