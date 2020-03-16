<?php
//error_reporting(E_ALL);

$ss = new Version();
$template = new Template();
$ctx = new Context();
$sitearr = $ctx->getSiteContext(); 

$cmsid = getParameter("cmsid");
if ($cmsid==NULL) $cmsid = $vars['cmsid'];
if ($cmsid==NULL) {
   $shortname = getParameter("shortname");
   if ($shortname!=NULL) {
      $cmsfile = $ss->getFileByShortname($shortname);
      $cmsid = $cmsfile['cmsid'];
   }
}

if ($vars['removecmsid']==1) $cmsid = NULL;

$curdir = getParameter("curdir");
if ($curdir == NULL) $curdir="";

$orderby = getParameter("orderby");
$searchstr = getParameter("searchstr");
$viewaddhtmlfile = getParameter("viewaddhtmlfile");
$viewaddemailtemplate = getParameter("viewaddemailtemplate");
$viewaddhtmlemailtemplate = getParameter("viewaddhtmlemailtemplate");
$viewaddsnpfile = getParameter("viewaddsnpfile");
$viewuploadfile = getParameter("viewuploadfile");
$deleteversion = getParameter("deleteversion");
$viewnewversion = getParameter("viewnewversion");
$status = getParameter("status");
$contents = getParameter("contents");

if ($vars == NULL || empty($vars['theme'])) $theme = getParameter("theme");
else $theme = $vars['theme'];
if ($theme==NULL) $theme="0";

if ($vars == NULL || empty($vars['advsearch'])) $advsearch = getParameter("advsearch");
else $advsearch = $vars['advsearch'];

if ($vars == NULL || empty($vars['version'])) $version = getParameter("version");
else $version = $vars['version'];

if (($deleteversion==1) || ($viewnewversion==NULL && $version == NULL && $cmsid!=NULL)) {
   $tempvar = $ss->getFileByIdQuick($cmsid,$ss->getCurrentTheme());
   $version = $tempvar['version'];
}

$directories=NULL;
$newdirectory=false;

$sepDirs = separateStringBy($curdir,"/");
if ($sepDirs != NULL && count($sepDirs)>0) {
   $linkToDirs = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showversionfiles&orderby=".$orderby."&theme=".$theme."&advsearch=".$advsearch."&searchstr=".$searchstr."\">root/</a>";
   $tempDir = "";
   for ($i=0; $i<(count($sepDirs)-1); $i++) {
      $tempDir .= $sepDirs[$i]."/";
      $linkToDirs .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showversionfiles&orderby=".$orderby."&curdir=".$tempDir."&theme=".$theme."&advsearch=".$advsearch."&searchstr=".$searchstr."\">".$sepDirs[$i]."/</a>";
   }
   $linkToDirs .= $sepDirs[count($sepDirs)-1]."/";
}
else $linkToDirs = "Home/";
?>

<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>rte/js/richtext.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>rte/js/config.js" type="text/javascript" language="javascript"></script>

<?php

