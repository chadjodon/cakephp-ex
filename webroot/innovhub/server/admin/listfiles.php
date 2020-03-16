<table width="600" cellpadding="2" cellspacing="0" border="0">
<tr><td>File Name</td><td>Shortcut</td><td>Action</td></tr>

<?php
  $files = $template->list_dir($GLOBALS['configDir'].$vars['dir']);

   for ($i=0; $i<count($files); $i++) {
      $rowClass = ($i % 2) +1;
      $sc = $ss->getShortcutByURL($vars['dir'].$files[$i]);
      if ($sc != null) {
         //indicate that this shortcut exists and can be edited
         $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showshortcut&view=".$sc['view']."\">Edit</a>";
      }
      else {
         $view = substr($files[$i],0,(strlen($files[$i])-5));
         //allow user to create a shortcut since it's not already one
         $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=editview&view=".$view."&url=".$vars['dir'].$files[$i]."\">Create shortcut</a>";
      }
?>
      <tr class="list_row<?= $rowClass ?>"><td><?= $vars['dir'].$files[$i] ?></td><td><?= $sc['view'] ?></td><td><?= $link ?></td></tr>

<?php
   }
?>

</table>
