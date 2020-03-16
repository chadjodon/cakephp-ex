<?php
//error_reporting(E_ALL);
unset($_SESSION['params']);
$wdOBJ = new WebsiteData();
$wd_id = getParameter("wd_id");

//admincontroller.php?s_userid=10&userid=10&wd_id=3&simpledisplay=1&action=wd_listrows
$extrafields = "";
if (getParameter("s_userid")!=NULL) $extrafields .= "<input type=\"hidden\" name=\"s_userid\" value=\"".getParameter("s_userid")."\">\n";
if (getParameter("simpledisplay")!=NULL) $extrafields .= "<input type=\"hidden\" name=\"simpledisplay\" value=\"".getParameter("simpledisplay")."\">\n";
?>

<?php if ($vars['error']!= null) { ?>
   <font size="+1" color="red"><b><?php echo $vars['error']; ?></b></font>
<?php } ?>
<?php if ($vars['msg']!= null) { ?>
   <font size="+1" color="grey"><b><?php echo $vars['msg']; ?></b></font>
<?php } ?>
<?php
   print "<span class=\"button01\">";
   print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&wd_id=".$wd_id."&simpledisplay=".getParameter("simpledisplay")."&s_userid=".getParameter("s_userid")."&userid=".getParameter("userid")."\">";
   print "Cancel, Return to data table...</a></span><br><br>";
   $wdOBJ->printWebData($wd_id,NULL,NULL,NULL,NULL,NULL,"wd_listrows.php","admincontroller.php",TRUE,NULL,NULL,FALSE,$extrafields);
?>
