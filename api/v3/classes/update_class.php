<?php
class update
{
    static function update($table, $id, $column, $value)
    {
        global $pdo, $response;

        $indentityColumn = self::getIndentityColumn($table);
        
        $sql = "UPDATE $table SET $column = '$value' WHERE $indentityColumn = '$id'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
        return true;
    }

    private static function getIndentityColumn($table)
    {
        global $pdo;
        $sql = "SELECT * FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return array_key_first($result);
    }
}