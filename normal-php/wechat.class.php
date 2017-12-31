<?php
class weChat
{
    public $config; // 存储配置信息
    public $data; // 存储消息的数组
    public $postObj; // 接受的消息实例
    public $toUser; // 由谁发送
    public $fromUser; // 发送给谁
    public $msgType; // 消息类型

    public function __construct($config, $data = "")
    {
        $this->config = $config;
        $this->data = $data;
    }

    // token 校验
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

    // 获取 access_token
    public function getAccessToken()
    {
        $config = $this->config;
        $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$config['appid']}&secret={$config['appsecret']}";
        $access_token = $this->responseCURL($token_url);
        return $access_token;
    }

    // http 请求模拟操作（get / post）
    public function responseCURL($url, array $extension = array('type' => 'getAccessToken'))
    {
        // 初始化 cURL
        $channel = curl_init();

        // 设置传输选项
        // 设置 url
        curl_setopt($channel, CURLOPT_URL, $url);
        // 设置返回方式为字段
        curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);

        // 设置 post 传输方式
        if ($extension['type'] != NULL) {
            if ($extension['type'] == 'post') {
                curl_setopt($channel, CURLOPT_POST, 1);
                curl_setopt($channel, CURLOPT_POSTFIELDS, $extension['data']);
            }
        } else {
            return [
                'error_code' => '801',
                'error_message' => 'Wrong param!',
            ];
        }

        // 发送 cURL， 并获取返回结果，本例中为 json 字符串
        $result = curl_exec($channel);

        $arr = json_decode($result, TRUE);
        //dd($access_token_arr);

        // 关闭 cURL 资源
        curl_close($channel);

        return is_array($arr) ? $arr : $result;
    }

    // 微信菜单操作
    public function operateMenu($type, $menu_data) {
        $access_token_arr = $this->getAccessToken();
        $access_token = $access_token_arr['access_token'];
        if (is_string($access_token)) {
            $response_menu_url = "https://api.weixin.qq.com/cgi-bin/menu/{$type}?access_token={$access_token}";
            $result = responseCURL($response_menu_url, $menu_data);
            return $result;
        } else {
            return [
                'error_code' => '801',
                'error_message' => 'Wrong param!',
            ];
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

        // 初始化消息对象，获取基本参数
        $this->postObj = $postObj;
        $this->msgType = $postObj->MsgType;
        $this->toUser = $postObj->ToUserName;
        $this->fromUser = $postObj->FromUserName;
        $this->checkMsgType();
    }

    // 检查消息类型
    public function checkMsgType()
    {
        switch ($this->msgType) {
            case 'text':
                $this->receiveText();
                break;
            case 'image':
                $this->receiveImage();
                break;
            case 'voice':
                $this->receiveVoice();
                break;
            case 'event':
                $this->receiveEvent();
                break;
        }
    }

    public function tulingRobot($content)
    {
        $config = $this->config;
        $tuling_url = "http://www.tuling123.com/openapi/api?key={$config['tuling_key']}&info={$content}";
        $result = $this->responseCURL($tuling_url);
        switch ($result['code']) {
            case '100000':
                $this->replyText($result['text']);
                break;
            case '200000':
                $this->replyText("<a href='" . $result['url'] . "'>$result[text]</a>");
                break;
        }
    }

    // 处理文本消息
    public function receiveText()
    {
        $content = $this->postObj->Content;
        $this->tulingRobot($content);
    }

    // 处理图片消息
    public function receiveImage()
    {
        $mediaId = $this->postObj->MediaId;
        $this->replyImage($mediaId);
    }

    // 处理语音消息
    public function receiveVoice()
    {
        $recognition = $this->postObj->Recognition;
        $this->tulingRobot($recognition);
    }

    // 处理事件消息
    public function receiveEvent()
    {
        $event = $this->postObj->Event;
        switch ($event) {
            case 'subscribe':
                $data = $this->data;
                if (is_array($data) && isset($data['welcome_to_subscribe'])) {
                    $this->replyGraphics($data['welcome_to_subscribe']);
                } else {
                    echo '';
                }
                break;
            case 'unsubscribe':
                echo '';
                break;
            case 'CLICK' :
                $this->replyEventOfClick();
                break;
        }
    }

    // 回复菜单点击事件
    public function replyEventOfClick()
    {
        $event_key = $this->postObj->EventKey;
        switch ($event_key) {
            case 'TODAY_NEWS':
                $data = $this->data;
                if (is_array($data) && isset($data['today_news'])) {
                    $this->replyGraphics($data['today_news']);
                } else {
                    echo '';
                }
                break;
            case 'JOKES':
                echo '';
                break;
            case 'GOOD_BTN':
                $this->replyText('谢谢您的点赞！我们会继续加油！/:,@f/:,@f/:,@f');
                break;
        }
    }

    // 回复文本消息
    public function replyText($content)
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

    // 回复图片消息
    public function replyImage($mediaId)
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
        echo sprintf($xml, $this->fromUser, $this->toUser, time(), $mediaId);
    }

    // 回复音乐消息
    public function replyMusic($musicdata)
    {
        $xml = "<xml>
               <ToUserName><![CDATA[%s]]></ToUserName>
               <FromUserName><![CDATA[%s]]></FromUserName>
               <CreateTime>%d</CreateTime>
               <MsgType><![CDATA[music]]></MsgType>
               <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
               </Music>
               </xml>";
        echo sprintf($xml, $this->fromUser, $this->toUser, time(), $musicdata['title'], $musicdata['description'], $musicdata['music_url'], $musicdata['hd_music_url']);
    }

    // 回复图文消息
    public function replyGraphics($graphics)
    {
        $graphics_xml_str = '';
        foreach ($graphics as $graphic) {
            $graphics_xml_str .= "<item>
                                    <Title><![CDATA[$graphic[title]]]></Title>
                                    <Description><![CDATA[$graphic[description]]]></Description>
                                    <PicUrl><![CDATA[$graphic[pic_url]]]></PicUrl>
                                    <Url><![CDATA[$graphic[redirect_url]]]></Url>
                                   </item>";
        }
        $xml = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%d</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>" . count($graphics) . "</ArticleCount>
                    <Articles>" . $graphics_xml_str . "</Articles>
                </xml>";

        echo sprintf($xml, $this->fromUser, $this->toUser, time());
    }
}