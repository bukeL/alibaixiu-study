<?php

require_once '../functions.php';

xiu_get_current_user();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
<!--           <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for comments}}
          <tr{{if status == 'held'}} class="warning"{{else status=='rejected'}} class="danger"{{/if}} data-id="{{:id}}">
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td>{{:post_title}}</td>
            <td>{{:created}}</td>
            <td>{{:status}}</td>
            <td class="text-center">
              {{if status=='held'}}
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
              {{/if}}
              <a href="javascript:;" class="btn btn-danger btn-xs btn-delete">删除</a>
            </td>
          </tr>
    {{/for}}
  </script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js" ></script>
  <script type="text/javascript">


      var currentPage=1
    function loadPageData(page){
    //发送AJAX请求,  获取评论的数据并渲染到界面上
    $.getJSON('/admin/api/comments.php',{page:page},function(res){
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
        var html= $('#comments_tmpl').render({comments:res.comments})
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
