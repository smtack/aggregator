<?php

namespace Models;

use Core\Model;

class CommentModel extends Model
{
    public function createComment($comment)
    {
        if($this->db->insert('comments', $comment)) {
            return true;
        }

        return false;
    }

    public function getComment($comment)
    {
        $sql = "SELECT
                    *
                FROM
                    comments
                LEFT JOIN
                    users
                ON
                    users.user_id = comments.comment_by
                WHERE
                    comment_id = :comment_id";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':comment_id' => $comment])) {
            return $stmt->fetch();
        }

        return false;
    }

    public function getComments($post)
    {
        $sql = "SELECT
                    *
                FROM
                    comments
                LEFT JOIN
                    users
                ON
                    users.user_id = comments.comment_by
                WHERE
                    comment_post = :comment_post
                ORDER BY
                    comment_date
                DESC";

        $stmt = $this->db->pdo->prepare($sql);

        if($stmt->execute([':comment_post' => $post])) {
            return $stmt->fetchAll();
        }

        return false;
    }

    public function deleteComment($comment)
    {
        if($this->db->delete('comments', array('comment_id' => $comment))) {
            return true;
        }

        return false;
    }
}