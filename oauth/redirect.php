<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../function/function_core.php';
use EasyWeChat\Factory;

$config = require __DIR__ . '/../config/official_account.php';

$app = Factory::officialAccount($config);

$response = $app->oauth->redirect('http://www.aragakiyui.xin/oauth/callback.php');

$response->send();