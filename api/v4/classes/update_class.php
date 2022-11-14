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
}