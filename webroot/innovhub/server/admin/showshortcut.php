<?php

$template = new Template;
$ss = new SystemSettings;
$shortcut = null;
if ($vars['view'] != null) $shortcut = $ss->getViewShortcut($vars['view']);
$contents = $template->getFileWithoutSub($GLOBALS['configDir'].$shortcut['url'],FALSE);
?>


<table width="100%" cellpadding="0" cellspacing="0">
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="scForm" method="POST">
<tr><td  align="right" colspan="2"><input type="submit" name="Save" value="Save"></td></tr>

<?php
if ($shortcut['view'] == null) {
?>

<input type="hidden" name="action" value="editview">
<tr><td colspan="2" align="center"><h2>Add a new page to the site</h2></td></tr>
<tr><td>Short name: </td><td><input type="text" size="80" name="view" value="<?= $vars['view'] ?>"></td></tr>

<?php
}
else {
?>

<input type="hidden" name="action" value="editview">
<input type="hidden" name="view" value="<?= $shortcut['view'] ?>">
<tr><td colspan="2" align="center"><h2>Update a page on the site</h2></td></tr>
<tr><td>Short name: </td><td><b><?= $shortcut['view'] ?></b></td></tr>

<tr>
     <td>Site url: </td>
     <td>
         <a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$shortcut['view'].".html" ?>" target="_new">
         <?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$shortcut['view'].".html" ?></a>
     </td>
</tr>

<?php
}
?>

<tr><td>Title: </td><td><input type="text" size="80" name="title" value="<?= $shortcut['title'] ?>"></td></tr>
<tr><td>Description: </td><td><input type="text" size="80" name="descr" value="<?= $shortcut['descr'] ?>"></td></tr>
<tr><td>Key Words: </td><td><input type="text" size="80" name="keywords" value="<?= $shortcut['keywords'] ?>"></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">HTML:</td></tr>
<tr><td colspan="2"><textarea name="contents" rows="40" cols="90" wrap="off"><?= $contents ?></textarea></td></tr>
<tr><td  align="right" colspan="2"><input type="submit" name="Save" value="Save"></td></tr>
</form>

<?php
if ($shortcut['view'] != null) {
?>

<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
   <tr><td colspan="2">

      <table border="0" width="100%" cellpadding="2" cellspacing="0" bgcolor="#AACCEE"><TR><TD>
      <table border="0" width="100%" cellpadding="3" cellspacing="2" bgcolor="#FFFFFF">
      <TR><TD colspan="2"><h2>Dream Weaver</h2></td></tr>
      <tr>
        <td bgcolor="lightgrey">DreamWeaver url: </td>
        <td>
            <a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessExportDir'].$shortcut['view'].".html" ?>" target="_new">
            <?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessExportDir'].$shortcut['view'].".html" ?></a>
        </td>
      </tr>

          <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
          <input type="hidden" name="view" value="<?= $shortcut['view'] ?>">
          <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
          <input type="hidden" name="action" value="uploadDW">
          <TR>
              <TD bgcolor="lightgrey">Upload this file:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
              <TD><input name="userfile" type="file"></TD>
          </TR>
          <TR>
             <td colspan="2">
             <input type="submit" value="Upload Your File">
             </td>
          </tr>
          </form>
      

      </table>
      </td></tr></table>

   </td></tr>
<?php
   }
?>

</table>
