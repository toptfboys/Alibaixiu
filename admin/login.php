<?php
/**
 * 判断请求
 */

require_once '../config.php';


// 给用户找一个箱子(如果有就用当前的，如果没有就给一个新的)

session_start();
function login()
{
    //1. 接手并校验
    //2. 持久换
    //3. 响应
    if (empty($_POST['email'])) {
        $GLOBALS['message'] = '请填写邮箱';
        return;
    }
    if (empty($_POST['password'])) {
        $GLOBALS['message'] = '请填写密码';
        return;
    }
    $email = $_POST['email'];
    $pwd = $_POST['password'];

    $conn =mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

    if(!$conn){
        exit('<h1>数据库连接失败</h1>');
    }
    $query = mysqli_query($conn,"select * from users where email ='{$email}' limit 1");
    if(!$query){
        $GLOBALS['message'] = '登录失败，请重试';
        return;
    }
    $user = mysqli_fetch_assoc($query);
    if(!$user){
        $GLOBALS['message'] = '邮箱与密码不匹配';
        return;
    }
    if($user['password'] !== md5($pwd)){
        $GLOBALS['message'] = '邮箱与密码不匹配';
        return;
    }
//    if ($email != 'admin@zce.me') {
//        $GLOBALS['message'] = '用户名与密码不一致!';
//        return;
//    }
//    if ($pwd != '123') {
//        $GLOBALS['message'] = '用户名与密码不一致！';
//        return;
//    }
    $_SESSION['current_login_user'] = $user;
    header('Location:/aliBaixiu/admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();
}
// 退出功能
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])
    && $_GET['action'] === 'logout') {
    // 删除了登录标识
    unset($_SESSION['current_login_user']);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Sign in &laquo; Admin</title>
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/css/admin.css">
    <link rel="stylesheet" href="/aliBaixiu/static/assets/vendors/animate/animate.css">
</head>
<body>
<div class="login">
    <form class="login-wrap <?php echo (isset($message)) ? 'shake animated ' : '' ?> "
          action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post"
          novalidate autocomplete="off">
        <img class="avatar" src="/aliBaixiu/static/assets/img/default.png">
        <!-- 有错误信息时展示 -->
        <?php if (isset($message)): ?>
            <div class="alert alert-danger">
                <strong>错误！</strong><?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="email" class="sr-only">邮箱</label>
            <input id="email" name="email" type="email" class="form-control"
                   placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
        </div>
        <div class="form-group">
            <label for="password" class="sr-only">密码</label>
            <input id="password" name="password" type="password" class="form-control" placeholder="密码">
        </div>
        <button class="btn btn-primary btn-block">登 录</button>
    </form>
</div>
<script src="/aliBaixiu/static/assets/vendors/jquery/jquery.js"></script>
<script>
    $(function ($) {
        var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;
        $("#email").on('blur',function () {
            var value = $(this).val();
            if(!value || !emailFormat.test(value)) return;

            $.get('/aliBaixiu/admin/api/avatar.php',{email:value},function (res) {
                if(!res) return;
                $('.avatar').fadeOut(function () {
                    $(this).on('load',function () {
                        $(this).fadeIn();
                    }).attr('src',res);
                })
            })

        })
    })
</script>
</body>
</html>
