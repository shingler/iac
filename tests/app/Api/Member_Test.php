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


    /**
     * @group testGetRules
     */ 
    public function testAdd()
    {
//        $rs = $this->appApiMember->getRules();
        // Step 1. 构造
        $url = 's=Member.Add';
        $filedata = "";
        $filename = dirname(__FILE__).'/../../WechatIMG189.jpeg';
        $fp = fopen($filename, "rb");
        $filedata = base64_encode(fread($fp, filesize($filename)));
        fclose($fp);

        $params = array(
            'tel' => "18611106289",
            "nickname" => "老乐29",
            "cardno" => "[777777]",
            "devid" => "[215571]",
            "lockid" => "01",
            "start" => "2020-12-25 16:30:00",
            "end" => "2020-12-25 23:59:59",
            "filedata" => $filedata
        );


        // Step 2. 操作
        $rs = TestRunner::go($url, $params);
        var_dump($rs);

        // Step 3. 检验
        $this->assertArrayHasKey('content', $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
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

    /**
     * @group testUpdate
     */ 
    public function testUpdate()
    {
        $rs = $this->appApiMember->Update();
    }

    /**
     * @group testStatus
     */ 
    public function testStatus()
    {
        $rs = $this->appApiMember->Status();
    }

}
