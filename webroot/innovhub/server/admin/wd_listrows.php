<!-- *chj* jsfadmin/wd_listrows.php -->
<?php
//error_reporting(E_ALL);
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct;

   $wd_id = getParameter("wd_id");
   $userid = getParameter("userid");
   $s_userid = getParameter("s_userid");
   $pageLimit = getParameter("pageLimit");
   $filterStr = getParameter("filterStr");
   $orderby = getParameter("orderby");
   $pageNum = getParameter("pageNum");
   $simpledisplay = getParameter("simpledisplay");
   $showdelete = getParameter("showdelete");
   $public = getParameter("public");
   $page = getParameter("page");
   $phpinclude = getParameter("phpinclude");

   $mainurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php";
   if ($public==1) $mainurl = $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']."controller.php";

   if ($orderby == null) $orderby = "d.wd_row_id DESC";

   $webdata = $wdOBJ->getWebData($wd_id);
   $ret = $wdOBJ->getFieldsMultiIndex($webdata['wd_id']);
   $allfields = $ret['allfields'];
   $qs = $ret['bylabel'];
   
   // Get parent/children parameters ready if there is a relationship internal
   $parentfield = NULL;
   $p_fielddisp = NULL;
   for($i=0;$i<count($allfields);$i++) {
      if(0==strcmp($allfields[$i]['field_type'],"FOREIGN") && (strpos(strtolower($allfields[$i]['label']),"parent")!==FALSE || strpos(strtolower($allfields[$i]['map']),"parent")!==FALSE)){
         $temparr = separateStringBy(convertBack($allfields[$i]['question']),",");
         $pwebdata = $wdOBJ->getWebData(trim($temparr[0]));
         if($webdata['wd_id']==$pwebdata['wd_id']) {
            $p_fielddisp = $qs[strtolower(trim($temparr[1]))];
            $parentfield = $allfields[$i];
            //print "<br>\n[".$temparr[1]."] field to be used: ".$p_fielddisp."<br>\n";
            break;
         }
      }
   }
   
   $temp = $wdOBJ->getRows($webdata['wd_id'], null, null, $filterStr, TRUE, $s_userid);
   $countResults = $temp['results'][0]['count(*)'];
   $headers = $wdOBJ->getHeaderFields($webdata['wd_id']);

   $results = NULL;
   if ($pageLimit==null || ($countResults<200 && $parentfield!=NULL)) {
      $limitStmnt=null;
      $results = $wdOBJ->getRows($webdata['wd_id'], $orderby, $limitStmnt, $filterStr, FALSE, $s_userid);
      $rows = $results['results'];
      $pageNum=null;
      $totalPages=null;
      
      //Format rows into better visible formatting if there's a parent/child relationship
      if($parentfield!=NULL) {
         $temprows = $wdOBJ->structureParentChild($rows,$parentfield);
         if($temprows!=NULL && count($temprows)>0) $rows = $temprows;
      }
   } else {
      if ($pageNum==null || $pageNum==0) $pageNum=1;
      if ($pageLimit==NULL) $pageLimit=30;
      $pageStart = $pageLimit*($pageNum - 1);
      $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
      $results = $wdOBJ->getRows($webdata['wd_id'], $orderby, $limitStmnt, $filterStr, FALSE, $s_userid);
      $rows = $results['results'];
      $totalPages = ceil($countResults/$pageLimit);
      $parentfield = NULL;
   }

   $search_fields = "";
   $search_url = "";
   foreach($results['params'] as $key => $val) {
      $search_fields .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\">\n";
      $search_url .= "&".$key."=".$val;
   }

   $cellbg="lightblue";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;

   if ($simpledisplay!=1) {
         //Start non-simpledisplay
?>



<div style="width:10px;height:10px;overflow:hidden;"></div>

<!--
<div id="csvnoshow">
<div style="font-size:10px;font-family:arial;color:#555555;cursor:pointer;" onclick="$('#csvnoshow').hide();$('#csvshow').show();">+ Download CSV</div>
</div>
<div id="csvshow" style="display:none;">
<div style="font-size:10px;font-family:arial;color:#555555;cursor:pointer;" onclick="$('#csvshow').hide();$('#csvnoshow').show();">- Hide</div>
<form action="<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php" method="POST">
<input type="hidden" name="action" value="submitwdcsv">
<input type="hidden" name="wd_id" value="<?php echo $webdata['wd_id']; ?>">
<table cellpadding="2" cellspacing="0"><tr>
<td><b>Download CSV</b> &nbsp; </td>
<td> Subject:</td>
<td><input type="text" name="subject" value=""></td>
<td><input type="submit" name="submit" value="submit"></td>
</tr></table>
</form><br>
</div>
-->



      <form id="updaterow" name="updaterow" action="<?php echo $mainurl; ?>" method="POST">
      <input type="hidden" name="action" value="wd_updaterow">
      <input type="hidden" name="updatecomments" value="1">
      <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
      <input type="hidden" name="wd_id" value="<?= $webdata['wd_id'] ?>">
      <input type="hidden" name="orderby" value="<?= $orderby ?>">
      <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
      <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
      <input type="hidden" name="wd_row_id" id="wd_row_id" value="">
      <input type="hidden" name="comments" id="comments" value="">
      <input type="hidden" name="userid" value="<?= $userid ?>">
      <input type="hidden" name="s_userid" value="<?= $s_userid ?>">
      <input type="hidden" name="simpledisplay" value="<?= $simpledisplay ?>">
      <input type="hidden" name="showdelete" value="<?= $showdelete ?>">
      <input type="hidden" name="public" value="<?= $public ?>">
      <?php echo $search_fields; ?>
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


<table cellpadding="0" cellspacing="0">
<tr align="left" valign="top"><td><?php include ("wd_datavertmenu.php"); ?></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="1"></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td>

<!-- begin: jsfadmin/wd_listrows.php 2.0.6 -->

   <table border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
         <td valign="top">
            <span class="heading"><?php echo $webdata['name']; ?></span>
            <span class="button01">
               <a href="<?php echo $mainurl; ?>?action=webdata&wd_id=<?= $webdata['wd_id'] ?>">Edit data structure</a>
            </span>
             &nbsp;&nbsp;&nbsp;
            <span class="button01">
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=<?php echo $webdata['wd_id']; ?>" target="_blank"> Add a new Record </a>               
            </span>
            <?php if($webdata['privatesrvy']==10) { ?>
                &nbsp;&nbsp;&nbsp;
               <span class="button01">
                  <a href="<?php echo $mainurl; ?>?action=buildsearchindex&wd_id=<?php echo $webdata['wd_id']; ?>" target="_blank"> Build Search Index </a>               
               </span>
            <?php } else { ?>
               <span onclick="jQuery('#wd_listrows_buildindex').show();">.</span>
               <span id="wd_listrows_buildindex" style="display:none;">
                &nbsp;&nbsp;&nbsp;
               <span class="button01">
                  Keywords field to index: 
                  <input type="text" id="wd_listrows_buildindex_keywords" style="width:120px;font-size:12px;">
                  <span onclick="location.href='<?php echo $mainurl; ?>?action=buildsearchindex&wd_id=<?php echo $webdata['wd_id']; ?>&keywordsfield=' + jQuery('#wd_listrows_buildindex_keywords').val();" style="cursor:pointer;color:blue;"> Go </span>               
               </span>
               </span>
            <?php } ?>
            <br><br>
            
            <table border="0" cellpadding="0" cellspacing="0">
            <TR>
               <FORM ACTION="form">
               <TD align="left">
                  <?php print $countResults; ?> results, 
                  view  
                  <select name="pageLimit" onChange="window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;">
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>" <?php if ($pageLimit==null || $pageLimit=="") print "SELECTED" ?>>All</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=10" <?php if ($pageLimit==10) print "SELECTED" ?>>10</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=30" <?php if ($pageLimit==30) print "SELECTED" ?>>30</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=50" <?php if ($pageLimit==50) print "SELECTED" ?>>50</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=100" <?php if ($pageLimit==100) print "SELECTED" ?>>100</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=200" <?php if ($pageLimit==200) print "SELECTED" ?>>200</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $webdata['wd_id'] ?>&userid=<?= $userid ?>&s_userid=<?= $s_userid ?>&simpledisplay=<?= $simpledisplay ?>&filterStr=<?= $filterStr ?><?php echo $search_url; ?>&orderby=<?= $orderby ?>&pageLimit=1000" <?php if ($pageLimit==1000) print "SELECTED" ?>>1000</option>
                  </select>
                  at a time.
               </td>
               </form>
               <form action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="wd_id" value="<?= $webdata['wd_id'] ?>">
               <input type="hidden" name="action" value="wd_listrows">
               <td align="left">
                  &nbsp;&nbsp;Search:<input type="text" name="filterStr" value="" size="10">
                  <input type="submit" name="go" value="go">
               </td>
               </form>
               <form action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="wd_id" value="<?= $webdata['wd_id'] ?>">
               <input type="hidden" name="action" value="wd_search">
               <td align="left">
                  <input type="submit" name="go" value="Advanced Search">
               </td>
               </form>
            </tr>
<?php
         //End non-simpledisplay
      } else {
         // simpledisplay
         if ($userid!=NULL) {
            $user = $ua->getUser($userid);
            print "<br><a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermod&userid=".$userid."\">Return to profile: ".$user['fname']." ".$user['lname']." ".$user['company']."</a><br><br>";
         }
         print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
      }

      $pgGroups = 1;
      $curPgGroup = 1;
      if ($totalPages>30) {
         $pgGroups = ceil($totalPages/30);
         $curPgGroup = ceil($pageNum/30);
         print "<TR><td align=\"left\" colspan=\"3\">Page Group: ";
         for ($i=1; $i<=$pgGroups; $i++) {
            $url = $mainurl."?action=wd_listrows&wd_id=".$webdata['wd_id']."&simpledisplay=".$simpledisplay."&userid=".$userid."&s_userid=".$s_userid."&filterStr=".$filterStr.$search_url."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".((($i-1)*30)+1);
            if ($curPgGroup == $i) print "<b>".(($i-1)*30+1)."-".(($i-1)*30+30)."</b>";
            else print "<a href=\"".$url."\">".(($i-1)*30+1)."-".(($i-1)*30+30)."</a>";
            print " &nbsp; ";
         }
         print "</td></tr>";
      }
