<?php

//-------------------------------------------------
// Function getParameter( parmName )
// Returns variables in the GET or POST HTTP Request
//-------------------------------------------------
function getParameter ($paramName) {
   /*
   if ( !isset($_SESSION['params'][$paramName])) {
     if (!isset($_GET[$paramName])) {
         if (!isset($_POST[$paramName])) return NULL;
         else return $_POST[$paramName];
     }
     else return $_GET[$paramName];
  }
  else return $_SESSION['params'][$paramName];
  */
  $getParams = $_GET;
  if($_GET!=NULL && count($_GET)>0) $getParams = parseURLParams();
  if ( !isset($_SESSION['params'][$paramName])) {
     if (!isset($getParams[$paramName])) {
         if (!isset($_POST[$paramName])) return NULL;
         else return $_POST[$paramName];
     }
     else return $getParams[$paramName];
  }
  else return $_SESSION['params'][$paramName];
  
  
  
}

/*
function getParameter ($paramName) {
   if (!isset($_GET[$paramName])) {
      if (!isset($_POST[$paramName])) {
         if (!isset($_SESSION['params'][$paramName])) {
            return NULL;
         } else return $_SESSION['params'][$paramName];
      } else return $_POST[$paramName];
   } else return $_GET[$paramName];
}
*/

function getParameters($startswith){
   $results = array();
   if (isset($_SESSION['params'])) {
      foreach($_SESSION['params'] as $key => $value) {
         if (strpos($key, $startswith) === 0 && $value!=null) {
            $results[$key] = $value;
         }
      }
   }
   foreach($_POST as $key => $value) {
      if (strpos($key, $startswith) === 0 && $value!=null) {
         $results[$key] = $value;
      }
   }
   
   //foreach($_GET as $key => $value) {
   $getParams = parseURLParams();
   foreach($getParams as $key => $value) {
      if (strpos($key, $startswith) === 0 && $value!=null) {
         $results[$key] = $value;
      }
   }
   return $results;
}

function json_decode_jsf($str){
   if (!function_exists('json_decode')) {
      include_once("JSON.php");
      $json = new Services_JSON();
      return $json->decode($str);
   } else {
      return json_decode($str);
   }
}

function json_encode_jsf($str){
   $str = utf8ize($str);
   if (!function_exists('json_encode')) {
      include_once("JSON.php");
      $json = new Services_JSON();
      return $json->encode($str);
   } else {
      return json_encode($str);
   }
}

function utf8ize($d) {
    if (is_array($d) && count($d)<500) foreach ($d as $k => $v) $d[$k] = utf8ize($v);
    else if (is_string($d)) $d = utf8_encode($d);

    return $d;
}

function requestJSON($url,$showInfo=FALSE,$refresh=FALSE,$since=NULL){
   if ($showInfo) print "\n<!-- ***chj*** requestJSON URL: ".$url." -->\n";
   //if ($showInfo) error_reporting(E_ALL);
   $data = NULL;
   $dbLink = new MYSQLaccess;
   if (!$refresh) {
      if ($since==NULL) $since = date("Y-m-d",(time()-(60*60*24*7*4)));
      $newquery = "SELECT sqlresults FROM dbcache WHERE created>'".$since."' AND sqlstr='".mysqli_escape_string($url)."';";
      if ($showInfo) print "\n<!-- ***chj*** requestJSON checking cache: ".$newquery." -->\n";
      $results = $dbLink->queryGetResults($newquery);
      if ($results!=NULL || count($results)>0) {
         $data = objectToArray(json_decode_jsf($results[0]['sqlresults']));
      }
   }

   if ($data==NULL) {
      if ($showInfo) print "\n<!-- ***chj*** requestJSON calling out to get data -->\n";
      //Method 1 to get content:

      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_HEADER, false);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      if ($showInfo) print "\n<!-- ***chj*** inside Parse function url: ".$url." -->\n";
      $content = curl_exec($ch);
      curl_close($ch);

      
      //$ch2 = curl_init();
      //curl_setopt($ch2,CURLOPT_URL,$url);
      //curl_setopt($ch2,CURLOPT_POST, 1);
      //curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
      //$content = curl_exec($ch2);
      //curl_close($ch2);
      if ($showInfo) {
         print "\n<!-- method1:\n";
         print $content;
         print "\n-->\n";
      }
      $data = objectToArray(json_decode_jsf($content));      
      if ($data==NULL || !is_array($data)) {
         if ($showInfo) print "\n<!-- ***chj*** requestJSON trying file_get_contents -->\n";
         //Method 2 to get content:
         $content=file_get_contents($url);
         if ($showInfo) {
            //print "\n<!-- method2 url:".$url." yeilds:\n";
            //print $content;
            //print "\n-->\n";
         }
         $data = objectToArray(json_decode_jsf($content));      
         if ($showInfo) print "\n<!-- ***chj*** requestJSON back from file_get_contents -->\n";
      }

      $array_string=mysqli_escape_string(json_encode_jsf($data));

      $delQuery = "DELETE FROM dbcache WHERE sqlstr='".mysqli_escape_string($url)."';";
      $dbLink->delete($delQuery);

      $insQuery = "INSERT INTO dbcache (created,sqlstr,sqlresults) VALUES (NOW(),'".mysqli_escape_string($url)."','".$array_string."');";
      if ($showInfo) print "\n<!-- ***chj*** requestJSON storing in cache: ".$insQuery." -->\n";
      $dbLink->insert($insQuery);
   }

   return $data;
}

function copyRowsInsert($results,$tablename){
   $u_ins = NULL;
   if ($results!=NULL && count($results)>0){
       $u_ins = "INSERT INTO ".$tablename." (";
       $u_ins_cnt = 0;
       foreach($results[0] as $key => $val){
           if ($u_ins_cnt>0) $u_ins .= ",";
           $u_ins .= $key;
           $u_ins_cnt++;
       }
       $u_ins .= ") VALUES";
       for ($i=0;$i<count($results);$i++){
           if($i>0) $u_ins .= ",";
           $u_ins .= " (";
           $u_ins_cnt = 0;
           foreach($results[$i] as $key => $val){
               if ($u_ins_cnt>0) $u_ins .= ",";
               if (0==strcmp($val,"NOW()")) $u_ins .= $val;
               else if ($val==NULL || strlen($val)==0 || 0==strcmp($val,"NULL") || 0==strcmp($val,"undefined")) $u_ins .= "NULL";
               else $u_ins .= "'".$val."'";
               $u_ins_cnt++;
           }
           $u_ins .= ")";
       }
       //$sql->update($u_ins);
   }
   return $u_ins;
}

function requestREST($url){
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url);
      curl_setopt($ch,CURLOPT_HEADER, false);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      //print "\n<!-- ***chj*** inside Parse function url: ".$url." -->\n";
      $content = curl_exec($ch);
      curl_close($ch);
      //print "\n<!-- ***chj*** atom parse results object:\n";
      //print_r($content);
      //print "\n-->\n";
      //$data = XML_unserialize($content);
      //return $data;
      return $content;




   /*
   //error_reporting(E_ALL);
   $content = NULL;
   $dbLink = new MYSQLaccess;

   //Method 1 to get content:
   $ch2 = curl_init();
   curl_setopt($ch2,CURLOPT_URL,$url);
   curl_setopt($ch2,CURLOPT_POST, 1);
   curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
   $content = curl_exec($ch2);
   curl_close($ch2);

   if ($content==NULL) {
      //Method 2 to get content:
      $content=file_get_contents($url);
   }

   return $content;
   */
}

function getBaseURL($forceSSL=FALSE) {
   if($forceSSL) $_SESSION['secure']=1;
   if(isset($_SERVER["HTTPS"])) $_SESSION['secure']=1;
   
   $url = $GLOBALS['baseURL'];
   if (isset($_SESSION['secure'])){
      if($_SESSION['secure']==1) $url = $GLOBALS['baseURLSSL'];
   }
   return $url;
}

function getCookieDomain(){
   $domain = NULL;
   $url = getBaseURL();
   //print "\n<!-- URL: ".$url." -->\n";
   $url = str_replace("http://","",$url);
   $url = str_replace("https://","",$url);

   $urlArr = separateStringBy($url,"/");
   $url = $urlArr[0];

   //print "\n<!-- URL: ".$url." -->\n";
   $url = str_replace("/","",$url);
   $urlArr = separateStringBy($url,".");
   if (!is_numeric($urlArr[(count($urlArr)-2)]) && !is_numeric($urlArr[(count($urlArr)-1)])) {
      $domain = ".".$urlArr[(count($urlArr)-2)].".".$urlArr[(count($urlArr)-1)];
   }
   return $domain;
}

function setBitNumber($currentValue,$bitnumber){
   if (!checkBitSet($currentValue,$bitnumber)) $currentValue += pow(2,$bitnumber);
   return $currentValue;
}

function checkBitSet($currentValue,$bitnumber) {
   $val = (floor($currentValue/pow(2,$bitnumber)) % 2);
   return ($val==1);
}

function getBitsSet($value){
   $indexcounter = 0;
   $listcounter = 0;
   $curVal = $value;
   $str = "";
   while ($curVal>0) {
      if (($curVal % 2)==1) {
         if ($listcounter>0) $str .= ",";
         $str .= $indexcounter;
         $listcounter++;
      }
      $curVal = floor($curVal / 2);
      $indexcounter++;
   }
   return $str;
}

function saveUploadedFile($paramname,$directory,$prefix="",$newfn=NULL,$filetypes=NULL){
   if (is_uploaded_file($_FILES[$paramname]['tmp_name'])) {
      $counter = 1;
      $fn = str_replace("/","_",str_replace("\\","_",str_replace(" ","_",urldecode(getFileNameOnly($_FILES[$paramname]['name'])))));
      if($newfn!=NULL) $fn = substr($newfn,0,50);
      $ext = getExtension($_FILES[$paramname]['name']);
      
      $ftok = TRUE;
      if($filetypes!=NULL) {
         $filetypearr = separateStringBy($filetypes,",",NULL,TRUE);
         if(count($filetypearr)>0) {
            $ftok = FALSE;
            for($i=0;$i<count($filetypearr);$i++) {
               if(0==strcmp(strtolower($ext),strtolower($filetypearr[$i]))) {
                  $ftok = TRUE;
                  break;
               }
            }
         }
      }
      
      if($ftok && 0!=strcmp($ext,".php") && 0!=strcmp($ext,".js") && 0!=strcmp($ext,".html") && 0!=strcmp($ext,".exe")) {
         while(file_exists($directory.$prefix.$fn."_".$counter.$ext)){
            $counter++;
         }
         if (!move_uploaded_file($_FILES[$paramname]['tmp_name'],$directory.$prefix.$fn."_".$counter.$ext)) {
            return NULL;
         } else {
            return $prefix.$fn."_".$counter.$ext;
         }
      } else {
         return NULL;
      }
   } else {
      return NULL;
   }
}

function saveParameters(){
   $results = array();
   foreach($_POST as $key => $value) $results[$key] = $value;
   
   $getParams = parseURLParams();
   foreach($getParams as $key => $value) $results[$key] = $value;
   //foreach($_GET as $key => $value) $results[$key] = $value;

   foreach($_SESSION['params'] as $key => $value) $results[$key] = $value;
      //print "<BR>parameters saved:<br>";
      //print_r ($results);
   $_SESSION['lastparams'] = $results;
}

function isParameterSet($paramName){
  if ( empty($_POST[$paramName]) && empty($_GET[$paramName])) return false;
  else return true;
}

function printAllParams() {
   $PARAMS = $_POST;
   print "<BR><BR>POST:<BR>";
   foreach ( $PARAMS as $key=>$value ){
     if ( gettype( $value ) == "array" ){
        print "$key == <br>\n";
        foreach ( $value as $two_dim_value )
           print "...$two_dim_value<br>";
     }else {
        print "$key == $value<br>\n";
     }
   }
   
   $PARAMS = $_GET;
   print "<BR><BR>GET:<BR>";
   foreach ( $PARAMS as $key=>$value ){
     if ( gettype( $value ) == "array" ){
        print "$key == <br>\n";
        foreach ( $value as $two_dim_value )
           print "...$two_dim_value<br>";
     }else {
        print "$key == $value<br>\n";
     }
   }
}

function getParameterArray ($paramName) {
  $continue=True;
  $count = 0;
  while ($continue) {
     $str = getParameter($paramName."_".$count);
     if ($str == null) $continue=False;
     else $results[$count] = $str;
     $count++;
  }
  return $results;
}

function getCSVParameterArray ($paramName) {
  $continue=TRUE;
  $count = 0;
  $results="";
  while ($continue) {
     $str = convertString(getParameter($paramName."_".$count));
     if ($str == null) $continue=FALSE;
     else $results .= $str.", ";
     $count++;
  }
  return $results;
}

function array_contain($arr,$val) {
   foreach ($arr as $key=>$value) {
      if (strcmp($val,$value)==0) {
         return TRUE;
      }
   }
   return FALSE;

}

function getExtension($filename){
      $postfix = strtolower(substr($filename,strlen($filename) - strpos(strrev($filename),".")));
      $extension = ".".$postfix;
      return $extension;
}

function isImageFile($filename){
   $e = getExtension($filename);
   $a = FALSE;
   if(0==strcmp($e,".jpg")) $a = TRUE;
   else if(0==strcmp($e,".png")) $a = TRUE;
   else if(0==strcmp($e,".gif")) $a = TRUE;
   else if(0==strcmp($e,".jpeg")) $a = TRUE;
   
   if (0!==strcmp(strtolower(substr($filename,0,4)),"http") && 0!=strcmp(substr($filename,0,1),"/")) {
      $a = FALSE;
   }
   
   return $a;
}

function getFileNameOnly($filename,$ignoreCase=TRUE){
      $prefix = "";
      if ($ignoreCase) $prefix = strtolower(substr($filename,0,strlen($filename) - 1 - strpos(strrev($filename),".")));
      else $prefix = substr($filename,0,strlen($filename) - 1 - strpos(strrev($filename),"."));
      return str_replace(" ","_",$prefix);
}

function removeElementFromArray($arr,$i) {
   $count=0;
   $newArr = array();
   for ($j=0; $j<count($arr); $j++) {
      if ($i!=$j) {
         $newArr[$count] = $arr[$j];
         $count++;
      }
   }
   return $newArr;
}

