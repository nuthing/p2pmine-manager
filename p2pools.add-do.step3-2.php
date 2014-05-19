<?php
if (ob_get_level() == 0) ob_start();
require_once(".config.php");
require_once(".auth.php");

if(strlen($_POST['serverid'])<=0 || strlen($_POST['templateid'])<=0){
  die("Please go back and select a Server AND a Template to use!");
}


//function
function packet_handler($str)
{
    echo ".";
    @ob_flush();
    flush();
}



$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $debug[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}

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
  echo "Installing scrypt";
  $debug['install_scrypt'] = $ssh->exec('cd /home/'.$_POST['username'].'/sauce/py_modules/vertcoin_scrypt/;python setup.py install','packet_handler');
  
  echo "Completed!<br>";
}

mysqli_close($con);
print_r($debug);

echo '
<form action=p2pools.add-do.step4.php method=post>
<input type=hidden name=address value="'.$_POST['address'].'" />
<input type=hidden name=fee value="'.$_POST['fee'].'" />
<input type=hidden name=donationfee value="'.$_POST['donationfee'].'" />
<input type=hidden name=nodes value="'.$_POST['nodes'].'" />
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=coindid value="'.$_POST['coindid'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=port_worker value="'.$_POST['port_worker'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />
<input type=submit value="[Step 4]> Save to Database" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
ob_flush();
flush();
ob_end_flush();
