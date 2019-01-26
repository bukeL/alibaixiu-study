<?php

require_once '../functions.php';

xiu_get_current_user();
if (empty($_GET['id'])) {
  exit('缺少必要参数');
}
$id = $_GET['id'];
$sql="select * from users where id='{$id}'";
$query = xiu_fetch_one($sql);

function update(){
$id1 = $_GET['id'];
  if(empty($_POST['email'])){
  $GLOBALS['error_message']='请输入邮箱';
  return;
}
if(empty($_POST['slug'])){
  $GLOBALS['error_message']='请输入别名';
  return;
}
if(empty($_POST['nickname'])){
  $GLOBALS['error_message']='请输入昵称';
  return;
}
if(empty($_POST['password'])){
  $GLOBALS['error_message']='请输入密码';
  return;
}
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    //校验并接受完成  向数据库保存表单
    $query1="update  users  set slug = '{$slug}',  email='{$email}', nickname = '{$nickname}',password='{$password}' where id = '{$id1}'";
    $row =xiu_execute($query1);
      if ($row !== 1) {
    $GLOBALS['error_message'] = '更新数据失败';
    return;

}
header('Location: /admin/users.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  update();
}


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
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="row">
        <div class="col-md-4">
              <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                  <?php echo $error_message; ?>

                </div>
              <?php endif ?>
              
          <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>编辑用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php  echo $query['email'] ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php  echo $query['slug'] ?>">

            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php  echo $query['nickname'] ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码" value="<?php  echo $query['password'] ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">更新</button>
              <a href="users.php" class="btn btn-primary">回到添加</a>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <ul class="pagination pagination-sm pull-right"></ul>
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
<!--               <tr>
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="/static/assets/img/default.png"></td>
                <td>i@zce.me</td>
                <td>zce</td>
                <td>汪磊</td>
                <td>激活</td>
                <td class="text-center">
                  <a href="post-add.php" class="btn btn-default btn-xs">编辑</a>
                  <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr> -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for users}}
          <tr{{if status == 'held'}} class="warning"{{else status=='rejected'}} class="danger"{{/if}} data-id="{{:id}}">
              <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="{{:avatar}}"></td>
                <td>{{:email}}</td>
                <td>{{:slug}}</td>
                <td>{{:nickname}}</td>
                <td>激活</td>
                <td class="text-center">
                  <a href="user-edit.php?id={{:id}}" class="btn btn-default btn-xs">编辑</a>
                  <a href="user-delete.php?id={{:id}}" class="btn btn-danger btn-xs">删除</a>
                </td>
          </tr>
    {{/for}}
  </script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js" ></script>
  <script type="text/javascript">

      var currentPage=1
    function loadPageData(page){
    //发送AJAX请求,  获取评论的数据并渲染到界面上
    $.getJSON('/admin/api/user.php',{page:page},function(res){
      //请求完成过后自动执行的回调函数
      // 拿到了后台传过来的数据  console.log(res)
      // var data={}
      // data.comments=res
      // var html= $('#comments_tmpl').render(
      //   //comments:res;
      //   data
      //   )
      if(page>res.total_pages){
        loadPageData(res.total_pages)
        return false
      }
      $('.pagination').twbsPagination('destroy')
          $('.pagination').twbsPagination({
      first:'第一页',
      last:'最后一页',
      prev:'上一页',
      next:'下一页',
      startPage:page,
      totalPages:res.total_pages,
      visiablePages:5, 
      initiateStartPageClick:false,
      onPageClick:function(e,page){
        // console.log(page)
        loadPageData(page);
        //初始化时就会执行一次
      }

    })
        var html= $('#comments_tmpl').render({users:res.users})
        $('tbody').html(html)
        currentPage=page
    })
    }
         //初始化组件
          $('.pagination').twbsPagination({
      first:'第一页',
      last:'最后一页',
      prev:'上一页',
      next:'下一页',
      totalPages:100,
      visiablePages:5, 
      onPageClick:function(e,page){
        // console.log(page)
        loadPageData(page);
        //初始化时就会执行一次
      }

    })



//删除功能,由于是动态加载的数据,给自己注册时注册不上的,
//所以注册事件需要采用委托的方式
//点击事件,通过ajax删除数据
$('tbody').on('click','.btn-delete',function(){

      console.log("111")
      //1  先拿到需要删除的数据id
      var $tr = $(this).parent().parent()
      var id = $tr.data('id')
      //2  发送一条ajax请求,告诉服务端需要删除那一条
      //3  根据服务端返回是否成功决定是否删除界面上的这个元素
       $.get('/admin/api/comment-delete.php',{id:id},function(res){
        if(!res) return 
            loadPageData(currentPage)

       })
})

  </script> 
  <script>NProgress.done()</script>
</body>
</html>
