<?php
require __DIR__ . '/../wechat.class.php';
//require  __DIR__ . '/../db.class.php';
require  __DIR__ . '/../function/function_core.php';
$config = require __DIR__ . '/../config/config.php';
$db_config = require  __DIR__ . '/../db/config.php';

$db = new DB($db_config);

$conn = $db->dbConnect();
$sql = "SELECT * FROM common_member where username = 'liheng'";

$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_assoc($result);


dd($users);

$db->dbClose($conn);




