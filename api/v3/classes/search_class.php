<?php
class Select {
    /**
     * @param array $table
     * @param array $columns
     * @param array $options (optional)
     * @return array returns the table with the selected columns
     */
    static function select($table, $columns, $options = []) {
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

        if (isset($options["groupby"])) $sql .= ' GROUP BY '.$options["groupby"];
        if (isset($options["orderby"]) && isset($options["direction"])) $sql .= ' ORDER BY '.$options["orderby"].' '.$options["direction"];
        if (isset($options["size"]) && isset($options["page"])) $sql .= ' LIMIT '.$options["size"].' OFFSET '.$options["page"]*$options["size"];

        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $response["message"] = "Selection success";
        $response["response"] = 2;
        return $result;
    }

    static function search($table, $columns, $needles, $options = []) {
        global $pdo, $response;
        $page = 0;
        $size = null;
        if (array_key_exists("page", $options))
        {
            $page = $options["page"];
            unset($options["page"]);
        }
        if (array_key_exists("size", $options))
        {
            $size = $options["size"];
            unset($options["size"]);
        }
        if (array_key_exists("strict", $options) && $options["strict"] == true)
        {
            $strict = true;
            unset($options["strict"]);
        }
        else $strict = false;
        
        $haystack = self::select($table, $columns, $options);
        $result = self::searchalgo($needles, $haystack, $columns, $strict);
        if ($size !== 0)
            $result = array_slice($result, $size*$page, $size);
        return $result;
    }

    static function strictSearch($table, $column, $needle, $options = [])
    {
        global $pdo, $response;
        $sql = "SELECT * FROM $table WHERE $column LIKE '$needle'";
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
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
            foreach($needles as $needle)
            {
                $last_best = $best;
                for ($column = 0; $column < count($columns); $column++)
                {
                    $value = $haystack[$row][$columns[$column]];
                    similar_text(strtolower($needle),strtolower($value),$percent);
                    $best = max($best, round($percent, 1));
                }
                $average = ($average !== 0) ? ($best + $last_best)/2 : $best;
            }
                if (($average >= 50 && $strict == false) || ($average == 100 && $strict == true))
                array_push($result, ["accordance" => $average, "data" => $haystack[$row]]);
        }
        
        $column_accordance = array_column($result, 'accordance');
        array_multisort($column_accordance, SORT_DESC, $result);
        $response["message"] = "search success";
        $response["response"] = 0;
        return $result;
    }
}