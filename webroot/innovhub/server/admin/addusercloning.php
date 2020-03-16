<?php
$ua = new UserAcct();
$p_user = array();
         $p_user['email'] = getParameter("email");
         $p_user['fname'] = getParameter("fname");
         $p_user['lname'] = getParameter("lname");
         $p_user['company'] = getParameter("company");
         $p_user['title'] = getParameter("title");
         $p_user['website'] = getParameter("website");
         $p_user['addr1'] = getParameter("addr1");
         $p_user['addr2'] = getParameter("addr2");
         $p_user['city'] = getParameter("city");
         $p_user['state'] = getParameter("state");
         $p_user['zip'] = getParameter("zip");
         $p_user['country'] = getParameter("country");
         $p_user['phonenum'] = getParameter("phonenum");
         $p_user['phonenum2'] = getParameter("phonenum2");
         $p_user['phonenum3'] = getParameter("phonenum3");
         
         $type_extra = "onChange=\"checkUserType();\"";
?>

<script>
function checkUserType(){
   if ($("input[type='radio'][name='usertype']:checked").val()=='org') {
      $('#genderrow').hide();
      $('#fnamerow').hide();
      $('#lnamerow').hide();
   } else {
   	 $('#genderrow').show();
   	 $('#fnamerow').show();
   	 $('#lnamerow').show();
   }
}
</script>
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="addusercloning">
  <input type="hidden" name="refsrc" value="ADMINISTRATION:<?php echo isLoggedOn(); ?>">
  <div style="padding:10px;border:1px solid #607070;border-radius:8px;font-size:12px;font-family:verdana;">
  <table border="0" cellpadding="3" cellspacing="0">
  <TR><TD colspan="2"><h2>Add A Record</h2></td></tr>
  <tr id="usertyperow">
    <td>User Type </td>
    <td><?php echo getRadioBtnList("usertype", $ua->getUserTypes(), $p_user['usertype'],$type_extra); ?></td>
  </tr>
  <tr id="fnamerow">
     <td>First Name </td>
     <td><input type="text" name="fname" size="25" value="<?php echo $p_user['fname']; ?>"></td>
  </tr>
  <tr id="lnamerow">
    <td>Last Name </td>
    <td><input type="text" name="lname" size="25" value="<?php echo $p_user['lname']; ?>"></td>
  </tr>
  <tr id="companyrow">
    <td>Company Name </td>
    <td><input type="text" name="company" size="25" value="<?php echo $p_user['company']; ?>"></td>
  </tr>
  <tr id="genderrow">
    <td>Gender </td>
    <td>
      <input type="radio" name="gender" value="M" <?php if(0==strcmp($p_user['gender'],"M")) echo "CHECKED"; ?>>Male 
      &nbsp; &nbsp 
      <input type="radio" name="gender" value="F" <?php if(0==strcmp($p_user['gender'],"F")) echo "CHECKED"; ?>>Female
   </td>
  </tr>
  <tr>
    <td>Website</td>
    <td><input type="text" name="website" size="25" value="<?php echo $p_user['website']; ?>"></td>
  </tr>
  <tr>
    <td>Phone Number </td>
    <td><input type="text" name="phonenum" size="25" value="<?php echo $p_user['phonenum']; ?>"></td>
  </tr>
  <tr>
    <td>Address </td>
    <td><input type="text" name="addr1" size="50" value="<?php echo $p_user['addr1']; ?>"></td>
  </tr>
  <tr>
    <td>Address (Cont.)</td>
    <td><input type="text" name="addr2" size="50" value="<?php echo $p_user['addr2']; ?>"></td>
  </tr>
  <tr>
    <td>City </td>
    <td><input type="text" name="city" size="25" value="<?php echo $p_user['city']; ?>"></td>
  </tr>
  <tr>
    <td>State </td>
    <td>
         <?php echo getStateOptions($p_user['state'],"state",TRUE) ?>
         &nbsp;&nbsp;Zip Code &nbsp;&nbsp;<input type="text" name="zip" size="10" value="<?php echo $p_user['zip']; ?>">
     </td>
  </tr>
  <tr>
    <td>Country </td>
    <td><?php echo listCountries($p_user['country'],"country",TRUE) ?></td>
  </tr>
  <TR><TD>Email Address </TD><TD><input type="text" name="email" value="<?php echo $p_user['email']; ?>"></TD></TR>
  <TR><TD>Password </TD><TD> <input type="password" name="password"></TD></TR>
  <TR><TD>Confirm Password &nbsp;&nbsp;</TD><TD><input type="password" name="cpassword"></TD></TR>
  <TR><TD colspan="2" align="right"><input type="submit" name="submit" value="Add"></TD></TR>
 </table>
 </div>
 </form>
