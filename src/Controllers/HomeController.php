<?php

namespace Controllers;

use Core\Controller;
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
        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        $limit = 25;

        $start = ($p > 1) ? ($p * $limit) - $limit : 0;

        if($user = $this->userModel->checkUser()) {
            $posts = $this->postModel->getHomepagePosts($user->user_id, $start, $limit);
        } else {
            $posts = $this->postModel->getTopPosts($start, $limit);
        }

        $total = $this->userModel->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

        $pages = ceil($total / $limit);

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $this->loadPage('index', [
            'user' => $user,
            'p' => $p,
            'pages' => $pages,
            'posts' => $posts,
            'categories' => $categories,
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