<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Curl_model extends CI_Model {

    /**
     *  Check if Curl available
     *
     *  @return boolean
     */

    public function test() {
        $test = function_exists('curl_version');
		return $test;
    }

    /**
     *  Do request
     *
     *  @param  string  url
     *  @return array
     */

    public function request($url) {
        $result = wp_remote_post($url);
        return $result;
    }

}