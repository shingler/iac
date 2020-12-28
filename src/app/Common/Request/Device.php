<?php
namespace App\Common\Request;

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
            "devid" => json_encode($devid),
            "lockid" => $lockid,
            "startdate" => date("Y-m-d", strtotime($start)),
            "enddate" => date("Y-m-d", strtotime($end)),
            "starttime" => date("H:i", strtotime($start)),
            "endtime" => date("H:i", strtotime($end)),
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
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
            "devid" => json_encode($devid),
            "lockid" => $lockid,
            "startdate" => date("Y-m-d", strtotime($start)),
            "enddate" => date("Y-m-d", strtotime($end)),
            "starttime" => date("H:i", strtotime($start)),
            "endtime" => date("H:i", strtotime($end)),
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
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
            "devid" => json_encode($devid),
            "json" => true,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
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
            "devid" => json_encode($devid),
            "flag" => $is_remove ? "02" : "01",
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }

    /**
     * 查找指定绑定
     * @param string $tel
     * @throws \Exception
     * @return array|bool
     * {"data":{"endtime":"23:59","starttime":"00:00","startdate":"2020-12-26","devid":"215571","enddate":"2030-12-26","week":["0","1","2","3","4","5","6"]},"code":100101}
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
     * @throws \Exception
     * @return bool|string
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