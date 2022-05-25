<?php
require "config.php";
global $pdo, $usercardtype, $data, $device_1, $device_2;
$rfid1 = '';
$rfid2 = '';
$data['message'] = NULL;


if (isset($_GET['rfid1'])) {
  $rfid1 = $_GET['rfid1'];
  $stm1 = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '".$rfid1."'";
  $device_1 = $pdo->query($stm1)->fetch();
}

if (isset($_GET['rfid2'])) {
  $rfid2 = $_GET['rfid2'];
  $stm2 = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '".$rfid2."'";
  $device_2 = $pdo->query($stm2)->fetch();
}


/*
rfid1 muss Usercard sein und rfid2 Gerät, wenn man ausleihen möchte.

Wenn nur rfid1:
- Entweder Device für Rückgabe
- Oder Info für Usercard
*/

//wenn Input
if (!empty($rfid1) AND !empty($rfid2)) { // ausleihe
  if (rfid_form($rfid1) AND rfid_form($rfid2)) {
    if (!empty($device_1) AND !empty($device_2) AND $device_1['device_type'] == $usercardtype AND $device_2['device_type'] != $usercardtype) { // rfidcodes in der Datenbank? Und device_types überprüfen
      $user_status_stm = "SELECT * FROM devices LEFT JOIN user ON user.user_id = devices.device_lend_user_id WHERE device_lend_user_id = user.user_id AND user.user_usercard_id = '".$device_1["device_uid"]."'";
      $user_status = $pdo->query($user_status_stm)->fetch();
      $user = "SELECT * FROM user WHERE user_usercard_id = '".$device_1['device_id']."'";
      $user = $pdo->query($user)->fetch();
      if (!$user_status || $user['user_class'] == 1) { // Leiht man bereits etwas aus oder ist es eine Multiausleihe (Lehrer)?
        CreateUserObject(1);
        $device_status = "SELECT * FROM devices WHERE device_id = '".$device_2['device_id']."'";
        $device_status = $pdo->query($device_status)->fetch();
        if ($device_status["device_lend_user_id"] == 0) { // Wird das auszuleihende Gerät bereits ausgeliehen?
          $update_stm = $pdo->query("UPDATE devices SET device_lend_user_id = ".$user['user_id']." WHERE device_id = '".$device_2['device_id']."'");
          $user_status = $pdo->query($user_status_stm)->fetch();
          message(0);
          CreateUserObject(1); // User Status aktualisieren
          CreateDeviceObject(2);
          event(1);
        }
        else {
          message(6);
          CreateDeviceObject(2);
        }}
      else {
        message(5);
        CreateUserObject(1);
        CreateDeviceObject(2);
    }}
    else {
      if (empty($device_2)) {message(3);}
      elseif ($device_2['device_type'] == $usercardtype) {message(7);}}}
  else {message(3);}}
elseif(!empty($rfid1)) { // rückgabe oder info
  if (rfid_form($rfid1)) {
    if (!empty($device_1)) { // Rfid1 in der Datenbank?
      if ($device_1['device_type'] == $usercardtype) { // Soll Info angezeigt werden?
        CreateUserObject(1);
        message(2);
        CollectHistoryData();
      }
      else { // Gerät wird zurückgegeben
        $status_stm = "SELECT * FROM user LEFT JOIN devices ON devices.device_lend_user_id = user.user_id WHERE devices.device_uid = '".$rfid1."'";
        $device_status_ = $pdo->query($status_stm)->fetch();
        if ($device_status_) { // Wird das Gerät ausgeliehen, damit man es zurückgeben kann?
          CreateUserObject(1);
          $update_stm = $pdo->query("UPDATE devices SET device_lend_user_id = '0' WHERE device_uid = '".$rfid1."'");
          message(1);
          CreateDeviceObject(1);
          event(0);
        }
        else {
          CreateDeviceObject(1);
          message(4);
    }}}
    else {
      message(3);
  }}
  else {
      message(3);
}}
elseif (empty($rfid1) and empty($rfid2) or empty($rfid2)) { // enthält die URL rfid codes?
  message(8);
}

