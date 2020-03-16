<?php
   $surveyOBJ = new Survey();
   $ua = new UserAcct; 

   $showmenu=TRUE;
   $nomenu=getParameter("nomenu");
   if ($nomenu==1) $showmenu=FALSE;

   $survey_id = getParameter("survey_id");
   $contact_email = getParameter("contact_email");

   $qheaders = $surveyOBJ->getAllQuestions($survey_id);
   $showResequence=FALSE;
   for ($i=0; $i<count($qheaders); $i++) {
      if (0==strcmp(strtolower($qheaders[$i]['label']),"sequence")) {
         $showResequence=TRUE;
         $sequenceQid = $qheaders[$i]['question_id'];
         break;
      }
   }

   $orderby = getParameter("orderby");
   if ($orderby == null && $showResequence) $orderby = $sequenceQid." ASC";
   else if ($orderby == null && !$showResequence) $orderby = "company ASC";

   $survey = $surveyOBJ->getSurvey($survey_id);
   //$countResults = $surveyOBJ->getPersonCount($survey_id);
   $filterStr = getParameter("filterStr");
   $countResults = $surveyOBJ->getPersonCount($survey_id,$filterStr);

   $pageLimit = getParameter("pageLimit");
   if ($pageLimit==null) {
      $limitStmnt=null;
      $emails = $surveyOBJ->getPersons($survey_id, $orderby, $limitStmnt, $filterStr);
      $pageNum=null;
      $totalPages=null;
   }
   else {
      $pageNum = getParameter("pageNum");
      if ($pageNum==null || $pageNum==0) $pageNum=1;

      $pageStart = $pageLimit*($pageNum - 1);
      $limitStmnt = " LIMIT " . $pageStart . "," . $pageLimit;
      $emails = $surveyOBJ->getPersons($survey_id, $orderby, $limitStmnt, $filterStr);
      $totalPages = ceil($countResults/$pageLimit);
   }

   $cellbg="#FFFFFF";
   $neworderby ="";
   if ($orderby != null && $orderby != "") $neworderby = ",%20".$orderby;

   $surveyView = TRUE;   
   $privateSurveyView = FALSE;   
   if ($survey['privatesrvy']>2) $surveyView=FALSE;
   if ($survey['privatesrvy']==1) $privateSurveyView=TRUE;

   if ($survey['privatesrvy']==1) $addedTitle="(Private Survey)";
   else if ($survey['privatesrvy']>2) $addedTitle="(Data records)";
   else $addedTitle="(Public Survey)";
?>

               <form id="updatesurveyperson" name="updatesurveyperson" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
               <input type="hidden" name="action" value="updatesurveyperson">
               <input type="hidden" name="updatecomments" value="1">
               <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
               <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
               <input type="hidden" name="srvy_person_id" value="">
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
<tr align="left" valign="top"><td><?php if($showmenu) include ("datavertmenu.php"); ?></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="1"></td>
<td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="1" width="10"></td>
<td>

<!-- begin: jsfadmin/listemails.php -->

   <table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
      <tr>
         <td valign="top">
            <span class="heading"><?php echo $survey['name']." ".$addedTitle; ?></span>
            <?php if ($showResequence) { ?>
                &nbsp;&nbsp;&nbsp;
               <span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=resequence&survey_id=<?= $survey_id ?>&nomenu=<?= $nomenu ?>">Re-sequence</a> </span>
            <?php } ?>
             &nbsp;&nbsp;&nbsp;
            <span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&survey_id=<?= $survey_id ?>&nomenu=<?= $nomenu ?>">Edit data structure</a> </span>
             &nbsp;&nbsp;&nbsp;
            <span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewsurvey&survey_id=<?php echo $survey['survey_id']; ?>&nomenu=<?= $nomenu ?>"> Add a new Record </a></span>
            <br>

<?php       if ($survey['privatesrvy']==6) { ?>
               <br>RSS subscriber URL: <?php echo $GLOBALS['baseURL'].$GLOBALS['codeFolder']; ?>rssfeed.php?id=<?php echo $survey['survey_id']; ?>
<?php       } ?>

            <?php if ($surveyView && ($survey['status']==NULL || strcmp($survey['status'],"NEW")==0)) { ?>
               Update the email template below to notify private users and grant them access to this form/survey.
            <?php } ?>
            <BR>
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <TR>
               <FORM ACTION="form">
               <TD align="left">
                  <?= $countResults ?> results, 
                  view  
                  <select name="pageLimit" onChange="window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;">
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&survey_id=<?= $survey_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>" <?php if ($pageLimit==null || $pageLimit=="") print "SELECTED" ?>>All</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&survey_id=<?= $survey_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=10" <?php if ($pageLimit==10) print "SELECTED" ?>>10</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&survey_id=<?= $survey_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=25" <?php if ($pageLimit==25) print "SELECTED" ?>>25</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&survey_id=<?= $survey_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=50" <?php if ($pageLimit==50) print "SELECTED" ?>>50</option>
                  <option value="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&survey_id=<?= $survey_id ?>&filterStr=<?= $filterStr ?>&orderby=<?= $orderby ?>&pageLimit=100" <?php if ($pageLimit==100) print "SELECTED" ?>>100</option>
                  </select>
                  at a time.
               </td>
               </form>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
               <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
               <input type="hidden" name="orderby" value="<?= $orderby ?>">
               <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
               <input type="hidden" name="action" value="listemails">
               <td align="left">
                  &nbsp;&nbsp;Search:<input type="text" name="filterStr" value="" size="10">
                  <input type="submit" name="go" value="go">
               </td>
               </form>
               <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
               <input type="hidden" name="action" value="srvysearchresults">
               <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
               <td align="left">
                  <input type="submit" name="go" value="Advanced Search">
               </td>
               </form>
               
               <td align="right">

