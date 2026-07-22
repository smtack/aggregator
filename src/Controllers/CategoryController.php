<?php

namespace Controllers;

use Core\Controller;
use Core\Hash;
use Models\CategoryModel;
use Models\PostModel;
use Models\UserModel;

class CategoryController extends Controller
{
    protected UserModel $userModel;
    protected PostModel $postModel;
    protected CategoryModel $categoryModel;
    protected Hash $hash;

    public function __construct()
    {
        parent::__construct();

        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->categoryModel = new CategoryModel();
        $this->hash = new Hash();
    }

    public function createCategory()
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $this->loadPage('create-category', [
            'user' => $user,
            'categories' => $categories,
            'page_title' => "Create Category",
        ]);
    }

    public function newCategory()
    {
        $category_name = trim($_POST['category_name'] ?? '');
        $category_description = trim($_POST['category_description'] ?? '');

        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if(!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', 'create-category');
        }
        
        if(empty($category_name)) {
            $this->fail('Enter a category name', 'create-category');
        }

        $category = [
            'category_name' => $category_name,
            'category_description' => $category_description,
            'category_by' => $user->user_id
        ];

        $category_id = $this->categoryModel->createCategory($category);
  
        if(!$category_id) {
            $this->fail('Unable to create category', 'create-category');
        }
        
        $this->categoryModel->followCategory($user->user_id, $category_id);
        
        $this->success('Category created', "category/{$category_id}");
    }

    public function category(int $id)
    {
        $user = $this->userModel->checkUser();

        if (!$category_data = $this->categoryModel->getCategory($id)) {
            $this->abort(404);
        }

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        $limit = 25;

        $start = ($p > 1) ? ($p * $limit) - $limit : 0;

        $posts = $this->postModel->getPostsByCategory($id, $start, $limit);

        $total = $this->categoryModel->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

        $pages = ceil($total / $limit);

        $follow_data = $this->userModel->getFollowData($category_data->category_id);

        $this->loadPage('category', [
            'user' => $user,
            'category_data' => $category_data,
            'categories' => $categories,
            'p' => $p,
            'pages' => $pages,
            'posts' => $posts,
            'follow_data' => $follow_data,
            'page_title' => $category_data->category_name
        ]);
    }

    public function categories()
    {
        $user = $this->userModel->checkUser();

        $p = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        $limit = 5;

        $start = ($p > 1) ? ($p * $limit) - $limit : 0;

        $categories_list = $this->categoryModel->getCategories($start, $limit);

        $total = $this->categoryModel->db->pdo->query("SELECT FOUND_ROWS() AS total")->fetch()->total;

        $pages = ceil($total / $limit);

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $this->loadPage('categories', [
            'user' => $user,
            'categories' => $categories,
            'p' => $p,
            'pages' => $pages,
            'categories_list' => $categories_list,
            'page_title' => "All Categories",
        ]);
    }

    public function follow(int $category)
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$category) {
            $this->redirect('/');
        }

        if (!$this->categoryModel->followCategory($user->user_id, $category)) {
            $this->back();
        }

        $this->back();
    }

    public function unfollow(int $category)
    {
        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (!$category) {
            $this->redirect('/');
        }

        if (!$this->categoryModel->unfollowCategory($user->user_id, $category)) {
            $this->back();
        }

        $this->back();
    }
}