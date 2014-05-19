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

$result2 = mysqli_query($con,"SELECT id,serverid FROM p2pool_instances");
while($row2 = mysqli_fetch_array($result2)){
  $p2pool_instance[$row2[1]][] = $row2[0];
}

$result3 = mysqli_query($con,"SELECT id,serverid FROM coind_instances");
while($row3 = mysqli_fetch_array($result3)){
  $coind_instance[$row3[1]][] = $row3[0];
}
  
$result = mysqli_query($con,"SELECT id,name,ip FROM servers");
$rows = 0;
echo "<table border=1><tr><td>#</td><td>Name</td><td>IP</td><td>uptime</td><td>Disk<br>Usage</td><td>Memory<br>Free</td><td>Load Avg.</td><td>Instances<td>FUNC.</td></tr>";
while($row = mysqli_fetch_array($result)) {
  $server = getInfo($row['ip'],$sshkey_location);
  echo "<tr><td>".$row['id']."</td><td>".$row['name']."</td><td>".$row['ip']."</td><td>".$server['uptime']."</td><td>".$server['disk_usage']."</td><td>".$server['memory_free']."</td><td>".$server['load']."</td><td>p2pool: ".count($p2pool_instance[$row['id']])."<br>coind: ".count($coind_instance[$row['id']])."</td><td>[<a href=servers.update.php?id=".$row['id'].">Update</a>]</td></tr>";
  ++$rows;
}

if($rows<=0){
  echo "<tr><td>0</td><td colspan=7><i>There are no servers currently being managed!</i></td><td><a href=servers.add.php>[ADD]</a></td></tr>";
} else {
  echo "<tr><td>+</td><td colspan=8><a href=servers.add.php>Add Server</a></td></tr>";
}

echo "</table>";

mysqli_close($con);