print "<center>";
$files = NULL;
if ($advsearch==1) {
   //$files = $ss->advancedSearchFiles($orderby,$searchstr,$searchstr,$searchstr,$searchstr,$searchstr,$searchstr,$searchstr,$theme,$searchstr,"OR");
   $files = $ss->searchFiles(NULL,$searchstr,NULL,NULL,"AND",$orderby);
   if ($theme > 0) {
      $themeobj = $ss->getThemeById($theme);
      $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showversionfiles&orderby=".$orderby."&curdir=".$curdir."&cmsid=".$cmsid."&version=".$version."&advsearch=".$advsearch."&searchstr=".$searchstr."\">";
      $link .= "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/close_btn.gif\" border=\"0\"> ";
      $link .= "</a> ";
      print "<b>Content for Theme '".$themeobj['themename']."'</b>".$link."<BR>";
   }
}
else {
   $files = $ss->searchFiles($curdir,$searchstr,NULL,NULL,"AND",$orderby);
   //$files = $ss->searchFiles($orderby,$curdir,$searchstr,$searchstr,$searchstr,"OR");
   $directories = $template->list_dir($GLOBALS['rootDir'].$GLOBALS['contentDir'].$curdir, false);
   $newdirectory=true;
}
if ($searchstr != NULL) {
   $link = "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showversionfiles&orderby=".$orderby."&curdir=".$curdir."&cmsid=".$cmsid."&version=".$version."&advsearch=".$advsearch."&theme=".$theme."\">";
   $link .= "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/close_btn.gif\" border=\"0\"> ";
   $link .= "</a> ";
   print " <b>Search string: '".$searchstr."'</b>".$link."<BR>";
}
print "</center>";


      // Create consistency for URL management
      $quickURL = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=showversionfiles";
      $contextURL = $quickURL."&advsearch=".$advsearch."&curdir=".$curdir."&theme=".$theme."&searchstr=".$searchstr."&orderby=".$orderby."&cmsid=".$cmsid."&version=".$version;


?>

<table width="990" cellpadding="5" cellspacing="0" border="0">
<TR align="left" valign="top">
<TD width="20%" bgcolor="#DDDDDD">
   
   <table width="100%"cellpadding="0" cellspacing="0" border="0">
<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
   <form name="newfile1" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="viewuploadfile" value="1">
   <tr><td><input type="submit" name="Upload File" value="Upload Content"></td></tr>
   </form>
   <form name="newfile2" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="viewaddhtmlfile" value="1">
   <tr><td><input type="submit" name="New Template" value="New HTML Page"></td></tr>
   </form>
   <form name="newfile4" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="viewaddemailtemplate" value="1">
   <tr><td><input type="submit" name="submit" value="New Email Template"></td></tr>
   </form>
   <form name="newfile4b" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="viewaddhtmlemailtemplate" value="1">
   <tr><td><input type="submit" name="submit" value="HTML Email Template"></td></tr>
   </form>
   <form name="newfile3" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="viewaddsnpfile" value="1">
   <tr><td><input type="submit" name="New Snippet" value="New Snippet"></td></tr>
   </form>
<?php } ?>
   <tr><td><br></td></tr>
   <form name="searchfiles" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <tr><td>Search <input type="text" size="5" name="searchstr" value="<?php echo $searchstr; ?>"></td></tr>
   <tr><td>Order by <?php echo $ss->getOrderByOptions($orderby); ?></td></tr>
   <tr><td><input type="submit" name="submit" value="Go"><!--input type="submit" name="submit" value="Advanced"--></td></tr>
   </form>
   </table>

   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="0" border="0" class="tinytext">
<?php if ($newdirectory) { ?>
   <tr><td>Current Dir: /<?php echo $linkToDirs; ?></td></tr>
   <tr><td><BR></td></tr>
<?php
   }

       for ($i=0; $i<count($files); $i++) {
         if ($i==0) print "<TR><TD>Files:</td></tr>";
         $file = $files[$i];
?>      
   <tr class="tinytext"><td>
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir; ?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&cmsid=<?php echo $file['cmsid']; ?>">
      <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/doc1.gif" border=0">
      <?php echo $file['filename'].$file['extension']; ?>
      </a>
   </td></tr>
<?php
       }
       if ($files != NULL && count($files)>0) {
?>      
   <tr><td><BR></td></tr>
<?php
       }

       for ($i=0; $i<count($directories); $i++) {
         if ($i==0) print "<TR><TD>Subdirectories:</td></tr>";
         $directory = $directories[$i];
?>      
   <tr><td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr valign="top"><td align="left">
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir.$directory; ?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>">
      <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/folder1.gif" border=0">
      <?php echo $directory; ?>
      </a>
      </td><td align="right">
<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir;?>&deletedirectory=1&deldir=<?php echo $curdir.$directory; ?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $version; ?>">
      <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/delete.png" border=0">
      </a>
<?php } ?>
      </td></tr></table>
   </td></tr>
