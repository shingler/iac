<?php
namespace App\Api;
use PhalApi\Api;
use App\Common\MyException;

class Hello extends Api
{
    public function getRules() {
        return [
            "world" => [
                "username" => ["name" => "username", "desc" => "B站用户名"]
            ],
        ];
    }

    public function world() {
        \PhalApi\DI()->response->setDebug("x", \PhalApi\DI()->config);
        \PhalApi\DI()->tracer->mark("trace");
        return ["content" => sprintf("Hello, {%s}", $this->username)];
    }

    public function err() {
        //throw new MyException("自定义异常", 201);
        \PhalApi\DI()->response->setRet(202);
        \PhalApi\DI()->response->setMsg("自定义消息");
        return ["content" => "123"];
    }
}