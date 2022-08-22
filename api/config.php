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

$responseX["response"] = null;
$responseX["message"] = null;

try {
	$pdo = new PDO($__dsn, $__username, $__password);
	//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$usercardtype = $pdo->query("SELECT * FROM property_device_type WHERE device_type_name = 'Usercard'")->fetch();
	$usercardtype = $usercardtype['device_type_id'];
	$multiuser = $pdo->query("SELECT * FROM property_class WHERE class_name = 'Lehrer'")->fetch();
	$multiuser = $multiuser['class_id']; // multiuser is allowed to borrow more than one device
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
		if (empty($input))
			$input = false;
	}
	elseif ($method === "GET")
	{
		$input = (isset($_GET)) ? json_decode($_GET['data'], true) : false;
	}
	if (isset($requirements) && $input)
	{
		$errors = [];
		foreach ($requirements as $r) {
			if (!array_key_exists($r, $input) || empty($input[$r]))
				array_push($errors, $r);
		}
		if (!empty($errors))
		{
			$responseX["response"] = 77;
			$errors = implode(", ", $errors);
			$responseX["message"] = "Some input is missing ($errors)";
			echo json_encode($responseX);
			http_response_code(400);
			die;
		}
	}
	return $input;
}

function authorize($file)
{
	global $pdo, $jwt_key, $responseX;
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else return false;
	$jwt = explode(" ", $given_token)[1];

	// decode token
	try {
		$decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
		$decoded = (array) $decoded;
	} catch (Exception $e) {
		$responseX["response"] = 88;
		$responseX["message"] = "401 Unauthorized";
		echo json_encode($responseX);
		http_response_code(401);
		die;
	}

	// check if username and password in payload are correct
	$sql = "SELECT * FROM token WHERE token_username = '{$decoded['username']}' AND token_password = '{$decoded['password']}'";
	$sth = $pdo->prepare($sql);
	$sth->execute();
	$login = $sth->fetch();
	
	if ($login)
	{
		if (array_key_exists("permissions", $decoded) && in_array($file, (array) $decoded["permissions"]))
			return true;
		else
		{
			$responseX["response"] = 99;
			$responseX["message"] = "405 you dont have the permission to access this content";
		}
	}
	else
	{
		$responseX["response"] = 88;
		$responseX["message"] = "401 Unauthorized";
	}
	echo json_encode($responseX);
	die;
}