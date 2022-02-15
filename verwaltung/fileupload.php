<?php session_start();
if (json_decode(file_get_contents("php://input")) && isset(json_decode(file_get_contents("php://input"))->directory)) {
    $ajax = json_decode(file_get_contents("php://input"));
    $dir = $ajax->directory;
    _isDir($dir);
}
else
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="fileupload.js"></script>
    <title>CSV import</title>
</head>
<body>
    <form enctype="multipart/form-data" action="upload_request.php" method="POST" name="fileuploadform" id="fileuploadform" onsubmit="fileuploadsubmit(event);">
        <!-- MAX_FILE_SIZE muss vor dem Datei-Eingabefeld stehen -->
        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        <!-- Der Name des Eingabefelds bestimmt den Namen im $_FILES-Array -->
        Diese Datei hochladen: <input id="userfile" name="userfile" type="file" onclick="document.getElementById('directory').style.display = 'block'; document.getElementById('response').style.display = 'none'" required/>
        <input type="submit" value="Send File" />
        <input style="color:black;" type="text" placeholder="directory" id="textbox" onkeyup="keypress();" name="directory"/>
    </form>
    <br><br>
    <div id="response" style="display:none"></div>
    <div id="directory" style="display:block">
        <?php
        echo showDirectory('C:/xampp/htdocs/apollo');
        ?>
    </div>
</body>
</html>
    <?php
}

function _isDir($directory)
{
    $dir = 'C:/xampp/htdocs/apollo/'.$directory;
    if (is_dir($dir))
    {
        $a = showDirectory($dir);
        $response = ["response" => "directory", "text" => $a];
    }
    elseif (is_file("./$directory"))
    {
        $response = ["response" => "file", "text" => ""];
    }
    else{
        $response = ["response" => "false", "text" => ""];
    }
    echo json_encode($response);
}

function showDirectory($directory)
{
    if ($directory)
    {

        $ignored = array('.', '..', '.svn', '.git', '.vscode', '.prettierrc', 'plugins');
        $result = scandir($directory);
        $i = 0;
        $return = null;
        for ($i = 0, $c = 0; $i < count($result); $i++)
        {
            if (in_array($result[$i], $ignored)) continue; 
            $return .=   $i."  ";
            if (is_dir($directory."/".$result[$i]) && $result[$i] != '..' && $result[$i] != '.' && $result[$i] != '.git')
            {
                $return .= $result[$i]." (".count($result)."): (<br>";
                $return .= showDirectory("$directory/$result[$i]");
                $return .= ")<br>";
            }
            else
            {
                $return .= $result[$i]."<br>";
            }
            $c++;
        }
        $return .= "<br>$directory<br>";
        return $return;
    }
    else{
        return false;
    }
}


//require "_adduserClass.php";

/*$csv = new csv();
echo $csv->check("testtablecsv.csv");*/
class csv
{
    function check($file)
    {
        $this->errorcount = 0;
        echo $this->filename."<br>";
        if ($this->handle !== FALSE)
        {
            $row = 1;
            $handle = fopen($file, "r");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                    if (count($data) != 3)
                    {
                        $this->errorcount++;
                    }
            }
                fclose($handle);
                echo "Anzahl an Zeilen: $row<br>";
                echo "Anzahl an Zeilen mit Error: $this->errorcount";
        }
        if ($this->errorcount === 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    function csvimportExecute()
    {
        // insert;
        if ($this->errorcount !== 0)
        {
            // start execute
            // 
        }
        else
        {
            return false;
        }
    }
}

?>
