<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="../../dist/css/layui.css" media="all">
    <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>
<!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
<!--[if lt IE 9]>
<script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
<script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-col-xs12 layui-col-md12">
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 150px; text-align: center">
                <legend>用户反馈</legend>
            </fieldset>
            <form class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <label class="layui-form-label">学号</label>
                    <div class="layui-input-block">
                        <input type="text" name="uid" lay-verify="uid|required" placeholder="请输入学号" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="uname" lay-verify="required" placeholder="请输入姓名" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>

                <div class="layui-form-item" style="text-align: center">
                    <button class="layui-btn layui-btn-normal layui-btn-radius" lay-submit="" lay-filter="login">登录
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="../../dist/layui.js" charset="utf-8"></script>

<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>

    layui.use(['form', 'jquery', 'layer'], function (form) {

        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            uid: function (value) {
                if (value.trim().length < 10) {
                    return '请输入正确学号';
                }
            }
        });

        //监听指定开关
        form.on('switch(switchTest)', function (data) {
            layer.msg('开关checked：' + (this.checked ? 'true' : 'false'), {
                offset: '6px'
            });
            layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)
        });

        //监听提交
        form.on('submit(login)', function (data) {
            $.ajax({
                type: "POST",
                url: "check.php",
                data: {
                    uid:data.field.uid,
                    uname:data.field.uname
                },
                dataType: "json",
                beforeSend: function () {
                    layer.load(0, {shade: 0.1});
                },
                success: function (res) {
                    layer.closeAll('loading');
                    if (res.code == 0) {
                        layer.msg(res.info, {icon: 1});
                        setTimeout(window.location.href = 'index.php', 3000);
                    } else {
                        layer.alert(res.info, {icon: 2});
                    }
                }
            });
            return true;
        });


    });
</script>


</body>
</html>

