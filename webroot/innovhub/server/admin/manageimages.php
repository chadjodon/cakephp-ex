<?php
   //error_reporting(E_ALL);
   $dir = getParameter("dir");
   if ($dir==NULL) $dir = "/";
   if($dir[strlen($dir)-1]!='/') $dir.='/';

   $url = getBaseURL()."jsfadmin/admincontroller.php?action=manageimages";
?>

   <form name="chosedirfrm" id="chosedirfrm" action="<?php echo $url; ?>" method="POST">
   <input type="hidden" name="dir" value="">
   </form>

   <form name="deldirfrm" id="deldirfrm" action="<?php echo $url; ?>" method="POST">
   <input type="hidden" name="deldir" value="">
   <input type="hidden" name="dir" value="<?php echo $dir; ?>">
   <input type="hidden" name="deletedirectory" value="1">
   </form>

   <form name="delimgfrm" id="delimgfrm" action="<?php echo $url; ?>" method="POST">
   <input type="hidden" name="delimg" value="">
   <input type="hidden" name="dir" value="<?php echo $dir; ?>">
   <input type="hidden" name="deleteimage" value="1">
   </form>

   <table cellpadding="6" cellspacing="3" bgcolor="#DEDEDE">
   <tr><td colspan="5" style="font-size:18px;color:#212121;font-family:arial;font-weight:bold;">Your Images</td></tr>
   <tr><td colspan="5">Current Directory: <?php echo $dir; ?></td></tr>
   <tr>
      <td><div style="width:70px;height:1px;overflow:hidden;"></div></td>
      <td><div style="width:70px;height:1px;overflow:hidden;"></div></td>
      <td><div style="width:70px;height:1px;overflow:hidden;"></div></td>
      <td><div style="width:70px;height:1px;overflow:hidden;"></div></td>
      <td><div style="width:70px;height:1px;overflow:hidden;"></div></td>
   </tr>
