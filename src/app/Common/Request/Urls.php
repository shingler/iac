<?php
namespace App\Common\Request;

class Urls
{
    public static function member() {
        return "https://api.parkline.cc/api/facecgi";
    }

    public static function token() {
        return "https://api.parkline.cc/api/token";
    }

    public static function device() {
        return "https://api.parkline.cc/api/devicecgi";
    }
}