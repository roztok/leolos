<?php

namespace BaseApp\Utils;


Class Cookie {

    static function set( $name, $value, $day, $path, $domain, $httpOnly = True ) {
        return setcookie( $name, $value, self::setExpirationDate($day), $path,
            $domain, self::isSecure(), $httpOnly );
    }


    static function isSecure() {
        if( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) {
            return true;
        } else {
            return false;
        }
    }


    static function setExpirationDate( $day ) {
        return time() + ($day * 24 * 60 * 60);
    }


    static function get($name, $default = Null){
        return (isset($_COOKIE[$name]))? $_COOKIE[$name] : $default ;
    }
}
