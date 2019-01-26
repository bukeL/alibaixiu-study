<?php 






//接收文件
//保存文件
//返回这个文件的返回url
if(empty($_FILES['avatar'])){
	exit('必须上传文件');
}
$avatar= $_FILES['avatar'];

if($avatar['error']!==UPLOAD_ERR_OK){
	exit('上传失败1');
}
//校验大小


//移动文件到网站内
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target='../../static/uploads/img-'.uniqid().'.'.$ext;

if(!move_uploaded_file($avatar['tmp_name'], $target)){
	exit('上传失败2');
}
echo substr($target,5);