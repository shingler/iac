<?php
//防止接口提示发送频繁
sleep(2);
$params = array(
    'tel' => "18612345678",
    "devid" => 255751,
    "lockid" => "01",
    "start" => date("Y-m-d H:i:s", strtotime("now")),
    "end" => date("Y-m-d H:i:s", strtotime("+1day"))
);

$rs = $client->reset()
    ->withService('{s}')
    ->withParams('tel', $params["tel"])
    ->withParams('devid', $params["devid"])
    ->withParams('lockid', $params["lockid"])
    ->withParams('start', $params["start"])
    ->withParams('end', $params["end"])
    ->withTimeout(3000)
    ->request();