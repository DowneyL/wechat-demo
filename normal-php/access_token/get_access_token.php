<?php
// 加载核心函数
require __DIR__ . '/../function/function_core.php';
// 加载配置文件
$config = require __DIR__ . '/../config/config.php';

// 初始化 access_token 获取的 URL
// 获取 access_token
$token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$config['appid']}&secret={$config['appsecret']}";
$access_token_arrays = responseCURL($token_url, array('type' => 'getAccessToken'));
$access_token = $access_token_arrays['access_token'];
return $access_token;