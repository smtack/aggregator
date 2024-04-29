<?php flash('user_message'); ?>

<?php require_once VIEW_ROOT . '/includes/sidebar.php'; ?>

<div class="posts">
  <div class="form">
    <h2>Update Profile</h2>

    <form action="<?= base_url('update-profile') ?>" method="POST">
      <div class="form-group">
        <?php error('form_error'); ?>
      </div>
      <div class="form-group">
        <input type="text" name="user_username" value="<?= escape($user->user_username) ?>" disabled>
      </div>
      <div class="form-group">
        <input type="text" name="user_email" value="<?= escape($user->user_email) ?>" placeholder="Email">
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?= generate('token') ?>">
        <input type="submit" name="update" value="Update">
      </div>
    </form>
  </div>

  <div class="form">
    <h2>Change Password</h2>

    <form action="<?= base_url('update-password') ?>" method="POST">
      <div class="form-group">
        <?php error('password_error'); ?>
      </div>
      <div class="form-group">
        <input type="password" name="confirm_password" placeholder="Confirm Password">
      </div>
      <div class="form-group">
        <input type="password" name="new_password" placeholder="New Password">
      </div>
      <div class="form-group">
        <input type="password" name="confirm_new_password" placeholder="Confirm New Password">
      </div>
      <div class="form-group">
        <input type="hidden" name="password-token" value="<?= generate('password-token') ?>">
        <input type="submit" name="change_password" value="Change Password">
      </div>
    </form>
  </div>

  <div class="form">
    <h2>Delete Profile</h2>

    <form action="<?= base_url('delete-profile') ?>" method="POST">
      <div class="form-group">
        <?php error('delete_error'); ?>
      </div>
      <div class="form-group">
        <input type="password" name="user_password" placeholder="Enter Password">
      </div>
      <div class="form-group">
        <input type="hidden" name="delete-token" value="<?= generate('delete-token') ?>">
        <input type="submit" name="delete_profile" value="Delete Profile">
      </div>
    </form>
  </div>
</div>