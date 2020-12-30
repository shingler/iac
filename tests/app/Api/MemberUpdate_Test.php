<?php
/**
 * PhalApi_App\Api\Member_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的Update接口做PHPUnit单元测试
 *
 * @author: shingler 20201230
 */

namespace tests\App\Api;
use App\Api\Member;
use App\Common\Request\Device;
use PhalApi\Exception\BadRequestException;
use App\Common\Exception\AppException;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiMemberUpdate_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;
    public $url = 's=Member.Update';

    protected function setUp()
    {
        parent::setUp();
        $this->appApiMember = new Member();
    }

    protected function tearDown()
    {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    public function appProvider() {
        $tel = "18611106295";
        $devid = 215571;
        $lockid = "01";
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d 23:59:59");

        $params = array(
            'tel' => $tel,
            "devid" => $devid,
            "lockid" => $lockid,
            "start" => $start,
            "end" => $end
        );

        return [[$params]];
    }

    /**
     * 修改资料参数不全
     * @dataProvider appProvider
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testUpdateParamEmpty($params)
    {
        $params1 = $params;
        $params1["tel"] = "";
        $rs_fail1 = TestRunner::go($this->url, $params1);

        $params2 = $params;
        $params2["nickname"] = "";
        $rs_fail2 = TestRunner::go($this->url, $params2);

        $params3 = $params;
        unset($params3["devid"]);
        $rs_fail3 = TestRunner::go($this->url, $params3);

        $params4 = $params;
        unset($params4["start"]);
        $rs_fail4 = TestRunner::go($this->url, $params4);
    }

    /**
     * 新增不合理的日期
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1004
     */
    public function testUpdateInvalidDate($params) {
        $param1 = $params;
        $param1["start"] = "test start";
        $this->setExpectedException("\App\Common\Exception\AppException", "", 1003);
        $rs_fail1 = TestRunner::go($this->url, $param1);

        $param2 = $params;
        $param2["end"] = "test end";
        $this->setExpectedException("\App\Common\Exception\AppException", "", 1003);
        $rs_fail2 = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["end"] = date("Y-m-d H:i", strtotime("-1day"));
        $this->setExpectedException("\App\Common\Exception\AppException", "", 1004);
        $rs_fail3 = TestRunner::go($this->url, $param3);
    }

    /**
     * 新增字段格式不正确
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1003
     */
    public function testUpdateInvalidField($params) {
        $param2 = $params;
        $param2["cardno"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);

        $param4 = $params;
        $param4["lockid"] = "01,aaa";
        $rs_fail = TestRunner::go($this->url, $param4);
    }

    /**
     * 不存在的手机号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1001
     */
    public function testUpdateNotSignTel($params) {
        $param2 = $params;
        $param2["cardno"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);

        $param4 = $params;
        $param4["lockid"] = "01,aaa";
        $rs_fail = TestRunner::go($this->url, $param4);
    }

    /**
     * 不匹配的设备号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1002
     */
    public function testUpdateWrongDev($params) {
        $param2 = $params;
        $param2["cardno"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);

        $param4 = $params;
        $param4["lockid"] = "01,aaa";
        $rs_fail = TestRunner::go($this->url, $param4);
    }

    /**
     * 错误的锁编号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1005
     */
    public function testUpdateWrongLock($params) {
        $param2 = $params;
        $param2["cardno"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);

        $param4 = $params;
        $param4["lockid"] = "01,aaa";
        $rs_fail = TestRunner::go($this->url, $param4);
    }

    /**
     * 成功修改
     * @dataProvider appProvider
     */
    public function testUpdate($params)
    {
//        $this->markTestSkipped("先测试失败的用例");
        sleep(3);
        var_dump($params);
        $rs = TestRunner::go($this->url, $params);
        var_dump($rs);
        $this->assertArrayHasKey("data", $rs);
    }

    /**
     * 频繁调用
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\ApiException
     * @expectedExceptionCode 403
     */
    public function testAddFrequently($params)
    {
//        $this->markTestSkipped("先测试失败的用例");
//        sleep(3);
        $rs = TestRunner::go($this->url, $params);
    }

}
