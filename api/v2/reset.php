<?php
require 'config.php';
require 'classes/delete_class.php';

authorize("delete");
$data = getData("POST", ["table", "reset_id"]);

$table = $data["table"];
$reset_id = $data["reset_id"];
$condition = ($data["condition"]) ? $data["condition"] : null;


if ($table == "event")
    $rows = Delete::reset($table, $reset_id, "event_end IS NOT NULL");
else if ($condition)
    $rows = Delete::reset($table, $reset_id, $condition);
else
    $rows = Delete::reset($table, $reset_id);

Response::success("$rows Zeilen wurden gelöscht", "SUCCESS", ["rows" => $rows]);