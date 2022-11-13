<?php
require_once 'config.php';
require 'vendor/autoload.php';

$router = new \Bramus\Router\Router;

$router->set404('/', function() {
    throw new CustomException(Response::ROUTE_NOT_DEFINED, "ROUTE_NOT_DEFINED", 404);
});

$router->get('/status', function () {
    require 'status.php';
});

$router->get('/user(/[^/]+)?', function($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null) // search for user with $id
    {
        $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["user_id", "class_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Benutzer nicht gefunden";
    }
    else if (isset($booking) && $booking == "true") // show all booking users
    {
        $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]], ["table" => "event", "join" => ["user.user_id", "event.event_user_id"]]], ["user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name", "user.user_usercard_id", "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) AS amount"], ["page" => $page, "size" => $size, "groupby" => "user.user_id", "having" => "sum(case when event.event_end is null and event.event_user_id = user.user_id then 1 else 0 end) > 0"]);
        $response["message"] = ($response["data"]) ? "Benutzer gefunden" : "Es wird zurzeit nichts ausgeliehen";
    }
    else // show all users or search for user using ?query=
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["query"] = $query;
            $response["data"] = Select::search([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["user_firstname", "user_lastname"], $query, ["page" => $page, "size" => $size, "strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Benutzer";
            $response["data"] = Select::select([["table" => "user"], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $size]);
        }
    }
    
    // echo json_encode($response, JSON_PRETTY_PRINT); // return the response
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/user(/\d+)/history', function($id) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["data"] = Select::search([["table" => "event"]], ["event.event_user_id", "event.event_begin", "event.event_end", "event.event_multi_booking_id"], ["event_user_id"], $id, ["page" => $page, "size" => $size, "strict" => true, "groupby" => "event.event_multi_booking_id", "orderby" => "event.event_multi_booking_id", "direction" => "DESC"]);
    if (!$response["data"])
    {
        $response["message"] = "Keine Buchungen gefunden";
        echo json_encode($response);
        return;
    }
    $response["message"] = "Buchungen zu diesem Benutzer gefunden";

    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/device/type(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for class with $id
    {
        $response["data"] = Select::search([["table" => "property_device_type"]], ["*"], ["device_type_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
    }
    else // show every class
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_device_type"]], ["*"], ["device_type_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Gerätetyp gefunden" : "Gerätetyp nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle Gerätetypen";
            $response["data"] = Select::select([["table" => "property_device_type"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/device(/[^/]+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    if ($id !== null) // search for device with $id or uid
    {
        $response["data"] = Select::search([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["device_id", "device_uid", "device_type_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Gerät gefunden" : "Gerät nicht gefunden";
    }
    else if ($booking == "true") // show all booked devices
    {
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]], ["table" => "user", "join" => ["user.user_id", "devices.device_lend_user_id"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name", "user.user_id", "user.user_firstname", "user.user_lastname"], ["page" => $page, "size" => $size, "where" => "devices.device_lend_user_id != 0"]);
        $response["message"] = ($response["data"]) ? "Alle gebuchten Geräte" : "Es werden derzeit keine Geräte gebucht";
    }
    else // show every device
    {
        $response["message"] = "Alle Geräte";
        $response["data"] = Select::select([["table" => "devices"], ["table" => "property_device_type", "join" => ["property_device_type.device_type_id", "devices.device_type"]]], ["devices.device_id", "devices.device_uid", "property_device_type.device_type_id", "property_device_type.device_type_name"], ["page" => $page, "size" => $size]);
    }
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/device(/[^/]+)/history', function ($id) {
    require 'classes/search_class.php';
    authorize("search");

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    $response["message"] = "";

    // check if device exists
    $device = Select::search([["table" => "devices"]], ["device_id", "device_uid"], ["device_id", "device_uid"], $id, ["strict" => true]);
    if (!$device)
    {
        $response["message"] = "Gerät nicht gefunden";
        echo json_encode($response);
        return;
    }

    // if $id is an $uid
    $id = $device[0]["device_id"];

    $response["data"] = Select::search([["table" => "event"]], ["event.event_device_id", "event.event_begin", "event.event_end", "event.event_multi_booking_id"], ["event_device_id"], $id, ["page" => $page, "size" => $size, "strict" => true]);
    if (!$response["data"])
    {
        $response["message"] = "Keine Buchungen gefunden";
        echo json_encode($response);
        return;
    }
    $response["message"] = "Buchungen zu diesem Benutzer gefunden";

    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/user/class(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for class with $id
    {
        $response["data"] = Select::search([["table" => "property_class"]], ["*"], ["class_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
    }
    else // show every class
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_class"]], ["*"], ["class_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "Klasse gefunden" : "Klasse nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle Klassen";
            $response["data"] = Select::select([["table" => "property_class"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/usercard/type(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for usercard type with $id
    {
        $response["data"] = Select::search([["table" => "property_usercard_type"]], ["*"], ["usercard_type_id"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    }
    else // show every usercard type
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;
        
        if ($query)
        {
            $response["data"] = Select::search([["table" => "property_usercard_type"]], ["*"], ["usercard_type_name"], $query, ["strict" => $strict]);
            $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
        }
        else
        {
            $response["message"] = "Alle User Indentifikationen";
            $response["data"] = Select::select([["table" => "property_usercard_type"]], ["*"], ["page" => $page, "size" => $size]);
        }
    }
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/usercard(/[^/]+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;

    if ($id) // search for usercard with $id
    {
        $response["data"] = Select::search([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["usercard.*", "property_usercard_type.usercard_type_name", "user_id", "user_firstname", "user_lastname", "class_name"], ["usercard_id", "usercard_uid", "usercard_type_name"], $id, ["strict" => true]);
        $response["message"] = ($response["data"]) ? "User Indentifikation gefunden" : "User Indentifikation nicht gefunden";
    }
    else // show every usercard
    {
        $response["message"] = "Alle User Indentifikationen";
        $response["data"] = Select::select([["table" => "usercard"], ["table" => "property_usercard_type", "join" => ["property_usercard_type.usercard_type_id", "usercard.usercard_type"]], ["table" => "user", "join" => ["user.user_id", "usercard.usercard_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["usercard.*", "property_usercard_type.usercard_type_name", "user_id", "user_firstname", "user_lastname", "class_name"], ["page" => $page, "size" => $size]);
    }
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

$router->get('/token(/\d+)?', function ($id = null) {
    require 'classes/search_class.php';
    authorize("search");

    $response["message"] = "";

    $booking = (isset($_GET["booking"])) ? $_GET["booking"] : null;

    $size = (isset($_GET["size"]) && $_GET["size"] > 0) ? $_GET["size"] : 0;
    $page = ($size !== 0 && isset($_GET["page"])) ? $_GET["page"] : 0;
    
    if ($id !== null) // search for user with $id
    {
        $response["data"] = Select::search([["table" => "token"], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "token.token_permissions", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["token_id"], $id, ["strict" => true]);
        for ($i = 0; $i < count($response["data"]); $i++)
        {
            $permission_list = Select::select([["table" => "property_token_permissions"]], ["*"]);
            $decoded = json_decode($response["data"][$i]["token_permissions"]);
            for ($j = 0; $j < count($decoded); $j++)
                $new_permission_list[$decoded[$j]] = $permission_list[array_search($decoded[$j], array_column($permission_list, "permission_id"))]["permission_text"];
            $response["data"][$i]["token_permissions"] = $new_permission_list;
        }
        $response["message"] = ($response["data"]) ? "Token gefunden" : "Token nicht gefunden";
    }
    else // show all users or search for user using ?query=
    {
        $query = (isset($_GET["query"])) ? $_GET["query"] : null;
        $strict = (isset($_GET["strict"]) && $_GET["strict"] == "true") ? true : false;

        if ($query)
        {
            $response["query"] = $query;
            $response["data"] = Select::search([["table" => "token"], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "token.token_permissions", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["token_username"], $query, ["page" => $page, "size" => $size, "strict" => $strict]);
            
            $permission_list = Select::select([["table" => "property_token_permissions"]], ["*"]);
            for ($i = 0; $i < count($response["data"]); $i++)
            {
                unset($new_permission_list);
                if ($strict)
                    $decoded = json_decode($response["data"][$i]["token_permissions"]);
                else
                    $decoded = json_decode($response["data"][$i]["data"]["token_permissions"]);

                for ($j = 0; $j < count($decoded); $j++)
                    $new_permission_list[$decoded[$j]] = $permission_list[array_search($decoded[$j], array_column($permission_list, "permission_id"))]["permission_text"];
                $response["data"][$i]["token_permissions"] = $new_permission_list;
            }
            $response["message"] = ($response["data"]) ? "Suche erfolgreich" : "Keine Ergebnisse";
        }
        else
        {
            $response["message"] = "Alle Tokens";
            $response["data"] = Select::select([["table" => "token"], ["table" => "user", "join" => ["user.user_token_id", "token.token_id"]], ["table" => "property_class", "join" => ["property_class.class_id", "user.user_class"]]], ["token.token_id", "token.token_username", "token.token_permissions", "token.token_last_change", "user.user_id", "user.user_firstname", "user.user_lastname", "property_class.class_id", "property_class.class_name"], ["page" => $page, "size" => $size]);
            
            $permission_list = Select::select([["table" => "property_token_permissions"]], ["*"]);
            for ($i = 0; $i < count($response["data"]); $i++)
            {
                unset($new_permission_list);
                $decoded = json_decode($response["data"][$i]["token_permissions"]);
                for ($j = 0; $j < count($decoded); $j++)
                    $new_permission_list[$decoded[$j]] = $permission_list[array_search($decoded[$j], array_column($permission_list, "permission_id"))]["permission_text"];
                $response["data"][$i]["token_permissions"] = $new_permission_list;
            }
        }
    }
    
    // echo json_encode($response, JSON_PRETTY_PRINT); // return the response
    Response::success($response["message"], null, ["data" => $response["data"]]);
});

// Post

$router->post('/csv', function () {
    require 'classes/csv_class.php';
    authorize("add_csv");

    $inputData = getData("POST", ["table", "columns", "string", "seperator", "linebreak"]);
    $global = (isset($inputData["global"])) ? $inputData["global"] : [];
    $enclosure = (isset($inputData["enclosure"])) ? $inputData["enclosure"] : "";

    $csv = new Csv($inputData["table"], $inputData["columns"], $inputData["string"], $inputData["seperator"], $inputData["linebreak"], $global, $enclosure);
    $csv->checkForError();
    $csv->add();

    Response::success(count($csv->rows) . " Zeilen wurden eingefügt");
});

$router->run();