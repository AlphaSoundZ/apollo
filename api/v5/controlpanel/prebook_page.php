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
        <a href="/api/v5/dashboard">Dashboard</a>
        <a href="/api/v5/status">Status</a>
        <a class="active" href="/api/v5/prebook_page">Prebook</a>
        <a href="/api/v5/login">Logout</a>
    </div>
    <h2>Prebook:</h2>

    <form action="" method="post" onsubmit="return prebook(event);">
        <input type="number" name="amount" id="amount" min="1" placeholder="amount" autocomplete="off">
        <input type="datetime-local" name="date" id="begin" min="<?php echo date("Y-m-d", strtotime("+{$_ENV['MIN_PREBOOK_TIME_DISTANCE']} day"));?>T00:00" max="<?php echo date("Y-m-d", strtotime("+{$_ENV['MAX_PREBOOK_TIME_DISTANCE']} day"));?>T23:59" placeholder="begin" onchange="updateEndTime();" autocomplete="off">
        <input type="datetime-local" name="date" id="end" min="<?php echo date("Y-m-d", strtotime("+{$_ENV['MIN_PREBOOK_TIME_DISTANCE']} day"));?>T00:00" max="<?php echo date("Y-m-d", strtotime("+{$_ENV['MAX_PREBOOK_TIME_DISTANCE']} day"));?>T23:59" placeholder="end" onchange="updateBeginTime();" autocomplete="off">
        <input type="submit" value="Book">
    </form>
</body>
</html>

<script>
    login();

    var token;
    
    function login() {
        token = localStorage.getItem("token");
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
    }

    function prebook (ev) {
        ev.preventDefault();

        
        var amount = document.getElementById("amount").value;
        var begin = document.getElementById("begin").value;
        var end = document.getElementById("end").value;

        // convert times to US Format
        begin = begin.replace("T", " ");
        end = end.replace("T", " ");
        
        console.log(begin);

        data = {
            "begin": begin,
            "end": end,
            "amount": amount
        };

        console.log(data);

        // request token from api in background
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/api/v5/prebook", true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Authorization', 'Bearer ' + token);
        xhr.send(JSON.stringify(data));

        xhr.onreadystatechange = function() {
            if (this.readyState == 4)
            {
                if (this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert("Reservierung erfolgreich!");
                }
                else {
                    // error handling
                    var response = JSON.parse(this.responseText);

                    alert(response.message)
                    console.log(response);
                    highlightErrorFields(response.fields, response.message);
                    
                }
            }
        };
        
        return false;
    }

    function highlightErrorFields (fields = [], message) {
        fields.forEach(field => {
            const el = document.getElementById(field);

            // red border
            el.style.border = "1px solid red";

            // tooltip
            el.setAttribute("title", message);

            // remove tooltip after 3 seconds
            setTimeout(() => {
                el.removeAttribute("title");

                // reset to standard border settings
                el.style.border = "1px solid #333";
            }, 3000);
        });
    }

    function updateEndTime() {
        var begin = document.getElementById("begin").value;
        var end = document.getElementById("end").value;

        var min_duration = <?php echo $_ENV["MIN_BOOKING_DURATION"];?>;
        var max_duration = <?php echo $_ENV["MAX_BOOKING_DURATION"];?>;

        // time diff
        begin = new Date(begin);
        end = new Date(end);
        var diff = end - begin;
        // convert to minutes
        diff = diff / 60000;

        if (isNaN(diff))
        {
            diff = 0;
        }

        if (diff < min_duration) {
            // begin + $_ENV["min_duration"]
            var begin_date = new Date(begin);
            var end_date = new Date(begin_date.getTime() + min_duration * 60000);

            var end_date_string = end_date.getFullYear() + "-" + ("0" + (end_date.getMonth() + 1)).slice(-2) + "-" + ("0" + end_date.getDate()).slice(-2) + "T" + ("0" + end_date.getHours()).slice(-2) + ":" + ("0" + end_date.getMinutes()).slice(-2);

            document.getElementById("end").value = end_date_string;
        }
        else if (diff > max_duration) {
            // begin + $_ENV["max_duration"]
            var begin_date = new Date(begin);
            var end_date = new Date(begin_date.getTime() + max_duration * 60000);

            var end_date_string = end_date.getFullYear() + "-" + ("0" + (end_date.getMonth() + 1)).slice(-2) + "-" + ("0" + end_date.getDate()).slice(-2) + "T" + ("0" + end_date.getHours()).slice(-2) + ":" + ("0" + end_date.getMinutes()).slice(-2);

            document.getElementById("end").value = end_date_string;
        }
    }

    function updateBeginTime() {
        var begin = document.getElementById("begin").value;
        var end = document.getElementById("end").value;

        var min_duration = <?php echo $_ENV["MIN_BOOKING_DURATION"];?>;
        var max_duration = <?php echo $_ENV["MAX_BOOKING_DURATION"];?>;

        // time diff
        begin = new Date(begin);
        end = new Date(end);
        var diff = end - begin;
        // convert to minutes
        diff = diff / 60000;

        if (isNaN(diff))
        {
            diff = 0;
        }

        if (diff < min_duration) {
            // begin + $_ENV["min_duration"]
            var end_date = new Date(end);
            var begin_date = new Date(end_date.getTime() - min_duration * 60000);

            var begin_date_string = begin_date.getFullYear() + "-" + ("0" + (begin_date.getMonth() + 1)).slice(-2) + "-" + ("0" + begin_date.getDate()).slice(-2) + "T" + ("0" + begin_date.getHours()).slice(-2) + ":" + ("0" + begin_date.getMinutes()).slice(-2);

            document.getElementById("begin").value = begin_date_string;
        }
        else if (diff > max_duration) {
            // begin + $_ENV["max_duration"]
            var end_date = new Date(end);
            var begin_date = new Date(end_date.getTime() - max_duration * 60000);

            var begin_date_string = begin_date.getFullYear() + "-" + ("0" + (begin_date.getMonth() + 1)).slice(-2) + "-" + ("0" + begin_date.getDate()).slice(-2) + "T" + ("0" + begin_date.getHours()).slice(-2) + ":" + ("0" + begin_date.getMinutes()).slice(-2);

            document.getElementById("begin").value = begin_date_string;
        }
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

input {
    border: 1px solid #333;
}

body {
  margin: 10px;
  font-family: Arial, Helvetica, sans-serif;
}
</style>