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

echo "<form action=coinds.add.step1-2.php method=post>";

//COIND TEMPLATES
$result = mysqli_query($con,"SELECT id,name,gitsrc,port_p2p,port_rpc FROM coind_templates");
$rowsCOIN = 0;
echo "<table border=1><caption>Choose Template</cpation><tr><td colspan=2>Coin Name</td><td>git Source Link (for compiling&updates)</td></tr>";
while($row = mysqli_fetch_array($result)) {
  echo "<tr><td><input type=radio name=templateid value='".$row['id']."' /><td>".$row['name']."</td><td>".$row['gitsrc']."</td></tr>";
  ++$rowsCOIN;
}
if($rowsCOIN<=0){
  echo "<tr><td></td><td colspan=3><i>Sorry, there needs to be at least 1 template to choose from!</i></td></tr>";
}
echo "<tr><td>+></td><td colspan=3><a href=coinds.add-template.php>Create Template</a></td></tr>";
echo "</table>";

if($rowsCOIN>0){
  echo "<input type=submit value='[Step 1-2]> Server Selector' /></form>";
}

mysqli_close($con);
?>
