<?php
    $adSpace = new AdSpace;
    $ad = $adSpace->getAdSpace($vars['adnum']);
?>
      <form name="changeAdSpace" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="adspaces">
      <input type="hidden" name="modifyAd" value="1">
      <input type="hidden" name="adnum" value="<?php echo $vars['adnum']; ?>">
      <input type="hidden" name="cname" value="<?php echo $vars['cname']; ?>">
      <table border=1>
      <tr><Td>Ad Space Number: </td><td> <?php echo $ad['adnum']; ?></td></tr>
      <tr><Td>Campaign: </td><td> <?php echo $adSpace->getCampaignSelection($ad['cname']); ?></td></tr>
      <tr><Td>Start Date: </td><td> <input type="text" name="startdate" value="<?php echo $ad['startdate']; ?>"></td></tr>
      <tr><Td>End Date: </td><td> <input type="text" name="expire" value="<?php echo $ad['expire']; ?>"></td></tr>
<?php
      $options['ENABLED'] = "ENABLED";
      $options['DISABLED'] = "DISABLED";
      $statesDropList = getOptionList("adstate", $options, $ad['adstate']);
      $textAd = convertBack($ad['ad']);
?>
      <tr><td>Ad State:</td><td><?php echo $statesDropList; ?></td></tr>
      <tr><Td>Ad: </td><td> <textarea name="ad" rows=5 cols=50><?php echo $textAd; ?></textarea></td></tr>
      <tr><Td>Admin Note: </td><td> <input type="text" name="extra" value="<?php echo $ad['extra']; ?>" size="40"></td></tr>
      <tr><Td>Where Clause: </td><td> <input type="text" name="whereclause" value="<?php echo $ad['whereclause']; ?>" size="40"></td></tr>
      <tr><td colspan=2><input type="submit" name="submit" value="Update"></td></tr>
      </table>
      </form>

      <BR>
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=adspaces&adnum=<?php echo $vars['adnum']; ?>&deleteAd=1&cname=<?php echo $vars['cname']; ?>">Remove this Ad</a>
