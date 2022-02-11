<?php
$directory = "./upload/test.png";
for ($i=0; $i < strlen($directory); $i++)
{
    $result = $num = -1 * (abs($i)+1);
    echo $result;
    echo substr($directory, $result, 1)."<br>";
    if (substr($directory, $result, 1) == '/')
    {
        echo substr($directory, -1);
        if (substr($directory, -1) == basename($_FILES['userfile']['name']))
        {
        }
        exit;
    }
}
?>