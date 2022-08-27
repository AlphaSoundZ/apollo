<?php
require "../config.php";

authorize("add_csv");

$response["message"] = "";
$response["response"] = "";

$inputData = getData("POST", ["table", "columns", "string", "seperator", "enclosure", "escape"]);
$table = $inputData["table"];
$columns = $inputData["columns"];
$data = str_getcsv($inputData["string"], $inputData["seperator"], $inputData["enclosure"]);

$errors = csv::checkForError($columns, $data);

if ($errors == 0)
    csv::add($table, $columns, $data);
else
    $response["message"] .= $errors . "were found. Please check your data.";

echo json_encode($response); // return the response

class csv
{
    static public function add($table, $columns, $data)
    {
        for ($i = 0; $i < count($data); $i++)
        {
            self::addRow($table, $columns, $data[$i]);
        }
    }

    static private function addRow($table, $columns, $data)
    {
        global $pdo, $response;
        $sql = "INSERT INTO $table (";
        $sql .= implode(", ", $columns);
        $sql .= ") VALUES (";
        $sql .= "'" . implode("', '", $data) . "')";
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
    }

    static public function checkForError($data, $columns)
    {
        global $response;
        $errorCounter = 0;
        for ($i = 0; $i < count($data); $i++)
        {
            if (count($data[$i]) != count($columns))
            {
                $response["message"] .= "The number of columns does not match the number of data in row $i (row has " . count($data[$i]) . " columns, but " . count($columns) . " columns are expected). \n";
                $response["response"] = 0;
                $errorCounter++;
            }
        }
        return $errorCounter;
    }
    
}
?>