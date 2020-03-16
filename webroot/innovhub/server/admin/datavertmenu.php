<?php
  $surveyM = new Survey();
  $surveysM = $surveyM->getSurveys(isLoggedOn());
?>        


<span class="reg_table"><b><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listsurveys">Data Tables</a></b></span>
<table border="0" cellpadding="2" cellspacing="4" bgcolor="#EEEEEE">
<?php
   for ($iM=0; $iM<count($surveysM); $iM++) {
      $cellbgM="#FFFFFF";
      if (getParameter("survey_id") == $surveysM[$iM]['survey_id']) $cellbgM="#AACCEE";
?>
<TR class="small_table">
   <TD bgcolor="<?= $cellbgM ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&survey_id=<?= $surveysM[$iM]['survey_id'] ?>&pageLimit=25"><?= $surveysM[$iM]['name'] ?></a></TD>
</TR>
<?php } ?>
</table>
