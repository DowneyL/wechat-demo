<?php
// 初始化 cURL
$channel = curl_init();

// 设置传输选项
curl_setopt($channel, CURLOPT_URL, 'https://www.baidu.com');

curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);

// 发送 cURL
$result = curl_exec($channel);

echo $result;

// 关闭 cURL
curl_close($channel);
