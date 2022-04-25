<!DOCTYPE html>
<?php
require 'config.php';
global $pdo;

$sql_1 = "SELECT * FROM event LEFT JOIN user ON event.user = user.user_id";
$event = $pdo->query($sql_1);
$event = $event->fetchAll();
$sql_2 = "SELECT * FROM rfid_devices WHERE device_type != 2";
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
            if (is_null($row["end"]))
            {
                if (in_array($row["user"], $userontable) == false OR end($userontable) != $row["user"])
                {
                    $amount = 0;
                    for ($i = $key; $i < count($event); $i++)
                    {
                        if (is_null($event[$i]["end"]))
                        {
                            if ($row["user"] == $event[$i]["user"])
                            {
                                $amount++;
                            }
                            else
                            {
                                break;
                            }
                        }
                    }

                    array_push($userontable, $row["user"]);
                    echo "<th>".$row['vorname']." ".$row['name']."</th>";
                    echo "<th>".date( "h:i d/m/Y", strtotime($row['begin']))."</th>";
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
</body>
</html>

<style>
table {
  font-family: arial, sans-serif;
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
</style>