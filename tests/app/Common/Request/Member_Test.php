<?php
/**
 * PhalApi_App\Common\Request\Member_Test
 *
 * 针对 ./src/app/Common/Request/Member.php App\Common\Request\Member 类的PHPUnit单元测试
 *
 * @author: dogstar 20201223
 */

namespace tests\App\Common\Request;
use App\Common\Request\Member;
use App\Common\Auth\DoorLock;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../../bootstrap.php';

class PhpUnderControl_AppCommonRequestMember_Test extends \PHPUnit_Framework_TestCase
{
    public $commonRequestMember;

    protected function setUp()
    {
        parent::setUp();
        //获取token
        $token = DoorLock::getSignature();
        $this->commonRequestMember = new \App\Common\Request\Member($token);
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
        $tel = "18611106289";
        $nickname = "老乐29";
        $cardno = "[777777]";

        // Step 2. 操作
        $rs = $this->commonRequestMember->add($nickname, $tel, $cardno);
        var_dump($rs);

        // Step 3. 检验
        // $this->assertArrayHasKey('content', $rs);
    }

    /**
     * @group testDelete
     */ 
    public function testBind()
    {
        
    }

    /**
     * @group testUpdate
     */ 
    public function testAddFace()
    {
        
    }

    /**
     * @group testStatus
     */ 
    public function testUpgradeUnlock()
    {
        
    }

    public function testUpgradeFace() {

    }

    public function testFind() {

    }

}
