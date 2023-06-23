<?php
class Router {
  private $routes;

  public function __construct() {
    $this->routes = array(
      "index" => "index",
      "signup" => "signup",
      "register" => "register",
      "login" => "login",
      "authenticate" => "authenticate",
      "logout" => "logout",
      "update" => "update",
      "update-profile" => "updateProfile",
      "update-password" => "updatePassword",
      "delete-profile" => "deleteProfile",
      "profile" => "profile",
      "search" => "search",
      "create-category" => "createCategory",
      "new-category" => "newCategory",
      "category" => "category",
      "categories" => "categories",
      "follow" => "follow",
      "unfollow" => "unfollow",
      "new-post" => "newPost",
      "create-post" => "createPost",
      "post" => "post",
      "edit" => "edit",
      "edit-post" => "editPost",
      "delete-post" => "deletePost",
      "vote" => "vote",
      "unvote" => "unvote",
      "comment" => "comment",
      "delete-comment" => "deleteComment",
      "all" => "all"
    );
  }

  public function lookup($query) {
    if(array_key_exists($query, $this->routes)) {
      return $this->routes[$query];
    } else {
      return false;
    }
  }
}