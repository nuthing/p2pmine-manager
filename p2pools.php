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

$result = mysqli_query($con,"SELECT id,serverid,templateid,coindid,hostname,port_p2p,port_worker,username FROM p2pool_instances");
$rows = 0;
echo "<table border=1><tr><td>#</td><td>Service ID</td><td>Coin Name</td><td>IP</td><td>Ports</td><td>FUNC.</td></tr>";
while($row = mysqli_fetch_array($result)) {
  $result2 = mysqli_query($con,"SELECT name,named FROM p2pool_templates WHERE id='".$row['templateid']."'");
  $template = mysqli_fetch_row($result2);
  echo "<tr><td>".$row['id']."</td><td>Server: ".$row['serverid']."<br>Coind: ".$row['coindid']."</td><td>".$template[0]."</td><td>".$row['hostname']."</td><td>P2P: ".$row['port_p2p']."<br>Worker: ".$row['port_worker']."</td>";
  $result3 = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$row['serverid']."'");
  $serverinfo = mysqli_fetch_row($result3);
  echo "<td>";
  echo "[ <a href=p2pools.remove.php?id=".$row['id'].">Remove</a> ]";
  //log into server.,
  $ssh = new Net_SSH2($serverinfo[0]);
  $key = new Crypt_RSA();
  $key->loadKey(file_get_contents($sshkey_location));
  if (!$ssh->login('root', $key)) {
    $debug[]['ssh'] = "SSH Login Failed!";
  } else {  
    $debug[$rows]['pid'] = $ssh->exec('ps o pid=,cmd= -C python|grep /home/'.$row['username'].'/sauce/'.$template[1].'|awk "{ print $1 }"');
    if($debug[$rows]['pid']>0){ 
      echo "[ <a href=http://".$row['hostname'].":".$row['port_worker']." target=_blank>Stats</a> ]<br>";
      echo "[ <a href=p2pools.ioflip.php?id=".$row['id']."&type=stop>Stop</a> |";
      echo " <a href=p2pools.ioflip.php?id=".$row['id']."&type=restart>Restart</a> ]";
    } else {
      echo "<br>[ <a href=p2pools.ioflip.php?id=".$row['id']."&type=start>Start</a> ]";
    }
  }
  echo "</td></tr>";
 
  ++$rows;
}

if($rows<=0){
  echo "<tr><td>0</td><td colspan=6><i>There are no p2pool instances currently being managed!</i></td></tr>";
}
echo "<tr><td>+</td><td colspan=6><a href=p2pools.add.php>Create P2Pool Instance</a></td></tr>";

echo "</table>";

mysqli_close($con);
