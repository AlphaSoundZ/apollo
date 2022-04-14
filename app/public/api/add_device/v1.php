<?php
require "../../../config/config.php";
$device_code = '';
$device_type = '';
$response = '';
/*
    0 = usercard
    1 = Surface Book
    2 = Laptop
*/


global $pdo, $device_1, $device_2;
if (isset($_GET['device_code']) && isset($_GET['device_type']))
{
    $device_code = $_GET['device_code'];
    $device_type = $_GET['device_type'];
    $checkCode = addToDB::checkCode($device_code);
    if ($checkCode == false)
    {
      $upload = addToDB::upload($device_code, $device_type);
      $response["response"] = $upload["success"];
      $response["errorMsg"] = $upload["errorMsg"];
    }
    else
    {
        $response["response"] = 0;
        $response["errorMsg"] = "$device_code ($device_type) already exists";
    }
}

class addToDB
{
    static public function upload($device_code, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO rfid_devices (device_id, device_type, rfid_code, lend_id) VALUES (NULL, :device_type, :device_code, '0')";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $device_type, "device_code" => $device_code]);
        if ($status)
        {
            return ["success" => "1", "errorMsg" => "success"];
        }
        else
        {
            return ["success" => "0", "errorMsg" => "sql error"];
        }
    }
    static public function checkCode($device_code)
    {
        global $pdo;
        $sql = "SELECT * FROM rfid_devices WHERE rfid_code = :rfid_code";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["rfid_code" => $device_code]);
        $result = $stmt->fetch();
        if ($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}