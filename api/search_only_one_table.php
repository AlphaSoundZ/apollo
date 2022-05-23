<?php
require("config.php");
/*
$data = [
    "search" => ["value" => "foo", "filter" => []],
    "table" => "",
    "filter" => "", // Examples: ["page" => "3", "pagesize" => "50"] for just simple limit set page to 0
    "columns" => ['name', 'klasse'] // Example: ["firstname", "lastname", "class"] to get every column just leave it empty
];

example url: http://localhost:8080/search.php?data={%22table%22:%22user%22,%22filter%22:{%22page%22:%220%22,%22pagesize%22:%222%22},%22search%22:{%22value%22:%22Frische%22,%22filter%22:[%22name%22]}}
*/

$data = getData("POST");

if ($data)
{
    // check if input has a filter and/or columns
    $data["filter"] = (!empty($data["filter"])) ? $data["filter"] : "";
    $data["columns"] = (!empty($data["columns"])) ? $data["columns"] : "";

    $table_data = selectTable($data["table"], $data["filter"], $data["columns"]);

    if (!empty($data["search"]["value"]) && !empty($data["search"]["filter"]))
    {
        $result = doSearch($data["search"]["value"], $table_data, $data["search"]["filter"]);
        print_r($result);
    }

}
else
{
    echo "wrong data input!";
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

function doSearch($needle, $haystack, $filter)
{
    $result = array();
    //$limit = $arr["limit"];
    for ($row = 0; $row < count($haystack); $row++) // loop every user
    {
        for ($column = 0; $column < count($haystack[$row])/2; $column++) // loop columns to search
        {
            for ($filterColumn = 0; $filterColumn < count($filter); $filterColumn++)
            {
                if (array_search($haystack[$row][$column], $haystack[$row]) == $filter[$filterColumn])
                {
                    similar_text($needle,$haystack[$row][$column],$percent);
                    array_push($result, ["accordance" => $percent, "data" => $haystack[$row]]);
                }
            }
        }
    }
    return $result;
}

/*
TABLES:
"tables":{{"table":"user","column":["*"], "link_this":"","link_to":""}

FILTER:
"filter":{"page":"0","pagesize":"10"}

SEARCH:
"search":{"value":"peter","filter":["column","column"]}

if same columns twice rename column:
"tables":{{"table":"user","column":["name as username"]},}
"search":{"value":"peter","filter":["username","name"]}

nochn nicht umgesetzt!
bisher nur ein Table selektierbar!
*/

function selectMultiTables($tables, $filter = array(), $columns = array())
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

    $sql = 'SELECT '.$columns.' FROM '.$tables.'';

    for ($i = 1; $i < count($tables); $i++)
    {
        $table = $tables[$i];
        $sql .= ' LEFT JOIN '.$table.' ON '.$table.'.'.$column_to_link.' = '.$link_to;
    }

    if (isset($filter["page"]) && isset($filter["pagesize"]))
    {
        $sql .= ' LIMIT '.$filter["pagesize"].' OFFSET '.$filter["page"].'';
    }

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}