<?php
                  if ($pageNum != null && $totalPages != null && $totalPages > 1) {
                     $pageTable = "<table align=\"right\"><tr><td>Page: </td>";
                     $url = "admincontroller.php?action=listemails&survey_id=".$survey_id."&nomenu=".$nomenu."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=";
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
            
<?php if ($surveyView || $privateSurveyView) { ?>
            <!-- List emails -->
            <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
            <form name="updaterow" id="updaterow" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
            <input type="hidden" name="action" value="updatesurveyperson">
            <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
            <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
            <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
            <input type="hidden" name="orderby" value="<?= $orderby ?>">
            <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
            <TR><TD>
            <table width="100%" cellspacing="1" cellpadding="4" border="0">
            <TR class="small_table_header">
               <!-- TD bgcolor="<?= $cellbg ?>">&nbsp;</TD -->
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=company%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Company</a></TD>
               <!-- TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=contact_email%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Email</a></TD -->
               <!-- TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=contact_name%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Name</a></TD -->
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=category%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Category</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=contact_name%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Contact Info</a></TD>
               <TD bgcolor="<?= $cellbg ?>">Admin Notes</td>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=datesent%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Sent</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=lastmod%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Last Change</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?= $nomenu ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=state_code%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">State</a></TD>
               <TD colspan="3" bgcolor="<?= $cellbg ?>">Actions</TD>
               <TD bgcolor="<?= $cellbg ?>"><input type="checkbox" onClick="SetAllCheckBoxes('updaterow', 'srvy_person_id[]', document.updaterow.checkall.checked);" name="checkall" value="checkall"></TD>
            </tr>
            
            <?php
            if ($emails==NULL || count($emails) == 0) {
               print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"12\" ALIGN=\"CENTER\"><font color=\"red\"><b>There are no recipients of this survey yet, add a person below.</b></font?</td></tr>";
            }
            for ($i=0; $i<count($emails); $i++) {
            ?>
               <TR class="small_table">

               <?php if ($emails[$i]['company'] != NULL) { ?>
               <TD bgcolor="<?= $cellbg ?>">
                  <a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder'] ?>ViewSurvey.php?admin=1&srvy_person_id=<?= $emails[$i]['srvy_person_id'] ?>" target="_blank">
                  <?= $emails[$i]['company'] ?>
                  </a>
               </td>
               <?php } else { ?>
               <TD bgcolor="<?= $cellbg ?>">
                  <a href="<?= $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder'] ?>ViewSurvey.php?admin=1&srvy_person_id=<?= $emails[$i]['srvy_person_id'] ?>" target="_blank">
                  [Unknown]
                  </a>
               </td>
               <?php } ?>
               <TD bgcolor="<?= $cellbg ?>"><?= $emails[$i]['category'] ?></td>
               <TD bgcolor="<?= $cellbg ?>">
                     <?= $emails[$i]['contact_name'] ?><br>
                     <?= $emails[$i]['contact_email'] ?><br>
                     <?= $emails[$i]['tel'] ?>
               </td>
         
               <td bgcolor="<?= $cellbg ?>">
                     <textarea rows="2" cols="20" name="c<?php echo $emails[$i]['srvy_person_id']; ?>"><?= convertBack($emails[$i]['comments']) ?></textarea>
<?php
                        $updateComments = "javascript: ";
                        $updateComments .= "document.updatesurveyperson.srvy_person_id.value=".$emails[$i]['srvy_person_id'].";";
                        $updateComments .= "document.updatesurveyperson.comments.value=document.updaterow.c".$emails[$i]['srvy_person_id'].".value;";
                        $updateComments .= "document.updatesurveyperson.submit();";
?>
                     <a href="<?php echo $updateComments; ?>">Update</a>
               </td>

               <TD bgcolor="<?= $cellbg ?>"><?= $emails[$i]['datesent'] ?></td>
               <TD bgcolor="<?= $cellbg ?>"><?= $emails[$i]['lastmod'] ?></td>

                   <?php
                      if (0==strcmp($emails[$i]['complete'],"Y")) {
                           $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=L&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Lock</a> &nbsp;|&nbsp;";
                           $link .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=A&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Return</a>";
                           $statusbg="#57FF57";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"L")) {
                           $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=Y&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Unlock</a> &nbsp;|&nbsp;";
                           $link .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=A&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Return</a>";
                           $statusbg="#FF5757";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"N")) {
                           $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=L&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Lock</a>";
                           $statusbg="#DDDDDD";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"X")) {
                           $link = "&nbsp;";
                           $statusbg="#FEFF7D";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"A")) {
                           $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=L&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Lock</a> &nbsp;|&nbsp;";
                           $link .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=Y&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Open</a>";
                           $statusbg="#8DFBFF";
                      }
                      else {
                           $link = "&nbsp;";
                           $statusbg="#FFFFFF";
                      }
                   ?>

               <TD bgcolor="<?= $statusbg ?>">
                   <?php
                      if (0==strcmp($emails[$i]['complete'],"Y")) print "Open";
                      else if (0==strcmp($emails[$i]['complete'],"L")) print "Locked";
                      else if (0==strcmp($emails[$i]['complete'],"N")) print "Sent";
                      else if (0==strcmp($emails[$i]['complete'],"X")) print "Unsent";
                      else if (0==strcmp($emails[$i]['complete'],"A")) print "Returned";

                      print "<BR>";
                      print $surveyOBJ->getAnsweredPercentage($emails[$i]['srvy_person_id']);
                   ?>
               </td>
               <td bgcolor="<?= $cellbg ?>">
                  <?php
                     if ($emails[$i]['state_code'] == null || 0 == strcmp($emails[$i]['state_code'],"BL")) print "*N/A*";
                     else print $emails[$i]['state_code'];
                  ?>
               </td>
               <TD bgcolor="<?= $cellbg ?>">
                   <?= $link ?>
               </td>
               <?php if ($survey['status']!=NULL && strcmp($survey['status'],"NEW")!=0) { ?>
                     <?php if (0==strcmp($emails[$i]['complete'],"X")) { ?>
                       <TD bgcolor="#FFFF99">
                        <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?setstatus=N&action=updatesurveyperson&survey_id=<?= $emails[$i]['survey_id'] ?>&srvy_person_id=<?= $emails[$i]['srvy_person_id'] ?>&resend=1&orderby=<?= $orderby ?>">
                              Send Email
                        </a>
                      </td>
                     <?php } else { ?>
                       <TD bgcolor="<?= $cellbg ?>">
                        <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=updatesurveyperson&survey_id=<?= $emails[$i]['survey_id'] ?>&srvy_person_id=<?= $emails[$i]['srvy_person_id'] ?>&resend=1&orderby=<?= $orderby ?>">
                              Resend Email
                        </a>
                      </td>
                     <?php } ?>
               <?php } else { ?>
                  <TD bgcolor="<?= $cellbg ?>"></td>
               <?php } ?>

                     <?php
                        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { 
                     ?>
                        <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=updatesurveyperson&survey_id=<?= $emails[$i]['survey_id'] ?>&srvy_person_id=<?= $emails[$i]['srvy_person_id'] ?>&delete=1&orderby=<?= $orderby ?>" onclick="return confirm('Are you sure you want to delete this respondant and all his/her responses?')">Del</a></td>
                     <?php } else { ?>
                        <TD bgcolor="<?= $cellbg ?>"></td>
                     <?php } ?>
               <td bgcolor="<?= $cellbg ?>"><input type="checkbox" name="srvy_person_id[]" value="<?php echo $emails[$i]['srvy_person_id']; ?>"></td>
               </tr>

            <?php
            }
            ?>

               <tr>
                  <td colspan="12" bgcolor="<?= $cellbg ?>" align="right">
                     <input type="submit" name="submit" value="Send email to selected rows">

                     <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                        <input type="submit" name="submit" value="Delete selected rows" onClick="return confirm('Are you sure you want to delete these rows permanently?');">
                     <?php } ?>
                  </td>
               </tr>

            </table>
            </tr></td>
            </form></table>

