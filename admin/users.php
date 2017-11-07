<?php
require_once '../functions.php';

require_once '../mysql.php';

xiu_get_current_user();//session验证

function add_users() {
    //非空校验 文件校验
  if (empty($_FILES['avatar'])) {
    $GLOBALS['error'] = '请上传头像';
    return;
  }
  if ($_FILES['avatar']['error']!=0) {
    $GLOBALS['error'] = '头像上传错误';
    return;
  }
  $exit = array('image/png','image/jpg','image/jpeg');
  if(!in_array($_FILES['avatar']['type'],$exit)){
    $GLOBALS['error'] = '头像格式错误';
    return;
  }
  if (empty($_POST['email'])) {
    $GLOBALS['error'] = '邮箱不能为空';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['error'] = '别名不能为空';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['error'] = '昵称不能为空';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error'] = '密码不能为空';
    return;
  }

  //移动文件
  $avatar = $_FILES['avatar']['tmp_name'];
  $target = '../static/uploads/'.uniqid().$_FILES['avatar']['name'];
  $move = move_uploaded_file($avatar,$target);
  if (!$move) {//移动失败
    $GLOBALS['error'] = '头像上传错误';
    return;
  }
  //取值
  $avatar = substr($target,2,strlen($target));
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];

  $mysql=mysql("insert into users values(null,'{$slug}','{$email}','{$password}','{$nickname}','{$avatar}',null,'unactivated');");
  
  if (!$mysql) {
    $GLOBALS['error'] = '添加失败';
    return;
  }
  $GLOBALS['error'] = '添加成功';
}

function elip_users() {
  //非空校验 文件校验
  if (empty($_FILES['avatar'])) {
    $GLOBALS['error'] = '请上传头像';
    return;
  }
  if ($_FILES['avatar']['error']!=0) {
    $GLOBALS['error'] = '头像上传错误';
    return;
  }
  $exit = array('image/png','image/jpg','image/jpeg');
  if(!in_array($_FILES['avatar']['type'],$exit)){
    $GLOBALS['error'] = '头像格式错误';
    return;
  }
  if (empty($_POST['email'])) {
    $GLOBALS['error'] = '邮箱不能为空';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['error'] = '别名不能为空';
    return;
  }
  if (empty($_POST['nickname'])) {
    $GLOBALS['error'] = '昵称不能为空';
    return;
  }
  if (empty($_POST['password'])) {
    $GLOBALS['error'] = '密码不能为空';
    return;
  }

  //移动文件
  $avatar = $_FILES['avatar']['tmp_name'];
  $target = '../static/uploads/'.uniqid().$_FILES['avatar']['name'];
  $move = move_uploaded_file($avatar,$target);
  if (!$move) {//移动失败
    $GLOBALS['error'] = '头像上传失败';
    return;
  }
  //取值
  $id = $_GET['elip_id'];
  $avatar = substr($target,2,strlen($target));
  $email = $_POST['email'];
  $slug = $_POST['slug'];
  $nickname = $_POST['nickname'];
  $password = $_POST['password'];

  $mysql=mysql("update users set slug='{$slug}',email='{$email}',password='{$password}',nickname='{$nickname}',avatar='{$avatar}' where id={$id};");

  if (!$mysql) {
    $GLOBALS['edit_error'] = '保存失败';
    return;
  }
  $GLOBALS['edit_error'] = '保存成功';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'&&empty($_GET['elip_id'])) {
  add_users();
}

if (isset($_GET['elip_id'])) {
  $elip_id = $_GET['elip_id'];
  $mysql1 = mysql("select * from users where id = {$elip_id} limit 1");
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    elip_users();
  }    
}

$mysql = mysql("select * from users");
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    
    <?php include 'inc/navbar.php' ?>
  
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 add-->
      <?php if (isset($error)){ ?>
        <div class="alert alert-<?php echo $error=='添加成功'? 'success':'danger'?>">
          <?php echo $error; ?><strong><?php echo $error=='添加成功'? '√':'×'?></strong>
        </div>
      <?php } ?>
      <!-- 有错误信息时展示 delete-->
      <?php if (isset($_GET['error'])){ ?>
        <?php $error = $_GET['error'] ?>
        <div class="alert alert-<?php echo $error=='删除成功'? 'success':'danger'?>">
          <?php echo $error; ?><strong><?php echo $error=='删除成功'? '√':'×'?></strong>
        </div>
      <?php } ?>
      <!-- 有错误信息时展示 etit-->
      <?php if (isset($edit_error)){ ?>
        <?php $error = $edit_error ?>
        <div class="alert alert-<?php echo $error=='保存成功'? 'success':'danger'?>">
          <?php echo $error; ?><strong><?php echo $error=='保存成功'? '√':'×'?></strong>
        </div>
      <?php } ?>
      <div class="row">
        <div class="col-md-4">
          <form action='<?php $_SERVER['PHP_SELF']; ?>' method = 'post' enctype='multipart/form-data'>
            <?php if (isset($_GET['elip_id'])) { ?>
              <h2>编辑<?php echo $_GET['elip_name'] ?></h2>
            <?php }else{ ?>
              <h2>添加新用户</h2>
            <?php } ?>
            <div class="form-group">
              <label for="avatar">邮箱</label>
              <input id="avatar" class="form-control" name="avatar" type="file">
            </div>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" value='<?php echo isset($mysql1['email']) ? $mysql1['email'] : ''; ?>' placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" value='<?php echo isset($_POST['slug'])?$_POST['slug']:''; ?><?php echo isset($mysql1['slug']) ? $mysql1['slug'] : ''; ?>' placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" value='<?php echo isset($_POST['nickname'])?$_POST['nickname']:''; ?><?php echo isset($mysql1['nickname']) ? $mysql1['nickname'] : ''; ?>' type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="password" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit"><?php echo isset($_GET['elip_id'])?'保存':'添加' ?></button>
              <?php if(isset($_GET['elip_id'])){?>
                <a href='../admin/users.php' class="btn btn-default btn-cancel">取消</a>
              <?php } ?>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($mysql as $value): ?>
                <tr>
                  <td class="text-center"><input type="checkbox"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $value['avatar'] ?>"></td>
                  <td><?php echo $value['email'] ?></td>
                  <td><?php echo $value['slug'] ?></td>
                  <td><?php echo $value['nickname'] ?></td>
                  <td><?php echo $value['status']=='activated'?'激活':'未激活' ?></td>
                  <td class="text-center">
                    <a href="/admin/users.php?elip_id=<?php echo $value['id']; ?>&elip_name=<?php echo $value['nickname']; ?>" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/delete-users.php?delete_id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/asidebar.php'; ?>
  

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>