<?php
namespace VanguardLTE\Lib
{
	class games_Api
	{    
		
		// private $api = 'https://game-program.com/api/seamless/provider'; //staging
		private $api = 'https://api.thegameprovider.com/api/seamless/provider';
		
		private $api_password = 'kS4Hqj702WFlF9igfw';
		private $api_login = 'canada777_mc_s';
		
		public function getGameList($array)
		{	
			return json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				"method"  			=> "getGameList",
				"show_systems"  	=> 0, //if false, parameter is not needed
				"show_additional"  	=> false, //if false, parameter is not needed
				"currency"  		=> $array['currency']
			]),true);
		}
		
		public function getGame($array){
			$games =  json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				'method' 			=> $array['method'],
				'lang'				=> $array['lang'],
				
				'user_username' 	=> $array['user_username'], //not required for fun mode
				'user_password' 	=> $array['user_password'], //not required for fun mode
				
				'gameid' 			=> $array['game_id'], // you can also use game hash from getGameList to start a game - for example ne#ne-jingle-spin
				'homeurl' 			=> $array['homeurl'],
				'cashierurl' 		=> '' ,//optional
				'play_for_fun' 		=> $array['play_for_fun'], //to launch sportsbook in demo use method getGameDemo
				'currency' 			=> $array['currency']
			]),true);
			return $games;	
		}
		
		public function getGameDemo($array){
			$games =  json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				'method' 			=> $array['method'],
				'lang'				=> $array['lang'],
				'gameid' 			=> $array['game_id'], // you can also use game hash from getGameList to start a game - for example ne#ne-jingle-spin
				'homeurl' 			=> $array['homeurl'],
				'cashierurl' 		=> '' ,//optional
				'play_for_fun' 		=> $array['play_for_fun'], //to launch sportsbook in demo use method getGameDemo
				'currency' 			=> $array['currency']
			]),true);
			return $games;	
		}
		
		public function createPlayer($array){
			$games =  json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				"method" => "createPlayer",
				"user_username" => $array['user_username'], //should be unique - you can use your internal ID for this parameter
				"user_password" => $array['user_password'],
				"user_nickname" => $array['user_nickname'], //optional - non unique nickname of a player that is showed in some providers. If not passed user_username is used
				"currency" =>  $array['currency']
			]),true);
			return $games;	
		}

		public function loginPlayer($array){
			$games =  json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				"method" => "loginPlayer",
				"user_username" => $array['user_username'], //should be unique - you can use your internal ID for this parameter
				"user_password" => $array['user_password'],
				"currency" =>  $array['currency']
			]),true);
			return $games;
		}

		public function logoutPlayer($array){
			$games =  json_decode($this->curl([
				"api_password" 		=> $this->api_password,
				"api_login"  		=> $this->api_login,
				"method" => "logoutPlayer",
				"user_username" => $array['user_username'], //should be unique - you can use your internal ID for this parameter
				"user_password" => $array['user_password'],
				"currency" =>  $array['currency']
			]),true);
			return $games;
		}
		
		function curl($data)
		{
			$ch = curl_init($this->api);
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&')); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$html = curl_exec($ch);
			curl_close($ch);	
			return $html;
		}
	}
}