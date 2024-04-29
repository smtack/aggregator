<?php
class Controller {
  private $model;
  private $router;

  public function __construct() {
    $this->model = new Model();

    $this->router = new Router();

    $queryParams = false;

    if(strlen($_GET['query']) > 0) {
      $queryParams = explode("/", $_GET['query']);
    }

    $page = $_GET['page'];

    $endpoint = $this->router->lookup($page);

    if($endpoint === false) {
      $this->redirect(404);
    } else {
      $this->$endpoint($queryParams);
    }
  }

  private function redirect($location) {
    switch($location) {
      case 404:
        header('HTTP/1.0 404 Not Found');

        include_once VIEW_ROOT . '/errors/404.php';

        exit();
      break;
      case 'refer':
        header('Location: ' . $_SERVER['HTTP_REFERER']);

        exit();
      default:
        header('Location: ' . BASE_URL . $location);

        exit();
      break;
    }
  }

  private function loadView($view, $data = null) {
    if(is_array($data)) {
      extract($data);
    }

    require_once '../public/views/' . $view . '.php';
  }

  private function loadPage($view, $data = null) {
    $this->loadView('includes/header', $data);

    $this->loadView($view, $data);

    $this->loadView('includes/footer');
  }

  private function index() {
    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

    $limit = 25;

    $start = ($p > 1) ? ($p * $limit) - $limit : 0;

    if($user = $this->model->checkUser()) {
      $posts = $this->model->getHomepagePosts($user->user_id, $start, $limit);
    } else {
      $posts = $this->model->getPosts($start, $limit);
    }

    $total = $this->model->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

    $pages = ceil($total / $limit);

    $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

    $this->loadPage('index', array('user' => $user, 'p' => $p, 'pages' => $pages, 'posts' => $posts, 'categories' => $categories));
  }

  private function signup() {
    if($user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      $page_title = "Sign Up";

      $this->loadPage('signup', array('user' => $user, 'page_title' => $page_title));
    }
  }

