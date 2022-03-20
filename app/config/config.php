<?php
$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

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

function session() {
	if (!empty($_SESSION['sessioncheck']) && $_SESSION['sessioncheck'] == $_SERVER['HTTP_USER_AGENT']) {
		return true;
	}
	die();
	//return false;
}

function rfid_form($rfid_code) {
	/* --EXAMPLE--
	$rfid_code_len = 17;
	if ($rfid_code[4] == '.' AND strlen($rfid_code) == $rfid_code_len AND is_numeric(substr($rfid_code, 0, 4)) AND is_numeric(substr($rfid_code, 5, 12))) {
	return true;
	}
	else {
		return false;
	}
	*/
	return true;
}