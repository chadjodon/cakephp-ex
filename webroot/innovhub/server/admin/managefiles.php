<?php
//error_reporting(E_ALL);

$contenttype_view = getParameter("contenttype_view");
$contenttype = getParameter("contenttype");
//if ($contenttype_view==NULL) $contenttype_view=1;
$filetype_view = getParameter("filetype_view");
$filetype = getParameter("filetype");
$search_filetype = getParameter("search_filetype");
$search_filename = getParameter("search_filename");
$search_extension = getParameter("search_extension");

$ss = new Version();
$template = new Template();
$ctx = new Context();
$sitearr = $ctx->getSiteContext(); 

   $contentTypeArr = array();
   $contentTypeArr['']="";
   $contentTypeArr['HTML Template']="1";
   $contentTypeArr['Internal Snippet']="2";
   $contentTypeArr['Image']="3";
   $contentTypeArr['Document']="4";
   $contentTypeArr['Email Template (txt)']="5";
   $contentTypeArr['Email Template (html)']="6";

   $pagetitle = "All Your Content";
   if ($contenttype_view==1) $pagetitle = "Your Pages";
   else if (0==strcmp($filetype_view,"DESIGN")) $pagetitle = "Page Templates";
   else if ($contenttype_view==2) $pagetitle = "Page snippets";
   else if ($contenttype_view==3) $pagetitle = "Images";
   else if ($contenttype_view==4) $pagetitle = "Documents";
   else if ($contenttype_view==5 || $contenttype_view==6) $pagetitle = "Email Template";

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

$searchstr = getParameter("searchstr");

$htagfilter = getParameter("htagfilter");
$htagarray = separateStringBy($htagfilter,",");

$shownofiles = getParameter("shownofiles");
$htdisp = getParameter("htdisp");
if ($htdisp==1 && $htagfilter==NULL) $shownofiles=1;

$viewaddhtmlfile = getParameter("viewaddhtmlfile");
$viewaddemailtemplate = getParameter("viewaddemailtemplate");
$viewaddhtmlemailtemplate = getParameter("viewaddhtmlemailtemplate");
$viewaddsnpfile = getParameter("viewaddsnpfile");
$viewuploadfile = getParameter("viewuploadfile");
$viewnewfile = getParameter("viewnewfile");

$deleteversion = getParameter("deleteversion");
$status = getParameter("status");
$contents = getParameter("contents");

if ($vars == NULL || empty($vars['theme'])) $theme = getParameter("theme");
else $theme = $vars['theme'];
if ($theme==NULL) $theme="0";

if ($vars == NULL || empty($vars['version'])) $version = getParameter("version");
else $version = $vars['version'];


// Create consistency for URL management
$quickURL = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=managefiles&adminmid=".getParameter("adminmid")."&htdisp=".$htdisp."&i=".date("dHis");
$contextURL = $quickURL."&theme=".$theme."&contenttype_view=".$contenttype_view."&filetype_view=".$filetype_view;
$detailURL = $contextURL."&searchstr=".$searchstr."&htagfilter=".$htagfilter;
$detailContentURL = $detailURL."&contenttype=".$contenttype_view;
$fileURL = $detailURL."&cmsid=".$cmsid;
$nosearchURL = $contextURL."&cmsid=".$cmsid."&version=".$version."&htagfilter=".$htagfilter;
$fullURL = $nosearchURL."&searchstr=".$searchstr;
$pagetitle = "<a href=\"".$contextURL."\">".$pagetitle."</a>";

$default_new_title = "";
$default_new_metakw = "";
$default_new_metadescr = "";
$default_new_search = "";
if (($deleteversion==1) || ($version == NULL && $cmsid!=NULL)) {
   //$tempvar = $ss->getFileByIdQuick($cmsid,$ss->getCurrentTheme());
   //$version = $tempvar['version'];
   $versions = $ss->getAllVersions($cmsid);
   $version = $versions[(count($versions)-1)]['version'];
   $default_new_title = $versions[(count($versions)-1)]['title'];
   $default_new_metakw = $versions[(count($versions)-1)]['metakw'];
   $default_new_metadescr = $versions[(count($versions)-1)]['metadescr'];
   $default_new_search = $versions[(count($versions)-1)]['search'];
}

?>

