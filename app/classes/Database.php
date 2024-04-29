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
    $sql = "INSERT INTO " . $table . " (";
    $pref = "";

    foreach($array as $key => $value) {
      $sql .= $pref . $key;
      $pref = ", ";
    }

    $sql .= ") VALUES (";
    $pref = "";

    foreach($array as $key => $value) {
      $sql .= $pref . "'" . $value . "'";
      $pref = ", ";
    }

    $sql .= ");";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();

    return $stmt;
  }

  public function select($table, $array) {
    $sql = "SELECT * FROM " . $table;
    $pref = " WHERE ";

    foreach($array as $key => $value) {
      $sql .= $pref . $key . "='" . $value . "'";
      $pref = " AND ";
    }

    $sql .= ";";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();

    return $stmt;
  }

  public function update($table, $array, $field) {
    $sql = "UPDATE " . $table . " SET ";
    $pref = "";

    foreach($array as $key => $value) {
      $sql .= $pref . $key . "='" . $value . "'";
      $pref = ", ";
    }

    $sql .= " WHERE ";
    $pref = "";

    foreach($field as $key => $value) {
      $sql .= $pref . $key . "='" . $value . "'";
      $pref = " AND ";
    }

    $sql .= ";";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();

    return $stmt;
  }

  public function delete($table, $array) {
    $sql = "DELETE FROM " . $table;
    $pref = " WHERE ";

    foreach($array as $key => $value) {
      $sql .= $pref . $key . "='" . $value . "'";
      $pref = " AND ";
    }

    $sql .= ";";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute();

    return $stmt;
  }

  public function exists($table, $array) {
    $res = $this->select($table, $array);

    return ($res->rowCount() > 0) ? true : false;
  }
}