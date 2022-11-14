<?php
require_once "config.php";

class Create
{
    public static function user($firstname, $lastname, $class_id, $usercard_id = null, $token_id = null, $ignore_duplicates = true) 
    {
        global $pdo;
        // check if class_id exists
        $sql = "SELECT * FROM property_class WHERE class_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $class_id]);
        if (!$stmt->fetch())
            throw new CustomException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400);
            
        // check if usercard_id exists
        if ($usercard_id)
        {
            $sql = "SELECT * FROM usercard WHERE usercard_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id" => $usercard_id]);
            if (!$stmt->fetch())
                throw new CustomException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400);
        }
            
        // check if token_id exists
        if ($token_id)
        {
            $sql = "SELECT * FROM token WHERE token_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id" => $usercard_id]);
            if (!$stmt->fetch())
                throw new CustomException(Response::TOKEN_NOT_FOUND, "TOKEN_NOT_FOUND", 400);
        }
        
        // check if user already exists (only when $ignore_duplicates is set to false)
        if ($ignore_duplicates == false)
        {
            $sql = "SELECT * FROM user WHERE user_firstname = :firstname AND user_lastname = :lastname";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["firstname" => $firstname, "lastname" => $lastname]);
            if ($stmt->fetch())
                throw new CustomException(Response::USER_ALREADY_EXISTS, "USER_ALREADY_EXISTS", 400);
        }

        // insert user
        $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_token_id, user_usercard_id) VALUES (NULL, :firstname, :lastname, :class, :token, :usercard)";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class_id, "token" => $token_id, "usercard" => $usercard_id]);

        return $pdo->lastInsertId();
    }

    public static function usercard($uid, $type, $user_id = null, $allow_reassigning = false)
    {
        global $pdo;
        // check if usercard exists
        $sql = "SELECT * FROM usercard WHERE usercard_uid = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["uid" => $uid]);
        if ($stmt->fetch())
            throw new CustomException(Response::USERCARD_ALREADY_EXISTS, "USERCARD_ALREADY_EXISTS", 400);
            
        // check if type exists
        $sql = "SELECT * FROM property_usercard_type WHERE usercard_type_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $type]);
        if (!$stmt->fetch())
            throw new CustomException(Response::USERCARD_TYPE_NOT_FOUND, "USERCARD_TYPE_NOT_FOUND", 400);
            
        // check if user exists
        if ($user_id)
        {
            $sql = "SELECT * FROM user WHERE user_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["id" => $user_id]);
            $user = $stmt->fetch();
            if (!$user)
                throw new CustomException(Response::USER_NOT_FOUND, "USER_NOT_FOUND", 400);
            
            if (isset($user["user_usercard_id"]) && $allow_reassigning == false)
                throw new CustomException(Response::USER_ALREADY_ASSIGNED, "USER_ALREADY_ASSIGNED", 400);
        }
        
        // insert usercard
        $sql = "INSERT INTO usercard (usercard_id, usercard_type, usercard_uid) VALUES (NULL, :type, :uid)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["type" => $type, "uid" => $uid]);

        $usercard_id = $pdo->lastInsertId();

        // assign user
        if ($user_id)
        {
            $sql = "UPDATE user SET user_usercard_id = :usercard_id WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["usercard_id" => $usercard_id, "user_id" => $user_id]);
        }

        return $usercard_id;
    }
}

/*
class Create
{
    public static function createDevice($device_uid, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :device_uid, '0')";
        $stmt= $pdo->prepare($sql);
        $stmt->execute(["device_type" => $device_type, "device_uid" => $device_uid]);

        return $pdo->lastInsertId();
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
        $sql = "SELECT * FROM usercard WHERE usercard_uid = :uid";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $usercard = $sth->fetch();

        if (!$usercard)
            return "USERCARD_NOT_FOUND";

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
    public static function createUsercard($uid, $usercard_type)
    {
        global $pdo;
        // create usercard
        $sql = "INSERT INTO usercard (usercard_id, usercard_type, usercard_uid) VALUES (NULL, :device_type, :uid)";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $usercard_type, "uid" => $uid]);

        return $pdo->lastInsertId();
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
*/