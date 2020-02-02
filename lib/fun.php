<?php

/**
 * 数据库操作
 * @param $host string
 * @param $username string
 * @param $password string
 * @param $dbName string
 * @return bool|false|mysqli
 */
function mysqlInit($host, $username, $password, $dbName)//数据库地址，用户名，密码，数据库名称
{
    $con = mysqli_connect($host, $username, $password);          //连接数据库
    if (!$con) {                    //如果连接失败
        return false;//echo mysql_error();exit;//mysql_error() 函数返回上一个 MySQL 操作产生的文本错误信息
    }
    mysqli_select_db($con, $dbName);//函数设置活动的 MySQL 数据库/成功返回true/不成功返回false
    mysqli_set_charset($con, 'UTF-8'); //     设置字符集
    return $con;
}

/**
 * 密码加密
 * @param $password string
 * @return bool|string
 *
 */
function createPassword($password)
{
    if (!$password) {
        return false;
    }

    return md5(md5($password) . 'IMOOC');
}

/*
*消息提示
@param  int  $type  1：成功 2:失败
        null $msg
        null $url
*/
function msg($type, $msg = null, $url = null)
{

    $toUrl = "location:msg.php?type={$type}";
    $toUrl .= $msg ? "&msg={$msg}" : '';
    $toUrl .= $url ? "&url={$url}" : '';
    header($toUrl);
    exit;
}

/**
 * 图像上传
 * @param $file
 * @return string
 */
function imgUpload($file)
{
    //检查上传文件是否合法
    if (!is_uploaded_file($file['tmp_name'])) {
        msg(2, '请上传符合规范的图像');   //最好写成return false;
    }
    //图像类型验证
    $type = $file['type'];
    if (!in_array($type, array("image/png", "image/gif", "image/jpg"))) {
        msg(2, '请上传png，gif，jpg的图像');
    }
    //上传目录
    $uploadPath = './static/file/';
    //上传目录访问url
    $uploadUrl = '/static/file/';
    //上传文件夹
    $fileDir = date('Y/md', $_SERVER['REQUEST_TIME']);
    //检测上传目录是否存在
    if (!is_dir($uploadPath . $fileDir)) {
        mkdir($uploadPath . $fileDir, 0755, true);//递归创建目录
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    //上传图像名称
    $img = uniqid() . mt_rand(1000, 9999) . '.' . $ext;
    //物理地址
    $imgPath = $uploadPath . $fileDir . $img;
    //在浏览器url访问地址
    $imgUrl = 'http://localhost/project/mall' . $uploadUrl . $fileDir . $img;
    //操作失败 查看上传目录权限
    if (!move_uploaded_file($file['tmp_name'], $imgPath)) {
        msg(2, '服务器繁忙，请稍后再试');
    }
    return $imgUrl;
}

/**
 * 检查用户登录
 * @return bool
 */
function checkLogin()
{
//开启session
    session_start();
    if (!isset($_SESSION['user']) && empty($_SESSION['user'])) {
        return false;
    }
    return true;
}


?>