function getAge( $dob, $tdate ) {
   $age = -1;
   while( $tdate > $dob) {
      $dob = strtotime('+1 year', $dob);
      $age++;
   }
   return $age;
}

function timeSince($when,$poststr="ago"){
   $str = "";
   $now = time();
   $diff = $now - $when;
   if ($diff<60) $str = $diff." seconds ".$poststr;
   else {
      $diff = round($diff / 60);
      if ($diff<60) $str = $diff." minutes ".$poststr;
      else {
         $diff = round($diff / 60);
         if ($diff<24) $str = $diff." hours ".$poststr;
         else {
            $diff = round($diff / 24);
            if ($diff<30) $str = $diff." days ".$poststr;
            else {
               $diff = round($diff / 30);
               $str = $diff." months ".$poststr;
            }
         }
      }
   }
   return $str;
}

// converts a date in m/d/Y format to database standard yyyy-mm-dd
function convertStandardDate($docdate){
   $newdate = NULL;
   $docdate = str_replace(" ","",$docdate);
   if ($docdate!=NULL) {
      $ddArr = separateStringBy($docdate,"/");
      if ($ddArr[0]!=NULL && $ddArr[1]!=NULL && $ddArr[2]!=NULL) {
         if (strlen($ddArr[0])==1) $ddArr[0] = "0".$ddArr[0];
         if (strlen($ddArr[1])==1) $ddArr[1] = "0".$ddArr[1];
         if (strlen($ddArr[2])==2) $ddArr[2] = "20".$ddArr[2];
         $newdate = $ddArr[2]."-".$ddArr[0]."-".$ddArr[1];
      }
   }
   return $newdate;
}

function getPrettyDate($date, $style=1, $extra="") {
   $tArr = separateStringBy($date,"-");
   $year = $tArr[0];
   
   if (0 == strcmp($tArr[1],"01")) $month = "January";
   else if (0 == strcmp($tArr[1],"02")) $month = "February";
   else if (0 == strcmp($tArr[1],"03")) $month = "March";
   else if (0 == strcmp($tArr[1],"04")) $month = "April";
   else if (0 == strcmp($tArr[1],"05")) $month = "May";
   else if (0 == strcmp($tArr[1],"06")) $month = "June";
   else if (0 == strcmp($tArr[1],"07")) $month = "July";
   else if (0 == strcmp($tArr[1],"08")) $month = "August";
   else if (0 == strcmp($tArr[1],"09")) $month = "September";
   else if (0 == strcmp($tArr[1],"10")) $month = "October";
   else if (0 == strcmp($tArr[1],"11")) $month = "November";
   else if (0 == strcmp($tArr[1],"12")) $month = "December";

   $day = $tArr[2];

   if ($style==1) return $month." ".$day.", ".$year.$extra;
   if ($style==2) return $month.", ".$year.$extra;
   if ($style==3) return $year.$extra;
   if ($style==4) return "";
}

function getMonthText($month) {
   if ($month==1) return "January";
   if ($month==2) return "February";
   if ($month==3) return "March";
   if ($month==4) return "April";
   if ($month==5) return "May";
   if ($month==6) return "June";
   if ($month==7) return "July";
   if ($month==8) return "August";
   if ($month==9) return "September";
   if ($month==10) return "October";
   if ($month==11) return "November";
   if ($month==12) return "December";
}

function getDateForDB($yearsExtra=0, $monthsExtra=0) {
   $phpToday = getdate();
   
   $temp = $monthsExtra / 12;
   if ($temp < 0) $yearsExtra += ceil($monthsExtra / 12);
   else $yearsExtra += floor($monthsExtra / 12);
   $monthsExtra = ($monthsExtra % 12);
   $year = $phpToday['year'] + $yearsExtra;

   $month = $phpToday['mon'] + $monthsExtra;
   if ($month <= 0) {
      $year = $year - 1;
      $month = $month + 12;
   } else if ($month>=13) {
      $month = $month - 12;
      $year = $year + 1;
   }
   if ($month < 10) $month = "0".$month;
   $today = $year."-";
   $today .= $month."-";
   
   if ($phpToday['mday'] < 10) $today .= "0";
   $today .= $phpToday['mday'];

   return $today;
}

function getCalendar($month=null,$year=null,$dates=null,$url=NULL) {
   if ($month==null) $month = date("n");
   if ($year==null) $year = date("Y");
   
   $startOn = date("w", mktime(0, 0, 0, $month, 1, $year)); 
   $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
   $monthName = date("F", mktime(0, 0, 0, $month, 1, $year));
   
   $monthBefore = mktime(0, 0, 0, $month, 15, $year);
   $monthBefore = $monthBefore - (30*24*60*60);
   
   $monthAfter = mktime(0, 0, 0, $month, 15, $year);
   $monthAfter = $monthAfter + (30*24*60*60);
   
   $str = "<table class=\"mainTable\" cellspacing=\"2\" cellpadding=\"5\">";
   $str .= "<tr><td colspan=\"7\" CLASS=\"monthYearText\">".$monthName." ".$year."</td></tr>";
   $str .= "<tr class=\"dayNamesText\">";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">S</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">M</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">T</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">W</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">T</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">F</td>";
   $str .= "<td class=\"dayNamesRow\" width=\"14%\">S</td>";
   $str .= "</tr>";
   
   $numPrev = cal_days_in_month(CAL_GREGORIAN, date("n",$monthBefore),date("Y",$monthBefore));
   $count = 0;
   if ($startOn>0) {
       $str .= "<tr>";
       for ($i=0; $i<$startOn; $i++) {
           $tDay = $numPrev - ($startOn-$i-1);
           $str .= "<td class=\"sOther\">".$tDay."</td>";
           $count++;
       }
   }
   
   for ($i=0; $i<$num; $i++) {
       $class="s2";
       if (($count%7)==0) {
           $str .= "<tr>";
           $class="s200";
       }
       else if (($count%7)==6) $class="s200";
       if ($dates[$year][$month][$i+1] != null) $class="s22";
       if ($month==date("n") && $year==date("Y") && ($i+1)==date("j") && strcmp($class,"s22")!=0) $class = "s29999 today";
       else if ($month==date("n") && $year==date("Y") && ($i+1)==date("j")) $class .= " today";
       
       if ($dates[$year][$month][$i+1] != null && $url!= NULL) $str .= "<td class=\"".$class."\"><a href=\"".$url.$dates[$year][$month][$i+1]."\">".($i+1)."</a></td>";
       else $str .= "<td class=\"".$class."\">".($i+1)."</td>";
       if (($count%7)==6) $str .= "</tr>";
       $count++;
   }
   
   $daysLeft = 7 - ($count%7);
   if (($count%7)==0) $str .= "<tr>";
   for ($i=0; $i<$daysLeft; $i++) {
       $tDay = $i+1;
       $str .= "<td class=\"sOther\">".$tDay."</td>";
       $count++;
   }
   $str .= "</tr></table>";
   return $str;
}

function getMonthDayInt($date) {
   $tArr = separateStringBy($date,"-");
   return ($tArr[1]*32 + $tArr[2]);
}

function separateStringBy($str,$str2,$default=NULL,$excludenull=FALSE) {
      //print "<br>\nssb: [".$str."] [".$str2."]<br>\n";
      $results = array();

      $indx = 0;
      while ($indx<strlen($str)) {
         $indx2 = strpos($str,$str2,$indx);
         if ($indx2===FALSE) $indx2 = strlen($str);

         $temp=substr($str,$indx,($indx2-$indx));
         if($excludenull) $temp = trim($temp);
         if(!$excludenull || $temp!=NULL) $results[]=$temp;
         $indx = $indx2 + strlen($str2);
      }
      //print "ssb results:<br>\n";
      //print_r($results);
      //print "<br>\n";
      
      if(count($results)<1 && $default!=NULL) $results[] = $default;
      
      return $results;
}

function separateStringBySeparators($str) {
   if(is_array($str)) $str = implode(",",$str);
   
   $emailsArr = array();
   $emailsArr1 = separateStringBy(trim($str),",");
   for ($i=0; $i<count($emailsArr1); $i++) {
      $emailsArr2 = separateStringBy(trim($emailsArr1[$i]),";");
      for ($j=0; $j<count($emailsArr2); $j++) {
         $emailsArr3 = separateStringBy(trim($emailsArr2[$j]),"\n");
         for ($k=0; $k<count($emailsArr3); $k++) {
            $emailsArr4 = separateStringBy(trim($emailsArr3[$k])," ");
            for ($m=0; $m<count($emailsArr4); $m++) {
               $emailAddr = trim($emailsArr4[$m]);
               if ($emailAddr!=NULL) $emailsArr[] = $emailAddr;
            }
         }
      }
   }
   return $emailsArr;
}

function convertLatinString($str) {
   $newstr="";
   for ($i=0; $i<strlen($str); $i++) {
      if (ord(substr($str,$i))>127) {
         $newstr .= "&#".ord(substr($str,$i)).";";
      }
      else $newstr .= substr($str,$i,1);
   }
   return $newstr;
}

function removenonasciichars($Str) {
  if(trim($Str)==NULL) return $Str;
  if(strlen($Str)>10000) return $Str;
  if(strlen($Str)<3) return $Str;
  
  $StrArr = str_split($Str);
  $NewStr = "";
  foreach ($StrArr as $Char) {    
    $CharNo = ord($Char);
    if (($CharNo>31 || $CharNo==10 || $CharNo==13) && $CharNo < 127) {
      $NewStr .= $Char;    
    }
  }  
  return $NewStr;
}


function removeLinks($str) {
   $str = removenonasciichars($str);
   $link_pattern = "/<a[^>]*>(.*)<\/a>/iU";
   $str = preg_replace($link_pattern, "$1", $str);
   return $str;
}

function csvRemoveQuotes($contents) {
   $contents = removenonasciichars($contents);
   $newContents = "";
   $flag = "\"";

   if ($contents != null) {
      $contents = str_replace("\r\n","\n",$contents);
      $contents = str_replace("\r","\n",$contents);
      $contents = str_replace("\'","&#039;",$contents);
      $contents = str_replace("'","&#039;",$contents);

      $firstFlag = strpos($contents, $flag);
      if ($firstFlag === FALSE) return $contents;
      else {
         $startBack = $firstFlag + strlen($flag);
         $secondFlag = strpos($contents,$flag,$startBack);
         if ($secondFlag === FALSE) return $contents;
         else {
            $endPos = $secondFlag + strlen($flag);
            $start = substr($contents,0,$firstFlag);
            $middle = substr($contents,$startBack,$secondFlag-$startBack);
            $middle = str_replace("\n"," ",$middle);
            $middle = str_replace("\r","",$middle);
            $middle = str_replace(",","&#44;",$middle);
            if ($endPos<strlen($contents)) $end = substr($contents,$endPos);
            else $end="";
            
            return $start." ".$middle.csvRemoveQuotes($end);
         }
      }
   }

}

function convertJavascriptString($contents) {
   $contents = removenonasciichars($contents);
   $contents = str_replace("\r\n","",$contents);
   $contents = str_replace("\r","",$contents);
   $contents = str_replace("\n","",$contents);
   //$contents = str_replace("\'","&#039;",$contents);
   //$contents = str_replace("'","&#039;",$contents);
   $contents = str_replace("&#039;","'",$contents);
   $contents = str_replace("&#34;","\"",$contents);
   $contents = addslashes($contents);
   return $contents;
}


function csvEncodeDoubleQuotes($str) {
   $str = convertBack($str);
   $str = str_replace("\r\n","<BR>",$str);
   $str = str_replace("\n","<BR>",$str);   
   $str = str_replace("\"","\"\"",$str);
   return $str;
}

function arrayToCSV($dataArr,$printstuff=FALSE) {
   $csvStr = "";
   if ($dataArr!=NULL && count($dataArr)>0) {
      $fields = array();
      $counter = 0;
      foreach($dataArr[0] as $key => $val) {
         $fields[$counter] = $key;
         $csvStr .= "\"".csvEncodeDoubleQuotes(trim($key))."\",";
         $counter++;
      }
      if($printstuff) print "\n<!-- Header: ".$csvStr." -->\n";
      $csvStr .= "\n";

      for ($i=0; $i<count($dataArr); $i++) {
         $line = $dataArr[$i];
         for ($j=0; $j<count($fields); $j++) {
            $csvStr .= "\"".csvEncodeDoubleQuotes(trim(convertBack($line[$fields[$j]])))."\",";
         }
         $csvStr .= "\n";
      }
   } else if($printstuff){
      print "\n<!-- ERROR: your data array was empty -->\n";
   }
   return $csvStr;
}

function dateLessThan($str1,$str2) {

   $date1 = separateStringBy($str1,"-");
   $date2 = separateStringBy($str2,"-");

   if ($date1[0] < $date2[0]) return true;
   else if ($date1[0] == $date2[0] & $date1[1] < $date2[1]) return true;
   else if ($date1[0] == $date2[0] & $date1[1] == $date2[1] & $date1[2] < $date2[2]) return true;
   else return false;
}

function dateGreaterThan($str1,$str2) {

   $date1 = separateStringBy($str1,"-");
   $date2 = separateStringBy($str2,"-");

   if ($date1[0] > $date2[0]) return true;
   if ($date1[0] == $date2[0] & $date1[1] > $date2[1]) return true;
   if ($date1[0] == $date2[0] & $date1[1] == $date2[1] & $date1[2] > $date2[2]) return true;
   else return false;
}

function getDefaultTitle() {
  $ss = new Version();
  return $ss->getValue('defaultTitle');
}

function make_links_clickable($text){ 
    $text = preg_replace('!(((f|ht)tp://)[-a-zA-Z?-??-?()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" target="_new">$1</a>', $text); 
    return preg_replace('!((https://)[-a-zA-Z?-??-?()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" target="_new">$1</a>', $text); 
} 

function strcmp_easy($str1,$str2){
   $temp1 = convertString(strtolower(trim($str1)));   
   $temp2 = convertString(strtolower(trim($str2)));
   return strcmp($temp1,$temp2);   
}

