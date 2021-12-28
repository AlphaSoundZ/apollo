<?php session_start();?>
<html style="overflow:hidden;">
<link rel	="Stylesheet" href="style_main.css"/>
<head>
<title>Admin</title>
</head>
<body onload="onload();">
	<section class='topnav' id='topnav'>
		<form id="search" style="display:inline;" onsubmit="task(event, 'tabletest.php', '_allusers');">
			<input class="navbar_textfield" type="text" value="" id="searchinput" name="search" placeholder="search" onfocus="searchvalueupdate('focus');" onblur="searchvalueupdate('blur');"/>
			<input class="navbar_submit" type="submit" id="submit" value="tables"/>
		</form>
		<a href="index.php" id="logout"><button onclick="task(event, 'request.php', '_logout');" class="navbuttonright">logout</button></a>
		<a href="http://localhost/ausleihe/indexnew.html" target="_blank"><button class="navbutton">ausleihe</button></a>
		<a href="#allusers" id="allusers_id"><button class="navbutton" onclick="task(event, 'tabletest.php', '_allusers');">all users</button></a>
		<a href="#tables" id="tables_id"><button class="navbutton" onclick="task(event, '_tables.php', '');">tables</button></a>
		<a href="user_add.php" id="adduser_id"><button class="navbuttonleft" onclick="task(event, '_adduser.php', '_adduser');">add user</button></a>
	</section>
	<?php
		if (!empty($_SESSION['sessioncheck']) && $_SESSION['sessioncheck'] == $_SERVER['HTTP_USER_AGENT']) {
	?>
				<section id="main" class="main-section">
					<div id="main-default" style="display:none;">
					<p>Select in the Navbar:</p>
					</div>
					<section id="main-content" style="display:block">
						<p>Main content</p>
						<p>Main content</p>
						<p>Main content</p>
					</section>
				</section>
	<?php
		} else {
		echo "<meta http-equiv=\"refresh\" content=\"0; URL=index.php\">";
		}
	?>
</body>
</html>
<script type="text/javascript">

function updateerrormsg() {
	if (document.getElementById("warning").innerHTML != warningMessage) {
		console.log("update error msg");
		document.getElementById("warning").innerHTML = "";
	}
}

function searchvalueupdate($data) {
	if ($data == 'focus') {
		document.getElementById("submit").value = 'search';
	}
	if ($data == 'blur') {
		document.getElementById("submit").value = 'tables';
	}
}

function onload() {
	if (document.getElementById("searchinput").value != '') {
		document.getElementById("submit").value = 'search';
	} else {document.getElementById("submit").value = 'tables';}
	document.getElementById("main-default").style.display = 'none';
	var hashValue = window.location.hash.substr(1);
	if (hashValue) {
		if (hashValue && 'login' && hashValue != 'logout' && hashValue != 'allusers') {
			var file = '_'+hashValue+'.php';
			task(0, file, '_'+hashValue);
		} else if (hashValue == 'allusers') {
			task(0, 'tabletest.php', '_allusers');
		}
		else {
			var file = 'request.php';
			task(0, file, '_'+hashValue);
		}
	}
	else {
		document.getElementById("main-default").style.display = 'block';
	}
}

function validateForm(event) {
	event.preventDefault();
	if (_eCheck() == false) {
    document.getElementById("warning").innerHTML = warningMessage;
  }
  if (_eCheck() == true) {
		task(event, '_adduser.php', '_push');
		return true;
	}
	var search = document.getElementById('searchinput');
	if (search.value == true) {
		task(event, 'tabletest.php', '_allusers');
	}

}

function task(event, file, task) {
	if (event != '0') {
		event.preventDefault();
	}
	var data = {};
	data['task'] = task;
	if (task == '_push' && file == '_adduser.php') {
		data = {task: '_push', vorname: document.getElementById("input.vorname").value, nachname: document.getElementById("input.nachname").value, klasse: document.getElementById("input.klasse").value, rfid_code: document.getElementById("input.rfid_code").value};
	}
	else if (task == '_allusers' && file == 'tabletest.php') {
		data = {task: '_allusers', search: document.getElementById("searchinput").value};
	}



	var ajax = new XMLHttpRequest();
	ajax.open("POST", file, true);
	ajax.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
	    var response = this.responseText;
	    if (response == 0) {
				return false;
	    }
	    else if (response == 1) {
				if (task == '_logout') {
					window.location = "./index.php";
				}
	      return true;
	    }
			else if (task == '_adduser' || task == '_tables' || task == '_allusers') {
				document.getElementById("main-content").innerHTML = response;
				// document.getElementById("main-warning-section").innerHTML = response;
				window.location = "#"+task.substring(1);
				return true;
			}
			else if (task == '_push' && file == '_adduser.php') {
				document.getElementById("main-content").innerHTML += response;
			}
	  }
	};
	ajax.setRequestHeader("Content-Type", "application/json");
	ajax.send(JSON.stringify(data));
}

</script>

<script type="text/javascript">
var input_klasse = 0, input_vorname = 0, input_nachname = 0, input_rfid_code = 0;
var warningMessage = "Bitte beachten Sie, dass alle Felder ausgefüllt sein müssen!";

function checkInput(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122)) {
        return true;
    }
    return false;
}

function checkText(id) {
  var input = document.getElementById(id);
  if (input.value == '' || input.value.charAt(0) == ' ' || (id == "input.klasse" && input.value == "nothing_selected")) {
    if (inputCheck(input.id) == 0) {
      document.getElementById("warning").innerHTML = warningMessage;
    }
  }
  else {
    if (_eCheck()) {document.getElementById("warning").innerHTML = "";}
    if (id != "input.klasse") {input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);}
  }
}

function inputCheck(id) {
  switch(id) {
    case "input.klasse": return input_klasse;
      break;
    case "input.vorname": return input_vorname;
      break;
    case "input.nachname": return input_nachname;
      break;
    case "input.rfid_code": return input_rfid_code;
      break;
  }
}

function _eCheck() {
  var vorname = document.getElementById("input.vorname");
  var nachname = document.getElementById("input.nachname");
  var rfid_code = document.getElementById("input.rfid_code");
  var klasse = document.getElementById("input.klasse");
  if (vorname.value == '' || vorname.value.charAt(0) == ' ' ||
      nachname.value == '' || nachname.value.charAt(0) == ' ' ||
      rfid_code.value == '' || rfid_code.value.charAt(0) == ' ' ||
      klasse.value == 'nothing_selected') {return false;}
      else {return true;}
}

</script>
<style>

</style>
