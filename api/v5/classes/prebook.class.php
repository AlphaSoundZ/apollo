<?php
require_once 'config.php';

class Prebook {
    public static function create($user_id, $device_amount, $booking_begin, $booking_end) {
        global $pdo;
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
        $maximum_duration = $_ENV["MAXIMUM_BOOKING_DURATION"]; // in minutes
        
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

            
            if ($time_diff > $maximum_duration) {
                return $maximum_duration;
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

        return $maximum_duration; 
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