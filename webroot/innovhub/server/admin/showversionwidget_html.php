<?php
  $url = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php";
  if ($urloverride!=NULL) $url = $urloverride;
?>
      <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
      <form action="<?php echo $url; ?>" name="versioncontrolcontent" method="POST">
      <input type="hidden" name="action" value="<?php echo getParameter("action"); ?>">
      <input type="hidden" name="editcontents" value="1">
      <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
      <input type="hidden" name="version" value="<?php echo $version; ?>">
      <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
      <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
      <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
      <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
      <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
      <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">

         <!--tr valign="top"><td colspan="2" align="center"><input type="submit" name="submit" value="Update/Save File Contents" <?php echo $disabled; ?>></td></tr-->
         <tr align="left" valign="top"><td colspan="2">
               <?php if (0== strcmp(getParameter("viewtype"),"nonrte") || strpos($contents,"</script>")!==FALSE) { ?>
               <textarea cols="80" rows="25" name="contents" <?php echo $disabled; ?>><?php echo $contents; ?></textarea>
               <?php } else { ?>
               <script>initRTE('<?php echo convertJavascriptString($contents); ?>', 'rte/html/example.css');</script>
               <?php } ?>
         </td></tr>
         <tr><td colspan="2" align="center">
                  <input type="submit" name="submit" value="Update/Save File Contents" <?php echo $disabled; ?>>
         </td></tr>
      </form>
      </table>

