<?php
namespace App\Common\Auth;

class DoorLock
{
    private $_appid = "";
    private $_appkey = "";
    /**
     * 加载配置
     */
    public function __construct() {
        $this->_appid = "bl8d4b38a1f2635d92";
        $this->_appkey = "8a2b2fde9c8625211f70b5b257c472e9";
    }


    /**
     * 获取访问签名
     */
    public function getSignature() {
        $url = "https://api.parkline.cc/api/token";
        $params = [
            "appid" => $this->_appid,
            "apikey" => $this->_appkey
        ];
        $data = \App\curl_post($url, $params);
        echo $data;
        return json_decode($data, true);
    }

}