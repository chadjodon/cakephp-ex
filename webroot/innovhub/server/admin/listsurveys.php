<?php
  $survey = new Survey();
  $cellbg = "#FFFFFF";

   $privatesrvy = getParameter('privatesrvy');
   $url = "admincontroller.php?action=listsurveys&privatesrvy=";
   $limitOpts['All Data Lists']= $url."0";
   $limitOpts['Public Surveys']= $url."2";
   $limitOpts['Private Surveys']= $url."1";
   $limitOpts['Website Data']= $url."3";
   $limitOpts['Admin Data']= $url."4";
   $limitOpts['Other Data']= $url."5";
   $limitOpts['RSS Feeds']= $url."6";
   $extra = "onChange=\"window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;\"";
   $displayOptions = getOptionList("pageLimit", $limitOpts, $url.$privatesrvy, false, $extra);

?>
<h2>List of Data Tables (Website forms, website surveys, and data storage)</h2>

<form action="form">
Display: <?php echo $displayOptions; ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey"> New Data Table </a></span>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&viewtype=6"> New RSS Feed </a></span>
</form>
<br>

<table border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
<TR><TD>

<table border="0" cellpadding="4" cellspacing="1">
   <?php
   $surveys = $survey->getSurveys(isLoggedOn(),$privatesrvy);
   if ($surveys==null || count($surveys)==0) print "<TR><TD colspan=\"3\" bgcolor=\"white\">You currently have no surveys to display.</td></tr>";
   else {
      ?>
         <TR class="reg_table_header">
             <TD bgcolor="<?= $cellbg ?>">Data</TD>
             <TD bgcolor="<?= $cellbg ?>">Modified</TD>
             <TD bgcolor="<?= $cellbg ?>">Info</TD>
             <TD bgcolor="<?= $cellbg ?>">Type</TD>
             <TD bgcolor="<?= $cellbg ?>">&nbsp;</TD>
             <!-- TD bgcolor="<?= $cellbg ?>">Public URL</TD -->
             <TD bgcolor="<?= $cellbg ?>">&nbsp;</TD>

            <?php
               //only show this one if a user has permission to delete a survey
               $ua = new UserAcct; 
               if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { 
            ?>
             <TD bgcolor="<?= $cellbg ?>">&nbsp;</TD>
            <?php } ?>

         </TR>

      <?php
   }

   for ($i=0; $i<count($surveys); $i++) {
      $url = "";
      if ($surveys[$i]['privatesrvy'] != 1) {
         $url=$GLOBALS['baseURLSSL'].$GLOBALS['formsrewrite'].$surveys[$i]['survey_id'].".html";
      }

      $infoDisplay = $surveys[$i]['info'];
      if (strlen($surveys[$i]['info'])>100) $infoDisplay = substr($surveys[$i]['info'],0,97)."...";

      if ($surveys[$i]['privatesrvy']==1) $srvyTypeDisplay = "Private Survey";
      else if ($surveys[$i]['privatesrvy']==6) $srvyTypeDisplay = "RSS Feed";
      else if ($surveys[$i]['privatesrvy']>2) $srvyTypeDisplay = "Data Storage";
      else $srvyTypeDisplay = "Public Survey";

      ?>
         <TR class="reg_table">
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&survey_id=<?= $surveys[$i]['survey_id'] ?>&pageLimit=25"><?= $surveys[$i]['name'] ?></a></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $surveys[$i]['lastmod']; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $infoDisplay; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $srvyTypeDisplay; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&survey_id=<?= $surveys[$i]['survey_id'] ?>">Edit</a></TD>
          <TD bgcolor="<?= $cellbg ?>">
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&subaction=makecopy&survey_id=<?= $surveys[$i]['survey_id'] ?>">Copy</a>
          </td>

         <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
             <td bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&subaction=remove&survey_id=<?= $surveys[$i]['survey_id'] ?>" onclick="return confirm('Are you sure you want to delete the survey and all its responses?')">Delete</a> 
             </td>
         <?php } ?>
         
         </TR>
      <?php
   }
   ?>
</table>

</td></tr>
</table>
