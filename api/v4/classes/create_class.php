<?php
require_once "config.php";

class Create
{
    public static function user($firstname, $lastname, $class_id, $usercard_id = null, $token_id = null, $ignore_duplicates = false) 
    {
        global $pdo;
        
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
        try {
            $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_token_id, user_usercard_id) VALUES (NULL, :firstname, :lastname, :class, :token, :usercard)";
            $sth = $pdo->prepare($sql);
            $sth->execute(["firstname" => $firstname, "lastname" => $lastname, "class" => $class_id, "token" => $token_id, "usercard" => $usercard_id]);
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1452") // check if class exists
            {
                $string = $th->getMessage();

                if (str_contains($string, "FK_user_property_class")) // class_id does not exist in property_class table
                    throw new CustomException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400);
                if (str_contains($string, "FK_user_usercard")) // usercard_id does not exist in usercard table
                    throw new CustomException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400);
                if (str_contains($string, "FK_user_token")) // token_id does not exist in token table
                    throw new CustomException(Response::TOKEN_NOT_FOUND, "TOKEN_NOT_FOUND", 400);
            }
            // unexpected error
            throw $th;
        }

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

    public static function device($uid, $type)
    {
        global $pdo;
        // check if device exists
        $sql = "SELECT * FROM devices WHERE device_uid = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["uid" => $uid]);
        if ($stmt->fetch())
            throw new CustomException(Response::DEVICE_ALREADY_EXISTS, "DEVICE_ALREADY_EXISTS", 400);
        
        // check if device type exists
        $sql = "SELECT * FROM property_device_type WHERE device_type_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $type]);
        if (!$stmt->fetch())
            throw new CustomException(Response::DEVICE_TYPE_NOT_FOUND, "DEVICE_TYPE_NOT_FOUND", 400);
        
        // insert device
        $sql = "INSERT INTO devices (device_id, device_type, device_uid, device_lend_user_id) VALUES (NULL, :type, :uid, NULL)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["uid" => $uid, "type" => $type]);

        return $pdo->lastInsertId();
    }

    public static function property_class ($text, $multi_booking)
    {
        global $pdo;
        try {
            $sql = "INSERT INTO property_class (class_id, class_name, multi_booking) VALUES (NULL, :text, :multi_booking)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["text" => $text, "multi_booking" => $multi_booking]);

            return $pdo->lastInsertId();
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::CLASS_ALREADY_EXISTS, "CLASS_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    public static function property_device_type ($text)
    {
        global $pdo;
        try {
            $sql = "INSERT INTO property_device_type (device_type_id, device_type_name) VALUES (NULL, :text)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["text" => $text]);

            return $pdo->lastInsertId();
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::DEVICE_TYPE_ALREADY_EXISTS, "DEVICE_TYPE_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    public static function property_usercard_type ($text)
    {
        global $pdo;
        try {
            $sql = "INSERT INTO property_usercard_type (usercard_type_id, usercard_type_name) VALUES (NULL, :text)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["text" => $text]);

            return $pdo->lastInsertId();
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::USERCARD_TYPE_ALREADY_EXISTS, "USERCARD_TYPE_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    public static function token ($username, $password, array $permissions)
    {
        global $pdo;
        // check permissions
        $sql = "SELECT permission_id FROM property_token_permissions";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $diff = array_diff($permissions, $stmt->fetchAll(PDO::FETCH_COLUMN));
        if ($diff)
            throw new CustomException(Response::PERMISSION_NOT_FOUND . " (Permission id: " . implode(', ', $diff) . ")", "PERMISSION_NOT_FOUND", 400);
        if (count($permissions) !== count(array_flip($permissions))) // check for duplicates entries in $permissions array
            throw new CustomException(Response::DUPLICATE_ENTRY . ". Bitte geben Sie Permissions jeweils nur einmal an!" , "DUPLICATE_ENTRY", 400);
        
        // create token
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO token (token_id, token_username, token_password, token_last_change) VALUES (NULL, :username, :password, CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["username" => $username, "password" => $password_hash]);
            
            $id = $pdo->lastInsertId();
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if username exists
                throw new CustomException(Response::TOKEN_ALREADY_EXISTS, "TOKEN_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }

        // create permissions links
        for ($i = 0; $i < count($permissions); $i++)
        {
            $sql = "INSERT INTO token_link_permissions (link_permission_id, link_token_id, link_token_permission_id) VALUES (NULL, :token, :permission)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["token" => $id, "permission" => $permissions[$i]]);
        }
        
        return $id;
    }
}