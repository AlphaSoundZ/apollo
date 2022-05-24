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

if (authorize("add_device") == false)
{
    echo "Wrong token!";
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
    static public function upload($device_uid, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :device_uid, '0')";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $device_type, "device_uid" => $device_uid]);
        if ($status)
        {
            return ["success" => "1", "log" => "success"];
        }
        else
        {
            return ["success" => "3", "log" => "sql error"];
        }
    }
    static public function checkCode($device_uid)
    {
        global $pdo;
        $sql = "SELECT * FROM devices WHERE device_uid = :device_uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["device_uid" => $device_uid]);
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