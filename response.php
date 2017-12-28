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

/* 针对单个用户发送消息
$response  = $app->customer_service->message('这是一条针对单个用户的测试数据')->to('o5b7Lw_onq9noT0AHX7AOR-TwUeI')->send();
var_dump($response);
*/

/**
 * 模板消息
 */

$result = $app->template_message->send([
    'touser' => 'o5b7Lw_IdHDPTxVYPNaEvCqVgUnw',
    'template_id' => 'XVTGR9XqOBiI7BrUhTDvikTrqVSD9Z7LVcwwUacaCAA',
    'url' => 'https://www.baidu.com',
    'data' => [
        'first' => ["感谢您购买本教程！", '#FFA54F'],
        'title' => "《微信小程序的开发》",
        'price' => "￥ 188",
        'remark' => "如果在使用过程中，有任何问题，欢迎联系我们！",
    ],
]);

var_dump($result);