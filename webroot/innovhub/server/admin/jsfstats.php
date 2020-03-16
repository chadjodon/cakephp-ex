<script src="/jsfcode/calendar.js"></script>
<?php
//error_reporting(E_ALL);
$sql = new MYSQLaccess();
$query = getParameter("sql");
$name = getParameter("name");
$datefield = getParameter("datefield");
$start = getParameter("start");
$end = getParameter("end");
$backurl = getParameter("backurl");


if($query!=NULL) {
   $newquery = $query;
   if ($start==NULL) $start = getDateForDB(0,-1);
   if ($end==NULL) $end = getDateForDB();
   $datequery = "";
   if($datefield!=NULL) {
      $datequery .= " AND ".$datefield.">='".$start." 00:00:00'";
      $datequery .= " AND ".$datefield."<='".$end." 23:59:59'";
   }
   
   $x = strpos(strtolower($newquery)," group by ");
   if($x!==FALSE) {
      $tempquery = substr($newquery,0,$x)." ";
      $tempquery .= $datequery;
      $tempquery .= substr($newquery,$x);
      $newquery = $tempquery;
   } else {
      $x = strpos(strtolower($newquery)," order by ");
      if($x!==FALSE) {
         $tempquery = substr($newquery,0,$x)." ";
         $tempquery .= $datequery;
         $tempquery .= substr($newquery,$x);
         $newquery = $tempquery;
      } else {
         $x = strpos(strtolower($newquery)," limit ");
         if($x!==FALSE) {
            $tempquery = substr($newquery,0,$x)." ";
            $tempquery .= $datequery;
            $tempquery .= substr($newquery,$x);
            $newquery = $tempquery;
         } else {
            $newquery .= $datequery;
         }
      }
      
   }
   
   $runquery = $newquery;
   if(strpos(strtolower($runquery)," limit ")===FALSE) $runquery .= " LIMIT 0,50";
   print "\n<!-- SQL Query: ".$newquery." -->\n<!--running query: ".$runquery." -->\n";
   $results = $sql->queryGetResults($runquery);
   
   if($results!=NULL && count($results)>0 && getParameter("getcsv")==1) {
      $obj = new ScheduledSQLCSV();
      $obj->createJob($newquery,"Report: ".$name,1);
      print "<div style=\"margin:12px;padding:1px;color:darkgreen;font-size:14px;\">Your job was scheduled.</div>";
   }
?>

<div style="margin:10px;padding:10px;border:1px solid #AAAAAA;border-radius:8px;">

<?php if($backurl!=NULL) { ?>
   <div onclick="location.href='<?php echo $backurl; ?>';" style="margin:5px;cursor:pointer;font-size:8px;color:blue;">
   &lt; Back
   </div>
<?php } ?>

<div style="font-size:18px;font-weight:bold;color:#444444;font-family:verdana;margin-bottom:15px;">
Online Stats: <?php echo $name; ?>
</div>

<?php if($datefield!=NULL) { ?>
<div id="reportinput"></div>

<script>
   function jsfstats_replaceAll(find, replace, str) {
     return str.replace(new RegExp(find, 'g'), replace);
   }


var htmlstr = '';
htmlstr += '<div id=\"position:relative;margin-bottom:10px;\">';
htmlstr += '<div style=\"float:left;width:70px;\">Start</div>';
htmlstr += '<div style=\"float:left;\">';
htmlstr += showCalendarInput("start",'<?php echo $start; ?>','',1);
htmlstr += '</div>';
htmlstr += '<div style=\"clear:both;\"></div>';
htmlstr += '<div style=\"float:left;width:70px;\">End</div>';
htmlstr += '<div style=\"float:left;\">';
htmlstr += showCalendarInput("end",'<?php echo $end; ?>','',1);
htmlstr += '</div>';
htmlstr += '<div style=\"clear:both;\"></div>';
htmlstr += '</div>';

htmlstr += '<div onclick=\"';
htmlstr += 'if(Boolean(jQuery(\'#start\').val()) && Boolean(jQuery(\'#end\').val())) ';
//htmlstr += 'location.href=\'/jsfadmin/admincontroller.php?action=jsfstats&start=\' + encodeURIComponent(jQuery(\'#start\').val() + \'&end=\' + encodeURIComponent(jQuery(\'#end\').val() + \'&sql=\' + encodeURIComponent(\'<?php echo str_replace("'","\\'",$query); ?>\') + \'&name=\' + encodeURIComponent(\'<?php echo $name; ?>\') + \'&datefield=\' + encodeURIComponent(\'<?php echo $datefield; ?>\');\";';
htmlstr += 'location.href=\'/jsfadmin/admincontroller.php?action=jsfstats&start=\' + encodeURIComponent(jQuery(\'#start\').val()) + \'&end=\' + encodeURIComponent(jQuery(\'#end\').val()) + \'&sql=\' + encodeURIComponent(\'<?php echo str_replace("'","\\\\\\'",$query); ?>\') + \'&name=\' + encodeURIComponent(\'<?php echo $name; ?>\') + \'&datefield=\' + encodeURIComponent(\'<?php echo $datefield; ?>\') + \'&backurl=\' + encodeURIComponent(\'<?php echo $backurl; ?>\');\";';
htmlstr += ' else alert(\'Please specify a beginning and end date.\');';
htmlstr += '\" style=\"width:140px;font-size:10px;padding:4px;text-align:center;background-color:#BBBBBB;border-radius:3px;font-family:verdana;cursor:pointer;margin:10px 2px 10px 2px;\">Re-run Report</div>';
htmlstr += '<div onclick=\"';
htmlstr += 'if(Boolean(jQuery(\'#start\').val()) && Boolean(jQuery(\'#end\').val())) ';
//htmlstr += 'location.href=\'/jsfadmin/admincontroller.php?action=jsfstats&start=\' + encodeURIComponent(jQuery(\'#start\').val() + \'&end=\' + encodeURIComponent(jQuery(\'#end\').val() + \'&sql=\' + encodeURIComponent(\'<?php echo str_replace("'","\\'",$query); ?>\') + \'&name=\' + encodeURIComponent(\'<?php echo $name; ?>\') + \'&datefield=\' + encodeURIComponent(\'<?php echo $datefield; ?>\');\";';
htmlstr += 'location.href=\'/jsfadmin/admincontroller.php?action=jsfstats&getcsv=1&start=\' + encodeURIComponent(jQuery(\'#start\').val()) + \'&end=\' + encodeURIComponent(jQuery(\'#end\').val()) + \'&sql=\' + encodeURIComponent(\'<?php echo str_replace("'","\\\\\\'",$query); ?>\') + \'&name=\' + encodeURIComponent(\'<?php echo $name; ?>\') + \'&datefield=\' + encodeURIComponent(\'<?php echo $datefield; ?>\') + \'&backurl=\' + encodeURIComponent(\'<?php echo $backurl; ?>\');\";';
htmlstr += ' else alert(\'Please specify a beginning and end date.\');';
htmlstr += '\" style=\"width:140px;font-size:10px;padding:4px;text-align:center;background-color:#BBBBBB;border-radius:3px;font-family:verdana;cursor:pointer;margin:10px 2px 10px 2px;\">Download CSV</div>';
jQuery('#reportinput').html(htmlstr);
</script>
<?php } ?>


<?php if(count($results)<1) { ?>
   
   <b>Sorry, there were no results for the criteria specified</b>
   
<?php } else { ?>


<table cellpadding="5" cellspacing="1">
<?php
   $countindex = NULL;
   $countwidth = 250;
   echo "<tr style=\"background-color:#EDEDED;\">";
   foreach($results[0] as $key => $val) {
      if(0==strcmp(strtolower($key),"total_clicks") || 0==strcmp(strtolower($key),"count(*)") || 0==strcmp(strtolower($key),"total")) {
         $countindex = $key;
      } else {
         echo "<td>".$key."</td>";
      }
      if($countindex!=NULL) echo "<td>".$countindex."</td>";
   }
   echo "</tr>";
   
   $countmax = 0;
   if($countindex!=NULL) {
      for($i=0;$i<count($results);$i++) {
         if($results[$i][$countindex]>$countmax) $countmax = $results[$i][$countindex];
      }
   }
   
   for($i=0;$i<count($results);$i++) {
      $bg = '#FFFFFF';
      if(($i%2)==1) $bg = '#F6FBFF';
      echo "<tr style=\"background-color:".$bg.";\">";
      foreach($results[$i] as $key => $val) {
         if($countindex==NULL || 0!=strcmp($key,$countindex)) echo "<td>".$val."</td>";
      }
      if($countindex!=NULL) {
         echo "<td><div style=\"position:relative;z-index:1;width:".$countwidth."px;height:20px;overflowhidden;\">";
         echo "<div style=\"position:absolute;left:2px;top:2px;color:#000000;font-weight:bold;z-index:3;\">".$results[$i][$countindex]."</div>";
         if($countmax>0) echo "<div style=\"position:absolute;z-index:2;background-color:#AAAAFF;left:0px;top:0px;height:20px;width:".round(($results[$i][$countindex]/$countmax) * $countwidth)."px;overflow:hidden;\"></div>";
         echo "</div></td>";
      }
      echo "</tr>";
   }
?>
</table>
<?php } ?>

</div>

<?php } else { ?>
   <div>Error occurred.</div>
<?php } ?>
