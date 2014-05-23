<?php
require_once(".config.php");
require_once(".auth.php");
if($_GET['id']<=0){
  die("Please go back and select a p2pool!");
}

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $debug[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}
$result = mysqli_query($con,"SELECT id,name,gitsrc FROM p2pool_themes");

echo '<form action=p2pools.theme-do.php method=post>';

echo "<table border=1><caption>Choose Theme</cpation><tr><td>Name</td><td>Source</td></tr>";
$rows = 0;
while($row = mysqli_fetch_array($result)){
   echo "<tr><td><input type=radio name=themeid value='".$row['id']."' /> ".$row['name']."</td><td>".$row['gitsrc']."</td>";
   ++$rows;
}

if($rows<=0){
  echo "<tr><td>~</td><td><i>Sorry, there needs to be at least 1 theme to choose from!</i></td></tr>";
}
echo "<tr><td>+></td><td colspan=2><a href=p2pools.add-theme.php>Add Theme</a></td></tr>";
echo "</table>";

if($rows>0){
  echo "<input type=hidden name=id value='".$_GET['id']."' />";
  echo "<input type=submit value='Install Theme' /></form>";
}

mysqli_close($con);
print_r($debug);
