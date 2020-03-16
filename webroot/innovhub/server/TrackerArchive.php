<?php

//-----------------------------------------------------------------------------
// Template class
//
//-----------------------------------------------------------------------------
class TrackerArchive
{
   function decipherAgent($agent){
      $thisagent = "";
      if ($agent!=NULL) {
         if (strpos(strtolower($agent),"iphone")!==FALSE) {
            $thisagent = "ios";
         } else if (strpos(strtolower($agent),"ipod")!==FALSE) {
            $thisagent = "ios";
         } else if  (strpos(strtolower($agent),"ipad")!==FALSE) {
            $thisagent = "ios";
         } else if  (strpos(strtolower($agent),"android")!==FALSE) {
            $thisagent = "android";
         } else if  (strpos(strtolower($agent),"windows nt")!==FALSE) {
            $thisagent = "windows";
         } else if  (strpos(strtolower($agent),"iemobile")!==FALSE) {
            $thisagent = "wince";
         } else if  (strpos(strtolower($agent),"windows ce")!==FALSE) {
            $thisagent = "wince";
         } else if  (strpos(strtolower($agent),"windows phone")!==FALSE) {
            $thisagent = "wince";
         } else if  (strpos(strtolower($agent),"macintosh")!==FALSE) {
            $thisagent = "macintosh";
         } else if  (strpos(strtolower($agent),"linux")!==FALSE) {
            $thisagent = "linux";
         } else if  (strpos(strtolower($agent),"blackberry")!==FALSE) {
            $thisagent = "bberry";
         }
      }
      return $thisagent;
   }
  
   function getBaseDomain($ref){
      $referring = strtolower($ref);
      $prt_pos = strpos($referring,"://");
      if ($prt_pos!==FALSE) $referring=substr($referring,($prt_pos+3));
      $prt_pos = strpos($referring,"/");
      if ($prt_pos!==FALSE) $referring=substr($referring,0,$prt_pos);
      return $referring;
   }
   
   function getDomainURI($ref){
      $referring = strtolower($ref);
      $prt_pos = strpos($referring,"://");
      if ($prt_pos!==FALSE) $referring=substr($referring,($prt_pos+3));
      $prt_pos = strpos($referring,"/");
      if ($prt_pos!==FALSE) $referring=substr($referring,($prt_pos+1));
      $prt_pos = strpos($referring,"?");
      if ($prt_pos!==FALSE) $referring=substr($referring,0,$prt_pos);
      return $referring;
   }

   /*
   function moveTrackingRow($trkid){
      $dbLink = new MYSQLAccess;
      $query = "SELECT * FROM tracker WHERE trkid=".$trkid.";";
      $results = $dbLink->queryGetResults($query);
      $line = $results[0];
      $archid = NULL;
      if ($line!=NULL && $line['trkid']!=NULL) {
         $counter = 0;
         $names = "";
         $values = "";
         foreach ($line as $key => $value) {
            if ($counter>0) $names .= ", ";
            $names .= $key;
            if ($counter>0) $values .= ", ";
            $values .= "'".$value."'";
         }
         $query = "INSERT INTO trackerarch (".$names.") VALUES (".$values.");";
         $archid = $dbLink->insertGetValue($query);
         if ($archid>0) {
            $query = "DELETE FROM tracker WHERE trkid=".$trkid.";";
            $dbLink->delete($query);
         }
      }
      return $archid;
   }
   */
   
   function moveTrackingRowArray($rows,$printstuff=FALSE){
      $dbLink = new MYSQLAccess;
      $aliases = array();
      $names = "";
      $counter = 0;
      $ssn_geo = array();
      $upd_rows = array();
      $usr_swch = array();
      
      $initrow = $rows[0];
      
      if ($rows!=NULL && count($rows)>0) {
         foreach ($initrow as $key => $value) {
            if ($counter>0) $names .= ", ";
            $names .= $key;
            $counter++;
         }
         if ($counter>0) {
            $deletequery = "DELETE FROM tracker WHERE trkid IN (";
            $deleteCount = 0;

            for ($i=0; $i<count($rows); $i++) {
               $line = $rows[$i];
               if ($line!=NULL && $line['trkid']!=NULL) {
                  if (0==strcmp($line['view'],"UserSwitch")) {
                     /**
                     if ($aliases[$line['jsftrack2']]==NULL) $aliases[$line['jsftrack2']] = "'".$line['jsftrack1']."'";
                     else $aliases[$line['jsftrack2']] .= ",'".$line['jsftrack1']."'";
                     **/
                     $usr_swch[$line['jsftrack1']] = $line['jsftrack2'];
                     if ($deleteCount>0) $deletequery .= ", ";
                     $deletequery .= $line['trkid'];
                     $deleteCount++;
                  } else if (0==strcmp($line['view'],"TrackGeoCode")) {
                     //$ugeo_query = "UPDATE trackerarch SET country='".$line['country']."', region='".$line['region']."', city='".$line['city']."', lat=".$line['lat'].", lng=".$line['lng']." WHERE sessionid='".$line['sessionid']."' AND (lat IS NULL OR lat=0.000000)";
                     //$dbLink->update($ugeo_query);
                     //$ugeo_query = "UPDATE tracker SET country='".$line['country']."', region='".$line['region']."', city='".$line['city']."', lat=".$line['lat'].", lng=".$line['lng']." WHERE sessionid='".$line['sessionid']."' AND (lat IS NULL OR lat=0.000000)";
                     //$dbLink->update($ugeo_query);
                     $ssn_geo[$line['sessionid']]['country'] = $line['country'];
                     $ssn_geo[$line['sessionid']]['region'] = $line['region'];
                     $ssn_geo[$line['sessionid']]['city'] = $line['city'];
                     $ssn_geo[$line['sessionid']]['postal'] = $line['postal'];
                     $ssn_geo[$line['sessionid']]['lat'] = $line['lat'];
                     $ssn_geo[$line['sessionid']]['lng'] = $line['lng'];
                     if ($deleteCount>0) $deletequery .= ", ";
                     $deletequery .= $line['trkid'];
                     $deleteCount++;
                  } else {
                     $upd_rows[$line['trkid']] = array();
                     foreach ($initrow as $key => $value) $upd_rows[$line['trkid']][$key] = $line[$key];
                     if ($deleteCount>0) $deletequery .= ", ";
                     $deletequery .= $line['trkid'];
                     $deleteCount++;
                  }
               }
            }

            $insertCount = 0;
            $query = "INSERT INTO trackerarch (".$names.") VALUES ";
            if ($printstuff) {
               print "<br>\n<BR>\n************chj************<br>\n";
               print "count: ".count($upd_rows)."\n<br>\n";
            }
            foreach($upd_rows as $line){
               $values = "";
               $counter = 0;
               if ($line['region']==NULL && isset($ssn_geo[$line['sessionid']]) && $ssn_geo[$line['sessionid']]['region']!=NULL) {
                  $line['country'] = $ssn_geo[$line['sessionid']]['country'];
                  $line['region'] = $ssn_geo[$line['sessionid']]['region'];
                  $line['city'] = $ssn_geo[$line['sessionid']]['city'];
                  $line['postal'] = $ssn_geo[$line['sessionid']]['postal'];
                  $line['lat'] = $ssn_geo[$line['sessionid']]['lat'];
                  $line['lng'] = $ssn_geo[$line['sessionid']]['lng'];
               }
               if (strpos($line['jsftrack1'],"_")!==FALSE && isset($usr_swch[$line['jsftrack1']]) && $usr_swch[$line['jsftrack1']]!=NULL) {
                  $line['jsftrack1']=$usr_swch[$line['jsftrack1']];
               }
               foreach ($initrow as $key => $value) {
               //foreach ($line as $key => $value) {
                  if ($counter>0) $values .= ", ";
                  if ($line[$key]==NULL || strlen($line[$key])==0 || 0==strcmp($line[$key],"NULL") || 0==strcmp($line[$key],"undefined")) $values .= "NULL";
                  else $values .= "'".$line[$key]."'";
                  $counter++;
               }
               if ($insertCount>0) $query .= ", ";
               $query .= "(".$values.")";
               $insertCount++;
            }

            $query .= ";";
            $deletequery .= ");";
            if ($printstuff) {
               print "\n<br>insert count: ".$insertCount.", delete count: ".$deleteCount."\n<br>";
               print "\n<br>Query: ".$query."\n<br>";
               print "\n<br>Delete Query: ".$deletequery."\n<br>";
            }
            $dbLink->insert($query);
            $dbLink->delete($deletequery);
         }
      }
   }

