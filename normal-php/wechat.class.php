<?php
class weChat
{
    public $postObj;
    public $toUser;
    public $fromUser;
    public $msgType;

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
            case 'event':
                $this->receiveEvent();
                break;
        }
    }

    // 处理文本消息
    public function receiveText()
    {
        $content = $this->postObj->Content;
        switch ($content) {
            case '点歌':
                $content = "歌单列表如下：\n";
                $musics = scandir(__DIR__. '/music');
                $musics_list = '';
                $i = 1;
                foreach ($musics as $music) {
                    if ($music != '.' && $music != '..') {
                        $musics_list .= $i++ . '. ' . basename($music, ".mp3") . "\n";
                    }
                }
                $content .= $musics_list . "输入编号，即可获取歌曲！/:8-)";
                $this->replyText($content);
                break;
            case '笑话':
                $this->replyText('你想听笑话？冷笑话听不听？');
                break;
            case '签到':
                $this->replyText('恭喜你！签到成功！');
                break;
            case '抽奖':
                $this->replyText('Oops.. 抽奖功能维护中，请稍后重试！');
                break;
            case '新闻':
                $graphics = array(
                    array(
                        'title' => '被长辈看见用 Laravel 是一种什么样的体验？',
                        'description' => 'Laravel是一套简洁、优雅的PHP Web开发框架(PHP Web Framework)。它可以让你从面条一样杂乱的代码中解脱出来；它可以帮你构建一个完美的 Web App，而且每行代码都可以简洁、富于表达力。',
                        'pic_url' => 'http://www.aragakiyui.xin/normal-php/image/1.jpg',
                        'redirect_url' => 'https://www.baidu.com',
                    ),
                    array(
                        'title' => '如何评价微信小程序推出的「跳一跳」小游戏？',
                        'description' => '1827年法国人尼塞福尔・尼埃普斯在涂有沥青的铜板上曝光了8个小时，拍摄了楼顶上的鸽子窝，这是历史上第一张成功拍摄且可以永久保存的照片。从这天开始的190年间里，对于绘画和摄影谁更艺术的争吵就一直不断。',
                        'pic_url' => 'http://www.aragakiyui.xin/normal-php/image/2.jpg',
                        'redirect_url' => 'https://www.baidu.com',
                    ),
                    array(
                        'title' => '习惯某一款FPS游戏后玩新的FPS游戏会有哪些有意思的习惯？',
                        'description' => '作为现代艺术的创始人，巴勃罗・毕加索曾说过：「最失落的两个职业是牙医和摄影师：牙医想当医生，摄影师想成为画家。」这句话表明了画家在那个时代对于摄影与摄影师的不屑与调侃。',
                        'pic_url' => 'http://www.aragakiyui.xin/normal-php/image/3.jpg',
                        'redirect_url' => 'https://www.baidu.com',
                    ),
                );

                $this->replyGraphics($graphics);
                break;
            default :
                $content = $this->postObj->Content;
                $musicData = array();
                if (preg_match('/^\d{1,2}$/', $content)) {
                    $musics = scandir(__DIR__. '/music');
                    $i = 1;
                    foreach ($musics as $music) {
                        if ($music != '.' && $music != '..') {
                            if ($content == $i) {
                                $musicData = array(
                                    'title' => $music,
                                    'description' => $music,
                                    'music_url' => 'http://www.aragakiyui.xin/normal-php/music/' . $music,
                                    'hd_music_url' => 'http://www.aragakiyui.xin/normal-php/music/' . $music,
                                );
                            }
                            $i++;
                        }
                    }
                    $this->replyMusic($musicData);
                } else {
                    $content = "本测试号提供以下功能\n回复对应关键字即可使用\n/:jump点歌\n/:kotow笑话\n/:circle签到\n/:<L>抽奖\n/:#-0新闻";
                    $this->replyText($content);
                }
                break;
        }
    }

    // 处理图片消息
    public function receiveImage()
    {
        $mediaId = $this->postObj->MediaId;
        $this->replyImage($mediaId);
    }

    // 处理时间消息
    public function receiveEvent()
    {
        $event = $this->postObj->Event;
        switch ($event) {
            case 'subscribe':
                $subscribe_graphics = array(
                    array(
                        'title' => '你好，欢迎关注本测试账号！',
                        'description' => '本测试账号关联模具论坛，你可以点击此消息，进入论坛学习！',
                        'pic_url' => 'http://www.aragakiyui.xin/normal-php/image/1.jpg',
                        'redirect_url' => 'http://www.mouldbbs.com',
                    ),
                );
                $this->replyGraphics($subscribe_graphics);
                break;
            case 'unsubscribe':
                echo '';
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