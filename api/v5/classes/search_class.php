<?php
class Select {
    static function select($table, $columns, $response_structure = [], $options = []) {
        global $pdo, $response;
        $first_table = $table[0]["table"];
        array_shift($table);
        $column = implode(", ", $columns);
        $join = "";

        foreach ($table as $key => $t)
        {
            $table_name = $t['table'];
            $link_1 = $t['join'][0];
            $link_2 = $t['join'][1];
            $join .= " LEFT JOIN $table_name ON $link_1 = $link_2";
        }
        $sql = "SELECT $column FROM $first_table $join";

        if (isset($options["where"])) $sql .= ' WHERE '.$options["where"];
        if (isset($options["groupby"])) $sql .= ' GROUP BY '.$options["groupby"];
        if (isset($options["having"])) $sql .= ' having '.$options["having"];
        if (isset($options["orderby"]) && isset($options["direction"])) $sql .= ' ORDER BY '.$options["orderby"].' '.$options["direction"];
        if (isset($options["size"]) && isset($options["page"]) && $options["size"] != 0) $sql .= ' LIMIT '.$options["size"].' OFFSET '.$options["page"]*$options["size"];

        $sth = $pdo->prepare($sql);
        $sth->execute();

        $result = array();
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) // loop every row to change the structure of the response to the one specified in the response_structure array
        {
            $filledStructure = self::fillResponseStructure($response_structure, $row);
            $other_keys = $filledStructure[1];
            
            // add other columns
            /*foreach ($row as $key => $value)
            {
                if (!in_array($key, $other_keys))
                $filledStructure[0][$key] = $value;
            }*/

            $result[] = $filledStructure[0];
        }

        $response["message"] = "Selection success";
        $response["response"] = 2;
        return $result;
    }

    static function search($table, $columns, $response_structure, $search_in_colomns, $needles, $options = []) {
        $page = 0;
        $size = null;
        if (array_key_exists("page", $options))
            $page = $options["page"];
        if (array_key_exists("size", $options))
            $size = $options["size"];
        
        $strict = (array_key_exists("strict", $options) && $options["strict"] == true) ? true : false;
        unset($options["page"]);
        unset($options["size"]);
        unset($options["strict"]);
        
        $haystack = self::select($table, $columns, $response_structure, $options);
        $result = self::searchalgo($needles, $haystack, $search_in_colomns, $strict);
        if ($size !== 0)
            $result = array_slice($result, $size*$page, $size);
        return $result;
    }

    private static function searchalgo($needles, $haystack, $columns, $strict = false) {
        global $response;
        $result = array();
        $needles = explode(" ", strtolower($needles));
        for ($row = 0; $row < count($haystack); $row++) // loop every row
        {
            $best = 0;
            $average = 0;
            foreach($needles as $needle) // loop every needle
            {
                $last_best = $best;

                $best = self::recursiveSearch($needle, $haystack[$row], $columns);

                $average = ($average !== 0) ? ($best + $last_best)/2 : $best;
            }
            if ($strict == false && $average >= 50)
                array_push($result, ["accordance" => $average, "data" => $haystack[$row]]);
            else if ($average == 100)
                array_push($result, $haystack[$row]);

        }
        if ($strict == false)
        {
            $column_accordance = array_column($result, 'accordance');
            array_multisort($column_accordance, SORT_DESC, $result);
        }
        $response["message"] = "search success";
        $response["response"] = 0;
        return $result;
    }

    private static function fillResponseStructure($response_structure, $row, $keys = []) {

        foreach ($response_structure as $key => $value)
        {
            if (is_array($value))
            {
                $recursive_results = self::fillResponseStructure($value, $row, $keys);
                $response_structure[$key] = $recursive_results[0];
                $keys = $recursive_results[1];
            } else {
                $response_structure[$key] = $row[$value];
                array_push($keys, $value);
            }
        }

        return [$response_structure, $keys];
    }

    private static function recursiveSearch($needle, $haystack, $columns, $best = 0) {
        foreach ($columns as $key => $value)
        {
            if (is_array($value))
            {
                $best = self::recursiveSearch($needle, $haystack[$key], $value, $best);
            } else {
                similar_text(strtolower($needle),strtolower($haystack[$value]),$percent);
                $best = max($best, round($percent, 1));
            }
        }
        
        return $best;
    }
}