function removeSpecialChars($str){
   if (is_array($str)) return NULL;
   $str = strip_tags(convertBack($str));
   $str = removenonasciichars($str);
   $str = str_replace("!","",$str);
   $str = str_replace("@","",$str);
   $str = str_replace("#","",$str);
   $str = str_replace("$","",$str);
   $str = str_replace("%","",$str);
   $str = str_replace("^","",$str);
   $str = str_replace("&","",$str);
   $str = str_replace("*","",$str);
   $str = str_replace("(","",$str);
   $str = str_replace(")","",$str);
   $str = str_replace("-","",$str);
   $str = str_replace("+","",$str);
   $str = str_replace("=","",$str);
   $str = str_replace(".","",$str);
   $str = str_replace("?","",$str);
   $str = str_replace("{","",$str);
   $str = str_replace("}","",$str);
   $str = str_replace("|","",$str);
   $str = str_replace("/","",$str);
   $str = str_replace(";","",$str);
   $str = str_replace(":","",$str);
   $str = str_replace("<","",$str);
   $str = str_replace(">","",$str);
   $str = str_replace(",","",$str);
   $str = str_replace("'","",$str);
   $str = str_replace("\"","",$str);
   $str = str_replace("\\","",$str);
   $str = str_replace(" ","",$str);
   $str = str_replace("\r\n","",$str);
   $str = str_replace("\n","",$str);
   $str = str_replace("\t","",$str);
   $str = str_replace("\0","",$str);
   $str = str_replace("\x0B" ,"",$str);
   
   return $str;
}

function convertString($str,$correctLinks=TRUE) {
   if (is_array($str)) return $str;
   $str = removenonasciichars($str);
   $str = convertBack($str);

   $str = str_replace("<","&lt;",$str);
   $str = str_replace(">","&gt;",$str);
   $str = str_replace(",","&#44;",$str);
   $str = str_replace("\'","&#039;",$str);
   $str = str_replace("'","&#039;",$str);
   $str = str_replace("\"","&#34;",$str);
   //$str = str_replace('"',"&#34;",$str);
   $str = str_replace("\\\\","",$str);
   $str = str_replace("  "," &nbsp;",$str);
   $str = str_replace("\r\n","<BR>",$str);
   $str = str_replace("\n","<BR>",$str);
   $str = str_replace("\t","&nbsp;",$str);
   $str = str_replace("\0","&nbsp;",$str);
   $str = str_replace("\x0B" ,"&nbsp;",$str);
   
   if($correctLinks) {
      //correct the links:
      $x = strpos($str,"=\\&#34;");
      while ($x!==FALSE) {
         $end = substr($str,$x+7);
         $y = strpos($end,"\\&#34;");
         if ($y!==FALSE) {
            $start = substr($str,0,$x);
            $start .= "=\"";
            $start .= substr($end,0,$y);
            $start .="\"".substr($end,$y+6);
            $str = $start;
         }
         $x = strpos($str,"=\\&#34;");
      }
   
      //correct the links:
      $x = strpos($str,"=&#34;");
      while ($x!==FALSE) {
         $end = substr($str,$x+6);
         $y = strpos($end,"&#34;");
         if ($y!==FALSE) {
            $start = substr($str,0,$x);
            $start .= "=\"";
            $start .= substr($end,0,$y);
            $start .="\"".substr($end,$y+5);
            $str = $start;
         }
         $x = strpos($str,"=&#34;");
      }
   }
   //print "<!-- convert string: ".$str." END -->";
   return $str;
}

function convertBack($str,$additionalchecks=FALSE) {
   $str = removenonasciichars($str);
   //$str = str_replace("<BR>","\r\n",$str);
   $str = str_replace("<BR>","\n",$str);
   $str = str_replace("<br>","\n",$str);
   $str = str_replace("&nbsp;"," ",$str);
   //$str = str_replace("&#96;","'",$str);
   $str = str_replace("&#039;","'",$str);
   $str = str_replace("&#34;","\"",$str);
   $str = str_replace("&#44;",",",$str);
//print "\n\n\n<br><br><br>str: ".$str."<br><br><br><br>\n\n\n\n";

   $str = str_replace("&#59;",";",$str);
   $str = str_replace("&lt;","<",$str);
   $str = str_replace("&gt;",">",$str);
   
   if($additionalchecks) {
      $str = str_replace("#jsfquote#","\"",$str);
      $str = str_replace("#jsfsquote#","'",$str);
      $str = str_replace("#jsflf#","\n",$str);
      $str = str_replace("#jsfcr#","\r",$str);
      $str = str_replace("#jsfbullet#",chr(149),$str);
   }
//print "\n\n\n<br><br><br>str: ".$str."<br><br><br><br>\n\n\n\n";
   return $str;
}


   function getShortnameSQLStatement($name,$value,$extended=FALSE){
      $value = strtolower(" ".$value." ");
      $value = str_replace("&nbsp;"," ",$value);
      $value = str_replace("-","",$value);
      $value = str_replace(".","",$value);
      $value = str_replace("'","",$value);
      $value = str_replace(",","",$value);
      $value = str_replace("(","",$value);
      $value = str_replace(")","",$value);
      $value = str_replace("&#44;","",$value);
      $value = str_replace("&#039;","",$value);
      $value = str_replace("<br>","",$value);
      $value = str_replace("&amp;","",$value);
      $value = str_replace("&","",$value);
      if($extended===TRUE) {
         $value = str_replace("#","",$value);
         $value = str_replace("*","",$value);
         $value = str_replace("!","",$value);
         $value = str_replace("?","",$value);
         $value = str_replace("$","",$value);
         $value = str_replace("{","",$value);
         $value = str_replace("}","",$value);
         $value = str_replace("[","",$value);
         $value = str_replace("]","",$value);
         $value = str_replace("_","",$value);
         $value = str_replace(" and ","",$value);
         $value = str_replace(" of ","",$value);
         $value = str_replace(" or ","",$value);
         $value = str_replace(" the ","",$value);
      }
      $value = str_replace(" ","",$value);
      $query = "LOWER(".$name.")";
      $query = "REPLACE(".$query.",\"&nbsp;\",\" \")";
      $query = "REPLACE(".$query.",\"-\",\"\")";
      $query = "REPLACE(".$query.",\".\",\"\")";
      $query = "REPLACE(".$query.",\"'\",\"\")";
      $query = "REPLACE(".$query.",\",\",\"\")";
      $query = "REPLACE(".$query.",\"(\",\"\")";
      $query = "REPLACE(".$query.",\")\",\"\")";
      $query = "REPLACE(".$query.",\"&#44;\",\"\")";
      $query = "REPLACE(".$query.",\"&#039;\",\"\")";
      $query = "REPLACE(".$query.",\"<br>\",\"\")";
      $query = "REPLACE(".$query.",\"&amp;\",\"\")";
      $query = "REPLACE(".$query.",\"&\",\"\")";
      
      if($extended===TRUE) {
         $query = "REPLACE(".$query.",\"#\",\" \")";
         $query = "REPLACE(".$query.",\"*\",\"\")";
         $query = "REPLACE(".$query.",\"!\",\"\")";
         $query = "REPLACE(".$query.",\"?\",\"\")";
         $query = "REPLACE(".$query.",\"$\",\"\")";
         $query = "REPLACE(".$query.",\"{\",\"\")";
         $query = "REPLACE(".$query.",\"}\",\"\")";
         $query = "REPLACE(".$query.",\"[\",\"\")";
         $query = "REPLACE(".$query.",\"]\",\"\")";
         $query = "REPLACE(".$query.",\"-\",\"\")";
         $query = "REPLACE(".$query.",\" and \",\"\")";
         $query = "REPLACE(".$query.",\" of \",\"\")";
         $query = "REPLACE(".$query.",\" or \",\"\")";
         $query = "REPLACE(".$query.",\" the \",\"\")";
      }
      
      $query = "REPLACE(".$query.",\" \",\"\")";
      $query .= "='".$value."'";
      return $query;
   }

   function convertHashtag($ht) {
      $ht = preg_replace('/[^A-Za-z0-9_:-]/','',$ht);
      return $ht;
   }

   function removeAmbiguity($value,$checkAddr=FALSE){
      $value = strtolower(" ".$value." ");      
      $value = str_replace("&nbsp;"," ",$value);
      $value = str_replace("<br>"," ",$value);
      $value = str_replace("\n"," ",$value);
      $value = str_replace("\r"," ",$value);
      $value = str_replace("-"," ",$value);
      $value = str_replace("_"," ",$value);
      $value = str_replace("'"," ",$value);
      $value = str_replace("("," ",$value);
      $value = str_replace(")"," ",$value);
      $value = str_replace("#"," ",$value);
      $value = str_replace("&#039;"," ",$value);
      $value = str_replace("&#44;"," ",$value);
      $value = str_replace("&amp;"," ",$value);
      $value = str_replace("&"," ",$value);
      $value = str_replace("."," ",$value);
      $value = str_replace(","," ",$value);
      $value = str_replace(" the "," ",$value);
      $value = str_replace(" of "," ",$value);
      $value = str_replace(" and "," ",$value);
      $value = str_replace(" incorporated "," ",$value);
      $value = str_replace(" inc "," ",$value);
      $value = str_replace(" llc "," ",$value);
      
      if($checkAddr) {
         $value = str_replace(" street "," ",$value);
         $value = str_replace(" st "," ",$value);
         $value = str_replace(" lane "," ",$value);
         $value = str_replace(" ln "," ",$value);
         $value = str_replace(" road "," ",$value);
         $value = str_replace(" rd "," ",$value);
         $value = str_replace(" drive "," ",$value);
         $value = str_replace(" dr "," ",$value);
         $value = str_replace(" place "," ",$value);
         $value = str_replace(" pl "," ",$value);
         $value = str_replace(" court "," ",$value);
         $value = str_replace(" ct "," ",$value);
         $value = str_replace(" circle "," ",$value);
         $value = str_replace(" cr "," ",$value);
         $value = str_replace(" avenue "," ",$value);
         $value = str_replace(" ave "," ",$value);
         $value = str_replace(" av "," ",$value);
         $value = str_replace(" parkway "," ",$value);
         $value = str_replace(" pkwy "," ",$value);
         $value = str_replace(" highway "," ",$value);
         $value = str_replace(" hwy "," ",$value);
         $value = str_replace(" boulevard "," ",$value);
         $value = str_replace(" blvd "," ",$value);
         $value = str_replace(" p o box "," ",$value);
         $value = str_replace(" po box "," ",$value);
         $value = str_replace(" pobox "," ",$value);
         $value = str_replace(" post office box "," ",$value);
         $value = str_replace(" postoffice box "," ",$value);
      }
      
      $value = str_replace(" ","",trim($value));
      return $value;      
   }

   function getSQLStatementLike($name,$value,$length=5,$checkAddr=FALSE){
      if ($length==NULL || $length<2) $length=5;
      $value = removeAmbiguity($value,$checkAddr);
      $value = substr($value,0,$length);
      
      $query = NULL;
      if($value!=NULL && strlen($value)>0){
         $query = "LOWER(".$name.")";
         $query = "REPLACE(".$query.",\"&nbsp;\",\" \")";
         $query = "REPLACE(".$query.",\".\",\" \")";
         $query = "REPLACE(".$query.",\",\",\" \")";      
         $query = "REPLACE(".$query.",\"&#44;\",\" \")";
         $query = "REPLACE(".$query.",\"-\",\" \")";
         $query = "REPLACE(".$query.",\"_\",\" \")";
         $query = "REPLACE(".$query.",\"#\",\" \")";
         $query = "REPLACE(".$query.",\"'\",\" \")";
         $query = "REPLACE(".$query.",\"(\",\" \")";
         $query = "REPLACE(".$query.",\")\",\" \")";
         $query = "REPLACE(".$query.",\"&#039;\",\" \")";
         $query = "REPLACE(".$query.",\"<br>\",\" \")";
         $query = "REPLACE(".$query.",\"&amp;\",\" \")";
         $query = "REPLACE(".$query.",\"&\",\" \")";
         $query = "REPLACE(".$query.",\" and \",\" \")";
         $query = "REPLACE(".$query.",\" of \",\" \")";
         $query = "REPLACE(".$query.",\" the \",\" \")";
         $query = "REPLACE(".$query.",\" \",\"\")";
         $query .= " LIKE '%".$value."%'";
      }
      
      return $query;
   }



function convertApostrophes($str){
   $str = removenonasciichars($str);
   $str = str_replace("\'","&#039;",$str);
   $str = str_replace("'","&#039;",$str);
   return $str;
}

function convertToHTML($str){
   $str = str_replace("&gt;",">",$str);
   $str = str_replace("&lt;","<",$str);
   return $str;
}

function convertBackHTML($str){
   $str = str_replace("</textarea>","&lt;/textarea&gt;",$str);
   $str = str_replace("</TEXTAREA>","&lt;/TEXTAREA&gt;",$str);
   //$str = str_replace(">","&gt;",$str);
   //$str = str_replace("<","&lt;",$str);
   return $str;
}

function getFacebook(){
   $facebook = NULL;
   if (isset($GLOBALS['facebookappid']) && isset($GLOBALS['facebooksecret'])) {
      if (!isset($GLOBALS['facebook'])) {
         $GLOBALS['facebook'] = new Facebook(array('appId' => $GLOBALS['facebookappid'],'secret' => $GLOBALS['facebooksecret'],'cookie' => true));
      }
      $facebook = $GLOBALS['facebook'];
   }
   return $facebook;
}

function isLoggedOn() {
   $userid = NULL;
   if (isset($_SESSION['s_user']['emailAddress'])) {
      $userid = $_SESSION['s_user']['userid'];
//   } else if ($GLOBALS['usefacebookauth']==1) {
//      $facebook = new Facebook(array(
//        'appId'  => '139415026120877',
//        'secret' => 'f926b0b0b3f68b33e1cc3a1c0094c2f5',
//      ));
//      $user = $facebook->getUser();
//      if ($user) {
//         $userinfo = $facebook->api('/me');
//            if ($userinfo['email']!=NULL) $email = $userinfo['email'];
//            else $email = $userinfo['id']."@facebookdummy.com";
//            $ua = new UserAcct();
//            if ($ua->userExists($email)) {
//               $jsfuser = $ua->getUserByEmail($email);
//               $ua->addUserToSession($jsfuser);
//               $userid = $jsfuser['userid'];
//               $ua->updateUserProperty($userid,"facebook id",$userinfo['id']);
//            } else {
//               $userid = $ua->addAccount($email, "", $userinfo['first_name'], $userinfo['last_name'], $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $age, $userinfo['gender'], null, null, null, TRUE, FALSE, NULL, NULL, NULL, NULL, NULL, NULL, 1, "Facebook initiated");
//               $jsfuser = $ua->getUser($userid);
//               $ua->addUserToSession($jsfuser);
//               $ua->updateUserProperty($userid,"facebook id",$userinfo['id']);
//            }
//      }

   }
   return $userid;
}

