<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ausleihe sim</title>
</head>
<body>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php" target="_blank"><button>ausleihe</button></a><br>
    <form action="ausleihe_sim.php" method="POST" target="_blank">
    <p>
        User_id: 12<br>
        Name: Peter Wedemann<br>
        Klasse: Lehrer (1)<br>
        Rfid_code: 0000.568845734567 (11)<br>
        Gerät: 6815.510528922222 (5)<br>
        Gerät 2: a8fg419z (24)<br>
    </p>
        <input type="submit" name="iframesrc1" value="Lehrer Ausleihe"></input>
        <input type="submit" name="iframesrc2" value="Lehrer Ausleihe Gerät 2"></input>
        <input type="submit" name="iframesrc3" value="Lehrer Rückgabe"></input>
        <input type="submit" name="iframesrc4" value="Lehrer Rückgabe Gerät 2"></input>
        <input type="submit" name="iframesrc5" value="Lehrer Info"></input>
        <p>
            User_id: 11<br>
            Name: Emil Test<br>
            Klasse: 5a (2)<br>
            Rfid_code: 0000.000000000003 (10)<br>
            Gerät: 6815.510528921625 (1)<br>
        </p>
        <input type="submit" name="iframesrc6" value="Schüler Ausleihe"></input>
        <input type="submit" name="iframesrc7" value="Schüler Ausleihe 2"></input>
        <input type="submit" name="iframesrc8" value="Schüler Rückgabe"></input>
        <input type="submit" name="iframesrc9" value="Schüler Rückgabe 2"></input>
        <input type="submit" name="iframesrc10" value="Schüler Info"></input>
    </form><br>
    <?php 
    switch (true) {
        case isset($_POST["iframesrc1"]):
            $iframesrc = 'rfid1=0000.568845734567&rfid2=6815.510528922222';
            break;
        case isset($_POST["iframesrc2"]):
            $iframesrc = 'rfid1=0000.568845734567&rfid2=a8fg419z';
            break;
        case isset($_POST["iframesrc3"]):
            $iframesrc = 'rfid1=6815.510528922222';
            break;
        case isset($_POST["iframesrc4"]):
            $iframesrc = 'rfid1=a8fg419z';
            break;
        case isset($_POST["iframesrc5"]):
            $iframesrc = 'rfid1=0000.568845734567';
            break;
        case isset($_POST["iframesrc6"]):
            $iframesrc = 'rfid1=0000.000000000003&rfid2=6815.510528921625';
            break;
        case isset($_POST["iframesrc7"]):
            $iframesrc = 'rfid1=0000.000000000003&rfid2=6815.123498761543';
            break;
        case isset($_POST["iframesrc8"]):
            $iframesrc = 'rfid1=6815.510528921625';
            break;
        case isset($_POST["iframesrc9"]):
            $iframesrc = 'rfid1=6815.123498761543';
            break;
        case isset($_POST["iframesrc10"]):
            $iframesrc = 'rfid1=0000.000000000003';
            break;
        default:
            $iframesrc = false;
            break;
    }
    if ($iframesrc) { ?><iframe id="frame" width="100%" height="200px" src="http://localhost/apollo/ausleihe/ausleihe_request.php?<?php echo $iframesrc?>"></iframe><?php }?>
</body>
</html>