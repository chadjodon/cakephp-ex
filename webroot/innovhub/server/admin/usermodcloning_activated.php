<?php
//print "\n<!-- in admincontroller.php, including: Classes,CustomCMS_Admin -->\n";
include_once "../jsfcode/Classes.php";
//error_reporting(E_ALL);
include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS_Admin.php";
$ua=new UserAcct();

if (!$ua->isUserAdmin(isLoggedOn())) {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 Sorry, you are not authorized to perform this function.
 </div>

<?php
} else {
   $xuser = $ua->getUser(getParameter("userid"));
   $action = getParameter("action");
   if (0==strcmp($action,"activate")) {
      $ua->activateAccount($xuser['userid'],FALSE);
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 This account was successfully activated.  You will need to refresh your user account screen to reflect this update.
 </div>

<?php
   } else if (0==strcmp($action,"deactivate")) {
      $ua->deactivateAccount($xuser['userid'],getParameter("reason"),FALSE);
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 This account was successfully deactivated.  You will need to refresh your user account screen to reflect this update.
 </div>

<?php
   } else if ($xuser['activated']==1) {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 Enter a reason below to deactivate this account.<BR>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <form action="usermodcloning_activated.php" method="post">
 <input type="hidden" name="action" value="deactivate">
 <input type="hidden" name="userid" value="<?php echo $xuser['userid']; ?>">
 <input type="text" name="reason" style="width:200px;font-size:14px;font-family:arial;" value="<?php echo $xuser['activatedstr']; ?>"><br>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <input type="submit" name="submit" value="Deactivate">
 </form>
 </div>

<?php
   } else if ($xuser['activated']==0) {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 This account is currently inactive (Reason: <?php echo $xuser['activatedstr']; ?>)<br>
 Click below to activate.<br>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <form action="usermodcloning_activated.php" method="post">
 <input type="hidden" name="action" value="activate">
 <input type="hidden" name="userid" value="<?php echo $xuser['userid']; ?>">
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <input type="submit" name="submit" value="Activate">
 </form>
 </div>

<?php
   }
}
?>
