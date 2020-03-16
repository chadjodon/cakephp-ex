<?php
$up = new UserPost();
$ua = new UserAcct();

$searchstr = getParameter("searchstr");

$orderby = getParameter("orderby");
if ($orderby==NULL) $orderby = "p.created DESC";

$visible = getParameter("visible");
if ($visible==NULL || !is_numeric($visible)) $public = NULL;
else $public = ($visible == 1);

$url = getBaseURL()."jsfadmin/admincontroller.php?action=userposts&searchstr=".$searchstr."&visible=".$visible;

$posts = $up->getPostsFor(NULL,NULL,NULL,NULL,NULL,$public,NULL,$searchstr,$orderby);

$visopts['View all posts'] = "";
$visopts['View visible posts'] = "1";
$visopts['View inactive posts'] = "0";

?>


<table cellpadding="2" cellspacing="1" bgcolor="#DDDDDD">
<form method="post" action="<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php">
<input type="hidden" name="action" value="userposts">
<tr>
<td>Search</td>
<td><input type="text" name="searchstr" value="<?php echo $searchstr; ?>"></td>
<td></td>
<td>Status</td>
<td><?php echo getOptionList("visible", $visopts, $visible); ?></td>
</tr>
<tr>
<td colspan="5"><input type="submit" name="submit" value="submit"></td>
</tr>
</form>
</table>
<br><br>
<table cellpadding="2" cellspacing="1">
<tr bgcolor="#DDDDDD">
<td><a href="<?php echo $url; ?>&orderby=p.created%20DESC">Created</a></td>
<td>Message</td>
<td><a href="<?php echo $url; ?>&orderby=u.lname">Name</a></td>
<td><a href="<?php echo $url; ?>&orderby=u.username">Username</a></td>
<td><a href="<?php echo $url; ?>&orderby=u.email">Email</a></td>
<td><a href="<?php echo $url; ?>&orderby=p.posttype">Type</a></td>
<td>Reference</td>
<td><a href="<?php echo $url; ?>&orderby=p.visibility">Enabled</a></td>
<td></td>
</tr>

<form method="post" action="<?php echo $url."&orderby=".$orderby; ?>">
<?php
$bgcolor="";
for ($i=0; $i<count($posts); $i++) {
   if (0==strcmp($bgcolor,"#FFFFFF")) $bgcolor="#CCCCCC";
   else $bgcolor = "#FFFFFF";
?>

   <tr bgcolor="<?php echo $bgcolor; ?>">
   <td><?php echo date("m/d/Y H:i",strtotime($posts[$i]['created'])); ?></td>
   <td><?php echo $posts[$i]['content']; ?></td>
   <td><?php echo $posts[$i]['fname']." ".$posts[$i]['lname']; ?></td>
   <td><?php echo $posts[$i]['username']; ?></td>
   <td><?php echo $posts[$i]['email']; ?></td>
   <td><?php echo $posts[$i]['posttype']; ?></td>
   <td><?php echo $posts[$i]['refid']; ?></td>
   <td><?php if ($posts[$i]['visibility']==1) print "Yes"; else print "No"; ?></td>
   <td><input type="checkbox" name="postid[]" value="<?php echo $posts[$i]['postid']; ?>"></td>
   </tr>

<?php
}
?>
<tr bgcolor="#AAAAAA"><td colspan="9" align="right">
<input type="submit" name="submit" value="Enable" onClick="return confirm('Are you sure you want to enable all messages selected above?');">
&nbsp;
<input type="submit" name="submit" value="Disable" onClick="return confirm('Are you sure you want to disable all messages selected above?');">
&nbsp;
<input type="submit" name="submit" value="Delete" onClick="return confirm('Are you sure you want to delete all messages selected above?  Note: you can only delete messages that are currently disabled.');">
&nbsp;
</td></tr>
</form>
</table>
