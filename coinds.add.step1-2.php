<?php
require_once(".config.php");
require_once(".auth.php");

//connect to database
$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
// Check connection
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
  $debug[] = mysqli_connect_error();
}

if(!isset($_POST['templateid'])){
  die("Go back and select a template.");
}

echo "<form action=coinds.add.step1-3.php method=post>";

//SERVERS
$result = mysqli_query($con,"SELECT id,name,ip FROM servers");
$rowsSERVER = 0;
echo "<table border=1><caption>Choose Server</cpation><tr><td colspan=2>Name</td><td>IP</td><td>{Functions}</td></tr>";
while($row = mysqli_fetch_array($result)) {
  echo "<tr><td><input type=radio name=serverid value='".$row['id']."' /><td>".$row['name']."</td><td>".$row['ip']."</td><td>[<a href=servers.php>Status</a>]</td></tr>";
  ++$rowsSERVER;
}
if($rowsSERVER<=0){
  echo "<tr><td></td><td colspan=4><i>Sorry, there needs to be at least 1 server to choose from!</i></td></tr>";
}
echo "<tr><td>+></td><td colspan=4><a href=servers.add.php>Add Server</a></td></tr>";
echo "</table>";

if($rowsSERVER>0){
  echo "<input type=hidden name=templateid value='".$_POST['templateid']."' />";
  echo "<input type=submit value='[Step 1-3]> Configure' /></form>";
}

mysqli_close($con);
?>
