<?php
include_once("../jsfcode/Classes.php");
//error_reporting(E_ALL);
unset($_SESSION['params']);

$wdOBJ = new WebsiteData();
$template = new Template;
$template->getSurveyTop($survey['name'],$vars);

$ua = new UserAcct();
if ($ua->isUserAdmin(isLoggedOn())) {

   $wd_id = getParameter("wd_id");
   $origemail = getParameter("origemail");
   $userid = getParameter("userid");
   $wd_row_id = getParameter("wd_row_id");
?>

<?php if ($vars['error']!= null) { ?>
   <font size="+1" color="red"><b><?php echo $vars['error']; ?></b></font>
<?php } ?>
<?php if ($vars['msg']!= null) { ?>
   <font size="+1" color="grey"><b><?php echo $vars['msg']; ?></b></font>
<?php } ?>

<?php if ($vars['showThankyou'] ==1) { ?>
   <table border="0" cellspacing="0" cellpadding="1" width="100%">
      <tr><td valign="top"><img src="../jsfadmin/images/pixel.gif" width="15" height="15" alt="Spacer"><br/>
                  <font color="green" size="+1">
                     Your data has been submitted <b><u>successfully</u></b> - 

                        <script type="text/javascript">
                        <!--
                        var currentTime = new Date();
                        var month = currentTime.getMonth() + 1;
                        var day = currentTime.getDate();
                        var year = currentTime.getFullYear();
                        document.write(month + "/" + day + "/" + year + " ");

                        var hour = currentTime.getHours();
                        var minute = currentTime.getMinutes();
                        var second = currentTime.getSeconds();

                        if (minute < 10) { minute = "0" + minute; }
                        if (second < 10) { second = "0" + second; }
                        var ap = "AM";
                        if (hour   > 11) { ap = "PM";        }
                        if (hour   > 12) { hour = hour - 12; }
                        if (hour   == 0) { hour = 12;        }

                        document.write(hour + ":" + minute + ":" + second + " " + ap);

                        //-->
                        </script>

                     <br>
                     Thank you!
                  </font>
                  <br><BR>
       </TD></TR>
       <tr><td><BR><hr></td></tr>
     </table>
<?php }
   $wdOBJ->printWebData($wd_id,$origemail,$userid,$wd_row_id);
} else {
   print "<br><b>You were logged out of the admin system.  Please log back in to view/change survey data.</b><br>";
}



$template->getSurveyBottom($vars);
?>
