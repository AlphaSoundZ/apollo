<?php // not ready to use!
require "config.php";
$myfile = fopen("liste.txt", "r") or die("Unable to open file!");
echo fread($myfile,filesize("webdictionary.txt"));
fclose($myfile);
?>