<?php } else { ?>
            <!-- List data in table -->
            <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
            <form name="updaterow" id="updaterow" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
            <input type="hidden" name="action" value="listemails">
            <input type="hidden" name="updateForm" value="1">
            <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
            <input type="hidden" name="filterStr" value="<?= $filterStr ?>">
            <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
            <input type="hidden" name="orderby" value="<?= $orderby ?>">
            <input type="hidden" name="pageLimit" value="<?= $pageLimit ?>">
            <input type="hidden" name="pageNum" value="<?= $pageNum ?>">
            <TR><TD>
            <table width="100%" cellspacing="1" cellpadding="4" border="0">
            <TR class="small_table_header">
               <TD bgcolor="<?= $cellbg ?>">&nbsp;</TD>
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=complete%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Status</a></TD>
               <!--TD bgcolor="<?= $cellbg ?>">Action</TD-->
               <!--TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=contact_email%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Email</a></td-->
               <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=created%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>">Created</a></td>
<?php
          for ($j=0; $j<count($qheaders); $j++) {
            if (0!=strcmp($qheaders[$j]['question_type'],"INFO") && 0!=strcmp($qheaders[$j]['question_type'],"HTML") && 0!=strcmp($qheaders[$j]['question_type'],"SPACER")) {
?>
               <TD bgcolor="<?php echo $cellbg; ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&nomenu=<?php echo $nomenu; ?>&filterStr=<?= $filterStr ?>&survey_id=<?= $survey_id ?>&orderby=<?php echo $qheaders[$j]['question_id']; ?>%20ASC<?= $neworderby ?>&pageLimit=<?= $pageLimit ?>"><?php echo $qheaders[$j]['label']; ?></a></td>
<?php
            }
          }
?>
               <TD bgcolor="<?= $cellbg ?>">Admin Notes</td>
               <TD bgcolor="<?= $cellbg ?>">
                  <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                     <input type="checkbox" onClick="SetAllCheckBoxes('updaterow', 'del_srvy_person_id[]', document.updaterow.checkall.checked);" name="checkall" value="checkall">
                  <?php } ?>
               </TD>
            </tr>
            
            <?php
            $numOfRows = count($qheaders)+5;
            if ($emails==NULL || count($emails) == 0) {
               print "<TR><TD bgcolor=\"".$cellbg."\" colspan=\"".$numOfRows."\" ALIGN=\"left\"><font color=\"red\"><b>No data yet.</b></font?</td></tr>";
            }
            for ($i=0; $i<count($emails); $i++) {
               if (strcmp($cellbg,"#FFFFFF")==0) $cellbg="#DDDDDD";
               else $cellbg="#FFFFFF";
            ?>
               <input type="hidden" name="survey[<?php echo ($i+1); ?>]" value="<?php echo $survey_id; ?>">
               <input type="hidden" name="srvy_person_id[<?php echo ($i+1); ?>]" value="<?php echo $emails[$i]['srvy_person_id']; ?>">

               <TR class="small_table">
               <TD bgcolor="<?php echo $cellbg; ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewsurvey&srvy_person_id=<?php echo $emails[$i]['srvy_person_id']; ?>">View</a>
                  <!--a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>ViewSurvey.php?secure=1&admin=1&srvy_person_id=<?php echo $emails[$i]['srvy_person_id']; ?>" target="_blank">Edit</a-->
               </td>

                   <?php
                     $openlink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=Y&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">OK</a>";
                     $closelink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=L&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Close</a>";
                     $attnlink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=A&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">ATTN!</a>";
                     $speciallink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&nomenu=".$nomenu."&complete=X&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Caution</a>";
                     $seplink = "<br>";

                      //if (0==strcmp($emails[$i]['complete'],"Y")) {
                      if (0==strcmp($emails[$i]['complete'],"Y") || 0==strcmp($emails[$i]['complete'],"N")) {
                           //$link = $closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $link = $speciallink.$seplink.$attnlink;
                           //$link = $closelink.$seplink.$attnlink;
                           $statusbg="#6FFF6F";
                           $status = "OK";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"L")) {
                           //$link = $openlink.$seplink.$speciallink.$seplink.$attnlink;
                           //$link = $openlink.$seplink.$attnlink;
                           $link = $openlink.$seplink.$speciallink.$seplink.$attnlink;
                           $statusbg="#DDDDDD";
                           $status = "Closed";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"N")) {
                           //$link = $openlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $link = $openlink.$seplink.$closelink.$seplink.$attnlink;
                           $statusbg="#FFFFFF";
                           $status = "New";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"X")) {
                           //$link = $openlink.$seplink.$closelink.$seplink.$attnlink;
                           $link = $openlink.$seplink.$attnlink;
                           $statusbg="#FDFF5B";
                           $status = "Caution";
                      }
                      else if (0==strcmp($emails[$i]['complete'],"A")) {
                           //$link = $openlink.$seplink.$closelink.$seplink.$speciallink;
                           //$link = $openlink.$seplink.$closelink;
                           $link = $openlink.$seplink.$speciallink;
                           $statusbg="#FF4348";
                           $status = "Attention!";
                      }
                      else {
                           //$link = $openlink.$seplink.$closelink.$seplink.$speciallink.$seplink.$attnlink;
                           $link = $openlink.$seplink.$closelink;
                           $statusbg="#FFFFFF";
                           $status = "N/A";
                      }
                   ?>

               <TD bgcolor="<?php echo $statusbg; ?>"><?php echo $status; ?><br><?php echo $link; ?></td>
               <!--TD bgcolor="<?php echo $cellbg; ?>"><?php echo $emails[$i]['contact_email']; ?></td-->
               <TD bgcolor="<?php echo $cellbg; ?>"><?php echo $emails[$i]['created']; ?></td>
