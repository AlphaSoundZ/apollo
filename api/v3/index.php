<?php
require_once 'config.php';
require 'vendor/autoload.php';

$router = new \Bramus\Router\Router;

$router->set404('/', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['response'] = "404";
    $jsonArray['message'] = "route not defined";

    echo json_encode($jsonArray);
});

$router->get('/status', function () {
    require 'status.php';
});

$router->get('/user(/\d+)?', function($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["response"] = "";
    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null) // search for user with $id
    {
        $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["user_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Benutzer nicht gefunden";
    }
    else if (isset($booking) && $booking == "true") // show all booking users
    {
        $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "devices", "join" => ["user.user_usercard_id", "devices.device_id"]], ["table" => "event", "join" => ["user.user_id", "event.event_user_id"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name", "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) AS amount"], ["page" => $page, "size" => $size, "groupby" => "user.user_id", "having" => "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) > 0"]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Es wird zurzeit nichts ausgeliehen";
    }
    else // show all users or search for user using ?query=
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["query"] = $query;
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
    echo json_encode($response); // return the response
});

$router->get('/user(/\d+)/history', function($id) {
    require 'classes/search_class.php';
    authorize("search");

    $response["response"] = "";
    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["data"] = Select::search([["table" => "event"]], ["event.event_user_id", "event.event_begin", "event.event_end", "event.event_multi_booking_id"], ["event_user_id"], $id, ["page" => $page, "size" => $size, "strict" => true, "groupby" => "event.event_multi_booking_id", "orderby" => "event.event_multi_booking_id", "direction" => "DESC"]);
    if (!$response["data"])
    {
        $response["message"] = "Keine Events gefunden";
        echo json_encode($response);
        return;
    }
    $response["message"] = "Events zu diesem Benutzer gefunden";

    echo json_encode($response);
});

$router->get('/device(/[^/]+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";
    $response["response"] = "";
    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    if ($id !== null) // search for device with $id or uid
    {
        $response["data"] = Select::search([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["device_id", "device_uid"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Gerät gefunden" : "Gerät nicht gefunden";
    }
    else if ($booking == "true") // show all booked devices - DOES NOT WORK
    {
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]], ["table" => "user", "join" => ["user.user_id", "devices.device_lend_user_id"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name", "user.user_id", "user.user_firstname", "user.user_lastname"], ["page" => $page, "size" => $size, "groupby" => "devices.device_id"]);
        $response["message"] = ($response["data"]) ? "Alle gebuchten Geräte" : "Es werden derzeit keine Geräte gebucht";
    }
    else // show every device
    {
        $response["message"] = "Alle Geräte";
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["page" => $page, "size" => $size]);
    }
    echo json_encode($response); // return the response
});

$router->get('/booking/history(/\d+)?', function ($id = null) { // Device Type fehlt
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";
    $response["response"] = "";

    if ($id !== null) // search for booking with $id
    {
        $response["data"] = Select::search([["table" => "event"], ["table" => "user", "join" => ["user.user_id", "event.event_user_id"]], ["table" => "devices", "join" => ["devices.device_id", "event.event_device_id"]], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["event_id", "event_begin", "event_end", "user.user_id", "user.user_firstname", "user.user_lastname", "devices.device_id", "devices.device_uid", "property_device_type.device_tye_name"], ["user_id"], $id, ["page" => $page, "size" => $size, "strict" => true]);
        $response["message"] = ($response["data"]) ? "Buchung gefunden" : "Buchung mit der ID nicht gefunden";
    }
    else // show all bookings
    {
        $response["message"] = "Alle Buchungen";
        $response["data"] = Select::select([["table" => "event"], ["table" => "user", "join" => ["user.user_id", "event.event_user_id"]], ["table" => "devices", "join" => ["devices.device_id", "event.event_device_id"]]], ["event_id", "event_begin", "event_end", "user.user_id", "user.user_firstname", "user.user_lastname", "devices.device_id", "devices.device_uid"], ["page" => $page, "size" => $size]);
    }
    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

$router->run();