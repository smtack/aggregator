<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="heading">
    <h1 id="heading">Search: <?=isset($keywords) ? str_replace('%', '', $keywords) : ''?></h1>

    <ul>
      <li id="toggle-post-results"><a href="#">Posts</a></li>
      <li id="toggle-category-results"><a href="#">Categories</a></li>
      <li id="toggle-user-results"><a href="#">Users</a></li>
    </ul>
  </div>
  <div class="post-results">
    <?php if(!$post_results): ?>
      <h3 class="message">No posts found</h3>
    <?php else: ?>
      <?php foreach($post_results as $post_result): ?>
        <div class="post">
          <span class="vote">
            <?php $points = $this->model->getPoints($post_result->post_id); ?>

            <?php if($user): ?>
              <?php if(!findValue($points, 'point_user', $user->user_id)): ?>
                <a href="/vote/<?=$post_result->post_id?>"><img src="/Resource/public/img/vote.svg" alt="Vote"></a>
              <?php else: ?>
                <a href="/unvote/<?=$post_result->post_id?>"><img src="/Resource/public/img/unvote.svg" alt="Vote"></a>
              <?php endif; ?>
            <?php else: ?>
              <img src="/Resource/public/img/vote.svg" alt="Vote">
            <?php endif; ?>
            
            <h4><?=count($points)?></h4>
          </span>

          <h3>
            <?php if(isset($post_result->post_url)): ?>
              <a href="<?=$post_result->post_url?>"><?=escape($post_result->post_title)?></a>
            <?php else: ?>
              <a href="/post/<?=escape($post_result->post_id)?>"><?=escape($post_result->post_title)?></a>
            <?php endif; ?>
          </h3>

          <h5>in <a href="/category/<?=escape($post_result->category_id)?>"><?=escape($post_result->category_name)?></a> by
            <?php if($post_result->user_username): ?>
              <a href="/profile/<?=escape($post_result->user_username)?>"><?=escape($post_result->user_username)?></a>
            <?php else: ?>
              [Deleted]
            <?php endif; ?>

            on <?=escape(date('l j F Y H:i', strtotime($post_result->post_date)))?>
          </h5>

          <h6><a href="/post/<?=escape($post_result->post_id)?>#comments">Comments</a></h6>

          <?php if($user && $post_result->post_by === $user->user_id): ?>
            <h6><a href="/edit/<?=$post_result->post_id?>">Edit</a></h6>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="category-results">
    <?php if(!$category_results): ?>
      <h3 class="message">No categories found</h3>
    <?php else: ?>
      <?php foreach($category_results as $category_result): ?>
        <div class="post">
          <h3><a href="/category/<?=escape($category_result->category_id)?>"><?=escape($category_result->category_name)?></a></h3>
          <h5>Created by
            <?php if($category_result->user_username): ?>
              <a href="/profile/<?=escape($category_result->user_username)?>"><?=escape($category_result->user_username)?></a>
            <?php else: ?>
              [Deleted]
            <?php endif; ?>

            on <?=escape(date('l j F Y H:i', strtotime($category_result->category_created)))?>
          </h5>
          <p><?=escape($category_result->category_description)?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="user-results">
    <?php if(!$user_results): ?>
      <h3 class="message">No users found</h3>
    <?php else: ?>
      <?php foreach($user_results as $user_result): ?>
        <div class="post">
          <h3><a href="/profile/<?=escape($user_result->user_username)?>"><?=escape($user_result->user_username)?></a></h3>
          <h5>Joined on <?=escape(date('l j F Y H:i', strtotime($user_result->user_joined)))?></h5>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>