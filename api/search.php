<?php
/*

*/

/*
response codes:
0 = search success
1 = no input

*/
require 'config.php';
// get input:
$data = getData("POST");
$response["message"] = "";
$response["response"] = "";

if (isset($data["table"]))
{
    $table = new table();
    $response["table"] = $table->selectTable($data["table"], $data["column"], $data["filter"]);
    if (isset($data["search"]))
    {
        $limit = (isset($data["search"]["limit"])) ? $data["search"]["limit"] : "";
        $response["search"] = $table->search($data["search"]["value"], $response["table"], $data["search"]["column"]);
    }
}
else
{ // input missing
    $response["message"] = "wrong input data";
    $response["response"] = 1;
}

echo json_encode($response); // return the response

class table {
    static function selectTable($table, $column, $filter = []) {
        global $pdo;
        $first_table = $table[0]["table"];
        array_shift($table);
        $column = implode(", ", $column);
        $join = "";

        foreach ($table as $key => $t)
        {
            $table_name = $t['table'];
            $link_1 = $t['link'][0];
            $link_2 = $t['link'][1];
            $join .= " LEFT JOIN $table_name ON $link_1 = $link_2";
        }
        $sql = "SELECT $column FROM $first_table $join";
        if (isset($filter["orderby"]) && isset($filter["direction"])) $sql .= ' ORDER BY '.$filter["orderby"].' '.$filter["direction"].' ';
        if (isset($filter["size"]) && isset($filter["page"])) $sql .= ' LIMIT '.$filter["size"].' OFFSET '.$filter["page"].' ';
        echo $sql."<br>";

        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        return $result;
    }

    static function search($needle, $haystack, $filter) {
        $result = array();
        for ($row = 0; $row < count($haystack); $row++) // loop every row
        {
            $best = 0;
            for ($column = 0; $column < count($filter); $column++)
            {
                $value = $haystack[$row][$filter[$column]];
                similar_text(strtolower($needle),strtolower($value),$percent);
                $best = max($best, $percent);
            }
            array_push($result, ["accordance" => $best, "data" => $haystack[$row]]);
        }
        return $result;
    }
}