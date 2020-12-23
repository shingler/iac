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
            'tel' => "18611106295",
            "nickname" => "老乐",
            "cardno" => "[666666]",
            "devid" => "[215571]",
            "lockid" => "01",
            "start" => "2020-12-23 16:30:00",
            "end" => "2020-12-23 23:59:59",
            "filedata" => $filedata
        );


        // Step 2. 操作
        $rs = TestRunner::go($url, $params);

        // Step 3. 检验
        $this->assertEquals(1, $rs['id']);
        $this->assertArrayHasKey('content', $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testDelete()
    {
        $rs = $this->appApiMember->Delete();
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
