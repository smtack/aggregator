<div class="header">
  <h1><a href="/index">aggregator</a></h1>
</div>

<div class="form">
  <h2>Sign Up</h2>
  
  <form action="/register" method="POST">
    <div class="form-group">
      <?php error('form_error'); ?>
    </div>
    <div class="form-group">
      <input type="text" name="user_username" placeholder="Username">
    </div>
    <div class="form-group">
      <input type="text" name="user_email" placeholder="Email">
    </div>
    <div class="form-group">
      <input type="password" name="user_password" placeholder="Password">
    </div>
    <div class="form-group">
      <input type="password" name="confirm_password" placeholder="Confirm Password">
    </div>
    <div class="form-group">
      <input type="hidden" name="token" value="<?=generate('token')?>">
      <input type="submit" name="signup" value="Sign Up">
    </div>
    <div class="form-group">
      <p>Already have an account? <a href="/login">Log In</a></p>
    </div>
  </form>
</div>

<div class="page-footer">
  <p>&copy; <?=date('Y')?> aggregator</p>
</div>