<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>rte/js/richtext.js" type="text/javascript" language="javascript"></script>
<script src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>rte/js/config.js" type="text/javascript" language="javascript"></script>
<script language="javascript">
   function toggleDiv(e,c) {
     if (e.checked==true) {
         document.getElementById(c).style.display = "";
     } else {
         document.getElementById(c).style.display = "none";
     }
   }
</script>

<?php

$files = $ss->searchFiles(NULL,$searchstr,$search_filetype,$contenttype_view,"AND",NULL,10000,getParameter("page"),$search_filename,$search_extension,$htagfilter);
if ($searchstr != NULL) {
   $link = "<a href=\"".$nosearchURL."\">";
   $link .= "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/close_btn.gif\" border=\"0\"> ";
   $link .= "</a> ";
   print " <b>Search string: '".$searchstr."'</b>".$link."<BR>";
}

if ($htagfilter!= NULL && count($htagarray)>0) {
   print " <b>Viewing folders:</b> ";
   for ($i=0;$i<count($htagarray);$i++) {
      $t = trim($htagarray[$i]);
      if ($t!=NULL) {
         $newurl = str_replace(",".$t,"",$detailURL);
         $newurl = str_replace($t,"",$newurl);
         $newurl = str_replace(",,",",",$newurl);
         print " ".$t." ";
         print "<a href=\"".$newurl."\">";
         print "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/close_btn.gif\" border=\"0\">";
         print "</a>";
         print " &nbsp; &nbsp; ";
      }
   }
   print "<br>";
}

?>

<table cellpadding="5" cellspacing="0" border="0">
<TR align="left" valign="top">
<TD width="180" bgcolor="#DDDDDD">
   
   <table cellpadding="3" cellspacing="0" border="0">

   <form name="searchfiles" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
   <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">
   <tr>
      <td><input type="text" style="font-size:12px;font-family:verdana;color:#222222;width:100px;" name="searchstr" value="<?php echo $searchstr; ?>"></td>
      <td><input type="submit" style="font-size:12px;font-family:verdana;color:#111111;width:70px;" name="submit" value="Search"></td>
   </tr>
   </form>
   </table>
   <br>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="0" border="0" class="tinytext">
   <tr class="normal"><td colspan="2">
      <?php echo $pagetitle; ?>
   </td></tr>
   <tr><td colspan="2"><br></td></tr>
   <tr class="tinytext"><td colspan="2">
      <a href="<?php echo $detailContentURL; ?>&viewnewfile=1">
      <img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/add.png" border=0">
      New
      </a>
   </td></tr>

