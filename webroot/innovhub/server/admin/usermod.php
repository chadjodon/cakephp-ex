<?php
   $ctx = new Context();
   $ss = new Version();

   $tab = getParameter("tab");
   if ($tab==NULL) $tab="usersect";
   print "\n<!-- tab: ".$tab." -->\n";

   if ($GLOBALS['usermod_override']!=NULL) {
       include $GLOBALS['usermod_override'];
   } else {   
       $ua = new UserAcct;
       $userid = $vars['userid'];
       if ($userid==NULL) $userid=getParameter("userid");
       $user = $ua->getFullUserInfo($userid);
?>

<script type="text/javascript">
      function expanduser() {
         document.getElementById('usersect').style.display = "";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#AAAAAA";
         document.getElementById('propertiestab').style.backgroundColor="#DDDDDD";
         document.getElementById('addltab').style.backgroundColor="#DDDDDD";
         document.getElementById('admintab').style.backgroundColor="#DDDDDD";
         document.getElementById('reltab').style.backgroundColor="#DDDDDD";
      }

      function expandproperties() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('propertiessect').style.display = "";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#DDDDDD";
         document.getElementById('propertiestab').style.backgroundColor="#AAAAAA";
         document.getElementById('addltab').style.backgroundColor="#DDDDDD";
         document.getElementById('admintab').style.backgroundColor="#DDDDDD";
         document.getElementById('reltab').style.backgroundColor="#DDDDDD";
      }

      function expandaddl() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('addlsect').style.display = "";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#DDDDDD";
         document.getElementById('propertiestab').style.backgroundColor="#DDDDDD";
         document.getElementById('addltab').style.backgroundColor="#AAAAAA";
         document.getElementById('admintab').style.backgroundColor="#DDDDDD";
         document.getElementById('reltab').style.backgroundColor="#DDDDDD";
      }

      function expandadmin() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "";
         document.getElementById('relsect').style.display = "none";
         document.getElementById('usertab').style.backgroundColor="#DDDDDD";
         document.getElementById('propertiestab').style.backgroundColor="#DDDDDD";
         document.getElementById('addltab').style.backgroundColor="#DDDDDD";
         document.getElementById('admintab').style.backgroundColor="#AAAAAA";
         document.getElementById('reltab').style.backgroundColor="#DDDDDD";
      }

      function expandrel() {
         document.getElementById('usersect').style.display = "none";
         document.getElementById('propertiessect').style.display = "none";
         document.getElementById('addlsect').style.display = "none";
         document.getElementById('adminsect').style.display = "none";
         document.getElementById('relsect').style.display = "";
         document.getElementById('usertab').style.backgroundColor="#DDDDDD";
         document.getElementById('propertiestab').style.backgroundColor="#DDDDDD";
         document.getElementById('addltab').style.backgroundColor="#DDDDDD";
         document.getElementById('admintab').style.backgroundColor="#DDDDDD";
         document.getElementById('reltab').style.backgroundColor="#AAAAAA";
      }

<?php
   $sumtab = getParameter("selectusermodtab");
   if ($sumtab!=NULL && (0==strcmp($sumtab,"user") || 0==strcmp($sumtab,"properties") || 0==strcmp($sumtab,"addl") || 0==strcmp($sumtab,"admin") || 0==strcmp($sumtab,"rel"))) print "window.onload = function() {\nexpand".$sumtab."();\n}\n";
