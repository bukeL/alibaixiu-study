<?php 
require_once 'functions.php';
//分类展示
if (empty($_GET['posts_id'])) {
  exit('缺少必要参数');
};
if (empty($_GET['id'])) {
  exit('缺少必要参数');
};

$posts_id=$_GET['posts_id'];
$id=$_GET['id'];
$posts = xiu_fetch_one("select
  posts.id as posts_id,
  categories.id as categories_id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status,
  posts.slug,
  posts.content,
  posts.views,
  posts.likes as posts_likes
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where category_id='{$id}' and posts.id='{$posts_id}'
"); 
$views2=(int)$posts['views']+1;
xiu_execute("update posts set views='{$views2}' where id='{$posts['posts_id']}'");
$categories = xiu_fetch_all('select * from categories;');

//获取随机推荐
$postAll = xiu_fetch_all('select * from posts;');
$arr1=array_rand($postAll,5);
//重组数组
foreach($arr1 as $val){
    $data_last[]=$postAll[$val];
}
 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>阿里百秀-发现生活，发现美!</title>
  <link rel="stylesheet" href="static/assets/css/style.css">
  <link rel="stylesheet" href="static/assets/vendors/font-awesome/css/font-awesome.css">
</head>
<body>
  <div class="wrapper">
    <div class="topnav">
      <ul>
      <?php foreach ($categories as $item): ?>
        <li><a href="list.php?id=<?php echo $item['id'] ?>"><i class="fa <?php echo $item['id']=='1'?'fa-glass':($item['id']=='2'?'fa-phone':($item['id']=='3'?'fa-fire':'fa-gift')) ?>"></i><?php echo $item['name'] ?></a></li>

       <?php endforeach ?>
      </ul>
    </div>
    <div class="header">
      <h1 class="logo"><a href="index.php"><img src="static/assets/img/logo.png" alt=""></a></h1>
      <ul class="nav">
      <?php foreach ($categories as $item): ?>
        <li><a href="list.php?id=<?php echo $item['id'] ?>"><i class="fa <?php echo $item['id']=='1'?'fa-glass':($item['id']=='2'?'fa-phone':($item['id']=='3'?'fa-fire':'fa-gift')) ?>"></i><?php echo $item['name'] ?></a></li>

       <?php endforeach ?>
      </ul>
      <div class="search">
        <form>
          <input type="text" class="keys" placeholder="输入关键字">
          <input type="submit" class="btn" value="搜索">
        </form>
      </div>
      <div class="slink">
        <a href="admin/login.php">后台</a> | <a href="javascript:;">链接02</a>
      </div>
    </div>
    <div class="aside">
      <div class="widgets">
        <h4>搜索</h4>
        <div class="body search">
          <form>
            <input type="text" class="keys" placeholder="输入关键字">
            <input type="submit" class="btn" value="搜索">
          </form>
        </div>
      </div>
      <div class="widgets">
        <h4>随机推荐</h4>

<?php foreach ($data_last as $item): ?>
        <ul class="body random">
          <li>
            <a href="detail.php?posts_id=<?php echo $item['id'] ?>&id=<?php echo $item['category_id'] ?>">
              <p class="title"><?php echo $item['title'] ?></p>
              <p class="reading">阅读(<?php echo $item['views'] ?>)</p>
              <div class="pic">
                <img src="static/uploads/widget_1.jpg" alt="">
              </div>
            </a>
          </li>
      </ul>
<?php endforeach ?>
      </div>
      <div class="widgets">
 
      </div>
    </div>
    <div class="content">
      <div class="article">
        <div class="breadcrumb">
          <dl>
            <dt>当前位置：</dt>
            <dd><a href="list.php?id=<?php echo $posts['categories_id'] ?>"><?php echo $posts['category_name'] ?></a></dd>
            <dd></dd>
          </dl>
        </div>
        <h2 class="title">
          <a href="javascript:;"><?php echo $posts['title'] ?></a>
        </h2>
        <p><?php echo $posts['content'] ?></p>
        <div class="meta">
          <span><?php echo $posts['user_name'] ?> 发布于 <?php echo $posts['created'] ?></span>
          <span>分类: <a href="list.php?id=<?php echo $posts['categories_id'] ?>:;"><?php  echo $posts['category_name'] ?></a></span>
          <span>阅读: (<?php echo $views2 ?>)</span>
              <a href="javascript:;" class="like">
                <i class="fa fa-thumbs-up"></i>
                <span style="margin: 0">赞</span>
                <span class='likes' data-num="<?php echo $posts['posts_id'] ?>"><?php echo $posts['posts_likes'] ?></span>
              </a>
        </div>
      </div>
      <div class="panel hots">
       
      </div>
    </div>
    <div class="footer">
      <p>© 2016 XIU主题演示 本站主题由 themebetter 提供</p>
    </div>
  </div>
</body>
  <script src="static/assets/vendors/jquery/jquery.js"></script>
  <script type="text/javascript">
    //点赞
$('.like').on('click',function(){
  var goodNum=(Number($(this).find('span.likes').text())+1)
  var posts_id=$(this).find('span.likes').data("num")
  $(this).find('span.likes').text(goodNum)

      $.get('/likes.php', { likes: goodNum, posts_id:posts_id}, function (res) {
          // 希望 res => 这个邮箱对应的头像地址
          if (!res) return
              alert("谢谢点赞")
          })
})
  </script>
</html>
