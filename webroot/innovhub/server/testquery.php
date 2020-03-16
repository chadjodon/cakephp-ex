<?php
include_once "Classes.php";

if($vars['query']==NULL) $vars['query'] = getParameter("query");
$vars['query'] = str_replace("\\","",$vars['query']);
$vars['query'] = str_replace("\'","'",$vars['query']);
?>
<table cellpadding="3" cellspacing="0">
<form action="testquery.php" method="post">
<input type="hidden" name="action" value="sqlquery">
<tr valign="top"><td>Query:</td><td><textarea name="query" cols="80" rows="8"><?php echo $vars['query']; ?></textarea></td></tr>
<tr><td colspan="2"><input type="submit" name="submit" value="submit"></td></tr>
</form>
</table>
<br><br>

<?php
$csvquery = NULL;
if ($vars['query']!=NULL) {
   $dbLink = new MYSQLaccess;
   
   $queryArr = array();
   if (!is_array($vars['query'])) {
      $queryArr = separateStringBy($vars['query'],";");
   } else {
      $queryArr = $vars['query'];
   }

   for ($i=0;$i<count($queryArr);$i++) {
      $query = trim(str_replace("\r"," ",str_replace("\n"," ",str_replace("\r\n"," ",$queryArr[$i]))));
      //print "<br>".$i.". ".$query."<br>";
      if ($query!=NULL) {
         if($csvquery==NULL) $csvquery = $query;
         $things = $dbLink->queryGetResults($query);
         print "<br>\n<div style=\"font-size:10px;font-size:10px;color:#888888;font-weight:bold;\">Query: ".$query."</div>";
         if (count($things)<1) {
            print "Your query executed, but there were no responses.<br>";
         } else {
            $keys = array_keys($things[0]);
            print "<table cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#222222\">\n";
            print "<tr><td bgcolor=\"#FFFFFF\">#</td>";
            for ($j=0; $j<count($keys); $j++) print "<td bgcolor=\"#FFFFFF\">".$keys[$j]."</td>";
            print "</tr>\n";
   
            for ($j=0; $j<count($things); $j++) {
               print "<tr><td bgcolor=\"#FFFFFF\">".$j."</td>";
               for ($k=0; $k<count($keys); $k++) print "<td bgcolor=\"#FFFFFF\">".convertBack($things[$j][$keys[$k]])."</td>";
               print "</tr>\n";
            }
            print "</table><br><br>\n";
         }
      }
   }
} else {
   print "<div style=\"padding:15px;font-size:10px;font-family:arial;color:#AAAAAA;\">No DB query entered.  Please enter a valid SQL query above.</div>";
}
?>