<?php
       }
?>
   <tr><td><BR></td></tr>
<?php
      if ($newdirectory && $ua->doesUserHaveAccessToLevel(isLoggedOn(),3))
 { ?>
   <form name="newcmsdirectory" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newdirectory" value="1">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <tr><td>New subdirectory:</td></tr>
   <tr><td><input type="text" size="5" name="newdir" value=""></td></tr>
   <tr><td><input type="submit" name="submit" value="Create"></td></tr>
   </form>
<?php } ?>      
   <tr><td><BR></td></tr>
   </table>
   </td></tr>
   </table>
</td>

<TD width="90%">
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir; ?>&theme=<?php echo $theme; ?>&searchstr=<?php echo $searchstr; ?>">
List files in current directory
</a> &nbsp;&nbsp;|&nbsp;&nbsp; 
<a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir; ?>&theme=<?php echo $theme; ?>&searchstr=<?php echo $searchstr; ?>&advsearch=1">
List all system content
</a>

<?php 
   $fileTypeArr['Unknown']="";
   $fileTypeArr['HTML Template']="1";
   $fileTypeArr['Internal Snippet']="2";
   $fileTypeArr['Image']="3";
   $fileTypeArr['Document']="4";
   $fileTypeArr['Email Template (txt)']="5";
   $fileTypeArr['Email Template (html)']="6";

