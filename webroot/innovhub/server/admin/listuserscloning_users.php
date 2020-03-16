<script type="text/javascript">      
   function submitPage(pg){
      document.getElementById('gotopagenum').value=pg;
      document.getElementById('gotopageform').submit();
      return false;
   }
   
   function SetAllCheckBoxes(FormName, FieldName, CheckValue) {
      if(!document.forms[FormName]) return;

      var objCheckBoxes = document.forms[FormName].elements[FieldName];

      if(!objCheckBoxes) return;

      var countCheckBoxes = objCheckBoxes.length;

      if(!countCheckBoxes) objCheckBoxes.checked = CheckValue;
      else for(var i = 0; i < countCheckBoxes; i++) objCheckBoxes[i].checked = CheckValue;
   }
</script>

   <form name="gotopageform" id="gotopageform" action="<?php echo $pageurl; ?>" method="post">
   <input id="gotopagenum" type="hidden" name="page" value="1">
   </form>

   <form name="userlistform" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <!-- input type="hidden" name="action" value="userlistaction" -->
   <input type="hidden" name="action" value="listuserscloning">
   <input type="hidden" name="subjecttext" value="">
   <input type="hidden" name="from" value="">
   <input type="hidden" name="type" value="">
   <input type="hidden" name="wd_id" value="">
   <input type="hidden" name="emailtext" value="">
   <?php echo $hiddenFields; ?>

   <table border="0" cellpadding="2" cellspacing="1" bgcolor="#999999"> <!--*** results table ***-->
   <TR bgcolor="#FFFFFF"> <!--*** results table ***-->
     <TD colspan="4" align="left">

<?php 
   if ($vars['segmentid'] != NULL) {
      $uSeg = $ua->getUserSegment($vars['segmentid']);
?>
   <input type="hidden" name="segmentid" value="<?php echo $vars['segmentid']; ?>">
   <table width="100%" cellpadding="0" cellspacing="0"><tr><td align="left" style="font-size:16px;font-weight:bold;font-family:verdana;color:#222222;">
   <?php echo $uSeg['name']; ?><div style="font-size:12px;font-weight:normal;font-family:verdana;"><?php echo $uSeg['descr']; ?></div>
   </td><td align="right">
      <!--a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usersegment&segmentid=<?php echo $vars['segmentid']; ?>">[edit this segment]</a><br -->
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
     <TD colspan="3" align="right">
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
      print "View saved search:<BR>".getOptionList("segmentFilter", $segmentOpts, $segmentListURL.getParameter("segmentid"), false, $extra);
?>

     </td>
     
   </tr>

   <tr> <!--*** results table ***-->
      <td colspan="7" align="left" bgcolor="#AAAAAA">
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
                        if ($page == $i) $pageTable .= "<td bgcolor=\"#FFFFFF\"><b>".$i."</b></td>";
                        else $pageTable .= "<td><a href=\"".$url.$i."\">".$i."</a></td>";
                     }
                     $pageTable .= "</tr></table>\n";
                     print $pageTable;
                  }
?>
   </td></tr>
   <tr bgcolor="#DFDFDF"> <!--*** results table ***-->
      <TH><a href="<?php echo $masterURL."&orderby=u.userid"; ?>">User</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.usertype"; ?>">Type</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=u.dbmode"; ?>">Status</a></TH>
      <TH><a href="<?php echo $masterURL."&orderby=".urlencode("u.lname, u.company, u.email"); ?>">Name</a></TH>
      <th>Address</th><th>Phone</th>
      <th><input type="checkbox" onClick="SetAllCheckBoxes('userlistform', 'userid[]', document.userlistform.checkall.checked);" name="checkall" value="checkall"></th>
   </tr>

