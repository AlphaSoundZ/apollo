<?php
require 'config.php';
require 'classes/token_class.php';

$data = getData("POST", ["username", "password"]);

$username = $data["username"];
$password = $data["password"];

$token["jwt"] = Token::getToken($username, $password, $_SERVER["JWT_KEY"]);

Response::success(Response::SUCCESS, "SUCCESS", $token);