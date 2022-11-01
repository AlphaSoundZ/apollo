<?php
require "config.php";

// for using the created database, please update in .ENV file the database name (DB)

$db_current_api_version = "ausleihe_v3";
$new_version = "ausleihe_v4";
$overwrite = false;


// check if the database is already created
$sql = "CREATE DATABASE IF NOT EXISTS $new_version";

$host = "localhost";

    $root = $ENV["USERNAME"];
    $root_password = $ENV["PASSWORD"];

    try {
        $dbh = new PDO("mysql:host=$host", $root, $root_password);

        $dbh->exec("CREATE DATABASE `$db`;
                GRANT ALL ON `$db`.* TO '$root'@'localhost';
                FLUSH PRIVILEGES;")
        or die(print_r($dbh->errorInfo(), true));

    }
    catch (PDOException $e) {
        die("DB ERROR: " . $e->getMessage());
    }