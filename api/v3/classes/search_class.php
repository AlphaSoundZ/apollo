<?php
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
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
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
            $average = 0;
            foreach($needles as $needle)
            {
                $last_best = $best;
                for ($column = 0; $column < count($filter); $column++)
                {
                    $value = $haystack[$row][$filter[$column]];
                    similar_text(strtolower($needle),strtolower($value),$percent);
                    $best = max($best, round($percent, 1));
                }
                $average = ($average !== 0) ? ($best + $last_best)/2 : $best;
            }
            array_push($result, ["accordance" => $average, "data" => $haystack[$row]]);
        }
        
        $column_accordance = array_column($result, 'accordance');
        array_multisort($column_accordance, SORT_DESC, $result);
        $response["message"] = "search success";
        $response["response"] = 0;
        return $result;
    }
}