   function moveTrackingRowArraySimple($rows,$printstuff=FALSE){
      $dbLink = new MYSQLAccess;
      $names = "";
      $counter = 0;
      $upd_rows = array();
      if ($rows!=NULL && count($rows)>0) {
         foreach ($rows[0] as $key => $value) {
            if ($counter>0) $names .= ", ";
            $names .= $key;
            $counter++;
         }
         if ($counter>0) {
            $deletequery = "DELETE FROM tracker WHERE trkid IN (";
            $deleteCount = 0;

            for ($i=0; $i<count($rows); $i++) {
               $line = $rows[$i];
               if ($line!=NULL && $line['trkid']!=NULL) {
                  $upd_rows[$line['trkid']] = array();
                  foreach ($line as $key => $value) $upd_rows[$line['trkid']][$key] = $value;
                  if ($deleteCount>0) $deletequery .= ", ";
                  $deletequery .= $line['trkid'];
                  $deleteCount++;
               }
            }

            $insertCount = 0;
            $query = "INSERT INTO trackerarch (".$names.") VALUES ";
            if ($printstuff) {
               print "<br>\n<BR>\n************chj************<br>\n";
               print "count: ".count($upd_rows)."\n<br>\n";
            }
            foreach($upd_rows as $line){
               $values = "";
               $counter = 0;
               foreach ($line as $key => $value) {
                  if ($counter>0) $values .= ", ";
                  $values .= "'".$value."'";
                  $counter++;
               }
               if ($insertCount>0) $query .= ", ";
               $query .= "(".$values.")";
               $insertCount++;
            }

            $query .= ";";
            $deletequery .= ");";
            if ($printstuff) {
               print "\n<br>insert count: ".$insertCount.", delete count: ".$deleteCount."\n<br>";
               print "\n<br>Query: ".$query."\n<br>";
               print "\n<br>Delete Query: ".$deletequery."\n<br>";
            }
            $dbLink->insert($query);
            $dbLink->delete($deletequery);
         }
      }
   }