function getSessionEmail() {
   return $_SESSION['s_user']['emailAddress'];
}

function getRandomNum($something = NULL, $small=FALSE) {
     //date_default_timezone_set('America/New_York');
     $num3 = date("s");
     if ($something != null) {
        $num1 = strlen($something)+ord(substr($something,(strlen($something)%5),1));
        $num2 = ceil(fmod($num1,7));
        $num3 = $num3 + $num1 + $num2;
     }
     srand((double)microtime()*100000000);
     $random_key = rand(10000000, 399992599) + rand(10289021, 699999999);
     
     srand((double)microtime()*1000000);
     $random_key = $random_key + rand(10021, 79999999) + rand(1234, 64299999);
     $result = $random_key+$num3;
     
     if ($small) $result = substr($result,2,4);

     return $result;
}

//-------------------------------------------------
// function getStateOptions(default,varname)
// Returns all the states of the U.S. in an HTML selection list
//-------------------------------------------------
  function getOptionList($name, $options, $selected=NULL, $leaveblank=FALSE, $extra="", $useNameAsSelect=FALSE, $maxChars=NULL, $defaultname="") {
     if ($options==null || count($options)<1) return "";
     $str =  "<select name=\"".$name."\" ".$extra.">\n";
     //if ($leaveblank) $str .="<option value=\"\" ".$extra."> </option>\n";
     if ($leaveblank) $str .="<option value=\"\" style=\"color:#BBBBBB;font-style:italic;\">".$defaultname."</option>\n";
     foreach($options as $key => $value) {
        $checked = "";
        if (strcmp($value,$selected)==0 || strcmp($key,$selected)==0) $checked = " SELECTED";
        $str .="<option value=\"".$value."\"".$checked.">";
        //$str .="<option value=\"".$value."\"".$checked." ".$extra.">";
        if ($maxChars!=NULL && $maxChars>3) $key = substr($key,0,$maxChars);
        $str .= $key."</option>\n";
      }
     $str .= "</select>\n";
      return $str;
  }

   function getCheckboxList($name, $options, $selects, $extra="") {
      $count2=1;
      $outHtml .= "\n\n<!-- util.php code to print an array of checkboxes -->\n";
      $outHtml .= "<table cellpadding=\"0\" cellspacing=\"0\">\n";
      foreach($options as $key => $value) {
         if ($value != NULL) {
            $temp = trim($key);
            $selected = "";
            if ($selects[trim($value)] == 1) $selected="CHECKED";
            if (($count2 % 3)==1) $outHtml .= "<TR valign=\"top\">";
            //$outHtml .= "<!-- name: ".$name." key: ".$key." value: ".$value." selects[]: ".$selects[$value]." -->";
            $outHtml .= "<TD><input type=\"checkbox\" name=\"".$name."[]\" id=\"".$name.$count2."\" value=\"".$value."\" ".$selected.">".$temp." &nbsp;&nbsp;&nbsp;</td>";
            if (($count2 % 3)==0) $outHtml .= "</TR>\n";
            $count2++;
         }
      }
      if (($count2 % 3)==2) $outHtml .= "<TD></TD><TD></TD></TR>\n";
      if (($count2 % 3)==0) $outHtml .= "<TD></TD></TR>\n";
      $outHtml .= "</table>\n\n";
      return $outHtml;
   }

   function getCheckboxListDiv($name, $options, $selects, $divextra="", $cbextra="") {
      //print "\n<!-- name: ".$name." options: ";
      //print_r($options);
      //print " selects: ";
      //print_r($selects);
      //print "\n-->\n";

      $count2=1;
      $outHtml = "";
      foreach($options as $key => $value) {
         if ($value != NULL) {
            $temp = trim($key);
            $selected = "";
            if ($selects[trim(strtolower($value))] == 1 || $selects[trim($value)] == 1 || $selects[trim($key)] == 1 || $selects[strtolower(trim($key))] == 1) $selected="CHECKED";
            $outHtml .= "<div id=\"div_".$name.$count2."\" ".$divextra."><input type=\"checkbox\" name=\"".$name."[]\" id=\"".$name.$count2."\" value=\"".$value."\" ".$cbextra." ".$selected.">".$temp."</div>";
            $count2++;
         }
      }
      return $outHtml;
   }

   function getCheckboxList2Across($name, $options, $selects, $extra="",$class="") {
      $count2=1;
      $outHtml .= "\n\n<!-- util.php code to print an array of checkboxes -->\n";
      $outHtml .= "<table cellpadding=\"0\" cellspacing=\"0\" class=\"".$class."\">\n";
      foreach($options as $key => $value) {
         if ($value != NULL) {
            $temp = trim($key);
            $selected = "";
            if ($selects[trim($value)] == 1) $selected="CHECKED";
            if (($count2 % 2)==1) $outHtml .= "<TR valign=\"top\">";
            //$outHtml .= "<!-- name: ".$name." key: ".$key." value: ".$value." selects[]: ".$selects[$value]." -->";
            $outHtml .= "<TD><input type=\"checkbox\" name=\"".$name."[]\" id=\"".$name.$count2."\" value=\"".$value."\" ".$selected.">".$temp." &nbsp;&nbsp;&nbsp;</td>";
            if (($count2 % 2)==0) $outHtml .= "</TR>\n";
            $count2++;
         }
      }
      if (($count2 % 2)==0) $outHtml .= "<TD></TD></TR>\n";
      $outHtml .= "</table>\n\n";
      return $outHtml;
   }

   function getRadioBtnList($name, $options, $select, $extra="") {
      $count2=1;
      $outHtml .= "\n\n<!-- util.php code to print an array of radio buttons -->\n";
      $outHtml .= "<table cellpadding=\"0\" cellspacing=\"0\">\n";
      foreach($options as $key => $value) {
         $temp = trim($key);
         $selected = "";
         if (0==strcmp(trim(strtolower($select)),trim(strtolower($value)))) $selected="CHECKED";
         if (($count2 % 3)==1) $outHtml .= "<TR valign=\"top\">";
         $outHtml .= "<TD><input type=\"radio\" ".$extra." name=\"".$name."\" id=\"".$name.$count2."\" value=\"".$value."\" ".$selected.">".$temp." &nbsp;&nbsp;&nbsp;</td>";
         if (($count2 % 3)==0) $outHtml .= "</TR>\n";
         $count2++;
      }
      if (($count2 % 3)==2) $outHtml .= "<TD></TD><TD></TD></TR>\n";
      if (($count2 % 3)==0) $outHtml .= "<TD></TD></TR>\n";
      $outHtml .= "</table>\n\n";
      return $outHtml;
   }

function getStrDateSelection($fullDate,$prefix="date"){
   $fields = separateStringBy($fullDate,"-");
   return getDateSelection($fields[2],$fields[1],$fields[0],$prefix);
}

function getDateAbbrevArray() {
   $result_m["Jan" ] = '01';
   $result_m["Feb" ] = '02';
   $result_m["Mar" ] = '03';
   $result_m["Apr" ] = '04';
   $result_m["May" ] = '05';
   $result_m["June"] = '06';
   $result_m["July"] = '07';
   $result_m["Aug" ] = '08';
   $result_m["Sept"] = '09';
   $result_m["Oct" ] = '10';
   $result_m["Nov" ] = '11';
   $result_m["Dec" ] = '12';
   return $result_m;
}

function getDateSelection($selectedDay=null,$selectedMonth=null,$selectedYear=null,$prefix="date",$suffix=NULL, $yearsbefore=90, $yearsahead=10, $leaveblank=FALSE) {
   $phpToday = getdate();
   if ($selectedYear==null && !$leaveblank) $selectedYear = $phpToday['year'];
   
   for ($i=1; $i<32; $i++) {
      if ($i<10) $result_d["0".$i] = "0".$i;
      else $result_d[$i] = $i;
   }
   
   $result_m = getDateAbbrevArray();

   for ($i=0; $i<($yearsbefore + $yearsahead); $i++) $result_y[(($phpToday['year']-$yearsbefore)+$i)] = ($phpToday['year']-$yearsbefore)+$i;

   $str = "<table cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
   $str .= "<td>";
   $str .= getOptionList($prefix."_m".$suffix,$result_m,$selectedMonth,$leaveblank,"id=\"".$prefix."_m".$suffix."\"");
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_d".$suffix,$result_d,$selectedDay,$leaveblank,"id=\"".$prefix."_d".$suffix."\"");
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_y".$suffix,$result_y,$selectedYear,$leaveblank,"id=\"".$prefix."_y".$suffix."\"");
   $str .= "\n</td>\n";
   $str .= "</tr></table>\n";
   
   return $str;
}

function getEmptyDateSelection($selectedDay=null,$selectedMonth=null,$selectedYear=null,$prefix="date",$class=NULL,$suffix=NULL) {
   $extra = "";
   if ($class!=NULL) $extra="class=\"".$class."\"";

   $phpToday = getdate();
   
   for ($i=1; $i<32; $i++) {
      if ($i<10) $result_d["0".$i] = "0".$i;
      else $result_d[$i] = $i;
   }
   
   $result_m = getDateAbbrevArray();

   for ($i=0; $i<100; $i++) $result_y[(($phpToday['year']-90)+$i)] = ($phpToday['year']-90)+$i;

   $str = "<table cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
   $str .= "<td>";
   if (!is_numeric($selectedMonth) || $selectedMonth<1 || $selectedMonth>12) $selectedMonth=NULL;
   $str .= getOptionList($prefix."_m".$suffix,$result_m,$selectedMonth,TRUE,$extra." id=\"".$prefix."_m".$suffix."\"");
   $str .= "\n</td><td>\n";
   if (!is_numeric($selectedDay) || $selectedDay<1 || $selectedDay>31) $selectedDay=NULL;
   $str .= getOptionList($prefix."_d".$suffix,$result_d,$selectedDay,TRUE,$extra." id=\"".$prefix."_d".$suffix."\"");
   $str .= "\n</td><td>\n";
   if (!is_numeric($selectedYear) || $selectedYear<1900 || $selectedYear>2200) $selectedYear=NULL;
   $str .= getOptionList($prefix."_y".$suffix,$result_y,$selectedYear,TRUE,$extra." id=\"".$prefix."_y".$suffix."\"");
   $str .= "\n</td>\n";
   $str .= "</tr></table>\n";
   
   return $str;
}

function getTimeSelection($selectedHour=null,$selectedMinute=null,$selectedTimeOfDay=null,$prefix="time",$suffix=NULL,$extra=NULL) {
   if ($selectedHour==null) $selectedHour = date("h");
   if ($selectedMinute==null) $selectedMinute = date("i");
   if ($selectedTimeOfDay==null) $selectedTimeOfDay = date("A");
   
   for ($i=0; $i<60; $i++) {
      if ($i<10) $result_min["0".$i] = "0".$i;
      else $result_min[$i] = $i;
   }

   for ($i=1; $i<13; $i++) {
      if ($i<10) $result_hour["0".$i] = "0".$i;
      else $result_hour[$i] = $i;
   }
   
   $result_tod["AM"] = 'AM';
   $result_tod["PM"] = 'PM';

   $str = "<table cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
   $str .= "<td>";
   $str .= getOptionList($prefix."_hour".$suffix,$result_hour,$selectedHour,FALSE,"id=\"".$prefix."_hour".$suffix."\" ".$extra);
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_min".$suffix,$result_min,$selectedMinute,FALSE,"id=\"".$prefix."_min".$suffix."\" ".$extra);
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_tod".$suffix,$result_tod,$selectedTimeOfDay,FALSE,"id=\"".$prefix."_tod".$suffix."\" ".$extra);
   $str .= "\n</td>\n";
   $str .= "</tr></table>\n";
   
   return $str;
}

