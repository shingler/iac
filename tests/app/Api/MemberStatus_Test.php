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
            ["18611106295"]
        ];
    }

    /**
     * 缺少必须参数
     * @dataProvider appProvider
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testStatusEmptyField($tel) {
        $tel = "";
        TestRunner::go($this->url, compact("tel"));

        TestRunner::go($this->url, []);
    }

    /**
     * 未注册的手机号
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\AppException
     * @expectedExceptionCode 1001
     */
    public function testStatusNotSignTel($tel) {
        $tel = "18911106295";
        sleep(2);
        TestRunner::go($this->url, compact('tel'));
    }

    /**
     * 正确返回
     * @dataProvider appProvider
     */
    public function testStatus($tel) {
        sleep(2);
        $rs = TestRunner::go($this->url, compact('tel'));
        $this->assertArrayHasKey("data", $rs);
        $this->assertNotEmpty($rs["data"]);
    }

    /**
     * 太频繁
     * @dataProvider appProvider
     * @expectedException \App\Common\Exception\ApiException
     * @expectedExceptionCode 403
     */
    public function testStatusFrequency($tel) {
        TestRunner::go($this->url, compact('tel'));
    }
}