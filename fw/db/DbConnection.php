<?php

namespace fw\db;

use \fw\AppComponent;


class DbConnection extends AppComponent
{
    public $dsn = '';
    public $user = 'root';
    public $password = '';
    public $options = null;

    /** @var \PDO */
    private $pdo = null;


    public function init()
    {
        $this->open();
    }

    private function open()
    {
        $this->pdo = new \PDO($this->dsn, $this->user, $this->password, $this->options);
    }

    private function close()
    {
        $this->pdo = null;
    }

    public function destroy()
    {
        $this->close();
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function insert($tableName, $row)
    {
        $pdo = $this->getPdo();
        $columns = [];
        $values = [];
        foreach ($row as $columnName => $value) {
            $columns[] = "`$columnName`";
            $values[":$columnName"] = $value;
        }
        $columns = implode(', ', $columns);
        $valuesRaw = implode(', ', array_keys($values));
        $sql = "INSERT INTO `{$tableName}` ({$columns}) VALUES ({$valuesRaw})";
        $statement = $pdo->prepare($sql);
        return (bool) $statement->execute($values);
    }

    public function update($tableName, $row, $where)
    {
        $pdo = $this->getPdo();
        $parts = [];
        foreach ($row as $columnName => $value) {
            $parts[] = "`$columnName` = :$columnName";
        }
        $set = implode(", ", $parts);
        $sql = "UPDATE `{$tableName}` SET {$set} WHERE {$where}";
        $statement = $pdo->prepare($sql);
        return (bool) $statement->execute($row);
    }

    public function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }

}
