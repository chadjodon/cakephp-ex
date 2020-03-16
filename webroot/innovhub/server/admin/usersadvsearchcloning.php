<?php
   $ua = new UserAcct;
   $uopts = $ua->getUserTypes();
   $list = "";
   foreach($uopts as $key => $value) {
      $list .= $value.", ";
   }
   
   $postToForm = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listuserscloning";
   print "<form action=\"".$postToForm."\" method=\"post\">\n";
   print "<input type=\"hidden\" name=\"segmentid\" value=\"".getParameter("segmentid")."\">\n";
   print "<input type=\"hidden\" name=\"segment\" value=\"".getParameter("segment")."\">\n";
   if($_SESSION['showdebug']) print "<input type=\"hidden\" name=\"showdebug\" value=\"1\">\n";

   print "<div style=\"clear: both;\"></div><div style=\"margin-top:10px;\"><input type=\"submit\" name=\"submit\" value=\"Search\"></div>\n";   
   
   $exceptions = getParameter("exceptions");
   //if ($exceptions==NULL) $exceptions = "nletter,parentid,ownersite,field1,field2,field3,privacy,siteid";
   if ($exceptions==NULL) $exceptions = "nletter,ownersite,field1,field2,field3,privacy,siteid";
   $ex = separateStringBy($exceptions,",");

//print "\n\n<!-- ***chj*** Exceptions: ".$exceptions." -->\n\n";
//print "\n\n<!-- ***chj*** before sending Exceptions:\n";
//print_r($ex);
//print "\n\nin_array: ".in_array("parentid",$ex);
//print "\n-->\n\n";

   $renArr = separateStringBy(getParameter("renames"),",");
   $ren = array();
   for ($i=0;$i<count($renArr);$i++) if (($i%2)==0) $ren[$renArr[$i]]=$renArr[($i+1)];

   print $ua->getSearchHTMLSmall($list,getParameter("s_usertype"),$ex,$ren,TRUE);

   print "<div style=\"clear: both;\"></div><div style=\"margin-top:10px;\"><input type=\"submit\" name=\"submit\" value=\"Search\"></div>\n";
   print "</form>\n";


?>
