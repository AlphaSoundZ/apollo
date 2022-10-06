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

// get all user or search for user using ?query=
$router->get('/user(/\d+)?', function($id = null) {
    require 'classes/search_class.php';
    // authorize("search");

    $response["response"] = "";
    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $limit = (isset($_GET["limit"]) && $_GET["limit"] > 0) ? $_GET["limit"] : 0;
    $page = ($limit !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null)
    {
        $response["data"] = Select::strictSearch("user", "user_id", $id);
        if (isset($response["data"]))
            $response["message"] = "Benutzer gefunden";
        else
            $response["message"] = "Benutzer nicht gefunden";
    }
    else if (isset($booking))
    {
        $response["data"] = Select::select([["table" => "user"], ["table" => "devices", "join" => ["user.user_usercard_id", "devices.device_id"]], ["table" => "event", "join" => ["user.user_id", "event.event_user_id"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end)"], ["orderby" => "user.lastname", "page" => $page, "size" => $limit, "groupby" => "user.user_id"]);
    }
    else
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["message"] = "Suche erfolgreich";
            $response["query"] = $query;

            $options = ["page" => $page, "size" => $limit, "strict" => $strict];
            
            $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], $query, $options);
            if ($response["data"] == null)
                $response["message"] = "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Benutzer";
            $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $limit]);
        }
    }
    
    // echo json_encode($response, JSON_PRETTY_PRINT); // return the response
    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

// get all devices or search for device using ?query=
$router->get('/device(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    // authorize("search");

    $response["message"] = "";
    $response["response"] = "";
    if ($id == null)
    {
        $response["message"] = "Alle Geräte";
        $response["data"] = Select::select([["table" => "devices"]], ["device_id"]);
    }
    else
    {
        $response["message"] = "Gerät gefunden";
        $response["data"] = Select::strictSearch("devices", "device_id", $id);
        if (isset($response["data"]))
            $response["message"] = "Gerät gefunden";
        else
            $response["message"] = "Gerät nicht gefunden";
    }
    echo json_encode($response); // return the response
});

$router->run();