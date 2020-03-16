<?php
session_start();

ini_set('display_errors', 1);
error_reporting(0);
//error_reporting(E_ALL);

   $paramname = "newimage";
   $newfilename = "";
   $newurlname = "";
   if ($_POST['uploadimage']==1) {
      if (is_uploaded_file($_FILES[$paramname]['tmp_name'])) {
         $counter = 1;
         while(file_exists($_SESSION['imagesdir']."\\".$counter."_".$_FILES[$paramname]['name'])){
            $counter++;
         }
         $newfilename = $_SESSION['imagesdir']."\\".$counter."_".$_FILES[$paramname]['name'];
         $newurlname = $_SESSION['imagesurl']."/".$counter."_".$_FILES[$paramname]['name'];
         move_uploaded_file($_FILES[$paramname]['tmp_name'],$newfilename);
      }
?>

<script>
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
			window.opener.rteInsertHTML(html);
			window.close();
		} else {
			alert('You must select an image!');
		}
	}
</script>
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
   <html>
      <head>
         <style>
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
      </head>
   <body>
      <form enctype="multipart/form-data" action="<?php echo "http:/"."/".$_SERVER['SERVER_NAME']."/".$_SERVER['SCRIPT_NAME']; ?>" method="POST">
      <input name="<?php echo $paramname; ?>" type="file"><br>
      <input type="submit" name="submit" value="submit">
      <input type="hidden" name="uploadimage" value="1">
      </form>
   </body>
   </html>
<?php } ?>