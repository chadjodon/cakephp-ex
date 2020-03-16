<?php
print "\n<!-- ***chj*** test beginning -->\n";
   $ua = new UserAcct;
   $ss = new Version;

//error_reporting(E_ALL);
   if ($vars['segmentid']==NULL) $vars['segmentid']=getParameter("segmentid");

print "\n<!-- ***chj*** test a -->\n";
   $tempParams = $ua->searchUsersSQL();
print "\n<!-- ***chj*** test b -->\n";
   $getParams = $tempParams['getParams'];
   $masterURL = "admincontroller.php?action=listusers&segmentid=".$vars['segmentid'];
   for ($i=0; $i<count($getParams); $i++) $masterURL .= "&".$getParams[$i]['name']."=".$getParams[$i]['value'];
print "\n<!-- ***chj*** test 1 -->\n";
   $totalCount = $ua->getUsersForSegment(getParameter("segment"), $vars['segmentid'], NULL,NULL,NULL,TRUE);
print "\n<!-- ***chj*** test 2 -->\n";
   $orderby = getParameter("orderby");
   $page = getParameter("page");
   $limit = getParameter("limit");
   if ($page==NULL) $page = 1;
   if ($limit == NULL) $limit = 100;
   $numPages = ceil($totalCount/$limit);
   $pageurl = $masterURL."&orderby=".$orderby."&limit=".$limit;

print "\n<!-- ***chj*** test 3 -->\n";
   $userSearchObj = $ua->getUsersForSegment(getParameter("segment"), $vars['segmentid'], $orderby, $page, $limit);
print "\n<!-- ***chj*** test 4 -->\n";
   $values = $userSearchObj['users'];
   $hiddenFields = $userSearchObj['hiddenFields'];
   
   $shortcuts = $ss->getAllShortcuts(5);
   $options = array();
   for ($i=0; $i<count($shortcuts); $i++) {
      $options[$shortcuts[$i]['title']] = $shortcuts[$i]['filename'];
   }
   $shortcuts = $ss->getAllShortcuts(6);
   for ($i=0; $i<count($shortcuts); $i++) {
      $options[$shortcuts[$i]['title']] = $shortcuts[$i]['filename'];
   }
   //$extra = " onchange=\"showcmstxtonly('".$GLOBALS['baseURL'].$GLOBALS['codeFolder']."ajaxcontroller.php?action=cmstextonly&shortname='+this.value)\"";
   $extra = " onchange=\"showcmstxtonly('".$GLOBALS['baseURL'].$GLOBALS['codeFolder']."ajaxcontroller.php?action=cmstextonly&shortname='+this.value);showcmstitleonly('".$GLOBALS['baseURL'].$GLOBALS['codeFolder']."ajaxcontroller.php?action=cmstitleonly&shortname='+this.value)\"";
   $sel = getOptionList("emailcontent", $options, NULL, TRUE, $extra);

   $typeExtra = " id=\"fr_type\"";
   $msgTypeOpts = NULL;
   $msgTypeOpts['Email'] = "email";
   $msgTypeOpts['Internal User Message'] = "usmg";
   $msgTypeOpts['User Message + short email'] = "shortusmg";
   $msgTypeOpts['Both'] = "both";
   $typeSel = getOptionList("type", $msgTypeOpts, NULL, FALSE, $typeExtra);
?>

