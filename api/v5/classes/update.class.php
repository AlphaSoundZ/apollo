<?php
class DataUpdate
{
    static function update($table, $id, $updating_values, $duplicate_errorhandling = Response::DUPLICATE, $not_found_errorhandling = Response::ID_NOT_FOUND, $changeable_columns)
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
                Response::error(Response::INVALID_KEY, [$key]);
            
            // check if value changed (if not, skip)
            if ($row[$key] == $value || $value === "" || $value === null)
                unset($updating_values[$key]);
            else
                $changes = true;
        }

        if (!$row)
            Response::error($not_found_errorhandling, ["id"]);
        if (!$changes || empty($updating_values))
            Response::success(Response::NO_CHANGES);
        
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
                Response::error($duplicate_errorhandling, ["id"]);
            
            // unexpected error
            throw $th;
        }
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