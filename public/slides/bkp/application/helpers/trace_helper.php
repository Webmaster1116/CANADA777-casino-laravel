<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('trace')) {
	function trace($var) {
		echo('<pre>');
		print_r($var);
		echo('</pre>');
	}
}

if( ! function_exists('callback_export')) {
	function callback_export($callback) {
        if (is_string($callback)) {
            $export = $callback;
        } elseif (is_array($callback)) {
            $temp = array();
            foreach ($callback as $part) {
                if (is_object($part)) {
                    $temp[] = get_class($part);
                } else {
                    $temp[] = $part;
                }
            }
            $export = implode('->', $temp);
        } elseif (is_object($callback)) {
            $export = get_class($callback);
        } else {
            $export = var_export($callback, true);
        }
        return $export;
	}
}
