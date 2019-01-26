<?php 

require_once 'functions.php';
// 接收筛选参数
// ==================================

$where = '1 = 1';
$search = '';

// 分类筛选
if (isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id = ' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}
//有这个状态值并且这个状态值不为all的时候;
//将这个get的值
if (isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}

// $where => "1 = 1 and posts.category_id = 1 and posts.status = 'published'"
// $search => "&category=1&status=published"

// 处理分页参数
// =========================================

$size = 4;
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$lastPage=$page-1;
$nextPage=$page+1;
// 必须 >= 1 && <= 总页数

// $page = $page < 1 ? 1 : $page;
if ($page < 1) {
  // 跳转到第一页
  header('Location: /index.php?page=1' . $search);

}

// 只要是处理分页功能一定会用到最大的页码数
$total_count = (int)xiu_fetch_one("select count(1) as count from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where posts.status='published';")['count'];
$total_pages = (int)ceil($total_count / $size);

// $page = $page > $total_pages ? $total_pages : $page;
if ($page > $total_pages) {
  // 跳转到第最后页
  header('Location: /index.php?page=' . $total_pages . $search);
}

// 获取全部数据
// ===================================

// 计算出越过多少条
$offset = ($page - 1) * $size;
$posts = xiu_fetch_all("select
  posts.id as posts_id,
  categories.id as categories_id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  users.slug as users_slug,
  posts.created,
  posts.status,
  posts.slug,
  posts.content,
  posts.views,
  posts.likes,
  posts.feature
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where posts.status='published'
order by posts.created desc
limit {$offset}, {$size};");
// 查询全部的分类数据
$categories = xiu_fetch_all('select * from categories;');


//获取随机推荐
$postAll = xiu_fetch_all('select * from posts;');
$arr1=array_rand($postAll,5);
//重组数组
foreach($arr1 as $val){
    $data_last[]=$postAll[$val];
}


// 处理分页页码
// ===============================

$visiables = 5;

// 计算最大和最小展示的页码
$begin = $page - ($visiables - 1) / 2;
$end = $begin + $visiables - 1;

// 重点考虑合理性的问题
// begin > 0  end <= total_pages
$begin = $begin < 1 ? 1 : $begin; // 确保了 begin 不会小于 1
$end = $begin + $visiables - 1; // 因为 50 行可能导致 begin 变化，这里同步两者关系
$end = $end > $total_pages ? $total_pages : $end; // 确保了 end 不会大于 total_pages
$begin = $end - $visiables + 1; // 因为 52 可能改变了 end，也就有可能打破 begin 和 end 的关系
$begin = $begin < 1 ? 1 : $begin; // 确保不能小于 1

// 处理数据格式转换
// ===========================================

/**
 * 转换状态显示
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status ($status) {
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
  );
  return isset($dict[$status]) ? $dict[$status] : '未知';
}

/**
 * 转换时间格式
 * @param  [type] $created [description]
 * @return [type]          [description]
 */
function convert_date ($created) {
  // => '2017-07-01 08:08:00'
  // 如果配置文件没有配置时区
  // date_default_timezone_set('PRC');
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
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
    <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
<style type="text/css">

</style>
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
        <a href="admin/login.php">后台</a> | <a href="/index.html">个人主页</a>
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
      <div class="swipe">
        <ul class="swipe-wrapper">
          <li>
            <a href="#">
              <img src="static/uploads/1.png" style="width: 860px;height: 420px">
              <span>动漫人物轮播1</span>
            </a>
          </li>
          <li>
            <a href="#">
              <img src="static/uploads/2.jpg" style="width: 860px;height: 420px">
              <span>动漫人物轮播2</span>
            </a>
          </li>
          <li>
            <a href="#">
              <img src="static/uploads/3.png" style="width: 860px;height: 420px">
              <span>动漫人物轮播3</span>
            </a>
          </li>
          <li>
            <a href="#">
              <img src="static/uploads/4.png" style="width: 860px;height: 420px">
              <span>动漫人物轮播4</span>
            </a>
          </li>
        </ul>
        <p class="cursor"><span class="active"></span><span></span><span></span><span></span></p>
        <a href="javascript:;" class="arrow prev"><i class="fa fa-chevron-left"></i></a>
        <a href="javascript:;" class="arrow next"><i class="fa fa-chevron-right"></i></a>
      </div>


      <h3>最新发布</h3>

             
      <?php foreach ($posts as $item): ?>


      <div class="panel new">
        <div class="entry">
        <div class="entry">
          <div class="head">
            <span class="sort"><?php echo $item['category_name'] ?></span>
            <a href="detail.php?posts_id=<?php echo $item['posts_id'] ?>&id=<?php echo $item['categories_id']?>"><?php echo $item['title'] ?></a>
          </div>
          <div class="main">
            <p class="info"><?php  echo $item['users_slug']?> 发表于 <?php echo $item['created'] ?></p>
            <p class="brief"><?php echo $item['content'] ?></p>
            <p class="extra">
              <span class="reading">阅读(<?php echo $item['views'] ?>)</span>
              <span class="comment">评论(0)</span>
              <a href="javascript:;" class="like">
                <i class="fa fa-thumbs-up"></i>
                <span>赞</span>
                <span class='likes' data-num="<?php echo $item['posts_id'] ?>"><?php echo $item['likes'] ?></span>
              </a>
              <a href="javascript:;" class="tags">
                分类：<span><?php echo $item['category_name'] ?></span>
              </a>
            </p>
            <a href="javascript:;" class="thumb">
              <img src="<?php echo $item['feature'] ?>" alt="">
            </a>
          </div>
        </div>
      </div>
    </div>
      <?php endforeach ?>
        <div id="fenye">
      		 <ul class="pagination pagination-sm pull-right fenye1">
	          <li><a href="?<?php echo("page=$lastPage") ?>">上一页</a></li>
	          <?php for ($i = $begin; $i <= $end; $i++): ?>
	          <li<?php echo $i === $page ? ' class="active"' : '' ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
	          <?php endfor ?>
	          <li><a href="?<?php echo("page=$nextPage") ?>">下一页</a></li>
	        </ul>
      </div>
    <div class="footer">
      <p>© 2016 XIU主题演示 本站主题由 themebetter 提供</p>
    </div>
  </div>
  <script src="static/assets/vendors/jquery/jquery.js"></script>
  <script src="static/assets/vendors/swipe/swipe.js"></script>
  <script>
    //
    var swiper = Swipe(document.querySelector('.swipe'), {
      auto: 3000,
      transitionEnd: function (index) {
        // index++;

        $('.cursor span').eq(index).addClass('active').siblings('.active').removeClass('active');
      }
    });

    // 上/下一张
    $('.swipe .arrow').on('click', function () {
      var _this = $(this);

      if(_this.is('.prev')) {
        swiper.prev();
      } else if(_this.is('.next')) {
        swiper.next();
      }
    })

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
</body>
</html>