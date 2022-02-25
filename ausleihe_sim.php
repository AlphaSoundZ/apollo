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
    <p>
        User_id: 12<br>
        Name: Peter Wedemann<br>
        Klasse: Lehrer (1)<br>
        Rfid_code: 0000.568845734567 (11)<br>
        Gerät: 6815.510528922222 (5)<br>
        Gerät 2: a8fg419z (24)<br>
    </p>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.568845734567&rfid2=6815.510528922222" target="_blank"><button>Lehrer Ausleihe</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.568845734567&rfid2=a8fg419z" target="_blank"><button>Lehrer Ausleihe Gerät 2</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=6815.510528922222" target="_blank"><button>Lehrer Rückgabe</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=a8fg419z" target="_blank"><button>Lehrer Rückgabe Gerät 2</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.568845734567" target="_blank"><button>Lehrer Info</button></a><br>
    <p>
    11
Emil
Test
2
10
        User_id: 11<br>
        Name: Emil Test<br>
        Klasse: 5a (2)<br>
        Rfid_code: 0000.000000000003 (10)<br>
        Gerät: 6815.510528921625 (1)<br>
    </p>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.000000000003&rfid2=6815.510528921625" target="_blank"><button>Schüler Ausleihe</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.000000000003&rfid2=6815.123498761543" target="_blank"><button>Schüler Ausleihe 2</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=6815.510528921625" target="_blank"><button>Schüler Rückgabe</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=6815.123498761543" target="_blank"><button>Schüler Rückgabe 2</button></a>
    <a href="http://localhost/apollo/ausleihe/ausleihe_request.php?rfid1=0000.000000000003" target="_blank"><button>Schüler Info</button></a>
</body>
</html>
<?php


?>