   function cacheuserstats($printstuff=FALSE,$skipreferrals=FALSE){
      $dbLink = new MYSQLAccess();
      $start_time = time();
      $end_time = $start_time + (2*60);
      $finished = FALSE;

      while(time()<=$end_time && !$finished) {
         //For updating trackerref to track the opt-in referrals
         $ref_date = date("Y-m",time())."-01 00:00:00";
         $ref_arr = array();
         $ref_arr['insert'] = array();
         $ref_arr['dellist'] = "";
         $ref_arr['delcnt'] = 0;

         $query = "SELECT DISTINCT jsftrack1 FROM trackerarch WHERE jsftrack1 REGEXP '^[0-9]+$' AND (scan1 IS NULL OR scan1=0) LIMIT 0,100;";
         $results = $dbLink->queryGetResults($query);
         if ($printstuff) print "distinct query: ".$query."\n<br>Returned: ".count($results)." results\n<br><br>";

         $i = 0;
         $ust_arr = array();
         $ust_arr_count = 0;
         $archids = "";
         $deluserids = "";
         $useridstats = array();
         $useridinfo = array();

         if (count($results)>0 && time()<=$end_time) {
            $userStr = "";
            for ($j=0;$j<count($results);$j++) {
               if ($j>0) $userStr .= ", ";
               $useridstats[$results[$j]['jsftrack1']] = array();
               $userStr .= "'".$results[$j]['jsftrack1']."'";
            }
            $query = "SELECT * FROM trackerarch WHERE jsftrack1 IN (".$userStr.") AND (scan1 IS NULL OR scan1=0);";
            $tempstats = $dbLink->queryGetResults($query);
            for ($j=0;$j<count($tempstats);$j++) $useridstats[$tempstats[$j]['jsftrack1']][] = $tempstats[$j];
            if ($printstuff) print "user stats: ".$query."\n\n<br><br>";

            $query = "SELECT * FROM trackeruser WHERE userid IN (".$userStr.");";
            $userinfo = $dbLink->queryGetResults($query);
            for ($j=0;$j<count($userinfo);$j++) $useridinfo[$userinfo[$j]['userid']] = $userinfo[$j];
            if ($printstuff) print "users query: ".$query."\n\n<br><br>";

         } else {
            $finished=TRUE;
         }


         while(!$finished && $i<count($results)) {
            $newstats = FALSE;
            //$query = "SELECT * FROM trackeruser WHERE userid='".$results[$i]['jsftrack1']."';";
            //$user_n = $dbLink->queryGetResults($query);
            //$user = $user_n[0];
            //if ($printstuff) print "users query: ".$query."\n\n<br><br>";
            $user = NULL;
            if(isset($useridinfo[$results[$i]['jsftrack1']])) $user = $useridinfo[$results[$i]['jsftrack1']];

            if ($user==NULL || $user['userid']==NULL) {
               //$query = "INSERT INTO trackeruser (userid) VALUES ('".$results[$i]['jsftrack1']."');";
               //$dbLink->insert($query);
               $user = array();
               $user = array();
               $user['userid']=$results[$i]['jsftrack1']; 
               $user['win'] = 0;
               $user['ios'] = 0;
               $user['mac'] = 0;
               $user['linux'] = 0;
               $user['android'] = 0;
               $user['bb'] = 0;
               $user['other'] = 0;
               $user['wince'] = 0;
               $user['pg_1'] = 0;
               $user['pg_2'] = 0;
               $user['pg_3'] = 0;
               $user['pg_4'] = 0;
               $user['pg_5'] = 0;
               $user['pg_6'] = 0;
               $user['pg_7'] = 0;
               $user['pg_8'] = 0;
               $user['news_1'] = 0;
               $user['news_2'] = 0;
               $user['news_3'] = 0;
               $user['news_4'] = 0;
               $user['news_5'] = 0;
               $user['news_6'] = 0;
               $user['news_7'] = 0;
               $user['news_8'] = 0;
               $user['visits']=0;
               $user['sessions']=0;
               $user['opens']=0;
               $user['clkthrus']=0;
               $user['cur_value']=0;
               $user['lft_value']=0;
               $user['o_ref']="";
               $user['o_ref2']="";
               $user['o_location']="";
               $user['l_location']="";
               $user['o_agent']="";
               $user['l_agent']="";
               $newstats = TRUE;
               //if ($printstuff) print "insert query: ".$query."<br>";
            }
   
            $lastvisit = 0;
            if (isset($user['lastvisit']) && $user['lastvisit']!=NULL) $lastvisit = strtotime($user['lastvisit']);
            $lastopen = 0;
            if (isset($user['lastopen']) && $user['lastopen']!=NULL) $lastopen = strtotime($user['lastopen']);
   
            $win = intval($user['win']);
            $ios = intval($user['ios']);
            $mac = intval($user['mac']);
            $linux = intval($user['linux']);
            $android = intval($user['android']);
            $bb = intval($user['bb']);
            $other = intval($user['other']);
            $wince = intval($user['wince']);
            $pg = array();
            $news = array();
            $pg[1] = intval($user['pg_1']);
            $pg[2] = intval($user['pg_2']);
            $pg[3] = intval($user['pg_3']);
            $pg[4] = intval($user['pg_4']);
            $pg[5] = intval($user['pg_5']);
            $pg[6] = intval($user['pg_6']);
            $pg[7] = intval($user['pg_7']);
            $pg[8] = intval($user['pg_8']);
            $news[1] = intval($user['news_1']);
            $news[2] = intval($user['news_2']);
            $news[3] = intval($user['news_3']);
            $news[4] = intval($user['news_4']);
            $news[5] = intval($user['news_5']);
            $news[6] = intval($user['news_6']);
            $news[7] = intval($user['news_7']);
            $news[8] = intval($user['news_8']);
            $visits = intval($user['visits']);
            $sessions = intval($user['sessions']);
            $opens = intval($user['opens']);
            $clkthrus = intval($user['clkthrus']);
            $cur_value = intval($user['cur_value']);
            $lft_value = intval($user['lft_value']);
   
            $o_ref = trim($user['o_ref']);
            $o_ref2 = trim($user['o_ref2']);
            $o_location = $user['o_location'];
            $l_location = $user['l_location'];
            $o_agent = $user['o_agent'];
            $l_agent = $user['l_agent'];
   
            $newsessions = array();
   
            //$query = "SELECT * FROM trackerarch WHERE jsftrack1='".$results[$i]['jsftrack1']."' AND (scan1 IS NULL OR scan1=0);";
            //$userstats = $dbLink->queryGetResults($query);
            //if ($printstuff) print "user stats: ".$query."\n\n<br><br>";
            $userstats = $useridstats[$results[$i]['jsftrack1']];

            $deluserids .= $results[$i]['jsftrack1'].", ";
            for ($j=0;$j<count($userstats);$j++) {
               $pageview = FALSE;
               $emaillink = FALSE;
               $newsignup = FALSE;
               if (0==strcmp(strtolower($userstats[$j]['view']),"emaillink")) $emaillink=TRUE;
               else if (0==strcmp(strtolower($userstats[$j]['view']),"pageload")) $pageview=TRUE;
               else if (0==strcmp(strtolower($userstats[$j]['view']),"newsignup")) $newsignup=TRUE;
               $newsessions[$userstats[$j]['sessionid']] = 1;
               $timeofrecord = strtotime($userstats[$j]['created']);
               $hour = substr($userstats[$j]['created'],11,2);
               $tod = floor(((int)$hour)/3) + 1;
   
               $loc = NULL;            
               if (isset($userstats[$j]['country']) && $userstats[$j]['country']!=NULL) $loc = $userstats[$j]['country'].";".$userstats[$j]['region'].";".$userstats[$j]['city'].";".$userstats[$j]['postal'].";".$userstats[$j]['lat'].";".$userstats[$j]['lng'];
   
               $thisagent = $this->decipherAgent($userstats[$j]['agent']);
               if (0==strcmp($thisagent,"ios")) $ios++;
               else if (0==strcmp($thisagent,"windows")) $win++;
               else if (0==strcmp($thisagent,"macintosh")) $mac++;
               else if (0==strcmp($thisagent,"linux")) $linux++;
               else if (0==strcmp($thisagent,"android")) $android++;
               else if (0==strcmp($thisagent,"wince")) $wince++;
               else if (0==strcmp($thisagent,"bberry")) $bb++;
               else $other++;
   
               if ($newstats) {
                  $lastvisit = $timeofrecord;
                  $o_agent = $thisagent;
                  $l_agent=$thisagent;
                  $o_location=$loc;
                  $l_location=$loc;
                  $newstats = FALSE;
               } else {
                  if ($timeofrecord>$lastvisit) {
                     $lastvisit = $timeofrecord;
                     $l_agent=$thisagent;
                     if ($loc!=NULL) $l_location=$loc;
                  } else {
                     if ($l_location==NULL && $loc!=NULL) $l_location = $loc;
                     if ($l_agent==NULL) $l_agent = $thisagent;                  
                  }
               }
   
               //depreciate the current value based on the last time we scored them
               $diff = time() - $lastopen;
               $yrPct = (($diff/(24*60*60)) /365);
               if ($yrPct>1) $yrPct = 1;
               else if ($yrPct<0) $yrPct = 0;
               $depr = (1 - $yrPct);
               $cur_value = ceil($cur_value * $depr);
               $lastopen = time();
   
               if ($pageview || $emaillink || $newsignup) {
                  $visits++;
                  if ($tod!=NULL && $tod>0 && $tod<9) $pg[$tod]++;
   
                  $diff = round((time() - $timeofrecord)/(24*60*60));
                  if ($diff<120) $cur_value = $cur_value + 1;
   
                  $lft_value++;
               }
   
               if ($emaillink) {
                  $clickthrus++;
                  if ($tod!=NULL && $tod>0 && $tod<9) $news[$tod]++;
               }
   
               //if this is a new user, remember the referrer for this entry (even if it is redtri.com)
               if (0==strcmp(strtolower($userstats[$j]['view']),"newsignup") && $o_ref==NULL) {
                  $o_ref = $userstats[$j]['referer'];
                  if (!$skipreferrals) $ref_arr = $this->updateTrackerReferences($o_ref,$ref_arr,$ref_date);
                  
               } else if ($o_ref==NULL) {
                  $referring = $this->getBaseDomain($userstats[$j]['referer']);
                  if (strpos($referring,"redtri.com")===FALSE && strpos($referring,"redtricom.wordpress")===FALSE) {
                     $o_ref = $referring;
                  }
               }

               if ($o_ref2==NULL) {
                  $referring = $this->getBaseDomain($userstats[$j]['referer']);
                  if (strpos($referring,"redtri.com")===FALSE && strpos($referring,"redtricom.wordpress")===FALSE) {
                     $o_ref2 = $referring;
                  }
               }
               $archids .= $userstats[$j]['archid'].", ";
            }
            $query = "('".$results[$i]['jsftrack1']."', ";
            $query .= "'".$o_ref."', ";
            $query .= "'".$o_ref2."', ";
            $query .= "'".$o_agent."', ";
            $query .= "'".$l_agent."', ";
            $query .= "'".$o_location."', ";
            $query .= "'".$l_location."', ";
            $query .= $cur_value.", ";
            $query .= $lft_value.", ";
            $query .= $visits.", ";
            $query .= $clkthrus.", ";
            $query .= ($sessions + count($newsessions)).", ";
            $query .= $win.", ";
            $query .= $ios.", ";
            $query .= $mac.", ";
            $query .= $android.", ";
            $query .= $wince.", ";
            $query .= $linux.", ";
            $query .= $bb.", ";
            $query .= $other.", ";
            $query .= $pg[1].", ";
            $query .= $pg[2].", ";
            $query .= $pg[3].", ";
            $query .= $pg[4].", ";
            $query .= $pg[5].", ";
            $query .= $pg[6].", ";
            $query .= $pg[7].", ";
            $query .= $pg[8].", ";
            $query .= $news[1].", ";
            $query .= $news[2].", ";
            $query .= $news[3].", ";
            $query .= $news[4].", ";
            $query .= $news[5].", ";
            $query .= $news[6].", ";
            $query .= $news[7].", ";
            $query .= $news[8].", ";
            $query .= "'".date("Y-m-d H:i:s",$lastvisit)."', ";
            $query .= "'".date("Y-m-d H:i:s",$lastopen)."')";
            $ust_arr[$ust_arr_count] = $query;
            $ust_arr_count++;
            $i++;
         }
      
         if (0!=strcmp($deluserids,"") && $deluserids!=NULL) {
            $query = "DELETE FROM trackeruser WHERE userid IN (".$deluserids." 0);";
            if ($printstuff) print "first, remove all tracker user data: ".$query."\n\n<br><br>";
            $dbLink->delete($query);
         }

         if (0!=strcmp($archids,"") && $deluserids!=NULL) {
            $query = "UPDATE trackerarch SET scan1=1 WHERE archid IN (".$archids." 0);";
            if ($printstuff) print "update trackerarch so we don't rescan: ".$query."\n\n<br><br>";
            $dbLink->update($query);
         }

         if (count($ust_arr)>0) {
            $query = "INSERT INTO trackeruser (";
            $query .= "userid, ";
            $query .= "o_ref, ";
            $query .= "o_ref2, ";
            $query .= "o_agent, ";
            $query .= "l_agent, ";
            $query .= "o_location, ";
            $query .= "l_location, ";
            $query .= "cur_value, ";
            $query .= "lft_value, ";
            $query .= "visits, ";
            $query .= "clkthrus, ";
            $query .= "sessions, ";
            $query .= "win, ";
            $query .= "ios, ";
            $query .= "mac, ";
            $query .= "andr, ";
            $query .= "wince, ";
            $query .= "lnx, ";
            $query .= "bb, ";
            $query .= "other, ";
            $query .= "pg_1, ";
            $query .= "pg_2, ";
            $query .= "pg_3, ";
            $query .= "pg_4, ";
            $query .= "pg_5, ";
            $query .= "pg_6, ";
            $query .= "pg_7, ";
            $query .= "pg_8, ";
            $query .= "news_1, ";
            $query .= "news_2, ";
            $query .= "news_3, ";
            $query .= "news_4, ";
            $query .= "news_5, ";
            $query .= "news_6, ";
            $query .= "news_7, ";
            $query .= "news_8, ";
            $query .= "lastvisit, ";
            $query .= "lastopen) VALUES ";
            for ($j=0;$j<count($ust_arr);$j++) {
               if ($j>0) $query .= ", ";
               $query .= $ust_arr[$j];
            }
            $query .= ";";
            if ($printstuff) print "now insert all user stats: ".$query."\n\n<br><br>";
            $dbLink->insert($query);
         }

         //delete existing references from this batch of signups: trackerref
         if ($ref_arr['delcnt']==1) {
            $query = "DELETE FROM trackerref WHERE trid=".$ref_arr['dellist'].";";
            if ($printstuff) print "remove all tracker reference data: ".$query."\n\n<br><br>";
            $dbLink->delete($query);
         } else if ($ref_arr['delcnt']>1) {
            $query = "DELETE FROM trackerref WHERE trid IN (".$ref_arr['dellist'].");";
            if ($printstuff) print "remove all tracker reference data: ".$query."\n\n<br><br>";
            $dbLink->delete($query);
         }

         //insert references from this batch of signups: trackerref
         $ref_inscnt = 0;
         $query = "";
         foreach($ref_arr['insert'] as $tref => $values) {
            if ($ref_inscnt==0) $query = "INSERT INTO trackerref (tref,reftype,counter,refmonth) VALUES ";
            else $query .= ", ";
            $query .= "('".$tref."','".$values['reftype']."',".$values['counter'].",'".$ref_date."')";
            $ref_inscnt++;
         }
         if ($ref_inscnt>0) $dbLink->insert($query);
      }

      $finished=FALSE;
      $checkbefore = time() - 30*24*60*60;
      $checkbeforestr = date("Y-m-d",$checkbefore)." 00:00:00";
      while(time()<=$end_time && !$finished) {
         $query = "SELECT * FROM trackeruser WHERE lastopen<'".$checkbeforestr."' LIMIT 0,100;";
         $results = $dbLink->queryGetResults($query);
         if ($results==NULL || count($results)<1) $finished=TRUE;
         for ($i=0;$i<count($results);$i++) {
            //depreciate the current value based on the last time we scored them
            $lastopen = strtotime($results[$i]['lastopen']);
            $diff = time() - $lastopen;
            $yrPct = (($diff/(24*60*60)) /365);
            if ($yrPct>1) $yrPct = 1;
            else if ($yrPct<0) $yrPct = 0;
            $depr = (1 - $yrPct);
            $cur_value = ceil($results[$i]['cur_value'] * $depr);
            $query = "UPDATE trackeruser SET cur_value=".$cur_value.", lastopen=NOW() WHERE userid='".$results[$i]['userid']."';";
            $dbLink->update($query);
         }
      }

      
   }