<script type="text/javascript">
      function expandSection(c,s) {
        if (document.getElementById(c).checked==true) {
            document.getElementById(s).style.display = "";
        } else {
            document.getElementById(s).style.display = "none";
        }
      }
      
      function submitPage(pg){
         document.getElementById('gotopagenum').value=pg;
         document.getElementById('gotopageform').submit();
         return false;
      }

      function expandnewuser() {
         document.getElementById('newusersect').style.display = "";
         document.getElementById('emaillistsect').style.display = "none";
         document.getElementById('searchusersect').style.display = "none";
         document.getElementById('emailusersect').style.display = "none";
         document.getElementById('emptysect').style.display = "none";

         document.getElementById('newuserelement').style.backgroundColor="#AAAAAA";
         document.getElementById('emaillistelement').style.backgroundColor="#DDDDDD";
         document.getElementById('searchuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emailuserelement').style.backgroundColor="#DDDDDD";
      }

      function expandemaillist() {
         document.getElementById('newusersect').style.display = "none";
         document.getElementById('emaillistsect').style.display = "";
         document.getElementById('searchusersect').style.display = "none";
         document.getElementById('emailusersect').style.display = "none";
         document.getElementById('emptysect').style.display = "none";

         document.getElementById('newuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emaillistelement').style.backgroundColor="#AAAAAA";
         document.getElementById('searchuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emailuserelement').style.backgroundColor="#DDDDDD";
      }

      function expandsearchuser() {
         document.getElementById('newusersect').style.display = "none";
         document.getElementById('emaillistsect').style.display = "none";
         document.getElementById('searchusersect').style.display = "";
         document.getElementById('emailusersect').style.display = "none";
         document.getElementById('emptysect').style.display = "none";

         document.getElementById('newuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emaillistelement').style.backgroundColor="#DDDDDD";
         document.getElementById('searchuserelement').style.backgroundColor="#AAAAAA";
         document.getElementById('emailuserelement').style.backgroundColor="#DDDDDD";
      }

      function expandemailuser() {
         document.getElementById('newusersect').style.display = "none";
         document.getElementById('emaillistsect').style.display = "none";
         document.getElementById('searchusersect').style.display = "none";
         document.getElementById('emailusersect').style.display = "";
         document.getElementById('emptysect').style.display = "none";

         document.getElementById('newuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emaillistelement').style.backgroundColor="#DDDDDD";
         document.getElementById('searchuserelement').style.backgroundColor="#DDDDDD";
         document.getElementById('emailuserelement').style.backgroundColor="#AAAAAA";

      }

        function SetAllCheckBoxes(FormName, FieldName, CheckValue) {
        	if(!document.forms[FormName]) return;
        	var objCheckBoxes = document.forms[FormName].elements[FieldName];
        	if(!objCheckBoxes) return;
        	var countCheckBoxes = objCheckBoxes.length;
        	if(!countCheckBoxes) objCheckBoxes.checked = CheckValue;
        	else
        		for(var i = 0; i < countCheckBoxes; i++)
        			objCheckBoxes[i].checked = CheckValue;
        }
 </script>


   <p>
   <?php
print "\n<!-- ***chj*** test 10 -->\n";
      if (class_exists('CustomUserDownload')) {
         $cud = new CustomUserDownload();
         $str = $cud->getUserDownloadHTML();
         $str = str_replace("%%%HIDDENFIELDS%%%",$hiddenFields,$str);
         print $str;
      } else {
   ?>
       <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
       <input type="hidden" name="action" value="dluserscsv">
       <input type="hidden" name="segmentid" value="<?php echo $vars['segmentid']; ?>">
       <input type="hidden" name="segment" value="<?php echo getParameter("segment"); ?>">
       <?php echo $hiddenFields; ?>
       <input type="submit" name="submit" value="User Results CSV">
       </form>
   <?php } ?>

   <form name="gotopageform" id="gotopageform" action="<?php echo $pageurl; ?>" method="post">
   <input id="gotopagenum" type="hidden" name="page" value="1">
   </form>

   <form name="userlistform" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="userlistaction">
   <input type="hidden" name="subjecttext" value="">
   <input type="hidden" name="from" value="">
   <input type="hidden" name="type" value="">
   <input type="hidden" name="wd_id" value="">
   <input type="hidden" name="emailtext" value="">
   <?php echo $hiddenFields; ?>   

   <table border="0" cellpadding="2" cellspacing="1" bgcolor="#999999">
   <TR bgcolor="#FFFFFF">
     <TD colspan="4" align="left">

