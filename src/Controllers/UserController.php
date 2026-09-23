<?php

namespace Controllers;

use Core\Controller;
use Core\Hash;
use Core\Pagination;
use Models\PostModel;
use Models\UserModel;

class UserController extends Controller
{
    protected Hash $hash;
    protected PostModel $postModel;
    protected UserModel $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->hash = new Hash();
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
    }

    public function signup()
    {
        if ($this->userModel->checkUser()) {
            $this->redirect('/');
        }

        $token = $this->hash->generate('token');

        $this->loadPage('signup', [
            'token' => $token,
            'page_title' => "Sign Up",
        ]);
    }

    public function register()
    {
        $username = trim($_POST['user_username'] ?? '');
        $email = trim($_POST['user_email'] ?? '');
        $password = $_POST['user_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', 'signup');
        }

        if ($this->userModel->checkUser()) {
            $this->redirect('/');
        }

        if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
            $this->fail('Fill in all fields', 'signup');
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Enter a valid email address', 'signup');
        }
        
        if ($this->userModel->db->exists('users', ['user_username' => $username])) {
            $this->fail('This username is taken', 'signup');
        }
        
        if ($this->userModel->db->exists('users', ['user_email' => $email])) {
            $this->fail('This email address is already in use', 'signup');
        }
        
        if ($password !== $confirm) {
            $this->fail('Passwords must match', 'signup');
        }

        $user = [
            'user_username' => $username,
            'user_email' => $email,
            'user_password' => $password
        ];

        if(!$this->userModel->createUser($user)) {
            $this->fail('Unable to sign up. Try again later.', 'signup');
        }
        
        $this->success("Welcome to aggregator {$username}!", '/');
    }

    public function login()
    {
        if($this->userModel->checkUser()) {
            $this->redirect('/');
        }

        $token = $this->hash->generate('token');
        
        $this->loadPage('login', [
            'token' => $token,
            'page_title' => "Log In",
        ]);
    }

    public function authenticate()
    {
        $username = trim($_POST['user_username'] ?? '');
        $password = $_POST['user_password'] ?? '';

        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', 'login');
        }

        if ($this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if (empty($username) || empty($password)) {
            $this->fail('Enter your Username and Password', 'login');
        }
        
        $user = [
            'user_username' => $username,
            'user_password' => $password
        ];

        if (!$this->userModel->login($user, isset($_POST['remember']))) {
            $this->fail('Username or Password Incorrect', 'login');
        }

        $this->session->regenerate();

        $this->success("Welcome back, {$user['user_username']}", '/');
    }

    public function logout()
    {
        $this->userModel->logout();

        $this->redirect('/');
    }

    public function update()
    {
        $user = $this->userModel->checkUser();

        if(!$user) {
            $this->redirect('/');
        }

        $token = $this->hash->generate('token');
        $passwordToken = $this->hash->generate('password-token');
        $deleteToken = $this->hash->generate('delete-token');

        $this->loadPage('update', [
            'user' => $user,
            'categories' => $this->userModel->getUsersFollows($user->user_id),
            'token' => $token,
            'password_token' => $passwordToken,
            'delete_token' => $deleteToken,
            'page_title' => "Update Profile",
        ]);
    }

    public function updateProfile()
    {
        $user = $this->userModel->checkUser();

        $email = trim($_POST['user_email'] ?? '');
        
        if (!$this->hash->check($_POST['token'] ?? '', 'token')) {
            $this->fail('Token Failure', 'update');
        }

        if (!$user) {
            $this->redirect('/');
        }

        if (empty($email)) {
            $this->fail('Enter a new email address', 'update');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->fail('Enter a valid email address', 'update');
        }

        if($this->userModel->db->exists('users', ['user_email' => $email]) && $email !== $user->user_email) {
            $this->fail('This email address is already in use', 'update');
        }

        if(!$this->userModel->updateProfile(['user_email' => $email], $user->user_id)) {
            $this->fail('Unable to update profile', 'update');
        }

        $this->success('Profile Updated', 'update');
    }

    public function updatePassword()
    {
        $current = $_POST['confirm_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_new_password'] ?? '';

        $user = $this->userModel->checkUser();

        if (!$this->hash->check($_POST['password-token'] ?? '', 'password-token')) {
            $this->fail('Token Failure', 'update');
        }

        if (!$user) {
            $this->redirect('/');
        }

        if (empty($current) || empty($new) || empty($confirm)) {
            $this->fail('Fill in all fields', 'update');
        }
        
        if (!password_verify($current, $user->user_password)) {
            $this->fail('Enter current password correctly', 'update');
        }
        
        if ($new !== $confirm) {
            $this->fail('Passwords must match', 'update');
        }

        $password = ['user_password' => password_hash($new, PASSWORD_DEFAULT)];
    
        if (!$this->userModel->changePassword($password, $user->user_id)) {
            $this->fail('Unable to change password', 'update');
        }

        $this->success('Password Updated', 'update');
    }

    public function deleteProfile()
    {
        $password = $_POST['user_password'] ?? '';

        $user = $this->userModel->checkUser();

        if (!$user) {
            $this->redirect('/');
        }
        
        if (!$this->hash->check($_POST['delete-token'] ?? '', 'delete-token')) {
            $this->fail('Token Failure', 'update');
        }
        
        if (empty($password)) {
            $this->fail('Enter your password', 'update');
        }
        
        if (!password_verify($password, $user->user_password)) {
            $this->fail('Enter your password correctly', 'update');
        }

        if (!$this->userModel->deleteProfile($user->user_id)) {
            $this->fail('Unable to delete profile. Try again later.', 'update');  
        }

        $this->logout();
    }

    public function profile(string $username)
    {
        $postModel = new PostModel();

        if (!$user = $this->userModel->checkUser()) {
            $this->redirect('/');
        }
        
        if(!$profile_data = $this->userModel->getProfile($username)) {
            $this->abort(404);
        }

        $categories = $user ? $this->userModel->getUsersFollows($user->user_id) : null;

        $pagination = new Pagination($_GET['p'] ?? 1, 25);

        $posts = $postModel->getUsersPosts($profile_data->user_id, $pagination->offset(), $pagination->limit());

        $pagination->setTotal($posts['total']);

        $this->loadPage('profile', [
            'user' => $user,
            'categories' => $categories,
            'profile_data' => $profile_data,
            'posts' => $posts['posts'],
            'pagination' => $pagination,
            'page_title' => $profile_data->user_username . "'s Profile",
        ]);
    }
}