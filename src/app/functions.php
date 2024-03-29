<?php
namespace App;

function hello() {
    return 'Hey, man~';
}

/**
 * curl访问智能门禁的接口
 * @param string $apiurl
 * @param array $data
 * @param string $format
 * @throws \Exception
 * @return bool|string
 */
function curl_post($apiurl, $data, $format="json") {
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
    $logger = \PhalApi\DI()->api_logger;
    $logger->info("data", $data);
    $result = curl_exec($ch);
    if ($status_code = curl_errno($ch)) {
        $exception = curl_error($ch);
        $logger->error($status_code, $exception);
        throw new \Exception($exception, $status_code);
    }
    $logger->info("result", $result);
    if ($format == "json") {
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
    }
    return $result;
}
