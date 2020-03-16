<?php

$surveyOBJ = new Survey();
$survey = null;
$rss = null;
$update = false;
$email = isLoggedOn();
$button = "Create New RSS Feed";
$link = $GLOBALS['baseURLSSL'];
if ($survey_id != NULL) {
   $survey = $surveyOBJ->getSurvey($survey_id);
   $rss = $surveyOBJ->getRss($survey_id);
   $update = true;
   $button = "Update RSS Feed details";
   $email = $survey['adminemail'];
   $link = $rss['link'];
}

?>
<center><h2>RSS feed</h2></center>
<table cellpadding="3" cellspacing="0" border="0">
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
<input type="hidden" name="action" value="survey">
<input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">

<?php if ($update) { ?>
<input type="hidden" name="updateFeed" value="1">
<?php } else { ?>
<input type="hidden" name="newFeed" value="1">
<?php } ?>

<tr>
<td>Title: </td>
<td><input type="text" name="title" value="<?php echo $survey['name']; ?>" size="35"></td>
</tr>

<tr>
<td>Description: </td>
<td><textarea name="description" rows="5" cols="40"><?php echo $survey['info']; ?></textarea></td>
</tr>

<tr>
<td>URL link: </td>
<td><input type="text" name="link" value="<?php echo $link; ?>" size="35"></td>
</tr>

<tr><td colspan="2"><hr><b>Optional</b></td></tr>

<tr>
<td>Image URL: </td>
<td><input type="text" name="image_url" value="<?php echo $rss['image_url']; ?>" size="35"></td>
</tr>

<tr>
<td>Image Title: </td>
<td><input type="text" name="image_title" value="<?php echo $rss['image_title']; ?>" size="35"></td>
</tr>

<tr>
<td>Image Link: </td>
<td><input type="text" name="image_link" value="<?php echo $rss['image_link']; ?>" size="35"></td>
</tr>

<tr>
<td>Image Width: </td>
<td><input type="text" name="image_width" value="<?php echo $rss['image_width']; ?>" size="35"></td>
</tr>

<tr>
<td>Image Height: </td>
<td><input type="text" name="image_height" value="<?php echo $rss['image_height']; ?>" size="35"></td>
</tr>

<tr>
<td>Copyright Statement: </td>
<td><input type="text" name="copyright" value="<?php echo $rss['copyright']; ?>" size="35"></td>
</tr>

<tr>
<td>Managing Editor Email: </td>
<td><input type="text" name="managingEditor" value="<?php echo $rss['managingEditor']; ?>" size="35"></td>
</tr>

<tr>
<td>WebMaster Email: </td>
<td><input type="text" name="webMaster" value="<?php echo $email; ?>" size="35"></td>
</tr>

<tr>
<td>Maximum items: </td>
<td><input type="text" name="max" value="<?php echo $rss['max']; ?>" size="35"></td>
</tr>

<input type="hidden" name="generator" value="jStorefront System v1.0">
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="<?php echo $button; ?>">
</td></tr>

</form>
</table>