//--------------------------------------------------------------------
// Main Portion of the screen below conditional
//--------------------------------------------------------------------

   if ($viewuploadfile == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <tr><td bgcolor="#CCCCCC">Unique Shortname</td><td><input type="text" size="40" name="filename" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Short Description</td><td><input type="text" size="40" name="metadescr" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Key Words</td><td><input type="text" size="40" name="metakw" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Search</td><td><textarea cols="35" rows="5" name="search"></textarea></td></tr>
   <tr><td bgcolor="#CCCCCC">Admin Notes</td><td><input type="text" size="40" name="adminnotes" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Content Type</td><td><?php echo getOptionList("contenttype", $fileTypeArr); ?></td></tr>
   <tr><td bgcolor="#CCCCCC">File Upload</td><td><input name="userfile" type="file"></td></tr>
   <tr><td colspan="2">
      <input type="submit" name="Add New Content" value="Add New Content">
   </td></tr>
   </form>
   </table>
   </td></tr>
   </table>

<?php
   } else if ($viewaddhtmlfile == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype" value="1">
   <tr><td bgcolor="#CCCCCC">Unique Shortname</td><td><input type="text" size="40" name="filename" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Short Description</td><td><input type="text" size="40" name="metadescr" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Key Words</td><td><input type="text" size="40" name="metakw" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Search</td><td><textarea cols="35" rows="5" name="search"></textarea></td></tr>
   <tr><td bgcolor="#CCCCCC">Admin Notes</td><td><input type="text" size="40" name="adminnotes" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC" colspan="2">New File:</td></tr>
   <!--tr><td colspan="2"><textarea cols="70" rows="35" name="contents"></textarea></td></tr-->
   <tr><td colspan="2"><script>initRTE('', 'rte/html/example.css');</script></td></tr>
   <tr><td colspan="2">
      <input type="submit" name="Add New Content" value="Add New Content">
   </td></tr>
   </form>
   </table>
   </td></tr>
   </table>
<?php
   } else if ($viewaddemailtemplate == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype" value="5">
   <tr><td bgcolor="#CCCCCC">Unique Shortname</td><td><input type="text" size="40" name="filename" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Email Subject</td><td><input type="text" size="40" name="metadescr" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Admin Notes</td><td><input type="text" size="40" name="adminnotes" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC" colspan="2">New File:</td></tr>
   <tr><td colspan="2"><textarea cols="70" rows="35" name="contents"></textarea></td></tr>
   <tr><td colspan="2">
      <input type="submit" name="Add New Content" value="Add New Content">
   </td></tr>
   </form>
   </table>
   </td></tr>
   </table>
<?php
   } else if ($viewaddhtmlemailtemplate == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype" value="6">
   <tr><td bgcolor="#CCCCCC">Unique Shortname</td><td><input type="text" size="40" name="filename" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Email Subject</td><td><input type="text" size="40" name="metadescr" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Admin Notes</td><td><input type="text" size="40" name="adminnotes" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC" colspan="2">New File:</td></tr>
   <tr><td colspan="2"><textarea cols="70" rows="35" name="contents"></textarea></td></tr>
   <tr><td colspan="2">
      <input type="submit" name="Add New Content" value="Add New Content">
   </td></tr>
   </form>
   </table>
   </td></tr>
   </table>
<?php
   } else if ($viewaddsnpfile == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype" value="2">
   <tr><td bgcolor="#CCCCCC">Unique Shortname</td><td><input type="text" size="40" name="filename" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Short Description</td><td><input type="text" size="40" name="metadescr" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC">Admin Notes</td><td><input type="text" size="40" name="adminnotes" value=""></td></tr>
   <tr><td bgcolor="#CCCCCC" colspan="2">New File:</td></tr>
   <tr><td colspan="2"><script>initRTE('', 'rte/html/example.css');</script>
   <tr><td colspan="2">
      <input type="submit" name="Add New Content" value="Add New Content">
   </td></tr>
   </form>
   </table>
   </td></tr>
   </table>

<?php
   }
   else if ($cmsid != NULL) { 
      $cmsfile = $ss->getFileById($cmsid);
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
      $accessOpt["All Administrators"]=-1;
      $accessOpt["Super Administrators"]=-2;
      $filePermissionOptions = getOptionList("privacy", $accessOpt, $cmsfile['privacy']);
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <tr><td colspan="2">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
         <td><b>Unique Content Shortname: <?php echo $cmsfile['filename'];?></b></td>
         <td>%%%CMS_<?php echo $cmsfile['filename'];?>_CMS%%%</td>
      </tr>
      <!-- tr>
         <td colspan="2">Public access URL: <?php echo $GLOBALS['baseURL'].$GLOBALS['codeFolder']; ?>controller.php?action=publicviewversion&cmsid=<?php echo $cmsfile['cmsid']; ?>&rdr=<?php echo $cmsfile['xmp_full']; ?></td>
      </tr-->
      </table>
   </td></tr>

   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrol" method="POST">
   <input type="hidden" name="action" value="showversionfiles">
   <input type="hidden" name="updatefile" value="1">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <tr><td bgcolor="#CCCCCC">Location</td><td><?php echo $ss->getDirOptions($cmsfile['dir'],"movedir"); ?></td></tr>
   <tr><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value="<?php echo $cmsfile['title'];?>"></td></tr>
   <tr><td bgcolor="#CCCCCC">File Type</td><td><?php echo $ss->getTypeOptions($cmsfile['filetype']);?></td></tr>
   <tr><td bgcolor="#CCCCCC">Content Type</td><td><?php echo getOptionList("contenttype", $fileTypeArr,$cmsfile['contenttype']); ?></td></tr>
   <tr><td bgcolor="#CCCCCC">Access Level</td><td><?php echo $filePermissionOptions;?></td></tr>

   <tr><td bgcolor="#CCCCCC">Track this content</td><td><input type="checkbox" name="track" value="1" <?php if($cmsfile['track']==1) echo "CHECKED"; ?>></td></tr>
 <?php if (0==strcmp($cmsfile['filetype'],"TEXT") || 0==strcmp($cmsfile['filetype'],"DESIGN")) { ?>
   <tr><td bgcolor="#CCCCCC">Seconds this page is cached</td><td><input type="text" size="40" name="cachetime" value="<?php echo $cmsfile['cachetime'];?>"></td></tr>
 <?php } ?>

 <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3) || $ua->isUserAccessible(isLoggedOn(),"CMS",$cmsid)) { ?>
   <tr><td colspan="2">
      <input type="submit" name="updatestr" value="Update Header Info"> &nbsp;&nbsp;
 <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
      <input type="submit" name="updatestr" value="Delete This File">
<?php } ?>
   </td></tr>