<?php 
print "\n<!-- ***chj*** test 11 -->\n";
   if ($vars['segmentid'] != NULL) {
      $uSeg = $ua->getUserSegment($vars['segmentid']);
?>
   <input type="hidden" name="segmentid" value="<?php echo $vars['segmentid']; ?>">
   <table width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">
   User Segment: <font size="+2"><b><?php echo $uSeg['name']; ?></b></font><br><?php echo $uSeg['descr']; ?>
   </td><td align="right">
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usersegment&segmentid=<?php echo $vars['segmentid']; ?>">[edit this segment]</a><br>
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usersegment&createschemail=1&segmentid=<?php echo $vars['segmentid']; ?>">[send email]</a>
   </td></tr>
   </table>
<?php } else { ?>
       <div class="blackTextBigBold" align="left">System Users</div>
<?php } ?>
       <div class="tinytext">
         <?php echo $totalCount; ?> users returned (<?php echo $numPages; ?> pages)

         <?php if ($numPages>30) { ?>
            Go to page: 
            <input type="text" name="gotopagetext" id="gotopagetext" value="" style="font-size:12px;width:35px;">
            <input type="button" name="gotopagebtn" value="Go" style="font-size:12px;" onClick="submitPage(document.getElementById('gotopagetext').value);">
         <?php } ?>

      </div>
     </td>
     <TD colspan="6" align="right">
<?php
      $segmentListURL = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listusers&segmentid=";
      //$segments = $ua->getAllUserSegments();
      $segments = $ua->getAllDropdownSegments();
      $segmentOpts["All Users"]=$segmentListURL;
      for ($i=0; $i<count($segments); $i++) {
         $segmentOpts[$segments[$i]['name']]=$segmentListURL.$segments[$i]['segmentid'];
      }
      $segmentOpts["Create New Segment"]=$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&viewnewsegment=1";
      $extra = "onChange=\"window.location.href=this.form.segmentFilter.options[this.form.segmentFilter.selectedIndex].value;\"";
      print "View user segment:<BR>".getOptionList("segmentFilter", $segmentOpts, $segmentListURL.getParameter("segmentid"), false, $extra);
?>

     </td>
     
   </tr>

   <tr><td colspan="10" align="left">
<?php
                  $pageTable = "";
                  if ($page != null && $numPages != null && $numPages > 1) {
                     $url = $masterURL."&orderby=".$orderby;
                     $url .= "&limit=".$limit."&page=";
                     $groupstart = 0;
                     $groupend = $numPages;
                     if ($numPages>30) {
                        $pggroup = ceil($page/30);
                        $groupstart = ($pggroup-1)*30;
                        if ($groupend>$groupstart+30) $groupend = $groupstart+30;
                     }
                     $pageTable .= "\n<table align=\"left\"><tr><td>Page: </td>";
                     for ($i=($groupstart+1); $i<=$groupend; $i++) {
                        if ($page == $i) $pageTable .= "<td bgcolor=\"#AAAAAA\"><b>".$i."</b></td>";
                        else $pageTable .= "<td><a href=\"".$url.$i."\">".$i."</a></td>";
                     }
                     $pageTable .= "</tr></table>\n";
                     print $pageTable;
                  }
?>
   </td></tr>
   <tr bgcolor="#DDDDDD">
      <TH><a href="<?php echo $masterURL."&orderby=u.userid"; ?>">User</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.usertype"; ?>">Type</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.lname"; ?>">Name</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.company"; ?>">Company</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.email"; ?>">Email</a></TH>
      <th>Address</th><th>Phone</th><th>&nbsp;</th><TH>&nbsp;</TH>
      <th><input type="checkbox" onClick="SetAllCheckBoxes('userlistform', 'userid[]', document.userlistform.checkall.checked);" name="checkall" value="checkall"></th>
   </tr>

