<?php
/**
 * PhalApi_App\Api\MemberStatus_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的Status接口做PHPUnit单元测试
 *
 * @author: shingler 20201230
 */

namespace tests\App\Api;
use App\Model\Test;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiDeviceStatus_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;
    public $url = 's=Device.Status';

    protected function setUp() {
        parent::setUp();
    }

    protected function tearDown() {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    public function appProvider() {
        return [
            [215571, "01"]
        ];
    }

    /**
     * 缺少必须参数
     * @dataProvider appProvider
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testStatusEmptyField($devid, $lockid) {
        $devid = "";
        TestRunner::go($this->url, compact("devid", "lockid"));

        $lockid = "";
        TestRunner::go($this->url, compact("devid", "lockid"));

        TestRunner::go($this->url, compact("devid"));

        TestRunner::go($this->url, compact("lockid"));
    }

    /**
     * 不正确的锁编号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1004
     */
    public function testStatusWrongLock($devid, $lockid) {
        $lockid = "aaa";
        TestRunner::go($this->url, compact('devid', 'lockid'));
    }

    /**
     * 不正确的设备号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1003
     */
    public function testStatusWrongDev($devid, $lockid) {
        $devid = "testdev";
        TestRunner::go($this->url, compact("devid", "lockid"));
    }

    /**
     * 正确返回
     * @dataProvider appProvider
     */
    public function testStatus($devid, $lockid) {
        sleep(2);
        $rs = TestRunner::go($this->url, compact("devid", "lockid"));
        $this->assertArrayHasKey("data", $rs);
        $this->assertNotEmpty($rs["data"]);
        var_dump($rs);
        $this->assertEquals($devid, $rs["data"]["devid"]);
    }
}