<?php
namespace App\Api;
use PhalApi\Api;
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use App\Common\Request;
use App\Common\Request\Startspace;

/**
 * 智能门禁开锁回调
 * @package App\Api
 */
class Callback extends Api
{
    public function getRules() {
        return [
            "Unlock" => [
                "callback" => ["name" => "callback", "desc" => "回调json字符串", "type" => "array", "format" => "json", "min" => 1, "require" => true]
            ]
        ];
    }

    /**
     * 接收智能门禁刷脸开锁回调
     * @method POST
     * @desc 智能门禁网关会将刷脸成功信息以异步回调的方式返回。本接口接收该数据，并写入文件日志和数据库（待定）
     * @return array
     */
    public function Unlock()
    {
        $callback_data = $this->callback;
//        var_dump($callback_data);
        //{"code":"900","devid":"215571","status":"无开锁权限","reqstatus":"0","mid":"135010","flag":"0","uid":"18611102795","imageurl":"http:\/\/huweibing-1253704117.cos.ap-guangzhou.myqcloud.com\/215571\/2021-01-07\/38a4f16e-cf34-4378-b955-6b7da647da0e.jpg","addtime":"2021-01-07 12:11:49"}
        //{"code":"900","devid":"215571","status":"人脸识别成功","reqstatus":"1","mid":"135010","flag":"0","uid":"18611102795","imageurl":"http:\/\/huweibing-1253704117.cos.ap-guangzhou.myqcloud.com\/215571\/2021-01-07\/377f6e4c-bbcb-44c9-a4d3-b91835594037.jpg","temperature":"0.0","addtime":"2021-01-07 12:19:53"}
        \PhalApi\DI()->callback_logger->info(json_encode($callback_data, JSON_UNESCAPED_UNICODE));

        // 按api要求，返回指定格式
        // {"code":"900","devid":"610001","lockid":"01","uid":"15602898989","result":"1"}
        $return = [
            "code" => $callback_data["code"],
            "devid" => $callback_data["devid"],
            "lockid" => "01",
            "uid" => $callback_data["uid"],
            "result" => "1"
        ];
        if ($callback_data["code"] == "900") {
            $callback_data["result"] = $callback_data["reqstatus"];
        }
        $return_data = json_encode($return, JSON_UNESCAPED_UNICODE);
        \PhalApi\DI()->callback_logger->debug($return_data);
        // 发送日志信息到startspace
        Startspace::door_is_open($return_data);
        echo $return_data;
        exit;
    }
}