<?php

    for ($i=0; $i<count($values); $i++)
    {
      $lineU = $values[$i];
      $line = $ua->getUser($lineU['userid']);
      $rowClass = ($i % 2) +1;
      $usermodurl = "admincontroller.php?action=usermod&userid=".$line['userid'];
      $emailLink="<a href=\"".$usermodurl."\">".$line['email']."</a>";
      $useridLink="<a href=\"".$usermodurl."\">".$line['userid']."</a>";
      $addressLink = "";
      if ($line['addr1'] != null) $addressLink .= $line['addr1']."&nbsp;&nbsp;";
      if ($line['addr2'] != null) $addressLink .= $line['addr2']."&nbsp;&nbsp;";
      if ($line['city'] != null) $addressLink .= $line['city'].", ";
      if ($line['state'] != null && $line['state'] != "BL") $addressLink .= $line['state']."&nbsp;&nbsp;";
      if ($line['zip'] > 0) $addressLink .= $line['zip'];
      //$addressLink = $line['addr1']."&nbsp;&nbsp;".$line['addr2']."&nbsp;&nbsp;".$line['city'].", ".$line['state']."  &nbsp;&nbsp;&nbsp;".$line['zip'];
      $display_name = $line['fname']." ".$line['lname'];
      if ($line['fname']==NULL && $line['lname']==NULL) {
         $display_name = $line['title'];
         if ($line['title']==NULL) {
            $display_name = $line['username'];
            if ($line['username']==NULL) {
               $display_name = $line['company'];
            }
         }
      }
?>           


      <tr class='list_row<?= $rowClass ?>'>
        <td> <?php echo $useridLink; ?> &nbsp; </td>
        <td> <?php echo $line['usertype']; ?> &nbsp; </td>
        <td> <?php echo substr($display_name,0,32); ?> &nbsp; </td>
        <td> <?php echo substr($line['company'],0,32); ?> &nbsp; </td>
        <td> <?php echo $emailLink; ?> &nbsp; </td>
        <TD> <?php echo $addressLink; ?> &nbsp; </td>
        <TD> <?php echo $line['phonenum']; ?> &nbsp; </td>
        <TD> <?php echo $line['ulevel']; ?> &nbsp; </td>

        <td>
         <?php 
            $privacyOpts = array();
            $privacyOpts[-1]="Administrator";
            $privacyOpts[0]="No Approval";
            for ($j=1; $j<=10; $j++) $privacyOpts[$j]='Website level '.$j;
            $levels = $ua->getUsersAccessPointsFor($line['userid'],"WEBSITE");
            $selected = 0;
            if (count($levels)>0) $selected = $levels[count($levels)-1]['id'];
            if ($ua->isUserAdmin($line['userid'])) $selected=-1;
            print $privacyOpts[$selected]."\n";
         ?>
         </td>

         <td>
         <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
            <input type="checkbox" name="userid[]" value="<?php echo $line['userid']; ?>">
         <?php } ?>
         </td>
      </tr>


<?php
    }
?>
    <tr>
   <td colspan="10" align="right">
         <?php 
            if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
               $segmentOpts = array();
               $segmentOpts = $ua->getUserListSegments();
               print getOptionList("a_segmentid", $segmentOpts, NULL, FALSE, " class=\"selectbox\" ");
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Add Users To Segment\">\n";
               print " &nbsp; &nbsp; ";

               $privacyOpts = array();
               $privacyOpts['No Approval']=0;
               for ($j=1; $j<=10; $j++) $privacyOpts['Website level '.$j]=$j;
               $selected = 0;
               print getOptionList("privacy", $privacyOpts, $selected, FALSE, " class=\"selectbox\" ");
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Update User Access\">\n";

               if ($vars['segmentid']!=NULL && $ua->isUserListSegment($vars['segmentid'])) {
                  print " &nbsp; &nbsp; ";
                  print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Remove Users From Segment\">\n";
               }

               print " &nbsp; &nbsp; ";
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Delete Selected Users\" onclick=\"return confirm('Are you sure you want to delete these users and all related data?')\">\n";
            } 
         ?>

   </td>
   </tr>
   </table>
   </form>

   <br>

