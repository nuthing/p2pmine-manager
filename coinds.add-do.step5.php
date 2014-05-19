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
  
  //insert into coind_instances $port_rpc,$port_p2p in database
  if(!mysqli_query($con,"INSERT INTO coind_instances (id,templateid,serverid,username,coinname,coind,ip_p2p,port_p2p,ip_rpc,port_rpc,rpc_user,rpc_password) VALUES (NULL,'".$_POST['templateid']."','".$_POST['serverid']."','".$_POST['username']."','".$coindinfo['name']."','".$coindinfo['coind']."','".$serverinfo['ip']."','".$_POST['port_p2p']."','".$serverinfo['ip']."','".$_POST['port_rpc']."','".$_POST['rpc_user']."','".$_POST['rpc_password']."')")){
    $debug['mysql_error'] = "Unable to add coind instance to database!";
    $debug['mysql_error_raw'] = mysqli_error($con);
  } else {
    $debug['mysql_success'] = "Success! Coind instance added to database.";
  }
  
  $debug['chmod_dir'] = $ssh->exec("chown -R ".$_POST['username']." /home/".$_POST['username']."/sauce");
}

mysqli_close($con);
print_r($debug);

echo '
<form action=coinds.add-do.step-FIN.php method=post>
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=port_rpc value="'.$_POST['port_rpc'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />
<input type=submit value="[FIN]> Start Daemon" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
