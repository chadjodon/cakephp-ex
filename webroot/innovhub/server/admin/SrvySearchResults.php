<?php
   $surveyOBJ = new Survey();
   $url = "admincontroller.php?survey_id=".$vars['survey_id']."&action=srvysearchresults&cmslimit=".getParameter("cmslimit")."&cmsorderby=".getParameter("cmsorderby");
   $urlPages = "admincontroller.php?survey_id=".$vars['survey_id']."&action=srvysearchresults";
   $searchHtml = $surveyOBJ->getCMSSearchFields($vars['survey_id'],$url);
   $tableHtml = $surveyObj->getFullTable($vars['survey_id'],$urlPages);
?>

<table width="100%" cellpadding="2" cellspacing="0">
<tr align="left" valign="top">
<td>
   <?php echo $searchHtml; ?>
   <br>
   <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?survey_id=<?php echo $vars['survey_id']; ?>&action=srvysearchresults&newentry=1">Add New Entry</a>
</td>
<td><?php echo $tableHtml; ?></td>
</tr>
</table>
