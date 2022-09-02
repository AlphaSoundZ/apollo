<?php
require_once "config.php";

class Create
{
    public static function createDevice($device_uid, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :device_uid, '0'  )";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(["device_type" => $device_type, "device_uid" => $device_uid]);
        
        http_response_code(200);
        echo json_encode(["response" => "200", "message" => "success"]);
        return true;
    }
    public static function checkDevice($device_uid)
    {
        global $pdo;
        $sql = "SELECT * FROM devices WHERE device_uid = :device_uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["device_uid" => $device_uid]);
        $result = $stmt->fetch();
        if ($result)
            return $result["device_id"];
        return false;
    }
    public static function checkUser($firstname, $lastname)
    {
        global $pdo;
        $sql = "SELECT * FROM user WHERE user_firstname = :firstname AND user_lastname = :lastname";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname]);
        $result = $sth->fetch();

        if ($result)
            return $result["user_id"];
        return false;
    }
    public static function checkDeviceType($id)
    {
        global $pdo;
        $sql = "SELECT * FROM property_device_type WHERE device_type_id = :id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["id" => $id]);
        $result = $sth->fetch();
        if ($result)
            return $result["device_type_name"];
        return false;
    }
    public static function checkClass($class)
    {
        global $pdo;
        $sql = "SELECT * FROM property_class WHERE class_id = :class";
        $sth = $pdo->prepare($sql);
        $sth->execute(["class" => $class]);
        $result = $sth->fetch();
        if (!$result)
            return false;
        return true;
    }
    public static function checkUsercard($uid)
    {
        global $pdo;
        $sql = "SELECT * FROM devices WHERE device_uid = :uid AND device_type = '{$_SERVER["USERCARD_TYPE"]}'";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $usercard = $sth->fetch();

        if (!$usercard)
            return "DEVICE_NOT_FOUND";

        $sql = "SELECT * FROM user WHERE user_usercard_id = :usercard_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["usercard_id" => $usercard["device_id"]]);
        $result = $sth->fetch();
        if ($result)
        {
            $user = $result["user_firstname"].' '.$result["user_lastname"];
            return "USERCARD_ALREADY_ASSIGNED";
        }

        return $usercard["device_id"];

    }
    public static function checkUserForAssignement($user_id)
    {
        global $pdo;
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["user_id" => $user_id]);
        $result = $sth->fetch();
        if (!$result || $result["user_usercard_id"] == 0 || $result["user_usercard_id"] == NULL)
            return false;
        return true;
    }
    public static function createUser($firstname, $lastname, $class)
    {
        global $pdo;
        // User erstellen
        $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_usercard_id) VALUES (NULL, :firstname, :lastname, :class, NULL)";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class]);
        
        // User ID holen
        $sql = "SELECT user_id FROM user WHERE user_firstname = :firstname AND user_lastname = :lastname AND user_class = :class";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class]);
        $result = $sth->fetch();

        return $result["user_id"];
    }
    public static function createUsercard($uid)
    {
        global $pdo;
        // create usercard
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :uid, NULL)";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $_SERVER["USERCARD_TYPE"], "uid" => $uid]);
        
        // Usercard ID holen
        $sql = "SELECT * FROM devices WHERE device_uid = :uid";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $usercard_id = $sth->fetch();
        
        return $usercard_id["device_id"];
    }  
    public static function bindUserToUsercard($user_id, $usercard_id)
    {
        global $pdo;
        $sql = "UPDATE user SET user_usercard_id = :usercard_id WHERE user_id = :user_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["user_id" => $user_id, "usercard_id" => $usercard_id]);
        return true;
    }
}