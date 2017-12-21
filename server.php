<?php
use EasyWeChat\Factory;

$options = [
    'app_id' => 'wx9e2ecbc0419d67be',
    'secret' => '4e6bf9993cb1da3f0bd9ece1aec9da6b',
    'token' => 'aragakicyann',
    'log' => [
        'level' => 'debug',
        'file' => '/wechat.log',
    ],
];

$app = Factory::officialAccount($options);

$server = $app->server;
/*
$user = $app->user;
$server->push(function ($message) use ($user) {
    $fromUser = $user->get($message['FromUserName']);

    return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
});
*/

$server->serve()->send();