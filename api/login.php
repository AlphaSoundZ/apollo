<?php
require 'config.php';

authorize("login");

$data = getData("POST");

$md5_username = md5($data["username"]);
$md5_password = md5($data["password"]);

$stmt = "SELECT * FROM login";
$stmt = $pdo->prepare($stmt);
$stmt->execute();
$login_data = $stmt->fetch();

if ($login_data["username"] == $md5_username && $login_data["password"] == $md5_password)
{
    $response["response"] = 0;
    $response["message"] = "success";
}
else
{
    $response["response"] = 1;
    $response["message"] = "username oder password is wrong";
}

echo json_encode($response);