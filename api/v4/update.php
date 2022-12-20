<?php
require 'config.php';
require 'classes/update_class.php';

authorize("update");

// get input:
$data = getData("POST", ["table", "id", "update"]);

// here you will be able to modify every entry in the database

foreach ($data["update"] as $key => $value)
{
    update::update($data["table"], $data["id"], $key, $value);
}

Response::success("Zeile wurde aktuallisiert", "SUCCESS");