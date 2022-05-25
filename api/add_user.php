<?php
require 'config.php';
authorize("add_user");
$data = getData("POST", ["firstname", "lastname", "class", "usercard_uid", "new_usercard"]);


$adduser = new add_user();
$response = $adduser->checkuser($data["firstname"], $data["lastname"], $data["class"]);

if ($response["response"] == 7)
{
    $response = $adduser->checkUsercard($data["usercard_uid"], $data["new_usercard"]);
    if ($response["response"] == 3 || $response["response"] == 4) $adduser->execute($data["firstname"], $data["lastname"], $data["class"], $data["usercard_uid"], $response["response"]);
}
echo json_encode($response);


class add_user {
    function checkUser($firstname, $lastname, $class) {
        global $pdo;
        $sql = "SELECT * FROM user WHERE user_firstname = :firstname AND user_lastname = :lastname";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname]);
        $result = $sth->fetch();
        if ($result)
        {
            $response["response"] = 6;
            $response["message"] = "User ist bereits in der Datenbank";
        }
        else
        {
            // check class
            $sql = "SELECT * FROM property_class WHERE class_id = :class";
            $sth = $pdo->prepare($sql);
            $sth->execute(["class" => $class]);
            $result = $sth->fetch();
            if ($result)
            {
                $response["response"] = 7;
                $response["message"] = "User wird erstellt";
            }
            else
            {
                $response["response"] = 5;
                $response["message"] = "Klasse ist nicht in der Datenbank";
            }
        }
        return $response;
    }

    function checkUsercard($uid, $newusercard) {
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
                    $response["response"] = 1;
                    $response["message"] = "Usercard ist bereits $user zugewiesen";
                }
                else
                { // nur Zuweisung erforderlich
                    $response["response"] = 4;
                    $response["message"] = "Usercard wird nur zugewiesen";
                }
            }
            else
            {
                $response["response"] = 0;
                $response["message"] = "Usercard ist bereits in der Datenbank";
            }
        }
        elseif ($newusercard === true || $newusercard === "auto")
        { // Usercard muss erstellt werden und wird zugewiesen
            $response["response"] = 3;
            $response["message"] = "Usercard wird erstellt und zugewiesen";
        }
        else {
            $response["response"] = 2;
            $response["message"] = "Usercard Uid ist nicht in der Datenbank";
        }
        return $response;
    }
    function execute($firstname, $lastname, $class, $uid, $response_code) {
        global $pdo, $usercardtype;
        if ($response_code == 3)
        {
            // create usercard
            $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :device_type, :uid, NULL)";
            $stmt= $pdo->prepare($sql);
            $status = $stmt->execute(["device_type" => $usercardtype, "uid" => $uid]);
        }
        // usercard id

        $sql = "SELECT * FROM devices WHERE device_uid = :uid";
        $sth = $pdo->prepare($sql);
        $sth->execute(["uid" => $uid]);
        $usercard_id = $sth->fetch();

        // User erstellen
        $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_usercard_id) VALUES (NULL, :firstname, :lastname, :class, :usercard_id)";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class, "usercard_id" => $usercard_id["device_id"]]);

        return true;
    }
}

/*
Wenn Usercard noch nicht registiert ist:
Registriere usercard, wenn "new usercard" true ist;

Wenn Usercard nicht neu ist, aber "new usercard" true ist: die;
Wenn Usercard existiert und "new usercard" false ist, aber die Usercard bereits einem user zugewiesen ist: die;

*/