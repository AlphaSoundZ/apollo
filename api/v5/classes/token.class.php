<?php
require_once "config.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Token
{
    public static function getToken($username, $password, $key)
    {
        global $pdo;
        $sql = "SELECT * FROM token WHERE token_username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["username" => $username]);
        $login_data = $stmt->fetch();

        if (!$login_data || !password_verify($password, $login_data["token_password"]))
            Response::error(array_merge(Response::NOT_AUTHORIZED, ["message" => Response::NOT_AUTHORIZED["message"] . ": Username oder Passwort falsch"]), ["username", "password"]);
        
        $token_id = $login_data["token_id"];

        $sql = "SELECT link_token_permission_id FROM token_link_permissions WHERE link_token_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $token_id]);
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $payload = [
            'permissions' => $permissions,
            'sub' => $token_id,
            'iat' => round(microtime(true)),
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }

    public static function validateToken($jwt, $key)
    {
        global $pdo;
        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $decoded = (array) $decoded;
        } catch (Exception $e) {
            Response::error(array_merge(Response::NOT_AUTHORIZED, ["message" => Response::NOT_AUTHORIZED["message"] . " ({$e->getMessage()})"]));
        }
        
        // check if iat and username in payload are correct
        $stmt = "SELECT * FROM token WHERE token_id = '{$decoded['sub']}'";
        $stmt = $pdo->prepare($stmt);
        $stmt->execute();
        $login_data = $stmt->fetch();       
        
        if (!$login_data)
        Response::error(array_merge(Response::NOT_AUTHORIZED, ["message" => "Username oder Passwort falsch"]), ["username", "password"]);
        else if ($decoded["iat"] <= strtotime($login_data['token_last_change']))
            Response::error(array_merge(Response::NOT_AUTHORIZED, ["message" => Response::NOT_AUTHORIZED["message"] . ". Token ist abgelaufen"]));
        
        return ["permissions" => array_values((array) $decoded["permissions"]), "username" => $login_data["token_username"], "id" => $decoded["sub"]];
    }
}