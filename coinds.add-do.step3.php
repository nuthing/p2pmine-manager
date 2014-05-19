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
  $debug['git_src'] = $ssh->exec('git clone '.$coindinfo['gitsrc'].' /home/'.$_POST['username'].'/sauce');
}

mysqli_close($con);
print_r($debug);

echo '
<form action=coinds.add-do.step4.php method=post>
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=rpc_threads value="'.$_POST['rpc_threads'].'" />
<input type=hidden name=allowedips value="'.$_POST['allowedips'].'" />
<input type=hidden name=port_rpc value="'.$_POST['port_rpc'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />
<input type=submit value="[Step 4]> Compile Source*" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
?>
<br>
<b>* WARNING! This next step requires you to not close out the page/window.<br>
If so, the next parts of the script WILL NOT WORK.<br>
Thus, resulting in a manual clean-up.<br>
The process could take between 5-30 minutes (1 hour max execution time).<br><br>
</b>
