<?php
require "config.php";
$device_code = '';
$device_type = '';
$response = '';
/*
    0 = usercard
    1 = Surface Book
    2 = Laptop
*/

if (authorize("add_device") === false)
{
    exit;
}


$data = getData("POST");

if ($data && !empty($data["rfid_code"]) && !empty($data["type"]))
{
    $checkCode = addToDB::checkCode($data["rfid_code"]);
    if ($checkCode == false)
    {
        $upload = addToDB::upload($data["rfid_code"], $data["type"]);
    }
    else
    {
        $upload["success"] = 0;
        $upload["log"] = "device already exists";
    }

    
}
else
{
    $upload["success"] = 2;
    $upload["log"] = "wrong data input";
}
echo json_encode($upload);


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
            return ["success" => "1", "log" => "success"];
        }
        else
        {
            return ["success" => "3", "log" => "sql error"];
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