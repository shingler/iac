<?php
namespace App\Api;
use PhalApi\Api;
use App\Common\Auth\DoorLock;
use App\Common\Exception\ApiException;
use App\Common\Request\Device as DeviceModel;
use App\Common\Exception\AppException;

/**
 * 设备管理类
 * @package App\Api
 */
class Device extends Api
{
    protected $token;
    protected $lockids = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"];

    public function getRules() {
        return [
            "Status" => [
                "devid" => ["name" => "devid", "type" => "string", "require" => true, "min" => 1, "desc" => "设备编号"],
                "lockid" => ["name" => "lockid", "type" => "string", "require" => true, "min" => 1, "desc" => "锁编号，取值范围01-10"]
            ]
        ];
    }
    
    public function __construct() {
        // 获取token并缓存
        $this->token = DoorLock::getSignature();
    }

    /**
     * 查看设备在线状态
     * @method GET
     * @desc 查看某设备的在线状态
     * @return string content 在线状态
     * @return string data 详细在线信息
     * @exception 1001 设备编号有误
     * @exception 1002 锁编号错误
     */
    public function Status()
    {
        $devid = $this->devid;
        $lockid = $this->lockid;

        //锁编号检查
        if (!in_array($lockid, $this->lockids)) {
            throw new AppException("锁编号错误", 1002);
        }
        $deviceModel = new DeviceModel($this->token);
        $ret = $deviceModel->status($devid, $lockid);
        // 设备错误会返回信息不匹配错误
        if ($ret["code"] == 406) {
            throw new AppException("设备编号有误", 1001);
        } elseif ($ret["code"] == 403) {
            throw new ApiException($ret["msg"], $ret["code"]);
        }
        return ["content" => $ret["status"], "data" => $ret];
    }
}