<?php

      $newfilename = "";
      $newurlname = "";
      if ($_POST['uploadimage']==1) {
         if (is_uploaded_file($_FILES["cms_img_upl"]['tmp_name'])) {
            $counter = 1;
            $fn = str_replace(" ","_",$_FILES["cms_img_upl"]['name']);
            $newfilename = $_SESSION['imagesdir'].$dir.$fn;
            $newurlname = $_SESSION['imagesurl'].$dir.$fn;
            while(file_exists($newfilename)){
               $counter++;
               $newfilename = $_SESSION['imagesdir'].$dir.$counter."_".$fn;
               $newurlname = $_SESSION['imagesurl'].$dir.$counter."_".$fn;
            }
            move_uploaded_file($_FILES["cms_img_upl"]['tmp_name'],$newfilename);
         }
      }

      $newdirectory = $_POST["newdirectory"];
      if ($newdirectory==1) {
         $newdir = $_POST["newdir"];
         $newdir = str_replace(" ","_",$newdir);
         if (!file_exists($_SESSION['imagesdir'].$dir.$newdir)) mkdir($_SESSION['imagesdir'].$dir.$newdir);
      }

      $deletedirectory = $_POST["deletedirectory"];
      if ($deletedirectory==1) {
         $deldir = $_POST["deldir"];
         if (file_exists($_SESSION['imagesdir'].$dir.$deldir)) rmdir($_SESSION['imagesdir'].$dir.$deldir);
      }

      $deleteimage = $_POST["deleteimage"];
      if ($deleteimage==1) {
         $delimg = $_POST["delimg"];
         if (file_exists($_SESSION['imagesdir'].$dir.$delimg)) unlink($_SESSION['imagesdir'].$dir.$delimg);
      }

      $file_array=array(); 
      $dir_array=array(); 
      $handle=opendir($_SESSION['imagesdir'].$dir); 
      while ($file = readdir($handle)) { 
         if(0==strcmp($file,".") || 0==strcmp($file,"..")) continue;
      
         if(is_dir($_SESSION['imagesdir'].$dir.$file)) $dir_array[]=$file."/";
         else if (is_file($_SESSION['imagesdir'].$dir.$file)) $file_array[]=$file;
      } 
      closedir($handle);
      sort($file_array);
      sort($dir_array);

      $count = 0;
      if (0!=strcmp($dir,'/')) {
         $totallength = strlen($dir);
         $parentlength = strpos(substr(strrev($dir),1),"/")+1;
         $dirlength = $totallength - $parentlength;
         $backdir = substr($dir,0,$dirlength);
         print "<tr>";
         print "<td align=\"center\"><a href=\"#\" onclick=\"document.chosedirfrm.dir.value='".$backdir."';document.chosedirfrm.submit();\"><img src=\"".$_SESSION['imagesurl']."/jsffoldericon.gif\" width=\"35\" height=\"35\" border=\"0\"><br>&lt;- Back</a></td>";
         $count++;
      }

      for ($i=0; $i<count($dir_array); $i++) {
         if (($count%5) ==0) print "<tr>";
         print "<td align=\"center\"><a href=\"#\" onclick=\"document.chosedirfrm.dir.value='".$dir.$dir_array[$i]."';document.chosedirfrm.submit();\"><img src=\"".$_SESSION['imagesurl']."/jsffoldericon.gif\" width=\"35\" height=\"35\" border=\"0\"><br>".$dir_array[$i]."</a><br>[<a href=\"#\" style=\"color:red;font-size:10px;font-family:arial;\" onClick=\"if(confirm('Are you sure you want to delete this directory?')) { document.deldirfrm.deldir.value='".$dir_array[$i]."';document.deldirfrm.submit(); } \">Delete</a>]</td>";
         if (($count%5) ==4) print "</tr>";
         $count++;
      }
      
      for ($i=0; $i<count($file_array); $i++) {
         $extension = strtolower(substr($file_array[$i],(strlen($file_array[$i])-4),4));
         if ((0==strcmp($extension,".png") || 
            0==strcmp($extension,"jpeg") || 
            0==strcmp($extension,".gif") || 
            0==strcmp($extension,".jpg")) &&
            0!=strcmp($file_array[$i],"jsffoldericon.gif") &&
            0!=strcmp($file_array[$i],"jsfpixel.gif")) {
            if (($count%5) ==0) print "<tr>";
            print "<td align=\"center\"><a href=\"".$_SESSION['imagesurl'].$dir.$file_array[$i]."\" target=\"_new\"><img src=\"".$_SESSION['imagesurl'].$dir.$file_array[$i]."\" width=\"50\" height=\"50\" border=\"0\"><br>".$file_array[$i]."</a><br>[<a href=\"#\" style=\"color:red;font-size:10px;font-family:arial;\" onClick=\"if(confirm('Are you sure you want to delete this image?')) { document.delimgfrm.delimg.value='".$file_array[$i]."';document.delimgfrm.submit(); } \">Delete</a>]</td>";
            if (($count%5) ==4) print "</tr>";
            $count++;
         }
      }
      if (($count%5)>0) print "</tr>";

      ?>
      <tr><td colspan="5"><br></td></tr>
      <form enctype="multipart/form-data" action="<?php echo $url; ?>" method="POST">
      <input type="hidden" name="dir" value="<?php echo $dir; ?>">
      <input type="hidden" name="uploadimage" value="1">
      <tr><td colspan="5">
         <table cellpadding="0" cellspacing="1" border="0" bgcolor="blue"><tr><td>
         <table cellpadding="3" cellspacing="0" border="0" bgcolor="white"><tr>
            <td>Upload a new image</td>
            <td><input name="cms_img_upl" type="file"></td>
            <td><input type="submit" name="submit" value="Upload Image"></td>
         </tr></table>
         </td></tr></table>
      </td></tr>
      </form>
      <tr><td colspan="5"><br></td></tr>
      <form action="<?php echo $url; ?>" method="POST">
      <input type="hidden" name="dir" value="<?php echo $dir; ?>">
      <input type="hidden" name="newdirectory" value="1">
      <tr><td colspan="5">
         <table cellpadding="0" cellspacing="1" border="0" bgcolor="blue"><tr><td>
         <table cellpadding="3" cellspacing="0" border="0" bgcolor="white"><tr>
            <td>create a new directory</td>
            <td><input type="text" name="newdir" value=""></td>
            <td><input type="submit" name="submit" value="Create Directory"></td>
         </tr></table>
         </td></tr></table>
      </td></tr>
      </form>
      </table>