<div style="margin:10px;">

<?php
$wdOBJ = new WebsiteData();
$wd_id = getParameter("wd_id");
if ($wd_id != NULL) {
   $webdata = $wdOBJ->getWebData($wd_id);
   $groupname = getParameter("groupname");
   
   print "<div style=\"margin-bottom:20px;\">";
   print "<a style=\"font-size:10px;font-family:arial;\" href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=webdata&wd_id=".$webdata['wd_id']."\">Return to jData \"".$webdata['name']."\"</a>";
   print "</div>";
   
   if($groupname!=NULL && 0==strcmp(getParameter("subaction"),"visualdd")) {
      
   } else if($groupname!=NULL && (0==strcmp(getParameter("subaction"),"showcoordinates") || 0==strcmp(getParameter("subaction"),"save"))) {
      print "<form action=\"admincontroller.php\" method=\"POST\">";
      print "<input type=\"hidden\" name=\"action\" value=\"wd_fieldposition\">";
      print "<input type=\"hidden\" name=\"subaction\" value=\"save\">";
      print $wdOBJ->setFieldPositions(0!=strcmp(getParameter("subaction"),"save"));
      print "<input type=\"submit\" name=\"submit\" value=\"Save\">";
      print "</form>";
   } else {
      $groups = $wdOBJ->getFieldPositionGroups($wd_id);
      print "<table cellpadding=\"4\" cellspacing=\"5\" style=\"\">";
      print "<tr><td colspan=\"3\">Field Mapping</td></tr>";
      for($i=0;$i<count($groups);$i++) {
         print "<tr>";
         print "<td>";
         print "<input type=\"text\" style=\"font-size:10px;width:140px;\" id=\"grpname_".$i."\" value=\"".convertBack($groups[$i]['groupname'])."\">";
         print "<span onclick=\"location.href='/jsfadmin/admincontroller.php?action=wd_fieldposition&changename=1&from_groupname=".urlencode(convertBack($groups[$i]['groupname']))."&to_groupname=' + encodeURIComponent(jQuery('#grpname_".$i."').val()) + '&wd_id=".urlencode($wd_id)."';\" style=\"margin-left:4px;margin-right:8px;color:blue;font-size:10px;cursor:pointer;\">Update</span>";
         print "</td>";
         print "<td>";
         print "<a href=\"/jsfadmin/admincontroller.php?action=wd_fieldposition&subaction=showcoordinates&groupname=".urlencode(convertBack($groups[$i]['groupname']))."&wd_id=".urlencode($wd_id)."\">";
         print "View";
         print "</a>";
         print "</td>";
         print "<td>";
         print "<a href=\"/jsfadmin/admincontroller.php?action=wd_fieldposition&copyfieldpositions=1&groupname=".urlencode(convertBack($groups[$i]['groupname']))."&wd_id=".urlencode($wd_id)."\">";
         print "Copy";
         print "</a>";
         print "</td>";
         print "<td>";
         print "<a onclick=\"return confirm('Are you absolutely sure?  This will completely delete this mapping.')\" href=\"/jsfadmin/admincontroller.php?action=wd_fieldposition&deletefieldposition=1&groupname=".urlencode(convertBack($groups[$i]['groupname']))."&wd_id=".urlencode($wd_id)."\">";
         print "Delete";
         print "</a>";
         print "</td>";
         print "</tr>";
      }
      print "</table>";
      print "<div style=\"margin-top:5px;padding:4px;border:1px solid #EEEEEE;border-radus:3px;\">";
      print "<form action=\"admincontroller.php\" method=\"POST\">";
      print "<input type=\"hidden\" name=\"action\" value=\"wd_fieldposition\">";
      print "<input type=\"hidden\" name=\"subaction\" value=\"showcoordinates\">";
      print "<input type=\"hidden\" name=\"wd_id\" value=\"".$wd_id."\">";
      print "<input type=\"text\" name=\"groupname\" value=\"\">";
      print "<input type=\"submit\" name=\"submit\" value=\"Add a new mapping\">";
      print "</form>";
      print "</div>";
   }
} else {
   echo "error occurred.";
}

?>

</div>