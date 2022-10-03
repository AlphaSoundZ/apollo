<?php

/*
response codes:
0 = search success
1 = no input
2 = only selection success

*/

require 'config.php';

authorize("search");

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

class table {
    function selectTable($table, $column, $filter = []) {
        global $pdo, $response;
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

        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll();
        $response["message"] = "Selection success";
        $response["response"] = 2;
        return $result;
    }

    static function search($needles, $haystack, $filter) {
        global $response;
        $result = array();
        $needles = explode(" ", strtolower($needles));
        for ($row = 0; $row < count($haystack); $row++) // loop every row
        {
            $best = 0;
            for ($column = 0; $column < count($filter); $column++)
            {
                foreach($needles as $needle)
                {
                    $value = $haystack[$row][$filter[$column]];
                    similar_text(strtolower($needle),strtolower($value),$percent);
                    $best = max($best, $percent);
                }
            }
            array_push($result, ["accordance" => $best, "data" => $haystack[$row]]);
        }
        
        $column_accordance = array_column($result, 'accordance');
        array_multisort($column_accordance, SORT_DESC, $result);
        $response["message"] = "search success";
        $response["response"] = 0;
        return $result;
    }
}