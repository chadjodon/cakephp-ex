<?php
//error_reporting(E_ALL);

   print "\n<!-- ***chj*** wd_listsurveyrowsopti.php start: ".date("Y-m-d h:i:s")." -->\n";
   $wdOBJ = new WebsiteData();
   $ua = new UserAcct;
   $dbi = new MySQLAccess();
   $adminusers = NULL;
   $tempusers = $ua->getAdminUsers();
   for ($i=0; $i<count($tempusers); $i++) $adminusers[$tempusers[$i]['userid']]=$tempusers[$i]['email'];
   
   //$compInfo = new CompanyInfo();
   
   $mainurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php";   

   $wd_id = getParameter("wd_id");

   $pageLimit = getParameter("pageLimit");
   if($pageLimit==NULL) $pageLimit=25;
   
   $filterStr = getParameter("filterStr");
   $field5 = getParameter("field5");
   $orderby = getParameter("orderby");
   if ($orderby == null) $orderby = "d.wd_row_id DESC";

   $webdata = $wdOBJ->getWebData($wd_id);
   //$temp = $wdOBJ->getRowsSurveyOrgAdmin ($wd_id, null, null, $filterStr, TRUE,(getParameter("refresh")==1));
   $temp = $wdOBJ->getRowsSurveyAdmin ($wd_id, null, null, $filterStr, TRUE,(getParameter("refresh")==1), $field5);
   print "\n<!-- ***chj*** after count: ".date("Y-m-d h:i:s")." sql: ".$temp['sql']."-->\n";
   $countResults = $temp['results'][0]['count(*)'];

   $params = array();
   $display = array();
   $results = array();
   if ($pageLimit==null) {
      $limitStmnt=null;
      //$results = $wdOBJ->getRowsSurveyOrgAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $orgParams);
      $results = $wdOBJ->getRowsSurveyAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $field5);
      $params = $results['params'];
      $display = $results['display'];
      $rows = $results['results'];
      $pageNum=null;
      $totalPages=null;
   } else {
      $pageNum = getParameter("pageNum");
      if ($pageNum==null || $pageNum==0) $pageNum=1;
      if ($pageLimit<10) $pageLimit=30;
      $pageStart = $pageLimit*($pageNum - 1);
      $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
      //$results = $wdOBJ->getRowsSurveyOrgAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $orgParams);
      $results = $wdOBJ->getRowsSurveyAdmin($wd_id, $orderby, $limitStmnt, $filterStr, FALSE, FALSE, $field5);
      $params = $results['params'];
      $display = $results['display'];
      $rows = $results['results'];
      $totalPages = ceil($countResults/$pageLimit);
   }
   print "\n<!-- ***chj*** after rows: ".date("Y-m-d h:i:s")." sql: ".$results['sql']." count: ".count($results['results'])." -->\n";

   $param_hiddenfields = "";
   $param_urlfields = "";
   foreach($params as $k=>$v) {
      $k = trim($k);
      $v = trim($v);
      if($k!=NULL && $v!=NULL) {
         $param_hiddenfields .= "\n<input type=\"hidden\" name=\"".$k."\" value=\"".$v."\">\n";
         $param_urlfields .= "&".$k."=".rawurlencode($v);
      }
   }
   
   $cellbg="#FFFFFF";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;
