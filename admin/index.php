<?php

// // 校验数据当前访问用户的 箱子（session）有没有登录的登录标识
// session_start();

// if (empty($_SESSION['current_login_user'])) {
//   // 没有当前登录用户信息，意味着没有登录
//   header('Location: /admin/login.php');
// }

require_once '../functions.php';

// 判断用户是否登录一定是最先去做
xiu_get_current_user();

// 获取界面所需要的数据
// 重复的操作一定封装起来
$posts_count = xiu_fetch_one('select count(1) as num from posts;')['num'];
$caogao = xiu_fetch_one('select count(1) as num from posts where status="drafted";')['num'];
var_dump($caogao);
$categories_count = xiu_fetch_one('select count(1) as num from categories;')['num'];

$comments_count = xiu_fetch_one('select count(1) as num from comments;')['num'];

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
      <div class="jumbotron text-center">
        <h3>今天就和昨天一样,想要明天有所改变,今天就必须有所行动</h3>
        <p>思考、记录、分享</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $posts_count; ?></strong>篇文章（<strong><?php echo $caogao ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories_count; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments_count; ?></strong>条评论（<strong>1</strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="chart"></canvas>
        </div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/chart/Chart.js"></script>
  <script>
    var ctx = document.getElementById('chart').getContext('2d')
    new Chart(ctx, {
      type: 'pie',
      data: {
        datasets: [
          {
            data: [<?php echo (int)$posts_count - (int)$caogao; ?>, <?php echo $caogao; ?>, <?php echo $comments_count; ?>],
            backgroundColor: [
              'hotpink',
              'pink',
              'deeppink',
            ]
          },
          {
            data: [<?php echo (int)$posts_count - (int)$caogao; ?>, <?php echo $caogao; ?>, <?php echo $comments_count; ?>],
            backgroundColor: [
              'hotpink',
              'pink',
              'deeppink',
            ]
          }
        ],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
          '发布文章',
          '草稿',
          '评论'
        ]
      }
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
