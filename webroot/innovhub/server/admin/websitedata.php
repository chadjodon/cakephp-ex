<?php
  $survey = new Survey();
  $cellbg = "#FFFFFF";

   $ua = new UserAcct; 
   $privatesrvy = getParameter('privatesrvy');
   $url = "admincontroller.php?action=listsurveys&privatesrvy=";
   $limitOpts['All']= $url."0";
   $limitOpts['Public Surveys']= $url."2";
   $limitOpts['Private Surveys']= $url."1";
   $limitOpts['Website Data']= $url."3";
   $limitOpts['Admin Data']= $url."4";
   $limitOpts['Other Data']= $url."5";
   $limitOpts['RSS Feeds']= $url."6";
   $extra = "onChange=\"window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;\"";
   $displayOptions = getOptionList("pageLimit", $limitOpts, $url.$privatesrvy, false, $extra);
?>
        
<h2>Website Data and Surveys</h2>
[<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey"> New Data/Form/Survey </a>]
&nbsp;&nbsp; | &nbsp;&nbsp;
[<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&viewtype=6"> New RSS Feed </a>]
<BR><BR>
<form action="form">Display: <?php echo $displayOptions; ?></form>

<table border="0" cellpadding="4" cellspacing="1" bgcolor="#223355">
  <?php
   $surveys = $survey->getSurveys(isLoggedOn(),$privatesrvy);
   if ($surveys==null || count($surveys)==0) print "<TR><TD colspan=\"3\" bgcolor=\"white\">No data/forms/surveys to display.</td></tr>";

   for ($i=0; $i<count($surveys); $i++) {

      $infoDisplay = $surveys[$i]['info'];
      if (strlen($surveys[$i]['info'])>100) $infoDisplay = substr($surveys[$i]['info'],0,97)."...";
      $infoDisplay .= "  Created on: ".<?php echo $surveys[$i]['createdon']; ?>

  ?>
         <TR class="small_table">
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listemails&survey_id=<?= $surveys[$i]['survey_id'] ?>&pageLimit=25"><?= $surveys[$i]['name'] ?></a></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $infoDisplay; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&survey_id=<?= $surveys[$i]['survey_id'] ?>">Edit</a></TD>
          <TD bgcolor="<?= $cellbg ?>">
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=survey&subaction=makecopy&survey_id=<?= $surveys[$i]['survey_id'] ?>">Make a Copy</a>
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