<?php
         for ($j=0; $j<count($qheaders); $j++) {
            if (0!=strcmp($qheaders[$j]['question_type'],"INFO") && 0!=strcmp($qheaders[$j]['question_type'],"HTML") && 0!=strcmp($qheaders[$j]['question_type'],"SPACER")) {

               $columnDescr = $emails[$i][($qheaders[$j]['question_id'])];
               $descrColor = $cellbg;
               if (strcmp(strtolower($qheaders[$j]['label']),"enabled")==0) {
                  $enableLink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?answer=Yes&question_id=".$qheaders[$j]['question_id']."&action=listemails&updateAnswer=1&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Enable</a>";
                  $disableLink = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?answer=No&question_id=".$qheaders[$j]['question_id']."&action=listemails&updateAnswer=1&srvy_person_id=".$emails[$i]['srvy_person_id']."&survey_id=".$survey_id."&filterStr=".$filterStr."&orderby=".$orderby."&pageLimit=".$pageLimit."&pageNum=".$pageNum."\">Disable</a>";
                  if (strcmp($emails[$i][($qheaders[$j]['question_id'])],"Yes")==0) {
                     $columnDescr = "Yes<br>".$disableLink;
                     $descrColor = "#73D975";
                  } else {
                     $columnDescr = "No<br>".$enableLink;
                     $descrColor = "#D97373";
                  }
               } else if (0==strcmp($qheaders[$j]['question_type'],"IMAGE")) {
                  $info = getHeightProportion ($GLOBALS['srvyDir'].$columnDescr, "60");
                  $columnDescr = "<a href=\"".$GLOBALS['srvyURL'].$columnDescr."\" target=\"_new\"><img src=\"".$GLOBALS['srvyURL'].$columnDescr."\" width=\"".$info['width']."\" height=\"".$info['height']."\"></a>";
               } else if (0==strcmp($qheaders[$j]['question_type'],"TEXT") || 0==strcmp($qheaders[$j]['question_type'],"DATE") || 0==strcmp($qheaders[$j]['question_type'],"AGE") || 0==strcmp($qheaders[$j]['question_type'],"DATETIME")) {
                  $columnDescr = "<input class=\"tableinput\" size=\"30\" type=\"text\" name=\"a".$qheaders[$j]['question_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
               } else if (0==strcmp($qheaders[$j]['question_type'],"INT") || 0==strcmp($qheaders[$j]['question_type'],"DEC") || 0==strcmp($qheaders[$j]['question_type'],"MONEY")) {
                  $columnDescr = "<input class=\"tableinput\" size=\"3\" type=\"text\" name=\"a".$qheaders[$j]['question_id']."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
               } else if (0==strcmp($qheaders[$j]['question_type'],"TEXTAREA")) {
                  $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"a".$qheaders[$j]['question_id']."[".($i+1)."]\">".$columnDescr."</textarea>\n";
               } else if (0==strcmp($qheaders[$j]['question_type'],"SITELIST")) {
                  $temp = "<select class=\"tableinput\" name=\"a".$qheaders[$j]['question_id']."[".($i+1)."]\" id=\"a".$qheaders[$j]['question_id']."\">\n";
                  $ctx = new Context();
                  $optionList = $ctx->getSiteOptions();
                  if ($optionList != NULL) {
                    $a = 0;
                    foreach ($optionList as $key => $value) {
                       $selected="";
                       if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                        $temp .= "<option id=\"a".$qheaders[$j]['question_id']."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                       $a++;
                    }
                  }
                  $temp .= "</select>";
                  $columnDescr = $temp;
               } else if (0==strcmp($qheaders[$j]['question_type'],"DROPDOWN") || 0==strcmp($qheaders[$j]['question_type'],"RADIO")) {
                  $temp = "<select class=\"tableinput\" name=\"a".$qheaders[$j]['question_id']."[".($i+1)."]\" id=\"a".$qheaders[$j]['question_id']."\">\n";
                  $temp .= "<option value=\"\" id=\"a".$qheaders[$j]['question_id']."_0\"></option>\n";
                  $optionList = convertBack($qheaders[$j]['question']);
                  if ($optionList != NULL) {
                     $options = separateStringBy($optionList,",");
                     for ($a=0; $a<count($options); $a++) {
                        $selected="";
print "<!-- option: [".$options[$a]."] and value: [".$columnDescr."] -->\n";
                        if (strcmp(strtolower(trim(convertBack($columnDescr))),strtolower(trim($options[$a])))==0) $selected="selected=\"selected\"";
                        $temp .= "<option id=\"a".$qheaders[$j]['question_id']."_".($a+1)."\" value=\"".$options[$a]."\" ".$selected.">".$options[$a]."</option>\n";
                     }
                  }
                  $temp .= "</select>";
                  $columnDescr = $temp;
               } else {                  
                  if (strlen($columnDescr)>100) $columnDescr = substr($columnDescr,0,97)."...";
               }

?>
               <td bgcolor="<?php echo $descrColor; ?>"><?php echo $columnDescr; ?></td>
<?php
            }
          }
?>

               <!------ admin Notes ------>
               <td bgcolor="<?= $cellbg ?>">
                     <textarea  class="tableinput" rows="2" cols="22" id="comments<?php echo ($i+1); ?>" name="comments[<?php echo ($i+1); ?>]"><?= convertBack($emails[$i]['comments']) ?></textarea>
<?php
                        $updateComments = "javascript: ";
                        $updateComments .= "document.updatesurveyperson.srvy_person_id.value=".$emails[$i]['srvy_person_id'].";";
                        $updateComments .= "document.updatesurveyperson.comments.value=document.getElementById('comments".($i+1)."').value;";
                        $updateComments .= "document.updatesurveyperson.submit();";
?>
                     <!-- a href="<?php echo $updateComments; ?>">Update</a -->
               </td>
               <!------ admin Notes ------>

               <!------ delete option ------>
               <td bgcolor="<?= $cellbg ?>">
                  <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                     <input type="checkbox" name="del_srvy_person_id[]" value="<?php echo $emails[$i]['srvy_person_id']; ?>">
                  <?php } ?>
               </td>
               <!------ delete option ------>

               </tr>
