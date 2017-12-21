<?php
require __DIR__.'/vendor/autoload.php';

use EasyWeChat\Factory;
$config = [
    'app_id' => 'wx9e2ecbc0419d67be',
    'secret' => '4e6bf9993cb1da3f0bd9ece1aec9da6b',

    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 将响应输出
$response->send(); // Laravel 里请使用：return $response;