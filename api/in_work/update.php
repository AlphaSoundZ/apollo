<?php
require '../config.php';

authorize("update");

// get input:
$data = getData("POST", ["table", "id", "update"]);
$response["message"] = "";
$response["response"] = "";

// here you will be able to modify every entry in the database

foreach ($data["update"] as $key => $value)
{
    modify::update($data["table"], $data["id"], $key, $value);
}

echo json_encode($response); // return the response

class modify
{
    static function update($table, $id, $column, $value)
    {
        global $pdo, $response;

        $indentityColumn = self::getIndentityColumn($table);
        
        $sql = "UPDATE $table SET $column = '$value' WHERE $indentityColumn = '$id'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
        $response["message"] = "Update success";
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