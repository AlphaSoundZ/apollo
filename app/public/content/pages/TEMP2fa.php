<?php
declare(strict_types=1);
echo "cwd:".getcwd();
require '../../../plugins/vendor/autoload.php';
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
?>
<p>Please scan this with your prefered 2 factor authenticator!</p>
<p>
<?php
$link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('Schule', $secret, 'GoogleAuthenticatorExample');
?>

<a  href="<?php echo $link; ?>"><img style="border: 0; padding:10px" src="<?php echo $link; ?>"/></a>
</p>
<p>Current code is:</p>
➡️➡️ <input type="textfield" id="code" onclick="copyToClipboard();" value="<?= $auth->getCode($secret)?>"> ⬅️⬅️