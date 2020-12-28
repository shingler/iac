<?php
/**
 * PhalApi_App\Api\Member_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的PHPUnit单元测试
 *
 * @author: dogstar 20201223
 */

namespace tests\App\Api;
use App\Api\Member;
use App\Common\Request\Device;
use App\Common\AppException;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiMember_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;

    protected function setUp()
    {
        parent::setUp();
        $this->appApiMember = new \App\Api\Member();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    public function appProvider() {
        $rand4 = mt_rand(1000, 9999);
        return [
            ["1861110".$rand4, 215571]
        ];
    }


    /**
     * @dataProvider appProvider
     * @expectedException \App\Common\AppException
     * @expectedExceptionCode 1001
     */ 
    public function testAdd($tel, $devid)
    {
//        $rs = $this->appApiMember->getRules();
        // Step 1. 构造
        $url = 's=Member.Add';
        $filename = dirname(__FILE__).'/../../WechatIMG189.jpeg';
        $filedata = base64_encode(file_get_contents($filename));
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d 23:59:59");

        // 应该报已存在
        $params = array(
            'tel' => "18611106295",
            "nickname" => "老乐",
            "cardno" => "[777777]",
            "devid" => json_encode([$devid]),
            "lockid" => "01",
            "start" => $start,
            "end" => $end,
            "filedata" => $filedata
        );
//        $rs_fail = TestRunner::go($url, $params);

        // 随机手机号，应该成功
        $params = array(
            'tel' => $tel,
            "nickname" => "老乐".substr($tel, -4),
            "cardno" => substr($tel, -6),
            "devid" => $devid,
            "lockid" => "01",
            "start" => $start,
            "end" => $end,
            "filedata" => $filedata
        );
        $rs = TestRunner::go($url, $params);

        $this->markTestIncomplete("该测试用例尚未完成");
    }

    /**
     * @group testUpdate
     */ 
    public function testUpdate()
    {

    }

    /**
     * @group testStatus
     */ 
    public function testStatus()
    {

    }

    /**
     *
     */
    public function testDelete()
    {
        $this->markTestSkipped("暂不测试删除");
        $cache = \PhalApi\DI()->cache;
        $token = $cache->get("access_token");
        if ($token == NULL) {
            // 获取token并缓存
            $doorLock = new Auth\DoorLock();
            $token = $doorLock->getSignature();
            $expires = strtotime($token["expires_in"]) - strtotime("now");
            $cache->set("access_token", $token["access_token"], $expires);
        }
        $deviceModel = new Device($token);
        $devid = "215571";
        $lockid = "01";
        $ret = $deviceModel->unlock($devid, $lockid);
        var_dump($ret);
    }

}