<?php
      $hashtagcounts = array();
      $htmlstr = "<tr class=\"tinytext\"><td colspan=\"2\"><br></td></tr>\n";
       for ($i=0; $i<count($files); $i++) {
         $file = $files[$i];
         $temp_ht = separateStringBy($file['htags']," ");
         for ($j=0;$j<count($temp_ht);$j++) {
            $t = trim($temp_ht[$j]);
            if ($t!=NULL) {
               if (isset($hashtagcounts[$t])) $hashtagcounts[$t]++; 
               else $hashtagcounts[$t]=1;
            }
         }

         if ($shownofiles!=1) {
            $versionsOfFile = $ss->getAllVersions($file['cmsid']);
            $statusArray = array();
            $statusArray['NEW'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
            $statusArray['ACTIVE'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
            $statusArray['INACTIVE'] = "<td bgcolor=\"#FFFFFF\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
            for ($j=0; $j<count($versionsOfFile); $j++) {
               $statusArray[$versionsOfFile[$j]['status']] = "<td bgcolor=\"".$ss->getStatusColor($versionsOfFile[$j]['status'])."\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"8\" height=\"8\"></td>";
            }
            
            $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/empty.png";
            if (0==strcmp($file['filetype'],"DESIGN")) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/template.png";
            else if (0==strcmp($file['filetype'],"TMPLT")) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/template2.png";
            else if ($file['contenttype']==1) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/doc.png";
            else if ($file['contenttype']==3) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/img.png";
            else if (0==strcmp($file['filetype'],"TEXT")) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/txtdoc.png";
            else if (0==strcmp($file['filetype'],"SURVEY")) $imageurl = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/survey.png";
            else if (0==strcmp($file['filetype'],"BINARY") && (0==strcmp(strtolower($file['extension']),".ppt") || 0==strcmp(strtolower($file['extension']),"pptx"))) $imageurl = $GLOBALS['baseURLSSL']."jsfimages/ppt.png";
            else if (0==strcmp($file['filetype'],"BINARY") && (0==strcmp(strtolower($file['extension']),".doc") || 0==strcmp(strtolower($file['extension']),"docx"))) $imageurl = $GLOBALS['baseURLSSL']."jsfimages/doc.png";
            else if (0==strcmp($file['filetype'],"BINARY") && 0==strcmp(strtolower($file['extension']),".pdf")) $imageurl = $GLOBALS['baseURLSSL']."jsfimages/pdf.png";

            $htmlstr .= "<tr class=\"tinytext\">\n";
            $htmlstr .= "      <td align=\"left\"";
            if ($file['cmsid']==$cmsid) $htmlstr .= " bgcolor=\"#DDDDDD\"";
            $htmlstr .= ">\n";
            $htmlstr .= "         <a href=\"".$detailURL."&cmsid=".$file['cmsid']."\">\n";
            $htmlstr .= "         <img src=\"".$imageurl."\" border=0\">\n";
            $htmlstr .= $file['filename'];
            $htmlstr .= "         </a>\n";
            $htmlstr .= "      </td>\n";
            $htmlstr .= "      <td align=\"right\">\n";
            $htmlstr .= "               <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";
            $htmlstr .= "               <tr><td colspan=\"5\" bgcolor=\"#999999\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"1\" height=\"1\"></td></tr>\n";
            $htmlstr .= "               <tr>\n";
            $htmlstr .= "                  <td bgcolor=\"#999999\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"1\" height=\"1\"></td>\n";
            $htmlstr .= $statusArray['NEW'];
            $htmlstr .= $statusArray['ACTIVE'];
            $htmlstr .= $statusArray['INACTIVE'];
            $htmlstr .= "                  <td bgcolor=\"#999999\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"1\" height=\"1\"></td>\n";
            $htmlstr .= "               </tr>\n";
            $htmlstr .= "               <tr><td colspan=\"5\" bgcolor=\"#999999\"><img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/pixel.gif\" width=\"1\" height=\"1\"></td></tr>\n";
            $htmlstr .= "               </table>\n";
            $htmlstr .= "      </td>\n";
            $htmlstr .= "   </tr>\n";

         }
      }

      //Get Display for Folders...
      $folderstr = "<tr class=\"tinytext\"><td colspan=\"2\"><br></td></tr>\n";
      //Now show hashtags to help filter the results
      arsort($hashtagcounts);
      $htcount = 0;
      foreach($hashtagcounts as $key => $val){
         //if ($htcount>9) break 1;
         if ($htcount>49) break 1;
         $intagnow = FALSE;
         for ($i=0;$i<count($htagarray);$i++) {
            if (0==strcmp(substr($key,1),$htagarray[$i])) {
               $intagnow=TRUE;
               break 1;
            }
         }

         if (!$intagnow) {
            $folderstr .= "   <tr class=\"tinytext\">\n";
            $folderstr .= "      <td colspan=\"2\">\n";
            $folderstr .= "      <a href=\"".$detailURL.",".substr($key,1)."\">\n";
            $folderstr .= "      <img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/folder1.gif\" border=0\">\n";
            $folderstr .= substr($key,1);
            $folderstr .= "      </a>\n";
            $folderstr .= "      </td>\n";
            $folderstr .= "   </tr>\n";
            $htcount++;
         }
      }

   print $folderstr;
   print $htmlstr;
   //print $folderstr;
?>


   <tr><td colspan="2"><BR></td></tr>
   </table>
   </td></tr>
   </table>
</td>

<TD>
<div style="width:800px;">
<?php 

//--------------------------------------------------------------------
// Main Portion of the screen below conditional
//--------------------------------------------------------------------

   if ($viewnewfile == 1) {
      $y = 10;
?>
   <div style="position:relative;width:600px;height:200px;font-size:14px;font-family:verdana;">
   <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfileform" method="POST">
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="contenttype" value="<?php echo $contenttype_view; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
   <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="contents" value=" ">

   <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:200px;height:20px;">Unique Shortname</div>
   <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:305px;height:20px;"><input type="text" style="font-size:14px;font-family:verdana;width:300px;" name="filename" value=""></div>
   <?php $y += 25; ?>

   <!--
   <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:200px;height:20px;">File Type</div>
   <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:305px;height:20px;"><?php echo $ss->getTypeOptions($filetype_view); ?></div>
   -->
   <?php $y += 0; ?>

   <?php if ($contenttype_view==NULL || $contenttype_view==1) { ?>
      <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:200px;height:20px;">Title</div>
      <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:305px;height:20px;"><input type="text" style="font-size:14px;font-family:verdana;width:300px;" name="title" value=""></div>
      <?php $y += 25; ?>

      <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:200px;height:20px;">Short description</div>
      <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:305px;height:20px;"><input type="text" style="font-size:14px;font-family:verdana;width:300px;" name="metadescr" value=""></div>
      <?php $y += 25; ?>
   <?php } ?>

   <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:200px;height:20px;">Notes</div>
   <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:305px;height:20px;"><input type="text" style="font-size:14px;font-family:verdana;width:300px;" name="adminnotes" value=""></div>
   <?php $y += 25; ?>

   <?php if (0!=strcmp($filetype_view,"DESIGN")) { ?>
      <div style="position:absolute;top:<?php echo $y; ?>px;left:10px;width:180px;height:20px;"><input type="checkbox" name="filetoggle" value="1" onClick="toggleDiv(this,'fileuploaddiv');">Upload a file</div>
      <div id="fileuploaddiv" style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:100px;height:20px;display:none;">
         <input name="userfile" type="file">
      </div>
      <?php $y += 25; ?>
   <?php } ?>

   <div style="position:absolute;top:<?php echo $y; ?>px;left:215px;width:180px;height:20px;">
      <input type="submit" name="Add New Content" value="Add New Content" style="font-size:14px;font-family:verdana;">
   </div>
   </form>
   </div>

<?php
   } else if ($viewaddhtmlfile == 1) {
?>
   <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
   <tr><td>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newfile" method="POST">
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="curdir" value="main/">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
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
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="main/">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
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
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="main/">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
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
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="newfile" value="1">
   <input type="hidden" name="extension" value=".html">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
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
   } else if ($cmsid != NULL) {
   //-----------------------------------------------------
   //-- START view file...
   //-----------------------------------------------------




      $cmsfile = $ss->getFileById($cmsid);
      $versions = $ss->getAllVersions($cmsid);

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

   <div style="width:100%;height:5px;"></div>
   <div style="background-color:#777777;width:100%;height:1px;overflow:hidden;"></div>
   <div style="width:100%;height:5px;"></div>
   <div style="position:relative;width:100%;height:25px;">
      <div style="color:#333333;font-size:20px;font-weight:bold;font-face:verdana;width:50%;float:left;">
      <?php echo $cmsfile['filename'];?>
      </div>
<?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3) || $ua->isUserAccessible(isLoggedOn(),"CMS",$cmsid)) { ?>
      <div style="float:left;text-align:right;width:50%;">

<?php
$newversionURL = $fullURL."&newversion=1";
$newversionURL .= "&title=".urlencode($default_new_title);
$newversionURL .= "&metakw=".urlencode($default_new_metakw);
$newversionURL .= "&metadescr=".urlencode($default_new_metadescr);
$newversionURL .= "&search=".urlencode($default_new_search);
?>
      <input type="button" name="newversion" value="Create new version" style="background-color:#1396db;font-size:14px;font-family:verdana;font-weight:bold;" onclick="location.href='<?php echo $newversionURL; ?>';">

      </div>
<?php } ?>
   </div>
   <div style="width:100%;height:5px;"></div>
   <div style="background-color:#777777;width:100%;height:1px;overflow:hidden;"></div>
   <div style="width:100%;height:5px;"></div>


   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <TR class="list_rowlabel"><TD><b>Version</b></TD><TD><b>Created</b></TD><TD><b>By</b></TD><TD><b>Site</b></TD><TD><b>Theme</b></TD><TD><b>Actions</b></TD></TR>
<?php
   //display all versions of this file...
   if ($version==NULL) $version = $versions[(count($versions)-1)]['version'];
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
      $rowclass = "list_row";
      if ($versioninfo['version'] == $version) {
         $view_icon = "<img src=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."images/glasses.png\">";
         $rowclass = "list_row_highlight";
      }
?>
   <tr class="<?php echo $rowclass; ?>" bgcolor="<?php echo $bgcolor; ?>">
   <td><?php echo $versioninfo['version'].$view_icon; ?></td>
   <td><?php echo date("m/d/Y H:i",strtotime($versioninfo['created'])); ?></td>
   <td><?php echo $versioninfo['owner']; ?></td>
   <td><?php echo $sitename; ?></td>
   <td><?php echo $themename; ?></td>
   <td>
<?php if ($allowLinks) { ?>
      <a href="<?php echo $fileURL; ?>&version=<?php echo $versioninfo['version']; ?>">view</a> &nbsp;|&nbsp;
      <a href="<?php echo $fileURL; ?>&version=<?php echo $versioninfo['version']; ?>&viewtype=nonrte">code</a> &nbsp;|&nbsp;
   <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) { ?>
         <?php if (0==strcmp($versioninfo['status'],"ACTIVE")) { ?>
         <a href="<?php echo $fileURL; ?>&statusupdate=INACTIVE&version=<?php echo $versioninfo['version']; ?>">deactivate</a> 
         <?php } elseif (0==strcmp($versioninfo['status'],"NEW")) { ?>
         <a href="<?php echo $fileURL; ?>&deleteversion=1&version=<?php echo $versioninfo['version']; ?>">delete</a>  &nbsp;|&nbsp;
         <a href="<?php echo $fileURL; ?>&statusupdate=ACTIVE&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } elseif (0==strcmp($versioninfo['status'],"INACTIVE")) { ?>
         <a href="<?php echo $fileURL; ?>&deleteversion=1&version=<?php echo $versioninfo['version']; ?>">delete</a>  &nbsp;|&nbsp;
         <a href="<?php echo $fileURL; ?>&statusupdate=ACTIVE&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } else { ?>
         <a href="<?php echo $fileURL; ?>&statusupdate=ACTIVE&version=<?php echo $versioninfo['version']; ?>">activate</a> 
         <?php } ?>
   <?php } ?>
<?php } ?>
   </td>
   </tr>
