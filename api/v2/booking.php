<?php
require "config.php";
require "classes/booking_class.php";

/*
rfid1 muss Usercard sein und rfid2 Gerät, wenn man ausleihen möchte.

Wenn nur rfid1:
- Entweder Device für Rückgabe
- Oder Info für Usercard
*/

authorize("book");
$input = getData("POST", ["uid_1"]);
$uid_1 = $input["uid_1"];
$uid_2 = (!empty($input["uid_2"])) ? $input["uid_2"] : null;
$booking = new Booking($uid_1, $uid_2);
$response_code = $booking->execute();
$response["data"] = $booking->fetchUserData();
Response::success(Response::getValue($response_code), $response_code, $response);