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

if(!mysqli_query($con,"INSERT INTO coind_templates (id,name,gitsrc,port_p2p,port_rpc,cd_gitsrc,coind) VALUES (NULL,'".$_POST['name']."','".$_POST['gitsrc']."','".$_POST['port_p2p']."','".$_POST['port_rpc']."','".$_POST['cd_gitsrc']."','".$_POST['named']."')")){
  $message[] = "Unable to insert template!";
  $debug[] = mysqli_error($con);
} else {
  $message[] = "Success! Added template to database.";
}

mysqli_close($con);
print_r($message);
print_r($debug);
?>
<a href=/manager/coinds.add.php>go back</a>
