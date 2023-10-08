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

    // Fetch first usercard with $this->uid_1
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
					Response::error(Response::DEVICE_NOT_FOUND, ["uid_2"]);
        
        // Ausleihe
        // ist $this->uid_1 eine usercard und $this->uid_2 ein Gerät?
        if ($usercard && $device_2) 
        {
          // Wird das auszuleihende Gerät bereits ausgeliehen?
          if ($device_2['device_lend_user_id'] != 0)
            Response::error(Response::DEVICE_ALREADY_LENT, ["uid_2"]);
          
          // Darf der User ein Device ausleihen?
          $sql = "SELECT * FROM devices WHERE device_lend_user_id = '{$user['user_id']}'";
          $status = $pdo->query($sql)->fetchAll();
          if ($status && $user['multi_booking'] != 1)
            Response::error(Response::NOT_ALLOWED_FOR_THIS_CLASS, ["uid_1"]);

          // Ist das Gerät reserviert?
          $max_duration = self::maxBookingDuration();
          if ($max_duration == 0) {
            Response::error(Response::CONFLICT_WITH_PREBOOK);
          }
          
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

          $return_result = self::return($device_1['device_id']);
          $this->userInfo($user['user_id']);
          $this->deviceInfo($device_1['device_id']);
          return $return_result;
        }
        else if (!$usercard) // Keine Rückgabe möglich: Gerät wird nicht ausgeliehen
          Response::error(Response::DEVICE_NOT_LENT, ["uid_1"]);
        else if ($usercard) // Info
        {
          return $this->userInfo($user['user_id']);
        }
      }
    }
    else
    {
      // Input $this->uid_1 is empty
      Response::error(Response::DEVICE_NOT_FOUND, ["uid_1"]);
    }
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

    return Response::BOOKING_SUCCESS;
  }
  
  private function return($device_id)
  {
    global $pdo;

    // Event table security check of multiple lends of the same device
    $sql = "SELECT * FROM event WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $find_events = $pdo->query($sql)->fetchAll();
    if (count($find_events) > 1)
			Response::error(array_merge(Response::UNEXPECTED_ERROR, ["message" => Response::UNEXPECTED_ERROR["message"] . 'In Event wurden '.count($find_events).' Einträge statt 1 gefunden. Device wurde nicht zurückgegeben. Bitte wenden Sie sich an einen Administrator']));
    
    // Update device_lend_user_id
    $sql = "UPDATE devices SET device_lend_user_id = NULL WHERE device_id = '$device_id'";
    $pdo->query($sql);

    // Update event using timestamp
    $sql = "UPDATE event SET event_end = CURRENT_TIMESTAMP WHERE event_device_id = '$device_id' AND event_end IS NULL";
    $pdo->query($sql);

    return Response::RETURN_SUCCESS;
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
    $this->data['user']['max_booking_duration'] = self::maxBookingDuration();

    // Amount of devices
    $this->data['user']['devices_amount']['currently'] = $amount_of_devices[0];
    $this->data['user']['devices_amount']['session'] = ($amount_of_devices[0] > 0) ? $amount_of_devices_in_session[0] : 0;
    $this->data['user']['devices_amount']['ever'] = $amount_of_devices_ever[0];
    $this->data['user']['devices_amount']['available'] = self::availableDevicesForBooking();

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
    return Response::INFO_SUCCESS;
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

    return Response::INFO_SUCCESS;
  }

  public static function availableDevicesForBooking()
  {
      global $pdo;

      // get total amount of devices
      $sql = "SELECT COUNT(*) FROM devices";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total_devices = $stmt->fetchColumn();

      // get total amount of booked devices
      $sql = "SELECT COUNT(*) FROM event WHERE event_begin <= NOW() AND event_end IS NULL";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $total_booked_amount = $stmt->fetchColumn();


      // Get future prebookings in buffer ($buffer time before the prebook_begin time until prebook_end time)
      $buffer = $_ENV["PREBOOK_BUFFER"]; // in minutes

      $sql = "SELECT *, SUM(prebook_amount) AS sum FROM prebook WHERE prebook_begin <= NOW() + INTERVAL $buffer MINUTE AND prebook_end >= NOW()";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $prebookings_in_buffer = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $prebookings_in_buffer_amount = $prebookings_in_buffer[0]['sum'];

      $overall_bookings_in_prebookings_currently = 0;
      $available_devices = $total_devices;

      if (!$prebookings_in_buffer || $prebookings_in_buffer_amount == null) {
          return $available_devices - $total_booked_amount;
      }

      foreach ($prebookings_in_buffer as $prebooking) {
          // Get amount of devices that are already booked and yet to be returned
          $sql = "SELECT COUNT(*) FROM event WHERE event_begin >= :prebook_begin AND event_end IS NULL AND event_user_id = :user_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
              "prebook_begin" => $prebooking["prebook_begin"],
              "user_id" => $prebooking["prebook_user_id"]
          ));
          $current_amount_of_devices = $stmt->fetchColumn();

          $overall_bookings_in_prebookings_currently += $current_amount_of_devices;
          $booking_progress = (time() - strtotime($prebooking['prebook_begin'])) / (strtotime($prebooking['prebook_end']) - strtotime($prebooking['prebook_begin']));
          $total_time = strtotime($prebooking['prebook_end']) - strtotime($prebooking['prebook_begin']);
          $amount_of_devices = $prebooking['prebook_amount'];
          $not_yet_booked_devices = $amount_of_devices - $current_amount_of_devices;

          $available_devices_in_prebook = $booking_progress * ($current_amount_of_devices / $amount_of_devices) * $not_yet_booked_devices;

          $available_devices += $available_devices_in_prebook;
      }

      $available_devices -= $prebookings_in_buffer_amount;
      $available_devices -= $total_booked_amount - $overall_bookings_in_prebookings_currently;

      return $available_devices;
  }

  public static function maxBookingDuration()
  {
      // returns the maximum duration of a booking in minutes before the devices are needed for a prebooking
      global $pdo;

      /*
        * - get amount of available devices
        * - get next prebooking (which isn't in buffer right now), where the current amount of available devices is not enough
        *      - at each event, check the amount of needed devices (add amount of every running prebooking)
        * - get the time difference between now and the event_begin time
        */

      // get amount of available devices
      $available_devices = self::availableDevicesForBooking();

      if ($available_devices == 0) {
          return 0;
      }

      $buffer = $_ENV["PREBOOK_BUFFER"]; // in minutes
      $max_duration = $_ENV["MAX_BOOKING_DURATION"]; // in minutes

      // get upcoming prebookings
      $sql = "SELECT * FROM prebook WHERE prebook_begin > NOW() + INTERVAL $buffer MINUTE ORDER BY prebook_begin ASC";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $prebookings = $stmt->fetchAll(PDO::FETCH_ASSOC);


      // get next prebooking, where the sum of needed devices at that time is greater than the amount of available devices
      foreach ($prebookings as $prebooking) {
          // get prebooks at time: $prebooking["prebook_begin"]
          $time_diff = strtotime($prebooking["prebook_begin"]) - time();
          $time_diff = $time_diff / 60;


          if ($time_diff > $max_duration) {
              return $max_duration;
          }

          $amount_of_needed_devices = 0;
          $time = $prebooking["prebook_begin"];
          for ($i = 0; $i < count($prebookings); $i++) {
              if ($prebookings[$i]["prebook_begin"] <= $time && $prebookings[$i]["prebook_end"] >= $time) {
                  $amount_of_needed_devices += $prebookings[$i]["prebook_amount"];
              }


              if ($amount_of_needed_devices >= $available_devices) {
                  // return the time difference between now and the prebooking begin time
                  return floor($time_diff);
              }
          }
      }

      return $max_duration;
  }
}

