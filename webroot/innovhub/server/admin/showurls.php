<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td align="left">
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showshortcut">Add a New Page to the site</a>
</td><td align="right">
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displaysitemenu">Show Site Menu</a>
</td></tr>
</table>
<HR>

<?php

//$ss = new SystemSettings;
//$template = new Template;
//$menu = new mm_menu;

$shortcuts = $ss->getAllShortcuts();
$files = $template->list_dir($GLOBALS['configDir'].$vars['dir']);

$cnt_files = 0;
$arr_files = null;
$cnt_both  = 0;
$arr_both  = null;

for ($i=0; $i<count($files); $i++) {
      $sc = $ss->getShortcutByURL($vars['dir'].$files[$i]);
      if ($sc != null) {
         $arr_both[$cnt_both]['view'] = $sc['view'];
         $arr_both[$cnt_both]['url']  = $sc['url'];
         $cnt_both++;

         for ($j=0; $j<count($shortcuts); $j++) {
            if (0== strcmp($shortcuts[$j]['view'],$sc['view'])) {
               array_splice($shortcuts, $j, 1);
               break;
            }
         }
      }
      else {
         $view = substr($files[$i],0,(strlen($files[$i])-5));
         $arr_files[$cnt_files]['view'] = $view;
         $arr_files[$cnt_files]['url']  = $vars['dir'].$files[$i];
         $cnt_files++;
      }
}



?>


<!-- *************** System Pages - both in DB and on filesystem **************** -->
<?php  if (count($arr_both)>0) { ?>

<h2>System Pages</h2>
<table width="100%" cellpadding="3" cellspacing="0" border="0">
<tr class="list_rowheader"><td>File Name</td><td>Shortcut</td><td>In menu?</td><td>Action</td></tr>

<?php

   for ($i=0; $i<count($arr_both); $i++) {
      $rowClass = ($i % 2) +1;
      $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showshortcut&view=".$arr_both[$i]['view']."\">[Edit]</a>";

      $results = $menu->getMenu($GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$arr_both[$i]['view'].".html");
      if (count($results)>0) $inMenu="<font color=\"green\" face=\"bold\"><b>Yes</b></font>";
      else $inMenu="<font color=\"red\"><b>No</b></font>";
?>
      <tr class="list_row<?= $rowClass ?>">
      <td><a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$arr_both[$i]['view'] ?>.html" target="_new"><?= $GLOBALS['baseURLSSL'].$GLOBALS['htaccessDir'].$arr_both[$i]['view'] ?>.html</a></td>
      <td><?= $arr_both[$i]['view'] ?></td>
      <td><?= $inMenu ?></td>
      <td><?= $link ?></td>
      </tr>
<?php
   }
?>

</table>
<HR>
<br><br>

<?php  }  ?>



<!-- *************** System Pages - in DB, but missing on filesystem **************** -->

<?php  if (count($shortcuts)>0) { ?>

<h2>System Database Views With Missing Files</h2>
<table width="100%" cellpadding="3" cellspacing="0" border="0">
<tr class="list_rowheader"><td>File Name</td><td>Shortcut</td><td>Action</td></tr>

<?php

   for ($i=0; $i<count($shortcuts); $i++) {
      $rowClass = ($i % 2) +1;
      $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showshortcut&view=".$shortcuts[$i]['view']."\">[Add File]</a>";
?>
      <tr class="list_row<?= $rowClass ?>"><td><?= $shortcuts[$i]['url'] ?></td><td><?= $shortcuts[$i]['view'] ?></td><td><?= $link ?></td></tr>
<?php
   }
?>

</table>
<HR>
<br><br>

<?php  }  ?>


<!-- *************** System Pages - on filesystem, but not in DB **************** -->
<?php  if (count($arr_files)>0) { ?>

<h2>System Files</h2>
<table width="100%" cellpadding="3" cellspacing="0" border="0">
<tr class="list_rowheader"><td>File Name</td><td>Action</td></tr>

<?php

   for ($i=0; $i<count($arr_files); $i++) {
      $rowClass = ($i % 2) +1;
      $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=editview&view=".$arr_files[$i]['view']."&url=".$arr_files[$i]['url']."\">[Create shortcut]</a>";
?>
      <tr class="list_row<?= $rowClass ?>"><td><?= $arr_files[$i]['url'] ?></td><td><?= $link ?></td></tr>
<?php
   }
?>

</table>
<HR>
<br><br>

<?php  }  ?>


