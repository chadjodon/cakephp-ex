<?php
   session_start();
   
   ini_set('display_errors', 1);
   error_reporting(0);
   //serror_reporting(E_ALL);
   
   $dir = $_POST["dir"];
   if ($dir==NULL) $dir = "/";
   if($dir[strlen($dir)-1]!='/') $dir.='/';
   
   $scriptname = $_POST["scriptname"];
   if ($scriptname==NULL) $scriptname = $_GET["scriptname"];
   if ($scriptname==NULL) $scriptname = "rteInsertHTML";

   $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    
?>
<!-- <?php echo $_SESSION['imagesurl'].", ".$_SESSION['imagesdir']; ?> -->
<script type="text/javascript">
	function AddImage() {
		if (document.getElementById("url").value != "") {
				var html = "";
						html += '<img src="' + document.getElementById("url").value + '"';
					if (document.getElementById("border").value != "") {
						html += ' border="' + document.getElementById("border").value + '"';
					}
					if (document.getElementById("hspace").value != "") {
						html += ' hspace="' + document.getElementById("hspace").value + '"';
					}
					if (document.getElementById("vspace").value != "") {
						html += ' vspace="' + document.getElementById("vspace").value + '"';
					}
					if (document.getElementById("align").value != "" && document.getElementById("align").value != "Default" ) {
						html += ' align="' + document.getElementById("align").value + '"';
					}
					if (document.getElementById("alt").value != "") {
						html += ' alt="' + document.getElementById("alt").value + '"';
						html += ' title="' + document.getElementById("alt").value + '"';
					}
						html += ' />';
			window.opener.<?php echo $scriptname; ?>(html);
			window.close();
		} else {
			alert('You must select an image!');
		}
	}
</script>
<style type="text/css">
body, td {
background-color:#ECE9D8;
font-family:arial;
font-size:11px;
}
input {
font-family:arial;
font-size:11px;
}
select {
font-family:arial;
font-size:11px;
}
</style>

   <form name="chosedirfrm" id="chosedirfrm" action="<?php echo $protocol.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>" method="POST">
   <input type="hidden" name="dir" value="">
   <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
   </form>

   <form name="deldirfrm" id="deldirfrm" action="<?php echo $protocol.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>" method="POST">
   <input type="hidden" name="deldir" value="">
   <input type="hidden" name="dir" value="<?php echo $dir; ?>">
   <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
   <input type="hidden" name="deletedirectory" value="1">
   </form>

   <form name="delimgfrm" id="delimgfrm" action="<?php echo $protocol.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>" method="POST">
   <input type="hidden" name="delimg" value="">
   <input type="hidden" name="dir" value="<?php echo $dir; ?>">
   <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
   <input type="hidden" name="deleteimage" value="1">
   </form>

