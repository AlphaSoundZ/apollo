<?php
require("config.php");
/*
$data = [
    "search" => "",
    "table" => "",
    "filter" => "", // Examples: ["page" => "3", "pagesize" => "50"] for just simple limit set page to 0
    "columns" => ['name', 'klasse'] // Example: ["firstname", "lastname", "class"] to get every column just leave it empty
];

example url: http://localhost:8080/search.php?data={%22table%22:%22user%22,%20%22filter%22:{%22page%22:%220%22,%20%22pagesize%22:%222%22},%20%22columns%22:[%22name%22,%20%22vorname%22]}
*/

$data = getData();

//var_dump(json_decode($data, true));



if ($data)
{
    // check if input has a filter and/or columns
    $data["filter"] = (!empty($data["filter"])) ? $data["filter"] : "";
    $data["columns"] = (!empty($data["columns"])) ? $data["columns"] : "";

    $table_data = selectTable($data["table"], $data["filter"], $data["columns"]);
    
    var_dump($table_data);

}


function selectTable($table, $filter = array(), $columns = array())
{
    global $pdo;

    if (empty($columns))
    {
        $columns = "*";
    }
    else
    {
        $columns = implode(", ", $columns);
    }

    $sql = 'SELECT '.$columns.' FROM '.$table.'';
    if (isset($filter["page"]) && isset($filter["pagesize"]))
    {
        $sql .= ' LIMIT '.$filter["pagesize"].' OFFSET '.$filter["page"].'';
    }

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}




if (isset($_GET['search']) && isset($_GET['table']))
{
    $search = $_GET['search'];
    $table = $_GET['table'];
    array_diff($array1, $array2);
}
