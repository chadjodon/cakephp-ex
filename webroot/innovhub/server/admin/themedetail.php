<?php
$ss = new Version();
$ua = new UserAcct();

$theme = $ss->getThemeById($vars['themeid']);

   $months['Jan'] = 1;   
   $months['Feb'] = 2;   
   $months['Mar'] = 3;   
   $months['Apr'] = 4;   
   $months['May'] = 5;   
   $months['June'] = 6;   
   $months['July'] = 7;   
   $months['Aug'] = 8;   
   $months['Sept'] = 9;   
   $months['Oct'] = 10;   
   $months['Nov'] = 11;   
   $months['Dec'] = 12;   
   
   for ($i=1; $i<=31; $i++) {
      $days[$i] = $i;
   }

   $statusOpt['Active'] = "ACTIVE";
   $statusOpt['Inactive'] = "INACTIVE";

   $smonth = ((int)($theme['startday']/32))+1;
   $sday = $theme['startday'] % 32;
   $emonth = ((int)($theme['endday']/32))+1;
   $eday = $theme['endday'] % 32;
   $sdayOpt = getOptionList("startd",$days,$sday);
   $smonthOpt = getOptionList("startm",$months,$smonth);
   $edayOpt = getOptionList("endd",$days,$eday);
   $emonthOpt = getOptionList("endm",$months,$emonth);

  $survey = new Survey();
  $surveys = $survey->getSurveys(isLoggedOn());
  $surveyOpts = array();
  for ($i=0; $i<count($surveys); $i++) $surveyOpts[$surveys[$i]['name']]=$surveys[$i]['survey_id'];

  $compareOpts["is equal to"] = "==";
  $compareOpts["is not equal to"] = "!=";
  $compareOpts["is greater than"] = ">";
  $compareOpts["is less than"] = "<";
  $compareOpts["contains"] = "contains";

   $logOpts['logged on'] = 0;
   $logOpts['approved'] = 1;
   $logOpts['approved (level 2)'] = 2;
   $logOpts['approved (level 3)'] = 3;
   $logOpts['approved (level 4)'] = 4;
   $logOpts['approved (level 5)'] = 5;
   $logOpts['approved (level 6)'] = 6;
   $logOpts['approved (level 7)'] = 7;
   $logOpts['approved (level 8)'] = 8;
   $logOpts['approved (level 9)'] = 9;
   $logOpts['approved (level 10)'] = 10;
   $logOpts['an administrator'] = -1;
   $logOpts['a super administrator'] = -2;
  
  $isisnotOpts["is"] = "1";
  $isisnotOpts["is not"] = "2";

  $viewOpts["view"] = "1";
  $viewOpts["action"] = "2";

  $atleastOpts["at least"] = "1";
  $atleastOpts["less than"] = "2";

?>

<table border="0" cellpadding="3" cellspacing="1">
<form name="themeform<?php echo $i;?>" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
<input type="hidden" name="action" value="viewthemes">
<input type="hidden" name="update" value="1">
<input type="hidden" name="themeid" value="<?php echo $theme['themeid'];?>">
<tr>
<td bgcolor="#CCCCCC">Name of theme:</td>
<td><input type="text" name="themename" value="<?php echo convertBack($theme['themename']); ?>" size="25"></td>
</tr><tr>
<td bgcolor="#CCCCCC">Priority:</td>
<td><input type="text" name="priority" value="<?php echo $theme['priority']; ?>" size="2"> </td>
</tr><tr>
<td bgcolor="#CCCCCC">When theme starts being active:</td>
<td><?php echo $smonthOpt." ".$sdayOpt; ?></td>
</tr><tr>
<td bgcolor="#CCCCCC">When theme stops being active:</td>
<td><?php echo $emonthOpt." ".$edayOpt; ?></td>
</tr><tr>
<td bgcolor="#CCCCCC">Status:</td>
<td><?php echo getOptionList("status",$statusOpt,$theme['status']); ?></td>
</tr><tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Update"></td>
</tr>
</form>
</table>


<BR><HR><BR>

<script type="text/javascript">
        function showSearch() {
                document.getElementById('search_sect').style.display = "";
                document.getElementById('privacy_sect').style.display = "none";
                document.getElementById('profile_sect').style.display = "none";
                document.getElementById('session_sect').style.display = "none";
                document.getElementById('view_sect').style.display = "none";
        }

        function showPrivacy() {
                document.getElementById('search_sect').style.display = "none";
                document.getElementById('privacy_sect').style.display = "";
                document.getElementById('profile_sect').style.display = "none";
                document.getElementById('session_sect').style.display = "none";
                document.getElementById('view_sect').style.display = "none";
        }

        function showSession() {
                document.getElementById('search_sect').style.display = "none";
                document.getElementById('privacy_sect').style.display = "none";
                document.getElementById('profile_sect').style.display = "none";
                document.getElementById('session_sect').style.display = "";
                document.getElementById('view_sect').style.display = "none";
        }        

        function showView() {
                document.getElementById('search_sect').style.display = "none";
                document.getElementById('privacy_sect').style.display = "none";
                document.getElementById('profile_sect').style.display = "none";
                document.getElementById('session_sect').style.display = "none";
                document.getElementById('view_sect').style.display = "";
        }        
