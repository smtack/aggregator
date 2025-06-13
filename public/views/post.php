<?php flash('post_message'); ?>

<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="post">
    <span class="vote">
      <?php if($user): ?>
        <?php if(!$this->model->hasVoted($user->user_id, $post_data->post_id)): ?>
          <a href="<?= base_url('vote') ?>/<?= $post_data->post_id ?>"><img src="/Resource/img/vote.svg" alt="Vote"></a>
        <?php else: ?>
          <a href="<?= base_url('unvote') ?>/<?= $post_data->post_id ?>"><img src="/Resource/img/unvote.svg" alt="Unvote"></a>
        <?php endif; ?>
      <?php else: ?>
        <img src="/Resource/img/vote.svg" alt="Vote">
      <?php endif; ?>
      
      <h4><?= $post_data->pts ?></h4>
    </span>

    <h3>
      <?php if(isset($post_data->post_url)): ?>
        <a href="<?= $post_data->post_url ?>"><?= escape($post_data->post_title) ?></a>
      <?php else: ?>
        <?= escape($post_data->post_title) ?>
      <?php endif; ?>
    </h3>

    <h5>
      By
        <?php if($post_data->user_username): ?>
          <a href="<?= base_url('profile') ?>/<?= escape($post_data->user_username) ?>"><?= escape($post_data->user_username) ?></a>
        <?php else: ?>
          [Deleted]
        <?php endif; ?>
      on
        <?= escape(date('l j F Y \a\t H:i', strtotime($post_data->post_date))) ?>
      in
        <a href="<?= base_url('category') ?>/<?= $post_data->category_id ?>"><?= escape($post_data->category_name) ?></a>
    </h5>

    <p><?= escape($post_data->post_text) ?></p>

    <?php if($user && $post_data->post_by === $user->user_id): ?>
      <h6><a href="<?= base_url('edit') ?>/<?= $post_data->post_id ?>">Edit</a></h6>
    <?php endif; ?>
  </div>
  
  <?php if($user): ?>
    <div class="submit-comment">
      <div class="form">
        <h2>Comment</h2>

        <form action="<?= base_url('comment') ?>/<?= escape($post_data->post_id) ?>" method="POST">
          <div class="form-group">
            <?php flash('form_error'); ?>
          </div>
          <div class="form-group">
            <textarea name="comment_text" placeholder="Comment"></textarea>
          </div>
          <div class="form-group">
            <input type="hidden" name="token" value="<?= generate('token') ?>">
            <input type="submit" name="create_comment" value="Comment">
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <div class="comments" id="comments">
    <?php foreach($comments as $comment): ?>
      <div class="post">
        <h5>
          By
            <?php if($comment->user_username): ?>
              <a href="<?= base_url('profile') ?>/<?= escape($comment->user_username) ?>"><?= escape($comment->user_username) ?></a>
            <?php else: ?>
              [Deleted]
            <?php endif; ?>
          on
            <?= escape(date('l j F Y \a\t H:i', strtotime($comment->comment_date))) ?>
        </h5>
        
        <p><?= escape($comment->comment_text) ?></p>

        <?php if($user && $comment->comment_by === $user->user_id): ?>
          <h6><a href="<?= base_url('delete-comment') ?>/<?= $comment->comment_id ?>">Delete</a></h6>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>