?>
 </script>


  <table cellpadding="10" cellspacing="0">
  <tr align="left" valign="top"><td>

  <table cellpadding="5" cellspacing="0">
  <tr>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td>
      <td nowrap="nowrap" id="usertab" style="background-color: #AAAAAA;"><a href="#" onclick="expanduser(); return false" ><b>User info</b></a></td>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1"></td>
      <td nowrap="nowrap" id="propertiestab" style="background-color: #DDDDDD;"><a href="#" onclick="expandproperties(); return false" ><b><?php echo strtoupper(substr($user['usertype'],0,1)).strtolower(substr($user['usertype'],1)); ?> Properties</b></a></td>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1"></td>
      <td nowrap="nowrap" id="addltab" style="background-color: #DDDDDD;"><a href="#" onclick="expandaddl();return false" ><b>Other</b></a></td>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1"></td>
      <td nowrap="nowrap" id="admintab" style="background-color: #DDDDDD;"><a href="#" onclick="expandadmin();return false" ><b>User Admin</b></a></td>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1"></td>
      <td nowrap="nowrap" id="reltab" style="background-color: #DDDDDD;"><a href="#" onclick="expandrel();return false" ><b>User Relationships</b></a></td>
  <td style="background-color: #FFFFFF;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="20" height="1"></td>
  </tr>
  </table>
  <table cellpadding="0" cellspacing="0">
  <tr><td style="background-color: #AAAAAA;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="800" height="2"></td></tr> 
  </table>
  <br>


  <div id="usersect" <?php if (0!=strcmp($tab,"usersect")) print "style=\"display:none;\""; ?>>
  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="modifyuser">
  <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
  <input type="hidden" name="ulevel" value="<?= $user['ulevel'] ?>">
  <table cellpadding="5" cellspacing="1" align="center"><tr valign="top"><td>

  <table border="1">
  <tr><td colspan="2"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=useroverride&userid=".$user['userid']; ?>">View Site as this user (this will log you out)</a></td></tr>
  <tr><td bgcolor="lightgrey">User ID</td><td><?php echo $user['userid']; ?></td></tr>
  <tr><td bgcolor="lightgrey">User Created</td><td><?php echo $user['created']; ?></td></tr>
  <tr><td bgcolor="lightgrey">Email</td><td><input type="text" name="email" size="25" value="<?= $user['email'] ?>"></td></tr>
  
  <?php if($GLOBALS['usertypeview'] && 0==strcmp($user['usertype'],"org")) { ?> 
     <input type="hidden" name="fname" value="<?= $user['fname'] ?>">
     <input type="hidden" name="title" value="<?= $user['title'] ?>">
     <input type="hidden" name="gender" value="<?= $user['gender'] ?>">
     <tr><td bgcolor="lightgrey">Parent Company</td><td><input type="text" name="lname" size="25" value="<?= $user['lname'] ?>"></td></tr>
     <tr><td bgcolor="lightgrey">Former name</td><td><input type="text" name="username" size="25" value="<?= $user['username'] ?>"></td></tr>
  <?php } else { ?>
     <tr><td bgcolor="lightgrey">First Name</td><td><input type="text" name="fname" size="25" value="<?= $user['fname'] ?>"></td></tr>
     <tr><td bgcolor="lightgrey">Last Name</td><td><input type="text" name="lname" size="25" value="<?= $user['lname'] ?>"></td></tr>
     <tr><td bgcolor="lightgrey">UserName</td><td><input type="text" name="username" size="25" value="<?= $user['username'] ?>"></td></tr>
     <tr><td bgcolor="lightgrey">Title</td><td><input type="text" name="title" size="25" value="<?= $user['title'] ?>"></td></tr>
     <tr>
         <td bgcolor="lightgrey">Gender </td>
         <td><input type="radio" name="gender" value="M" <?php if (0==strcmp($user['gender'],"M")) echo "CHECKED"; ?>>Male 
         &nbsp; &nbsp <input type="radio" name="gender" value="F" <?php if (0==strcmp($user['gender'],"F")) echo "CHECKED"; ?>>Female</td>
     </tr>
  <?php } ?>
  <tr><td bgcolor="lightgrey">Company Name</td><td><input type="text" name="company" size="25" value="<?php echo $user['company']; ?>"></td></tr>
  <tr><td bgcolor="lightgrey">User Type </td><td><?php echo getRadioBtnList("usertype", $ua->getUserTypes(), $user['usertype']); ?></td></tr>
  <tr>
    <td bgcolor="lightgrey">Account </td>
    <td>
      <input type="radio" name="alive" value="0" <?php if (0==$user['alive']) echo "CHECKED"; ?>>Dormant 
      &nbsp; &nbsp 
      <input type="radio" name="alive" value="1" <?php if (1==$user['alive']) echo "CHECKED"; ?>>Active
   </td>
  </tr>
  <tr><td bgcolor="lightgrey">Website</td><td><input type="text" name="website" size="25" value="<?php echo $user['website']; ?>"></td></tr>
  <tr><td bgcolor="lightgrey">Phone Number</td><td><input type="text" name="phonenum" size="25" value="<?= $user['phonenum'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">Fax</td><td><input type="text" name="phonenum2" size="25" value="<?= $user['phonenum2'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">Alternate</td><td><input type="text" name="phonenum3" size="25" value="<?= $user['phonenum3'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">Phone Number 4</td><td><input type="text" name="phonenum4" size="25" value="<?= $user['phonenum4'] ?>"></td></tr>
  <TR><TD bgcolor="lightgrey">Source</TD><td><?php echo $user['refsrc']; ?></td></TR>
  <TR><TD bgcolor="lightgrey">Parent</TD><td><input type="text" name="parentid" size="25" value="<?php echo $user['parentid']; ?>"></td></TR>
  <TR><TD bgcolor="lightgrey">Parent 2</TD><td><input type="text" name="parentid2" size="25" value="<?php echo $user['parentid2']; ?>"></td></TR>
  </table>