<?php
   }
?>
   </table>

<div style="position:relative;width:100%;height:15px;"></div>

<?php
   $tabsArr = array();

   $acmstab = getParameter("acmstab");
   if ($acmstab==NULL) $acmstab = "acmst1";
   if (count($versions)<1) {
      $acmstab = "acmst3";
      $tabsArr[0]['id']="acmst3";
      $tabsArr[0]['name']="Content Information";
   } else {
      $tabsArr[0]['id']="acmst1";
      $tabsArr[0]['name']="Content";
      $tabsArr[1]['id']="acmst2";
      $tabsArr[1]['name']="Content Info";
      $tabsArr[2]['id']="acmst4";
      $tabsArr[2]['name']="Hashtags";
      $tabsArr[3]['id']="acmst3";
      $tabsArr[3]['name']="Advanced";
   }

   $tabbedbar = getTabs($tabsArr,$acmstab,"admbtn1","admbtn2");
   print $tabbedbar['javascript'];
   print $tabbedbar['links'];
?>

<div style="position:relative;top:-2px;background-color:#777777;width:100%;height:3px;overflow:hidden;"></div>
<div style="width:100%;height:5px;"></div>



<div id="acmst4" <?php if (0!=strcmp($acmstab,"acmst4")) echo "style=\"display:none;\""; ?>>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <tr><td colspan="2">
      Current Hashtags:
      <div style="position:relative;">
      <?php
         $htags = $ss->getHashTags($cmsfile['cmsid']);
         foreach($htags as $key => $val) {
            $ht = preg_replace("/[^A-Za-z0-9_-]/",'',$val);
            print "<div style=\"float:left;\">";
            print "<table cellpadding=\"1\" cellspacing=\"1\" style=\"font-size:12px;font-family:arial;\"><tr>";
            print "<td>".$val."</td>";
            print "<td><a href=\"".$fullURL."&cmsdelhtag=1&hashtag=".$ht."&acmstab=acmst4\" onclick=\"return confirm('Are you sure you want to delete this tag?');\"><img src=\"".getBaseURL()."jsfimages/delete.png\" border=\"0\"></a></td>";
            print "</tr></table>";
            print "</div>";
            print "<div style=\"float:left;width:12px;height:12px;overflow:hidden;\"></div>";
         }
      ?>
      <div style="clear:both;"></div>
      </div>
   </td></tr>
   <tr><td colspan="2"><br></td></tr>
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrol" method="POST">
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="cmsaddhtag" value="1">
   <input type="hidden" name="acmstab" value="acmst4">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <tr>
      <td style="width:160px;"><input type="text" style="width:150px;" name="hashtag" value=""></td>
      <td><input type="submit" name="updateht" value="Add hashtag"></td>
   </tr>
   </form>
   </table>
