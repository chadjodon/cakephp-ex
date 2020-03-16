<?php

if ($vars['limit']==null) $limit=100;
else $limit = $vars['limit'];

if ($vars['ctype']==null) $ctype=null;
else $ctype = $vars['ctype'];


$newsAds = $pubcont->getContent($ctype,$limit);
?>

<table width="100%" cellpadding="3" cellspacing="0" border="1">
<tr align="center"><td>
<a href="http://www.napcor.com/test_index.php" target="_blank">See Test Homepage (will display disabled articles below)</a>
</td><td>
<a href="http://www.napcor.com" target="_blank">Published Homepage (only first 5 enabled articles)</a>
</td></tr>
</table>

<br/>


<form name="form" action="form">
View: 
<SELECT NAME="pubcontsel" onChange="window.location.href=this.form.pubcontsel.options[this.form.pubcontsel.selectedIndex].value;">
<OPTION value="http://napcor.com/admin/admincontroller.php?action=displaypubcontent">All content</option>
<OPTION value="http://napcor.com/admin/admincontroller.php?action=displaypubcontent&dispctype=homepg" <?php if($vars['ctype']=="homepg") print "SELECTED"; ?>>Homepage content</option>
<OPTION value="http://napcor.com/admin/admincontroller.php?action=displaypubcontent&dispctype=news" <?php if($vars['ctype']=="news") print "SELECTED"; ?>>News content</option>
<OPTION value="http://napcor.com/admin/admincontroller.php?action=displaypubcontent&dispctype=other" <?php if($vars['ctype']=="other") print "SELECTED"; ?>>Other content</option>
</SELECT>
</form>

<table width="100%" cellpadding="2" cellspacing="0" border="1">
<tr><th>Date</th><th>Type</th><th>Title</th><th>Content</th><th colspan="4">URLs</th>
<th>&nbsp;</th><th>&nbsp;</th></tr>


