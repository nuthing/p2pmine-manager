<?php
require_once(".config.php");
require_once(".auth.php");

?>
<form action=p2pools.add-template-do.php method=post>
<table>
  <tr><td>Coin Name</td><td><input type=text name=name value=bitcoin /></td></tr>
  <tr><td>Daemon Name</td><td><input type=text name=named value=run_p2pool.py /></td></tr>
  <tr><td>Git Source</td><td><input type=text name=gitsrc /></td></tr>
  <tr><td>Default P2P Port</td><td><input type=number name=port_p2p min="1025" max="65535" value=1025 /></td></tr>
  <tr><td>Default Worker Port</td><td><input type=number name=port_worker min="1025" max="65535" value=1026 /></td></tr>
  <tr><td>Module</td><td><select name=module><option value=ltcscrypt>LTC Scrypt</option><option value=scryptn>Scrypt-N</option><option value=xcoin-hash>xcoin-hash</option></select></td></tr>
</table>
<input type=submit value="Add Template" />
</form>
