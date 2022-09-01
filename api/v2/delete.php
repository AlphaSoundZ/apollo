<?php
require "config.php";
require "classes/delete_class.php";

authorize("delete");
$data = getData("POST", ["table", "id"]);

$table = $data["table"];
$id = $data["id"];

delete::deleteRow($table, $id);

Response::success("Zeile wurde gelöscht", "SUCCESS");