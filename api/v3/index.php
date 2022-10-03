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

$router->get('/user/select/{column}/([^/]+)(/\d+)?', function ($column, $param, $limit = 0) {
    require 'classes/search_class.php';
    $response["message"] = "";
    $response["response"] = "";
    $response["search"] = $param;

    switch ($column)
    {
        case "name":
            $search_by = ["user_firstname", "user_lastname"];
            break;
        case "id":
            $search_by = ["user_id"];
            break;
        default:
            $search_by = ["user_id", "user_firstname", "user_lastname"];
            break;
    }

    $table = new table();
    $selectedTable = $table->selectTable([["table" => "user"]], ["*"]);
    // $limit = (isset($data["search"]["limit"])) ? $data["search"]["limit"] : "";
    $response["data"] = $table->search($param, $selectedTable, $search_by);
    // reduce the array to only the first 10 results
    if ($limit !== 0)
        $response["data"] = array_slice($response["data"], 0, $limit);
    
    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

$router->run();