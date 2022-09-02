<?php
require "config.php";
require "classes/create_class.php";

authorize("add_device");

$data = getData("POST", ["rfid_code", "type"]);
$rfid_code = $data["rfid_code"];
$type = $data["type"];

if (Create::checkDevice($data["rfid_code"]))
    throw new CustomException(Response::DEVICE_ALREADY_EXISTS, "DEVICE_ALREADY_EXISTS", 400);
$type_name = Create::checkDeviceType($data["type"]);
if (!$type_name)
    throw new CustomException(Response::DEVICE_TYPE_NOT_FOUND, "DEVICE_TYPE_NOT_FOUND", 400);

$id = Create::createDevice($data["rfid_code"], $data["type"]);

Response::success("$type wurde erstellt (id: $id)", "SUCCESS", ["device_id" => $id, "device_type_name" => $type_name]);