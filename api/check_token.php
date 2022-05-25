<?php
require 'config.php';
if (isset($_SERVER["HTTP_AUTHORIZATION"])) $given_token = $_SERVER["HTTP_AUTHORIZATION"];
else return false;
$token_hash = md5(explode(" ", $given_token)[1]);

// search for token
$sql = "SELECT token_permissions FROM token WHERE token_hash = :token_hash";
$sth = $pdo->prepare($sql);
$sth->execute(["token_hash" => $token_hash]);
$result  = $sth->fetch();
$given_permissions = json_decode($result["token_permissions"]);
var_dump($given_permissions);


foreach($given_permissions as $p)
{
    $sql = "SELECT * FROM property_token_permissions WHERE permission_id = :id";
    $sth = $pdo->prepare($sql);
    $sth->execute(["id" => $p]);
    $row = $sth->fetch();
    echo $row['permission_text']."<br>";
}