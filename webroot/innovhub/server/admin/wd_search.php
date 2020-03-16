<?php
   $wdOBJ = new WebsiteData();
   $url = "admincontroller.php?wd_id=".$vars['wd_id']."&action=wd_listrows&pageLimit=25&cmslimit=".getParameter("cmslimit")."&cmsorderby=".getParameter("cmsorderby");
   //$searchHtml = $wdOBJ->getCMSSearchFields($vars['wd_id'],$url);
   $searchHtml = "";
   //$searchHtml .= $wdObj->getSearchHTMLAllFields("Org Properties");
   //$searchHtml .= "<tr><td colspan=\"2\"><br><BR></td></tr>";
   $searchHtml .= $wdOBJ->getSearchHTMLAllFields(NULL, $vars['wd_id']);
?>


<table width="100%" cellpadding="2" cellspacing="1">
<form name="cancelSearchSrvy" action="<?php echo $url; ?>" method="POST">
<tr><td colspan="2" align="left"><input type="submit" name="submit" value="Cancel Search and Return"></td></tr>
</form>
</table>

<br><br>

<table width="100%" cellpadding="2" cellspacing="1">
<form name="searchSrvy" action="<?php echo $url; ?>" method="POST">
<?php print $searchHtml; ?>
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Search"></td></tr>
</form>
</table>
