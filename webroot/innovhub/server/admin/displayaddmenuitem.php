<?php

//displayaddmenuitem.php
//$parent,$name,$menuname,$width,$url,$sequence

$menuInit="";
$linkInit="style=\"display: none;\"";
$menuSelected=" CHECKED";
$linkSelected="";

if ($vars['addsiteitem']==1 && $vars['itemid'] != null && $vars['menuid']!=null) {
	$action="addmenuitem";
   $title = "Edit Site Menu Item";
	$menuitem = $menu->getMenuItem($vars['itemid'],$vars['menuid']);
} else if ($vars['itemid'] != null && $vars['menuid']!=null) {
	$action = "editmenuitem";
   $title = "Edit Menu Item";
	$menuitem = $menu->getMenuItem($vars['itemid'],$vars['menuid']);
} else {
	$action="addmenuitem";
   $title = "Add Menu Item";
}

if ($menuitem['sequence']==null) $menuitem['sequence']=0;

$extra = " onChange=\"chooseURL();\"";
$shortcuts = $ss->getAllShortcuts();

$titles = "var pageTitles=new Array(";
for ($i=0; $i<count($shortcuts); $i++) {
   $titles .="\"".$shortcuts[$i]['title']."\",";
   $options[$shortcuts[$i]['filename']] = $GLOBALS['baseURL'].$GLOBALS['htaccessView'].$shortcuts[$i]['filename'].".html";
}
$titles .= "\"\");\n";
$sel = getOptionList("urlchoice", $options, $menuitem['url'],TRUE,$extra);

$enableOpt['Enabled'] = 1;
$enableOpt['Disabled'] = 0;
$enableSel = getOptionList("status", $enableOpt, $menuitem['status']);

$accessOpt["Everybody"]=0;
$accessOpt["Approved Website Users Level 1"]=1;
$accessOpt["Approved Website Users Level 2"]=2;
$accessOpt["Approved Website Users Level 3"]=3;
$accessOpt["Approved Website Users Level 4"]=4;
$accessOpt["Approved Website Users Level 5"]=5;
$accessOpt["Approved Website Users Level 6"]=6;
$accessOpt["Approved Website Users Level 7"]=7;
$accessOpt["Approved Website Users Level 8"]=8;
$accessOpt["Approved Website Users Level 9"]=9;
$accessOpt["Approved Website Users Level 10"]=10;
$accessOpt["Administrators"]=-1;
$accessOpt["Super Administrators"]=-2;
$privacySel = getOptionList("privacy", $accessOpt, $menuitem['privacy'],FALSE,"onChange=\"checkUserAccess();\"");

$onlineOpt['Always'] = 0;
$onlineOpt['Only for users logged in'] = 1;
$onlineOpt['Only for users logged out'] = 2;
$onlineSel = getOptionList("onlinest", $onlineOpt, $menuitem['onlinest']);


?>


<script language="javascript">

function chooseURL() {
   <?= $titles ?>
   document.addeditmenu.url.value=document.addeditmenu.urlchoice.options[document.addeditmenu.urlchoice.selectedIndex].value;
   document.addeditmenu.menuname.value=pageTitles[(document.addeditmenu.urlchoice.selectedIndex-1)];
}

function checkUserAccess() {
   if(document.addeditmenu.privacy.options[document.addeditmenu.privacy.selectedIndex].value==-1) document.getElementById('adminaccesssect').style.display = "";
   else document.getElementById('adminaccesssect').style.display = "none";
}
</script>

<h2><?= $title ?></h2>

<BR>

<table cellpadding="2" cellspacing="0" border="0" id="menutable">
<form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="addeditmenu" method="POST">
<input type="hidden" name="action" value="<?= $action ?>">
<input type="hidden" name="parent" value="<?= $vars['parent'] ?>">
<input type="hidden" name="menuid" value="<?= $vars['menuid'] ?>">
<input type="hidden" name="adminmid" value="<?php echo getParameter('adminmid'); ?>">
<input type="hidden" name="itemid" value="<?= $vars['itemid'] ?>">
<input type="hidden" name="name" value="">
<tr><td>Shortcut Options:</td><td><?php echo $sel; ?></td></tr>
<tr><td>Menu Name:</td><td><input type="text" size="40" name="menuname" value="<?= $menuitem['menuname'] ?>"></td></tr>
<tr><td>Width:</td><td><input type="text" size="10" name="width" value="<?= $menuitem['width'] ?>"></td></tr>
<tr><td>URL:</td><td><input type="text" size="80" name="url" value="<?= $menuitem['url'] ?>"></td></tr>
<tr><td>Sequence:</td><td><input type="text" size="10" name="sequence" value="<?= $menuitem['sequence'] ?>"></td></tr>
<tr><td>Status:</td><td><?= $enableSel ?></td></tr>
<tr><td>Show:</td><td><?= $onlineSel ?></td></tr>
<tr><td>Privacy:</td><td><?= $privacySel ?></td></tr>

<?php if ($menuitem['icon']!=NULL) { ?>
   <tr><td>Current Icon:</td><td><img src="<?php echo $GLOBALS['srvyURL'].$menuitem['icon']; ?>"></td></tr>
<?php } ?>
<tr><td>Upload Icon:</td><td><input id="jsfmenuicon" name="icon" type="file"></td></tr>

<tr id="adminaccesssect" <?php if($menuitem['privacy']!=-1) print "style=\"display: none;\"";?>><td colspan="2">
<?php 
   $ua = new UserAcct();
   print getRadioBtnList("adminprivacy", $ua->getLevels(), $menuitem['adminprivacy']); 
?>
</td></tr>
<tr><td colspan="2" align="right"><input type="submit" name="submit" value="submit"></td></tr>
</form>
</table>

<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displaysitemenu&menuid=<?php echo $vars['menuid']; ?>&adminmid=<?php echo getParameter('adminmid'); ?>">Return to menu</a>
