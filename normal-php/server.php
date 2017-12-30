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
    case 'text':
        $content = $postObj->Content;
        switch ($content) {
            case 'liheng' :
                echo sprintf(sendTextMessage(), $fromUser, $toUser, time(), '啦啦啦啦');
                break;
            default :
                echo sprintf(sendTextMessage(), $fromUser, $toUser, time(), $content);
                break;
        }
        break;
    case 'image' :
        $media_id = $postObj->MediaId;
        $pic_url = $postObj->PicUrl;
        $content = '这个图片的地址是：' . $pic_url;
        echo sprintf(sendTextMessage(), $fromUser, $toUser, time(), $content);
//        echo sprintf(sendImageMessage(), $fromUser, $toUser, time(), $media_id);
        break;
}

/*
if ($msgType == 'text') {
    $content = $postObj->Content;
    switch ($content) {
        case 'liheng' :
            echo sprintf(sendTextMessage(), $fromUser, $toUser, time(), '啦啦啦啦');
            break;
        default :
            echo sprintf(sendTextMessage(), $fromUser, $toUser, time(), $content);
            break;
    }
}
*/

/* 函数穿参形式
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
*/

function sendTextMessage()
{
    $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <MsgType><![CDATA[text]]></MsgType>
            <CreateTime>%d</CreateTime>
            <Content><![CDATA[%s]]></Content>
            </xml>";
    return $xml;
}

function sendImageMessage()
{
    $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%d</CreateTime>
            <MsgType><![CDATA[image]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
    return $xml;
}