   function getTopSignupReferences($num=14){
      $dbLink = new MYSQLAccess();
      $ref_date = date("Y-m",time())."-01 00:00:00";
      $query = "SELECT * FROM trackerref WHERE refmonth='".$ref_date."' order by counter desc limit 0,".$num.";";
      $results = $dbLink->queryGetResults($query);
      //print "\n<!-- ***chj*** query: ".$query." -->\n";
      return $results;
   }

   function updateTrackerReferences($o_ref,$ref_arr,$ref_date,$printstuff=FALSE){
      $dbLink = new MYSQLAccess();
      //Update trackerref table data
      if ($o_ref!=NULL) {
         $referring = $this->getBaseDomain($o_ref);
         $reftype = "external";
         if (strpos($referring,"redtri.com")!==FALSE || strpos($referring,"redtricom.wordpress")!==FALSE) {
            $referring = $this->getDomainURI($o_ref);
            $reftype = "internal";
         }
         if ($printstuff) print "\n<br>Referring: ".$referring;
         if ($referring!=NULL) {
            if (isset($ref_arr['insert'][$referring]) && $ref_arr['insert'][$referring]['counter']>0) {
               if ($printstuff) print "\n<br>already exists old counter: ".$ref_arr['insert'][$referring]['counter'];
               $ref_arr['insert'][$referring]['counter']++;
               if ($printstuff) print "\n<br>already exists new counter: ".$ref_arr['insert'][$referring]['counter'];
            } else {
               $counter = 1;
               $query = "SELECT trid,counter FROM trackerref WHERE tref='".$referring."' AND refmonth='".$ref_date."' ORDER BY counter DESC;";
               $results = $dbLink->queryGetResults($query);
               if ($printstuff) print "\n<br>does not exist, query: ".$query;
               if ($results!=NULL && count($results)>0) {
                  if ($ref_arr['delcnt']>0) $ref_arr['dellist'] .= ", "; 
                  $ref_arr['dellist'] .= $results[0]['trid'];
                  $counter = $results[0]['counter'] + 1;
                  $ref_arr['delcnt'] = $ref_arr['delcnt'] + 1;
                  if ($printstuff) print "\n<br>does not exist, but found in DB";
               }
               $ref_arr['insert'][$referring] = array();
               $ref_arr['insert'][$referring]['counter'] = $counter;
               $ref_arr['insert'][$referring]['reftype'] = $reftype;
               if ($printstuff) print "\n<br>new counter: ".$ref_arr['insert'][$referring]['counter'];
            }
         }
      }
      return $ref_arr;
   }

