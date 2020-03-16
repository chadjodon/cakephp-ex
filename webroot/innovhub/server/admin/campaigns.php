<?php
      $adSpace = new AdSpace;
      $values = $adSpace->getAllCampaigns();
      print "<TABLE border=1 cellpadding=3 cellspacing=0>";
      print "<TR><th>Campaign Name</th><th>Campaign Starts on</th><th>Campaign ends on</th><th colspan=3>Campaign Actions</th></tr>";
      for ($i=0; $i<count($values); $i++) {
         $line = $values[$i];
?>

         <TR>
          <form name="campaign_<?php echo $i; ?>" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
          <input type="hidden" name="action" value="">
          <input type="hidden" name="subaction" value="">
          <input type="hidden" name="cstate" value="ENABLED">
          <input type="hidden" name="cname" value="<?php echo $line['cname']; ?>">
          <TD><?php echo $line['cname']; ?></TD>
          <TD><input type="text" name="startdate" value="<?php echo $line['startdate']; ?>"></TD>
          <TD><input type="text" name="expire" value="<?php echo $line['expire']; ?>"></TD>
          </form>
          <TD><a href="#" onClick="document.campaign_<?php echo $i; ?>.action.value='adspaces'; document.campaign_<?php echo $i; ?>.submit(); return false;">View Marketing Ads</a></TD>
          <TD><a href="#" onClick="document.campaign_<?php echo $i; ?>.action.value='campaigns'; document.campaign_<?php echo $i; ?>.subaction.value='update';document.campaign_<?php echo $i; ?>.submit(); return false;">Update</a></TD>
          <TD><a href="#" onClick="document.campaign_<?php echo $i; ?>.action.value='campaigns'; document.campaign_<?php echo $i; ?>.subaction.value='delete';document.campaign_<?php echo $i; ?>.submit(); return false;">Delete</a></TD>
         </TR>
<?php
      }

?>
         <TR><TD colspan=6><BR></td></TR>
         <TR><TD colspan=6 align=center>New</td></TR>
         <TR>
          <form name="new_campaign" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
          <input type="hidden" name="action" value="campaigns">
          <input type="hidden" name="subaction" value="new">
          <input type="hidden" name="cstate" value="ENABLED">
          <TD><input type="text" name="cname" value=""></TD>
          <TD><input type="text" name="startdate" value="<?php echo getDateForDB(); ?>"></TD>
          <TD><input type="text" name="expire" value="<?php echo getDateForDB(1,0); ?>"></TD>
          <TD colspan=3 align=center><a href="#" onClick="document.new_campaign.submit(); return false;">Add</a></TD>
          </form>
         </TR>
       </table>
      <BR><HR>
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=adspaces">View all Adspaces</a><BR><BR>
