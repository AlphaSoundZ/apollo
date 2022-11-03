<?php
require "../config.php";

authorize("add_csv");

$response["message"] = "";
$response["response"] = "";

$inputData = getData("POST", ["table", "columns", "string", "seperator", "enclosure"]);
$table = $inputData["table"];
$columns = $inputData["columns"];
$global = (isset($inputData["global"])) ? $inputData : [];
$escape = (isset($inputData["escape"])) ? $inputData : null;
$data = str_getcsv($inputData["string"], $inputData["seperator"], $inputData["enclosure"], $escape);

$errors = csv::checkForError($data, $columns);

if ($errors == 0)
    csv::add($table, $columns, $data, $global);
else
    $response["message"] .= $errors . "were found. Please check your data.";

echo json_encode($response); // return the response

class csv
{
    static public function add($table, $columns, $data, $global = [])
    {
        for ($i = 0; $i < count($data); $i++)
        {
            self::addRow($table, $columns, $data[$i], $global);
        }
    }

    static private function addRow($table, $columns, $data, $global = [])
    {
        
        global $pdo, $response;
        $sql = "INSERT INTO $table (";
        $sql .= implode(", ", $columns);
        $sql .= ") VALUES (";

        if ($global)
        {
            for ($i = 0; $i < count($columns); $i++)
            {
                if ($global[$i])
                    $sql .= $global[$i]."', '";

                if ($data[$i])
                    $sql .= $data[$i]."', '";
            }
            rtrim($sql, "', '");
            $sql .= "')";
        }
        else
            $sql .= "'" . implode("', '", $data) . "')";
        
        $sth = $pdo->prepare($sql);
        $result = $sth->execute();
    }

    static public function checkForError($data, $columns)
    {
        global $response;
        $errorCounter = 0;
        var_dump($data);
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

    /*static public function basicImport($filename, $columns, $table, $path = __DIR__ . "/../../../uploads/csv/")
    {
        global $pdo;
        $csvFilePath = "import-template.csv";
        $file = fopen($csvFilePath, "r");
        $columns_str = implode(", ", $columns);
        while (($row = fgetcsv($file)) !== FALSE) {
            self::addRow($table, $columns, $row);
        }
    }*/
    
}
?>