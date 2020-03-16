<?php
   include_once("../jsfcode/Classes.php");
   if (getParameter("a")!=2) {
      $output_file_name = getParameter("filename");
      if ($output_file_name==NULL) $output_file_name = $vars['filename']; 
      if ($output_file_name==NULL) $output_file_name = 'surveyresponses.csv'; 
      $sfn = getParameter("sfn");
      if ($sfn==NULL) $sfn = $vars['sfn']; 
      if ($sfn==NULL) $sfn = 'surveyresponses.csv'; 
           
      //print "<BR>";
      //print_r($_SESSION);
      //print "<BR>";

      $template = new Template();
      if (getParameter("showtemplate")==1 || $vars['showtemplate']==1) $template->getMainTop($vars['title']);

?>
      <div align="center"><h3>If download does not happen automatically, 
      <a href="<?php echo getBaseURL(); ?>jsfadmin/download.php?a=2&sfn=<?php echo $sfn; ?>&filename=<?php echo $output_file_name; ?>">click here</a>.</h3></div>
      <BR><BR><BR>
      <script type="text/javascript">
      window.open('<?php echo getBaseURL(); ?>jsfadmin/download.php?a=2&filename=<?php echo $output_file_name; ?>&sfn=<?php echo $sfn; ?>','csvdownload');
      </script>
<?php
      if (getParameter("showtemplate")==1 || $vars['showtemplate']==1) $template->getMainBottom($vars);
   } else {
      $output_file_name = getParameter("filename");
      if ($output_file_name==NULL) $output_file_name = $vars['filename']; 
      if ($output_file_name==NULL) $output_file_name = 'surveyresponses.csv'; 
      $sfn = getParameter("sfn");
      if ($sfn==NULL) $sfn = $vars['sfn']; 
      if ($sfn==NULL) $sfn = 'surveyresponses.csv'; 
      
      $fd = fopen ($output_file_name, "r");
      $entire_file = fread ($fd, filesize ($output_file_name));
      fclose ($fd);
      
      @ini_set('zlib.output_compression', 'Off'); 
      header('Pragma: public'); 
      header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT'); 
      header('Cache-Control: no-store, no-cache, must-revalidate'); 
      header('Cache-Control: pre-check=0, post-check=0, max-age=0'); 
      header('Content-Encoding: UTF-8');
      header('Content-Transfer-Encoding: none'); 
      //header('Content-Type: application/octetstream; name="' . $sfn . '"'); 
      header('Content-type: text/csv; charset=UTF-8');
      header('Content-Disposition: inline; filename="' . $sfn . '"'); 
      echo $entire_file; 
      exit();
   } 
?>
