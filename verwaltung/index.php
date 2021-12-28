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
        visibility('loading', 'none');
        var response = this.responseText;
        if (response == 0) {
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

<style>
.loading {
  -webkit-animation:fadein 1s;
     -moz-animation:fadein 1s;
       -o-animation:fadein 1s;
          animation:fadein 1s;
}
@-moz-keyframes fadein {
  from {opacity:0}
  to {opacity:1}
}
@-webkit-keyframes fadein {
  from {opacity:0}
  to {opacity:1}
}
@-o-keyframes fadein {
  from {opacity:0}
  to {opacity:1}
}
@keyframes fadein {
  from {opacity:0}
  to {opacity:1}
}

.spinner-wrapper {
  min-width:100%;
  min-height:100%;
  height:100%;
  top:0;
  left:0;
  position:absolute;
  z-index:300;
  display:none;
}

.spinner {
  margin:0 auto;
  display:block;
  position:relative;
  left:0%;
  top:16%;
  border:25px solid #3e6c7d;
  width:1px;
  height:1px;
  border-left-color:transparent;
  border-right-color:transparent;
  -webkit-border-radius:50px;
     -moz-border-radius:50px;
          border-radius:50px;
  -webkit-animation:spin 1.5s infinite;
     -moz-animation:spin 1.5s infinite;
          animation:spin 1.5s infinite;
}

.loading-pic {
  opacity:100%;
}

@-webkit-keyframes spin {
  0%,100% {-webkit-transform:rotate(0deg) scale(0.6)}
  50%     {-webkit-transform:rotate(720deg) scale(1)}
}

@-moz-keyframes spin  {
  0%,100% {-moz-transform:rotate(0deg) scale(0.6)}
  50%     {-moz-transform:rotate(720deg) scale(1)}
}
@-o-keyframes spin  {
  0%,100% {-o-transform:rotate(0deg) scale(0.6)}
  50%     {-o-transform:rotate(720deg) scale(1)}
}
@keyframes spin  {
  0%,100% {transform:rotate(0deg) scale(0.6)}
  50%     {transform:rotate(720deg) scale(1)}
}
</style>
