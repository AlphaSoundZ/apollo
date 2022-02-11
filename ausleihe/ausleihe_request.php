<?php
require "config.php";
$rfid1;
$rfid2;
$data['user'] = array();
$data['device'] = array();
$data['message'] = '';
$data['response'] = 9;
global $pdo, $usercardtype, $data, $device_1, $device_2;
if ($rfid1 = isset($_GET['rfid1'])) {
  $rfid1 = $_GET['rfid1'];
  $stm1 = "SELECT * FROM rfid_devices LEFT JOIN rfid_device_type ON rfid_devices.device_type = rfid_device_type.device_type_id WHERE rfid_devices.rfid_code = '".$rfid1."'";
  $device_1 = $pdo->query($stm1)->fetch();
}
if ($rfid2 = isset($_GET['rfid2'])) {
  $rfid2 = $_GET['rfid2'];
  $stm2 = "SELECT * FROM rfid_devices LEFT JOIN rfid_device_type ON rfid_devices.device_type = rfid_device_type.device_type_id WHERE rfid_devices.rfid_code = '".$rfid2."'";
  $device_2 = $pdo->query($stm2)->fetch();
}
/*
rfid1 muss Usercard sein und rfid2 Gerät, wenn man ausleihen möchte.

Wenn nur rfid1:
- Entweder Ipad für Rückgabe
- Oder Info für Usercard
*/

