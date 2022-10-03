<?php

/*
response codes:
0 = search success
1 = no input
2 = only selection success

*/

require 'config.php';
require 'classes/search_class.php';

authorize("search");

// get input:
$data = getData("POST", ["table"]);
$response["message"] = "";
$response["response"] = "";


$table = new table();
$response["data"] = $table->selectTable($data["table"], $data["column"], $data["filter"]);
if (isset($data["search"]))
{
    $response["data"] = $table->search($data["search"]["value"], $response["data"], $data["search"]["column"]);
    
    // reduce the array to only the first 10 results
    if (isset($data["search"]["limit"]) && $data["search"]["limit"] !== 0)
        $response["data"] = array_slice($response["data"], 0, $data["search"]["limit"]);
}
echo json_encode($response); // return the response