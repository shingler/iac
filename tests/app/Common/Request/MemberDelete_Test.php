<?php
/**
 * PhalApi_App\Common\Request\MemberDelete_Test
 * 主要对减员一系列功能的测试
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

class PhpUnderControl_AppCommonRequestMemberDelete_Test extends \PHPUnit_Framework_TestCase
{
    public $commonRequestMember;
    public $commonRequestDevice;
    public static $rand3;

    protected function setUp() {
        parent::setUp();
        //获取token
        $token = DoorLock::getSignature();
        $this->commonRequestMember = new Member($token);
        $this->commonRequestDevice = new Device($token);
    }

    /**
     * 提供数据
     * @return array
     */
    public function additionProvider() {
        return [
            ["18611106696", 215571]
        ];
    }

    protected function tearDown() {
        // 输出本次单元测试所执行的SQL语句
        // var_dump(\PhalApi\DI()->tracer->getSqls());

        // 输出本次单元测试所涉及的追踪埋点
        // var_dump(\PhalApi\DI()->tracer->getStack());
    }

    /**
     * 查找指定成员
     * @dataProvider additionProvider
     */
    public function testFind($tel) {
        echo "test find a member" . PHP_EOL;
        $rs = $this->commonRequestMember->find($tel);
        $this->assertNotFalse($rs);
        var_dump($rs);
    }

    /**
     * 查找指定绑定
     * @depends testFind
     * @dataProvider additionProvider
     */
    public function testFindBind($tel, $devid) {
        echo "test find bind" . PHP_EOL;
        $rs = $this->commonRequestDevice->find($tel, $devid);

        $this->assertNotFalse($rs);
    }

    /**
     * 删除离线人脸库
     * @depends testFind
     * @dataProvider additionProvider
     */
    public function testDeleteFaceOffline($tel, $devid) {
        echo "test delete face offline".PHP_EOL;
        $rs = $this->commonRequestMember->upgradeFace($tel, $devid, true);
        //{"devid":"215571","code":"300103","msg":"device offline"}
        //{"devid":"215571","code":"0","msg":"success"}

        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], "0");

        if ($rs["code"] != "0") {
            var_dump($rs);
        }
    }

    /**
     * 删除离线开锁权限
     * @depends testFind
     * @dataProvider additionProvider
     */
    public function testDeleteUnlock($tel, $devid) {
        echo "test delete unlock".PHP_EOL;
        $rs = $this->commonRequestDevice->upgradeUnlock($tel, $devid, true);
        //{"devid":"215571","code":"0","msg":"success"}

        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], "0");

        if ($rs["code"] != "0") {
            var_dump($rs);
        }
    }

    /**
     * 删除人脸
     * @depends testFind
     * @dataProvider additionProvider
     */
    public function testDeleteFace($tel, $devid) {
        echo "test delete face".PHP_EOL;
        $rs = $this->commonRequestMember->deleteFace($tel, $devid);
        //{"code":1,"msg":"删除成功"}
        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }

    /**
     * 删除绑定
     * @depends testFindBind
     * @dataProvider additionProvider
     */
    public function testDeleteBind($tel, $devid) {
        echo "test delete bind".PHP_EOL;
        $rs = $this->commonRequestDevice->deleteBind($tel, $devid);
        //{"code":1,"msg":"成功"}
        $this->assertArrayHasKey('code', $rs);
        $this->assertEquals($rs['code'], 1);

        if ($rs["code"] != 1) {
            var_dump($rs);
        }
    }
}
