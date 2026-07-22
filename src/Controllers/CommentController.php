<?php

namespace Controllers;

use Core\Controller;
use Core\Hash;
use Models\UserModel;
use Models\PostModel;
use Models\CommentModel;

class CommentController extends Controller
{   
    protected Hash $hash;
    protected PostModel $postModel;
    protected UserModel $userModel;
    protected CommentModel $commentModel;

    public function __construct()
    {
        parent::__construct();

        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
        $this->commentModel = new CommentModel();
        $this->hash = new Hash();
    }

    public function comment(int $post)
    {
        $text = trim($_POST['comment_text'] ?? '');

        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$post) {
            $this->redirect('/');
        }
        
        if (!$post_data = $this->postModel->getPost($post)) {
            $this->redirect('/');
        }
        
        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', "/post/{$post_data->post_id}");
        }
        
        if (empty($text)) {
            $this->fail('Enter a comment', "/post/{$post_data->post_id}");
        }

        $comment = [
            'comment_text' => $text,
            'comment_post' => $post_data->post_id,
            'comment_by' => $user->user_id
        ];

        if (!$this->commentModel->createComment($comment)) {
            $this->fail('Unable to post comment', "/post/{$post_data->post_id}");
        }

        $this->success('', "/post/{$post_data->post_id}");
    }

    public function deleteComment(int $id)
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$id) {
            $this->redirect('/');
        }
        
        if (!$comment_data = $this->commentModel->getComment($id)) {
            $this->abort(404);
        }
        
        if ($comment_data->comment_by !== $user->user_id) {
            $this->redirect('/');
        }

        if (!$this->commentModel->deleteComment($comment_data->comment_id)) {
            $this->fail('Unable to delete comment', "/post/{$comment_data->comment_post}");
        }

        $this->success('Comment deleted', "/post/{$comment_data->comment_post}");
    }
}