<?php
require_once 'config.php';
require 'vendor/autoload.php';

$router = new \Bramus\Router\Router;

$router->set404('/', function() {
    throw new CustomException(Response::ROUTE_NOT_DEFINED, "ROUTE_NOT_DEFINED", 404);
});

// GET
$router->get('/status', function () {
    require 'status.php';
});

$router->get('/user/class(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for class with $id
    {
        $response["data"] = Select::search([["table" => "property_class"]], ["*"], ["class_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
    }
    else // show every class
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_class"]], ["*"], ["class_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle Klassen";
            $response["data"] = Select::select([["table" => "property_class"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/user(/[^/]+)?', function($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null) // search for user with $id
    {
        $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["user_id", "class_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Benutzer nicht gefunden";
    }
    else if (isset($booking) && $booking == "true") // show all booking users
    {
        $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "event", "join" => ["user.user_id", "event.event_user_id"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name", "user.user_usercard_id", "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) AS amount"], ["page" => $page, "size" => $size, "groupby" => "user.user_id", "having" => "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) > 0"]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Es wird zurzeit nichts ausgeliehen";
    }
    else // show all users or search for user using ?query=
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["user_firstname", "user_lastname"], $query, ["page" => $page, "size" => $size, "strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Benutzer";
            $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $size]);
        }
    }
    
    // echo json_encode($response, JSON_PRETTY_PRINT); // return the response
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/user(/\d+)/history', function($id) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["data"] = Select::search([["table" => "event"]], ["event.event_user_id", "event.event_begin", "event.event_end", "event.event_multi_booking_id"], ["event_user_id"], $id, ["page" => $page, "size" => $size, "strict" => true, "groupby" => "event.event_multi_booking_id", "orderby" => "event.event_multi_booking_id", "direction" => "DESC"]);
    if (!$response["data"])
    {
        $response["message"] = "Keine Buchungen gefunden";
        echo json_encode($response);
        return;
    }
    $response["message"] = "Buchungen zu diesem Benutzer gefunden";

    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/device/type(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for class with $id
    {
        $response["data"] = Select::search([["table" => "property_device_type"]], ["*"], ["device_type_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
    }
    else // show every class
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_device_type"]], ["*"], ["device_type_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle Gerätetypen";
            $response["data"] = Select::select([["table" => "property_device_type"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/device(/[^/]+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    if ($id !== null) // search for device with $id or uid
    {
        $response["data"] = Select::search([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["device_id", "device_uid", "device_type_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Gerät gefunden" : "Gerät nicht gefunden";
    }
    else if ($booking == "true") // show all booked devices
    {
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]], ["table" => "user", "join" => ["user.user_id", "devices.device_lend_user_id"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name", "user.user_id", "user.user_firstname", "user.user_lastname"], ["page" => $page, "size" => $size, "where" => "devices.device_lend_user_id != 0"]);
        $response["message"] = ($response["data"]) ? "Alle gebuchten Geräte" : "Es werden derzeit keine Geräte gebucht";
    }
    else // show every device
    {
        $response["message"] = "Alle Geräte";
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["page" => $page, "size" => $size]);
    }
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/device(/[^/]+)/history', function ($id) {
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";

    // check if device exists
    $device = Select::search([["table" => "devices"]], ["device_id", "device_uid"], ["device_id", "device_uid"], $id, ["strict" => true]);
    if (!$device)
    {
        $response["message"] = "Gerät nicht gefunden";
        echo json_encode($response);
        return;
    }

    // if $id is an $uid
    $id = $device[0]["device_id"];

    $response["data"] = Select::search([["table" => "event"]], ["event.event_device_id", "event.event_begin", "event.event_end", "event.event_multi_booking_id"], ["event_device_id"], $id, ["page" => $page, "size" => $size, "strict" => true]);
    if (!$response["data"])
    {
        $response["message"] = "Keine Buchungen gefunden";
        echo json_encode($response);
        return;
    }
    $response["message"] = "Buchungen zu diesem Benutzer gefunden";

    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/usercard/type(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for usercard type with $id
    {
        $response["data"] = Select::search([["table" => "property_usercard_type"]], ["*"], ["usercard_type_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    }
    else // show every usercard type
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_usercard_type"]], ["*"], ["usercard_type_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle User Indentifikationen";
            $response["data"] = Select::select([["table" => "property_usercard_type"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/usercard(/[^/]+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for usercard with $id
    {
        $response["data"] = Select::search([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["usercard.*", "property_usercard_type.usercard_type_name", "user_id", "user_firstname", "user_lastname", "class_name"], ["usercard_id", "usercard_uid", "usercard_type_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    }
    else // show every usercard
    {
        $response["message"] = "Alle User Indentifikationen";
        $response["data"] = Select::select([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["usercard.*", "property_usercard_type.usercard_type_name", "user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $size]);
    }
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/token(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null) // search for user with $id
    {
        $response["data"] = Select::search([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["token_id"], $id, ["strict" => true, "groupby" => "token.token_id"]);
        $response["message"] = ($response["data"]) ? "Token gefunden" : "Token nicht gefunden";

        if ($response["data"])
        {
            $response["data"][0]["permission_id"] = explode(", ", $response["data"][0]["permission_id"]);
            $response["data"][0]["permission_text"] = explode(", ", $response["data"][0]["permission_text"]);
        }
    }
    else // show all users or search for user using ?query=
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["query"] = $query;
            $response["data"] = Select::search([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["token_username"], $query, ["strict" => $strict, "page" => $page, "size" => $size, "groupby" => "token.token_id"]);
            
            for ($i = 0; $i < count($response["data"]); $i++)
            {
                $response["data"][$i]["data"]["permission_id"] = explode(", ", $response["data"][$i]["data"]["permission_id"]);
                $response["data"][$i]["data"]["permission_text"] = explode(", ", $response["data"][$i]["data"]["permission_text"]);
            }
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Tokens";
            $response["data"] = Select::select([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["strict" => true, "page" => $page, "size" => $size, "groupby" => "token.token_id"]);
            
            for ($i = 0; $i < count($response["data"]); $i++)
            {
                $response["data"][$i]["permission_id"] = explode(", ", $response["data"][$i]["permission_id"]);
                $response["data"][$i]["permission_text"] = explode(", ", $response["data"][$i]["permission_text"]);
            }
        }
    }
    
    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

$router->get('/token/permission(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id !== null)
    {
        $response["data"] = Select::search([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], ["permission_id"], $id, ["page" => $page, "size" => $size, "strict" => true]);
        $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
    }
    else
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], ["permission_text"], $query, ["page" => $page, "size" => $size, "strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Token Permissions";
            $response["data"] = Select::select([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], ["page" => $page, "size" => $size]);
        }
    }

    Response::success($response["message"], "SUCCESS", ["data" => $response["data"]]);
});

// POST
$router->post('/csv', function () {
    require 'classes/csv_class.php';
    authorize("add_csv");

    $data = getData("POST", ["table", "columns", "string", "seperator", "linebreak"]);
    $global = (isset($data["global"])) ? $data["global"] : [];
    $enclosure = (isset($data["enclosure"])) ? $data["enclosure"] : "";

    $csv = new Csv($data["table"], $data["columns"], $data["string"], $data["seperator"], $data["linebreak"], $global, $enclosure);
    $csv->checkForError();
    $csv->add();

    Response::success(count($csv->rows) . " Zeilen wurden eingefügt");
});

$router->post('/token/authorize', function () {
    require 'classes/token_class.php';

    $data = getData("POST", ["username", "password"]);

    $username = $data["username"];
    $password = $data["password"];

    $token["jwt"] = Token::getToken($username, $password, $_ENV["JWT_KEY"]);

    Response::success(Response::SUCCESS, "SUCCESS", $token);
});

$router->post('/user/create', function () {
    require "classes/create_class.php";
    authorize("create_user");

    $data = getData("POST", ["firstname", "lastname", "class_id"]);
    $usercard_id = (isset($data["usercard_id"])) ? $data["usercard_id"] : null;
    $token_id = (isset($data["token_id"])) ? $data["token_id"] : null;
    $ignore_duplicates = (isset($data["ignore_duplicates"]) && $data["ignore_duplicates"] == true) ? true : false;

    $id = Create::user($data["firstname"], $data["lastname"], $data["class_id"], $usercard_id, $token_id, $ignore_duplicates);

    Response::success(Response::SUCCESS, "SUCCESS", ["user_id" => $id]);
});

$router->post('/usercard/create', function () {
    require "classes/create_class.php";
    authorize("create_usercard");

    $data = getData("POST", ["uid", "type"]);

    $allow_reassigning = (isset($data["allow_reassigning"]) && $data["allow_reassigning"] == true) ? true : false;
    $user_id = (isset($data["user_id"]) && $data["user_id"] == true) ? $data["user_id"] : null;

    $id = Create::usercard($data["uid"], $data["type"], $user_id, $allow_reassigning);

    Response::success(Response::SUCCESS, "SUCCESS", ["usercard_id" => $id]);
});

$router->post('/device/create', function () {
    require "classes/create_class.php";
    authorize("create_device");

    $data = getData("POST", ["uid", "type"]);

    $id = Create::device($data["uid"], $data["type"]);

    Response::success(Response::SUCCESS, "SUCCESS", ["device_id" => $id]);
});

$router->post('/user/class/create', function () {
    require "classes/create_class.php";
    authorize("create_user_class");
    
    $data = getData("POST", ["text", "multi_booking"]);

    $id = Create::property_class($data["text"], $data["multi_booking"]);

    Response::success(Response::SUCCESS, "SUCCESS", ["class_id" => $id]);
});

$router->post('/device/type/create', function () {
    require "classes/create_class.php";
    authorize("create_device_type");
    
    $data = getData("POST", ["text"]);

    $id = Create::property_device_type($data["text"]);

    Response::success(Response::SUCCESS, "SUCCESS", ["device_type_id" => $id]);
});

$router->post('/usercard/type/create', function () {
    require "classes/create_class.php";
    authorize("create_usercard_type");
    
    $data = getData("POST", ["text"]);

    $id = Create::property_usercard_type($data["text"]);

    Response::success(Response::SUCCESS, "SUCCESS", ["usercard_type_id" => $id]);
});

$router->post('/token/create', function () {
    require 'classes/create_class.php';
    authorize("create_token");

    $data = getData("POST", ["username", "password", "permissions"]);

    $id = Create::token($data["username"], $data["password"], $data["permissions"]);

    Response::success(Response::SUCCESS, "SUCCESS", ["token_id" => $id]);
});

// PATCH
$router->patch('/user/change', function () {
    require "classes/update_class.php";
    authorize("create_user");

    $data = getData("POST", ["id"]);

    Update::update(
        "user",
        $data["id"],
        $updating_values = [
            "user_firstname" => $data["firstname"] ?? null,
            "user_lastname" => $data["lastname"] ?? null,
            "user_class" => $data["class_id"] ?? null,
            "user_token_id" => $data["token_id"] ?? null,
            "user_usercard_id" => $data["usercard_id"] ?? null
        ],
        $duplicate_errorhandling = [
            "message" => Response::USER_ALREADY_EXISTS, 
            "response_code" => "USER_ALREADY_EXISTS"
        ],
        $not_found_errorhandling = [
            "message" => Response::USER_NOT_FOUND, 
            "response_code" => "USER_NOT_FOUND"
        ],
        $changeable_columns = [
            "user_firstname",
            "user_lastname",
            "user_class",
            "user_token_id",
            "user_usercard_id"
        ]
    );

    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->patch('/user/class/change', function () {
    require "classes/update_class.php";
    authorize("create_user_class");

    $data = getData("POST", ["id"]);

    Update::update(
        "property_class", 
        $data["id"], 
        $updating_values = [
            "class_name" => $data["name"] ?? null,
            "multi_booking" => $data["multi_booking"] ?? null
        ],
        $duplicate_errorhandling = [
            "message" => Response::CLASS_ALREADY_EXISTS, 
            "response_code" => "CLASS_ALREADY_EXISTS"
        ], 
        $not_found_errorhandling = [
            "message" => Response::CLASS_NOT_FOUND, 
            "response_code" => "CLASS_NOT_FOUND"
        ], 
        $changeable_columns = [
            "class_name",
            "multi_booking"
        ]
    );

    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->patch('/device/type/change', function () {
    require "classes/update_class.php";
    authorize("create_device_type");

    $data = getData("POST", ["id", "name"]);

    Update::update(
        "property_device_type", 
        $data["id"], 
        $updating_values = [
            "device_type_name" => $data["name"]
        ],
        $duplicate_errorhandling = [
            "message" => Response::DEVICE_TYPE_ALREADY_EXISTS, 
            "response_code" => "DEVICE_TYPE_ALREADY_EXISTS"
        ], 
        $not_found_errorhandling = [
            "message" => Response::DEVICE_TYPE_NOT_FOUND, 
            "response_code" => "DEVICE_TYPE_NOT_FOUND"
        ], 
        $changeable_columns = [
            "device_type_name"
        ]
    );
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->patch('/usercard/type/change', function () {
    require "classes/update_class.php";
    authorize("create_usercard_type");

    $data = getData("POST", ["id", "name"]);

    Update::update(
        "property_usercard_type", 
        $data["id"], 
        $updating_values = [
            "usercard_type_name" => $data["name"]
        ],
        $duplicate_errorhandling = [
            "message" => Response::USERCARD_TYPE_ALREADY_EXISTS, 
            "response_code" => "USERCARD_TYPE_ALREADY_EXISTS"
        ], 
        $not_found_errorhandling = [
            "message" => Response::USERCARD_TYPE_NOT_FOUND, 
            "response_code" => "USERCARD_TYPE_NOT_FOUND"
        ], 
        $changeable_columns = [
            "usercard_type_name"
        ]
    );

    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->patch('device/change', function () {
    require "classes/update_class.php";
    authorize("change_device");

    $data = getData("POST", ["id"]);

    Update::update(
        "devices", 
        $data["id"], 
        $updating_values = [
            "device_uid" => $data["uid"] ?? null, 
            "device_type" => $data["type"] ?? null
        ],
        $duplicate_errorhandling = [
            "message" => Response::DEVICE_ALREADY_EXISTS, 
            "response_code" => "DEVICE_ALREADY_EXISTS"
        ], 
        $not_found_errorhandling = [
            "message" => Response::DEVICE_NOT_FOUND, 
            "response_code" => "DEVICE_NOT_FOUND"
        ], 
        $changeable_columns = [
            "device_uid", 
            "device_type"
        ]
    );

    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->patch('/usercard/change', function () {
    require "classes/update_class.php";
    authorize("change_usercard");

    $data = getData("POST", ["id"]);

    Update::update(
        "usercard",
        $data["id"],
        $updating_values = [
            "usercard_uid" => $data["uid"] ?? null,
            "usercard_type" => $data["type"] ?? null
        ],
        $duplicate_errorhandling = [
            "message" => Response::USERCARD_ALREADY_EXISTS, 
            "response_code" => "USERCARD_ALREADY_EXISTS"
        ],
        $not_found_errorhandling = [
            "message" => Response::USERCARD_NOT_FOUND, 
            "response_code" => "USERCARD_NOT_FOUND"
        ],
        $changeable_columns = [
            "usercard_uid",
            "usercard_type"
        ]
    );

    Response::success(Response::SUCCESS, "SUCCESS");
});

// DELETE
$router->delete('/user/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_user");

    // get data from request (id can be either an array of id's or a single id)
    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "user", 
                $id, 
                $not_found_errorhandling = [
                    "message" => Response::USER_NOT_FOUND, 
                    "response_code" => "USER_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::USER_HAS_BOOKINGS, 
                    "response_code" => "USER_HAS_BOOKINGS"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "user", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::USER_NOT_FOUND, 
                "response_code" => "USER_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::USER_HAS_BOOKINGS, 
                "response_code" => "USER_HAS_BOOKINGS"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/user/class/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_user_class");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "property_class", 
                $id, 
                $not_found_errorhandling = [
                    "message" => Response::CLASS_NOT_FOUND, 
                    "response_code" => "CLASS_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::CLASS_HAS_USERS,
                    "response_code" => "CLASS_HAS_USERS"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "user", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::CLASS_NOT_FOUND, 
                "response_code" => "CLASS_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::CLASS_HAS_USERS, 
                "response_code" => "CLASS_HAS_USERS"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/device/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_device");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "devices", 
                $id, 
                $not_found_errorhandling = [
                    "message" => Response::DEVICE_NOT_FOUND, 
                    "response_code" => "DEVICE_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::DEVICE_HAS_ACTIVE_BOOKING,
                    "response_code" => "DEVICE_HAS_ACTIVE_BOOKING"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "devices", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::DEVICE_NOT_FOUND, 
                "response_code" => "DEVICE_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::DEVICE_HAS_ACTIVE_BOOKING, 
                "response_code" => "DEVICE_HAS_ACTIVE_BOOKING"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/device/type/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_device_type");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "property_device_type", 
                $id, 
                $not_found_errorhandling = [
                    "message" => Response::DEVICE_TYPE_NOT_FOUND, 
                    "response_code" => "DEVICE_TYPE_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::DEVICE_TYPE_HAS_DEVICES,
                    "response_code" => "DEVICE_TYPE_HAS_DEVICES"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "property_device_type", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::DEVICE_TYPE_NOT_FOUND,
                "response_code" => "DEVICE_TYPE_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::DEVICE_TYPE_HAS_DEVICES,
                "response_code" => "DEVICE_TYPE_HAS_DEVICES"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/usercard/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_usercard");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "usercard", 
                $id,
                $not_found_errorhandling = [
                    "message" => Response::USERCARD_NOT_FOUND, 
                    "response_code" => "USERCARD_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::USERCARD_HAS_USER,
                    "response_code" => "USERCARD_HAS_USER"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "usercard", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::USERCARD_NOT_FOUND,
                "response_code" => "USERCARD_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::USERCARD_HAS_USER,
                "response_code" => "USERCARD_HAS_USER"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/usercard/type/delete', function () {
    require "classes/delete_class.php";
    authorize("delete_usercard_type");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::delete(
                "property_usercard_type", 
                $id, 
                $not_found_errorhandling = [
                    "message" => Response::USERCARD_TYPE_NOT_FOUND, 
                    "response_code" => "USERCARD_TYPE_NOT_FOUND"
                ],
                $foreign_key_errorhandling = [
                    "message" => Response::USERCARD_TYPE_HAS_USERCARDS,
                    "response_code" => "USERCARD_TYPE_HAS_USERCARDS"
                ]
            );
        }
    }
    else {
        Delete::delete(
            "property_usercard_type", 
            $data["id"], 
            $not_found_errorhandling = [
                "message" => Response::USERCARD_TYPE_NOT_FOUND,
                "response_code" => "USERCARD_TYPE_NOT_FOUND"
            ],
            $foreign_key_errorhandling = [
                "message" => Response::USERCARD_TYPE_HAS_USERCARDS,
                "response_code" => "USERCARD_TYPE_HAS_USERCARDS"
            ]
        );
    
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

$router->delete('/token/delete', function () {
    require "classes/delete_class.php";
    $token = authorize("delete_token");

    $data = getData("POST", ["id"]);

    
    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            Delete::deleteToken($id, $token);
        }
    }
    else {
        Delete::deleteToken($data["id"], $token);
    }
    
    Response::success(Response::SUCCESS, "SUCCESS");
});

// Client side routes
$router->post('/booking', function () {
    require 'classes/booking_class.php';
    authorize("book");

    $data = getData("POST", ["uid_1"]);
    $uid_2 = (isset($data["uid_2"])) ? $data["uid_2"] : null;

    $booking = new Booking($data["uid_1"], $uid_2);
    $response_code = $booking->execute();
    $response["data"] = $booking->fetchUserData();

    Response::success(Response::getValue($response_code), $response_code, $response);
});

$router->post('/token/validate', function () {
    require 'classes/token_class.php';
    if (!isset($_SERVER["HTTP_AUTHORIZATION"]))
        throw new CustomException(Response::REQUIRED_DATA_MISSING . " (HTTP_AUTHORIZATION)", "REQUIRED_DATA_MISSING", 400);
    $given_token = $_SERVER["HTTP_AUTHORIZATION"];
    $jwt = explode(" ", $given_token)[1];
    
    $permissions["permissions"] = Token::validateToken($jwt, $_ENV["JWT_KEY"]);
    
    Response::success(Response::SUCCESS . ": Token ist valide", "SUCCESS", $permissions);
});

// Run the router
$router->run();