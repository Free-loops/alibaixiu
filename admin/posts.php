<?php
require_once '../functions.php';

require_once '../mysql.php';

xiu_get_current_user();//session验证
$visiables = 5;//显示页数的按钮有几个
$size = 10;//每页文章数量
$where = '1 = 1';//默认筛选条件

//文章分类
$search = '';

// 分类筛选
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}

if (!empty($_GET['search'])) {
  $var=$_GET['search'];
  $where .= " and posts.title like '%".$var."%'";// or posts.content like '%$var%'
  $where .= " or users.nickname like '%".$var."%'";
  $where .= " or posts.content like '%".$var."%'";
  $where .= " or posts.created like '%".$var."%'";

  $search .= '&search=' . $_GET['search'];
}

$total_count = (int)mysql("select count(1) as count from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where};")['count'];//文章总数量
$total_pages = (int)ceil($total_count / $size);//显示的最大页数 向上取整并转换为数字类型

//限制当前页码只能为1到最大值
$page = empty($_GET['p']) ? 1 :(int)$_GET['p'];
$page = $page < 1 ? 1: $page;
$page = $page > $total_pages ? $total_pages : $page;

$offset = $size*($page-1);//根据当前页码和每页显示数量 计算数据库查询时越过多少条

$sql = "select
posts.id,
posts.title,
users.nickname as user_name,
categories.name as category_name,
posts.created,
posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where} 
order by posts.created desc
limit {$offset}, {$size};";

$posts = mysql($sql);

// 查询全部的分类数据
$categories = mysql('select * from categories;');

$star = $page-($visiables-1)/2;//第一个按钮的页码

$end = $page+($visiables-1)/2;//最后一个按钮的页码

//限制第一个按钮的页码和最后一个按钮的页码
$star = $star < 1 ? 1 : $star;
$end = $star + $visiables - 1;
$end = $end > $total_pages? $total_pages : $end;
$star = $end-$visiables+1;
$star = $star < 1 ? 1 : $star;

function xiu_get_status($status) {
  $get_status = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );
  return isset($get_status[$status]) ? $get_status[$status] : '未知' ;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    #go_page .go-page {
      width:50px;
      display:inline-block;
    }
    #search　#search_inp {
      width:100px;
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    
    <?php include 'inc/navbar.php' ?>
  
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error)){ ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?echo $error ?>
      </div>
      <?php } ?>
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action='<?php $_SERVER['PHP_SELF'] ?>' >
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $value) { ?>
              <option value="<?php echo $value['id']; ?>" <?php echo isset($_GET['category'])&&$_GET['category']==$value['id']? ' selected' :"" ?>>
                <?php echo $value['name'] ?>
              </option>
            <?php } ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="published" <?php echo isset($_GET['status'])&&$_GET['status']=='published'?' selected':'' ?>>已发布</option>
            <option value="drafted" <?php echo isset($_GET['status'])&&$_GET['status']=='drafted'?' selected':'' ?>>草稿</option>
            <option value="trashed" <?php echo isset($_GET['status'])&&$_GET['status']=='trashed'?' selected':'' ?>>回收站</option>            
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
          <input type="text" name='search' class="form-control input-sm go-page" id='search_inp' placeholder='标题/内容/作者/时间...' value='<?php echo isset($_GET['search'])?$_GET['search']:''; ?>'>
          <button class="btn btn-default btn-sm" id='search_btn'>搜索</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="/admin/posts.php?p=<?php echo '1'.$search; ?>">首页</a></li>
          <?php if($page!==1): ?>
            <li><a href="/admin/posts.php?p=<?php echo ($page-1).$search; ?>">«</a></li>
          <?php endif; ?>
            <?php for($i=$star; $i<=$end;$i++) : ?>
              <li <?php echo $page==$i? "class=active":'' ?>><a href="/admin/posts.php?p=<?php echo $i.$search; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
          <?php if($page!==$total_pages): ?>
            <li><a href="/admin/posts.php?p=<?php echo ($page+1).$search; ?>">»</a></li>
          <?php endif; ?>
          <li><a href="/admin/posts.php?p=<?php echo $total_pages.$search; ?>">尾页</a></li>          
        </ul>
        <form class="form-inline pull-right" id = 'go_page'>
        第<input type="text" name='p' class="form-control input-sm go-page" id='go_inp'>页
          <a href='' class="btn btn-default btn-sm" id='go_btn'>GO</a>
        </form>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($posts['title'])){ ?>
            <tr>
              <td class="text-center"><input type="checkbox"></td>
              <td><?php echo $posts['title'] ?></td>
              <td><?php echo $posts['user_name'] ?></td>
              <td><?php echo $posts['category_name'] ?></td>
              <td class="text-center"><?php echo $posts['created'] ?></td>
              <td class="text-center"><?php echo xiu_get_status($posts['status']); ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php } ?>
          <?php if(!empty($posts)&&empty($posts['title'])){ ?>
          <?php foreach($posts as $value) { ?>
            <tr>
              <td class="text-center"><input type="checkbox"></td>
              <td><?php echo $value['title'] ?></td>
              <td><?php echo $value['user_name'] ?></td>
              <td><?php echo $value['category_name'] ?></td>
              <td class="text-center"><?php echo $value['created'] ?></td>
              <td class="text-center"><?php echo xiu_get_status($value['status']); ?></td>
              <td class="text-center">
                <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php } ?>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/asidebar.php' ?>
  
  <script src="/static/assets/vendors/jquery/jquery.min.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>

  <script>NProgress.done()</script>
 
</body>
<script>
    $('#go_inp').on('input',function(){
      var a='<?php echo $search; ?>';
      $('#go_btn').attr('href',"/admin/posts.php?p="+$(this).val()+a)
    })
    $('#search_inp').on('input',function(){
      var a='<?php echo $search; ?>';
      $('#go_btn').attr('href',"/admin/posts.php?p="+$('#go_inp').val()+a)
    })
</script> 
</html>
