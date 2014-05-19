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

$result = mysqli_query($con,"SELECT coinname FROM coind_instances WHERE id='".$_POST['coindid']."'");
$cinfo = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT gitsrc,module FROM p2pool_templates WHERE id='".$_POST['templateid']."'");
$info = mysqli_fetch_row($result);

$info['gitsrc'] = $info[0];
$info['module'] = $info[1];


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
  $debug['git_src'] = $ssh->exec('git clone '.$info['gitsrc'].' /home/'.$_POST['username'].'/sauce');
}

mysqli_close($con);
print_r($debug);

if($info['module']=="ltcscrypt"){
  echo '<form action=p2pools.add-do.step3-1.php method=post>';
} elseif($info['module']=="scryptn"){
  echo '<form action=p2pools.add-do.step3-2.php method=post>';
} elseif($info['module']=="xcoin-hash"){
  echo '<form action=p2pools.add-do.step3-3.php method=post>';
} else {
  echo '<form action=p2pools.add-do.step4.php method=post>';
}
echo '
<input type=hidden name=address value="'.$_POST['address'].'" />
<input type=hidden name=fee value="'.$_POST['fee'].'" />
<input type=hidden name=donationfee value="'.$_POST['donationfee'].'" />
<input type=hidden name=nodes value="'.$_POST['nodes'].'" />
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=coindid value="'.$_POST['coindid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=port_worker value="'.$_POST['port_worker'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />';

if($info['module']=="ltscrypt"){
 echo '<input type=submit value="[Step 3-1]> Install LTC Scrypt for P2Pool" onclick="this.value = \"Please wait...\"; this.disabled = true"/>';
} elseif($info['module']=="scryptn") {
 echo '<input type=submit value="[Step 3-2]> Install Scrypt-N for P2Pool" onclick="this.value = \"Please wait...\"; this.disabled = true"/>';
} elseif($info['module']=="xcoin-hash") {
 echo '<input type=submit value="[Step 3-3]> Install xcoin-hash for P2Pool" onclick="this.value = \"Please wait...\"; this.disabled = true"/>';
} else {
 echo '<input type=submit value="[Step 4]> Save To Database" onclick="this.value = \"Please wait...\"; this.disabled = true"/>';
}
echo '</form>';
?>
