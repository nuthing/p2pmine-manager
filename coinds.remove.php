<?php
require_once(".config.php");
include_once(".functions.php");
require_once(".auth.php");

//connect to database
$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
// Check connection
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
  $debug[] = mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT serverid,templateid,username,coind FROM coind_instances WHERE id='".$_GET['id']."'");
$coind = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT name FROM coind_templates WHERE id='".$coind[1]."'");
$template = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$coind[0]."'");
$serverinfo = mysqli_fetch_row($result);


//check in database is a p2pool is connecting to this daemon

//log into server.,
$ssh = new Net_SSH2($serverinfo[0]);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {
  //kill user proccess
  $debug['exec_killuser'] = $ssh->exec('skill -KILL -u '.$coind[2]);

  //rem user & delete thier home folder
  $debug['exec_rmuser_files-folders-system'] = $ssh->exec('userdel -r '.$coind[2]);

  //remove database entry
  if(!mysqli_query($con,"DELETE FROM coind_instances WHERE id='".$_GET['id']."'")){
    $debug['mysql_error'] = "Unable to remove coind instance from database!";
    $debug['mysql_error_raw'] = mysqli_error($con);
  } else {
    $debug['mysql_success'] = "Success! Coind instance removed from database.";
  }
}

mysqli_close($con);
print_r($debug);
?>
<a href=/manager>go back</a>
