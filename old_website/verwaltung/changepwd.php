
<?php
// check for session
session_start();
require 'config.php';
session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <form action="update_pwd.php" method="post" onsubmit="validateForm(event);">
        <input type="password" id="currentPwd" placeholder="Current password" required><br>
        <input type="password" id="newPwd" placeholder="New password" required><br>
        <input type="password" id="CnewPwd" placeholder="Confirm new password" required><br>
		<input type="button">
        <input type="submit">
    </form>
    <div name="warningMsg">
        <!-- Warning message content -->
    </div>
</body>
</html>
<script>
    function validateForm(event)
    {
        event.preventDefault();
        // get input elements
        currentPwd = document.getElementById("currentPwd");
        newPwd = document.getElementById("newPwd");
        CnewPwd = document.getElementById("CnewPwd");
        if (newPwd.value === CnewPwd.value) // Does the confirm have the same value as the new pwd?
        {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "changePwd_update.php", true);
            loading('start');
            xhttp.onreadystatechange = function() 
            {
                if (this.readyState == 4 && this.status == 200)
                {
                    var response = this.responseText;
                    js_response = json_decode(response);
                    console.log(js_response);
                }
            };
            xhttp.setRequestHeader("Content-Type", "application/json");
            xhttp.send(JSON.stringify(data));
        }
        
    }
</script>