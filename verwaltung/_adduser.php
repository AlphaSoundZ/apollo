<?php
session_start();
require 'config.php';
$data = json_decode(file_get_contents("php://input"));
$task = $data->task;
// Start desired function
try {
  $task();
} catch (Exception $e) {
  $data['message'] = $e->getMessage();
	message();
	echo json_encode($data);
	exit;
}

function check_rfid() {
  message();
  echo $data['response'];
  global $data, $pdo, $usercardtype;

  // check usercard
  $stmt = $pdo->prepare("SELECT * FROM rfid_devices WHERE rfid_code = ?");
  $rfid_exist = $stmt->execute([$rfid_code])->fetch();

  if ($rfid_exist) {
    $rfid_assigned = $pdo->query("SELECT * FROM user WHERE rfid_code = '".$rfid_exist['device_id']."'")->fetch();
    if ($rfid_exist['device_type'] == $usercardtype) {
      if ($rfid_assigned) {
        message(3);
        return false;
      }
      if (check_user()) {
        message(1);
        return true;
    }}
    else {
      message(4);
      return false;
}}}

function check_user() {
  $stmt = $pdo->prepare("SELECT * FROM user WHERE vorname = ? AND name = ?");
  $user_exist = $stmt->execute([$vorname, $nachname])->fetch();
  if ($userexist) {
    message(2);
    return false;
  }
  else {
    return true;
}}


if (check_user() AND check_rfid()) {
  if ($data['response'] == 1) {
    createuser();
  }
  else if (empty($data['response'])) {
    createuser();
    createusercard();
  }
}




  global $data, $pdo, $usercardtype, $rfid_code_len;
  $vorname = $data->vorname;
  $nachname = $data->nachname;
  $klasse = $data->klasse;
  $rfid_code = $data->rfid_code;

  // Nach Existenz in DB von Usercard und User prüfen
  $stmt = $pdo->prepare("SELECT * FROM rfid_devices WHERE rfid_code = ?");
  $stmt->execute([$rfid_code]);
  $Usercard_EZ = $stmt->fetch();
  $Usercard_E = false;
  $stmt = $pdo->prepare("SELECT * FROM user WHERE vorname = ? AND name = ?");
  $stmt->execute([$vorname, $nachname]);
  $User_E = $stmt->fetch();

  // Wenn Usercard exisitert, dann nach Zuweisung prüfen
  if ($Usercard_EZ) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE ? = rfid_code");
    $stmt->execute([$Usercard_EZ['device_id']]);
    $Usercard_EZ = $stmt->fetch();
    $Usercard_E = true;
  }
  if (rfid_form($rfid_code)) {
    if (empty($Usercard_EZ) AND $Usercard_E == false) {
      // Erstelle Usercard
      echo "Usercard wird erstellt.<br>";
      $stmt = $pdo->prepare("INSERT INTO rfid_devices (device_type, rfid_code) VALUES (?, ?)");
      $stmt->execute([$usercardtype, $rfid_code]);
    }
      // Nach Id von Usercard suchen
      $stmt = "SELECT * FROM rfid_devices WHERE rfid_code = ?";
      $stmt = $pdo->prepare($stmt);
      $stmt->execute([$rfid_code]);
      $Usercard_id = $stmt->fetch();
      // User erstellen
      echo "User wird erstellt.<br>";
      $user_insert = "INSERT INTO user (vorname, name, klasse, rfid_code) VALUES (?, ?, ?, ?)";
      $pdo->prepare($user_insert)->execute([$vorname, $nachname, $klasse, $Usercard_id['device_id']]);
    }
    else {
      error(1);
    }
  }
}
else {
  error(0);
}

function message($messageID) {
  global $data;
  switch ($messageID) {
  case 0:
      $data['message'] = $data['message']."User wird erstellt. ";
      $data['response'] = '0';
      break;
  case 1:
      $data['message'] = $data['message']."Nur Zuweisung der Usercard. ";
      $data['response'] = '1';
      break;
  case 2:
      $data['message'] = $data['message']."User existiert schon. ";
      $data['response'] = '2';
      break;
  case 3:
      $data['message'] = $data['message']."Usercard ist bereits ".$Usercard_EZ['vorname']." ".$Usercard_EZ['name']." zugewiesen. ";
      $data['response'] = '3';
      break;
  case 4:
      $data['message'] = $data['message']."RFID Code gehört zu einem Device. ";
      $data['response'] = '4';
      break;
  default:
     $data['message'] = $data['message']."Unexpected Error ";
     $data['response'] = '8';
}}


function _adduser() {
  global $data, $pdo;
  $klassen_auswahl = "Klasse";
?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Add User</title>
  </head>
  <body>

  <p class="main_text">Add user</p>
  <form>
    <input type="text" class="main_textfield" name="vorname" placeholder="Vorname" onblur="checkText('input.vorname')" onfocus="updateerrormsg();" id="input.vorname"></input><br><br><br><br>
    <input type="text" class="main_textfield" name="nachname" placeholder="Nachname" onblur="checkText('input.nachname')" onfocus="updateerrormsg();" id="input.nachname"></input><br><br><br>
    <select id="input.klasse" onblur="checkText('input.klasse')" onfocus="updateerrormsg();">
    <?php
      $klassen = "SELECT * FROM klassen ORDER BY id";
      echo "<option value='nothing_selected'>Klasse</option>";
      foreach ($pdo->query($klassen) as $row) {
        echo "<option value='".$row['id']."'>".$row['klassen_name']."</option>";
      }
    ?>
  </select>
  <br>
  <input type="text" class="main_textfield" name="rfid_code" placeholder="RFID Code" id="input.rfid_code" onblur="checkText('input.rfid_code')" onfocus="updateerrormsg();"></input><br><br><br> <!-- Idee: Man klickt in das Feld und dann hält man das Gerät, welches eingescannt werden soll an das Lesegerät. Sobald das Lesegerät den Code hat, wird er einfach kopiert und anschließend automatisch eingefügt. Dann kann man sich das mit einer extra seite zum scannen etc. sparen und es spart deutlich zeit, weil man nicht jedesmal auf eine seite geleitet wird, sondern es einache eingefügt wird. -->
  <br>
  <div><p class="main_warningmsg" id="warning"></p>
  <input type="submit" class="main_submit" value="Add" id="input.submit" onclick="validateForm(event);"></input>
  </form>
  </body>
  </html>
<?php } ?>
