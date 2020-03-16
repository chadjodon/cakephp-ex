<?php 
   $glossary = new Glossary();
   $glossaries = $glossary->getGlossaries();
?>

<table cellpadding="0" cellspacing="0">
<tr><td align="center"><h2>Glossaries</h2></td></tr>
<tr><td>
   Listed below are the Glossaries available in the system.<br>
   Click "view" to see the terms/definitions for any of the glossaries, or create a new glossary.
</td></tr>
<tr><td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="10"></td></tr>
<tr><td>
<table cellpadding="3" cellspacing="1" bgcolor="#AAAAAA">
<tr bgcolor="#EEEEEE">
   <td> </td>
   <td>Glossary Name</td>
   <td>Short description</td>
   <td></td>
</tr>

<?php
   for ($i=0; $i<count($glossaries); $i++) {
      $line = $glossaries[$i];
?>
<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="editglossary">
   <input type="hidden" name="glossid" value="<?php echo $line['glossid']; ?>">
   <td><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displayglossaryterms&glossid=<?php echo $line['glossid']; ?>">View</a></td>
   <td><input type="text" size="40" name="glosstitle" value="<?php echo $line['glosstitle']; ?>"></td>
   <td><textarea name="descr" rows="3" cols="50"><?php echo $line['descr']; ?></textarea></td>
   <td><input type="submit" name="subaction" value="Update"><br><input type="submit" name="subaction" value="Delete"></td>
   </form>
</tr>

<?php   } ?>

<tr bgcolor="#EEEEEE">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="addglossary">
   <td></td>
   <td><input type="text" size="40" name="glosstitle" value=""></td>
   <td><textarea name="descr" rows="3" cols="50"></textarea></td>
   <td><input type="submit" name="subaction" value="Add"></td>
   </form>
</tr>
</table>
</td></tr></table>
