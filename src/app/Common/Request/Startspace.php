<?php
namespace App\Common\Request;

use App\Common\Exception\ApiException;

class Startspace
{
    /**
     * 传递回调信息
     * @param string $data
     * @throws \Exception
     * @return bool
     * {"code":1,"msg":"成功"}
     */
    public static function door_is_open($data) {
        $url = Urls::startspace_callback();
        \PhalApi\DI()->callback_logger->debug($url);
        $params = [
            "service" => "App.Callback.Unlock",
            "callback" => $data,
        ];
        \PhalApi\DI()->callback_logger->debug($params);
        $ret = \App\curl_post($url, $params);
        \PhalApi\DI()->callback_logger->debug($ret);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            return false;
        }
        return true;
    }
}