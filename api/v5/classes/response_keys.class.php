<?php
abstract class BasicEnum {
	/**
	 * @param array $response ["status" => "SUCCESS", "message" => "Success", "code" => 200]
	 * @param array $custom
	 * @return void
	 */
	public static function success($response = ["status" => Response::SUCCESS["status"], "message" => Response::SUCCESS["message"], "code" => Response::SUCCESS["code"]], $custom = []) {
		$response_status = $response["status"];
		$response_code = $response["code"];
		$message = $response["message"];

		// standard payload
		$payload = [
			"status" => $response_status,
			"message" => $message,
			"code" => $response_code,
			"version" => "v5",
			"timestamp" => time(),
			"request" => $_SERVER["REQUEST_URI"] ?? "",
			"method" => $_SERVER["REQUEST_METHOD"] ?? "",
		];
		
		
		http_response_code($response_code);
		echo json_encode(array_merge($payload, $custom), JSON_NUMERIC_CHECK); // JSON_NUMERIC_CHECK converts numeric strings to numbers
		die;
	}

	/**
	 * @param array $response ["status" => "SUCCESS", "message" => "Success", "code" => 200]
	 * @param array $fields
	 * @return void
	 */
	public static function error($response = ["status" => Response::SUCCESS["status"], "message" => Response::SUCCESS["message"], "code" => Response::SUCCESS["code"]], $fields = [], $custom = []) {
		$response_status = $response["status"];
		$response_code = $response["code"];
		$message = $response["message"];

		// standard payload
		$payload = [
			"status" => $response_status,
			"message" => $message,
			"code" => $response_code,
			"fields" => $fields,
			"version" => "v5",
			"timestamp" => time(),
			"request" => $_SERVER["REQUEST_URI"] ?? "",
			"method" => $_SERVER["REQUEST_METHOD"] ?? "",
		];
		
		throw new ResponseException(array_merge($payload, $custom), $payload["code"]);
	}
}

