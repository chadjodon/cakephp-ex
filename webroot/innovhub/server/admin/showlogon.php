
<div style="font-size:22px;font-weight:bold;font-family:verdana;color:#1f1f1f;"><?php echo $vars['defaultTitle']." Admin Login Page"; ?></h2>
<table cellpadding="10" cellspacing="0"><tr><td>

<?php
   if ($vars['forgottenPW'] ==1) {
?>
   <table cellpadding="3" cellspacing="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="forgottenpwForm" method="POST">
   <input type="hidden" name="action" value="resetpw">
   <tr><td><div style="font-size:14px;font-weight:normal;font-family:verdana;color:#333333;">Email/logon id: </div></td><td><input type="text" size="40" name="email" value="<?php echo $vars['email']; ?>"></td></tr>
   <tr><td colspan="2"><br></td></tr>
   <tr><td  align="left"><input type="submit" name="submit" value="Reset Password"></td><td align="right"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showlogon" style="font-size:10px;font-weight:normal;font-family:verdana;color:blue;">Cancel and return to login screen</a></td></tr>
   </form>
   </table>

<?php } else { ?>

   <table cellpadding="3" cellspacing="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="loginForm" method="POST">
   <input type="hidden" name="action" value="login">
   <input type="hidden" name="s_siteid" value="-1">
   <tr>
      <td><div style="font-size:14px;font-weight:normal;font-family:verdana;color:#333333;">Email/logon id: </div></td>
      <td><input type="text" size="40" name="email" value=""></td>
   </tr>
   <tr>
      <td><div style="font-size:14px;font-weight:normal;font-family:verdana;color:#333333;">Password: </div></td>
      <td><input type="password" size="40" name="password" value=""></td>
   </tr>
   <tr><td colspan="2" style="font-size:12px;font-weight:normal;color:#333333;"><input type="checkbox" name="setcookie" value="TRUE"> Log me in automatically</td></tr>
   <tr><td colspan="2"><br></td></tr>
   <tr><td  align="left"><input type="submit" name="Log In" value="Log In"></td><td align="right"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showlogon&forgottenPW=1" style="font-size:10px;font-weight:normal;font-family:verdana;color:blue;">Forgot my password</a></td></tr>
   </form>
   </table>

<?php
   }
?>

</td></tr></table>
