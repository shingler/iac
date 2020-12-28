<?php
/**
 * PhalApi_App\Common\Request\Device_Test
 *
 * 针对 ./src/app/Common/Request/Device.php App\Common\Request\Device 类的PHPUnit单元测试
 *
 * @author: dogstar 20201223
 */

namespace tests\App\Common\Request;
use App\Common\Request\Device;
use App\Common\Auth\DoorLock;

require_once dirname(__FILE__) . '/../../../bootstrap.php';

class PhpUnderControl_AppCommonRequestDevice_Test extends \PHPUnit_Framework_TestCase
{
    public $commonRequestDevice;

    protected function setUp() {
        parent::setUp();
        //获取token
        $token = DoorLock::getSignature();
        $this->commonRequestDevice = new Device($token);
    }

    protected function tearDown() {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    /**
     * @group testGetRules
     */
    public function testUnlock() {
        echo sprintf("test unlock%s", PHP_EOL);
        $devid = 215571;
        $lockid = "01";
        $rs = $this->commonRequestDevice->unlock($devid, $lockid);
        //{"devid":"215571","code":"0","msg":"success"}

        // Step 3. 检验
        $this->assertArrayHasKey('code', $rs);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @group testGetRules
     */
    public function testStatus() {
        echo sprintf("test status%s", PHP_EOL);
        $devid = 215571;
        $lockid = "01";

        // Step 2. 操作
        $rs = $this->commonRequestDevice->status($devid, $lockid);
        //{"devid":"215571","status":"在线","lastopenlocktime":"未知","lastduandiantime":"未知","lastcloselocktime":"未知","lockstatus":"未知","doorstatus":"未知","qudianstatus":"未知","lastopendoortime":"未知","lastclosedoortime":"未知","lastqudiantime":"未知"}

        // Step 3. 检验
        $this->assertNotEmpty($rs);
        $this->assertInternalType("array", $rs);

        var_dump($rs);
    }

}