<?

$git = new CzProject\GitPhp\Git;
// create repo object
$repo = $git->open('./');

$repo->pull('origin', 'master');