   function getTopUsers($num=200){
      $dbLink = new MYSQLAccess();

      $timeint = time() - 24*60*60;
      $query = "SELECT * FROM trackertopu WHERE timeint like '".date("Y-m-d",$timeint)."%' ORDER BY rank LIMIT 0,".$num.";";
      $results = $dbLink->queryGetResults($query);
      //print "\n<!-- query1 : ".$query." -->\n";
      //print "\n<!-- results: \n";
      //print_r($results);
      //print "\n -->\n";

      if ($results==NULL || count($results)<1) {
         $query = "DELETE FROM trackertopu;";
         $dbLink->delete($query);

         //max top users you can get is 200
         $query = "SELECT u.email, u.fname, u.lname, u.created, u.field4, u.notes as other_u, u.ownersite as oisource, u.refsrc as csource, u.title as cmedium, t.* FROM trackeruser t, useracct u WHERE u.userid=t.userid ORDER BY t.lft_value DESC LIMIT 0,200;";
         $results = $dbLink->queryGetResults($query);
         //print "\n<!-- results: \n";
         //print_r($results);
         //print "\n -->\n";
         $query = "INSERT INTO trackertopu ";
         for ($i=0;$i<count($results);$i++) {
            if ($i==0) {
               $query .= "(rank,timeint";
               foreach($results[$i] as $n => $v) $query .= ", ".$n;
               $query .= ") VALUES ";
            }
            
            if ($i>0) $query .= ", ";
            $query .= "(".$i.",'".date("Y-m-d",$timeint)." 00:00:00'";
            foreach($results[$i] as $v) $query .= ", '".$v."'";
            $query .= ")";
         }
         $dbLink->insert($query);
         //print "\n<!-- ***chj*** query2 : ".$query." -->\n";
      }
      return $results;
   }

