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

echo "<pre>\n";
print_r($bitcoin->getinfo()); echo "\n";
echo "</pre>";

mysqli_close($con);
