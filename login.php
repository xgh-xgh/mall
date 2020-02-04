<?php
include_once './lib/fun.php';   //在脚本执行期间包含并运行指定文件
//开启session
if (checkLogin()) {
    msg(1, '您已登录', 'index.php');
}

//表单进行提交处理
if (!empty($_POST['username'])) { //empty() 函数用于检查一个变量是否为空

    include_once './lib/fun.php';   //在脚本执行期间包含并运行指定文件

    $username = trim($_POST['username']);// 也可以用 mysqli_real_escape_string()进行过滤
    $password = trim($_POST['password']);//trim() 函数移除字符串两侧的空白字符或其他预定义字符

    //判断用户名不能为空
    if (!$username) {
        msg(2, '用户不能为空');
    }
    if (!$password) {
        msg(2, '密码不能为空');
    }

    $con = mysqlInit('localhost', 'root', 'phpcj', 'imooc_mall');
    if (!$con) {
        echo mysqli_errno($con);         //mysql错误信息函数
        exit;
    }
    //根据用户名查询用户
    $sql = "SELECT * FROM im_user WHERE username = '{$username}' LIMIT 1";
    $obj = mysqli_query($con, $sql);
    $result = mysqli_fetch_assoc($obj);

    if (is_array($result) && !empty($result)) {
        if (createPassword($password) === $result['password']) {
            $_SESSION['user'] = $result;
            header('Location:index.php');
            //echo '登录成功';
            exit;
        } else {
            msg(2, '密码不正确请重新输入');
        }
    } else {
        msg(2, '用户名不存在请重新输入');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|用户登录</title>
    <link type="text/css" rel="stylesheet" href="./static/css/common.css">
    <link type="text/css" rel="stylesheet" href="./static/css/add.css">
    <link rel="stylesheet" type="text/css" href="./static/css/login.css">
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>

            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="center">
        <div class="center-login">
            <div class="login-banner">
                <a href="#"><img src="./static/image/login_banner.png" alt=""></a>
            </div>
            <div class="user-login">
                <div class="user-box">
                    <div class="user-title">
                        <p>用户登录</p>
                    </div>
                    <form class="login-table" name="login" id="login-form" action="login.php" method="post">
                        <div class="login-left">
                            <label class="username">用户名</label>
                            <input type="text" class="yhmiput" name="username" placeholder="Username" id="username"/>
                        </div>
                        <div class="login-right">
                            <label class="passwd">密码</label>
                            <input type="password" class="yhmiput" name="password" placeholder="Password" id="password">
                        </div>
                        <div class="login-btn">
                            <button type="submit">登录</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <p><span>M-GALLARY</span> ©2017 POWERED BY IMOOC.INC</p>
</div>

</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script src="./static/js/layer/layer.js"></script>
<script>
    $(function () {
        $('#login-form').submit(function () {
            var username = $('#username').val(),
                password = $('#password').val();
            if (username == '' || username.length <= 0) {
                layer.tips('用户名不能为空', '#username', {time: 2000, tips: 2});
                $('#username').focus();
                return false;
            }

            if (password == '' || password.length <= 0) {
                layer.tips('密码不能为空', '#password', {time: 2000, tips: 2});
                $('#password').focus();
                return false;
            }


            return true;
        })

    })
</script>

</script>
<
/html>