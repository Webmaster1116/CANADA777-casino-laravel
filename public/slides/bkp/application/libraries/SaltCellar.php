<?php

class SaltCellar {

    public static function getToken($length = 64) {
        if ( ! isset($length) || intval($length) <= 32 ) {
            $length = 32;
        }
        $tokens = array();
        if (function_exists('random_bytes')) {
            $tokens[] = bin2hex(random_bytes($length));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $tokens[] = bin2hex(openssl_random_pseudo_bytes($length));
        }
        return $tokens[mt_rand(0, count($tokens) - 1)];
    }

    public static function getSalt($min = 32, $max = 44) {
        return substr(strtr(base64_encode(hex2bin(self::getToken(1024))), '+', '.'), 0, mt_rand($min, $max));
    }

}

if( ! function_exists('hex2bin')) {
    function hex2bin($hexstr) 
    { 
        $n = strlen($hexstr); 
        $sbin="";   
        $i=0; 
        while($i<$n) 
        {       
            $a =substr($hexstr,$i,2);           
            $c = pack("H*",$a); 
            if ($i==0){$sbin=$c;} 
            else {$sbin.=$c;} 
            $i+=2; 
        } 
        return $sbin; 
    } 
}