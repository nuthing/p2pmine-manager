<?php
function getInfo($ip,$sshkey_location){
  $ssh = new Net_SSH2($ip);
  
  //key
  $key = new Crypt_RSA();
  $key->loadKey(file_get_contents($sshkey_location));
  if (!$ssh->login('root', $key)) {
      die("SSH Login Failed!");
  } else {
    $return['disk_usage'] = $ssh->exec("df -k .|awk 'NR==2 {print $5}'");
    $return['disk_remaining'] = (100-substr($return['disk_usage'], 0, -1))."%";
    $uptime = $ssh->exec('echo `uptime`');
    list($uptime,$load) = explode('load average:',$uptime);
    list($trash,$uptime) = explode(' up ',$uptime);
    list($uptime,$trash) = explode(', ',$uptime);
    $return['uptime'] = $uptime;
    $return['load'] = $load;
    
    //get memory
    $return['memory_free'] = $ssh->exec("cat /proc/meminfo|grep 'MemFree'");
    list($trash,$return['memory_free']) = explode('MemFree:',$return['memory_free']);
  }
  return $return;
}

//ADD SERVER
function addServer($ip_public,$ip_private){ //ip['private'] is incoded as of now
  //make sure server isn't already in database
  //if not, test to make sure we can connect....
  //if so, add us up
  //if not, return error
  return 0;
}

//ADD COINDAEMON
#function addCoinDaemon($server['id'],$coin['rpc_user'],$coin['rpc_password'],$coin['rpc_threads'],$coin['rpcallowips'],$coin['

//ADD P2POOLDAEMON
