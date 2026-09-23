<?php

namespace Models;

use Core\Model;

class PostModel extends Model
{
    public function createPost($data)
    {
        return $this->db->insert('posts', $data);
    }

    public function getTopPosts(int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
            COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            GROUP BY posts.post_id
            ORDER BY pts DESC, posts.post_date DESC
            LIMIT {$offset}, {$limit}"
        );

        $posts = $query->fetchAll();

        $total = $this->db->query("SELECT COUNT(*) FROM posts")->fetchColumn();

        return [
            'posts' => $posts,
            'total' => $total,
        ];
    }

    public function getNewPosts(int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            GROUP BY posts.post_id
            ORDER BY posts.post_date DESC
            LIMIT {$offset}, {$limit}"
        );

        $posts = $query->fetchAll();

        $total = $this->db->query("SELECT COUNT(*) FROM posts")->fetchColumn();

        return [
            'posts' => $posts,
            'total' => $total,
        ];
    }

    public function getHomepagePosts($user, int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            WHERE posts.post_category IN (
                SELECT follow_category
                FROM follows
                WHERE follow_user = :user
            )
            GROUP BY posts.post_id
            ORDER BY pts DESC, posts.post_date DESC
            LIMIT {$offset}, {$limit}",
            [':user' => $user]
        );

        $posts = $query->fetchAll();

        $total = $this->db->query(
            "SELECT COUNT(*)
            FROM posts
            WHERE posts.post_category IN (
                SELECT follow_category
                FROM follows
                WHERE follow_user = :user
            )",
            [':user' => $user]
        )->fetchColumn();

        return [
            'posts' => $posts,
            'total' => $total,
        ];
    }

    public function getPostsByCategory($id, int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN points
                ON points.point_post = posts.post_id
            WHERE post_category = :category_id
            GROUP BY posts.post_id
            ORDER BY pts DESC, posts.post_date DESC
            LIMIT {$offset}, {$limit}",
            [':category_id' => $id]
        );

        $posts = $query->fetchAll();

        $total = $this->db->query(
            "SELECT COUNT(*)
            FROM posts
            WHERE posts.post_category = :category_id",
            [':category_id' => $id]
        )->fetchColumn();

        return [
            'posts' => $posts,
            'total' => $total,
        ];
    }

    public function getUsersPosts($user, int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            WHERE post_by = :post_by
            GROUP BY posts.post_id
            ORDER BY pts DESC, posts.post_date DESC
            LIMIT {$offset}, {$limit}",
            [':post_by' => $user]
        );

        $posts = $query->fetchAll();

        $total = $this->db->query(
            "SELECT COUNT(*)
            FROM posts
            WHERE posts.post_by = :post_by",
            [':post_by' => $user]
        )->fetchColumn();

        return [
            'posts' => $posts,
            'total' => $total,
        ];
    }

    public function searchPosts($keywords)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            WHERE post_title LIKE \"%" . $keywords . "%\"
            OR post_text LIKE \"%" . $keywords . "%\"
            GROUP BY posts.post_id
            ORDER BY pts DESC, posts.post_date DESC"
        );

        return $query->fetchAll();
    }

    public function getPost($id)
    {
        $query = $this->db->query(
            "SELECT
                posts.*,
                users.*,
                categories.*,
                COUNT(points.point_post) AS pts
            FROM posts
            LEFT JOIN users
                ON users.user_id = posts.post_by
            LEFT JOIN categories
                ON categories.category_id = posts.post_category
            LEFT JOIN points
                ON points.point_post = posts.post_id
            WHERE post_id = :post_id
            GROUP BY posts.post_id",
            [':post_id' => $id]
        );

        return $query->fetch();
    }

    public function editPost($data, $post)
    {
        return $this->db->update('posts', $data, array('post_id' => $post));
    }

    public function deletePost($post)
    {
        return $this->db->delete('posts', array('post_id' => $post));
    }

    public function addPoint($user, $post)
    {
        return $this->db->insert('points', array('point_user' => $user, 'point_post' => $post));
    }

    public function removePoint($user, $post)
    {
        return $this->db->delete('points', array('point_user' => $user, 'point_post' => $post));
    }
    
    public function hasVoted($user, $post)
    {
        return $this->db->exists('points', array('point_user' => $user, 'point_post' => $post));
    }
}