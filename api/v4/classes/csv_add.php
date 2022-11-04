<?php

use GrahamCampbell\ResultType\Success;

require "../config.php";

authorize("add_csv");

$inputData = getData("POST", ["table", "columns", "string", "seperator", "linebreak"]);
$global = (isset($inputData["global"])) ? $inputData["global"] : [];
$enclosure = (isset($inputData["enclosure"])) ? $inputData["enclosure"] : "";

$rows = str_getcsv($inputData["string"], $inputData["linebreak"]);
for ($i = 0; $i < count($rows); $i++)
    $data[$i] = str_getcsv($rows[$i], $inputData["seperator"], $inputData["enclosure"]);

csv::checkForError($data, count($inputData["columns"])-count($global));
csv::add($inputData["table"], $inputData["columns"], $data, $global);

Response::success(count($rows) . " Zeilen wurden eingefügt");

class csv
{
    /*public function __construct(string $table, array $columns, string $string, string $seperator, string $linebreak, string $enclosure = "")
    {
        $rows = str_getcsv($string, $linebreak);
        for ($i = 0; $i < count($rows); $i++)
            $data[$i] = str_getcsv($rows[$i], $seperator, $enclosure);
        
        $this->table = $table;
        $this->columns = $columns;
        $this->data = $data;
        $this->seperator = $seperator;
        $this->linebreak = $linebreak;
        $this->enclosure = $enclosure;
    }*/

    static public function add($table, $columns, $data, $global = [])
    {
        for ($i = 0; $i < count($data); $i++)
        {
            self::addRow($table, $columns, $data[$i], $global);
        }
    }

    static private function addRow($table, $columns, $data, $global = [])
    {
        
        global $pdo;
        $sql = "INSERT INTO $table (";
        $sql .= implode(", ", $columns);
        $sql .= ") VALUES (";

        if ($global)
        {
            for ($i = 0; $i < count($columns); $i++)
            {
                if (array_key_exists($columns[$i], $global))
                    $sql .= "'" . $global[$columns[$i]] . "'";

                if (isset($data[$i]))
                    $sql .= "'" . $data[$i] . "'";
                
                if ($i < count($columns)-1)
                    $sql .= ", ";
            }
            $sql .= ")";
        }
        else
            $sql .= "'" . implode("', '", $data) . "')";
        
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
    }

    static public function checkForError($data, $columns)
    {
        $errorCounter = 0;
        $rowsWithError = [];
        for ($i = 0; $i < count($data); $i++)
        {
            if (count($data[$i]) != $columns)
            {
                array_push($rowsWithError, $i);
                $errorCounter++;
            }
        }
        if ($errorCounter > 0)
            throw new CustomException("$errorCounter rows with errors: " . implode(", ", $rowsWithError) . ". " . $columns . " columns expected. No rows were inserted", "INSERT_ERROR", 400);
    } 
}
?>