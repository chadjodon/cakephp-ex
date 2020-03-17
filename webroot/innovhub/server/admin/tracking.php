<?php
//error_reporting(E_ALL);

//$temp = new Template();
$tracker = new TrackerArchive();
$start = getParameter("s_start");
if ($start!=NULL) $start .= " 00:00:00";
$end = getParameter("s_end");
if ($end!=NULL) $end .= " 23:59:59";
$searchstr = getParameter("searchstr");
$viewstr = getParameter("viewstr");
$actionstr = getParameter("actionstr");
$jsftrack1 = getParameter("jsftrack1");
$jsftrack2 = getParameter("jsftrack2");
$jsftrack3 = getParameter("jsftrack3");
$distinctfld = getParameter("distinctfld");
$orderby = getParameter("orderby");
if ($orderby==NULL) $orderby = "p.created DESC";
$page = getParameter("page");
if ($page==NULL) $page = 1;
$limit = getParameter("limit");
if ($limit==NULL) $limit = 50;
$table = getParameter("table");
if ($table==NULL) $table = "trackerarch";

$basicurl = getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?action=tracking&limit=".$limit;
$b_searchstr = "&searchstr=".urlencode($searchstr);
$b_searchstr .= "&viewstr=".urlencode($viewstr);
$b_searchstr .= "&actionstr=".urlencode($actionstr);
$b_searchstr .= "&jsftrack1=".urlencode($jsftrack1);
$b_searchstr .= "&jsftrack2=".urlencode($jsftrack2);
$b_searchstr .= "&jsftrack3=".urlencode($jsftrack3);
$b_searchstr .= "&s_start=".urlencode($start);
$b_searchstr .= "&s_end=".urlencode($end);
$b_searchstr .= "&distinctfld=".$distinctfld;
$b_table = "&table=".urlencode($table);
$b_orderby = "&orderby=".urlencode($orderby);
$b_page = "&page=".$page;

$pageurl = $basicurl.$b_orderby.$b_searchstr.$b_table;
$sorturl = $basicurl.$b_searchstr.$b_table."&page=1";
$searchurl = $basicurl.$b_orderby."&page=1&i=".time();
$deleteurl = $basicurl.$b_orderby.$b_table.$b_page;

//$postscount = $temp->getTracking($searchstr,$orderby,$limit,$page,TRUE,$table);
//$posts = $temp->getTracking($searchstr,$orderby,$limit,$page,FALSE,$table);

$printstuff = FALSE;
//$printstuff = TRUE;

$postscount = $tracker->getTracking($searchstr,$start,$end,$viewstr,$actionstr,$orderby,$limit,$page,TRUE, $table, $printstuff,FALSE,$jsftrack1,$jsftrack2,$jsftrack3,$distinctfld);
//print "\n<!-- ***chj*** posts count:\n";
//print_r($postscount);
//print "\n-->\n";

$posts = $tracker->getTracking($searchstr,$start,$end,$viewstr,$actionstr,$orderby,$limit,$page,FALSE, $table, $printstuff,FALSE,$jsftrack1,$jsftrack2,$jsftrack3,$distinctfld);
$sql_query = $tracker->getTracking($searchstr,$start,$end,$viewstr,$actionstr,$orderby,$limit,$page,FALSE,$table,FALSE,TRUE,$jsftrack1,$jsftrack2,$jsftrack3,$distinctfld);

//print_r($postscount);
$pages = ceil($postscount[0]['totalcount']/$limit);
if ($pages>25) $pages = 25;
?>


<script type="text/javascript">
   function SelectAll(frmid,caid,param){                                                                    
      var checked = document.getElementById(caid).checked;
      //if (checked) alert('checked!');
      var formel = document.getElementById(frmid);
      for (var i=0;i<formel.elements.length;i++){                                                                  
         var e = formel.elements[i];                           
         if (e.type=='checkbox' && e.name==param) e.checked=checked;                  
      }                                                                  
   }
   
   function togglesearchform() {
      if (jQuery('#showmoresearch').html()=='Show More') {
         jQuery('#showmoresearch').html('Show Less');
         jQuery('#advsearch').show();
      } else {
         jQuery('#showmoresearch').html('Show More');
         jQuery('#advsearch').hide();
      }
   }
</script>                                                            


<div style="width:800px;">
<table cellpadding="2" cellspacing="2" width="100%" style="font-size:12px;font-family:arial;">
<form id="searchtracking" action="<?php echo $searchurl; ?>" method="post">
<tr><td colspan="12" align="right">
   <span style="font-size:8px;color:blue;margin-right:12px;cursor:pointer;" id="showmoresearch" onclick="togglesearchform();">Show More</span>
   Start: <input type="text" id="datepicker1" name="s_start" value="<?php echo substr($start,0,10); ?>"> &nbsp; &nbsp;
   End: <input type="text" id="datepicker2" name="s_end" value="<?php echo substr($end,0,10); ?>"> &nbsp; &nbsp;
  <select name="table">
   <option value="trackerarch" <?php if (0==strcmp($table,"trackerarch")) echo "SELECTED"; ?>>Archive DB</option>
   <option value="tracker" <?php if (0==strcmp($table,"tracker")) echo "SELECTED"; ?>>Active DB</option>
  </select>
  <input style="font-size:12px;font-family:arial;width:130px;" type="text" name="searchstr" value="<?php echo $searchstr; ?>">
  <input style="font-size:12px;font-family:arial;" type="submit" name="submit" value="Search">
