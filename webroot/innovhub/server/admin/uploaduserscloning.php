<?php
//error_reporting(E_ALL);
?>

   <br>
   <div style="font-size:16px;font-family:arial;font-weight:bold;color:#121212;">Upload Companies</div>
   <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" name="newuserfile" method="POST">
   <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
   <input type="hidden" name="action" value="uploaduserscloning">
   <input type="hidden" name="upload" value="1">
   <table cellpadding="2" cellspacing="1">
   <tr><td>User CSV File Upload:</td><td><input name="usercsv" type="file"></td></tr>
   <tr><td></td><td><input type="submit" name="Submit" value="submit"></td></tr>
   </table>
   </form>

<br><br><hr><br>

<a href="<?php print getBaseURL(); ?>jsfadmin/admincontroller.php?action=scheduledcsvs&type=CUSTOM">View your queue</a>

<br><br><hr><br>

<div onclick="jQuery('#tiptable').show();" style="">+ show tips</div>
<div id="tiptable" style="display:none;">
<table cellpadding="5" cellspacing="1">
<tr><td>CSV Parameter Name</td><td></td><td>Description</td></tr>

<tr valign="top">
<td>userid</td>
<td>Optional</td>
<td>If you want to update an existing record, enter this value in CSV to update the correct record</td>
</tr>

<tr valign="top">
<td>parentid</td>
<td>Optional</td>
<td>If this record references a parent record, set the parent's userid in this column in the csv</td>
</tr>

<tr valign="top">
<td>usertype</td>
<td>Optional</td>
<td>org, user, place, aor, etc</td>
</tr>

<tr valign="top">
<td>search</td>
<td>Optional</td>
<td>set this to "1" if you don't have the userid but you want to update an existing record and you want to try and find that record based on email/name/address</td>
</tr>

<tr valign="top">
<td>delete</td>
<td>Optional</td>
<td>set this to "1" if you want to delete this record from the database</td>
</tr>

<tr valign="top">
<td>approve</td>
<td>Optional</td>
<td>set this to "1" if you want to approve this CSV entry after it's created or updated</td>
</tr>

<tr valign="top">
<td>reject</td>
<td>Optional</td>
<td>set this to "1" if you want to reject this CSV entry after it's created or updated</td>
</tr>

<tr valign="top">
<td>addr1 (alternate: address1)</td>
<td>Optional</td>
<td>set this to the first line of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>addr2 (alternate: address2)</td>
<td>Optional</td>
<td>set this to the second line of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>city</td>
<td>Optional</td>
<td>set this to the city of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>state</td>
<td>Optional</td>
<td>set this to the 2 character state of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>zip</td>
<td>Optional</td>
<td>set this to the 2 character state of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>country</td>
<td>Optional</td>
<td>set this to the 2 character country of the address if you wish to update it (or add to a new record)</td>
</tr>

<tr valign="top">
<td>phonenum (alternate: phone)</td>
<td>Optional</td>
<td>set this to the phone number for the database entry</td>
</tr>

<tr valign="top">
<td>email</td>
<td>Optional</td>
<td>set this to the email address for the database entry</td>
</tr>

<tr valign="top">
<td>company (alternate: name)</td>
<td>Optional</td>
<td>set this to the name of the company for this entry</td>
</tr>

<tr valign="top">
<td>website (alternate: url)</td>
<td>Optional</td>
<td>set this to the web address reference for this entry</td>
</tr>

</table>
</div>