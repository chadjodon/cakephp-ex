<a name="styleanchor"></a>
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
      <input type="hidden" name="contenttype_view" value="<?php echo $contenttype_view; ?>">
      <input type="hidden" name="filetype_view" value="<?php echo $filetype_view; ?>">

<?php            
            $divs = $widgetClass->getDesignDivs($cmsid,$version);
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
               $propTable .= "<td><a name=\"divstylelink".$divs[$i]['divid']."\"/><a style=\"font-size:10px;font-family:arial;\" href=\"#divstylelink".$divs[$i]['divid']."\" onclick=\"document.getElementById('divstylerow_".$divs[$i]['divid']."').style.display='';\">".($i+1)."</a></td>\n";                                                                                             
               $propTable .= "<input type=\"hidden\" name=\"divids[".$i."]\" value=\"".$divs[$i]['divid']."\">\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_label\"  name=\"div".$divs[$i]['divid']."_label\"  value=\"".$divs[$i]['label']."\"  style=\"font-size:10px;font-family:arial;width:30px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";  

               $propTable .= "<td><input ".$disabled." type=\"checkbox\" id=\"div".$divs[$i]['divid']."_status\" name=\"div".$divs[$i]['divid']."_status\" ";
               $propTable .= "value=\"1\"  onchange=\"setInputValues('".$divs[$i]['divid']."');\"";
               if ($divs[$i]['status']==1) $propTable .= " checked";
               $propTable .= "></td>\n";

               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_zindex\"  name=\"div".$divs[$i]['divid']."_zindex\"  value=\"".$divs[$i]['zindex']."\"  style=\"font-size:10px;width:20px;font-family:arial;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";  
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_bgcolor\" name=\"div".$divs[$i]['divid']."_bgcolor\" value=\"".$divs[$i]['bgcolor']."\" style=\"font-size:10px;width:50px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_borderw\" name=\"div".$divs[$i]['divid']."_borderw\" value=\"".$divs[$i]['borderw']."\" style=\"font-size:10px;width:20px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_borderc\" name=\"div".$divs[$i]['divid']."_borderc\" value=\"".$divs[$i]['borderc']."\" style=\"font-size:10px;width:50px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td>";
               $propTable .= "<input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_bgimage\" name=\"div".$divs[$i]['divid']."_bgimage\" value=\"".$divs[$i]['bgimage']."\" style=\"font-size:10px;width:65px;\">";
               if (0!=strcmp($disabled,"DISABLED")) $propTable .= "<a href=\"#styleanchor\" onClick=\"window.open('showversionwidget_layout_bgimg.php?index=".$divs[$i]['divid']."', 'blank','toolbar=no,scrollbars=yes,width=500,height=400');\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"</a>";
               $propTable .= "</td>\n";
               $propTable .= "<td>";
               $propTable .= "<input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_contentref\" name=\"div".$divs[$i]['divid']."_contentref\" value=\"".$divs[$i]['contentref']."\" style=\"font-size:10px;width:65px;\">";
               //if (0!=strcmp($disabled,"DISABLED") && $divs[$i]['contentref']!=NULL) $propTable .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=".getParameter("action")."&view=1&shortname=".$divs[$i]['contentref']."\" target=\"_new\"><img src=\"".getBaseURL()."jsfadmin/images/view.png\" border=\"0\"></a>";

               if ($divs[$i]['contentref']!=NULL) {
                  //$propTable .= "<a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=".getParameter("action")."&view=1&shortname=".$divs[$i]['contentref']."\" target=\"_new\"><img src=\"".getBaseURL()."jsfadmin/images/view.png\" border=\"0\"></a>";
                  $jsaction = "window.open('".getBaseURL()."jsfadmin/newsnippet.php?shortname=' + document.getElementById('div".$divs[$i]['divid']."_contentref').value + '&dir=".$cmsfile['dir']."' , 'jsfcms_des_".$divs[$i]['divid']."','toolbar=no,scrollbars=yes,width=600,height=670');";
                  $propTable .= "<a href=\"#styleanchor\" onclick=\"".$jsaction."\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"></a>";
               } else if (0!=strcmp($disabled,"DISABLED") && $divs[$i]['contentref']==NULL) {
                  $shortname = $cmsfile['filename']."_des_div".$divs[$i]['divid'];
                  $jsaction = "document.getElementById('div".$divs[$i]['divid']."_contentref').value='".$shortname."';window.open('".getBaseURL()."jsfadmin/newsnippet.php?shortname=".$shortname."&dir=".$cmsfile['dir']."' , 'jsfcms_des_".$divs[$i]['divid']."','toolbar=no,scrollbars=yes,width=630,height=700');";
                  $propTable .= "<a href=\"#styleanchor\" onclick=\"".$jsaction."\"><img src=\"".getBaseURL()."jsfimages/view.png\" border=\"0\"></a>";
               }
               $propTable .= "</td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_url\"       name=\"div".$divs[$i]['divid']."_url\"       value=\"".$divs[$i]['url']."\"       style=\"font-size:10px;width:80px;\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_divtop\"    name=\"div".$divs[$i]['divid']."_divtop\"    value=\"".$divs[$i]['divtop']."\"    style=\"font-size:10px;width:24px;\"          onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_divleft\"   name=\"div".$divs[$i]['divid']."_divleft\"   value=\"".$divs[$i]['divleft']."\"   style=\"font-size:10px;width:24px;\"       onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_divwidth\"  name=\"div".$divs[$i]['divid']."_divwidth\"  value=\"".$divs[$i]['divwidth']."\"  style=\"font-size:10px;width:24px;\"    onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";
               $propTable .= "<td><input ".$disabled." type=\"text\" id=\"div".$divs[$i]['divid']."_divheight\" name=\"div".$divs[$i]['divid']."_divheight\" value=\"".$divs[$i]['divheight']."\" style=\"font-size:10px;width:24px;\" onkeyup=\"setInputValues('".$divs[$i]['divid']."');\"></td>\n";

               $propTable .= "<td><input ".$disabled." type=\"checkbox\" id=\"div".$divs[$i]['divid']."_fixed\" name=\"div".$divs[$i]['divid']."_fixed\" ";
               $propTable .= "value=\"1\"  style=\"font-size:10px;width:30px;\"";
               if ($divs[$i]['fixed']==1) $propTable .= " checked";
               $propTable .= "></td>\n";

               $propTable .= "<td>";
               if (0!=strcmp($disabled,"DISABLED")) $propTable .= "<a style=\"font-size:10px;font-family:arial;\" href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=".getParameter("action")."&view=1&curdir=".$curdir."&theme=".$theme."&advsearch=".$advsearch."&searchstr=".$searchstr."&orderby=".$orderby."&cmsid=".$cmsid."&version=".$versioninfo['version']."&deletediv=1&divid=".$divs[$i]['divid']."#styleanchor\" onClick=\"return confirm('Are you sure you want to delete this section?');\"><img src=\"".getBaseURL()."jsfimages/delete.png\" border=\"0\"></a>";
               $propTable .= "</td>";
               $propTable .= "</tr>\n";

               $propTable .= "<tr id=\"divstylerow_".$divs[$i]['divid']."\" style=\"display:none;\" bgcolor=\"#FFFFFF\">\n";                                                                                
               $propTable .= "<td colspan=\"16\">";
               $propTable .= "Explicit Style: ";
               $propTable .= "<input ".$disabled." type=\"text\" id=\"divstyle".$divs[$i]['divid']."\" name=\"divstyle".$divs[$i]['divid']."\" ";
               $propTable .= "style=\"font-size:12px;width:300px;\" value=\"".$divs[$i]['style']."\">";
               $propTable .= "<a href=\"#\" ";
               $propTable .= "onClick=\"location.href='".getBaseURL()."jsfadmin/admincontroller.php?";
               $propTable .= "action=".getParameter("action")."&editdivstyle=1&cmsid=".$cmsid."&version=".$version."&advsearch=".$advsearch."&curdir=".$curdir."&searchstr=".$searchstr."&orderby=".$orderby;
               $propTable .= "&divid=".$divs[$i]['divid']."&style=' + document.getElementById('divstyle".$divs[$i]['divid']."').value + '#divstylelink".$divs[$i]['divid']."';\">";
               $propTable .= "Use this explicit style</a>";
               $propTable .= "</td>\n";
               $propTable .= "</tr>\n";

               $jscript2 .= "coelement = document.getElementById('co_cmsfdes_".$divs[$i]['divid']."');\n";
               $jscript2 .= "coelement.innerHTML = '".$divs[$i]['label']."<BR>".($i+1).". ".$divs[$i]['divleft']."px,".$divs[$i]['divtop']."px<BR>".$divs[$i]['divwidth']." by ".$divs[$i]['divheight']."';\n";

               $jscript2 .= "document.getElementById('cmsfdes_".$divs[$i]['divid']."').onmouseover=divMouseOver;\n";
               $jscript2 .= "document.getElementById('cmsfdes_".$divs[$i]['divid']."').onmouseout=divMouseOut;\n";
               if (0!=strcmp($disabled,"DISABLED")) {
                  $jscript2 .= "document.getElementById('cmsfdes_".$divs[$i]['divid']."').onmousedown=startDrag;\n";
                  $jscript2 .= "document.getElementById('cmsfdes_".$divs[$i]['divid']."').onmouseup=stopDrag;\n";
               }

               print "<div class=\"draggable\" data-divid=\"".$divs[$i]['divid']."\" data-index=\"".($i+1)."\" id=\"cmsfdes_".$divs[$i]['divid']."\" ";
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
               print "<div class=\"stretchable\" data-divid=\"".$divs[$i]['divid']."\" data-index=\"".($i+1)."\" id=\"st_cmsfdes_".$divs[$i]['divid']."\" ";
               print "style=\"position:relative;";
               print "top:0px;";
               print "left:0px;";
               print "width:10px;";
               print "height:10px;";
               print "overflow:hidden;";
               print "background-color:RED;";
               print "z-index:".($divs[$i]['zindex']+1).";";
               print "\"></div>\n";
               print "<div class=\"coords\" data-divid=\"".$divs[$i]['divid']."\" data-index=\"".($i+1)."\" id=\"co_cmsfdes_".$divs[$i]['divid']."\" ";
               print "style=\"position:relative;";
               print "top:15px;";
               print "left:3px;";
               print "width:75px;";
               print "height:35px;";
               print "background-color:#e4e2b1;font-size:10px;color:BLACK;";
               print "z-index:".($divs[$i]['zindex']+2).";";
               print "\"></div>\n";
               print "</div>";
            }
       ?>
         </div>
         <table cellpadding="4" cellspacing="1" bgcolor="#333333">
         <tr bgcolor="#FFFFFF" style="font-size:10px;font-family:arial;">
            <td></td><td>Label</td><td>Active</td><td>Z</td><td>BG Color</td><td colspan="2">Border Width/Color</td><td>BG Image</td><td>Content</td>
            <td>URL</td><td>Top</td><td>Left</td><td>Width</td><td>Height</td><td>Fixed</td><td></td>
         </tr>
         <?php echo $propTable; ?>
         <tr><td bgcolor="#FFFFFF" colspan="16">
            <?php if (0!=strcmp($disabled,"DISABLED")) { ?>
            <input type="submit" name="designsubmit" value="Save"><input type="submit" name="designsubmit" value="New section">
            <?php } ?>
         </td></tr>
         </table>


         <!-- div creation screen -->
         <script language="javascript">
         var _startX = 0;
         var _startY = 0;
         var _sizeX = 0;
         var _sizeY = 0;
         var _offsetX = 0;
         var _offsetY = 0;
         var _dragElement;
         var _oldZIndex=0;
         var _oldColor;
         var _curdrag = false;

         function divMouseOver(){
            _oldColor = document.getElementById('dragrow_' + this.getAttribute('data-divid')).style.backgroundColor;
            document.getElementById('dragrow_' + this.getAttribute('data-divid')).style.backgroundColor='#df823e';
         }

         function divMouseOut(){
            document.getElementById('dragrow_' + this.getAttribute('data-divid')).style.backgroundColor=_oldColor;
         }

         function initDrag(){
            //alert('chad');
            var coelement;
            <?php echo $jscript2; ?>
         }

         // start dragging
         function startDrag(e){
         	if(!e){var e=window.event};
         	var targ=e.target?e.target:e.srcElement;
         	if(targ.className=='stretchable'){
               _startX=e.clientX;
               _startY=e.clientY;
               var friendelement = document.getElementById('cmsfdes_' + targ.getAttribute('data-divid'));
               _offsetX = ExtractNumber(friendelement.style.left);
               _offsetY = ExtractNumber(friendelement.style.top);
               _sizeX = ExtractNumber(friendelement.style.width);
               _sizeY = ExtractNumber(friendelement.style.height);
               _oldZIndex = friendelement.style.zIndex;
               targ.style.zIndex = 10001;
               friendelement.style.zIndex = 10000;
               _dragElement=friendelement;
               document.onmousemove=stretchDiv;
               document.body.focus();
               document.onselectstart = function () { return false; };
               targ.ondragstart = function() { return false; };
               document.getElementById('dragrow_' + targ.getAttribute('data-divid')).style.backgroundColor='#df823e';
            } else if(targ.className=='draggable'){
               _curdrag = false;
               _startX=e.clientX;
               _startY=e.clientY;
               _offsetX = ExtractNumber(targ.style.left);
               _offsetY = ExtractNumber(targ.style.top);
               _oldZIndex = targ.style.zIndex;
               targ.style.zIndex = 10000;
               _dragElement=targ;
               _dragElement.style.opacity = "0.75";
               document.onmousemove=dragDiv;
               document.body.focus();
               document.onselectstart = function () { return false; };
               targ.ondragstart = function() { return false; };
               document.getElementById('dragrow_' + targ.getAttribute('data-divid')).style.backgroundColor='#df823e';
            }
            return false;
         }
         // continue dragging
         function dragDiv(e){
         	if(!e){var e=window.event};
            
            var deltax = e.clientX - _startX;
            var deltay = e.clientY - _startY;
            //Don't move it till it's dragged at least 10 pixels
            //if (_curdrag || deltax>5 || deltay>5) {
               //_curdrag = true;
            	// move div element
              	_dragElement.style.left= (_offsetX + deltax) + 'px';
              	_dragElement.style.top= (_offsetY + deltay) + 'px';
   
               var coelement = document.getElementById('co_cmsfdes_' + _dragElement.getAttribute('data-divid'));
   
               //coelement.innerHTML = _dragElement.getAttribute('data-index') + '. ' + _dragElement.style.left + ',' + _dragElement.style.top + "<BR>" + _dragElement.style.width + ' by ' + _dragElement.style.height;
               var divid = _dragElement.getAttribute('data-divid');
               coelement.innerHTML = document.getElementById('div' + divid + '_label').value + '<br>' + _dragElement.getAttribute('data-index') + '. ' + _dragElement.style.left + ',' + _dragElement.style.top + "<BR>" + _dragElement.style.width + ' by ' + _dragElement.style.height;
               document.getElementById('div' + _dragElement.getAttribute('data-divid') + '_divtop').value = ExtractNumber(_dragElement.style.top);
               document.getElementById('div' + _dragElement.getAttribute('data-divid') + '_divleft').value = ExtractNumber(_dragElement.style.left);
            //}
         }
         // continue stretching
         function stretchDiv(e){
         	if(!e){var e=window.event};

            var divid = _dragElement.getAttribute('data-divid');
            var deltax = e.clientX - _startX;
            var deltay = e.clientY - _startY;

            _dragElement.style.width = (_sizeX - deltax) + 'px';
            _dragElement.style.height = (_sizeY - deltay) + 'px';
           	_dragElement.style.left= (_offsetX + deltax) + 'px';
           	_dragElement.style.top= (_offsetY + deltay) + 'px';
            
            var coelement = document.getElementById('co_cmsfdes_' + divid);
            coelement.innerHTML = document.getElementById('div' + divid + '_label').value + '<br>' + _dragElement.getAttribute('data-index') + '. ' + _dragElement.style.left + ',' + _dragElement.style.top + "<BR>" + _dragElement.style.width + ' by ' + _dragElement.style.height;
            //coelement.innerHTML = friendelement.getAttribute('data-index') + '. ' + friendelement.style.left + ',' + friendelement.style.top + "<BR>" + friendelement.style.width + ' by ' + friendelement.style.height;
            document.getElementById('div' + divid + '_divtop').value = ExtractNumber(_dragElement.style.top);
            document.getElementById('div' + divid + '_divleft').value = ExtractNumber(_dragElement.style.left);
            document.getElementById('div' + divid + '_divwidth').value = ExtractNumber(_dragElement.style.width);
            document.getElementById('div' + divid + '_divheight').value = ExtractNumber(_dragElement.style.height);
         }
         // stop dragging
         function stopDrag(){
            document.getElementById('dragrow_' + _dragElement.getAttribute('data-divid')).style.backgroundColor='';
            _dragElement.style.opacity = "1.0";
            _dragElement.style.zIndex = _oldZIndex;
            var friendelement1 = document.getElementById('st_cmsfdes_' + _dragElement.getAttribute('data-divid'));
            friendelement1.style.zIndex = _oldZIndex + 1;
            var friendelement2 = document.getElementById('co_cmsfdes_' + _dragElement.getAttribute('data-divid'));
            friendelement2.style.zIndex = _oldZIndex + 2;
            document.onmousemove = null;
            document.onselectstart = null;
            _dragElement.ondragstart = null;
            _dragElement = null;
         }

         function setInputValues(divid) {
            var elem = document.getElementById('cmsfdes_' + divid);
            elem.style.top    =document.getElementById('div' + divid + '_divtop').value + 'px';
            elem.style.left   =document.getElementById('div' + divid + '_divleft').value + 'px';
            elem.style.width  =document.getElementById('div' + divid + '_divwidth').value + 'px';
            elem.style.height =document.getElementById('div' + divid + '_divheight').value + 'px';
            elem.style.border =document.getElementById('div' + divid + '_borderw').value + 'px solid ' + document.getElementById('div' + divid + '_borderc').value;
            elem.style.backgroundColor =document.getElementById('div' + divid + '_bgcolor').value;
            elem.style.zIndex =document.getElementById('div' + divid + '_zindex').value;
            document.getElementById('co_cmsfdes_' + divid).innerHTML = document.getElementById('div' + divid + '_label').value + '<BR>' + elem.getAttribute('data-index') + '. ' + elem.style.left + ',' + elem.style.top + "<BR>" + elem.style.width + ' by ' + elem.style.height;
         }

         function ExtractNumber(value){
             var n = parseInt(value);
             return (n == null || isNaN(n)) ? 0 : n;
         }

         function setBGImage(img,ndx) {
            var divid = 'div' + ndx + '_bgimage';
            //alert(divid);
            document.getElementById(divid).value = img;
         }

         window.onload=function(){
            //alert('hellloooo world');
            initDrag();
         }
         </script>
         </td></tr>

      </form>
      </table>
