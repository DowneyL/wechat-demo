<?php

return [
//    'debug' => true,
    'app_id' => 'wx4b939851c5044ee8',
    'secret' => '5588db2e011ef577cb60a3f31f2eb6e0',
    'token' => 'aragakicyann',
    'response_type' => 'array',
    
    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/../EasyWeChat.log',
    ],
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'callback' => '/oauth_callback',
    ],
];
