<?php
require_once 'config.php';
class DataDelete
{
    static function delete($table, $id, $not_found_errorhandling = Response::ID_NOT_FOUND, $foreign_key_errorhandling = Response::FOREIGN_KEY_ERROR) 
    {
        global $pdo;
        $identityColumn = self::getIdentityColumn($table);

        $sql = "SELECT * FROM $table WHERE $identityColumn = '$id'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == false) // check if id exists
            Response::error($not_found_errorhandling, ["id"]);
        
        try {
            $sql = "DELETE FROM $table WHERE $identityColumn = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["id" => $id]);
            
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1451) // check for constraint error
                Response::error($foreign_key_errorhandling, ["id"]);
            else
                throw $e;
        }
    }

    static function deleteToken($id, $own_token_id)
    {
        global $pdo;
        $identityColumn = self::getIdentityColumn("token");
        // check if id is valid

        $sql = "SELECT * FROM token WHERE $identityColumn = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        if (!$row)
            Response::error(Response::TOKEN_NOT_FOUND, ["id"]);
        else if ($id == $own_token_id)
            Response::error(Response::DELETE_OWN_TOKEN_NOT_ALLOWED, ["id"]);
        
        try {
            // delete linked permissions
            $sql = "DELETE FROM token_link_permissions WHERE link_token_id = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["id" => $id]);

            // delete token itself
            $sql = "DELETE FROM token WHERE $identityColumn = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["id" => $id]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1451) // foreign key error (token_link_permissions)
                Response::error(Response::FOREIGN_KEY_ERROR, ["id"]);
            else // other error
                throw $e;
        }
    }

    static function deletePrebook($id, $own_token_id = null)
    {
        global $pdo;

        if ($own_token_id == null)
        {
            // check if user is allowed to delete prebook (user of token is owner of prebook, or user has CRUD_prebook permission)
            $sql = "SELECT * FROM token WHERE token_id = :token_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                "token_id" => $own_token_id
            ));
            $token_user = $stmt->fetch(PDO::FETCH_ASSOC)["user_id"];
    
            // get user_id of prebook
            $sql = "SELECT * FROM prebook WHERE prebook_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                "id" => $id
            ));
            $user_id = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$user_id)
                Response::error(Response::PREBOOK_NOT_FOUND, ["id"]);
            
            $user_id = $user_id["prebook_user_id"];
            
            if ($user_id != $token_user && !isset(authorize()["permissions"]["CRUD_prebook"]))
            {
                // user is not allowed to delete prebook for others
                Response::error(Response::NOT_ALLOWED);
            }
        }
        
        self::delete("prebook", $id, Response::PREBOOK_NOT_FOUND, Response::FOREIGN_KEY_ERROR);
    }

    static function reset($table, bool $reset_primary, $condition = null)
    {
        global $pdo;
        $sql = "SELECT COUNT(1) FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";
        $sth = $pdo->query($sql);
        $countCondition = $sth->fetchAll();

        $sql = "SELECT COUNT(1) FROM $table";
        $sth = $pdo->query($sql);
        $countAll = $sth->fetchAll();

        $sql = ($reset_primary && !$condition) ? "TRUNCATE TABLE $table" : "DELETE FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";

        if ($countCondition[0]["COUNT(1)"] == $countAll[0]["COUNT(1)"] && $reset_primary)
            $sql = "TRUNCATE TABLE $table";
        
        $sth = $pdo->prepare($sql);
        $sth->execute();
        return $countCondition[0]["COUNT(1)"];
    }

    static function clearEvent() // delete active events
    {
        global $pdo;

        // set device_lend_user_id to 0 for all devices that are currently lent
        $sql = "UPDATE devices SET device_lend_user_id = 0 WHERE device_lend_user_id != 0";
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // delete all events that are currently active
        $sql = "DELETE FROM event WHERE event_end IS NULL";
        $sth = $pdo->prepare($sql);
        $sth->execute();

        // return number of deleted rows
        return $sth->rowCount();
    }

    static function clearUserEvent($user_id) // delete active events of user
    {
        global $pdo;

        // check for user
        $sql = "SELECT * FROM user WHERE user_id = :id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["id" => $user_id]);

        if (!$sth->fetch())
            Response::error(Response::USER_NOT_FOUND, ["id"]);

        // set device_lend_user_id to 0 for all devices that are currently lent
        $sql = "UPDATE devices SET device_lend_user_id = 0 WHERE device_lend_user_id = :id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["id" => $user_id]);

        // delete all events that are currently active of user
        $sql = "DELETE FROM event WHERE event_end IS NULL AND event_user_id = :id";
        $sth = $pdo->prepare($sql);
        $sth->execute(["id" => $user_id]);

        // return number of deleted rows
        return $sth->rowCount();
    }

    private static function getIdentityColumn($table)
    {
        global $pdo;
        $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table_name AND COLUMN_KEY = 'PRI'";
        $sth = $pdo->prepare($sql);
        $sth->execute(["table_name" => $table]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!$result)
            Response::error(Response::INTERNAL_SERVER_ERROR);
        return $result["COLUMN_NAME"];
    }
}