<?php

$ds_link = FALSE;
$ds_dbname = "";

// This class can be used to access a database...
class MYSQLaccess {
   var $dbUserName;
   var $dbPassword;
   var $dbName;
   var $dbHost;

   function changeConnectionInfo($db,$dbName,$dbPW,$dbHost){
      $this->dbUserName = $dbName;
      $this->dbPassword = $dbPW;
      $this->dbName = $db;
      $this->dbHost = $dbHost;
   }

   function checkInstanceVariables() {
      if ($this->dbUserName==NULL) $this->dbUserName = $GLOBALS['dbName'];
      if ($this->dbPassword==NULL) $this->dbPassword = $GLOBALS['dbPW'];
      if ($this->dbName==NULL) $this->dbName = $GLOBALS['db'];
      if ($this->dbHost==NULL) $this->dbHost = $GLOBALS['dbHost'];
   }

   function update($update) {
      $this->query($update);
   }

   function delete($delete) {
      $this->query($delete);
   }
      
   function insert($insert) {
     $this->query($insert);
   }

   function getMyConnection() {
      $this->checkInstanceVariables();
      $conn = new mysqli($this->dbHost, $this->dbUserName, $this->dbPassword, $this->dbName);
      $conn->set_charset("utf8");
      return $conn;
   }

   function queryGetResults($query) {
     return $this->query($query);
   }

   function printResults($dbArray) {
      for ($i=0; $i<count($dbArray); $i++) {
         foreach($dbArray[$i] as $key => $value) {
            print("key: ".$key." value: ".$value."<BR>");
         }
      }
   }

   function strip_slashes_mysql_results($line){
       foreach($line as $key => $value){
          $result[$key] = str_replace("\'", "'", $value);
          $result[$key] = str_replace("\\\"", "\"", $result[$key]);
       }
       return $result;
   }

   function insertGetValue($insert) {
     return $this->query($insert);
   }

   
   function escape($str) {
      $conn = $this->getMyConnection();
      return $conn->real_escape_string($str);
   }
   
   function query($sql,$countonly=FALSE) {
      $conn = $this->getMyConnection();
      $result = $conn->query($sql);
      if(!$result) print "\n<br>Query failed: ".$conn->error."\n<br>Original Query: ".$sql."\n<br>";
      
      //print "\n\n<br>".$sql." RESULTS: \n\n<br>";
      //print_r($result);
      //print "\n\n<br>";
      
      if($countonly) {
         $num = $result->num_rows;
         $result->free();
         return $num;
      } else {
         if($conn->insert_id!=NULL && $conn->insert_id>0){
         //if($result->insert_id!=NULL && $result->insert_id>0){
            //$id = $result->insert_id;
            $id = $conn->insert_id;
            $conn->close();
            return $id;
         } else {         
            $ans = array();
            if ($result->num_rows > 0) {
               while($row = $result->fetch_assoc()) $ans[] = $this->strip_slashes_mysql_results($row);
               $result->free();
            }
            $conn->close();
            return $ans;
         }
      }
   }
   
   
   
   
   function basicQuery($query) {
     return $this->query($query);
   }

   function countResultRows($query){
     return $this->query($query,TRUE);
   }
   
   
   

   function queryUseCache($query,$hours=NULL){
     //return $this->query($query);
      
      
      
      if ($hours==NULL) $hours = 12;
      $crdate = date("Y-m-d H:i:s",time()+(60*60*$hours));
      $newquery = "SELECT * FROM dbcache WHERE created<'".$crdate."' AND sqlstr='".convertString($query)."';";
      $results2 = array();
      $results = $this->query($newquery);
      if ($results==NULL || count($results)<1) {
         $results2 = $this->query($query);
         if ($query!=NULL && count($results2)>0) {
            $array_string=json_encode_jsf($results2);
            $insQuery = "INSERT INTO dbcache (created,sqlstr,sqlresults) VALUES (NOW(),'".convertString($query)."','".convertString($array_string)."');";
            $this->query($insQuery);
         }
      } else {
         $results2= objectToArray(json_decode_jsf(convertBack($results[0]['sqlresults'])));
      }
      return $results2;
      
      

   }
   
   function deleteCache(){
      $query = "DELETE FROM dbcache;";
      $this->delete($query);   	 
   }
}




Class ClearDBCache {
   function doWork($job){
   	if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";

      $job['content'] = substr(date("Y-m-d H:i:s")."; ".$job['content'],0,100);
      
      $sql = new MYSQLaccess();
      $query = "DELETE FROM dbcache WHERE created<'".date("Y-m-d H:i:s",(time()-(60*60*24)))."';";
      $sql->delete($query);
      
      $job['status'] = "NEW";
      $job['finished']=TRUE;
      $job['starton']=date("Y-m-d H:i:s",(time()+(60*60*36)));
      
      return $job;
   }
}



?>
