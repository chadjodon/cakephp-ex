<?php
  echo $_SERVER['REQUEST_URI'];
  print "<BR>";
  echo $_SERVER['REQUEST_URL'];
  
  $host = "127.0.0.1";
  $user = "cakephp";
  $pw = "KfKbg6fHqEVFxoNd";
  $db = "default";
  $conn = new mysqli($host, $user, $pw, $db);

  $sql = "SHOW TABLES;";
  $result = $conn->query($sql);
  if(!$result) print "\n<br>Query failed: ".$conn->error."\n<br>Original Query: ".$sql."\n<br>";
      
      //print "\n\n<br>".$sql." RESULTS: \n\n<br>";
      //print_r($result);
      //print "\n\n<br>";
   $ans = array();
      
   if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) $ans[] = $this->strip_slashes_mysql_results($row);
      $result->free();
   }
   $conn->close();
   print "<br>\n";
   print "<br>\n";
   print "RESULTS:";
   print "<br>\n";
   print_r($ans);
   print "<br>\n";
?>