<?php } ?>
   </form>
   </table>
   </td></tr>
   </table>

      <script language="javascript">
      function expandSection(c,s) {
        if (document.getElementById(c).checked==true) {
            document.getElementById(s).style.display = "";
        } else {
            document.getElementById(s).style.display = "none";
        }
      }
      </script>


<!--User Access Code below-->
<?php 
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) {
         $divstyle = "style=\"display: none;\"";
         if (0==strcmp($vars['defaultusersection'],"CHECKED")) $divstyle = "style=\"\"";
?>
   <BR>
   <input id="useraccesstbl_cb" type="checkbox" onclick="javascript: expandSection('useraccesstbl_cb','useraccesstbl');" <?php echo $vars['defaultusersection']; ?>>Show User Information
   <table bgcolor="WHITE" border="0" cellpadding="0" cellspacing="0" id="useraccesstbl" <?php echo $divstyle; ?>>
   <TR align="left" valign="top"><TD>
         <table bgcolor="BLUE" border="0" cellpadding="1" cellspacing="0"><TR><TD>
         <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0" align="left" valign="top">
         <tr align="left" valign="top"><td colspan="2"><b>Users that have Access to this content</b></td></tr>
   <?php
         //$accessUsers = $ua->usersAccessible("CMS",$cmsid);
         for ($i=0; $i<count($accessUsers); $i++) {
   ?>
   
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyAccess" method="POST">
         <input type="hidden" name="removeaccess" value="1">
         <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
         <input type="hidden" name="email" value="<?php echo $accessUsers[$i]['email']; ?>">
         <input type="hidden" name="userid" value="<?php echo $accessUsers[$i]['userid']; ?>">
         <input type="hidden" name="action" value="showversionfiles">
         <input type="hidden" name="version" value="<?php echo $version; ?>">
         <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
         <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
         <input type="hidden" name="theme" value="<?php echo $theme; ?>">
         <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
             <tr>
             <td><?php echo $accessUsers[$i]['email']; ?></td><TD><input type="submit" name="remove" value="remove -->"></td>
             </tr>
         </form>
   
   <?php
         }
   ?>
         </table>
         </td></tr></table>
   </td><td>
         <table bgcolor="BLUE" border="0" cellpadding="1" cellspacing="0"><TR><TD>
         <table bgcolor="WHITE" border="0" cellpadding="5" cellspacing="0" align="left" valign="top">
         <tr align="left" valign="top"><td colspan="2"><b>Users that do not have Access to this content</b></td></tr>
   <?php
   
         //$accessUsers = $ua->usersNotAccessible("CMS",$cmsid);
         for ($i=0; $i<count($accessUsers); $i++) {
            if (!$ua->doesUserHaveAccessToLevel($accessUsers[$i]['userid'],3)) {
   ?>
         <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="surveyAccess" method="POST">
         <input type="hidden" name="addaccess" value="1">
         <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
         <input type="hidden" name="email" value="<?php echo $accessUsers[$i]['email']; ?>">
         <input type="hidden" name="userid" value="<?php echo $accessUsers[$i]['userid']; ?>">
         <input type="hidden" name="action" value="showversionfiles">
         <input type="hidden" name="version" value="<?php echo $version; ?>">
         <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
         <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
         <input type="hidden" name="theme" value="<?php echo $theme; ?>">
         <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
             <tr>
               <TD><input type="submit" name="add" value="<-- add"></td><td><?php echo $accessUsers[$i]['email']; ?></td>
             </tr>
         </form>
   <?php 
            }
         }
   ?>
         </table>
         </td></tr></table>
   </td></tr>
   </table>
   <br><BR>
<?php } ?>
<!-- End User Access -->


   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
