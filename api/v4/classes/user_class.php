<?php
require_once "config.php";

class user // not tested yet
{
    /**
     * @param user_firstname string User's first name
     * @param user_lastname string User's last name
     * @param user_class int User's class id
     * @param user_usercard_id int User's usercard id (optional)
     * @param create_token array(int) User's permissions (optional)
     */
    public static function create($user_firstname, $user_lastname, $user_class, $user_usercard_id = null, $create_token = [])
    {
        global $pdo;
        self::checkUser($user_firstname, $user_lastname);

        if (isset($user_usercard_id))
        {
            $sql = "SELECT * FROM usercard WHERE usercard_id = :usercard_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["usercard_id" => $user_usercard_id]);
            $result = $stmt->fetch();
            if (!$result)
                throw new customException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400);

            $sql = "SELECT * FROM user WHERE user_usercard_id = :user_usercard_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["user_usercard_id" => $user_usercard_id]);
            $result = $stmt->fetch();
            if ($result)
                throw new customException(Response::USER_ALREADY_ASSIGNED . " (user_id: ".$result['user_id'].")", "USER_ALREADY_ASSIGNED", 400);
        }

        // insert user
        $sql = "INSERT INTO user (user_id, user_firstname, user_lastname, user_class, user_usercard_id, create_token) VALUES (NULL, :user_firstname, :user_lastname, :user_class, :user_usercard_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["user_firstname" => $user_firstname, "user_lastname" => $user_lastname, "user_class" => $user_class, "user_usercard_id" => $user_usercard_id]);
        
        $user_id = $pdo->lastInsertId();

        if (count($create_token) > 0)
        {
            // check permissions
            foreach ($create_token as $permission)
            {
                $sql = "SELECT * FROM property_token_permissions WHERE permission_id = :permission_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(["permission_id" => $permission]);
                $result = $stmt->fetch();
                if (!$result)
                    throw new customException(Response::PERMISSION_NOT_FOUND . "(id: $permission)", "PERMISSION_NOT_FOUND", 400);
            }

            // insert token
            $token_username = $user_firstname . "_" . $user_lastname;
            $token_password = "IDK";
            $token_user_id = $user_id;
            $token_permissions = json_encode($create_token);

            // check if there already is a token with the same user_id
            $sql = "SELECT * FROM token WHERE token_user_id = :token_user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["token_user_id" => $token_user_id]);
            $result = $stmt->fetch();
            if ($result)
                throw new customException(Response::USER_ALREADY_ASSIGNED_TO_TOKEN . " (token_id: ".$result['token_id'].")", "TOKEN_ALREADY_ASSIGNED", 400);
            
            // insert token
            $sql = "INSERT INTO token (token_id, token_username, token_password, token_user_id, token_permissions, token_last_change) VALUES (NULL, :token_username, :token_password, :token_user_id, :token_permissions, CURRENT_TIMESTAMP)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["token_username" => $token_username, "token_password" => $token_password, "token_user_id" => $token_user_id, "token_permissions" => $token_permissions]);
        }
    }

    private static function checkUser($firstname, $lastname)
    {
        global $pdo;
        $sql = "SELECT * FROM user WHERE user_firstname LIKE :firstname AND user_lastname LIKE :lastname";
        $sth = $pdo->prepare($sql);
        $sth->execute(["firstname" => $firstname, "lastname" => $lastname]);
        $result = $sth->fetch();

        if ($result)
            throw new CustomException(Response::USER_ALREADY_EXISTS . " (id: ".$result['user_id'].")", "USER_ALREADY_EXISTS", 400);
        return true;
    }
}