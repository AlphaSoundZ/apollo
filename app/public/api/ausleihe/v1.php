<<<<<<< HEAD:app/public/api/ausleihe/v1.php
<?php
require "../../../config/config.php";
$rfid1 = '';
$rfid2 = '';
$data['message'] = NULL;

global $pdo, $usercardtype, $data, $device_1, $device_2;
if (isset($_GET['rfid1'])) {
  $rfid1 = $_GET['rfid1'];
  $stm1 = "SELECT * FROM rfid_devices LEFT JOIN rfid_device_type ON rfid_devices.device_type = rfid_device_type.device_type_id WHERE rfid_devices.rfid_code = '".$rfid1."'";
  $device_1 = $pdo->query($stm1)->fetch();
}
if (isset($_GET['rfid2'])) {
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
if ($rfid1 AND $rfid2) { // ausleihe
  if (rfid_form($rfid1) AND rfid_form($rfid2)) {
    if (!empty($device_1) AND !empty($device_2) AND $device_1['device_type'] == $usercardtype AND $device_2['device_type'] != $usercardtype) { // rfidcodes in der Datenbank? Und device_types überprüfen
      $user_status_stm = "SELECT * FROM rfid_devices LEFT JOIN user ON user.user_id = rfid_devices.lend_id WHERE lend_id = user.user_id AND user.rfid_code = '".$device_1["device_id"]."'";
      $user_status = $pdo->query($user_status_stm)->fetch();
      $user = "SELECT * FROM user WHERE rfid_code = '".$device_1['device_id']."'";
      $user = $pdo->query($user)->fetch();
      if (!$user_status || $user['klasse'] == 1) { // Leiht man bereits etwas aus oder ist es eine Multiausleihe (Lehrer)?
        CreateUserObject(1);
        $device_status = "SELECT * FROM rfid_devices WHERE device_id = '".$device_2['device_id']."'";
        $device_status = $pdo->query($device_status)->fetch();
        if ($device_status["lend_id"] == 0) { // Wird das auszuleihende Gerät bereits ausgeliehen?
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
            message('server update error');}}
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
elseif($rfid1) { // rückgabe oder info
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
          $update_stm = $pdo->query("UPDATE rfid_devices SET lend_id = '0' WHERE rfid_code = '".$rfid1."'");
          $device_status_ = $pdo->query($status_stm)->fetch();
          if (empty($device_status_['rfid_device_id'])) { // update success?
            message(1);
            CreateDeviceObject(1);
            event(0);
          }
          else {
            CreateDeviceObject(1);
            message('server update error');
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
    $user_stm = "SELECT * FROM user LEFT JOIN klassen ON klassen.id = user.klasse WHERE rfid_code = '".${"device_".$device_a}['device_id']."'";
  }
  else
  {
    $user_stm = "SELECT * FROM user LEFT JOIN rfid_devices ON rfid_devices.lend_id = user.user_id LEFT JOIN klassen ON klassen.id = user.klasse WHERE rfid_devices.device_id = '".${"device_".$device_a}['device_id']."'";
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
  $data['user']['status'] = ($user_data) ? "Hier wird die Device ID stehen" : false;
}

function CreateDeviceObject($device_a) { // status muss noch automatisiert werden.
  global $data, $device_1, $device_2, $pdo, $usercardtype;
  $object_data = $pdo->query(selectData($device_a))->fetch();
  $device = "device_".$device_a;
  $data['device']['device_type'] = ${$device}['name']; // varable variables: Call Variable from String with Index for Array
  $data['device']['status'] = ($object_data) ? $object_data['user_id'] : false;
  $data['device']['id'] = ${$device}['device_id'];
  $data['device']['rfid_code'] = ${$device}['rfid_code'];
}

function CollectHistoryData() {
  global $data, $device_1, $pdo, $rfid_read;
  $history_stm = "SELECT rfid_devices.device_type, rfid_devices.device_id, rfid_device_type.device_type_id, rfid_device_type.name, event.* FROM event LEFT JOIN rfid_devices ON event.device = rfid_devices.device_id LEFT JOIN rfid_device_type ON rfid_device_type.device_type_id = rfid_devices.device_type WHERE user = '".$data['user']['user_id']."' ORDER BY begin DESC LIMIT 20";
  $history = $pdo->query($history_stm)->fetchAll();
  if ($history) {
    for ($i=0;$i < count($history); $i++) {
      $data['user']['history'][$i]['device_id'] = $history[$i]['device'];
      $data['user']['history'][$i]['begin'] = $history[$i]['begin'];
      $data['user']['history'][$i]['end'] = $history[$i]['end'];
      $data['user']['history'][$i]['device_type'] = $history[$i]['name'];
    }
  }
}

function event($status) {
  global $rfid_read, $pdo, $data;
  if ($data['response'] == 0) { // Bei ausleihe begin aktuallisieren
    $pdo->query("INSERT INTO event (id, user, device, begin, end) VALUES (NULL, ".$data['user']['user_id'].", ".$data['device']['id'].", CURRENT_TIMESTAMP, NULL)");
  }
  if ($data['response'] == 1) { // Bei Rückgabe end aktuallisieren
    $event_line = $pdo->query("SELECT * FROM event WHERE user = '".$data['user']['user_id']."' AND end <=> null")->fetchAll();
    if ($event_line) {
      if (count($event_line) > 1) {
        message('event table '.count($event_line).' rows instead of 1');
      }
      $pdo->query("UPDATE event SET end = CURRENT_TIMESTAMP WHERE event.id = '".$event_line[0]['id']."'");
    }
    else {
      message('event table no avaiable event');
    }
  }
}

echo json_encode($data);
?>
=======
<?php
require "../config/config.php";
$device_code = '';
$device_type = '';
$response = '';
/*
    0 = usercard
    1 = Surface Book
    2 = Laptop
*/


global $pdo, $device_1, $device_2;
if (isset($_GET['device_code']) && isset($_GET['device_type']))
{
    $device_code = $_GET['device_code'];
    $device_type = $_GET['device_type'];
    $checkCode = addToDB::checkCode($device_code);
    if ($checkCode == false)
    {
      $upload = addToDB::upload($device_code, $device_type);
      $response["response"] = $upload["success"];
      $response["errorMsg"] = $upload["errorMsg"];
    }
    else
    {
        $response["response"] = 0;
        $response["errorMsg"] = "$device_code ($device_type) already exists";
    }
}

class addToDB
{
    static public function upload($device_code, $device_type)
    {
        global $pdo;
        $sql = "INSERT INTO rfid_devices (device_id, device_type, rfid_code, lend_id) VALUES (NULL, :device_type, :device_code, '0')";
        $stmt= $pdo->prepare($sql);
        $status = $stmt->execute(["device_type" => $device_type, "device_code" => $device_code]);
        if ($status)
        {
            return ["success" => "1", "errorMsg" => "success"];
        }
        else
        {
            return ["success" => "0", "errorMsg" => "sql error"];
        }
    }
    static public function checkCode($device_code)
    {
        global $pdo;
        $sql = "SELECT * FROM rfid_devices WHERE rfid_code = :rfid_code";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["rfid_code" => $device_code]);
        $result = $stmt->fetch();
        if ($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
