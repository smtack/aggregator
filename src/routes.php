<?php

use Controllers\CategoryController;
use Controllers\CommentController;
use Controllers\HomeController;
use Controllers\UserController;
use Controllers\PostController;

return [
    "/"                         => [HomeController::class, 'index'],
    "/search"                   => [HomeController::class, 'search'],
    "/signup"                   => [UserController::class, 'signup'],
    "/register"                 => [UserController::class, 'register'],
    "/login"                    => [UserController::class, 'login'],
    "/authenticate"             => [UserController::class, 'authenticate'],
    "/logout"                   => [UserController::class, 'logout'],
    "/update"                   => [UserController::class, 'update'],
    "/update-profile"           => [UserController::class, 'updateProfile'],
    "/update-password"          => [UserController::class, 'updatePassword'],
    "/delete-profile"           => [UserController::class, 'deleteProfile'],
    "/profile/{username}"       => [UserController::class, 'profile'],
    "/create-category"          => [CategoryController::class, 'createCategory'],
    "/new-category"             => [CategoryController::class, 'newCategory'],
    "/category/{id}"            => [CategoryController::class, 'category'],
    "/categories"               => [CategoryController::class, 'categories'],
    "/follow/{category}"        => [CategoryController::class, 'follow'],
    "/unfollow/{category}"      => [CategoryController::class, 'unfollow'],
    "/new-post"                 => [PostController::class, 'newPost'],
    "/create-post"              => [PostController::class, 'createPost'],
    "/post/{id}"                => [PostController::class, 'post'],
    "/edit/{id}"                => [PostController::class, 'edit'],
    "/edit-post/{id}"           => [PostController::class, 'editPost'],
    "/delete-post/{id}"         => [PostController::class, 'deletePost'],
    "/vote/{id}"                => [PostController::class, 'vote'],
    "/unvote/{id}"              => [PostController::class, 'unvote'],
    "/top"                      => [PostController::class, 'top'],
    "/new"                      => [PostController::class, 'new'],
    "/comment/{post}"           => [CommentController::class, 'comment'],
    "/delete-comment/{id}"      => [CommentController::class, 'deleteComment'],
];