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

$router->get('/user/select/{column}/{param}/{limit}', function ($column, $param, $limit = 0) {
    require 'classes/search_class.php';
    $response["message"] = "";
    $response["response"] = "";

    $table = new table();
    $selectedTable = $table->selectTable([["table" => "user"]], ["user_id", "user_firstname", "user_lastname"]);
    // $limit = (isset($data["search"]["limit"])) ? $data["search"]["limit"] : "";
    $response["data"] = $table->search($param, $selectedTable, ["user_id"]);

    // reduce the array to only the first 10 results
    if ($limit !== 0)
        $response["data"] = array_slice($response["data"], 0, $limit);
    
    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

$router->run();