</td><td>
  <table border="1">
  <tr><td bgcolor="lightgrey">Address</td><td><input type="text" name="addr1" size="40" value="<?= $user['addr1'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">Address (Cont.)</td><td><input type="text" name="addr2" size="40" value="<?= $user['addr2'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">City</td><td><input type="text" name="city" size="25" value="<?= $user['city'] ?>"></td></tr>
  <tr><td bgcolor="lightgrey">State</td><td>
         <?= getStateOptions($user['state'],"state",TRUE) ?>
         Zip Code<input type="text" name="zip" size="10" value="<?= $user['zip'] ?>">
     </td>
  </tr>
  <tr><td bgcolor="lightgrey">Country</td><td><?php echo listCountries($user['country'],"country",TRUE); ?></td></tr>
  <tr><td bgcolor="lightgrey">Location</td><td><?php echo $user['lat'].",".$user['lng']; ?></td></tr>
   <?php
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

   <?php
         $opts = $ctx->getSiteOptions(-1, 0, NULL, TRUE);
         $cityOptions = getOptionList("siteid", $opts,$user['siteid'],TRUE);
         $field1Lbl = $ss->getValue("user_field1_label");
         $field2Lbl = $ss->getValue("user_field2_label");
         $field3Lbl = $ss->getValue("user_field3_label");
         $field4Lbl = $ss->getValue("user_field4_label");
         $field5Lbl = $ss->getValue("user_field5_label");
         $field6Lbl = $ss->getValue("user_field6_label");
         $field6Ops = array();
         for ($i=1; $i<=10; $i++) $field6Ops[$i] = $i;
   ?>
  <TR><TD bgcolor="lightgrey">Site</TD><td><?php echo $cityOptions; ?></td></TR>
  <TR><TD bgcolor="lightgrey">Notes</TD><td><div style="max-width:400px;"><?php echo $user['notes']; ?></div></td></TR>

  <?php if ($field1Lbl!=NULL) { ?>
   <TR><TD bgcolor="lightgrey"><?php echo $field1Lbl; ?></TD><td><input type="text" name="field1" size="25" value="<?php echo $user['field1']; ?>"></td></TR>
  <?php } ?>
  <?php if ($field2Lbl!=NULL) { ?>
   <TR><TD bgcolor="lightgrey"><?php echo $field2Lbl; ?></TD><td><input type="text" name="field2" size="25" value="<?php echo $user['field2']; ?>"></td></TR>
  <?php } ?>
  <?php if ($field3Lbl!=NULL) { ?>
   <TR><TD bgcolor="lightgrey"><?php echo $field3Lbl; ?></TD><td><input type="text" name="field3" size="25" value="<?php echo $user['field3']; ?>"></td></TR>
  <?php } ?>
  <?php if ($field4Lbl!=NULL) { ?>
   <TR><TD bgcolor="lightgrey"><?php echo $field4Lbl; ?></TD><td><input type="text" name="field4" size="25" value="<?php echo $user['field4']; ?>"></td></TR>
  <?php } ?>
   <?php 
      if ($field5Lbl!=NULL) {
         $field5Input = "<input type=\"text\" name=\"field5\" size=\"15\" value=\"".$user['field5']."\">";
         if (strpos(strtolower($field5Lbl),"user")!==FALSE) {
            $allUsers = $ua->getAdminUsers();
            $userOpts = NULL;
            for ($i=0; $i<count($allUsers); $i++) {
               $userOpts[$allUsers[$i]['email']] = $allUsers[$i]['userid'];
            }
            $field5Input = getOptionList("field5", $userOpts, $user['field5'], TRUE);
         }
   ?>
      <TR><TD bgcolor="lightgrey"><?php echo $field5Lbl; ?></TD><td><?php echo $field5Input; ?></td></TR>
  <?php } ?>
  <?php if ($field6Lbl!=NULL) { ?>
   <TR><TD bgcolor="lightgrey"><?php echo $field6Lbl; ?></TD><td><?php echo getOptionList("field6", $field6Ops, $user['field6'], TRUE); ?></td></TR>
  <?php } ?>

 </table>
 </td></tr>
 <TR><TD colspan="2" align="center"><BR><input type="submit" name="submit" value="Modify User Info"></TD></TR>
 </table>
 </form>
 </div> <!-- end usersect -->




  <div id="propertiessect" <?php if (0!=strcmp($tab,"propertiessect")) print "style=\"display:none;\""; ?>>
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

   <?php
               $wdObj = new WebsiteData();
               $webdata_arr = $wdObj->getWebDataByFuzzyName($user['usertype']." objects%");
               if ($webdata_arr != NULL && count($webdata_arr)>0) {
                  for ($i=0; $i<count($webdata_arr); $i++) {
                     print "\n<br><a href=\"".getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?s_userid=".$user['userid']."&userid=".$user['userid']."&wd_id=".$webdata_arr[$i]['wd_id']."&simpledisplay=1&action=wd_listrows\">".$webdata_arr[$i]['name']."</a>";
                  }
               }

   ?>
   </div>




   <div id="adminsect" <?php if (0!=strcmp($tab,"adminsect")) print "style=\"display:none;\""; ?>>
        <!-- User password --> 
        <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
	     <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
	     <input type="hidden" name="action" value="modifypassword">
	     <input type="hidden" name="email" value="<?= $vars['email'] ?>">
	     <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
	     <input type="hidden" name="p_userid" value="<?= $user['userid'] ?>">
        <tr><td colspan="2" align="center"><b>Modify User's Password</b></td></tr>
        <tr><td>New Password:</td><td><input type="password" name="password" size=20></td></tr>
        <tr><td>Confirm Password:</td><td><input type="password" name="cpassword" size=20></td></tr>    
		  <tr><td colspan="2" align="center"><input type="submit" name="submit" value="Modify Password"></td></tr>
        </form>
	     </table>


   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <br>
      <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
      <tr><th>User Authority Settings</th></tr>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="changeuseraccess">
      <input type="hidden" name="email" value="<?= $user['email'] ?>">
      <input type="hidden" name="userid" value="<?= $user['userid'] ?>">
      <tr><td>
      <?php print getCheckboxList2Across("useraccess", $ua->getLevels(), $ua->getUserAccessLevels($user['userid'])); ?>
      </td></tr>
      <tr><td align="center"><input type="submit" name="submit" value="submit"></td></tr>
      </form>
      </table>

      <br>
      <?php $points = $ua->getUsersAccessPoints($user['userid']); ?>
      <table  bgcolor="lightgrey" border="1" cellspacing="2" cellpadding="2" align="center">
      <tr><th colspan="3">Additional User Authority Settings</th></tr>
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
   <?php } ?>
  </div>




   <div id="relsect" <?php if (0!=strcmp($tab,"relsect")) print "style=\"display:none;\""; ?>>
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <!-- user relationships -->
      <br>
      <?php
         $relTypeOpt = $ua->getUserRelations();
         $relTypeSel = getOptionList("rel_type", $relTypeOpt);
         $rels = $ua->getUsersRelated($user['userid'],"to");
      ?>
      <!-- script type="text/javascript" src="<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js"></script -->
      <script type="text/javascript">
         if (typeof showcmstxtonly == 'undefined') { 
            var e = document.createElement("script");
            e.src = "<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js";
            e.type = "text/javascript";
            document.getElementsByTagName("head")[0].appendChild(e);
         }
      </script>
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
         <input type="hidden" name="userid" value="<?php echo $user['userid']; ?>">
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
         <tr><td>Country </td><td><?php echo listCountries("","country",TRUE); ?></td></tr>
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
   <?php } ?>
      </div>





   <div id="addlsect" <?php if (0!=strcmp($tab,"addlsect")) print "style=\"display:none;\""; ?>>
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>
      <!-- custom area -->
      <?php
//error_reporting(E_ALL);
         if (class_exists("CustomUserDetails")) {
            $customObj = new CustomUserDetails();
            print $customObj->getUserDetailsHTML($user);
         }
      ?>

   <?php } ?>
   </div>


</td></tr>
</table>
<?php } ?>
