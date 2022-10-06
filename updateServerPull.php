<?php
require 'vendor/autoload.php';

// create repo object
$git = new CzProject\GitPhp\Git;
$repo = $git->open('./');

echo "response: ".$repo->pull("origin");