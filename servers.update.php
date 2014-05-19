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

$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$_GET['id']."'");
$serverinfo = mysqli_fetch_row($result);


//log into server.,
$ssh = new Net_SSH2($serverinfo[0]);
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
  $debug['exec_apt-get_install'] = $ssh->exec('apt-get install -y libtool autotools-dev autoconf build-essential make libssl-dev libboost-dev libboost-all-dev libdb++-dev libminiupnpc-dev python-dev git python2.7 python-zope.interface python-twisted python-twisted-web pkg-config','packet_handler');
  echo "Finished!<br>";
}

mysqli_close($con);
print_r($debug);
?>
<a href=/manager>go back</a>
