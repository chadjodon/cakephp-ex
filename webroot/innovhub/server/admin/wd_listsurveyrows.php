<?php
//error_reporting(E_ALL);
print "\n<!-- ***chj*** start: ".date("Y-m-d h:i:s")." -->\n";
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct; 
   //$compInfo = new CompanyInfo();

   $wd_id = getParameter("wd_id");

   $pageLimit = getParameter("pageLimit");
   $filterStr = getParameter("filterStr");
   $orderby = getParameter("orderby");
   if ($orderby == null) $orderby = "d.wd_row_id DESC";

   $webdata = $wdOBJ->getWebData($wd_id);
   $temp = $wdOBJ->getRowsSurveyOrgAdmin ($wd_id, null, null, $filterStr, TRUE,(getParameter("refresh")==1));
print "\n<!-- ***chj*** after count: ".date("Y-m-d h:i:s")." -->\n";
   $countResults = $temp['results'][0]['count(*)'];

   $orgParams[0] = "q35";
   $orgParams[1] = "q47";
   $orgParams[2] = "q8";
   $orgParams[3] = "q7";

   if ($pageLimit==null) {
      $limitStmnt=null;
      $results = $wdOBJ->getRowsSurveyOrgAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $orgParams);
      $rows = $results['results'];
      $pageNum=null;
      $totalPages=null;
   } else {
      $pageNum = getParameter("pageNum");
      if ($pageNum==null || $pageNum==0) $pageNum=1;
      if ($pageLimit<10) $pageLimit=30;
      $pageStart = $pageLimit*($pageNum - 1);
      $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
      $results = $wdOBJ->getRowsSurveyOrgAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $orgParams);
      $rows = $results['results'];
      $totalPages = ceil($countResults/$pageLimit);
   }
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
            </tr><tr>
               <td colspan="4" align="right">

<?php
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
            <TR class="small_table_header">
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=c.company%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Company</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=o.q35%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Priority</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=o.q47%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Primary Org type</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=o.q8%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Admin Responsible</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Survey Notes</td>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.datesent%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Sent</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.lastupdate%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Change</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <!-- TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.q211%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Priority</a></TD -->
               <!-- TD bgcolor="<?= $cellbg ?>">Percentage</td -->
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
            $adminusers = NULL;
            $tempusers = $ua->getAdminUsers();
            for ($i=0; $i<count($tempusers); $i++) $adminusers[$tempusers[$i]['userid']]=$tempusers[$i]['email'];
            $totalcolumns = 10 + count($headers);
            if ($rows==NULL || count($rows) == 0) print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"".$totalcolumns."\" ALIGN=\"CENTER\"><font color=\"red\"><b>There is no data to display.</b></font></td></tr>";
            for ($i=0; $i<count($rows); $i++) {
               //$company = $ua->getFullUserInfo($rows[$i]['orgid']);
               $companyDisplay = $rows[$i]['company']."<BR>".$rows[$i]['website'];
            ?>
               <TR class="small_table">
               <TD bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>ViewWData.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  <?php echo $companyDisplay; ?></a>
               </td>

               <TD bgcolor="<?= $cellbg ?>"><?php print $rows[$i]['orgq35']; ?></TD>
               <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['orgq47'] ?></td>
               <TD bgcolor="<?= $cellbg ?>"><?php echo $adminusers[$rows[$i]['orgq8']]; ?><br></td>
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
               <!-- td bgcolor="<?= $cellbg ?>"><?php echo $rows[$i]['q211']; ?></td -->
               <!-- td bgcolor="<?= $cellbg ?>">
                  <?php 
                     //echo $wdOBJ->getAnsweredPercentage($rows[$i]['wd_row_id']); 
                  ?>
               </td -->
               <td bgcolor="<?= $cellbg ?>"><?php echo $rows[$i]['origemail']; ?></td>

               <?php
                  $headers = $wdOBJ->getHeaderFields($wd_id);
                  for ($j=0; $j<count($headers); $j++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print $rows[$i][$headers[$j]['field_id']];
                     print "</td>\n";
                  }
               ?>

               <?php if (0==strcmp($rows[$i]['orgq7'],"YES")) { ?>
                  <td bgcolor="<?= $cellbg ?>">
                  <input type="checkbox" name="wd_row_id_CB[]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
                  </td>
               <?php } else { ?>
                  <td bgcolor="RED">
                  <input type="checkbox" name="wd_row_id_CB[]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
                  </td>
               <?php } ?>
               </tr>

            <?php
            }
            ?>

               <tr>
                  <td colspan="<?php echo $totalcolumns; ?>" bgcolor="<?= $cellbg ?>" align="right">
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
         <table id="csvdownloadsect" cellpadding="5" cellspacing="0" style="display: none;">
          <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
          <tr><td colspan="3"><b>CSV Download:</b></td></tr>
         <tr>
            <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post" id="csvfields" name="csvfields">
            <input type="hidden" name="action" value="dlcsvwd">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
            <td colspan="3">
            Please select the survey elements you would like in your csv:<br>
            <input type="checkbox" onClick="SetAllCheckBoxes('csvfields', 'qids[]', document.csvfields.checkall.checked);" name="checkall" value="checkall">Select All
<?php
   function printQOptions($wd_id,$section) {
      $wdOBJ = new WebsiteData();
      $s = $wdOBJ->getSection($wd_id,$section);
      print "<table cellpadding=\"2\" cellspacing=\"2\"><tr><td>";
      if ($s!=NULL) print "<b>".$s['label']."</b>";
      $fields = $wdOBJ->getFields($wd_id, $section);
      for ($i=0; $i<count($fields); $i++) {
         $q = $fields[$i];
         if (0!=strcmp($q['field_type'],"INFO") && 0!=strcmp($q['field_type'],"SPACER")) {
            if (strcmp($q['field_type'],"TABLE")==0) {
               $questionText = convertBack($q['label']);
               $temp = separateStringBy(" ".$questionText,";");
               $headers = separateStringBy(" ".$temp[0],",");
               $rows = separateStringBy(" ".$temp[1],",");
               $label = "Table ".$q['field_id']." (".strip_tags($headers[1].":".$rows[0])."...)";
               $options[$label]= $q['field_id'];
            } else {
               $label = strip_tags($q['label']);
               if (strlen($label) > 25) {
                  $label = substr($label,0,22)."... (".$q['field_id'].")";
               } else {
                  $label = $label." (".$q['field_id'].")";
               }
               $options[$label]= $q['field_id'];
            }
         }
      }
      if (count($fields)>0) print getCheckboxList("qids", $options, null);
   
      $sections = $wdOBJ->getDataSections($wd_id,$section);
      for ($i=0; $i<count($sections); $i++) printQOptions($wd_id,$sections[$i]['section']);
      print "</td></tr></table>";
   }

               printQOptions($wd_id,-1);
?>               
               <hr><input type="submit" name="submit" value="Download CSV">
            </td>
            </form>
         </tr>
         </table>
   </td></tr>
<?php } ?>
   </table>

<!-- end: jsfadmin/wd_listrows.php -->

</td></tr>
</table>

<?php
print "\n<!-- ***chj*** end: ".date("Y-m-d h:i:s")." -->\n";
?>