function getEmptyTimeSelection($selectedHour=null,$selectedMinute=null,$selectedTimeOfDay=null,$prefix="time",$suffix=NULL) {
   for ($i=0; $i<60; $i++) {
      if ($i<10) $result_min["0".$i] = "0".$i;
      else $result_min[$i] = $i;
   }

   for ($i=1; $i<13; $i++) {
      if ($i<10) $result_hour["0".$i] = "0".$i;
      else $result_hour[$i] = $i;
   }
   
   $result_tod["AM"] = 'AM';
   $result_tod["PM"] = 'PM';

   $str = "<table cellpadding=\"0\" cellspacing=\"0\"><tr>\n";
   $str .= "<td>";
   $str .= getOptionList($prefix."_hour".$suffix,$result_hour,$selectedHour,TRUE);
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_min".$suffix,$result_min,$selectedMinute,TRUE);
   $str .= "\n</td><td>\n";
   $str .= getOptionList($prefix."_tod".$suffix,$result_tod,$selectedTimeOfDay,TRUE);
   $str .= "\n</td>\n";
   $str .= "</tr></table>\n";
   
   return $str;
}


   function getMapJSCode($points,$center=NULL,$zoom=NULL,$width="850px",$height="350px",$addGoogleJS=TRUE,$addflightpath=FALSE,$prefix="",$skiponload=FALSE, $apikey="AIzaSyCb9hEAztlzWrk-A4aGU0DZheQJvnu5VHY"){
      if (is_numeric($width)) $width .= "px";
      if (is_numeric($height)) $height .= "px";
      $str = "<div id=\"".$prefix."map_canvas\" style=\"height:".$height."; width:".$width.";\"></div>\n";
      if ($center==NULL) $center = "39,-97";
      if ($zoom==NULL) $zoom = 3;
      
      $js1 = "";
      $js2 = "";
      $js3 = "";
      $js4 = "var ".$prefix."fpCoor = [\n";
      for ($i=0; $i<count($points); $i++) {
         if ($points[$i]!=NULL && $points[$i]['lat']!=NULL && $points[$i]['lng']!=NULL) {
            $title = "";
            $content = "";
            $image = $points[$i]['img'];
      
            $js1 .= "var ".$prefix."latlng".$i." = new google.maps.LatLng(".$points[$i]['lat'].", ".$points[$i]['lng'].");\n";
            $js2 .= "var ".$prefix."marker".$i." = new google.maps.Marker({\n";
            $js2 .= "position: ".$prefix."latlng".$i.",\n";
            $js2 .= "map: ".$prefix."map,\n";
            if ($image != NULL) $js2 .= "icon: '".$image."',\n";
            $js2 .= "title:'".$points[$i]['title']."',\n";
            $js2 .= "zIndex: 500\n";
            $js2 .= "});\n";
            $js2 .= "google.maps.event.addListener(".$prefix."marker".$i.", 'click', function() {\n";
            $js2 .= "".$prefix."infowindow".$i.".open(".$prefix."map,".$prefix."marker".$i.");\n";
            $js2 .= "});\n";
            $js3 .= "var ".$prefix."contentString".$i." = '".str_replace("'","\\'",$points[$i]['content'])."';\n";
            $js3 .= "var ".$prefix."infowindow".$i." = new google.maps.InfoWindow({\n";
            $js3 .= "content: ".$prefix."contentString".$i."\n";
            $js3 .= "});\n";
            $js4 .= "new google.maps.LatLng(".$points[$i]['lat'].", ".$points[$i]['lng'].")";
            if ($i<(count($points)-1)) $js4 .= ",\n";
         }
      }
      $js4 .= "\n];\n";
      $js4 .= "var ".$prefix."flightPath = new google.maps.Polyline({\npath: ".$prefix."fpCoor,\n strokeColor: '#FF0000', strokeOpacity: 0.70, \n strokeWeight: 2\n});\n";
      $js4 .= "".$prefix."flightPath.setMap(".$prefix."map);\n";

      //if($addGoogleJS) $str .= "<script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>\n";
      //if($addGoogleJS) $str .= "\n<script async defer src=\"https://maps.googleapis.com/maps/api/js?key=".$apikey."&callback=initMap\" type=\"text/javascript\"></script>\n";
      if($addGoogleJS) $str .= "\n<script async defer src=\"https://maps.googleapis.com/maps/api/js?key=".$apikey."\" type=\"text/javascript\"></script>\n";
      $str .= "<script type=\"text/javascript\">\n";
      $str .= "var ".$prefix."map;\n";
      $str .= "var ".$prefix."latlng;\n";
      $str .= "function ".$prefix."gmaps_initialize() {\n";
      $str .= $js1;
      $str .= "\n";
      //$str .= "alert('hi3');\n";
      $str .= "".$prefix."latlng = new google.maps.LatLng(".$center.");\n";
      $str .= "var ".$prefix."myOptions = {\n";
      $str .= "zoom: ".$zoom.",\n";
      $str .= "center: ".$prefix."latlng,\n";
      $str .= "mapTypeId: google.maps.MapTypeId.ROADMAP\n";
      $str .= "};\n";
      //$str .= "alert('hi4');\n";
      $str .= "".$prefix."map = new google.maps.Map(document.getElementById('".$prefix."map_canvas'),".$prefix."myOptions);\n";
      if ($addflightpath) $str .= $js4;
      $str .= $js3."\n";
      //$str .= "alert('hi5');\n";
      $str .= $js2."\n";
      //$str .= "alert('hi6');\n";
      $str .= "}\n";

      if (!$skiponload) {
         $str .= "var ".$prefix."tmpFunc = window.onload;\n";
         $str .= "window.onload = function() { \n";
         $str .= $prefix."gmaps_initialize();\n";
         $str .= "if(Boolean(".$prefix."tmpFunc)) ".$prefix."tmpFunc();\n}\n";
      }

      $str .= "</script>\n";
      return $str;
   }


function distance($lat1, $lon1, $lat2, $lon2, $unit) { 
  $theta = $lon1 - $lon2; 
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
  $dist = acos($dist); 
  $dist = rad2deg($dist); 
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);
  if ($unit == "K") {
    return ($miles * 1.609344); 
  } else if ($unit == "N") {
    return ($miles * 0.8684);
  } else {
    return $miles;
  }
}


function getStateOptions($selectedState,$name=NULL,$blank=FALSE) {
  print listStates($selectedState,$name,$blank);
}


function listStates($selectedState=NULL,$name=NULL,$blank=FALSE,$extra=NULL) {
  $checked="";
  if ($name==NULL) $name="state";
  $result = getStateOptionList($blank);
  if ($selectedState==NULL) $selectedState=getParameter($name);
  return getOptionList($name,$result,$selectedState,FALSE,$extra);
}

function getStateOptionList($blank=FALSE){
  if ($blank) $result[' '] = " ";
  $result['AB']="AB"; $result['AK']="AK"; $result['AL']="AL"; $result['AR']="AR"; $result['AZ']="AZ"; $result['BC']="BC";
  $result['CA']="CA"; $result['CO']="CO"; $result['CT']="CT"; $result['DC']="DC"; $result['DE']="DE"; $result['FL']="FL";
  $result['GA']="GA"; $result['HI']="HI"; $result['IA']="IA"; $result['ID']="ID"; $result['IL']="IL"; $result['IN']="IN";
  $result['KS']="KS"; $result['KY']="KY"; $result['LA']="LA"; $result['MA']="MA"; $result['MB']="MB"; $result['MD']="MD";
  $result['ME']="ME"; $result['MI']="MI"; $result['MO']="MO"; $result['MN']="MN"; $result['MS']="MS"; $result['MT']="MT";
  $result['NB']="NB"; $result['NC']="NC"; $result['ND']="ND"; $result['NE']="NE"; $result['NH']="NH"; $result['NJ']="NJ";
  $result['NL']="NL"; $result['NM']="NM"; $result['NS']="NS"; $result['NT']="NT"; $result['NU']="NU";
  $result['NV']="NV"; $result['NY']="NY"; $result['OH']="OH"; $result['OK']="OK"; $result['ON']="ON"; $result['OR']="OR";
  $result['PA']="PA"; $result['PE']="PE"; $result['PR']="PR"; $result['QC']="QC";
  $result['RI']="RI"; $result['SC']="SC"; $result['SD']="SD"; $result['SK']="SK"; $result['TN']="TN"; $result['TX']="TX";
  $result['UT']="UT"; $result['VI']="VI"; $result['VT']="VT"; $result['VA']="VA"; $result['WA']="WA"; $result['WI']="WI";
  $result['WV']="WV"; $result['WY']="WY"; $result['YT']="YT";
  return $result;
}

function substituteStateName($n){
   $st = strtolower($n);
   $st = str_replace("alabama","AL",$st);
   $st = str_replace("alberta","AB",$st);
   $st = str_replace("alaska","AK",$st);
   $st = str_replace("arkansas","AR",$st);
   $st = str_replace("arizona","AZ",$st);
   $st = str_replace("british columbia","BC",$st);
   
   if(strpos($st,"california,")===FALSE) $st = str_replace("california","CA",$st);
   
   $st = str_replace("colorado","CO",$st);
   $st = str_replace("connecticut","CT",$st);
   $st = str_replace("delaware","DE",$st);
   $st = str_replace("texas","TX",$st);
   $st = str_replace("pennsylvania","PA",$st);
   $st = str_replace("north carolina","NC",$st);
   //$st = str_replace("new york","NY",$st);
   
   return $st;
}

function testCanadianPostal($pc){
   $ans = FALSE;
   $pc = str_replace("-","",str_replace(" ","",$pc));
   if(strlen($pc)==6){
      if(is_numeric(substr($pc,0,1)) && !is_numeric(substr($pc,1,1)) && is_numeric(substr($pc,2,1)) && !is_numeric(substr($pc,3,1)) && is_numeric(substr($pc,4,1)) && !is_numeric(substr($pc,5,1))) {
         $ans = TRUE;
      } else if(!is_numeric(substr($pc,0,1)) && is_numeric(substr($pc,1,1)) && !is_numeric(substr($pc,2,1)) && is_numeric(substr($pc,3,1)) && !is_numeric(substr($pc,4,1)) && is_numeric(substr($pc,5,1))) {
         $ans = TRUE;
      }
   }
   
   return $ans;
}

function testUSPostal($pc){
   $ans = FALSE;
   if(strlen($pc)>4) {
      $pc = substr(str_replace("-","",str_replace(" ","",$pc)),0,5);
      if(is_numeric($pc)) $ans = TRUE;
   }
   
   return $ans;
}