<?php 
         } 
         $cellbg="#FFCCCC"; //for new row change color

?>

               <input type="hidden" name="survey[<?php echo (count($emails)+1); ?>]" value="<?php echo $survey_id; ?>">
               <TR class="small_table">
               <TD  colspan="3" align="left" bgcolor="<?php echo $cellbg; ?>"><i>Enter values to create a new entry</i></td>
<?php
         for ($j=0; $j<count($qheaders); $j++) {
            if (0!=strcmp($qheaders[$j]['question_type'],"INFO") && 0!=strcmp($qheaders[$j]['question_type'],"SPACER")) {
               $columnDescr = "";
               if (0==strcmp($qheaders[$j]['question_type'],"TEXT") || 0==strcmp($qheaders[$j]['question_type'],"DATE") || 0==strcmp($qheaders[$j]['question_type'],"AGE") || 0==strcmp($qheaders[$j]['question_type'],"DATETIME") || 0==strcmp($qheaders[$j]['question_type'],"INT") || 0==strcmp($qheaders[$j]['question_type'],"DEC") || 0==strcmp($qheaders[$j]['question_type'],"MONEY")) {
               //if (0==strcmp($qheaders[$j]['question_type'],"TEXT") || 0==strcmp($qheaders[$j]['question_type'],"INT") || 0==strcmp($qheaders[$j]['question_type'],"DEC") || 0==strcmp($qheaders[$j]['question_type'],"MONEY")) {
                  $columnDescr = "<input class=\"tableinput\" size=\"30\" type=\"text\" name=\"a".$qheaders[$j]['question_id']."[".(count($emails)+1)."]\" value=\"\">\n";
               } else if (0==strcmp($qheaders[$j]['question_type'],"TEXTAREA")) {
                  $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"27\" name=\"a".$qheaders[$j]['question_id']."[".(count($emails)+1)."]\"></textarea>\n";
               } else if (0==strcmp($qheaders[$j]['question_type'],"DROPDOWN") || 0==strcmp($qheaders[$j]['question_type'],"RADIO")) {
                  $temp = "<select class=\"tableinput\" name=\"a".$qheaders[$j]['question_id']."[".(count($emails)+1)."]\" id=\"a".$qheaders[$j]['question_id']."\">\n";
                  $temp .= "<option value=\"\" id=\"a".$qheaders[$j]['question_id']."_0\"></option>\n";
                  $optionList = convertBack($qheaders[$j]['question']);
                  if ($optionList != NULL) {
                     $options = separateStringBy($optionList,",");
                     for ($a=0; $a<count($options); $a++) {
                        $selected="";
                        $temp .= "<option id=\"a".$qheaders[$j]['question_id']."_".($a+1)."\" value=\"".$options[$a]."\" ".$selected.">".$options[$a]."</option>\n";
                     }
                  }
                  $temp .= "</select>";
                  $columnDescr = $temp;
               } // end dropdown option
?>
               <td bgcolor="<?php echo $cellbg; ?>"><?php echo $columnDescr; ?></td>
<?php
            }
          }
