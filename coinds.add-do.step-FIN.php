<?php
require_once(".config.php");
require_once(".auth.php");

if(strlen($_POST['serverid'])<=0 || strlen($_POST['templateid'])<=0){
  die("Please go back and select a Server AND a Template to use!");
}

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $debug[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}

//function
function packet_handler($str)
{
    echo ".";
    @ob_flush();
    flush();
}

$result = mysqli_query($con,"SELECT name,coind,gitsrc,cd_gitsrc,port_p2p,port_rpc FROM coind_templates WHERE id='".$_POST['templateid']."'");
$coindinfo = mysqli_fetch_row($result);

$coindinfo['name'] = $coindinfo[0];
$coindinfo['coind'] = $coindinfo[1];
$coindinfo['gitsrc'] = $coindinfo[2];
$coindinfo['cd_gitsrc'] = $coindinfo[3];
$coindinfo['port_p2p'] = $coindinfo[4];
$coindinfo['port_rpc'] = $coindinfo[5];


$result2 = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$_POST['serverid']."'");
$serverinfo = mysqli_fetch_row($result2);

$serverinfo['ip'] = $serverinfo[0];


//log into server.,
$ssh = new Net_SSH2($serverinfo['ip']);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {
  $ssh->setTimeout(25);
  $debug['exec_fixutf8bug'] = $ssh->exec('su -c "export LC_ALL=\"en_US.UTF-8\"" -s /bin/sh '.$_POST['username'],'packet_handler');
  $cmd = "cd /home/".$_POST['username']."/sauce/".$coindinfo['cd_gitsrc']."/;";
  $cmd .= "./".$coindinfo['coind']." -daemon &";
  $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$_POST['username'],'packet_handler');
  
  $debug['step'] = "Daemon Started! <a href=coinds.php>Enjoy your service.</a>";
}

mysqli_close($con);
print_r($debug);
