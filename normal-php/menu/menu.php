<?php
require  __DIR__ . '/../function/function_core.php';
require __DIR__ . '/../wechat.class.php';
$config = require __DIR__ . '/../config/config.php';

// 菜单处理类型
$type = '';
$error = array();

$_GET['type'] ? $type = trim($_GET['type']) : $type = 'get';

if ($type != 'create' && $type != 'get' && $type != 'delete') {
    $error = [
        'error_code' => '800',
        'error_message' => 'Wrong type!',
    ];
    dd($error);
    exit;
}

$menu_data = array('type' => $type);
if ($type == 'create') {
    $menu_data = array(
        'type' => 'post',
        'data' => '{
             "button":[
             {    
                  "type":"click",
                  "name":"今日新闻",
                  "key":"TODAY_NEWS"
              },
              {
                   "name":"娱乐一下",
                   "sub_button":[
                   {    
                       "type":"view",
                       "name":"玩玩游戏",
                       "url":"http://www.aragakiyui.xin/normal-php/games/index.html"
                    },
                    {
                       "type":"click",
                       "name":"讲个笑话",
                       "key":"JOKES"
                    }]
               },
              {
                "type":"click",
                "name":"论坛签到",
                "key":"BBS_SIGN",
             }]
            }',
    );
}

$wechat = new weChat($config);
$result = $wechat->operateMenu($type, $menu_data);

dd($result);
