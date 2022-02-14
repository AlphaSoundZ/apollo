<?php
require 'config.php';
function convertSearch($data) {
  global $columnnames, $activerows, $search, $pdo, $columnnamesdb, $rfid_code_len, $dataselect;
  // find params;
  $classfilter = false;
  $searchsplit = str_split(strtolower($search), 1);
  $searchcount = count($searchsplit);
  $activerows = array();
  foreach ($pdo->query($dataselect) as $row) {
    if (strtolower($row['klassen_name']) == strtolower($search)) {
      if ($classfilter == false) {
        $activerows = array();
      }
      $activerows[$row['user_id']] = 100;
      $classfilter = true;
    }
    else {
      for ($columnid=0; $columnid < 3; $columnid++) {
        if ($columnid==0) {$dbdatasplit = str_split(strtolower($row['vorname']), 1);}
        else if ($columnid==1) {$dbdatasplit = str_split(strtolower($row['name']), 1);}
        else if ($columnid==2) {$dbdatasplit = str_split(strtolower($row['vorname'])." ".strtolower($row['name']), 1);}
        $dbdatacount = count($dbdatasplit);
        for($i = 0, $a = 1, $correspondance = 0, $abstand = 0;$i < $searchcount;$i++) {
          if (in_array($searchsplit[$i], $dbdatasplit)) {
            $correspondance++;
            if ((array_search($searchsplit[$i], $dbdatasplit)-$i) < 3 && ($i-array_search($searchsplit[$i], $dbdatasplit)) > -3) {
              if (array_search($searchsplit[$i], $dbdatasplit) > $i) {
                $abstand += array_search($searchsplit[$i], $dbdatasplit)-$i;
                $a++;
              }
              else if (array_search($searchsplit[$i], $dbdatasplit) < $i) {
                $abstand += $i-array_search($searchsplit[$i], $dbdatasplit);
                $a++;
              }
            }
          }
          unset($dbdatasplit[array_search($searchsplit[$i], $dbdatasplit)]);
        }
      }
      if ($classfilter == false) {
        if ($a > 1) {$abstand = $abstand/($a-1);}
        $abstand = $abstand*-0.2+1;
        if ($searchcount < $dbdatacount) {$len = $searchcount/$dbdatacount;}
        else {$len = $dbdatacount/$searchcount;}
        if ($len > 1) {$len = 1-($len-1);}
        /*echo $len.": Len<br>";
        echo $abstand.": Abstand<br>";
        echo $correspondance/$searchcount.": correspondance buchstaben in %/100<br><br><br>";*/
        $average = ceil(($len+$abstand+$correspondance/$searchcount)/3*100);
        if ((str_contains(strtolower($row['vorname']), strtolower($search)) OR str_contains(strtolower($row['name']), strtolower($search))) AND $average != 100) {$average = 99;}
        $activerows[$row['user_id']] = $average;
      }
    }
  }
  $column = 'user.user_id';
  //$activerows['column'] = array_search($column, $columnnamesdb);
  $activerows['column'] = $column;
  if ($classfilter == false) {

    arsort($activerows, SORT_NUMERIC);
  }

  echo "Suchergebnisse (".array_values($activerows)[0]."% Übereinstimmung): ".$search."<br>";
}

?>
