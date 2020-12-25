<?php
namespace App;

function hello() {
    return 'Hey, man~';
}

/**
 * curl访问智能门禁的接口
 * @param string $apiurl
 * @param array $data
 * @return bool|string
 */
function curl_post($apiurl, $data) {
    header("Content-type: text/html; charset=utf-8");
    $acsurl = \PhalApi\DI()->config->get("elock.acsurl");
    $ch = curl_init();
    $data = http_build_query($data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $apiurl);
    curl_setopt($ch,CURLOPT_REFERER,$acsurl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    // 日志记录
    $logger = \PhalApi\DI()->logger;
    $logger->info("apiurl", $apiurl);
    $logger->info("acsurl", $acsurl);
    $logger->info("data", json_encode($data, JSON_UNESCAPED_UNICODE));
    $data = curl_exec($ch);
    $status_code = curl_errno($ch);
    $logger->info("result", $status_code.":".$data);
    if ($status_code != 200) {
        throw new Exception(curl_error($ch), $status_code);
    }
    return $data;
}
