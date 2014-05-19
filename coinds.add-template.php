<?php
require_once(".config.php");
require_once(".auth.php");

?>
<form action=coinds.add-template-do.php method=post>
<table>
  <tr><td>Coin Name</td><td><input type=text name=name value=bitcoin /></td></tr>
  <tr><td>Coin Daemon Name</td><td><input type=text name=named value=bitcoind /></td></tr>
  <tr><td>Git Source</td><td><input type=text name=gitsrc /></td></tr>
  <tr><td>Compile Folder</td><td><input type=text name=cd_gitsrc value="src" />*</td></tr>
  <tr><td>Default P2P Port</td><td><input type=number name=port_p2p min="1025" max="65535" value=1025 /></td></tr>
  <tr><td>Default RPC Port</td><td><input type=number name=port_rpc min="1025" max="65535" value=1026 /></td></tr>
</table>
<input type=submit value="Add Template" />
</form>
<br>
* This should typically be <tt>src</tt>. Needs to be where the <tt>makefile.unix</tt> file is located.
