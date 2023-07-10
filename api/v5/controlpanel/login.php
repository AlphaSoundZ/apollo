<?php
require_once 'config.php';

header("Content-Type: text/html; charset=UTF-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="" method="post" onsubmit="return validate(event);">
        <input type="text" name="username" placeholder="Username" id="username">
        <input type="password" name="password" placeholder="Password" id="password">
        <input type="submit" value="Login">
    </form>
</body>
</html>

<script>
    logout();
    
    function validate(ev) {
        ev.preventDefault();
        
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;

        data = {
            "username": username,
            "password": password
        };

        // request token from api in background
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/api/v5/token/authorize", true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));

        xhr.onreadystatechange = function() {
            if (this.readyState == 4)
            {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    var token = response.jwt;
                    //var expires = response.expires;
                    var user = response.user;
    
                    // save token in local storage
                    localStorage.setItem("token", token);
                    //localStorage.setItem("expires", expires);
                    localStorage.setItem("user", JSON.stringify(user));
    
                    // redirect to control panel
                    window.location.href = "/api/v5/dashboard";
                }
                else {
                    // alert response message
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                }
            }
        };
        
        return false;
    }

    function logout() {
        localStorage.removeItem("token");
        // localStorage.removeItem("expires");
        localStorage.removeItem("user");
    }

</script>