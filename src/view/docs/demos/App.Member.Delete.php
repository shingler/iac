<?php
//注意需要有时间间隔，防止访问频繁
sleep(2);
$params = array(
    'tel' => "18612345678",
    "devid" => 255751,
    "lockid" => "01"
);

$rs = $client->reset()
    ->withService('{s}')
    ->withParams('tel', $params["tel"])
    ->withParams('devid', $params["devid"])
    ->withParams('lockid', $params["lockid"])
    ->withTimeout(3000)
    ->request();