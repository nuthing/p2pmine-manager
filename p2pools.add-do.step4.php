<?php
require_once(".config.php");
require_once(".auth.php");

if(strlen($_POST['serverid'])<=0 || strlen($_POST['templateid'])<=0){
  die("Please go back and select a Server AND a Template to use!");
}

$con=mysqli_connect($db['host'],$db['user'],$db['pass'],$db['name']);
if (mysqli_connect_errno()) {
  $debug[] = "Failed to connect to MySQL";
  $debug['mysql_error'] = mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT name FROM p2pool_templates WHERE id='".$_POST['templateid']."'");
$info = mysqli_fetch_row($result);

$info['name'] = $info[0];


$result2 = mysqli_query($con,"SELECT ip FROM servers WHERE id='".$_POST['serverid']."'");
$serverinfo = mysqli_fetch_row($result2);

$serverinfo['ip'] = $serverinfo[0];


//log into server.,
$ssh = new Net_SSH2($serverinfo['ip']);
$key = new Crypt_RSA();
$key->loadKey(file_get_contents($sshkey_location));
if (!$ssh->login('root', $key)) {
  $debug[] = "SSH Login Failed!";
} else {  
  
  //insert into coind_instances $port_rpc,$port_p2p in database
  if(!mysqli_query($con,"INSERT INTO p2pool_instances (id,templateid,serverid,coindid,username,hostname,port_p2p,port_worker,fee,donationfee,address,nodes) VALUES 
  (NULL,'".$_POST['templateid']."','".$_POST['serverid']."','".$_POST['coindid']."','".$_POST['username']."','".$serverinfo['ip']."','".$_POST['port_p2p']."','".$_POST['port_worker']."','".$_POST['fee']."','".$_POST['donationfee']."','".$_POST['address']."','".$_POST['nodes']."')")){
    $debug['mysql_error'] = "Unable to add p2pool instance to database!";
    $debug['mysql_error_raw'] = mysqli_error($con);
  } else {
    $debug['mysql_success'] = "Success! P2Pool instance added to database.";
  }
  
  $debug['chmod_dir'] = $ssh->exec("chown -R ".$_POST['username']." /home/".$_POST['username']."/sauce");
}

mysqli_close($con);
print_r($debug);

echo '
<form action=p2pools.add-do.step-FIN.php method=post>
<input type=hidden name=address value="'.$_POST['address'].'" />
<input type=hidden name=fee value="'.$_POST['fee'].'" />
<input type=hidden name=donationfee value="'.$_POST['donationfee'].'" />
<input type=hidden name=nodes value="'.$_POST['nodes'].'" />
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=coindid value="'.$_POST['coindid'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=port_worker value="'.$_POST['port_worker'].'" />
<input type=hidden name=port_p2p value="'.$_POST['port_p2p'].'" />
<input type=submit value="[FIN]> Start Daemon" onclick="this.value = \"Please wait...\"; this.disabled = true"/>
</form>';
