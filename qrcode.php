<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__. '/function/function_core.php';

use EasyWeChat\Factory;

$config = require __DIR__ . '/config/official_account.php';

$app = Factory::officialAccount($config);

/* 临时二维码的创建  获取二维码详情信息
$result = $app->qrcode->temporary('foo', 6 * 24 * 3600);

dd($result);
*/

/* 打印的结果
Array
(
    [ticket] => gQG58DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTWJLcUlVQi1mYjMxcUpkZGhxYy0AAgStpEVaAwQA6QcA
    [expire_seconds] => 518400
    [url] => http://weixin.qq.com/q/02MbKqIUB-fb31qJddhqc-
)
*/

/* 永久二维码的创建
$result = $app->qrcode->forever(56);

dd($result);
*/

/*
Array
(
    [ticket] => gQFv8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycEI0MUlYQi1mYjMxMDAwMHcwMzUAAgRTpkVaAwQAAAAA
    [url] => http://weixin.qq.com/q/02pB41IXB-fb310000w035
)
*/

$ticket = 'gQFv8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAycEI0MUlYQi1mYjMxMDAwMHcwMzUAAgRTpkVaAwQAAAAA';

$url = $app->qrcode->url($ticket);

dd($url);


