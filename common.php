<?php
/**
 * curl发起请求
 * @param $url
 * @param int $retry
 * @return mixed
 */
function make_request($url, $retry = 3)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    do {
        $source = curl_exec($ch);
    } while (--$retry > 0 && $source === false);

    curl_close($ch);

    return $source;
}

/**
 * 得到配置中某一项的值
 * @param $key
 * @param string $default
 * @return string
 */
function config_item($key, $default = '') {
    static $config = array();

    if (empty($config)) {
        $config = require 'setting.php';
    }

    return isset($config[$key]) ? $config[$key] : $default;
}