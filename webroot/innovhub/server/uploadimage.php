<?php
include "../jsfcode/Classes.php";

// **************************
// CONFIGURATION PARAMETERS:

//$bgclr_body = "#367aba";
//$bgclr_title = "#97bc19";
//$ftclr_body = "#DDDDDD";
//$ftclr_title = "#FFFFFF";

//$bgclr_body = "#EEEEEE";
//$bgclr_title = "#DDDDDD";
//$ftclr_body = "#717171";
//$ftclr_title = "#222222";

$fontfamily = "verdana";
if(getParameter("fontfamily")!=NULL) $fontfamily = getParameter("fontfamily");

$bgclr_body = "#EEEEEE";
if(getParameter("bgclr_body")!=NULL) $bgclr_body = getParameter("bgclr_body");
$bgclr_body = checkforhash($bgclr_body);

$bgclr_title = "#DDDDDD";
if(getParameter("bgclr_title")!=NULL) $bgclr_title = getParameter("bgclr_title");
$bgclr_title = checkforhash($bgclr_title);

$ftclr_body = "#717171";
if(getParameter("ftclr_body")!=NULL) $ftclr_body = getParameter("ftclr_body");
$ftclr_body = checkforhash($ftclr_body);

$ftclr_title = "#222222";
if(getParameter("ftclr_title")!=NULL) $ftclr_title = getParameter("ftclr_title");
$ftclr_title = checkforhash($ftclr_title);

// Determine the system's max upload size - or use the desired one if passed in
$maxuploadsz = 10;
$maxupl_pf = strtoupper(substr(ini_get('upload_max_filesize'),-1));
//print "<br>".$maxupl_pf."<br>";
$maxuploadsz = substr(ini_get('upload_max_filesize'),0,(strlen(ini_get('upload_max_filesize')) - 1));
//print "<br>".$maxuploadsz."<br>";
if(0==strcmp($maxupl_pf,"K")) $maxuploadsz = $maxuploadsz / 1024;
else if(0==strcmp($maxupl_pf,"G")) $maxuploadsz = $maxuploadsz * 1024;
if(getParameter("maxuploadsz")!=NULL) $maxuploadsz = getParameter("maxuploadsz");



function checkforhash($clr){
   if (0!=strcmp(substr($clr,0,1),"#")) {
      $clr = "#".$clr;
   }
   return $clr;
}



//$default_jstocall = "jsfwd_uploadfinished";

// **************************
// USAGE:
//  //uploadimage.php?userid=X&token=Y&wd_id=W&field_id=Z&prefix=ABC&jstocall=FUNCTION
//  var iurl = 'uploadimage.php?userid=' + globaluser.userid + '&token=' + globaluser.token;
//  str = str + '<div style=\"clear:both;\"></div>';
//  str = str + '<table cellpadding=\"1\" cellspacing=\"1\"><tr><td>';
//  str = str + '<div ';
//  str = str + 'style=\"font-size:16px;font-weight:bold;color:#000000;cursor:pointer;background-color:#E0E0E0;padding:8px;border:1px solid #000000;border-radius:3px;margin-right:5px;margin-top:15px;margin-bottom:12px;\" ';
//  str = str + 'onclick=\"window.open(\'' + iurl + '\');\"';
//  str = str + '>+ Image</div>';
//  str = str + '</td></tr></table>\n';
//  ...
//
//
//  function uploadfinished(fn){
//    //SAVE fn URL TO WDATA
//    //DISPLAY fn ON SCREEN
//  }



//error_reporting(E_ALL);


$wd_id = getParameter("wd_id");
$field_id = getParameter("field_id");
$prefix = getParameter("prefix");
if($prefix==NULL) $prefix="jsfwd";
$userid = getParameter("userid");
$token = getParameter("token");
$resize = getParameter("resize");
$wm = getParameter("wm");
$imageonly = getParameter("imageonly");
$buttonstyle = getParameter("buttonstyle");
$titlestyle = getParameter("titlestyle");
//$jstocall = getParameter("jstocall");
//if ($jstocall == NULL) $jstocall = $default_jstocall;

