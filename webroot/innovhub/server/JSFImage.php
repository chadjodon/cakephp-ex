<?php

class JSFImage {
   
   var $image;
   var $image_type;
   var $filename;
 
   function load($picfilename) {
      if ( 0==strcmp(strtolower(substr($picfilename,-4)),".jpg") || 
           0==strcmp(strtolower(substr($picfilename,-4)),"jpeg") || 
           0==strcmp(strtolower(substr($picfilename,-4)),".gif") || 
           0==strcmp(strtolower(substr($picfilename,-4)),".png")) {
         ini_set("memory_limit","128M");
         $this->filename = $picfilename;
         $image_info = getimagesize($picfilename);
         $this->image_type = $image_info[2];
         if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($picfilename);
         } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($picfilename);
         } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($picfilename);
         } else {
            return FALSE;
         }
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function save($newfilename, $image_type=NULL, $compression=75, $permissions=null) {
      ini_set("memory_limit","128M");
      if ($image_type == NULL) $image_type = $this->getType();
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$newfilename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$newfilename);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$newfilename);
      }   
      if( $permissions != null) {
         chmod($newfilename,$permissions);
      }
   }

   function output($image_type=NULL) {
      if ($image_type == NULL) $image_type = $this->getType();
      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      } elseif( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }

   function getWidth() {
      return imagesx($this->image);
   }

   function getHeight() {
      return imagesy($this->image);
   }

   function getType() {
      return $this->image_type;
   }

   function getZoomToRectangle($desiredWidth, $desiredHeight, $extrastyle=NULL) {
      $width = $this->getWidth();
      $height = $this->getHeight();
      $result['left'] = 0;
      $result['top'] = 0;
      $result['desiredproportion'] = $desiredWidth/$desiredHeight;
      $result['proportion'] = $width/$height;
      $result['width'] = $desiredWidth;
      $result['height'] = $desiredHeight;

      $proportion = 1;
      if ($result['desiredproportion']>=$result['proportion']) {
         $result['height'] = round(($desiredWidth/$width)*$height);
         $result['top'] = round(($desiredHeight - $result['height'])/2);
      } else {
         $result['width'] = round(($desiredHeight/$height)*$width);
         $result['left'] = round(($desiredWidth - $result['width'])/2);
      }

      $url = str_replace($GLOBALS['baseDir'],"",$this->filename);
      $str = "<div style=\"position:relative;width:".$desiredWidth."px;height:".$desiredHeight."px;overflow:hidden;".$extrastyle."\">";
      $str .= "<div style=\"position:absolute;left:".$result['left']."px;top:".$result['top']."px;\">";
      $str .= "<img src=\"".getBaseURL().$url."\" width=\"".$result['width']."\" height=\"".$result['height']."\">";
      $str .= "</div>";
      $str .= "</div>";
      return $str;
   }

   function getFullToRectangle($desiredWidth, $desiredHeight, $extrastyle=NULL) {
      $width = $this->getWidth();
      $height = $this->getHeight();
      $result['left'] = 0;
      $result['top'] = 0;
      $result = $this->getNewProportions($desiredWidth, $desiredHeight);
      if ($result['width']<$desiredWidth) $result['left'] = round(($desiredWidth - $result['width'])/2);
      if ($result['height']<$desiredHeight) $result['top'] = round(($desiredHeight - $result['height'])/2);

      $url = str_replace($GLOBALS['baseDir'],"",$this->filename);
      $str = "<div style=\"position:relative;width:".$desiredWidth."px;height:".$desiredHeight."px;overflow:hidden;".$extrastyle."\">";
      $str .= "<div style=\"position:absolute;left:".$result['left']."px;top:".$result['top']."px;\">";
      $str .= "<img src=\"".getBaseURL().$url."\" width=\"".$result['width']."\" height=\"".$result['height']."\">";
      $str .= "</div>";
      $str .= "</div>";
      return $str;
   }

   function resizeToRectangle($width, $height) {
      $result = $this->getNewProportions($width,$height);
      $this->resize($result['width'],$result['height']);
   }

   function resizeToHeight($height) {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }

   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }

   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;   
   }

   function getNewProportions ($desiredWidth, $desiredHeight,$width=NULL,$height=NULL) {
      if ($width==NULL || $height==NULL) {
         $width = $this->getWidth();
         $height = $this->getHeight();
      }
      $result['width'] = $width;
      $result['height'] = $height;
      $proportion = 1;
      if ($desiredWidth >= $width && $desiredHeight >= $height) {
         $result['width'] = $width;
         $result['height'] = $height;
      } else if ($width > $desiredWidth && $height <= $desiredHeight) {
         $proportion = $desiredWidth / $width;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      } else if ($width <= $desiredWidth && $height > $desiredHeight) {
         $proportion = $desiredHeight / $height;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      } else if ($width > $desiredWidth && $height > $desiredHeight) {
         $dratio = $desiredWidth / $desiredHeight;
         $aratio = $width / $height;
         if ($aratio > $dratio) $proportion = $desiredWidth / $width;
         else $proportion = $desiredHeight / $height;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      }

      return $result;
   }

   function layerImage($layerfile,$pct=60){
      $watermark = NULL;
      $image_info = getimagesize($layerfile);
      $img_type = $image_info[2];
      if( $img_type == IMAGETYPE_JPEG ) {
         $watermark = imagecreatefromjpeg($layerfile);
      } elseif( $img_type == IMAGETYPE_GIF ) {
         $watermark = imagecreatefromgif($layerfile);
      } elseif( $img_type == IMAGETYPE_PNG ) {
         $watermark = imagecreatefrompng($layerfile);
      }

      $watermark_width = imagesx($watermark);  
      $watermark_height = imagesy($watermark);  
      imagecopymerge($this->image, $watermark, 0, 0, 0, 0, $watermark_width, $watermark_height,$pct);
      imagedestroy($watermark);
   }

   function rotateImage($degrees=90) {
      $rotate = imagerotate($this->image, $degrees, 0);
      $this->closeimage();
      $this->image = $rotate;
   }

   function addWatermark($watermarkFile,$pct=70){
      //print "\n<!-- watermark file: ".$watermarkFile." -->\n";
      $watermark = NULL;
      $image_info = getimagesize($watermarkFile);
      $img_type = $image_info[2];
      if( $img_type == IMAGETYPE_JPEG ) {
         $watermark = imagecreatefromjpeg($watermarkFile);
         //print "\n<!-- jpg file type -->\n";
      } elseif( $img_type == IMAGETYPE_GIF ) {
         $watermark = imagecreatefromgif($watermarkFile);
         //print "\n<!-- gif file type -->\n";
      } elseif( $img_type == IMAGETYPE_PNG ) {
         $watermark = imagecreatefrompng($watermarkFile);
         //print "\n<!-- png file type -->\n";
      }

      $watermark_width = imagesx($watermark);  
      $watermark_height = imagesy($watermark);  
      //print "\n<!-- wm width: ".$watermark_width." height: ".$watermark_height." -->\n";
      $new_image = $this->image;
      $curr_width = imagesx($this->image);  
      $curr_height = imagesy($this->image);  
      //print "\n<!-- img width: ".$curr_width." height: ".$curr_height." -->\n";
      //$dest_x = $curr_width - $watermark_width - 10;  
      $dest_x = round($curr_width/2 - $watermark_width/2);  
      $dest_y = $curr_height - $watermark_height - 10;  
      //print "\n<!-- dest_x: ".$dest_x." dest_y: ".$dest_y." -->\n";
      imagecopymerge($this->image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $pct);
      imagedestroy($watermark);
   }
   
   function positionNewLayer($jsfi,$x,$y,$wd,$ht) {
      imagecopyresampled($this->image,$jsfi->image,$x,$y,0,0,$wd,$ht,$jsfi->getWidth(),$jsfi->getHeight());
   }
   
   function resizeAndFill($fn,$newfilename,$width,$height,$r=255,$g=255,$b=255,$compression=100){
      $watermark = NULL;
      $image_info = getimagesize($fn);
      $img_type = $image_info[2];
      if( $img_type == IMAGETYPE_JPEG ) {
         $watermark = imagecreatefromjpeg($fn);
      } elseif( $img_type == IMAGETYPE_GIF ) {
         $watermark = imagecreatefromgif($fn);
      } elseif( $img_type == IMAGETYPE_PNG ) {
         $watermark = imagecreatefrompng($fn);
      }

      $watermark_width = imagesx($watermark);  
      $watermark_height = imagesy($watermark);  

      $result = $this->getNewProportions($width, $height, $watermark_width, $watermark_height);
      $result['left'] = round(($width - $result['width'])/2);
      $result['top'] = round(($height - $result['height'])/2);

      $image_p = imagecreatetruecolor($result['width'],$result['height']);
      imagecopyresampled($image_p, $watermark, 0, 0, 0, 0,$result['width'],$result['height'], $watermark_width, $watermark_height);

      imagedestroy($watermark);

      $im = imagecreatetruecolor($width,$height);
      $bg = imagecolorallocate($im, $r, $g, $b);
      imagefilledrectangle($im,0,0,$width,$height,$bg);

      imagecopymerge($im, $image_p, $result['left'], $result['top'], 0, 0, $result['width'], $result['height'], "100");
      //imagecopymerge($im, $image_p, 0, 0, 0, 0, $result['width'], $result['height'], "100");
      imagedestroy($image_p);

      if( $img_type == IMAGETYPE_JPEG ) {
         imagejpeg($im,$newfilename,$compression);
      } elseif( $img_type == IMAGETYPE_GIF ) {
         imagegif($im,$newfilename);         
      } elseif( $img_type == IMAGETYPE_PNG ) {
         imagepng($im,$newfilename);
      }   

      imagedestroy($im);

      return $result;
   }
   
   function closeimage(){
      imagedestroy($this->image);
   }


   //---------------------------------------------------------------------------------------------------------
   // Functions for viewing the gallery on the website
   //---------------------------------------------------------------------------------------------------------

   function getAllParameters() {
      $authInfo['subaction'] = getParameter("subaction");
      $authInfo['fn'] = getParameter("fn");
      $authInfo['seq'] = getParameter("seq");
      $authInfo['name']= getParameter("name");
      $authInfo['pwd'] = getParameter("pwd");
      $authInfo['type'] = getParameter("type");
      $authInfo['pic'] = getParameter("pic");
      $authInfo['viewAll'] = getParameter("viewAll");
      $authInfo['page'] = getParameter("page");
      $ua = new UserAcct;
      return $authInfo;
   }

   function getCategory($fn) {
      $template = new Template;
      $result_array['cat'] = array();
      $result_array['fil'] = array();
      $path = "";
      $tok = strtok($fn, "/");
      while ($tok) {
          if ($tok==NULL) {
             $tok = strtok("/");
             continue;
          }
          $path .= $tok."/";
          $result_array['cat'][] = $tok;
          $result_array['fil'][] = $path;
          $tok = strtok("/");
      }
      return $result_array;
   }

   function getParamList($authInfo) {
      $result = "j=j";
      foreach ($authInfo as $key => $value) {
            $result .= "&".$key."=".$value;
      }
      return $result;
   }

   function arrCopy($authInfo) {
    foreach ($authInfo as $key => $value) {
         $copyArr[$key]=$value;
    }
    return $copyArr;
   }

   function displayPicsBeforeAndAfter($authInfo) {
      print $this->getDisplayPicsBeforeAndAfter($authInfo);
   }

   function getDisplayPicsBeforeAndAfter($authInfo,$url=NULL) {
      $str = "";
      if ($url==NULL) $url=getBaseURL().$GLOBALS['codeFolder']."controller.php?action=viewpics";
      $fn = $authInfo['fn'];
      $seq = $authInfo['seq'];
      $pathInfo = $this->getPathFromFN($fn);
      $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);
      $prevLink = "";
      $nextLink = "";
      $next = $seq+1;
      $prev = $seq-1;

      if (count($images) > $next) {
         $desiredWidth = 80;

         $paramArr = $this->arrCopy($authInfo);
         $paramArr['seq'] = $next;
         $paramArr['subaction'] = "single";
         $params = $this->getParamList($paramArr);

         //$nextLink = "<a href=\"".$url."&".$params."\">Next&nbsp;&nbsp;\n";
         $nextLink = "<a href=\"".$url."&".$params."\">\n";
         if (file_exists($pathInfo['dir']."thumbs/".$images[$next])) {
            $nextLink .= "<img src=\"".$pathInfo['url']."thumbs/".$images[$next]."\" border=\"0\">";
         } else {
            $desiredSize = $this->getHeightProportion($pathInfo['dir'].$images[$next],$desiredWidth);
            $nextLink .= "<img src=\"".$pathInfo['url'].$images[$next]."\" height=".$desiredSize['height']." width=".$desiredSize['width']." border=0>\n";
         }
         $nextLink .= "</a>\n";
      }

      if ($seq > 0) {
         $desiredWidth = 80;
         $paramArr = $this->arrCopy($authInfo);
         $paramArr['seq'] = $prev;
         $paramArr['subaction'] = "single";
         $params = $this->getParamList($paramArr);

         $prevLink = "<a href=\"".$url."&".$params."\">\n";
         if (file_exists($pathInfo['dir']."thumbs/".$images[$prev])) {
            $prevLink .= "<img src=\"".$pathInfo['url']."thumbs/".$images[$prev]."\" border=\"0\">";
         } else {
            $desiredSize = $this->getHeightProportion($pathInfo['dir'].$images[$prev],$desiredWidth);
            $prevLink .= "<img src=\"".$pathInfo['url'].$images[$prev]."\" height=".$desiredSize['height']." width=".$desiredSize['width']." border=0>\n";
         }
         //$prevLink .= "&nbsp;&nbsp;Previous</a>\n";
         $prevLink .= "</a>\n";

      }

      $str .= "<table cellpadding=\"2\" cellspacing=\"0\" align=\"center\">";
      $str .= "<tr>\n<td align=\"left\">".$prevLink."</td>\n<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"400\" height=\"1\"></td>\n<td align=\"right\">".$nextLink."</td>\n</tr>";
      $str .= "</table>";
      return $str;
   }

   function getHeightProportion ($picLocation, $desiredWidth) {
      list($width, $height, $type, $attr) = getimagesize($picLocation);

      if ($desiredWidth > $width) {
         $result['width'] = $width;
         $result['height'] = $height;
      }
      else {
         $desiredHeight = round(($desiredWidth * $height)/$width);
         $result['width'] = $desiredWidth;
         $result['height'] = $desiredHeight;
      }
      return $result;
   }

   function getPathFromFN ($fn) {
      $result['dir'] = $GLOBALS['picDir'].$fn;
      $result['url'] = $GLOBALS['picURL'].$fn;
      $result['filter'] = NULL;
      $result['type'] = "personal";
      return $result;
   }

   function getImages($fn,$start=NULL,$end=".jpg",$end2=".jpeg",$end3=".gif",$end4=".png") {
      $template = new Template;
      $result_array = array();
      $files = $template->list_dir($fn);
      foreach ($files as $value) {
         $fs_filename = strtolower($value);
         if ( (0==strcmp(substr($fs_filename,-(strlen($end))),$end) || 
               0==strcmp(substr($fs_filename,-(strlen($end2))),$end2) ||
               0==strcmp(substr($fs_filename,-(strlen($end3))),$end3) ||
               0==strcmp(substr($fs_filename,-(strlen($end4))),$end4) 
               )
               && ($start==NULL || 0==strcmp(substr($fs_filename,0,strlen($start)),strtolower($start))) )
         {
            $result_array[] = $value;
         }
      }
      return $result_array;
   }

   function getZipFiles($dir,$start=NULL,$end=".zip",$end2=".jar",$end3=".rar",$end4=".7z") {
      $template = new Template;
      $result_array = array();
      $files = $template->list_dir($dir);
      foreach ($files as $value) {
         $fs_filename = strtolower($value);
         if ( (0==strcmp(substr($fs_filename,-(strlen($end))),$end) || 
               0==strcmp(substr($fs_filename,-(strlen($end2))),$end2) ||
               0==strcmp(substr($fs_filename,-(strlen($end3))),$end3) ||
               0==strcmp(substr($fs_filename,-(strlen($end4))),$end4) 
               )
               && ($start==NULL || 0==strcmp(substr($fs_filename,0,strlen($start)),strtolower($start))) )
         {
            $result_array[$value] = $value;
         }
      }
      return $result_array;
   }

   function displaySinglePicture($fn,$seq,$desiredWidth=640) {
      print $this->getDisplaySinglePicture($fn,$seq,$desiredWidth);
   }

   function getDisplaySinglePicture($fn,$seq,$desiredWidth=640,$showlink=TRUE) {
      if ($seq===NULL) $seq=0;
      $pathInfo = $this->getPathFromFN($fn);
      $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);
      $desiredSize = $this->getHeightProportion($pathInfo['dir'].$images[$seq],$desiredWidth);
      $link = "<center>";
      if($showlink) $link .="<a href=\"".$pathInfo['url'].$images[$seq]."\" target=\"_new\">";
      $link .= "<img src=\"".$pathInfo['url'].$images[$seq]."\" height=".$desiredSize['height']." width=".$desiredSize['width']." border=\"0\">";
      if($showlink) $link .= "</a>";
      $link .= "</center><BR><BR>";
      return $link;
   }
   
   function getJSGallery($fn,$thwidth=80,$thheight=60,$width=800,$height=600,$cellpadding=2,$cellspacing=0,$bgcolor="#000000",$selbg="#FFFFFF",$nextandprev=TRUE) {
      $pathInfo = $this->getPathFromFN($fn);
      $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);

      $str = "";
      $jsc = "";
      $tbl = "";

      $tbl .= "<table cellpadding=\"".$cellpadding."\" cellspacing=\"".$cellspacing."\">\n";

      $jsc .= "<script language=\"javascript\">\n";
      $jsc .= "var curindx = 0;\n";
      $jsc .= "var jsfimg = new Array();\n";

      for ($i=0; $i<count($images); $i++) {
         $jsc .= "jsfimg[".$i."] = '".$pathInfo['url'].$images[$i]."';\n";

         if (($i % 3)==0) $tbl .= "<tr>";
         $img = $images[$i];
         if (file_exists($pathInfo['dir']."thumbs/".$img)) $img="thumbs/".$img;
         if ($seq == $i) $tbl .= "<td id=\"jsfimgth".$i."\" bgcolor=\"".$selbg."\">";
         else $tbl .= "<td id=\"jsfimgth".$i."\" bgcolor=\"".$bgcolor."\">";
         $tbl .= "<div style=\"height:".$thheight."px;width:".$thwidth."px;overflow:hidden;\" onclick=\"show_pic(".$i.");\">";
         $tbl .= "<img src=\"".$pathInfo['url'].$img."\" style=\"outline:0;max-width:100%;height:auto;\" border=\"0\">";
         $tbl .= "</div>";
         $tbl .= "</td>";
         if (($i % 3)==2) $tbl .= "</tr>\n";
      }   
      if ((count($images)%3)==1) $tbl.="<td></td><td></td></tr>\n";
      if ((count($images)%3)==2) $tbl.="<td></td></tr>\n";
      $tbl .= "</table>\n";
      
      $jsc .= "var images = new Array();\n";
      $jsc .= "function preload() {\n";
      $jsc .= "   for (i = 0; i < jsfimg.length; i++) {\n";
      $jsc .= "      images[i] = new Image();\n";
      $jsc .= "      images[i].src = jsfimg[i];\n";
      $jsc .= "   }\n";
      $jsc .= "}\n";
      $jsc .= "function show_pic(indx) {\n";
      $jsc .= "while(indx<0) indx = jsfimg.length + indx;\n";
      $jsc .= "while(indx>=jsfimg.length) indx = indx - jsfimg.length;\n";
      $jsc .= "var jsfimgTag = 'jsfimg';\n";
      $jsc .= "var jsfimg2Tag = 'jsfimg2';\n";
      $jsc .= "var jsfimgCntTag = 'jsfimgcnt';\n";
      $jsc .= "var jsfimg2CntTag = 'jsfimg2cnt';\n";
      $jsc .= "var jsfimgthTag = 'jsfimgth' + indx;\n";

      $jsc .= "var oldImage = $('#' + jsfimgTag).attr('src');\n";
      //$jsc .= "alert(oldImage);\n";
      $jsc .= "   for (i=0; i<jsfimg.length; i++) {\n";
      $jsc .= "      $('#jsfimgth' + i).css('backgroundColor', '".$bgcolor."');\n";
      $jsc .= "   }\n";
      $jsc .= "$('#' + jsfimgthTag).css('backgroundColor', '".$selbg."');\n";


      $jsc .= "$('#' + jsfimg2Tag).attr('src',oldImage);\n";
      $jsc .= "$('#' + jsfimg2Tag).css('width', '100%');\n";
      $jsc .= "$('#' + jsfimg2Tag).css('height', 'auto');\n";
      $jsc .= "$('#' + jsfimg2Tag).css('outline', '0');\n";

      $jsc .= "$('#' + jsfimgCntTag).css('display', 'none');\n";
      $jsc .= "$('#' + jsfimgTag).css('width', '100%');\n";
      $jsc .= "$('#' + jsfimgTag).css('height', 'auto');\n";
      $jsc .= "$('#' + jsfimgTag).css('outline', '0');\n";
      $jsc .= "$('#' + jsfimgTag).attr('src',jsfimg[indx]);\n";
      $jsc .= "$('#' + jsfimgCntTag).fadeIn(1000);\n";
      $jsc .= "curindx = indx;\n";
      //$jsc .= "alert('passed index: ' + indx);\n";
      //$jsc .= "alert('cur index: ' + curindx);\n";
      $jsc .= "}\n";
      $jsc .= "window.onload = preload;\n";
      $jsc .= "</script>\n";

      $str .= $jsc."<table cellpadding=\"0\" cellspacing=\"2\"><tr valign=\"top\">";
      $str .= "<td>".$tbl."</td><td></td><td>";
      $str .= "<div id=\"jsfimgoutercnt\" style=\"position:relative;padding:0px;margin:0px;height:".$height."px;width:".$width."px;overflow:hidden;\">";
      $str .= "  <div id=\"jsfimgcnt\" style=\"position:absolute;left:0px;top:0px;height:".$height."px;width:".$width."px;overflow:hidden;z-index:10;background-color:#FFFFFF;\">";
      $str .= "    <img id=\"jsfimg\" src=\"".$pathInfo['url'].$images[0]."\" style=\"outline:0;max-width:100%;height:auto;\" border=\"0\">";
      $str .= "  </div>\n";
      $str .= "  <div id=\"jsfimg2cnt\" style=\"position:absolute;left:0px;top:0px;height:".$height."px;width:".$width."px;overflow:hidden;z-index:9;background-color:#FFFFFF;\">";
      $str .= "    <img id=\"jsfimg2\" src=\"".$pathInfo['url'].$images[0]."\" style=\"outline:0;max-width:100%;height:auto;\" border=\"0\">";
      $str .= "  </div>\n";
      $str .= "</div>\n";
      if ($nextandprev) {
         $str .= "<table cellpadding=\"5\" cellspacing=\"0\"><tr>\n";
         $str .= "<td><a href=\"#\" onclick=\"show_pic((curindx-1));\" style=\"font-size:12px;font-family:tahoma;font-weight:bold;color:#333333;text-decoration:none;\">&lt; Previous</td>";
         $str .= "<td><a href=\"#\" onclick=\"show_pic((curindx+1));\" style=\"font-size:12px;font-family:tahoma;font-weight:bold;color:#333333;text-decoration:none;\">Next &gt;</td>";
         $str .= "</tr></table>\n";
      }
      $str .= "</td></tr></table>\n";

      return $str;
   }

   function getThumbnails($fn,$seq,$url,$width=80,$height=60,$cellpadding=2,$cellspacing=0,$bgcolor="#000000",$selbg="#FFFFFF",$nextandprev=TRUE) {
      $pathInfo = $this->getPathFromFN($fn);
      $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);
      $link = "<table cellpadding=\"".$cellpadding."\" cellspacing=\"".$cellspacing."\">\n";
      for ($i=0; $i<count($images); $i++) {
         if (($i % 3)==0) $link .= "<tr>";
         $img = $images[$i];
         if (file_exists($pathInfo['dir']."thumbs/".$img)) $img="thumbs/".$img;
         $desiredSize = fitToBoxProportion($pathInfo['dir'].$img, $width, $height);
         if ($seq == $i) $link .= "<td bgcolor=\"".$selbg."\">";
         else $link .= "<td bgcolor=\"".$bgcolor."\">";
         $link .= "<a href=\"".$url."&seq=".$i."\">";
         $link .= "<img src=\"".$pathInfo['url'].$img."\" height=\"".$desiredSize['height']."\" width=\"".$desiredSize['width']."\" border=\"0\">";
         $link .= "</a>";
         $link .= "</td>";
         if (($i % 3)==2) $link .= "</tr>\n";
      }
      if ((count($images)%3)==1) $link.="<td></td><td></td></tr>\n";
      if ((count($images)%3)==2) $link.="<td></td></tr>\n";
      $link .= "</table>\n";

      if ($nextandprev) {
         if ($seq<(count($images)-1)) $link .= "<br><center><a href=\"".$url."&seq=".($seq+1)."\"><b>Next Image</b></a></center>";
         else $link .= "<br><center><a href=\"".$url."&seq=0\"><b>Next Image</b></a></center>";
      }

      return $link;
   }

   function displayBreadCrumb($fn,$display=TRUE,$url=NULL) {
      if ($url==NULL) $url=getBaseURL().$GLOBALS['codeFolder']."controller.php?action=viewpics";
      $result = $this->getCategory($fn);
      $currentCat = "Pictures";
      $indent="";
      $count = count($result['fil']);
      print "<a href=\"".$url."&subaction=all\">Pictures Home</a><BR>";

      for ($i = 0; $i < $count; $i++) {
         $link="";
         $indent .= "&nbsp;&nbsp;&nbsp;&nbsp;";
         if (0 == strcmp($fn,$result['fil'][$i]) | 0 == strcmp($fn,$result['cat'][$i])) {
            $link = $indent.$result['cat'][$i]."<br>";
            $currentCat = $result['cat'][$i];
         }
         else {
            $link = $indent."<a href=\"".$url."&subaction=all&fn=".$result['fil'][$i]."\">";
            $link .= $result['cat'][$i]."</a><br>";
         }
         print $link;
      }
      //if ($count > 0 & $display) print "<HR>";
      return $currentCat;
   }

   function displayPics($authInfo,$currentCat,$perRow=4,$total=16,$desiredWidth=160,$url=NULL) {
      print $this->getDisplayPics($authInfo,$currentCat,$perRow,$total,$desiredWidth,$url);
   }

   function getDisplayPics($authInfo,$currentCat,$perRow=4,$total=16,$desiredWidth=160,$url=NULL) {
      $str = "";
      if ($url==NULL) $url=getBaseURL().$GLOBALS['codeFolder']."controller.php?action=viewpics";
      $fn = $authInfo['fn'];
      $page = $authInfo['page'];
      if ($page == null) $page=1;
      $viewAll = $authInfo['viewAll'];
      if ($viewAll == null) $viewAll=0;

      $pathInfo = $this->getPathFromFN($fn);
      $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);

      if ($page == NULL) $page = 1;
      if ($viewAll == NULL) $viewAll = 0;
      $pgTbl="";

      $count = count($images);
      $start=0;
      $end = $count;
      //Display the page numbers... and viewAll link
      if ($viewAll!=1 && $count > $total) {
         $numOfPages = ceil($count / ($total*1.0));
         
         $paramArr = $this->arrCopy($authInfo);
         $paramArr['viewAll'] = 1;
         $paramArr['subaction'] = "all";
         $params = $this->getParamList($paramArr);
         $pgTbl = "<table cellpadding=\"2\" cellspacing=\"0\"><tr><td align=\"left\"><a href=\"".$url."&".$params."\">View All</a></td>";
         $pgTbl .= "<td align=\"right\">Pages: ";
         for ($j = 1; $j<=$numOfPages; $j++) {
            if ($j == $page) $pgTbl .= $j." ";
            else {
               $paramArr = $this->arrCopy($authInfo);
               $paramArr['page'] = $j;
               $paramArr['subaction'] = "all";
               $params = $this->getParamList($paramArr);
               $pgTbl .= "<a href=\"".$url."&".$params."\">".$j."</a> ";
            }
         }
         $pgTbl .= "</td></tr></table>";
         $start = ($page - 1) * $total;
         $end = $start + $total;
         if ($end > $count) $end = $count;
      }
   
      if ($count > 0) {
         $str .= $pgTbl;
         $str .= "<BR>\n";
         $str .= "<CENTER><H2>".$currentCat."</H2>\n";
         $str .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"center\"><TR>";
         if ($count==1 && $start==0) {
            $str .= $this->getDisplaySinglePicture($authInfo['fn'],0,640,FALSE);
         } else {
            for ($i = $start; $i < $end; $i++) {
               $paramArr = $this->arrCopy($authInfo);
               $paramArr['seq'] = $i;
               $paramArr['subaction'] = "single";
               $params = $this->getParamList($paramArr);
               $link = "<TD align=\"center\"><a href=\"".$url."&".$params."\">";
               if (file_exists($pathInfo['dir']."thumbs/".$images[$i])) {
                  $link .= "<img src=\"".$pathInfo['url']."thumbs/".$images[$i]."\" border=\"0\">";
               } else {
                  $desiredSize = $this->getHeightProportion($pathInfo['dir'].$images[$i],$desiredWidth);
                  $link .= "<img src=\"".$pathInfo['url'].$images[$i]."\" width=\"".$desiredSize['width']."\" height=\"".$desiredSize['height']."\" border=\"0\">";
               }
               $link .= "</a>";
               $link .= "</TD>\n";
               if ((($i % $perRow)-($perRow-1)) == 0)  $link.="\n</tr>\n<tr>\n";
               $str .= $link;
            }
         }
         $str .= "</tr></table>";
         $str .= "</center><BR>";
         $str .= $pgTbl;
         //$str .= "\n<hr>\n";
      }
      else {
        //no images to display
      }
      return $str;
   }

   function displayDirs($fn) {
         print $this->getDisplayDirs($fn);
         //print "<HR>";
   }

   function getDisplayDirs($fn,$picprops=FALSE,$url=NULL) {
      $str = "";
      if ($url==NULL) $url=getBaseURL().$GLOBALS['codeFolder']."controller.php?action=viewpics";
      $directories = $this->getDirs($fn);
      if (count($directories) > 0) {
         $ss = new Version();
         $str .= "<BR>";
         $link = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
         if (!$picprops) $link .= "\n<TR>";
         foreach($directories as $directory) {
            if (0!=strcmp("config",strtolower(substr($directory,0,6))) && 0!=strcmp("thumbs",strtolower(substr($directory,0,6)))) {
               $display = str_replace("/","",$directory);
               $display = str_replace("_"," ",$display);
               if ($picprops) {
                  $img = trim($ss->getValue($fn.$directory."_img"));
                  if ($img==NULL) $img = trim($ss->getValue($directory."_img"));
                  if ($img==NULL) {
                     $pathInfo = $this->getPathFromFN($fn);
                     $images = $this->getImages($pathInfo['dir'],$pathInfo['filter']);
                     if ($images!=NULL && count($images)>0) $img=$pathInfo['url'].$images[0];
                  }
                  if ($img!=NULL) $display = "<img src=\"".$GLOBALS['picURL'].$img."\" border=\"0\">";
                  $link .= "\n<tr><TD><a href=\"".$url."&subaction=all&fn=".$fn.$directory."\">".$display."</a></td></tr>";
                  $link .= "\n<tr><td><img src=\"".getBaseURL().$GLOBALS['imagesDir']."pixel.gif\" height=\"8\" width=\"1\"></td></tr>\n";
               } else {
                  $link .= "\n<TD><a href=\"".$url."&subaction=all&fn=".$fn.$directory."\">".$display."</a></td>";
                  $link .= "<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>";
               }
            }
         }
         if (!$picprops) $link .= "\n</tr>";
         $link .= "</table>\n\n\n\n";
         $str .= $link;
      } else {
         //$str .= "<b>Pictures coming soon...</b>";
      }
      return $str;
   }

   function getDisplayDirsDropdown($fn,$url=NULL) {
      $str = "";
      if ($url==NULL) $url=getBaseURL().$GLOBALS['codeFolder']."controller.php?action=viewpics";
      $directories = $this->getDirs($fn);
      if (count($directories) > 0) {
         $str .= "<form id=\"pics".getRandomNum()."\">";
         $ss = new Version();
         foreach($directories as $directory) {
            if (0!=strcmp("config",strtolower(substr($directory,0,6))) && 0!=strcmp("thumbs",strtolower(substr($directory,0,6)))) {
               $display = str_replace("/","",$directory);
               $display = str_replace("_"," ",$display);
               $txt = trim($ss->getValue($fn.$directory."_txt"));
               if ($txt==NULL) $txt = trim($ss->getValue($directory."_txt"));
               if ($txt==NULL) $txt = $display;
               $dirOpts[$txt] = $directory;
            }
         }
         $extra = "onChange=\"window.location.href='".$url."&subaction=all&fn=".$fn."' + this.form.picdir.options[this.form.picdir.selectedIndex].value;\"";
         $str .= getOptionList("picdir", $dirOpts, NULL, TRUE, $extra);
         $str .= "</form>";
      } else {
         $str .= "<b>Pictures coming soon...</b>";
      }
      return $str;
   }

   function getDirs($fn) {
      $template = new Template;
      $dir = $GLOBALS['picDir'].$fn;
      $files = $template->list_dir($dir,FALSE);
      return $files;
   }

   //---------------------------------------------------------------------------------------------------------
   // Static methods zip file
   //---------------------------------------------------------------------------------------------------------
   
   function processUploadZipFile($wm){
      if (is_uploaded_file($_FILES['zipfile']['tmp_name']) && 0==strcmp(strtolower(substr($_FILES['zipfile']['name'],-4)),".zip")) {
         $counter = 0;
         $dateToday = getDateForDB();
         $prefix = $dateToday."_".$counter;
         $fileUpld = $prefix."_".$_FILES['zipfile']['name'];
         while(file_exists($GLOBALS['srvyDir'].$fileUpld)){
            $counter++;
            $prefix = $dateToday."_".$counter;
            $fileUpld = $prefix."_".$_FILES['zipfile']['name'];
         }
         move_uploaded_file($_FILES['zipfile']['tmp_name'],$GLOBALS['picDir'].$fileUpld);

         $tempDir = $prefix."_"."tmp/";
         $this->processZipFile($fileUpld,$tempDir,$wm);
      } else {
         return FALSE;
      }
   }

   function processZipFile($fileUpld,$tempDir,$wm){
      if(!is_dir($GLOBALS['picDir'].$tempDir)) mkdir($GLOBALS['picDir'].$tempDir); 
      $zip = new ZipArchive;
      $zip->open($GLOBALS['picDir'].$fileUpld); 
      $zip->extractTo($GLOBALS['picDir'].$tempDir); 
      $zip->close(); 

      $this->processDirectory($GLOBALS['picDir'].$tempDir,$GLOBALS['picDir'],"",$wm);
      rmdir($GLOBALS['picDir'].$tempDir);
   }

   function processDirectory($src,$dest,$dir,$wm){
      $template = new Template();
      $files =  $template->list_dir($src.$dir, TRUE);
      $dirs =  $template->list_dir($src.$dir, FALSE);
      if(!is_dir($dest.$dir)) mkdir($dest.$dir); 
      for ($i=0; $i<count($files); $i++) {
         if ($this->load($src.$dir.$files[$i])) {
            $this->resizeToRectangle(500, 375);
            $this->addWatermark($wm);
            $picFN = $dest.$dir.$files[$i];
            $counter=0;
            while(file_exists($picFN) || file_exists($thumbFN)){
               $counter++;
               $picFN = $dest.$dir.$counter."_".$files[$i];
            }
            $this->save($picFN);
            $this->resizeToRectangle(100, 75);
            $this->save($dest.$dir."tn_".$files[$i]);
            $this->closeimage();
            unlink($src.$dir.$files[$i]);
         }
      }
      for ($i=0; $i<count($dirs); $i++) {
         $this->processDirectory($src,$dest,$dir.$dirs[$i],$wm);
         rmdir($src.$dir.$dirs[$i]);
      }
   }

   function processDirectory2($src,$dest,$dir,$wm){
      $template = new Template();
      $files =  $template->list_dir($src.$dir, TRUE);
      $dirs =  $template->list_dir($src.$dir, FALSE);
      if(!is_dir($dest.$dir)) mkdir($dest.$dir); 
      for ($i=0; $i<count($files); $i++) {
         if ($this->load($src.$dir.$files[$i])) {
            $this->resizeToRectangle(500, 375);
            $this->addWatermark($wm);
            $picFN = $dest.$dir.$files[$i];
            $counter=0;
            while(file_exists($picFN) || file_exists($thumbFN)){
               $counter++;
               $picFN = $dest.$dir.$counter."_".$files[$i];
            }
            $this->save($picFN);
            $this->resizeToRectangle(100, 75);
            $this->save($dest.$dir."tn_".$files[$i]);
            $this->closeimage();
            unlink($src.$dir.$files[$i]);
         }
      }
      for ($i=0; $i<count($dirs); $i++) {
         $this->processDirectory($src,$dest,$dir.$dirs[$i],$wm);
         rmdir($src.$dir.$dirs[$i]);
      }
   }

   //This allows you to specify height and width of an image, and puts images into pics or thumbs directory
   function processPictures($src,$dest,$dir,$wm, $picW=500, $picH=375, $thumbW=100, $thumbH=75){
//print "<br>\nprocessPictures(".$src.", ".$dest.", ".$dir.", ".$wm.", ".$picW.", ".$picH.", ".$thumbW.", ".$thumbH." )<br>\n";
      $template = new Template();
      $files =  $template->getFiles($src.$dir,"","jpg");
      $dirs =  $template->list_dir($src.$dir, FALSE);
//print "<BR>files: ";
//print_r($files);
//print "<BR>dirs: ";
//print_r($dirs);
      if(!is_dir($dest.$dir)) mkdir($dest.$dir); 
      if(!is_dir($dest.$dir."pics/") && $files != NULL && count($files)>0) mkdir($dest.$dir."pics/"); 
      if(!is_dir($dest.$dir."thumbs/") && $files != NULL && count($files)>0) mkdir($dest.$dir."thumbs/"); 
      for ($i=0; $i<count($files); $i++) {
         if ($this->load($src.$dir.$files[$i])) {
            $this->resizeToRectangle($picW, $picH);
            $this->addWatermark($wm);
            $picFN = $dest.$dir."pics/".$files[$i];
            $thumbFN = $dest.$dir."thumbs/".$files[$i];
            $counter=0;
            while(file_exists($picFN) || file_exists($thumbFN)){
               $counter++;
               $picFN = $dest.$dir."pics/".$counter."_".$files[$i];
               $thumbFN = $dest.$dir."thumbs/".$counter."_".$files[$i];
            }
            $this->save($picFN);
            $this->resizeToRectangle($thumbW, $thumbH);
            $this->save($thumbFN);
            $this->closeimage();
            unlink($src.$dir.$files[$i]);
         }
      }
      for ($i=0; $i<count($dirs); $i++) {
         if (0!=strcmp($dirs[$i],"thumbs/") && 0!=strcmp($dirs[$i],"pics/")) {
            $this->processPictures($src,$dest,$dir.$dirs[$i],$wm,$picW,$picH,$thumbW,$thumbH);
            if (0!=strcmp($src,$dest)) rmdir($src.$dir.$dirs[$i]);
         }
      }
      print "<br><b>Step (watermark/resize).</b> done. ".count($files)." images processed.<br><br>";
   }


   function unzipfilesonly($file,$toDir=""){
       $zip=zip_open($file);
       if(!$zip) {return("Unable to proccess file '{$file}'");}
       else print "<br><b>Step (unzip files).</b> ".$file." opened successfully.  Copying to ".$toDir."<br><br>";

       $e='';
       while($zip_entry=zip_read($zip)) {
          $zname=zip_entry_name($zip_entry);
          $zfile=basename($zname);
          if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Unable to proccess file '{$zname}'";continue;}
   
          $zip_fs=zip_entry_filesize($zip_entry);
          if(empty($zip_fs)) continue;

          //print "saving file: ".$zfile."<br>";
   
          $zz=zip_entry_read($zip_entry,$zip_fs);
   
          $z=fopen($toDir.$zfile,"w");
          fwrite($z,$zz);
          fclose($z);
          zip_entry_close($zip_entry);
       } 
       zip_close($zip);
   
       return($e);
   } 
   
   function unzip($file,$toDir=""){
       $zip=zip_open($file);
       if(!$zip) {return("Unable to proccess file '{$file}'");}
   
       $e='';
   
       while($zip_entry=zip_read($zip)) {
          $zdir=dirname(zip_entry_name($zip_entry));
          $zname=zip_entry_name($zip_entry);
   
          if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Unable to proccess file '{$zname}'";continue;}
          if(!is_dir($toDir.$zdir)) $this->mkdirr($toDir.$zdir,0777);
   
          #print "{$zdir} | {$zname} \n";
   
          $zip_fs=zip_entry_filesize($zip_entry);
          if(empty($zip_fs)) continue;
   
          $zz=zip_entry_read($zip_entry,$zip_fs);
   
          $z=fopen($toDir.$zname,"w");
          fwrite($z,$zz);
          fclose($z);
          zip_entry_close($zip_entry);
   
       } 
       zip_close($zip);
   
       return($e);
   } 
   
   function mkdirr($pn,$mode=null) {
   
     if(is_dir($pn)||empty($pn)) return true;
     $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);
   
     if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}
   
     $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
     if($this->mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
     return false;
   }
}

?>