<?php

    for ($i=0; $i<count($values); $i++) {
      $lineU = $values[$i];
      $line = $ua->getUser($lineU['userid']);
      $rowClass = ($i % 2) +1;
      $usermodurl = "admincontroller.php?action=usermodcloning&userid=".$line['userid'];
      $useridLink="<a href=\"".$usermodurl."\">".$line['userid']."</a>";
      $addressLink = "";
      if ($line['addr1'] != null) $addressLink .= $line['addr1']."&nbsp;&nbsp;";
      if ($line['addr2'] != null) $addressLink .= $line['addr2']."&nbsp;&nbsp;";
      if ($line['city'] != null) $addressLink .= $line['city'].", ";
      if ($line['state'] != null && $line['state'] != "BL") $addressLink .= $line['state']."&nbsp;&nbsp;";
      if ($line['zip'] > 0) $addressLink .= $line['zip'];
      //$addressLink = $line['addr1']."&nbsp;&nbsp;".$line['addr2']."&nbsp;&nbsp;".$line['city'].", ".$line['state']."  &nbsp;&nbsp;&nbsp;".$line['zip'];
      $display_name = NULL;
      if ($line['fname']!=NULL || $line['lname']!=NULL) $display_name = substr(trim($line['lname'].", ".$line['fname']),0,32);
      if (0==strcmp($line['usertype'],"org")) {
         $display_name = substr(trim($line['company']),0,32);
      } else if (0==strcmp($line['usertype'],"user") && $display_name==NULL) {
         $display_name = substr(trim($line['title']),0,32);
      }

      if ($display_name==NULL && strpos($line['email'],"dummy")===FALSE) {
         $display_name = substr($line['email'],0,32);
      } else if ($display_name==NULL && trim($line['company'])!=NULL) {
         $display_name = substr($line['company'],0,32);
      } else if ($display_name==NULL && trim($line['lname'])!=NULL) {
         $display_name = substr($line['lname'],0,32);
      } else if ($display_name==NULL && trim($line['title'])!=NULL) {
         $display_name = substr($line['title'],0,32);
      } else if ($display_name==NULL) {
         $display_name = "&lt;unknown&gt;";
      }

      $display_name="<a href=\"".$usermodurl."\">".$display_name."</a>";
      if (0==strcmp($line['dbmode'],"UPDATED")) {
         $display_name .= "<br><a style=\"font-size:10px;font-family:arial;font-style:italic;\" href=\"";
         $display_name .= $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usercomparecloning&userid=".$line['userid'];
         $display_name .= "\" target=\"_new\">compare</a>";
      }
?>           


      <tr class='list_row<?= $rowClass ?>'> <!--*** results table ***-->
        <td> <?php echo $useridLink; ?> &nbsp; </td>
        <td> <?php echo $line['usertype']; ?> &nbsp; </td>
        <td> <?php echo $line['dbmode']; ?> &nbsp; </td>
        <td> <?php echo $display_name; ?> &nbsp; </td>
        <TD> <?php echo $addressLink; ?> &nbsp; </td>
        <TD> <?php echo $line['phonenum']; ?> &nbsp; </td>
        <!--TD> <?php echo $line['ulevel']; ?> &nbsp; </td-->

        <!--td>
         <?php 
            //$privacyOpts = array();
            //$privacyOpts[-1]="Administrator";
            //$privacyOpts[0]="No Access";
            //for ($j=1; $j<=10; $j++) $privacyOpts[$j]='Website level '.$j;
            //$levels = $ua->getUsersAccessPointsFor($line['userid'],"WEBSITE");
            //$selected = 0;
            //if (count($levels)>0) $selected = $levels[count($levels)-1]['id'];
            //if ($ua->isUserAdmin($line['userid'])) $selected=-1;
            //print $privacyOpts[$selected]."\n";
         ?>
         </td-->

         <td>
         <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
            <input type="checkbox" name="userid[]" value="<?php echo $line['userid']; ?>">
         <?php } ?>
         </td>
      </tr>


<?php
    }
?>
   <tr> <!--*** results table ***-->
      <td colspan="7" align="right">
         <?php 
            if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
               $segmentOpts = array();
               $segmentOpts = $ua->getUserListSegments();
               print getOptionList("a_segmentid", $segmentOpts, NULL, FALSE, " class=\"selectbox\" ");
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Add Users To List\">\n";
               print " &nbsp; &nbsp; ";

               //$privacyOpts = array();
               //$privacyOpts['No Approval']=0;
               //for ($j=1; $j<=10; $j++) $privacyOpts['Website level '.$j]=$j;
               //$selected = 0;
               //print getOptionList("privacy", $privacyOpts, $selected, FALSE, " class=\"selectbox\" ");
               //print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Update User Access\">\n";

               if ($vars['segmentid']!=NULL && $ua->isUserListSegment($vars['segmentid'])) {
                  print " &nbsp; &nbsp; ";
                  print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Remove Users From List\">\n";
               }

               print " &nbsp; &nbsp; ";
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Approve\" onclick=\"return confirm('Are you sure you want to approve these users?');\">\n";
               print " &nbsp; &nbsp; ";
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Reject\" onclick=\"return confirm('Are you sure you want to reject these users?');\">\n";
               print " &nbsp; &nbsp; ";
               print "<input class=\"input\" type=\"submit\" name=\"subaction\" value=\"Delete\" onclick=\"return confirm('Are you sure you want to delete these users and all related data?');\">\n";
            } 
         ?>

   </td>
   </tr>
   </table> <!--*** results table ***-->
   </form>

