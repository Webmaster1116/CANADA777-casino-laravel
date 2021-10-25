<?php
namespace VanguardLTE\Lib
{
	class automizy_Api
	{    
		
		private $api = 'https://gateway.automizy.com/v2/';
		
		private $token = 'fbed718e835edebc8160ea9a264a841b97c4bbcf';
		
		public function getAllLists()
		{
			$url = $this->api.'smart-lists';
			$method = 'GET';
			return json_decode($this->curl([], $url, $method),true);
		}

		public function getListById($id)
		{
			$url = $this->api.'smart-lists/'.$id;
			$method = 'GET';
			return json_decode($this->curl([], $url, $method),true);
		}

		public function getContactsByList()
		{
			$url = $this->api.$id.'/contacts';
			$method = 'GET';
			return json_decode($this->curl([], $url, $method), true);
		}
		
		public function createList($array)
		{
			$url = $this->api.'smart-lists';
			$method = 'POST';
			return json_decode($this->curl($array, $url, $method), true);
		}

		public function editList($array, $id)
		{
			$url = $this->api.'smart-lists/'.$id;
			$method = 'PATCH';
			return json_decode($this->curl($array, $url, $method), true);
		}

		public function deleteList($id)
		{
			// var_dump($id);exit;
			$url = $this->api.'smart-lists/'.$id;
			$method = 'DELETE';
			return json_decode($this->curl([], $url, $method), true);

		}
		
		public function getAllContactsByList($id)
		{
			$url = $this->api.'smart-lists/'.$id.'/contacts';
			$method = 'GET';
			return json_decode($this->curl([], $url, $method), true);
		}

		public function addContactsByList($array, $id){
			$url = $this->api.'smart-lists/'.$id.'/contacts';
			$method = 'POST';
			return json_decode($this->curl($array, $url, $method), true);
		}
		function curl($data, $url, $method)
		{
            $curl = curl_init();
			curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    'Content-Type: application/json',
					"Authorization: Bearer ".$this->token
                )
            ));
            $response = curl_exec($curl);
			curl_close($curl);
			return $response;
		}
	}
}