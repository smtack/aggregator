<?php if (!empty($flash['message'])): ?>
  <div class="flash"><p><?= escape($flash['message']) ?></p></div>
<?php endif; ?>

<?php require_once view('includes/sidebar'); ?>

<div class="posts">
  <div class="form">
    <h2>Update Profile</h2>

    <form action="<?= base_url('/update-profile') ?>" method="POST">
      <?php if (!empty($flash['error'])): ?>
        <div class="form-group">
          <?= escape($flash['error']) ?>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <input type="text" name="user_username" value="<?= escape($user->user_username) ?>" disabled>
      </div>
      <div class="form-group">
        <input type="text" name="user_email" value="<?= escape($user->user_email) ?>" placeholder="Email">
      </div>
      <div class="form-group">
        <input type="hidden" name="token" value="<?= $this->hash->generate('token') ?>">
        <input type="submit" name="update" value="Update">
      </div>
    </form>
  </div>

  <div class="form">
    <h2>Change Password</h2>

    <form action="<?= base_url('/update-password') ?>" method="POST">
      <?php if (!empty($flash['error'])): ?>
        <div class="form-group">
          <?= escape($flash['error']) ?>
        </div>
      <?php endif; ?>
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
        <input type="hidden" name="password-token" value="<?= $this->hash->generate('password-token') ?>">
        <input type="submit" name="change_password" value="Change Password">
      </div>
    </form>
  </div>

  <div class="form">
    <h2>Delete Profile</h2>

    <form action="<?= base_url('/delete-profile') ?>" method="POST">
      <?php if (!empty($flash['error'])): ?>
        <div class="form-group">
          <?= escape($flash['error']) ?>
        </div>
      <?php endif; ?>
      <div class="form-group">
        <input type="password" name="user_password" placeholder="Enter Password">
      </div>
      <div class="form-group">
        <input type="hidden" name="delete-token" value="<?= $this->hash->generate('delete-token') ?>">
        <input type="submit" name="delete_profile" value="Delete Profile">
      </div>
    </form>
  </div>
</div>