<?php
for ($i=0; $i<count($newsAds); $i++) {
   $rowClass = ($i % 2) +1;
?>

<tr class='list_row<?php echo $rowClass; ?>'>
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
<input type="hidden" name="action" value="editpubcont">
<input type="hidden" name="dispctype" value="<?php echo $vars['ctype']; ?>">
<input type="hidden" name="contid" value="<?php echo $newsAds[$i]['contid']; ?>">
<td>
      <input type="radio" name="datedisp" value="1" <?php if($newsAds[$i]['datedisp']==1) print "CHECKED"; ?>>Display date<br>
      <input type="radio" name="datedisp" value="2" <?php if($newsAds[$i]['datedisp']==2) print "CHECKED"; ?>>Display month/year<br>
      <input type="radio" name="datedisp" value="3" <?php if($newsAds[$i]['datedisp']==3) print "CHECKED"; ?>>Display year<br>
      <input type="radio" name="datedisp" value="4" <?php if($newsAds[$i]['datedisp']==4) print "CHECKED"; ?>>Display no date<br>
      <?php print getStrDateSelection($newsAds[$i]['publishdate'],"publishdate"); ?>
</td>
<td>
      <?php
        $results=null;
        $results=Array();
        $results['Home Page'] = "homepg";
        $results['News'] = "news";
        $results['Other'] = "other";
         print getOptionList("ctype",$results,$newsAds[$i]['ctype']);
      ?>
</td>
<td><textarea name="title" rows="6" cols="15"><?php echo $newsAds[$i]['title']; ?></textarea></td>
<td><textarea name="contdata" rows="6" cols="25"><?php echo $newsAds[$i]['contdata']; ?></textarea></td>
<td colspan="4">

   <table border="0" cellpadding="0" cellspacing="0"><tr>
   <tr><td>URL1: </td><td><input type="text" name="link1url" value="<?php echo $newsAds[$i]['link1url']; ?>" size="35"></td></tr>
   <tr><td>URL1 Display: </td><td><input type="text" name="link1display" value="<?php echo $newsAds[$i]['link1display']; ?>" size="35"></td></tr>
   <tr><td>URL2: </td><td><input type="text" name="link2url" value="<?php echo $newsAds[$i]['link2url']; ?>" size="35"></td></tr>
   <tr><td>URL2 Display:</td><td><input type="text" name="link2display" value="<?php echo $newsAds[$i]['link2display']; ?>" size="35"></td></tr>
   </table>

</td>
<!-- td><input type="text" name="link1url" value="<?php echo $newsAds[$i]['link1url']; ?>" size="10"></td>
<td><input type="text" name="link1display" value="<?php echo $newsAds[$i]['link1display']; ?>" size="10"></td>
<td><input type="text" name="link2url" value="<?php echo $newsAds[$i]['link2url']; ?>" size="10"></td>
<td><input type="text" name="link2display" value="<?php echo $newsAds[$i]['link2display']; ?>" size="10"></td -->
<td><input type="submit" name="Update" value="Update"></td>
</form>

<?php if ($newsAds[$i]['enable'] == 1) { ?>
<td bgcolor="green">
<?php } else { ?>
<td bgcolor="red">
<?php } ?>

<table cellpadding="0" cellspacing="0">
<?php if ($newsAds[$i]['enable'] == 1) { ?>
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="disablepubcont">
   <input type="hidden" name="dispctype" value="<?php echo $vars['ctype']; ?>">
   <input type="hidden" name="contid" value="<?php echo $newsAds[$i]['contid']; ?>">
   <TR><TD><input type="submit" name="Disable" value="Disable"></td></tr>
   </form>
<?php } else { ?>
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
   <input type="hidden" name="action" value="enablepubcont">
   <input type="hidden" name="dispctype" value="<?php echo $vars['ctype']; ?>">
   <input type="hidden" name="contid" value="<?php echo $newsAds[$i]['contid']; ?>">
   <tr><td><input type="submit" name="Enable" value="Enable"></td></tr>
   </form>
<?php } ?>

<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
<input type="hidden" name="action" value="removepubcont">
<input type="hidden" name="dispctype" value="<?php echo $vars['ctype']; ?>">
<input type="hidden" name="contid" value="<?php echo $newsAds[$i]['contid']; ?>">
<tr><td><input type="submit" name="Remove" value="Remove"></td></tr>
</form>
</table>


</td>
</tr>
<?php } ?>


<tr><td colspan="10">&nbsp;<br></td></tr>

<tr><th>Date</th><th>Type</th><th>Title</th><th>Content</th><th>URL 1</th><th>URL 1 Display</th><th>URL 2</th><th>URL 2 Display</th>
<th>&nbsp;</th><th>&nbsp;</th></tr>

<tr  class='list_row1'>
<form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
<input type="hidden" name="action" value="addpubcont">
<input type="hidden" name="dispctype" value="<?php echo $vars['ctype']; ?>">
<td>
      <input type="radio" name="datedisp" value="1" CHECKED>Display date<br>
      <input type="radio" name="datedisp" value="2">Display month/year<br>
      <input type="radio" name="datedisp" value="3">Display year<br>
      <input type="radio" name="datedisp" value="4">Display no date<br>
      <?php print getDateSelection(null,null,null,"publishdate"); ?>
</td>
<td>
      <?php
        $results=null;
        $results=Array();
        $results['Home Page'] = "homepg";
        $results['News'] = "news";
        $results['Other'] = "other";
         print getOptionList("ctype",$results,$newsAds[$i]['ctype']);
      ?>
</td>
<td><textarea name="title" rows="8" cols="15"></textarea></td>
<td><textarea name="contdata" rows="8" cols="25"></textarea></td>
<td><input type="text" name="link1url" value="" size="10"></td>
<td><input type="text" name="link1display" value="" size="10"></td>
<td><input type="text" name="link2url" value="" size="10"></td>
<td><input type="text" name="link2display" value="" size="10"></td>
<td><input type="submit" name="Add" value="Add"></td>
<td>&nbsp;</td>
</form>
</tr>


</table><br><br>
