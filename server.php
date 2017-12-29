<?php
require __DIR__ . '/vendor/autoload.php';

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;

$config = require __DIR__ . '/config/official_account.php';

$app = Factory::officialAccount($config);

$app->server->push(function ($message) use ($app) {
    $user = $app->user->get($message['FromUserName']);
    switch ($message['MsgType']) {
        case 'event' :
            if ($message['Event'] == 'subscribe') {
                return '感谢订阅本测试号！';
            } elseif ($message['Event'] == 'unsubscribe') {
                return '欢迎下次再来！';
            } else {
                return '收到事件消息';
            }
            break;
        case 'text' :
            return '你好！';
            break;
        case 'image' :
            return '收到图片消息，这是图片的地址：'.$message['PicUrl'];
            break;
        case 'voice' :
            return '收到语音消息';
            break;
        case 'location' :
            return "收到坐标消息\n地理位置纬度：" . $message['Location_X'] . "\n地理位置经度：" . $message['Location_Y'] . "\n地图缩放大小：" . $message['Scale'] . "\n地理位置信息：" . $message['Label'];
            break;
        case 'link' :
            return "收到链接消息\n链接标题：" . $message['Title'] . "\n链接描述：" . $message['Description'] . "\n链接：" . $message['Url'];
            break;
        default :
            return '收到其他消息';
            break;
    }
});

// 加个注释
$response = $app->server->serve();

$response->send();