?>
               <td bgcolor="<?= $cellbg ?>"></td>
               <TD bgcolor="<?= $cellbg ?>"></td>
               </tr>

            </table>
            </td></tr>
            <tr><td align="left" bgcolor="#FFFFFF">
               <table width="100%" cellpadding="5" cellspacing="0"><tr>
               <td align="left"><input type="submit" name="submit" value="Save Values In Table"> <input type="submit" name="submit" value="Cancel Table Changes"></td>
               <td align="right">
                     <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
                        <input type="submit" name="submit" value="Delete Selected Rows" onClick="return confirm('Are you sure you want to delete these rows permanently?');">
                     <?php } ?>
               </td>
               </tr></table>
            </td></tr>
            </form></table>

<?php } ?>

         <BR>
         <span class="button01">
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewsurvey&survey_id=<?php echo $survey['survey_id']; ?>"> Add a new Record </a>
         </span><br><br>

         <?php if ($surveyView || $privateSurveyView) { ?>
            <BR><BR>
          <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="personSurvey" method="POST">
          <input type="hidden" name="action" value="updatesurveyperson">
          <input type="hidden" name="newcompany" value="1">
          <input type="hidden" name="nomenu" value="<?= $nomenu ?>">
          <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
          <input type="hidden" name="recycler_id" value="<?= $recycler_id ?>">
          <input type="hidden" name="emailList" value="1">
      
          <table border="0" cellspacing="0" cellpadding="0" align="center" width="850">
                  <Tr>
                  <td valign="top" style="padding-left:25px;">


                  <table border="0" cellspacing="0" cellpadding="0" WIDTH="100%" bgcolor="#DDDDDD">
                  <TR><TD VALIGN="TOP" WIDTH="50%">

                  <table border="0" cellspacing="0" cellpadding="0" WIDTH="100%">
         <?php if ($companyDisabled) { ?>
                     <tr>
                        <td class="label">Company:&nbsp;</td>
                        <td>
                          <input type="hidden" name="company" value="<?php echo getParameter('company'); ?>">
                           <?php echo getParameter('company'); ?>
                        </td>
                     </tr>
         <?php } else { ?>
                     <tr>
                        <td class="label">Company:&nbsp;</td>
                        <td>
                          <input type="text" name="company" id="txtCompany" size="25" value="<?php echo getParameter('company'); ?>">
                        </td>
                     </tr>
         <?php } ?>

                     <tr>
                        <td class="label">Address 1:&nbsp;</td>
                        <td>
                           <input type="text" name="address1" value="<?php echo getParameter('address1'); ?>" id="txtAddress1" size="25">
                        </td>
                     </tr>

                     <tr>
                        <td class="label">Address 2:&nbsp;</td>
                        <td><input type="text" name="address2" value="<?php echo getParameter('address2'); ?>" id="txtAddress2" size="25"></td>
                     </tr>

                     <tr>
                        <td class="label">City:&nbsp;</td>
                        <td><input type="text" name="city" value="<?php echo getParameter('city'); ?>" id="txtCity" size="25">
                                                </td>
                     </tr>
                     <tr>

                        <td class="label">State (or Province):&nbsp;</td>
                        <td>
                              <?php getStateOptions(getParameter('state_code'),"state_code"); ?>
                        </td>
                     </tr>

                     <tr>
                        <td class="label">Zip:&nbsp;</td>
                        <td>
                           <input type="text" name="zipcode" value="<?php echo getParameter('zipcode'); ?>" id="txtZip" size="25">
                        </td>
                     </tr>

                     <tr>
                        <td class="label">Web Site:&nbsp;</td>
                        <td>
                           <input type="text" name="website" value="<?php echo getParameter('website'); ?>" id="txtWebSite" size="25">
                        </td>
                     </tr>
                  </table>

                  </TD><TD VALIGN="TOP" WIDTH="50%">

                  <table border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td class="label">Contact:&nbsp;</td>
                        <td>
                           <input type="text" name="contact_name" value="<?php echo getParameter('contact_name'); ?>" id="txtContact" size="25">
                        </td>
                     </tr>
                     <tr>
                        <td class="label">Title:&nbsp;</td>
                        <td>
                           <input type="text" name="contact_title" value="<?php echo getParameter('contact_title'); ?>" id="txtContactTitle" size="25">
                        </td>
                     </tr>
                     <tr>
                        <td class="label">Contact Email:&nbsp;</td>
                        <td>
                           <input type="text" name="contact_email" value="<?php echo getParameter('contact_email'); ?>" id="txtContactEmail" size="25">
                        </td>
                     </tr>
                     <tr>
                        <td class="label">Phone Number:&nbsp;</td>
                        <td><input type="text" name="tel" value="<?php echo getParameter('tel'); ?>" id="txtPhone" size="25"></td>
                     </tr>

                     <tr>
                        <td class="label">Fax Number:&nbsp;</td>
                        <td><input type="text" name="fax" value="<?php echo getParameter('fax'); ?>" id="txtFax" size="25"></td>
                     </tr>

                     <tr>
                        <td class="label">Company Category:&nbsp;</td>
                        <td><input type="text" name="category" value="<?php echo getParameter('category'); ?>" id="txtCategory" size="25"></td>
                     </tr>
                     <tr>
                        <td class="label" colspan="2"><input type="submit" name="AddCompany" value="Add Company Only"></td>
                     </tr>

                     <tr>
                        <td class="label" colspan="2"><input type="submit" name="AddCompany" value="Add Company And Send Email"></td>
                     </tr>

                  </TABLE>

                  </td></tr>
                  </table>

         </form>
         <?php } ?>

      </td></tr>
   
