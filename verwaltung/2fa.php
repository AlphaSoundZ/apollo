<?php
declare(strict_types=1);
require 'plugins/vendor/autoload.php';
$secret = 'AJFLKDJSLKEJLKD';

$qrcode = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('Schule', $secret, 'Ausleihsystem');

$auth = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    if ($auth->checkCode($secret, $code)) {
        echo "YES <br>";
    } else {
        echo "NO <br>";
    }
}
else {
    echo "no code deezinput <br>";
}

echo 'Current Code is: ';
echo $auth->getCode($secret)."<br>";
?>
<a>QR-Code:</a><br>
<img src="<?=$qrcode;?>"><br>