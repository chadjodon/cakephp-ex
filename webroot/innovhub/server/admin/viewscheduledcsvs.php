<?php
   $scheduler = new Scheduler();
   $ua = new UserAcct();
   
   $semailid=trim(getParameter("semailid"));
   $viewdetails=trim(getParameter("viewdetails"));
   
   $searchtxt=trim(getParameter("searchtxt"));
   
   $status=trim(getParameter("status"));
   if($status==NULL) $status="NEW";
   
   $priority=trim(getParameter("priority"));
   $orderby=trim(getParameter("orderby"));
   if ($orderby==NULL) $orderby = "e.timeadded DESC";
   $page = trim(getParameter("page"));
   if ($page==NULL || $page==0 || !is_numeric($page)) $page=1;
   $limit=trim(getParameter("limit"));
   if ($limit==NULL) $limit=50;
   $type = trim(getParameter("type"));
   if ($type==NULL) $type = "CUSTOM";
   $classname = trim(getParameter("classname"));
   $pgaction = getParameter("pgaction");
   
   if(0==strcmp($pgaction,"updateresched")) {
      $resched = getParameter("resched");
      $updateid = getParameter("updateid");
      //print "<br><br>resched: ".$resched."<br>updateid: ".$updateid."<br><br>";
      $scheduler->updateEmailJob($updateid,NULL,NULL,$resched);
   }
   
   $tstatus = $status;
   if(0==strcmp($tstatus,"ALL")) $tstatus="";
   $results = $scheduler->getScheduledEmails($semailid,$tstatus,$priority,NULL,NULL,NULL,$orderby,$page,$limit,$type,$classname,$searchtxt);

   if ($semailid!=NULL) {
      $line = $results[0];
      $answer = unserialize($line['phpobj']);
      print_r($answer);
   }

   $emails = $results['emails'];
   $totalPages = $results['totalPages'];

   $url = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=scheduledcsvs&semailid=".$semailid."&status=".$status."&type=".$type."&priority=".$priority."&classname=".$classname;
   $pageurl = $url."&orderby=".$orderby."&limit=".$limit."&page=";
   $orderbyurl = $url."&limit=".$limit."&page=1&orderby=";
   $reschedurl = $pageurl.$page."&pgaction=updateresched";
   $colspan=11;
?>

