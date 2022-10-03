<?php

/*
response codes:
0 = search success
1 = no input
2 = only selection success

*/

require 'config.php';
require 'classes/search_class.php';

// authorize("search");

// get input:
$data = getData("POST", ["table"]);
$response["message"] = "";
$response["response"] = "";


$table = new table();
$response["table"] = $table->selectTable($data["table"], $data["column"], $data["filter"]);
if (isset($data["search"]))
{
    $limit = (isset($data["search"]["limit"])) ? $data["search"]["limit"] : "";
    $response["search"] = $table->search($data["search"]["value"], $response["table"], $data["search"]["column"]);
}
echo json_encode($response); // return the response