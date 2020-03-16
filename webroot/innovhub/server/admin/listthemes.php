<?php
$ss = new Version();

$themes = $ss->getThemes();
?>

<table border="1" cellpadding="3" cellspacing="0">
<TR bgcolor="#DDDDDD">
<TH>Theme Name</TH>
<TH>Priority </TH>
<TH>Starts </TH>
<TH>Ends   </TH>
<TH>Status   </TH>
<TH>&nbsp;</TH>
</TR>

<tr>
<td>Default Theme</td>
<td>0</td>
<td>Always</td>
<td>Always</td>
<td>Always Active</td>
<td>&nbsp;</td>
</tr>


<?php
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

for ($i=0; $i<count($themes); $i++) {
   $theme = $themes[$i];
   
   $smonth = ((int)($theme['startday']/32))+1;
   $sday = $theme['startday'] % 32;
   $emonth = ((int)($theme['endday']/32))+1;
   $eday = $theme['endday'] % 32;
   $sdayOpt = getOptionList("startd",$days,$sday);
   $smonthOpt = getOptionList("startm",$months,$smonth);
   $edayOpt = getOptionList("endd",$days,$eday);
   $emonthOpt = getOptionList("endm",$months,$emonth);
   //$smonth = getMonthText(((int)($theme['startday']/33))+1);
   //$sday = $theme['startday'] % 32;
   //$start = $smonth." ".$sday;
   //$emonth = getMonthText(((int)($theme['endday']/32))+1);
   //$eday = $theme['endday'] % 32;
   //$end = $emonth." ".$eday;

?>

<tr>
<form name="themeform<?php echo $i;?>" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
<input type="hidden" name="action" value="viewthemes">
<input type="hidden" name="update" value="1">
<input type="hidden" name="themeid" value="<?php echo $theme['themeid'];?>">
<td><input type="text" name="themename" value="<?php echo convertBack($theme['themename']); ?>" size="25"></td>
<td><input type="text" name="priority" value="<?php echo $theme['priority']; ?>" size="2"> </td>
<td><?php echo $smonthOpt." ".$sdayOpt; ?></td>
<td><?php echo $emonthOpt." ".$edayOpt; ?></td>
<td><?php echo getOptionList("status",$statusOpt,$theme['status']); ?></td>
<td><input type="submit" name="submit" value="View Info"><input type="submit" name="submit" value="Update"><input type="submit" name="submit" value="Delete"></td>
</form>
</tr>

<?php

}

?>

<tr>
<form name="themeformnew" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
<input type="hidden" name="action" value="viewthemes">
<input type="hidden" name="add" value="1">
<td><input type="text" name="themename" value="" size="25"></td>
<td><input type="text" name="priority" value="1" size="2"> </td>
<td><?php echo getOptionList("startm",$months,"1")." ".getOptionList("startd",$days,"1"); ?></td>
<td><?php echo getOptionList("endm",$months,"1")." ".getOptionList("endd",$days,"1"); ?></td>
<td><?php echo getOptionList("status",$statusOpt,"INACTIVE"); ?></td>
<td><input type="submit" name="submit" value="Add Theme"></td>
</form>
</tr>

</table>
