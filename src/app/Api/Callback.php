<?php
namespace App\Api;
use PhalApi\Api;
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;

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
        \PhalApi\DI()->callback_logger->debug(json_encode($return, JSON_UNESCAPED_UNICODE));
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
        exit;
    }
}