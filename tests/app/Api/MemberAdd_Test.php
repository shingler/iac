<?php
/**
 * PhalApi_App\Api\Member_Test
 *
 * 针对 ./src/app/Api/Member.php App\Api\Member 类的Add接口做PHPUnit单元测试
 *
 * @author: shingler 20201223
 */

namespace tests\App\Api;
use App\Api\Member;
use App\Common\Request\Device;
use PhalApi\Exception\BadRequestException;
use App\Common\Exception\AppException;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../bootstrap.php';

class PhpUnderControl_AppApiMemberAdd_Test extends \PHPUnit_Framework_TestCase
{
    public $appApiMember;
    public $url = 's=Member.Add';

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
        $rand4 = mt_rand(1000, 9999);
        $tel = "1861110".$rand4;
        $nickname = sprintf("老乐%s", $rand4);
        $devid = 215571;
        $lockid = "01";
        $cardno = substr($tel, -6);
        $filename = dirname(__FILE__).'/../../WechatIMG189.jpeg';
        $filedata = base64_encode(file_get_contents($filename));
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d 23:59:59");

        $params = array(
            'tel' => $tel,
            "nickname" => $nickname,
            "cardno" => json_encode([$cardno]),
            "devid" => $devid,
            "lockid" => $lockid,
            "start" => $start,
            "end" => $end,
            "filedata" => $filedata
        );

        return [[$params]];
    }


    /**
     * 新增一个已有账号
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1001
     */ 
    public function testAddExist($params)
    {
        $params["tel"] = "18611106295";
        $rs_fail = TestRunner::go($this->url, $params);
//        $this->assertInstanceOf("App\Common\Exception\AppException", $rs_fail);
//        $this->assertEquals(1001, $rs_fail->getCode());

    }

    /**
     * 新增大于1M的照片用例
     * @dataProvider appProvider
     * @expectedException App\Common\Exception\AppException
     * @expectedExceptionCode 1002
     */ 
    public function testAddInvalidFace($params)
    {
        $params["filedata"] = base64_encode(file_get_contents(dirname(__FILE__).'/../../bigpic.jpeg'));
        $rs_fail = TestRunner::go($this->url, $params);
//        $this->assertInstanceOf("App\Common\Exception\AppException", $rs_fail);
//        $this->assertEquals(1002, $rs_fail->getCode());
    }

    /**
     * 新增参数不全
     * @dataProvider appProvider
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testAddParamEmpty($params)
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
     */
    public function testAddInvalidDate($params) {
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
     * @expectedException PhalApi\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testAddInvalidField($params) {
        $param1 = $params;
        $param1["devid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param1);

        $param2 = $params;
        $param2["cardno"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param2);

        $param3 = $params;
        $param3["lockid"] = "aaa";
        $rs_fail = TestRunner::go($this->url, $param3);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);
//        $this->assertInstanceOf("App\Common\Exception\AppException", $rs_fail);
//        $this->assertEquals(1005, $rs_fail->getCode());


        $param4 = $params;
        $param4["lockid"] = "01,aaa";
        $rs_fail = TestRunner::go($this->url, $param4);
        $this->setExpectedException("App\Common\Exception\AppException", "", 1005);
//        $this->assertInstanceOf("App\Common\Exception\AppException", $rs_fail);
//        $this->assertEquals(1005, $rs_fail->getCode());
    }

    /**
     * 新增一个随机手机号
     * @dataProvider appProvider
     */
    public function testAddRandom($params)
    {
//        $this->markTestSkipped("先测试失败的用例");
        sleep(3);
        $rs = TestRunner::go($this->url, $params);
//        var_dump($rs);
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
//        var_dump($rs);
//        $this->assertNotInstanceOf("App\Common\Exception\AppException", $rs);
//        $this->assertEquals("403", $rs->getCode());
    }

}
