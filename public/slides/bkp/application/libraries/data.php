<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Data {

    private $_data = array();

    /**
     *  Get data
     *
     *  @param  string  $key
     *  @param  var     $default
     *  @return var
     */

    public function get($key, $default = false) {
        return isset($this->_data[$key]) ? $this->_data[$key] : $default;
    }

    /**
     *  Set data
     *
     *  @param  string  $key
     *  @param  string  $value
     *  @return var
     */

    public function set($key, $value) {
        $this->_data[$key] = $value;
    }

}