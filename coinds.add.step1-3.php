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
$result = mysqli_query($con,"SELECT id FROM `coind_instances` ORDER BY id DESC LIMIT 1");
$return = mysqli_fetch_row($result);
$id = $return[0]+1;


echo "<form action=coinds.add-do.step2.php method=post>";

$allowedips = "127.0.0.1";
$allowedips .= ",".$_SERVER['SERVER_ADDR'];

$result = mysqli_query($con,"SELECT ip FROM servers");
while($row = mysqli_fetch_array($result)){
  if(strpos($row['ip'],$allowedips) === false){
    $allowedips .= ",".$row['ip'];
  }
}

mysqli_close($con);
print_r($debug);
?>
</table>
<table>
  <tr><td>*nix User</td><td><input type=text name=username value="<?="coind_".$id."_".rand(0,1000);?>"/>*</td></tr>
  <tr><td>rpcuser</td><td><input type=text name=rpc_user value="<?=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);?>" /></td></tr>
  <tr><td>rpcpassword</td><td><input type=text name=rpc_password value="<?=substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 32);?>" /></td></tr>
  <tr><td>rpcthreads</td><td><select name=rpc_threads><option value=100>100</option><option value=250 selected>250</option><option vaue=500>500</option><option value=1000>1000</option></select></td></tr>
  <tr><td>rpcallowip</td><td><input type=text name=allowedips value='<?=$allowedips;?>' /></td></tr>
</table>
<input type=hidden name=serverid value="<?=$_POST['serverid'];?>" />
<input type=hidden name=templateid value="<?=$_POST['templateid'];?>" />
<input type=submit value="[Step 2]> Add User & Save Configuration" />
</form>
<br>
<b>* The username MUST be unique!</b>
