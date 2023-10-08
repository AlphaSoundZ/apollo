<?php
require_once 'config.php';

class Prebook
{
    /**
     * @param int $user_id The id of the user who wants to prebook
     * @param int $device_amount The amount of devices the user wants to prebook
     * @param string $booking_begin The begin of the prebooking (US format)
     * @param string $booking_end The end of the prebooking (US format)
     * @param int $token_id if set to some id, it will check whether the user prebooks for himself; if not set, user can also prebook for others
     * @return int The prebook id
     * @throws ResponseException
     */
    public static function create($device_amount, $booking_begin, $booking_end, $user_id = null, $own_token_id = null)
    {
        global $pdo;

        if (!$user_id && !$own_token_id) {
            Response::error(array_merge(Response::REQUIRED_DATA_MISSING, ["message" => Response::REQUIRED_DATA_MISSING["message"] . " (user_id)"]), ["user_id"]);
        }

        if ($own_token_id) {
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

            if ($user_id != null && $user_id != $token_user && !isset(authorize()["permissions"]["CRUD_prebook"])) {
                // user is not allowed to prebook for others
                Response::error(Response::NOT_ALLOWED);
            } else {
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

        if ($booking_time < $min_booking_time || $booking_time > $max_booking_time) { // check if the booking time is in the allowed range
            Response::error(Response::PREBOOK_DURATION_NOT_ALLOWED, ["begin", "end"], ["duration" => ["min_booking_duration" => $min_booking_time, "max_booking_duration" => $max_booking_time, "current_duration" => $booking_time]]);
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
            Response::error(Response::PREBOOK_TIME_NOT_ALLOWED, ["begin"], ["min_booking_begin" => $day_of_min]);
        } else if ($day_of_booking_begin > $day_of_max) {
            Response::error(Response::PREBOOK_TIME_NOT_ALLOWED, ["begin"], ["max_booking_begin" => $day_of_max]);
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

    public static function availableDevicesForPrebooking($prebook_begin, $prebook_end)
    {
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



            for ($i = 0; $i < count($prebookings); $i++) {
                if (strtotime($prebookings[$i]["prebook_begin"]) <= $time && strtotime($prebookings[$i]["prebook_end"]) >= $time) {
                    $amount_of_needed_devices += $prebookings[$i]["prebook_amount"];
                }
            }

            $availableDevices = min($availableDevices, $total_devices - $amount_of_needed_devices);
        }

        return $availableDevices;
    }
}

/*
 * STEPS TO GET AMOUNT OF AVAILABLE DEVICES FOR PREBOOKING:
 * - get prebookings within the booking time
 * - subtract the amount of devices from the total amount
 */