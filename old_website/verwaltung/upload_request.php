<?php
sleep(2);
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
    $uploaddir = 'C:/xampp/htdocs/apollo/uploads/';
}
if ($_FILES) {
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        $response = "upload success!";
    } else {
        $response = "file missing!";
    }
}



echo json_encode(['success' => $response, 'info' => ['name' => $_FILES["userfile"]["name"], 'size' => $_FILES["userfile"]["size"]]]);
?>  