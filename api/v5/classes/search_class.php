<?php
class Select {
    static function select($table, $columns, $response_structure = [], $options = []) {
        global $pdo;
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
        if (isset($options["order_by"]) && isset($options["order_strategy"])) $sql .= ' ORDER BY '.$options["order_by"].' '.$options["order_strategy"];

        $page = null;

        // get total number of rows
        $sth = $pdo->prepare($sql);
        $sth->execute();
        
        if (isset($options["size"]) && isset($options["page"]) && $options["size"] != 0) $sql .= ' LIMIT '.$options["size"].' OFFSET '.$options["page"]*$options["size"];

        $options["size"] = isset($options["size"]) && $options["size"] != 0 ? $options["size"] : $sth->rowCount();
        
        $page["total"] = $options["size"] != 0 ? ceil($sth->rowCount()/$options["size"]) : 0;
        $page["current"] = isset($options["page"]) ? $options["page"] : 0;
        $page["size"] = $options["size"] ? $options["size"] : $sth->rowCount();
        $page["total_rows"] = $sth->rowCount();
        
            

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

        //array_push($result, $page);
        return ["data" => $result, "page" => $page];
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

        // set sort = false if there is a custom order
        $sort = true;
        if (isset($options["order_by"]) && isset($options["order_strategy"]))
        {
            $sort = false;
        }
        
        $select = self::select($table, $columns, $response_structure, $options);
        $haystack = $select["data"];
        $result = self::searchalgo($needles, $haystack, $search_in_colomns, $strict, $sort);

        $select["page"]["total"] = $size ? ceil(count($result)/$size) : 1;
        $select["page"]["current"] = $page;
        $select["page"]["size"] = $size ? $size : count($result);
        $select["page"]["total_rows"] = count($result);
        
        if ($size !== 0)
            $result = array_slice($result, $size*$page, $size);
        return ["data" => $result, "page" => $select["page"]];
    }

    private static function searchalgo($needles, $haystack, $columns, $strict = false, $sort = true) {
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

            if ($sort == true)
            {
                array_multisort($column_accordance, SORT_DESC, $result);
            }
        }
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

    public static function getValueOfStructure($response_structure, $path) {
        for ($i = 0; $i < count($path); $i++)
        {
            $response_structure = $response_structure[$path[$i]];
        }

        return is_array($response_structure) ? null : $response_structure;
    }
}