</div>



<div id="acmst3" <?php if (0!=strcmp($acmstab,"acmst3")) echo "style=\"display:none;\""; ?>>
   <table width="100%" bgcolor="white" cellpadding="1" cellspacing="1" border="0">
   <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrol" method="POST">
   <input type="hidden" name="action" value="managefiles">
   <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
   <input type="hidden" name="updatefile" value="1">
   <input type="hidden" name="htdisp" value="<?php echo $htdisp; ?>">
   <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
   <input type="hidden" name="version" value="<?php echo $version; ?>">
   <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
   <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
   <input type="hidden" name="theme" value="<?php echo $theme; ?>">
   <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
   <input type="hidden" name="htagfilter" value="<?php echo $htagfilter; ?>">
   <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
   <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
   <input type="hidden" name="filetitle" value="<?php echo $cmsfile['title']; ?>">
   <tr><td bgcolor="#CCCCCC">File Type</td><td><?php echo $ss->getTypeOptions($cmsfile['filetype']);?></td></tr>
   <tr><td bgcolor="#CCCCCC">Content Type</td><td><?php echo getOptionList("contenttype", $contentTypeArr,$cmsfile['contenttype']); ?></td></tr>
   <tr><td bgcolor="#CCCCCC">Access Level</td><td><?php echo $filePermissionOptions;?></td></tr>
   <tr><td bgcolor="#CCCCCC">Track this content</td><td><input type="checkbox" name="track" value="1" <?php if($cmsfile['track']==1) echo "CHECKED"; ?>></td></tr>
 <?php if (0==strcmp($cmsfile['filetype'],"TEXT") || 0==strcmp($cmsfile['filetype'],"DESIGN")) { ?>
   <tr><td bgcolor="#CCCCCC">Seconds this page is cached</td><td><input type="text" style="width:50px;" name="cachetime" value="<?php echo $cmsfile['cachetime'];?>"></td></tr>
 <?php } ?>

