<?php flash('post_message'); ?>

<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="form">
    <h2>Edit Post</h2>

    <form action="/edit-post/<?=$post_data->post_id?>" method="POST">
      <div class="form-group">
        <?php error('form_error'); ?>
      </div>
      <div class="form-group">
        <select name="post_category">
          <?php foreach($categories as $category): ?>
            <option value="<?=escape($category->category_id)?>" disabled><?=escape($category->category_name)?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <input type="text" name="post_title" value="<?=$post_data->post_title?>">
      </div>
      <div class="form-group">
        <input type="text" name="post_url" value="<?=$post_data->post_url?>" placeholder="Post URL (Optional)">
      </div>
      <div class="form-group">
        <textarea name="post_text" placeholder="Post Text (Optional)"><?=$post_data->post_text?></textarea>
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?=generate('token')?>">
        <input type="submit" name="edit_post" value="Edit Post">
      </div>
    </form>
  </div>

  <div class="form">
    <h2>Delete Post</h2>

    <form action="/delete-post/<?=$post_data->post_id?>" method="POST">
      <div class="form-group">
        <?php error('delete_error'); ?>
      </div>
      <div class="form-group">
        <input type="hidden" name="delete-token" value="<?=generate('delete-token')?>">
        <input type="submit" name="delete_post" value="Delete Post">
      </div>
    </form>
  </div>
</div>