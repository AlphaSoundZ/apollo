<?php
authorize("reset");
$data = getData("POST", ["table", "reset_id"]);

$table = $data["table"];
$sql = ($data["reset_id"] == true) ? "TRUNCATE TABLE $table" : "DELETE * FROM $table";

$columns_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table ORDER BY ORDINAL_POSITION";