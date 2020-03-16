      <table border="0" width="100%" cellpadding="2" cellspacing="0" bgcolor="#AACCEE"><TR><TD>
      <table border="0" width="100%" cellpadding="3" cellspacing="2" bgcolor="#FFFFFF">
      <TR><TD colspan="2"><h2>Dream Weaver</h2></td></tr>
          <form enctype="multipart/form-data" action="<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php" method="post">
          <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
          <input type="hidden" name="action" value="uploadFile">
          <TR>
              <TD bgcolor="lightgrey">Upload this file:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
              <TD><input name="userfile" type="file"></TD>
          </TR>
          <TR>
             <td colspan="2">
             <select name="conversion">
             <option value="csv">CSV</option>
             <option value="latin">Latin</option>
             </select>
             <input type="submit" value="Upload Your File">
             </td>
          </tr>
          </form>
      

      </table>
      </td></tr></table>
<?php

                 print convertLatinString("Vive con pasión&#44; y el resto vendrá rodado,Destaca entre la multitud con las elegantes");

?>