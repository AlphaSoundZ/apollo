<?php
require 'config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
$given_token = $_SERVER["HTTP_AUTHORIZATION"];
$jwt = explode(" ", $given_token)[1];

// decode token
try {
    $decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
    $decoded = (array) $decoded;
    $response["response"] = 0;
    $response["message"] = "Authorized";
    $response["permissions"] = array_values((array) $decoded["permissions"]);;
    echo json_encode($response);
} catch (\Throwable) {
    $response["response"] = 88;
    $response["message"] = "401 Unauthorized";
    echo json_encode($response);
    die;
}
}