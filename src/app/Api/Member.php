<?php
namespace App\Api;
use App\Common\Exception\AppException;
use PhalApi\Api;
use App\Common\Auth;
use PhalApi\Exception\BadRequestException;
use App\Common\Exception\ApiException;
use App\Common\Request\Member as MemberModel;
use App\Common\Request\Device as DeviceModel;

/**
 * 人员管理接口
 * @package App\Api
 */
class Member extends Api
{
    protected $token;

    public function getRules() {
        return [
            "Add" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true, "min" => 1],
                "nickname" => ["name" => "nickname", "desc" => "成员昵称", "type" => "string", "require" => true, "min" => 1],
                "cardno" => ["name" => "cardno", "desc" => "卡号（6位卡号，请使用JSON数组字符串传递，单次不超过10个卡号）", "type" => "array", "format" => "json", "require" => true],
                "devid" => ["name" => "devid", "desc" => "设备编号", "type" => "string", "require" => true, "min" => 1],
                "lockid" => ["name" => "lockid", "desc" => "锁编号（01-10），支持逗号连接，注意请用英文半角", "type" => "string", "require" => true],
                "start" => ["name" => "start", "desc" => "开始日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1],
                "end" => ["name" => "end", "desc" => "到期日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1],
                "filedata" => ["name" => "filedata", "desc" => "人脸数据，对图片二进制数据做base64编码（最大不超过1M）", "type" => "string", "require" => true, "min" => 1]
            ],
            "Delete" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true],
                "devid" => ["name" => "devid", "desc" => "设备编号（请使用JSON数组字符串传递，单次不超过20个设备）", "type" => "array", "format" => "json", "require" => true],
            ],
            "Update" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true],
                "devid" => ["name" => "devid", "desc" => "设备编号（请使用JSON数组字符串传递，单次不超过20个设备）", "type" => "array", "format" => "json", "require" => true],
                "lockid" => ["name" => "lockid", "desc" => "锁编号（01-10），支持逗号连接，注意请用英文半角", "type" => "string", "require" => true],
                "start" => ["name" => "start", "desc" => "开始日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true],
                "end" => ["name" => "end", "desc" => "到期日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true]
            ],
            "Status" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true],
                "devid" => ["name" => "devid", "desc" => "设备编号（请使用JSON数组字符串传递，单次不超过20个设备）", "type" => "json", "require" => true],
            ]
        ];
    }

    public function __construct() {
        // 获取token并缓存
        $this->token = Auth\DoorLock::getSignature();
    }

    /**
     * 增加人员
     * @method POST
     * @desc 新添加一个能被智能门禁刷脸识别的用户
     */
    public function Add()
    {
        $tel = $this->tel;
        $nickname = $this->nickname;
        $cardno = $this->cardno;
        $devid = $this->devid;
        $lockid = $this->lockid;
        $start = $this->start;
        $end = $this->end;
        $filedata = $this->filedata;

        //json数据检查
        if (!is_array($cardno)) {
            throw new BadRequestException("cardno请使用JSON数组字符串传递", 1);
        }
        if (count($cardno) > 10) {
            throw new BadRequestException("cardno的数量不能超过10", 2);
        }

//        if (!is_array($devid)) {
//            throw new BadRequestException("devid请使用JSON数组字符串传递", 3);
//        }
//        if (count($devid) > 20) {
//            throw new BadRequestException("devid的数量不能超过20", 4);
//        }
        //检查日期格式
        if (!strtotime($start)) {
            throw new AppException("开始时间不符合日期格式", 1003);
        }
        if (!strtotime($end)) {
            throw new AppException("结束时间不符合日期格式", 1003);
        }
        if (strtotime($end) < strtotime($start)) {
            throw new AppException("结束时间应该大于开始时间", 1004);
        }

        //检查锁编号
        $lock_ids = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"];
        if (strpos($lockid, ",") !== false) {
            $lockid_arr = explode(",", $lockid);
            foreach ($lockid_arr as $lockid) {
                if (!in_array($lockid, $lock_ids)) {
                    throw new AppException("锁编号不符合要求", 1005);
                }
            }
        } else if (!in_array($lockid, $lock_ids)) {
            throw new AppException("锁编号不符合要求", 1005);
        }

        //检查文件大小
        $filesize = strlen($filedata)-(strlen($filedata)/8)*2;
        if ($filesize > 1024*1024) {
            throw new AppException("图片大小不应超过1M", 1002);
        }

        $memberModel = new MemberModel($this->token);
        $deviceModel = new DeviceModel($this->token);
        //检查人员是否已存在

        $ret = $memberModel->find($tel);
        if ($ret) {
            throw new AppException("用户已存在", 1001);
        }

        //新增成员
        $ret = $memberModel->add($nickname, $tel, $cardno);
        //{"code":1,"msg":"成功"}
        if ($ret["code"] != 1) {
            return ["content" => "成员新增失败，".$ret["msg"]];
        }
        
        //绑定成员
        $ret = $deviceModel->bind($tel, $devid, $lockid, $start, $end);
        if ($ret["code"] != 1) {
            return ["content" => sprintf("绑定成员失败，%s", $ret["msg"])];
        }

        //注册人脸
        $ret = $memberModel->addFace($tel, $filedata, $devid);
        if ($ret["code"] != 1) {
            return ["content" => sprintf("注册人脸失败，%s", $ret["msg"])];
        }

        //下发离线开锁权限
        $ret = $deviceModel->upgradeUnlock($tel, $devid, false);
        if ($ret["code"] != "0") {
            return ["content" => sprintf("下发离线开锁权限失败，%s", $ret["msg"])];
        }

        //注册离线人脸库
        $ret = $memberModel->upgradeFace($tel, $devid, false);
        if ($ret["code"] != "0") {
            return ["content" => sprintf("注册离线人脸库失败，%s", $ret["msg"])];
        }

        //查找信息以检查
        $ret = $memberModel->find($tel);
        if (!$ret) {
            return ["content"=>"注册完成，数据同步有延迟，请稍候"];
        }

        return ["content"=>"成员新增成功", "data"=>$ret];
    }

    /**
     * 减少人员
     * @method POST
     * @desc 将某手机号从某设备中删除
     */
    public function Delete()
    {

    }

    /**
     * 修改时效
     * @method POST
     * @desc 修改某手机号可被门禁识别的有效期
     */
    public function Update()
    {

    }

    /**
     * 查看时效
     * @method GET
     * @desc 查看某手机号可被门禁识别的有效期
     */
    public function Status()
    {

    }
}