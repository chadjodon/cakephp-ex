<?php

$ss = new SystemSettings;
$shortcuts = $ss->getAllShortcuts();
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td align="left">
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showshortcut">Add a New Page to the site</a>
</td><td align="right">
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displaysitemenu">Show Site Menu</a>
</td></tr>
</table>



<BR>

<table width="100%" cellpadding="2" cellspacing="0" border="1">
<tr><th>Shortname</th><th>Site URL</th></tr>

<?php
for ($i=0; $i<count($shortcuts); $i++) {
	$line = $shortcuts[$i];
   $rowClass = ($i % 2) +1;
	
	
?>

<tr class='list_row<?= $rowClass ?>'>
<td>
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showshortcut&view=<?= $line['view'] ?>">
<?= $line['view'] ?></a>
</td>

<td>
<a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$line['view'].".html" ?>" target="_new">
<?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$line['view'].".html" ?></a>
</td>

</tr>

<?php
}
?>

</table><br><br>
