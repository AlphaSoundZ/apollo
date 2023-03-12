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

    static function device($id, $data)
    {
        global $pdo;

        // check id:
        $sql = "SELECT * FROM devices WHERE device_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        $changes = false;

        foreach ($data as $key => $value) {
            if ($value === "")
                throw new CustomException(Response::EMPTY_FIELD, "EMPTY_FIELD", 400);
            
            // check if key is valid
            if (!in_array($key, ["device_uid", "device_type"]))
                throw new CustomException(Response::INVALID_KEY, "INVALID_KEY", 400);
            
            // check if value changed (if not, skip)
            if (($row[$key] ?? null) == $value)
                unset($data[$key]);
            else
                $changes = true;
        }

        if (!$row)
            throw new CustomException(Response::DEVICE_NOT_FOUND, "DEVICE_NOT_FOUND", 400);
        if (!$changes)
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        if (empty($data))
            Response::success("Keine Änderung: alter und neuer Wert sind indentisch", "SUCCESS");
        
        try {
            $sql = "UPDATE devices SET ";
            
            foreach ($data as $key => $value) {
                $sql .= $key . " = :" . $key . ", ";
            }

            $sql = substr($sql, 0, -2); // remove last ", "

            $sql .= " WHERE device_id = :id";

            $sth = $pdo->prepare($sql);
            $result = $sth->execute(array_merge($data, ["id" => $id]));
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                throw new CustomException(Response::DEVICE_ALREADY_EXISTS, "DEVICE_ALREADY_EXISTS", 400);
            
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
}