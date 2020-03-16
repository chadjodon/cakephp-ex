<?php $menu->displayExpandJavascript($vars['menuid']); ?>

<h2>Site Main Menu Administration</h2>

<table cellpadding="4" cellspacing="0">
<tr><td align="left">
<a href="#" onClick="expandAll();">Expand All</a> &nbsp;&nbsp;|&nbsp;&nbsp;
<a href="#" onClick="collapseAll();">Collapse All</a>
</td><td align="right"></td>
</tr>
</table>
<br>
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displayaddmenuitem&menuid=<?php echo $vars['menuid']; ?>&itemid=-1&adminmid=<?php echo getParameter('adminmid'); ?>">Add an item at the root level</a><br>

<BR>
<table cellpadding="1" cellspacing="0">

<?php $menu->displayHierarchy(-1,0,2,$vars['menuid']); ?>

</table>