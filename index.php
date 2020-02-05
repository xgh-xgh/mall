<?php
include_once './lib/fun.php';

if($login =  checkLogin()){
    $user = $_SESSION['user'];
}
//商品查询
$page = isset($_GET['page'])? intval($_GET ['page']): 1;
//把page与1对比，取最大的
$page = max($page,1);
//每页显示的条数
$pageSize = 1;
    //page =1  limit 0,2
    //page =2  limit 2,2;
    //page = 3   limit 4,2
$offset = ($page-1)* $pageSize;
$con = mysqlInit('localhost','root','phpcj','imooc_mall');

$sql = "SELECT COUNT(id) as total from im_goods";
$obj = mysqli_query($con,$sql);
$result = mysqli_fetch_assoc($obj);

$total = isset($result['total'])?$result['total']:0;
unset($sql,$result,$obj);
//排序id从小到大，浏览人数从大到小排序
$sql = "SELECT * FROM im_goods ORDER BY `id` asc, `view` desc   limit {$offset},{$pageSize} ";



$mysqlResultObj = mysqli_query($con,$sql);
$listData = [];

while ($row = $mysqlResultObj->fetch_assoc()) {

    $listData[] = $row;
}
$pages = pages($total,$page,$pageSize,6);









//$sql = "SELECT * FROM im_goods limit 1,10";
//$con = mysqlInit('localhost', 'root', 'phpcj', 'imooc_mall');
//$mysqlResultObj = mysqli_query($con, $sql);
//
//$listData = [];
//
//while ($row = $mysqlResultObj->fetch_assoc()) {
//
//    $listData[] = $row;
//}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|首页</title>
    <link rel="stylesheet" type="text/css" href="./static/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="./static/css/index.css"/>
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <?php if($login): ?>
            <li><span>管理员:<?php echo $user['username'] ?></span></li>
                <li><a href="publish.php">发布</a></li>
            <li><a href="login_out.php">退出</a></li>
            <?php else : ?>
            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<div class="content">
    <div class="banner">
        <img class="banner-img" src="./static/image/welcome.png" width="732px" height="372" alt="图片描述">
    </div>
    <div class="img-content">
        <ul>
            <?php foreach ($listData as $item) : ?>
                <li>
                    <img class="img-li-fix" src="<?php echo $item['pic'] ?>" alt="<?php echo $item['name'] ?>">
                    <div class="info">
                        <a href="detail.php?id=<?php echo $item['id'] ?>"><h3 class="img_title"><?php echo $item['name'] ?></h3></a>
                        <p>
                            <?php echo $item['des'] ?>
                        </p>
                        <div class="btn">
                            <a href="edit.php?id=<?php echo $item['id'] ?>" class="edit">编辑</a>
                            <a href="delete.php?id=<?php echo $item['id'] ?>" class="del">删除</a>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
   <?php echo $pages ?>
</div>

<div class="footer">
    <p><span>M-GALLARY</span>©2017 POWERED BY IMOOC.INC</p>
</div>
</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script>
    $(function () {
        $('.del').on('click', function () {
            if (confirm('确认删除该画品吗?')) {
                window.location = $(this).attr('href');
            }
            return false;
        })
    })
</script>


</html>
