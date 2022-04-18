<script src="/assets/dynamicContent.js"></script>
<script src="/assets/index.js"></script>
<script src="/content/pages/assets/js/getStorageItems.js"></script>
<?php
session_start();
include '../config/config.php';
include '../requires/Route.php';

// Define a global basepath
define('BASEPATH','/');

// Get Pagestructures
$json_obj = file_get_contents("pages.txt");
define("PAGES", json_decode($json_obj, true));


// Include router class

// If your script lives in a subfolder you can use the following example
// Do not forget to edit the basepath in .htaccess if you are on apache
// define('BASEPATH','/api/v1');

// Lets define some slugs for automatic route and navigation generation
// See examples below
?>
<!DOCTYPE html>
<html lang="en" manifest="demo.appcache">
<head>
  <!-- link dynamic content loader -->
  
  <link rel="stylesheet" href="content/pages/assets/css/loading_animation.css">
  <link rel="preload" href="/assets/index.js" as="script">
  <link rel="stylesheet" href="/assets/style.css">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    
    
  <!-- <button id="page1" onclick="handleClick('home');">Administration</button> -->
  <!-- dynamic content-->
  <div class="navbar" id="navbar" data-page="navbar.php" data-js="navbar.js"></div>
  <div class="dynamic-content" id="dynamic-content"></div>
</body>
</html>
<script>
  var usedLaterScript = document.createElement('script');
  usedLaterScript.src = '/assets/index.js';
  document.body.appendChild(usedLaterScript);
</script>
<?php

Route::generate();
Route::run(BASEPATH);
?>