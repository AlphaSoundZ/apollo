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

    static function reset($table, $reset_id, $condition = null)
    {
        global $pdo;
        $sql = "SELECT COUNT(1) FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";
        $sth = $pdo->query($sql);
        $countCondition = $sth->fetchAll();

        $sql = "SELECT COUNT(1) FROM $table";
        $sth = $pdo->query($sql);
        $countAll = $sth->fetchAll();

        if (!$countAll)
            throw new CustomException("$table table ist leer", "BAD_REQUEST", 400);
        else if ($condition && empty($countCondition[0]["COUNT(1)"]))
            throw new CustomException("In $table wurden keine löschbaren Zeilen gefunden", "BAD_REQUEST", 400);
        
        $sql = ($reset_id && !$condition) ? "TRUNCATE TABLE $table" : "DELETE FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";

        if ($countCondition[0]["COUNT(1)"] == $countAll[0]["COUNT(1)"] && $reset_id)
            $sql = "TRUNCATE TABLE $table";
        
        $sth = $pdo->prepare($sql);
        $sth->execute();
        return $countCondition[0]["COUNT(1)"];
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