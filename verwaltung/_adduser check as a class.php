<?php
session_start();
require 'config.php';
global $rdata;
$rdata = json_decode(file_get_contents("php://input"));
// Start desired function
try {
    $task = $rdata->task;
    $task();
} catch (Exception $e) {
    $data['message'] = $e->getMessage();
    message('');
    echo json_encode($rdata);
	exit;
}

function _push() {
    global $vorname, $nachname, $klasse, $rfid_code, $rdata;
    $vorname = $rdata->vorname;
    $nachname = $rdata->nachname;
    $klasse = $rdata->klasse;
    $rfid_code = $rdata->rfid_code;
    $adduser = new _adduser($vorname, $nachname, $klasse, $rfid_code);
    $bruh = $adduser->run();
}

function _adduser() {
    global $data, $pdo;
    $klassen_auswahl = "Klasse";
  ?>
    <!DOCTYPE html>
    <html>
    <head>
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
        }
      ?>
    </select>
    <br>
    <input type="text" class="main_textfield" name="rfid_code" placeholder="RFID Code" id="input.rfid_code" onblur="checkText('input.rfid_code')" onfocus="updateerrormsg();"></input><br><br><br> <!-- Idee: Man klickt in das Feld und dann hält man das Gerät, welches eingescannt werden soll an das Lesegerät. Sobald das Lesegerät den Code hat, wird er einfach kopiert und anschließend automatisch eingefügt. Dann kann man sich das mit einer extra seite zum scannen etc. sparen und es spart deutlich zeit, weil man nicht jedesmal auf eine seite geleitet wird, sondern es einache eingefügt wird. -->
    <br>
    <div><p class="main_warningmsg" id="warning"></p>
    <input type="submit" class="main_submit" value="Add" id="input.submit" onclick="validateForm(event);"></input>
    </form>
    </body>
    </html>
  <?php }



class _adduser {
    function __construct($vorname, $name, $klasse, $rfid_code) {
        $this->vorname = $vorname;
        $this->nachname = $name;
        $this->klasse = $klasse;
        $this->rfid_code = $rfid_code;
        $this->data["message"] = NULL;
        $this->data["response"] = NULL;
    }
    function run() {
        $check = new check();
        $check->checknow();
        //$this->check();

        if ($check->data["response"] < 2) {
            if ($check->data["response"] == '0') {
                $this->createUsercard();
            }
            $this->createUser();
        }
        echo "message (".$check->data["response"]."): ".$check->data["message"];
    }
    protected function createUser() {
        global $pdo;
        // Create the user
        $check = new check();
        $check->checknow();
        $stmt = $pdo->prepare("INSERT INTO user (vorname, name, klasse, rfid_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->vorname, $this->nachname, $this->klasse, $check->usercard['device_id']]);
        //$this->check();
        $check->checknow();
        if ($this->data["response"] == '2') {
            // Success
        }
        elseif($this->data["response"] == ('0' OR '1')) {
            $this->message(6);
        }
    }
    protected function createUsercard() {
        global $usercardtype, $pdo;
        // Create the usercard
        $stmt = $pdo->prepare("INSERT INTO rfid_devices (device_type, rfid_code) VALUES (?, ?)");
        $stmt->execute([$usercardtype, $this->rfid_code]);
        //$this->check();
        $check = new check();
        $check->checknow();
        if ($check->data["response"] == '1') {
            // Success
        }
        else {
            $this->message(5);
        }
    }
    protected function message($messageId) {
        switch($messageId) {
            case 0:
                $this->data['message'] = "User und Usercard erstellt. ";
                break;
            case 1:
                $this->data['message'] = "Usercard existiert bereits und wurde zugewiesen. ";
                break;
            case 2:
                $this->data['message'] = "User existiert schon. ";
                break;
            case 3:
                //$this->data['message'] = "Usercard ist bereits ".$this->check->rfid_assignedUser['vorname']." ".$this->check->rfid_assignedUser['name']." zugewiesen. ";
                $this->data['message'] = "Usercard ist bereits zugewiesen. ";
                break;
            case 4:
                $this->data['message'] = "RFID Code gehört zu einem Device. ";
                break;
            case 5:
                $this->data['message'] = "Usercard erstellen fehlgeschlagen. ";
                break;
            case 6:
                $this->data['message'] = "User erstellen fehlgeschlagen. ";
                break;
            default:
               $this->data['message'] = "Unexpected Error ";
               $this->data['response'] = '8';
        }
    }
}

class check extends _adduser {
    function __construct() {
        global $vorname, $nachname, $klasse, $rfid_code;
        parent::__construct($vorname, $nachname, $klasse, $rfid_code);
    }
    function checknow() {
        global $pdo, $usercardtype;
        // check if user exists:
        $stmt = $pdo->prepare("SELECT * FROM user WHERE vorname = ? AND name = ?");
        $stmt->execute([$this->vorname, $this->nachname]);
        $user_exists = $stmt->fetch();
        if ($user_exists) {
            $this->data['response'] = 2;
        }
        else {
            $stmt = $pdo->prepare("SELECT * FROM rfid_devices WHERE rfid_code = ?");
            $stmt->execute([$this->rfid_code]);
            $this->usercard = $stmt->fetch();
            if ($this->usercard) {
                if ($this->usercard['device_type'] == $usercardtype) {
                    $stmt = $pdo->prepare("SELECT * FROM user WHERE rfid_code = ?");
                    $stmt->execute([$this->usercard['device_id']]);
                    $rfid_assignedUser = $stmt->fetch();
                    if ($rfid_assignedUser) {
                        $this->data['response'] = 3;
                    }
                    else {
                        $this->data['response'] = 1;
                    }}
                else {
                    $this->data['response'] = 4;
                }}
            else {
                $this->data['response'] = 0;
            }
        }
        _adduser::message($this->data["response"]);
    }
}
?>