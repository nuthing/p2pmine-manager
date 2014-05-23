<?php
require_once(".config.php");
require_once(".auth.php");

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $debug[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT serverid,username FROM p2pool_instances WHERE id='".$_POST['id']."'");
$info = mysqli_fetch_row($result);

$info['serverid'] = $info[0];
$info['username'] = $info[1];

$result3 = mysqli_query($con, "SELECT gitsrc FROM p2pool_themes WHERE id='".$_POST['themeid']."'");
$theme = mysqli_fetch_row($result3);

$theme['gitsrc'] = $theme[0];


$result2 = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$info['serverid']."'");
$serverinfo = mysqli_fetch_row($result2);

$serverinfo['ip'] = $serverinfo[0];


//log into server.,
$ssh = new Net_SSH2($serverinfo['ip']);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {
  $debug['rm_web-static'] = $ssh->exec('cd /home/'.$info['username'].'/sauce/;rm -rf web-static');
  
  $debug['make_web-static'] = $ssh->exec('cd /home/'.$info['username'].'/sauce/;mkdir web-static');
  
  $debug['git_new'] = $ssh->exec('cd /home/'.$info['username'].'/sauce/web-static/;git clone '.$theme['gitsrc'].' .');
  
  $debug['chown'] = $ssh->exec('chown -R '.$info['username'].' /home/'.$info['username'].'/sauce/');
}

mysqli_close($con);
print_r($debug);

echo "Installed! <a href='p2pools.php'>go back</a>";
