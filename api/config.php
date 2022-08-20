<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

$jwt_key = 'example_key';

$response["response"] = null;
$response["message"] = null;

try {
	$pdo = new PDO($__dsn, $__username, $__password);
	//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$usercardtype = $pdo->query("SELECT * FROM property_device_type WHERE device_type_name = 'UserCard'")->fetch();
	$usercardtype = $usercardtype['device_type_id'];
} catch (PDOException $e) {
	$data['message'] = $e->getMessage();
	$data['response'] = "10";
	echo json_encode($data);
	die;
}

function getData($method, $requirements = [])
{
	if ($method === "POST")
	{
		$input = (isset($_POST)) ? json_decode(file_get_contents("php://input"), true) : false;
	}
	elseif ($method === "GET")
	{
		$input = (isset($_GET)) ? json_decode($_GET['data'], true) : false;
	}
	if (isset($requirements) && isset($input))
	{
		$errors = [];
		foreach ($requirements as $r) {
			if (!array_key_exists($r, $input))
			{
				array_push($errors, $r);
			}
		}
		if (!empty($errors))
		{
			$response["response"] = 77;
			$errors = implode(", ", $errors);
			$response["message"] = "Some input is missing ($errors)";
			echo json_encode($response);
			http_response_code(400);
			die;
		}
	}
	return $input;
}

function authorize($file)
{
	global $pdo, $jwt_key, $response;
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else return false;
	$jwt = explode(" ", $given_token)[1];

	// decode token
	try {
		$decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
		$decoded = (array) $decoded;
	} catch (\Throwable) {
		$response["response"] = 88;
		$response["message"] = "401 Unauthorized";
		echo json_encode($response);
		http_response_code(401);
		die;
	}
	// check for permission
	if (in_array($file, (array) $decoded["permissions"])) return true;
	else
	{
		$response["response"] = 99;
		$response["message"] = "405 you dont have the permission to access this content";
	}
	echo json_encode($response);
	die;
}