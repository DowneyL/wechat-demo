<?php
require __DIR__ . '/../db.class.php';
$db_config = require  __DIR__ . '/../db/config.php';
$username = htmlspecialchars(trim($_POST['username']));
$password = htmlspecialchars(trim($_POST['password']));
$openid = $_POST['openid'];

$db = new DB($db_config);
$conn = $db->dbConnect();
$sql = "select * from common_member where username = '{$username}'";
$result = mysqli_query($conn, $sql);
$userinfo = mysqli_fetch_assoc($result);

if ($userinfo) {
    if ($userinfo['password'] == $password) {
        $sql = "update common_member set openid = '{$openid}' where username = '{$username}'";
        $result = mysqli_query($conn, $sql);
        echo $result ? '关联成功！' : '关联失败，稍后重试';
    } else {
        echo "密码错误!";
    }
} else {
    echo "验证失败！";
}
