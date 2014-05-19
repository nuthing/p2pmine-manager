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

$result = mysqli_query($con,"SELECT coinname,ip_rpc FROM coind_instances WHERE id='".$_POST['coindid']."'");
$info = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT named FROM p2pool_templates WHERE id='".$_POST['templateid']."'");
$info2 = mysqli_fetch_row($result);

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
  $cmd = "cd /home/".$_POST['username']."/sauce/;";
  $cmd .= "python ".$info2[0]; //process
  $cmd .= " --net ".$info[0]; //coin network
  if(strlen($_POST['address'])>0){
    $cmd .= " -a ".$_POST['address'];  //wallet address for fees
  }
  $nodes = explode(',',$_POST['nodes']);
  foreach($nodes as $node){
    if(strlen($node)>0){
      $cmd .= " --p2pool-node ".$node; //add p2pool node
    }
  }
  $cmd .= " --fee ".$_POST['fee']; //fee
  $cmd .= " --give-author ".$_POST['donationfee']; //donation fee
  $cmd .= " --bitcoind-address ".$info[1];
  $cmd .= " --p2pool-port ".$_POST['port_p2p'];
  $cmd .= " --worker-port ".$_POST['port_worker'];
  echo 'CMD$ '.$cmd.'<br>';
  $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$_POST['username'],'packet_handler');
  
  $debug['step'][] = "Daemon Started! <a href=p2pools.php>Enjoy your service.</a>";
}

mysqli_close($con);
print_r($debug);
