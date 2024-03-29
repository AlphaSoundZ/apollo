<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

$router = new \Bramus\Router\Router;

$router->set404('/', function () {
    Response::error(Response::ROUTE_NOT_DEFINED);
});

// Home route
$router->get('/', function () {
    Response::success(Response::API_RUNNING);
});

// ####################
// #### USER ROUTES ###

$router->get('/status', function () {
    require_once 'controlpanel/status.php';
});

// ####################
// #### API ROUTES ####

// GET
$router->get('/user/class(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_user_class");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "class_id",
        "name" => "class_name",
        "amount" => "amount",
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $path = array(
        "id" => [
            "id",
        ],
        "query" => [
            "name",
        ]
    );

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    if ($id) // search for class with $id
    {
        $response = Data::search(
            [
                ["table" => "property_class"],
                ["table" => "user", "join" => ["property_class.class_id", "user.user_class"]],
            ],
            ["COUNT(user.user_id) AS amount, property_class.*"],
            $response_structure,
            $path["id"],
            $id,
            [
                "strict" => true,
                "page" => $page,
                "size" => $size,
                "order_by" => $order_by,
                "order_strategy" => $order_strategy,
                "groupby" => "property_class.class_id",
            ]
        );
        $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
    } else {

        if ($query) // search for class with query
        {
            $response = Data::search(
                [
                    ["table" => "property_class"],
                    ["table" => "user", "join" => ["property_class.class_id", "user.user_class"]],
                ],
                ["COUNT(user.user_id) AS amount, property_class.*"],
                $response_structure,
                $path["query"],
                $query,
                [
                    "strict" => $strict,
                    "page" => $page,
                    "size" => $size,
                    "order_by" => $order_by,
                    "order_strategy" => $order_strategy,
                    "groupby" => "property_class.class_id",
                ]
            );
            $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
        } else // get all classes
        {
            $response = Data::select(
                [
                    ["table" => "property_class"],
                    ["table" => "user", "join" => ["property_class.class_id", "user.user_class"]],
                ],
                ["COUNT(user.user_id) AS amount, property_class.*"],
                $response_structure,
                [
                    "page" => $page,
                    "size" => $size,
                    "order_by" => $order_by,
                    "order_strategy" => $order_strategy,
                    "groupby" => "property_class.class_id",
                ]
            );
            $response["message"] = "Alle Klassen";
        }
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/user(/[^/]+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_user");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "user_id",
        "firstname" => "user_firstname",
        "lastname" => "user_lastname",
        "class" => [
            "id" => "class_id",
            "name" => "class_name",
        ],
        "token" => [
            "id" => "token_id",
            "username" => "token_username",
            "last_change" => "token_last_change",
        ],
        "usercard_id" => "user_usercard_id",
        "multi_booking" => "multi_booking"
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $response_structure_booking = $response_structure;
    $response_structure_booking["devices"]["amount"] = "amount";


    $path = array(
        "id" => [
            "id",
        ],
        "query" => [
            "id",
            "firstname",
            "lastname",
            "class" => [
                "name",
            ],
        ],
    );

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    if ($id !== null) // search for user with $id
    {
        $response = Data::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "token", "join" => ["token.token_user_id", "user.user_id"]]], ["*"], $response_structure, $path["id"], $id, ["strict" => true, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Benutzer nicht gefunden";
    } else if (isset($booking) && $booking == "true") // show all booking users
    {
        $response = Data::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "event", "join" => ["user.user_id", "event.event_user_id"]], ["table" => "token", "join" => ["token.token_user_id", "user.user_id"]]], ["user_id", "user_firstname", "user_lastname", "class_name", "class_id", "multi_booking", "user_usercard_id", "token_id", "token_username", "token_last_change", "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) AS amount"], $response_structure_booking, ["page" => $page, "size" => $size, "groupby" => "user.user_id", "having" => "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) > 0", "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Es wird zurzeit nichts ausgeliehen";
    } else // show all users or search for user using ?query=
    {
        if ($query) {
            $response = Data::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "token", "join" => ["token.token_user_id", "user.user_id"]]], ["user_id", "user_firstname", "user_lastname", "class_name", "class_id", "multi_booking", "user_usercard_id", "token_id", "token_username", "token_last_change"], $response_structure, $path["query"], $query, ["page" => $page, "size" => $size, "strict" => $strict, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        } else {
            $response = Data::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "token", "join" => ["token.token_user_id", "user.user_id"]]], ["user_id", "user_firstname", "user_lastname", "class_name", "class_id", "multi_booking", "user_usercard_id", "token_id", "token_username", "token_last_change"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = "Alle Benutzer";
        }
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/user(/\d+)/history', function ($id) {
    require_once 'classes/search.class.php';
    authorize("CRUD_user");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : "duration.begin";

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : "DESC";

    $response_structure = array(
        "duration" => [
            "begin" => "event_begin",
            "end" => "event_end",
        ],
        "multi_booking_id" => "event_multi_booking_id",
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    // check if user exists
    $user = Data::select([["table" => "user"]], ["user_id"], ["user_id"], ["where" => "user_id = " . $id]);
    if (!$user["data"]) {
        Response::error(Response::USER_NOT_FOUND);
    }

    $response = Data::select([["table" => "event"]], ["event.event_begin", "event.event_end", "event.event_multi_booking_id"], $response_structure, ["page" => $page, "size" => $size, "strict" => true, "order_by" => $order_by, "order_strategy" => $order_strategy, "where" => "event.event_user_id = " . $id]);

    // Check if bookings were found
    if ($response["data"]) {
        $response["message"] = "Buchungen zu diesem Benutzer gefunden";
    } else {
        // No bookings found
        $response["message"] = "Keine Buchungen zu diesem Benutzer gefunden";
    }

    // Group bookings by multi_booking_id
    $grouped = array();
    foreach ($response["data"] as $booking) {
        $grouped[$booking["multi_booking_id"]][] = $booking;
    }

    // remove multi_booking_id from data
    foreach ($grouped as $key => $booking) {
        foreach ($booking as $key2 => $value) {
            unset($grouped[$key][$key2]["multi_booking_id"]);
        }
    }

    // Set response data
    $results["page"] = $response["page"];
    $results["search"]["id"] = $id;
    $results["data"] = $grouped;


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/device/type(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_device_type");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "device_type_id",
        "name" => "device_type_name",
        "amount" => "amount",
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    if ($id) // search for class with $id
    {
        $response = Data::select([["table" => "property_device_type"], ["table" => "devices", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["COUNT(devices.device_id) AS amount, property_device_type.*"], $response_structure, ["strict" => true, "where" => "device_type_id = " . $id, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy, "groupby" => "property_device_type.device_type_id"]);
        $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
    } else {

        if ($query) // search for class with $query
        {
            $response = Data::search([["table" => "property_device_type"], ["table" => "devices", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["COUNT(devices.device_id) AS amount, property_device_type.*"], $response_structure, ["name"], $query, ["strict" => $strict, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy, "groupby" => "property_device_type.device_type_id"]);
            $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
        } else // show all classes
        {
            $response = Data::select([["table" => "property_device_type"], ["table" => "devices", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["COUNT(devices.device_id) AS amount, property_device_type.*"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy, "groupby" => "property_device_type.device_type_id"]);
            $response["message"] = "Alle Gerätetypen";
        }
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/device(/[^/]+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_device");

    if ($id == null) {
        $id = (isset($_GET["query"])) ? $_GET["query"] : null;
    }

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $response_structure = array(
        "id" => "device_id",
        "uid" => "device_uid",
        "type" => [
            "id" => "device_type_id",
            "name" => "device_type_name",
        ],
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $response_structure_booking = array(
        "id" => "device_id",
        "uid" => "device_uid",
        "type" => [
            "id" => "device_type_id",
            "name" => "device_type_name",
        ],
        "booking" => [
            "user" => [
                "id" => "user_id",
                "firstname" => "user_firstname",
                "lastname" => "user_lastname",
            ],
            "begin" => "event_begin",
            "multi_booking_id" => "event_multi_booking_id"
        ]
    );

    if ($id !== null) // search for device with $id or uid
    {
        $response = Data::search([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], $response_structure, ["id", "uid"], $id, ["strict" => true, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Gerät gefunden" : "Gerät nicht gefunden";
    } else if ($booking == "true") // show all booked devices
    {
        $response = Data::select([["table" => "event"], ["table" => "devices", "join" => ["devices.device_id", "event.event_device_id"]], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]], ["table" => "user", "join" => ["user.user_id", "event.event_user_id"]]], ["event.event_begin", "event.event_multi_booking_id", "devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name", "user.user_id", "user.user_firstname", "user.user_lastname"], $response_structure_booking, ["page" => $page, "size" => $size, "where" => "event.event_end IS NULL", "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Alle gebuchten Geräte" : "Es werden derzeit keine Geräte gebucht";
    } else // show every device
    {
        $response = Data::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = "Alle Geräte";
    }
    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/device(/[^/]+)/history', function ($id) {
    require_once 'classes/search.class.php';
    authorize("CRUD_device");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response["message"] = "";

    $response_structure = array(
        "user" => [
            "user_id" => "user_id",
            "firstname" => "user_firstname",
            "lastname" => "user_lastname",
        ],
        "duration" => [
            "begin" => "event_begin",
            "end" => "event_end",
        ],
        "multi_booking_id" => "event_multi_booking_id",
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    // check if device exists
    $device = Data::select([["table" => "devices"]], ["device_id", "device_uid"], ["device_id" => "device_id"], ["strict" => true, "where" => "device_id = " . $id, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
    if (!$device["data"]) {
        // device not found
        Response::error(Response::DEVICE_NOT_FOUND);
    }

    // if $id is an $uid
    $id = $device["data"][0]["device_id"];


    $response = Data::select([["table" => "event"], ["table" => "user", "join" => ["event.event_user_id", "user.user_id"]]], ["event.*", "user.*"], $response_structure, ["page" => $page, "size" => $size, "strict" => true, "where" => "event.event_device_id = " . $id, "order_by" => $order_by, "order_strategy" => $order_strategy]);
    if ($response["data"]) {
        $response["message"] = "Buchungen zu diesem Device gefunden";
    } else {
        // No bookings found
        $response["message"] = "Keine Buchungen zu diesem Device gefunden";
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    $results["data"] = $response["data"];

    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/usercard/type(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_usercard_type");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "usercard_type_id",
        "name" => "usercard_type_name",
    );

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    if ($id) // search for usercard type with $id
    {
        $response = Data::select([["table" => "property_usercard_type"]], ["*"], $response_structure, ["strict" => true, "where" => "usercard_type_id = " . $id, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    } else {
        if ($query) // search for usercard type with $query
        {
            $response = Data::search([["table" => "property_usercard_type"]], ["*"], $response_structure, ["name"], $query, ["strict" => $strict, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
        } else // show every usercard type
        {
            $response = Data::select([["table" => "property_usercard_type"]], ["*"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = "Alle User Indentifikationen";
        }
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/usercard(/[^/]+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_usercard");

    if ($id == null) {
        $id = (isset($_GET["query"])) ? $_GET["query"] : null;
    }

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "usercard_id",
        "uid" => "usercard_uid",
        "type" => [
            "id" => "usercard_type_id",
            "name" => "usercard_type_name",
        ],
        "user" => [
            "id" => "user_id",
            "firstname" => "user_firstname",
            "lastname" => "user_lastname",
        ],
    );

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    if ($id) // search for usercard with $id
    {
        $response = Data::search([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]]], ["usercard.*", "property_usercard_type.*", "user.*"], $response_structure, ["id", "uid"], $id, ["strict" => true, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    } else // show every usercard
    {
        $response = Data::select([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]]], ["usercard.*", "property_usercard_type.*", "user.*"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = "Alle User Indentifikationen";
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/token(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_token");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "token_id" => "token_id",
        "username" => "token_username",
        "permission_id" => "permission_id",
        "permission_text" => "permission_text",
        "last_change" => "token_last_change",
        "user" => [
            "id" => "user_id",
            "firstname" => "user_firstname",
            "lastname" => "user_lastname",
            "class" => [
                "id" => "class_id",
                "name" => "class_name",
            ]
        ]
    );

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    if ($id !== null) // search for user with $id
    {
        $response = Data::select([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_id", "token.token_user_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], $response_structure, ["strict" => true, "groupby" => "token.token_id", "where" => "token.token_id = " . $id, "page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Token gefunden" : "Token nicht gefunden";

        if ($response["data"]) {
            $response["data"][0]["permission_id"] = explode(", ", $response["data"][0]["permission_id"]);
            $response["data"][0]["permission_text"] = explode(", ", $response["data"][0]["permission_text"]);
        }
    } else // show all users or search for user using ?query=
    {

        if ($query) {
            $response["query"] = $query;
            $response = Data::search([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_id", "token.token_user_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], $response_structure, ["username"], $query, ["strict" => $strict, "page" => $page, "size" => $size, "groupby" => "token.token_id", "order_by" => $order_by, "order_strategy" => $order_strategy]);

            for ($i = 0; $i < count($response["data"]); $i++) {
                $response["data"][$i]["data"]["permission_id"] = explode(", ", $response["data"][$i]["data"]["permission_id"]);
                $response["data"][$i]["data"]["permission_text"] = explode(", ", $response["data"][$i]["data"]["permission_text"]);
            }
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        } else {
            $response = Data::select([["table" => "token"], ["table" => "token_link_permissions", "join" => ["token_link_permissions.link_token_id", "token.token_id"]], ["table" => "property_token_permissions", "join" => ["property_token_permissions.permission_id", "token_link_permissions.link_token_permission_id"]], ["table" => "user", "join" => ["user.user_id", "token.token_user_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "GROUP_CONCAT(token_link_permissions.link_token_permission_id SEPARATOR ', ') AS permission_id", "GROUP_CONCAT(property_token_permissions.permission_text SEPARATOR ', ') AS permission_text", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], $response_structure, ["strict" => true, "page" => $page, "size" => $size, "groupby" => "token.token_id", "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = "Alle Tokens";

            for ($i = 0; $i < count($response["data"]); $i++) {
                $response["data"][$i]["permission_id"] = explode(", ", $response["data"][$i]["permission_id"]);
                $response["data"][$i]["permission_text"] = explode(", ", $response["data"][$i]["permission_text"]);
            }
        }
    }


    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/token/permission(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_token");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;

    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "permission_id",
        "name" => "permission_text"
    );

    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    if ($id !== null) {
        $response = Data::select([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], $response_structure, ["page" => $page, "size" => $size, "strict" => true, "where" => "permission_id = " . $id, "order_by" => $order_by, "order_strategy" => $order_strategy]);
        $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
    } else {

        if ($query) {
            $response = Data::search([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], $response_structure, ["name"], $query, ["page" => $page, "size" => $size, "strict" => $strict, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        } else {
            $response = Data::select([["table" => "property_token_permissions"]], ["permission_id", "permission_text"], $response_structure, ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy]);
            $response["message"] = "Alle Token Permissions";
        }
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    if ($query !== null) {
        $results["search"]["strict"] = $strict;
        $results["search"]["query"] = $query;
    }
    $results["data"] = $response["data"];


    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), $results);
});

$router->get('/prebook(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    $token = authorize("prebook", function () {
        return authorize("CRUD_prebook");
    });

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : null;
    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : null;

    $response_structure = array(
        "id" => "prebook_id",
        "begin" => "prebook_begin",
        "end" => "prebook_end",
        "amount" => "prebook_amount",
        "user_id" => "prebook_user_id",
    );

    $table = [
        ["table" => "prebook"],
    ];

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $options = ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy];

    if ($id) {
        $response = Data::select($table, ["*"], $response_structure, array_merge($options, ["where" => "user_id = " . $id]));
    } else {
        // everything which ends in the future
        $response = Data::select($table, ["*"], $response_structure, array_merge($options, ["where" => "prebook_end >= NOW()"]));
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    $results["data"] = $response["data"];


    Response::success(Response::SUCCESS, $results);
});

$router->get('event(/\d+)?', function ($id = null) {
    require_once 'classes/search.class.php';
    authorize("CRUD_event", function () {
        return authorize("book");
    });

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    $order_by = (isset($_GET["order_by"])) ? $_GET["order_by"] : "multi_booking_id";
    $order_strategy = (isset($_GET["order_strategy"])) ? $_GET["order_strategy"] : "DESC";

    $response_structure = array(
        "id" => "event_id",
        "user_id" => "event_user_id",
        "begin" => "event_begin",
        "end" => "event_end",
        "multi_booking_id" => "event_multi_booking_id",
        "device_id" => "event_device_id",
    );

    $table = [
        ["table" => "event"],
    ];

    $results = array();
    if ($order_by !== null && $order_strategy !== null) {
        $results["order"]["by"] = $order_by;
        $results["order"]["strategy"] = $order_strategy;

        $order_by = explode(".", $order_by);
        $order_by = Data::getValueOfStructure($response_structure, $order_by);
    }

    $options = ["page" => $page, "size" => $size, "order_by" => $order_by, "order_strategy" => $order_strategy];

    if ($id) {
        $response = Data::select($table, ["*"], $response_structure, array_merge($options, ["where" => "event_multi_booking_id = " . $id]));
    } else {
        $response = Data::select($table, ["*"], $response_structure, $options);
    }

    $results["page"] = $response["page"];
    if ($id !== null) {
        $results["search"]["id"] = $id;
    }
    $results["data"] = $response["data"];


    Response::success(Response::SUCCESS, $results);
});

$router->get('/booking/available', function () {
    require_once 'classes/booking.class.php';
    // authorize();

    $data = getData("GET");

    $result = Booking::availableDevicesForBooking();

    Response::success(Response::SUCCESS, ["data" => [["amount" => $result]]]);
});

// POST
$router->post('/csv/import', function () {
    require_once 'classes/csv.class.php';
    authorize("add_csv");

    $response["message"] = "";

    $data = getData("POST", ["table", "columns", "string", "seperator", "linebreak"]);
    $global = (isset($data["global"])) ? $data["global"] : [];
    $enclosure = (isset($data["enclosure"])) ? $data["enclosure"] : "";

    $csv = new Csv($data["table"], $data["columns"], $data["string"], $data["seperator"], $data["linebreak"], $global, $enclosure);
    $csv->checkForError();
    $csv->add();

    $total_rows = count($csv->rows);
    $response["message"] = count($csv->rows) . " Zeilen wurden eingefügt";

    Response::success(array_merge(Response::SUCCESS, ["message" => $response["message"]]), ["amount" => $total_rows]);
});

$router->post('/token/authorize', function () {
    require_once 'classes/token.class.php';
    require_once 'classes/search.class.php';

    $data = getData("POST", ["username", "password"]);

    $username = $data["username"];
    $password = $data["password"];

    $token = Token::getToken($username, $password, $_ENV["JWT_KEY"]);
    $token_id = $token["token_id"];

    // Get user
    $user_raw = Data::select(
        [["table" => "token"], ["table" => "user", "join" => ["user.user_id", "token.token_user_id"]], ["table" => "usercard", "join" => ["usercard.usercard_id", "user.user_usercard_id"]]],
        ["*"],
        ["id" => "user_id", "firstname" => "user_firstname", "lastname" => "user_lastname", "username" => "token_username", "class_id" => "user_class", "usercard" => ["id" => "usercard_id", "uid" => "usercard_uid"]],
        ["where" => "token_id = '" . $token_id . "'"]
    );
    $user = $user_raw["data"][0];

    Response::success(Response::SUCCESS, array_merge($token, ["user" => $user]));
});

$router->post('/user/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_user");

    $data = getData("POST", ["firstname", "lastname", "class_id"]);
    $usercard_id = (isset($data["usercard_id"])) ? $data["usercard_id"] : null;
    $ignore_duplicates = (isset($data["ignore_duplicates"]) && $data["ignore_duplicates"] == true) ? true : false;

    $id = Create::user($data["firstname"], $data["lastname"], $data["class_id"], $usercard_id, $ignore_duplicates);

    Response::success(Response::SUCCESS, ["user_id" => $id]);
});

$router->post('/usercard/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_usercard");

    $data = getData("POST", ["uid", "type"]);

    $allow_reassigning = (isset($data["allow_reassigning"]) && $data["allow_reassigning"] == true) ? true : false;
    $user_id = (isset($data["user_id"]) && $data["user_id"] == true) ? $data["user_id"] : null;

    $id = Create::usercard($data["uid"], $data["type"], $user_id, $allow_reassigning);

    Response::success(Response::SUCCESS, ["usercard_id" => $id]);
});

$router->post('/device/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_device");

    $data = getData("POST", ["uid", "type"]);

    $id = Create::device($data["uid"], $data["type"]);

    Response::success(Response::SUCCESS, ["device_id" => $id]);
});

$router->post('/user/class/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_user_class");

    $data = getData("POST", ["name", "multi_booking"]);

    $id = Create::property_class($data["name"], $data["multi_booking"]);

    Response::success(Response::SUCCESS, ["class_id" => $id]);
});

$router->post('/device/type/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_device_type");

    $data = getData("POST", ["name"]);

    $id = Create::property_device_type($data["name"]);

    Response::success(Response::SUCCESS, ["device_type_id" => $id]);
});

$router->post('/usercard/type/create', function () {
    require_once "classes/create.class.php";
    authorize("CRUD_usercard_type");

    $data = getData("POST", ["name"]);

    $id = Create::property_usercard_type($data["name"]);

    Response::success(Response::SUCCESS, ["usercard_type_id" => $id]);
});

$router->post('/token/create', function () {
    require_once 'classes/create.class.php';
    authorize("CRUD_token");

    $data = getData("POST", ["username", "password", "user_id"], ["permissions"]); // permissions as optional so it could be empty if the token should have no permissions

    $id = Create::token($data["username"], $data["password"], $data["permissions"], $data["user_id"]);

    Response::success(Response::SUCCESS, ["token_id" => $id]);
});

$router->post('/prebook/create', function () {
    require_once 'classes/prebook.class.php';
    $token = authorize("prebook", function () {
        return authorize("CRUD_prebook");
    });

    $data = getData("POST", ["amount", "begin", "end"], ["user_id"]);

    $user_id = (isset($data["user_id"])) ? $data["user_id"] : null;

    if ($user_id)
        $id = Prebook::create($data["amount"], $data["begin"], $data["end"], $data["user_id"], $token["user_id"]);
    else
        $id = Prebook::create($data["amount"], $data["begin"], $data["end"], null, $token["user_id"]);

    Response::success(Response::SUCCESS, ["prebook_id" => $id]);
});

// PATCH
$router->patch('/user/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_user");

    $data = getData("POST", ["id"]);

    DataUpdate::update(
        "user",
        $data["id"],
        $updating_values = [
            "user_firstname" => $data["values"]["firstname"] ?? null,
            "user_lastname" => $data["values"]["lastname"] ?? null,
            "user_class" => $data["values"]["class_id"] ?? null,
            "user_usercard_id" => $data["values"]["usercard_id"] ?? null
        ],
        $duplicate_errorhandling = Response::USER_ALREADY_EXISTS,
        $not_found_errorhandling = Response::USER_NOT_FOUND,
        $changeable_columns = [
            "user_firstname",
            "user_lastname",
            "user_class",
            "user_usercard_id"
        ]
    );

    Response::success();
});

$router->patch('/user/class/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_user_class");

    $data = getData("POST", ["id"]);

    DataUpdate::update(
        "property_class",
        $data["id"],
        $updating_values = [
            "class_name" => $data["values"]["name"] ?? null,
            "multi_booking" => $data["values"]["multi_booking"] ?? null
        ],
        $duplicate_errorhandling = Response::CLASS_ALREADY_EXISTS,
        $not_found_errorhandling = Response::CLASS_NOT_FOUND,
        $changeable_columns = [
            "class_name",
            "multi_booking"
        ]
    );

    Response::success();
});

$router->patch('/device/type/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_device_type");

    $data = getData("POST", ["id", "name"]);

    DataUpdate::update(
        "property_device_type",
        $data["id"],
        $updating_values = [
            "device_type_name" => $data["values"]["name"]
        ],
        $duplicate_errorhandling = Response::DEVICE_TYPE_ALREADY_EXISTS,
        $not_found_errorhandling = Response::DEVICE_TYPE_NOT_FOUND,
        $changeable_columns = [
            "device_type_name"
        ]
    );

    Response::success();
});

$router->patch('/usercard/type/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_usercard_type");

    $data = getData("POST", ["id", "name"]);

    DataUpdate::update(
        "property_usercard_type",
        $data["id"],
        $updating_values = [
            "usercard_type_name" => $data["values"]["name"]
        ],
        $duplicate_errorhandling = Response::USERCARD_TYPE_ALREADY_EXISTS,
        $not_found_errorhandling = Response::USERCARD_TYPE_NOT_FOUND,
        $changeable_columns = [
            "usercard_type_name"
        ]
    );

    Response::success();
});

$router->patch('/device/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_device");

    $data = getData("POST", ["id"]);

    DataUpdate::update(
        "devices",
        $data["id"],
        $updating_values = [
            "device_uid" => $data["values"]["uid"] ?? null,
            "device_type" => $data["values"]["type"] ?? null
        ],
        $duplicate_errorhandling = Response::DEVICE_ALREADY_EXISTS,
        $not_found_errorhandling = Response::DEVICE_NOT_FOUND,
        $changeable_columns = [
            "device_uid",
            "device_type"
        ]
    );

    Response::success();
});

$router->patch('/usercard/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_usercard");

    $data = getData("POST", ["id"]);

    DataUpdate::update(
        "usercard",
        $data["id"],
        $updating_values = [
            "usercard_uid" => $data["values"]["uid"] ?? null,
            "usercard_type" => $data["values"]["type"] ?? null
        ],
        $duplicate_errorhandling = Response::USERCARD_ALREADY_EXISTS,
        $not_found_errorhandling = Response::USERCARD_NOT_FOUND,
        $changeable_columns = [
            "usercard_uid",
            "usercard_type"
        ]
    );

    Response::success();
});

$router->patch('/token/change', function () {
    require_once "classes/update.class.php";
    authorize("CRUD_token");

    $data = getData("POST", ["id"]);

    DataUpdate::token(
        $data["id"],
        [
            "token_username" => $data["values"]["username"] ?? null,
            "token_password" => $data["values"]["password"] ?? null,
            "token_user_id" => $data["values"]["user_id"] ?? null,
            "permissions" => $data["values"]["permissions"] ?? null,
        ]
    );

    Response::success();
});

// DELETE
$router->delete('/user/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_user");

    // get data from request (id can be either an array of id's or a single id)
    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "user",
                $id,
                $not_found_errorhandling = Response::USER_NOT_FOUND,
                $foreign_key_errorhandling = Response::USER_HAS_BOOKINGS,
            );
        }
    } else {
        DataDelete::delete(
            "user",
            $data["id"],
            $not_found_errorhandling = Response::USER_NOT_FOUND,
            $foreign_key_errorhandling = Response::USER_HAS_BOOKINGS,
        );
    }

    Response::success();
});

$router->delete('/user/class/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_user_class");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "property_class",
                $id,
                $not_found_errorhandling = Response::CLASS_NOT_FOUND,
                $foreign_key_errorhandling = Response::CLASS_HAS_USERS,
            );
        }
    } else {
        DataDelete::delete(
            "user",
            $data["id"],
            $not_found_errorhandling = Response::CLASS_NOT_FOUND,
            $foreign_key_errorhandling = Response::CLASS_HAS_USERS,
        );
    }

    Response::success();
});

$router->delete('/device/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_device");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "devices",
                $id,
                $not_found_errorhandling = Response::DEVICE_NOT_FOUND,
                $foreign_key_errorhandling = Response::DEVICE_HAS_ACTIVE_BOOKING,
            );
        }
    } else {
        DataDelete::delete(
            "devices",
            $data["id"],
            $not_found_errorhandling = Response::DEVICE_NOT_FOUND,
            $foreign_key_errorhandling = Response::DEVICE_HAS_ACTIVE_BOOKING,
        );
    }

    Response::success();
});

$router->delete('/device/type/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_device_type");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "property_device_type",
                $id,
                $not_found_errorhandling = Response::DEVICE_TYPE_NOT_FOUND,
                $foreign_key_errorhandling = Response::DEVICE_TYPE_HAS_DEVICES,
            );
        }
    } else {
        DataDelete::delete(
            "property_device_type",
            $data["id"],
            $not_found_errorhandling = Response::DEVICE_TYPE_NOT_FOUND,
            $foreign_key_errorhandling = Response::DEVICE_TYPE_HAS_DEVICES,
        );
    }

    Response::success();
});

