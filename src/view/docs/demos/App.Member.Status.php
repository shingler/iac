<?php
$params = array(
    'tel' => "18612345678"
);

$rs = $client->reset()
    ->withService('{s}')
    ->withParams('tel', $params["tel"])
    ->withTimeout(3000)
    ->request();