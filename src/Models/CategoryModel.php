<?php

namespace Models;

use Core\Model;

class CategoryModel extends Model
{
    public function createCategory($category)
    {
        if($this->db->insert('categories', $category)) {
            return $this->db->pdo->lastInsertId();
        }

        return false;
    }

    public function getCategories($start, $limit)
    {
        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                *
                FROM
                    categories
                LEFT JOIN
                    users
                ON
                    users.user_id = categories.category_by
                ORDER BY
                    category_created
                DESC
                LIMIT {$start}, {$limit}";
    
        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute()) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function getCategory($id)
    {
        if($stmt = $this->db->select('categories', array('category_id' => $id))) {
            return $stmt->fetch();
        }

        return false;
    }

    public function followCategory($user, $category)
    {
        if($this->db->insert('follows', array('follow_user' => $user, 'follow_category' => $category))) {
            return true;
        }

        return false;
    }

    public function unfollowCategory($user, $category)
    {
        if($this->db->delete('follows', array('follow_user' => $user, 'follow_category' => $category))) {
            return true;
        }

        return false;
    }

    public function searchCategories($keywords)
    {
        $sql = "SELECT
                    *
                FROM
                    categories
                LEFT JOIN
                    users
                ON
                    users.user_id = categories.category_by
                WHERE
                    category_name
                LIKE
                    \"%" . $keywords . "%\"
                OR
                    category_description
                LIKE
                    \"%" . $keywords . "%\"
                ORDER BY
                    category_created
                DESC";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute()) {
            return $stmt->fetchAll();
        }

        return false;
    }
}