<?php
namespace App\Common\Auth;
use App\Common\Request\Urls;

class DoorLock
{
    private $_apiid;
    private $_apikey;

    private function __construct() {
        $this->_apiid = \PhalApi\DI()->config->get("elock.apiid");
        $this->_apikey = \PhalApi\DI()->config->get("elock.apikey");
    }

    public function getToken() {
        $url = Urls::token();
        $params = [
            "apiid" => $this->_apiid,
            "apikey" => $this->_apikey
        ];
        $data = \App\curl_post($url, $params);
        echo $data;
        return json_decode($data, true);
    }
    /**
     * 获取访问签名
     * @param bool $reload true=强制从远程获取，false=从缓存获取
     */
    public static function getSignature($reload=false) {
        $cache = \PhalApi\DI()->cache;
        if ($reload || $cache->get("access_token") == NULL) {
            // 获取token并缓存
            $doorLock = new DoorLock();
            $token = $doorLock->getToken();
            $expires = strtotime($token["expires_in"]) - strtotime("now");
            $cache->set("access_token", $token["access_token"], $expires);
        }
        return $cache->get("access_token");
    }

}