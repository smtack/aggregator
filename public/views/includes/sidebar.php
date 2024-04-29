<div class="sidebar">
  <h1><a href="<?= base_url() ?>">aggregator</a></h1>

  <?php if($user): ?>
    <div class="user-info">
      <p>Welcome <a href="<?= base_url('profile') ?>/<?=escape($user->user_username)?>"><?=escape($user->user_username)?></a></p>
      
      <img class="toggle-menu" src="<?= base_url('public/img/menu.svg') ?>" alt="Toggle Menu">
    </div>
  <?php else: ?>
    <p>Welcome to aggregator. <a href="<?= base_url('signup') ?>">Sign Up</a> or <a href="<?= base_url('login') ?>">Login</a>.</p>
  <?php endif; ?>

  <div class="menu">
    <ul>
      <a href="<?= base_url('profile') ?>/<?= escape($user->user_username) ?>"><li>Your Profile</li></a>
      <a href="<?= base_url('update') ?>"><li>Update Profile</li></a>
      <a href="<?= base_url('logout') ?>"><li>Log Out</li></a>
    </ul>
  </div>

  <div class="form">
    <form action="<?= base_url('search') ?>" method="POST">
      <div class="form-group search">
        <input type="text" name="s" placeholder="Search" value="<?= isset($keywords) ? $keywords : '' ?>">
      </div>
    </form>
  </div>

  <?php if($user): ?>
    <a href="<?= base_url('create-category') ?>"><button>Create Category</button></a>
    <a href="<?= base_url('new-post') ?>"><button>New Post</button></a>
  <?php endif; ?>
  
  <?php if(isset($category_data)): ?>
    <div class="category-info">
      <h2><?= escape($category_data->category_name) ?></h2>
      <h5><?= count($follow_data) ?> Following</h5>
      <p><?= escape($category_data->category_description) ?></p>

      <?php if($user): ?>
        <?php if(!findValue($follow_data, 'follow_user', $user->user_id)): ?>
          <a href="<?= base_url('follow') ?>/<?= escape($category_data->category_id) ?>"><button>Follow</button></a>
        <?php else: ?>
          <a href="<?= base_url('unfollow') ?>/<?= escape($category_data->category_id) ?>"><button>Unfollow</button></a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if(isset($profile)): ?>
    <div class="category-info">
      <h1><?= escape($profile->user_username) ?>'s Profile</h1>
    </div>
  <?php endif; ?>

  <?php if($user): ?>
    <h2>Your Categories</h2>

    <?php if(!$categories): ?>
      <span>You aren't following any categories.</span>
    <?php else: ?>
      <ul>    
        <?php foreach($categories as $category): ?>
          <a href="<?= base_url('category') ?>/<?= escape($category->category_id) ?>"><li><?= escape($category->category_name) ?></li></a>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>

  <a href="<?= base_url('categories') ?>"><button>All Categories</button></a>
  <a href="<?= base_url('all') ?>"><button>All Posts</button></a>

  <div class="footer">
    <p>&copy; <?= date('Y') ?> aggregator</p>
  </div>
</div>