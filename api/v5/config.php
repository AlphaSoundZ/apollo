<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'classes/exception_handler_class.php';
require 'classes/response_keys_class.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
if (!$dotenv->safeLoad())
	throw new CustomException(Response::UNEXPECTED_ERROR . "Could not load .env file", "UNEXPECTED_ERROR", 500);

$__host = $_ENV['HOST'];
$__db = $_ENV['DB'];
$__username = $_ENV['USERNAME'];
$__password = $_ENV['PASSWORD'];
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

$jwt_key = $_ENV['JWT_KEY'];
$authorization_bool = (isset($_ENV['AUTHORIZATION'])) ? $_ENV['AUTHORIZATION'] : 1;

$pdo = new PDO($__dsn, $__username, $__password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getData($method, array $requirements)
{
	if ($method === "POST")
		$input = (isset($_POST)) ? json_decode(file_get_contents("php://input"), true) : false;
	elseif ($method === "GET")
		$input = (isset($_GET)) ? $_GET : false;

	if (empty($input))
	{
		$errors_str = implode(", ", $requirements);
		throw new CustomException(Response::REQUIRED_DATA_MISSING . " ($errors_str)", "REQUIRED_DATA_MISSING", 400, $requirements);
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
			$errors_str = implode(", ", $errors);
			throw new CustomException(Response::REQUIRED_DATA_MISSING . " ($errors_str)", "REQUIRED_DATA_MISSING", 400, $errors);
		}
	}
	return $input;
}

function authorize($file = null)
{
	global $pdo, $jwt_key, $authorization_bool;

	if ($authorization_bool == 0)
		return "no_auth";
	
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else throw new CustomException(Response::NOT_AUTHORIZED, "NOT_AUTHORIZED", 401);
	$jwt = explode(" ", $given_token)[1];

	// decode token
	try {
		$decoded = JWT::decode($jwt, new Key($jwt_key, 'HS256'));
		$decoded = (array) $decoded;
	} catch (Exception $e) {
		throw new CustomException(Response::NOT_AUTHORIZED . " ({$e->getMessage()})", "NOT_AUTHORIZED", 401);
	}

	// check if iat and username/password in payload are correct

	$stmt = "SELECT * FROM token WHERE token_id = '{$decoded['sub']}'";
	$stmt = $pdo->prepare($stmt);
	$stmt->execute();
	$login_data = $stmt->fetch();
	if (!$login_data)
		throw new CustomException(Response::NOT_AUTHORIZED . ": Username oder Passwort falsch", "NOT_AUTHORIZED", 401);
	
	$token_last_change = strtotime($login_data['token_last_change']);
	if ($decoded["iat"] <= $token_last_change)
		throw new CustomException(Response::NOT_AUTHORIZED . ": Token ist abgelaufen", "NOT_AUTHORIZED", 401);


	if (!$file)
		return $decoded;
	
	// check if token has the right permissions:
	$sql = "SELECT * FROM property_token_permissions WHERE permission_text = '{$file}'";
	$sth = $pdo->prepare($sql);
	$sth->execute();
	$file_id = $sth->fetch();
	if (array_key_exists("permissions", $decoded) && $file_id && in_array($file_id["permission_id"], (array) $decoded["permissions"]))
		return $decoded;
	else
		throw new CustomException(Response::NOT_ALLOWED, "NOT_ALLOWED", 403);
}