   function getUserStats($userid){
      $dbLink = new MYSQLAccess();
      $query = "SELECT t.* FROM trackeruser t WHERE t.userid=".$userid.";";
      $results = $dbLink->queryGetResults($query);
      return $results[0];
   }

   function cacheStats($day,$printstuff=FALSE){
      $dbLink = new MYSQLAccess();
      $query = "select * from trackerstats where trkhour like '".date("Y-m-d",$day)."%' limit 0,1;";
      $results = $dbLink->queryGetResults($query);
      if ($results==NULL || count($results)<1) {
         $states = getStateOptionList();
         $start = date("Y-m-d",$day)." 00:00:00";
         $end = date("Y-m-d",$day)." 23:59:59";
         
         $ipaddr_region = array();

         //$newrows = $this->getTracking(NULL,$start,$end,"PageLoad,EmailLink,NewUser",NULL,NULL,999999,1,FALSE,"trackerarch", $printstuff);
         $newrows = $this->getTracking(NULL,$start,$end,NULL,NULL,NULL,999999,1,FALSE,"trackerarch", $printstuff);
         if ($newrows==NULL || count($newrows)<1) {
            if ($printstuff) print "\n<br>count of rows: ".count($rows);
            $otherrefs = array();
            $counters = array();
            $counters['visits'] = 0;
            $counters['ios'] = 0;
            $counters['android'] = 0;
            $counters['wince'] = 0;
            $counters['windows'] = 0;
            $counters['macintosh'] = 0;
            $counters['linux'] = 0;
            $counters['bberry'] = 0;
            $counters['ignore'] = 0;
            foreach($states as $value) {
               $counters["st".$value]=0;
            }
      
            for ($i=0; $i<count($rows); $i++) {
               $line = $rows[$i];
               $thisagent = "ignore";
               $thisref = "ignore";
               $thisstate = "ignore";
               
               if($line['region']==NULL && $line['ipaddr']!=NULL) {
                  if(isset($ipaddr_region[$line['ipaddr']])) {
                     $line['country'] = $ipaddr_region[$line['ipaddr']]['country'];
                     $line['region'] = $ipaddr_region[$line['ipaddr']]['region'];
                     $line['city'] = $ipaddr_region[$line['ipaddr']]['city'];
                     $line['lat'] = $ipaddr_region[$line['ipaddr']]['lat'];
                     $line['lng'] = $ipaddr_region[$line['ipaddr']]['lng'];
                     $line['postal'] = $ipaddr_region[$line['ipaddr']]['postal'];
                  } else {
                     $jsonurl = "http://freegeoip.net/json/" + $line['ipaddr'];
                     $data = requestJSON($jsonurl,$printstuff);
                     if($data!=NULL) {
                        $line['country'] = $data['country_code'];
                        $line['region'] = $data['region_code'];
                        $line['city'] = $data['zip_code'];
                        $line['lat'] = $data['latitude'];
                        $line['lng'] = $data['longitude'];
                        $line['postal'] = $data['zip_code'];
                        $query = "UPDATE trackerarch SET country='".$line['country']."', region='".$line['region']."', city='".$line['city']."', lat='".$line['lat']."', lng='".$line['lng']."', postal='".$line['postal']."' WHERE ipaddr='".$line['ipaddr']."'";
                        $dbLink->update($query);
                        $ipaddr_region[$line['ipaddr']] = $line;
                     }
                  }
               }
               
               
               //calculate stats.
               if (isset($line['region']) && $line['region']!=NULL && isset($states[$line['region']]) && $states[$line['region']]!=NULL) {
                  $thisstate = "st".$line['region'];
                  $counters[$thisstate]++;
               }
               if ($line['agent']!=NULL) {
                  $thisagent = $this->decipherAgent($line['agent']);
                  $counters[$thisagent]++;
               }
               $counters['visits']++;
            }
            $keeperrefs = array();
            foreach($otherrefs as $key => $value) if ($value>5) $keeperrefs[$key]=$value;
            if ($keeperrefs!=NULL && count($keeperrefs)>0) $counters['field8'] = mysqli_escape_string(serialize($keeperrefs));
            $this->writeStats($counters,$start,"stats");
            $this->writeStats($counterClickthruState,$start,"ctstate");
   
            $this->writeStats($counterAgentState['ios'],$start,"st_ios");
            $this->writeStats($counterAgentState['android'],$start,"st_andr");
            $this->writeStats($counterAgentState['wince'],$start,"st_wce");
            $this->writeStats($counterAgentState['windows'],$start,"st_win");
            $this->writeStats($counterAgentState['macintosh'],$start,"st_mac");
            $this->writeStats($counterAgentState['linux'],$start,"st_linux");
            $this->writeStats($counterAgentState['bberry'],$start,"st_bberry");
            
            $this->writeStats($counterAgentState['google'],$start,"st_ggl");
            $this->writeStats($counterAgentState['facebook'],$start,"st_fb");
            $this->writeStats($counterAgentState['yahoo'],$start,"st_yho");
            $this->writeStats($counterAgentState['bing'],$start,"st_bing");
            $this->writeStats($counterAgentState['pinterest'],$start,"st_pint");
            $this->writeStats($counterAgentState['ask'],$start,"st_ask");
         } else {
            $waitingonarch = TRUE;
         }
      }
      return $waitingonarch;
   }

