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
        $user = [
            'user_username' => $data['user_username'],
            'user_email' => $data['user_email'],
            'user_password' => password_hash($data['user_password'], PASSWORD_DEFAULT),
            'user_joined' => date('Y-m-d H:i:s'),
        ];

        if($this->db->insert('users', $user)) {
            $user_id = $this->db->pdo->lastInsertId();

            if ($user_id) {
                $this->createUserSession($user_id);

                $this->session->put('user_id', $user_id);

                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    public function createUserSession($user_id)
    {
        $session_token = $this->hash->random(32);
        $hashed_token = hash('sha256', $session_token);

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $expires = date('Y-m-d H:i:s', strtotime("+1 days"));

        $sql = "DELETE FROM user_sessions WHERE session_user = :user_id AND expires_at < NOW()";

        $stmt = $this->db->pdo->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $session = [
            'session_user' => $user_id,
            'session_token' => $hashed_token,
            'user_agent' => $user_agent,
            'ip_address' => $ip_address,
            'expires_at' => $expires
        ];

        if (!$this->db->insert('user_sessions', $session)) {
            return false;
        }

        setcookie('session_token', $session_token, [
            'expires' => time() + 86400,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return $session_token;
    }

    public function validateUserSession()
    {
        if (empty($_COOKIE['session_token']) || !preg_match('/\A[a-f0-9]{64}\z/', $_COOKIE['session_token'])) {
            return false;
        }

        $session_token = $_COOKIE['session_token'];
        $hashed_token = hash('sha256', $session_token);

        $sql = "SELECT session_user AS user_id FROM user_sessions WHERE session_token = :session_token AND expires_at > NOW() LIMIT 1";

        $stmt = $this->db->pdo->prepare($sql);
        $stmt->execute([':session_token' => $hashed_token]);

        $row = $stmt->fetch();

        if (!empty($row)) {
            return $row->user_id;
        }

        return false;
    }

    public function deleteUserSession()
    {
        $session_token = $_COOKIE['session_token'] ?? null;

        if (empty($session_token)) {
            return false;
        }

        $hashed_token = hash('sha256', $session_token);

        if ($this->db->delete('user_sessions', ['session_token' => $hashed_token])) {
            setcookie('session_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            return true;
        }

        return false;        
    }

    public function createRememberToken($user_id)
    {
        $selector = $this->hash->random(16);
        $validator = $this->hash->random(32);

        $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        $remember = [
            'remember_user' => $user_id,
            'selector' => $selector,
            'hashed_validator' => $hashed_validator,
            'user_agent' => $user_agent,
            'ip_address' => $ip_address,
            'expires_at' => $expires,
        ];

        if ($this->db->insert('remember_tokens', $remember)) {
            $cookie_value = $selector . ':' . $validator;

            setcookie('remember_token', $cookie_value, [
                'expires' => strtotime($expires),
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            return true;
        }

        return false;
    }

    public function validateRememberToken()
    {
        if (isset($_SESSION['user_id']) || !empty($this->validateUserSession())) {
            return false;
        }

        if (empty($_COOKIE['remember_token'])) {
            return false;
        }

        $cookie_value = $_COOKIE['remember_token'];
        $cookie_parts = explode(':', $cookie_value, 2);

        if (count($cookie_parts) !== 2) {
            $this->deleteRememberCookie();

            return false;
        }

        $selector = $cookie_parts[0];
        $validator = $cookie_parts[1];

        if (!preg_match('/\A[a-f0-9]{32}\z/', $selector) || !preg_match('/\A[a-f0-9]{64}\z/', $validator)) {
            $this->deleteRememberCookie();

            return false;
        }

        $sql = "SELECT remember_user AS user_id, hashed_validator FROM remember_tokens WHERE selector = :selector AND expires_at > NOW() LIMIT 1";

        $stmt = $this->db->pdo->prepare($sql);

        $stmt->execute([':selector' => $selector]);

        $row = $stmt->fetch();

        if ($row && password_verify($validator, $row->hashed_validator)) {
            if (!$this->deleteRememberTokenBySelector($selector)){
                return false;
            }

            if (!$this->createRememberToken($row->user_id)) {
                return false;
            }

            if (!$this->createUserSession($row->user_id)) {
                return false;
            }

            return $row->user_id;
        } else {
            $this->deleteRememberCookie();
        }

        return false;
    }

    public function deleteRememberTokenBySelector($selector)
    {
        if ($this->db->delete('remember_tokens', ['selector' => $selector])) {
            return true;
        }

        return false;
    }

    public function deleteRememberToken()
    {
        $user_id = $this->getLoggedInUserId();

        if ($user_id !== false) {
            $this->db->delete('remember_tokens', ['remember_user' => $user_id]);
        }

        $this->deleteRememberCookie();
    }

    private function deleteRememberCookie()
    {
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public function login($user, $remember = false)
    {
        $stmt = $this->db->select('users', array('user_username' => $user['user_username']));

        $row = $stmt->fetch();

        if(!$row || !password_verify($user['user_password'], $row->user_password)) {
            return false;
        }

        $this->createUserSession($row->user_id);

        $this->session->put('user_id', $row->user_id);
        
        if ($remember) {
            $this->createRememberToken($row->user_id);
        }

        return true;
    }

    public function getLoggedInUserId()
    {
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        if ($user_id = $this->validateUserSession()) {
            return $user_id;
        }

        $this->validateRememberToken();

        if ($user_id = $this->validateUserSession()) {
            return $user_id;
        }

        return false;
    }

    public function checkUser()
    {
        $user_id = $this->getLoggedInUserId();

        if ($user_id === false) {
            return false;
        }

        $stmt = $this->db->select('users', array('user_id' => $user_id));

        return $stmt->fetch();
    }

    public function logout()
    {
        $this->deleteRememberToken();

        $this->deleteUserSession();
        
        $this->session->destroy();
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