<?php
require 'vendor/autoload.php';

$git = new CzProject\GitPhp\Git;
// create repo object
$repo = $git->open('./');

echo "response: ".$repo->execute("pull")[0];