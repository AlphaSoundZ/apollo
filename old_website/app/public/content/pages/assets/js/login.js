function visibility(id, visibility) {
  var element = document.getElementById(id);
  var picture = document.getElementById('loading-pic_id');
  element.style.display = visibility;
  if (visibility == 'block') {
    picture.style.transition = 'opacity 1s';
    picture.style.opacity = '50%';
  }
  if (visibility == 'none') {
    picture.style.transition = 'opacity 0s';
    picture.style.opacity = '100%';
  }
}

function validateForm(event) {
  event.preventDefault();
  var input_username = document.getElementById("login");
  var input_password = document.getElementById("password");
  var input_authcode = document.getElementById("authcode");
  if (input_username.value == '' || input_username.value.charAt(0) == ' ' || input_password.value == '' || !input_authcode.value) {
    document.getElementById("warning").innerHTML = "Fill empty fields!";
  }
  else if (input_authcode.value.length != 6) {
    document.getElementById("warning").innerHTML = "Authentication must be 6 digits!";
    document.getElementById("authcode").value = "";
  }
  else {
    var data = {task: '_login', username: input_username.value, password: input_password.value, authcode: input_authcode.value};
    var ajax = new XMLHttpRequest();
    ajax.open("POST", "content/pages/assets/php/authentication.php", true);
    ajax.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        const response = this.responseText;
        if (response == 0) {
          visibility('loading', 'none');
          document.getElementById("warning").innerHTML = "Username / password / authentication was wrong!";
          document.getElementById("login").value = "";
          document.getElementById("password").value = "";
          document.getElementById("authcode").value = "";
        }
        else if (response == 1) {
          // load home Page
            handleClick("home");
          return true;
        }
        else if (reponse) {
          visibility('loading', 'block');
        }
      }
    };
    visibility('loading', 'block');
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.send(JSON.stringify(data));
  }
}