<?php

declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: DELETE, POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'classes/exception_handler.class.php';
require_once 'classes/response_keys.class.php';
require_once 'classes/token.class.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
if (!$dotenv->safeLoad())
	Response::error(array_merge(Response::INTERNAL_SERVER_ERROR, ["message" => "Could not load .env file"]));

// Database
$__host = $_ENV['HOST'];
$__db = $_ENV['DB'];
$__username = $_ENV['USERNAME'];
$__password = $_ENV['PASSWORD'];
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

$jwt_key = $_ENV['JWT_KEY'];
$authorization_bool = (isset($_ENV['AUTHORIZATION'])) ? $_ENV['AUTHORIZATION'] : 1;

try {
	$pdo = new PDO($__dsn, $__username, $__password);
} catch (\Throwable $th) {
	Response::error(array_merge(Response::INTERNAL_SERVER_ERROR, ["message" => "Could not connect to database"]));
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getData($method, array $requirements, array $optional = [])
{
	if ($method === "POST")
		$input = (isset($_POST)) ? json_decode(file_get_contents("php://input"), true) : false;
	elseif ($method === "GET")
		$input = (isset($_GET)) ? $_GET : false;


	if (empty($input)) {
		$errors_str = implode(", ", $requirements);
		Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " ($errors_str)"]), $requirements, ["optional_fields" => $optional]);
	}

	if (isset($requirements) && $input) {
		$errors = [];
		foreach ($requirements as $r) {
			if (!array_key_exists($r, $input) || empty($input[$r]))
				array_push($errors, $r);
		}
		if (!empty($errors)) {
			$errors_str = implode(", ", $errors);
			Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " ($errors_str)"]), $errors, ["optional_fields" => $optional]);
		}
	}
	return $input;
}

function authorize($permission = null, $callback = null)
{
	global $pdo, $jwt_key, $authorization_bool;

	if (isset($_SERVER["HTTP_AUTHORIZATION"]))
		$given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else if ($authorization_bool != 0)
		Response::error(Response::NOT_AUTHORIZED);
	else
		return ["permissions" => [], "id" => null, "username" => null];

	$jwt_raw = explode(" ", $given_token);

	if ((count($jwt_raw) != 2 || $jwt_raw[0] != "Bearer")) {
		if ($authorization_bool != 0)
			Response::error(Response::NOT_AUTHORIZED);
		else
			return ["permissions" => [], "id" => null, "username" => null];
	}

	$jwt = $jwt_raw[1];

	$token = Token::validateToken($jwt, $jwt_key);
	$permissions = $token["permissions"];

	if (!$permission || $authorization_bool == 0)
		return $token;

	// check if token has the right permission:
	$sql = "SELECT * FROM property_token_permissions WHERE permission_text = '{$permission}'";
	$sth = $pdo->prepare($sql);
	$sth->execute();
	$permission_id = $sth->fetch();
	if ($permissions && $permission_id && in_array($permission_id["permission_id"], $permissions))
		return $token;
	else {
		// run callback if given
		if ($callback) {
			if ($callback())
				return $token;
			else
				Response::error(Response::NOT_ALLOWED, [], ["permission" => $permission]);
		} else {
			Response::error(Response::NOT_ALLOWED, [], ["permission" => $permission]);
		}
	}
}
