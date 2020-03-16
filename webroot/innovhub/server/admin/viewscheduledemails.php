<?php
   $scheduler = new Scheduler();
   
   $semailid=trim(getParameter("semailid"));
   $status=trim(getParameter("status"));
   $priority=trim(getParameter("priority"));
   $orderby=trim(getParameter("orderby"));
   if ($orderby==NULL) $orderby = "e.timeadded DESC";
   $email=trim(getParameter("email"));
   $fname=trim(getParameter("fname"));
   $lname=trim(getParameter("lname"));
   $page = trim(getParameter("page"));
   if ($page==NULL || $page==0 || !is_numeric($page)) $page=1;
   $limit=trim(getParameter("limit"));
   if ($limit==NULL) $limit=50;
   $results = $scheduler->getScheduledEmails($semailid,$status,$priority,$email,$fname,$lname,$orderby,$page,$limit);

   $emails = $results['emails'];
   $totalPages = $results['totalPages'];

   $url = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=scheduledemails&semailid=".$semailid."&status=".$status."&priority=".$priority."&email=".$email."&fname=".$fname."&lname=".$lname;
   $pageurl = $url."&orderby=".$orderby."&limit=".$limit."&page=";
   $orderbyurl = $url."&limit=".$limit."&page=1&orderby=";
   $colspan=7;
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
   $statusOpt['NEW'] = "NEW";
   $statusOpt['FINISHED'] = "FINISHED";
   $statusOpt['PAUSED'] = "PAUSED";
   $statusSel = getOptionList("status", $statusOpt, $status, TRUE);

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
   <table cellpadding="5" cellspacing="1" bgcolor="#555555">
   <form action="admincontroller.php" name="searchoptions" id="searchoptions" method="post">
   <input type="hidden" name="action" value="scheduledemails">
   <input type="hidden" name="page" value="<?php echo $page; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <tr>
   <td bgcolor="#EEEEEE">First name:</td><td bgcolor="#EEEEEE"><input type="text" name="fname" value="<?php echo $fname; ?>"></td>
   <td bgcolor="#EEEEEE">Last name:</td><td bgcolor="#EEEEEE"><input type="text" name="lname" value="<?php echo $lname; ?>"></td>
   </tr><tr>
   <td bgcolor="#EEEEEE">Email:</td><td bgcolor="#EEEEEE"><input type="text" name="email" value="<?php echo $email; ?>"></td>
   <td bgcolor="#EEEEEE">Status:</td><td bgcolor="#EEEEEE"><?php echo $statusSel; ?></td>
   </tr><tr>
   <td bgcolor="#EEEEEE">Priority:</td><td bgcolor="#EEEEEE"><?php echo $prioritySel; ?></td>
   <td bgcolor="#EEEEEE">Results Per Page:</td><td bgcolor="#EEEEEE"><?php echo $limitSel; ?></td>
   </tr><tr>
      <td bgcolor="#EEEEEE" colspan="4" align="right"><input type="submit" name="submit" value="Search"></td>
   </tr>
   </form>
   </table></td></tr></table>
      

   <form action="admincontroller.php" name="semaillist" id="semaillist" method="post">
   <input type="hidden" name="action" value="scheduledemails">
   <input type="hidden" name="page" value="<?php echo $page; ?>">
   <input type="hidden" name="limit" value="<?php echo $limit; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="semailid" value="<?php echo $semailid; ?>">
   <input type="hidden" name="status" value="<?php echo $status; ?>">
   <input type="hidden" name="priority" value="<?php echo $priority; ?>">
   <input type="hidden" name="email" value="<?php echo $email; ?>">
   <input type="hidden" name="fname" value="<?php echo $fname; ?>">
   <input type="hidden" name="lname" value="<?php echo $lname; ?>">
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
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>u.email">Email Address</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.field3">From Email</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.status">Status</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.priority">Priority</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.timeadded%20DESC">Created</a></td>
      <td bgcolor="#ccccff"><a href="<?php echo $orderbyurl; ?>e.timesent%20DESC">Sent</a></td>
      <td bgcolor="#ccccff"><input type="checkbox" name="checkall" id="checkall" value="1" onClick="SetAllCheckBoxes('semaillist', 'a_semailid[]', document.semaillist.checkall.checked);"></td>
   </tr>
<?php 
   for ($i=0; $i<count($emails); $i++) { 
      $bgcolor="#DDDDDD";
      if (($i%2)==0) $bgcolor="#FFFFFF";
?>
   <tr>
      <td bgcolor="<?php echo $bgcolor; ?>">
         <a href="<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?noMenu=1&action=showemail&semailid=<?php echo $emails[$i]['semailid']; ?>" target="_new">
         <?php 
            if ($emails[$i]['email']!=NULL) echo $emails[$i]['email'];
            else echo $emails[$i]['field6'];
         ?>
         </a>
      </td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['field3']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['status']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo $emails[$i]['priority']; ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php echo date("m/d/Y H:i",strtotime($emails[$i]['timeadded'])); ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><?php if ($emails[$i]['timesent']!= NULL) echo date("m/d/Y H:i",strtotime($emails[$i]['timesent'])); ?></td>
      <td bgcolor="<?php echo $bgcolor; ?>"><input type="checkbox" name="a_semailid[]" value="<?php echo $emails[$i]['semailid']; ?>"></td>
   </tr>
<? } ?>
   <tr><td colspan="<?php echo $colspan; ?>" align="right">
      <input type="submit" name="submit" value="Pause"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Unpause"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Process Now" onClick="return confirm('Are you sure you want to send these emails now?');"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Re-send Email" onClick="return confirm('Are you sure you want to send these email jobs again?');"> &nbsp; &nbsp;
      <input type="submit" name="submit" value="Delete" onClick="return confirm('Are you sure you want to delete these email jobs permanently?');"> &nbsp; &nbsp;
   </td></tr>
   </table>
   </form>
