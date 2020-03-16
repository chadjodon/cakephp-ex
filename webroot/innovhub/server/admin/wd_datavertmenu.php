<?php
  $webdataM = new WebsiteData();
  $webdatasM = $webdataM->getWebTables(isLoggedOn());
?>        


<span class="reg_table"><b><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listwebdata">Data Tables</a></b></span>
<table border="0" cellpadding="2" cellspacing="4" bgcolor="#EEEEEE">
<?php
   for ($iM=0; $iM<count($webdatasM); $iM++) {
      $cellbgM="#FFFFFF";
      if (getParameter("wd_id") == $webdatasM[$iM]['wd_id']) $cellbgM="#AACCEE";
?>
<TR class="small_table">
   <TD bgcolor="<?= $cellbgM ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $webdatasM[$iM]['wd_id'] ?>&pageLimit=25"><?= $webdatasM[$iM]['name'] ?></a></TD>
</TR>
<?php } ?>
</table>