function findCoords($searchtxt,$trygoogle=FALSE, $googleOnly=FALSE, $showInfo=FALSE) {
   $template = new Template();
   $dbLink = new MYSQLaccess;
   $foundTbl = FALSE;
   $resp = array();
   $resp['city'] = NULL;
   $resp['state'] = NULL;
   $resp['postal'] = NULL;
   $resp['country'] = NULL;
   $resp['accuracy'] = "unknown";
   //$resp['trust'] = 0;
   $resp['query'] = NULL;
   $resp['latitude'] = NULL;
   $resp['longitude'] = NULL;

   if(!$googleOnly) {
      $query = "show tables;";
      $results = $dbLink->queryGetResults($query);
      for($i=0;$i<count($results);$i++){
         foreach($results[$i] as $k => $v) {
            if(0==strcmp($v,"e911zip")){
               $foundTbl = TRUE;
               break 2;
            }
         }
      }
         
      if($foundTbl) {
         if($showInfo) print "\n<!-- e911zip table found, trying that. -->\n";
         $states = getStateOptionList();
         $countries = getCountryArray();
         $alg1 = str_replace(","," ",str_replace("\r"," ",str_replace("\n"," ",substituteStateName(trim($searchtxt)))));
         $alg1_arr = separateStringBy($alg1," ",NULL,TRUE);
         $l = count($alg1_arr);
         
         //Check for country first
         if($showInfo) print "\n<!-- Checking for country. -->\n";
         foreach($countries as $key => $val) {
            $t = "non existent country";
            $t_arr = separateStringBy($key," ",NULL,TRUE);
            if($l >= count($t_arr)) {
               $t = "";
               for($i=1;$i<=count($t_arr);$i++) {
                  $t = $alg1_arr[($l - $i)]." ".$t;
               }
            }
            if(0==strcmp(strtolower($key),strtolower($t))) {
               $resp['country'] = $val;
               $l = $l - count($t_arr);
            } else if (0==strcmp(strtolower($val),strtolower($alg1_arr[$l-1]))) {
               if(isset($states[strtoupper($alg1_arr[$l-1])])) {
                  $resp['state'] = strtoupper($alg1_arr[$l-1]);
               } else {
                  $resp['country'] = $val;
               }
               $l = $l - 1;
            }
         }
         
         //Check for postal code
         if($showInfo) print "\n<!-- Checking for postal. -->\n";
         if($l>1 && strlen($alg1_arr[$l-1])==3 && strlen($alg1_arr[$l-2])==3) {
            if(testCanadianPostal($alg1_arr[$l-2].$alg1_arr[$l-1])) {
               $alg1_arr[$l-2] .= $alg1_arr[$l-1];
               $l = $l - 1;
            }
         } else if ($l>0 && strlen($alg1_arr[$l-1])==7 && 0==strcmp(substr($alg1_arr[$l-1],3,1),"-")) {
            $alg1_arr[$l-1] = str_replace("-","",$alg1_arr[$l-1]);
         }
         
         if($l>1 && strlen($alg1_arr[$l-1])==4 && is_numeric($alg1_arr[$l-1]) && strlen($alg1_arr[$l-2])==5 && is_numeric($alg1_arr[$l-2])) {
            $l = $l - 1;
         } else if ($l>0 && strlen($alg1_arr[$l-1])==10 && 0==strcmp(substr($alg1_arr[$l-1],5,1),"-") && is_numeric(substr($alg1_arr[$l-1],0,5))) {
            $alg1_arr[$l-1] = substr($alg1_arr[$l-1],0,5);
         }
         
         if($l>0 && testCanadianPostal($alg1_arr[$l-1])) {
            $resp['postal'] = $alg1_arr[$l-1];
            $resp['country']="CA";
            $l = $l - 1;
         } else if($l>0 && testUSPostal($alg1_arr[$l-1])) {
            $resp['postal'] = $alg1_arr[$l-1];
            $resp['country']="US";
            $l = $l - 1;
         }
         
         //$resp['log'] = "";
         if($resp['postal']==NULL && $l>0) {
            //Check for state
            if($l>0 && isset($states[strtoupper($alg1_arr[$l-1])])) {
               $resp['state'] = strtoupper($alg1_arr[$l-1]);
               $l = $l - 1;
            }
            
            if($resp['state']==NULL) {
               for($i=0;$i<count($alg1_arr);$i++) {
                  if(isset($states[strtoupper($alg1_arr[$i])])) {
                     $resp['state'] = strtoupper($alg1_arr[$i]);                  
                  }
               }
            }
            
            //check for city
            if($showInfo) print "\n<!-- Checking for city. -->\n";
            $savedQuery = NULL;
            $savedResults = array();
            $totalqueries = 0;
            $g_city = "";
            for($i=1; $i<=3; $i++) {
               if($l>=$i) {
                  $t_city = strtolower($alg1_arr[$l-$i]);
                  $query = "SELECT * FROM e911zip WHERE ";
                  $query .= "LOWER(City) LIKE '%".$t_city."%' ";
                  if($resp['state']!=NULL) $query .= "AND State='".strtoupper($resp['state'])."' ";
                  $query .= "ORDER BY Population DESC LIMIT 0,4;";
                  if($showInfo) print "\n<!-- Query for city: ".$query." -->\n";
                  $results = $dbLink->queryGetResults($query);
                  $totalqueries++;
                  if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
                  //$resp['log'] .= ";Query:".$query.";results:".count($results);
                  if(count($results)>0) {
                     $g_city = $t_city." ".$g_city;
                     //if($savedQuery==NULL || count($results)<count($savedResults)) {
                     if($savedQuery==NULL || strlen($t_city) > strlen($resp['city'])) {
                        $savedQuery = $query;
                        $savedResults = $results;
                        $resp['city'] = $t_city;
                     }
                  }
               }
            }
            //$resp['g_city'] = $g_city;
            if($g_city!=NULL && 0!=strcmp($g_city,$resp['city'])) {            
               $query = "SELECT * FROM e911zip WHERE ";
               $query .= "LOWER(City) LIKE '%".trim($g_city)."%' ";
               if($resp['state']!=NULL) $query .= "AND State='".strtoupper($resp['state'])."' ";
               $query .= "ORDER BY Population DESC LIMIT 0,4;";
               $results = $dbLink->queryGetResults($query);
               $totalqueries++;
               if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
               //$resp['log'] .= ";Query:".$query.";results:".count($results);
               if(count($results)>0) {
                  //if($savedQuery==NULL || count($results)<count($savedResults)) {
                  if($savedQuery==NULL || strlen($g_city) > strlen($resp['city'])) {
                     $savedQuery = $query;
                     $savedResults = $results;
                     $resp['city'] = $g_city;
                  }
               }
            }
            
            if($showInfo) print "\n<!-- length: ".$l." -->\n";
            if($l>2) {
               $n_city = strtolower($alg1_arr[$l-3]." ".$alg1_arr[$l-2]);
               $query = "SELECT * FROM e911zip WHERE ";
               $query .= "LOWER(City) LIKE '%".trim($n_city)."%' ";
               if($resp['state']!=NULL) $query .= "AND State='".strtoupper($resp['state'])."' ";
               $query .= "ORDER BY Population DESC LIMIT 0,4;";
               $results = $dbLink->queryGetResults($query);
               $totalqueries++;
               if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
               //$resp['log'] .= ";Query:".$query.";results:".count($results);
               if(count($results)>0) {
                  //if($savedQuery==NULL || count($results)<count($savedResults)) {
                  if($savedQuery==NULL || strlen($n_city) > strlen($resp['city'])) {
                     $savedQuery = $query;
                     $savedResults = $results;
                     $resp['city'] = $n_city;
                  }
               }
            }
            
            if($savedQuery!=NULL) {
               $resp['query'] = $savedQuery;
               $resp['latitude'] = $savedResults[0]['Latitude'];
               $resp['longitude'] = $savedResults[0]['Longitude'];
               $resp['accuracy'] = "city";
            }
         } else if($resp['postal']!=NULL) {
            $query = "SELECT * FROM e911zip WHERE ";
            $query .= "ZipCode='".strtoupper($resp['postal'])."' ";
            $results = $dbLink->queryGetResults($query);
            if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
            if($results!=NULL && count($results)>0) {
               $resp['query'] = $query;
               $resp['latitude'] = $results[0]['Latitude'];
               $resp['longitude'] = $results[0]['Longitude'];
               $resp['accuracy'] = "zip";
               $trygoogle = FALSE;
            }
         }
         
         if($resp['query']==NULL && $resp['state']!=NULL && $resp['postal']==NULL){
            $query = "SELECT * FROM e911zip WHERE State='".strtoupper($resp['state'])."' ORDER BY Population DESC LIMIT 0,1;";                  
            $results = $dbLink->queryGetResults($query);
            if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
            if($results!=NULL && count($results)>0) {
               $resp['query'] = $query;
               $resp['latitude'] = $results[0]['Latitude'];
               $resp['longitude'] = $results[0]['Longitude'];
               $resp['accuracy'] = "state";
            }
         } else if($resp['query']==NULL && $resp['postal']==NULL) {
            $temp = str_replace(","," ",$searchtxt);
            $temp = str_replace("'"," ",$temp);
            $temp = str_replace("-"," ",$temp);
            $tArr = separateStringBy($temp," ",NULL,FALSE);
            $query = "SELECT * FROM e911zip WHERE ";
            
            for($i=0;$i<count($tArr);$i++){
               if($i>0) $query .= " OR ";
               $query .= "LOWER(City) LIKE '%".strtolower(trim($tArr[$i]))."%' ";
               $query .= "OR LOWER(CityAliasName) LIKE '%".strtolower(trim($tArr[$i]))."%'";
               $query .= "OR LOWER(StateFullName) LIKE '%".strtolower(trim($tArr[$i]))."%'";
               $query .= "OR LOWER(ZipCode) LIKE '%".strtolower(trim($tArr[$i]))."%'";
            }
            
            $query .= " ORDER BY Population DESC LIMIT 0,1;";                  
            $results = $dbLink->queryGetResults($query);
            if($showInfo) print "\n<!-- findCoords Last ditch effort Query: ".$query." -->\n";
            if($results!=NULL && count($results)>0) {
               $resp['query'] = $query;
               $resp['latitude'] = $results[0]['Latitude'];
               $resp['longitude'] = $results[0]['Longitude'];
               $resp['accuracy'] = "unknown";
            }
         }
   
            /*
            if(strpos($searchtxt,",")!==FALSE) {
               $query = NULL;
               $cArr = separateStringBy(trim($searchtxt),",");
               if(count($cArr)==2) {
                  //print "found a comma, and 2 parameters.<br>\n";
                  if (strlen(trim($cArr[1]))==2) {
                     $query = "SELECT * FROM e911zip WHERE LOWER(City)='".strtolower(trim($cArr[0]))."' AND LOWER(State)='".strtolower(trim($cArr[1]))."' LIMIT 0,1;";                  
                  } else {
                     $sArr = separateStringBy(trim($cArr[1])," ");
                     if (strlen(trim($sArr[0]))==2) {
                        $query = "SELECT * FROM e911zip WHERE LOWER(City)='".strtolower(trim($cArr[0]))."' AND LOWER(State)='".strtolower(trim($sArr[1]))."' LIMIT 0,1;";
                     }
                  }
               }
               
               if($query!=NULL) {
                  $results = $dbLink->queryGetResults($query);
                  if($results!=NULL && count($results)>0) {
                     $foundloc = TRUE;
                     $resp['query'] = $query;
                     $resp['latitude'] = $results[0]['Latitude'];
                     $resp['longitude'] = $results[0]['Longitude'];
                     $resp['accuracy'] = "city";
                  }
               }
            } else {
               $query = "SELECT * FROM e911zip WHERE LOWER(ZipCode)='".strtolower(str_replace(" ","",$searchtxt))."' LIMIT 0,1;";
               $results = $dbLink->queryGetResults($query);
               if($results!=NULL && count($results)>0) {
                  $foundloc = TRUE;
                  $resp['query'] = $query;
                  $resp['latitude'] = $results[0]['Latitude'];
                  $resp['longitude'] = $results[0]['Longitude'];
                  $resp['accuracy'] = "zip";
               }
               
               if(!$foundloc){            
                  $query = "SELECT * FROM e911zip WHERE LOWER(State)='".strtolower(trim($searchtxt))."' ORDER BY Population DESC LIMIT 0,1;";                  
                  $results = $dbLink->queryGetResults($query);
                  if($results!=NULL && count($results)>0) {
                     $foundloc = TRUE;
                     $resp['query'] = $query;
                     $resp['latitude'] = $results[0]['Latitude'];
                     $resp['longitude'] = $results[0]['Longitude'];
                     $resp['accuracy'] = "state";
                  }
               }
   
               if(!$foundloc){            
                  $query = "SELECT * FROM e911zip WHERE LOWER(City)='".strtolower(trim($searchtxt))."' ORDER BY Population DESC LIMIT 0,1;";                  
                  $results = $dbLink->queryGetResults($query);
                  if($results!=NULL && count($results)>0) {
                     $foundloc = TRUE;
                     $resp['query'] = $query;
                     $resp['latitude'] = $results[0]['Latitude'];
                     $resp['longitude'] = $results[0]['Longitude'];
                     $resp['accuracy'] = "city";
                  }
               }            
            }
            
            if(!$foundloc){
               $temp = str_replace(","," ",$searchtxt);
               $temp = str_replace("'"," ",$temp);
               $temp = str_replace("-"," ",$temp);
               $tArr = separateStringBy($temp," ",NULL,FALSE);
               
               if ((count($tArr)>1) && strlen($tArr[(count($tArr)-1)])==2) {
                  $c = "";
                  $s = $tArr[(count($tArr)-1)];
                  for($i=0;$i<(count($tArr)-1);$i++){
                     $c .= $tArr[$i]." ";
                  }
                  $query = "SELECT * FROM e911zip WHERE ";
                  $query .= "LOWER(City) LIKE '%".strtolower(trim($c))."%' AND LOWER(State)='".strtolower(trim($s))."'";
                  $query .= " ORDER BY Population DESC LIMIT 0,1;";                  
                  $results = $dbLink->queryGetResults($query);
                  if($results!=NULL && count($results)>0) {
                     $foundloc = TRUE;
                     $resp['query'] = $query;
                     $resp['latitude'] = $results[0]['Latitude'];
                     $resp['longitude'] = $results[0]['Longitude'];
                     $resp['accuracy'] = "city";
                  }
                  
               }            
            }
            
            if(!$foundloc){
               $temp = str_replace(","," ",$searchtxt);
               $temp = str_replace("'"," ",$temp);
               $temp = str_replace("-"," ",$temp);
               $tArr = separateStringBy($temp," ",NULL,FALSE);
               $query = "SELECT * FROM e911zip WHERE ";
               
               for($i=0;$i<count($tArr);$i++){
                  if($i>0) $query .= " OR ";
                  $query .= "LOWER(City) LIKE '%".strtolower(trim($tArr[$i]))."%' OR LOWER(State) LIKE '%".strtolower(trim($tArr[$i]))."%' OR LOWER(ZipCode) LIKE '%".strtolower(trim($tArr[$i]))."%'";
               }
               
               $query .= " ORDER BY Population DESC LIMIT 0,1;";                  
               $results = $dbLink->queryGetResults($query);
               if($results!=NULL && count($results)>0) {
                  $foundloc = TRUE;
                  $resp['query'] = $query;
                  $resp['latitude'] = $results[0]['Latitude'];
                  $resp['longitude'] = $results[0]['Longitude'];
                  $resp['accuracy'] = "unknown";
               }
            }         
         }
         
         if(!$foundloc) {
            $ua = new UserAcct();
            $userid = convertString(trim(getParameter("userid")));
            $showInfo = (getParameter("showinfo")==1);
            if ($userid==NULL || !is_numeric($userid)) {
               $authtoken = getParameter("authtoken");
               if($authtoken!=NULL) {
                  $aarr = separateStringBy($authtoken,"%%%");
                  $query = "SELECT * from useracct WHERE userid='".$aarr[0]."' AND token='".$aarr[1]."';";
                  $results = $dbLink->queryGetResults($query);
                  if ($results != NULL && count($results)>0) {
                     $obj['addr1'] = convertString(trim(getParameter("addr1")));
                     $obj['addr2'] = convertString(trim(getParameter("addr2")));
                     $obj['city'] = convertString(trim(getParameter("city")));
                     $obj['state'] = convertString(trim(getParameter("state")));
                     $obj['zip'] = convertString(trim(getParameter("zip")));
                     $resp = $ua->getGeoCodeExplicit($obj,$showInfo);
                     $resp['accuracy'] = "unknown";
                  }
               }
            } else {
               $resp = $ua->getUserGeoCode($userid,$showInfo);
               $resp['accuracy'] = "unknown";
            }
         }
         
         if ($resp['latitude']!=NULL && $resp['longitude']!=NULL) {
            $resp['responsecode'] = 1;
         } else {
            $resp['responsecode'] = 0;
         }
         */
      } else {
         $query = "CREATE TABLE e911zip ( ";
         $query .= "zip_id int(20) unsigned NOT NULL auto_increment, ";
         $query .= "ZipCode varchar(6) NOT NULL, ";
         $query .= "PrimaryRecord varchar(1) default NULL, ";
         $query .= "Population int(11) default NULL, ";
         $query .= "Latitude decimal(12,6) default NULL, ";
         $query .= "Longitude decimal(12,6) default NULL, ";
         $query .= "Country varchar(2) default NULL, ";
         $query .= "State varchar(2) default NULL, ";
         $query .= "StateFullName varchar(64) default NULL, ";
         $query .= "City varchar(64) default NULL, ";
         $query .= "CityAliasName varchar(64) default NULL, ";
         $query .= "PRIMARY KEY(zip_id));";
         $dbLink->insert($query);
         if($showInfo) print "\n<!-- findCoords Query: ".$query." -->\n";
      }
   }
   
   if($showInfo) print "\n<!-- Done checking, maybe check google. -->\n";

   if ($trygoogle && ($resp['latitude'] == NULL || $resp['longitude']==NULL)) {
      $url = "https://maps.googleapis.com/maps/api/geocode/json";
      $address = "";
      if($resp['postal']!=NULL) {
         $temp_postal = trim($resp['postal']);
         if(strlen($temp_postal)==6) $temp_postal = strtoupper(substr($temp_postal,0,3)." ".substr($temp_postal,3));
         $gmapsURL = $url."?address=".urlencode($temp_postal);
      } else if(trim($searchtxt) != NULL) {
         $gmapsURL = $url."?address=".urlencode(trim($searchtxt));
      } else {
         $gmapsURL = NULL;
      }
      
      if($gmapsURL!=NULL) {
         $ss = new Version();
         $apikey = $ss->getValue('GoogleMapsKey');
         if($apikey!=NULL){
            $gmapsURL .= "&key=".$apikey;
         }
      }
      
      
      $data = NULL;
      if($gmapsURL!=NULL) {
         $gmapsURL .= "&sensor=false";
         if ($showInfo) print "\n<!-- findCoords gmaps url: ".$gmapsURL." -->\n";
         //$content=file_get_contents($gmapsURL);
         $resp['query'] = $gmapsURL;
         //if ($showInfo) {
         //   print "\n<!-- findcoords google maps response:\n";
         //   print $content;
         //   print "\n-->\n";
         //}
         //$data = objectToArray(json_decode_jsf($content));
   
         
         $data = requestJSON($gmapsURL,$showInfo,TRUE);
         $template->trackItem("Google API geocode lookup",$data['status'],$gmapsURL);
         
         if(0==strcmp($data['status'],"ZERO_RESULTS")) {
            if(!$googleOnly) {
               // Remember that this zip code successfully returned no coordinates
               // - so we don't try google again in the future
               $query = "INSERT into e911zip (ZipCode) VALUES ('".$resp['postal']."');";
               $dbLink->insert($query);
               if ($showInfo) print "\n<!-- findCoords query: ".$query." -->\n";
            }
         } else if(0==strcmp($data['status'],"OK")) {         
            $resp['latitude'] = $data['results'][0]['geometry']['location']['lat'];
            $resp['longitude'] = $data['results'][0]['geometry']['location']['lng'];
            if($resp['latitude']!=NULL && $resp['latitude']!=NULL) {
               $resp['accuracy'] = "zip";
               $components = $data['results'][0]['address_components'];
               for ($i=0;$i<count($components);$i++){
                  $types = $components[$i]['types'];
                  for ($j=0;$j<count($types);$j++){
                     if (0==strcmp($types[$j],"locality")){
                         $resp['city'] = $components[$i]['long_name'];
                         $resp['cityalias'] = $components[$i]['short_name'];
                         break;
                     } else if (0==strcmp($types[$j],"administrative_area_level_1")){
                         $resp['state'] = $components[$i]['short_name'];
                         $resp['statelong'] = $components[$i]['long_name'];
                         break;
                     } else if (0==strcmp($types[$j],"postal_code") && $resp['postal']==NULL){
                         $resp['postal'] = strtoupper($components[$i]['short_name']);
                         if(strlen($resp['postal'])>7) $resp['postal'] = substr($resp['postal'],0,5);
                         $resp['postal'] = str_replace(" ","",$resp['postal']);
                         $resp['postal'] = str_replace("-","",$resp['postal']);
                         break;
                     } else if (0==strcmp($types[$j],"country")){
                         $resp['country'] = $components[$i]['short_name'];
                         break;
                     }
                  }
               }
               if($resp['postal']!=NULL) {
                  if(!$googleOnly) {
                     $query = "SELECT * FROM e911zip WHERE LOWER(ZipCode)='".strtolower($resp['postal'])."';";
                     $r = $dbLink->queryGetResults($query);
                     if($r==NULL || count($r)<1) {
                        $query = "INSERT into e911zip (";
                        $query .= "ZipCode,Latitude,Longitude,Country,State,StateFullName,City,CityAliasName";
                        $query .= ") VALUES (";
                        $query .= "'".strtoupper($resp['postal'])."'";
                        $query .= ",".$resp['latitude'];
                        $query .= ",".$resp['longitude'];
                        $query .= ",'".strtoupper($resp['country'])."'";
                        $query .= ",'".strtoupper($resp['state'])."'";
                        $query .= ",'".$resp['statelong']."'";
                        $query .= ",'".$resp['city']."'";
                        $query .= ",'".$resp['cityalias']."'";
                        $query .= ");";
                        $dbLink->insert($query);
                        if ($showInfo) print "\n<!-- findCoords query: ".$query." -->\n";
                     }
                  }
               }
            }
         }
      } else {
         if ($showInfo) print "\n<!-- findCoords was sent no data to call google -->\n";
      }
         
   }
   if ($showInfo) {
      print "\n<!-- findcoords:\n";
      print $resp;
      print "\n-->\n";
   }
   return $resp;
}