<?php if (($ua->doesUserHaveAccessToLevel(isLoggedOn(),3) || $ua->isUserAccessible(isLoggedOn(),"CMS",$cmsid)) && $viewnewversion!=1) { ?>
   <TR><TD colspan="6" align="center">
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&viewnewversion=1&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>">
      <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/newversion_btn.jpg" border="0">
      </a>
   </td></tr>
<?php } ?>
   <TR bgcolor="#DDDDDD"><TD><b>Version</b></TD><TD><b>Created</b></TD><TD><b>By</b></TD><TD><b>Site</b></TD><TD><b>Theme</b></TD><TD><b>Actions</b></TD></TR>
<?php
   //display all versions of this file...
   $versions = $ss->getAllVersions($cmsid);
   $default_new_title = $versions[(count($versions)-1)]['title'];
   $default_new_metakw = $versions[(count($versions)-1)]['metakw'];
   $default_new_metadescr = $versions[(count($versions)-1)]['metadescr'];
   $default_new_search = $versions[(count($versions)-1)]['search'];
   for ($i=0; $i<count($versions); $i++) {
      $versioninfo = $versions[$i];
      $themeobj = $ss->getThemeById($versioninfo['theme']);
      $themename = $themeobj['themename'];
      $siteObj = $ctx->getSiteInfo($versioninfo['siteid']);
      $sitename = $siteObj['name'];
      $bgcolor = $ss->getStatusColor($versioninfo['status']);
      $allowLinks = TRUE;
      
      // Uncomment below 4 lines if you want to prevent someone from editing a site-specific file
      //if ($versioninfo['siteid']!=$sitearr[0]['siteid']) {
      //   $bgcolor = "#EEEEEE";
      //   $allowLinks = FALSE;
      //}

      $view_icon="";
      if ($versioninfo['version'] == $version) $view_icon = "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/view.gif\">";
?>
   <tr bgcolor="<?php echo $bgcolor; ?>">
   <td><?php echo $versioninfo['version'].$view_icon; ?></td>
   <td><?php echo $versioninfo['created']; ?></td>
   <td><?php echo $versioninfo['owner']; ?></td>
   <td><?php echo $sitename; ?></td>
   <td><?php echo $themename; ?></td>
   <td>
<?php if ($allowLinks) { ?>
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&view=1&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">view</a> &nbsp;|&nbsp;
      <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&view=1&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>&viewtype=nonrte">code</a> &nbsp;|&nbsp;
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
         <?php if (0==strcmp($versioninfo['status'],"ACTIVE")) { ?>
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&statusupdate=INACTIVE&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">deactivate</a> 
         <?php } elseif (0==strcmp($versioninfo['status'],"NEW")) { ?>
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&deleteversion=1&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">delete</a>  &nbsp;|&nbsp;
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&statusupdate=ACTIVE&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } elseif (0==strcmp($versioninfo['status'],"INACTIVE")) { ?>
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&deleteversion=1&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">delete</a>  &nbsp;|&nbsp;
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&statusupdate=ACTIVE&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } else { ?>
         <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&statusupdate=ACTIVE&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&orderby=<?php echo $orderby; ?>&cmsid=<?php echo $cmsid; ?>&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } ?>
   <?php } ?>
<?php } ?>
   </td>
   </tr>
<?php
   }
?>
   </table>
   </td></tr>
   </table>

