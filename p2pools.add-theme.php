<?php
require_once(".config.php");
require_once(".auth.php");

?>
<form action=p2pools.add-theme-do.php method=post>
<table>
  <tr><td>Name</td><td><input type=text name=name /></td></tr>
  <tr><td>Git Source</td><td><input type=text name=gitsrc /></td></tr>
</table>
<input type=submit value="Add Theme" />
</form>
