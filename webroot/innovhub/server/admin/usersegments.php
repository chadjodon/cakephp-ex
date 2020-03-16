<?php

    function displaySegmentHierarchy($seggroupid=-1,$depth=0, $rowId=2){
       $ua = new UserAcct();
       $displayFlag = "";
       if ($seggroupid != -1) $displayFlag=" style=\"display: none;\"";

       $indent = "";
       for ($j=0; $j<$depth; $j++) $indent .= ".........";

       $segments = $ua->getUserSegmentsFor($seggroupid);
   		 print "<tr class=\"list_row1\" id=\"segment".$seggroupid."\"".$displayFlag."><td>\n";
          print "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
          for ($i=0; $i<count($segments); $i++) {
            print "<tr align=\"left\" valign=\"top\" class='list_row1'>\n";
            print "<td>".$indent."</td><td><a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&segmentid=".$segments[$i]['segmentid']."\">".$segments[$i]['name']."</a></td>\n";
            print "<td> &nbsp; ".$segments[$i]['descr']."</td>\n";
            print "<td align=\"center\"> &nbsp; &nbsp; [<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&viewaddusers=1&segmentid=".$segments[$i]['segmentid']."\">Add users</a>]</td>\n";
            print "<td align=\"center\"> &nbsp; &nbsp; [<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=dluserscsv&segmentid=".$segments[$i]['segmentid']."\">download csv</a>]</td>\n";
            print "<td align=\"center\"> &nbsp; &nbsp; [<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listuserscloning&segmentid=".$segments[$i]['segmentid']."\">list segment users</a>]</td>\n";
            print "<td align=\"center\"> &nbsp; &nbsp; [<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&createschemail=1&segmentid=".$segments[$i]['segmentid']."\">send scheduled email</a>]</td>\n";
            print "<td align=\"center\"> &nbsp; &nbsp; [<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&deletesegment=1&delsegmentid=".$segments[$i]['segmentid']."\" onClick=\"return confirm('Are you sure you want to delete this segment permanently?');\">delete segment</a>]</td>\n";
            print "</tr>\n";
          }
          print "</table>\n";
          print "</td></tr>\n";

       $results = $ua->getSegmentGroupsFor($seggroupid);
       for ($i=0; $i<count($results); $i++) {
          $name = $results[$i]['name'];
          $image="<a href=\"#\" onClick=\"expanditem".$results[$i]['seggroupid']."();\">+</a><img src=\"folder.gif\">\n";

          $removeLink = "".getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&removeseggroup=1&seggroupid=".$results[$i]['seggroupid'];
          $removeLink = " <a href=\"".$removeLink."\">[Remove]</a>";
      		 
          $editLink = "<a href=\"".getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&viewgroup=1&seggroupid=".$results[$i]['seggroupid'];
          $editLink .= "\">[Edit]</a>\n ";
   		 
   		 print "<tr class=\"list_row1\" id=\"sgroup".$results[$i]['seggroupid']."\"".$displayFlag."><td>";
          print $indent.$image."<B>".$name."</B> &nbsp; ".$editLink." &nbsp; ".$removeLink."</td></tr>\n";
	       
          $rowId = displaySegmentHierarchy($results[$i]['seggroupid'],$depth+1,$rowId);
       }
       return $rowId;
    }

    function displayExpandJavascript(){
       print "<script language=\"javascript\">\n";
       $result = recurExpandJSFunctions();
       print $result['jsExpand'];

       print "function expandAll() {\n";
       print $result['jsExpandAll'];
       print "}\n\n";
       
       print "function collapseAll() {\n";
       print $result['jsCollapseAll'];
       print "}\n\n";

       print "</script>\n";
    }

    function recurExpandJSFunctions($seggroupid=-1){
       $jsExpandAll   = "";
       $jsCollapseAll = "";
       $jsExpand      = "";
       
       $result['jsExpandAll']   =  $jsExpandAll  ;
       $result['jsCollapseAll'] =  $jsCollapseAll;
       $result['jsExpand']      =  $jsExpand     ;
       $ua = new UserAcct();
       $segments = $ua->getUserSegmentsFor($seggroupid);
       $results = $ua->getSegmentGroupsFor($seggroupid);
       if (count($results)<1 && count($segments)<1) return $result;

       $jsExpandAll   .= "      document.getElementById('segment".$seggroupid."').style.display = \"\";\n";
       $jsCollapseAll .= "      document.getElementById('segment".$seggroupid."').style.display = \"none\";\n";
       $thisjsExpand .= "  if (document.getElementById('segment".$seggroupid."').style.display == \"none\") {\n";
       $thisjsExpand .= "      document.getElementById('segment".$seggroupid."').style.display = \"\";\n";
       $thisjsExpand .= "  } else {\n";
       $thisjsExpand .= "      document.getElementById('segment".$seggroupid."').style.display = \"none\";\n";
       $thisjsExpand .= "  }\n\n";

       for ($i=0; $i<count($results); $i++) {
          if ($seggroupid != -1) {
             $jsExpandAll   .= "      document.getElementById('sgroup".$results[$i]['seggroupid']."').style.display = \"\";\n";
             $jsCollapseAll .= "      document.getElementById('sgroup".$results[$i]['seggroupid']."').style.display = \"none\";\n";
          }

          if (!$ua->isSegGroupEmpty($results[$i]['seggroupid'])) {
             $result = recurExpandJSFunctions($results[$i]['seggroupid']);
             $jsExpand      .= $result['jsExpand']     ;
             $jsExpandAll   .= $result['jsExpandAll']  ;
             $jsCollapseAll .= $result['jsCollapseAll'];
          }
       }

       if ($seggroupid != -1) {
          if (!$ua->isSegGroupEmpty($seggroupid)) {
             $jsExpand .= "function expanditem".$seggroupid."() {\n";
             $jsExpand .= $thisjsExpand;
             $jsExpand .= recurDisableRows($results);
             $jsExpand.="}\n\n";
          }
       }

       $result['jsExpandAll']   =  $jsExpandAll  ;
       $result['jsCollapseAll'] =  $jsCollapseAll;
       $result['jsExpand']      =  $jsExpand     ;
       return $result;
    }

    function recurDisableRows($groups,$parent=null) {
       $ua = new UserAcct();
       $jsExpand = "";
       if (count($groups)<1) return "";

       for ($i=0; $i<count($groups); $i++) {
          $parentId = $groups[$i]['seggroupid'];
          if ($parent == null) {
             $jsExpand .= "  if (document.getElementById('sgroup".$parentId."').style.display == \"none\") {\n";
             $jsExpand .= "      document.getElementById('sgroup".$parentId."').style.display = \"\";\n";
             $jsExpand .= "      document.getElementById('segment".$parentId."').style.display = \"\";\n";
             $jsExpand .= "  } else {\n";
             $jsExpand .= "      document.getElementById('sgroup".$parentId."').style.display = \"none\";\n";
             $jsExpand .= "      document.getElementById('segment".$parentId."').style.display = \"none\";\n";
             $jsExpand .= "  }\n\n";
             $results = $ua->getSegmentGroupsFor($parentId);
             $jsExpand .= recurDisableRows($results,$parentId);
          }
          else {
             $jsExpand .= "  if (document.getElementById('sgroup".$parent."').style.display == \"none\") {\n";
             $jsExpand .= "      document.getElementById('sgroup".$parentId."').style.display = \"none\";\n";
             $jsExpand .= "      document.getElementById('segment".$parentId."').style.display = \"none\";\n";
             $jsExpand .= "  } else {\n";
             $jsExpand .= "      document.getElementById('sgroup".$parentId."').style.display = \"\";\n";
             $jsExpand .= "      document.getElementById('segment".$parentId."').style.display = \"\";\n";
             $jsExpand .= "  }\n\n";
             $results = $ua->getSegmentGroupsFor($parentId);
             $jsExpand .= recurDisableRows($results,$parent);
          }
       }
       return $jsExpand;
    }

   $ua = new UserAcct();
   $ss = new Version();
   displayExpandJavascript();
   
