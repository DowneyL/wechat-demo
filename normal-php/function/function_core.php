<?php
function dd($params)
{
    if (is_array($params)) {
        echo "<pre>";
        print_r($params);
        echo "</pre>";
    } else {
        echo $params . "<br />";
    }
}

function responseCURL($url, array $extension)
{
    // 初始化 cURL
    $channel = curl_init();

    // 设置传输选项
    // 设置 url
    curl_setopt($channel, CURLOPT_URL, $url);
    // 设置返回方式为字段
    curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);

    // 设置 post 传输方式
    if ($extension) {
        if ($extension['type'] == 'post') {
            curl_setopt($channel, CURLOPT_POST, 1);
            curl_setopt($channel, CURLOPT_POSTFIELDS, $extension['data']);
        }
    }

    // 发送 cURL， 并获取返回结果，本例中为 json 字符串
    $json = curl_exec($channel);

    // 获取 access_token 相关的 php 数组
    $arr = json_decode($json, TRUE);
    //dd($access_token_arr);

    // 关闭 cURL 资源
    curl_close($channel);
    return $arr;
}