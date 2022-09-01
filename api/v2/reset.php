<?php
require 'config.php';
require 'classes/delete_class.php';

authorize("delete");
$data = getData("POST", ["table", "reset_id"]);

$table = $data["table"];
$reset_id = $data["reset_id"];

$rows = Delete::reset($table, $reset_id);

Response::success("$rows Zeilen wurden gelöscht", "SUCCESS", ["rows" => $rows]);