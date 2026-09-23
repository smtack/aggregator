<?php

namespace Controllers;

use Core\Controller;
use Core\Pagination;
use Models\CategoryModel;
use Models\UserModel;
use Models\PostModel;

class HomeController extends Controller
{
    protected UserModel $userModel;
    protected PostModel $postModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        parent::__construct();

        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $user = $this->userModel->checkUser();

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $pagination = new Pagination($_GET['p'] ?? 1, 25);

        if ($user) {
            $posts = $this->postModel->getHomepagePosts($user->user_id, $pagination->offset(), $pagination->limit());
        } else {
            $posts = $this->postModel->getTopPosts($pagination->offset(), $pagination->limit());
        }

        $pagination->setTotal($posts['total']);

        $this->loadPage('index', [
            'user' => $user,
            'categories' => $categories,
            'posts' => $posts['posts'],
            'pagination' => $pagination,
        ]);
    }

    public function search()
    {
        $user = $this->userModel->checkUser();

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $keywords = isset($_POST['s']) ? escape($_POST['s']) : '';

        $user_results = $this->userModel->searchUsers($keywords);
        $post_results = $this->postModel->searchPosts($keywords);
        $category_results = $this->categoryModel->searchCategories($keywords);

        $this->loadPage('search', [
            'user' => $user,
            'categories' => $categories,
            'keywords' => $keywords,
            'user_results' => $user_results,
            'post_results' => $post_results,
            'category_results' => $category_results,
            'page_title' => "Search: " . str_replace('%', '', $keywords),
        ]);
    }
}