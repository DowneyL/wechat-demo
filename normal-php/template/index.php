<?php
require __DIR__ . '/../wechat.class.php';
require __DIR__ . '/../function/function_core.php';
$config = require  __DIR__ . '/../config/config.php';

$wechat = new WeChat($config);

/* 直接对指定用户发送模板
*/

/*
$data = array(
    'result' => array(
        'value' => '恭喜中奖啦',
    ),
    'totalWinMoney' => array(
        'value' => " 中奖 10000000 元",
        'color' => '#f00',
    ),
    'issueInfo' => array(
        'value' => " 双色球2013023期",
    ),
    'fee' => array(
        'value' => " 投注金额 20 元",
    ),
    'betTime' => array(
        'value' => " " . date('Y-m-d H:i:s'),
    ),
    'remark' => array(
        'value' => "开奖号码: 1234567890123\n投注号码: 2233445566AWEC\n\n奖金将于 01:00 前到账，请稍后领取",
    ),
);

$result = $wechat->sendTemplate('o5b7Lw_onq9noT0AHX7AOR-TwUeI', 'gLHbPk5Q2aOhPjeC5A0XETX9dXdpvYOqla1kzJNfnxw', $data);
dd($result);
*/

/* 设置所属行业为
IT科技	互联网/电子商务
IT科技	IT软件与服务

$industryids = array(
    'industry_id1' => "2",
    "industry_id2" => "3",
);

$result = $wechat->setIndustry($industryids);
dd($result);

*/

/* 获取设置的所属行业
Array
(
    [primary_industry] => Array
        (
            [first_class] => IT科技
            [second_class] => 互联网|电子商务
        )

    [secondary_industry] => Array
        (
            [first_class] => IT科技
            [second_class] => IT软件与服务
        )

)
$result = $wechat->getIndustry();
dd($result);
*/

/* 获取对应设置行业的 template_id 也就是模板 id
Array
(
    [errcode] => 0
    [errmsg] => ok
    [template_id] => HCPM-HSJpCnTuMI7aXOxWbiAj5-3GoMCl6XzrC9UCH8
    [template_id] => qXo6gXH4QlckkOcJGE0qO84y4ZA4UElP6fGsqLjy2ls
)
*/

/*
$template_id_short = array(
    "template_id_short" => 'TM00012',
);
$result = $wechat->getTemplateId($template_id_short);
dd($result);
*/

/* 获取列表
$result = $wechat->getTemplateList();
dd($result);
*/

/*删除模板
$result = $wechat->deleteTemplateById(['template_id' => 'gLHbPk5Q2aOhPjeC5A0XETX9dXdpvYOqla1kzJNfnxw']);
dd($result);
*/