?>

               <form id="updaterow" name="updaterow" action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="action" value="wd_updaterow">
               <input type="hidden" name="updatecomments" value="1">
               <input type="hidden" name="filterStr" value="<?php echo $filterStr; ?>">
               <input type="hidden" name="field5" value="<?php echo $field5; ?>">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
               <input type="hidden" name="pageLimit" value="<?php echo $pageLimit; ?>">
               <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>">
               <input type="hidden" name="wd_row_id" value="">
               <input type="hidden" name="comments" value="">
               <?php echo $param_hiddenfields; ?>
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
            <span class="button01"><a href="<?php echo $mainurl; ?>?action=webdata&wd_id=<?php echo $wd_id; ?>">Edit data structure</a> </span>
             &nbsp;&nbsp;&nbsp;
            <br><br>
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <TR>
               <FORM ACTION="form">
               <TD align="left">
                  <?php print $countResults; ?> results, 
                  view  
                  <select name="pageLimit" onChange="window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;">
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?><?php echo $param_urlfields; ?>" <?php if ($pageLimit==null || $pageLimit=="") print "SELECTED" ?>>All</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?>&pageLimit=10<?php echo $param_urlfields; ?>" <?php if ($pageLimit==10) print "SELECTED" ?>>10</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?>&pageLimit=30<?php echo $param_urlfields; ?>" <?php if ($pageLimit==25) print "SELECTED" ?>>25</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?>&pageLimit=30<?php echo $param_urlfields; ?>" <?php if ($pageLimit==30) print "SELECTED" ?>>30</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?>&pageLimit=50<?php echo $param_urlfields; ?>" <?php if ($pageLimit==50) print "SELECTED" ?>>50</option>
                  <option value="<?php echo $mainurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&filterStr=<?php echo $filterStr; ?>&orderby=<?php echo $orderby; ?>&pageLimit=100<?php echo $param_urlfields; ?>" <?php if ($pageLimit==100) print "SELECTED" ?>>100</option>
                  </select>
                  at a time.
               </td>
               </form>
               
               <FORM ACTION="form">
               <TD align="left">
                  Admin: 
                  <select name="field5" onChange="window.location.href=this.form.field5.options[this.form.field5.selectedIndex].value;">
                  <?php
                     print "\n<option value=\"".$mainurl."?action=wd_listrows&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit.$param_urlfields."\"></option>";                  
                     for ($i=0; $i<count($tempusers); $i++) {
                        print "\n<option value=\"".$mainurl."?action=wd_listrows&wd_id=".$wd_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&field5=".$tempusers[$i]['userid'].$param_urlfields."\"";
                        if($field5!=NULL && $field5==$tempusers[$i]['userid']) print " SELECTED";
                        print ">";
                        print substr($tempusers[$i]['email'],0,8);
                        print "</option>";
                     }
                  ?>
                  </select>
               </td>
               </form>
               
               
               <form action="<?php echo $mainurl; ?>" method="POST">
               <input type="hidden" name="pageLimit" value="<?php echo $pageLimit; ?>">
               <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <input type="hidden" name="field5" value="<?php echo $field5; ?>">
               <input type="hidden" name="action" value="wd_listrows">
               <?php echo $param_hiddenfields; ?>
               <td align="left">
                  &nbsp;&nbsp;Search:<input type="text" name="filterStr" value="<?php echo getParameter("filterStr"); ?>" size="10">
                  <input type="submit" name="go" value="go">
               </td>
               </form>
               
               <form action="<?php echo $mainurl; ?>?searchuri=<?php echo rawurlencode($param_urlfields); ?>" method="POST">
               <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
               <!-- input type="hidden" name="action" value="wd_search" -->
               <input type="hidden" name="action" value="wd_search2">
               <input type="hidden" name="filterStr" value="<?php echo $filterStr; ?>">
               <td align="left">
                  <input type="submit" name="go" value="Advanced Search">
               </td>
               </form>
               <!--td>
               <a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&orderby=<?php echo $orderby; ?>&pageLimit=<?php echo $pageLimit; ?>&refresh=1">Refresh</a>
               </td-->
            </tr><tr>
               <td colspan="3" style="font-size:8px;font-family:arial;color:#545454;">
                 <?php
                    if($params!=NULL && count($params)>0) {
                       print "Filters: ";
                       foreach($params as $k => $v) {
                          print $display[$k]." [".$v."] ";
                       }
                    }
                 ?>
               </td>
               <td>
                  <div onclick="location.href='<?php echo $marinurl; ?>?action=wd_listrows&wd_id=<?php echo $wd_id; ?>&refresh=1';" style="margin:3px 10px 3px 10px;width:55px;padding:4px;text-align:center;border:1px solid #111111;border-radius:3px;font-size:8px;font-family:arial;cursor:pointer;">Clear Search</div>
               </td>

            </tr><tr>
               <td colspan="4" align="right">

