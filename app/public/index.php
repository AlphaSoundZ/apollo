<?php
session_start();
include '../config/config.php';
session();


// Include router class
include '../requires/Route.php';

// If your script lives in a subfolder you can use the following example
// Do not forget to edit the basepath in .htaccess if you are on apache
// define('BASEPATH','/api/v1');

// Lets define some slugs for automatic route and navigation generation
// See examples below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- link dynamic content loader -->
    <script src="/assets/dynamicContent.js"></script>
    <script src="/assets/index.js"></script>
    <link rel="stylesheet" href="/assets/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div id='loading' class="spinner-wrapper" style="display:none;">
        <span id ='spinner' class="spinner"></span>
    </div>
    
    <button id="page1" onclick="handleClick('home');">Administration</button>

    <!-- dynamic content-->
    <div class="dynamic-content" id="dynamic-content"></div>
</body>
</html>
<?php

Route::generate();
Route::run(BASEPATH);
?>