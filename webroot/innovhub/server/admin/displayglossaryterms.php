<?php 
   $glossary = new Glossary($vars['glossid']);
   $terms = $glossary->getGlossary();
?>

<table cellpadding="0" cellspacing="0">
<tr><td><h2>Terms & definitions for "<?php echo $glossary->getGlossaryTitle(); ?>".</h2></td></tr>
<tr><td><a href="/jsfadmin/admincontroller.php?action=displayglossaries">Return to all glossaries</a></td></tr>
<tr><td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="10"></td></tr>
<tr><td>
<table cellpadding="3" cellspacing="1" bgcolor="#AAAAAA">
<tr bgcolor="#EEEEEE">
   <td>Glossary Term/Alternate spelling</td>
   <td>Definition</td>
   <td></td>
</tr>

<?php
   for ($i=0; $i<count($terms); $i++) {
      $line = $terms[$i];
?>
<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="editglossaryterm">
   <input type="hidden" name="glossaryid" value="<?php echo $vars['glossid']; ?>">
   <input type="hidden" name="glossid" value="<?php echo $vars['glossid']; ?>">
   <input type="hidden" name="term" value="<?php echo $line['term']; ?>">
   <td class="tinytext"><font size="+1"><b><?php echo $line['term']; ?></b></font><br/>Other spelling: <input type="text" size="40" name="alternates" value="<?php echo $line['alternates']; ?>"></td>
   <td><textarea name="definition" rows="4" cols="55"><?php echo $line['definition']; ?></textarea></td>
   <td><input type="submit" name="subaction" value="Update"><br><input type="submit" name="subaction" value="Delete"></td>
   </form>
</tr>

<?php   } ?>

<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="addglossaryterm">
   <input type="hidden" name="glossaryid" value="<?php echo $vars['glossid']; ?>">
   <input type="hidden" name="glossid" value="<?php echo $vars['glossid']; ?>">
   <td class="tinytext">Term: <input type="text" size="30" name="term" value=""><br/>Other spelling: <input type="text" size="40" name="alternates" value=""></td>
   <td><textarea name="definition" rows="4" cols="55"></textarea></td>
   <td><input type="submit" name="subaction" value="Add"></td>
   </form>
</tr>
</table>
</td></tr></table>
