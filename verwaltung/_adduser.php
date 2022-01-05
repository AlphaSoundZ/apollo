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
    function check() {
        global $pdo, $usercardtype, $rfid_assigned;
        $check_response = [];
        // check if user exists:
        $stmt = $pdo->prepare("SELECT * FROM user WHERE vorname = ? AND name = ?");
        $stmt->execute([$this->vorname, $this->nachname]);
        $user_exists = $stmt->fetch();
        if ($user_exists) {
            $check_response["createUser"] = false;
            $check_response["messageId"] = 2;
        }
        else {
            $check_response["createUser"] = true;
            $stmt = $pdo->prepare("SELECT * FROM rfid_devices WHERE rfid_code = ?");
            $stmt->execute([$this->rfid_code]);
            $rfid_exists = $stmt->fetch();
            if ($rfid_exists) {
                if ($rfid_exists['device_type'] == $usercardtype) {
                    $stmt = $pdo->prepare("SELECT * FROM user WHERE rfid_code = ?");
                    $stmt->execute([$rfid_exists['device_id']]);
                    $rfid_assigned = $stmt->fetch();
                    if ($rfid_assigned) {
                        $check_response["messageId"] = 3;
                    }
                    else {
                        $check_response["createUsercard"] = false;
                        $check_response["messageId"] = 1;
                    }}
                else {
                    $check_response["messageId"] = 4;
                }}
            else {
                $check_response["createUsercard"] = true;
                $check_response["messageId"] = 0;
            }
        }

        switch($check_response["messageId"]) {
            case 0:
                $this->data['message'] = $this->data['message']."User + Usercard wird erstellt. ";
                $this->data['response'] = '0';
                break;
            case 1:
                $this->data['message'] = $this->data['message']."Usercard existiert schon und wird zugewiesen. ";
                $this->data['response'] = '1';
                break;
            case 2:
                $this->data['message'] = $this->data['message']."User existiert schon. ";
                $this->data['response'] = '2';
                break;
            case 3:
                $this->data['message'] = $this->data['message']."Usercard ist bereits ".$rfid_assigned['vorname']." ".$rfid_assigned['name']." zugewiesen. ";
                $this->data['response'] = '3';
                break;
            case 4:
                $this->data['message'] = $this->data['message']."RFID Code gehört zu einem Device. ";
                $this->data['response'] = '4';
                break;
            default:
               $this->data['message'] = $this->data['message']."Unexpected Error ";
               $this->data['response'] = '8';
            }
        return $check_response;
    }
    function run() {
        global $data;
        if ($this->check()["messageId"] < 2) {
            echo "success";
            if ($this->data["response"] == '0') {
                $this->createUsercard();
            }
            $this->createUser();
        }
        else {
            // Failed to create;
            echo "fail<br>";
            echo "message (".$this->data["response"]."): ".$this->data["message"];
        }
    }
    protected function createUser() {
        echo "creating user...";
        // Create the user
    }
    protected function createUsercard() {
        echo "creating usercard...";
        // Create the usercard
    }
}
?>