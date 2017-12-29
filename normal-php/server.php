<?php
// 定义 token
define(TOKEN, 'aragakicyann');

// 获取微信加密签名
$signature = $_GET['signature'];
// 获取事件戳
$timestamp = $_GET['timestamp'];
// 获取随机数
$nonce = $_GET['nonce'];
// 获取随机字符串
$echostr = $_GET['echostr'];

// 验证 TOKEN、时间戳以及生成的随机数
$validateArray = array(TOKEN, $timestamp, $nonce);

// 对数组数据进行字典排序
sort($validateArray, SORT_STRING);

// 使用 sha1 加密数据结合字符串
$validateStr = sha1(implode($validateArray));

// 验证微信加密签名
if ($validateStr == $signature) {
    echo $echostr;
} else {
    echo 'error';
    exit;
}

$postXmlStr = file_get_contents("php://input");
//$postXmlStr = $GLOBALS['HTTP_RAW_POST_DATA'];
if (!$postXmlStr) {
    echo '';
    exit;
}
$postObj = simplexml_load_string($postXmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
$toUser = $postObj->ToUserName;
$fromUser = $postObj->FromUserName;
$msgType = $postObj->MsgType;

switch ($msgType) {
    case 'text' :
        echo sendTextMessage($toUser, $fromUser, '哈哈哈，你好呀！');
        break;
    default :
        echo sendTextMessage($toUser, $fromUser, '嘿嘿');
}

function sendTextMessage($to, $from, $message)
{
    $xml = "<xml>
            <ToUserName><![CDATA[" . $from . "]]></ToUserName>
            <FromUserName><![CDATA[" . $to . "]]></FromUserName>
            <MsgType><![CDATA[text]]></MsgType>
            <CreateTime>".time()."</CreateTime>
            <Content><![CDATA[" . $message . "]]></Content>
        </xml>";
    return $xml;
}


