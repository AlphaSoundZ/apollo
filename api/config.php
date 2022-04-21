<?php
$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";
$token["add_device"] = "1234-1234-1234-1234";

try {
	$pdo = new PDO($__dsn, $__username, $__password);
	if ($pdo) {
		$usercardtype = $pdo->query("SELECT * FROM rfid_device_type WHERE name = 'UserCard'")->fetch();
		$usercardtype = $usercardtype['device_type_id'];
	}
} catch (PDOException $e) {
	$data['message'] = $e->getMessage();
	$data['response'] = "10";
	echo json_encode($data);
	exit;
}

function getData($method)
{
	if ($method === "POST")
	{
		return json_decode(file_get_contents("php://input"), true);
		//return (isset($_POST['data'])) ? json_decode($_POST['data'], true) : false;
	}
	elseif ($method === "GET")
	{
		return (isset($_GET['data'])) ? json_decode($_GET['data'], true) : false;
	}
	return false;
}

function authorize($file)
{
	global $token;
	$given_token = $_SERVER["HTTP_AUTHORIZATION"];
	if ($token[$file] == explode(" ", $given_token)[1])
	{
		return true;
	}
	return false;
}