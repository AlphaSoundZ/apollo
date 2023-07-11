<?php
require_once 'config.php';

class Prebook {
    /**
     * @param int $user_id The id of the user who wants to prebook
     * @param int $device_amount The amount of devices the user wants to prebook
     * @param string $booking_begin The begin of the prebooking (US format)
     * @param string $booking_end The end of the prebooking (US format)
     * @param int $token_id if set to some id, it will check whether the user prebooks for himself; if not set, user can also prebook for others
     * @return int The prebook id
     * @throws ResponseException
     */
    public static function create($device_amount, $booking_begin, $booking_end, $user_id = null, $own_token_id = null) {
        global $pdo;

        if (!$user_id && !$own_token_id)
        {
            Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " (user_id)"]), ["user_id"]);
        }

        if ($own_token_id)
        {
            // check if the user is allowed to prebook (for himself or for all)
            $sql = "SELECT * FROM token WHERE token_id = :token_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                "token_id" => $own_token_id
            ));
            
            $token_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$token_user)
                Response::error(Response::TOKEN_NOT_FOUND);
            else
                $token_user = $token_user["token_user_id"];
            
            if ($user_id != null && $user_id != $token_user && !isset(authorize()["permissions"]["CRUD_prebook"]))
            {
                // user is not allowed to prebook for others
                Response::error(Response::NOT_ALLOWED);
            }
            else
            {
                $user_id = $token_user;
            }
        }

        // check if the user has already prebooked devices at that time
        $sql = "SELECT * FROM prebook WHERE prebook_user_id = :user_id AND prebook_begin < :booking_end AND prebook_end > :booking_begin";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            "user_id" => $user_id,
            "booking_begin" => $booking_begin,
            "booking_end" => $booking_end
        ));

        if ($stmt->rowCount() > 0) {
            Response::error(Response::USER_ALREADY_HAS_PREBOOKING_AT_THAT_TIME);
        }

        // check if the user has multibooking permission
        $sql = "SELECT * FROM user LEFT JOIN property_class ON property_class.class_id = user.user_class WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            "user_id" => $user_id
        ));
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user["multi_booking"] == 0) {
            Response::error(Response::NOT_ALLOWED_FOR_THIS_CLASS);
        }

        // check if min and max booking time is respected
        $min_booking_time = $_ENV["MIN_BOOKING_DURATION"]; // in minutes
        $max_booking_time = $_ENV["MAX_BOOKING_DURATION"]; // in minutes

        // time difference in seconds
        $booking_time = strtotime($booking_end) - strtotime($booking_begin);

        // time to minutes
        $booking_time = $booking_time / 60;

        if ($booking_time < $min_booking_time || $booking_time > $max_booking_time) {
            Response::error(Response::BOOKING_TIME_NOT_ALLOWED, ["begin", "end"], ["min_booking_duration" => $min_booking_time, "max_booking_duration" => $max_booking_time]);
        }

        // check if the booking_begin is at least $min_next_prebook_distance days and at most $max_next_prebook_distance days in the future
        $min_next_prebook_distance = $_ENV["MIN_PREBOOK_TIME_DISTANCE"]; // in days
        $max_next_prebook_distance = $_ENV["MAX_PREBOOK_TIME_DISTANCE"]; // in days
        $day_of_booking_begin = date("Y-m-d", strtotime($booking_begin));
        $day_of_min = date("Y-m-d", strtotime("+$min_next_prebook_distance days"));
        $day_of_max = date("Y-m-d", strtotime("+$max_next_prebook_distance days"));

        // check if there are enough devices available
        $available_devices = self::availableDevicesForPrebooking($booking_begin, $booking_end);

        if ($available_devices < $device_amount) {
            Response::error(Response::NOT_ENOUGH_DEVICES_AVAILABLE, [], ["available_devices" => $available_devices]);
        }

        if ($day_of_booking_begin < $day_of_min) {
            Response::error(Response::BOOKING_TIME_NOT_ALLOWED, ["begin"], ["min_booking_begin" => $day_of_min]);
        } else if ($day_of_booking_begin > $day_of_max) {
            Response::error(Response::BOOKING_TIME_NOT_ALLOWED, ["begin"], ["max_booking_begin" => $day_of_max]);
        }
        
        // finally create the prebooking
        try {
            $sql = "INSERT INTO prebook (prebook_user_id, prebook_amount, prebook_begin, prebook_end) VALUES (:user_id, :device_amount, :booking_begin, :booking_end)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                "user_id" => $user_id,
                "device_amount" => $device_amount,
                "booking_begin" => $booking_begin,
                "booking_end" => $booking_end
            ));
        } catch (PDOException $e) {
            // check if user exists
            $sql = "SELECT * FROM user WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                "user_id" => $user_id
            ));

            if ($stmt->rowCount() == 0) {
                Response::error(Response::USER_NOT_FOUND);
            }

            throw $e;
        }

        return $pdo->lastInsertId();
    }

    public static function availableDevicesForBooking() {
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

    public static function availableDevicesForPrebooking($prebook_begin, $prebook_end) {
        global $pdo;

        // get total amount of devices
        $sql = "SELECT COUNT(*) FROM devices";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $total_devices = $stmt->fetchColumn();

        // get prebookings at the time span
        $sql = "SELECT * FROM prebook WHERE prebook_begin < :prebook_end AND prebook_end > :prebook_begin ORDER BY prebook_begin ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
                "prebook_begin" => $prebook_begin,
                "prebook_end" => $prebook_end
        ));
        $prebookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $availableDevices = $total_devices;
        
        // get next prebooking, where the sum of needed devices at that time is the highest
        foreach ($prebookings as $prebooking) {
            
            $amount_of_needed_devices = 0;
            $time = strtotime($prebooking["prebook_begin"]);


            
            for ($i = 0; $i < count($prebookings); $i++)
            {
                if (strtotime($prebookings[$i]["prebook_begin"]) <= $time && strtotime($prebookings[$i]["prebook_end"]) >= $time) {
                    $amount_of_needed_devices += $prebookings[$i]["prebook_amount"];
                }
            }

            $availableDevices = min($availableDevices, $total_devices - $amount_of_needed_devices);
        }

        return $availableDevices;
    }

    public static function maxBookingDuration() {
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
            for ($i = 0; $i < count($prebookings); $i++)
            {
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
 * STEPS TO GET AMOUNT OF AVAILABLE DEVICES FOR PREBOOKING:
 * - get prebookings within the booking time
 * - subtract the amount of devices from the total amount
 * 
 * STEPS TO GET AMOUNT OF AVAILABLE DEVICES FOR NORMAL BOOKING:
 * - bekomme prebookings, die sich im Puffer befinden (+/- 30 Minuten von der jeweiligen booking_begin Zeit)
 * - ziehe die Anzahl der Geräte * puffer_size (z.B. 110%) von der Gesamtanzahl ab
 * - ziehe die Anzahl der Geräte ab, welche momentan gebucht sind.
 * - zeige maximale Zeit an, die man ausleihen kann, bis die Geräte wieder gebraucht werden
 * 
 * 
 * umso mehr geräte man schon von den prebookings ausleiht und desto mehr der Gesamtduration erreicht ist, desto mehr Geräte werden für die normale Ausleihe frei:
 * 
 * Formel für Anzahl der Geräte, die wieder frei sind (Ausleihdauer, Gesamtdauer, Anzahl_der_vorbestellten_Geräte, Ausgeliehene_Geräte)): 
 * f(Ausleihdauer) = (Ausleihdauer / Gesamtdauer * (Anzahl_der_vorbestellten_Geräte - Ausgeliehene_Geräte) / Anzahl_der_vorbestellten_Geräte) * (Anzahl_der_vorbestellten_Geräte - Ausgeliehene_Geräte)
 * 
 * Formel für Anzahl der freien Geräte zum Ausleihen:
 * a = summe(f(running_prebookings.booking_end - now)) + (total_devices - sum(running_prebookings.device_amount)) - sum(prebookings_not_runnig_but_in_buffer_device_amount) - normal_bookings_amount
 */