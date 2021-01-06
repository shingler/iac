<?php
/**
 * PhalApi_App\Api\MemberStatus_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的Status接口做PHPUnit单元测试
 *
 * @author: shingler 20201230
 */

namespace tests\App\Api;
use App\Api\Member;
use App\Model\Test;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiMemberStatus_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;
    public $url = 's=Member.Status';

    protected function setUp() {
        parent::setUp();
        $this->appApiMember = new Member();
    }

    protected function tearDown() {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    public function appProvider() {
        return [
            ["18611106295", 215571]
        ];
    }

    /**
     * 缺少必须参数
     * @dataProvider appProvider
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testStatusEmptyField($tel, $devid) {
        $tel = "";
        TestRunner::go($this->url, compact("tel", "devid"));
        $devid = "";
        TestRunner::go($this->url, compact("tel", "devid"));

        TestRunner::go($this->url, compact("tel"));
        TestRunner::go($this->url, compact("devid"));
    }

    /**
     * 不正确的设备号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1003
     */
    public function testDeleteWrongDev($tel, $devid) {
        $devid = "testdev";
        $rs = TestRunner::go($this->url, compact('tel', 'devid'));
    }

    /**
     * 未注册的手机号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 2003
     */
    public function testStatusNotSignTel($tel, $devid) {
        $tel = "18911106295";
        sleep(2);
        TestRunner::go($this->url, compact('tel', 'devid'));
    }

    /**
     * 正确返回
     * @dataProvider appProvider
     */
    public function testStatus($tel, $devid) {
        sleep(2);
        $rs = TestRunner::go($this->url, compact('tel', 'devid'));
        $this->assertArrayHasKey("data", $rs);
        $this->assertNotEmpty($rs["data"]);
        $this->assertArrayHasKey("member", $rs["data"]);
        $this->assertArrayHasKey("binding", $rs["data"]);
        $this->assertEquals($tel, $rs["data"]["member"]["tel"]);
    }

    /**
     * 太频繁
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\ApiException
     * @expectedExceptionCode 403
     */
    public function testStatusFrequency($tel, $devid) {
        TestRunner::go($this->url, compact('tel', 'devid'));
    }
}