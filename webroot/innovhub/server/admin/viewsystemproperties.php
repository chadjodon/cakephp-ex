<?php
   $ss = new Version();
   $url = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=viewsystemproperties&viewtheme=";
   $selected = "";

   $viewtheme = getParameter("viewtheme");
   if ($viewtheme==NULL) $viewtheme=0;

   if (isParameterSet("orderby")) $orderby = getParameter("orderby");
   else $orderby="name";

   $props = $ss->getSystemProperties($viewtheme,$orderby);
    $extra = "onChange=\"window.location.href=this.form.pageTheme.options[this.form.pageTheme.selectedIndex].value;\"";
    $themeOptions = $ss->getThemeOptions($url.$viewtheme, $url, "pageTheme", true, $extra);
?>

    <form action="form">
    View properties for Theme: <?php echo $themeOptions; ?>
    </form>


   <table cellpadding="2" cellspacing="1" border="0">
   <TR bgcolor="#DDDDDD">
      <td><b>Theme</b></td><TD><b>Name</b></TD><TD><b>Value</b></td><td></td>
      <td bgcolor="white">&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td><b>Theme</b></td><TD><b>Name</b></TD><TD><b>Value</b></td><td></td>
   </tr>
<?php
   $totalrows = round(count($props)/2);
   
   for ($i=0; $i<$totalrows; $i++) {
      $list_row = ($i % 2)+1;
      $line=$props[$i];
      $line2=$props[$i+$totalrows];
      $tempTheme = $ss->getThemeById($line['themeid']);
      $tempTheme2 = $ss->getThemeById($line2['themeid']);
?>
      <tr class="list_row<?php echo $list_row; ?>">
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
         <input type="hidden" name="action" value="viewsystemproperties">
         <input type="hidden" name="updateprop" value="1">
         <input type="hidden" name="name" value="<?php echo $line['name']; ?>">
         <input type="hidden" name="theme" value="<?php echo $line['themeid']; ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
         <td><?php echo $tempTheme['themename']; ?> </td>
         <td><?php echo $line['name']; ?></td>
         <td><input type="text" size="10" name="value" value="<?php echo $line['value']; ?>"></td>
         <td><input type="submit" name="submit" value="Update"><input type="submit" name="submit" value="Remove"></td>
         </form>
<?php if ($line2 == NULL) { ?>
         <td bgcolor="white" colspan="5"></td>
<?php } else { ?>
         <td bgcolor="white"></td>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
         <input type="hidden" name="action" value="viewsystemproperties">
         <input type="hidden" name="updateprop" value="1">
         <input type="hidden" name="name" value="<?php echo $line2['name'];?>">
         <input type="hidden" name="theme" value="<?php echo $line2['themeid'];?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
         <td><?php echo $tempTheme2['themename']; ?> </td>
         <td><?php echo $line2['name']; ?></td>
         <td><input type="text" size="10" name="value" value="<?php echo $line2['value']; ?>"></td>
         <td><input type="submit" name="submit" value="Update"><input type="submit" name="submit" value="Remove"></td>
         </form>
<?php } ?>
      </tr>

<?php
   }
?>

      <tr><td colspan="9"><br></td></tr>
      <tr>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
         <input type="hidden" name="action" value="viewsystemproperties">
         <input type="hidden" name="updateprop" value="1">
         <input type="hidden" name="viewtheme" value="<?php echo $viewtheme; ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
         <td colspan="9">
            <?php echo $ss->getThemeOptions($viewtheme); ?>
            <input type="text" size="8" name="name" value="">
            <input type="text" size="10" name="value" value="">
            <input type="submit" name="submit" value="New Property">
         </td>
         </form>
      </tr>


   </table>
