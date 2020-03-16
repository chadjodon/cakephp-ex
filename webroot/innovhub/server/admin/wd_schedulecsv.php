<?php
   $wdOBJ = new WebsiteData();
   $url = "admincontroller.php?wd_id=".$vars['wd_id']."&action=wd_listrows&pageLimit=25&cmslimit=".getParameter("cmslimit")."&cmsorderby=".getParameter("cmsorderby");
   $url2 = "admincontroller.php?wd_id=".$vars['wd_id']."&action=scheduledcsvs";
   $searchHtml = "";
   $searchHtml .= $wdObj->getSearchHTMLAllFields("Org Properties");
   $searchHtml .= "<tr><td colspan=\"2\"><br><HR></td></tr>";
   $searchHtml .= $wdOBJ->getSearchHTMLAllFields(NULL, $vars['wd_id']);
?>


<table width="100%" cellpadding="2" cellspacing="1">
<form name="cancelSearchSrvy" action="<?php echo $url; ?>" method="POST">
<tr><td colspan="2" align="left"><input type="submit" name="submit" value="Cancel Search and Return"></td></tr>
</form>
</table>

<br><br>

<table width="100%" cellpadding="2" cellspacing="1">
<form name="searchSrvy" action="<?php echo $url2; ?>" method="POST">
<input type="hidden" name="scheduleSurveyCSV" value="1">
<tr><td>Search</td><td><input type="text" name="filterStr" value=""></td></tr>
<tr><td colspan="2"><hr></td></tr>
<?php print $searchHtml; ?>
<tr><td colspan="2" align="center">
   <table cellpadding="0" cellspacing="0"><tr>
   <td>Name this upload: </td>
   <td><input type="text" name="subject" value=""></td>
   <td>&nbsp; &nbsp;</td>
   <?php
      $dateOpt = getDateSelection(date("d"),date("m"),date("Y"),"start");
      $timeOpt = getTimeSelection(date("h"),date("i"),date("A"),"start");
   ?>
   <td>Start this upload at: </td>
   <td><?php echo $dateOpt; ?></td>
   <td><?php echo $timeOpt; ?></td>
   <td>&nbsp; &nbsp;</td>
   <td><input type="submit" name="submit" value="Generate CSV"></td>
   </tr></table>
</td></tr>
</form>
</table>
