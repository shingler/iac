<?php
namespace App\Common\Request;

use App\Common\Exception\ApiException;

class Member
{
    private $token;
    protected static $_TYPE_ID = [
        "add" => 100,
        "update" => 203,
        "status" => 201,
        "delete" => 104,
        "find" => 101,
        "face" => 300,
        "delete_face" => 301,
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
     * {"code":1,"msg":"成功"}
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
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
        }
        return $ret;
    }

    /**
     * 注册人脸
     * @param string $tel
     * @param string $filedata
     * @param string $devid
     * @throws \Exception
     * @return bool
     * {"code":1,"msg":"注册成功"}
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
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
        }
        return $ret;
    }

    /**
     * 删除人脸
     * @param string $tel
     * @param string $devid
     * @throws \Exception
     * @return bool
     * {"code":1,"msg":"删除成功"}
     */
    public function deleteFace($tel, $devid) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["delete_face"],
            "tel" => $tel,
            "devid" => $devid,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
        }
        return $ret;
    }

    /**
     * 注册/删除离线人脸库
     * @param string $tel
     * @param string $devid
     * @param bool $is_remove
     * @throws \Exception
     * @return bool
     * {"devid":"215571","code":"0","msg":"success"}
     */
    public function upgradeFace($tel, $devid, $is_remove=false) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["face_offline"],
            "tel" => $tel,
            "devid" => $devid,
            "flag" => $is_remove ? "02" : "01",
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != "0") {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
        }
        return $ret;
    }

    /**
     * 查找指定成员
     * @param string $tel
     * @throws \Exception
     * @return array|bool
     * {"data":{"tel":"18611106639","name":"老乐639","cishu":"999999","cardno":"777777"},"code":100101}
     * {"code":100100,"msg":"成员信息不存在"}
     * {"code":403,"msg":"发送频繁"}
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
            //成员信息存在
            return $ret["data"];
        } elseif ($ret["code"] == 403) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
            throw new ApiException($ret["msg"], $ret["code"]);
        } else {
            //成员信息不存在
            return false;
        }
    }

    /**
     * 删除指定成员
     * @param $tel
     * @throws \Exception
     * @return bool|array
     * {"code":1,"msg":"成功"}
     */
    public function delete($tel) {
        $url = Urls::member();
        $params = [
            "typeid" => self::$_TYPE_ID["delete"],
            "tel" => $tel,
            "token" => $this->token
        ];
        $ret = \App\curl_post($url, $params);
        if ($ret["code"] != 1) {
            \PhalApi\DI()->logger->error($ret["msg"], $params);
        }
        return $ret;
    }

}