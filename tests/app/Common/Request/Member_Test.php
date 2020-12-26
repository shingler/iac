<?php
/**
 * PhalApi_App\Common\Request\Member_Test
 *
 * 针对 ./src/app/Common/Request/Member.php App\Common\Request\Member 类的PHPUnit单元测试
 *
 * @author: dogstar 20201223
 */

namespace tests\App\Common\Request;
use App\Common\Request\Device;
use App\Common\Request\Member;
use App\Common\Auth\DoorLock;
use PhalApi\Helper\TestRunner;

require_once dirname(__FILE__) . '/../../../bootstrap.php';

class PhpUnderControl_AppCommonRequestMember_Test extends \PHPUnit_Framework_TestCase
{
    public $commonRequestMember;
    public $commonRequestDevice;
    public static $rand3;

    protected function setUp()
    {
        parent::setUp();
        //获取token
        $token = DoorLock::getSignature();
        $this->commonRequestMember = new \App\Common\Request\Member($token);
        $this->commonRequestDevice = new Device($token);
    }

    public static function setUpBeforeClass() {
        static::$rand3 = mt_rand(100, 999);
        echo sprintf("rand3=%s", static::$rand3);
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
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $nickname = sprintf("老乐%s", $rand3);
        $cardno = "[777777]";

        // Step 2. 操作
        $rs = $this->commonRequestMember->add($nickname, $tel, $cardno);

        // Step 3. 检验
        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testAdd
     */ 
    public function testBind()
    {
        echo "test bind".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $lockid = "01";
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d 23:59:59");
        $rs = $this->commonRequestDevice->bind($tel, $devid, $lockid, $start, $end);

        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testBind
     */
    public function testFindBind() {
        echo "test find bind".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $rs = $this->commonRequestDevice->find($tel, $devid);
        $this->assertArrayHasKey("code", $rs);
        $this->assertEquals($rs['code'], 100101);
        //{"data":{"endtime":"23:59","starttime":"00:00","startdate":"2020-12-26","devid":"215571","enddate":"2030-12-26","week":["0","1","2","3","4","5","6"]},"code":100101}

        if ($rs["code"] != 100101) {
            var_dump($rs);
        }
    }

    /**
     * @depends testBind
     */
    public function testUpdateBind() {
        echo "test update bind".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $lockid = "01";
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d 23:59:59", strtotime("+1day"));
        $rs = $this->commonRequestDevice->updateBind($tel, $devid, $lockid, $start, $end);
        //{"code":1,"msg":"成功"}
        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testAdd
     */ 
    public function testAddFace()
    {
        echo "test add face".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $filename = dirname(__FILE__)."/../../../WechatIMG189.jpeg";
        $fp = fopen($filename, "rb");
        $filedata = base64_encode(fread($fp, filesize($filename)));
        fclose($fp);
        $this->assertNotEmpty($filedata);

        $rs = $this->commonRequestMember->addFace($tel, $filedata, $devid);

        $this->assertArrayHasKey("code", $rs);
        $this->assertEquals($rs["code"], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testAdd
     */ 
    public function testUpgradeUnlock()
    {
        echo "test upgrade unlock".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $rs = $this->commonRequestDevice->upgradeUnlock($tel, $devid, false);
        $this->assertArrayHasKey("code", $rs);
        $this->assertEquals($rs["code"], 1);
        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testAdd
     */
    public function testUpgradeFace() {
        echo "test upgrade face".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = 215571;
        $rs = $this->commonRequestMember->upgradeFace($tel, $devid, false);
        $this->assertArrayHasKey("code", $rs);
        $this->assertEquals($rs["code"], 1);
        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * @depends testAdd
     */
    public function testFind() {
        echo "test find a member".PHP_EOL;
        $rand3 = static::$rand3;
        $tel = sprintf("18611106%s", $rand3);
        $devid = [215571];
        $rs = $this->commonRequestMember->find($tel);

        $this->assertNotEquals($rs, false);
        var_dump($rs);
    }

}
