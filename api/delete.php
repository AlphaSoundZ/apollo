<?php
require "config.php";

authorize("delete");

$data = getData("POST", ["table", "id"]);
$response["message"] = "";
$response["response"] = "";

delete::deleteRow($data["table"], $data["id"]);

echo json_encode($response); // return the response

class delete
{
    static function deleteRow($table, $id)
    {
        global $pdo, $response;
        $indentityColumn = self::getIndentityColumn($table);
        $sql = "DELETE FROM $table WHERE $indentityColumn = '$id'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
        $response["message"] = "Delete success";
        $response["response"] = 1;
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