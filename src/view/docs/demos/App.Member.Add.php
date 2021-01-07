<?php
$params = array(
    'tel' => "18612345678",
    "nickname" => "昵称",
    "cardno" => "[123456]",
    "devid" => 255751,
    "lockid" => "01",
    "start" => date("Y-m-d H:i:s", strtotime("now")),
    "end" => date("Y-m-d H:i:s", strtotime("+1day")),
    "filedata" => "对图像文件做base64加密，大小不超过1M"
);

$rs = $client->reset()
    ->withService('{s}')
    ->withParams('tel', $params["tel"])
    ->withParams('nickname', $params["nickname"])
    ->withParams('cardno', $params["cardno"])
    ->withParams('devid', $params["devid"])
    ->withParams('lockid', $params["lockid"])
    ->withParams('start', $params["start"])
    ->withParams('end', $params["end"])
    ->withParams('filedata', $params["filedata"])
    ->withTimeout(3000)
    ->request();