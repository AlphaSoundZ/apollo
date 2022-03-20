<?php
$__host = 'localhost';
$__db = 'ausleihe';
$__username = 'root';
$__password = '';
$__dsn = "mysql:host=$__host;dbname=$__db;charset=UTF8";

// Define a global basepath
define('BASEPATH','/');

// Get Pagestructures
$json_obj = file_get_contents("pages.txt");
define("PAGES", json_decode($json_obj, true));

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
		//echo '<script> XdynamicContent.loadContent("'.PAGES["index"][0].'", '.json_encode(PAGES["index"][1]).', "'.PAGES["index"][2].'", ""); </script>';
		//echo "<meta http-equiv=\"refresh\" content=\"0; URL=index.php\">"; exit;
	}
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

?>
