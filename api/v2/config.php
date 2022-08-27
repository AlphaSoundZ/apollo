<?php
declare(strict_types=1);

set_exception_handler(function ($e) {
	$data["response"] = $e->getCode();
	$data["message"] = $e->getMessage();
	echo json_encode($data);
	
	$status_code = array("100","101","200","201","202","203","204","205","206","300","301","302","303","304","305","306","307","400","401","402","403","404","405","406","407","408","409","410","411","412","413","414","415","416","417","500","501","502","503","504","505");
	
	if(in_array($e->getCode(), $status_code))
		http_response_code($e->getCode());
	else
		http_response_code(400);
	
	die;
} );

require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
if (!$dotenv->safeLoad())
	throw new Exception("Could not load .env file", 400);


$__host = $_ENV['HOST'];
$__db = $_ENV['DB'];
$__username = $_ENV['USERNAME'];
$__password = $_ENV['PASSWORD'];
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

$jwt_key = $_ENV['JWT_KEY'];

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

	}
	elseif ($method === "GET")
	{
		$input = (isset($_GET)) ? json_decode($_GET['data'], true) : false;
	}

	if (empty($input))
		throw new Exception("No input", 400);

	if (isset($requirements) && $input)
	{
		$errors = [];
		foreach ($requirements as $r) {
			if (!array_key_exists($r, $input) || empty($input[$r]))
				array_push($errors, $r);
		}
		if (!empty($errors))
		{
			$errors = implode(", ", $errors);
			throw new Exception("Some input is missing ($errors)", 400);
		}
	}
	return $input;
}

function authorize($file)
{
	global $pdo, $jwt_key;
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else throw new Exception("No token provided", 401);
	$jwt = explode(" ", $given_token)[1];

	// decode token
	try {
		$decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
		$decoded = (array) $decoded;
	} catch (Exception $e) {
		throw new Exception($e->getMessage(), 401);
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
			throw new Exception("403 Forbidden", 403);
		}
	}
	else
	{
		throw new Exception("401 Unauthorized", 401);
	}
}