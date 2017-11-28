<?php
include_once "_check.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>用户反馈</title>
    <link rel="stylesheet" href="../../dist/css/layui.css">
<!--    <link rel="stylesheet" href="../../assets/css/menu.css">-->
<!--    <link href="favicon.ico" type="image/vnd.microsoft.icon" rel="shortcut icon"/>-->
</head>

<body data-uid="<?php echo $user_id; ?>" data-uname="<?php echo $user_name; ?>">
<!--<script src="../../assets/layui/layui.js"></script>-->
<script src="../../dist/layui.js"></script>

<script>


    layui.use(['layim', 'jquery', 'layer'], function (layim) {
        var layim = layui.layim;
        var $ = layui.jquery;
        var uid = $("body").data("uid");
        var uname = $("body").data("uname");
        //建立WebSocket通讯
        //注意：如果你要兼容ie8+，建议你采用 socket.io 的版本。下面是以原生WS为例
        var socket = new WebSocket('ws://127.0.0.1:8023');


        layim.config({
            init: {
//                //配置客户信息
                mine: {
                    "username": uname //我的昵称
                    , "id": parseInt(uid) //我的ID
                    , "status": "online" //在线状态 online：在线、hide：隐身
                    , "remark": "默默的搬运代码" //我的签名
                    , "avatar": "http://markdown-1252847423.file.myqcloud.com/avatar.png" //我的头像
                }
            }
            //开启客服模式
            , brief: true
            , notice: true
            , initSkin: '2.jpg'
            , uploadImage: {
                url: ''
            }
            , uploadFile: {
                url: ''
            }
            //以下为我们内置的模版，也可以换成你的任意页面。若不开启，剔除该项即可
            , chatLog: layui.cache.dir + 'css/modules/layim/html/chatlog.html'

        });




        //连接成功时触发
        socket.onopen = function () {
            socket.send('{"msg":"XXX连接成功"}');


            //打开一个客服面板
            layim.chat({
                name: '苏苏苏' //名称
                , type: 'kefu' //聊天类型
                , avatar: 'https://pm.sky31.com/zd.png' //头像
                , id: 2016551736 //定义唯一的id方便你处理信息
            });

//            layim.setChatMin(); //收缩聊天面板


            layim.on('sendMessage', function (data) { //监听发送消息
                console.log("sendMessage:\n");
                console.log(data);
                socket.send(JSON.stringify({
                    type: "kefu",
                    data: data
                }));
            });

        };

        //监听收到的消息
        socket.onmessage = function (res) {
            console.log("RecivedMessage:\n");
            console.log(res);
            res = JSON.parse(res.data);
            switch (res.emit) {
                case "chatMessage":
                    layim.getMessage(res.data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                    break;
            }
            //res为接受到的值，如 {"emit": "messageName", "data": {}}
            //emit即为发出的事件名，用于区分不同的消息

        };

        //另外还有onclose、onerror，分别是在链接关闭和出错时触发。

        socket.onclose = function (res) {
            console.log(res);
            layer.confirm('由于长时间未进行操作，已自动退出与当前客服的联系。', {
                btn: ['重新连接', '退出'] //可以无限个按钮

            }, function (index, layero) {
                window.location.href = './index.php'
            }, function (index) {
                layer.close(index);
                window.location.href = './login.php'
            });

        }


        socket.onerror = function (error) {
            console.log(error);
        }


    });


</script>
</body>

</html>
