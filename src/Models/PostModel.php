<?php

namespace Models;

use Core\Model;

class PostModel extends Model
{
    public function createPost($data)
    {
        if($this->db->insert('posts', $data)) {
            return true;
        }

        return false;
    }

    public function getTopPosts($start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                GROUP BY
                    posts.post_id
                ORDER BY
                    pts DESC
                LIMIT {$start}, {$limit}";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute()) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getNewPosts($start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                GROUP BY
                    posts.post_id
                ORDER BY
                    posts.post_date DESC
                LIMIT {$start}, {$limit}";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute()) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getHomepagePosts($user, $start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                WHERE
                    posts.post_category = categories.category_id AND post_category
                IN
                (SELECT
                    follow_category
                FROM
                    follows
                WHERE
                    follow_user = :user)
                GROUP BY
                    posts.post_id
                ORDER BY
                    pts DESC
                LIMIT {$start}, {$limit}";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':user' => $user])) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getPostsByCategory($id, $start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    posts.*,
                    users.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                WHERE
                    post_category = :category_id
                GROUP BY
                    posts.post_id
                ORDER BY
                    pts DESC
                LIMIT {$start}, {$limit}";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':category_id' => $id])) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getUsersPosts($user, $start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                WHERE
                    post_by = :post_by
                GROUP BY
                    posts.post_id
                ORDER BY
                    pts DESC
                LIMIT {$start}, {$limit}";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':post_by' => $user])) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function searchPosts($keywords)
    {
        $sql = "SELECT
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                WHERE
                    post_title
                LIKE
                    \"%" . $keywords . "%\"
                OR
                    post_text
                LIKE
                    \"%" . $keywords . "%\"
                GROUP BY
                    posts.post_id
                ORDER BY
                    pts DESC";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute()) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getPost($id)
    {
        $sql = "SELECT
                    posts.*,
                    users.*,
                    categories.*,
                    COUNT(points.point_post) AS pts
                FROM
                    posts
                LEFT JOIN
                    users
                ON
                    users.user_id = posts.post_by
                LEFT JOIN
                    categories
                ON
                    categories.category_id = posts.post_category
                LEFT JOIN
                    points
                ON
                    points.point_post = posts.post_id
                WHERE
                    post_id = :post_id
                GROUP BY
                    posts.post_id";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':post_id' => $id])) {
            return $stmt->fetch();
        }

        return false;
    }

    public function editPost($data, $post)
    {
        if($this->db->update('posts', $data, array('post_id' => $post))) {
            return true;
        }

        return false;
    }

    public function deletePost($post)
    {
        if($this->db->delete('posts', array('post_id' => $post))) {
            return true;
        }

        return false;
    }

    public function addPoint($user, $post)
    {
        if($this->db->insert('points', array('point_user' => $user, 'point_post' => $post))) {
            return true;
        }

        return false;
    }

    public function removePoint($user, $post)
    {
        if($this->db->delete('points', array('point_user' => $user, 'point_post' => $post))) {
            return true;
        }

        return false;
    }
    
    public function hasVoted($user, $post)
    {
        return $this->db->exists('points', array('point_user' => $user, 'point_post' => $post));
    }
}