<?php
   $ua = new UserAcct;
   $uopts = $ua->getUserTypes();
   $list = "";
   foreach($uopts as $key => $value) {
      $list .= $value.", ";
   }
   
   $postToForm = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listusers";
   print "<form action=\"".$postToForm."\" method=\"post\">";
   //$str .= "<input type=\"hidden\" name=\"action\" value=\"listusers\">\n";
   print "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
   print "<TR><TD colspan=\"2\"><h2>Search For Users</h2></td></tr>\n";

   $ex = separateStringBy(getParameter("exceptions"),",");

   $renArr = separateStringBy(getParameter("renames"),",");
   $ren = array();
   for ($i=0;$i<count($renArr);$i++) if (($i%2)==0) $ren[$renArr[$i]]=$renArr[($i+1)];

   //function getSearchHTML($usertypes="user,org",$includeForm=TRUE,$selected=NULL,$postToForm=NULL,$exceptions=NULL,$renames=NULL,$includeCustom=FALSE)
   print $ua->getSearchHTML($list,FALSE,getParameter("s_usertype"),NULL,$ex,$ren);
   if (class_exists("CustomUserSegment")) {
      $customObj = new CustomUserSegment();
      print $customObj->getSearchParamsHTML();
   }

   print "<TR><TD colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Search\"></TD></TR>\n";
   print "</table>";
   print "</form>\n";


?>
