<?php
require __DIR__ . '/wechat.class.php';

define(TOKEN, 'aragaki');
$wechat = new weChat();
if (isset($_GET['echostr'])) {
    $wechat->valid();
}
$wechat->responseMsg();
