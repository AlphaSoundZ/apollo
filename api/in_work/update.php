<?php
require '../config.php';

authorize("update");

// get input:
$data = getData("POST", ["table", "column", "value_to_update", "new_value"]);
$response["message"] = "";
$response["response"] = "";

// here you will be able to modify every entry in the database

$modify = new modify();
$modify->update($data["table"], $data["column"], $data["value_to_update"], $data["new_value"]);

echo json_encode($response); // return the response

class modify
{
    function update($table, $column, $value_to_update, $new_value)
    {
        global $pdo, $response;
        $sql = "UPDATE $table SET $column = '$new_value' WHERE $column = '$value_to_update'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
        $response["message"] = "Update success";
        $response["response"] = 1;
    }
}