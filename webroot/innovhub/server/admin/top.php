<html>
<head>
<title>Website Administration</title>
<LINK REL=STYLESHEET HREF="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['styleDir']; ?>style.css" media="screen" TYPE="text/css">
</head>

<body>

<table width="1000" cellpadding="2" cellspacing="0" border="0">
<tr><td>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<TR class="tinytext">
	<TD align="left"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>administration.jpg"></td>
   <TD align="left"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="10" height="1"></td>

<?php
if (!isset($customCode) || $customCode==null) $customCode = new AdminUI();

if (isLoggedOn() != false) {
?>

<form id="setsitefrm" name="setsitefrm">
   <TD align="left">
<?php
   $ss = new Version();
   if ($ss->getValue("multisites")==1) {
      $ctx = new Context();
      $opts = $ctx->getAdminSiteOptions(isLoggedOn());
      $extra = "onChange=\"window.location.href='admincontroller.php?action=welcome&s_siteid='+this.form.s_siteid.options[this.form.s_siteid.selectedIndex].value;\"";
      $sitearr = $ctx->getSiteContext();
      $siteDropDown = getOptionList("s_siteid", $opts, $sitearr[0]['siteid'], FALSE, $extra);
      print $siteDropDown;
   }
?>      
   </td>
</form>


	<td align="right"><?php echo $_SESSION['s_user']['emailAddress']; ?> logged in.
	  <BR>
     <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=usermod&userid=<?php echo isLoggedOn(); ?>">Account</a> 
     | <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=logout">Switch User</a>
	  | <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=logout">Log Out</a>
	</td>
<?php
} else {
?>
	<td align="right"></td>
<?php
}
?>

</tr>
</table>

  <table bgcolor="#aaccee" border="0" cellpadding="0" cellspacing="0" width="100%">
  <TR><TD><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['imagesDir']; ?>pixel.gif" width="1" height="28"></td><td width="100%">

<div style="position: relative; z-index: 0; left: 0px; top: 0px; visibility: visible;">

<?php
$ua = new UserAcct;
if ($ua->isUserAdmin(isLoggedOn())) {
	//only show links if user is not logged in
   $menu = new Menu($GLOBALS['baseURLSSL']);
   $menuid = $menu->getMenuIdFromName("AdminMenu");
   print $menu->menuHTML("mm_menu", $menuid);
?>

</div>

 <!--a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listusers">List Users</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showurls">List All Site Pages</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displaysitemenu">Main Site Menu</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewthemes">Website Themes</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=viewsystemproperties">System Properties</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles">Website Content</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=displayallmenus">Site Menu</a>&nbsp;&nbsp;|&nbsp;&nbsp;
 <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=listsurveys">Data</a>&nbsp;&nbsp;|&nbsp;&nbsp;-->

<?php 
      //for ($i=0; $i<count($links); $i++) {
      //   $urlSubaction = str_replace(" ", "%20", $links[$i]);
      //   print "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=custom&subaction=".$urlSubaction."\">".$links[$i]."</a>&nbsp;&nbsp;|&nbsp;&nbsp;\n";
      //}

   } 
?>
   </td></tr>
   </table>

   
<br>
<center>
<?php

if (isset($vars['msg']) && $vars['msg'] != null) {
?>
<font color="green">
<b><?= $vars['msg'] ?></b>
</font>
<?php
}

if (isset($vars['error']) && $vars['error'] != null) {
?>
<font color="red">
<b><?= $vars['error'] ?></b>
</font>
<?php
}

?>
</center>

