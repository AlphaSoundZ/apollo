<?php
session_start();
require 'config.php';
global $rdata, $data, $pdo;

global $pdo;
$klassen_auswahl = "Klasse";
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add User</title> 
    </head>
    <body>
        <p class="main_text">Add user</p>
        <form>
            <input type="text" class="main_textfield" name="vorname" placeholder="Vorname" onblur="checkText('input.vorname')" onfocus="updateerrormsg();" id="input.vorname"></input><br><br><br><br>
            <input type="text" class="main_textfield" name="nachname" placeholder="Nachname" onblur="checkText('input.nachname')" onfocus="updateerrormsg();" id="input.nachname"></input><br><br><br>
            <select id="input.klasse" onblur="checkText('input.klasse')" onfocus="updateerrormsg();">
                <?php
                $klassen = "SELECT * FROM klassen ORDER BY id";
                echo "<option value='nothing_selected'>Klasse</option>";
                foreach ($pdo->query($klassen) as $row) {
                echo "<option value='".$row['id']."'>".$row['klassen_name']."</option>";
                } ?>
            </select>
            <br>
            <input type="text" class="main_textfield" name="rfid_code" placeholder="RFID Code" id="input.rfid_code" onblur="checkText('input.rfid_code')" onfocus="updateerrormsg();"></input><br><br><br> <!-- Idee: Man klickt in das Feld und dann hält man das Gerät, welches eingescannt werden soll an das Lesegerät. Sobald das Lesegerät den Code hat, wird er einfach kopiert und anschließend automatisch eingefügt. Dann kann man sich das mit einer extra seite zum scannen etc. sparen und es spart deutlich zeit, weil man nicht jedesmal auf eine seite geleitet wird, sondern es einache eingefügt wird. -->
            <br>
            <p class="main_warningmsg" id="warning"></p>
            <input type="submit" class="main_submit" value="Add" id="input.submit" onclick="submit(event);"></input>
        </form>
    </body>
</html> 
<?php
?>
<script type="text/javascript">

    function submit()
    {
        event.preventDefault();
        if (checkTextfields())
        {
            var data = {"firstname" : document.getElementById("input.vorname"), "lastname" : document.getElementById("input.nachname"), "class" : document.getElementById("input.klasse"), "rfid_code" : "input.rfid_code"};
            var ajax = new XMLHttpRequest();
            ajax.open("POST", file, true);
            ajax.onreadystatechange = function()
            {
                if (this.readyState == 4 && this.status == 200)
                {
                var response = this.responseText;
                }
            };
            ajax.setRequestHeader("Content-Type", "application/json");
            ajax.send(JSON.stringify(data))
        }
    }
    function checkTextfields() {
        return True;
    }
</script>