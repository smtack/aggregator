<?php

namespace Models;

use Core\Model;

class CommentModel extends Model
{
    public function createComment($comment)
    {
        return $this->db->insert('comments', $comment);
    }

    public function getComment($comment)
    {
        $query = $this->db->query(
            "SELECT *
            FROM comments
            LEFT JOIN users
                ON users.user_id = comments.comment_by
            WHERE comment_id = :comment_id",
            [':comment_id' => $comment]
        );

        return $query->fetch();
    }

    public function getComments($post)
    {
        $query = $this->db->query(
            "SELECT *
            FROM comments
            LEFT JOIN users
                ON users.user_id = comments.comment_by
            WHERE comment_post = :comment_post
            ORDER BY comment_date DESC",
            [':comment_post' => $post]
        );

        return $query->fetchAll();
    }

    public function deleteComment($comment)
    {
        return $this->db->delete('comments', array('comment_id' => $comment));
    }
}