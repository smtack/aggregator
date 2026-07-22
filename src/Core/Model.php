<?php

namespace Core;

use PDO;
use Core\Database;

class Model
{
    public Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }
}