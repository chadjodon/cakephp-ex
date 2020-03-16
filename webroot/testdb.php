<?php
   function strip_slashes_mysql_results($line){
       foreach($line as $key => $value){
          $result[$key] = str_replace("\'", "'", $value);
          $result[$key] = str_replace("\\\"", "\"", $result[$key]);
       }
       return $result;
   }



  echo $_SERVER['REQUEST_URI'];
  print "<BR>";
  echo $_SERVER['REQUEST_URL'];
  
  $host = "127.0.0.1";
  $user = "cakephp";
  $pw = "KfKbg6fHqEVFxoNd";
  $db = "default";
  
  
  
   $host = getenv(strtoupper(getenv("DATABASE_SERVICE_NAME"))."_SERVICE_HOST");
   $user = getenv("DATABASE_USER");
   $pw = getenv("DATABASE_PASSWORD");
  
   print "<br>\n";
   print "Host: ".$host;
   print "<br>\n";
   print "user: ".$user;
   print "<br>\n";
   print "pw: ".$pw;
   print "<br>\n";
  
  
  
  $conn = new mysqli($host, $user, $pw, $db);

  $sql = "SHOW TABLES;";
  $result = $conn->query($sql);
  if(!$result) print "\n<br>Query failed: ".$conn->error."\n<br>Original Query: ".$sql."\n<br>";
      
      //print "\n\n<br>".$sql." RESULTS: \n\n<br>";
      //print_r($result);
      //print "\n\n<br>";
   $ans = array();
      
   if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) $ans[] = strip_slashes_mysql_results($row);
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
