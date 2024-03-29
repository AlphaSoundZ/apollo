<?php
require_once "config.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Token
{
    public static function getToken($username, $password, $key)
    {
        global $pdo;
        $stmt = "SELECT * FROM token WHERE token_username = :username";
        $stmt = $pdo->prepare($stmt);
        $stmt->execute(["username" => $username]);
        $login_data = $stmt->fetch();

        if (!$login_data || !password_verify($password, $login_data["token_password"]))
            throw new CustomException("401 Unauthorized. Username or password is wrong", 9, 401);

        $given_permissions = json_decode($login_data["token_permissions"]);
        $token_id = $login_data["token_id"];

        $payload = [
            'permissions' => $given_permissions,
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
            throw new CustomException(Response::NOT_AUTHORIZED . ": " . $e->getMessage(), "NOT_AUTHORIZED", 401);
        }
        
        // check if iat and username in payload are correct
        $stmt = "SELECT * FROM token WHERE token_id = '{$decoded['sub']}'";
        $stmt = $pdo->prepare($stmt);
        $stmt->execute();
        $login_data = $stmt->fetch();
        $token_last_change = strtotime($login_data['token_last_change']);
        if (!$login_data || $decoded["iat"] <= $token_last_change)
            throw new CustomException(Response::NOT_AUTHORIZED . ": Username oder Passwort falsch", "NOT_AUTHORIZED", 401);

        
        return array_values((array) $decoded["permissions"]);
    }
}