  private function register() {
    if($user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('signup');
    } else if(empty($_POST['user_username']) || empty($_POST['user_email']) || empty($_POST['user_password']) || empty($_POST['confirm_password'])) {
      error('form_error', 'Fill in all fields');

      $this->redirect('signup');
    } else if($this->model->db->exists('users', array('user_username' => $_POST['user_username']))) {
      error('form_error', 'This username is taken');

      $this->redirect('signup');
    } else if($this->model->db->exists('users', array('user_email' => $_POST['user_email']))) {
      error('form_error', 'This email address is already in use');

      $this->redirect('signup');
    } else if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
      error('form_error', 'Enter a valid email address');

      $this->redirect('signup');
    } else if($_POST['user_password'] !== $_POST['confirm_password']) {
      error('form_error', 'Passwords must match');

      $this->redirect('signup');
    } else {
      $signup = [
        'user_username' => escape($_POST['user_username']),
        'user_email' => escape($_POST['user_email']),
        'user_password' => password_hash($_POST['user_password'], PASSWORD_BCRYPT)
      ];

      if($this->model->createUser($signup)) {
        flash('user_message', 'Welcome to aggregator, ' . $signup['user_username']);

        $this->redirect('index');
      } else {
        error('form_error', 'Unable to sign up. Try again later.');

        $this->redirect('signup');
      }
    }
  }

  private function login() {
    if($user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      $page_title = "Log In";

      $this->loadPage('login', array('user' => $user, 'page_title' => $page_title));
    }
  }

  private function authenticate() {
    if($user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('login');
    } else if(empty($_POST['user_username']) || empty($_POST['user_password'])) {
      error('form_error', 'Enter your Username and Password');

      $this->redirect('login');
    } else {
      $login = [
        'user_username' => escape($_POST['user_username']),
        'user_password' => $_POST['user_password']
      ];

      if($this->model->login($login)) {
        flash('user_message', 'Welcome back, ' . $login['user_username']);

        $this->redirect('index');
      } else {
        error('form_error', 'Username or Password Incorrect');

        $this->redirect('login');
      }
    }
  }

  private function logout() {
    $this->model->logout($_COOKIE['Auth']);

    $this->redirect('index');
  }

  private function update() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      $categories = $this->model->getUsersFollows($user->user_id);

      $page_title = "Update Profile";

      $this->loadPage('update', array('user' => $user, 'categories' => $categories, 'page_title' => $page_title));
    }
  }

  private function updateProfile() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('update');
    } else if(empty($_POST['user_email'])) {
      error('form_error', 'Enter a new email address');

      $this->redirect('update');
    } else if($this->model->db->exists('users', array('user_email' => $_POST['user_email'])) && $_POST['user_email'] !== $user->user_email) {
      error('form_error', 'This email address is already in use');

      $this->redirect('update');
    } else if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
      error('form_error', 'Enter a valid email address');

      $this->redirect('update');
    } else {
      $update = ['user_email' => escape($_POST['user_email'])];

      if($this->model->updateProfile($update, $user->user_id)) {
        flash('user_message', 'Profile Updated');

        $this->redirect('update');
      } else {
        error('form_error', 'Unable to update profile');

        $this->redirect('update');
      }
    }
  }

  private function updatePassword() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['password-token'], 'password-token')) {
      error('form_error', 'Token Failure');

      $this->redirect('update');
    } else if(empty($_POST['confirm_password']) || empty($_POST['new_password']) || empty($_POST['confirm_new_password'])) {
      error('password_error', 'Fill in all fields');

      $this->redirect('update');
    } else if(!password_verify($_POST['confirm_password'], $user->user_password)) {
      error('password_error', 'Enter current password correctly');

      $this->redirect('update');
    } else if($_POST['new_password'] !== $_POST['confirm_new_password']) {
      error('password_error', 'Passwords must match');

      $this->redirect('update');
    } else {
      $password = ['user_password' => password_hash($_POST['new_password'], PASSWORD_BCRYPT)];
    
      if($this->model->changePassword($password, $user->user_id)) {
        flash('user_message', 'Password Updated');

        $this->redirect('update');
      } else {
        error('password_error', 'Unable to change password');

        $this->redirect('update');
      }
    }
  }

  private function deleteProfile() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['delete-token'], 'delete-token')) {
      error('form_error', 'Token Failure');

      $this->redirect('update');
    } else if(empty($_POST['user_password'])) {
      error('delete_error', 'Enter your password');

      $this->redirect('update');
    } else if(!password_verify($_POST['user_password'], $user->user_password)) {
      error('delete_error', 'Enter your password correctly');

      $this->redirect('update');
    } else {
      if($this->model->deleteProfile($user->user_id)) {
        $this->logout();
      } else {
        error('delete_error', 'Unable to delete profile');

        $this->redirect('update');
      }
    }
  }

  private function profile() {
    $user = $this->model->checkUser();
    
    if(!$profile = $_GET['query']) {
      $this->redirect('index');
    } else if(!$profile_data = $this->model->getProfile($profile)) {
      $this->redirect(404);
    } else {
      $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

      $limit = 25;

      $start = ($p > 1) ? ($p * $limit) - $limit : 0;

      $posts = $this->model->getUsersPosts($profile_data->user_id, $start, $limit);

      $total = $this->model->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

      $pages = ceil($total / $limit);

      $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;
      
      $page_title = $profile_data->user_username . "'s Profile";

      $this->loadPage('profile', array('user' => $user, 'profile_data' => $profile_data, 'categories' => $categories, 'p' => $p, 'pages' => $pages, 'posts' => $posts, 'page_title' => $page_title));
    }
  }

  private function search() {
    $user = $this->model->checkUser();

    $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

    $keywords = isset($_POST['s']) ? escape($_POST['s']) : '';

    $user_results = $this->model->searchUsers($keywords);
    $post_results = $this->model->searchPosts($keywords);
    $category_results = $this->model->searchCategories($keywords);

    $page_title = "Search: " . str_replace('%', '', $keywords);

    $this->loadPage('search', array('user' => $user, 'categories' => $categories, 'keywords' => $keywords, 'user_results' => $user_results, 'post_results' => $post_results, 'category_results' => $category_results, 'page_title' => $page_title));
  }

  private function createCategory() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

      $page_title = "Create Category";

      $this->loadPage('create-category', array('user' => $user, 'categories' => $categories, 'page_title' => $page_title));
    }
  }

  private function newCategory() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('create-category');
    } else if(empty($_POST['category_name'])) {
      error('form_error', 'Enter a category name');

      $this->redirect('create-category');
    } else {
      $category = [
        'category_name' => escape($_POST['category_name']),
        'category_description' => escape($_POST['category_description']),
        'category_by' => $user->user_id
      ];
  
      if($this->model->createCategory($category)) {
        flash('post_message', 'Category created');

        $this->redirect('index');
      } else {
        error('form_error', 'Unable to create category');

        $this->redirect('create-category');
      }
    }
  }

  private function category() {
    $user = $this->model->checkUser();
    
    if(!$category = $_GET['query']) {
      $this->redirect('index');
    } else if(!$category_data = $this->model->getCategory($category)) {
      $this->redirect(404);
    } else {
      $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

      $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

      $limit = 25;

      $start = ($p > 1) ? ($p * $limit) - $limit : 0;

      $posts = $this->model->getPostsByCategory($category, $start, $limit);

      $total = $this->model->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

      $pages = ceil($total / $limit);

      $follow_data = $this->model->getFollowData($category_data->category_id);

      $page_title = $category_data->category_name;

      $this->loadPage('category', array('user' => $user, 'category_data' => $category_data, 'categories' => $categories, 'p' => $p, 'pages' => $pages, 'posts' => $posts, 'follow_data' => $follow_data, 'page_title' => $page_title));
    }
  }

  private function categories() {
    $user = $this->model->checkUser();

    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

    $limit = 5;

    $start = ($p > 1) ? ($p * $limit) - $limit : 0;

    $categories_list = $this->model->getCategories($start, $limit);

    $total = $this->model->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

    $pages = ceil($total / $limit);

    $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

    $page_title = "All Categories";

    $this->loadPage('categories', array('user' => $user, 'categories' => $categories, 'p' => $p, 'pages' => $pages, 'categories_list' => $categories_list, 'page_title' => $page_title));
  }

  private function follow() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$follow = escape($_GET['query'])) {
      $this->redirect('index');
    } else {
      if($this->model->followCategory($user->user_id, $follow)) {
        $this->redirect('category/' . $follow);
      } else {
        $this->redirect('category/' . $follow);
      }
    }
  }

  private function unfollow() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$follow = escape($_GET['query'])) {
      $this->redirect('index');
    } else {
      if($this->model->unfollowCategory($user->user_id, $follow)) {
        $this->redirect('category/' . $follow);
      } else {
        $this->redirect('category/' . $follow);
      }
    }
  }

  private function newPost() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

      $page_title = "New Post";

      $this->loadPage('new-post', array('user' => $user, 'categories' => $categories, 'page_title' => $page_title));
    }
  }

  private function createPost() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('new-post');
    } else if(empty($_POST['post_category']) || empty($_POST['post_title'])) {
      error('form_error', 'Fill in all fields');

      $this->redirect('new-post');
    } else {
      if(!empty($_POST['post_url'])) {
        $post = [
          'post_title' => escape($_POST['post_title']),
          'post_url' => escape($_POST['post_url']),
          'post_text' => escape($_POST['post_text']),
          'post_category' => escape($_POST['post_category']),
          'post_by' => $user->user_id
        ];
  
        if($this->model->createPost($post)) {
          flash('post_message', 'Post created');
  
          $this->redirect('index');
        } else {
          error('form_error', 'Unable to create post');
  
          $this->redirect('new-post');
        }
      } else {
        $post = [
          'post_title' => escape($_POST['post_title']),
          'post_text' => escape($_POST['post_text']),
          'post_category' => escape($_POST['post_category']),
          'post_by' => $user->user_id
        ];
  
        if($this->model->createPost($post)) {
          flash('post_message', 'Post created');
  
          $this->redirect('index');
        } else {
          error('form_error', 'Unable to create post');
  
          $this->redirect('new-post');
        }
      }
    }
  }

  private function post() {
    $user = $this->model->checkUser();

    if(!$post = $_GET['query']) {
      $this->redirect('index');
    } else if(!$post_data = $this->model->getPost($post)) {
      $this->redirect(404);
    } else {
      $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

      $comments = $this->model->getComments($post);

      $page_title = $post_data->post_title;

      $this->loadPage('post', array('user' => $user, 'post_data' => $post_data, 'categories' => $categories, 'comments' => $comments, 'page_title' => $page_title));
    }
  }

  private function edit() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else {
      if(!$post = $_GET['query']) {
        $this->redirect('index');
      } else if(!$post_data = $this->model->getPost($post)) {
        $this->redirect(404);
      } else if($post_data->post_by !== $user->user_id) {
        $this->redirect('index');
      } else {
        $categories = $this->model->getUsersFollows($user->user_id);

        $page_title = "Edit Post";
  
        $this->loadPage('edit', array('user' => $user, 'post_data' => $post_data, 'categories' => $categories, 'page_title' => $page_title));
      }
    }
  }

  private function editPost() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$post = $_GET['query']) {
      $this->redirect('index');
    } else if(!$post_data = $this->model->getPost($post)) {
      $this->redirect(404);
    } else if($post_data->post_by !== $user->user_id) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('edit/' . $post_data->post_id);
    } else if(empty($_POST['post_title'])) {
      error('form_error', 'Enter a title');

      $this->redirect('edit/' . $post_data->post_id);
    } else {
      if(!empty($_POST['post_url'])) {
        $update = [
          'post_title' => escape($_POST['post_title']),
          'post_url' => escape($_POST['post_url']),
          'post_text' => escape($_POST['post_text'])
        ];
  
        if($this->model->editPost($update, $post_data->post_id)) {
          flash('post_message', 'Post Updated');
  
          $this->redirect('edit/' . $post_data->post_id);
        } else {
          error('form_error', 'Unable to edit post');
  
          $this->redirect('edit/' . $post_data->post_id);
        }
      } else {
        $update = [
          'post_title' => escape($_POST['post_title']),
          'post_text' => escape($_POST['post_text'])
        ];
  
        if($this->model->editPost($update, $post_data->post_id)) {
          flash('post_message', 'Post Updated');
  
          $this->redirect('edit/' . $post_data->post_id);
        } else {
          error('form_error', 'Unable to edit post');
  
          $this->redirect('edit/' . $post_data->post_id);
        }
      }
    }
  }

  private function deletePost() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$post = $_GET['query']) {
      $this->redirect('index');
    } else if(!$post_data = $this->model->getPost($post)) {
      $this->redirect(404);
    } else if($post_data->post_by !== $user->user_id) {
      $this->redirect('index');
    } else if(!check($_POST['delete-token'], 'delete-token')) {
      error('delete_error', 'Token Failure');

      $this->redirect('edit/' . $post_data->post_id);
    } else {
      if($this->model->deletePost($post_data->post_id)) {
        flash('post_message', 'Post Deleted');

        $this->redirect('index');
      } else {
        error('delete_error', 'Unable to delete post');

        $this->redirect('edit/' . $post_data->post_id);
      }
    }
  }

  private function vote() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$vote = escape($_GET['query'])) {
      $this->redirect('index');
    } else {
      if($this->model->addPoint($user->user_id, $vote)) {
        $this->redirect('refer');
      } else {
        $this->redirect('refer');
      }
    }
  }

  private function unvote() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$vote = escape($_GET['query'])) {
      $this->redirect('index');
    } else {
      if($this->model->removePoint($user->user_id, $vote)) {
        $this->redirect('refer');
      } else {
        $this->redirect('refer');
      }
    }
  }

  private function comment() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$post = $_GET['query']) {
      $this->redirect('index');
    } else if(!$post_data = $this->model->getPost($post)) {
      $this->redirect('index');
    } else if(!check($_POST['token'], 'token')) {
      error('form_error', 'Token Failure');

      $this->redirect('post/' . $post_data->post_id);
    } else if(empty($_POST['comment_text'])) {
      error('form_error', 'Enter a comment');

      $this->redirect('post/'. $post_data->post_id);
    } else {
      $comment = [
        'comment_text' => escape($_POST['comment_text']),
        'comment_post' => $post_data->post_id,
        'comment_by' => $user->user_id
      ];

      if($this->model->createComment($comment)) {
        $this->redirect('post/'. $post_data->post_id);
      } else {
        error('form_error', 'Unable to post comment');

        $this->redirect('post/'. $post_data->post_id);
      }
    }
  }

  private function deleteComment() {
    if(!$user = $this->model->checkUser()) {
      $this->redirect('index');
    } else if(!$comment = $_GET['query']) {
      $this->redirect('index');
    } else if(!$comment_data = $this->model->getComment($comment)) {
      $this->redirect(404);
    } else if($comment_data->comment_by !== $user->user_id) {
      $this->redirect('index');
    } else {
      if($this->model->deleteComment($comment_data->comment_id)) {
        flash('post_message', 'Comment Deleted');

        $this->redirect('post/' . $comment_data->comment_post);
      } else {
        error('post_message', 'Unable to delete post');

        $this->redirect('post/' . $comment_data->post_id);
      }
    }
  }

  private function all() {
    $user = $this->model->checkUser();

    $categories = $user ? $this->model->getUsersFollows($user->user_id) : null;

    $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

    $limit = 25;

    $start = ($p > 1) ? ($p * $limit) - $limit : 0;

    $posts = $this->model->getPosts($start, $limit);

    $total = $this->model->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

    $pages = ceil($total / $limit);

    $page_title = "All Posts";

    $this->loadPage('all', array('user' => $user, 'categories' => $categories, 'p' => $p, 'pages' => $pages, 'posts' => $posts, 'page_title' => $page_title));
  }
}