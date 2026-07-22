<?php require_once view('includes/sidebar'); ?>

<div class="posts">
  <div class="form">
    <h2>Create Category</h2>

    <form action="<?= base_url('/new-category') ?>" method="POST">
      <?php if (!empty($flash['error'])): ?>
        <div class="form-group">
          <?= escape($flash['error']) ?>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <input type="text" name="category_name" placeholder="Category Name">
      </div>
      <div class="form-group">
        <textarea name="category_description" placeholder="Category Description"></textarea>
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?= $this->hash->generate('token') ?>">
        <input type="submit" name="create_category" value="Create Category">
      </div>
    </form>
  </div>
</div>