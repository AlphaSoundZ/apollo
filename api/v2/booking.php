<?php
require "config.php";

global $pdo, $usercardtype, $data, $device_1, $device_2;

// set response format to json
header("Content-Type: application/json");

authorize("book");
$data['message'] = NULL;
$input = getData("POST", ["uid_1"]);

/*
rfid1 muss Usercard sein und rfid2 Gerät, wenn man ausleihen möchte.

Wenn nur rfid1:
- Entweder Device für Rückgabe
- Oder Info für Usercard
*/

$uid_1 = $input["uid_1"];
$uid_2 = (!empty($input["uid_2"])) ? $input["uid_2"] : null;
ausleihe::execute($uid_1, $uid_2);

class ausleihe
{
  public static function execute($uid_1, $uid_2 = null)
  {
    global $pdo, $usercardtype, $multiuser;
    
    // Fetch first device with $uid_1
    $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$uid_1'";
    $device_1 = $pdo->query($sql)->fetch();

    if (!empty($device_1))
    {
      if ($device_1['device_type_id'] == $usercardtype)
      {
        $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_usercard_id = '{$device_1['device_id']}'";
        $user = $pdo->query($sql)->fetch();
      }
      
      if (!empty($uid_2)) // Ausleihe oder Rückgabe
      {
        $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$uid_2'";
        $device_2 = $pdo->query($sql)->fetch();

        if (!$device_2)
					throw new CustomException(Response::DEVICE_NOT_FOUND . " (uid: $uid_2)", "DEVICE_NOT_FOUND", 400);

        if ($device_2['device_type_id'] == $usercardtype)
          throw new CustomException(Response::WRONG_DEVICE_TYPE, "WRONG_DEVICE_TYPE", 400);
        
        // Ausleihe
        // ist $uid_1 eine usercard und $uid_2 ein Gerät?
        if ($device_1['device_type_id'] == $usercardtype && $device_2['device_type_id'] != $usercardtype) 
        { // Darf der User ein Device ausleihen?
          $sql = "SELECT * FROM devices WHERE device_lend_user_id = '{$user['user_id']}'";
          $status = $pdo->query($sql)->fetchAll();
          if ($status && $user['user_class'] != $multiuser)
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_CLASS, "NOT_ALLOWED_FOR_THIS_CLASS", 400);
          
          // Wird das auszuleihende Gerät bereits ausgeliehen?
          if ($device_2['device_lend_user_id'] != 0)
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_DEVICE, "NOT_ALLOWED_FOR_THIS_DEVICE", 400);
          // Keine Probleme, Gerät kann ausgeliehen werden
          self::lend($user['user_id'], $device_2['device_id']);
        }
        if ($device_1['device_type_id'] != $usercardtype || $device_2['device_type_id'] == $usercardtype)
          throw new CustomException(Response::WRONG_DEVICE_TYPE, "WRONG_DEVICE_TYPE", 400);
      }
      else
      {
        // Rückgabe
        // ist $uid_1 ein Gerät?
        if ($device_1['device_type_id'] != $usercardtype && $device_1['device_lend_user_id'] != 0) // Rückgabe
          self::return($device_1['device_id']);
        else if ($device_1['device_type_id'] != $usercardtype) // Keine Rückgabe möglich
          throw new CustomException(Response::RETURN_NOT_POSSIBLE, "RETURN_NOT_POSSIBLE", 400);
        else if ($device_1['device_type_id'] == $usercardtype) // Info
          self::info($user['user_id']);
      }
    }
    else
    {
      // Input $uid_1 is empty
      throw new CustomException(Response::DEVICE_NOT_FOUND . " (uid: $uid_1)", "DEVICE_NOT_FOUND", 400);
    }
  }

  private static function lend($user_id, $device_id)
  {
    global $pdo, $data;
    // Update device_lend_user_id
    $sql = "UPDATE devices SET device_lend_user_id = $user_id WHERE device_id = '$device_id'";
    $pdo->query($sql);

    // Update event using timestamp
    $sql = "INSERT INTO event (event_id, event_user_id, event_device_id, event_begin, event_end) VALUES (NULL, '$user_id', '$device_id', CURRENT_TIMESTAMP, NULL)";
    $pdo->query($sql);

    $data['message'] = "Gerät ausgeliehen";
    $data['response'] = 0;
  }
  
  private static function return($device_id)
  {
    global $pdo, $data;

    // Event table security check of multiple lends of the same device
    $sql = "SELECT * FROM event WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $find_events = $pdo->query($sql)->fetchAll();
    if (count($find_events) > 1)
			throw new CustomException(Response::UNEXPECTED_ERROR . 'In Event wurden '.count($find_events).' Einträge statt 1 gefunden. Device wurde nicht zurückgegeben. Bitte wenden Sie sich an einen Administrator', "UNEXPECTED_ERROR", 400);
    
    // Update device_lend_user_id
    $sql = "UPDATE devices SET device_lend_user_id = '0' WHERE device_id = '$device_id'";
    $pdo->query($sql);

    // Update event using timestamp
    $sql = "UPDATE event SET event_end = CURRENT_TIMESTAMP WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $pdo->query($sql);

    $data['message'] = "Gerät zurückgegeben";
    $data['response'] = 1;
    http_response_code(200);
  }

  private static function info($user_id)
  {
    global $pdo, $data;

    // Fetch user info
    $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_id = '$user_id'";
    $user = $pdo->query($sql)->fetch();

    // Fetch user Status and if true fetch the device data
    $sql = "SELECT * FROM devices WHERE device_lend_user_id = '$user_id'";
    $status = $pdo->query($sql)->fetchAll() or false;
    
    // General user info
    $data['user']['firstname'] = $user['user_firstname'];
    $data['user']['lastname'] = $user['user_lastname'];
    $data['user']['user_id'] = $user_id;
    $data['user']['class'] = $user['class_name'];
    $data['user']['status'] = $status;

    // History of devices
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

    $data['message'] = "Info zu User erfolgt";
    $data['response'] = 2;
    http_response_code(200);
  }

}

echo json_encode($data);