<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) { ?>

<script type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['codeFolder']; ?>getcms.js"></script>

  <table cellpadding="5" cellspacing="0">
  <tr>
  <td style="background-color: #FFFFFF;">
   <img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1">
  </td><td id="newuserelement" style="background-color: #DDDDDD;">
  <a href="#" onclick="expandnewuser(); return false" ><b>Add user</b></a>
  </td><td style="background-color: #FFFFFF;">
   <img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1">

  </td><td id="emaillistelement" style="background-color: #DDDDDD;">
  <a href="#" onclick="expandemaillist(); return false" ><b>Add email list</b></a>
  </td><td style="background-color: #FFFFFF;">
   <img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1">

  </td><td id="searchuserelement" style="background-color: #DDDDDD;">
  <a href="#" onclick="expandsearchuser();return false" ><b>Search</b></a>
  </td><td style="background-color: #FFFFFF;">
   <img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="5" height="1">
  </td><td id="emailuserelement" style="background-color: #DDDDDD;">
  <a href="#" onclick="expandemailuser();return false" ><b>Send email to selected users</b></a>
  </td><td style="background-color: #FFFFFF;">
   <img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="20" height="1">
  </td>
  </tr>
  <!--tr><td colspan="10" style="background-color: #AAAAAA;"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="10"></td></tr--> 
  </table>

  <table cellpadding="5" cellspacing="0" bgcolor="#AAAAAA">
  <tr><td><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="700" height="1"></td></tr> 
  <tr><td> 
  <table width="100%" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF"><tr><td> 


  <table id="emptysect" cellpadding="5" cellspacing="0">
  <tr><td><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="500" height="10"></td></tr>
  <tr><td>Click on a tab to add a user, filter you list of users, or to send an email to the above user(s)</td></tr>
  <tr><td><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="500" height="10"></td></tr>
  </table>

  <table id="newusersect" cellpadding="5" cellspacing="0" style="display: none;">
  <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr><td>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="adduser">
  <input type="hidden" name="refsrc" value="ADMINISTRATION:<?php echo isLoggedOn(); ?>">
  <table border="0" cellpadding="1" cellspacing="0">
  <TR><TD colspan="2"><h2>Add A New User</h2></td></tr>
  <tr>
     <td>First Name </td>
     <td><input type="text" name="fname" size="25" value=""></td>
  </tr>
  <tr>
    <td>Last Name </td>
    <td><input type="text" name="lname" size="25" value=""></td>
  </tr>
  <tr>
    <td>Company Name </td>
    <td><input type="text" name="company" size="25" value=""></td>
  </tr>
  <tr>
    <td>Gender </td>
    <td>
      <input type="radio" name="gender" value="M">Male 
      &nbsp; &nbsp 
      <input type="radio" name="gender" value="F">Female
   </td>
  </tr>
  <tr>
    <td>User Type </td>
    <td><?php echo getRadioBtnList("usertype", $ua->getUserTypes(), $user['usertype']); ?></td>
  </tr>
  <tr>
    <td>Account </td>
    <td>
      <input type="radio" name="alive" value="0" >Dormant 
      &nbsp; &nbsp 
      <input type="radio" name="alive" value="1" CHECKED>Active
   </td>
  </tr>
  <tr>
    <td>Website</td>
    <td><input type="text" name="website" size="25"></td>
  </tr>
  <tr>
    <td>Phone Number </td>
    <td><input type="text" name="phonenum" size="25"></td>
  </tr>
  <tr>
    <td>Phone Number 2</td>
    <td><input type="text" name="phonenum2" size="25"></td>
  </tr>
  <tr>
    <td>Phone Number 3</td>
    <td><input type="text" name="phonenum3" size="25"></td>
  </tr>
  <tr>
    <td>Phone Number 4</td>
    <td><input type="text" name="phonenum4" size="25"></td>
  </tr>
  <tr>
    <td>Address </td>
    <td><input type="text" name="addr1" size="50"></td>
  </tr>
  <tr>
    <td>Address (Cont.)</td>
    <td><input type="text" name="addr2" size="50"></td>
  </tr>
  <tr>
    <td>City </td>
    <td><input type="text" name="city" size="25"></td>
  </tr>
  <tr>
    <td>State </td>
    <td>
         <?= getStateOptions("BL","state") ?>
         &nbsp;&nbsp;Zip Code &nbsp;&nbsp;<input type="text" name="zip" size="10">
     </td>
  </tr>
  <TR><TD>Email Address </TD><TD><input type="text" name="email"></TD></TR>
  <TR><TD>Password </TD><TD> <input type="password" name="password"></TD></TR>
  <TR><TD>Confirm Password &nbsp;&nbsp;</TD><TD><input type="password" name="cpassword"></TD></TR>
  <TR><TD colspan="2" align="right"><input type="submit" name="submit" value="Add New User"></TD></TR>
 </table>
 </form>

 </td></tr>
 </table>


  <table id="emaillistsect" cellpadding="5" cellspacing="0" style="display: none;">
  <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr>
  <td>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="emaillist">
  <table border="0" cellpadding="1" cellspacing="0">
  <TR><TD><h2>Add a list of users</h2></td></tr>
  <tr><td>Emails (separate by commas<FONT COLOR="red">*</FONT>)</td></tr>
  <tr><td><textarea name="emaillist" rows="5" cols="35"></textarea></td></tr>
  <TR><TD align="right"><input type="submit" name="submit" value="Add List"></TD></TR>
 </table>
 </form>

 </td>
 <td bgcolor="#CCCCCC"> </td>
 <td>
   <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newuserfile" method="POST">
   <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
   <input type="hidden" name="action" value="uploaduserscsv">
   &nbsp;&nbsp;User CSV File Upload:<br>
   &nbsp;&nbsp;<input name="usercsv" type="file"><br>
   &nbsp;&nbsp;<input type="submit" name="Load Users" value="Load Users">
   </form>
 </td>
 </tr>
 </table>


  <table id="searchusersect" cellpadding="5" cellspacing="0" style="display: none;">
  <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr><td>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="listusers">
  <table border="0" cellpadding="1" cellspacing="0">
  <TR><TD colspan="2"><h2>Search For Users</h2></td></tr>
  <TR><TD>User id</td><TD><input type="text" name="s_filter" value="<?php echo getParameter("s_filter"); ?>"></td></tr>
  <TR><TD>First Name</td><TD><input type="text" name="s_fname" value="<?php echo getParameter("s_fname"); ?>"></td></tr>
  <TR><TD>Last Name</td><TD><input type="text" name="s_lname" value="<?php echo getParameter("s_lname"); ?>"></td></tr>
  <TR><TD>Company</td><TD><input type="text" name="s_company" value="<?php echo getParameter("s_company"); ?>"></td></tr>
  <TR><TD>Parent User ID</td><TD><input type="text" name="s_parentid" value="<?php echo getParameter("s_parentid"); ?>"></td></tr>
  <tr>
    <td>User Type </td>
    <td><?php echo getRadioBtnList("s_usertype", $ua->getUserTypes(), getParameter("s_usertype")); ?></td>
  </tr>
  <tr>
    <td>Activity </td>
    <td>
      <input type="radio" name="s_alive" value="" <?php if (getParameter("s_alive")==NULL) echo "CHECKED"; ?>>Active &nbsp; &nbsp 
      <input type="radio" name="s_alive" value="NO" <?php if (0==strcmp(strtolower(getParameter("s_alive")),"no")) echo "CHECKED"; ?>>Dormant &nbsp; &nbsp
      <input type="radio" name="s_alive" value="BOTH" <?php if (0==strcmp(strtolower(getParameter("s_alive")),"both")) echo "CHECKED"; ?>>All &nbsp; &nbsp
   </td>
  </tr>
  <TR>
      <TD>Gender</td>
      <TD>
         <input type="radio" name="s_gender" value="M" <?php if (0==strcmp(strtolower(getParameter("s_gender")),"m")) echo "CHECKED"; ?>>Male &nbsp;&nbsp;
         <input type="radio" name="s_gender" value="F" <?php if (0==strcmp(strtolower(getParameter("s_gender")),"f")) echo "CHECKED"; ?>>Female
      </td>
   </tr>
  <TR><TD>City</td><TD><input type="text" name="s_city" value="<?php echo getParameter("s_city"); ?>"></td></tr>
  <TR><TD>State</td><TD><input type="text" name="s_state" value="<?php echo getParameter("s_state"); ?>"></td></tr>
  <TR><TD>Postal Code</td><TD><input type="text" name="s_zip" value="<?php echo getParameter("s_zip"); ?>"></td></tr>
  <TR><TD>Phone Number</td><TD><input type="text" name="s_phonenumber" value="<?php echo getParameter("s_phonenumber"); ?>"></td></tr>
  <TR><TD>Email</td><TD><input type="text" name="s_email" value="<?php echo getParameter("s_email"); ?>"></td></tr>
  <TR><TD>Source</td><TD><input type="text" name="s_refsrc" value="<?php echo getParameter("s_refsrc"); ?>"></td></tr>
<?php
      $privacyOpts = array();
      $privacyOpts['Administrator']=-1;
      $privacyOpts['No Approval']=0;
      for ($j=1; $j<=10; $j++) $privacyOpts['Website level '.$j]=$j;
      $privacySearch = getOptionList("s_privacy", $privacyOpts, getParameter("s_privacy"), TRUE);
?>
     <tr><td>Access Level:</td><td><?php echo $privacySearch; ?></td></tr>
<?php
      $surveyObj = new Survey();
      $survey = $surveyObj->getSurveyByName("User Properties");
      if ($survey!=NULL && $survey['survey_id']>0) {
         $questions = $surveyObj->getAllQuestions($survey['survey_id']);
         for ($i=0; $i<count($questions); $i++) {
            print $surveyObj->getSearchHTML($questions[$i]);
         }
      } else {
         $wdObj = new WebsiteData();
         print $wdObj->getSearchHTMLAllFields("User Properties");
      }
?>
  <TR><TD colspan="2" align="right"><input type="submit" name="submit" value="Search"></TD></TR>
 </table>
 </form>

 </td></tr>
 </table>

  <table id="emailusersect" cellpadding="5" cellspacing="0" style="display: none;">
  <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr><td>

  <table border="0" cellpadding="1" cellspacing="0">
  <TR align="left" valign="top"><TD colspan="2"><h2>Email Users</h2></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Email Template: </td><td><?php echo $sel; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Email Subject: </td><td><input id="fr_subjtxt" type="text" name="fr_subjtxt" value="" size="80"></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>From Email: </td><td><input id="fr_from" type="text" name="fr_from" value="<?php echo $ss->getValue("WebsiteContact"); ?>" size="80"></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Message Type: </td><td><?php echo $typeSel; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Email content:</td><td><textarea rows="15" cols="80" id="fr_cmstext" name="fr_cmstext"></textarea></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <TR align="left" valign="top">
   <TD colspan="2" align="right">
      <input type="button" name="button" value="Send Email" onclick="document.userlistform.type.value=document.getElementById('fr_type').options[document.getElementById('fr_type').selectedIndex].value; document.userlistform.emailtext.value=document.getElementById('fr_cmstext').value; document.userlistform.subjecttext.value=document.getElementById('fr_subjtxt').value; document.userlistform.from.value=document.getElementById('fr_from').value; document.userlistform.submit(); return false;">
   </TD>
  </TR>
 </table>
 </td></tr>
 </table>

 </td></tr></table>
 </td></tr></table>

<br>







<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
<input type="submit" name="submit" value="Add selected users above to survey" onclick="document.userlistform.action.value='addusertowd';document.userlistform.wd_id.value=this.form.wd_id.options[this.form.wd_id.selectedIndex].value;document.userlistform.submit();return false;">
<select name="wd_id">
<?php
  $webdataM = new WebsiteData();
  $wdsM = $webdataM->getWebTables(isLoggedOn(),1);
  for ($iM=0; $iM<count($wdsM); $iM++) {
?>
  <option value="<?php echo $wdsM[$iM]['wd_id']; ?>"><?php echo $wdsM[$iM]['name']; ?></option>
<?php 
   }
   $wdsM = $webdataM->getWebTables(isLoggedOn(),2);
   for ($iM=0; $iM<count($wdsM); $iM++) {
?>
  <option value="<?php echo $wdsM[$iM]['wd_id']; ?>"><?php echo $wdsM[$iM]['name']; ?></option>
<?php 
   }
   $wdsM = $webdataM->getWebTables(isLoggedOn(),101);
   for ($iM=0; $iM<count($wdsM); $iM++) {
?>
  <option value="<?php echo $wdsM[$iM]['wd_id']; ?>"><?php echo $wdsM[$iM]['name']; ?></option>
<?php } ?>
</select>
</form>








<?php 
   } 

unset($_SESSION['params']);

?>

<br>
