<?php
namespace App\Api;
use App\Common\Exception\AppException;
use PhalApi\Api;
use App\Common\Auth;
use PhalApi\Exception\BadRequestException;
use App\Common\Exception\ApiException;
use App\Common\Exception\DeviceException;
use App\Common\Request\Member as MemberModel;
use App\Common\Request\Device as DeviceModel;

/**
 * 人员管理接口
 * @package App\Api
 */
class Member extends Api
{
    protected $token;
    protected $lockids = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"];

    public function getRules() {
        return [
            "Add" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true, "min" => 1],
                "nickname" => ["name" => "nickname", "desc" => "成员昵称", "type" => "string", "require" => true, "min" => 1],
                "cardno" => ["name" => "cardno", "desc" => "卡号（6位卡号，请使用JSON数组字符串传递，单次不超过10个卡号）", "type" => "array", "format" => "json", "require" => true],
                "devid" => ["name" => "devid", "desc" => "设备编号", "type" => "string", "require" => true],
                "lockid" => ["name" => "lockid", "desc" => "锁编号（01-10），支持逗号连接，注意请用英文半角", "type" => "string", "require" => true],
                "start" => ["name" => "start", "desc" => "开始日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1],
                "end" => ["name" => "end", "desc" => "到期日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1],
                "filedata" => ["name" => "filedata", "desc" => "人脸数据，对图片二进制数据做base64编码（最大不超过1M）", "type" => "string", "require" => true, "min" => 1]
            ],
            "Delete" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true, "min" => 1],
                "devid" => ["name" => "devid", "desc" => "设备编号", "type" => "string", "require" => true, "min" => 1],
                "lockid" => ["name" => "lockid", "desc" => "锁编号（01-10）", "type" => "string", "require" => true, "min" => 1, "default" => "01"],
            ],
            "Update" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true, "min" => 1],
                "devid" => ["name" => "devid", "desc" => "设备编号", "type" => "string", "require" => true, "min" => 1],
                "lockid" => ["name" => "lockid", "desc" => "锁编号（01-10），支持逗号连接，注意请用英文半角", "type" => "string", "require" => true, "min" => 1],
                "start" => ["name" => "start", "desc" => "开始日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1],
                "end" => ["name" => "end", "desc" => "到期日期时间，格式：2020-12-21 13:30:00", "type" => "date", "require" => true, "min" => 1]
            ],
            "Status" => [
                "tel" => ["name" => "tel", "desc" => "国内11位手机号", "type" => "string", "require" => true, "min" => 1],
                "devid" => ["name" => "devid", "desc" => "设备编号", "type" => "string", "require" => true, "min" => 1]
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
     * @desc 新添加一个能被智能门禁刷脸识别的用户。<br/>注意：由于设备网关限制，每3秒只能运行一次。<br/>注意：设备离线将直接报错
     * @return string content 操作结果反馈
     * @return array data 如果成功，返回查询到的用户信息
     * @return string data[].tel 电话
     * @return string data[].name 用户昵称
     * @return string data[].cishu 可刷卡的次数，默认999999
     * @return string data[].cardno 卡号，默认取手机号后6位
     * @exception 401 cardno请使用JSON数组字符串传递
     * @exception 402 cardno的数量不能超过10
     * @exception 1001 用户已存在
     * @exception 1002 图片大小不应超过1M
     * @exception 1003 开始时间/结束时间不符合日期格式
     * @exception 1004 结束时间应该大于开始时间
     * @exception 1005 锁编号不符合要求
     * @exception 1006 设备信息有误
     * @exception 2001 设备离线，请检查设备后再重试
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
        if (strpos($lockid, ",") !== false) {
            $lockid_arr = explode(",", $lockid);
            foreach ($lockid_arr as $lockid) {
                if (!in_array($lockid, $this->lockids)) {
                    throw new AppException("锁编号不符合要求", 1005);
                }
            }
        } else if (!in_array($lockid, $this->lockids)) {
            throw new AppException("锁编号不符合要求", 1005);
        }

        //检查文件大小
        $filesize = strlen($filedata)-(strlen($filedata)/8)*2;
        if ($filesize > 1024*1024) {
            throw new AppException("图片大小不应超过1M", 1002);
        }

        $memberModel = new MemberModel($this->token);
        $deviceModel = new DeviceModel($this->token);

        //判断设备是否在线，离线状态不能删除
        $device_status = $deviceModel->status($devid, $lockid);
        if (isset($device_status["code"])) {
            throw new AppException("设备信息有误", 1006);
        } elseif (isset($device_status["status"]) && $device_status["status"] == "离线") {
            throw new DeviceException("设备离线，请检查设备后再重试", 2001);
        }

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
        if (!isset($ret["code"]) || $ret["code"] != 1) {
            return ["content" => sprintf("绑定成员失败，%s", $ret["msg"])];
        }

        //注册人脸
        $ret = $memberModel->addFace($tel, $filedata, $devid);
        if (!isset($ret["code"]) || $ret["code"] != 1) {
            return ["content" => sprintf("注册人脸失败，%s", $ret["msg"])];
        }

        $device_msg = "";
        //下发离线开锁权限
        $ret = $deviceModel->upgradeUnlock($tel, $devid, false);
        if (!isset($ret["code"]) || $ret["code"] != "0") {
            $device_msg .= sprintf("下发离线开锁权限失败，%s", $ret["msg"]);
        }

        //注册离线人脸库
        $ret = $memberModel->upgradeFace($tel, $devid, false);
        if (!isset($ret["code"]) || $ret["code"] != "0") {
            $device_msg .= sprintf("注册离线人脸库失败，%s", $ret["msg"]);
        }

        //查找信息以检查
        $ret = $memberModel->find($tel);
        if (!$ret) {
            return ["content"=>"注册完成，数据同步有延迟，请稍候"];
        }

        $msg = "成员新增成功";
        if (strlen($device_msg) > 0) {
            $msg = sprintf("%s (%s)",$msg, $device_msg);
        }
        return ["content" => $msg, "data" => $ret];
    }

    /**
     * 减少人员
     * @method POST
     * @desc 将某手机号从某设备中删除<br/>注意：设备离线将直接报错
     * @exception 1001 锁编号有误
     * @exception 1002 设备信息有误
     * @exception 1003 成员信息不存在
     * @exception 1004 绑定信息不存在
     * @exception 1005 删除离线人脸库失败
     * @exception 1006 下发离线开锁权限失败
     * @exception 1007 删除人脸信息失败
     * @exception 1008 删除绑定失败
     * @exception 2001 设备离线，请检查设备后再重试
     * @return string content
     */
    public function Delete()
    {
        $tel = $this->tel;
        $devid = $this->devid;
        $lockid = $this->lockid;
        //参数格式检查
        if (!in_array($lockid, $this->lockids)) {
            throw new AppException("锁编号有误", 1001);
        }
        //判断设备是否在线，离线状态不能删除
        $deviceModel = new DeviceModel($this->token);
        $device_status = $deviceModel->status($devid, $lockid);
        if (isset($device_status["code"])) {
            throw new AppException("设备信息有误", 1002);
        } elseif (isset($device_status["status"]) && $device_status["status"] == "离线") {
            throw new DeviceException("设备离线，请检查设备后再重试", 2001);
        }
        
        //*****   删除流程  ****
        $memberModel = new MemberModel($this->token);
        //查找成员信息
        $ret = $memberModel->find($tel);
        if (!$ret) {
            throw new AppException("成员信息不存在", 1003);
        }

        //检查绑定是否存在
        try {
            $binding = $deviceModel->find($tel, $devid);
            if (!$binding) {
                //绑定不存在
                throw new AppException("绑定信息不存在", 1004);
            }
        } catch (ApiException $ex) {
            throw new AppException("设备信息有误", 1002);
        }

        //更新离线人脸库
        $res = $memberModel->upgradeFace($tel, $devid, true);
        if (!isset($res["code"]) || $res["code"] != "0") {
            throw new AppException(sprintf("删除离线人脸库失败，%s", $res["msg"]), 1005);
        }
        //更新离线开锁权限
        $res = $deviceModel->upgradeUnlock($tel, $devid, true);
        if (!isset($res["code"]) || $res["code"] != "0") {
            throw new AppException(sprintf("下发离线开锁权限失败，%s", $res["msg"]), 1006);
        }
        //删除人脸
        $res = $memberModel->deleteFace($tel, $devid);
        if (!isset($res["code"]) || $res["code"] != "1") {
            throw new AppException(sprintf("删除人脸信息失败，%s", $res["msg"]), 1007);
        }
        //删除绑定
        $res = $deviceModel->deleteBind($tel, $devid);
        if (!isset($res["code"]) || $res["code"] != "1") {
            throw new AppException(sprintf("删除绑定失败，%s", $res["msg"]), 1008);
        }
        //删除会员
        $res = $memberModel->delete($tel);
        
        return ["content" => "删除成功"];
    }

    /**
     * 修改时效
     * @method POST
     * @desc 修改某手机号可被门禁识别的有效期<br/>注意：设备离线将直接报错
     * @return string content 操作结果
     * @return data array api返回结果
     * @return data[].code api返回代码
     * @return data[].msg api返回结果
     * @exception 1001 锁编号不正确
     * @exception 1003 开始时间/结束时间不符合日期格式
     * @exception 1004 结束时间应该大于开始时间
     * @exception 1006 会员信息不存在
     * @exception 1007 设备编号错误
     * @exception 2001 设备离线，请检查设备后再重试
     */
    public function Update()
    {
        $tel = $this->tel;
        $devid = $this->devid;
        $lockid = $this->lockid;
        $start = $this->start;
        $end = $this->end;

        //参数检测
        if (!in_array($lockid, $this->lockids)) {
            throw new AppException("锁编号不正确", 1001);
        }
        if (!strtotime($start)) {
            throw new AppException("开始时间不符合日期格式", 1003);
        }
        if (!strtotime($end)) {
            throw new AppException("结束时间不符合日期格式", 1003);
        }
        if (strtotime($end) < strtotime($start)) {
            throw new AppException("结束时间应该大于开始时间", 1004);
        }

        $memberModel = new MemberModel($this->token);
        $deviceModel = new DeviceModel($this->token);

        //检查锁编号
        if (strpos($lockid, ",") !== false) {
            $lockid_arr = explode(",", $lockid);
            foreach ($lockid_arr as $lockid) {
                if (!in_array($lockid, $this->lockids)) {
                    throw new AppException("锁编号不符合要求", 1005);
                }
            }
        } else if (!in_array($lockid, $this->lockids)) {
            throw new AppException("锁编号不符合要求", 1005);
        }

        //检查设备是否在线
        $device_status = $deviceModel->status($devid, $lockid);
        if (isset($device_status["code"])) {
            throw new AppException("设备信息有误", 1006);
        } elseif (isset($device_status["status"]) && $device_status["status"] == "离线") {
            throw new DeviceException("设备离线，请检查设备后再重试", 2001);
        }

        //先检查会员信息是否存在
        $member = $memberModel->find($tel);
        if (!$member) {
            throw new AppException("会员信息不存在", 1007);
        }
        
        //检查绑定是否存在
        try {
            $binding = $deviceModel->find($tel, $devid);
            if (!$binding) {
                //不存在，创建绑定
                $ret = $deviceModel->bind($tel, $devid, $lockid, $start, $end);
            } else {
                //修改绑定
                $ret = $deviceModel->updateBind($tel, $devid, $lockid, $start, $end);
            }
        } catch (DeviceException $ex) {
            throw new AppException("设备编号错误", 1006);
        }
        //更新离线开锁权限
        $device_msg = "";
        $res = $deviceModel->upgradeUnlock($tel, $devid, false);
        if (!isset($res["code"]) || $res["code"] != "0") {
            $device_msg .= sprintf("下发离线开锁权限失败，%s", $res["msg"]);
        }

        //更新离线人脸库
        $res = $memberModel->upgradeFace($tel, $devid, false);
        if (!isset($res["code"]) || $res["code"] != "0") {
            $device_msg .= sprintf("注册离线人脸库失败，%s", $res["msg"]);
        }

        //合并消息
        $return_msg = $ret["msg"];
        if (strlen($device_msg) > 0) {
            $return_msg = sprintf("%s (%s)", $return_msg, $device_msg);
        }
        return ["content" => $return_msg, "data"=>$ret];
    }

    /**
     * 查看时效
     * @method GET
     * @desc 查看某手机号可被门禁识别的有效期
     * @return string content 错误信息
     * @return array data 如果成功，返回查询到的用户信息
     * @return string data[].member 会员信息
     * @return string data[].binding 绑定信息
     * @return string data[].member[].tel 手机号
     * @return string data[].member[].name 姓名
     * @return string data[].member[].tel 可刷卡的次数，默认999999
     * @return string data[].member[].cardno 卡号，默认取手机号后6位
     * @return string data[].binding[].startdate 开始日期
     * @return string data[].binding[].enddate 结束日期
     * @return string data[].binding[].starttime 开始时间
     * @return string data[].binding[].endtime 结束时间
     * @return string data[].binding[].devid 绑定的设备id
     * @return string data[].binding[].week 每周绑定的日期（0~6）
     * @exception 1001 用户不存在
     * @exception 1002 未查询到绑定信息
     * @exception 403 发送频繁
     */
    public function Status()
    {
        $memberModel = new MemberModel($this->token);
        
        if ($member = $memberModel->find($this->tel)) {
            // 获取绑定信息
            $deviceModel = new DeviceModel($this->token);
            try {
                if ($binding = $deviceModel->find($this->tel, $this->devid)) {
                    return ["content" => "用户信息获取成功", "data" => compact('member', 'binding')];
                } else {
                    throw new AppException("未查询到绑定信息", 1002);
                }
            } catch (DeviceException $ex) {
                throw new AppException("设备编号错误", 2002);
            }

        } else {
            throw new AppException("用户不存在", 1001);
        }
    }
}