/*
 * STEPS TO GET AMOUNT OF AVAILABLE DEVICES FOR NORMAL BOOKING:
 * - bekomme prebookings, die sich im Puffer befinden (+/- 30 Minuten von der jeweiligen booking_begin Zeit)
 * - ziehe die Anzahl der reservierten Geräte * puffer_size (z.B. 110%) von der Gesamtanzahl ab
 * - ziehe die Anzahl der Geräte ab, welche momentan gebucht sind.
 * - zeige maximale Zeit an, die man ausleihen kann, bis die Geräte wieder gebraucht werden
 *  
 * umso mehr geräte man schon von den prebookings ausleiht und desto mehr der Gesamtduration erreicht ist, 
 * desto mehr Geräte werden von denen die noch nicht ausgeliehen, aber reserviert sind für die normale Ausleihe frei, 
 * da die Wahrscheinlichkeit immer geringer wird, dass die Geräte noch gebraucht werden.:
 * 
 * Formel für Anzahl der Geräte, die wieder frei sind (Ausleihdauer, Gesamtdauer, Anzahl_der_vorbestellten_Geräte, Ausgeliehene_Geräte)): 
 * f(Ausleihdauer) = (Ausleihdauer / Gesamtdauer * (Anzahl_der_vorbestellten_Geräte - Ausgeliehene_Geräte) / Anzahl_der_vorbestellten_Geräte) * (Anzahl_der_vorbestellten_Geräte - Ausgeliehene_Geräte)
 * 
 * Formel für Anzahl der freien Geräte zum Ausleihen:
 * a = summe(f(running_prebookings.booking_end - now)) + (total_devices - sum(running_prebookings.device_amount)) - sum(prebookings_not_runnig_but_in_buffer_device_amount) - normal_bookings_amount
 */