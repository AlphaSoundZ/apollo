<?php
require 'config.php';
require 'classes/token_class.php';

$given_token = $_SERVER["HTTP_AUTHORIZATION"];
$jwt = explode(" ", $given_token)[1];

$permissions["permissions"] = Token::validateToken($jwt, $_SERVER["JWT_KEY"]);
Response::success(Response::SUCCESS . ": Token ist valide", "SUCCESS", $permissions);