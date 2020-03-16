<?php
  $webdata = new WebsiteData();
  $cellbg = "#FFFFFF";

   $privatesrvy = getParameter('privatesrvy');
   $url = "admincontroller.php?action=wd_listwebdata&privatesrvy=";
   $limitOpts['All Data Lists']= $url."0";
   $limitOpts['Public Surveys']= $url."2";
   $limitOpts['Private Surveys']= $url."1";
   $limitOpts['Website Data']= $url."3";
   $limitOpts['Admin Data']= $url."4";
   $limitOpts['Other Data']= $url."5";
   //$limitOpts['RSS Feeds']= $url."6";
   $extra = "onChange=\"window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;\"";
   $displayOptions = getOptionList("pageLimit", $limitOpts, $url.$privatesrvy, false, $extra);

?>
<h2>List of Data Tables (Website forms, website surveys, and data storage)</h2>

<form action="form">
Display: <?php echo $displayOptions; ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata"> New Data Table </a></span>
&nbsp;&nbsp;&nbsp;&nbsp;
<!-- span class="button01"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&viewtype=6"> New RSS Feed </a></span -->
</form>
<br>

<table border="0" cellpadding="0" cellspacing="1" bgcolor="#223355">
<TR><TD>

<table border="0" cellpadding="4" cellspacing="1">
   <?php
   $webdatas = $webdata->getWebTables(isLoggedOn(),$privatesrvy);
   if ($webdatas==null || count($webdatas)==0) print "<TR><TD colspan=\"3\" bgcolor=\"white\">You currently have no data to display.</td></tr>";
   else {
      ?>
         <TR class="reg_table_header">
             <TD bgcolor="<?= $cellbg ?>">Data</TD>
             <TD bgcolor="<?= $cellbg ?>">Modified</TD>
             <TD bgcolor="<?= $cellbg ?>">Info</TD>
             <TD bgcolor="<?= $cellbg ?>">Type</TD>
             <TD bgcolor="<?= $cellbg ?>">&nbsp;</TD>
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

   for ($i=0; $i<count($webdatas); $i++) {
      $url = "";
      if ($webdatas[$i]['privatesrvy'] != 1) {
         $url=$GLOBALS['baseURLSSL'].$GLOBALS['formsrewrite'].$webdatas[$i]['wd_id'].".html";
      }

      $infoDisplay = $webdatas[$i]['info'];
      if (strlen($webdatas[$i]['info'])>100) $infoDisplay = substr($webdatas[$i]['info'],0,97)."...";

      if ($webdatas[$i]['privatesrvy']==1) $typeDisplay = "Private Survey";
      else if ($webdatas[$i]['privatesrvy']==6) $typeDisplay = "RSS Feed";
      else if ($webdatas[$i]['privatesrvy']>2) $typeDisplay = "Data Storage";
      else $typeDisplay = "Public Survey";

      ?>
         <TR class="reg_table">
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=wd_listrows&wd_id=<?= $webdatas[$i]['wd_id'] ?>&pageLimit=25"><?= $webdatas[$i]['name'] ?></a></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $webdatas[$i]['lastmod']; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $infoDisplay; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><?php echo $typeDisplay; ?></TD>
          <TD bgcolor="<?= $cellbg ?>"><a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&wd_id=<?= $webdatas[$i]['wd_id'] ?>">Edit</a></TD>
          <TD bgcolor="<?= $cellbg ?>">
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=makecopy&wd_id=<?= $webdatas[$i]['wd_id'] ?>">Copy</a>
          </td>

         <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) { ?>
             <td bgcolor="<?= $cellbg ?>">
                  <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&subaction=remove&wd_id=<?= $webdatas[$i]['wd_id'] ?>" onclick="return confirm('Are you sure you want to delete all the data? (This cannot be undone)')">Delete</a> 
             </td>
         <?php } ?>
         
         </TR>
      <?php
   }
   ?>
</table>

</td></tr>
</table>