abstract class Response extends BasicEnum {
	const UNEXPECTED_ERROR = ["status" => "UNEXPECTED_ERROR", "message" => "Internal Server Error: ", "code" => 500]; // Default error message
	const PAGE_NOT_FOUND = ["status" => "PAGE_NOT_FOUND", "message" => "Page Not Found", "code" => 404]; // Page does not exist (404 error)
	const ROUTE_NOT_DEFINED = ["status" => "ROUTE_NOT_DEFINED", "message" => "Route Not Defined", "code" => 404]; // Route does not exist (404 error)
	const SUCCESS = ["status" => "SUCCESS", "message" => "Success", "code" => 200];
	const DEVICE_NOT_FOUND = ["status" => "DEVICE_NOT_FOUND", "message" => "Device ist in der Datenbank nicht verfügbar", "code" => 404]; // Devcies not found in database
	const NOT_ALLOWED = ["status" => "NOT_ALLOWED", "message" => "Dieser Request konnte aufgrund von fehlender Permission nicht ausgeführt werden", "code" => 403]; // User is not allowed to do this action (permission missing)
	const NOT_AUTHORIZED = ["status" => "NOT_AUTHORIZED", "message" => "Token ist nicht autorisiert", "code" => 401]; // Token not valid, or username/password wrong
	const NOT_ALLOWED_FOR_THIS_CLASS = ["status" => "NOT_ALLOWED_FOR_THIS_CLASS", "message" => "Es ist Ihnen nicht erlaubt mehrere Devices auszuleihen", "code" => 403]; // User is not allowed to lend more than one device at the same time
	const DEVICE_ALREADY_LENT = ["status" => "DEVICE_ALREADY_LENT", "message" => "Das Device wird bereits ausgeliehen", "code" => 409]; // Device already lending
	const REQUIRED_DATA_MISSING = ["status" => "REQUIRED_DATA_MISSING", "message" => "Input fehlt", "code" => 400]; // Required data missing
	const DEVICE_NOT_LENT = ["status" => "DEVICE_NOT_LENT", "message" => "Device kann nicht zurückgegeben werden", "code" => 409]; // Device not lent
	const RETURN_SUCCESS = ["status" => "RETURN_SUCCESS", "message" => "Device wurde zurückgegeben", "code" => 200]; // Device returned
	const INFO_SUCCESS = ["status" => "INFO_SUCCESS", "message" => "Info wird ausgegeben", "code" => 200]; // Info sent	
	const BOOKING_SUCCESS = ["status" => "BOOKING_SUCCESS", "message" => "Ausleihe erfolgreich", "code" => 201]; // Booking successfull
	const DEVICE_TYPE_NOT_FOUND = ["status" => "DEVICE_TYPE_NOT_FOUND", "message" => "Device Typ nicht gefunden", "code" => 404]; // Device type does not exist in database
	const DEVICE_ALREADY_EXISTS = ["status" => "DEVICE_ALREADY_EXISTS", "message" => "Device existiert bereits", "code" => 409]; // Device already exists in database
	const USER_ALREADY_EXISTS = ["status" => "USER_ALREADY_EXISTS", "message" => "User existiert bereits", "code" => 409]; // User already exists in database
	const USERCARD_ALREADY_ASSIGNED = ["status" => "USERCARD_ALREADY_ASSIGNED", "message" => "Usercard ist bereits einem User zugewiesen", "code" => 409]; // Usercard already assigned to a user
	const USERCARD_ALREADY_EXISTS = ["status" => "USERCARD_ALREADY_EXISTS", "message" => "Usercard existiert bereits", "code" => 409]; // Usercard already exists in database
	const BAD_REQUEST = ["status" => "BAD_REQUEST", "message" => "Bad Request", "code" => 400]; // Bad request
	const CLASS_NOT_FOUND = ["status" => "CLASS_NOT_FOUND", "message" => "Klasse nicht gefunden", "code" => 404]; // Class does not exist in database
	const USER_NOT_FOUND = ["status" => "USER_NOT_FOUND", "message" => "User nicht gefunden", "code" => 404]; // User does not exist in database
	const USER_ALREADY_ASSIGNED = ["status" => "USER_ALREADY_ASSIGNED", "message" => "User ist bereits einer Klasse zugewiesen", "code" => 409]; // User already assigned to a class
	const INVALID_PERMISSION = ["status" => "INVALID_PERMISSION", "message" => "Permission existiert nicht", "code" => 404]; // Permission does not exist in database 
	const USER_ALREADY_ASSIGNED_TO_TOKEN = ["status" => "USER_ALREADY_ASSIGNED_TO_TOKEN", "message" => "User ist bereits einem Token zugewiesen", "code" => 409]; // User already assigned to a token
	const USERCARD_NOT_FOUND = ["status" => "USERCARD_NOT_FOUND", "message" => "Usercard nicht gefunden", "code" => 404]; // Usercard does not exist in database
	const UID_ALREADY_EXISTS = ["status" => "UID_ALREADY_EXISTS", "message" => "UID existiert bereits", "code" => 409]; // UID already exists in database
	const INSERT_ERROR = ["status" => "INSERT_ERROR", "message" => "Fehler beim Einfügen in die Datenbank", "code" => 500]; // Error while inserting into database
	const TOKEN_NOT_FOUND = ["status" => "TOKEN_NOT_FOUND", "message" => "Token nicht gefunden", "code" => 404]; // Token does not exist in database
	const USERCARD_TYPE_NOT_FOUND = ["status" => "USERCARD_TYPE_NOT_FOUND", "message" => "Usercard Typ nicht gefunden", "code" => 404]; // Usercard type does not exist in database
	const CLASS_ALREADY_EXISTS = ["status" => "CLASS_ALREADY_EXISTS", "message" => "Klasse existiert bereits", "code" => 409]; // Class already exists in database
	const DEVICE_TYPE_ALREADY_EXISTS = ["status" => "DEVICE_TYPE_ALREADY_EXISTS", "message" => "Device Typ existiert bereits", "code" => 409]; // Device type already exists in database
	const USERCARD_TYPE_ALREADY_EXISTS = ["status" => "USERCARD_TYPE_ALREADY_EXISTS", "message" => "Usercard Typ existiert bereits", "code" => 409]; // Usercard type already exists in database
	const DUPLICATE_ENTRY = ["status" => "DUPLICATE_ENTRY", "message" => "Es wurden mehrmals der gleiche Eintrag gefunden (sql error: 1062)", "code" => 500]; // Integrity constraint violation: 1062 Duplicate entry (sql)
	const TOKEN_ALREADY_EXISTS = ["status" => "TOKEN_ALREADY_EXISTS", "message" => "Username des tokens existiert bereits", "code" => 409]; // Token already exists in database
	const INVALID_KEY = ["status" => "INVALID_KEY", "message" => "Ungültiger Key", "code" => 400]; // Key is not valid
	const ID_NOT_FOUND = ["status" => "ID_NOT_FOUND", "message" => "ID nicht gefunden", "code" => 404]; // ID does not exist in database (this is just a placeholder (e.g. DEVICE_NOT_FOUND, USER_NOT_FOUND, ...))
	const DUPLICATE = ["status" => "DUPLICATE", "message" => "Doppelter Eintrag", "code" => 409]; // Duplicate entry in database (this is just a placeholder (e.g. DEVICE_ALREADY_EXISTS, USER_ALREADY_EXISTS, ...))
	const FOREIGN_KEY_ERROR = ["status" => "FOREIGN_KEY_ERROR", "message" => "Fehler beim löschen, da Fremdschlüssel vorhanden", "code" => 409]; // Error while deleting, because of foreign key (this is just a placeholder (e.g. USER_HAS_BOOKINGS, ...))
	const USER_HAS_BOOKINGS = ["status" => "USER_HAS_BOOKINGS", "message" => "User hat noch aktive Ausleihen", "code" => 409]; // User has still aktive bookings
	const CLASS_HAS_USERS = ["status" => "CLASS_HAS_USERS", "message" => "Klasse hat noch aktive User", "code" => 409]; // Class has still active users (when deleting class, users aren't allowed to lend devices anymore)
	const DEVICE_HAS_ACTIVE_BOOKING = ["status" => "DEVICE_HAS_ACTIVE_BOOKING", "message" => "Device wird noch ausgeliehen", "code" => 409]; // Device has still active bookings
	const DEVICE_TYPE_HAS_DEVICES = ["status" => "DEVICE_TYPE_HAS_DEVICES", "message" => "Device Typ hat noch aktive Devices", "code" => 409]; // Device type has still active devices
	const USERCARD_HAS_USER = ["status" => "USERCARD_HAS_USER", "message" => "Usercard ist noch einem User zugewiesen", "code" => 409]; // Usercard is still assigned to a user
	const USERCARD_TYPE_HAS_USERCARDS = ["status" => "USERCARD_TYPE_HAS_USERCARDS", "message" => "Usercard Typ hat noch aktive Usercards", "code" => 409]; // Usercard type has still active usercards
	const DELETE_OWN_TOKEN_NOT_ALLOWED = ["status" => "DELETE_OWN_TOKEN_NOT_ALLOWED", "message" => "Löschen des eigenen Tokens nicht erlaubt", "code" => 409]; // Deleting own token is not allowed
	const TOKEN_HAS_USER = ["status" => "TOKEN_HAS_USER", "message" => "Token ist noch einem User zugewiesen", "code" => 409]; // Token is still assigned to a user
	const NO_CONTENT = ["status" => "NO_CONTENT", "message" => "Keine Daten gefunden", "code" => 204]; // No data found in database (this could be for example a user where no bookings are found)
	const NO_CHANGES = ["status" => "NO_CHANGES", "message" => "Keine Änderungen, alter und neuer Wert sind identisch", "code" => 200]; // No changes in database (this could be for example a user where no bookings are found)
	const API_RUNNING = ["status" => "API_RUNNING", "message" => "API ist aktiv", "code" => 200]; // API is running
	const INTERNAL_SERVER_ERROR = ["status" => "INTERNAL_SERVER_ERROR", "message" => "Interner Server Fehler", "code" => 500]; // Internal server error
	const CONFLICT_WITH_PREBOOK = ["status" => "CONFLICT_WITH_PREBOOK", "message" => "Ausleihe nicht möglich, da dieses Gerät reserviert ist", "code" => 409]; // Booking not allowed, because device is reserved
	const NOT_ENOUGH_DEVICES_AVAILABLE = ["status" => "NOT_ENOUGH_DEVICES_AVAILABLE", "message" => "Nicht genügend Geräte verfügbar", "code" => 409]; // Not enough devices available for prebooking
	const USER_ALREADY_HAS_PREBOOKING_AT_THAT_TIME = ["status" => "USER_ALREADY_HAS_PREBOOKING_AT_THAT_TIME", "message" => "User hat bereits eine Reservierung in diesem Zeitraum", "code" => 409]; // User already has a prebooking at that time period
	const PREBOOK_TIME_NOT_ALLOWED = ["status" => "PREBOOK_TIME_NOT_ALLOWED", "message" => "Ausleihe in dieser Zeit nicht erlaubt", "code" => 409]; // Prebook not allowed at that time
	const PREBOOK_DURATION_NOT_ALLOWED = ["status" => "PREBOOK_DURATION_NOT_ALLOWED", "message" => "Ausleihe mit dieser Dauer nicht erlaubt", "code" => 409]; // Prebook not allowed with that duration
	const PREBOOK_NOT_FOUND = ["status" => "PREBOOK_NOT_FOUND", "message" => "Reservierung nicht gefunden", "code" => 404]; // Prebooking not found
}