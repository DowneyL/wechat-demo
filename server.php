<?php
require __DIR__.'/vendor/autoload.php';

use EasyWeChat\Factory;

$config = require __DIR__.'/config/official_account.php';
$app = Factory::officialAccount($config);

//var_dump($config);exit();
$app->server->push(function($message) use ($app) {
    $user = $app->user->get($message['FromUserName']);
    return '你好！ '.$user['nickname'];
});

$response = $app->server->serve();


$response->send();
