<?php
require_once(".config.php");
require_once(".auth.php");
include('Net/SFTP.php');

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $error[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT name,coind FROM coind_templates WHERE id='".$_POST['templateid']."'");
$ctinfo = mysqli_fetch_row($result);

$result = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$_POST['serverid']."'");
$sinfo = mysqli_fetch_row($result);

$build_location = "builds/".$ctinfo[1];
$remote_sauce = "/home/".$_POST['username']."/sauce/";
$remote_sauce_src = "/home/".$_POST['username']."/sauce/src/";
$remote_location = $remote_sauce_src.$ctinfo[1];
$file_size = filesize($build_location);

if(file_exists($build_location)){
  $sftp = new Net_SFTP($sinfo[0]);
  $key = new Crypt_RSA();
  $key->loadKey(file_get_contents($sshkey_location));
  if (!$sftp->login('root', $key)) {
      $error[] = "Login Failed";
  } else {
    $debug[] = $sftp->mkdir($remote_sauce);
    $debug[] = $sftp->mkdir($remote_sauce_src);
    $debug[] = $sftp->put($remote_location, $file_size);
    $debug[] = $sftp->put($remote_location, $build_location, NET_SFTP_LOCAL_FILE);
    $debug[] = $sftp->chmod(0755, $remote_location);
    echo "Daemon uploaded!";
  }
} else {
  //if file doesnt exist, check in the database to see if we can borrow from another server ;)
  $error[] = "Unable to find coind file in the /builds folder.";
}

print_r($debug);
print_r($error);
echo '
<form action=coinds.add-do.step5.php method=post>
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=rpc_threads value="'.$_POST['rpc_threads'].'" />
<input type=hidden name=allowedips value="'.$_POST['allowedips'].'" />
<input type=hidden name=port_rpc value="'.$_POST['port_rpc'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />
<input type=submit value="[]> Save to Database" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
