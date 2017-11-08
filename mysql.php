<?php 
//用法 参数传数据库信息 和 sql语句 函数调用完是否关闭数据库(默认关闭)
//例如 mysql('127.0.0.1','root','123456','db_1','delete from user where id=2');
//返回值  错误会返回相应的错误信息 增删改返回受影响行数 查询返回要查询的数据
//基于mysql封装 不同sql语句函数
require_once 'config.php';
function mysql($sql='null',$bool=true) {
    //默认值 几个地方是否关闭数据库连接 为什么要关闭数据库链接
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$con) {
       // return '数据库链接失败';
       return false;
    }

    $query = mysqli_query($con,$sql);

    if (!$query) {
        //得到一个布尔值 无法释放一个布尔值
        if ($bool) mysqli_close($con);
        //return '查询失败,查询语句错误';
        return false;
    }
    //var_dump($query);//打印该对象
    if (substr($sql,0,6) == 'select') {
        if (!($user = mysqli_fetch_assoc($query))) {//得到一个空的查询对象
            mysqli_free_result($query);//释放查询对象 减少性能浪费
            if ($bool) mysqli_close($con);          
            //return '没有该数据';
            return false;
        }
        $date[]=$user;
        while ($user = mysqli_fetch_assoc($query)) {
            $date[] = $user;
        }
        mysqli_free_result($query);
        if ($bool) mysqli_close($con);
        //如果只查询一行数据 则返回该行数据 否则返回一个数组
        if (count($date) == 1) {
            return $date[0];
        }
        return $date;
    } 

    $rows = mysqli_affected_rows($con);

    if ($bool) mysqli_close($con);  //增删改无需释放查询对象  此处判断是否关闭数据库
    
    // if ($rows<=0) {
    //     //return '没有该数据';
    //     return false;
    // }
    return $rows;//
}
// =====================相关sql语句=========================================


// -----筛选 查询-------
// select id from users;  查询所有id从这个叫users的表中 查询多个属性逗号隔开
// select id from users where id in (1,5);
// select * from users;  全表查询
// select '某一个值' from users ;
// 查询得到的是结果集 增删改得到的是受影响的行数


// -----插入一行---------
// insert into users values(null,'ceo','张三',18);
// value也可以   如果不表明id或者行号 就必须在users后面加上相应的键


// ------删除行--------
// delete from users; 清空所有行
// delete from users where title='ceo'and id>1;
// 删除title为ceo的行 和id>1的行
// delete from users where id in (1,2,3);
// 删除id为1,2,3的行


// ------修改行---------
// update users set title = ‘ceo’ where id = 1;


// *********查询函数*********
// select count() from users; 表中某一个值/某一项的个数
// select count() as count from users; 
// select max(id) from users;   id中得最大值
// select * from users limit 2; 取前两行数据
// select * from users limit 1,2; 越过1行 取两行数据

//commit 提交 

//多表连查
// $sql="select

// posts.title,
// categories.name as category_name,
// users.nickname as user_name,
// from posts
// inner join categories on posts.category_id = categories.id
// inner join users on posts.user_id = users.id
// where {$where}   and posts.status = '状态1' or users.nickname like '昵称1'"
// order by posts.created desc
// limit {$offset}, {$size};"
//order by 按照...排序
//desc 升序


//多表统计
// "select count(posts.id) as 'posts_c',
// (select count(categories.id)  from categories) as 'categories_c',
// (select count(comments.id) from comments) as 'comment_c'
// from posts;"

// select count(posts.id) as '文章数量',
// (select count(posts.id) from posts where status='drafted') as '文章中草稿的数量',
// (select count(categories.id)  from categories) as '分类数量',
// (select count(comments.id) from comments) as '评论数量',
// (select count(comments.id) from comments where status='held') as '评论中待审核的数量'
// from posts;

//模糊查询条件
//where or users.nickname like '%你好%' or users.nickname like '%你好%'

//where 1=1; 无效条件

//sql注入where 1 or 1=1;