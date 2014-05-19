<?php
require_once(".config.php");
require_once(".auth.php");

//connect to database
$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
// Check connection
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
  $debug[] = mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT serverid,username FROM p2pool_instances WHERE id='".$_GET['id']."'");
$p2poold = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$p2poold[0]."'");
$serverinfo = mysqli_fetch_row($result);

//log into server.,
$ssh = new Net_SSH2($serverinfo[0]);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {
  //kill user proccess
  $debug['exec_killuser'] = $ssh->exec('skill -KILL -u '.$p2poold[1]);

  //rem user & delete thier home folder
  $debug['exec_rmuser_files-folders-system'] = $ssh->exec('userdel -r '.$p2poold[1]);

  //remove database entry
  if(!mysqli_query($con,"DELETE FROM p2pool_instances WHERE id='".$_GET['id']."'")){
    $debug['mysql_error'] = "Unable to remove p2pool instance from database!";
    $debug['mysql_error_raw'] = mysqli_error($con);
  } else {
    $debug['mysql_success'] = "Success! P2Pool instance removed from database.";
  }
}

mysqli_close($con);
print_r($debug);
?>
<a href=/manager>go back</a>
