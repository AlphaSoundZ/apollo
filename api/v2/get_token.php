<?php
require 'config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$data = getData("POST");

$username = $data["username"];
$password_hash = md5($data["password"]); // Passwords are saved as md5 hashes in the database

$stmt = "SELECT * FROM token WHERE token_username = :username AND token_password = :password";
$stmt = $pdo->prepare($stmt);
$stmt->execute(["username" => $username, "password" => $password_hash]);
$login_data = $stmt->fetch();

$given_permissions = json_decode($login_data["token_permissions"]);
$token_id = $login_data["token_id"];

if ($login_data)
{
    $response["response"] = 0;
    $response["message"] = "success";

    // fetch permissions for payload
    $given_permissions_str = implode("', '", $given_permissions);
    $sql = "SELECT * FROM property_token_permissions WHERE permission_id IN ('$given_permissions_str')";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $given_permission_names = $sth->fetchAll(\PDO::FETCH_ASSOC);
    $given_permission_names = array_column($given_permission_names, 'permission_text');
    $permissions = array_combine($given_permissions, $given_permission_names);

    $payload = [
        'permissions' => $permissions,
        'username' => $username,
        'password' => $password_hash,
    ];
    $jwt = JWT::encode($payload, $jwt_key, 'HS256');

    $response["jwt"] = $jwt;
    $response["response"] = 0;
    $response["message"] = "success";
}
else
{
    $response["response"] = 1;
    $response["message"] = "username oder password is wrong";
}

echo json_encode($response);