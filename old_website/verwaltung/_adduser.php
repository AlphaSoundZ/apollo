
<?php
session_start();
require 'config.php';
session();
global $rdata, $data, $pdo;
$rdata = json_decode(file_get_contents("php://input"));
try {
    $task = $rdata->task;
    $task();
} catch (Exception $e) {
    $data['message'] = $e->getMessage();
    message('');
    echo json_encode($rdata);
	exit;
}

function _adduser() 
{
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
                <input type="text" class="main_textfield" name="vorname" placeholder="Vorname" onblur="checkText('input.vorname')" onfocus="updateerrormsg();" id="input.vorname" onkeyup="saveValue(this);"></input><br><br><br><br>
                <input type="text" class="main_textfield" name="nachname" placeholder="Nachname" onblur="checkText('input.nachname')" onfocus="updateerrormsg();" id="input.nachname" onkeyup="saveValue(this);"></input><br><br><br>
                <select id="input.klasse" onblur="checkText('input.klasse')" onfocus="updateerrormsg();" onchange="saveValue(this);">
                    <?php
                    $klassen = "SELECT * FROM klassen ORDER BY id";
                    echo "<option value='nothing_selected'>Klasse</option>";
                    foreach ($pdo->query($klassen) as $row) {
                    echo "<option value='".$row['id']."'>".$row['klassen_name']."</option>";
                    } ?>
                </select>
                <br>
                <input type="text" class="main_textfield" name="rfid_code" placeholder="RFID Code" id="input.rfid_code" onblur="checkText('input.rfid_code')" onfocus="updateerrormsg();" onkeyup="saveValue(this);"></input><br><br><br> <!-- Idee: Man klickt in das Feld und dann hält man das Gerät, welches eingescannt werden soll an das Lesegerät. Sobald das Lesegerät den Code hat, wird er einfach kopiert und anschließend automatisch eingefügt. Dann kann man sich das mit einer extra seite zum scannen etc. sparen und es spart deutlich zeit, weil man nicht jedesmal auf eine seite geleitet wird, sondern es einache eingefügt wird. -->
                <br>
                <p class="main_warningmsg" id="warning"></p>
                <input type="submit" class="main_submit" value="Add" id="input.submit" onclick="validateForm(event);"></input>
            </form>
        </body>
    </html> 
    <?php
}

/*
$vorname = 'Paull bruh 420';
$nachname = 'Peter';
$klasse = '2';
$rfid_code = 'testrfidcode';
*/

function _push() {
    global $rdata;
    $vorname = $rdata->vorname;
    $nachname = $rdata->nachname;
    $klasse = $rdata->klasse;
    $rfid_code = $rdata->rfid_code;
    $adduser = new adduser($vorname, $nachname, $klasse, $rfid_code);
    $check = $adduser->prepare();
    echo $check["response"]["user"]["message"]." ".$check["response"]["usercard"]["message"]." ";
    $response = $adduser->execute();
    echo ($response) ? "Anfrage wurde erfolgreich bearbeitet!" : "Bei der Anfrage ist etwas schiefgelaufen!";
    /*echo $check["message"];
    if (in_array("test", $adduser->data)) 
    {
        $adduser->execute();
    }*/
}

