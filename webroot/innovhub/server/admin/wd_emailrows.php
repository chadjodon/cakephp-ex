<div style="margin:5px 5px 30px 5px;padding:5px;font-size:14px;color:#333333;">
<div style="font-size:20px;font-weight:bold;margin-bottom:15px;">Sending survey emails</div>
<?php
//error_reporting(E_ALL);

   print "\n<!-- ***chj*** wd_emailrows.php start: ".date("Y-m-d h:i:s")." -->\n";
   $dbi = new MySQLAccess();
   $wd = new WebsiteData();
   
   $sql = getParameter("sql");
   $wd_id = getParameter("wd_id");
   if($sql!=NULL && $wd_id!=NULL) {
      print "&gt; JData id: ".$wd_id."<br>";
      print "&gt; SQL to get email rows:<br>";
      print "<div style=\"margin-left:10px;margin-bottom:10px;color:#5555AA;font-family:courier;font-size:12px;\">";
      print $sql;
      print "</div>";
      
      $esr = new EmailSurveyRecipients();
      $esr->startjob($wd_id,$sql);
      print "&gt; Job is scheduled successfully.<br>";
      
      /*
      $rows = $dbi->queryGetResults($sql);
      if(count($rows)>0) {
         print "&gt; ".count($rows)." results were found for this query.<br>";
         print "<div style=\"padding-left:10px;padding-bottom:10px;font-size:10px;\">";
         for($i=0;$i<count($rows);$i++) {
            print ($i + 1).". ";
            print "row ".$rows[$i]['wd_row_id']." ";
            print "user ".$rows[$i]['userid']." ";
            print " &nbsp; &nbsp; ";
            $wd->sendEmail($wd_id,$rows[$i]['wd_row_id']);
         }
         print "</div>";
         print "&gt; Finished. The emails are being sent.<br>";
      } else {
         print "&gt; No results were found for this query.<br>";
      } 
      */
      
   } else {
      print "&gt; Internal error - please check with Chad<br>";
   }
   
   print "\n<!-- ***chj*** end: ".date("Y-m-d h:i:s")." -->\n";
?>

</div>