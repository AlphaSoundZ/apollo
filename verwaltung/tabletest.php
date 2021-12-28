<?php
require 'config.php';
require 'searchfunction.php';
//$task = '_allusers';
// Aktivieren, wenn Ajax request benutzt wird:
$data = json_decode(file_get_contents("php://input"));
$task = $data->task;
$search = $data->search;


if (!empty($search) && $search[0] == "%") {
  $dataselect = substr($search, 1);
  runselect();
  // $columnnames = array(columns...);
  // $columncount = count($columnnames);
  main();
}
else {
  try {
    $task();
  } catch (Exception $e) {
    exit;
}}

// task{}
// default function for table:
function _table() {
  $dataselect = "SELECT * FROM TABLE";
  runselect();
  $columnnames = array('column 1', 'column 2', 'column 3');
  $columncount = count($columnnames);
  main();
}



function _allusers() {
  global $columnnames, $pdo, $data, $columncount, $table, $tablelen, $activerows, $dataselect, $search;
  $dataselect = "SELECT user.user_id, user.vorname, user.name, klassen.klassen_name, user.rfid_code AS user_rfid_code, user.rfid_device_id, rfid_devices.device_id, rfid_devices.rfid_code FROM user LEFT JOIN klassen ON klassen.id = user.klasse LEFT JOIN rfid_devices on rfid_devices.device_id = user.rfid_code";
  runselect();
  global $task;
  $columnnames = array('Id' , 'Vorname' , 'Nachname' , 'Klasse' , 'RFID Code' , 'Device ID');
  $columncount = count($columnnames);
  main();
}

function _alldevices() {
  global $columnnames, $pdo, $data, $columncount, $table, $tablelen, $activerows, $dataselect, $search;
  $dataselect = "SELECT user.user_id, user.vorname, user.name, klassen.klassen_name, user.rfid_code AS user_rfid_code, user.rfid_device_id, rfid_devices.device_id, rfid_devices.rfid_code FROM user LEFT JOIN klassen ON klassen.id = user.klasse LEFT JOIN rfid_devices on rfid_devices.device_id = user.rfid_code";
  runselect();
  global $task;
  $columnnames = array('Id' , 'Vorname' , 'Nachname' , 'Klasse' , 'RFID Code' , 'Device ID');
  $columncount = count($columnnames);
  main();
}

function runselect() {
  global $dataselect, $table, $comlumntop, $columnnames, $columncount, $tablelen, $pdo, $data, $columnnamesdb;
  $select = $pdo->query($dataselect);
  $table = $select->fetchAll(PDO::FETCH_NUM);
  $columntop = $pdo->query($dataselect);
  $columnnamesdb = array_keys($columntop->fetch(PDO::FETCH_ASSOC));
  $columnnames = $columnnamesdb;
  $columncount = count($columnnamesdb);
  $tablelen = count($table);
  replaceZero();
}

function replaceZero() {
  global $table, $tablelen, $columncount;
  for ($i=0; $i < $tablelen; $i++) {
    for ($n=0; $n < $columncount; $n++) {
      if ($table[$i][$n] != 0 AND $table[$i][$n] != '') {
        //array_push($table[$i], '');
      }
      else {
        $table[$i][$n] = '-';
}}}}

function main() {
  global $columnnames, $pdo, $data, $trow, $tablelen, $columncount, $table, $activerows, $search, $dataselect, $columnnamesdb;
  if (!empty($search) and $search[0] != "%") {
    convertSearch($search);
  }
  ?>
  <form id="info" action="info.php" method="post"></form>
  <table class="content-table">
    <thead>
      <tr>
        <?php
        foreach ($columnnames as $value) {
          ?><th><?php echo $value ?></th><?php
        }
        ?>
      </tr>
    </thead>
    <tbody>
      <?php
        if (!empty($search) and $search[0] != "%" and array_key_exists('column', $activerows)) {
          //$loc = $columnnamesdb[$activerows['column']];
          $loc = $activerows['column'];
          for ($n=0, $e=0; $n < (count($activerows)-1); $n++) {
            ?><tr class="active-row"><?php
            if ($activerows[array_keys($activerows)[$n]] < 70) {break;}
            $selectsearch = $dataselect." WHERE ".$loc." = ?";
            $sth = $pdo->prepare($selectsearch);
            $sth->execute(array(array_keys($activerows)[$n]));
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            for ($i=0; $i < $columncount; $i++) {
              if ($result[0][$i] == 0) {$result[0][$i] = '-';}
              ?><th><?php echo $result[0][$i] ?></th><?php
            }
            ?></tr><?php
          }
        }

        for ($n=0; $n < $tablelen; $n++) {
          //in_array($table[$n][$columnnamesdb[$activerows['column']]], $activerows)
          if (true) {
            ?><tr><?php
            for ($i=0; $i < $columncount; $i++) {
              if ($table[$n][$i] == 0) {$table[$i][$n] = '-';}
              ?><th><?php echo $table[$n][$i] ?></th><?php
            }
          }
          ?></tr><?php
        }
      ?>
    </tbody>
  </table>
  <?php
}

?>

<style>
* {
  font-family: sans-serif; /* Change your font family */
}

.content-table {
  border-collapse: collapse;
  margin: 25px 0;
  font-size: 0.9em;
  min-width: 400px;
  border-radius: 5px 5px 0 0;
  overflow: hidden;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);

}

.content-table thead tr {
  background-color: #39ace7;
  color: #ffffff;
  text-align: left;
  font-weight: bold;

}

.content-table th,
.content-table td {
  padding: 12px 15px;

}

.content-table tbody tr {
  border-bottom: 1px solid #dddddd;

}

.content-table tbody tr:nth-of-type(even) {
  background-color: #f3f3f3;

}

.content-table tbody tr:last-of-type {
  border-bottom: 2px solid #39ace7;

}

.content-table tbody tr.active-row {
  font-weight: bold;
  color: #39ace7;

}
</style>
