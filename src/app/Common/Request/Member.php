<?php
namespace App\Common;

class Member
{
    private $token;
    protected static $_TYPE_ID = [
        "add" => 100,
        "update" => 203,
        "status" => 201,
        "delete" => 104
    ];

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * 新增成员
     * @param string $nickname
     * @param string $tel
     * @param string $cardno
     * @return bool
     */
    public function add($nickname, $tel, $cardno) {
        return true;
    }
}