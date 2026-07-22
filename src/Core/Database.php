<?php

namespace Core;

use PDO;

class Database
{
    private static ?PDO $shared = null;

    public PDO $pdo;

    public function __construct()
    {
        if (self::$shared === null) {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHAR
            );

            self::$shared = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        $this->pdo = self::$shared;
    }

    public function insert($table, $array)
    {
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach($array as $key => $value) {
            $columns[] = $key;
            $placeholders[] = ':' . $key;
            $values[':' . $key] = $value;
        }

        $sql = "INSERT INTO " . $table . " (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($values);

            return $stmt;
        } catch(\PDOException $e) {
            error_log(
                sprintf(
                    'Database Error: %s (Code: %d)',
                    $e->getMessage(),
                    $e->getCode()
                )
            );
            
            throw new \Exception("An error occurred", 0, $e);
        }
    }

    public function select($table, $array)
    {
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach($array as $key => $value) {
            $columns[] = $key;
            $placeholders[] = ':' . $key;
            $values[':' . $key] = $value;
        }

        $sql = "SELECT * FROM " . $table . " WHERE " . implode(' AND ', array_map(fn($col, $pl) => "$col = $pl", $columns, $placeholders)) . ";";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($values);

            return $stmt;
        } catch(\PDOException $e) {
            error_log(
                sprintf(
                    'Database Error: %s (Code: %d)',
                    $e->getMessage(),
                    $e->getCode()
                )
            );

            throw new \Exception("An error occurred", 0, $e);
        }
    }

    public function update($table, $array, $field)
    {
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach($array as $key => $value) {
            $columns[] = $key;
            $placeholders[] = ':' . $key;
            $values[':' . $key] = $value;
        }

        $fieldColumns = [];
        $fieldPlaceholders = [];
        $fieldValues = [];

        foreach($field as $key => $value) {
            $fieldColumns[] = $key;
            $fieldPlaceholders[] = ':' . $key;
            $fieldValues[':' . $key] = $value;
        }

        $sql = "UPDATE " . $table . " SET " . implode(', ', array_map(fn($col, $pl) => "$col = $pl", $columns, $placeholders)) . " WHERE " . implode(' AND ', array_map(fn($fcol, $fpl) => "$fcol = $fpl", $fieldColumns, $fieldPlaceholders)) . ";";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(array_merge($values, $fieldValues));

            return $stmt;
        } catch(\PDOException $e) {
            error_log(
                sprintf(
                    'Database Error: %s (Code: %d)',
                    $e->getMessage(),
                    $e->getCode()
                )
            );

            throw new \Exception("An error occurred", 0, $e);
        }
    }

    public function delete($table, $array)
    {
        $columns = [];
        $placeholders = [];
        $values = [];

        foreach($array as $key => $value) {
            $columns[] = $key;
            $placeholders[] = ':' . $key;
            $values[':' . $key] = $value;
        }

        $sql = "DELETE FROM " . $table . " WHERE " . implode(' AND ', array_map(fn($col, $pl) => "$col = $pl", $columns, $placeholders)) . ";";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($values);

            return $stmt;
        } catch(\PDOException $e) {
            error_log(
                sprintf(
                    'Database Error: %s (Code: %d)',
                    $e->getMessage(),
                    $e->getCode()
                )
            );

            throw new \Exception("An error occurred", 0, $e);
        }
    }

    public function exists($table, $array)
    {
        $res = $this->select($table, $array);

        return (bool) $res->fetch();
    }
}