<?php
class Model {
  public $db;

  public function __construct() {
    $this->db = new Database();
  }

  public function createUser($data) {
    if($this->db->insert('users', $data)) {
      $this->authorizeUser($data);

      return true;
    }

    return false;
  }

  public function authorizeUser($user) {
    $hash = hash('sha256', $user['user_username']);

    $hash .= random(64);

    if($this->db->insert('user_auth', array('auth_hash' => $hash, 'auth_user' => $user['user_username']))) {
      setcookie('Auth', $hash);

      return true;
    }

    return false;
  }

  public function userForAuth($hash) {
    $sql = "SELECT
              *
            FROM
              users
            JOIN
              (SELECT
                auth_user
              FROM
                user_auth
              WHERE
                auth_hash = :auth_hash
              LIMIT 1)
            AS
              UA
            WHERE
              users.user_username = UA.auth_user
            LIMIT 1";
    
    $stmt = $this->db->pdo->prepare($sql);

    $stmt->execute([':auth_hash' => $hash]);

    if($stmt->rowCount() > 0) {
      return $stmt->fetchObject();
    }

    return false;
  }

  public function checkUser() {
    if(isset($_COOKIE['Auth'])) {
      return $this->userForAuth($_COOKIE['Auth']);
    }

    return false;
  }

  public function login($user) {
    if($this->db->exists('users', array('user_username' => $user['user_username']))) {
      $stmt = $this->db->select('users', array('user_username' => $user['user_username']));

      $row = $stmt->fetch();

      if(password_verify($user['user_password'], $row->user_password)) {
        $this->authorizeUser($user);

        return $row;
      }
    }

    return false;
  }

  public function logout($hash) {
    $this->db->delete('user_auth', array('auth_hash' => $hash));

    setcookie('Auth', '', time() - 3600);

    session_destroy();

    return;
  }

  public function updateProfile($data, $id) {
    if($this->db->update('users', $data, array('user_id' => $id))) {
      return true;
    }

    return false;
  }

  public function changePassword($password, $id) {
    if($this->db->update('users', $password, array('user_id' => $id))) {
      return true;
    }

    return false;
  }

  public function deleteProfile($user) {
    if($this->db->delete('users', array('user_id' => $user))) {
      return true;
    }

    return false;
  }

  public function getProfile($profile) {
    if(!is_numeric($profile)) {
      $res = $this->db->select('users', array('user_username' => $profile));
    } else {
      $res = $this->db->select('users', array('user_id' => $profile));
    }

    return $res->fetch();
  }

  public function searchUsers($keywords) {
    $sql = "SELECT
              *
            FROM
              users
            WHERE
              user_username
            LIKE
              \"%" . $keywords . "%\"
            ORDER BY
              user_joined
            DESC";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute()) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function createCategory($category) {
    if($this->db->insert('categories', $category)) {
      return true;
    }

    return false;
  }

