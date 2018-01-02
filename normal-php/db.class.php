<?php
class DB{
    public $config;
    public $connect; // 连接的资源
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function dbConnect()
    {
        $config = $this->config;
        $db_host = $config['db_host'];
        $root_name = $config['root_name'];
        $root_pass = $config['root_pass'];
        $db_name = $config['db_name'];
        $conn = mysqli_connect($db_host, $root_name, $root_pass);
        if (!$conn) {
            return 'Could not connect';
        }
        mysqli_query($conn , "set names utf8");
        mysqli_select_db($conn, $db_name);
        return $conn;
    }

    public function dbClose($conn)
    {
        mysqli_close($conn);
    }
}