</td></tr>
<tr style="display:none;" id="advsearch"><td colspan="12" align="right">
  Action: <input style="font-size:10px;font-family:arial;width:100px;" type="text" name="actionstr" value="<?php echo $actionstr; ?>">
  View: <input style="font-size:10px;font-family:arial;width:100px;" type="text" name="viewstr" value="<?php echo $viewstr; ?>">
  Custom 1: <input style="font-size:10px;font-family:arial;width:100px;" type="text" name="jsftrack1" value="<?php echo $jsftrack1; ?>">
  2: <input style="font-size:10px;font-family:arial;width:100px;" type="text" name="jsftrack2" value="<?php echo $jsftrack2; ?>">
  3: <input style="font-size:10px;font-family:arial;width:100px;" type="text" name="jsftrack3" value="<?php echo $jsftrack3; ?>">
  <br>
  <input type="radio" name="distinctfld" value="view" <?php if(0==strcmp($distinctfld,"view")) echo "CHECKED"; ?>> Distinct view
  <input type="radio" name="distinctfld" value="action" <?php if(0==strcmp($distinctfld,"action")) echo "CHECKED"; ?>> Distinct action
  <input type="radio" name="distinctfld" value="jsftrack1" <?php if(0==strcmp($distinctfld,"jsftrack1")) echo "CHECKED"; ?>> Distinct custom 1
  <input type="radio" name="distinctfld" value="jsftrack2" <?php if(0==strcmp($distinctfld,"jsftrack2")) echo "CHECKED"; ?>> Distinct custom 2
  <input type="radio" name="distinctfld" value="jsftrack3" <?php if(0==strcmp($distinctfld,"jsftrack3")) echo "CHECKED"; ?>> Distinct custom 3
</td></tr>
</form>
<form id="trkidsform" action="<?php echo $deleteurl; ?>" method="post">
<tr><td colspan="12" bgcolor="#DEDEDE">
  <?php echo $postscount[0]['totalcount']; ?> results
  <?php
   if ($pages>1) {
      print "Page: ";
      for ($i=1; $i<=$pages; $i++) {
         if ($page==$i) print $i." &nbsp;";
         else print "<a href=\"".$pageurl."&page=".$i."\">".$i."</a> &nbsp;";
      }
   }
  ?> 
</td></tr>
<tr bgcolor="lightblue">
   <td><a href="<?php echo $sorturl; ?>&orderby=p.created+DESC">Date/Time</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.view">View</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.action">Action</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.agent">User Browser</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.referer">Referer</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.ipaddr">IP Addr</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.sessionid">Session</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.user">User ID</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.jsftrack1">Custom 1</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.jsftrack2">Custom 2</a></td>
   <td><a href="<?php echo $sorturl; ?>&orderby=p.jsftrack3">Custom 3</a></td>
   <td><input type="checkbox" id="checkall" name="checkall" value="1" onChange="SelectAll('trkidsform','checkall','trkids[]');"></td>
</tr>
<?php
for ($i=0; $i<count($posts); $i++) {
   if (0==strcmp($bgcolor,"#FFFFFF")) $bgcolor="#CCCCCC";
   else $bgcolor = "#FFFFFF";
?>

   <tr bgcolor="<?php echo $bgcolor; ?>">
   <td style="font-size:10px;font-family:arial;"><?php echo date("m/d/Y H:i:s",strtotime($posts[$i]['created'])); ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['view']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['action']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['agent']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['referer']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['ipaddr']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['sessionid']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo $posts[$i]['user']; ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo strip_tags($posts[$i]['jsftrack1']); ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo strip_tags($posts[$i]['jsftrack2']); ?></td>
   <td style="font-size:10px;font-family:arial;"><?php echo strip_tags($posts[$i]['jsftrack3']); ?></td>
   <td style="font-size:10px;font-family:arial;">
      <input type="checkbox" name="trkids[]" value="<?php echo $posts[$i]['trkid']; ?>">
   </td>
   </tr>

<?php
}
?>

<tr><td colspan="12" bgcolor="#DEDEDE" align="right">
  <input style="font-size:12px;font-family:arial;" type="submit" name="submit" value="Delete Rows" onClick="return(confirm('Are you sure you want to delete the selected rows above?'));">
</td></tr>
</form>
</table>
</div>

<div style="margin:10px;border-top:1px solid #DEDEDE;padding:10px;">
<?php
if(count($posts)>0) {
   $dlcsv = new DownloadSQLJob();
   $resp = $dlcsv->controller($pageurl,$sql_query);
   
   print $resp['msg'];
   print $resp['str'];
}
?>
</div>

<script>
   $(function() {
      $( "#datepicker1" ).datepicker({ dateFormat: "yy-mm-dd" });
      $( "#datepicker2" ).datepicker({ dateFormat: "yy-mm-dd" });
   });
</script>