<?php
                  print "\n<!-- ***chj*** print pages: ".date("Y-m-d h:i:s")." -->\n";
                  if ($pageNum != null && $totalPages != null && $totalPages > 1) {
                     $pageTable = "<table align=\"right\"><tr><td>Page: </td>";
                     $url = $mainurl."?action=wd_listrows&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit.$param_urlfields."&pageNum=";
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
            <form name="wd_updaterow" id="wd_updaterow" action="<?php echo $mainurl; ?>" method="POST">
            <input type="hidden" name="action" value="wd_updaterow">
            <input type="hidden" name="filterStr" value="<?php echo $filterStr; ?>">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <input type="hidden" name="field5" value="<?php echo $field5; ?>">
            <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
            <input type="hidden" name="pageLimit" value="<?php echo $pageLimit; ?>">
            <input type="hidden" name="pageNum" value="<?php echo $pageNum; ?>">
            <?php echo $param_hiddenfields; ?>
            <TR><TD>
            <table width="100%" cellspacing="1" cellpadding="4" border="0">
            
            <?php
            print "\n<!-- ***chj*** about to print rows: ".date("Y-m-d h:i:s")." -->\n";
            
            //print "\n<!-- ****chj*** Rows: \n";
            //print_r($rows);
            //print "\n\n-->\n";
            
            $totalcolumns = 10 + count($headers);
            
            $showcontact = FALSE;
            if(count($rows)>0 && count($rows)<101) {
               print "\n<!-- ***chj*** getting contact info for companies: ".date("Y-m-d h:i:s")." -->\n";
               $contactinfo = array();
               $totalcolumns++;
               $showcontact = TRUE;
               $lookuporg = TRUE;
               $lookupadmin = TRUE;
               $uids = "";
               for ($i=0; $i<count($rows); $i++) {
                  if($i>0) $uids .= ", ";
                  if($rows[$i]['userid']!=NULL) $uids .= $rows[$i]['userid'];
                  else $uids .= "-1";
                  
                  // initialize contactinfo object for this record
                  $contactinfo[$rows[$i]['userid']] = array();
                  
                  // check to see if we already have admin data for this record
                  if($rows[$i]['userid']!=NULL && $rows[$i]['email']!=NULL) {
                     $contactinfo[$rows[$i]['userid']]['email'] = $rows[$i]['email'];
                     $contactinfo[$rows[$i]['userid']]['fname'] = $rows[$i]['fname'];
                     $contactinfo[$rows[$i]['userid']]['lname'] = $rows[$i]['lname'];
                     //$contactinfo[$orgs[$i]['userid']]['field1'] = $rows[$i]['field1'];
                     $lookupadmin = FALSE;
                     //print "\n<!-- ***chj*** found admin -->\n";
                  }
                  
                  // check to see if we already have org data for this record
                  if($rows[$i]['userid']!=NULL && $rows[$i]['company']!=NULL) {
                     $contactinfo[$rows[$i]['userid']]['company'] = $rows[$i]['company'];
                     $contactinfo[$rows[$i]['userid']]['state'] = $rows[$i]['state'];
                     $contactinfo[$rows[$i]['userid']]['website'] = $rows[$i]['website'];
                     $contactinfo[$rows[$i]['userid']]['q240'] = $rows[$i]['q240'];
                     $contactinfo[$rows[$i]['userid']]['field1'] = $rows[$i]['field1'];
                     $contactinfo[$rows[$i]['userid']]['field5'] = $rows[$i]['field5'];
                     $lookuporg = FALSE;
                     //print "\n<!-- ***chj*** found org -->\n";
                  }
               }
               
               if($lookupadmin) {
                  $contactquery = "SELECT u.fname, u.lname, u.email, u.phonenum, r.userid, r.reluserid FROM useracct u, userrel r WHERE u.userid=r.reluserid AND r.userid IN (".$uids.") AND r.rel_type='SRVYADMIN';";
                  $contacts = $dbi->queryGetResults($contactquery);
                  for($i=0;$i<count($contacts);$i++){
                     $contactinfo[$contacts[$i]['userid']]['fname'] = $contacts[$i]['fname'];
                     $contactinfo[$contacts[$i]['userid']]['lname'] = $contacts[$i]['lname'];
                     $contactinfo[$contacts[$i]['userid']]['email'] = $contacts[$i]['email'];
                     $contactinfo[$contacts[$i]['userid']]['phonenum'] = $contacts[$i]['phonenum'];
                  }
               }
               
               if($lookuporg) {
                  $contactquery = "SELECT u.userid, u.company, u.state, u.website, u.field1, u.field5, u.field6, w.q240 FROM useracct u LEFT OUTER JOIN wd_53 w on u.userid=w.userid WHERE u.userid IN (".$uids.");";
                  $orgs = $dbi->queryGetResults($contactquery);
                  print "\n<!-- ***chj*** got user data for rows: ".date("Y-m-d h:i:s")." query: ".$contactquery." -->\n";
                  print "\n<!-- ***chj*** data: ".date("Y-m-d h:i:s")."\n";
                  print_r($orgs);
                  print "\n-->\n";
                  for($i=0;$i<count($orgs);$i++){
                     $contactinfo[$orgs[$i]['userid']]['company'] = $orgs[$i]['company'];
                     $contactinfo[$orgs[$i]['userid']]['website'] = $orgs[$i]['website'];
                     $contactinfo[$orgs[$i]['userid']]['state'] = $orgs[$i]['state'];
                     $contactinfo[$orgs[$i]['userid']]['field1'] = $orgs[$i]['field1'];
                     $contactinfo[$orgs[$i]['userid']]['field5'] = $orgs[$i]['field5'];
                     $contactinfo[$orgs[$i]['userid']]['q240'] = $orgs[$i]['q240'];
                  }
               }
               
            }
            ?>
            

            <TR class="small_table_header">
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=c.company%20ASC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">Company</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=c.state%20ASC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">State</a></TD>
               
               <?php if($showcontact) { ?>
               <TD bgcolor="<?= $cellbg ?>">Contact</TD>
               <?php } ?>
               
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=c.field5%20DESC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">Admin Responsible</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Survey Notes</td>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=d.datesent%20ASC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">Last Sent</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=d.lastupdate%20ASC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">Last Change</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $mainurl; ?>?action=wd_listrows&filterStr=<?php echo $filterStr; ?>&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&orderby=d.complete%20ASC<?= $neworderby ?>&pageLimit=<?php echo $pageLimit; ?><?php echo $param_urlfields; ?>">Status</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Security access</td>
               <?php
                  $headers = $wdOBJ->getHeaderFields($wd_id);
                  for ($i=0; $i<count($headers); $i++) {
                     print "<TD bgcolor=\"".$cellbg."\">";
                     print "<a href=\"".$mainurl."?action=wd_listrows&filterStr=".$filterStr."&wd_id=".$wd_id."&field5=".$field5."&orderby=d.".$headers[$i]['field_id']."%20ASC".$neworderby."&pageLimit=".$pageLimit.$param_urlfields."\">";
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
               $companyDisplay = $contactinfo[$rows[$i]['userid']]['company']."<BR>".$contactinfo[$rows[$i]['userid']]['website'];
               if ($contactinfo[$rows[$i]['userid']]['company']==NULL) $companyDisplay="Responder ".$rows[$i]['wd_row_id'];
            ?>
               <TR class="small_table">
               <TD bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  <?php echo $companyDisplay; ?></a>
               
                  <!-- a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>ViewWData.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  <?php echo $companyDisplay; ?></a><br>
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewWDataJSON.php?admin=1&wd_id=<?php echo $wd_id; ?>&wd_row_id=<?php echo $rows[$i]['wd_row_id']; ?>" target="_blank">
                  [Alt]</a -->
                  
               </td>
               <TD bgcolor="<?= $cellbg ?>"><?php echo $contactinfo[$rows[$i]['userid']]['state']; ?></td>

               <?php 
               if($showcontact) {
                  print "<TD bgcolor=\"".$cellbg."\">";
                  if(isset($contactinfo[$rows[$i]['userid']])) {
                     print $contactinfo[$rows[$i]['userid']]['fname']." ".$contactinfo[$rows[$i]['userid']]['lname']."<br>";
                     print $contactinfo[$rows[$i]['userid']]['email']."<br>";
                     print $contactinfo[$rows[$i]['userid']]['phonenum'];
                  }
                  print "</TD>\n";
               }
               ?>               
               
               <TD bgcolor="<?= $cellbg ?>"><?php echo $adminusers[$contactinfo[$rows[$i]['userid']]['field5']]; ?></td>

               <td bgcolor="<?= $cellbg ?>">
                     <textarea rows="2" cols="20" name="c<?php echo $rows[$i]['wd_row_id']; ?>" id="urc<?php echo $rows[$i]['wd_row_id']; ?>"><?php echo convertBack($rows[$i]['comments']); ?></textarea>
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
                     $newlink     = "<a href=\"".$mainurl."?action=wd_listrows&complete=N&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum.$param_urlfields."\">New</a>";
                     $openlink    = "<a href=\"".$mainurl."?action=wd_listrows&complete=Y&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum.$param_urlfields."\">Open</a>";
                     $closelink   = "<a href=\"".$mainurl."?action=wd_listrows&complete=L&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum.$param_urlfields."\">Close</a>";
                     $attnlink    = "<a href=\"".$mainurl."?action=wd_listrows&complete=A&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum.$param_urlfields."\">ATTN!</a>";
                     $speciallink = "<a href=\"".$mainurl."?action=wd_listrows&complete=X&wd_row_id=".$rows[$i]['wd_row_id']."&wd_id=".$wd_id."&field5=".$field5."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum.$param_urlfields."\">Special</a>";
                     $seplink = "<br>";

                      if (0==strcmp($rows[$i]['complete'],"Y")) {
                           $link = $newlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $statusbg="#6FFF6F";
                           $status = "Open";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"L")) {
                           $link = $newlink.$seplink.$openlink.$seplink.$speciallink.$seplink.$attnlink;
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
                           $link = $newlink.$seplink.$openlink.$seplink.$closelink.$seplink.$attnlink;
                           //$link = $closelink.$seplink.$attnlink;
                           $statusbg="#FDFF5B";
                           $status = "Special";
                      }
                      else if (0==strcmp($rows[$i]['complete'],"A")) {
                           $link = $newlink.$seplink.$openlink.$seplink.$closelink.$seplink.$speciallink;
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
                     <a href="<?php echo $mainurl; ?>?action=wd_updaterow&wd_id=<?php echo $wd_id; ?>&field5=<?php echo $field5; ?>&wd_row_id=<?= $rows[$i]['wd_row_id'] ?>&delete=1&orderby=<?php echo $orderby; ?><?php echo $param_urlfields; ?>" onclick="return confirm('Are you sure you want to delete this respondant and all his/her responses?')">Delete</a>
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

               <?php if (0==strcmp(strtolower($contactinfo[$rows[$i]['userid']]['q240']),"yes")) { ?>
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
                     <input type="submit" name="submit" value="Send email to selected rows" onClick="return confirm('Are you sure you want to email these rows?');">
                     <button onclick="if(confirm('Are you sure you would like to send emails to every entry on every page above?')) window.open('<?php echo $mainurl; ?>?action=wd_emailrows&wd_id=<?php echo $webdata['wd_id']; ?>&sql=<?php echo urlencode($results['rawsql']); ?>');return false;">Send email to all</button>

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
            <input type="hidden" name="action" value="wd_listrows">
            <input type="hidden" name="subaction" value="dlwdcsv">
            <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
            <input type="hidden" name="field5" value="<?php echo $field5; ?>">
            <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
            <input type="hidden" name="filterStr" value="<?php echo $filterStr ?>">
            <input type="hidden" name="pageLimit" value="<?php echo $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?php echo $pageNum ?>">
            <?php echo $param_hiddenfields; ?>
            <table id="csvdownloadsect" cellpadding="5" cellspacing="0" style="display: none;">
             <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
             <tr><td colspan="3"><b>CSV Download:</b></td></tr>
            <tr><td colspan="3"><b>Subject: </b> <input type="text" name="subject" value=""></td></tr>
            <tr>
               <td colspan="3">
               <div id="wdcsvfieldoptions_hide" style="color:blue;font-size:10px;font-weight:bold;cursor:pointer;" onclick="jQuery('#wdcsvfieldoptions').show();jQuery('#wdcsvfieldoptions_hide').hide();jQuery('#wdcsvfieldoptions_show').show();">Show fields</div>
               <div id="wdcsvfieldoptions_show" style="color:blue;font-size:10px;font-weight:bold;cursor:pointer;display:none;" onclick="jQuery('#wdcsvfieldoptions').hide();jQuery('#wdcsvfieldoptions_hide').show();jQuery('#wdcsvfieldoptions_show').hide();">Hide fields</div>
               <div id="wdcsvfieldoptions" style="display:none;">
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
               </div>
               <div>
                 Repeat:
                 <select name="resched">
                 <option value="never">Only run once</option>
                 <option value="weekly">Run weekly</option>
                 <option value="monthly">Run monthly</option>
                 </select>
               </div>
                  <hr><input type="submit" name="submit" value="Download CSV">
               </td>
            </tr>
            </table>
            </form>
      </td></tr>
      <tr><td>
         <div style="padding:10px;font-size:14px;font-family:arial;color:#555555;">
         <form enctype="multipart/form-data" action="<?php echo $mainurl; ?>" name="newwdfile" method="POST">
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
