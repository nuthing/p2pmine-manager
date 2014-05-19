<?php
$valid_users = array_keys($auth);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && (sha1(md5($auth_salt.sha1($pass))) == $auth[$user]);

if (!$validated) {
  header('WWW-Authenticate: Basic realm="p2pmine-manager Portal"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}
?>