<?php
   $newurlname = "";
   if ($_POST['choseimage']==1) {
      $newurlname = $_SESSION['imagesurl'].$dir.$_POST['image'];
      //$newurlname = str_replace("//","/",$newurlname);
?>

   <a href="#" onclick="document.chosedirfrm.dir.value='<?php echo $dir; ?>';document.chosedirfrm.submit();">&lt;- Back</a><br><br>
	<fieldset>
	<legend><strong>Insert Image</strong></legend>
	  <table border="0" cellpadding="0" cellspacing="2" width="250">
		<tbody>
		  <tr>
			<td colspan="2"><img src="<?php echo $newurlname; ?>" width="100" height="100"></td>
		  </tr>
		  <tr>
			<td width="125">Image URL</td>
			<td width="175"><input id="url" type="text" value="<?php echo $newurlname; ?>" style="background-color:#FFFFFF; border:1px solid #828177; font-family:arial; font-size:11px; color: #003399;"></td>
		  </tr>
		  <tr>
			<td>Image Description </td>
			<td><input id="alt" type="text" style="background-color:#FFFFFF; border:1px solid #828177; font-family:arial; font-size:11px; color: #003399;"></td>
		  </tr>
		  <tr>
			<td>Alignment</td>
			<td><select id="align">
			  <option></option>
			  <option value="baseline">Baseline</option>
			  <option value="top">Top</option>
			  <option value="middle">Middle</option>
			  <option value="bottom">Bottom</option>
			  <option value="texttop">TextTop</option>
			  <option value="absmiddle">Absolute Middle</option>
			  <option value="absbottom">Absolute Bottom</option>
			  <option value="left">Left</option>
			  <option value="right">Right</option>
			</select>        </td>
		  </tr>
		  <tr>
			<td>Border</td>
			<td><input name="border" type="text" id="border" value="0" size="3" maxlength="3" style="background-color:#FFFFFF; border:1px solid #828177; font-family:arial; font-size:11px; color: #003399;"></td>
		  </tr>
		  <tr>
			<td>HSpace</td>
			<td><input name="hspace" type="text" id="hspace" size="3" maxlength="3" style="background-color:#FFFFFF; border:1px solid #828177; font-family:arial; font-size:11px; color: #003399;"></td>
		  </tr>
		  <tr>
			<td>VSpace</td>
			<td><input name="vspace" type="text" id="vspace" size="3" maxlength="3" style="background-color:#FFFFFF; border:1px solid #828177; font-family:arial; font-size:11px; color: #003399;"></td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td colspan="2" align="center"><input type="submit" name="Submit" value="Insert Image" onclick="AddImage();"></td>
		  </tr>
	  </table>
	</fieldset>

<?php } else { ?>

   <form name="choseimagefrm" id="choseimagefrm" action="<?php echo $protocol.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; ?>" method="POST">
   <input type="hidden" name="image" value="">
   <input type="hidden" name="dir" value="">
   <input type="hidden" name="choseimage" value="1">
   <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
   </form>

   <table cellpadding="3" cellspacing="0">
   <tr><td colspan="5"><b>Choose an image to insert, or browse through directories.</b></td></tr>
   <tr><td colspan="5">Current Directory: <?php echo $dir; ?></td></tr>
   <tr>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
      <td><img src="<?php echo $_SESSION['imagesurl']; ?>/jsfpixel.gif" width="70" height="1"></td>
   </tr>
<?php

      $paramname = "newimage";
      $newfilename = "";
      $newurlname = "";
      if ($_POST['uploadimage']==1) {
         if (is_uploaded_file($_FILES[$paramname]['tmp_name'])) {
            $counter = 1;
            $fn = str_replace(" ","_",$_FILES[$paramname]['name']);
            $newfilename = $_SESSION['imagesdir'].$dir.$fn;
            $newurlname = $_SESSION['imagesurl'].$dir.$fn;
            while(file_exists($newfilename)){
               $counter++;
               $newfilename = $_SESSION['imagesdir'].$dir.$counter."_".$fn;
               $newurlname = $_SESSION['imagesurl'].$dir.$counter."_".$fn;
            }
            move_uploaded_file($_FILES[$paramname]['tmp_name'],$newfilename);
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
         print "<td align=\"center\"><a href=\"#\" onclick=\"document.chosedirfrm.dir.value='".$dir.$dir_array[$i]."';document.chosedirfrm.submit();\"><img src=\"".$_SESSION['imagesurl']."/jsffoldericon.gif\" width=\"35\" height=\"35\" border=\"0\"><br>".$dir_array[$i]."</a><br>[<a href=\"#\" onClick=\"if(confirm('Are you sure you want to delete this directory?')) { document.deldirfrm.deldir.value='".$dir_array[$i]."';document.deldirfrm.submit(); } \">Delete</a>]</td>";
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
            print "<td align=\"center\"><a href=\"#\" onclick=\"document.choseimagefrm.image.value='".$file_array[$i]."';document.choseimagefrm.dir.value='".$dir."';document.choseimagefrm.submit();\"><img src=\"".$_SESSION['imagesurl'].$dir.$file_array[$i]."\" width=\"50\" height=\"50\" border=\"0\"><br>".$file_array[$i]."</a><br>[<a href=\"#\" onClick=\"if(confirm('Are you sure you want to delete this image?')) { document.delimgfrm.delimg.value='".$file_array[$i]."';document.delimgfrm.submit(); } \">Delete</a>]</td>";
            if (($count%5) ==4) print "</tr>";
            $count++;
         }
      }
      if (($count%5)>0) print "</tr>";

      ?>
      <tr><td colspan="5"><br></td></tr>
      <form enctype="multipart/form-data" action="<?php echo $protocol.$_SERVER['SERVER_NAME']."/".$_SERVER['SCRIPT_NAME']; ?>" method="POST">
      <input type="hidden" name="dir" value="<?php echo $dir; ?>">
      <input type="hidden" name="uploadimage" value="1">
      <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
      <tr><td colspan="5">
         <table cellpadding="0" cellspacing="1" border="0" bgcolor="blue"><tr><td>
         <table cellpadding="3" cellspacing="0" border="0" bgcolor="white"><tr>
            <td>Upload a new image</td>
            <td><input name="<?php echo $paramname; ?>" type="file"></td>
            <td><input type="submit" name="submit" value="Upload Image"></td>
         </tr></table>
         </td></tr></table>
      </td></tr>
      </form>
      <tr><td colspan="5"><br></td></tr>
      <form action="<?php echo $protocol.$_SERVER['SERVER_NAME']."/".$_SERVER['SCRIPT_NAME']; ?>" method="POST">
      <input type="hidden" name="dir" value="<?php echo $dir; ?>">
      <input type="hidden" name="newdirectory" value="1">
      <input type="hidden" name="scriptname" value="<?php echo $scriptname; ?>">
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
      <?php

      print "</table>";      
   }
?>