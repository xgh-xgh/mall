<?php
//编辑商品
//开启session
include_once './lib/fun.php';
if (!checkLogin()) {
    msg(2, '请登录', 'login.php');
}
//表单进行了提交处理
if (!empty($_POST['name'])) {
    $con = mysqlInit('localhost', 'root', 'phpcj', 'imooc_mall');

    if (!$goodsId = intval($_POST['id'])) {
        msg(2, '参数非法');
    }
    //根据商品id效验商品信息
    $sql = "SELECT * FROM im_goods WHERE id = {$goodsId}";
    $obj = mysqli_query($con, $sql);
    //当根据商品查询商品信息为空 跳转商品列表页
    if (!$goods = mysqli_fetch_assoc($obj)) {
        msg(2, '画品不存在', 'index.php');
    }


    //处理表单数据
    //画品名称
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    //画品价格
    $price = intval($_POST['price']);
    //画品简介
    $des = mysqli_real_escape_string($con, trim($_POST['des']));
    //画品详情
    $content = mysqli_real_escape_string($con, trim($_POST['content']));

    $nameLength = mb_strlen($name, 'utf-8');
    if ($nameLength <= 0 || $nameLength > 30) {
        msg(2, '商品名应在1-30字符之间');
    }
    if ($price <= 0 || $price > 999999999) {
        msg(2, '商品价格应该小于999999999');
    }
    $desLength = mb_strlen($des, 'utf-8');
    if ($desLength <= 0 || $desLength > 100) {
        msg(2, '画品简介应在1-100字符之内');
    }
    if (empty($content)) {
        msg(2, '画品详情不能为空');
    }

    //跟新数组
    $update = array(
        'name' => $name,
        'price' => $price,
        'des' => $des,
        'content' => $content
    );

    //当用户先择图片上传，才 进行图片上传
    if ($_FILES['file']['size'] > 0) {
        $pic = imgUpload($_FILES['file']);
        $update['pic'] = $pic;
    }
    //跟新sql处理
    $updateSql = '';
    foreach ($update as $k => $v) {
        $updateSql .= "{$k} = '{$v}',";
    }
    //去除多余逗号
    $updateSql = rtrim($updateSql, ',');

    //只跟新被更改的信息
    foreach ($update as $k => $v) {
        if ($goods[$k] == $v) {
            unset($update[$k]);
        }
    }

} else {
    msg(2, '路由非法', 'index.php');
}