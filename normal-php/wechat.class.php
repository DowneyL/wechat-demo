<?php
require __DIR__ .'/db.class.php';

class weChat
{
    public $config; // 存储配置信息
    public $access_token; // 获取 access_token
    public $data; // 存储消息的数组
    public $postObj; // 接受的消息实例
    public $toUser; // 由谁发送
    public $fromUser; // 发送给谁
    public $msgType; // 消息类型

    public function __construct($config, $data = "")
    {
        $this->config = $config;
        $this->data = $data;
        $this->access_token = $this->getAccessToken();
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
        $access_token_arr = $this->responseCURL($token_url);
        $access_token = $access_token_arr['access_token'];
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

    // 微信菜单操作
    public function operateMenu($type, $menu_data) {
//        $access_token = $access_token_arr['access_token'];
        if (is_string($this->access_token)) {
            $response_menu_url = "https://api.weixin.qq.com/cgi-bin/menu/{$type}?access_token={$this->access_token}";
            $result = responseCURL($response_menu_url, $menu_data);
            return $result;
        } else {
            return [
                'error_code' => '801',
                'error_message' => 'Wrong param!',
            ];
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
                $this->replyJuHeJokes();
                break;
            case 'GOOD_BTN':
                $this->replyText('谢谢您的点赞！我们会继续加油！/:,@f/:,@f/:,@f');
                break;
            case 'BBS_SIGN':
                $db = new DB($this->config);
                $conn = $db->dbConnect();
                $sql = "select * from common_member where openid = '{$this->fromUser}'";
                $result = mysqli_query($conn, $sql);
                $userinfo = mysqli_fetch_assoc($result);
                if (!$userinfo) {
                    $this->replyText("抱歉，您还没有关联论坛账号！\n<a href='http://www.aragakiyui.xin/normal-php/test/bind.php?openid=". $this->fromUser ."'>点此链接关联账号》</a>");
                } else {
//                    $userinfo[]
                    $this->replyText("尊敬的 $userinfo[username] , 恭喜您签到成功！");
                }
                $db->dbClose($conn);
                break;
        }
    }

    // 聚合数据笑话大全接口
    public function replyJuHeJokes()
    {
        $config = $this->config;
        if (is_array($config) && isset($config['juhe_joke_appkey'])) {
            $juhe_joke_appkey = $config['juhe_joke_appkey'];
            $juhe_joke_url = "http://japi.juhe.cn/joke/content/list.from?sort=asc&page=" . rand(1, 10) . "&pagesize=1&time=" . (intval(time()) - 3600 * 24) . "&key={$juhe_joke_appkey}";
            $result = $this->responseCURL($juhe_joke_url);
            if (is_array($result) && isset($result['result']['data']['0']['content'])) {
                $this->replyText($result['result']['data']['0']['content']);
            } else {
                $this->replyText('Oops.. 笑话编辑失败，再试一次吧！');
            }
        } else {
            echo "";
        }
    }

    // 图灵机器人的自动回复
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

    // 用户信息处理
    public function getAllUsers($next_openid = "")
    {
        if (is_string($this->access_token)) {
            $response_user_url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$this->access_token}&next_openid={$next_openid}";
            $result = $this->responseCURL($response_user_url);
            return $result;
        } else {
            return [
                'error_code' => '801',
                'error_message' => 'Wrong param!',
            ];
        }
    }

    public function getUserInfo($open_id)
    {
        $data = array(
            'type' => 'get',
        );
        if (is_array($open_id)) {
            $open_id = array_values($open_id);
            $user_list_arr = array();
            foreach ($open_id as $key => $value) {
                $user_list_arr['user_list'][$key]["openid"] = $value;
            }
            $user_list_json = json_encode($user_list_arr);

            $data = array(
                'type' => 'post',
                'data' => $user_list_json,
            );
            $response_userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$this->access_token}";
        } else {
            $response_userinfo_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$this->access_token}&openid={$open_id}&lang=zh_CN";
        }
        $result = $this->responseCURL($response_userinfo_url, $data);
        return $result;
    }

    public function sendTemplate($openid, $templateid, $data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->getAccessToken();
        $params = array(
            'touser' => "$openid",
            'template_id' => "$templateid",
            'url' => 'https://www.baidu.com',
            'data' => $data,
        );
        $json = json_encode($params);
//        return $json;
        $result = $this->responseCURL($url, array('type'=>'post', 'data'=>$json));
        return $result;
    }

    public function setIndustry($industryids)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=" . $this->getAccessToken();
        $json = json_encode($industryids);
        $result = $this->responseCURL($url, array('type'=>'post', 'data'=>$json));
        return $result;
    }

    public function getIndustry()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=" . $this->getAccessToken();
        $result = $this->responseCURL($url);
        return $result;
    }

    public function getTemplateId($template_id_short)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=" . $this->getAccessToken();
        $json = json_encode($template_id_short);
        $result = $this->responseCURL($url, array('type' => 'post', 'data' => $json));
        return $result;
    }

    public function getTemplateList()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=" . $this->getAccessToken();
        $result = $this->responseCURL($url);
        return $result;
    }

    public function deleteTemplateById($templateid)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=" . $this->getAccessToken();
        $json = json_encode($templateid);
        $result = $this->responseCURL($url, array('type' => 'post', 'data' => $json));
        return $result;
    }
}
