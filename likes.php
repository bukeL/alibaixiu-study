<?php  
require_once 'functions.php';
if (empty($_GET['likes'])) {
  exit('缺少必要参数');
};
if (empty($_GET['posts_id'])) {
  exit('缺少必要参数');
};

$likes =$_GET['likes'];
$posts_id=$_GET['posts_id'];
$rows=xiu_execute("update posts set likes='{$likes}' where id='$posts_id'");
 echo $rows;