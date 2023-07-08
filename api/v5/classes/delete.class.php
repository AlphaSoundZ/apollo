<?php
require_once 'config.php';
class Delete
{
    static function deleteRow($table, $id) // delete row by id in table
    {
        global $pdo;
        $identityColumn = self::getIdentityColumn($table);

        $sql = "SELECT * FROM $table WHERE $identityColumn = '$id'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == false)
            throw new CustomException(Response::ID_NOT_FOUND, "ID_NOT_FOUND", 400, ["id"]);
        
        $sql = "DELETE FROM $table WHERE $identityColumn = '$id'";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
    }

    static function delete($table, $id, $not_found_errorhandling = ["message" => Response::ID_NOT_FOUND, "response_code" => "ID_NOT_FOUND"], $foreign_key_errorhandling = ["message" => Response::FOREIGN_KEY_ERROR, "response_code" => "FOREIGN_KEY_ERROR"]) 
    {
        global $pdo;
        $identityColumn = self::getIdentityColumn($table);

        $sql = "SELECT * FROM $table WHERE $identityColumn = '$id'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == false)
            throw new CustomException($not_found_errorhandling["message"], $not_found_errorhandling["response_code"], 400, ["id"]);
        
        try {
            $sql = "DELETE FROM $table WHERE $identityColumn = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["id" => $id]);
            
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1451)
                throw new CustomException($foreign_key_errorhandling["message"], $foreign_key_errorhandling["response_code"], 400, ["id"]);
            else
                throw new CustomException($e->getMessage(), "BAD_REQUEST", 400);
        }
    }

    static function deleteToken($id, $token)
    {
        global $pdo;
        $identityColumn = self::getIdentityColumn("token");
        // check if id is valid

        $sql = "SELECT * FROM token WHERE $identityColumn = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        if (!$row)
            throw new CustomException(Response::TOKEN_NOT_FOUND, "TOKEN_NOT_FOUND", 400, ["id"]);
        else if ($id == $token['sub'])
            throw new CustomException(Response::DELETE_OWN_TOKEN_NOT_ALLOWED, "DELETE_OWN_TOKEN_NOT_ALLOWED", 400, ["id"]);
        
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
                throw new CustomException(Response::FOREIGN_KEY_ERROR, "FOREIGN_KEY_ERROR", 400, ["id"]);
            else // other error
                throw new CustomException($e->getMessage(), "BAD_REQUEST", 400);
        }
    }

    static function reset($table, $reset_id, $condition = null)
    {
        global $pdo;
        $sql = "SELECT COUNT(1) FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";
        $sth = $pdo->query($sql);
        $countCondition = $sth->fetchAll();

        $sql = "SELECT COUNT(1) FROM $table";
        $sth = $pdo->query($sql);
        $countAll = $sth->fetchAll();
        /* // Could be useful in the future if wanted to throw an error when table is empty
        if (!$countAll)
            throw new CustomException("$table table ist leer", "BAD_REQUEST", 400, ["table"]);
        else if ($condition && empty($countCondition[0]["COUNT(1)"]))
            throw new CustomException("In $table wurden keine löschbaren Zeilen gefunden", "BAD_REQUEST", 400, ["table", "condition"]);
        */
        $sql = ($reset_id && !$condition) ? "TRUNCATE TABLE $table" : "DELETE FROM $table";
        $sql .= ($condition) ? " WHERE $condition" : "";

        if ($countCondition[0]["COUNT(1)"] == $countAll[0]["COUNT(1)"] && $reset_id)
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
            throw new CustomException(Response::USER_NOT_FOUND, "USER_NOT_FOUND", 400, ["id"]);

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
        $sql = "SELECT * FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!$result)
            throw new CustomException(Response::EMPTY_TABLE, "EMPTY_TABLE", 400, ["table"]);
        return array_key_first($result);
    }
}