$showupload = TRUE;
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <link rel="apple-touch-icon" href="custom_icon.png"/>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <meta name="msapplication-tap-highlight" content="no" />
    <title>File upload</title>
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="pragma" content="no-cache">
    <script language="javascript" type="text/javascript" src="jquery-1.11.2.min.js"></script>
    <script language="javascript" type="text/javascript">
    
      window.addEventListener("dragover",function(e){
        e = e || event;
        e.preventDefault();
      },false);
      window.addEventListener("drop",function(e){
        e = e || event;
        e.preventDefault();
      },false);
      
      var winwidth;
      var winheight;

      jQuery(document).ready(function() {
         winwidth = jQuery(window).width();
         winheight = jQuery(window).height();
         jQuery('#winwidth').val(winwidth); 
         jQuery('#winheight').val(winheight); 
         jQuery('#outer').css('width',winwidth + 'px').css('height',winheight + 'px').css('overflow-x','hidden').css('overflow-y','auto');
         
         if(winwidth<700) jQuery('#drop-files-container').hide();
      });
    </script>
    
</head>
<body style="margin:0;">
<?php
   print "<div id=\"outer\" style=\"";
   print "background-color:".$bgclr_body.";";
   print "font-family:".$fontfamily.";";
   print "color:".$ftclr_body.";";
   print "position:relative;";
   if(getParameter("winwidth")!=NULL && getParameter("winheight")!=NULL) {
      print "width:".getParameter("winwidth")."px;";
      print "height:".getParameter("winheight")."px;";
   }
   print "font-size:14px;";
   print "\">\n";
?>
   <div style="position:absolute;right:6px;top:6px;color:RED;font-size:20px;cursor:pointer;" onclick="window.close();">x</div>
   <div style="padding:32px 20px 10px 20px;">



