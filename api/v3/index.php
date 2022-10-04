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

$router->get('/user', function () {
    require 'classes/search_class.php';
    // authorize("search");

    $limit = (isset($_GET["limit"]) && $_GET["limit"] > 0) ? $_GET["limit"] : 0;
    $page = ($limit !== 0 && !isset($_GET["page"])) ? 0 : null;
    $page = ($limit !== 0 && isset($_GET["page"])) ? $_GET["page"] : $page;
    $query = (isset($_GET["query"])) ? $_GET["query"] : null;
    
    $response["message"] = "";
    $response["response"] = "";

    if ($query)
    {
        $response["message"] = "Suche erfolgreich";
        $response["query"] = $query;
        $response["data"] = Select::search([["table" => "user"]], ["user_id", "user_firstname", "user_lastname"], $query, $limit);
    }
    else
    {
        $response["message"] = "Alle Benutzer";
        $response["data"] = Select::select([["table" => "user"]], ["user_id", "user_firstname"], ["page" => $page, "size" => $limit]);
    }

    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

$router->run();