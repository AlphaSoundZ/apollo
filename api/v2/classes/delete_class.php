<?php
require_once 'config.php';
class delete
{
    static function deleteRow($table, $id)
    {
        global $pdo;
        $indentityColumn = self::getIndentityColumn($table);

        $sql = "SELECT * FROM $table WHERE $indentityColumn = '$id'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == false)
            throw new CustomException("Zeile existiert nicht", "BAD_REQUEST", 400);
        
        $sql = "DELETE FROM $table WHERE $indentityColumn = '$id'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
        
        return "SUCCESS";
    }

    static function reset($table, $reset_id)
    {
        global $pdo;
        $sth = $pdo->query("SELECT COUNT(1) FROM $table");
        $count = $sth->fetchAll();
        
        $sql = ($reset_id) ? "TRUNCATE TABLE $table" : "DELETE FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        return $count[0]["COUNT(1)"];
    }

    private static function getIndentityColumn($table)
    {
        global $pdo;
        $sql = "SELECT * FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!$result)
            throw new CustomException("Tabelle ist leer", "BAD_REQUEST", 400);
        return array_key_first($result);
    }
}