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

// GET PROCESS RUNTIME/MEMORY USAGE/ AND PID

$result = mysqli_query($con,"SELECT id,templateid,username,coind,coinname,ip_p2p,port_p2p,ip_rpc,port_rpc,serverid,rpc_user,rpc_password FROM coind_instances");
$rows = 0;
echo "<table border=1><tr><td>#</td><td>Coin</td><td>IP</td><td>Ports</td><td>FUNC.</td></tr>";
while($row = mysqli_fetch_array($result)) {
  #$bitcoin = new Bitcoin($row['rpc_username'],$row['rpc_password'],$row['ip_rpc'],$row['port_rpc']);
  
  $result2 = mysqli_query($con,"SELECT name FROM coind_templates WHERE id='".$row['templateid']."'");
  $row2 = mysqli_fetch_row($result2);
  
  echo "<tr><td>".$row['id']."</td><td>".$row2[0]."</td><td>".$row['ip_p2p']."</td><td>P2P: ".$row['port_p2p']."<br>RPC:".$row['port_rpc']."</td>";
  echo "<td>";
  echo "[ <a href=coinds.remove.php?id=".$row['id'].">Remove</a> ]";
  
  $result3 = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$row['serverid']."'");
  $serverinfo = mysqli_fetch_row($result3);

  //log into server.,
  $ssh = new Net_SSH2($serverinfo[0]);
  $key = new Crypt_RSA();
  $key->loadKey(file_get_contents($sshkey_location));
  if (!$ssh->login('root', $key)) {
    $debug[]['ssh'] = "SSH Login Failed!";
  } else {  
    $debug[$rows]['pid'] = $ssh->exec('cat /home/'.$row['username'].'/.'.$row['coinname'].'/'.$row['coind'].'.pid');
    if($debug[$rows]['pid']>0){ 
      echo "[ <a href=coinds.info.php?id=".$row['id'].">Info</a> ]<br>";
      echo "[ <a href=coinds.ioflip.php?id=".$row['id']."&type=stop>Stop</a> |";
      echo " <a href=coinds.ioflip.php?id=".$row['id']."&type=restart>Restart</a> ]";
    } else {
      echo "<br>[ <a href=coinds.ioflip.php?id=".$row['id']."&type=start>Start</a> ]";
    }
  }
  
  echo "</td></tr>";
  ++$rows;
}

if($rows<=0){
  echo "<tr><td>0</td><td colspan=5><i>There are no coin instances currently being managed!</i></td></tr>";
}
echo "<tr><td>+</td><td colspan=5><a href=coinds.add.php>Create Coind Instance</a></td></tr>";

echo "</table>";

mysqli_close($con);
