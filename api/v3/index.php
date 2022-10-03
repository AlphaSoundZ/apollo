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

$router->get('/user/select/{column}/{param}', function ($column, $param) {
    require 'classes/search_class.php';
    $response["message"] = "";
    $response["response"] = "";

    $table = new table();
    $selectedTable = $table->selectTable([["table" => "user"]], ["user_id", "user_firstname", "user_lastname"], ["limit" => 10]);
    // $limit = (isset($data["search"]["limit"])) ? $data["search"]["limit"] : "";
    $response["search"] = $table->search($param, $selectedTable, ["user_id"]);
    echo json_encode($response, JSON_PRETTY_PRINT); // return the response
});

$router->run();