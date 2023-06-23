<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <?php if(!$posts): ?>
    <h3 class="message">No posts in this category</h4>
  <?php else: ?>
    <?php foreach($posts as $post): ?>
      <div class="post">
        <span class="vote">
          <?php $points = $this->model->getPoints($post->post_id); ?>

          <?php if($user): ?>
            <?php if(!findValue($points, 'point_user', $user->user_id)): ?>
              <a href="/vote/<?=$post->post_id?>"><img src="/Resource/public/img/vote.svg" alt="Vote"></a>
            <?php else: ?>
              <a href="/unvote/<?=$post->post_id?>"><img src="/Resource/public/img/unvote.svg" alt="Vote"></a>
            <?php endif; ?>
          <?php else: ?>
            <img src="/Resource/public/img/vote.svg" alt="Vote">
          <?php endif; ?>
          
          <h4><?=count($points)?></h4>
        </span>

        <h3>
          <?php if(isset($post->post_url)): ?>
            <a href="<?=$post->post_url?>"><?=escape($post->post_title)?></a>
          <?php else: ?>
            <a href="/post/<?=escape($post->post_id)?>"><?=escape($post->post_title)?></a>
          <?php endif; ?>
        </h3>

        <h5>By
          <?php if($post->user_username): ?>
            <a href="/profile/<?=escape($post->user_username)?>"><?=escape($post->user_username)?></a>
          <?php else: ?>
            [Deleted]
          <?php endif; ?>

          on <?=escape(date('l j F Y H:i', strtotime($post->post_date)))?>
        </h5>
        
        <h6><a href="/post/<?=escape($post->post_id)?>#comments">Comments</a></h6>
        
        <?php if($user && $post->post_by === $user->user_id): ?>
          <h6><a href="/edit/<?=$post->post_id?>">Edit</a></h6>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>