<?php
declare(strict_types=1);
require '../application/plugins/vendor/autoload.php';
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
➡️➡️ <input type="button" id="code" onclick="copyToClipboard();" value="<?= $auth->getCode($secret)?>"> ⬅️⬅️


<script>
function copyToClipboard() {
  /* Get the text field */
  var copyText = document.getElementById("code");

  /* Select the text field */
  //copyText.select();
  //copyText.setSelectionRange(0, 99999); /* For mobile devices */

   /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.value);

  /* Alert the copied text */
  console.log("copied!");
}
</script>