<?php
class Update
{
    static function property_class($id, $value)
    {
        global $pdo;

        // check id:
        $sql = "SELECT * FROM property_class WHERE class_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        if (!$row)
            throw new CustomException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400);
        if ($row["class_name"] == $value)
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        
        try {
            $sql = "UPDATE property_class SET class_name = :value WHERE class_id = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["value" => $value, "id" => $id]);    
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::CLASS_ALREADY_EXISTS, "CLASS_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    static function property_device_type($id, $value)
    {
        global $pdo;

        // check id:
        $sql = "SELECT * FROM property_device_type WHERE device_type_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        if (!$row)
            throw new CustomException(Response::DEVICE_TYPE_NOT_FOUND, "DEVICE_TYPE_NOT_FOUND", 400);
        if ($row["device_type_name"] == $value)
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        
        try {
            $sql = "UPDATE property_device_type SET device_type_name = :value WHERE device_type_id = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["value" => $value, "id" => $id]);    
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::DEVICE_TYPE_ALREADY_EXISTS, "DEVICE_TYPE_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    static function update($table, $id, $updating_values, $duplicate_errorhandling = ["message" => Response::DUPLICATE, "response_code" => "DUPLICATE"], $not_found_errorhandling = ["message" => Response::ID_NOT_FOUND, "response_code" => "ID_NOT_FOUND"], $changeable_columns)
    {
        global $pdo;

        // get identity column
        $identity_column = self::getIndentityColumn($table);

        // check id:
        $sql = "SELECT * FROM " . $table . " WHERE " . $identity_column . " = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        $changes = false;

        foreach ($updating_values as $key => $value) {
            if ($value === "")
                throw new CustomException(Response::EMPTY_FIELD, "EMPTY_FIELD", 400);
            
            // check if key is valid
            if (!in_array($key, $changeable_columns))
                throw new CustomException(Response::INVALID_KEY, "INVALID_KEY", 400);
            
            // check if value changed (if not, skip)
            if (($row[$key] ?? null) == $value)
                unset($updating_values[$key]);
            else
                $changes = true;
        }

        if (!$row)
            throw new CustomException($not_found_errorhandling["message"], $not_found_errorhandling["response_code"], 400);
        if (!$changes)
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        if (empty($updating_values))
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        
        try {
            $sql = "UPDATE " . $table . " SET ";
            
            foreach ($updating_values as $key => $value) {
                $sql .= $key . " = :" . $key . ", ";
            }

            $sql = substr($sql, 0, -2); // remove last ", "

            $sql .= " WHERE " . $identity_column . " = :id";

            $sth = $pdo->prepare($sql);
            $result = $sth->execute(array_merge($updating_values, ["id" => $id]));
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException($duplicate_errorhandling["message"], $duplicate_errorhandling["response_code"], 400);
            
            // unexpected error
            throw $th;
        }
    }

    static function property_usercard_type($id, $value)
    {
        global $pdo;

        // check id:
        $sql = "SELECT * FROM property_usercard_type WHERE usercard_type_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        if (!$row)
            throw new CustomException(Response::USERCARD_TYPE_NOT_FOUND, "USERCARD_TYPE_NOT_FOUND", 400);
        if ($row["usercard_type_name"] == $value)
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        
        try {
            $sql = "UPDATE property_usercard_type SET usercard_type_name = :value WHERE usercard_type_id = :id";
            $sth = $pdo->prepare($sql);
            $result = $sth->execute(["value" => $value, "id" => $id]);    
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::USERCARD_TYPE_ALREADY_EXISTS, "USERCARD_TYPE_ALREADY_EXISTS", 400);
            
            // unexpected error
            throw $th;
        }
    }

    private static function getIndentityColumn($table)
    {
        global $pdo;
        $sql = "SELECT * FROM $table";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if (!$result)
            throw new CustomException("Tabelle ist leer", "BAD_REQUEST", 400);
        return array_key_first($result);
    }
}