function listCountries ($selected,$name=NULL,$blank=FALSE,$extra=NULL) {
  $checked="";
  if ($name==NULL) $name="country";
  $result = getCountryArray($blank);
  return getOptionList($name,$result,$selected,FALSE,$extra);
}

function getCountryArray($blank=FALSE) {
  if ($blank) $result[' '] = "XX";
  $result['United States']="US";
  $result['Canada']="CA";  
  $result['Mexico']="MX";  
  $result['China']="CN";  
  $result['Hong Kong']="HK";  
  $result['India']="IN";
  $result['Taiwan'] = "TW";
  $result['Viet Nam'] = "VN";
  $result['Korea'] = "KO";
  $result['Thailand'] = "TH";
  $result['United Kingdom'] = "UK";
  $result['Pakistan'] = "PK";
  $result['Singapore'] = "SG";
  $result['Bangladesh'] = "BD";
  $result['Brazil']="BR";
  $result['Jamaica']="JM";
  $result['Haiti']="HT";
  $result['Peru']="PE";
  return $result;
}

// Estimates the width in pixels of a string assuming arial 12px
function getStrPixelWidth($str) {
   $strPixelWidths = array( ' ' => 3, '!' => 3, '"' => 4, '#' => 7, '$' => 7, '%' => 11, 
   '&' => 8, '\'' => 2, '(' => 4, ')' => 4, '*' => 5, '+' => 7, ',' => 3, '-' => 4,
   '.' => 3, '/' => 3, '0' => 7, '1' => 7, '2' => 7, '3' => 7, '4' => 7, '5' => 7, '6' => 7, '7' => 7, '8' => 7, '9' => 7, ':' => 3, ';' => 3,
   '<' => 7, '=' => 7, '>' => 7, '?' => 7, '@' => 12, 'A' => 7, 'B' => 8, 'C' => 9, 'D' => 9, 'E' => 8, 'F' => 7, 'G' => 9, 'H' => 9, 'I' => 3,
   'J' => 6, 'K' => 8, 'L' => 7, 'M' => 9, 'N' => 9, 'O' => 9, 'P' => 8, 'Q' => 9, 'R' => 9, 'S' => 8, 'T' => 7, 'U' => 9, 'V' => 7, 'W' => 11,
   'X' => 7, 'Y' => 7, 'Z' => 7, '[' => 3, '\\' => 3, ']' => 3, '^' => 5, '_' => 7, '`' => 4, 'a' => 7, 'b' => 7, 'c' => 6, 'd' => 7, 'e' => 7,
   'f' => 3, 'g' => 7, 'h' => 7, 'i' => 3, 'j' => 3, 'k' => 6, 'l' => 3, 'm' => 11, 'n' => 7, 'o' => 7, 'p' => 7, 'q' => 7, 'r' => 4, 's' => 7,
   't' => 3, 'u' => 7, 'v' => 5, 'w' => 9, 'x' => 5, 'y' => 5, 'z' => 5, '{' => 4, '|' => 3, '}' => 4, '~' => 7);
   $totalLength = 0;
   for ($i=0; $i<strlen($str); $i++) {
      $totalLength += $strPixelWidths[substr($str,$i,1)];
   }
   return $totalLength;
}

function getSubStrPixelWidth($str,$width) {
   $totalLength = 0;
   $returnStr = "";
   for ($i=0; $i<strlen($str); $i++) {
      $chrstr = substr($str,$i,1);
      $totalLength += getStrPixelWidth($chrstr);
      if ($totalLength <= $width) $returnStr .= $chrstr;
      else break;
   }
   return $returnStr;
}

function numLines($str,$pxpl){
   $strArr = separateStringBy($str," ");
   $runningCount = 0;
   $lines = 1;
   for ($i=0; $i<count($strArr); $i++) {
      $x = trim($strArr[$i]);
      if ($x!=NULL) {
         $runningCount = $runningCount + 3 + getStrPixelWidth($x);
         if ($runningCount>$pxpl) {
            $lines++;
            $runningCount = getStrPixelWidth($x);
         }
      }
   }
   return $lines;
}

   function arrCopy($authInfo) {
    foreach ($authInfo as $key => $value) {
         $copyArr[$key]=$value;
    }
    return $copyArr;
   }

   function getHeightProportion ($picLocation, $desiredWidth) {
      list($width, $height, $type, $attr) = getimagesize($picLocation);

      $result['origWidth'] = $width;
      $result['origHeight'] = $height;

      if ($desiredWidth > $width) {
         $result['width'] = $width;
         $result['height'] = $height;
      }
      else {

         $desiredHeight = round(($desiredWidth * $height)/$width);
         $result['width'] = $desiredWidth;
         $result['height'] = $desiredHeight;
      }

      return $result;
   }

   function fitToBoxProportion ($picLocation, $boxWidth, $boxHeight) {
      list($width, $height, $type, $attr) = getimagesize($picLocation);
      //print "\n<!-- original width: ".$width." height: ".$height." pic location: ".$picLocation." -->\n";
      $result['origWidth'] = $width;
      $result['origHeight'] = $height;

      if ($boxWidth >= $width && $boxHeight >= $height) {
         $result['width'] = $width;
         $result['height'] = $height;
      } else if ($width > $boxWidth && $height <= $boxHeight) {
         $proportion = $boxWidth / $width;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      } else if ($width <= $boxWidth && $height > $boxHeight) {
         $proportion = $boxHeight / $height;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      } else if ($width > $boxWidth && $height > $boxHeight) {
         $dratio = $boxWidth / $boxHeight;
         $aratio = $width / $height;
         if ($aratio > $dratio) $proportion = $boxWidth / $width;
         else $proportion = $boxHeight / $height;
         $result['width'] = round($width * $proportion);
         $result['height'] = round($height * $proportion);
      }

      return $result;
   }

   function getEmailHeaders($from) {
      $headers  = "From: ".$from."\r\n";
      $headers .= "Reply-To: ".$from."\r\n";
      //$headers .= "X-Sender: ".$from."\r\n";
      //$headers .= "X-Mailer: PHP5\r\n";
      //$headers .= "X-Priority: 3\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      //$headers .= "Content-type:text/html;charset=iso-8859-1\r\n";
      $headers .= "Content-type:text/plain;charset=iso-8859-1\r\n";
      $headers .= "Return-Path: ".$from."\r\n";

      return $headers;
   }

   function getHTMLEmailHeaders($from) {
      $headers  = "From: ".$from."\r\n";
      $headers .= "Reply-To: ".$from."\r\n";
      //$headers .= "X-Sender: ".$from."\r\n";
      //$headers .= "X-Mailer: PHP5\r\n";
      //$headers .= "X-Priority: 3\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type:text/html;charset=iso-8859-1\r\n";
      //$headers .= "Content-type:text/plain;charset=iso-8859-1\r\n";
      $headers .= "Return-Path: ".$from."\r\n";

      return $headers;
   }

   function getMultipartHeaders($from,$htmlcontent){
      $semi_rand = md5(time());
      $mime_boundary = "==Multipart_Boundary_x".$semi_rand."x";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "From: ".$from."\r\n";
      $headers .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n";
      $headers .= "\r\nThis is a multi-part message in MIME format. Please let us know if this email is not formatted correctly.\r\n";
      $headers .= "\n--".$boundary."\n";
      $headers .= "Content-type: text/plain; charset=\"iso-8859-1\"\r\n";
      $headers .= "Content-Transfer-Encoding: 7bit\r\n";
      $headers .= "\r\n".strip_tags(convertBack($htmlcontent))."\r\n";
      $headers .= "\n--".$boundary."\n";
      $headers .= "Content-type: text/html; charset=\"iso-8859-1\"\r\n";
      $headers .= "Content-Transfer-Encoding: 7bit\r\n";
      $headers .= "\r\n".$htmlcontent."\r\n";
      //$headers .= "\r\n--".$boundary."--";
      return $headers;
   }

   function sendEmail($to,$subject,$content,$from=NULL) {
      $ss = new Version();
      if ($to==NULL || 0==strcmp(trim($to),"")) $to = $ss->getValue('WebsiteContact');
      if ($from==NULL) $from = $ss->getValue('WebsiteContact');
      mail($to, $subject, $content, getEmailHeaders($from));
   }

   //----------------------------------------------------------
   // Many of the template substitutions in this system use a tag
   // formatting.  This function will take a blob of text and find
   // its first occurence and shortname within that text.
   // Example text:
   // blah blah blah %%%CMS_chadjodon_CMS%%% blah blah blah
   // Method call: findTagInString("CMS", "blah blah blah %%%CMS_chadjodon_CMS%%% blah blah blah");
   // returns: "chadjodon"
   //----------------------------------------------------------
   function findTagInString($tag,$str) {
      $tagstart = "%%%".$tag."_";
      $tagend = "_".$tag."%%%";
      return findInternalString($tagstart,$tagend,$str);
   }

   function findInternalString($tagstart,$tagend,$str) {
      $start = strpos($str,$tagstart);
      if ($start === false) return NULL;
      else {
         $end = strpos($str,$tagend,($start+strlen($tagstart)));
         if ($end === false) return NULL;
         else {
            $offset = $start+strlen($tagstart);
            $length = $end - $offset;
            $shortname = substr($str,$offset,$length);
            return $shortname;
         }
      }
   }

   function startsWith ($str,$startStr) {
      $pos = strpos($str,$startStr);
      if ($pos!==FALSE && $pos == 0) return true;
      else return false;
   }

   function endsWith ($str,$endStr) {
      $pos = strpos($str,$endStr);
      if ($pos!==FALSE && $pos == (strlen($str)-strlen($endStr))) return true;
      else return false;
   }

   function encrypt($key,$text){
      $resultStr = "";
      $currPos = 0;
      for ($i=0; $i<strlen($text); $i++) {
         $currKey = ord(substr($key,$currPos,1));
         $currChr = ord(substr($text,$i,1));
         $resultChr = $currKey ^ $currChr;
         if (strlen($resultChr)==1) $resultChr = "0".$resultChr;
         $resultStr .= $resultChr.",";
         if ($currPos<(strlen($key)-1)) $currPos = $currPos + 1;
         else $currPos = 0;
      }
      return $resultStr;
   }
   
   function decrypt($key,$text){
      $strArray = separateStringBy($text,",");
      $resultStr = "";
      $currPos = 0;
      for ($i=0; $i<count($strArray); $i++) {
         $currKey = ord(substr($key,$currPos,1));
         $currChr = intval(trim($strArray[$i]));
         $resultChr = $currKey ^ $currChr;
         $resultStr .= chr($resultChr);
         if ($currPos<(strlen($key)-1)) $currPos = $currPos + 1;
         else $currPos = 0;
      }
      return $resultStr;
   }


   function lookForProfanity($str) {
      $profanityFound = FALSE;
      $lowerStr = strtolower($str);
      $profArr = array("fart","shit","piss","fag","ass","cunt","pussy","clit","cock","whore","twat","homo","dick");
      $wholeProfArr = array("fuck","bitch","bastard","testicle","penis","dickhead","asshole","faggot");

      foreach($wholeProfArr as $word){
         $pos = strpos($lowerStr,$word);
         if ($pos !== FALSE) {
            $profanityFound = TRUE;
            break;
         }
      }

      if (!$profanityFound) {
         foreach($profArr as $word){
            if (strpos($lowerStr," ".$word." ") !== FALSE) {
               $profanityFound = TRUE;
               break;
            } else if (strpos($lowerStr," ".$word.".") !== FALSE) {
               $profanityFound = TRUE;
               break;
            } else if (strpos($lowerStr," ".$word."?") !== FALSE) {
               $profanityFound = TRUE;
               break;
            } else if (strpos($lowerStr," ".$word."!") !== FALSE) {
               $profanityFound = TRUE;
               break;
            } else if (startsWith($str,$word." ")) {
               $profanityFound = TRUE;
               break;
            } else if (startsWith($str,$word.".")) {
               $profanityFound = TRUE;
               break;
            } else if (startsWith($str,$word."?")) {
               $profanityFound = TRUE;
               break;
            } else if (startsWith($str,$word."!")) {
               $profanityFound = TRUE;
               break;
            } else if (endsWith($str," ".$word)) {
               $profanityFound = TRUE;
               break;
            } else if (0==strcmp($lowerStr,$word)) {
               $profanityFound = TRUE;
               break;
            }
         }
      }

      return $profanityFound;
   }

   function getRelevantSiteObject($results){
       $ctx = new Context();
       $sitearr = $ctx->getSiteContext(); 
      for ($i=0; $i<count($sitearr); $i++) {
         for ($j=0; $j<count($results); $j++) {
            if ($sitearr[$i]['siteid'] == $results[$j]['siteid']) {
               return $results[$j];
            }
         }
      }
      return NULL;
   }

   function objectToArray( $object ) {
        if( !is_object( $object ) && !is_array( $object ) ){
            return $object;
        }
        if( is_object( $object ) ){
            $object = get_object_vars( $object );
        }
        return array_map( 'objectToArray', $object );
    }


   class DateSorter {
      var $cfield;
   
      function sort($mArr,$varfield=NULL) {
         if ($varfield!=NULL) $this->cfield = $varfield;
         usort($mArr, array($this, 'compare'));
         return $mArr;
      }
   
      function compare($a, $b) {
         $x = strtotime($a[$this->cfield]);
         $y = strtotime($b[$this->cfield]);
         if ($x == $y) return 0;
         return ($x < $y) ? -1 : 1;
      }      
   }