function message($messageID) {
  global $data;
  $data['response'] = $messageID;
  switch ($messageID) {
  case 0:
      $data['message'] = $data['message']."Ausleihen erfolgreich. ";
      break;
  case 1:
      $data['message'] = $data['message']."Rückgabe erfolgreich. ";
      break;
  case 2:
      $data['message'] = $data['message']."Info erfolgreich. ";
      break;
  case 3:
      $data['message'] = $data['message']."Device oder Usercard nicht Registriert. ";
      break;
  case 4:
      $data['message'] = $data['message']."Device kann nicht zurückgegeben werden, weil es nicht ausgeliehen wird. ";
      break;
  case 5:
      $data['message'] = $data['message']."Device kann nicht ausgeliehen weil man bereits etwas ausleiht. ";
      break;
  case 6:
      $data['message'] = $data['message']."Device wird bereits ausgeliehen. ";
      break;
  case 7:
      $data['message'] = $data['message']."Es handelt sich nicht um ein Device. ";
      break;
  case 8:
      $data['message'] = $data['message']."Keine rfid Angabe. ";
      break;
  default:
     $data['message'] = $data['message']."Unexpected Error (".$messageID.") ";
     $data['response'] = 9;
}}

function selectData($device_a) {
  global $usercardtype, $device_1, $device_2;
  $var = ${"device_".$device_a}['device_type'];
  if ($var == $usercardtype)
  {
    $user_stm = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_usercard_id = '".${"device_".$device_a}['device_id']."'";
  }
  else
  {
    $user_stm = "SELECT * FROM user LEFT JOIN devices ON devices.device_lend_user_id = user.user_id LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE devices.device_id = '".${"device_".$device_a}['device_id']."'";
  }
  return $user_stm;
}

function CreateUserObject($device_a) {
  global $rfid1, $data, $device_1, $device_2, $pdo, $device_status_, $usercardtype;
  $user_data = $pdo->query(selectData($device_a))->fetch();
  $data['user']['vorname'] = $user_data['user_firstname'];
  $data['user']['nachname'] = $user_data['user_lastname'];
  $data['user']['user_id'] = $user_data['user_id'];
  $data['user']['klasse'] = $user_data['class_name'];
  $data['user']['status'] = ($user_data) ? "Hier wird die Device ID stehen" : false;
}

function CreateDeviceObject($device_a) { // status muss noch automatisiert werden.
  global $data, $device_1, $device_2, $pdo, $usercardtype;
  $object_data = $pdo->query(selectData($device_a))->fetch();
  $device = "device_".$device_a;
  $data['device']['device_type'] = ${$device}['device_type_name']; // varable variables: Call Variable from String with Index for Array
  $data['device']['status'] = ($object_data) ? $object_data['user_id'] : false;
  $data['device']['id'] = ${$device}['device_id'];
  $data['device']['rfid_code'] = ${$device}['device_uid'];
}

function CollectHistoryData() {
  global $data, $device_1, $pdo, $rfid_read;
  $history_stm = "SELECT devices.device_type, devices.device_id, property_device_type.device_type_id, property_device_type.device_type_name, event.* FROM event LEFT JOIN devices ON event.event_device_id = devices.device_id LEFT JOIN property_device_type ON property_device_type.device_type_id = devices.device_type WHERE event_user_id = '".$data['user']['user_id']."' ORDER BY event_begin DESC LIMIT 20";
  $history = $pdo->query($history_stm)->fetchAll();
  if ($history) {
    for ($i=0;$i < count($history); $i++) {
      $data['user']['history'][$i]['device_id'] = $history[$i]['event_device_id'];
      $data['user']['history'][$i]['begin'] = $history[$i]['event_begin'];
      $data['user']['history'][$i]['end'] = $history[$i]['event_end'];
      $data['user']['history'][$i]['device_type'] = $history[$i]['device_type_name'];
    }
  }
}

function event($status) {
  global $rfid_read, $pdo, $data, $device_1;
  if ($data['response'] == 0) { // Bei ausleihe event_begin aktuallisieren
    $pdo->query("INSERT INTO event (event_id, event_user_id, event_device_id, event_begin, event_end) VALUES (NULL, ".$data['user']['user_id'].", ".$data['device']['id'].", CURRENT_TIMESTAMP, NULL)");
  }
  elseif ($data['response'] == 1) { // Bei Rückgabe event_end aktuallisieren
    $event_line = $pdo->query("SELECT * FROM event WHERE event_device_id = '".$device_1['device_id']."' AND event_end <=> null")->fetchAll();
    if ($event_line) {
      if (count($event_line) > 1) {
        message('event table '.count($event_line).' rows instead of 1');
      }
      $pdo->query("UPDATE event SET event_end = CURRENT_TIMESTAMP WHERE event.event_id = '".$event_line[0]['event_id']."'");
    }
    else {
      message('event table no avaiable event');
    }
  }
}

echo json_encode($data);
