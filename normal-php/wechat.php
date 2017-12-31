<?php
require  __DIR__ . '/../function/function_core.php';
require __DIR__ . '/wechat.class.php';
$config = require __DIR__ . '/config/config.php';
$data = require  __DIR__ . '/data.php';

define(TOKEN, 'aragaki');
$wechat = new weChat($config, $data);
if (isset($_GET['echostr'])) {
    $wechat->valid();
}
$wechat->responseMsg();
