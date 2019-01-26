<?php

require_once '../functions.php';

xiu_get_current_user();
$current_user= $_SESSION['current_login_user'];
// array(8) {
//   ["id"]=>
//   string(1) "4"
//   ["slug"]=>
//   string(3) "LYH"
//   ["email"]=>
//   string(16) "483025921@qq.com"
//   ["password"]=>
//   string(6) "123456"
//   ["nickname"]=>
//   string(9) "李永华"
//   ["avatar"]=>
//   string(27) "/static/uploads/avatar1.jpg"
//   ["bio"]=>
//   NULL
//   ["status"]=>
//   NULL
// }
function edit(){
  global $current_user;
  //校验文本
   if (empty($_POST['slug'])) {
    $GLOBALS['error_message'] = '请输入别名';
    return;
  }
    if (empty($_POST['nickname'])) {
    $GLOBALS['error_message'] = '请输入昵称';
    return;
  }
    if (empty($_POST['bio'])) {
    $GLOBALS['error_message'] = '请输入个性签名';
    return;
  }
    if (empty($_POST['email'])) {
    $GLOBALS['error_message'] = '请输入邮箱';
    return;
  }
  $slug=$_POST['slug'];
  $nickname=$_POST['nickname'];
  $bio=$_POST['bio'];
  $email=$_POST['email'];

if(empty($_FILES['avatar'])){
  $GLOBALS['error_message']='小伙子,把我的图片还给我';
  return;
}
$avatar=$_FILES['avatar'];
//验证错误类型
if($avatar['error']!==UPLOAD_ERR_OK){
  $GLOBALS['error_message']='请上传海报文件';
  return;
}
//校验文件类型
if(strpos($avatar['type'], 'image/')!==0){
  $GLOBALS['error_message']='请上传正确的海报类型';
  return;
}
//校验文件大小
if($avatar['size']>6*1024*1024){
  $GLOBALS['error_message']='海报文件过大';
  return;
}
//上传图片
$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);//提取了文件的类型(后缀)
$target = '../static/uploads/avatar-' . uniqid() . '.' . $ext;
  if (!move_uploaded_file($avatar['tmp_name'], $target)) {
    $GLOBALS['error_message'] = '上传海报失败';
    return;
  }
  $avatar = substr($target, 2);

  $affected_rows=xiu_execute("update users set slug='{$slug}', nickname='{$nickname}', bio='{$bio}', email='{$email}',avatar='{$avatar}' where id='{$current_user['id']}'");
  if ($affected_rows !== 1) {
    $GLOBALS['error_message'] = '更新数据失败';
    return;
      // 响应
  }
  $users = xiu_fetch_one("select * from users where id ='{$current_user['id']}'");
    $_SESSION['current_login_user'] = $users;
    header("Location: index.php");
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  edit();
}

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
      <div class="page-title">
        <h1>我的个人资料</h1>
          <?php if (isset($error_message)): ?>
    <div class="alert alert-danger">
      <?php echo $error_message; ?>
    </div>
    <?php endif ?>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file" name="avatar">
              <img src="/static/assets/img/default.png">
              <input type="hidden" name="avatar">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $current_user['email'] ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type"  placeholder="slug" value="<?php echo $current_user['slug'] ?>"/>
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type"  placeholder="昵称" value="<?php echo $current_user['nickname'] ?>">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" name="bio" cols="30" rows="6" placeholder="<?php echo $current_user['bio'] ?>"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <!-- <a class="btn btn-link" href="password-reset.php">修改密码</a> -->
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'profile'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>

  <script>
    //异步文件上传
    // $('#avatar').on('change',function(){
    //   //当文件选择状态发生变化是执行
    //   //判断是否选中了文件
    //   var files=$(this).prop('files')
    //   if (!files.length) return 

    //     //进行校验
    //   var file = files[0]
    //   //formData 是h5 新增的一个成员,配合ajax 用于客户端与服务端之间传递二进制数据
    //   var data = new FormData()
    //   data.append("avatar",file)

    //   var xhr = new XMLHttpRequest()
    //   xhr.open('POST','/admin/api/upload.php')
    //   xhr.send(data)        //借助于form data 传递文件
    //   xhr.onload=function(){
    //     console.log(this.responseText)
    //       $('#avatar').siblings('img').attr('src',this.responseText)
    //       $('#avatar').siblings('input').val(this.responseText)
    //     }
    // })
  </script>
</body>
</html>
