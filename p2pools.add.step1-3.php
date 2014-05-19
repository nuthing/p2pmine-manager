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
$result = mysqli_query($con,"SELECT id FROM `p2pool_instances` ORDER BY id DESC LIMIT 1");
$return = mysqli_fetch_row($result);
$id = $return[0]+1;

echo "<form action=p2pools.add-do.step2.php method=post>";

mysqli_close($con);
print_r($debug);

$allowedips = "127.0.0.1";
$allowedips .= ",".$_SERVER['SERVER_ADDR'];
?>
</table>
<table>
  <tr><td>*nix User</td><td><input type=text name=username value="<?="p2pool_".$id."_".rand(0,1000);?>" readonly />*</td></tr>
  <tr><td>Fee Address</td><td><input type=text name=address /></td></tr>
  <tr><td>Fee %</td><td><input type=text name=fee value=0.5 /></td></tr>
  <tr><td>Donation %</td><td><input type=text name=donationfee value=0 /></td></tr>
  <tr><td>P2Pool Nodes</td><td><input type=text name=nodes value="" /></td></tr>
</table>
<input type=hidden name=serverid value="<?=$_POST['serverid'];?>" />
<input type=hidden name=coindid value="<?=$_POST['coindid'];?>" />
<input type=hidden name=templateid value="<?=$_POST['templateid'];?>" />
<input type=submit value="[Step 2]> Git Source" />
</form>
