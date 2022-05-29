<?php
require 'config.php';

// declare all response keys
$response["permissions"] = "";


if (isset($_SERVER["HTTP_AUTHORIZATION"]))
{
    $given_token = $_SERVER["HTTP_AUTHORIZATION"];
    $token_hash = md5(explode(" ", $given_token)[1]);
}
else die;

	// search for token
	$sql = "SELECT * FROM token WHERE token_hash = :token_hash";
	$sth = $pdo->prepare($sql);
	$sth->execute(["token_hash" => $token_hash]);
	$token  = $sth->fetch();

    
if (!$token)
{
    $response["response"] = 88;
    $response["message"] = "wrong token";
    echo json_encode($response);
    die;
}

$token_hash = md5(explode(" ", $given_token)[1]);

// search for token
$sql = "SELECT token_permissions FROM token WHERE token_hash = :token_hash";
$sth = $pdo->prepare($sql);
$sth->execute(["token_hash" => $token_hash]);
$result  = $sth->fetch();
$given_permissions = json_decode($result["token_permissions"]);

// fetch the names of the given permission_ids
$given_permissions_str = implode("', '", $given_permissions);
$sql = "SELECT * FROM property_token_permissions WHERE permission_id IN ('$given_permissions_str')";
$sth = $pdo->prepare($sql);
$sth->execute();
$given_permission_names = $sth->fetchAll(\PDO::FETCH_ASSOC);
$given_permission_names = array_column($given_permission_names, 'permission_text');


if (count($given_permissions) == count($given_permission_names))
{
    $response["permissions"] = array_combine($given_permissions, $given_permission_names);
    $response["response"] = 0;
    $response["message"] = "success";
}
else
{
    $response["response"] = 1;
    $response["message"] = "something is wrong with the token_permissions in the db";
}
echo json_encode($response);