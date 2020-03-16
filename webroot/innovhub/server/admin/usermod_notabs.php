<?php
    $ua = new UserAcct;
    $ctx = new Context();
    $userid = $vars['userid'];
    if ($userid==NULL) $userid=getParameter("userid");
    $user = $ua->getFullUserInfo($userid);
?>

  <table width="100%" cellpadding="15" cellspacing="0">
  <tr align="left" valign="top"><td>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="modifyuser">
  <input type="hidden" name="userid" value="<?= $vars['userid'] ?>">
  <input type="hidden" name="ulevel" value="<?= $user['ulevel'] ?>">
  <table bgcolor="lightgrey" border="1" align="center">
  <tr><td colspan="2"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=useroverride&userid=".$user['userid']; ?>">View Site as this user (this will log you out)</a></td></tr>
  <tr>
     <td>User ID</td>
     <td><?php echo $user['userid']; ?></td>
  </tr>
  <tr>
     <td>User Created</td>
     <td><?php echo $user['created']; ?></td>
  </tr>
  <tr>
     <td>Email</td>
     <td><input type="text" name="email" size="25" value="<?= $user['email'] ?>"></td>
  </tr>
  <tr>
     <td>First Name</td>
     <td><input type="text" name="fname" size="25" value="<?= $user['fname'] ?>"></td>
  </tr>
  <tr>
    <td>Last Name</td>
    <td><input type="text" name="lname" size="25" value="<?= $user['lname'] ?>"></td>
  </tr>
  <tr>
    <td>Company Name</td>
    <td><input type="text" name="company" size="25" value="<?php echo $user['company']; ?>"></td>
  </tr>
  <tr>
    <td>Gender </td>
    <td>
      <input type="radio" name="gender" value="M" <?php if (0==strcmp($user['gender'],"M")) echo "CHECKED"; ?>>Male 
      &nbsp; &nbsp 
      <input type="radio" name="gender" value="F" <?php if (0==strcmp($user['gender'],"F")) echo "CHECKED"; ?>>Female
   </td>
  </tr>
  <tr>
    <td>User Type </td>
    <td><?php echo getRadioBtnList("usertype", $ua->getUserTypes(), $user['usertype']); ?></td>
  </tr>
  <tr>
    <td>Account </td>
    <td>
      <input type="radio" name="alive" value="0" <?php if (0==$user['alive']) echo "CHECKED"; ?>>Dormant 
      &nbsp; &nbsp 
      <input type="radio" name="alive" value="1" <?php if (1==$user['alive']) echo "CHECKED"; ?>>Active
   </td>
  </tr>
  <tr>
    <td>Website</td>
    <td><input type="text" name="website" size="25" value="<?php echo $user['website']; ?>"></td>
  </tr>
  <tr>
    <td>Phone Number</td>
    <td><input type="text" name="phonenum" size="25" value="<?= $user['phonenum'] ?>"></td>
  </tr>
  <tr>
    <td>Phone Number 2</td>
    <td><input type="text" name="phonenum2" size="25" value="<?= $user['phonenum2'] ?>"></td>
  </tr>
  <tr>
    <td>Phone Number 3</td>
    <td><input type="text" name="phonenum3" size="25" value="<?= $user['phonenum3'] ?>"></td>
  </tr>
  <tr>
    <td>Phone Number 4</td>
    <td><input type="text" name="phonenum4" size="25" value="<?= $user['phonenum4'] ?>"></td>
  </tr>
  <tr>
    <td>Address</td>
    <td><input type="text" name="addr1" size="40" value="<?= $user['addr1'] ?>"></td>
  </tr>
  <tr>
    <td>Address (Cont.)</td>
    <td><input type="text" name="addr2" size="40" value="<?= $user['addr2'] ?>"></td>
  </tr>
  <tr>
    <td>City</td>
    <td><input type="text" name="city" size="25" value="<?= $user['city'] ?>"></td>
  </tr>
  <tr>
    <td>State</td>
    <td>
         <?= getStateOptions($user['state'],"state",TRUE) ?>
         Zip Code<input type="text" name="zip" size="10" value="<?= $user['zip'] ?>">
     </td>
  </tr>
   <?php
      $ss = new Version();
      if ($ss->getValue("RequireActivation")==1 && $ua->userProfileExists($user['email'])) {
   ?>
  <tr>
      <td colspan="2">
   <?php if ($user['activated']==1) { ?>
         This user has activated their account.  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=deactivateuser&userid=".$user['userid']; ?>">Deactivate</a>
   <?php } else { ?>
         This user has not yet activated their account.  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=activateuser&userid=".$user['userid']; ?>">Activate</a>
   <?php } ?>
      </td>
  </tr>
   <?php } ?>
  <TR><TD>Source</TD><td><?php echo $user['refsrc']; ?></td></TR>

   <?php
         $opts = $ctx->getSiteOptions(-1, 0, NULL, TRUE);
         $cityOptions = getOptionList("siteid", $opts,$user['siteid'],TRUE);
   ?>
  <TR><TD>Site</TD><td><?php echo $cityOptions; ?></td></TR>


  <TR><TD>Parent</TD><td><input type="text" name="parentid" size="25" value="<?php echo $user['parentid']; ?>"></td></TR>
  <TR><TD>Parent 2</TD><td><input type="text" name="parentid2" size="25" value="<?php echo $user['parentid2']; ?>"></td></TR>
  <TR><TD colspan="2" align="center"><BR><input type="submit" name="submit" value="Modify User Info"></TD></TR>
 </table>
 </form>

   <?php
         $surveyObj = new Survey();
         $survey = $surveyObj->getSurveyByName($user['usertype']." Properties");
         if ($survey != NULL) {
   ?>
         <table cellpadding="2" cellspacing="2" bgcolor="#CCCCCC" border="1" align="center">
         <tr><td><b>Properties</b></td></tr>
         <tr><td>
         <?php
                  $results = $surveyObj->getDataBySurveyAndUserid($survey['survey_id'], $user['userid']);
                  $sci = $results[0];
                  $surveyObj->printSurvey(NULL, $sci['srvy_person_id'],$survey['survey_id'],NULL,"Short","usermod.php",$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php",TRUE,NULL,NULL,FALSE,$user['userid']);
         ?>
         </td></tr></table>
   <?php } else { ?>
         <?php
               $wdObj = new WebsiteData();
               $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
               if ($webdata != NULL) {
         ?>
         <table cellpadding="2" cellspacing="2" bgcolor="#CCCCCC" border="1" align="center">
         <tr><td><b>Properties</b></td></tr>
         <tr><td>
         <?php
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $user['userid']);
                  $sci = $results[0];
                  $wdObj->printWebData($webdata['wd_id'], NULL, $user['userid'], $sci['wd_row_id'], NULL, "Short", "usermod.php", $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php", TRUE,NULL,NULL,FALSE);
         ?>
         </td></tr></table>
         <?php } ?>
   <?php } ?>

</td><td>

        <!-- User password --> 
        <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
	<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
	<input type="hidden" name="action" value="modifypassword">
	<input type="hidden" name="email" value="<?= $vars['email'] ?>">
	<input type="hidden" name="userid" value="<?= $vars['userid'] ?>">
	<input type="hidden" name="p_userid" value="<?= $vars['userid'] ?>">
           <tr>
             <td colspan="2" align="center"><b>Modify User's Password</b></td>
           </tr>
           <tr>
             <td>New Password:</td>
             <td><input type="password" name="password" size=20></td>
           </tr>
           <tr>
             <td>Confirm Password:</td>
             <td><input type="password" name="cpassword" size=20></td>
           </tr>    
		   <tr>
             <td colspan="2" align="center"><input type="submit" name="submit" value="Modify Password"></td>
           </tr>
        </form>
	</table>


   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <br>
      <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
      <tr><th>User Authority Settings
      </th></tr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="changeuseraccess">
      <input type="hidden" name="email" value="<?= $user['email'] ?>">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <tr><td>
      <?php print getCheckboxList2Across("useraccess", $ua->getLevels(), $ua->getUserAccessLevels($user['userid'])); ?>
      </td></tr>
      <tr><td align="center">
      <input type="submit" name="submit" value="submit">
      </td></tr>
      </form>
      
      </td></tr>
      </table>

      <br>
      <?php
         $points = $ua->getUsersAccessPoints($user['userid']);
      ?>
      <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
      <tr><th colspan="3">Additional User Authority Settings
      </th></tr>
      <tr><td>System Name</td><td>System ID</td><td>&nbsp;</td></tr>
      
      <?php
          for ($i=0; $i<count($points); $i++) {
            $line = $points[$i];   
      ?>      
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
         <input type="hidden" name="action" value="useraccesspoints">
         <input type="hidden" name="email" value="<?= $user['email'] ?>">
         <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
         <input type="hidden" name="remove" value="1">
         <input type="hidden" name="sys" value="<?php echo $line['sys']; ?>">
         <input type="hidden" name="id" value="<?php echo $line['id']; ?>">
         <tr>
         <td><?php echo $line['sys']; ?></td>
         <td><?php echo $line['id']; ?></td>
         <td><input type="submit" name="submit" value="Remove"></td>
         </tr>
         </form>
      <?php } ?>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="useraccesspoints">
      <input type="hidden" name="email" value="<?= $user['email'] ?>">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <input type="hidden" name="add" value="1">
      <tr>
      <td><input type="text" name="sys" size="15" value="<?php echo ""; ?>"></td>
      <td><input type="text" name="id" size="10" value="<?php echo ""; ?>"></td>
      <td><input type="submit" name="submit" value="Add"></td>
      </tr>
      </form>
      </table>


      <!-- user relationships -->
      <br>
      <?php
         $relTypeOpt = $ua->getUserRelations();
         $relTypeSel = getOptionList("rel_type", $relTypeOpt);
         $rels = $ua->getUsersRelated($user['userid'],"to");
      ?>
      <script type="text/javascript" src="<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js"></script>
      <table bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
      <tr><td colspan="2" align="center"><b>Related Users</b></td></tr>
      <tr>
      <td><input type="text" id="usersearchajax" name="usearsearchajax" value="" size="30"></td>
      <td><input type="button" name="usersearchbtn" value="Search" onClick="var urlaj='<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/ajaxcontroller.php?action=listusers&userid1=<?php echo $user['userid']; ?>&search=' + document.getElementById('usersearchajax').value;showcmstxtonly(urlaj,3);"></td>
      </tr>
      <tr><td colspan="2"><div id="ajaxrechtml"></div></td></tr>
      <form id="userrel_aj" name="userrel_aj" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="userrelation">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <input type="hidden" name="reluserid" value="">
      <input type="hidden" name="add" value="1">
      <tr><td><?php echo $relTypeSel; ?></td><td><input type="submit" name="submit" value="Add"></td></tr>
      </form>

      <?php
          for ($i=0; $i<count($rels); $i++) {
            $line = $rels[$i];
            $user2 = $ua->getUser($rels[$i]['reluserid']);
      ?> 
         <tr><td colspan="2">     
            <table border="0" cellspacing="2" cellpadding="4" align="center">
            <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
            <input type="hidden" name="action" value="userrelation">
            <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
            <input type="hidden" name="userrel_id" value="<?= $line['userrel_id'] ?>">
            <input type="hidden" name="remove" value="1">
            <tr>
            <td colspan="2">
            <a href="<?php echo $GLOBALS['baseURLSSL']."jsfadmin/admincontroller.php?action=usermod&userid=".$user2['userid']; ?>">
            <?php echo $line['reluserid'].". ".$user2['fname']." ".$user2['lname']; ?></a> &nbsp;
            <?php echo "(".$user2['company'].") is a ".$ua->getRelTypeString($line['rel_type']); ?> &nbsp;
            <input type="submit" name="submit" value="Remove"></td>
            </tr>
            </form>
            <tr>
               <td><?php echo $user2['addr1']."<br>".$user2['addr2']."<BR>".$user2['city'].", ".$user2['state']."  ".$user2['zip']; ?></td>
               <td><?php echo "Email: ".$user2['email']."<BR>Phone: ".$user2['phonenum']."<BR>Fax: ".$user2['phonenum2']; ?></td>
            </tr>
            </table>
         </td></tr>
      <?php } ?>
      <tr><td colspan="2"><br>
         <table cellpadding="0" cellspacing="2">
         <tr><td colspan="2"><b>Create a new contact/relationship</b></td></tr>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
         <input type="hidden" name="action" value="adduserandrelation">
         <input type="hidden" name="userid" value="<?php echo $userid; ?>">
         <input type="hidden" name="refsrc" value="ADMINISTRATION:<?php echo isLoggedOn(); ?>">
         <tr><td></td><td><?php echo $relTypeSel; ?></td></tr>
         <tr><td>First Name </td><td><input type="text" name="fname" size="25" value=""></td></tr>
         <tr><td>Last Name </td><td><input type="text" name="lname" size="25" value=""></td></tr>
         <tr><td>Company Name</td><td><input type="text" name="company" size="25" value=""></td></tr>
         <tr><td>Gender </td><td><input type="radio" name="gender" value="M">Male &nbsp; &nbsp <input type="radio" name="gender" value="F">Female</td></tr>
         <tr><td>Website</td><td><input type="text" name="website" size="25"></td></tr>
         <tr><td>Phone Number </td><td><input type="text" name="phonenum" size="25"></td></tr>
         <tr><td>Fax</td><td><input type="text" name="phonenum2" size="25"></td></tr>
         <tr><td>Address </td><td><input type="text" name="addr1" size="25"></td></tr>
         <tr><td>Address2</td><td><input type="text" name="addr2" size="25"></td></tr>
         <tr><td>City </td><td><input type="text" name="city" size="25"></td></tr>
         <tr><td>State </td><td>
               <?= getStateOptions("BL","state") ?>&nbsp;&nbsp;Zip Code &nbsp;&nbsp;<input type="text" name="zip" size="5">
         </td></tr>
         <TR><TD>Email Address </TD><TD><input type="text" name="email"></TD></TR>
         <tr><td></td><td align="right"><input type="submit" name="submit" value="Add"></td></tr>
         </form>
         </table>
      </td></tr>

      <?php
          $reverserels = $ua->getUsersRelated($user['userid'],"from");
          if (count($reverserels)>0) {
             print "<TR><td colspan=\"2\"><b>Accounts that have this as a reference</b></td></tr>";            
             for ($i=0; $i<count($reverserels); $i++) {
               $line = $reverserels[$i];
               $user2 = $ua->getUser($line['userid']);
      ?> 
               <tr><td colspan="2">     
                  <table border="0" cellspacing="2" cellpadding="4" align="center">
                  <tr>
                  <td colspan="2">
                  [<b><?php echo $ua->getRelTypeString($line['rel_type']); ?></b>] for 
                  <a href="<?php echo $GLOBALS['baseURLSSL']."jsfadmin/admincontroller.php?action=usermod&userid=".$user2['userid']; ?>">
                  <?php echo $line['userid'].". ".$user2['fname']." ".$user2['lname']; ?>&nbsp;
                  <?php echo $user2['company']."</a>"; ?> &nbsp;
                  </td>
                  </tr>
                  <tr>
                     <td><?php echo $user2['addr1']."<br>".$user2['addr2']."<BR>".$user2['city'].", ".$user2['state']."  ".$user2['zip']; ?></td>
                     <td><?php echo "Email: ".$user2['email']."<BR>Phone: ".$user2['phonenum']."<BR>Fax: ".$user2['phonenum2']; ?></td>
                  </tr>
                  </table>
               </td></tr>
      <?php 
            }
         }
      ?>
      </table>

      <!-- custom area -->
      <?php
         if (class_exists("CustomUserDetails")) {
            $customObj = new CustomUserDetails();
            print $customObj->getUserDetailsHTML($user);
         }
      ?>

   <?php } ?>
</td></tr>
</table>
