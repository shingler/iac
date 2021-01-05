<?php
/**
 * PhalApi_App\Api\MemberDelete_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的Delete接口做PHPUnit单元测试
 *
 * @author: shingler 20210105
 */

namespace tests\App\Api;
use App\Api\Member;
use App\Common\Exception\AppException;
use App\Common\Exception\DeviceException;
use App\Model\Test;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiMemberDelete_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;
    public $url = 's=Member.Delete';

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
            ["18611102459", 215571, "01"]
        ];
    }

    /**
     * 缺少必须参数
     * @dataProvider appProvider
     * @expectedException \PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testDeleteEmptyField($tel, $devid, $lockid) {
        $tel = "";
        TestRunner::go($this->url, compact("tel", "devid", "lockid"));

        $devid = "";
        TestRunner::go($this->url, compact("tel", "devid", "lockid"));

        $lockid = "";
        TestRunner::go($this->url, compact("tel", "devid", "lockid"));

        TestRunner::go($this->url, compact("devid", "lockid"));

        TestRunner::go($this->url, compact("tel", "lockid"));

        TestRunner::go($this->url, compact("tel", "devid"));
    }

    /**
     * 未注册的手机号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1003
     */
    public function testDeleteNotSignTel($tel, $devid, $lockid) {
        $tel = "18911106295";
        try {
            TestRunner::go($this->url, compact('tel', 'devid', 'lockid'));
        } catch (DeviceException $ex) {
            $this->fail($ex->getMessage());
        }
        
    }

    /**
     * 不正确的设备号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1002
     */
    public function testDeleteWrongDev($tel, $devid, $lockid) {
        $devid = "testdev";
        try {
            $rs = TestRunner::go($this->url, compact('tel', 'devid', 'lockid'));
            var_dump($rs);
        } catch (DeviceException $ex) {
            $this->fail($ex->getMessage());
        }
    }

    /**
     * 不正确的锁编号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1001
     */
    public function testDeleteWrongLock($tel, $devid, $lockid) {
        $lockid = "testlock";
        TestRunner::go($this->url, compact('tel', 'devid', 'lockid'));
    }

    /**
     * 正确返回
     * @dataProvider appProvider
     */
    public function testDelete($tel, $devid, $lockid) {
        sleep(2);
        $rs = TestRunner::go($this->url, compact('tel', 'devid', 'lockid'));
        try {
            $this->assertArrayHasKey("data", $rs);
            $this->assertNotEmpty($rs["data"]);
        } catch (DeviceException $ex) {
            $this->fail($ex->getMessage());
        }
    }
}