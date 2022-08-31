<?php
require "config.php";
require "classes/booking_class.php";

global $pdo, $data, $device_1, $device_2;

// set response format to json
header("Content-Type: application/json");

authorize("book");
$data['message'] = NULL;
$input = getData("POST", ["uid_1"]);

/*
rfid1 muss Usercard sein und rfid2 Gerät, wenn man ausleihen möchte.

Wenn nur rfid1:D
- Entweder Device für Rückgabe
- Oder Info für Usercard
*/

$uid_1 = $input["uid_1"];
$uid_2 = (!empty($input["uid_2"])) ? $input["uid_2"] : null;
$response_code = Booking::execute($uid_1, $uid_2);

Response::success(Response::getValue($response_code), $response_code);