//wenn Input
if (!empty($rfid1 AND $rfid2)) { // ausleihe
  if (rfid_form($rfid1) AND rfid_form($rfid2)) {
    if (!empty($device_1) AND !empty($device_2) AND $device_1['device_type'] == $usercardtype AND $device_2['device_type'] != $usercardtype) { // rfidcodes in der Datenbank? Und device_types überprüfen
      $user_status_stm = "SELECT * FROM rfid_devices LEFT JOIN user ON user.user_id = rfid_devices.lend_id WHERE lend_id = user.user_id AND user.rfid_code = '".$device_1["device_id"]."'";
      $user_status = $pdo->query($user_status_stm)->fetch();
      if (!$user_status) { // Leiht man bereits etwas aus?
        CreateUserObject(1);
        $device_status = "SELECT * FROM rfid_devices WHERE device_id = '".$device_2['device_id']."'";
        $device_status = $pdo->query($device_status)->fetch();
        if ($device_status) { // Wird das auszuleihende Gerät bereits ausgeliehen?
          $user = "SELECT * FROM user WHERE rfid_code = '".$device_1['device_id']."'";
          $user = $pdo->query($user)->fetch();
          $update_stm = $pdo->query("UPDATE rfid_devices SET lend_id = ".$user['user_id']." WHERE device_id = '".$device_2['device_id']."'");
          $user_status = $pdo->query($user_status_stm)->fetch();
          if ($user_status) { // hat update mit Server funktioniert?
            message(0);
            CreateUserObject(1); // User Status aktualisieren
            CreateDeviceObject(2);
            event(1);
          }
          else {
            CreateDeviceObject(2);
            message('');}}
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
        $status_stm = "SELECT * FROM user LEFT JOIN rfid_devices ON rfid_devices.lend_id = user.user_id WHERE rfid_devices.rfid_code = '".$rfid1."'";
        $device_status_ = $pdo->query($status_stm)->fetch();
        if ($device_status_) { // Wird das Gerät ausgeliehen, damit man es zurückgeben kann?
          CreateUserObject(1);
          $update_stm = $pdo->query("UPDATE rfid_devices SET lend_id = '0' WHERE lend_id = '".$device_status_['user_id']."'");
          $device_status_ = $pdo->query($status_stm)->fetch();
          if (empty($device_status_['rfid_device_id'])) { // update success?
            message(1);
            CreateDeviceObject(1);
            event(0);
          }
          else {
            CreateDeviceObject(1);
            message('');
        }}
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
  message(9);
}

function message($messageID) {
  global $data;
  switch ($messageID) {
  case 0:
      $data['message'] = $data['message']."Ausleihen erfolgreich. ";
      $data['response'] = '0';
      break;
  case 1:
      $data['message'] = $data['message']."Rückgabe erfolgreich. ";
      $data['response'] = '1';
      break;
  case 2:
      $data['message'] = $data['message']."Info erfolgreich. ";
      $data['response'] = '2';
      break;
  case 3:
      $data['message'] = $data['message']."Device oder Usercard nicht Registriert. ";
      $data['response'] = '3';
      break;
  case 4:
      $data['message'] = $data['message']."Device kann nicht zurückgegeben werden, weil es nicht ausgeliehen wird. ";
      $data['response'] = '4';
      break;
  case 5:
      $data['message'] = $data['message']."Device kann nicht ausgeliehen weil man bereits etwas ausleiht. ";
      $data['response'] = '5';
      break;
  case 6:
      $data['message'] = $data['message']."Device wird bereits ausgeliehen. ";
      $data['response'] = '6';
      break;
  case 7:
      $data['message'] = $data['message']."Es handelt sich nicht um ein Device. ";
      $data['response'] = '7';
      break;
  case 9:
      $data['message'] = $data['message']."Keine rfid Angabe. ";
      $data['response'] = '9';
      break;
  default:
     $data['message'] = $data['message']."Unexpected Error ";
     $data['response'] = '8';
}}

function selectData($device_a) {
  global $usercardtype, $device_1, $device_2;
  $var = ${"device_".$device_a}['device_type'];
  if ($var == $usercardtype)
  {
    $user_stm = "SELECT * FROM user LEFT JOIN klassen ON klassen.id = user.klasse WHERE rfid_code = '".$var."'";
  }
  else
  {
    $user_stm = "SELECT * FROM user LEFT JOIN rfid_devices ON rfid_devices.lend_id = user.user_id LEFT JOIN klassen ON klassen.id = user.klasse WHERE rfid_devices.device_id = '".$var."'";
  }
  return $user_stm;
}

function CreateUserObject($device_a) {
  global $rfid1, $data, $device_1, $device_2, $pdo, $device_status_, $usercardtype;
  $user_data = $pdo->query(selectData($device_a))->fetch();
  $data['user']['vorname'] = $user_data['vorname'];
  $data['user']['nachname'] = $user_data['name'];
  $data['user']['user_id'] = $user_data['user_id'];
  $data['user']['klasse'] = $user_data['klassen_name'];
  $data['user']['status'] = ($user_data) ? "Hier wird die Device ID stehen" : "false";
}

function CreateDeviceObject($device_a) { // status muss noch automatisiert werden.
  global $data, $device_1, $device_2, $pdo, $usercardtype;
  $object_data = $pdo->query(selectData($device_a))->fetch();
  $device = "device_".$device_a;
  $data['device']['device_type'] = ${$device}['name']; // varable variables: Call Variable from String with Index for Array
  $data['device']['status'] = ($object_data) ? $object_data['user_id'] : "false";
  $data['device']['id'] = ${$device}['device_id'];
  $data['device']['rfid_code'] = ${$device}['rfid_code'];
}

function CollectHistoryData() {
  global $data, $device_1, $pdo, $rfid_read;
  $history_stm = "SELECT device_id, status, time_stamp FROM rfid_event WHERE event_type_id = '".$rfid_read."' AND user_id = '".$data['user']['user_id']."' LIMIT 20";
  $history = $pdo->query($history_stm)->fetch();
  $data['user']['history'] = $history;
}

function event($status) {
  global $rfid_read, $pdo, $data;
  $pdo->query("INSERT INTO rfid_event (id, event_type_id, user_id, device_id, status, time_stamp) VALUES (NULL, ".$rfid_read.", ".$data['user']['user_id'].", ".$data['device']['id'].", ".$status.", date('Y-m-d H:i:s'))"); // maybe use date('Y-m-d H:i:s')
}

echo json_encode($data);
?>
