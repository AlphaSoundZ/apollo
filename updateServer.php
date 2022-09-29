<?php
require 'vendor/autoload.php';

$git = new CzProject\GitPhp\Git;
// create repo object
$repo = $git->open('./');

$commitId = $repo->getLastCommitId();
$commit = $repo->getCommit($commitId);

echo "Current Branch: ".$repo->getCurrentBranchName()."<br>";
echo "Last Commit: ".$commit->getDate()->format('Y-m-d H:i:s')."<br>";
echo "Last Commit Message: ".$commit->getSubject()."<br>";

?>

<form target="_blank" action="updateServerPull.php">
    <input type="submit" value="update">
</form>