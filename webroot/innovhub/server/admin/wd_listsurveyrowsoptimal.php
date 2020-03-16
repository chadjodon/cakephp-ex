<?php
//error_reporting(E_ALL);
print "\n<!-- ***chj*** wd_listsurveyrowsopti.php start: ".date("Y-m-d h:i:s")." -->\n";
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct; 
   //$compInfo = new CompanyInfo();
   
   $mainurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php";   

   $wd_id = getParameter("wd_id");
   $pageLimit = getParameter("pageLimit");
   $filterStr = getParameter("filterStr");
   $orderby = getParameter("orderby");
   if ($orderby == null) $orderby = "d.wd_row_id DESC";

   $webdata = $wdOBJ->getWebData($wd_id);
   $temp = $wdOBJ->getRows($webdata['wd_id'],NULL,NULL,$filterStr,TRUE);
   
print "\n<!-- ***chj*** after count: ".date("Y-m-d h:i:s")." -->\n";
   $countResults = $temp['results'][0]['count(*)'];

   if ($pageLimit==null || $pageLimit<10 || $pageLimit>1000) $pageLimit=25;
   $pageNum = getParameter("pageNum");
   if ($pageNum==null || $pageNum==0) $pageNum=1;
   
   $results = $wdOBJ->getRows($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE);
   $rows = $results['results'];
   $totalPages = ceil($countResults/$pageLimit);
   
print "\n<!-- ***chj*** after rows: ".date("Y-m-d h:i:s")." -->\n";

   $cellbg="#FFFFFF";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;
?>

               <form id="updaterow" name="updaterow" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="action" value="wd_updaterow">
               <input type="hidden" name="updatecomments" value="1">
               <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
               <input type="hidden" name="wd_row_id" value="">
               <input type="hidden" name="comments" value="">
               </form>


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


<table width="100%" cellpadding="0" cellspacing="0">
<tr align="left" valign="top"><td><?php include ("wd_datavertmenu.php"); ?></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="1"></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td>


<!-- begin: jsfadmin/wd_listrows.php -->

   <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
      <tr>
         <td valign="top">
            <span class="heading"><?php echo $webdata['name']; ?></span>
            <span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&wd_id=<?= $wd_id ?>">Edit data structure</a> </span>
             &nbsp;&nbsp;&nbsp;
            <br><br>
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <TR>
               <FORM ACTION="form">
               <TD align="left">
                  <?php print $countResults; ?> results, 
                  view  
                  <select name="pageLimit" onChange="window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;">
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>" <?php if ($pageLimit==null || $pageLimit=="") print "SELECTED" ?>>All</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=10" <?php if ($pageLimit==10) print "SELECTED" ?>>10</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=30" <?php if ($pageLimit==30) print "SELECTED" ?>>30</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=50" <?php if ($pageLimit==50) print "SELECTED" ?>>50</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=100" <?php if ($pageLimit==100) print "SELECTED" ?>>100</option>
                  </select>
                  at a time.
               </td>
               </form>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
               <input type="hidden" name="action" value="wd_listrows">
               <td align="left">
                  &nbsp;&nbsp;Search:<input type="text" name="filterStr" value="<?php echo getParameter("filterStr"); ?>" size="10">
                  <input type="submit" name="go" value="go">
               </td>
               </form>

               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
               <input type="hidden" name="action" value="wd_search">
               <td align="left">
                  <input type="submit" name="go" value="Advanced Search">
               </td>
               </form>
               <td>
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=<?= $orderby ?>&pageLimit=<?= $pageLimit ?>&refresh=1">Refresh</a>
               </td>

               <!--
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
               <input type="hidden" name="action" value="wd_schedulecsv">
               <td align="left">
                  <input type="submit" name="go" value="CSV">
               </td>
               </form>
               -->

            </tr><tr>
               <td colspan="4" align="right">

