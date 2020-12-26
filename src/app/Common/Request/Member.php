<?php
namespace App\Common\Request;

class Member
{
    private $token;
    protected static $_TYPE_ID = [
        "add" => 100,
        "update" => 203,
        "status" => 201,
        "delete" => 104,
        "find" => 101,
        "bind" => 200,
        "face" => 300,
        "unlock" => 400,
        "face_offline" => 401
    ];

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * 新增成员
     * @param string $nickname
     * @param string $tel
     * @param string $cardno
     * @throws \Exception
     * @return bool
     */
    public function add($nickname, $tel, $cardno) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["add"],
            "nickname" => $nickname,
            "tel" => $tel,
            "cardno" => $cardno,
            "cishu" => 999999,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
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
     * 注册人脸
     * @param string $tel
     * @param string $filedata
     * @param string $devid
     * @throws \Exception
     * @return bool
     */
    public function addFace($tel, $filedata, $devid) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["face"],
            "tel" => $tel,
            "filedata" => $filedata,
            "devid" => $devid,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }

    /**
     * 下发/更新离线开锁权限
     * @param string $tel
     * @param string $devid
     * @param bool $is_new
     * @throws \Exception
     * @return bool
     */
    public function upgradeUnlock($tel, $devid, $is_remove=false) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["unlock"],
            "tel" => $tel,
            "devid" => json_encode($devid),
            "flag" => $is_remove ? "02" : "01",
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }

    /**
     * 注册/删除离线人脸库
     * @param string $tel
     * @param string $devid
     * @param bool $is_remove
     * @return bool
     */
    public function upgradeFace($tel, $devid, $is_remove=false) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["face_offline"],
            "tel" => $tel,
            "devid" => json_encode($devid),
            "flag" => $is_remove ? "02" : "01",
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        return $ret;
    }

    /**
     * 查找指定成员
     * @param string $tel
     * @throws \Exception
     * @return array|bool
     */
    public function find($tel) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["find"],
            "tel" => $tel,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);

        if ($ret["code"] == 100101) {
            return $ret["data"];
        } else {
            return false;
        }
    }
}