<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),7)) { ?>
   <tr><td>
         <br><hr>
   </td></tr>
   <TR><TD>
         <input id="csvdownloadtbl_cb" type="checkbox" onclick="javascript: expandSection('csvdownloadtbl_cb','csvdownloadsect');" >Display CSV Options
         <table id="csvdownloadsect" cellpadding="5" cellspacing="0" style="display: none;">
          <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="15"></td></tr>
          <tr><td colspan="3"><b>CSV Upload:</b></td></tr>
          <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
          <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
          <input type="hidden" name="action" value="uploadSurveyCSV">
          <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
          <TR>
              <TD bgcolor="lightgrey">Upload CSV file with data:&nbsp;&nbsp;&nbsp;</TD>
              <TD><input name="userfile" type="file">&nbsp;&nbsp;&nbsp;</TD>
              <td><input type="submit" value="Upload Your File"></td>
          </tr>
          </form>


          <tr><td colspan="3"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="20"></td></tr>
          <tr><td colspan="3"><b>CSV Download:</b></td></tr>
         <tr>
            <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post" id="csvfields" name="csvfields">
            <input type="hidden" name="action" value="dlcsv">
            <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
            <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
            <td colspan="3">
            Please select the survey elements you would like in your csv:<br>
            <input type="checkbox" onClick="SetAllCheckBoxes('csvfields', 'qids[]', document.csvfields.checkall.checked);" name="checkall" value="checkall">Select All
            <hr>
            Header Info<br>
            
<?php
            $options['Organization'] = "company";
            $options['Address 1'] = "address1";
            $options['Address 2'] = "address2";
            $options['City'] = "city";
            $options['State'] = "state_code";
            $options['Zip Code'] = "zipcode";
            $options['Website'] = "website";
            $options['Contact'] = "contact_name";
            $options['Title'] = "contact_title";
            $options['Email'] = "contact_email";
            $options['Telephone'] = "tel";
            $options['Fax'] = "fax";
            $options['Biz Category'] = "category";
            $options['Remove Me'] = "removeme";
            $options['Admin Notes'] = "comments";
            print getCheckboxList("qids", $options, null);

            $sections = $surveyOBJ->getQuestionSections($survey_id);
            for ($i=0; $i<count($sections); $i++) {
               $s = $sections[$i];
               $questions = $surveyOBJ->getQuestions($survey_id, $s['section']);
               $options = null;
               //$questions = $surveyOBJ->getAllQuestions($survey_id);
               for ($j=0; $j<count($questions); $j++) {
                  if (0!=strcmp($questions[$j]['question_type'],"INFO") && 0!=strcmp($questions[$j]['question_type'],"SPACER")) {
   
                     if (strcmp($questions[$j]['question_type'],"TABLE")==0) {
                        $questionText = $questions[$j]['label'];
                        $temp = separateStringBy(" ".$questionText,";");
                        $headers = separateStringBy(" ".$temp[0],",");
                        $rows = separateStringBy(" ".$temp[1],",");
          
                        $label = "Table ".$questions[$j]['question_id']." (".$headers[1].":".$rows[0]."...)";
                        $options[$label]= $questions[$j]['question_id'];
                     }
                     else {
                        if (strlen($questions[$j]['label']) > 30) {
                           $label = substr($questions[$j]['label'],0,27)."... (".$questions[$j]['question_id'].")";
                        }
                        else {
                           $label = $questions[$j]['label']." (".$questions[$j]['question_id'].")";
                        }
                        $options[$label]= $questions[$j]['question_id'];
                     }
                  }
               }

               if (count($questions)>0) {
                  print "<hr>Section: ".$s['label']."<br>";
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
<?php } ?>
   </table>

<?php 
      if ($surveyView) {
         $shortcuts = $ss->getAllShortcuts(5);
         $options = array();
         for ($i=0; $i<count($shortcuts); $i++) {
            $options[$shortcuts[$i]['title']] = $shortcuts[$i]['filename'];
         }
         $shortcuts = $ss->getAllShortcuts(6);
         for ($i=0; $i<count($shortcuts); $i++) {
            $options[$shortcuts[$i]['title']] = $shortcuts[$i]['filename'];
         }
         $extra = " onchange=\"showcmstxtonly('".$GLOBALS['baseURL'].$GLOBALS['codeFolder']."ajaxcontroller.php?action=cmstextonly&shortname='+this.value)\"";
         $sel = getOptionList("emailbody_tpl", $options, NULL, TRUE, $extra);


?>
<script type="text/javascript" src="<?php echo $GLOBALS['baseURL'].$GLOBALS['codeFolder']; ?>getcms.js"></script>
<table border="0" cellspacing="0" cellpadding="0" align="center" width="850">
   <TR><TD>
      <BR><HR>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
            <input type="hidden" name="action" value="updatesurveyperson">
            <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
            <table border="0" cellspacing="0" cellpadding="0">
            <tr><td colspan="2"><h2>Email Template</h2></td></tr>
            <tr><td>Admin Email: </td><td><input type="text" name="adminemail" value="<?php echo $survey['adminemail']; ?>" size="40"></td></tr>
            <tr><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="5" width="1"></td></tr>
            <tr><td>Subject: </td><td><input type="text" name="emailsubject" value="<?php echo $survey['emailsubject']; ?>" size="40"></td></tr>
            <tr><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="5" width="1"></td></tr>
            <tr><td>Email template file: </td><td><?php echo $sel; ?></td></tr>
            <tr><td colspan="2"><textarea id="fr_cmstext" name="emailbody" cols="80" rows="20"><?php echo $survey['emailbody']; ?></textarea></td></tr>
            <tr><td colspan="2"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" height="5" width="1"></td></tr>
            <tr><td colspan="2"><input type="submit" name="submit" value="Save Email Template"></td></tr>
            </table>
         </form>
      <BR><BR>
   </td></tr>   
</table>
<?php } ?>


<!-- end: jsfadmin/listemails.php -->

</td></tr>
</table>
