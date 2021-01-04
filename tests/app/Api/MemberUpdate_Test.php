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
        $tel = "18611106522";
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
     * 不存在的手机号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 2001
     */
    public function testUpdateNotSignTel($params) {
        $param = $params;
        $param["tel"] = "18912345678";
        $rs_fail = TestRunner::go($this->url, $param);
    }

    /**
     * 不匹配的设备号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 2002
     */
    public function testUpdateWrongDev($params) {
        //测试用例会访问对方接口，有频次限制
        sleep(2);
        $param2 = $params;
        $param2["devid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);
    }

    /**
     * 错误的锁编号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1001
     */
    public function testUpdateWrongLock($params) {
        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
    }

    /**
     * 成功修改
     * @dataProvider appProvider
     */
    public function testUpdate($params)
    {
//        $this->markTestSkipped("先测试失败的用例");
        sleep(2);
        var_dump($params);
        //获取修改前的时效，用于和修改后的做对比
        $rs_before = TestRunner::go("s=Member.Status", ["devid" => $params["devid"], "tel" => $params["tel"]]);
        $before_datetime = sprintf("%s %s", $rs_before["data"]["binding"]["enddate"], $rs_before["data"]["binding"]["endtime"]);
        $before_ts = strtotime($before_datetime);
        echo sprintf("修改前的时间字符串为%s，时间戳为%s", $before_datetime, $before_ts);

        sleep(2);
        $rs = TestRunner::go($this->url, $params);
        var_dump($rs);
        $this->assertArrayHasKey("data", $rs);
        $this->assertEquals(1, $rs["data"]["code"]);

        //获取修改后的时效
        sleep(2);
        $rs_after = TestRunner::go("s=Member.Status", ["devid" => $params["devid"], "tel" => $params["tel"]]);
        $after_datetime = sprintf("%s %s", $rs_before["data"]["binding"]["enddate"], $rs_before["data"]["binding"]["endtime"]);
        $after_ts = strtotime($after_datetime);
        echo sprintf("修改后的时间字符串为%s，时间戳为%s", $after_datetime, $after_ts);

        $this->assertGreaterThanOrEqual($before_ts, $after_ts);
        
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
