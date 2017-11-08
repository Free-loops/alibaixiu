<?php
include_once '../config.php'; //相对路径 php物理读文件
session_start();

function tuichu(){
  if (isset($_GET['id'])&&$_GET['id']=='t') {
    unset($_SESSION['email']);
    unset($_SESSION['password']);  
  }
}
//退出登录
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  tuichu();
}

function sess(){
  $con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);  
  if (!$con) {
    $GLOBALS['error'] = '数据库连接失败';
    return;
  }

  $email = $_SESSION['email'];
  $password = $_SESSION['password'];
  $query = mysqli_query($con,"select * from users where email = '{$email}' limit 1");

  if (!$query) {
    $GLOBALS['error'] = '数据查询失败';
    return;
  }
  $user = mysqli_fetch_assoc($query);
  if($email==$user['email']&&$password==$user['password']){
    mysqli_free_result($query);
    mysqli_close($con);
    header('Location: /admin/');
  }
}
//session验证
if (isset($_SESSION['email'])&&isset($_SESSION['password'])) {
  sess();
}

function login () {
  // 1. 接收并校验
  if (empty($_POST['email'])) {
    $GLOBALS['error'] = '请输入邮箱';
    return;
  }

  if (empty($_POST['password'])) {
    $GLOBALS['error'] = '请输入密码';
    return;
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  $con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);  
  
  if (!$con) {
    $GLOBALS['error'] = '数据库连接失败';
    return;
  }

  $query = mysqli_query($con,"select * from users where email = '{$email}' limit 1");

  if (!$query) {
    $GLOBALS['error'] = '数据查询失败';
    return;
  }
  //邮箱输错的情况
  $user = mysqli_fetch_assoc($query);


  if ($_POST['email']!=$user['email']) {
    $GLOBALS['error'] = '您输入的邮箱不正确';
    return;
  }

  if ($_POST['password']!=$user['password']) {
    $GLOBALS['error'] = '您输入的密码不正确';
    return;
  }
  //设置session
  $_SESSION['email'] = $email;
  $_SESSION['password'] = $password;

  mysqli_free_result($query);
  mysqli_close($con);

  header('Location: /admin/');

  // 3. 响应
}
//登录验证
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <!-- 可以通过在 form 上添加 novalidate 取消浏览器自带的校验功能 -->
    <!-- autocomplete="off" 关闭客户端的自动完成功能 -->
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)) { ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error; ?>
      </div>
      <?php } ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" value="<?php echo isset($_POST['email'])? $_POST['email'] : '';  ?>" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script>
  $(function($){
    $('#email').on('blur',function(){
      var value = $(this).val();
      $.get('/admin/api/avatar.php',{email:value},function(res){
        if(!res)return;
        $('.avatar').hide(200).show(200).attr('src',res)
      })
    })
  })
</script>
</html>