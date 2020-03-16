<?php
      if (0==strcmp($cmsfver['extension'],".jpg")||0==strcmp($cmsfver['extension'],".gif")||0==strcmp($cmsfver['extension'],".png")||0==strcmp($cmsfver['extension'],".jpeg")) {
         $picLocation = $GLOBALS['rootDir'].$ss->createURL($cmsfver);
         $proportions = getHeightProportion ($picLocation, 160);
?>
      <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
         <tr valign="top" align="left"><td bgcolor="#CCCCCC">Current Picture:</td><td>
         <a href="<?php echo $filename; ?>" target="_new"><img src="<?php echo $filename; ?>" width="<?php echo $proportions['width']; ?>" height="<?php echo $proportions['height']; ?>"></a>
         </td></tr>
      </table>
<?php } else {  ?>
         Click <a href="<?php echo $filename; ?>" target="_new"><?php echo $cmsfver['filename']; ?><?php echo $cmsfver['extension']; ?></a> to view this version.
<?php } ?>


<br><br>
<?php if (!$disabled) { ?>
      <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
      <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
      <input type="hidden" name="action" value="managefiles">
      <input type="hidden" name="editversion" value="1">
      <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
      <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
      <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
      <input type="hidden" name="cmsid" value="<?php echo $cmsfver['cmsid']; ?>">
      <input type="hidden" name="version" value="<?php echo $cmsfver['version']; ?>">
      <input type="hidden" name="owner" value="<?php echo $_SESSION['s_user']['emailAddress']; ?>">
      <input type="hidden" name="theme" value="<?php echo $cmsfver['theme']; ?>">

      <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
         <tr valign="top" align="left"><td bgcolor="#CCCCCC">Upload a new file</td><td>
         <input name="userfile" type="file">
         </td></tr>
         <tr><td colspan="2"><input type="submit" name="Upload Content" value="Upload Content"></td></tr>
      </table>
      </form>
   
<?php } else { ?>
    Deactivate this version, or create a new version to upload content.
<?php } ?>

<?php
   print "\n<!-- CMS ver:\n";
   print_r($cmsfver);
   print "\n-->\n";

   // updateVersion($cmsid,$version,$owner,$adminnotes,$status,$contents,$tempfilename,$theme,$title,$descr,$search,$metakw,$vsiteid))
   // updateVersion($cmsid,$version,$owner,$adminnotes,$status,$contents=NULL,$tempfilename=NULL,$theme=0,$title="",$descr="",$search="",$metakw="",$siteid=NULL)
?>

