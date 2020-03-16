<?php
//print "\n<!-- in admincontroller.php, including: Classes,CustomCMS_Admin -->\n";
include_once "../jsfcode/Classes.php";
//error_reporting(E_ALL);
include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS_Admin.php";
$ua=new UserAcct();

if (!$ua->isUserAdmin(isLoggedOn())) {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;margin-bottom:10px;">
 Sorry, you are not authorized to perform this function.
 </div>

<?php
} else {
   $xuser = $ua->getUser(getParameter("userid"));
   $action = getParameter("action");
   if (0==strcmp($action,"approve")) {
      $ua->promoteAccount($xuser['userid']);
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;margin-bottom:10px;">
 This account was successfully approved.  You will need to refresh your user account screen to reflect this update.
 </div>

<?php
   } else if (0==strcmp($action,"reject")) {
      $ua->revertAccount($xuser['userid'],NULL,getParameter("reason"));
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;margin-bottom:10px;">
 This account was successfully rejected.  You will need to refresh your user account screen to reflect this update.
 </div>

<?php
   } else if (0==strcmp($action,"inactivate")) {
      $ua->revertAccount($xuser['userid'],NULL,getParameter("reason"));
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;margin-bottom:10px;">
 This account was successfully inactivated.  You will need to refresh your user account screen to reflect this update.
 </div>

<?php
   } else if (0==strcmp($xuser['dbmode'],"APPROVED") || 0==strcmp($xuser['dbmode'],"REJECTED") || 0==strcmp($xuser['dbmode'],"DELETED")) {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 Enter a reason below to inactivate this account.<BR>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <form action="usermodcloning_reject.php" method="post">
 <input type="hidden" name="action" value="inactivate">
 <input type="hidden" name="userid" value="<?php echo $xuser['userid']; ?>">
 <input type="text" name="reason" style="width:200px;font-size:14px;font-family:arial;" value="<?php echo $xuser['activatedstr']; ?>"><br>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <input type="submit" name="submit" value="Inactivate">
 </form>
 </div>

<?php
   } else {
?>

 <div style="padding:10px;font-size:16px;color:#333333;font-family:arial;">
 This account is currently <?php echo $xuser['dbmode']; ?>
 <?php if ($xuser['activatedstr']!=NULL) echo " (".$xuser['activatedstr'].")"; ?><br>
 Click below to approve/reject.<br>
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <form action="usermodcloning_reject.php" method="post">
 <input type="hidden" name="action" value="approve">
 <input type="hidden" name="userid" value="<?php echo $xuser['userid']; ?>">
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <input type="submit" name="submit" value="Approve">
 </form>
 <form action="usermodcloning_reject.php" method="post">
 <input type="hidden" name="action" value="reject">
 <input type="hidden" name="userid" value="<?php echo $xuser['userid']; ?>">
   <div style="width:10px;height:5px;overflow:hidden;"></div>
 <input type="submit" name="submit" value="Reject">
 </form>
 </div>

<?php
   }
}
?>
