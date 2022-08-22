<?php
declare(strict_types=1);
session_start();
require '../../../../../plugins/vendor/autoload.php';
require '../../../../../config/config.php';
$data = json_decode(file_get_contents("php://input"));
$task = $data->task;

// Start desired function
try {
  $task();
} catch (Exception $e) {
  exit;
}


function _login() {
  global $pdo;
  $secret = 'AJFLKDJSLKEJLKD';
  $auth = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
  $data = json_decode(file_get_contents("php://input"));
  $username = $data->username;
  $password = $data->password;
  $authcode = $data->authcode;
  $login_post = "SELECT * FROM login WHERE username = '".$username."' AND password = '".$password."'";
  $login_post = $pdo->query($login_post)->fetch();
  if (!empty($login_post)) {
    if ($login_post['username'] == $username && $login_post['password'] == $password && $auth->checkCode($secret, $authcode)) {
      $_SESSION['sessioncheck'] = $_SERVER['HTTP_USER_AGENT'];
      echo 1;
    }
    else {
      echo 0;
    }
  }
  else {echo 0;}
  exit;
}

function _logout() {
  global $pdo;
  session_destroy();
  if (session_status() != PHP_SESSION_ACTIVE) {
    echo 1;
  }
  else {echo 0;}
  exit;
}
