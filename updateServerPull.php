<?php
require 'vendor/autoload.php';

$git = new CzProject\GitPhp\Git;
// create repo object
$repo = $git->open('./');

// $repo->pull();
$repo->execute("pull");