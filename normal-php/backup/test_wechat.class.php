<?php
class weChat
{
    public $toUser;
    public $fromUser;

    public function valid()
    {
        if ($this->checkSignature()) {
            echo "$_GET[echostr]";
        } else {
            echo "Connect failed.";
            exit;
        }
    }

    public function checkSignature()
    {
        // 获取微信加密签名
        $signature = $_GET['signature'];
        // 获取事件戳
        $timestamp = $_GET['timestamp'];
        // 获取随机数
        $nonce = $_GET['nonce'];

        // 验证 TOKEN、时间戳以及生成的随机数
        $validateArray = array(TOKEN, $timestamp, $nonce);

        // 对数组数据进行字典排序
        sort($validateArray, SORT_STRING);

        // 使用 sha1 加密数据结合字符串
        $validateStr = sha1(implode($validateArray));

        // 验证微信加密签名
        /*
        return ($validateStr == $signature);
        */
        if ($validateStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    // 响应用户请求消息
    public function responseMsg()
    {
        // 接收传过来的 XML 字符串数据
        $postStrXML = file_get_contents('php://input');
        if (!$postStrXML) {
            echo '';
            exit;
        }
        // 将字符串数据转化为对象
        $postObj = simplexml_load_string($postStrXML, 'SimpleXMLElement', LIBXML_NOCDATA);
        // 接收消息类型
        $msgType = $postObj->MsgType;
        $this->toUser = $postObj->ToUserName;
        $this->fromUser = $postObj->FromUserName;
        $this->checkMsgType($postObj, $msgType);
    }

    // 检查消息类型
    public function checkMsgType($postObj, $msgType)
    {
        switch ($msgType) {
            case 'text':
                $this->receiveText($postObj);
                break;
        }
    }

    // 处理文本消息
    public function receiveText($postObj)
    {
        $content = $postObj->Content;
        switch ($content) {
            case '点歌':
                $this->replyText($postObj, '你在点歌');
                break;
            case '笑话':
                $this->replyText($postObj, '你想听笑话');
                break;
            case '签到':
                $this->replyText($postObj, '签到成功！');
                break;
            case '抽奖':
                $this->replyText($postObj, '抽奖功能维护中，请稍后重试！');
                break;
            default:
                $content = "本测试号提供以下功能\n回复对应关键字即可使用\n/:jump点歌\n/:kotow笑话\n/:circle签到\n/:<L>抽奖";
                $this->replyText($postObj, $content);
                break;
        }
    }

    // 回复文本消息
    public function replyText($postObj, $content)
    {
        $xml = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <MsgType><![CDATA[text]]></MsgType>
            <CreateTime>%d</CreateTime>
            <Content><![CDATA[%s]]></Content>
            </xml>";
        echo sprintf($xml, $this->fromUser, $this->toUser, time(), $content);
    }
}