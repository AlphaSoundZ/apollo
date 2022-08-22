<?php
require 'config.php';
authorize("reset");
$data = getData("POST", ["table", "reset_id"]);
$response["response"] = "0";
$response["message"] = "success";

$table = $data["table"];

$sth = $pdo->query("SELECT COUNT(1) FROM $table");
$count = $sth->fetchAll();
$response["rows_deleted"] = $count[0]["COUNT(1)"];

$sql = ($data["reset_id"] == true) ? "TRUNCATE TABLE $table" : "DELETE FROM $table";
$sth = $pdo->prepare($sql);
$sth->execute();
echo json_encode($response);