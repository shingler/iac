<?php
namespace App\Common;

class Member
{
    private $token;
    protected static $_TYPE_ID = [
        "add" => 100,
        "update" => 203,
        "status" => 201,
        "delete" => 104
    ];

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * 新增成员
     * @param string $nickname
     * @param string $tel
     * @param string $cardno
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
     * @param string $devid
     * @param string $start
     * @param string $end
     * @return bool
     */
    public function bind($tel, $devid, $start, $end) {
        return true;
    }

    /**
     * 注册人脸
     * @param string $tel
     * @param string $filedata
     * @param string $devid
     * @return bool
     */
    public function addFace($tel, $filedata, $devid) {
        return true;
    }

    /**
     * 下发/更新离线开锁权限
     * @param string $tel
     * @param string $devid
     * @param bool $is_new
     * @return bool
     */
    public function upgradeUnlock($tel, $devid, $is_new=false) {
        return true;
    }

    /**
     * 注册/删除离线人脸库
     * @param string $tel
     * @param string $devid
     * @param bool $is_remove
     * @return bool
     */
    public function upgradeFace($tel, $devid, $is_remove=false) {
        return true;
    }
}