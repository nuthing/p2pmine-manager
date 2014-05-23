<?php
require_once(".config.php");
require_once(".auth.php");


//connect to database
$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
// Check connection
if (mysqli_connect_errno()) {
  $message[] = "Failed to connect to MySQL";
  $debug[] = mysqli_connect_error();
}

if(!mysqli_query($con,"INSERT INTO p2pool_themes (id,name,gitsrc) VALUES (NULL,'".$_POST['name']."','".$_POST['gitsrc']."')")){
  $message[] = "Unable to insert theme!";
  $debug[] = mysqli_error($con);
} else {
  $message[] = "Success! Added theme to database.";
}

mysqli_close($con);
print_r($message);
print_r($debug);
?>
<a href=/manager/p2pools.php>go back</a>
