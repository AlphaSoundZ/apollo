<?php session_start();?>
<html style="overflow:hidden;">
<link rel="Stylesheet" href="style_main.css"/>
<link rel="Stylesheet" href="loading_animation.css">
<script src="getInputValues.js"></script>
<script src="fileupload.js"></script>
<script src="import.js"></script>
<head>
<title>Admin</title>
</head>
<body onload="onload();">
	<!-- loading animation -->
	<div id='loading' class="spinner-wrapper" style="display:none;"><span id ='loading' class="spinner2"></span></div>

	<section class='topnav' id='topnav'>
		<form id="search" style="display:inline;" onsubmit="task(event, 'load_table.php', '_allusers', 'tables');">
			<input class="navbar_textfield" type="text" value="" id="searchinput" name="search" placeholder="search" onkeyup="searchvalueupdate(); saveValue(this);"/>
			<input class="navbar_submit" type="submit" id="submit" value="tables"/>
		</form>
		<a href="index.php" id="logout"><button onclick="clearAllSavedValues(); task(event, 'request.php', '_logout', 'logout');" class="navbuttonright">logout</button></a>
		<a href="http://localhost/apollo/ausleihe/ausleihe_request.php" target="_blank"><button class="navbutton">ausleihe</button></a>
		<a href="#allusers" id="allusers_id"><button class="navbutton" onclick="task(event, 'import.php', '', 'import');">import & reset</button></a>
		<a href="#allusers" id="tables_id"><button class="navbutton" onclick="task(event, 'fileupload.php', '_fileupload', 'fileupload');">file upload</button></a>
		<a href="user_add.php" id="adduser_id"><button class="navbuttonleft" onclick="task(event, '_adduser.php', '_adduser', 'adduser');">add user</button></a>
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

function searchvalueupdate() {
	if (document.getElementById("searchinput").value) {
		document.getElementById("submit").value = 'search';
	}
	else {
		document.getElementById("submit").value = 'tables';
	}
}

function onload() {
	getSavedValue(['searchinput']); // get recent input values after site was reload
	if (document.getElementById("searchinput").value != '') { // change between search and tables value of submit button
		document.getElementById("submit").value = 'search';
	} else {document.getElementById("submit").value = 'tables';}

	document.getElementById("main-default").style.display = 'none';
	page = localStorage.getItem('page');
	if (page) {
		switch (page) {
			case 'tables':
				task(0, 'load_table.php', '_allusers', page);
				break;
			case 'adduser':
				task(0, '_adduser.php', '_adduser', page);
				break;
			case 'fileupload':
				task(0, 'fileupload.php', '_fileupload', page);
				break;
			case 'import':
				task(0, 'import.php', '', page);
				break;
			default:
				document.getElementById("main-default").style.display = 'block';
				break;
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
		task(event, '_adduser.php', '_push', 'adduser');
		return true;
	}
	var search = document.getElementById('searchinput');
	if (search.value == true) {
		task(event, 'load_table.php', '_allusers', 'table');
	}

}

function task(event, file, task, name) {
	document.getElementById("main-default").style.display = 'none';
	if (event != 0) {
		event.preventDefault();
	}
	var data = {};
	if (task == '_push' && file == '_adduser.php') {
		data = {task: '_push', vorname: document.getElementById("input.vorname").value, nachname: document.getElementById("input.nachname").value, klasse: document.getElementById("input.klasse").value, rfid_code: document.getElementById("input.rfid_code").value};
	}
	else if(task == '_allusers' && file == 'load_table.php') {
		var search = (document.getElementById("submit").value == 'tables') ? '' : document.getElementById("searchinput").value;
		data = {task: '_allusers', search: search};
	}
	else {
		data['task'] = task;
	}


	var ajax = new XMLHttpRequest();
	ajax.open("POST", file, true);
	loading('start');
	ajax.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			localStorage.setItem('page', name);
			loading('stop');
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
			else if (task == '_push' && file == '_adduser.php') {
				document.getElementById("main-content").innerHTML += response;
				delSavedValue(['input.vorname', 'input.nachname', 'input.klasse', 'input.rfid_code']);
			}
			else { // else if (task == '_adduser' || task == '_tables' || task == '_allusers')
				document.getElementById("main-content").innerHTML = response;
				// document.getElementById("main-warning-section").innerHTML = response;
				if (task == '_adduser') {
					getSavedValue(['input.vorname', 'input.nachname', 'input.klasse', 'input.rfid_code']);
				}
			return true;
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
    return false
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

function loading(var1) {
	var element = document.getElementById('loading');
	if (var1 == 'start') {
		element.style.display = 'block';
	}
	if (var1 == 'stop') {
		element.style.display = 'none';
	}
}

</script>
