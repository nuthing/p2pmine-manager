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

if(!mysqli_query($con,"INSERT INTO p2pool_templates (id,name,named,gitsrc,port_p2p,port_worker,module) VALUES (NULL,'".$_POST['name']."','".$_POST['named']."','".$_POST['gitsrc']."','".$_POST['port_p2p']."','".$_POST['port_worker']."','".$_POST['module']."')")){
  $message[] = "Unable to insert template!";
  $debug[] = mysqli_error($con);
} else {
  $message[] = "Success! Added template to database.";
}

mysqli_close($con);
print_r($message);
print_r($debug);
?>
<a href=/manager/p2pools.add.php>go back</a>
