<!-- 也可以使用 $_SERVER['PHP_SELF'] 取代 $current_page -->
<?php

// 因为这个 sidebar.php 是被 index.php 载入执行，所以 这里的相对路径 是相对于 index.php
// 如果希望根治这个问题，可以采用物理路径解决
require_once '../functions.php';
$current_page = isset($current_page) ? $current_page : '';
$current_user = xiu_get_current_user();

?>
<div class="aside">
  <div class="profile">
    <img class="avatar" src="<?php echo $current_user['avatar']; ?>">
    <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
  </div>
  <ul class="nav">
    <li<?php echo $current_page === 'index' ? ' class="active"' : '' ?>>
      <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
    </li>
    <?php $menu_posts = array('posts', 'post-add', 'categories'); ?>
    <li<?php echo in_array($current_page, $menu_posts) ? ' class="active"' : '' ?>>
      <a href="#menu-posts"<?php echo in_array($current_page, $menu_posts) ? '' : ' class="collapsed"' ?> data-toggle="collapse">
        <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
      </a>
      <ul id="menu-posts" class="collapse<?php echo in_array($current_page, $menu_posts) ? ' in' : '' ?>">
        <li<?php echo $current_page === 'posts' ? ' class="active"' : '' ?>><a href="/admin/posts.php">所有文章</a></li>
        <li<?php echo $current_page === 'post-add' ? ' class="active"' : '' ?>><a href="/admin/post-add.php">写文章</a></li>
        <li<?php echo $current_page === 'categories' ? ' class="active"' : '' ?>><a href="/admin/categories.php">分类目录</a></li>
      </ul>
    </li>
    <li<?php echo $current_page === 'comments' ? ' class="active"' : '' ?>>
      <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
    </li>
    <li<?php echo $current_page === 'users' ? ' class="active"' : '' ?>>
      <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
    </li>
    <?php $menu_settings = array('nav-menus', 'slides', 'settings'); ?>
    <li<?php echo in_array($current_page, $menu_settings) ? ' class="active"' : '' ?>>
      <a href="#menu-settings"<?php echo in_array($current_page, $menu_settings) ? '' : ' class="collapsed"' ?> data-toggle="collapse">
       
    </li>
  </ul>
</div>
