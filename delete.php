<?php
include_once './lib/fun.php';
if(!checkLogin()){
    msg(2,'请登录','login.php');
}
$goodsId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']):'';
if(!$goodsId){
    msg(2,'参数非法','index.php');
}
$con = mysqlInit('localhost','root','phpcj','imooc_mall');
$sql = "SELECT id FROM im_goods WHERE id ='{$goodsId}'";
$obj = mysqli_query($con,$sql);
if(!$goods = mysqli_fetch_all($obj)){
    msg(2,'商品不存在','index.php');
}
$sql = "DELETE FROM im_goods WHERE id = '{$goodsId}'";
$result =mysqli_query($con,$sql);
if($result){
    msg(1,'操作成功','index.php');
}
else{
    msg(2,'删除失败','index.php');
}

