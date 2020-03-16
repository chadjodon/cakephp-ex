<?php      
      $adSpace = new AdSpace;
      $ads = $adSpace->getAllAdSpaces($vars['cname']);
?>
      <form name="displayAdSpaceModify" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
      <input type="hidden" name="action" value="adspaces">
      <input type="hidden" name="modifyAd" value="1">
      <input type="hidden" name="listadnum" value="">
      <input type="hidden" name="adstate" value="">
      <input type="hidden" name="cname" value="<?php echo $vars['cname']; ?>">
      </form>
<?php
      print "<table border=1>";
      print "<tr><th>Ad Number</th><th>Campaign Name</th><td>Start Date</th><th>End Date</th><th>Ad Status</th><th>Note</th><th>Ad</th><th>Status Change</th></tr>";
      for ($i=0; $i<count($ads); $i++) {
         $line = $ads[$i];
         $bgColor =  "RED";
         $newStatus = "ENABLED";
         $newStatusMsg = "Enable this Ad";
         if (strcmp($line['adstate'],"ENABLED")==0) {
            $bgColor =  "LIGHTGREEN";
            $newStatus = "DISABLED";
            $newStatusMsg = "Disable this Ad";
         }
         $statusChange = "<a href=\"#\" onClick=\"document.displayAdSpaceModify.listadnum.value='".$line['adnum']."'; document.displayAdSpaceModify.adstate.value='".$newStatus."'; document.displayAdSpaceModify.submit(); return false;\">".$newStatusMsg."</a>";
         print "<tr><td><a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=adspaces&adnum=".$line['adnum']."&cname=".$cname."\">".$line['adnum']."</a></td><td>".$line['cname']."</td><td>".$line['startdate']."</td><td>".$line['expire']."</td><td bgcolor=\"".$bgColor."\">".$line['adstate']."</td><td>".$line['extra']."</td><td>".$line['ad']."</td><td>".$statusChange."</td></tr>";
      }
      print "</table><BR>";
      print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=adspaces&createAd=1&cname=".$cname."\">Add an Ad Space</a>";
?>
