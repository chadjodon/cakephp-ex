
<!-- *chj* jsfadmin/wd_listrows.php -->
<?php
//error_reporting(E_ALL);
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct;

   $wd_id = getParameter("wd_id");
   $s_userid = getParameter("s_userid");
   $pageLimit = getParameter("pageLimit");
   $filterStr = getParameter("filterStr");
   $orderby = getParameter("orderby");
   $pageNum = getParameter("pageNum");
   $phpinclude = getParameter("phpinclude");

   $mainurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php";
   if ($orderby == null) $orderby = "d.wd_row_id DESC";

   $webdata = $wdOBJ->getWebData($wd_id);
   $temp = $wdOBJ->getRows($wd_id, null, null, $filterStr, TRUE, $s_userid);
   $countResults = $temp['results'][0]['count(*)'];
   $headers = $wdOBJ->getHeaderFields($wd_id);

   $results = NULL;
   if ($pageNum==null || $pageNum==0) $pageNum=1;
   if ($pageLimit==NULL) $pageLimit=30;
   $pageStart = $pageLimit*($pageNum - 1);
   $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
   $results = $wdOBJ->getRows($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, $s_userid);
   $rows = $results['results'];
   $totalPages = ceil($countResults/$pageLimit);

   $search_fields = "";
   $search_url = "";
   foreach($results['params'] as $key => $val) {
      $search_fields .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\">\n";
      $search_url .= "&".$key."=".$val;
   }

   $cellbg="lightblue";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;


   //----------------------
   // Paging
   print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
   $pgGroups = 1;
   $curPgGroup = 1;
   if ($totalPages>30) {
      $pgGroups = ceil($totalPages/30);
      $curPgGroup = ceil($pageNum/30);
      print "<TR><td align=\"left\" colspan=\"3\">Page Group: ";
      for ($i=1; $i<=$pgGroups; $i++) {
         $url = $mainurl."?action=wd_listrows&wd_id=".$wd_id."&s_userid=".$s_userid."&filterStr=".$filterStr.$search_url."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".((($i-1)*30)+1);
         if ($curPgGroup == $i) print "<b>".(($i-1)*30+1)."-".(($i-1)*30+30)."</b>";
         else print "<a href=\"".$url."\">".(($i-1)*30+1)."-".(($i-1)*30+30)."</a>";
         print " &nbsp; ";
      }
      print "</td></tr>";
   }
   
   print "<TR><td colspan=\"3\" align=\"left\">";

   if ($pageNum != null && $totalPages != null && $totalPages > 1) {
      $pageTable = "<table align=\"left\"><tr><td>Page: </td>";
      $url = $mainurl."?action=wd_listrows&wd_id=".$wd_id."&s_userid=".$s_userid."&filterStr=".$filterStr.$search_url."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=";
      $lastPage = (($curPgGroup-1)*30+30);
      if ($lastPage > $totalPages) $lastPage = $totalPages;
      for ($i=(($curPgGroup-1)*30+1); $i<=$lastPage; $i++) {
         if ($pageNum == $i) $pageTable .= "<td bgcolor=\"#AAAAAA\"><b>".$i."</b></td>";
         else $pageTable .= "<td><a href=\"".$url.$i."\">".$i."</a></td>";
      }
      $pageTable .= "</tr></table>";
      print $pageTable;
   }

   print "</td></tr></table>";
   // END Paging
   //----------------------
?>
            
            <!------------------------------------------------------->
            <!-- List rows -->
            <div style="border:1px solid #223355;">
            <table cellspacing="1" cellpadding="4" border="0">
            
            <TR class="small_table_header">
               <?php
                  for ($i=0; $i<count($headers); $i++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print "<a href=\"".$mainurl."?action=wd_listrows&s_userid=".$s_userid."&filterStr=".$filterStr."&wd_id=".$wd_id."&orderby=d.".$headers[$i]['field_id']."%20ASC".$neworderby."&pageLimit=".$pageLimit."\">";
                     print $headers[$i]['label'];
                     print "</a>";
                     print "</td>\n";
                  }
               ?>
               <td bgcolor="<?= $cellbg ?>"></td>
            </tr>
            
            <?php
            $totalcolumns = 1 + count($headers);
            if ($rows==NULL || count($rows) == 0) print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"".$totalcolumns."\" ALIGN=\"CENTER\"><font color=\"red\"><b>List is currently empty.</b></font></td></tr>";
            for ($i=0; $i<count($rows); $i++) {
               $cellbg="#FFFFFF";
               if (($i%2)==1) $cellbg="#DDDDDD";
            ?>
               <TR class="small_table">
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
               <td bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?= $orderby ?>&simpledisplay=<?php echo $simpledisplay; ?>&phpinclude=<?php echo $phpinclude; ?>" onclick="return confirm('Are you sure you want to delete this row?')">Delete</a>
               </td>
               </tr>

            <?php
            }
            $descrColor="#FFCCCC";
            ?>
            </table>
            </div>
            <!-- END List rows -->
            <!------------------------------------------------------->

         <BR>
      </td></tr>
   


   </table>
<!-- end: jsfadmin/wd_listrows.php 2.0.6 -->
