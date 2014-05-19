<?php
require_once(".config.php");
require_once(".auth.php");
?>
<form action=servers.add-do.php method=post>
<table>
  <tr><td>Name</td><td><input type=text name=name /></td></tr>
  <tr><td>IP</td><td><input type=text name=ip /></td></tr>
  <tr><td>Username</td><td><input type=text name=username value=root readonly></td></tr>
</table>
<input type=submit value="Add Server" />
</form>
<br>
Public SSH Key:<br><tt>
<?php
echo file_get_contents("/home/p2pmine/.ssh/id_rsa.pub");
?>
</tt>
