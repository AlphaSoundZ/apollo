<?php
session_start();
require 'config.php';
session(); // check if session runs
?>
<!DOCTYPE html>
<html lang="en" style="overflow:hidden">
<head>
    <link rel="Stylesheet" href="style_main.css"/>
    <link rel="Stylesheet" href="loading_animation.css">
    <script src="getStorageItems.js"></script>
    <script src="fileupload.js"></script>
    <script src="import.js"></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
</head>
<body onload="onloadMain();">
    <!-- loading animation -->
	<div id='loading' class="spinner-wrapper" style="display:none;">
        <span id ='spinner' class="spinner"></span>
    </div>
    <div name="navbar" class="navbar">
        Navbar
    </div>
    <div name="request-content" class="request-content">
        Main content
    </div>
</body>
</html>
<script>
    function onloadMain()
    {
        // onload function
    }
</script>