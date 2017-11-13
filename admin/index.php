<?php
include_once '../config.php';
include_once '../functions.php';
include_once '../mysql.php';



if (empty($_SESSION['email']) || empty($_SESSION['password'])) {
  header('Location: /admin/login.php');
}

$email = $_SESSION['email'];

$password = $_SESSION['password'];

$con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if (!$con) {
  die('数据库连接失败');
}

$query = mysqli_query($con,"select * from users where email = '{$email}' limit 1");

if (!$query) {
  die('数据查询失败');
}

$user = mysqli_fetch_assoc($query);

if ($user['email'] != $email || $user['password'] != $password) {
  header('Location: ../admin/login.php');
}


// $posts_count = xiu_fetch_one('select count(1) as num from posts;')['num'];

// $categories_count = xiu_fetch_one('select count(1) as num from categories;')['num'];

// $comments_count = xiu_fetch_one('select count(1) as num from comments;')['num'];

//多表统计
<<<<<<< HEAD
$count = mysql("select count(1) as 'posts_c',
=======
$count = mysql("select count(posts.id) as 'posts_c',
>>>>>>> 13d18ea6695489a31c4c5d824af3dc256c47ed9f
(select count(1) from posts where status='drafted') as 'drafted_c',
(select count(1)  from categories) as 'categories_c',
(select count(1) from comments) as 'comments_c',
(select count(1) from comments where status='held') as 'held_c'
from posts;");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $count['posts_c']; ?></strong>篇文章（<strong><?php echo $count['drafted_c']; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $count['categories_c']; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $count['comments_c']; ?></strong>条评论（<strong><?php echo $count['held_c']; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page = 'index'; ?>

  <?php include 'inc/asidebar.php' ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
