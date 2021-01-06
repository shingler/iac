<?php
namespace App\Common\Request;

use App\Common\Exception\ApiException;
use App\Common\Exception\AppException;
use App\Common\Exception\DeviceException;

class Device
{
    private $token;
    protected static $_TYPE_ID = [
        "unlock" => "01",
        "bind" => 200,
        "find" => 201,
        "update_bind" => 203,
        "delete_bind" => 204,
        "unlock_offline" => 400,
    ];

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * 绑定成员
     * @param string $tel
     * @param array $devid
     * @param string $lockid
     * @param string $start
     * @param string $end
     * @throws \Exception
     * @return bool
     * {"code":1,"msg":"成功"}
     */
    public function bind($tel, $devid, $lockid, $start, $end) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["bind"],
            "tel" => $tel,
            "devid" => $devid,
            "lockid" => $lockid,
            "startdate" => date("Y-m-d", strtotime($start)),
            "enddate" => date("Y-m-d", strtotime($end)),
            "starttime" => date("H:i", strtotime($start)),
            "endtime" => date("H:i", strtotime($end)),
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            $ret["typeid"] = $params["typeid"];
        }
        return $ret;
    }

    /**
     * 修改绑定
     * @param string $tel
     * @param array $devid
     * @param string $lockid
     * @param string $start
     * @param string $end
     * @throws \Exception
     * @return bool
     * {"code":1,"msg":"成功"}
     */
    public function updateBind($tel, $devid, $lockid, $start, $end) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["update_bind"],
            "tel" => $tel,
            "devid" => $devid,
            "lockid" => $lockid,
            "startdate" => date("Y-m-d", strtotime($start)),
            "enddate" => date("Y-m-d", strtotime($end)),
            "starttime" => date("H:i", strtotime($start)),
            "endtime" => date("H:i", strtotime($end)),
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            $ret["typeid"] = $params["typeid"];
        }
        return $ret;
    }

    /**
     * 删除绑定
     * @param string $tel
     * @param string $devid
     * @throws \Exception
     * @return bool|string
     */
    public function deleteBind($tel, $devid) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["delete_bind"],
            "tel" => $tel,
            "devid" => $devid,
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            $ret["typeid"] = $params["typeid"];
        }
        return $ret;
    }

    /**
     * 下发/更新离线开锁权限
     * @param string $tel
     * @param string $devid
     * @param bool $is_remove
     * @throws \Exception
     * @return bool
     * {"devid":"215571","code":"0","msg":"success"}
     */
    public function upgradeUnlock($tel, $devid, $is_remove=false) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["unlock_offline"],
            "tel" => $tel,
            "devid" => $devid,
            "flag" => $is_remove ? "02" : "01",
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != "0") {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            $ret["typeid"] = $params["typeid"];
        }
        return $ret;
    }

    /**
     * 查找指定绑定
     * @param string $tel
     * @throws \Exception
     * @return array|bool
     * {"data":{"endtime":"23:59","starttime":"00:00","startdate":"2020-12-26","devid":"215571","enddate":"2030-12-26","week":["0","1","2","3","4","5","6"]},"code":100101}
     * {"code":404,"msg":"传参错误：设备归属错误"}
     */
    public function find($tel, $devid) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["find"],
            "tel" => $tel,
            "devid" => $devid,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);

        if ($ret["code"] == 100101) {
            return $ret["data"];
        } elseif($ret["code"] == 404) {
            throw new ApiException($ret["msg"], $ret["code"]);
        } else {
            return false;
        }
    }

    /**
     * 发送开锁指令
     * @param string $devid
     * @param string $lockid
     * @throws \Exception
     * @return bool
     * {"devid":"215571","code":"0","msg":"success"}
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
        if ($ret["code"] != "0") {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            $ret["typeid"] = $params["typeid"];
        }
        return $ret;
    }

    /**
     * 查看设备状态
     * @param $devid
     * @throws \Exception
     * @return bool|string
     * {"devid":"215571","status":"在线","lastopenlocktime":"未知","lastduandiantime":"未知","lastcloselocktime":"未知","lockstatus":"未知","doorstatus":"未知","qudianstatus":"未知","lastopendoortime":"未知","lastclosedoortime":"未知","lastqudiantime":"未知"}
     * {"code":406,"msg":"信息不匹配"}
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