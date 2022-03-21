<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head manifest="demo.appcache">
    <meta charset="utf-8">
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> <!-- bootstrap_style.css -->
    <!-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> //maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js or bootstrap.js-->
    

  </head>
  <body onload="clearAllSavedValues();">
    <div class="wrapper fadeInDown">
      <div id="formContent">

        <!-- loading animation -->
        <div id='loading' class="spinner-wrapper">
          <span class="spinner"></span>
        </div>
        
        <!-- Icon -->
        <div class="loading-pic" id='loading-pic_id'>
          <div class="fadeIn first" id='picture'>
            <img src="content/pages/assets/img/ght_logo.png" id="icon" alt="ght-hh.de" onclick="window.open('https://ght-hh.de', '_blank');"/>
          </div>
        </div>

        <!-- Login Form -->
        <form action="main.php" method="post" onsubmit="validateForm(event);" id="Form">
          <input type="text" id="login" class="fadeIn second" name="username" placeholder="username" autofocus>
          <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
          <input type="text" id="authcode" class="fadeIn fourth" name="authcode" placeholder="authentication code" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="6">
          <input type="submit" class="fadeIn fifth" value="Log In">
        </form>

        <!-- Warning message -->
        <div id="formFooter">
          <a style="color:red" id="warning"></a>
        </div>
      </div>
    </div>
  </body>
</html>