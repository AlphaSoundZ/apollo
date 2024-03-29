<?php
require_once 'config.php';
require_once 'classes/create.class.php';

authorize("add_user");
$data = getData("POST", ["firstname", "lastname", "class", "usercard_uid", "new_usercard", "new_user"]);

$firstname = $data["firstname"];
$lastname = $data["lastname"];
$class = $data["class"];
$usercard_uid = $data["usercard_uid"];
$new_usercard = ($data["new_usercard"] == "true") ? true : false;
$new_user = ($data["new_user"] == "true") ? true : false;

$user_id = Create::checkUser($firstname, $lastname);
if ($new_user && $user_id)
    throw new ResponseException(Response::USER_ALREADY_EXISTS, "USER_ALREADY_EXISTS", 400, ["firstname", "lastname"]);
if (!$new_user && !$user_id)
    throw new ResponseException(Response::USER_NOT_FOUND, "USER_NOT_FOUND", 400, ["firstname", "lastname"]);
if (!$new_user && Create::checkUserForAssignement($user_id))
    throw new ResponseException(Response::USER_ALREADY_ASSIGNED, "USER_ALREADY_ASSIGNED", 400, ["firstname", "lastname"]);

$usercard_check = Create::checkUsercard($usercard_uid);
if ($usercard_check == "USERCARD_NOT_FOUND" && !$new_usercard)
    throw new ResponseException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400, ["usercard_uid"]);
else if ($usercard_check == "USERCARD_ALREADY_ASSIGNED")
    throw new ResponseException(Response::USERCARD_ALREADY_ASSIGNED, "USERCARD_ALREADY_ASSIGNED", 400, ["usercard_uid"]);

if ($usercard_check == "USERCARD_NOT_FOUND" && $new_usercard) // create new usercard and usercard does not exist
    $usercard_id = Create::createUserCard($usercard_uid);
else if ($usercard_check && !$new_usercard) // do not create usercand and usercard does exist
    $usercard_id = $usercard_check;
else if ($usercard_check && $new_usercard) // if you want to create a usercard but the usercard already exists
    throw new ResponseException(Response::USERCARD_ALREADY_EXISTS, "USERCARD_ALREADY_EXISTS", 400, ["usercard_uid"]);
else // if you only want to assign but usercard does not exist
    throw new ResponseException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400, ["usercard_uid"]);

if (!Create::checkClass($class))
    throw new ResponseException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400, ["class"]);

if ($new_user)
    $user_id = Create::createUser($firstname, $lastname, $class);

Create::bindUserToUsercard($user_id, $usercard_id);

Response::success(array_merge(Response::SUCCESS, ["message" => "Ausleih-Paket wurde erfolgreich für $firstname $lastname erstellt (user_id: $user_id, usercard_id: $usercard_id)"]), ["user_id" => $user_id, "usercard_id" => $usercard_id]);