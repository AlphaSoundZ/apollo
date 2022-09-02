<?php
require "config.php";
require "classes/update_class.php";

authorize("add_device");

$data = getData("POST", ["rfid_code", "type", "create_anyways"]);
$create_anyways = $data["create_anyways"];
$rfid_code = $data["rfid_code"];
$type = $data["type"];

if ($create_anyways)
    Create::checkDevice($data["rfid_code"], $data["type"]);
$upload = Create::createDevice($data["rfid_code"], $data["type"]);

Response::success("Device added", "SUCCESS");