<?php
$params = array(
    "devid" => 255751,
    "lockid" => "01"
);

$rs = $client->reset()
    ->withService('{s}')
    ->withParams('devid', $params["devid"])
    ->withParams('lockid', $params["lockid"])
    ->withTimeout(3000)
    ->request();