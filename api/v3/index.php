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

    if ($id !== null)
    {
        $response["data"] = Select::strictSearch("user", "user_id", $id);
        if (isset($response["data"]))
            $response["message"] = "Benutzer gefunden";
        else
            $response["message"] = "Benutzer nicht gefunden";
    }
    else
    {
        $limit = (isset($_GET["limit"]) && $_GET["limit"] > 0) ? $_GET["limit"] : 0;
        $page = ($limit !== 0 && !isset($_GET["page"])) ? 0 : null;
        $page = ($limit !== 0 && isset($_GET["page"])) ? $_GET["page"] : $page;
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"])) ? $_GET["strict"] : null;

        if ($query)
        {
            if ($strict == "true")
            {
                $response["message"] = "Suche erfolgreich";
                $response["query"] = $query;
                $response["data"] = Select::strictSearch([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], $query, $limit);
            }
            else
            {
                $response["message"] = "Suche erfolgreich";
                $response["query"] = $query;
                $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], $query, $limit);
            }
        }
        else
        {
            $response["message"] = "Alle Benutzer";
            $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $limit]);
        }
    }
    
    // echo json_encode($response, JSON_PRETTY_PRINT); // return the response
    echo json_encode($response); // return the response\
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