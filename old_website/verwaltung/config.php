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
	}
	else {
		echo "<meta http-equiv=\"refresh\" content=\"0; URL=index.php\">"; exit;
	}
}

?>
