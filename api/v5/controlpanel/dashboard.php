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

        document.body.innerHTML = "Hello " + userdata.firstname + " " + userdata.lastname + "!";
    }
</script>