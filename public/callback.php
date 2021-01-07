<?php
// 指定接口服务
$_REQUEST['s'] = 'App.Callback.Unlock';

// 处理其他参数的获取与接收

// 剩下交由框架继续处理
require_once dirname(__FILE__) . '/index.php';