   function writeStats($tstats,$starttime,$trkstat){
      $dbLink = new MYSQLAccess();
      $names = "";
      $values = "";
      if (is_array($tstats) && $tstats!=NULL) {
         foreach ($tstats as $key => $value) {
            if ($key!=NULL && 0!=strcmp($key,"ignore")) {
               $names .= ", ".$key;
               $values .= ", '".$value."'";
            }
         }
         $insert = "INSERT INTO trackerstats (trkhour, trkstat, created".$names.") VALUES ('".$starttime."','".$trkstat."', NOW()".$values.");";
         $dbLink->insert($insert);
      }
   }

   function getDailyStats($timeint,$trkstat="stats"){
      $query = "SELECT * FROM trackerstats WHERE trkhour LIKE '".date("Y-m-d",$timeint)."%' AND trkstat='".$trkstat."' LIMIT 0,24;";
      //$query = "SELECT * FROM trackerstats WHERE trkhour LIKE '".date("Y-m-d H:",$timeint)."%' LIMIT 0,24;";
      $dbLink = new MYSQLAccess();
      $results = $dbLink->queryGetResults($query);
      return $results;
   }

   function getWeeklyStats($timeint,$trkstat="stats"){
      $start = $timeint;
      $end = $timeint + (6*24*60*60);
      $query = "SELECT * FROM trackerstats WHERE trkhour>='".date("Y-m-d 00:00:00",$start)."%' AND trkhour<='".date("Y-m-d 23:59:59",$end)."%' AND trkstat='".$trkstat."' LIMIT 0,168;";
      $dbLink = new MYSQLAccess();
      $results = $dbLink->queryGetResults($query);
      return $results;
   }

   function getDailyStatsSum($timeint,$trkstat="stats") {
      $results = $this->getDailyStats($timeint,$trkstat);
      return $this->combineCachedResults($results);
   }

   function getWeeklyStatsSum($timeint,$trkstat="stats") {
      $results = $this->getWeeklyStats($timeint,$trkstat);
      return $this->combineCachedResults($results);
   }

   function combineCachedResults($results){
      $results_sum = array();
      $otherrefs = array();
      for ($i=0;$i<count($results);$i++) {
         $line = $results[$i];
         foreach($line as $key => $value){
            if (is_numeric($value)) {
               if (!isset($results_sum[$key])) $results_sum[$key]=$value;
               else $results_sum[$key]+=$value;
            }
         }
         $field8 = array();
         if($line['field8']!=NULL) $field8 = unserialize($line['field8']);
         foreach($field8 as $key => $value) {
            if (isset($otherrefs[$key])) $otherrefs[$key] += $value;
            else $otherrefs[$key] = $value;
         }
      }
      $results_sum['field8'] = serialize($otherrefs);
      return $results_sum;
   }
   function getHourlyStats($timeint,$trkstat="stats"){
      $query = "SELECT * FROM trackerstats WHERE trkhour LIKE '".date("Y-m-d H:",$timeint)."%' AND trkstat='".$trkstat."' LIMIT 0,1;";
      $dbLink = new MYSQLAccess();
      $results = $dbLink->queryGetResults($query);
      return $results[0];
   }

   function getTracking($searchstr=NULL,$start=NULL,$end=NULL,$view=NULL,$action=NULL,$orderby=NULL,$limit=100,$page=1,$countonly=FALSE, $table="trackerarch", $printstuff=FALSE, $sqlonly=FALSE, $jsftrack1=NULL, $jsftrack2=NULL, $jsftrack3=NULL, $distinctfld=NULL) {
      $dbLink = new MYSQLAccess;
      $query = "SELECT * ";
      if ($countonly) $query = "SELECT count(*) as totalcount ";
      
      if($distinctfld!=NULL) $query = str_replace("*"," DISTINCT(".$distinctfld.") ",$query);

      $query .= " FROM ".$table." p ";

      $where = NULL;

      if ($searchstr!=NULL) {
         $searchstr = str_replace(";"," ",str_replace(","," ",strtolower($searchstr)));
         $searchArr = separateStringBy($searchstr," ",NULL,TRUE);
         for($i=0;$i<count($searchArr);$i++) {
            if ($where==NULL) $where = " WHERE (";
            else $where .= " AND (";
            $where .= " LOWER(p.referer) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.ipaddr) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.sessionid) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.agent) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.view) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.action) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.jsftrack1) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.jsftrack2) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.jsftrack3) LIKE '%".$searchArr[$i]."%' OR ";
            $where .= " LOWER(p.created) LIKE '%".$searchArr[$i]."%') ";
         }
      }

      if ($start!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "p.created>='".$start."'";
      }

      if ($end!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "p.created<='".$end."'";
      }

      if ($view!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "(";
         $wherearr = separateStringBy($view,",");
         for ($i=0;$i<count($wherearr);$i++) {
            if ($i>0) $where .= " OR ";
            $where .= "LOWER(p.view) like '%".strtolower($wherearr[$i])."%'";
         }
         $where .= ") ";
      }

      if ($action!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "LOWER(p.action) like '%".strtolower($action)."%'";
      }

      if ($jsftrack1!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "LOWER(p.jsftrack1) like '%".strtolower($jsftrack1)."%'";
      }

      if ($jsftrack2!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "LOWER(p.jsftrack2) like '%".strtolower($jsftrack2)."%'";
      }

      if ($jsftrack3!=NULL) {
         if ($where==NULL) $where = " WHERE ";
         else $where .= " AND ";
         $where .= "LOWER(p.jsftrack3) like '%".strtolower($jsftrack3)."%'";
      }

      $query .= $where;
      
      if($sqlonly){
         if ($orderby!=NULL) $query .= " ORDER BY ".$orderby." ";
         return $query;
      } else {
         if (!$countonly) {
            if ($orderby!=NULL) $query .= " ORDER BY ".$orderby." ";
            if ($page==NULL || $page<2) $page=1;
            if ($limit==NULL || $limit<1) $limit=25;
            $start = ($page-1)*$limit;
            $query .= " LIMIT ".$start.",".$limit.";";
         }
         $results = $dbLink->queryGetResults($query);
         if ($printstuff) print "<br>Query tracking: ".$query."\n";
         return $results;
      }
   }

   function deleteTracking($trkid,$table=NULL){
      $dbLink = new MYSQLAccess;
      if (0!=strcmp($table,"tracker")) $table = "trackerarch";
      $query = "DELETE FROM ".$table." WHERE trkid=".$trkid.";";
      $dbLink->delete($query);
   }


