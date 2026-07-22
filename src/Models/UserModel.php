<?php

namespace Models;

use Core\Hash;
use Core\Model;
use Core\Session;

class UserModel extends Model
{
    protected Hash $hash;
    protected Session $session;

    public function __construct()
    {
        parent::__construct();

        $this->hash = new Hash();
        $this->session = new Session();
    }

    public function createUser($data)
    {
        if($this->db->insert('users', $data)) {
            $this->authorizeUser($data);

            return true;
        }

        return false;
    }

    public function authorizeUser($user)
    {
        $hash = hash('sha256', $user['user_username']);

        $hash .= $this->hash->random(64);

        if($this->db->insert('user_auth', array('auth_hash' => $hash, 'auth_user' => $user['user_username']))) {
            setcookie('Auth', $hash);

            return true;
        }

        return false;
    }

    public function userForAuth($hash)
    {
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

    public function checkUser()
    {
        if(isset($_COOKIE['Auth'])) {
            return $this->userForAuth($_COOKIE['Auth']);
        }

        return false;
    }

    public function login($user)
    {
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

    public function logout($hash)
    {
        $this->db->delete('user_auth', array('auth_hash' => $hash));

        setcookie('Auth', '', time() - 3600);

        $this->session->destroy();

        return;
    }

    public function updateProfile($data, $id)
    {
        if($this->db->update('users', $data, array('user_id' => $id))) {
            return true;
        }

        return false;
    }

    public function changePassword($password, $id)
    {
        if($this->db->update('users', $password, array('user_id' => $id))) {
            return true;
        }

        return false;
    }

    public function deleteProfile($user)
    {
        if($this->db->delete('users', array('user_id' => $user))) {
            return true;
        }

        return false;
    }

    public function getProfile($profile)
    {
        if(!is_numeric($profile)) {
            $res = $this->db->select('users', array('user_username' => $profile));
        } else {
            $res = $this->db->select('users', array('user_id' => $profile));
        }

        return $res->fetch();
    }

    public function searchUsers($keywords)
    {
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

    public function getUsersFollows($user)
    {
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

    public function userFollows($user, $category)
    {
        return $this->db->exists('follows', array('follow_user' => $user, 'follow_category' => $category));
    }

    public function getFollowData($category)
    {
        $sql = "SELECT * FROM follows WHERE follow_category = :category";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':category' => $category])) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return false;
    }
}