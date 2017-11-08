<?php
//根据用户邮箱 获取用户头像
include "../../mysql.php";
$email = $_GET['email'];
$mysql = mysql("select * from users where email='{$email}';");
//select avatar from users where email = '{$email}' limit 1;
if(!$mysql){
    die("/static/assets/img/default.png");
    //return;
}
echo $mysql['avatar'];