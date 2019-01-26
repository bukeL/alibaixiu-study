<?php

require_once '../functions.php';
xiu_get_current_user();
$current_user= $_SESSION['current_login_user'];
if (empty($_GET['id'])) {
  exit('<h1>必须传入指定参数</h1>');
}

$id = $_GET['id'];
 $posts= xiu_fetch_one("select * from posts  where id='{$id}'");  
if (!$posts) {
  exit('<h1>找不到你要编辑的数据</h1>');
}


function edit(){
global $posts;
  //判断文本域
    if (empty($_POST['title'])) {
    $GLOBALS['error_message'] = '请填写标题';
    return;
  }
    if (empty($_POST['content'])) {
    $GLOBALS['error_message'] = '请填写内容';
    return;
  }
    if (empty($_POST['slug'])) {
    $GLOBALS['error_message'] = '请填写别名';
    return;
  }
    if (empty($_POST['category'])) {
    $GLOBALS['error_message'] = '请填写分类';
    return;
  }
    if (empty($_POST['created'])) {
    $GLOBALS['error_message'] = '请填写时间';
    return;
  }
    if (empty($_POST['status'])) {
    $GLOBALS['error_message'] = '请填写状态';
    return;
  }
  //持久化
  $user_id=$_POST['user_id'];
  $title=$_POST['title'];
  $content=$_POST['content'];
  $slug=$_POST['slug'];
  $category=$_POST['category'];
  $created=$_POST['created'];
  $status=$_POST['status'];

  //检验图片

  if(empty($_FILES['pic'])){
  $GLOBALS['error_message']='小伙子,把我的图片还给我';
  return;
}
$pic=$_FILES['pic'];
//验证错误类型
if($pic['error']!==UPLOAD_ERR_OK){
  $GLOBALS['error_message']='请上传海报文件';
  return;
}
//校验文件类型
if(strpos($pic['type'], 'image/')!==0){
  $GLOBALS['error_message']='请上传正确的海报类型';
  return;
}
//校验文件大小
if($pic['size']>6*1024*1024){
  $GLOBALS['error_message']='海报文件过大';
  return;
}
//上传图片
$ext=pathinfo($pic['name'],PATHINFO_EXTENSION);//提取了文件的类型(后缀)
$target = '../static/uploads/pic-' . uniqid() . '.' . $ext;
  if (!move_uploaded_file($pic['tmp_name'], $target)) {
    $GLOBALS['error_message'] = '上传海报失败';
    return;
  }
  $pic = substr($target, 2);

  $affected_rows=xiu_execute("update posts set slug='{$slug}', title='{$title}', feature='{$pic}', created='{$created}',content='{$content}',views='{$posts['views']}',likes='{$posts['likes']}',status='{$status}',user_id='{$user_id}',category_id='{$category}' where id='{$posts['id']}'");
  if ($affected_rows !== 1) {
    $GLOBALS['error_message'] = '更新数据失败';
    return;
  }

  // 响应
  header('Location: posts.php');

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  edit();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>修改文章</h1>
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
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $posts['id']; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?php echo $current_user['id'] ?>">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" placeholder="文章标题" value="<?php echo $posts['title'] ?>">
          </div>
          <div class="form-group">
            <label for="content">内容</label>
            <!-- <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea> -->
            <script id="content" name="content" type="text/plain">这是初始值</script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $posts['slug'] ?>">
          </div>
      <div class="form-group">
        <label for="pic">海报</label>
        <!-- multiple 可以让一个文件域多选 -->
        <input type="file" class="form-control" id="pic" name="pic" accept="image/*" multiple>
      </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <option value="1">享技术</option>
              <option value="2">奇趣事</option>
              <option value="3">会生活</option>
              <option value="4">爱旅行</option>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo $posts['created'] ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script>
    UE.getEditor('content', {
      initialFrameHeight: 400,
      
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
