<?php
   $imageUtil = new JSFImage();
?>

<center><h2><?php echo $vars['title']; ?></h2></center>

<BR>
<center>
<table cellpadding="5" cellspacing="1" bgcolor="#555555">
<form enctype="multipart/form-data" action="admincontroller.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="hidden" name="action" value="siteadd">
<input type="hidden" name="makechanges" value="1">
<input type="hidden" name="parent" value="<?php echo $vars['parent']; ?>">
<input type="hidden" name="siteid" value="<?php echo $vars['siteid']; ?>">
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Name</td><td bgcolor="#FFFFFF"><input type="text" name="name" value="<?php echo $vars['name']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>SEO Page Name</td><td bgcolor="#FFFFFF"><input type="text" name="shortname" value="<?php echo $vars['shortname']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>SEO Meta-description</td><td bgcolor="#FFFFFF"><input type="text" name="metadescr" value="<?php echo $vars['metadescr']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>SEO Keywords</td><td bgcolor="#FFFFFF"><input type="text" name="keywords" value="<?php echo $vars['keywords']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Alternate Page Names</td><td bgcolor="#FFFFFF"><input type="text" name="alternates" value="<?php echo $vars['alternates']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Priority</td><td bgcolor="#FFFFFF"><input type="text" name="priority" value="<?php echo $vars['priority']; ?>"></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Short Description</td><td bgcolor="#FFFFFF"><textarea name="shortdescr" rows="4" cols="40"><?php echo $vars['shortdescr']; ?></textarea></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Description</td><td bgcolor="#FFFFFF"><textarea name="descr" rows="4" cols="40"><?php echo $vars['descr']; ?></textarea></td></tr>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Site URL</td><td bgcolor="#FFFFFF"><input type="text" name="site_url" value="<?php echo $vars['site_url']; ?>"></td></tr>

<?php
   $typeopts['User Facing Site']=1;
   $typeopts['Shared Content']=2;
   $sitetypeopts = getOptionList("site_type", $typeopts, $vars['site_type']);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Site Type</td><td bgcolor="#FFFFFF"><?php echo $sitetypeopts; ?></td></tr>


<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php 
   if ($vars['image1'] != NULL) {
      $info = fitToBoxProportion ($GLOBALS['srvyDir'].$vars['image1'], 350, 40);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Image1</td><td bgcolor="#FFFFFF"><img src="<?php echo $GLOBALS['srvyURL'].$vars['image1']; ?>" height="<?php echo $info['height']; ?>" width="<?php echo $info['width']; ?>"></td></tr>
<?php } ?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Upload Image1</td><td bgcolor="#FFFFFF"><input type="file" name="image1"></td></tr>

<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php 
   if ($vars['image2'] != NULL) {
      $info = fitToBoxProportion ($GLOBALS['srvyDir'].$vars['image2'], 350, 40);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Image2</td><td bgcolor="#FFFFFF"><img src="<?php echo $GLOBALS['srvyURL'].$vars['image2']; ?>" height="<?php echo $info['height']; ?>" width="<?php echo $info['width']; ?>"></td></tr>
<?php } ?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Upload Image2</td><td bgcolor="#FFFFFF"><input type="file" name="image2"></td></tr>

<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php 
   if ($vars['image3'] != NULL) {
      $info = fitToBoxProportion ($GLOBALS['srvyDir'].$vars['image3'], 350, 40);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Image3</td><td bgcolor="#FFFFFF"><img src="<?php echo $GLOBALS['srvyURL'].$vars['image3']; ?>" height="<?php echo $info['height']; ?>" width="<?php echo $info['width']; ?>"></td></tr>
<?php } ?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Upload Image3</td><td bgcolor="#FFFFFF"><input type="file" name="image3"></td></tr>

<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php 
   if ($vars['image4'] != NULL) {
      $info = fitToBoxProportion ($GLOBALS['srvyDir'].$vars['image4'], 350, 40);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Image4</td><td bgcolor="#FFFFFF"><img src="<?php echo $GLOBALS['srvyURL'].$vars['image4']; ?>" height="<?php echo $info['height']; ?>" width="<?php echo $info['width']; ?>"></td></tr>
<?php } ?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Upload Image4</td><td bgcolor="#FFFFFF"><input type="file" name="image4"></td></tr>

<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php 
   if ($vars['image5'] != NULL) {
      $info = fitToBoxProportion ($GLOBALS['srvyDir'].$vars['image5'], 350, 40);
?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Image5</td><td bgcolor="#FFFFFF"><img src="<?php echo $GLOBALS['srvyURL'].$vars['image5']; ?>" height="<?php echo $info['height']; ?>" width="<?php echo $info['width']; ?>"></td></tr>
<?php } ?>
<tr align="left" valign="top" bgcolor="#DDDDDD"><td>Upload Image5</td><td bgcolor="#FFFFFF"><input type="file" name="image5"></td></tr>



<tr bgcolor="#FFFFFF"><td colspan="2"><br></td></tr>
<?php if ($vars['siteid'] != -1) { ?>
<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Save"></td></tr>
<?php } ?>

</form>
</table>
</center>
<br>
<a href="admincontroller.php?action=sitemanagement">Return to microsite management</a><br><br>