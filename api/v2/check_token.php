<?php
require 'config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use FFI\Exception;

if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
$given_token = $_SERVER["HTTP_AUTHORIZATION"];
$jwt = explode(" ", $given_token)[1];

// decode token
try {
    $decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
    $decoded = (array) $decoded;
    Response::success(Response::SUCCESS . ": Token ist valide", "SUCCESS");
    // http_response_code(200);
    // $response["permissions"] = array_values((array) $decoded["permissions"]);;
    // echo json_encode($response);
} catch (Exception $e) {
    throw new CustomException(Response::NOT_AUTHORIZED, "INVALID_TOKEN", 400);
}
}