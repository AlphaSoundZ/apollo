<?php
$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";


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
		$input = json_decode(file_get_contents("php://input"), true);
		//return (isset($_POST['data'])) ? json_decode($_POST['data'], true) : false;
	}
	elseif ($method === "GET")
	{
		$input = (isset($_GET['data'])) ? json_decode($_GET['data'], true) : false;
	}
	else
	{
		die;
	}
	if (isset($requirements))
	{
		foreach ($requirements as $r) {
			if (!array_key_exists($r, $input))
			{
				$response["response"] = 77;
				$response["message"] = "Some input is missing ($r)";
				echo json_encode($response);
			}
		}
	}
	return $input;
}

function authorize($file)
{
	global $pdo;
	if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
	else return false;
	$token_hash = md5(explode(" ", $given_token)[1]);

	// search for token
	$sql = "SELECT * FROM token WHERE token_hash = :token_hash";
	$sth = $pdo->prepare($sql);
	$sth->execute(["token_hash" => $token_hash]);
	$result  = $sth->fetch();
	
	// token_permissions
	$sql = "SELECT * FROM property_token_permissions WHERE permission_text = :permission";
	$sth = $pdo->prepare($sql);
	$sth->execute(["permission" => $file]);
	$token_permission = $sth->fetch();

	if ($result)
	{
		$given_permissions = json_decode($result["token_permissions"]);
		if (in_array($token_permission["permission_id"], $given_permissions)) return true;
		else
		{
			$response["response"] = 99;
			$response["message"] = "405 you dont have the permission to access this content";
		}
	}
	else
	{
		$response["response"] = 88;
		$response["message"] = "wrong token";
	}
	echo json_encode($response);
	die;
}

function rfid_form($x) {
	return true;
}