</script>                                                            


      <table cellspacing="1" cellpadding="2" cellspacing="2">
      <?php
         $rules = $ss->getThemeRules($vars['themeid']);

         if ($rules == null || count($rules)<1) {
            print "<tr><td colspan=\"2\"><b>There are no rules currently attached to this theme</b></td></tr>";
         }
         else {
            print "<tr><td colspan=\"2\">Rules for this theme include</td></tr>";
            for ($i=0; $i<count($rules); $i++) {
               if (0==strcmp($rules[$i]['ruletype'],"SEARCH")) {
                  $srvy = $survey->getSurvey($rules[$i]['field1']);
                  $where = "";
                  if ($rules[$i]['field2'] != null) $where .= " where ".$rules[$i]['field2'];
                  if ($rules[$i]['field3'] != null) $where .= $rules[$i]['field4']." ".$rules[$i]['field3'];
                  print "<tr><td>User performs search on ".$srvy['name'].$where."</td>";
               }
               else if (0==strcmp($rules[$i]['ruletype'],"PRIVACY")) {
                  $isisnot=" is ";
                  if ($rules[$i]['field2']==2) $isisnot=" is not ";
   
                  if ($rules[$i]['field1']==0) $isisnot.="logged on";
                  else if ($rules[$i]['field1']==1) $isisnot.="an approved user (level 1)";
                  else if ($rules[$i]['field1']==2) $isisnot.="an approved user (level 2)";
                  else if ($rules[$i]['field1']==3) $isisnot.="an approved user (level 3)";
                  else if ($rules[$i]['field1']==4) $isisnot.="an approved user (level 4)";
                  else if ($rules[$i]['field1']==5) $isisnot.="an approved user (level 5)";
                  else if ($rules[$i]['field1']==6) $isisnot.="an approved user (level 6)";
                  else if ($rules[$i]['field1']==7) $isisnot.="an approved user (level 7)";
                  else if ($rules[$i]['field1']==8) $isisnot.="an approved user (level 8)";
                  else if ($rules[$i]['field1']==9) $isisnot.="an approved user (level 9)";
                  else if ($rules[$i]['field1']==10) $isisnot.="an approved user (level 10)";
                  else if ($rules[$i]['field1']==-1) $isisnot.="an administrator";
                  else if ($rules[$i]['field1']==-2) $isisnot.="a super administrator";
   
                  print "<tr><td>User ".$isisnot."</td>";
               }
               else if (0==strcmp($rules[$i]['ruletype'],"SESSION")) {
                  $isisnot=" is ";
                  if ($rules[$i]['field1']==2) $isisnot=" is not ";
                  print "<TR><TD>session search</td></tr>";
               }
               else if (0==strcmp($rules[$i]['ruletype'],"VIEW")) {
                  print "<TR><TD>User has viewed ";
                  if ($rules[$i]['field1']==2) print "action";
                  else print "view";
                  print " '".$rules[$i]['field2']."' ";
                  if ($rules[$i]['field4']==2) print " less than ";
                  else print " at least ";
                  print $rules[$i]['field3']." times</td></tr>";
               }
               
               print "<td>&nbsp&nbsp;";
               if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?ruleid=".$rules[$i]['ruleid']."&action=viewthemes&submit=View%20Info&update=1&removerule=1&themeid=".$vars['themeid']."\">[Remove]</a>";
               print "</td>";
               print "</tr>";
            }
         }

      ?>
      </table>
      <BR>

      <table cellpadding="2" cellspacing="2" id="top_sect">
      <tr align="left" valign="top">
         <td>Add a Rule:</td>
         <td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesFolder']; ?>pixel.gif" width="5" height="1"></td>
         <td><input type="radio" name="ruletype" value="SEARCH" onclick="javascript: showSearch();"></td>
         <td>Search</td>
         <td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesFolder']; ?>pixel.gif" width="5" height="1"></td>
         <td><input type="radio" name="ruletype" value="PRIVACY" onclick="javascript: showPrivacy();"></td>
         <td>User</td>
         <td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesFolder']; ?>pixel.gif" width="5" height="1"></td>
         <td><input type="radio" name="ruletype" value="SESSION" onclick="javascript: showSession();"></td>
         <td>Session</td>
         <td><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesFolder']; ?>pixel.gif" width="5" height="1"></td>
         <td><input type="radio" name="ruletype" value="VIEW" onclick="javascript: showView();"></td>
         <td>Pages</td>
      </tr>
      </table>         

      <table cellpadding="2" cellspacing="0" id="search_sect" style="display: none;">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
      <input type="hidden" name="action" value="viewthemes">
      <input type="hidden" name="submit" value="View Info">
      <input type="hidden" name="update" value="1">
      <input type="hidden" name="newrule" value="1">
      <input type="hidden" name="ruletype" value="SEARCH">
      <input type="hidden" name="themeid" value="<?php echo $vars['themeid']; ?>">
      <tr>
         <td><?php echo getOptionList("field1", $surveyOpts, ""); ?></td>
         <td>Parameter: </td>
         <td><input type="text" name="field2" value="" size="10"></td>
         <td><?php echo getOptionList("field4", $compareOpts, ""); ?></td>
         <td><input type="text" name="field3" value="" size="10"></td>
         <td><input type="submit" name="add" value="add"></td>
      </tr>
      </form>
      </table>

      <table cellpadding="2" cellspacing="0" id="session_sect" style="display: none;">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
      <input type="hidden" name="action" value="viewthemes">
      <input type="hidden" name="submit" value="View Info">
      <input type="hidden" name="update" value="1">
      <input type="hidden" name="newrule" value="1">
      <input type="hidden" name="ruletype" value="SESSION">
      <input type="hidden" name="themeid" value="<?php echo $vars['themeid']; ?>">
      <tr align="left" valign="top">
         <td>Session parameter: </td>
         <td><input type="text" name="field2" value="" size="10"></td>
         <td> <?php echo getOptionList("field1", $isisnotOpts, ""); ?></td>
         <td> set in the session </td>
         <td> with session value (optional): </td>
         <td><input type="text" name="field3" value="" size="10"></td>
         <td><input type="submit" name="add" value="add"></td>
      </tr>
      </form>
      </table>

      <table cellpadding="2" cellspacing="0" id="view_sect" style="display: none;">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
      <input type="hidden" name="action" value="viewthemes">
      <input type="hidden" name="submit" value="View Info">
      <input type="hidden" name="update" value="1">
      <input type="hidden" name="newrule" value="1">
      <input type="hidden" name="ruletype" value="VIEW">
      <input type="hidden" name="themeid" value="<?php echo $vars['themeid']; ?>">
      <tr align="left" valign="top">
         <td>User has viewed </td>
         <td> <?php echo getOptionList("field1", $viewOpts, ""); ?></td>
         <td> </td>
         <td><input type="text" name="field2" value="" size="10"></td>
         <td> </td>
         <td> <?php echo getOptionList("field4", $atleastOpts, ""); ?></td>
         <td><input type="text" name="field3" value="" size="5"></td>
         <td> times.</td>
         <td><input type="submit" name="add" value="add"></td>
      </tr>
      </form>
      </table>


      <table cellpadding="2" cellspacing="0" id="privacy_sect" style="display: none;">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
      <input type="hidden" name="action" value="viewthemes">
      <input type="hidden" name="submit" value="View Info">
      <input type="hidden" name="update" value="1">
      <input type="hidden" name="newrule" value="1">
      <input type="hidden" name="ruletype" value="PRIVACY">
      <input type="hidden" name="themeid" value="<?php echo $vars['themeid']; ?>">
      <tr>
         <td>User </td>
         <td> <?php echo getOptionList("field2", $isisnotOpts, ""); ?></td>
         <td> <?php echo getOptionList("field1", $logOpts, ""); ?></td>
         <td><input type="submit" name="add" value="add"></td>
      </tr>
      </form>
      </table>

      <table cellpadding="2" cellspacing="0" id="profile_sect" style="display: none;">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
      <input type="hidden" name="action" value="viewthemes">
      <input type="hidden" name="submit" value="View Info">
      <input type="hidden" name="update" value="1">
      <input type="hidden" name="newrule" value="1">
      <input type="hidden" name="ruletype" value="PROFILE">
      <input type="hidden" name="themeid" value="<?php echo $vars['themeid']; ?>">
      <tr>
         <td>User's Profile Contains </td>
         <td><input type="submit" name="add" value="add"></td>
      </tr>
      </form>
      </table>


<BR><HR><BR>

<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&theme=<?php echo $theme['themeid']; ?>&advsearch=1">View content related to this theme.</a><br>
<!-- a href="">View adspaces related to this theme.</a><br -->
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewsystemproperties&viewtheme=<?php echo $theme['themeid']; ?>">View system properties related to this theme.</a><br>
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder'] ?>controller.php?view=index&overridetheme=<?php echo $theme['themeid']; ?>" target="_new">View website as if this theme were now active</a><br>

