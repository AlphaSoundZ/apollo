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
  
  public function Execute()
  {
    global $pdo;
    
    // Fetch first device with $this->uid_1
    $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$this->uid_1'";
    $device_1 = $pdo->query($sql)->fetch();

    if (!empty($device_1))
    {
      if ($device_1['device_type_id'] == $_ENV['USERCARD_TYPE'])
      {
        $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_usercard_id = '{$device_1['device_id']}'";
        $user = $pdo->query($sql)->fetch();
      }
      
      if (!empty($this->uid_2)) // Ausleihe oder Rückgabe
      {
        $sql = "SELECT * FROM devices LEFT JOIN property_device_type ON devices.device_type = property_device_type.device_type_id WHERE devices.device_uid = '$this->uid_2'";
        $device_2 = $pdo->query($sql)->fetch();

        if (!$device_2)
					throw new CustomException(Response::DEVICE_NOT_FOUND . " (uid: $this->uid_2)", "DEVICE_NOT_FOUND", 400);

        if ($device_2['device_type_id'] == $_ENV['USERCARD_TYPE'])
          throw new CustomException(Response::WRONG_DEVICE_TYPE, "WRONG_DEVICE_TYPE", 400);
        
        // Ausleihe
        // ist $this->uid_1 eine usercard und $this->uid_2 ein Gerät?
        if ($device_1['device_type_id'] == $_ENV['USERCARD_TYPE'] && $device_2['device_type_id'] != $_ENV['USERCARD_TYPE']) 
        { // Darf der User ein Device ausleihen?
          $sql = "SELECT * FROM devices WHERE device_lend_user_id = '{$user['user_id']}'";
          $status = $pdo->query($sql)->fetchAll();
          if ($status && $user['user_class'] != $_ENV['MULTIUSER'])
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_CLASS, "NOT_ALLOWED_FOR_THIS_CLASS", 400);
          
          // Wird das auszuleihende Gerät bereits ausgeliehen?
          if ($device_2['device_lend_user_id'] != 0)
            throw new CustomException(Response::NOT_ALLOWED_FOR_THIS_DEVICE, "NOT_ALLOWED_FOR_THIS_DEVICE", 400);
          // Keine Probleme, Gerät kann ausgeliehen werden
          $this->userInfo($user['user_id']);
          $this->deviceInfo($device_2['device_id']);
          return self::lend($user['user_id'], $device_2['device_id']);
        }
        if ($device_1['device_type_id'] != $_ENV['USERCARD_TYPE'] || $device_2['device_type_id'] == $_ENV['USERCARD_TYPE'])
          throw new CustomException(Response::WRONG_DEVICE_TYPE, "WRONG_DEVICE_TYPE", 400);
      }
      else
      {
        // Rückgabe oder Info
        // ist $this->uid_1 ein Gerät?
        if ($device_1['device_type_id'] != $_ENV['USERCARD_TYPE'] && $device_1['device_lend_user_id'] != 0) // Rückgabe
        {
          $sql = "SELECT * FROM user WHERE user_id = '{$device_1['device_lend_user_id']}'";
          $user = $pdo->query($sql)->fetch();
          $this->userInfo($user['user_id']);
          $this->deviceInfo($device_1['device_id']);
          return self::return($device_1['device_id']);
        }
        else if ($device_1['device_type_id'] != $_ENV['USERCARD_TYPE']) // Keine Rückgabe möglich
          throw new CustomException(Response::RETURN_NOT_POSSIBLE, "RETURN_NOT_POSSIBLE", 400);
        else if ($device_1['device_type_id'] == $_ENV['USERCARD_TYPE']) // Info
        {
          return $this->userInfo($user['user_id']);
        }
      }
    }
    else
    {
      // Input $this->uid_1 is empty
      throw new CustomException(Response::DEVICE_NOT_FOUND . " (uid: $this->uid_1)", "DEVICE_NOT_FOUND", 400);
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

    // Update event using timestamp
    $sql = "INSERT INTO event (event_id, event_user_id, event_device_id, event_begin, event_end) VALUES (NULL, '$user_id', '$device_id', CURRENT_TIMESTAMP, NULL)";
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
    $sql = "UPDATE devices SET device_lend_user_id = '0' WHERE device_id = '$device_id'";
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
    
    // General user info
    $this->data['user']['firstname'] = $user['user_firstname'];
    $this->data['user']['lastname'] = $user['user_lastname'];
    $this->data['user']['user_id'] = $user_id;
    $this->data['user']['class'] = $user['class_name'];
    $this->data['user']['multiuser'] = ($user['user_class'] == $_ENV['MULTIUSER']) ? true : false;
    $this->data['user']['status'] = $status;

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