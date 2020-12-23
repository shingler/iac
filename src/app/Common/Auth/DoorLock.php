<?php
namespace App\Common\Auth;

class DoorLock
{
    private $_appid = "";
    private $_appkey = "";
    private $_authorized_host = "http://121.36.67.176:8081";
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
        \PhalApi\DI()->logger->info($url, $params);
        \PhalApi\DI()->logger->info("header", ["HTTP_REFERER" => $this->_authorized_host]);
//        $curl = new \PhalApi\CUrl();
//        $curl->setHeader(["HTTP_REFERER" => $this->_authorized_host]);
//        $res = $curl->post($url, $params, 10000);
        header("Content-type: text/html; charset=utf-8");
        $apiurl="https://api.parkline.cc/api/token";
        $acsurl="http://121.36.67.176:8081";//后台填写的接入地址，必须包含http或https协议
//$acsurl="http://".$_SERVER['HTTP_HOST'];//动态获取请求地址
        $ch = curl_init();
        $data["apiid"]="bl8d4b38a1f2635d92";
        $data["apikey"]="8a2b2fde9c8625211f70b5b257c472e9";
        $data = http_build_query($data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch,CURLOPT_REFERER,$acsurl);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        echo $data;

        \PhalApi\DI()->logger->info($data);
        return json_decode($data, true);
    }

}