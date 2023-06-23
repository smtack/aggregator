<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="heading">
    <h2 id="heading">All Categories</h2>
  </div>

  <?php foreach($categories_list as $category): ?>
    <div class="post">
      <h3><a href="/category/<?=escape($category->category_id)?>"><?=escape($category->category_name)?></a></h3>
      <p><?=escape($category->category_description)?></p>
      <h5>Created by
        <?php if($category->user_username): ?>
          <a href="/profile/<?=escape($category->user_username)?>"><?=escape($category->user_username)?></a>
        <?php else: ?>
          [Deleted]
        <?php endif; ?>

        on <?=escape(date('l j F Y H:i', strtotime($category->category_created)))?>
      </h6>
    </div>
  <?php endforeach; ?>
</div>