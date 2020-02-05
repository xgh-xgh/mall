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
    mysqli_select_db($con, $dbName);//函数用于更改连接的默认数据库
    mysqli_set_charset($con, 'UTF-8'); //  规定当与数据库服务器进行数据传送时要使用的默认字符集
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
    if (!in_array($type, array("image/png", "image/gif", "image/jpeg"))) {
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


/**
 * 获取当前url
 * @return string
 */
function getUrl()
{
    $url = '';
//获取端口号，http默认80，https是443
    $url .= $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
//获取到中间的域名
    $url .= $_SERVER['HTTP_HOST'];
//获取url问号后面的数值
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * 根据page生成url
 * @param $page      页数
 * @param string $url
 * @return string
 */
function pageUrl($page, $url = '')
{
    $url = empty($url) ? getUrl() :$url;
    //查询url中是否存在 ？如果查询不到就返回false,查询到返回在这个字符串的位置
    $pos = strpos($url,'?');
    if ($pos ===false){
        $url .='?page='.$page;
    }
    else{
        //截取字符串从$pos+1位置开始
        $queryString = substr($url,$pos+1);
        // 解析$queryString为数组
        parse_str($queryString,$queryArr);
        if (isset($queryArr['page'])){
            unset($queryArr['page']);
        }
        $queryArr['page'] = $page ;
        //将queryArr从新拼接成queryString
        $queryString = http_build_query($queryArr);
        //截取url 0-pos
        $url = substr($url,0,$pos).'?'.$queryString;
    }
    return $url;

}


/**
 * 分页显示
 * @param int $total 数据总条数
 * @param int $currentPage 当前所在页数
 * @param int $pageSize 每页显示条数
 * @param int $show 显示按钮数
 * @return string
 */
function pages($total, $currentPage, $pageSize, $show = 6)
{
    $pageStr = '';
    //仅当总数大于每页条数 才能进行分页
    if ($total > $pageSize) {
        //总页数
        $totalPage = ceil($total / $pageSize);//向上取整 获取总页数
        //对当前页进行处理
        $currentPage = $currentPage > $totalPage ? $totalPage : $currentPage;
        //分页起始页
        $form = max(1, ($currentPage - intval($show / 2)));
        //分页结束页
        $to = $form + $show - 1;

        $pageStr .= '<div class="page-nav">';
        $pageStr .= '<ul>';
        //仅当 当前页大于一时，存在首页和上一页按钮
        if ($currentPage > 1) {
            $pageStr .= "<li><a href='".pageUrl(1)."'>首页</a></li>";
            $pageStr .= "<li><a href='" . pageUrl($currentPage - 1) . "'>上一页</a></li>";
        }

        //当结束页大于总页数
        if ($to > $totalPage) {
            $to = $totalPage;
            $form = max(1, $to - $show + 1);
        }
        if ($form > 1) {
            $pageStr .= '<li>...</li>';
        }
        for ($i = $form; $i <= $to; $i++) {
            if ($i != $currentPage) {
                $pageStr .= "<li><a href='" . pageUrl($i). "'>{$i}</a></li>";
            } else {
                $pageStr .= "<li><span class='curr-page'>{$i}</span></li>";
            }
        }
        if ($to < $totalPage) {
            $pageStr .= '<li>...</li>';
        }

        if ($currentPage < $totalPage) {
            $pageStr .= "<li><a href='" . pageUrl($currentPage + 1) . "'>下一页</a></li>";
            $pageStr .= "<li><a href='" . pageUrl($totalPage) . "'>尾页</a></li>";
        }

        $pageStr .= '</ul>';
        $pageStr .= '</div>';
    }
    return $pageStr;
}


