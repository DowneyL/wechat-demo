<?php
$openid = trim($_GET['openid']);
?>

<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="http://www.aragakiyui.xin/normal-php/test/validate.php" method="post">
    <input type="hidden" name="openid" value="<?php echo $openid ?>">
    <p>用户名：</p>
    <p>
        <input type="text" name="username">
    </p>
    <p>登陆密码：</p>
    <p>
        <input type="password" name="password">
    </p>
    <p>
        <input type="submit" value="验证">
    </p>
</form>
</body>
</html>
