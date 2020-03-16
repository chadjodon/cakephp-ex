<?php
//$startdate, $ctype, $display, $other, $contdata, $sequence=10
if ($vars['contid'] != null) {
   $contObj = $pubcont->getContent($vars['contid']);
   $dateField = $contObj['startdate'];
   $action = "editpubcont";
   $title = "Edit Content";
} else {
   $dateField = getDateForDB();
   $action = "addpubcont";
   $title = "Add Content";
}
?>

<h2><?php echo $title; ?></h2>
<table border="1" cellpadding="2" cellspacing="0">
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
<input type="hidden" name="action" value="<?php echo $action; ?>">
<input type="hidden" name="contid" value="<?php echo $contObj['contid']; ?>">

<tr><td>Date:</td><td><input type="text" name="startdate" value="<?php echo $dateField; ?>" size="20"></td></tr>
<tr><td>Type of Content:</td><td><input type="text" name="ctype" value="<?php echo $contObj['ctype']; ?>" size="10"></td></tr>
<tr><td>Title of Content:</td><td><input type="text" name="display" value="<?php echo $contObj['display']; ?>" size="40"></td></tr>
<tr><td>Other:</td><td><input type="text" name="other" value="<?php echo $contObj['other']; ?>" size="40"></td></tr>
<tr><td>Sequence:</td><td><input type="text" name="sequence" value="<?php echo $contObj['sequence']; ?>" size="10"></td></tr>
<tr><td colspan="2">Content:<BR><textarea name="contdata" rows="25" cols="50"><?php echo $contObj['contdata']; ?></textarea></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="submit"></td></tr>


</form>
</table>
