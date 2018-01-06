<?php
require __DIR__ . '/../wechat.class.php';
require __DIR__ . '/../function/function_core.php';
$config = require  __DIR__ . '/../config/config.php';
$wechat = new WeChat($config);

if (!isset($_GET['code'])) {
    $redirect_uri = urlencode("http://www.aragakiyui.xin/normal-php/auth/index.php");
    $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$config['appid']}&redirect_uri={$redirect_uri}&response_type=code&scope={$config['scope']}#wechat_redirect";
    header('location:' . $url);
}
$code = $_GET['code'];
$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$config['appid']}&secret={$config['appsecret']}&code={$code}&grant_type=authorization_code";
$result = $wechat->responseCURL($url);
$access_token = $result['access_token'];
$openid = $result['openid'];

switch ($config['scope']) {
    case 'snsapi_base':
        dd($result);
        dd($openid);
        break;
    case 'snsapi_userinfo':
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $userinfo = $wechat->responseCURL($userinfo_url);
        dd($userinfo);
        break;
}
