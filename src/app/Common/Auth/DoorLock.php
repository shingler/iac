<?php
namespace App\Common\Auth;
use App\Common\Request\Urls;
use App\Common\Request\Device;

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
        $data = \App\curl_post($url, $params, "json");
        return $data;
    }
    /**
     * 获取访问签名
     * 先访问设备状态检查token是否过期，再决定是否修改token缓存
     * @param bool $reload true=强制从远程获取，false=从缓存获取
     */
    public static function getSignature($reload=false) {
        $cache = \PhalApi\DI()->cache;
        if (!$reload && $cache->get("access_token") != NULL) {
            // 检查token是否过期
            $token = $cache->get("access_token");
            $device = new Device($token);
            // 只为验证token，devid瞎写即可
            $ret = $device->status("check_token", "01");
            // token有效：{"code":406,"msg":"信息不匹配"}
            // token过期：{"code":402,"msg":"token过期"}
            if ($ret["code"] == 402) {
                // 获取token并缓存
                $doorLock = new DoorLock();
                $token = $doorLock->getToken();
                $expires = strtotime($token["expires_in"]) - strtotime("now");
                $cache->set("access_token", $token["access_token"], $expires);
            }
        }

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