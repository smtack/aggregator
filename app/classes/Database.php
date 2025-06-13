<?php
class Database {
  private $dbhost = DB_HOST;
  private $dbname = DB_NAME;
  private $dbuser = DB_USER;
  private $dbpass = DB_PASSWORD;
  private $dbchar = DB_CHARSET;

  public $pdo;
  public $dsn;
  public $opt;

  public function __construct() {
    $this->pdo = null;

    $this->dsn = "mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname . ";charset=" . $this->dbchar;

    $this->opt = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
      PDO::ATTR_EMULATE_PREPARES => false
    ];

    try {
      $this->pdo = new PDO($this->dsn, $this->dbuser, $this->dbpass, $this->opt);
    } catch(\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    return $this->pdo;
  }

  public function insert($table, $array) {
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
      error_log("Error: " . $e->getMessage() . "Code: " . $e->getCode());
      throw new Exception("An error occurred");
    }
  }

  public function select($table, $array) {
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
      error_log("Error: " . $e->getMessage() . "Code: " . $e->getCode());
      throw new Exception("An error occurred");
    }
  }

  public function update($table, $array, $field) {
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
      error_log("Error: " . $e->getMessage() . "Code: " . $e->getCode());
      throw new Exception("An error occurred");
    }
  }

  public function delete($table, $array) {
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
      error_log("Error: " . $e->getMessage() . "Code: " . $e->getCode());
      throw new Exception("An error occurred");
    }
  }

  public function exists($table, $array) {
    $res = $this->select($table, $array);

    return $res->rowCount() > 0;
  }
}