?>

   <font size="+2"><b>User Segments</b></font>
   <br>
   [<a href="#" onClick="expandAll();">Expand All</a>]
   &nbsp; &nbsp; 
   [<a href="#" onClick="collapseAll();">Collapse All</a>]
   &nbsp; &nbsp;
   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usersegment&viewnewsegment=1">Create a new segment</a>] 
   &nbsp; &nbsp;
   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usersegment&viewgroup=1">Create a new folder</a>]

   <br><br>
   <table cellpadding="2" cellspacing="0">
   <?php displaySegmentHierarchy(); ?>
   </table>

     <br><br><div style="height:1px;width:400px;background-color:#333333;overflow:hidden;margin:0;padding:0;"></div>


<?php
   $segmentid = getParameter("segmentid");
   $uSeg = $ua->getUserSegment($segmentid);
   
   if (getParameter("viewaddusers")==1 && $segmentid != NULL) {
?>
     <form name="addemailssegment" id="addemailssegment" action="admincontroller.php" method="post">
     <input type="hidden" name="action" value="usersegment">
     <input type="hidden" name="addusers" value="1">
     <input type="hidden" name="segmentid" value="<?php echo $segmentid; ?>">
     Email Segment: <?php echo $uSeg['name']; ?><br><?php echo $uSeg['descr']; ?><br>Separate emails by commas<br>
     <textarea name="emaillist" rows="5" cols="30"></textarea><br>
     <input type="submit" name="submit" value="Add Emails to segment">
     </form>

<?php
   } else if (getParameter("createschemail")==1 && $segmentid != NULL) {
      $shortcuts = $ss->getAllShortcuts(5);
      $options = array();
      for ($i=0; $i<count($shortcuts); $i++) {
         $options[$shortcuts[$i]['title']." (".$shortcuts[$i]['filename'].")"] = $shortcuts[$i]['filename'];
      }
      $shortcuts = $ss->getAllShortcuts(6);
      for ($i=0; $i<count($shortcuts); $i++) {
         $options[$shortcuts[$i]['title']." (".$shortcuts[$i]['filename'].")"] = $shortcuts[$i]['filename'];
      }
      $extra = " onchange=\"getEmailTemplate(this.value);\"";
      $sel = getOptionList("shortname", $options, NULL, TRUE,$extra);
      
      for ($k=1; $k<=10; $k++) $poptions[$k]=$k;
      $prioritysel = getOptionList("priority",$poptions,"10",FALSE);

      $msgTypeOpts['Email'] = "email";
      $msgTypeOpts['Internal User Message'] = "usmg";
      $msgTypeOpts['User Message + short email'] = "shortusmg";
      $msgTypeOpts['Both'] = "both";
      $typeSel = getOptionList("type", $msgTypeOpts, NULL, FALSE, $typeExtra);

?>
  <script type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['codeFolder']; ?>getcms.js"></script>
  <script type="text/javascript">
      function getEmailTemplate(cmsname) {
         showcmstxtonly('<?php echo getBaseURL().$GLOBALS['codeFolder']."ajaxcontroller.php?action=cmstextonly&convertstring=1&shortname="; ?>'+cmsname,2,'".getBaseURL()."jsfimages/loading.gif');
      }
  </script> 
  <table id="schemailusersect" cellpadding="5" cellspacing="0">
  <form name="schsegmentemail" id="schsegmentemail" action="admincontroller.php" method="post">
  <input type="hidden" name="action" value="usersegment">
  <input type="hidden" name="scheduleemails" value="1">
  <input type="hidden" name="createschemail" value="1">
  <input type="hidden" name="segmentid" value="<?php echo $segmentid; ?>">
  <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr><td>

  <table border="0" cellpadding="1" cellspacing="0">
  <TR align="left" valign="top"><TD colspan="2"><h2>Scheduled Email Segment: <?php echo $uSeg['name']; ?></h2><?php echo $uSeg['descr']; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Email Template: </td><td><?php echo $sel; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>From emails (separated by commas): </td><td><input id="fromemails" type="text" name="fromemails" value="" size="80"></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Priority: </td><td><?php echo $prioritysel; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Message Type: </td><td><?php echo $typeSel; ?></td></tr>
  <tr align="left" valign="top"><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="5"></td></tr>
  <tr align="left" valign="top"><td>Message: </td><td><div id="fr_cmstext"></div></td></tr>
  <TR align="left" valign="top">
   <TD colspan="2" align="right">
      <input type="button" name="button" value="Schedule Email" onclick="if (confirm('Are you sure you want to schedule this email to everyone in this user segment?')) document.schsegmentemail.submit(); return false;">
   </TD>
  </TR>
 </table>
 </td></tr>
 </form>
 </table>




<?php } else if (($segmentid != NULL && $uSeg != NULL) || getParameter("viewnewsegment")==1) { ?>

  <table id="searchusersect" cellpadding="5" cellspacing="0">
  <tr><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>
  <tr valign="top"><td>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="usersegment">
  <table border="0" cellpadding="1" cellspacing="0">
<?php
   $parentid = -1;
   $dropdownInd = "";
   if ($segmentid != NULL && $uSeg != NULL) {
      $parentid = $uSeg['seggroupid'];
      if ($uSeg['dropdown']==1) $dropdownInd="CHECKED";
?>
  <tr><td colspan="2"> 
   Viewing segment: <font size="+2"><b><?php echo $uSeg['name']; ?></b></font><br>
   <?php echo $uSeg['descr']; ?>
   </td></tr>
  <TR><TD colspan="2"><input type="checkbox" name="s_userlist" value="<?php echo $segmentid; ?>" <?php if ($ua->isUserListSegment($segmentid)) echo "CHECKED"; ?>>  Add users to this segment directly.</td></tr>

<?php } else { ?>
  <TR><TD colspan="2"><font size="+2"><b>New Customer Segment</b></font></td></tr>
  <TR><TD>New User Segment Name<font color="red">*</font></td><td><input type="text" name="name" value=""></TD></TR>
  <TR valign="top"><TD>New User Segment Description</td><td><textarea name="descr" rows="5" cols="40"></textarea></TD></TR>
  <TR><TD colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
  <TR><TD colspan="2"><input type="checkbox" name="uselist" value="1"> Add users to this segment directly.</td></tr>
<?php } ?>
  <TR><TD>Parent Folder </td><td><?php echo getOptionList("seggroupid", $ua->getSegGroupList(), $parentid); ?></td></tr>
  <TR><TD colspan="2"><input type="checkbox" name="dropdown" value="1" <?php echo $dropdownInd; ?>> Add this segment to segment dropdown lists.</td></tr>

<?php
      $uopts = $ua->getUserTypes();
      $list = "";
      foreach($uopts as $key => $value) $list .= $value.", ";
      print $ua->getSearchHTML($list,FALSE,getParameter("s_usertype"));
      if (class_exists("CustomUserSegment")) {
         $customObj = new CustomUserSegment();
         print $customObj->getSearchParamsHTML();
      }
?>

<?php if ($segmentid != NULL && $uSeg != NULL) { ?>
  <input type="hidden" name="segmentid" value="<?php echo $segmentid; ?>">
  <input type="hidden" name="updatesegment" value="1">
  <TR><TD colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
  <TR><TD>Update User Segment Name<font color="red">*</font></td><td><input type="text" name="name" value="<?php echo $uSeg['name']; ?>"></TD></TR>
  <TR><TD>Update User Segment Description</td><td><textarea name="descr" rows="5" cols="40"><?php echo $uSeg['descr']; ?></textarea></TD></TR>
  <TR><TD colspan="2" align="right"><input type="submit" name="submit" value="Update Segment"></TD></TR>
<?php } else { ?>
  <input type="hidden" name="newsegment" value="1">
  <TR><TD colspan="2" align="right"><input type="submit" name="submit" value="Create New Segment"></TD></TR>
<?php } ?>
 

</table>
 </form>

 </td><td>

<?php
   $segments = $ua->getAllUserSegments(); 
   if (count($segments)<2) print "<BR><font color=\"red\">Currently no other segments to include...</font><br>";
   if ($segmentid != NULL && $uSeg != NULL) { 
      print "<h2>Include other segments:</h2>";
      //$segments = $ua->getAllUserSegments();
      $opts = array();
      $currCondition = "AND";
      for ($i=0; $i<count($segments); $i++) {
         $opts[$segments[$i]['name']]=$segments[$i]['segmentid'];
      }
      unset($opts[$uSeg['name']]);

      $showRemoveTable=FALSE;
      $removeTable="";
      $removeTable .= "<table cellpadding=\"5\" cellspacing=\"1\" bgcolor=\"#333333\">\n";
      $removeTable .= "<tr bgcolor=\"#FFFFFF\"><td colspan=\"2\" align=\"center\"><b>References to other segments</b></td></tr>\n";
      for ($i=0; $i<count($uSeg['getParams']); $i++) {
         if (0==strcmp($uSeg['getParams'][$i]["name"],"SEGMENTID")) {
            $uname = $ua->getUserSegmentName($uSeg['getParams'][$i]["value"]);
            if (isset($opts[$uname])) unset($opts[$uname]);
            $removeTable .= "<tr bgcolor=\"#FFFFFF\"><td>".$uname."</td><td><a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&segmentid=".$segmentid."&removesegmentid=1&inclsegmentid=".$uSeg['getParams'][$i]["value"]."\">Remove</a></td></tr>\n" ;
            $showRemoveTable = TRUE;
         } else if (0==strcmp($uSeg['getParams'][$i]["name"],"SEGMENTCONDITION")) {
            $currCondition = $uSeg['getParams'][$i]["value"];
         }
      }
      $removeTable .= "</table>\n";

      $condOpts = array();
      $condOpts['Must fall into all of the referenced segments'] = "AND";
      $condOpts['Must fall into one of the referenced segments'] = "OR";
      $condOpts['Must not fall into any of the referenced segments'] = "NOT";
      
      if (count($opts)>0) {
?>
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="usersegment">
   <input type="hidden" name="segmentid" value="<?php echo $segmentid; ?>">
   <input type="hidden" name="addsegmentid" value="1">
   <?php echo getOptionList("inclsegmentid",$opts,NULL,TRUE); ?>
   <input type="submit" name="submit" value="Add Segment Reference">
   </form>
<?php 
      }
 
      if (count($condOpts)>0) {
?>
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="usersegment">
   <input type="hidden" name="segmentid" value="<?php echo $segmentid; ?>">
   <input type="hidden" name="addsegmentcondition" value="1">
   <?php echo getOptionList("inclsegmentcondition",$condOpts,$currCondition); ?>
   <input type="submit" name="submit" value="Set Segment Reference Rule">
   </form>
<?php } ?>

   <?php if ($showRemoveTable) echo $removeTable; ?>

   <br><br>

   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=dluserscsv&segmentid=".$segmentid; ?>">Download segment users CSV</a>]<br>
   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listuserscloning&segmentid=".$segmentid; ?>">List segment users</a>]<br>
   [<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=usersegment&createschemail=1&segmentid=".$segmentid; ?>">Send scheduled email</a>]<br>

