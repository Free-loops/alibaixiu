<?php
require_once '../functions.php';

require_once '../mysql.php';

xiu_get_current_user();//session验证

if (empty($_GET['delete_id'])) {
    header('Location: /admin/users.php?error=删除失败');
    die();
}

$id = $_GET['delete_id'];

$mysql = mysql("delete from users where id = {$id}");

if ($mysql<=0) {
    header('Location: /admin/users.php?error=删除失败');
    die();
}

header('Location: /admin/users.php?error=删除成功');