<?php
                  print "\n<!-- ***chj*** print pages: ".date("Y-m-d h:i:s")." -->\n";
                  if ($pageNum != null && $totalPages != null && $totalPages > 1) {
                     $pageTable = "<table align=\"right\"><tr><td>Page: </td>";
                     $url = "admincontroller.php?action=wd_listrows&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=";
                     for ($i=1; $i<=$totalPages; $i++) {
                        if ($pageNum == $i) $pageTable .= "<td bgcolor=\"#AAAAAA\"><b>".$i."</b></td>";
                        else $pageTable .= "<td><a href=\"".$url.$i."\">".$i."</a></td>";
                     }
                     $pageTable .= "</tr></table>";
                     print $pageTable;
                  }
?>

               </td>
            </tr>
            </table>
            
            <!-- List rows -->
            <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
            <form name="wd_updaterow" id="wd_updaterow" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
            <input type="hidden" name="action" value="wd_updaterow">
            <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
            <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
            <input type="hidden" name="orderby" value="<?= $orderby ?>">
            <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
            <TR><TD>
            <table width="100%" cellspacing="1" cellpadding="4" border="0">
            
            <?php
            print "\n<!-- ***chj*** about to print rows: ".date("Y-m-d h:i:s")." -->\n";
            
            print "\n<!-- ****chj*** Rows: \n";
            print_r($rows);
            print "\n\n-->\n";
            
            $adminusers = NULL;
            $tempusers = $ua->getAdminUsers();
            for ($i=0; $i<count($tempusers); $i++) $adminusers[$tempusers[$i]['userid']]=$tempusers[$i]['email'];
            $totalcolumns = 10 + count($headers);
            
            $showcontact = FALSE;
            if(count($rows)<101) {
               print "\n<!-- ***chj*** getting contact info for companies: ".date("Y-m-d h:i:s")." -->\n";
               $contactinfo = array();
               $totalcolumns++;
               $showcontact = TRUE;
               $uids = "";
               for ($i=0; $i<count($rows); $i++) {
                  if($i>0) $uids .= ", ";
                  if($rows[$i]['userid']!=NULL) $uids .= $rows[$i]['userid'];
                  else $uids .= "-1";
               }
               
               $dbi = new MYSQLAccess();
               $contactquery = "SELECT u.fname, u.lname, u.email, u.phonenum, r.userid, r.reluserid FROM useracct u, userrel r WHERE u.userid=r.reluserid AND r.userid IN (".$uids.") AND r.rel_type='SRVYADMIN';";
               $contacts = $dbi->queryGetResults($contactquery);
               for($i=0;$i<count($contacts);$i++){
                  $contactinfo[$contacts[$i]['userid']] = $contacts[$i];
               }
               
            }
            ?>
            

            <TR class="small_table_header">
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=c.company%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Company</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=c.state%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">State</a></TD>
               
               <?php if($showcontact) { ?>
               <TD bgcolor="<?= $cellbg ?>">Contact</TD>
               <?php } ?>
               
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=c.field5%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Admin Responsible</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Survey Notes</td>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.datesent%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Sent</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.lastupdate%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Change</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Security access</td>
               <?php
                  $headers = $wdOBJ->getHeaderFields($wd_id);
                  for ($i=0; $i<count($headers); $i++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&filterStr=".$filterStr."&wd_id=".$wd_id."&orderby=d.".$headers[$i]['field_id']."%20ASC".$neworderby."&pageLimit=".$pageLimit."\">";
                     print $headers[$i]['label'];
                     print "</a>";
                     print "</td>\n";
                  }
               ?>
               <TD bgcolor="<?= $cellbg ?>"><input type="checkbox" onClick="SetAllCheckBoxes('wd_updaterow', 'wd_row_id_CB[]', document.wd_updaterow.checkall.checked);" name="checkall" value="checkall"></TD>
            </tr>
            
            
            <?php
            
            if ($rows==NULL || count($rows) == 0) print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"".$totalcolumns."\" ALIGN=\"CENTER\"><font color=\"red\"><b>There is no data to display.</b></font></td></tr>";
            for ($i=0; $i<count($rows); $i++) {
               $companyDisplay = $rows[$i]['company']."<BR>".$rows[$i]['website'];
               if ($rows[$i]['company']==NULL) $companyDisplay="Responder ".$rows[$i]['wd_row_id'];
            ?>
               <TR class="small_table">
               <TD bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>ViewWData.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  <?php echo $companyDisplay; ?></a>
                  <br>
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  [Alt]</a>
               </td>
               <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['state'] ?></td>

               <?php 
               if($showcontact) {
                  print "<TD bgcolor=\"".$cellbg."\">";
                  if(isset($contactinfo[$rows[$i]['orgid']])) {
                     print $contactinfo[$rows[$i]['orgid']]['fname']." ".$contactinfo[$rows[$i]['orgid']]['lname']."<br>";
                     print $contactinfo[$rows[$i]['orgid']]['email']."<br>";
                     print $contactinfo[$rows[$i]['orgid']]['phonenum'];
                  }
                  print "</TD>\n";
               }
               ?>               
               
               <TD bgcolor="<?= $cellbg ?>"><?php echo $adminusers[$rows[$i]['field5']]; ?></td>

               <td bgcolor="<?= $cellbg ?>">
                     <textarea rows="2" cols="20" name="c<?php echo $rows[$i]['wd_row_id']; ?>" id="urc<?php echo $rows[$i]['wd_row_id']; ?>"><?= convertBack($rows[$i]['comments']) ?></textarea>
<?php
                        $updateComments = "";
                        $updateComments .= "document.updaterow.wd_row_id.value='".$rows[$i]['wd_row_id']."';";
                        $updateComments .= "document.updaterow.comments.value=document.getElementById('urc".$rows[$i]['wd_row_id']."').value;";
                        //$updateComments .= "alert('Hello: ".$rows[$i]['wd_row_id']." world! ' + document.updaterow.wd_row_id.value);";
                        $updateComments .= "document.updaterow.submit();";
?>
                     <a href="#" onClick="<?php echo $updateComments; ?>">Update</a>
               </td>

               <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['datesent'] ?></td>
               <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['lastupdate'] ?></td>

                   <?php
                     $openlink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&complete=Y&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Open</a>";
                     $closelink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&complete=L&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Close</a>";
                     $attnlink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&complete=A&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">ATTN!</a>";
                     $speciallink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=wd_listrows&complete=X&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Special</a>";
                     $seplink = "<br>";

                      if (0==strcmp($rows[$i]['complete'],"Y")) {
                           $link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $statusbg="#6FFF6F";
                           $status = "Open";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"L")) {
                           $link = $openlink.$seplink.$speciallink.$seplink.$attnlink;
                           //$link = $speciallink.$seplink.$attnlink;
                           $statusbg="#DDDDDD";
                           $status = "Closed";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"N")) {
                           $link = $openlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                           //$link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $statusbg="#FFFFFF";
                           $status = "New";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"X")) {
                           $link = $openlink.$seplink.$closelink.$seplink.$attnlink;
                           //$link = $closelink.$seplink.$attnlink;
                           $statusbg="#FDFF5B";
                           $status = "Special";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"A")) {
                           $link = $openlink.$seplink.$closelink.$seplink.$speciallink;
                           //$link = $closelink.$seplink.$speciallink;
                           $statusbg="#FF4348";
                           $status = "Attention!";
                      }
                      else {
                           $link = $openlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                           //$link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $statusbg="#FFFFFF";
                           $status = "N/A";
                      }
                   ?>
               <TD bgcolor="<?php echo $statusbg; ?>">
                     <?php echo $status; ?><br>
                     <?php echo $link; ?><br>
                     <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                     <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_updaterow&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>" onclick="return confirm('Are you sure you want to delete this respondant and all his/her responses?')">Delete</a>
                     <?php } ?>
               </td>
               <td bgcolor="<?= $cellbg ?>"><?php echo $rows[$i]['origemail']; ?></td>

               <?php
                  $headers = $wdOBJ->getHeaderFields($wd_id);
                  for ($j=0; $j<count($headers); $j++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print $rows[$i][$headers[$j]['field_id']];
                     print "</td>\n";
                  }
               ?>

               <?php if (0==strcmp($rows[$i]['field1'],"YES")) { ?>
                  <td bgcolor="<?= $cellbg ?>">
               <?php } else { ?>
                  <td bgcolor="RED">
               <?php } ?>
                  <input type="checkbox" name="wd_row_id_CB[]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
                  </td>

               </tr>

            <?php
            }
            print "\n<!-- ***chj*** after printing rows: ".date("Y-m-d h:i:s")." -->\n";
            ?>

               <tr>
                  <td colspan="<?php echo $totalcolumns; ?>" bgcolor="<?= $cellbg ?>" align="right">
                     <button onclick="window.open('<?php echo getBaseURL(); ?>jsfcode/ViewWDataJSON.php?admin=1&wd_id=<?php echo $webdata['wd_id']; ?>');return false;">Create a new row</button>
                     <input type="submit" name="submit" value="Send email to selected rows">

                     <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                        <input type="submit" name="submit" value="Delete selected rows" onClick="return confirm('Are you sure you want to delete these rows permanently?');">
                     <?php } ?>
                  </td>
               </tr>

            </table>
            </tr></td>
            </form></table>

         <BR>
      </td></tr>

      
      
      
      
      
      
      
      
      







   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),7)) { ?>
      <tr><td>
            <br><hr>
      </td></tr>
      <TR><TD>
            <input id="csvdownloadtbl_cb" type="checkbox" onclick="javascript: expandSection('csvdownloadtbl_cb','csvdownloadsect');" >Display CSV Options
            <form action="<?php echo $mainurl; ?>" method="post" id="csvfields" name="csvfields">
            <input type="hidden" name="action" value="dlwdcsv">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
            <input type="hidden" name="filterStr" value="<?php echo $filterStr ?>">
            <input type="hidden" name="pageLimit" value="<?php echo $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?php echo $pageNum ?>">
            <table id="csvdownloadsect" cellpadding="5" cellspacing="0" style="display: none;">
             <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
             <tr><td colspan="3"><b>CSV Download:</b></td></tr>
            <tr><td colspan="3"><b>Subject: </b> <input type="text" name="subject" value=""></td></tr>
            <tr>
               <td colspan="3">
               Please select the survey elements you would like in your csv:<br>
               <input type="checkbox" onClick="SetAllCheckBoxes('csvfields', 'qids[]', document.csvfields.checkall.checked);" name="checkall" value="checkall">Select All
   <?php
   
               $questions = $wdOBJ->getAllFieldsSystem($wd_id);
               $options = array();
               for ($j=0; $j<count($questions); $j++) {
                  if (0!=strcmp($questions[$j]['field_type'],"INFO") && 0!=strcmp($questions[$j]['field_type'],"SPACER")) {
                     $label = substr($questions[$j]['label'],0,27)."... (".$questions[$j]['field_id'].")";
                     $options[$label]= $questions[$j]['field_id'];
                  }
               }
               print getCheckboxList("qids", $options, null);
   
   ?>               
                  <hr><input type="submit" name="submit" value="Download CSV">
               </td>
            </tr>
            </table>
            </form>
      </td></tr>
      <tr><td>
         <div style="padding:10px;font-size:14px;font-family:arial;color:#555555;">
         <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newwdfile" method="POST">
         <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
         <input type="hidden" name="action" value="uploadwdcsv">
         <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
         <input type="hidden" name="filterStr" value="<?php echo $filterStr ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby ?>">
         <input type="hidden" name="pageLimit" value="<?php echo $pageLimit ?>">
         <input type="hidden" name="pageNum" value="<?php echo $pageNum ?>">
         <input type="hidden" name="simpledisplay" value="<?php echo $simpledisplay ?>">
         <input type="hidden" name="showdelete" value="<?php echo $showdelete ?>">
         <input type="hidden" name="public" value="<?php echo $public ?>">
         CSV File Upload: 
         <input name="wdcsv" type="file"> 
         <input type="submit" name="Load Data" value="Load Data">
         </form>
         </div>
      </td></tr>   
<?php } ?>








   </table>

<!-- end: jsfadmin/wd_listrows.php -->

</td></tr>
</table>

<?php
print "\n<!-- ***chj*** end: ".date("Y-m-d h:i:s")." -->\n";
?>
