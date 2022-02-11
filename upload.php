<?php
var_dump($_POST);
if ($_POST["directory"] !== "") 
{
    $uploaddir = 'C:\xampp\htdocs\apollo/'.$_POST["directory"].'/';
    $directory = $_POST["directory"];
    $result = null;
    if (!is_dir($directory)) {
        for ($i=0; $i < strlen($directory); $i++)
        {
            $result = $num = -1 * (abs($i)+1);
            if (substr($directory, $result, 1) == '/')
            {
                // echo "true: ".substr($directory, $result+1)."<br>";
                $directory = substr($directory, 0, $i+1);
                break;
            }
        }
    }
}
else
{
    $uploaddir = 'uploads/';
}
if ($_FILES)
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    $response = "upload success\n";
} else {
    $response = "file missing\n";
}



echo json_encode(['success' => $response, 'info' => $_FILES["userfile"]]);
?>  