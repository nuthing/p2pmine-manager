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

echo "<form action=p2pools.add.step1-1.php method=post>";

//p2pool TEMPLATES
$result = mysqli_query($con,"SELECT id,coinname,ip_p2p FROM coind_instances");
$rowsCOIN = 0;
echo "<table border=1><caption>Choose Coind Server</cpation><tr><td colspan=2>Coind Name / ID</td><td>IP</td></tr>";
while($row = mysqli_fetch_array($result)) {
  echo "<tr><td><input type=radio name=coindid value='".$row['id']."' /><td>".$row['coinname']." #[ ".$row['id']." ]</td><td>".$row['ip_p2p']."</td></tr>";
  ++$rowsCOIN;
}
if($rowsCOIN<=0){
  echo "<tr><td></td><td colspan=3><i>Sorry, there needs to be at least 1 server to choose from!</i></td></tr>";
}
echo "<tr><td>+></td><td colspan=3><a href=coinds.add.php>Create Coind Server</a></td></tr>";
echo "</table>";

if($rowsCOIN>0){
  echo "<input type=submit value='[Step 1-1]> Template Selector' /></form>";
}

mysqli_close($con);
?>
