<?php
require_once(".config.php");
include_once(".functions.php");
require_once(".auth.php");

if(!isset($_GET['id'])){
  die("Please go back and select a server to update.");
}

if(!isset($_GET['type'])){
  die("Please choose an option.");
}


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

$result = mysqli_query($con,"SELECT serverid,coindid,templateid,hostname,username,address,fee,donationfee,port_p2p,port_worker,nodes FROM p2pool_instances WHERE id='".$_GET['id']."'");
$info0 = mysqli_fetch_row($result);

$info0['serverid'] = $info0[0];
$info0['coindid'] = $info0[1];
$info0['templateid'] = $info0[2];
$info0['hostname'] = $info0[3];
$info0['username'] = $info0[4];
$info0['address'] = $info0[5];
$info0['fee'] = $info0[6];
$info0['donationfee'] = $info0[7];
$info0['port_p2p'] = $info0[8];
$info0['port_worker'] = $info0[9];
$info0['nodes'] = $info0[10];

$result = mysqli_query($con,"SELECT coinname,ip_rpc FROM coind_instances WHERE id='".$info0['coindid']."'");
$info = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT named FROM p2pool_templates WHERE id='".$info0['templateid']."'");
$info2 = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$info0['serverid']."'");
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
    $debug['pid'] = explode(' ',$ssh->exec('ps o pid=,cmd= -C python|grep /home/'.$info0['username'].'/sauce/'.$info2[0].'|awk "{ print $1 }"'));
    foreach($debug['pid'] as $num=>$data){
      if($data>0){
        $pid = $num;
        break;
      }
    }
    echo "pid=".$pid."<br>";
    $debug['kill_pid'] = $ssh->exec('kill '.$debug['pid'][$pid]);

    //start daemon
    $ssh->setTimeout(25);
    $cmd .= "python /home/".$info0['username']."/sauce/".$info2[0]; //process
    $cmd .= " --net ".$info[0]; //coin network
    if(strlen($info0['address'])>0){
      $cmd .= " -a ".$info0['address'];  //wallet address for fees
    }
    $nodes = explode(',',$info0['nodes']);
    foreach($nodes as $node){
      if(strlen($node)>0){
        $cmd .= " --p2pool-node ".$node; //add p2pool node
      }
    }
    $cmd .= " --fee ".$info0['fee']; //fee
    $cmd .= " --give-author ".$info0['donationfee']; //donation fee
    $cmd .= " --bitcoind-address ".$info[1];
    $cmd .= " --p2pool-port ".$info0['port_p2p'];
    $cmd .= " --worker-port ".$info0['port_worker'];
    echo $cmd.'<br>';
    $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$info0['username'],'packet_handler');
    
    echo "Daemon restarted";
    
  }elseif($_GET['type']=="stop"){
    //kill pid
    $debug['pid'] = explode(' ',$ssh->exec('ps o pid=,cmd= -C python|grep /home/'.$info0['username'].'/sauce/'.$info2[0].'|awk "{ print $1 }"'));
    foreach($debug['pid'] as $num=>$data){
      if($data>0){
        $pid = $num;
        break;
      }
    }
    echo "pid=".$pid."<br>";
    $debug['kill_pid'] = $ssh->exec('kill '.$debug['pid'][$pid]);
    
    echo "Daemon stopped";
    
  }elseif($_GET['type']=="start"){
    //start daemon
    $ssh->setTimeout(25);
    $cmd .= "python /home/".$info0['username']."/sauce/".$info2[0]; //process
    $cmd .= " --net ".$info[0]; //coin network
    if(strlen($info0['address'])>0){
      $cmd .= " -a ".$info0['address'];  //wallet address for fees
    }
    $nodes = explode(',',$info0['nodes']);
    foreach($nodes as $node){
      if(strlen($node)>0){
        $cmd .= " --p2pool-node ".$node; //add p2pool node
      }
    }
    $cmd .= " --fee ".$info0['fee']; //fee
    $cmd .= " --give-author ".$info0['donationfee']; //donation fee
    $cmd .= " --bitcoind-address ".$info[1];
    $cmd .= " --p2pool-port ".$info0['port_p2p'];
    $cmd .= " --worker-port ".$info0['port_worker'];
    echo $cmd.'<br>';
    $debug['exec_service'] = $ssh->exec('su -c "'.$cmd.'" -s /bin/sh '.$info0['username'],'packet_handler');
      
    
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
