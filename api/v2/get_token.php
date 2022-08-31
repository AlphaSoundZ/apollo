<?php
require 'config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$data = getData("POST");

$stmt = "SELECT * FROM token WHERE token_username = :username";
$stmt = $pdo->prepare($stmt);
$stmt->execute(["username" => $data["username"]]);
$login_data = $stmt->fetch();

if (!$login_data || !password_verify($data["password"], $login_data["token_password"]))
    throw new CustomException("401 Unauthorized. Username or password is wrong", 9, 401);

$given_permissions = json_decode($login_data["token_permissions"]);
$token_id = $login_data["token_id"];

$payload = [
    'permissions' => $given_permissions,
    'sub' => $token_id,
    'iat' => round(microtime(true)),
];
$jwt = JWT::encode($payload, $jwt_key, 'HS256');

$response["jwt"] = $jwt;
$response["response"] = 0;
$response["message"] = "success";

echo json_encode($response);