//-------------------------------------------------------------------
//         $tab = getParameter("tab");
//         if ($tab==NULL) $tab = "t1";
//         $tabsArr[0]['id']="t1";
//         $tabsArr[0]['name']="Tab 1";
//         $tabsArr[1]['id']="t2";
//         $tabsArr[1]['name']="Tab 2";
//         $tabsArr[2]['id']="t3";
//         $tabsArr[2]['name']="Tab 3";
//         $tabsArr[3]['id']="t4";
//         $tabsArr[3]['name']="Tab 4";
//         $tabbedbar = getTabs($tabsArr,$tab,"btn1","btn2");
//
//-------------------------------------------------------------------
function getTabs($tabArr,$selectedtab,$class,$class_sel,$postfix="",$jscript=NULL) {
   $javascript = "";
   $links = "";
   $javascript .= "<script type=\"text/javascript\">\nfunction clickTab".$postfix."(tab){\n";
   $links .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr>\n";

   foreach($tabArr as $key => $value) {
      $javascript .= "document.getElementById('".$value['id']."').style.display='none';\n";
      $javascript .= "document.getElementById('".$value['id']."_link').className='".$class."';\n";
      $links .= "<td class=\"";
      if (0==strcmp($selectedtab,$value['id'])) $links .= $class_sel;
      else $links .= $class;
      $links .= "\" id=\"".$value['id']."_link\"><a href=\"#tabs".$postfix."\" onclick=\"clickTab".$postfix."('".$value['id']."');\">".$value['name']."</a></td>\n";
      //$links .= "<td>&nbsp;</td>\n";
   }

   $javascript .= "document.getElementById(tab).style.display='';\n";
   $javascript .= "document.getElementById(tab + '_link').className='".$class_sel."';\n";
   if ($jscript!=NULL) $javascript .= $jscript."(tab);\n";
   $javascript .= "}\n</script>\n";

   $links .= "</tr></table>\n";
   $results['links'] = $links;
   $results['javascript'] = $javascript;
   return $results;
}

function getIntFromDecimal($str){
      if ($str==NULL || 0==strcmp($str,".0") || 0==strcmp($str,".00") || 0==strcmp($str,"0.0") || 0==strcmp($str,"0.")) return 0;
      else {
        if (!strstr($str, '.')) $formatting=$str."00";
        else $formatting = substr($str,0,strlen($str)-3) . substr($str,strlen($str)-2,strlen($str));
        return $formatting;
      }
}

function getDecimalFromInt($num){
      if ($num==NULL || $num == 0) $num="000";

      if (strlen($num)== 1) $num = "00".$num;
      elseif (strlen($num)== 2) $num = "0".$num;

      $formatting = substr($num,0,strlen($num)-2) . ".";
      $formatting = $formatting . substr($num,strlen($num)-2,strlen($num));
      return $formatting;   
}

function formatNumberCommas($i) {
   $x = separateStringBy(trim($i),".");
   
   $str = "";
   if($x[1]!=NULL) $str = ".".$x[1];
   
   $rmn = $x[0];
   
   while(strlen($rmn)>3) {
      $l = strlen($rmn);
      $a = substr($rmn,0,($l-3));
      $b = substr($rmn,($l-3));
      $rmn = $a;
      if(strlen($str)<3) $str = $b;
      else $str = $b.",".$str;
   }
   
   if(strlen($rmn)>0 && strlen($str)>2) $str = $rmn.",".$str;
   else if(strlen($rmn)>0) $str = $rmn;
   
   return $str;
}

function luhnCheck($num){
   $odd = TRUE;
   $total = 0;

   for ($i=strlen($num); $i>0; $i--) {
      $digit = substr($num,($i-1),1);
      if ($odd) {
         $total += $digit;
      } else {
         $newdig = 2 * $digit;
         while($newdig>9) $newdig = $newdig - 9;
         $total += $newdig;
      }
      $odd = !$odd;
   }
   return (($total % 10)==0);
}

function luhnGetLastDigit($num){
   $odd = FALSE;
   $total = 0;
   $num = strval($num);
   for ($i=strlen($num); $i>0; $i--) {
      $digit = substr($num,($i-1),1);
      if ($odd) {
         $total += $digit;
      } else {
         $newdig = 2 * $digit;
         while($newdig>9) $newdig = $newdig - 9;
         $total += $newdig;
      }
      $odd = !$odd;
   }
   $newProduct = 9 * $total;
   return ($newProduct % 10);
}

function createQRCode($qr_filename,$qr_codeStr) {
   if ($qr_filename!=NULL && !file_exists($qr_filename) && $qr_codeStr!=NULL) {
      $qr_image = "http://chart.apis.google.com/chart?chs=150x150&cht=qr&chld=L|0&chl=".$qr_codeStr;
      $ch = curl_init($qr_image); 
      $fp = fopen($qr_filename, 'wb'); 
      curl_setopt($ch, CURLOPT_FILE, $fp); 
      curl_setopt($ch, CURLOPT_HEADER, 0); 
      curl_exec($ch); 
      curl_close($ch); 
      fclose($fp);
   }
}

function parseURLParams($url=NULL,$testing=0) {
   if($url==NULL) {
      if($testing==1) print "<br>\nNO URL SENT<br>\n";
      $url = $_SERVER['REQUEST_URI'];
   } else {
      if($testing==1) print "<br>\nURL SENT: ".$url."<br>\n";      
   }
   
   //could be a full URL, or just the URI, be prepared for either
   $uri = separateStringBy($url,"?");
   $params = array();
   if(isset($uri[1])) {
      $params=separateStringBy($uri[1],"&",NULL,TRUE);
      if($testing==1) print "<br>\nQuestion mark found: ".$uri[1];
   } else {
      $params=separateStringBy($uri[0],"&",NULL,TRUE);
      if($testing==1) print "<br>\nNO Question mark found: ".$uri[0];
   }
   if($testing==1) print "<br>\nparams passed in: ".count($params);
   
   $results = array();
   for ($i=0; $i<count($params); $i++) {
      $variable = separateStringBy($params[$i],"=");
      if($testing==1) print "<br>\nparams line: ".$params[$i];
      if (isset($variable[0]) && $variable[0]!=NULL && isset($variable[1]) && $variable[1]!=NULL) {
          $lasttwo = substr($variable[0],-2);
           if(0==strcmp($lasttwo,"[]")) {
              if($testing==1) print "<BR>\narray nvp: ".$variable[0].",".$variable[1];
              $variable[0] = substr($variable[0],0,(strlen($variable[0])-2));
              if(!isset($results[$variable[0]]) || !is_array($results[$variable[0]])) $results[$variable[0]]=array();
              $results[$variable[0]][] = urldecode($variable[1]);
           } else {
              if($testing==1) print "<BR>\nregular nvp: ".$variable[0].",".$variable[1];
              $results[$variable[0]] = urldecode($variable[1]);
           }
      }
   }
   if($testing==1) print "<br>\nCount of results: ".count($results);
   if($results==NULL || count($results)<1) $results = $_GET;
   return $results;
}


Class ScheduledSQLCSV {
   function doWork($job){
      // $job:
      // field1: 1 if limit should be set, 0 if the sql should just run
      // field2: number of entries
      // field3: indicates the text of last time this ran
      // field5: filename relative to the working web directory (this can be set manually if desired)
      // content: SQL to run
      // subject: title given (also used in filename)
      if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
       //$phpobj = unserialize($job['phpobj']);
         
      $subject = trim($job['subject']);
      if($subject==NULL) $subject = "notitle";
      $subject = substr(removeSpecialChars($subject),0,8);
      
      if($job['field5']==NULL) $job['field5'] = "jsfadmin/usercsv/sql_se".$job['semailid']."_".date("YmdHis")."_".$subject.".csv";
      if($job['field1']==NULL) $job['field1']=0;
      if($job['field2']==NULL) $job['field2']=0;
      $job['field3'] = "Last ran: ".date("m/d/Y H:i:s");
   
      if($job['content']!=NULL) {
         $query = trim($job['content']);
         if(0==strcmp(substr($query,(strlen($query)-1),1),";")) $query = substr($query,0,(strlen($query)-1));

         if($job['field1']==1) $query .= " LIMIT ".$job['field2'].",200";
         $content = "";
         $sql = new MYSQLaccess();
         $results = $sql->queryGetResults($query);
         if($results!=NULL && count($results)>0) {
            for ($i=0;$i<count($results);$i++) {
               if($job['field2']==0) foreach($results[0] as $key => $val) $content .= "\"".csvEncodeDoubleQuotes($key)."\",";
               if($job['field2']==0) $content .= "\n";
               foreach($results[$i] as $key => $val) $content .= "\"".csvEncodeDoubleQuotes($val)."\",";
               $content .= "\n";
               $job['field2']++;
            }
            
            $file = fopen($GLOBALS['baseDir'].$job['field5'],"a");
            fwrite($file, $content);
            fclose($file);
         }
         if(count($results)<200 || $job['field1']==0) $job['status'] = "FINISHED";
         else $job['status'] = "NEW";      
      } else {
         $job['status'] = "FINISHED";
      }
   
      return $job;
   }

   function createJob($sql,$subj="",$field1=1) {
      $subj .= " (".date("m/d/Y H:i:s").")";
      $sched = new Scheduler();
      //addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL){
      $sched->addSchedCustom("ScheduledSQLCSV",$subj,4,NULL,$sql,$field1);
   }
   
   function copyJob($copyid=NULL) {
      $jobresults = FALSE;
      if($copyid!=NULL){
         $dbLink = new MYSQLaccess;
         
         $query = "SELECT * FROM schedemail WHERE semailid=".$copyid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            $sched = new Scheduler();
            $sched->addSchedCustom("ScheduledSQLCSV",$results[0]['subject'],4,NULL,NULL,$results[0]['field1']);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
}

Class ScheduledJSONCSV {
   function doWork($job){
      // $job:
      // field1: limit, 0 if the sql should just run
      // field2: page number
      // field3: indicates the text of last time this ran
      // field4: parameter names for JSON: limitparam,pageparam,resultsparam
      // field5: filename of resulting csv
      // content: JSON to run
      // subject: title given (also used in filename)
      if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
      //$phpobj = unserialize($job['phpobj']);
      
      $job['status'] = "FINISHED";
      $runagain = FALSE;
         
      $subject = trim($job['subject']);
      if($subject==NULL) $subject = "notitle";
      $subject = substr(removeSpecialChars($subject),0,8);
      
      if($job['field5']==NULL) $job['field5'] = "jsfadmin/usercsv/json_se".$job['semailid']."_".date("YmdHis")."_".$subject.".csv";
      if($job['field1']===NULL) $job['field1']=50;
      if($job['field2']==NULL || $job['field2']<1) $job['field2']=1;
      $job['field3'] = "Last ran: ".date("m/d/Y H:i:s");
   
      if($job['content']!=NULL) {
         $param = separateStringBy(convertBack($job['field4']),",");
         $query = $job['content'];
         if($job['field1']>0 && $param[0]!=NULL && $param[1]!=NULL) {
            $query .= "&".$param[0]."=".$job['field1'];
            $query .= "&".$param[1]."=".$job['field2'];
            $runagain = TRUE;
         }
         $content = "";
         $results = requestJSON($query,FALSE,TRUE);
         
         //Get the results from the json response
         $rows = array();
         if($param[2]!=NULL) $rows = $results[$param[2]];
         else if($results['rows']!=NULL && count($results['rows'])>0) $rows = $results['rows'];
         else if($results['results']!=NULL && count($results['results'])>0) $rows = $results['results'];
         else if($results['users']!=NULL && count($results['users'])>0) $rows = $results['users'];
         else if($results['records']!=NULL && count($results['records'])>0) $rows = $results['records'];
         else $rows = $results;
         
         //Make sure there are results to record
         if($rows!=NULL && is_array($rows) && count($rows)>0) {
            for ($i=0;$i<count($rows);$i++) {
               if($job['field2']==1 && $i==0) {
                  foreach($rows[0] as $key => $val) $content .= "\"".csvEncodeDoubleQuotes($key)."\",";
                  $content .= "\n";
               }
               foreach($rows[$i] as $key => $val) {
                  $content .= "\"".csvEncodeDoubleQuotes($val)."\",";
               }
               $content .= "\n";
            }
            
            $file = fopen($GLOBALS['baseDir'].$job['field5'],"a");
            fwrite($file, $content);
            fclose($file);
            
            if($runagain) $job['status'] = "NEW";      
            $job['field2']++;
         }
      }
      return $job;
   }

   // $sjsoncsv = new ScheduledJSONCSV();
   // $sjsoncsv->createJob($json,$subj,$limitparam,$pageparam,$resultsparam,$field1,$userid);
   function createJob($json,$subj="",$limitparam=NULL,$pageparam=NULL,$resultsparam=NULL,$field1=NULL,$userid=NULL,$field2=NULL) {
      $subj .= " (".date("m/d/Y H:i:s").")";
      $sched = new Scheduler();
      $field4 = $limitparam.",".$pageparam.",".$resultsparam;
      if($field2==NULL) $field2=1;
      //addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL,$phpfile=NULL,$userid=NULL)
      $sched->addSchedCustom("ScheduledJSONCSV",$subj,3,NULL,$json,$field1,$field2,"New",$field4,NULL,NULL,NULL,0,NULL,NULL,$userid);
   }
}


?>