<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="form">
    <h2>Create Post</h2>

    <form action="/create-post" method="POST">
      <div class="form-group">
        <?php error('form_error'); ?>
      </div>
      <div class="form-group">
        <select name="post_category">
          <?php foreach($categories as $category): ?>
            <option value="<?=escape($category->category_id)?>"><?=escape($category->category_name)?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <input type="text" name="post_title" placeholder="Post Title">
      </div>
      <div class="form-group">
        <input type="text" name="post_url" placeholder="Post URL (Optional)">
      </div>
      <div class="form-group">
        <textarea name="post_text" placeholder="Post Text (Optional)"></textarea>
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?=generate('token')?>">
        <input type="submit" name="create_post" value="Create Post">
      </div>
    </form>
  </div>
</div>