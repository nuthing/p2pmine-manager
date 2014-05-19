<?php
if (ob_get_level() == 0) ob_start();
ob_end_flush();
require_once(".config.php");
require_once(".auth.php");

//function
function packet_handler($str)
{
    echo ".";
    @ob_flush();
    flush();
}


if(strlen($_POST['serverid'])<=0 || strlen($_POST['templateid'])<=0){
  die("Please go back and select a Server AND a Template to use!");
}

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
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
  echo "Updating repository files";
  $debug['exec_apt-get_update'] = $ssh->exec('apt-get update','packet_handler');
  echo "Updated!<br>";
  echo "Upgrading server";
  $debug['exec_apt-get_upgrade'] = $ssh->exec('apt-get upgrade -y','packet_handler');
  echo "Upgraded!<br>";
  #echo "Downloading & Installing required libboost1.53-dev";
  #$debug['exec_libboost1.53-dev'] = $ssh->exec('wget http://p2pmine.com/tools/repo/libboost1.53-dev_1.53.0-6+exp3ubuntu8_amd64.deb; dpkg -i libboost1.53-dev_1.53.0-6+exp3ubuntu8_amd64.deb');
  #echo "Finished!<br>";
  echo "Installing required files";
  $debug['exec_apt-get_install'] = $ssh->exec('apt-get install -y libtool autotools-dev autoconf build-essential make libssl-dev libboost-dev libboost-all-dev libdb++-dev libminiupnpc-dev python-dev git python2.7','packet_handler');
  echo "Finished!<br>";
}

mysqli_close($con);
print_r($debug);

echo '
<form action=coinds.add-do.step2.php method=post>
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_threads value="'.$_POST['rpc_threads'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=allowedips value="'.$_POST['allowedips'].'" />
<input type=submit value="[Step 2]> Add User & Create Configs" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
ob_flush();
flush();
ob_end_flush();
