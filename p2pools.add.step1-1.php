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

if($_POST['coindid']<=0){
  die("Go back and select a Coin Daemon!");
}

echo "<form action=p2pools.add.step1-2.php method=post>";
echo "<input type=hidden name=coindid value=".$_POST['coindid']." />";

$result = mysqli_query($con,"SELECT coinname FROM coind_instances WHERE id='".$_POST['coindid']."'");
$coin = mysqli_fetch_row($result);

//p2pool TEMPLATES
$result = mysqli_query($con,"SELECT id,name,gitsrc FROM p2pool_templates WHERE name='".$coin[0]."'");
$rowsCOIN = 0;
echo "<table border=1><caption>Choose Template</cpation><tr><td colspan=2>p2pool Name</td><td>git Source Link (for compiling&updates)</td></tr>";
while($row = mysqli_fetch_array($result)) {
  echo "<tr><td><input type=radio name=templateid value='".$row['id']."' /><td>".$row['name']."</td><td>".$row['gitsrc']."</td></tr>";
  ++$rowsCOIN;
}
if($rowsCOIN<=0){
  echo "<tr><td></td><td colspan=3><i>Sorry, there needs to be at least 1 template created for the Coin Name!</i></td></tr>";
}
echo "<tr><td>+></td><td colspan=3><a href=p2pools.add-template.php>Create Template</a></td></tr>";
echo "</table>";

if($rowsCOIN>0){
  echo "<input type=submit value='[Step 1-2]> Server Selector' /></form>";
}

mysqli_close($con);
?>
