<?php

namespace Controllers;

use Core\Controller;
use Core\Hash;
use Core\Pagination;
use Models\PostModel;
use Models\UserModel;
use Models\CommentModel;

class PostController extends Controller
{
    protected CommentModel $commentModel;
    protected UserModel $userModel;
    protected PostModel $postModel;
    protected Hash $hash;

    public function __construct()
    {
        parent::__construct();

        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->hash = new Hash();
    }

    public function newPost()
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $token = $this->hash->generate('token');

        $this->loadPage('new-post', [
            'user' => $user,
            'categories' => $categories,
            'token' => $token,
            'page_title' => "New Post",
        ]);
    }

    public function createPost()
    {
        $category = trim($_POST['post_category'] ?? '');
        $title = trim($_POST['post_title'] ?? '');
        $text = trim($_POST['post_text'] ?? '');
        $url = trim($_POST['post_url'] ?? '');

        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', 'new-post');
        }
        
        if (empty($category) || empty($title)) {
            $this->fail('Fill in all fields', 'new-post');
        }

        $post = [
            'post_title' => $title,
            'post_text' => $text,
            'post_category' => $category,
            'post_by' => $user->user_id
        ];

        if (!empty($url)) {
            $post['post_url'] = $url;
        }
  
        if (!$this->postModel->createPost($post)) {
            $this->fail('Unable to create post', 'new-post');
        }

        $this->success('Post Created', '/');
    }

    public function post(int $id)
    {
        $user = $this->userModel->checkUser();

        if(!$id) {
            $this->redirect('/');
        }
        
        if (!$post_data = $this->postModel->getPost($id)) {
            $this->abort(404);
        }

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $token = $this->hash->generate('token');

        $comments = $this->commentModel->getComments($id);

        $this->loadPage('post', [
            'user' => $user,
            'post_data' => $post_data,
            'categories' => $categories,
            'token' => $token,
            'comments' => $comments,
            'page_title' => $post_data->post_title,
        ]);
    }

    public function edit(int $id)
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }

        if (!$id) {
            $this->redirect('/');
        }
        
        if (!$post_data = $this->postModel->getPost($id)) {
            $this->abort(404);
        }
        
        if($post_data->post_by !== $user->user_id) {
            $this->redirect('/');
        }

        $categories = $this->userModel->getUsersFollows($user->user_id);

        $token = $this->hash->generate('token');
        $deleteToken = $this->hash->generate('delete-token');

        $this->loadPage('edit', [
            'user' => $user,
            'post_data' => $post_data,
            'categories' => $categories,
            'token' => $token,
            'delete_token' => $deleteToken,
            'page_title' => "Edit Post",
        ]);
    }

    public function editPost(int $id)
    {
        $title = trim($_POST['post_title'] ?? '');
        $text = trim($_POST['post_text'] ?? '');
        $url = trim($_POST['post_url'] ?? '');

        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$id) {
            $this->redirect('/');
        }
        
        if (!$post_data = $this->postModel->getPost($id)) {
            $this->abort(404);
        }
        
        if ($post_data->post_by !== $user->user_id) {
            $this->redirect('/');
        }
        
        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', "/edit/{$post_data->post_id}");
        }
        
        if (empty($title)) {
            $this->fail('Enter a title', "/edit/{$post_data->post_id}");
        }

        $update = [
            'post_title' => $title,
            'post_text' => $text
        ];

        if (!empty($url)) {
            $update['post_url'] = $url;
        }
  
        if (!$this->postModel->editPost($update, $post_data->post_id)) {
            $this->fail('Unable to edit post', "/post/{$post_data->post_id}");
        }
        
        $this->success('Post Updated', "/post/{$post_data->post_id}");
    }

    public function deletePost(int $id)
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$id) {
            $this->redirect('/');
        }
        
        if (!$post_data = $this->postModel->getPost($id)) {
            $this->abort(404);
        }
        
        if ($post_data->post_by !== $user->user_id) {
            $this->redirect('/');
        }
        
        if (!$this->hash->check($_POST['delete-token'] ?? '', 'delete-token')) {
            $this->fail('Token Failure', "/edit/{$post_data->post_id}");
        }

        if (!$this->postModel->deletePost($post_data->post_id)) {
            $this->fail('Unable to delete post', "/edit/{$post_data->post_id}");
        }

        $this->success('Post Deleted', '/');
    }

    public function vote(int $id)
    {
        if(!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if(!$id) {
            $this->redirect('/');
        }

        if (!$this->postModel->addPoint($user->user_id, $id)) {
            $this->back();
        }

        $this->back();
    }

    public function unvote(int $id)
    {
        if(!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if(!$id) {
            $this->redirect('/');
        }

        if (!$this->postModel->removePoint($user->user_id, $id)) {
            $this->back();
        }

        $this->back();
    }

    public function top()
    {
        $user = $this->userModel->checkUser();

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $pagination = new Pagination($_GET['p'] ?? 1, 25);

        $posts = $this->postModel->getTopPosts($pagination->offset(), $pagination->limit());

        $pagination->setTotal($posts['total']);

        $this->loadPage('top', [
            'user' => $user,
            'categories' => $categories,
            'posts' => $posts['posts'],
            'pagination' => $pagination,
            'page_title' => "Top Posts",
        ]);
    }

    public function new()
    {
        $user = $this->userModel->checkUser();

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $pagination = new Pagination($_GET['p'] ?? 1, 25);

        $posts = $this->postModel->getNewPosts($pagination->offset(), $pagination->limit());

        $pagination->setTotal($posts['total']);

        $this->loadPage('new', [
            'user' => $user,
            'categories' => $categories,
            'posts' => $posts['posts'],
            'pagination' => $pagination,
            'page_title' => "New Posts",
        ]);
    }
}