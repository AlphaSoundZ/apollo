<?php session_start(); ?>
<?php
if ($ajax = json_decode(file_get_contents("php://input"))) {
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
    <title>CSV import</title>
</head>
<body>
    <form enctype="multipart/form-data" action="upload.php" method="POST" name="form">
        <!-- MAX_FILE_SIZE muss vor dem Datei-Eingabefeld stehen -->
        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        <!-- Der Name des Eingabefelds bestimmt den Namen im $_FILES-Array -->
        Diese Datei hochladen: <input id="userfile" name="userfile" type="file" required/>
        <input type="submit" value="Send File" />
        <input style="color:black;" type="text" placeholder="directory" id="textbox" onkeyup="keypress();" name="directory"/>
    </form>
    <br><br>
    <div id="response"></div>
    <div id="directory"></div>
</body>
</html>
<script type="text/javascript">
    function keypress()
    {
        var data = {"directory" : document.getElementById("textbox").value};
            var ajax = new XMLHttpRequest();
            ajax.open("POST", "CSVimport.php", true);
            ajax.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status == 200) {
                    const response = JSON.parse(this.responseText);
                    console.log(response);
                    if (response.response == "directory")
                    {
                        document.getElementById("textbox").style.color = "green";
                        if (response.text == false)
                        {
                            document.getElementById("directory").innerHTML = "";
                        }
                        else 
                        {
                            document.getElementById("directory").innerHTML = response.text;
                        }
                    }
                    else if (response.response == "file")
                    {
                        document.getElementById("textbox").style.color = "orange";
                    }
                    else if (response.response == "false")
                    {
                        document.getElementById("textbox").style.color = "red";
                    }
                }
            };
            ajax.setRequestHeader("Content-Type", "application/json");
            ajax.send(JSON.stringify(data));
    }


    var form = document.forms.namedItem("form");
    form.addEventListener('submit', function(event) {

    var fOutput = document.getElementById("response"),
        oData = new FormData(form);

    oData.append("username", "This is some extra data");

    var fReq = new XMLHttpRequest();
    fReq.open("POST", "upload.php", true);
    fReq.onload = function(event) {
        if (fReq.status == 200 && fReq.readyState == 4) {
            if (fReq.responseText) {
                console.log(fReq.responseText);
                const rt = JSON.parse(fReq.responseText).success;
                fOutput.innerHTML = rt;
                fOutput.innerHTML += JSON.stringify(JSON.parse(fReq.responseText).info);
            }
        } else {
        fOutput.innerHTML = "Error " + fReq.status + " occurred when trying to upload your file.<br \/>";
        }
    };

    fReq.send(oData);
    event.preventDefault();
    }, false);
    
</script>
    <?php
}

function _isDir($directory)
{
    if (is_dir("./$directory"))
    {
        $a = showDirectory($directory);
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

        $ignored = array('.', '..', '.svn', '.git', '.vscode', '.prettierrc');
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