<script type="text/javascript">
      function expandSection(c,s) {
        if (document.getElementById(c).checked==true) {
            document.getElementById(s).style.display = "";
        } else {
            document.getElementById(s).style.display = "none";
        }
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

<?php
   $statusOpt['Not Finished'] = "NEW";
   $statusOpt['Running'] = "RUNNING";
   $statusOpt['Finished'] = "FINISHED";
   $statusOpt['Paused'] = "PAUSED";
   $statusOpt['All'] = "ALL";
   $statusSel = getOptionList("status", $statusOpt, $status);

   $limitOpt['17'] = 17;
   $limitOpt['25'] = 25;
   $limitOpt['50'] = 50;
   $limitOpt['100'] = 100;
   $limitOpt['200'] = 200;
   $limitOpt['1000'] = 1000;
   $limitSel = getOptionList("limit", $limitOpt, $limit);

   for ($i=1; $i<=10; $i++) $priorityOpt[$i] = $i;
   $prioritySel = getOptionList("priority", $priorityOpt, $priority, TRUE);
?>
   <table cellpadding="10" cellspacing="0"><tr><td>
   <table cellpadding="5" cellspacing="1" bgcolor="#EEEEEE">
   <form action="admincontroller.php" name="searchoptions" id="searchoptions" method="post">
   <input type="hidden" name="action" value="scheduledcsvs">
   <input type="hidden" name="type" value="<?php echo $type; ?>">
   <input type="hidden" name="page" value="<?php echo $page; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <tr>
   <td bgcolor="#EEEEEE"></td><td bgcolor="#EEEEEE"></td>
   <td bgcolor="#EEEEEE">Search:</td><td bgcolor="#EEEEEE"><input type="text" name="searchtxt" value="<?php echo $searchtxt; ?>" style="width:100px;font-size:12px;"></td>
   <td bgcolor="#EEEEEE"></td><td bgcolor="#EEEEEE"></td>
   <td bgcolor="#EEEEEE">Status:</td><td bgcolor="#EEEEEE"><?php echo $statusSel; ?></td>
   <td bgcolor="#EEEEEE"></td><td bgcolor="#EEEEEE"></td>
   <td bgcolor="#EEEEEE">Priority:</td><td bgcolor="#EEEEEE"><?php echo $prioritySel; ?></td>
   <td bgcolor="#EEEEEE"></td><td bgcolor="#EEEEEE"></td>
   <td bgcolor="#EEEEEE">Results Per Page:</td><td bgcolor="#EEEEEE"><?php echo $limitSel; ?></td>
   <td bgcolor="#EEEEEE"></td><td bgcolor="#EEEEEE"></td>
   <td bgcolor="#EEEEEE"><input type="submit" name="submit" value="Search"></td>
   </tr>
   </form>
   </table></td></tr></table>
      

   <form action="admincontroller.php" name="semaillist" id="semaillist" method="post">
   <input type="hidden" name="action" value="scheduledcsvs">
   <input type="hidden" name="type" value="<?php echo $type; ?>">
   <input type="hidden" name="classname" value="<?php echo $classname; ?>">
   <input type="hidden" name="page" value="<?php echo $page; ?>">
   <input type="hidden" name="limit" value="<?php echo $limit; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="semailid" value="<?php echo $semailid; ?>">
   <input type="hidden" name="status" value="<?php echo $status; ?>">
   <input type="hidden" name="priority" value="<?php echo $priority; ?>">
   <table cellpadding="5" cellspacing="1" bgcolor="#333333">
   <tr><td bgcolor="#FFFFFF" colspan="<?php echo $colspan; ?>">
   <table cellpadding="1" cellspacing="0" bgcolor="#EEEEEE"><tr>
   <td> Total entries: <?php echo $results['totalJobs']; ?> </td>
<?php
   if ($totalPages>600) $totalPages=600;
   if ($totalPages>1) {
      $start = 0;
      $end = $totalPages;
      if ($totalPages>30) {
         print "<td> &nbsp; </td><td> View pages:</td>";
         $pggroup = ceil($page/30);
         $start = ($pggroup-1)*30;
         if ($end>$start+30) $end = $start+30;
         $count=0;
         while ($totalPages>($count*30)) {
                  if (($count+1) == $pggroup) {
         ?>
                     <td bgcolor="#AAAAAA"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="3" height="1"></td>
                     <td bgcolor="#AAAAAA"><b><?php echo (($count*30)+1)."-".(($count*30)+30); ?></b></td>
                     <td bgcolor="#AAAAAA"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="3" height="1"></td>
            <?php } else { ?>
                     <td bgcolor="#EEEEEE"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="3" height="1"></td>
                     <td bgcolor="#EEEEEE"><a href="<?php echo $pageurl.(($count*30)+1); ?>"><b><?php echo (($count*30)+1)."-".(($count*30)+30); ?></b></a></td>
                     <td bgcolor="#EEEEEE"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="3" height="1"></td>
         <?php
                  }
            $count++;
         }
         $totalPages=30;
         ?>
            </tr></table>
            <table cellpadding="1" cellspacing="0" bgcolor="#EEEEEE"><tr>
         <?php
      }
      print "<td> &nbsp; &nbsp; Page: </td>";
      for ($i=$start; $i<$end; $i++) {
         $pg = $i+1;
         if ($pg == $page) {
?>
            <td bgcolor="#AAAAAA"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="2" height="1"></td>
            <td bgcolor="#AAAAAA"><b><?php echo $pg; ?></b></td>
            <td bgcolor="#AAAAAA"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="2" height="1"></td>
   <?php } else { ?>
            <td bgcolor="#EEEEEE"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="2" height="1"></td>
            <td bgcolor="#EEEEEE"><a href="<?php echo $pageurl.$pg; ?>"><b><?php echo $pg; ?></b></a></td>
            <td bgcolor="#EEEEEE"><img src="<?php echo $GLOBALS['baseURL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="2" height="1"></td>
<?php
         }
      }
   }
?>
   </table>
   </td></tr>
   <tr>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.timeadded%20DESC">Created</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>u.subject">Name</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.status">Status</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.field5">File</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.field1">Iteration</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.field2">Entries Created</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.priority">Priority</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.field3">Other</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.content">Content</a></td>
      <!-- td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.starton%20DESC">Starts On</a></td -->
      <!-- td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.timesent%20DESC">Finished</a></td -->
      <td bgcolor="#ccccff"><input type="checkbox" name="checkall" id="checkall" value="1" onClick="SetAllCheckBoxes('semaillist', 'a_semailid[]', document.semaillist.checkall.checked);"></td>
   </tr>
<?php 
   for ($i=0; $i<count($emails); $i++) { 
      $bgcolor="#DDDDDD";
      if (($i%2)==0) $bgcolor="#FFFFFF";
      //print "<TR><td colspan=\"10\">".$emails[$i]['userid']."</td></tr>";
      $disp = "";
      if($emails[$i]['userid']!=NULL && is_numeric($emails[$i]['userid'])) {
         $user = $ua->getUser($emails[$i]['userid']);
         if($user['fname']!=NULL || $user['lname']!=NULL) $disp = $user['fname']." ".$user['lname'];
         else $disp = $user['email'];
      }
?>
   <tr>
      <td bgcolor="<?php echo $bgcolor; ?>">
         <?php echo date("m/d/Y H:i",strtotime($emails[$i]['timeadded'])); ?>
         <div style="font-size:8px;color:#909090;"><?php echo $disp; ?></div>
      </td>
      <td bgcolor="<?php echo $bgcolor; ?>">
         <div onClick="jQuery('.schedrow<?php echo $i; ?>').show();">
         <?php
            $xtradisp = "";
            if(0==strcmp($emails[$i]['status'],"NEW") && 0==strcmp($emails[$i]['resched'],"weekly")) $xtradisp .= "<span style=\"font-size:8px;font-weight:bold;color:red;margin-right:15px;\">Runs Weekly</span>";
            else if(0==strcmp($emails[$i]['status'],"NEW") && 0==strcmp($emails[$i]['resched'],"monthly")) $xtradisp .= "<span style=\"font-size:8px;font-weight:bold;color:red;margin-right:15px;\">Runs Monthly</span>";
            
            if($emails[$i]['starton']!=NULL) {
               $starton = strtotime($emails[$i]['starton']);
               if($starton > time()) {
                  $xtradisp .= "<span style=\"font-size:8px;font-weight:bold;color:blue;margin-right:15px;\">Scheduled ".date("m/d/Y",$starton)."</span>";
               }
            }
            if($xtradisp!=NULL) print "<div>".$xtradisp."</div>";
         
            if($emails[$i]['subject']!=NULL) print $emails[$i]['subject'];
            else print $emails[$i]['classname'];
         ?>
         </div>
      </td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['status']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>">
         <?php
            if (0!=strcmp($emails[$i]['status'],"FINISHED")) print "(Not finished yet)<br>";

            //if (0==strcmp($emails[$i]['status'],"FINISHED")) {
               print "<a href=\"".getBaseURL().$emails[$i]['field5']."\" target=\"_new\">";
               print $emails[$i]['field5']; 
               print "</a>"; 
               
            //} else {
            //   print $emails[$i]['field5']; 
            //}
         ?>
      </td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['field1']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['field2']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['priority']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['field3']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo substr($emails[$i]['content'],0,32); ?></td>
      <!-- td bgcolor="<?php echo $bgcolor; ?>"><?php echo date("m/d/Y H:i",strtotime($emails[$i]['starton'])); ?></td -->
      <!-- td bgcolor="<?php echo $bgcolor; ?>"><?php if ($emails[$i]['timesent']!= NULL) echo date("m/d/Y H:i",strtotime($emails[$i]['timesent'])); ?></td -->
      <td bgcolor="<?php echo $bgcolor; ?>"><input type="checkbox" name="a_semailid[]" value="<?php echo $emails[$i]['semailid']; ?>"></td>
   </tr>
   <tr><td class="schedrow<?php echo $i; ?>" bgcolor="<?php echo $bgcolor; ?>" colspan="<?php echo $colspan; ?>" style="display:none;">
   <?php 
      if(0!=strcmp($emails[$i]['status'],"FINISHED")) {
         if($emails[$i]['classname']!=NULL && class_exists($emails[$i]['classname'])) {
            $phpclass = new $emails[$i]['classname']();
            if(method_exists($phpclass,"rescheduleJob")) {
               // display options for repeating this on weekly/monthly frequency
               $chk = array();
               $chk['never'] = "";
               $chk['weekly'] = "";
               $chk['monthly'] = "";
               if(0==strcmp($emails[$i]['resched'],"weekly")) {
                  $chk['never'] = "";
                  $chk['weekly'] = " SELECTED";
                  $chk['monthly'] = "";
               } else if(0==strcmp($emails[$i]['resched'],"monthly")) {
                  $chk['never'] = "";
                  $chk['weekly'] = "";
                  $chk['monthly'] = " SELECTED";
               }
               print "<div style=\"padding:5px;\">";
               print "<div style=\"float:left;margin-right:20px;\">Repeat this job:</div>";
               print "<div style=\"float:left;margin-right:20px;\">";
               print "<select id=\"reschedopt\">";
               print "<option value=\"never\"".$chk['never']."> Never</option>";
               print "<option value=\"weekly\"".$chk['weekly']."> Weekly</option>";
               print "<option value=\"monthly\"".$chk['monthly']."> Monthly</option>";
               print "</select>";
               print "</div>";
               print "<div style=\"float:left;margin-right:20px;\"><span onclick=\"location.href='".$reschedurl."&updateid=".$emails[$i]['semailid']."&resched=' + jQuery('#reschedopt').val();\" style=\"margin:3px;padding:3px 8px 3px 8px;border:1px solid #555555;border-radius:3px;background-color:#CCCCCC;font-size:10px;cursor:pointer;\">Submit</span></div>";
               print "<div style=\"clear:both;\"></div>";
               print "</div>";
            }
         }
      }
   ?>
   </td></tr>
   <tr><td id="rowc<?php echo $i; ?>" class="schedrow<?php echo $i; ?>" bgcolor="<?php echo $bgcolor; ?>" colspan="<?php echo $colspan; ?>" style="display:none;">
         <?php echo $emails[$i]['classname']; ?>
         Full content:<br>
         <?php echo $emails[$i]['content']; ?>
   </td></tr>
   <tr><td id="row<?php echo $i; ?>" class="schedrow<?php echo $i; ?>" bgcolor="<?php echo $bgcolor; ?>" colspan="<?php echo $colspan; ?>" style="display:none;">
         Full phpobj:<br>
         <?php 
            $answer = unserialize($emails[$i]['phpobj']);
            print_r($answer);
         ?>
   </td></tr>
<? } ?>
   <tr><td colspan="<?php echo $colspan; ?>" align="right">
      <input type="submit" name="submit" value="Pause"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Unpause"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Prioritize" onClick="return confirm('Please do not set too many jobs to high priority.  Are you sure you want to do this?');"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Delete" onClick="return confirm('Are you sure you want to delete these email jobs permanently?');"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Redo" onClick="return confirm('Are you sure you want to create a copy of the selected jobs?  NOTE: not all jobs can be duplicated at this time.');"> &nbsp; &nbsp;
   </td></tr>
   </table>
   </form>
   
   <div onclick="window.open('/jsfcode/cron_url_custom.php');" style="margin-top:20px;margin-bottom:10px;cursor:pointer;color:blue;border:1px solid #333333;text-align:center;font-size:8px;padding:4px;border-radius:3px;width:60px;">Force Run</div>
