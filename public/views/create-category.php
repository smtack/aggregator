<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="form">
    <h2>Create Category</h2>

    <form action="/new-category" method="POST">
      <div class="form-group">
        <?php error('form_error'); ?>
      </div>
      <div class="form-group">
        <input type="text" name="category_name" placeholder="Category Name">
      </div>
      <div class="form-group">
        <textarea name="category_description" placeholder="Category Description"></textarea>
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?=generate('token')?>">
        <input type="submit" name="create_category" value="Create Category">
      </div>
    </form>
  </div>
</div>