<?php
require 'vendor/autoload.php';

// create repo object
$git = new CzProject\GitPhp\Git;
$repo = $git->open('./');

$response = $repo->pull("origin");
echo "updated";