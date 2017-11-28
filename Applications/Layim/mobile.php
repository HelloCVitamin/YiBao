<?php
include_once "_check.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>用户反馈</title>

    <link rel="stylesheet" href="../../dist/css/layui.mobile.css">

</head>
<body data-uid="<?php echo $user_id; ?>" data-uname="<?php echo $user_name; ?>">


<script src="../../dist/layui.js"></script>
<script>

    layui.use(['mobile'], function (jquery) {
        var mobile = layui.mobile
            , layim = mobile.layim;

        var uid = "<?php echo $user_id; ?>";
        var uname = "<?php echo $user_name; ?>";

        //建立WebSocket通讯
        //注意：如果你要兼容ie8+，建议你采用 socket.io 的版本。下面是以原生WS为例
        var socket = new WebSocket('ws://127.0.0.1:8023');

        //基础配置
        layim.config({
            init: {
                //设置我的基础信息
                mine: {
                    "username": "佟丽娅" //我的昵称
                    , "id": 123 //我的ID
                    , "avatar": "http://tp4.sinaimg.cn/1345566427/180/5730976522/0" //我的头像
                }
                //好友列表数据
                , friend: [] //见下文：init数据格式


            }
            , uploadImage: {
                url: '' //接口地址
                , type: 'post' //默认post
            }
        });
        //创建一个会话
        layim.chat({
            id: 111111
            , name: '许闲心'
            , type: 'kefu' //friend、group等字符，如果是group，则创建的是群聊
            , avatar: 'http://tp1.sinaimg.cn/1571889140/180/40030060651/1'
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
