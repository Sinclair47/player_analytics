<?php

class Util {

    public static function setCookie($key, $value = null) { 
        if(!isset($value) || empty($value) || !isset($key) || empty($key)) {
            throw new Exception('No value for cookie provided');
        }
        setcookie($key, $value, 0, '/player_analytcis'); 
    }


    public static function getCookie($value) {
        if (isset($_COOKIE[$value]) && !empty($_COOKIE[$value])) {
            return $_COOKIE[$value];
        }
        return null; 
    }


    public static function getCookieJson($value) {
        if (isset($_COOKIE[$value]) && !empty($_COOKIE[$value])) {
            return json_decode($_COOKIE[$value], true);
        }
        return null; 
    }


}

/**
* Nice print
*/
function pr($input) {
    echo "<pre>";
    print_r($input);
    echo "</pre>";
}


