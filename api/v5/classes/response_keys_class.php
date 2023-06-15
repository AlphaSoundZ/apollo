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

	public static function success($message, $response_code = "SUCCESS", $custom = []) {
		http_response_code(200);
		echo json_encode(array_merge(["response" => $response_code, "message" => $message], $custom), JSON_NUMERIC_CHECK);
		die;
	}

	public static function getValue($name)
	{
		return self::getConstants()[$name];
	}
}

abstract class Response extends BasicEnum {

	const UNEXPECTED_ERROR = "Internal Server Error: "; // Default
	const PAGE_NOT_FOUND = "Page Not Found"; // Page does not exist (404 error)
	const ROUTE_NOT_DEFINED = "Route Not Defined"; // Route does not exist (404 error)
	const SUCCESS = "Success";
	const DEVICE_NOT_FOUND = "Device ist in der Datenbank nicht verfügbar"; // Devcies not found in database
	const NOT_ALLOWED = "Dieser Request konnte aufgrund von fehlender Permission nicht ausgeführt werden"; // User is not allowed to do this action (permission missing)
	const NOT_AUTHORIZED = "Token ist nicht autorisiert"; // Token not valid, or username/password wrong
	const NOT_ALLOWED_FOR_THIS_CLASS = "Es ist Ihnen nicht erlaubt mehrere Devices auszuleihen"; // User already lending but is no Multibooking-User
	const NOT_ALLOWED_FOR_THIS_DEVICE = "Das Device wird bereits ausgeliehen"; // Device already lending
	const REQUIRED_DATA_MISSING = "Input fehlt"; // Required data missing
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
	const PERMISSION_NOT_FOUND = "Token Permission nicht gefunden"; // Permission not found in databases
	const USER_ALREADY_ASSIGNED_TO_TOKEN = "User ist bereits einem Token zugewiesen"; // User already assigned to a token
	const USERCARD_NOT_FOUND = "Usercard nicht gefunden"; // Usercard not found in database
	const UID_ALREADY_EXISTS = "UID existiert bereits"; // UID already exists in database
	const INSERT_ERROR = "Fehler beim Einfügen in die Datenbank"; // Error while inserting into database
	const TOKEN_NOT_FOUND = "Token nicht gefunden"; // Token does not exist in database
	const USERCARD_TYPE_NOT_FOUND = "Usercard Typ nicht gefunden"; // Usercard type does not exist in database
	const CLASS_ALREADY_EXISTS = "Klasse existiert bereits"; // class already exists in database
	const DEVICE_TYPE_ALREADY_EXISTS = "Device Typ existiert bereits"; // Device type already exists in database
	const USERCARD_TYPE_ALREADY_EXISTS = "Usercard Typ existiert bereits"; // Usercarcd type already exists in database
	const DUPLICATE_ENTRY = "Es wurden mehrmals der gleiche Eintrag gefunden"; // Integrity constraint violation: 1062 Duplicate entry (sql)
	const TOKEN_ALREADY_EXISTS = "Username des Tokens existiert bereits"; // Token already exists in database
	const INVALID_KEY = "Ungültiger Key"; // Invalid key
	const ID_NOT_FOUND = "ID nicht gefunden"; // ID not found in database (this is just a placeholder (e.g. DEVICE_NOT_FOUND), this will never be returned)
	const DUPLICATE = "Doppelter Eintrag"; // Duplicate entry (this is just a placeholder (e.g. DEVICE_ALREADY_EXISTS), this will never be returned)
	const FOREIGN_KEY_ERROR = "Fehler beim Löschen, da Fremdschlüssel vorhanden"; // Error while deleting, because foreign key exists (this is just a placeholder (e.g. USER_HAS_BOOKINGS), this will never be returned)
	const EMPTY_TABLE = "Tabelle ist leer"; // Table is empty
	const USER_HAS_BOOKINGS = "User hat noch aktive Ausleihen"; // User has still active bookings
	const CLASS_HAS_USERS = "Klasse hat noch aktive User"; // Class has still active users
	const DEVICE_HAS_ACTIVE_BOOKING = "Device wird noch ausgeliehen"; // Device has still active booking
	const DEVICE_TYPE_HAS_DEVICES = "Device Typ hat noch aktive Devices"; // Device type has still active devices
	const USERCARD_HAS_USER = "Usercard hat noch User"; // Usercard has still active user
	const USERCARD_TYPE_HAS_USERCARDS = "Usercard Typ hat noch Usercards"; // Usercard type has still active usercards
	const DELETE_OWN_TOKEN_NOT_ALLOWED = "Löschen des eigenen Tokens nicht erlaubt"; // Deleting own token not allowed
	const TOKEN_HAS_USER = "Token hat noch User"; // Token has still active user
}