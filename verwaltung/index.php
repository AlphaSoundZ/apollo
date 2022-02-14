<!DOCTYPE html>
<?php
session_start();
session_destroy();
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="bootstrap_style.css" rel="stylesheet" id="bootstrap-css"> <!-- maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css -->
    <!-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> //maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js or bootstrap.js-->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="loading_animation.css">

  </head>
  <body>
    <div class="wrapper fadeInDown">
      <div id="formContent">

        <!-- Icon -->
        <div class="loading-pic" id='loading-pic_id'>
          <div class="fadeIn first" id='picture'>
            <img src="default_logo2.png" id="icon" alt="ght-hh.de" onclick="window.open('https://ght-hh.de', '_blank');"/>
          </div>
        </div>

        <!-- Login Form -->
        <form action="main.php" method="post" onsubmit="validateForm(event);" id="Form">
          <input type="text" id="login" class="fadeIn second" name="username" placeholder="username">
          <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
          <input type="submit" class="fadeIn fourth" value="Log In">
        </form>

        <!-- loading animation -->
        <div id='loading' class="spinner-wrapper">
          <span class="spinner"></span>
        </div>

        <!-- Warning message -->
        <div id="formFooter">
          <a style="color:red" id="warning"></a>
        </div>
      </div>
    </div>
  </body>
</html>
<script type="text/javascript">

function visibility(id, visibility) {
  var element = document.getElementById(id);
  var picture = document.getElementById('loading-pic_id');
  element.style.display = visibility;
  if (visibility == 'block') {
    picture.style.transition = 'opacity 1s';
    picture.style.opacity = '50%';
  }
  if (visibility == 'none') {
    picture.style.transition = 'opacity 0s';
    picture.style.opacity = '100%';
  }
}

function validateForm(event) {
  event.preventDefault();
  var input_username = document.getElementById("login");
  var input_password = document.getElementById("password");
  if (input_username.value == '' || input_username.value.charAt(0) == ' ' || input_password.value == '') {
    document.getElementById("warning").innerHTML = "Fill empty fields!";
  }
  else {
    var data = {task: '_login', username: input_username.value, password: input_password.value};
    var ajax = new XMLHttpRequest();
    ajax.open("POST", "request.php", true);
    ajax.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = this.responseText;
        if (response == 0) {
          visibility('loading', 'none');
          document.getElementById("warning").innerHTML = "Username or password was wrong!";
          document.getElementById("login").value = "";
          document.getElementById("password").value = "";
        }
        else if (response == 1) {
          document.getElementById("Form").submit();
          return true;
        }
      }
    };
    visibility('loading', 'block');
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.send(JSON.stringify(data));
  }
}
</script>

