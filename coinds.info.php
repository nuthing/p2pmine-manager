<?php
require_once(".config.php");
require_once(".include.jsonRPCClient.php");
require_once(".auth.php");

//connect to database
$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
// Check connection
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
  $debug[] = mysqli_connect_error();
}

// GET PROCESS RUNTIME/MEMORY USAGE/ AND PID

$result = mysqli_query($con,"SELECT templateid,username,coind,coinname,ip_p2p,port_p2p,ip_rpc,port_rpc,serverid,rpc_user,rpc_password FROM coind_instances WHERE id='".$_GET['id']."'");
$row = mysqli_fetch_row($result);

$bitcoin = new jsonRPCClient('http://'.$row[9].':'.$row[10].'@'.$row[6].':'.$row[7].'/');
$binfo = $bitcoin->getinfo();
?>

<table>
  <tr><td>coinname</td><td><?=$row[3];?></td></tr>
  <tr><td>username</td><td><?=$row[1];?></td></tr>
  <tr><td>executablefile</td><td><?=$row[2];?></td></tr>
  <tr><td>templateid</td><td><?=$row[0];?></td></tr>
  <tr><td>publicipp2p</td><td><?=$row[4];?></td></tr>
  <tr><td>privateiprpc</td><td><?=$row[6];?></td></tr>
  <tr><td>portp2p</td><td><?=$row[5];?></td></tr>
  <tr><td>portrpc</td><td><?=$row[7];?></td></tr>
  <tr><td>serverid</td><td><?=$row[8];?></td></tr>
  <? foreach($binfo as $bkey=>$bvalue){ if(strlen($bvalue)>0){ echo "<tr><td>".$bkey."</td><td>".$bvalue."</td></tr>"; } } ?>
  <? if(isset($_GET['auth'])){ echo "<tr><td>rpcuser</td><td>".$row[9]."</td></tr>"; } ?>
  <? if(isset($_GET['auth'])){ echo "<tr><td>rpcpassword</td><td>".$row[10]."</td></tr>"; } ?>
</table>

<?php
mysqli_close($con);
