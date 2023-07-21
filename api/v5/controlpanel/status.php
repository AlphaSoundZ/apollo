<?php
require_once 'config.php';

header("Content-Type: text/html; charset=UTF-8");

global $pdo;

$sql_1 = "SELECT * FROM event LEFT JOIN user ON event.event_user_id = user.user_id 
            LEFT JOIN property_class ON user.user_class = property_class.class_id ORDER BY event.event_id DESC";
$event = $pdo->query($sql_1);
$event = $event->fetchAll();
$sql_2 = "SELECT * FROM devices";
$all = $pdo->query($sql_2);
$all = $all->fetchAll();
$all = count($all);
$number = 0;

$sql_4 = "SELECT * FROM user";
$totalusers = $pdo->query($sql_4);
$totalusers = $totalusers->fetchAll();
$totalusers = count($totalusers);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info</title>
</head>
<body>

<!-- navbar --> 
<div class="topnav">
    <a href="/api/v5/dashboard">Dashboard</a>
    <a class="active" href="/api/v5/status">Status</a>
    <a href="/api/v5/prebook_page">Prebook</a>
    <a href="/api/v5/login">Logout</a>
</div>


<h2>Ausleihsystem Status:</h2>

<table>
  <tr>
    <th>name</th>
    <th>time</th>
    <th>amount</th>
  </tr>
    <?php
        $userontable = array();
        foreach($event as $key => $row)
        {
            echo "<tr>";
            if (is_null($row["event_end"]))
            {
                if (in_array($row["event_user_id"], $userontable) == false OR end($userontable) != $row["event_user_id"])
                {
                    $amount = 0;
                    for ($i = $key; $i < count($event); $i++)
                    {
                        if (is_null($event[$i]["event_end"]))
                        {
                            if ($row["event_user_id"] == $event[$i]["event_user_id"])
                            {
                                $amount++;
                            }
                            else
                            {
                                break;
                            }
                        }
                    }

                    array_push($userontable, $row["event_user_id"]);
                    echo "<th>".$row['user_firstname']." ".$row['user_lastname']."</th>";
                    echo "<th>".date( "H:i / d.m.y", strtotime($row['event_begin']))."</th>";
                    echo "<th>".$amount."</th>";
                }
                $number++;
            }
                echo "</tr>";
        }
    ?>
</table>

<h3>Infos:</h3>

<table>
    <tr>
        <th>devices in use</th>
        <th><?php echo $number; ?></th>
    </tr>
    <tr>
        <th>total devices</th>
        <th><?php echo $all; ?></th>
    </tr>
    <tr>
        <th>total users</th>
        <th><?php echo $totalusers; ?></th>
    </tr>
</table>

<h3>History:</h3>

<table>
    <tr>
        <th>name</th>
        <th>class / teacher</th>
        <th>begin</th>
        <th>end</th>
    </tr>

    <?php
        foreach ($event as $row)
        {
            echo "<tr>";
            echo "<th>".$row['user_firstname']." ".$row['user_lastname']."</th>";
            echo "<th>".$row['class_name']."</th>";
            echo "<th>".date( "H:i / d.m.y", strtotime($row['event_begin']))."</th>";
            echo "<th>".date( "H:i / d.m.y", strtotime($row['event_end']))."</th>";
            echo "</tr>";
        }
    ?>
</table>
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
    }
</script>

<style>
table {
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

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