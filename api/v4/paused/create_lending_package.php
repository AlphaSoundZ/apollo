<?php
require 'config.php';
require 'classes/create_class.php';

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
    throw new CustomException(Response::USER_ALREADY_EXISTS, "USER_ALREADY_EXISTS", 400);
if (!$new_user && !$user_id)
    throw new CustomException(Response::USER_NOT_FOUND, "USER_NOT_FOUND", 400);
if (!$new_user && Create::checkUserForAssignement($user_id))
    throw new CustomException(Response::USER_ALREADY_ASSIGNED, "USER_ALREADY_ASSIGNED", 400);

$usercard_check = Create::checkUsercard($usercard_uid);
if ($usercard_check == "USERCARD_NOT_FOUND" && !$new_usercard)
    throw new CustomException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400);
else if ($usercard_check == "USERCARD_ALREADY_ASSIGNED")
    throw new CustomException(Response::USERCARD_ALREADY_ASSIGNED, "USERCARD_ALREADY_ASSIGNED", 400);

if ($usercard_check == "USERCARD_NOT_FOUND" && $new_usercard) // create new usercard and usercard does not exist
    $usercard_id = Create::createUserCard($usercard_uid);
else if ($usercard_check && !$new_usercard) // do not create usercand and usercard does exist
    $usercard_id = $usercard_check;
else if ($usercard_check && $new_usercard) // if you want to create a usercard but the usercard already exists
    throw new CustomException(Response::USERCARD_ALREADY_EXISTS, "USERCARD_ALREADY_EXISTS", 400);
else // if you only want to assign but usercard does not exist
    throw new CustomException(Response::USERCARD_NOT_FOUND, "USERCARD_NOT_FOUND", 400);

if (!Create::checkClass($class))
    throw new CustomException(Response::CLASS_NOT_FOUND, "CLASS_NOT_FOUND", 400);

if ($new_user)
    $user_id = Create::createUser($firstname, $lastname, $class);

Create::bindUserToUsercard($user_id, $usercard_id);

Response::success("Ausleih-Paket wurde erfolgreich für $firstname $lastname erstellt (user_id: $user_id, usercard_id: $usercard_id)", "SUCCESS", ["user_id" => $user_id, "usercard_id" => $usercard_id]);