<?php
   //display file contents if version is specified
   $cmsfver = NULL;
   if ($version!=NULL) {
      $cmsfver = $ss->getFileVersion($cmsid,$version);
      //print_r($cmsfver);
      $filename = $GLOBALS['baseURLSSL'].$ss->createURL($cmsfver);
      $textfilename = $GLOBALS['rootDir'].$ss->createURL($cmsfver);
      if ($GLOBALS['printstuff']) print "*managefiles* filename: ".$filename.", textfilename: ".$textfilename."<BR>";
      $contents = convertBackHtml($template->getFileWithoutSub($textfilename));
      $disabled="";
      if (!$ss->isVersionEditable($cmsfver)) $disabled="DISABLED";
?>
      <input type="hidden" name="status" value="<?php echo $cmsfver['status']; ?>">
      <input type="hidden" name="editversion" value="1">
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Title</td><td><input type="text" size="40" name="title" value="<?php echo $cmsfver['title'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Description</td><td><input type="text" size="40" name="metadescr" value="<?php echo $cmsfver['metadescr'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Key Words</td><td><input type="text" size="40" name="metakw" value="<?php echo $cmsfver['metakw'];?>"></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Search</td><td><textarea cols="45" rows="3" name="search"><?php echo $cmsfver['search'];?></textarea></td></tr>
      <tr align="left" valign="top"><td bgcolor="#CCCCCC">Version <?php echo $version; ?> Notes</td><td><textarea cols="45" rows="3" name="adminnotes"><?php echo $cmsfver['adminnotes'];?></textarea></td></tr>
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
<?php
   }
?>

      <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3) || $ua->isUserAccessible(isLoggedOn(),"CMS",$cmsid)) { ?>
         <tr><td colspan="2">
            <input type="submit" name="updatestr" value="Update Header Info"> &nbsp;&nbsp;
      <?php if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3) && ($versions==NULL || count($versions)<1)) { ?>
            <input type="submit" name="updatestr" value="Delete This File" onclick="return(confirm('Are you sure you wish to delete this file?'));">
      <?php } ?>
         </td></tr>
      <?php } ?>
      </form>
      </table>

