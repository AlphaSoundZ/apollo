<?php
$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";
$token["add_device"] = "1234-1234-1234-1234";

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
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else return false;
	if ($token[$file] == explode(" ", $given_token)[1])
	{
		return true;
	}
	return false;
}

function rfid_form($x) {
	return true;
}