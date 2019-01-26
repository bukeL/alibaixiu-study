<?php 
//勇于接受客户端传来的AJAX请求   返回用户数据

 //载入封装的函数
require_once '../../functions.php';

//拿到客户端传来的page
$page = empty($_GET['page'])?1:intval($_GET['page']);
$length = 3 ;
$offset = ($page - 1) * $length;
$sql = sprintf('select 
	*
	from 
	comments
	limit %d,%d;', $offset , $length);

//关联查询 查询评论表单所有数据和文章标的标题
$comments = xiu_fetch_all($sql);

//取数据总量 计算总页数

$total_count=xiu_fetch_one('select count(1)  as count
		from comments
		')['count'];
$total_pages=ceil($total_count / $length);//得到的时float类型,但数字是整数,在js里不区分

// var_dump($comments);
//因为网络之间传输为字符串,所以将上面的数据转换为字符串
//json可以轻松将数据转为有格式的字符串

$json =json_encode(array(
	'total_pages'=>$total_pages,
	'comments'=>$comments
));


//设置响应体的响应类型为json
header('Content-Type: application/json');

//响应给客户端
echo $json;