$router->delete('/usercard/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_usercard");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "usercard",
                $id,
                $not_found_errorhandling = Response::USERCARD_NOT_FOUND,
                $foreign_key_errorhandling = Response::USERCARD_HAS_USER
            );
        }
    } else {
        DataDelete::delete(
            "usercard",
            $data["id"],
            $not_found_errorhandling = Response::USERCARD_NOT_FOUND,
            $foreign_key_errorhandling = Response::USERCARD_HAS_USER,
        );
    }

    Response::success();
});

$router->delete('/usercard/type/delete', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_usercard_type");

    $data = getData("POST", ["id"]);

    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::delete(
                "property_usercard_type",
                $id,
                $not_found_errorhandling = Response::USERCARD_TYPE_NOT_FOUND,
                $foreign_key_errorhandling = Response::USERCARD_TYPE_HAS_USERCARDS,
            );
        }
    } else {
        DataDelete::delete(
            "property_usercard_type",
            $data["id"],
            $not_found_errorhandling = Response::USERCARD_TYPE_NOT_FOUND,
            $foreign_key_errorhandling = Response::USERCARD_TYPE_HAS_USERCARDS,
        );
    }

    Response::success();
});

$router->delete('/token/delete', function () {
    require_once "classes/delete.class.php";
    $token = authorize("CRUD_token")["id"];

    $data = getData("POST", ["id"]);


    // when id is an array of id's
    if (is_array($data["id"])) {
        foreach ($data["id"] as $id) {
            DataDelete::deleteToken($id, $token);
        }
    } else {
        DataDelete::deleteToken($data["id"], $token);
    }

    Response::success();
});

