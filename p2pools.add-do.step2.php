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

$result = mysqli_query($con,"SELECT coinname,ip_p2p,port_p2p,port_rpc,rpc_user,rpc_password FROM coind_instances WHERE id='".$_POST['coindid']."'");
$coininfo = mysqli_fetch_row($result);
$coininfo['coinname'] = $coininfo[0];
$coininfo['ip_p2p'] = $coininfo[1];
$coininfo['port_p2p'] = $coininfo[2];
$coininfo['port_rpc'] = $coininfo[3];
$coininfo['rpc_user'] = $coininfo[4];
$coininfo['rpc_password'] = $coininfo[5];

$result = mysqli_query($con,"SELECT name,named,gitsrc,port_p2p,port_worker FROM p2pool_templates WHERE id='".$_POST['templateid']."'");
$info = mysqli_fetch_row($result);

$info['name'] = $info[0];
$info['named'] = $info[1];
$info['gitsrc'] = $info[2];
$info['port_p2p'] = $info[3];
$info['port_worker'] = $info[4];


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
  //check if ports are in used
    //yes? randomly choose ports and check those yes?...repeat
  $port = $info['port_p2p'];
  $valid_port = false;
  while(!$valid_port){
    $debug['port_check'] = $ssh->exec('netstat -ln | grep \':'.$port.' \' | grep \'LISTEN\'');
    if(strlen($debug['port_check'])>0){
      $port = rand(1025,65535);
    } else {
      $valid_port = true;
      $port_p2p = $port;
    }
  }  
  
  $port = $info['port_worker'];
  $valid_port = false;
  while(!$valid_port){
    $debug['port_check'] = $ssh->exec('netstat -ln | grep \':'.$port.' \' | grep \'LISTEN\'');
    if(strlen($debug['port_check'])>0){
      $port = rand(1025,65535);
    } else {
      $valid_port = true;
      $port_worker = $port;
    }
  }  
  $debug['adduser'] = $ssh->exec('adduser '.$_POST['username'].' --disabled-password --gecos "First Last,RoomNumber,WorkPhone,HomePhone"');
  
  $debug['step'][] = "user created...";

  $debug['mkdir_coinname'] = $ssh->exec('mkdir /home/'.$_POST['username'].'/.'.$info['name']);
  
  $debug['step'][] = "created coind config directory...";

  //build the config
  $coinconfig = "server=0\n";
  $coinconfig .= "daemon=0\n";
  $coinconfig .= "port=".$coininfo['port_p2p']."\n";
  $coinconfig .= "rpcport=".$coininfo['port_rpc']."\n";
  $coinconfig .= "rpcuser=".$coininfo['rpc_user']."\n";
  $coinconfig .= "rpcpassword=".$coininfo['rpc_password']."\n";
  
  //latter check if we have other coinds running, if so...add those as nodes
  
  //now submit it
  $debug['mkdir_coinname'] = $ssh->exec('printf "'.$coinconfig.'" >> /home/'.$_POST['username'].'/.'.$info['name'].'/'.$info['name'].'.conf');
  
  $debug['step'][] = "created & saved config file...";

  $debug['chown_coinddir'] = $ssh->exec('chown -R '.$_POST['username'].' /home/'.$_POST['username'].'/.'.$info['name']);
  
  $debug['step'][] = "chown'ed directory & .conf file...";
  
}

mysqli_close($con);
print_r($debug);

echo '
<form action=p2pools.add-do.step3.php method=post>
<input type=hidden name=address value="'.$_POST['address'].'" />
<input type=hidden name=fee value="'.$_POST['fee'].'" />
<input type=hidden name=donationfee value="'.$_POST['donationfee'].'" />
<input type=hidden name=nodes value="'.$_POST['nodes'].'" />
<input type=hidden name=templateid value="'.$_POST['templateid'].'" />
<input type=hidden name=serverid value="'.$_POST['serverid'].'" />
<input type=hidden name=coindid value="'.$_POST['coindid'].'" />
<input type=hidden name=rpc_user value="'.$_POST['rpc_user'].'" />
<input type=hidden name=rpc_password value="'.$_POST['rpc_password'].'" />
<input type=hidden name=username value="'.$_POST['username'].'" />
<input type=hidden name=port_worker value="'.$port_worker.'" />
<input type=hidden name=port_p2p value="'.$port_p2p.'" />
<input type=submit value="[Step 3]> git source" onclick="this.value = \"Please wait...\"; this.disabled = true" />
</form>';
