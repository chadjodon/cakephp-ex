<!-- jsfadmin/wd_listdatarows.php -->
<?php
//error_reporting(E_ALL);
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct;

   $wd_id = getParameter("wd_id");
   $s_userid = getParameter("s_userid");
   $userid = getParameter("userid");
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
   $temp = $wdOBJ->getRows($wd_id, null, null, $filterStr, TRUE, $s_userid);
   $countResults = $temp['results'][0]['count(*)'];
   $headers = $wdOBJ->getHeaderFields($wd_id);

   if ($pageLimit==null) {
      $limitStmnt=null;
      $results = $wdOBJ->getRows($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, $s_userid);
      $rows = $results['results'];
      $pageNum=null;
      $totalPages=null;
   } else {
      if ($pageNum==null || $pageNum==0) $pageNum=1;
      if ($pageLimit==NULL) $pageLimit=30;
      $pageStart = $pageLimit*($pageNum - 1);
      $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
      $results = $wdOBJ->getRows($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, $s_userid);
      $rows = $results['results'];
      $totalPages = ceil($countResults/$pageLimit);
   }

   $cellbg="lightblue";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;

   if ($simpledisplay!=1) {
         //Start non-simpledisplay
?>
      <form id="updaterow" name="updaterow" action="<?php echo $mainurl; ?>" method="POST">
      <input type="hidden" name="action" value="wd_updaterow">
      <input type="hidden" name="updatecomments" value="1">
      <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
      <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
      <input type="hidden" name="orderby" value="<?= $orderby ?>">
      <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
      <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
      <input type="hidden" name="wd_row_id" value="">
      <input type="hidden" name="comments" value="">
      <input type="hidden" name="userid" value="<?= $userid ?>">
      <input type="hidden" name="simpledisplay" value="<?= $simpledisplay ?>">
      <input type="hidden" name="showdelete" value="<?= $showdelete ?>">
      <input type="hidden" name="public" value="<?= $public ?>">
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

<!-- begin: jsfadmin/wd_listdatarows.php 2.0.13 -->

   <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
      <tr>
         <td valign="top">
            <span class="heading"><?php echo $webdata['name']; ?></span>
            <span class="button01"><a href="<?php echo $mainurl; ?>?action=webdata&wd_id=<?= $wd_id ?>">Edit data structure</a> </span>
            <br><br>
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <TR>
               <FORM ACTION="form">
               <TD align="left">
                  <?php print $countResults; ?> results, 
                  view  
                  <select name="pageLimit" onChange="window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;">
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>" <?php if ($pageLimit==null || $pageLimit=="") print "SELECTED" ?>>All</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=10" <?php if ($pageLimit==10) print "SELECTED" ?>>10</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=30" <?php if ($pageLimit==30) print "SELECTED" ?>>30</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=50" <?php if ($pageLimit==50) print "SELECTED" ?>>50</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=100" <?php if ($pageLimit==100) print "SELECTED" ?>>100</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=200" <?php if ($pageLimit==200) print "SELECTED" ?>>200</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?= $wd_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=1000" <?php if ($pageLimit==1000) print "SELECTED" ?>>1000</option>
                  </select>
                  at a time.
               </td>
               </form>
               <form action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
               <input type="hidden" name="action" value="wd_listrows">
               <td align="left">
                  &nbsp;&nbsp;Search:<input type="text" name="filterStr" value="" size="10">
                  <input type="submit" name="go" value="go">
               </td>
               </form>
               <form action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
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
         print "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
      }

      $pgGroups = 1;
      $curPgGroup = 1;
      if ($totalPages>30) {
         $pgGroups = ceil($totalPages/30);
         $curPgGroup = ceil($pageNum/30);
         print "<TR><td align=\"left\" colspan=\"3\">Page Group: ";
         for ($i=1; $i<=$pgGroups; $i++) {
            $url = $mainurl."?action=wd_listrows&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".((($i-1)*30)+1);
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
                     $url = $mainurl."?action=wd_listrows&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=";
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
            <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
            <form enctype="multipart/form-data" name="wd_updaterow" id="wd_updaterow" action="<?php echo $mainurl; ?>" method="POST">
            <input type="hidden" name="action" value="wd_updaterow">
            <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
            <input type="hidden" name="wd_id" value="<?= $wd_id ?>">
            <input type="hidden" name="orderby" value="<?= $orderby ?>">
            <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
            <input type="hidden" name="userid" value="<?= $userid ?>">
            <input type="hidden" name="simpledisplay" value="<?= $simpledisplay ?>">
            <input type="hidden" name="showdelete" value="<?= $showdelete ?>">
            <input type="hidden" name="public" value="<?= $public ?>">
            <input type="hidden" name="page" value="<?= $page ?>">
            <input type="hidden" name="updateForm" value="1">
            <input type="hidden" name="phpinclude" value="<?= $phpinclude ?>">
            <TR><TD>
            <table width="100%" cellspacing="1" cellpadding="4" border="0">
            
            <TR class="small_table_header">
               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>">Contact Info</TD>
                  <TD bgcolor="<?= $cellbg ?>">Notes</TD>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <?php } else { ?>
                  <td bgcolor="<?= $cellbg ?>"></TD>
               <?php } ?>

               <?php
                  for ($i=0; $i<count($headers); $i++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print "<a href=\"".$mainurl."?action=wd_listrows&filterStr=".$filterStr."&wd_id=".$wd_id."&orderby=d.".$headers[$i]['field_id']."%20ASC".$neworderby."&pageLimit=".$pageLimit."\">";
                     print $headers[$i]['label'];
                     print "</a>";
                     print "</td>\n";
                  }
               ?>

               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.created%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Created</a></TD>
                  <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?= $filterStr ?>&wd_id=<?= $wd_id ?>&orderby=d.lastupdate%20DESC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Change</a></TD>
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
               <input type="hidden" name="wd[<?php echo ($i+1); ?>]" value="<?php echo $wd_id; ?>">
               <input type="hidden" name="wd_row_id[<?php echo ($i+1); ?>]" value="<?php echo $rows[$i]['wd_row_id']; ?>">
               <TR class="small_table">

               <?php if ($simpledisplay!=1) { ?>
                  <TD bgcolor="<?= $cellbg ?>">
                     <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>ViewWData.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                     <?php echo $userDisplay; ?></a>
                  </td>
                  <td bgcolor="<?= $cellbg ?>">
                        <textarea rows="2" cols="20" name="c<?php echo $rows[$i]['wd_row_id']; ?>"><?= convertBack($rows[$i]['comments']) ?></textarea>
                        <?php
                           $updateComments = "javascript: ";
                           $updateComments .= "document.wd_updaterow.wd_row_id.value=".$rows[$i]['wd_row_id'].";";
                           $updateComments .= "document.wd_updaterow.comments.value=document.wd_updaterow.c".$rows[$i]['wd_row_id'].".value;";
                           $updateComments .= "document.wd_updaterow.submit();";
                        ?>
                        <a href="<?php echo $updateComments; ?>">Update</a>
                  </td>
   
                      <?php
                        $openlink = "<a href=\"".$mainurl."?action=wd_listrows&complete=Y&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Open</a>";
                        $closelink = "<a href=\"".$mainurl."?action=wd_listrows&complete=L&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Close</a>";
                        $attnlink = "<a href=\"".$mainurl."?action=wd_listrows&complete=A&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">ATTN!</a>";
                        $speciallink = "<a href=\"".$mainurl."?action=wd_listrows&complete=X&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Special</a>";
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
                        <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>" onclick="return confirm('Are you sure you want to delete this respondant and all his/her responses?')">Delete</a>
                        <?php } ?>
                  </td>
               <?php } else { ?>
                  <td bgcolor="<?php echo $cellbg; ?>">
                  </td>
               <?php } ?>

               <?php
                  for ($j=0; $j<count($headers); $j++) {
                     $columnDescr = $rows[$i][$headers[$j]['field_id']];
                     $descrColor = $cellbg;
                     if (strcmp(strtolower($headers[$j]['label']),"enabled")==0) {
                        $enableLink = "<a href=\"".$mainurl."?answer=Yes&field_id=".$headers[$j]['field_id']."&action=wd_listrows&updateAnswer=1&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Enable</a>";
                        $disableLink = "<a href=\"".$mainurl."?answer=No&field_id=".$headers[$j]['field_id']."&action=wd_listrows&updateAnswer=1&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Disable</a>";
                        if (strcmp(strtoupper($rows[$i][($headers[$j]['field_id'])]),"YES")==0) {
                           $columnDescr = "Yes<br>".$disableLink;
                           $descrColor = "#73D975";
                        } else {
                           $columnDescr = "No<br>".$enableLink;
                           $descrColor = "#D97373";
                        }
                     } else if (0==strcmp($headers[$j]['field_type'],"IMAGE")) {
                        $info = getHeightProportion ($GLOBALS['srvyDir'].$columnDescr, "60");
                        $columnDescr = "<a href=\"".$GLOBALS['srvyURL'].$columnDescr."\" target=\"_new\"><img src=\"".$GLOBALS['srvyURL'].$columnDescr."\" width=\"".$info['width']."\" height=\"".$info['height']."\"></a>";
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXT") || 0==strcmp($headers[$j]['field_type'],"DATETIME")) {
                        $columnDescr = "<input class=\"tableinput\" size=\"30\" type=\"text\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"DATE") || 0==strcmp($headers[$j]['field_type'],"AGE")) {
                        $yr = substr($columnDescr,0,4);
                        $mo = substr($columnDescr,5,2);
                        $da = substr($columnDescr,8,2);
                        $columnDescr = getEmptyDateSelection($da,$mo,$yr,"w".$wd_id."date_".$headers[$j]['field_id'],"tableinput","[".($i+1)."]");
                     } else if (0==strcmp($headers[$j]['field_type'],"INT") || 0==strcmp($headers[$j]['field_type'],"DEC") || 0==strcmp($headers[$j]['field_type'],"MONEY")) {
                        $columnDescr = "<input class=\"tableinput\" size=\"3\" type=\"text\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SNGLCHKBX")) {
                        $chkd = "";
                        if (0==strcmp($columnDescr,"YES")) $chkd=" selected=\"selected\"";
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"NO\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_0\">No</option>\n";
                        $temp .= "<option value=\"YES\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_1\"".$chkd.">Yes</option>\n";
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXTAREA")) {
                        $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\">".$columnDescr."</textarea>\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SITELIST")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $ctx = new Context();
                        $optionList = $ctx->getSiteOptions();
                        if ($optionList != NULL) {
                          $a = 0;
                          foreach ($optionList as $key => $value) {
                             $selected="";
                             if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$wd_id."a".$headers[$j]['field_id']."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                             $a++;
                          }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"DROPDOWN") || 0==strcmp($headers[$j]['field_type'],"RADIO")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".($i+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $optionList = convertBack($headers[$j]['question']);
                        if ($optionList != NULL) {
                           $options = separateStringBy($optionList,",");
                           for ($a=0; $a<count($options); $a++) {
                              $selected="";
                              if (strcmp($columnDescr,$options[$a])==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$wd_id."a".$headers[$j]['field_id']."_".($a+1)."\" value=\"".$options[$a]."\" ".$selected.">".$options[$a]."</option>\n";
                           }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else {                  
                        if (strlen($columnDescr)>100) $columnDescr = substr($columnDescr,0,97)."...";
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
                           <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>&simpledisplay=<?php echo $simpledisplay; ?>&showdelete=<?php echo $showdelete; ?>&phpinclude=<?php echo $phpinclude; ?>" onclick="return confirm('Are you sure you want to delete this row?')">Delete</a>
                     <?php } ?>
               <?php } ?>
               </td>
               </tr>

            <?php
            }
            $descrColor="#FFCCCC";
            ?>
            <input type="hidden" name="wd[<?php echo (count($rows)+1); ?>]" value="<?php echo $wd_id; ?>">
            <tr>
            <?php if ($simpledisplay!=1) { ?>
             <td bgcolor="<?php echo $descrColor; ?>" colspan="3"><i>Add a new row</i></td>
            <?php } else { ?>
             <td bgcolor="<?php echo $descrColor; ?>"><i>New</i></td>
            <?php } ?>
               <?php
                  
                  //$headers = $wdOBJ->getHeaderFields($wd_id);
                  for ($j=0; $j<count($headers); $j++) {
                     $columnDescr="";
                     if (0==strcmp($headers[$j]['field_type'],"TEXT") || 0==strcmp($headers[$j]['field_type'],"DATETIME")) {
                        $columnDescr = "<input class=\"tableinput\" size=\"30\" type=\"text\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"IMAGE")) {
                        $columnDescr = "<input class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" type=\"file\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\" size=\"10\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"DATE") || 0==strcmp($headers[$j]['field_type'],"AGE")) {
                        $columnDescr = getEmptyDateSelection(NULL,NULL,NULL,"w".$wd_id."date_".$headers[$j]['field_id'],"tableinput","[".(count($rows)+1)."]");
                     } else if (0==strcmp($headers[$j]['field_type'],"INT") || 0==strcmp($headers[$j]['field_type'],"DEC") || 0==strcmp($headers[$j]['field_type'],"MONEY")) {
                        $columnDescr = "<input class=\"tableinput\" size=\"3\" type=\"text\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" value=\"".$columnDescr."\">\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SNGLCHKBX")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_0\">No</option>\n";
                        $temp .= "<option value=\"YES\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_1\">Yes</option>\n";
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"TEXTAREA")) {
                        $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\">".$columnDescr."</textarea>\n";
                     } else if (0==strcmp($headers[$j]['field_type'],"SITELIST")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $ctx = new Context();
                        $optionList = $ctx->getSiteOptions();
                        if ($optionList != NULL) {
                          $a = 0;
                          foreach ($optionList as $key => $value) {
                             $selected="";
                             if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$wd_id."a".$headers[$j]['field_id']."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                             $a++;
                          }
                        }
                        $temp .= "</select>";
                        $columnDescr = $temp;
                     } else if (0==strcmp($headers[$j]['field_type'],"DROPDOWN") || 0==strcmp($headers[$j]['field_type'],"RADIO")) {
                        $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$headers[$j]['field_id']."[".(count($rows)+1)."]\" id=\"w".$wd_id."a".$headers[$j]['field_id']."\">\n";
                        $temp .= "<option value=\"\" id=\"w".$wd_id."a".$headers[$j]['field_id']."_0\"></option>\n";
                        $optionList = convertBack($headers[$j]['question']);
                        if ($optionList != NULL) {
                           $options = separateStringBy($optionList,",");
                           for ($a=0; $a<count($options); $a++) {
                              $selected="";
                              if (strcmp($columnDescr,$options[$a])==0) $selected="selected=\"selected\"";
                              $temp .= "<option id=\"w".$wd_id."a".$headers[$j]['field_id']."_".($a+1)."\" value=\"".$options[$a]."\" ".$selected.">".$options[$a]."</option>\n";
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
            <tr>
               <form action="<?php echo $mainurl; ?>" method="post" id="csvfields" name="csvfields">
               <input type="hidden" name="action" value="custom">
               <input type="hidden" name="subaction" value="dlcsv">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
               <td colspan="3">
               Please select the survey elements you would like in your csv:<br>
               <input type="checkbox" onClick="SetAllCheckBoxes('csvfields', 'qids[]', document.csvfields.checkall.checked);" name="checkall" value="checkall">Select All
   <?php
   
               $questions = $wdOBJ->getAllFieldsSystem($wd_id);
               $options = null;
               for ($j=0; $j<count($questions); $j++) {
                  if (0!=strcmp($questions[$j]['field_type'],"INFO") && 0!=strcmp($questions[$j]['field_type'],"SPACER")) {
   
                     if (strcmp($questions[$j]['field_type'],"TABLE")==0) {
                        $questionText = $questions[$j]['label'];
                        $temp = separateStringBy(" ".$questionText,";");
                        $headers = separateStringBy(" ".$temp[0],",");
                        $rows = separateStringBy(" ".$temp[1],",");
          
                        $label = "Table ".$questions[$j]['field_id']." (".$headers[1].":".$rows[0]."...)";
                        $options[$label]= $questions[$j]['field_id'];
                     } else {
                        if (strlen($questions[$j]['label']) > 30) {
                           $label = substr($questions[$j]['label'],0,27)."... (".$questions[$j]['field_id'].")";
                        }
                        else {
                           $label = $questions[$j]['label']." (".$questions[$j]['field_id'].")";
                        }
                        $options[$label]= $questions[$j]['field_id'];
                     }
                  }
   
                  if (count($questions)>0) {
                     print getCheckboxList("qids", $options, null);
                  }
               }
   
   ?>               
                  <hr><input type="submit" name="submit" value="Download CSV">
               </td>
               </form>
            </tr>
            </table>
      </td></tr>
      <tr><td>
         <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newwdfile" method="POST">
         <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
         <input type="hidden" name="action" value="uploadwdcsv">
         <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
         <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
         <input type="hidden" name="orderby" value="<?= $orderby ?>">
         <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
         <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
         <input type="hidden" name="simpledisplay" value="<?= $simpledisplay ?>">
         <input type="hidden" name="showdelete" value="<?= $showdelete ?>">
         <input type="hidden" name="public" value="<?= $public ?>">
         &nbsp;&nbsp;CSV File Upload:<br>
         &nbsp;&nbsp;<input name="wdcsv" type="file"><br>
         &nbsp;&nbsp;<input type="submit" name="Load Data" value="Load Data">
         </form>
      </td></tr>   
<?php } ?>
<?php } ?>



   </table>
<!-- end: jsfadmin/wd_listdatarows.php 2.0.13 -->


<?php if ($simpledisplay!=1) { ?>
   </td></tr>
   </table>
<?php } ?>
