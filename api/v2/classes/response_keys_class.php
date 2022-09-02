<?php
abstract class BasicEnum {
	private static $constCacheArray = NULL;

	private static function getConstants() {
		if (self::$constCacheArray == NULL) {
			self::$constCacheArray = [];
		}
		$calledClass = get_called_class();
		if (!array_key_exists($calledClass, self::$constCacheArray)) {
			$reflect = new ReflectionClass($calledClass);
			self::$constCacheArray[$calledClass] = $reflect->getConstants();
		}
		return self::$constCacheArray[$calledClass];
	}

	public static function isValidName($name, $strict = false) {
		$constants = self::getConstants();

		if ($strict) {
			return array_key_exists($name, $constants);
		}

		$keys = array_map('strtolower', array_keys($constants));
		return in_array(strtolower($name), $keys);
	}

	public static function isValidValue($value, $strict = true) {
		$values = array_values(self::getConstants());
		return in_array($value, $values, $strict);
	}

	public static function success($message, $response_code, $custom = []) {
		http_response_code(200);
		echo json_encode(array_merge(["response" => $response_code, "message" => $message], $custom));
		die;
	}

	public static function getValue($name)
	{
		return self::getConstants()[$name];
	}
}

abstract class Response extends BasicEnum {
	// Device includes Usercard and Devices that can be lend to users

	const UNEXPECTED_ERROR = "Internal Server Error: "; // Default
	const SUCCESS = "Success";
	const DEVICE_NOT_FOUND = "Usercard / Device ist in der Datenbank nicht verfügbar"; // Devcies not found in database
	const NOT_ALLOWED = "Dieser Request konnte aufgrund von fehlender Permission nicht ausgeführt werden"; // User is not allowed to do this action (permission missing)
	const NOT_AUTHORIZED = "Token ist nicht autorisiert"; // Token not valid, or username/password wrong
	const NOT_ALLOWED_FOR_THIS_CLASS = "Es ist Ihnen nicht erlaubt mehrere Devices auszuleihen"; // User already lending but is no MultiUser
	const NOT_ALLOWED_FOR_THIS_DEVICE = "Das Device wird bereits ausgeliehen"; // Device already lending
	const REQUIRED_DATA_MISSING = "Input fehlt"; // Required data missing
	const WRONG_DEVICE_TYPE = "Falscher Device Typ"; // Not a valid device type
	const RETURN_NOT_POSSIBLE = "Device kann nicht zurückgegeben werden"; // Device not lent
	const RETURN_SUCCESS = "Device wurde zurückgegeben"; // Device returned
	const INFO_SUCCESS = "Info wird ausgegeben"; // Info sent
	const BOOKING_SUCCESS = "Ausleihe erfolgreich"; // Booking successfull
	const DEVICE_TYPE_NOT_FOUND = "Device Typ nicht gefunden"; // Device type does not exist in database
	const DEVICE_ALREADY_EXISTS = "Device existiert bereits"; // Device already exists in database
	const USER_ALREADY_EXISTS = "User existiert bereits"; // Device already exists in database
	const USERCARD_ALREADY_ASSIGNED = "Usercard ist bereits einem User zugewiesen"; // Usercard already assigned to a user
	const USERCARD_ALREADY_EXISTS = "Usercard existiert bereits"; // Usercard already exists in database
	const BAD_REQUEST = "Bad Request"; // Bad request
	const CLASS_NOT_FOUND = "Klasse nicht gefunden"; // Class not found in database
	const USER_NOT_FOUND = "User nicht gefunden"; // User not found in database
	const USER_ALREADY_ASSIGNED = "User ist bereits einer Usercard zugewiesen"; // User already assigned to a user
}