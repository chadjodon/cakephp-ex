<?php
   $ua = new UserAcct;
   $userid = $vars['userid'];
   if ($userid==NULL) $userid=getParameter("userid");
   
   $user = $ua->getFullUserInfo($userid,FALSE,TRUE);
   $pubuser = $ua->getFullUserInfo($userid,TRUE,TRUE);

   print "<div style=\"max-width:800px;padding:20px;margin-top:5px;margin-bottom:5px;\">";
   print "<div style=\"margin-top:5px;margin-bottom:15px;font-size:16px;font-weight:bold;font-family:verdana;\">";
   print "Compare account updates (<a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$userid."\">".$userid."</a>)";
   print "</div>";
   print "<table cellpadding=\"5\" cellspacing=\"0\" style=\"font-size:12px;font-family:verdana;color:#333333;\">";
   print "<tr style=\"font-size:14px;font-weight:bold;\"><td></td><td>Approved Values</td><td></td><td>New Values</td></tr>";
   $anychanges = FALSE;
   $fieldcounter = 0;
   foreach($user as $key=>$val) {
      if (0==strcmp($key,"lastupdateby") || ($key!=NULL && 0!=strcmp($key,"siteid") && 0!=strcmp($key,"lastlogin") && 0!=strcmp($key,"orgid") && 0!=strcmp($key,"ulevel") && 0!=strcmp($key,"parentid2") && 0!=strcmp($key,"dbmode") && 0!=strcmp($key,"token") && 0!=strcmp($key,"password2") && 0!=strcmp($key,"password") && 0!=strcmp($key,"wd_id") && 0!=strcmp($key,"wd_row_id") && 0!=strcmp($key,"srvy_person_id") && 0!=strcmp($key,"userid") && 0!=strcmp($key,"addrid") && !(0==strcmp("q",substr($key,0,1)) && is_numeric(substr($key,1))))) {
      //if (0==strcmp($key,"lastupdateby") || ($key!=NULL && 0!=strcmp($key,"siteid") && 0!=strcmp($key,"lastlogin") && 0!=strcmp($key,"orgid") && 0!=strcmp($key,"ulevel") && 0!=strcmp($key,"parentid") && 0!=strcmp($key,"parentid2") && 0!=strcmp($key,"dbmode") && 0!=strcmp($key,"token") && 0!=strcmp($key,"password2") && 0!=strcmp($key,"password") && 0!=strcmp($key,"wd_id") && 0!=strcmp($key,"wd_row_id") && 0!=strcmp($key,"srvy_person_id") && 0!=strcmp($key,"userid") && 0!=strcmp($key,"addrid") && !(0==strcmp("q",substr($key,0,1)) && is_numeric(substr($key,1))))) {
         $style = "";
         $showmerge = FALSE;
         if (0!=strcmp($key,"lastupdateby") && ($user[$key]!=NULL || $pubuser[$key]!=NULL) && 0!=strcmp(strtolower(trim($user[$key])),strtolower(trim($pubuser[$key]))) && $pubuser!=NULL) {
            $style=" style=\"color:RED;font-weight:bold;\"";
            $showmerge = TRUE;
            $anychanges = TRUE;
         }
         $bgcolor = "#FFFFFF";
         if (($fieldcounter%2)==0) $bgcolor = "#DEDEDE";
         $fieldcounter++;
         print "<tr bgcolor=\"".$bgcolor."\">";
         print "<td>".$key."</td>";
         print "<td".$style.">".$pubuser[$key]."</td>";
         print "<td>";
         if ($showmerge) {
            print "<button ";
            print "onclick=\"location.href='".getBaseURL()."jsfadmin/admincontroller.php?action=usercomparecloning&subaction=approvesinglefield&userid=".$user['userid']."&field=".$key."&value=".$user[$key]."';\" ";
            print "style=\"font-size:8px;font-family:verdana;padding:3px;\" ";
            print ">&lt;Acc</button>";

            print "<button ";
            print "onclick=\"location.href='".getBaseURL()."jsfadmin/admincontroller.php?action=usercomparecloning&subaction=rejectsinglefield&userid=".$user['userid']."&field=".$key."&value=".$pubuser[$key]."';\" ";
            print "style=\"font-size:8px;font-family:verdana;padding:3px;\" ";
            print ">Rej&gt;</button>";
         }
         print "</td>";
         print "<td".$style.">".$user[$key]."</td>";
         print "</tr>";
      }
   }

   
   $rels = $ua->getUsersRelated($userid);
   $pubrels = $ua->getUsersRelated($userid,"to",NULL,NULL,"useracct_pub");
   print "<tr bgcolor=\"#CCEECC\">";
   print "<td>Related Users</td><td>";
   for ($i=0;$i<count($pubrels);$i++) {
   	 print "<div style=\"margin-top:10px;font-size:10px;font-family:arial;\">";
   	 print $pubrels[$i]['rel_type'].":".$pubrels[$i]['fname']." ".$pubrels[$i]['lname']." ".$pubrels[$i]['email']." ".$pubrels[$i]['phonenum']." ".$pubrels[$i]['addr1']." ".$pubrels[$i]['addr2']." ".$pubrels[$i]['city']." ".$pubrels[$i]['state'];
   }   
   print "</td><td></td><td>";
   for ($i=0;$i<count($rels);$i++) {
   	 print "<div style=\"margin-top:10px;font-size:10px;font-family:arial;\">";
   	 print $rels[$i]['rel_type'].":".$rels[$i]['fname']." ".$rels[$i]['lname']." ".$rels[$i]['email']." ".$rels[$i]['phonenum']." ".$rels[$i]['addr1']." ".$rels[$i]['addr2']." ".$rels[$i]['city']." ".$rels[$i]['state'];
   }
   print "</td></tr>";

   
   print "<tr>";
   print "<td colspan=\"4\" align=\"right\">";
   print "<button ";
   print "onclick=\"location.href='".getBaseURL()."jsfadmin/admincontroller.php?action=usercomparecloning&subaction=approve&userid=".$user['userid']."';\"";
   print ">Accept all changes</button>";
   print " &nbsp; ";
   print "<button ";
   print "onclick=\"location.href='".getBaseURL()."jsfadmin/admincontroller.php?action=usercomparecloning&subaction=reject&userid=".$user['userid']."';\"";
   print ">Reject changes and revert</button>";   
   print "</td>";
   print "</tr>";
   
   
   
   print "</table>";


   $wdObj = new WebsiteData();
   $webdata_arr = $wdObj->getWebDataByFuzzyName($user['usertype']." objects%");
   if ($webdata_arr != NULL && count($webdata_arr)>0) {
      for ($i=0; $i<count($webdata_arr); $i++) {
         //$webdata_arr[$i]['wd_id'],$webdata_arr[$i]['name']
         $wdObj->startCloning($webdata_arr[$i]['wd_id']);
         $qs = $wdObj->getFieldNames($webdata_arr[$i]['wd_id']);
         
         $results = $wdObj->getDataByUserid($webdata_arr[$i]['wd_id'], $userid, "wd_row_id", FALSE, TRUE);
         $results_pub = $wdObj->getDataByUserid($webdata_arr[$i]['wd_id'], $userid, "wd_row_id", TRUE, TRUE);
         
         //print "\n\n<!-- ***chj*** results:\n";
         //print_r($results);
         //print "\n-->\n\n";

         print "<div style=\"margin:6px;padding:4px;border:1px solid #000000;border-radius:3px;\">";
         print "<div style=\"padding:5px;font-size:16px;font-family:verdana;\">".substr($webdata_arr[$i]['name'],strlen($user['usertype']." objects "))."</div>";

         print "<table cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#FFFFFF\" style=\"font-family:verdana;\"><tr bgcolor=\"#AACCEE\">";
         foreach($qs as $key => $val) print "<td>".$val."</td>";

         print "</tr>";         
         
         print "<tr bgcolor=\"#DDDDDD\"><td colspan=\"".count($qs)."\">Last Approved Values</td></tr>";
         for ($j=0;$j<count($results_pub);$j++) {
            print "<tr>";
            foreach($qs as $key => $val) print "<td>".$results_pub[$j][$key]."</td>";
            print "</tr>";
         }

         print "<tr><td colspan=\"".count($qs)."\"><hr></td></tr>";
         
         print "<tr bgcolor=\"#DDDDDD\"><td colspan=\"".count($qs)."\">Updated Values</td></tr>";
         for ($j=0;$j<count($results);$j++) {
            if($results[$j][$key]==-1) $results[$j][$key]=""; 
            print "<tr>";
            foreach($qs as $key => $val) print "<td>".$results[$j][$key]."</td>";
            print "</tr>";
         }

         print "</table></div>";
      }
   }

   print "</div>";
?>