<?php } ?>

 </td></tr>
 </table>


<?php 
   } else if (getParameter("viewgroup")==1) {
      $seggroupid = getParameter("seggroupid"); 
      $name = getParameter("name"); 
      $parentid = getParameter("parentid"); 
?>

  <table cellpadding="5" cellspacing="0">
  <tr><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="1"></td></tr>

  <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
  <input type="hidden" name="action" value="usersegment">
<?php 
   if ($seggroupid != NULL) { 
      $segGroup = $ua->getSegmentGroup($seggroupid);
      $name = $segGroup['name'];
      $parentid = $segGroup['parentid'];   
?>
  <input type="hidden" name="updateseggroup" value="1">
  <input type="hidden" name="seggroupid" value="<?php echo $seggroupid; ?>">
  <TR><TD colspan="2"><font size="+2"><b>Update Segment Folder</b></font></td></tr>
<?php } else { ?>
  <input type="hidden" name="newseggroup" value="1">
  <TR><TD colspan="2"><font size="+2"><b>New Segment Folder</b></font></td></tr>
<?php } ?>

  <TR><TD>Folder Name</td><TD><input type="text" name="name" value="<?php echo $name; ?>"></td></tr>
  <TR><TD>Parent Folder </td><td><?php echo getOptionList("parentid", $ua->getSegGroupList(), $parentid); ?></td></tr>
  <TR><TD colspan="2"><input type="submit" name="submit" value="Save"></td></tr>
 </form>
 </table>

<?php } ?>


<?php
  unset($_SESSION['params']);
?>
