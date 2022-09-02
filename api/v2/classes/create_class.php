<?php
require_once "config.php";
class Create
{
    static public function createDevice($device_uid, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :device_uid, '0'  )";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(["device_type" => $device_type, "device_uid" => $device_uid]);
        
        http_response_code(200);
        echo json_encode(["response" => "200", "message" => "success"]);
        return true;
    }

    static public function checkDevice($device_uid, $type)
    {
        global $pdo;
        // Check type
        $sql = "SELECT device_type_id FROM property_device_type WHERE device_type_id = '$type'";
        $stmt= $pdo->prepare($sql);
        $stmt->execute();
        if (!$stmt->fetch())
            throw new CustomException(Response::TYPE_NOT_FOUND, "TYPE_NOT_FOUND", 400);
        
        // Check if device is already in database
        $sql = "SELECT * FROM devices WHERE device_uid = :device_uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["device_uid" => $device_uid]);
        $result = $stmt->fetch();
        if ($result)
            throw new CustomException(Response::DEVICE_ALREADY_EXISTS, "DEVICE_ALREADY_EXISTS", 400);
        return true;
    }
}