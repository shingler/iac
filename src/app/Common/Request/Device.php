<?php
namespace App\Common\Request;

class Device
{
    private $token;
    protected static $_TYPE_ID = [
        "unlock" => "01"
    ];

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * 发送开锁指令
     * @param string $devid
     * @param string $lockid
     * @throws \Exception
     * @return bool
     */
    public function unlock($devid, $lockid) {
        $url = Urls::device();
        $params = [
            "typeid" => self::$_TYPE_ID["unlock"],
            "devid" => $devid,
            "lockid" => $lockid,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }

    /**
     * 查看设备状态
     * @param $devid
     */
    public function status($devid, $lockid) {
        $url = Urls::status();
        $params = [
            "devid" => $devid,
            "lockid" => $lockid,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }
}