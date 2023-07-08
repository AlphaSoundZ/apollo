<?php
declare(strict_types=1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'classes/exception_handler.class.php';
require 'classes/response_keys.class.php';
require 'classes/token.class.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
if (!$dotenv->safeLoad())
	Response::error(array_merge(Response::INTERNAL_SERVER_ERROR, ["message" => "Could not load .env file"]));

$__host = $_ENV['HOST'];
$__db = $_ENV['DB'];
$__username = $_ENV['USERNAME'];
$__password = $_ENV['PASSWORD'];
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

$jwt_key = $_ENV['JWT_KEY'];
$authorization_bool = (isset($_ENV['AUTHORIZATION'])) ? $_ENV['AUTHORIZATION'] : 1;

$pdo = new PDO($__dsn, $__username, $__password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getData($method, array $requirements, array $optional = [])
{
	if ($method === "POST")
		$input = (isset($_POST)) ? json_decode(file_get_contents("php://input"), true) : false;
	elseif ($method === "GET")
		$input = (isset($_GET)) ? $_GET : false;

	if (empty($input))
	{
		$errors_str = implode(", ", $requirements);
		Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " ($errors_str)"]), $requirements, ["optional_fields" => $optional]);
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
			Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " ($errors_str)"]), $requirements, ["optional_fields" => $optional]);
		}
	}
	return $input;
}

function authorize($permission = null)
{
	global $pdo, $jwt_key, $authorization_bool;

	if ($authorization_bool == 0)
		return "no_auth";
	
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else Response::error(Response::NOT_AUTHORIZED);
	$jwt = explode(" ", $given_token)[1];

	$permissions = Token::validateToken($jwt, $jwt_key);

	if (!$permission)
		return $permissions;
	
	// check if token has the right permission:
	$sql = "SELECT * FROM property_token_permissions WHERE permission_text = '{$permission}'";
	$sth = $pdo->prepare($sql);
	$sth->execute();
	$permission_id = $sth->fetch();
	if ($permissions && $permission_id && in_array($permission_id["permission_id"], $permissions))
		return $permissions;
	else
		Response::error(Response::NOT_ALLOWED, [], ["permission" => $permission]);
}