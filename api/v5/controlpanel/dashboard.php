<?php

header("Content-Type: text/html; charset=UTF-8");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control Panel</title>
</head>
<body>
    <!-- navbar --> 
    <div class="topnav">
        <a class="active" href="/api/v5/dashboard">Dashboard</a>
        <a href="/api/v5/status">Status</a>
        <a href="/api/v5/prebook_page">Prebook</a>
        <a href="/api/v5/login">Logout</a>
    </div>
    <h1 id="greetings">Hallo Guest</h1>
</body>
</html>

<script>
    login();
    
    function login() {
        var token = localStorage.getItem("token");
        var expires = localStorage.getItem("expires");
        var user = localStorage.getItem("user");

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/api/v5/token/validate", true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        xhr.send();

        xhr.onreadystatechange = function() {
            if (this.readyState == 4)
            {
                if (!this.status == 200) {
                    window.location.href = "/api/v5/login";
                }
            }
        };

        if (token == null || user == null) { //  || expires == null
            window.location.href = "/api/v5/login";
        }
        /*
        var now = new Date();
        var expires = new Date(expires);

        if (now > expires) {
            window.location.href = "/login";
        }
        */

        const userdata = JSON.parse(user)

        const user_field = document.getElementById("greetings")

        user_field.innerHTML = "Hallo " + userdata.firstname + " " + userdata.lastname + "!"
    }
</script>

<style>
    /* Add a black background color to the top navigation */
.topnav {
  background-color: #333;
  overflow: hidden;
}

/* Style the links inside the navigation bar */
.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

/* Change the color of links on hover */
.topnav a:hover {
  background-color: #ddd;
  color: black;
}

/* Add a color to the active/current link */
.topnav a.active {
  background-color: #04AA6D;
  color: white;
}

body {
  margin: 10px;
  font-family: Arial, Helvetica, sans-serif;
}
</style>