<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="heading">
    <h2 id="heading">All Posts</h2>
  </div>

  <?php foreach($posts as $post): ?>
    <div class="post">
      <span class="vote">
        <?php $points = $this->model->getPoints($post->post_id); ?>

        <?php if($user): ?>
          <?php if(!findValue($points, 'point_user', $user->user_id)): ?>
            <a href="<?= base_url('vote') ?>/<?= $post->post_id ?>"><img src="<?= base_url('public/img/vote.svg') ?>" alt="Vote"></a>
          <?php else: ?>
            <a href="<?= base_url('unvote') ?>/<?= $post->post_id ?>"><img src="<?= base_url('public/img/unvote.svg') ?>" alt="Unvote"></a>
          <?php endif; ?>
        <?php else: ?>
          <img src="<?= base_url('public/img/vote.svg') ?>" alt="Vote">
        <?php endif; ?>
        
        <h4><?= count($points) ?></h4>
      </span>

      <h3>
        <?php if(isset($post->post_url)): ?>
          <a href="<?= $post->post_url ?>"><?= escape($post->post_title) ?></a>
        <?php else: ?>
          <a href="<?= base_url('post') ?>/<?= escape($post->post_id) ?>"><?= escape($post->post_title) ?></a>
        <?php endif; ?>
      </h3>

      <h5>
        in
          <a href="<?= base_url('category') ?>/<?= escape($post->category_id) ?>"><?= escape($post->category_name) ?></a>
        by
          <?php if($post->user_username): ?>
            <a href="<?= base_url('profile') ?>/<?= escape($post->user_username) ?>"><?= escape($post->user_username) ?></a>
          <?php else: ?>
            [Deleted]
          <?php endif; ?>
        on
          <?= escape(date('l j F Y H:i', strtotime($post->post_date))) ?>
      </h5>

      <h6><a href="<?= base_url('post') ?>/<?= escape($post->post_id) ?>#comments">Comments</a></h6>

      <?php if($user && $post->post_by === $user->user_id): ?>
        <h6><a href="<?= base_url('edit') ?>/<?= $post->post_id ?>">Edit</a></h6>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <div class="pagination">
    <?php if($p > 1): ?>
      <a id="prev" href="?p=<?php echo $p - 1 ?>">&#129168; Previous</a>
    <?php endif; ?>
    <?php if($p < $pages): ?>
      <a id="next" href="?p=<?php echo $p + 1 ?>">Next &#129170;</a>
    <?php endif; ?>
  </div>
</div>