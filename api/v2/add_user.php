<?php
require 'config.php';
authorize("add_user");
$data = getData("POST", ["firstname", "lastname", "class", "usercard_uid", "new_usercard"]);


$adduser = new add_user();
$firstname = $data["firstname"];
$lastname = $data["lastname"];
$class = $data["class"];
$usercard_uid = $data["usercard_uid"];
$new_usercard = $data["new_usercard"];


class add_user {
    public static function checkUser($firstname, $lastname) {
        global $pdo;
        $sql = "SELECT * FROM user WHERE user_firstname = :firstname AND user_lastname = :lastname";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname]);
        $result = $sth->fetch();

        if ($result)
            throw new CustomException(Response::USER_ALREADY_EXISTS, "USER_ALREADY_EXISTS", 400);
        return true;
    }

    public static function checkClass($class)
    {
        global $pdo;
        $sql = "SELECT * FROM property_class WHERE class_id = :class";
        $sth = $pdo->prepare($sql);
        $sth->execute(["class" => $class]);
        $result = $sth->fetch();
        if (!$result)
            throw new CustomException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400);
        return true;
    }
    public static function checkUsercard($uid, $newusercard)
    {
        global $pdo;
        $sql = "SELECT * FROM devices WHERE device_uid = :uid";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $result = $sth->fetch();
        if ($result) {
            if ($newusercard === false || $newusercard === "auto")
            {
                $sql = "SELECT * FROM user WHERE user_usercard_id = :usercard_id";
                $sth = $pdo->prepare($sql);
                $sth->execute(["usercard_id" => $result["device_id"]]);
                $result = $sth->fetch();
                if ($result)
                {
                    $user = $result["user_firstname"].' '.$result["user_lastname"];
                    throw new CustomException(Response::USERCARD_ALREADY_ASSIGNED . "(User: $user)", "USERCARD_ALREADY_ASSIGNED", 400);
                } // else: no user assigned to this usercard, so assign
                return "ASSIGN_USERCARD_ONLY";
            }
            throw new CustomException(Response::USERCARD_ALREADY_EXISTS, "USERCARD_ALREADY_EXISTS", 400);
        }
        elseif ($newusercard === true || $newusercard === "auto") // Usercard muss erstellt werden und wird zugewiesen
            return "CREATE_USERCARD";
        throw new CustomException(Response::DEVICE_NOT_FOUND, "DEVICE_NOT_FOUND", 400);
    }
    public static function createUser($firstname, $lastname, $class) {
        global $pdo;
        // User erstellen
        $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_usercard_id) VALUES (NULL, :firstname, :lastname, :class, NULL)";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class]);

        return true;
    }
    public static function createUsercard($uid)
    {
        global $pdo;
        // create usercard
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :uid, NULL)";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $_SERVER["USERCARD_TYPE"], "uid" => $uid]);
        // get usercard id
        $sql = "SELECT * FROM devices WHERE device_uid = :uid";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $usercard_id = $sth->fetch();
        return $usercard_id["device_id"];
    }    
    public static function assignUserToUsercard($user_id, $usercard_id)
    {
        global $pdo;
        $sql = "UPDATE user SET user_usercard_id = :usercard_id WHERE user_id = :user_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["user_id" => $user_id, "usercard_id" => $usercard_id]);
        return true;
    }
}

/*
Wenn Usercard noch nicht registiert ist:
Registriere usercard, wenn "new usercard" true ist;

Wenn Usercard nicht neu ist, aber "new usercard" true ist: die;
Wenn Usercard existiert und "new usercard" false ist, aber die Usercard bereits einem user zugewiesen ist: die;

*/