<?php
   //display file contents if version is specified
   if ($viewnewversion==1) {
?>
         <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
         <tr><td colspan="2"><BR><h3>New File Version</h3></td></tr>
         <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrol" method="POST">
         <input type="hidden" name="action" value="showversionfiles">
         <input type="hidden" name="newversion" value="1">
         <input type="hidden" name="edit" value="1">
         <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
         <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
         <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
         <input type="hidden" name="theme" value="<?php echo $theme; ?>">
         <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
         <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value="<?php echo $default_new_title; ?>"></td></tr>
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Description</td><td><input type="text" size="40" name="metadescr" value="<?php echo $default_new_metadescr; ?>"></td></tr>
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Key Words</td><td><input type="text" size="40" name="metakw" value="<?php echo $default_new_metakw; ?>"></td></tr>
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Search</td><td><textarea cols="35" rows="5" name="search"><?php echo $default_new_search; ?></textarea></td></tr>
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Version Notes</td><td><textarea cols="25" rows="5" name="adminnotes"></textarea></td></tr>
         <tr align="left" valign="top"><td bgcolor="#CCCCCC">Theme</td><td><?php echo $ss->getThemeOptions(0);?></td></tr>
<?php if (0==strcmp($cmsfile['filetype'],"BINARY")) { ?>
         <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
         <tr valign="top" align="left"><td bgcolor="#CCCCCC">Upload File</td><td><input name="userfile" type="file"></td></tr>
<?php } ?>
         <tr align="center" valign="top"><td colspan="2">
            <input type="submit" name="submit" value="Add New Version">
         </td></tr>
         </form>
         </table>
<?php   
   }
   else if ($version!=NULL) {
      $cmsfver = $ss->getFileVersion($cmsid,$version);
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
      <b>Viewing version: <?php echo $cmsfver['version']; ?></b> &nbsp;&nbsp;&nbsp;&nbsp;
      Status: <?php echo $cmsfver['status']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
      Last updated <?php echo $cmsfver['lastupdate']; ?> by <?php echo $cmsfver['lastupdateby']; ?>
      <br><a href="admincontroller.php?action=previewcontent&noPrint=1&shortname=<?php echo $cmsfver['filename']; ?>&version=<?php echo $cmsfver['version']; ?>" target="_new">Preview this version</a>
      <BR>
      <BR>
<?php
      $filename = $GLOBALS['baseURLSSL'].$ss->createURL($cmsfver);
      $textfilename = $GLOBALS['rootDir'].$ss->createURL($cmsfver);
      if ($GLOBALS['printstuff']) print "*showversionfiles* filename: ".$filename.", textfilename: ".$textfilename."<BR>";
      $contents = convertBackHtml($template->getFileWithoutSub($textfilename));
      $disabled="";
      if (!$ss->isVersionEditable($cmsfver)) $disabled="DISABLED";
?>
      <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrol" method="POST">
      <input type="hidden" name="action" value="showversionfiles">
      <input type="hidden" name="editversion" value="1">
      <input type="hidden" name="status" value="<?php echo $cmsfver['status']; ?>">
      <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
      <input type="hidden" name="version" value="<?php echo $version; ?>">
      <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
      <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
      <input type="hidden" name="theme" value="<?php echo $theme; ?>">
      <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
      <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
<?php if ($ss->isVersionHeaderEditable($cmsfver)) { ?>
      <TR align="center"><td colspan="2"><input type="submit" name="submit" value="Save Version <?php echo $cmsfver['version']; ?> Header Info"></td></tr>
<?php } ?>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value="<?php echo $cmsfver['title'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Description</td><td><input type="text" size="40" name="metadescr" value="<?php echo $cmsfver['metadescr'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Key Words</td><td><input type="text" size="40" name="metakw" value="<?php echo $cmsfver['metakw'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Search</td><td><textarea cols="35" rows="5" name="search"><?php echo $cmsfver['search'];?></textarea></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Version Notes</td><td><textarea cols="25" rows="5" name="adminnotes"><?php echo $cmsfver['adminnotes'];?></textarea></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Theme</td><td><?php echo $ss->getThemeOptions($cmsfver['theme']);?></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Site</td><td>
      <?php
         if ($ss->getValue("multisites")==1) {
            $opts = $ctx->getSiteOptions();
            $siteDropDown = getOptionList("vsiteid", $opts, $cmsfver['siteid'], FALSE);
            print $siteDropDown;
         }
      ?>
      </td></tr>
      </form>
      </table>
<?php
      $widgetname = $ss->getFileTypeObject($cmsfile['filetype']);
      $widgetClass = new $widgetname();
      $widgetInclude = $widgetClass->getAdminPHPInclude();
      include($widgetInclude);
?>
   </td></tr>
   </table>
   </td></tr>
   </table>
<?php

   }
  } else {
      //list file info if nothing else is available
?>
            <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
            <tr><td>
            <table width="100%" bgcolor="#DDDDDD" cellpadding="1" cellspacing="1" border="0">
         <?php

            if ($files == NULL || count($files)<1) { 
?>      
               <tr><td><BR></td></tr>
               <tr><td><BR></td></tr>
               <tr><td align="center">
                     <?php if ($newdirectory) { ?>
                     No content in this directory: '<?php echo $linkToDirs; ?>'.
                     <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
                     <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&viewuploadfile=1&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir;?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>">
                     Add Content
                     </a>
                     <?php } ?>
                     <?php } ?>
               </td></tr>
               <tr><td><BR></td></tr>
               <tr><td><BR></td></tr>
<?php 
            } 
            else {
?>      
               <tr><td><b>Shortname</b></td><td><b>Filename</b></td><td><b>Cache</b></td><td><b>title</b></td><td></td></tr>
<?php 
                for ($i=0; $i<count($files); $i++) {
                  $file = $files[$i];
                  $versionsOfFile = $ss->getAllVersions($file['cmsid']);
                  $statusArray['NEW'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
                  $statusArray['ACTIVE'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
                  $statusArray['INACTIVE'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
                  for ($j=0; $j<count($versionsOfFile); $j++) {
                     $statusArray[$versionsOfFile[$j]['status']] = "<td bgcolor=\"".$ss->getStatusColor($versionsOfFile[$j]['status'])."\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
                  }
                  $list_row = ($i % 2)+1;
                  //$shortdescr = $file['metadescr'];
                  //if (strlen($file['metadescr'])>25) $shortdescr=substr($file['metadescr'],0,22)."...";
         ?>      
            <tr class="list_row<?php echo $list_row; ?>">
            <td>
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir; ?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&cmsid=<?php echo $file['cmsid']; ?>">
               <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/doc1.gif" border=0">
               <?php echo $file['filename']; ?>
               </a>
            </td>
            <td>
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php?action=showversionfiles&orderby=<?php echo $orderby; ?>&curdir=<?php echo $curdir; ?>&theme=<?php echo $theme; ?>&advsearch=<?php echo $advsearch; ?>&searchstr=<?php echo $searchstr; ?>&cmsid=<?php echo $file['cmsid']; ?>">
               /<?php echo $file['dir'].$file['filename'].$file['extension']; ?>
               </a>
            </td>
            <td>
               <?php if($file['cachetime']>0) echo $file['cachetime']; ?>
            </td>
            <td>
               <a href="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']; ?>controller.php?view=<?php echo $file['filename']; ?>" target="_new">
               <?php echo $file['title']; ?>
               </a>
            </td>
            <td>
               <table cellpadding="0" cellspacing="0" border="0">
               <tr><td colspan="5" bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/pixel.gif" width="1" height="1"></td></tr>
               <tr>
                  <td bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/pixel.gif" width="1" height="1"></td>
                  <?php echo $statusArray['NEW']; ?>
                  <?php echo $statusArray['ACTIVE']; ?>
                  <?php echo $statusArray['INACTIVE']; ?>
                  <td bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/pixel.gif" width="1" height="1"></td>
               </tr>
               <tr><td colspan="5" bgcolor="#999999"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/pixel.gif" width="1" height="1"></td></tr>
               </table>
            </td>
            </tr>
         <?php
               } 
            }
?>      
            </table>
            </td></tr>
            </table>
<?php
  }
?>
</td>
</TR>

</table>
