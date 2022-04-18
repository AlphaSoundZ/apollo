<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="import.js"></script>
    <title>import & reset</title>
</head>
<body>
    <form enctype="multipart/form-data" action="upload_request.php" method="POST" name="fileuploadform" id="fileuploadform" onsubmit="event.preventDefault();">
        <!-- MAX_FILE_SIZE muss vor dem Datei-Eingabefeld stehen -->
        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        <!-- Der Name des Eingabefelds bestimmt den Namen im $_FILES-Array -->
        File to import (only .csv & .txt): <input id="userfile" name="userfile" type="file" accept=".csv, .txt" required/>
        <input type="text" placeholder="seperator" required></input>
        <input type="submit" value="Send File" /><br>
    </form>
    <h2>File Preview:</h2>
    <div class="preview-container">
        <div style="display:block" class="nofileselected" id="nofileselected"><p>No file selected</p></div>
        <div style="display:none" class="filepreview" id="filepreview"></div>
    </div>
</body>
</html>