<?php

   if(getParameter("testing")==1) $_SESSION['showdebug']=TRUE;
   //$_SESSION['showdebug']=TRUE;
   
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php start -->\n";

   $ua = new UserAcct;
   $ss = new Version;
   //error_reporting(E_ALL);
   if ($vars['segmentid']==NULL) $vars['segmentid']=getParameter("segmentid");

   $tempParams = $ua->searchUsersSQL();
   $getParams = $tempParams['getParams'];

   //first call to get count
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php getting count -->\n";
   $totalCount = $ua->getUsersForSegment(getParameter("segment"), $vars['segmentid'], NULL,NULL,NULL,TRUE);
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php count returned -->\n";
   $orderby = getParameter("orderby");
   $page = getParameter("page");
   $limit = getParameter("limit");
   if ($page==NULL) $page = 1;
   if ($limit == NULL) $limit = 25;
   $numPages = ceil($totalCount/$limit);

   //second call to get actual users
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php users being queried -->\n";
   $userSearchObj = $ua->getUsersForSegment(getParameter("segment"), $vars['segmentid'], $orderby, $page, $limit);
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php query: ".$userSearchObj['sql']." -->\n";
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php users returned -->\n";

   //$getParams = $userSearchObj['getParams'];
   //print "\n<!-- userSearchObj:\n";
   //print_r($getParams);
   //print "\n-->\n";
   $masterURL = "admincontroller.php?action=listuserscloning&segmentid=".$vars['segmentid'];
   if($_SESSION['showdebug']) $masterURL .= "&showdebug=1";
   for ($i=0; $i<count($getParams); $i++) $masterURL .= "&".$getParams[$i]['name']."=".$getParams[$i]['value'];

   $pageurl = $masterURL."&orderby=".$orderby."&limit=".$limit;
   $values = $userSearchObj['users'];
   $hiddenFields = $userSearchObj['hiddenFields'];

   if (class_exists('CustomUserDownload')) {
      $cud = new CustomUserDownload();
      $str = $cud->getUserDownloadHTML();
      $str = str_replace("%%%HIDDENFIELDS%%%",$hiddenFields,$str);
      print $str;
   } else {
      $cud = new DownloadUserData();
      $str = $cud->getUserDownloadHTML();
      $str = str_replace("%%%HIDDENFIELDS%%%",$hiddenFields,$str);
      print $str;
   }
?>


<!-- start facets + results table -->
<table border="0" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF"><tr valign="top">
   <td>

   <!-- left side facets -->
   <div style="padding:5px;border:1px solid #CCCCCC;border-radius:4px;font-size:10px;font-family:verdana;">
   <?php
   
      if ($userSearchObj['parentsegment']!=NULL && $userSearchObj['parentsegment']>0) {
     	   $savedsearch = $ua->getUserSegmentName($userSearchObj['parentsegment']);
         $tempURL = "admincontroller.php?action=listuserscloning";
         for ($j=0; $j<count($getParams); $j++) if($i!=$j) $tempURL .= "&".$getParams[$j]['name']."=".$getParams[$j]['value'];
         $tempURL .= "&orderby=".$orderby;
         print "<a href=\"".$tempURL."\" style=\"font-size:10px;font-family:verdana;\">";
         print "<img src=\"".getBaseURL()."jsfimages/delete.png\" border=\"0\" style=\"height:9px;width:auto;\">";
         print " ".$savedsearch;
         print "</a><br>";
      }
      
      for ($i=0; $i<count($getParams); $i++) {
         $tempURL = "admincontroller.php?action=listuserscloning&segmentid=".$vars['segmentid'];
         for ($j=0; $j<count($getParams); $j++) if($i!=$j) $tempURL .= "&".$getParams[$j]['name']."=".$getParams[$j]['value'];
         $tempURL .= "&orderby=".$orderby;
         print "<a href=\"".$tempURL."\" style=\"font-size:10px;font-family:verdana;\">";
         print "<img src=\"".getBaseURL()."jsfimages/delete.png\" border=\"0\" style=\"height:9px;width:auto;\">";
         if(strlen($getParams[$i]['display'])>32) print " ".substr($getParams[$i]['display'],0,32)."...";
         else print " ".$getParams[$i]['display'];
         print "</a><br>";
      }

      if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php adv cloning start -->\n";
      include "usersadvsearchcloning.php";
      if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php adv cloning end -->\n";
   ?>
   </div>

   </td>
   <td>
      <?php
         $users_table_include = $ss->getValue("users_table_include");
         if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php users table: ".$users_table_include." -->\n";
         if ($users_table_include!=NULL) {
            include $GLOBALS['baseDir'].$users_table_include;
         } else {
            include "listuserscloning_users.php";
         }
         if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." listusercloning.php end -->\n";
      ?>
   </td>
</tr></table>
<!-- end facets + results table -->


<?php unset($_SESSION['params']); ?>