?>
            <TR>
            <td colspan="3" align="left">
<?php
                  if ($pageNum != null && $totalPages != null && $totalPages > 1) {
                     $pageTable = "<table align=\"left\"><tr><td>Page: </td>";
                     $url = $mainurl."?action=wd_listrows&wd_id=".$webdata['wd_id']."&simpledisplay=".$simpledisplay."&userid=".$userid."&s_userid=".$s_userid."&filterStr=".$filterStr.$search_url."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=";
                     $lastPage = (($curPgGroup-1)*30+30);
                     if ($lastPage > $totalPages) $lastPage = $totalPages;
                     for ($i=(($curPgGroup-1)*30+1); $i<=$lastPage; $i++) {
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
            <table border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
            <form enctype="multipart/form-data" name="wd_updaterow" id="wd_updaterow" action="<?php echo $mainurl; ?>" method="POST">
            <input type="hidden" name="action" value="wd_updaterow">
            <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
            <input type="hidden" name="wd_id" value="<?= $webdata['wd_id'] ?>">
            <input type="hidden" name="orderby" value="<?= $orderby ?>">
            <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
            <input type="hidden" name="userid" value="<?= $userid ?>">
            <input type="hidden" name="s_userid" value="<?= $s_userid ?>">
            <input type="hidden" name="simpledisplay" value="<?= $simpledisplay ?>">
            <input type="hidden" name="showdelete" value="<?= $showdelete ?>">
            <input type="hidden" name="public" value="<?= $public ?>">
            <input type="hidden" name="page" value="<?= $page ?>">
            <input type="hidden" name="updateForm" value="1">
            <input type="hidden" name="phpinclude" value="<?= $phpinclude ?>">
            <?php echo $search_fields; ?>
            <TR><TD>
            <table cellspacing="1" cellpadding="4" border="0">
            
            <TR class="small_table_header">
               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>">Contact Info</TD>
                  <TD bgcolor="<?= $cellbg ?>">Notes</TD>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $webdata['wd_id'] ?>&orderby=d.complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <?php } else { ?>
                  <td bgcolor="<?= $cellbg ?>"></TD>
               <?php } ?>

               <?php
                  $fld_subs = array();
                  for ($i=0; $i<count($headers); $i++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print "<a href=\"".$mainurl."?action=wd_listrows&simpledisplay=".$simpledisplay."&userid=".$userid."&s_userid=".$s_userid."&filterStr=".$filterStr."&wd_id=".$webdata['wd_id']."&orderby=d.".$headers[$i]['field_id']."%20ASC".$neworderby."&pageLimit=".$pageLimit."\">";
                     print $headers[$i]['label'];
                     print "</a>";
                     print "</td>\n";
                     
                     if(0==strcmp($headers[$i]['field_type'],"FOREIGN") || 0==strcmp($headers[$i]['field_type'],"FOREIGNCB")) {                     
                        $survey_info = separateStringBy(convertBack($headers[$i]['question']),",",NULL,TRUE);
                        $fld_subs[$headers[$i]['field_id']] = $wdOBJ->getSurveyRowsIndexed($survey_info[0],$survey_info[1]);
                     }
                     
                  }
               ?>

               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $webdata['wd_id'] ?>&orderby=d.created%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Created</a></TD>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $webdata['wd_id'] ?>&orderby=d.lastupdate%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Change</a></TD>
                  <TD bgcolor="<?= $cellbg ?>"><input type="checkbox" onClick="SetAllCheckBoxes('wd_updaterow', 'wd_row_id_CB[]', document.wd_updaterow.checkall.checked);" name="checkall" value="checkall"></TD>
               <?php } else { ?>
                  <td bgcolor="<?= $cellbg ?>"></td>
               <?php } ?>
            </tr>
            
            <?php
            $totalcolumns = 6 + count($headers);
            if ($simpledisplay==1) $totalcolumns = 2 + count($headers);
            if ($rows==NULL || count($rows) == 0) print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"".$totalcolumns."\" ALIGN=\"CENTER\"><font color=\"red\"><b>List is currently empty.</b></font></td></tr>";
            for ($i=0; $i<count($rows); $i++) {
               $user = $ua->getUser($rows[$i]['userid']);
               $userDisplay = $user['fname']." ".$user['lname']."<BR>".$user['email'];
               if ($user==NULL || $user['userid']==NULL) $userDisplay="view";
               $cellbg="#FFFFFF";
               if (($i%2)==1) $cellbg="#DDDDDD";
            ?>
               <input type="hidden" name="wd[<?php echo ($i+1); ?>]" value="<?php echo $webdata['wd_id']; ?>">
               <input type="hidden" name="wd_row_id[<?php echo ($i+1); ?>]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
               <TR class="small_table">

               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>">
                     <!-- a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>ViewWData.php?admin=1&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank" -->
                     <!--a href="<?php echo $mainurl; ?>?action=viewwd&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>&s_userid=<?php echo $s_userid; ?>&userid=<?php echo $userid; ?>&simpledisplay=<?php echo $simpledisplay; ?>">
                     <?php echo $userDisplay; ?></a>
                     <br-->
                     <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                     <?php echo $userDisplay; ?>
                     </a>
                     
                  </td>
                  <td bgcolor="<?= $cellbg ?>">
                        <textarea rows="2" cols="20" name="c<?php echo $rows[$i]['wd_row_id']; ?>" id="c<?php echo $rows[$i]['wd_row_id']; ?>"><?= convertBack($rows[$i]['comments']) ?></textarea>
                        <?php
                           //$updateComments = "javascript: ";
                           $updateComments = "";
                           $updateComments .= "document.updaterow.wd_row_id.value=".$rows[$i]['wd_row_id'].";";
                           $updateComments .= "document.updaterow.comments.value=document.wd_updaterow.c".$rows[$i]['wd_row_id'].".value;";
                           $updateComments .= "document.updaterow.submit();";
                        ?>
                        <a href="#" onclick="<?php echo $updateComments; ?>">Update</a>
                  </td>
   
                      <?php
                        $openlink = "<a href=\"".$mainurl."?action=wd_listrows&complete=Y&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Open</a>";
                        $closelink = "<a href=\"".$mainurl."?action=wd_listrows&complete=L&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Close</a>";
                        $attnlink = "<a href=\"".$mainurl."?action=wd_listrows&complete=A&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">ATTN!</a>";
                        $speciallink = "<a href=\"".$mainurl."?action=wd_listrows&complete=X&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Special</a>";
                        $seplink = "<br>";
   
                         if (0==strcmp($rows[$i]['complete'],"Y")) {
                              $link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                              $statusbg="#6FFF6F";
                              $status = "Open";
                         } else if (0==strcmp($rows[$i]['complete'],"L")) {
                              $link = $openlink.$seplink.$speciallink.$seplink.$attnlink;
                              //$link = $speciallink.$seplink.$attnlink;
                              $statusbg="#DDDDDD";
                              $status = "Closed";
                         } else if (0==strcmp($rows[$i]['complete'],"N")) {
                              $link = $openlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                              //$link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                              $statusbg="#FFFFFF";
                              $status = "New";
                         } else if (0==strcmp($rows[$i]['complete'],"X")) {
                              $link = $openlink.$seplink.$closelink.$seplink.$attnlink;
                              //$link = $closelink.$seplink.$attnlink;
                              $statusbg="#FDFF5B";
                              $status = "Special";
                         } else if (0==strcmp($rows[$i]['complete'],"A")) {
                              $link = $openlink.$seplink.$closelink.$seplink.$speciallink;
                              //$link = $closelink.$seplink.$speciallink;
                              $statusbg="#FF4348";
                              $status = "Attention!";
                         } else {
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
                        <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>" onclick="return confirm('Are you sure you want to delete this respondant and all his/her responses?')">Delete</a>
                        <?php } ?>
                  </td>
               <?php } else { ?>
                  <td bgcolor="<?php echo $cellbg; ?>">
                     <a href="<?php echo $mainurl; ?>?action=viewwd&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>&s_userid=<?php echo $s_userid; ?>&userid=<?php echo $userid; ?>&simpledisplay=<?php echo $simpledisplay; ?>">
                     View
                     </a>
                  </td>
               <?php } ?>

               <?php
                  for ($j=0; $j<count($headers); $j++) {
                     $columnDescr = $rows[$i][$headers[$j]['field_id']];
                     $descrColor = $cellbg;
                     if (strcmp(strtolower($headers[$j]['label']),"enabled")==0) {
                        $enableLink = "<a href=\"".$mainurl."?answer=Yes&field_id=".$headers[$j]['field_id']."&action=wd_listrows&updateAnswer=1&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Enable</a>";
                        $disableLink = "<a href=\"".$mainurl."?answer=No&field_id=".$headers[$j]['field_id']."&action=wd_listrows&updateAnswer=1&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$webdata['wd_id']."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Disable</a>";
                        if (strcmp(strtoupper($rows[$i][($headers[$j]['field_id'])]),"YES")==0) {
                           $columnDescr = "Yes<br>".$disableLink;
                           $descrColor = "#73D975";
                        } else {
                           $columnDescr = "No<br>".$enableLink;
                           $descrColor = "#D97373";
                        }
                     } else if (0==strcmp($headers[$j]['field_type'],"COLOR")) {
                        $columnDescr = "<div style=\"width:40px;height:40px;overflow:hidden;background-color:".$columnDescr.";border:1px solid #DDDDDD;\"></div>";
                     //} else if (0==strcmp($headers[$j]['field_type'],"MBL_UPL")) {
                     //   $ext = getExtension($columnDescr);
                     //   if(0==strcmp($ext,".jpg") || 0==strcmp($ext,".jpeg") || 0==strcmp($ext,".png") || 0==strcmp($ext,".gif")){
                     //      $columnDescr = "<a href=\"".$columnDescr."\" target=\"_new\"><img src=\"".$columnDescr."\" style=\"width:60px;height:auto;\"></a>";
                     //   }
                     } else if (
                           0==strcmp($headers[$j]['field_type'],"IMAGE") 
                        || 0==strcmp($headers[$j]['field_type'],"MBL_UPL") 
                        || ((0==strcmp($headers[$j]['field_type'],"TEXTAREA") || 0==strcmp($headers[$j]['field_type'],"TEXT")) && isImageFile($columnDescr))
                        ) {
                        if(strlen($columnDescr)>5 && 0!=strcmp(substr($columnDescr,0,4),"http")) $columnDescr = $GLOBALS['srvyURL'].$columnDescr;
                        $ext = getExtension($columnDescr);
                        if(0==strcmp($ext,".jpg") || 0==strcmp($ext,".jpeg") || 0==strcmp($ext,".png") || 0==strcmp($ext,".gif")){
                           $columnDescr = "<a href=\"".$columnDescr."\" target=\"_new\"><img src=\"".$columnDescr."\" style=\"width:60px;height:auto;\"></a>";
                        }
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXT") || 0==strcmp($headers[$j]['field_type'],"DATETIME")) {
                        $columnDescr = "<input class=\"tableinput\" style=\"width:130px;\" type=\"text\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"DATE") || 0==strcmp($headers[$j]['field_type'],"AGE")) {
                        $yr = substr($columnDescr,0,4);
                        $mo = substr($columnDescr,5,2);
                        $da = substr($columnDescr,8,2);
                        $columnDescr = getEmptyDateSelection($da,$mo,$yr,"w".$webdata['wd_id']."date_".$headers[$j]['field_id'],"tableinput","[".($i+1)."]");
                     } else if (0==strcmp($headers[$j]['field_type'],"INT") || 0==strcmp($headers[$j]['field_type'],"DEC") || 0==strcmp($headers[$j]['field_type'],"MONEY")) {
                        $columnDescr = "<input class=\"tableinput\" style=\"width:65px;\" type=\"text\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SNGLCHKBX")) {
                        $chkdy = "";
                        if (0==strcmp(strtoupper($columnDescr),"YES")) $chkdy=" selected=\"selected\"";
                        $chkdn = "";
                        if (0==strcmp(strtoupper($columnDescr),"NO")) $chkdn=" selected=\"selected\"";
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $temp .= "<option value=\"NO\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_1\"".$chkdn.">No</option>\n";
                        $temp .= "<option value=\"YES\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_2\"".$chkdy.">Yes</option>\n";
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXTAREA")) {
                        $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\">".$columnDescr."</textarea>\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SITELIST")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $ctx = new Context();
                        $optionList = $ctx->getSiteOptions();
                        if ($optionList != NULL) {
                          $a = 0;
                          foreach ($optionList as $key => $value) {
                             $selected="";
                             if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                             $a++;
                          }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"DROPDOWN") || 0==strcmp($headers[$j]['field_type'],"RADIO")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $optionList = convertBack($headers[$j]['question']);
                        if ($optionList != NULL) {
                           $optionsvals = separateStringBy($optionList,";");
                           $options = separateStringBy($optionsvals[0],",");
                           $optionsv = separateStringBy($optionsvals[1],",");
                           for ($a=0; $a<count($options); $a++) {
                              $selected="";
                              if($optionsv[$a]==NULL) $optionsv[$a] = $options[$a];
                              if (trim($columnDescr)!=NULL && (strcmp($columnDescr,trim($options[$a]))==0 || strcmp($columnDescr,trim($optionsv[$a]))==0)) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_".($a+1)."\" value=\"".trim($optionsv[$a])."\" ".$selected.">".trim($options[$a])."</option>\n";
                           }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if(0==strcmp($headers[$j]['field_type'],"USERSRCH") || 0==strcmp($headers[$j]['field_type'],"USERS")) {
                        if($columnDescr!=NULL && is_numeric($columnDescr)){
                           $u = $ua->getUser($columnDescr);
                           $columnDescr = "<a href=\"admincontroller.php?action=usermodcloning&userid=".$u['userid']."\" target=\"_new\">";
                           $columnDescr .= $u['userid'].". ".$u['company']." ".$u['fname']." ".$u['lname'];
                           $columnDescr .= "</a>";
                        }
                     } else if (isset($fld_subs[$headers[$j]['field_id']])) {
                        $ans_arr = separateStringBy(convertBack($columnDescr),",",NULL,TRUE);
                        $newans = "";
                        for ($a=0;$a<count($ans_arr);$a++) {
                          if ($a>0) $newans .= ", ";
                          $temp = trim($ans_arr[$a]);
                          if($fld_subs[$headers[$j]['field_id']][$temp]!=NULL) $newans .= convertBack($fld_subs[$headers[$j]['field_id']][$temp]);
                          else if ($temp!=NULL) $newans .= convertBack($temp);
                        }   
                        $columnDescr = $newans;
                     } else {                  
                        if (strlen($columnDescr)>100) $columnDescr = substr($columnDescr,0,97)."...";
                     }
                     
                     // Display formatting for Parent/children
                     if($parentfield!=NULL && $p_fielddisp!=NULL && isset($rows[$i]['structure_depth']) && 0==strcmp($headers[$j]['field_id'],$p_fielddisp)) {
                        $temp = "<div style=\"margin-left:".($rows[$i]['structure_depth'] * 25)."px;\">";
                        $temp .= $columnDescr;
                        $temp .= "</div>";
                        $columnDescr = $temp;
                     }
                     
                     print "<TD bgcolor=\"".$descrColor."\">";
                     print $columnDescr;
                     print "</td>\n";
                  }
               ?>

               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['created'] ?></td>
                  <TD bgcolor="<?= $cellbg ?>"><?= $rows[$i]['lastupdate'] ?></td>
               <?php } ?>

               <td bgcolor="<?= $cellbg ?>">
               <?php if ($simpledisplay!=1) { ?>
                  <input type="checkbox" name="wd_row_id_CB[]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
               <?php } else { ?>
                     <?php if ($showdelete==1) { ?>
                           <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $webdata['wd_id']; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>&simpledisplay=<?php echo $simpledisplay; ?>&showdelete=<?php echo $showdelete; ?>&phpinclude=<?php echo $phpinclude; ?>" onclick="return confirm('Are you sure you want to delete this row?')">Delete</a>
                     <?php } ?>
               <?php } ?>
               </td>
               </tr>

            <?php
            }
            $descrColor="#FFCCCC";
            ?>
            <input type="hidden" name="wd[<?php echo (count($rows)+1); ?>]" value="<?php echo $webdata['wd_id']; ?>">
            <tr>
            <?php if ($simpledisplay!=1) { ?>
             <td bgcolor="<?php echo $descrColor; ?>" colspan="3"><i>Add a new row</i></td>
            <?php } else { ?>
             <td bgcolor="<?php echo $descrColor; ?>"><i>New</i></td>
            <?php } ?>
               <?php
                  
                  //$headers = $wdOBJ->getHeaderFields($webdata['wd_id']);
                  for ($j=0; $j<count($headers); $j++) {
                     $columnDescr="";
                     if (0==strcmp($headers[$j]['field_type'],"TEXT") || 0==strcmp($headers[$j]['field_type'],"DATETIME")) {
                        $columnDescr = "<input class=\"tableinput\" style=\"width:130px;\" type=\"text\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"IMAGE") || 0==strcmp($headers[$j]['field_type'],"MBL_UPL")) {
                        $columnDescr = "<input class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" type=\"file\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\" size=\"10\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"DATE") || 0==strcmp($headers[$j]['field_type'],"AGE")) {
                        $columnDescr = getEmptyDateSelection(NULL,NULL,NULL,"w".$webdata['wd_id']."date_".$headers[$j]['field_id'],"tableinput","[".(count($rows)+1)."]");
                     } else if (0==strcmp($headers[$j]['field_type'],"INT") || 0==strcmp($headers[$j]['field_type'],"DEC") || 0==strcmp($headers[$j]['field_type'],"MONEY")) {
                        $columnDescr = "<input class=\"tableinput\" style=\"width:65px;\" type=\"text\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SNGLCHKBX")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $temp .= "<option value=\"NO\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_1\">No</option>\n";
                        $temp .= "<option value=\"YES\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_2\">Yes</option>\n";
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXTAREA")) {
                        $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\">".$columnDescr."</textarea>\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SITELIST")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $ctx = new Context();
                        $optionList = $ctx->getSiteOptions();
                        if ($optionList != NULL) {
                          $a = 0;
                          foreach ($optionList as $key => $value) {
                             $selected="";
                             if (strcmp($columnDescr,trim($value))==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_".$a."\" value=\"".trim($value)."\" ".$selected.">".$key."</option>\n";
                             $a++;
                          }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"DROPDOWN") || 0==strcmp($headers[$j]['field_type'],"RADIO")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $optionList = convertBack($headers[$j]['question']);
                        if ($optionList != NULL) {
                           $options = separateStringBy($optionList,",");
                           for ($a=0; $a<count($options); $a++) {
                              $selected="";
                              if (strcmp($columnDescr,trim($options[$a]))==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$webdata['wd_id']."a".$headers[$j]['field_id']."_".($a+1)."\" value=\"".trim($options[$a])."\" ".$selected.">".trim($options[$a])."</option>\n";
                           }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     }
                     print "<TD bgcolor=\"".$descrColor."\">";
                     print $columnDescr;
                     print "</td>\n";
                  }
               ?>
            <?php if ($simpledisplay!=1) { ?>
               <td bgcolor="<?php echo $descrColor; ?>" colspan="3"></td>
            <?php } else { ?>
               <td bgcolor="<?php echo $descrColor; ?>"></td>
            <?php } ?>
            </tr>

               <tr>
                  <td bgcolor="<?= $cellbg ?>" align="left" colspan="2"><input type="submit" name="submit" value="Save Values Above"> <input type="submit" name="submit" value="Cancel Changes"></td>
                  <td colspan="<?php echo $totalcolumns-1; ?>" bgcolor="<?= $cellbg ?>" align="right">
                     <?php if ($simpledisplay!=1 && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                        <input type="submit" name="submit" value="Delete selected rows" onClick="return confirm('Are you sure you want to delete these rows permanently?');">
                     <?php } ?>
                  </td>
               </tr>

            </table>
            </tr></td>
            </form></table>

         <BR>
      </td></tr>
   
<?php if ($simpledisplay!=1) { ?>
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),7)) { ?>
      <tr><td>
            <br><hr>
      </td></tr>
      <TR><TD>
            <input id="csvdownloadtbl_cb" type="checkbox" onclick="javascript: expandSection('csvdownloadtbl_cb','csvdownloadsect');" >Display CSV Options
            <table id="csvdownloadsect" cellpadding="5" cellspacing="0" style="display: none;">
             <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
             <tr><td colspan="3"><b>CSV Download:</b></td></tr>
               <form action="<?php echo $mainurl; ?>" method="post" id="csvfields" name="csvfields">
               <input type="hidden" name="action" value="dlwdcsv">
               <input type="hidden" name="wd_id" value="<?php echo $webdata['wd_id']; ?>">
               <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
               <input type="hidden" name="filterStr" value="<?php echo $filterStr ?>">
               <input type="hidden" name="pageLimit" value="<?php echo $pageLimit ?>">
               <input type="hidden" name="pageNum" value="<?php echo $pageNum ?>">
            <tr><td colspan="3"><b>Subject: </b> <input type="text" name="subject" value=""></td></tr>
            <tr>
               <td colspan="3">
               Please select the survey elements you would like in your csv:<br>
               <input type="checkbox" onClick="SetAllCheckBoxes('csvfields', 'qids[]', document.csvfields.checkall.checked);" name="checkall" value="checkall">Select All
   <?php
   
               $questions = $wdOBJ->getAllFieldsSystem($webdata['wd_id']);
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
            </form>
            </table>
      </td></tr>
      <tr><td>
         <div style="padding:10px;font-size:14px;font-family:arial;color:#555555;">
         <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newwdfile" method="POST">
         <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
         <input type="hidden" name="action" value="uploadwdcsv">
         <input type="hidden" name="wd_id" value="<?php echo $webdata['wd_id']; ?>">
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
<?php } ?>



   </table>
<!-- end: jsfadmin/wd_listrows.php 1.00.2.49 -->


<?php if ($simpledisplay!=1) { ?>
   </td></tr>
   </table>
<?php } ?>
