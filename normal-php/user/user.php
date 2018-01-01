<?php
require __DIR__ . '/../function/function_core.php';
require  __DIR__ . '/../wechat.class.php';
$config = require __DIR__ . '/../config/config.php';
$wechat_users = new weChat($config);
// 取所有关注用户的信息
$users = $wechat_users->getAllUsers();

// 取某个用户的详细信息
$user_info = $wechat_users->getUserInfo('o5b7Lw_onq9noT0AHX7AOR-TwUeI');

// 同时取多个用户的详细信息
$userlist = ['o5b7Lw_onq9noT0AHX7AOR-TwUeI', 'o5b7Lw89yiNRudh3KLzFWzyZmV_s'];
$arr_info = $wechat_users->getUserInfo($userlist);

dd($users);
dd($user_info);
dd($arr_info);