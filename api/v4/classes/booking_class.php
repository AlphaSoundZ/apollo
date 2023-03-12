<?php
require_once "config.php";

class Booking
{
  public $uid_1;
  public $uid_2;
  public $data;

  function __construct($uid_1, $uid_2 = null)
  {
    $this->uid_1 = $uid_1;
    $this->uid_2 = $uid_2;
    $this->data = null;
  }
  
  public function execute()
  {
    global $pdo;
    
    // Fetch first device with $this->uid_1
    $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$this->uid_1'";
    $device_1 = $pdo->query($sql)->fetch();

    // Fetch first device with $this->uid_1
    $sql = "SELECT * FROM usercard WHERE usercard.usercard_uid = '$this->uid_1'";
    $usercard = $pdo->query($sql)->fetch();

    if (!empty($device_1) || !empty($usercard))
    {
      if ($usercard)
      {
        $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_usercard_id = '{$usercard['usercard_id']}'";
        $user = $pdo->query($sql)->fetch();
        $device_1 = $usercard;
      }
      
      if (!empty($this->uid_2)) // Ausleihe oder Rückgabe
      {
        $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$this->uid_2'";
        $device_2 = $pdo->query($sql)->fetch();

        if (!$device_2)
					throw new CustomException(Response::DEVICE_NOT_FOUND . " (uid: $this->uid_2)", "DEVICE_NOT_FOUND", 400);
        
        // Ausleihe
        // ist $this->uid_1 eine usercard und $this->uid_2 ein Gerät?
        if ($usercard && $device_2) 
        {
          // Wird das auszuleihende Gerät bereits ausgeliehen?
          if ($device_2['device_lend_user_id'] != 0)
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_DEVICE, "NOT_ALLOWED_FOR_THIS_DEVICE", 400);
          
          // Darf der User ein Device ausleihen?
          $sql = "SELECT * FROM devices WHERE device_lend_user_id = '{$user['user_id']}'";
          $status = $pdo->query($sql)->fetchAll();
          if ($status && $user['multi_booking'] != 1)
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_CLASS, "NOT_ALLOWED_FOR_THIS_CLASS", 400);
          
          // Keine Probleme, Gerät kann ausgeliehen werden
          $return_result = self::lend($user['user_id'], $device_2['device_id']);
          $this->userInfo($user['user_id']);
          $this->deviceInfo($device_2['device_id']);

          return $return_result;
        }
      }
      else
      {
        // Rückgabe oder Info
        // ist $this->uid_1 ein Gerät?
        if (!$usercard && $device_1['device_lend_user_id'] != 0) // Rückgabe
        {
          $sql = "SELECT * FROM user WHERE user_id = '{$device_1['device_lend_user_id']}'";
          $user = $pdo->query($sql)->fetch();
          $this->userInfo($user['user_id']);
          $this->deviceInfo($device_1['device_id']);
          return self::return($device_1['device_id']);
        }
        else if (!$usercard) // Keine Rückgabe möglich
          throw new CustomException(Response::RETURN_NOT_POSSIBLE, "RETURN_NOT_POSSIBLE", 400);
        else if ($usercard) // Info
        {
          return $this->userInfo($user['user_id']);
        }
      }
    }
    else
    {
      // Input $this->uid_1 is empty
      throw new CustomException(Response::UID_NOT_FOUND . " (uid: $this->uid_1)", "UID_NOT_FOUND", 400);
    }
  }

  public function fetchUserData()
  {
    if ($this->data)
      return $this->data;
    return [];
  }

  private function lend($user_id, $device_id)
  {
    global $pdo;
    // Update device_lend_user_id
    $sql = "UPDATE devices SET device_lend_user_id = $user_id WHERE device_id = '$device_id'";
    $pdo->query($sql);
    
    // get multi_id
    $sql = "SELECT * FROM event WHERE event_user_id = $user_id AND event_multi_booking_id = ( SELECT max(event_multi_booking_id) FROM event WHERE event_user_id = $user_id ) ORDER BY event_end DESC";
    $events = $pdo->query($sql)->fetchAll();

    $sql = "SELECT max(event_multi_booking_id) FROM event";
    $latest_mulit_id_of_all = $pdo->query($sql)->fetch();
    if ($latest_mulit_id_of_all)
    {
      if ($events)
      {
        $multi_id = $events[0]['event_multi_booking_id']; // when multi id is not gonna change
        if (strtotime("now") > strtotime($events[0]["event_end"]) && end($events)["event_end"] != null)
          $multi_id = $latest_mulit_id_of_all[0] + 1;
      }
      else
        $multi_id = $latest_mulit_id_of_all[0] + 1; // new multi id, because of first booking
    }

    // Update event using timestamp
    $sql = "INSERT INTO event (event_id, event_user_id, event_device_id, event_begin, event_end, event_multi_booking_id) VALUES (NULL, '$user_id', '$device_id', CURRENT_TIMESTAMP, NULL, '$multi_id')";
    $pdo->query($sql);

    return "BOOKING_SUCCESS";
  }
  
  private function return($device_id)
  {
    global $pdo;

    // Event table security check of multiple lends of the same device
    $sql = "SELECT * FROM event WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $find_events = $pdo->query($sql)->fetchAll();
    if (count($find_events) > 1)
			throw new CustomException(Response::UNEXPECTED_ERROR . 'In Event wurden '.count($find_events).' Einträge statt 1 gefunden. Device wurde nicht zurückgegeben. Bitte wenden Sie sich an einen Administrator', "UNEXPECTED_ERROR", 400);
    
    // Update device_lend_user_id
    $sql = "UPDATE devices SET device_lend_user_id = NULL WHERE device_id = '$device_id'";
    $pdo->query($sql);

    // Update event using timestamp
    $sql = "UPDATE event SET event_end = CURRENT_TIMESTAMP WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $pdo->query($sql);

    return "RETURN_SUCCESS";
  }

  private function userInfo($user_id)
  {
    global $pdo;

    // Fetch user info
    $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_id = '$user_id'";
    $user = $pdo->query($sql)->fetch();

    // Fetch user Status and if true fetch the device data
    $sql = "SELECT device_id, device_type FROM devices WHERE device_lend_user_id = '$user_id'";
    $status = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) or false;

    // Fetch current amount of devices booking by user
    $sql = "SELECT COUNT(*) FROM event WHERE event_user_id = '$user_id' AND event_end IS NULL";
    $amount_of_devices = $pdo->query($sql)->fetch();

    // Fetch amount of devices in current booking session
    $event_multi_booking_id = $pdo->query("SELECT max(event_multi_booking_id) FROM event WHERE event_user_id = '$user_id'")->fetch();
    $sql = "SELECT COUNT(*) FROM event WHERE event_multi_booking_id = '$event_multi_booking_id[0]'";
    $amount_of_devices_in_session = $pdo->query($sql)->fetch();

    // Fetch amount of devices ever booked by user
    $sql = "SELECT COUNT(*) FROM event WHERE event_user_id = '$user_id'";
    $amount_of_devices_ever = $pdo->query($sql)->fetch();
    
    // General user info
    $this->data['user']['firstname'] = $user['user_firstname'];
    $this->data['user']['lastname'] = $user['user_lastname'];
    $this->data['user']['user_id'] = $user_id;
    $this->data['user']['class'] = $user['class_name'];
    $this->data['user']['multi_booking'] = $user['multi_booking'];
    $this->data['user']['status'] = $status;
    $this->data['user']['amount_of_devices'] = $amount_of_devices[0];
    $this->data['user']['amount_of_devices_in_session'] = $amount_of_devices_in_session[0];
    $this->data['user']['amount_of_devices_ever'] = $amount_of_devices_ever[0];

    // History of devices
    $history_stm = "SELECT devices.device_type, devices.device_id, property_device_type.device_type_id, property_device_type.device_type_name, event.* FROM event LEFT JOIN devices ON event.event_device_id = devices.device_id LEFT JOIN property_device_type ON property_device_type.device_type_id = devices.device_type WHERE event_user_id = '".$this->data['user']['user_id']."' ORDER BY event_begin DESC LIMIT 20";
    $history = $pdo->query($history_stm)->fetchAll();
    if ($history) {
      for ($i=0;$i < count($history); $i++) {
        $this->data['user']['history'][$i]['device_id'] = $history[$i]['event_device_id'];
        $this->data['user']['history'][$i]['begin'] = $history[$i]['event_begin'];
        $this->data['user']['history'][$i]['end'] = $history[$i]['event_end'];
        $this->data['user']['history'][$i]['device_type'] = $history[$i]['device_type_name'];
      }
    }
    return "INFO_SUCCESS";
  }

  private function deviceInfo($device_id)
  {
    global $pdo;
    // If device_id is set, fetch device info
    if ($device_id)
    {
      $sql = "SELECT * FROM devices 
        LEFT JOIN property_device_type ON property_device_type.device_type_id = devices.device_type 
        WHERE device_id = '$device_id'";
      $device = $pdo->query($sql)->fetch();
      $this->data['device']['device_id'] = $device['device_id'];
      $this->data['device']['device_type_id'] = $device['device_type_id'];
      $this->data['device']['device_type_name'] = $device['device_type_name'];
    }

    return "INFO_SUCCESS";
  }

}