<?php
if ($userid!=NULL && $token!=NULL) {
   //$ua = new UserAcct();
   //$user = $ua->getUser($userid);

   //if (0==strcmp($token,$user['token'])) {
   if ($userid>0) {
   	 $titletxt = "Upload file ";
   	 $temp = getParameter("title");
   	 if ($temp != NULL) $titletxt .= " for ".$temp;
   	 
   	 $temp = getParameter("overridetitle");
   	 if ($temp != NULL) $titletxt = $temp;
   	 
   	 $mainlogo = getParameter("mainlogo");
   	 if($mainlogo!=NULL) {
   	    print "<div style=\"margin-bottom:5px;\">";
   	    print "<img src=\"".$mainlogo."\" style=\"max-width:190px;max-height:40px;width:auto;height:auto;\">";
   	    print "</div>";
   	 }
   	 
   	 $maintitle = getParameter("maintitle");
   	 if($maintitle!=NULL) {
   	    $maintitleclr = getParameter("maintitleclr");
   	    if($maintitleclr==NULL) $maintitleclr = "#222222";
   	    $maintitleclr = checkforhash($maintitleclr);
   	    print "<div style=\"margin-bottom:15px;font-size:20px;font-weight:bold;color:".$maintitleclr.";\">";
   	    print $maintitle;
   	    print "</div>";
   	    
   	 }
   	 
   	 if(0==strcmp($titlestyle,"simple")) {
         print "<div style=\"color:".$ftclr_title.";font-size:14px;font-weight:bold;\">";
         print $titletxt;
         print "</div>";
   	 } else {
         print "<div style=\"background-color:".$bgclr_title.";color:".$ftclr_title.";font-size:16px;font-weight:bold;padding:5px;border:1px solid #999999;border-radius:5px;\">";
         print $titletxt;
         print "</div>";
   	 }
   	 
   	 if($imageonly == 1) {
         print "<div style=\"color:".$ftclr_title.";font-size:10px;font-style:italic;margin:5px 0px 5px 0px;\">";
         print "Please upload an image in the following formats: *.jpg, *.png, *.gif";
         print "</div>";
   	 }
?>

      <div style="width:10px;height:15px;overflow:hidden;"></div>

<?php 
   if (getParameter("uploadimage")==1) {
      $fileparam = "logofile";
      if(!is_uploaded_file($_FILES[$fileparam]['tmp_name'])) $fileparam = "dragfile";
      
      //$newfndir = $GLOBALS['srvyDir'];
      $newfndir = "";
      $newfn = date("YmdHis");
      if($userid!=NULL) $newfn .= "_".removeSpecialChars($userid);
      if($prefix!=NULL) $newfn .= "_".removeSpecialChars($prefix);
      if($wd_id!=NULL) {
         if(!file_exists($GLOBALS['srvyDir'].$newfndir."wd_".removeSpecialChars($wd_id))) {
            if (mkdir($GLOBALS['srvyDir'].$newfndir."wd_".removeSpecialChars($wd_id), 0755)) {
               $newfndir .= "wd_".removeSpecialChars($wd_id)."/";
            }
         } else {
            $newfndir .= "wd_".removeSpecialChars($wd_id)."/";
         }
         
         $newfn .= "_".removeSpecialChars($wd_id);
         if($field_id!=NULL) $newfn .= "_".removeSpecialChars($field_id);
      }

      $filetypes = NULL;
      if($imageonly==1) $filetypes = ".jpg,.jpeg,.png,.gif";
      $fn = saveUploadedFile($fileparam,$GLOBALS['srvyDir'].$newfndir,"",$newfn,$filetypes);

      if ($fn!=NULL) {
         $ext = getExtension($fn);
         if($imageonly!=1 || 0==strcmp($ext,".jpg") || 0==strcmp($ext,".jpeg") || 0==strcmp($ext,".png") || 0==strcmp($ext,".gif")){
            $showupload = FALSE;
            $img = "<img src=\"".getBaseURL()."jsfimages/icon_unknown.png\" style=\"\">";
            if(0==strcmp($ext,".jpg") || 0==strcmp($ext,".jpeg") || 0==strcmp($ext,".png") || 0==strcmp($ext,".gif")) {
               if($resize==1) {
                  $jsfi = new JSFImage();
                  $chkld = $jsfi->load($GLOBALS['srvyDir'].$newfndir.$fn);
                  if ($chkld) {
                     $jsfi->resizeToRectangle(900,800);
                     if($wm!=NULL) {
                        //print "\n<!-- wm now: ".$wm." -->\n";
                        if(!file_exists($wm)) $wm = $GLOBALS['baseDir'].$wm;
                        //print "\n<!-- wm aftercheck: ".$wm." -->\n";
                        //if(file_exists($wm)) print "\n<!-- jsfi->addWatermark(wm,80); -->\n";
                        if(file_exists($wm)) $jsfi->addWatermark($wm,50);
                     }
                     if(file_exists($GLOBALS['srvyDir'].$newfndir.$fn)) unlink($GLOBALS['srvyDir'].$newfndir.$fn);
                     $jsfi->save($GLOBALS['srvyDir'].$newfndir.$fn,NULL,80);
                     $jsfi->closeimage();
                  }
               }

              $img = "<img src=\"".$GLOBALS['srvyURL'].$newfndir.$fn."\" style=\"height:100px;width:auto;\">";
            } else if(0==strcmp($ext,".doc") || 0==strcmp($ext,".docx")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_doc.jpg\" style=\"\"> <span style=\"font-size:8px;\">word doc uploaded</span>";
            } else if(0==strcmp($ext,".html")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_html.jpg\" style=\"\"> <span style=\"font-size:8px;\">html doc uploaded</span>";
            } else if(0==strcmp($ext,".pdf")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_pdf.jpg\" style=\"\"> <span style=\"font-size:8px;\">pdf doc uploaded</span>";
            } else if(0==strcmp($ext,".txt")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_txt.jpg\" style=\"\"> <span style=\"font-size:8px;\">text doc uploaded</span>";
            } else if(0==strcmp($ext,".zip")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_zip.jpg\" style=\"\"> <span style=\"font-size:8px;\">zip file uploaded</span>";
            } else if(0==strcmp($ext,".ppt") || 0==strcmp($ext,".pptx")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_ppt.jpg\" style=\"\"> <span style=\"font-size:8px;\">powerpoint doc uploaded</span>";
            } else if(0==strcmp($ext,".xlsx") || 0==strcmp($ext,".xlsm") || 0==strcmp($ext,".xlt") || 0==strcmp($ext,".xls")) {
              $img = "<img src=\"".getBaseURL()."jsfimages/icon_xl.jpg\" style=\"\"> <span style=\"font-size:8px;\">spreadsheet uploaded</span>";
            }
            
?>
     <script language="javascript" type="text/javascript">
      window.opener.postMessage(<?php echo json_encode($prefix.",".$field_id.",".$wd_id.",".$GLOBALS['srvyURL'].$newfndir.$fn.",".$img); ?>,'*');
      setTimeout(function(){ window.close(); }, 5000);
     </script>
     Your file was successfully uploaded.  This window will automatically close in 4 seconds.
      <div style="width:10px;height:20px;overflow:hidden;"></div>
      <?php echo $img; ?>

<?php
            } else {
               echo("<div style=\"color:#CC3333;margin-top:5px;margin-bottom:12px;font-size:14px;\">");
               echo("Select an image to upload, please.  We're sorry, but only PNG, JPG and GIF file formats are accepted at this time.");
               echo("</div>");
            }
         } else { 
            print "Your file was not uploaded successfully. ";
            if($imageonly==1) print " Please make sure you tried a valid image format (jpg, png, gif).";
            else print " Please try again later.";
         } 
      }
      
      if($showupload) {
?>
      <div style="margin:10px;">
      
      <div style="margin-bottom:20px;background-color:#f7f7f7;position:relative;border:2px dashed #d9dde3;border-radius:4px;width:280px;height:90px;overflow:hidden;" id="drop-files-container">
      <div style="position:absolute;left:10px;top:25px;font-size:14px;font-weight:bold;color:#9bada5;text-align:center;width:260px;">
        Drop File Here
        <div style="margin-top:4px;font-size:20px;font-weight:bold;">+</div>
      </div>
      </div>

      
      <form enctype="multipart/form-data" id="uploadimagefrm" action="uploadimage.php" method="POST">
      <input type="hidden" name="uploadimage" value="1">
      <input type="hidden" name="prefix" value="<?php echo $prefix; ?>">
      <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
      <input type="hidden" name="field_id" value="<?php echo $field_id; ?>">
      <input type="hidden" name="userid" value="<?php echo $userid; ?>">
      <input type="hidden" name="token" value="<?php echo $token; ?>">
      <input type="hidden" name="winwidth" id="winwidth" value="">
      <input type="hidden" name="winheight" id="winheight" value="">
      <input type="hidden" name="resize" value="<?php echo $resize; ?>">
      <input type="hidden" name="wm" value="<?php echo $wm; ?>">
      <input type="hidden" name="imageonly" value="<?php echo $imageonly; ?>">
      <input type="hidden" name="buttonstyle" value="<?php echo $buttonstyle; ?>">
      <input type="hidden" name="titlestyle" value="<?php echo $titlestyle; ?>">
      <input type="hidden" name="fontfamily" value="<?php echo $fontfamily; ?>">
      <input type="hidden" name="bgclr_body" value="<?php echo $bgclr_body; ?>">
      <input type="hidden" name="bgclr_title" value="<?php echo $bgclr_title; ?>">
      <input type="hidden" name="ftclr_body" value="<?php echo $ftclr_body; ?>">
      <input type="hidden" name="ftclr_title" value="<?php echo $ftclr_title; ?>">
      <input id="logofile" name="logofile" type="file" style="display:none;">
      
      <?php if (0==strcmp($buttonstyle,"browse")) { ?>
       
         <div id="browsebutton" style="padding:10px;width:100px;text-align:center;font-size:14px;color:#FFFFFF;background-color:#9bada5;border-radius:4px;cursor:pointer;" onclick="jQuery('#logofile').click();">
         Browse
         </div>

      <?php } else { ?>
         
         <div id="browsebutton" style="padding:5px;text-align:center;width:168px;font-size:14px;color:#000000;background-color:#F8F8F8;border:1px solid #555555;border-radius:4px;cursor:pointer;" onclick="jQuery('#logofile').click();">
         Choose File/Take a Pic
         </div>

      <?php } ?>
      
      
      <div id="logostatus" style="width:200px;height:25px;overflow:hidden;"></div>
      <input type="submit" style="display:none;font-size:18px;border-radius:5px;padding:8px;" name="btnsubmit" id="btnsubmit" value="Upload File">
      </form>
      </div>
      
      <script language="javascript" type="text/javascript">
      jQuery('#logofile').change(function() {
      	if(!Boolean(jQuery('#logofile').val())) {
      	   jQuery('#submit').hide();
      	} else if(jQuery('#logofile')[0].files[0].size > (<?php echo $maxuploadsz; ?> * 1024 * 1024)) {
      	   alert('Sorry, we can only accept files under <?php echo $maxuploadsz; ?>MB.  Please select a smaller file.');
      	} else {
      	   //jQuery('#submit').show();
      	   //alert('here');
      	   jQuery('#uploadimagefrm').submit();
      	   //document.getElementById('uploadimagefrm').submit();
      	   jQuery('#logostatus').html('<span style=\"margin-top:5px;font-size:16px;color:#000000;font-weight:bold;\">Processing...</span>');
      	   jQuery('#browsebutton').hide();
      	   jQuery('#drop-files-container').hide();
      	}
      });
      </script>

<?php } ?>


<?php    } else { ?>
            Your file was can not be uploaded at this time.  Please try again later.
<?php    } ?>
<?php } else { ?>
      Your file was can not be uploaded at this time.  Please try again later.
<?php } ?>


   <div style="margin-top:25px;margin-bottom:15px;color:blue;font-size:10px;font-family:arial;cursor:pointer;" onClick="window.close();">Close Window</div>  

   </div>
</div>


   <script>
      function processFileUpload(droppedFiles) {
               // add your files to the regular upload form
          var uploadFormData = new FormData(document.getElementById('uploadimagefrm')); 
          if(droppedFiles.length > 0) {
      	   jQuery('#logostatus').html('<span style=\"margin-top:5px;font-size:16px;color:#000000;font-weight:bold;\">Processing...</span>');
      	   jQuery('#browsebutton').hide();
      	   jQuery('#drop-files-container').hide();
             
              uploadFormData.append('dragfile',droppedFiles[0]);
             
              //for(var f = 0; f < droppedFiles.length; f++) {
              //    uploadFormData.append('dragfile',droppedFiles[f]);
              //}
              
            jQuery.ajax({
               url : 'uploadimage.php', // use your target
               type : "POST",
               data : uploadFormData,
               cache : false,
               contentType : false,
               processData : false,
               success : function(data) {
                  jQuery('html').html(data);
               }
            });
          }
       }
       
      //jQuery('#drop-files-container').bind('drop', function(e) {
      jQuery('#outer').bind('drop', function(e) {
          var files = e.originalEvent.dataTransfer.files;
          processFileUpload(files); 
          // forward the file object to your ajax upload method
          return false;
      });       
    </script>

</body>
</html>