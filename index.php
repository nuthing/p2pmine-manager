<?php
require_once(".config.php");
require_once(".auth.php");
?>

<table>
  <tr>
    <td colspan=2>
      <strong><a href=servers.php>Servers</a></strong>
      <?php include_once('servers.php'); ?><br>
    </td>
  </tr>
  <tr>
    <td valign="top">
      <strong><a href=coinds.php>Coind Instances</a></strong>
      <?php $mod['coinds']['disable_functions'] = true; include_once('coinds.php'); ?>
    </td>
    <td valign="top">
      <strong><a href=p2pools.php>P2Pool Instances</a></strong>
      <?php $mod['p2pools']['disable_functions'] = true; include_once('p2pools.php'); ?>
    </td>
  </tr>
</table>