$router->delete('/event/clear', function () {
    require_once "classes/delete.class.php";
    authorize("CRUD_event");

    $amount = DataDelete::clearEvent();

    Response::success(Response::SUCCESS, ["amount" => $amount]);
});

$router->delete('/user/event/clear', function () {
    require_once "classes/delete.class.php";
    authorize("delete_event");

    $data = getData("POST", ["id"]);

    $amount = DataDelete::clearUserEvent($data["id"]);

    Response::success(Response::SUCCESS, ["amount" => $amount]);
});

$router->delete('/prebook/delete', function () {
    require_once "classes/delete.class.php";
    $token = authorize("CRUD_prebook", function () {
        return authorize("prebook");
    });

    $data = getData("POST", ["id"]);

    DataDelete::deletePrebook($data["id"], $token["user_id"]);

    Response::success(Response::SUCCESS);
});

// Client side routes
$router->post('/booking', function () {
    require_once 'classes/booking.class.php';
    authorize("book");

    $data = getData("POST", ["uid_1"]);
    $uid_2 = (isset($data["uid_2"])) ? $data["uid_2"] : null;

    $booking = new Booking($data["uid_1"], $uid_2);
    $response_type = $booking->execute();
    $response["data"] = $booking->data;

    Response::success($response_type, $response);
});

$router->post('/token/validate', function () {
    require_once 'classes/token.class.php';

    if (!isset($_SERVER["HTTP_AUTHORIZATION"]))
        Response::error(Response::REQUIRED_DATA_MISSING, ["HTTP_AUTHORIZATION"]);
    $given_token = $_SERVER["HTTP_AUTHORIZATION"];
    $jwt = explode(" ", $given_token)[1];

    $permissions["permissions"] = Token::validateToken($jwt, $_ENV["JWT_KEY"])["permissions"];

    Response::success(array_merge(Response::SUCCESS, ["message" => "Token ist valide"]), $permissions);
});

$router->options('/{route:.*}/', function () {
    Response::success();
});

// Run the router
$router->run();
