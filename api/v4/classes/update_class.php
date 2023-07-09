<?php
class Update
{
    static function update($table, $id, $updating_values, $duplicate_errorhandling = ["message" => Response::DUPLICATE, "response_code" => "DUPLICATE"], $not_found_errorhandling = ["message" => Response::ID_NOT_FOUND, "response_code" => "ID_NOT_FOUND"], $changeable_columns)
    {
        global $pdo;

        // get identity column
        $identity_column = self::getIdentityColumn($table);

        // check id:
        $sql = "SELECT * FROM " . $table . " WHERE " . $identity_column . " = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        $changes = false;

        foreach ($updating_values as $key => $value) {
            // check if key is valid
            if (!in_array($key, $changeable_columns))
                throw new CustomException(Response::INVALID_KEY, "INVALID_KEY", 400);
            
            // check if value changed (if not, skip)
            if ($row[$key] == $value || $value === "" || $value === null)
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

    private static function getIdentityColumn($table)
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