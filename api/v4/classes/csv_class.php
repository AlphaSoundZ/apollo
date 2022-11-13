<?php
require_once 'config.php';
class Csv
{
    public function __construct(string $table, array $columns, string $string, string $seperator, string $linebreak, array $global = [], string $enclosure = "")
    {
        $rows = str_getcsv($string, $linebreak);
        for ($i = 0; $i < count($rows); $i++)
            $data[$i] = str_getcsv($rows[$i], $seperator, $enclosure);
        
        $this->table = $table;
        $this->columns = $columns;
        $this->data = $data;
        $this->seperator = $seperator;
        $this->linebreak = $linebreak;
        $this->global = $global;
        $this->enclosure = $enclosure;
        $this->rows = $rows;
    }

    public function add()
    {
        for ($i = 0; $i < count($this->data); $i++)
            self::addRow($this->data[$i]);
    }

    private function addRow($row)
    {
        
        global $pdo;
        $sql = "INSERT INTO {$this->table} (";
        $sql .= implode(", ", $this->columns);
        $sql .= ") VALUES (";

        if ($this->global)
        {
            for ($i = 0; $i < count($this->columns); $i++)
            {
                if (array_key_exists($this->columns[$i], $this->global))
                    $sql .= "'" . $this->global[$this->columns[$i]] . "'";

                if (isset($row[$i]))
                    $sql .= "'" . $row[$i] . "'";
                
                if ($i < count($this->columns)-1)
                    $sql .= ", ";
            }
            $sql .= ")";
        }
        else
            $sql .= "'" . implode("', '", $row) . "')";
        
        $sth = $pdo->prepare($sql);
        $sth->execute();
    }

    public function checkForError()
    {
        $errorCounter = 0;
        $rowsWithError = [];
        for ($i = 0; $i < count($this->data); $i++)
        {
            if (count($this->data[$i]) != (count($this->columns) - count($this->global)))
            {
                array_push($rowsWithError, $i);
                $errorCounter++;
            }
        }
        if ($errorCounter > 0)
            throw new CustomException("$errorCounter rows with errors: " . implode(", ", $rowsWithError) . ". " . count($this->columns) . " columns expected. No rows were inserted", "INSERT_ERROR", 400);
    } 
}