</div>

<div id="acmst2" <?php if (0!=strcmp($acmstab,"acmst2")) echo "style=\"display:none;\""; ?>>
   <?php if ($version!=NULL) { ?>
      <table cellpadding="5" cellspacing="1" border="0" class="">
      <tr valign="top">
         <td colspan="2" style="font-size:12px;">
         You are currently viewing version <?php echo $cmsfver['version']; ?> of this content.  To view a different version, please select "view" from the version you're interested in above, or create a new version of this content.
         </td>
      </tr>
      <tr valign="top">
         <td><b><?php echo $cmsfile['filename'];?></b></td>
         <td>%%%CMS_<?php echo $cmsfile['filename'];?>_CMS%%%</td>
      </tr>
      <tr valign="top">
         <td colspan="2">
         <a href="admincontroller.php?action=previewcontent&noPrint=1&shortname=<?php echo $cmsfver['filename']; ?>&version=<?php echo $cmsfver['version']; ?>" target="_new">Preview version <?php echo $cmsfver['version']; ?></a>
         </td>
      </tr>
      <tr><td>Viewing version:</td><td><?php echo $cmsfver['version']; ?></b></td></tr>
      <tr><td>Status:</td><td><?php echo $cmsfver['status']; ?></td></tr>
      <tr><td>Created:</td><td> <?php echo date("m/d/Y H:i",strtotime($cmsfver['created'])); ?></td></tr>
      <tr><td>Last updated:</td><td> <?php echo date("m/d/Y H:i",strtotime($cmsfver['lastupdate'])); ?></td></tr>
      <tr><td>Last updated by:</td><td><?php echo $cmsfver['lastupdateby']; ?></td></tr>
      </table>
   <?php } ?>
</div>

<div id="acmst1" <?php if (0!=strcmp($acmstab,"acmst1")) echo "style=\"display:none;\""; ?>>
<?php
   //display file contents if version is specified
   if ($version!=NULL) {
      $widgetname = $ss->getFileTypeObject($cmsfile['filetype']);
      $widgetClass = new $widgetname();
      $widgetInclude = $widgetClass->getAdminPHPInclude();
      include($widgetInclude);
?>
</div>

<?php
     }

   //-----------------------------------------------------
   //-- END view file...
   //-----------------------------------------------------
   } else {
      //list file info if nothing else is available
?>
            <table width="100%" bgcolor="blue" cellpadding="1" cellspacing="0" border="0">
            <tr><td>
            <table width="100%" bgcolor="#DDDDDD" cellpadding="1" cellspacing="1" border="0">

         <?php if ($files != NULL && count($files)>0) { ?>      
               <tr>
                  <td colspan="2"><b>Search Content</b></td>
               </tr>

               <form name="searchfiles" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="POST">
               <input type="hidden" name="action" value="managefiles">
               <input type="hidden" name="adminmid" value="<?php echo getParameter("adminmid"); ?>">
               <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
               <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">
               <tr>
                  <td>File name: </td>
                  <td><input type="text" style="font-size:12px;font-family:verdana;color:#222222;width:100px;" name="search_filename" value=""></td>
               </tr>
               <tr>
                  <td>File extension: </td>
                  <td><input type="text" style="font-size:12px;font-family:verdana;color:#222222;width:100px;" name="search_extension" value=""></td>
               </tr>
               <tr>
                  <td>File Type</td>
                  <td><?php echo $ss->getTypeOptions($search_filetype,"search_filetype",TRUE);?></td>
               </tr>
               <tr>
                  <td colspan="2" align="right"><input type="submit" style="font-size:12px;font-family:verdana;color:#111111;width:70px;" name="submit" value="Search"></td>
               </tr>
               </form>

         <?php } ?>


            </table>
            </td></tr>
            </table>
<?php
  }
?>
</div>
</td>
</TR>

</table>
