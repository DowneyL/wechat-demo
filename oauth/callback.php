<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../function/function_core.php';

use EasyWeChat\Factory;

$config = require __DIR__ . '/../config/official_account.php';

$app = Factory::officialAccount($config);

$user = $app->oauth->user();

//echo json_encode($user->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
//echo json_encode($app->oauth->user()->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
session_start();
$_SESSION['wechat_user'] = $user->toArray();

dd($user->getId());
dd($user->getName());
dd($user->getAvatar());

