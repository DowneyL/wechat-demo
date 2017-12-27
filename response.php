<?php
require __DIR__ . '/vendor/autoload.php';
use EasyWeChat\Factory;

$config = require __DIR__ . '/config/official_account.php';

$app = Factory::officialAccount($config);

$user = $app->user->list();
var_dump($user['data']);

/* 群发消息
$broadcast = $app->broadcasting;

$response = $broadcast->sendText("这是一条测试数据", $user['data']['openid']);

var_dump($response);
*/

$response  = $app->customer_service->message('这是一条针对单个用户的测试数据')->to('o5b7Lw_onq9noT0AHX7AOR-TwUeI')->send();
var_dump($response);