  public function getCategories($start, $limit) {
    $sql = "SELECT
              SQL_CALC_FOUND_ROWS
              *
            FROM
              categories
            LEFT JOIN
              users
            ON
              users.user_id = categories.category_by
            ORDER BY
              category_created
            DESC
            LIMIT
              {$start}, {$limit}";
    
    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute()) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getUsersFollows($user) {
    $sql = "SELECT
              *
            FROM
              categories
            LEFT JOIN
              follows
            ON
              categories.category_id = follows.follow_category
            WHERE
              follows.follow_user = :user";
    
    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':user' => $user])) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getCategory($id) {
    if($stmt = $this->db->select('categories', array('category_id' => $id))) {
      return $stmt->fetch();
    }

    return false;
  }

  public function followCategory($user, $category) {
    if($this->db->insert('follows', array('follow_user' => $user, 'follow_category' => $category))) {
      return true;
    }

    return false;
  }

  public function unfollowCategory($user, $category) {
    if($this->db->delete('follows', array('follow_user' => $user, 'follow_category' => $category))) {
      return true;
    }

    return false;
  }

  public function getFollowData($category) {
    $sql = "SELECT * FROM follows WHERE follow_category = :category";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':category' => $category])) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false;
  }

  public function searchCategories($keywords) {
    $sql = "SELECT
              *
            FROM
              categories
            LEFT JOIN
              users
            ON
              users.user_id = categories.category_by
            WHERE
              category_name
            LIKE
              \"%" . $keywords . "%\"
            OR
              category_description
            LIKE
              \"%" . $keywords . "%\"
            ORDER BY
              category_created
            DESC";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute()) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function createPost($data) {
    if($this->db->insert('posts', $data)) {
      return true;
    }

    return false;
  }

  public function getPosts($start, $limit) {
    $sql = "SELECT
              SQL_CALC_FOUND_ROWS
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            LEFT JOIN
              categories
            ON
              category_id = posts.post_category
            ORDER BY
              post_date
            DESC
            LIMIT
              {$start}, {$limit}";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute()) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getHomepagePosts($user, $start, $limit) {
    $sql = "SELECT
              SQL_CALC_FOUND_ROWS
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            LEFT JOIN
              categories
            ON
              category_id = posts.post_category
            WHERE
              posts.post_category = categories.category_id AND post_category
            IN
              (SELECT
                follow_category
              FROM
                follows
              WHERE
                follow_user = :user)
            ORDER BY
              post_date
            DESC
            LIMIT
              {$start}, {$limit}";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':user' => $user])) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getPostsByCategory($id, $start, $limit) {
    $sql = "SELECT
              SQL_CALC_FOUND_ROWS
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            WHERE
              post_category = :category_id
            ORDER BY
              post_date
            DESC
            LIMIT
              {$start}, {$limit}";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':category_id' => $id])) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getUsersPosts($user, $start, $limit) {
    $sql = "SELECT
              SQL_CALC_FOUND_ROWS
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            LEFT JOIN
              categories
            ON
              category_id = posts.post_category
            WHERE
              post_by = :post_by
            ORDER BY
              post_date
            DESC
            LIMIT
              {$start}, {$limit}";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':post_by' => $user])) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function getPost($id) {
    $sql = "SELECT
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            LEFT JOIN
              categories
            ON
              categories.category_id = posts.post_category
            WHERE
              post_id = :post_id";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':post_id' => $id])) {
      return $stmt->fetch();
    }

    return false;
  }

  public function editPost($data, $post) {
    if($this->db->update('posts', $data, array('post_id' => $post))) {
      return true;
    }

    return false;
  }

  public function deletePost($post) {
    if($this->db->delete('posts', array('post_id' => $post))) {
      return true;
    }

    return false;
  }

  public function addPoint($user, $post) {
    if($this->db->insert('points', array('point_user' => $user, 'point_post' => $post))) {
      return true;
    }

    return false;
  }

  public function removePoint($user, $post) {
    if($this->db->delete('points', array('point_user' => $user, 'point_post' => $post))) {
      return true;
    }

    return false;
  }

  public function getPoints($post) {
    $sql = "SELECT * FROM points WHERE point_post = :post";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':post' => $post])) {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return false;
  }

  public function createComment($comment) {
    if($this->db->insert('comments', $comment)) {
      return true;
    }

    return false;
  }

  public function getComment($comment) {
    $sql = "SELECT
              *
            FROM
              comments
            LEFT JOIN
              users
            ON
              users.user_id = comments.comment_by
            WHERE
              comment_id = :comment_id";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':comment_id' => $comment])) {
      return $stmt->fetch();
    }

    return false;
  }

  public function getComments($post) {
    $sql = "SELECT
              *
            FROM
              comments
            LEFT JOIN
              users
            ON
              users.user_id = comments.comment_by
            WHERE
              comment_post = :comment_post
            ORDER BY
              comment_date
            DESC";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute([':comment_post' => $post])) {
      return $stmt->fetchAll();
    }

    return false;
  }

  public function deleteComment($comment) {
    if($this->db->delete('comments', array('comment_id' => $comment))) {
      return true;
    }

    return false;
  }

  public function searchPosts($keywords) {
    $sql = "SELECT
              *
            FROM
              posts
            LEFT JOIN
              users
            ON
              users.user_id = posts.post_by
            LEFT JOIN
              categories
            ON
              category_id = posts.post_category
            WHERE
              post_title
            LIKE
              \"%" . $keywords . "%\"
            OR
              post_text
            LIKE
              \"%" . $keywords . "%\"
            ORDER BY
              post_date
            DESC";

    $stmt = $this->db->pdo->prepare($sql);

    if($stmt->execute()) {
      return $stmt->fetchAll();
    }

    return false;
  }
}