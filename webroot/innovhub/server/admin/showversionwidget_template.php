<a name="styleanchor"></a>
<?php
   $parent = $widgetClass->getParent($cmsid);
   if ($parent==NULL) {

      $files = $ss->searchFiles(NULL,NULL,"DESIGN");
      $opt = array();
      for ($i=0; $i<count($files); $i++) {
         $opt[$files[$i]['filename']]=$files[$i]['cmsid'];
      }
?>

      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrolcontent" method="POST">
      <input type="hidden" name="action" value="<?php echo getParameter("action"); ?>">
      <input type="hidden" name="setparent" value="1">
      <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
      <input type="hidden" name="version" value="<?php echo $version; ?>">
      <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
      <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
      <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
      <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
      <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
      <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">

      <?php echo getOptionList("tempcmsid", $opt); ?>
      <input type="submit" name="submit" value="Use Selected Template">
      </form>

<?php
   } else {
      $cmsfile = $ss->getFileByIdQuick($parent);
      print "<b>".$cmsfile['filename']."</b>";
?>
      <table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="white">
      <tr><td colspan="2" align="center"><img src="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>images/pixel.gif" width="1" height="20"><tr></td>
      <form action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="versioncontrolcontent" method="POST">
      <input type="hidden" name="action" value="<?php echo getParameter("action"); ?>">
      <input type="hidden" name="editcontents" value="1">
      <input type="hidden" name="cmsid" value="<?php echo $cmsid; ?>">
      <input type="hidden" name="version" value="<?php echo $version; ?>">
      <input type="hidden" name="advsearch" value="<?php echo $advsearch; ?>">
      <input type="hidden" name="curdir" value="<?php echo $curdir; ?>">
      <input type="hidden" name="searchstr" value="<?php echo $searchstr; ?>">
      <input type="hidden" name="orderby" value="<?php echo $orderby; ?>">

<?php            
            $divs = $widgetClass->getTemplateDesignDivs($cmsid,$version);
?>
         <tr><td colspan="2">
         <div style="position:relative;width:780px;height:585px;overflow:auto;background-color:#DDDDDD;border:1px solid #333333;">
       <?php
            $propTable = "";
            $jscript2 = "";
            for ($i=0; $i<count($divs); $i++) {
               $bgcolor="#FFFFFF";
               if (($i%2)==1) $bgcolor="#DDDDDD";
               $propTable .= "<tr id=\"dragrow_".$divs[$i]['divid']."\" bgcolor=\"".$bgcolor."\">\n";                                                                                
               $propTable .= "<td>".($i+1)."</td>\n";                                                                                             
               $propTable .= "<input type=\"hidden\" name=\"divids[".$i."]\" value=\"".$divs[$i]['divid']."\">\n";
               $propTable .= "<td>".$divs[$i]['label']."</td>\n";                                                                                             

               $propTable .= "<input type=\"hidden\" name=\"div".$divs[$i]['divid']."_bgcolor2\" value=\"".$divs[$i]['bgcolor2']."\">\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_bgcolor\" name=\"div".$divs[$i]['divid']."_bgcolor\" value=\"".$divs[$i]['bgcolor']."\" style=\"font-size:12px;width:65px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";

               $propTable .= "<input type=\"hidden\" name=\"div".$divs[$i]['divid']."_url2\" value=\"".$divs[$i]['url2']."\">\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_url\" name=\"div".$divs[$i]['divid']."_url\" value=\"".$divs[$i]['url']."\" style=\"font-size:12px;width:60px;\"></td>\n";

               $propTable .= "<input type=\"hidden\" name=\"div".$divs[$i]['divid']."_bgimage2\" value=\"".$divs[$i]['bgimage2']."\">\n";
               $propTable .= "<td>";
               $propTable .= "<input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_bgimage\" name=\"div".$divs[$i]['divid']."_bgimage\" value=\"".$divs[$i]['bgimage']."\" style=\"font-size:12px;width:65px;\">";
               if (0!=strcmp($disabled,"DISABLED")) $propTable .= "<a href=\"#styleanchor\" onClick=\"window.open('showversionwidget_layout_bgimg.php?index=".$divs[$i]['divid']."', 'blank','toolbar=no,scrollbars=yes,width=500,height=400');\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"</a>";
               $propTable .= "</td>\n";

               $propTable .= "<input type=\"hidden\" name=\"div".$divs[$i]['divid']."_contentref2\" value=\"".$divs[$i]['contentref2']."\">\n";
               $propTable .= "<td>";
               $propTable .= "<input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_contentref\" name=\"div".$divs[$i]['divid']."_contentref\" value=\"".$divs[$i]['contentref']."\" style=\"font-size:12px;width:65px;\">";

               // ver 1:
               //if (0!=strcmp($disabled,"DISABLED") && $divs[$i]['contentref']!=NULL) $propTable .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=".getParameter("action")."&view=1&shortname=".$divs[$i]['contentref']."\" target=\"_new\"><img src=\"".getBaseURL()."jsfadmin/images/view.png\" border=\"0\"></a>";
               // ver 2:
               //$propTable .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=".getParameter("action")."&view=1&shortname=".$divs[$i]['contentref']."\" target=\"_new\"><img src=\"".getBaseURL()."jsfadmin/images/view.png\" border=\"0\"></a>";
               // ver 3:
               if ($divs[$i]['contentref']!=NULL) {
                  $jsaction = "window.open('".getBaseURL()."jsfadmin/newsnippet.php?shortname=' + document.getElementById('div".$divs[$i]['divid']."_contentref').value + '&dir=".$cmsfile['dir']."' , 'jsfcms_tpl_".$divs[$i]['divid']."','toolbar=no,scrollbars=yes,width=600,height=670');";
                  $propTable .= "<a href=\"#styleanchor\" onclick=\"".$jsaction."\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"></a>";
               } else if (0!=strcmp($disabled,"DISABLED") && $divs[$i]['contentref']==NULL) {
                  $shortname = $cmsfile['filename']."_tpl_div".$divs[$i]['divid'];
                  $jsaction = "document.getElementById('div".$divs[$i]['divid']."_contentref').value='".$shortname."';window.open('".getBaseURL()."jsfadmin/newsnippet.php?shortname=".$shortname."&dir=".$cmsfile['dir']."' , 'jsfcms_tpl_".$divs[$i]['divid']."','toolbar=no,scrollbars=yes,width=630,height=700');";
                  $propTable .= "<a href=\"#styleanchor\" onclick=\"".$jsaction."\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"></a>";
               }

               $propTable .= "</td>\n";

               $propTable .= "</tr>\n";

               print "<div id=\"cmsfdes_".$divs[$i]['divid']."\" ";
               print "style=\"position:absolute;";
               print "top:".$divs[$i]['divtop']."px;";
               print "left:".$divs[$i]['divleft']."px;";
               print "width:".$divs[$i]['divwidth']."px;";
               print "height:".$divs[$i]['divheight']."px;";
               if ($divs[$i]['borderw']==NULL) $divs[$i]['borderw']=1; 
               if ($divs[$i]['borderc']==NULL) $divs[$i]['borderc']="#000000"; 
               print "border:".$divs[$i]['borderw']."px solid ".$divs[$i]['borderc'].";";
               if ($divs[$i]['bgcolor']==NULL) $divs[$i]['bgcolor']="#FFFFFF"; 
               print "background-color:".$divs[$i]['bgcolor'].";";
               if ($divs[$i]['bgimage']!=NULL) print "background-image: URL(".$template->doSubstitutions($divs[$i]['bgimage'],$vars).");";
               if ($divs[$i]['zindex']==NULL) $divs[$i]['zindex']=1; 
               print "z-index:".$divs[$i]['zindex'].";";
               print "\">\n";
               print $divs[$i]['label'];
               print "</div>";
            }
       ?>
         </div>
         <table cellpadding="4" cellspacing="1" bgcolor="#333333">
         <tr bgcolor="#FFFFFF" style="font-size:12px;font-family:arial;">
            <td colspan="2"></td><td>BG Color</td><td>URL</td><td>BG Image</td><td>Content</td>
         </tr>
         <?php echo $propTable; ?>
         <tr><td bgcolor="#FFFFFF" colspan="6">
            <?php if (0!=strcmp($disabled,"DISABLED")) { ?>
            <input type="submit" name="designsubmit" value="Save">
            <?php } ?>
         </td></tr>
         </table>


         <!-- div creation screen -->
         <script language="javascript">
         function setInputValues(divid) {
            var elem = document.getElementById('cmsfdes_' + divid);
            elem.style.backgroundColor =document.getElementById('div' + divid + '_bgcolor').value;
         }

         function setBGImage(img,ndx) {
            var divid = 'div' + ndx + '_bgimage';
            //alert(divid);
            document.getElementById(divid).value = img;
         }
         </script>
         </td></tr>

      </form>
      </table>
<?php
   }
?>
