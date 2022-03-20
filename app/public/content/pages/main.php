<!-- link dynamic content loader -->
<script src="content/pages/assets/dynamicContent.js"></script>
<script src="content/pages/assets/index.js"></script>
<?php
session_start();
include '../../../config/config.php';
session();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/assets/style.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div id='loading' class="spinner-wrapper" style="display:none;">
        <span id ='spinner' class="spinner"></span>
    </div>

	<div class='navbar' id='navbar'>
        <input type="text" id="search_text">
        <input type="button" id="search_submit" value="Search">

        <input type="button" id="add_user" class="navbutton" onclick="" value="Tables">
        <input type="button" id="add_user" class="navbutton" onclick="" value="Add User">
        <input type="button" id="import_and_reset" class="navbutton" onclick="" value="Import & Reset">
        <input type="button" id="test_system" class="navbutton" onclick="" value="Test System">
		<input type="button" id="logout" class="navbutton" onclick="handleClick('index');" value="Logout">
        

		<!-- EXAMPLE & REFERENCE:
            <button id="" class="navbutton" onclick="task(event, 'file', 'task', 'keyword')">Title</button>
            <a id="allusers_id"><button class="navbutton" onclick="task(event, 'import.php', '', 'import');">import & reset</button></a>
		    <a id="tables_id"><button class="navbutton" onclick="task(event, 'fileupload.php', '_fileupload', 'fileupload');">file upload</button></a>
		    <a id="adduser_id"><button class="navbuttonleft" onclick="task(event, '_adduser.php', '_adduser', 'adduser');">add user</button></a>
            <a id="logout"><button onclick="clearAllSavedValues(); task(event, 'request.php', '_logout', 'logout');" class="navbuttonright">logout</button></a>
            <form id="search" style="display:inline;" onsubmit="task(event, 'load_table.php', '_allusers', 'tables');">
			    <input class="navbar_textfield" type="text" value="" id="searchinput" name="search" placeholder="search" onkeyup="searchvalueupdate(); saveValue(this);"/>
			    <input class="navbar_submit" type="submit" id="submit" value="tables"/>
		    </form>
            <input type="button" id="ausleihe_id" class="navbutton" onclick="task(event, 'ausleihe_sim.php', '', 'ausleihe')">Ausleihe</button>
        -->
    </div>


    <p>Navbar:</p>
    <div class="navbar-wrapper">
        <button id="page1" onclick="handleClick('pg1');">Page 1</button>
        <button id="page2" onclick="handleClick('pg2');">Page 2</button>
        <button id="page3" onclick="handleClick('pg3');">Page 3</button>
        <button id="home" onclick="handleClick('index');">Logout</button>
    </div>
    <button onclick="getInfo();">Get Page Data</button>
    <h1>Static Content</h1>
    <!-- dynamic content-->
    <div class="dynamic-content" id="dynamic-content"></div>
    <p>Static Content</p>
</body>
</html>