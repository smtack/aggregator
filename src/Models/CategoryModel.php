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

    public function getCategories(int $offset, int $limit)
    {
        $query = $this->db->query(
            "SELECT *
            FROM categories
            LEFT JOIN users
                ON users.user_id = categories.category_by
            ORDER BY
                category_created DESC
            LIMIT {$offset}, {$limit}"
        );
    
        $categories = $query->fetchAll();

        $total = $this->db->query("SELECT COUNT(*) FROM categories")->fetchColumn();

        return [
            'categories' => $categories,
            'total' => $total,
        ];
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
        return $this->db->insert('follows', array('follow_user' => $user, 'follow_category' => $category));
    }

    public function unfollowCategory($user, $category)
    {
        return $this->db->delete('follows', array('follow_user' => $user, 'follow_category' => $category));
    }

    public function searchCategories($keywords)
    {
        $query = $this->db->query(
            "SELECT *
            FROM categories
            LEFT JOIN users
                ON users.user_id = categories.category_by
            WHERE category_name LIKE \"%" . $keywords . "%\"
            OR category_description LIKE \"%" . $keywords . "%\"
            ORDER BY category_created DESC"
        );

        return $query->fetchAll();
    }
}