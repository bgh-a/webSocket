<?php
// 引入workerman 的功能，引入入口文件
require_once "workerman/Autoloader.php";

// 创建websocket服务器监听本机的3000 端口
$wsServer = new \Workerman\Worker('websocket://127.0.0.1:3000');

$userIds = [];
$wsServer->onConnect = function ($conn) {
    $conn->onWebSocketConnect = function ($conn, $http_header) {
        $conn->to_id = $_GET['toid'];
        // $connection->room_id = $_GET['room_id'];

        global $userIds;
        $userIds[$_GET['toid']] = $conn;
        if ($_GET['toid']) {

        }
    };

    // 心跳是让服务器与客服端处于长连接的状态
    \Workerman\Lib\TImer::add(1,function ()use ($conn) {
        $conn->send(json_encode(array("type" => "ping")));
    });

};

$wsServer->onMessage = function ($conn,$data) {
    // 数组 $conn->worker->connections 保存的是所有连接的人员
    // foreach ($conn->worker->connections as $v) {
    //     if ($v != $conn) {
    //         $v->send(json_encode(array("type" => "msg", "content" => $data)));
    //     }
    // }
    $userIds[$conn->user_id] = $userIds[$conn->user_id];
    return $conn->send($data);
   
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
