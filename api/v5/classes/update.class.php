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

    public static function token($id, $updating_values)
    {
        global $pdo;

        $changeable_columns = [
            "token_username",
            "token_password",
            "token_user_id",
            "permissions",
        ];

        // check id:
        $sql = "SELECT * FROM token WHERE token_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $id]);
        $row = $stmt->fetch();

        $changes = false;

        foreach ($updating_values as $key => $value) {
            if ($value === null) {
                unset($updating_values[$key]);
                continue;
            }

            // check if key is valid
            if (!in_array($key, $changeable_columns))
                Response::error(Response::INVALID_KEY, [$key]);

            // check if key is "permisions"
            if ($key == "permissions") {

                $sql = "SELECT link_token_permission_id FROM token_link_permissions WHERE link_token_id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(["id" => $id]);
                $current_permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $sql = "SELECT permission_id FROM property_token_permissions";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

                // check if permissions are valid
                foreach ($value as $permission) {
                    if (!in_array($permission, $permissions))
                        Response::error(Response::INVALID_PERMISSION, [$permission]);
                }


                // get permissions to create
                $new_permissions = array_diff($value, $current_permissions);

                // get permissions to delete
                $delete_permissions = array_diff($current_permissions, $value);

                // add permissions
                foreach ($new_permissions as $permission) {
                    $sql = "INSERT INTO token_link_permissions (link_token_id, link_token_permission_id) VALUES (:id, :permission)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(["id" => $id, "permission" => $permission]);
                }

                // delete permissions
                foreach ($delete_permissions as $permission) {
                    $sql = "DELETE FROM token_link_permissions WHERE link_token_id = :id AND link_token_permission_id = :permission";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(["id" => $id, "permission" => $permission]);
                }

                if (!empty($delete_permissions) || !empty($new_permissions)) {
                    $changes = true;
                }

                unset($updating_values[$key]);
                continue;
            } else if ($key == "token_password") {
                $updating_values[$key] = password_hash($value, PASSWORD_BCRYPT);
                $changes = true;
                continue;
            }

            // check if value changed (if not, skip)
            if ($row[$key] == $value || $value === "" || $value === null)
                unset($updating_values[$key]);
            else
                $changes = true;
        }


        if (!$row)
            Response::error(Response::ID_NOT_FOUND, ["id"]);
        if (!$changes || (empty($updating_values) && !$changes))
            Response::success(Response::NO_CHANGES);

        try {
            $sql = "UPDATE token SET ";

            foreach ($updating_values as $key => $value) {
                $sql .= $key . " = :" . $key . ", ";
            }

            $sql .= "token_last_change = NOW()";

            $sql .= " WHERE token_id = :id";

            $sth = $pdo->prepare($sql);
            $result = $sth->execute(array_merge($updating_values, ["id" => $id]));
        } catch (PDOException $th) {
            if ($th->errorInfo[1] == "1062") // check if class exists
                Response::error(Response::DUPLICATE, ["id"]);

            // unexpected error
            throw $th;
        }
    }
}