   function cron_CacheRows($timeint=NULL,$printstuff=FALSE,$runningTime=2){
      $starttime = time();
      if ($runningTime==NULL || $runningTime<=0) $runningTime=2;
      $endtime = time() + (60 * $runningTime);
      if ($timeint==NULL || $timeint<1364097600) $timeint = time() - (24 * 60 * 60);
      $j = 0;
      $finished=FALSE;
      while($j<24 && !$finished && time()<$endtime) {
         $hourstr = date("Y-m-d",$timeint)." ".str_pad($j,2,"0",STR_PAD_LEFT).":01:01";
         if ($printstuff) print "<br>".$j.". Caching Hour: ".$hourstr;
         $hour = strtotime($hourstr);
         $finished = $this->cacheStats($hour,$printstuff);
         $j++;
      }
   }

   function cron_MoveOldRows($printstuff=FALSE){
      $timeint = time() - (24 * 60 * 60);
      $endquery = date("Y-m-d",$timeint);
      $endquery .= " 23:59:59";

      $startTime = time();
      //$maxRunningTime = 3 * 60;
      $maxRunningTime = 60;
      $endTime = $startTime+$maxRunningTime;

      $finished = FALSE;
      $totalmoved = 0;
      while (!$finished && time()<$endTime) {
         //if ($printstuff) print "<br> before query: ".date("m/d/Y H:i:s")."\n";
         //$rows = $this->getTracking(NULL,NULL,$endquery,NULL,NULL,"p.trkid",500,1,FALSE,"tracker",TRUE);
         $rows = $this->getTracking(NULL,NULL,$endquery,NULL,NULL,"p.created",500,1,FALSE,"tracker",TRUE);
         //if ($printstuff) print "<br> before move: ".date("m/d/Y H:i:s")." endquery: ".$endquery."\n<br>rows: ".count($rows)."\n";
         if (count($rows)>0) $this->moveTrackingRowArray($rows,$printstuff);
         else $finished = TRUE;
         
         $totalmoved = $totalmoved + count($rows);
         //if ($printstuff) print "<br> after move: ".date("m/d/Y H:i:s")."\n";
      }
      //$invalidateCache = "DELETE FROM dbcache where sqlstr like '%trackerarch%' and created like '".date("Y-m-d")."%';";
      //$dbLink = new MYSQLAccess;
      //$dbLink->delete($invalidateCache);
      return $totalmoved;
   }

   function cron_CleanTracking($numdays=730){
      $timeint = time() - ($numdays * 24 * 60 * 60);
      $endquery = date("Y-m-d",$timeint)." 00:00:00";
      $dbLink = new MYSQLAccess;

      $startTime = time();
      $maxRunningTime = 60;
      $endTime = $startTime+$maxRunningTime;

      $query = "delete from trackerarch where created<'".$endquery."';";
      $dbLink->delete($query);

      if(time()<$endTime) {
         $query = "delete from tracker where created<'".$endquery."';";
         $dbLink->delete($query);
         if(time()<$endTime) {
            $query = "delete from trackerstats where trkhour<'".$endquery."';";
            $dbLink->delete($query);
         }
      }

   }
   
   
   function resolveIPAddresses($printstuff=FALSE){
      $dbLink = new MYSQLAccess();
      $ipaddr_region = array();
      $maxdate = date("Y-m-d H:i:s",time()-(30*24*60*60));
      $query = "SELECT * FROM trackerarch WHERE created>='".$maxdate."' AND (region is NULL OR region='') ORDER BY created DESC LIMIT 0,200;";
      $results = $dbLink->queryGetResults($query);
      $counter = 0;
      if ($results!=NULL && count($results)>0) {
         if ($printstuff) print "\n<br>count of rows: ".count($results);
         for ($i=0; $i<count($results); $i++) {
            $line = $results[$i];
            if ($printstuff) {
               print "\n<br>Line: ";
               print_r($line);
               print "\n<br>";
            }
            
            if($line['latitude']==NULL && $line['ipaddr']!=NULL && !isset($ipaddr_region[$line['ipaddr']])) {
               $jsonurl = "http://freegeoip.net/json/".$line['ipaddr'];
               $data = requestJSON($jsonurl,$printstuff);
               if ($printstuff) {
                  print "\n<br>json: ".$jsonurl;
                  print "\n<br>json response: ";
                  print_r($data);
                  print "\n<br>";
               }
               if($data!=NULL && $data['latitude']!=NULL) {
                  $line['country'] = $data['country_code'];
                  $line['region'] = $data['region_code'];
                  $line['city'] = $data['zip_code'];
                  $line['lat'] = $data['latitude'];
                  $line['lng'] = $data['longitude'];
                  $line['postal'] = $data['zip_code'];
                  $query = "UPDATE trackerarch SET country='".$line['country']."', region='".$line['region']."', city='".$line['city']."', lat='".$line['lat']."', lng='".$line['lng']."', postal='".$line['postal']."' WHERE ipaddr='".$line['ipaddr']."'";
                  $dbLink->update($query);
                  $ipaddr_region[$line['ipaddr']] = $line;
                  $counter++;
               }
            }
         }
      }
      return $counter;
   }
}



Class TrackerCleanup {
   function addJob(){
      $sched = new Scheduler();
      $sched->addSchedCustom("TrackerCleanup","Ongoing tracking cleanup - leave this running");
   }

   function doWork($job){
      //error_reporting(E_ALL);
      $tr = new TrackerArchive();
      $day = date("j");
      if($day==1 || $day==11 || $day==16) {
         $totalmoved = $tr->cron_MoveOldRows($job['printstuff']);
         if($totalmoved>0) $job['content'] = date("Y-m-d H:i").": moved ".$totalmoved." rows; ".$job['content'];
      } else if($day==2 || $day==17) {
         $tr->cron_CleanTracking(730);
         //$job['content'] = date("Y-m-d H:i").": deleted old data; ".$job['content'];
      } else {
         $totalupdates = $tr->resolveIPAddresses($job['printstuff']);
         if($totalupdates>0) $job['content'] = date("Y-m-d H:i").": geolocated ".$totalupdates." rows; ".$job['content'];
      }
      
      $job['status'] = "NEW";
      if(strlen($job['content'])>512) $job['content'] = substr($job['content'],0,512);
      $job['finished'] = TRUE;
      
      return $job;
   }
}

?>
