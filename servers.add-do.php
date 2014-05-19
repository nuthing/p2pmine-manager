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

if(!mysqli_query($con,"INSERT INTO servers (id,name,ip) VALUES (NULL,'".$_POST['name']."','".$_POST['ip']."')")){
  $error[] = "Unable to insert new server!";
  $debug[] = mysqli_error($con);
} else {
  $debug[] = "Success! Added server to database.";
}

mysqli_close($con);
print_r($error);
print_r($debug);
?>