class adduser 
{
    function __construct($firstname, $lastname, $class, $rfid_code) 
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->class = $class;
        $this->rfid_code = $rfid_code;
        $this->data = [];
    }
    function prepare()
    {
        global $pdo, $usercardtype;
        // check User
        $selectU = "SELECT * FROM user LEFT JOIN klassen on klassen.id = user.klasse WHERE vorname = :firstname AND name = :lastname";
        $stmt = $pdo->prepare($selectU);
        $stmt->execute(['firstname' => $this->firstname, 'lastname' => $this->lastname]);
        $userexists = $stmt->fetch();
        if ($userexists)
        {
            $selectU .= " AND klassen.id = :class";
            $stmt = $pdo->prepare($selectU);
            $stmt->execute(['firstname' => $this->firstname, 'lastname' => $this->lastname, 'class' => $this->class]);
            $userexistsOnClass = $stmt->fetch();
            if ($userexistsOnClass) 
            {
                if ($userexistsOnClass["rfid_code"])
                {
                    $this->message(0, 2);
                }
                else
                {
                    $this->message(0, 1);
                }
            }
            else
            {
                $this->message(0, 4);
            }
        }
        else {$this->message(0, 0);}
        
        // check Usercard:
        $selectUC = "SELECT *FROM rfid_devices WHERE rfid_code = :rfid_code";
        $stmt = $pdo->prepare($selectUC);
        $stmt->execute(array('rfid_code' => $this->rfid_code));
        $usercardexists = $stmt->fetch();
        if ($usercardexists)
        {
            if ($usercardexists["device_type"] == $usercardtype)
            {
                $selectUC = "SELECT * FROM user WHERE rfid_code = :rfid_code";
                $stmt = $pdo->prepare($selectUC);
                $stmt->execute(array('rfid_code' => $usercardexists["device_id"]));
                $check_assignment = $stmt->fetch();
                if ($check_assignment)
                {
                    $this->message(1, 2);
                }
                else
                {
                    $this->message(1, 1);
                }
            }
            else
            {
                $this->message(1, 4);
            }
        }
        else
        {
            $this->message(1, 0);
        }

        //  Check if assignement of User with Usercard already happened:
        if (isset($usercardexists) && isset($userexistsOnClass) && $usercardexists["device_id"] == $userexistsOnClass["rfid_code"])
        {
            $this->message(0, 5);
            $this->message(1, 5);
        }

        return $this->data;
    }
    function execute()
    {
        global $pdo;
        if (!empty($this->data["response"]["user"]) AND !empty($this->data["response"]["usercard"]) AND !array_diff([$this->data["response"]["user"]["id"], $this->data["response"]["usercard"]["id"]], [0, 1]))
        {
            $userDataID = $this->data["response"]["user"]["id"];
            $usercardDataID = $this->data["response"]["usercard"]["id"];
            if ($usercardDataID == 0)
            {
                $this->createUsercard();
            }
            if ($userDataID == 0)
            {
                $this->createUser();        
            }
            // Zuweisung User und Usercard:
            $update = "UPDATE user SET rfid_code = :rfid_code WHERE user_id = :user_id";
            $stmt = $pdo->prepare($update);
            $stmt->execute(array('rfid_code' => $this->fetch()["stmtUC"][0]["device_id"], 'user_id' => $this->fetch()["stmtU"][0]["user_id"]));
            $stmt->fetch();
            // check for success:
            if ($stmt->rowCount() > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    protected function createUser()
    {
        global $pdo;
        // create User:
        $insert = "INSERT INTO user (vorname, name, klasse) VALUES (:firstname, :lastname, :class)";
        $stmt = $pdo->prepare($insert);
        $stmt->execute(array('firstname' => $this->firstname, 'lastname' => $this->lastname, 'class' => $this->class));

        // check for success:
        if ($stmt->rowCount())
        {
            $this->message(0, 0);
        }
        else
        {
            $this->message(0, 3);
        }
    }
    protected function createUsercard()
    {
        global $pdo, $usercardtype;
        // create Usercard:
        $insert = "INSERT INTO rfid_devices (device_type, rfid_code) VALUES (:device_type, :rfid_code)";
        $stmt = $pdo->prepare($insert);
        $stmt->execute(array('device_type' => $usercardtype, 'rfid_code' => $this->rfid_code));

        // check for success:
        if ($stmt->rowCount())
        {
            $this->message(1, 0);
        }
        else
        {
            $this->message(1, 3);
        }
    }
    protected function fetch()
    {
        global $pdo;
        $stmtU = "SELECT * FROM user WHERE vorname = :firstname AND name = :lastname";
        $stmtU = $pdo->prepare($stmtU);
        $stmtU->execute(array('firstname' => $this->firstname, 'lastname' => $this->lastname));
        
        $stmtUC = "SELECT * FROM rfid_devices WHERE rfid_code = :rfid_code";
        $stmtUC = $pdo->prepare($stmtUC);
        $stmtUC->execute(array('rfid_code' => $this->rfid_code));
        $response["stmtU"] = [$stmtU->fetch()];
        $response["stmtUC"] = [$stmtUC->fetch()];
        return $response;
    }
    protected function message($param1, $param2)
    { // $param1 is User (0) or Usercard (1), $param2 is the case
        $caseMsg = array(
            0 => array( 0 => "User wird erstellt",
                        1 => "User wird zugewiesen",
                        2 => "User ist bereits einer Usercard zugewiesen",
                        3 => "User erstellen fehlgeschlagen",
                        4 => "User existiert bereits. Die Klasse stimmt nicht überein!",
                        5 => "Dem User wurde bereits die Usercard zugewiesen."),
            1 => array( 0 => "Usercard wird erstellt",
                        1 => "Usercard wird zugewiesen",
                        2 => "Usercard wurde bereits einem User zugewiesen",
                        3 => "Usercard erstellen fehlgeschlagen",
                        4 => "Es handelt sich nicht um eine Usercard. Gerätetyp falsch!",
                        5 => "Die Usercard wurde bereits dem User zugewiesen.")
        );
        $this->data["response"][$a = ($param1 == 0) ? "user" : (($param1 == 1) ? "usercard" : "error")]["id"] = $param2;
        $this->data["response"][$a]["message"] = $caseMsg[$param1][$param2];
    }
}
?>