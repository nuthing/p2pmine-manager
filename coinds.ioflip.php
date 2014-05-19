<meta http-equiv='refresh' content='20;url=/manager'>
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


//function
function packet_handler($str)
{
    echo ".";
    @ob_flush();
    flush();
}


if(!isset($_GET['id'])){
  die("Please go back and select a server to update.");
}

if(!isset($_GET['type'])){
  die("Please choose an option.");
}

$result = mysqli_query($con,"SELECT coinname,serverid,username,coind,templateid FROM coind_instances WHERE id='".$_GET['id']."'");
$coindinfo = mysqli_fetch_row($result);

$coindinfo['coinname'] = $coindinfo[0];
$coindinfo['serverid'] = $coindinfo[1];
$coindinfo['username'] = $coindinfo[2];
$coindinfo['coind'] = $coindinfo[3];
$coindinfo['templateid'] = $coindinfo[4];

$result = mysqli_query($con,"SELECT cd_gitsrc FROM coind_templates WHERE id='".$coindinfo['templateid']."'");
$template = mysqli_fetch_row($result);
$template['cd_gitsrc'] = $template[0];


$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$coindinfo['serverid']."'");
$serverinfo = mysqli_fetch_row($result);


//log into server.,
$ssh = new Net_SSH2($serverinfo[0]);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {  
  //if restart
  if($_GET['type']=="restart"){
    //kill pid
    $debug['pid'] = $ssh->exec('cat /home/'.$coindinfo['username'].'/.'.$coindinfo['coinname'].'/'.$coindinfo['coind'].'.pid','packet_handler');
    $debug['kill_pid'] = $ssh->exec('kill '.$debug['pid']);
    $debug['rm_pid'] = $ssh->exec('rm -f /home/'.$coindinfo['username'].'/.'.$coindinfo['coinname'].'/'.$coindinfo['coind'].'.pid','packet_handler');

    //start daemon
    $debug['exec_fixutf8bug'] = $ssh->exec('su -c "export LC_ALL=\"en_US.UTF-8\"" -s /bin/sh '.$coindinfo['username'],'packet_handler');
    $cmd = "cd /home/".$coindinfo['username']."/sauce/".$template['cd_gitsrc']."/;";
    $cmd .= "./".$coindinfo['coind']." -daemon";
    $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$coindinfo['username'],'packet_handler');
  
    echo "Daemon restarted";
    
  }elseif($_GET['type']=="stop"){
    //kill pid
    $debug['pid'] = $ssh->exec('cat /home/'.$coindinfo['username'].'/.'.$coindinfo['coinname'].'/'.$coindinfo['coind'].'.pid','packet_handler');
    $debug['kill_pid'] = $ssh->exec('kill '.$debug['pid'],'packet_handler');
    $debug['rm_pid'] = $ssh->exec('rm -f /home/'.$coindinfo['username'].'/.'.$coindinfo['coinname'].'/'.$coindinfo['coind'].'.pid','packet_handler');
    
    echo "Daemon stopped";
    
  }elseif($_GET['type']=="start"){
    //start daemon
    $debug['exec_fixutf8bug'] = $ssh->exec('su -c "export LC_ALL=\"en_US.UTF-8\"" -s /bin/sh '.$coindinfo['username'],'packet_handler');
    $cmd = "cd /home/".$coindinfo['username']."/sauce/".$template['cd_gitsrc']."/;";
    $cmd .= "./".$coindinfo['coind']." -daemon";
    echo $cmd;
    $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$coindinfo['username'],'packet_handler');
    
    echo "Daemon started";
  } else {
    echo "Not sure what to do...";
  }
}

mysqli_close($con);
echo "<br>";
print_r($debug);
?>
<a href=/manager>go back</a>
