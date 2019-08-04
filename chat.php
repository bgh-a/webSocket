<?php
// 引入workerman 的功能，引入入口文件
require_once "workerman/Autoloader.php";

// 创建websocket服务器监听本机的3000 端口
$wsServer = new \Workerman\Worker('websocket://127.0.0.1:3000');

$userIds = [];
// 保存所有的用户
$users = [];

$toUser = [];
$userConn = [];

$wsServer->onConnect = function ($conn) {
    $conn->onWebSocketConnect = function ($conn, $http_header) {
        // 保存当前用户信息
        global $users, $worker, $userConn;

     
        if ($_GET['toid']) {
            echo "哈哈";
        }

         $users[$_GET['userid']] = $_GET['userid'];
         $userConn[$_GET['userid']] = $conn;

    };
    // var_dump($toUser);

    // 心跳是让服务器与客服端处于长连接的状态
    \Workerman\Lib\TImer::add(1,function ()use ($conn) {
        $conn->send(json_encode(array("type" => "ping")));
    });

};

$wsServer->onMessage = function ($conn,$data) {
   
    global $users, $worker, $userConn;

    $ret = explode(":",$data);
    $toUser = $ret[0];
    $rawData = implode(':', $ret);
    $userConn[$toUser]->send($rawData);
   
   
};

// 这个是出问题的时候
$wsServer->onError = function () {
    echo "出错了";
};

// 关闭连接的时候
$wsServer->onClose = function () {
    echo "您关闭了窗口";
};

// 让服务器开始运行的
$wsServer->runAll();
