<?php
class Scheduler {

   function scheduleJob($starttime,$byuserid,$typestr,$tinterval1=0,$tinterval2=0,$ninterval1=0,$ninterval2=0,$field1=NULL,$field2=NULL,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL){
      if ($byuserid==NULL || $typestr==NULL) return FALSE;

      $params = "starttime, ";
      if ($starttime==NULL) $vals = "NOW(), ";
      else $vals = "'".$starttime."',";

      $params .= "byuserid, ";
      $vals .= $byuserid.",";
      $params .= "typestr, ";
      $vals .= "'".$typestr."',";
      $params .= "status, ";
      $vals .= "'NEW',";
      $params .= "tinterval1, ";
      $vals .= $tinterval1.",";
      $params .= "tinterval2, ";
      $vals .= $tinterval2.",";
      $params .= "ninterval1, ";
      $vals .= $ninterval1.",";
      $params .= "ninterval2, ";
      $vals .= $ninterval2.",";
      if ($field1!=NULL) {
         $params .= "field1, ";
         $vals .= $field1.",";
      }
      if ($field2!=NULL) {
         $params .= "field2, ";
         $vals .= $field2.",";
      }
      $params .= "field3, ";
      $vals .= "'".convertString(trim($field3))."',";
      $params .= "field4, ";
      $vals .= "'".convertString(trim($field4))."',";
      $params .= "field5, ";
      $vals .= "'".convertString(trim($field5))."',";
      $params .= "field6";
      $vals .= "'".convertString(trim($field6))."'";
      
      $field7 = trim($field7);
      if($field7!=NULL) {
         $params .= ", field7";
         $vals .= ", '".$field7."'";
      }

      $dbLink = new MYSQLaccess;
      $query = "INSERT INTO schedjobs (".$params.") VALUES (".$vals.");";
      $schedid = $dbLink->insertGetValue($query);
      return $schedid;
   }

   function getJobByType($typestr){
      $dbLink = new MYSQLaccess;
      $query = "SELECT * FROM schedjobs WHERE typestr='".$typestr."';";
      $results = $dbLink->queryGetResults($query);
      return $results;
   }

   function sendGroupMessage($segmentid,$type,$fromemails,$shortname,$priority,$content=NULL,$subject=NULL,$contenttype=NULL) {
      //print "<br>function sendGroupMessage($segmentid,$type,$fromemails,$shortname,$priority,$content=NULL,$subject=NULL,$contenttype=NULL) {";
      $ua = new UserAcct();
      $numOfEmails = count($fromemails);
      $results = $ua->getUsersForSegment(NULL,$segmentid);
      $users = $results['users'];
      for ($i=0; $i<count($users); $i++) {
         //print "<br>Adding email #: ".$i." email address: ".$users[$i]['email'];
         $from = $fromemails[($i % $numOfEmails)];
         $this->sendMessage($users[$i]['userid'],$from,$shortname,$content,$subject,$contenttype,$priority,$type);
      }
      return $users;
   }

   function sendMessage($userid,$from,$shortname=NULL,$content=NULL,$subject=NULL,$contenttype=NULL,$priority=10,$type=NULL,$names=NULL,$values=NULL) {
      $sendEmail=FALSE;
      $sendShortEmail=FALSE;
      $sendUMsg = FALSE;
      $shortMsg = "";
      if ($type==NULL || 0==strcmp($type,"email") || 0==strcmp($type,"both")) $sendEmail = TRUE;
      if (0==strcmp($type,"shortusmg") || 0==strcmp($type,"usmg") || 0==strcmp($type,"both")) $sendUMsg = TRUE;
      if (0==strcmp($type,"shortusmg")) {
         $sendShortEmail=TRUE;
         $shortMsg = "%%%USER_FNAME%%%,\nYou have just received a new message from ".getDefaultTitle().".\n";
         $shortMsg .= "You can read all your ".getDefaultTitle()." messages by logging in and clicking on the 'Messages' tab:\n".$GLOBALS['baseURL'].".\n\nThanks!";
      }

      if ($sendUMsg) {
         $usrmsg = new UserMessages();
         $namesArr = explode(",",$names);
         $valuesArr = explode(",",$values);
         for ($i=0; $i<count($namesArr); $i++) $_SESSION['params'][$namesArr[$i]] = $valuesArr[$i];
         //print "\n<!--\n";
         //print "\nParams:\n";
         //print_r($_SESSION['params']);
         //print "\n-->\n";
         if ($shortname!=NULL) $usrmsg->newMessageByShortname($userid,$shortname,$from);
         else $usrmsg->newMessage($userid,$subject,$from,$content);
      }
      if ($sendEmail) {
         $this->addSchedEmail(NULL,$shortname,$content, $subject, $contenttype, $userid,$from,$priority,TRUE,$names,$values);
      }
      if ($sendShortEmail) {
         $this->addSchedEmail(NULL,NULL,$shortMsg,"New ".getDefaultTitle()." Message Waiting", 5, $userid,$from,$priority);
      }      
   }

   //--------------------------------------------------------------------
   // Use this method to queue up email requests
   // If the priority is 1-5, try to send now.  If not, let the scheduler
   // pick it up.
   //--------------------------------------------------------------------
   function addSchedEmail($cmsid,$shortname,$content, $subject, $contenttype, $userid,$fromemail,$priority=5,$allowRepeats=FALSE,$names=NULL,$values=NULL,$toemail=NULL,$trackthis=TRUE,$status=NULL,$resched=NULL){
$template = new Template();
if($trackthis) $template->trackItem("addschedemail","1",$subject,$toemail." from ".$fromemail,$content);

      if($status==NULL) $status="NEW";
      if (($cmsid==NULL && $shortname==NULL && $content==NULL) || ($userid==NULL && $toemail==NULL) || $fromemail==NULL) return FALSE;
if($trackthis) $template->trackItem("addschedemail","2 ".$allowRepeats,$subject,$toemail." from ".$fromemail,$content);

      if ($userid==NULL || !is_numeric($userid)) $userid = 0;

      if ($cmsid==NULL && $shortname!=NULL) {
         $version = new Version();
         $fileinfo = $version->getFileByShortname($shortname);
         $cmsid = $fileinfo['cmsid'];
      }

      $results = $this->getJobByType("EMAIL");
      $schedid=NULL;
      $tinterval1=60;
      $tinterval2=3600;
      $ninterval1=20;
      $ninterval2=380;
      if ($results==NULL || count($results)<1) {
         $schedid = $this->scheduleJob(NULL,-1,"EMAIL",$tinterval1,$tinterval2,$ninterval1,$ninterval2);
      } else {
         $schedid=$results[0]['schedid'];
         $tinterval1 = $results[0]['tinterval1'];
         $tinterval2 = $results[0]['tinterval2'];
         $ninterval1 = $results[0]['ninterval1'];
         $ninterval2 = $results[0]['ninterval2'];
      }

      $dbLink = new MYSQLaccess;
      $semailid=NULL;
      if ($cmsid!=NULL && $cmsid>0) {
         $emails = NULL;
         if (!$allowRepeats) {
            //$query = "SELECT * FROM schedemail WHERE cmsid=".$cmsid." AND userid=".$userid." AND field6='".$toemail."' AND status='NEW' AND schedid=".$schedid;
            $query = "SELECT * FROM schedemail WHERE cmsid=".$cmsid." AND userid=".$userid." AND field6='".$toemail."' AND status<>'FINISHED' AND schedid=".$schedid;
            $emails = $dbLink->queryGetResults($query);
         }
         if ($allowRepeats || $emails == NULL || count($emails)<1) {
            $query = "INSERT INTO schedemail";
            if ($priority > 5) $query = "INSERT DELAYED INTO schedemail";
            $query .= " (schedid,cmsid,userid,status,field3,timeadded,priority,field4,field5,field6,resched)";
            $query .= " VALUES (".$schedid.", ".$cmsid.", ".$userid.", '".$status."', '".$fromemail."',NOW(),".$priority.",'".$names."','".$values."','".$toemail."','".$resched."');";
            $semailid = $dbLink->insertGetValue($query);
         } else {
            $semailid = $emails[0]['semailid'];
         }
      } else {
         $emails = NULL;
         if (!$allowRepeats && $userid!=NULL && $subject!=NULL) {
            $query = "SELECT * FROM schedemail WHERE userid=".$userid." AND subject='".$subject."' AND timeadded>DATE_SUB(NOW(), INTERVAL 48 HOUR) AND field3='".$fromemail."' AND schedid=".$schedid;
            $emails = $dbLink->queryGetResults($query);
            //print "\n<!-- ***chj*** checking for duplicate emails... ".$query." -->\n";
         } else if (!$allowRepeats && $toemail!=NULL && $subject!=NULL) {
            $query = "SELECT * FROM schedemail WHERE field6='".$toemail."' AND subject='".$subject."' AND timeadded>DATE_SUB(NOW(), INTERVAL 48 HOUR) AND field3='".$fromemail."' AND schedid=".$schedid;
            $emails = $dbLink->queryGetResults($query);
         }
         
         if ($allowRepeats || $emails == NULL || count($emails)<1) {
            if ($contenttype!=6) $contenttype=5;
            $query = "INSERT INTO schedemail";
            if ($priority > 5) $query = "INSERT DELAYED INTO schedemail";
            $query .= " (schedid,cmsid,content,subject,field1,userid,status,field3,timeadded,priority,field4,field5,field6,resched)";
            $query .= " VALUES (".$schedid.", -1, '".convertString(trim($content))."','".convertString(trim($subject))."', ".$contenttype.",".$userid.", '".$status."', '".$fromemail."',NOW(),".$priority.",'".$names."','".$values."','".$toemail."','".$resched."');";
            if($trackthis) $template->trackItem("addschedemail","3",$subject,$toemail." from ".$fromemail,$query);
            $semailid = $dbLink->insertGetValue($query);
         }
      }

      if ($priority < 6 && 0==strcmp($status,"NEW")) $this->checkAndSend($semailid,$schedid,$tinterval1,$tinterval2,$ninterval1,$ninterval2);
      //print "<br>****chj*** emailid: ".$emailid."<br><br>\n\n";
      return $semailid;         
   }

   function copyEmailJob($semailid) {
      $dbLink = new MYSQLaccess();
      $schemails = $this->getScheduledEmails($semailid);
      $semail = $schemails['emails'][0];
      if ($semail['cmsid']==NULL) $semail['cmsid']=-1;
      if ($semail['field1']==NULL) $semail['field1']=0;
      if ($semail['field2']==NULL) $semail['field2']=0;
      $query = "INSERT INTO schedemail (schedid,status,cmsid,userid,timeadded,content,subject,priority,field1,field2,field3,field4,field5,field6,resched) VALUES ( ";
      $query .= $semail['schedid'].",";
      $query .= "'NEW',";
      $query .= $semail['cmsid'].",";
      $query .= $semail['userid'].",";
      $query .= "NOW(),";
      $query .= "'".convertString($semail['content'])."',";
      $query .= "'".convertString($semail['subject'])."',";
      $query .= $semail['priority'].",";
      $query .= $semail['field1'].",";
      $query .= $semail['field2'].",";
      $query .= "'".convertString($semail['field3'])."',";
      $query .= "'".convertString($semail['field4'])."',";
      $query .= "'".convertString($semail['field5'])."',";
      $query .= "'".convertString($semail['field6'])."',;";
      $query .= "'".convertString($semail['resched'])."');";
      $semailid = $dbLink->insertGetValue($query);
      return $semailid;      
   }

   function updateEmailJob($semailid,$status=NULL,$priority=NULL,$resched=NULL){
      if ($semailid==NULL || ($status==NULL && $priority==NULL && $resched==NULL)) return FALSE;

      $query = "UPDATE schedemail SET ";

      $field_count=0;
      
      if (0==strcmp($status,"FINISHED")) {
         if ($field_count>0) $query .= ", ";
         $query .= "timesent=NOW()";
         $field_count++;
      }
      
      if ($status!=NULL) {
         if ($field_count>0) $query .= ", ";
         $query .= "status='".trim($status)."'";
         $field_count++;
      }
      
      if ($priority!=NULL && is_numeric($priority)) {
         if ($field_count>0) $query .= ", ";
         $query .= "priority=".$priority;
         $field_count++;
      }
      
      if($resched!=NULL) {
         if ($field_count>0) $query .= ", ";
         $query .= "resched='".$resched."'";
         $field_count++;
      }
      
      $query .= " WHERE semailid=".$semailid.";";
      $dbLink = new MYSQLaccess;
      $dbLink->update($query);
   }

   function removeSchedEmail($semailid){
      if ($semailid==NULL) return FALSE;
      $query = "DELETE FROM schedemail WHERE semailid=".$semailid.";";
      $dbLink = new MYSQLaccess;
      $dbLink->delete($query);
   }

   function getScheduledEmails($semailid=NULL,$status=NULL,$priority=NULL,$email=NULL,$fname=NULL,$lname=NULL,$orderby=NULL,$page=NULL,$limit=NULL,$jobtype="EMAIL",$classname=NULL,$searchtxt=NULL){

      $results = $this->getJobByType($jobtype);
      //print "\n<!-- ";
      //print_r($results);
      //print "\n -->\n";
      if ($results==NULL || count($results)<1) return NULL;
      $whereClause = "";
      
      if ($orderby == NULL) $orderby = "e.priority, e.timeadded";
      if ($page==NULL || $page==0 || !is_numeric($page)) $page=1;
      if ($limit==NULL) $limit=25;

      if ($semailid!=NULL) $whereClause .= " AND e.semailid=".$semailid;
      if ($status!=NULL) $whereClause .= " AND e.status='".$status."'";
      if ($priority!=NULL) $whereClause .= " AND e.priority=".$priority;
      if ($email!=NULL) $whereClause .= " AND (LOWER(u.email) like '%".strtolower($email)."%' OR LOWER(e.field6) like '%".strtolower($email)."%')";
      if ($fname!=NULL) $whereClause .= " AND LOWER(u.fname) like '%".strtolower($fname)."%'";
      if ($lname!=NULL) $whereClause .= " AND LOWER(u.lname) like '%".strtolower($lname)."%'";
      if ($classname!=NULL) $whereClause .= " AND e.classname='".$classname."'";
      if ($searchtxt!=NULL) {
         $whereClause .= " AND (";
         $whereClause .= " LOWER(e.classname) LIKE '%".strtolower(trim($searchtxt))."%'";
         $whereClause .= " OR LOWER(e.subject) LIKE '%".strtolower(trim($searchtxt))."%'";
         $whereClause .= " OR LOWER(e.content) LIKE '%".strtolower(trim($searchtxt))."%'";
         $whereClause .= ") ";
      }

      $dbLink = new MYSQLaccess;
      $query = " FROM schedemail e LEFT OUTER JOIN useracct u on e.userid=u.userid WHERE e.schedid=".$results[0]['schedid']." ".$whereClause;
      //print "\n<!-- ".$query." -->\n";
      $countquery = "SELECT count(*) ".$query;
      $countjobs = $dbLink->queryGetResults($countquery);
      $totaljobs = $countjobs[0]["count(*)"];
      $totalPages = ceil($totaljobs/$limit);

      $select = "SELECT ";
      $select .= " e.semailid, ";
      $select .= " e.schedid, ";
      $select .= " e.cmsid, ";
      $select .= " e.userid, ";
      $select .= " e.status, ";
      $select .= " e.timesent, ";
      $select .= " e.timeadded, ";
      $select .= " e.starton, ";
      $select .= " e.content, ";
      $select .= " e.subject, ";
      $select .= " e.classname, ";
      $select .= " e.phpobj, ";
      $select .= " e.priority, ";
      $select .= " e.field1, ";
      $select .= " e.field2, ";
      $select .= " e.field3, ";
      $select .= " e.field4, ";
      $select .= " e.field5, ";
      $select .= " e.field6, ";
      $select .= " e.field7, ";
      $select .= " e.resched, ";
      $select .= " u.fname, ";
      $select .= " u.lname, ";
      $select .= " u.email ";

      $query = $select.$query." ORDER BY ".$orderby;
      if ($limit!=NULL && $page!=NULL && 0!=strcmp($limit,"All")) {
         $pageStart = $limit*($page - 1);
         $query .= " LIMIT " . $pageStart . "," . $limit;
      }
      //print "<br>".$query."<BR>";
      //print "\n<!-- final query: ".$query." -->\n";
      $emails = $dbLink->queryGetResults($query);
      $results['emails'] = $emails;
      $results['totalPages'] = $totalPages;
      $results['totalJobs'] = $totaljobs;
      return $results;
   }

   //--------------------------------------------------------------------
   // process a single email in the queue.. if the statis is "FINISHED"
   // this will do nothing since it's already sent.
   //--------------------------------------------------------------------
   function processSchedEmail($semailid,$runasynch=TRUE){
      //error_reporting(E_ALL);
      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." started.\n";
      //print "<br>".date("Y-m-d H:i:s")." processchedemail: ".$emailid."<BR>";
      if ($semailid==NULL) return FALSE;
      $results = $this->getScheduledEmails($semailid,"NEW");
      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." queried DB for semail.\n";
    //print "<br>".date("Y-m-d H:i:s")." ****chj*** results: <BR>";
    //print_r($results);
    //print "<BR>";
      if ($results['totalJobs']==1) {
         if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." found and sending successfully!\n";
         $version = new Version();
         $ua = new UserAcct();
         $value = $results['emails'][0];
         $this->updateEmailJob($value['semailid'],"RUNNING");
         if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." updated status to \"RUNNING\".\n";
         $subject = "";
         $contents = "";
         $contettype = 5;
         // Get the email body and subject by 1 of 2 different ways : content system, or explicit message saved.
         if ($value['cmsid'] != NULL && $value['cmsid']>0) {
            $cmsinfo = $version->getFileById($value['cmsid']);
            $fileinfo = $version->getAsciiFileContents($cmsinfo['filename']);
            $contents = $fileinfo['contents'];
            $contenttype = $fileinfo['contenttype'];
            $subject = $fileinfo['title'];
            if ($subject==NULL) $subject = $fileinfo['filetitle'];
            if ($subject==NULL) $subject = $fileinfo['metadescr'];
         } else {
            $contents = convertBack($value['content']);
            $subject = convertBack($value['subject']);
            $contenttype = $value['field1'];
         }
   
         // Send email, update status in DB, and remove it from the array for next iteration
         $namesArr = explode(",",$value['field4']);
         $valuesArr = explode(",",$value['field5']);
         for ($i=0; $i<count($namesArr); $i++) $_SESSION['params'][$namesArr[$i]] = $valuesArr[$i];
         //print "<!-- ".strlen($contents)." email: ".$value['field3']." content type: ".$contenttype." -->";
         //print "<br>".date("Y-m-d H:i:s")." sending email<BR>";
         if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." sending...\n";
    //print "<br><br>contents: ".$contents." ****chj***<br><br>\n\n";
         if ($contents!=NULL) $ua->sendEmailTo($value['userid'],$subject,$contents,$value['field3'],$contenttype,$value['field6']);
         if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." sent\n";
         //print "<br>".date("Y-m-d H:i:s")." finished sending email<BR>";
         $this->updateEmailJob($value['semailid'],"FINISHED");
         if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." processSchedEmail: ".$semailid." updated status to \"FINISHED\".<br>\n";
         return TRUE;
      } else return FALSE;
   }

   //--------------------------------------------------------------------
   // Simply check to make sure we haven't exceeded our email capacity
   // and send the email now if we're within the window, otherwise do
   // nothing and return FALSE
   //--------------------------------------------------------------------
   function checkAndSend($semailid,$schedid,$tinterval1=240,$tinterval2=14400,$ninterval1=2000,$ninterval2=4500,$runasynch=TRUE){
   //function checkAndSend($semailid,$schedid,$tinterval1=60,$tinterval2=3600,$ninterval1=20,$ninterval2=380){
      if (isset($GLOBALS['sendintervalperemail']) && $GLOBALS['sendintervalperemail']==1) {
         return $this->checkAndSendPerEmail($semailid,$schedid,$tinterval1,$tinterval2,$ninterval1,$ninterval2,$runasynch);
      } else {
         //return $this->checkAndSendGlobal($semailid,$schedid,$tinterval1,$tinterval2,$ninterval1,$ninterval2,$runasynch);
         return $this->checkAndSendGlobalQuick($semailid,$schedid,$tinterval2,$ninterval2,$runasynch);
      }
   }

   function checkAndSendPerEmail($semailid,$schedid,$tinterval1=60,$tinterval2=3600,$ninterval1=20,$ninterval2=380,$runasynch=TRUE){
      $dbLink = new MYSQLaccess;
      $query = "SELECT count(s1.semailid) FROM schedemail s1, schedemail s2 WHERE ";
      $query .= "s2.field3=s1.field3 ";
      $query .= "AND s2.semailid=".$semailid." ";
      $query .= "AND s1.status='FINISHED' ";
      $query .= "AND s1.schedid=".$schedid." ";
      $query .= "AND s2.schedid=".$schedid." ";
      $query .= "AND DATE_ADD(s1.timesent,INTERVAL ".$tinterval1." SECOND)>NOW() ";
      $query .= ";";
      $emails = $dbLink->queryGetResults($query);
      //print "<br>query:<br>".$query;
      if ($emails[0]['count(s1.semailid)']>=$ninterval1) return FALSE;

      $query = "SELECT count(s1.semailid) FROM schedemail s1, schedemail s2 WHERE ";
      $query .= "s2.field3=s1.field3 ";
      $query .= "AND s2.semailid=".$semailid." ";
      $query .= "AND s1.status='FINISHED' ";
      $query .= "AND s1.schedid=".$schedid." ";
      $query .= "AND s2.schedid=".$schedid." ";
      $query .= "AND DATE_ADD(s1.timesent,INTERVAL ".$tinterval2." SECOND)>NOW() ";
      $query .= ";";
      $emails = $dbLink->queryGetResults($query);
      //print "<br>query:<br>".$query;
      if ($emails[0]['count(s1.semailid)']>=$ninterval2) return FALSE;

      return $this->processSchedEmail($semailid);
   }

   function checkAndSendGlobal($semailid,$schedid,$tinterval1=240,$tinterval2=86400,$ninterval1=2000,$ninterval2=30000,$runasynch=TRUE){
   //function checkAndSendGlobal($semailid,$schedid,$tinterval1=60,$tinterval2=3600,$ninterval1=20,$ninterval2=380){
      $dbLink = new MYSQLaccess;
      $query = "SELECT count(semailid) FROM schedemail WHERE ";
      $query .= "status='FINISHED' ";
      $query .= "AND schedid=".$schedid." ";
      $query .= "AND DATE_ADD(timesent,INTERVAL ".$tinterval1." SECOND)>NOW() ";
      $query .= ";";
      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." Checking interval1 for ".$semailid.".  Time: ".$tinterval1." Number: ".$ninterval1."<br>\nQuery: ".$query."<br>\n";
      $emails = $dbLink->queryGetResults($query);
      //print "<br>query:<br>".$query;
      if ($emails[0]['count(semailid)']>=$ninterval1) return FALSE;

      $query = "SELECT count(semailid) FROM schedemail WHERE ";
      $query .= "status='FINISHED' ";
      $query .= "AND schedid=".$schedid." ";
      $query .= "AND DATE_ADD(timesent,INTERVAL ".$tinterval2." SECOND)>NOW() ";
      $query .= ";";
      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." Checking interval2 for ".$semailid.".  Time: ".$tinterval2." Number: ".$ninterval2."<br>\nQuery: ".$query."<br>\n";
      $emails = $dbLink->queryGetResults($query);
      //print "<br>query:<br>".$query;
      if ($emails[0]['count(semailid)']>=$ninterval2) return FALSE;

      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." Both intervals passed: ".$semailid."<br>\n";

      return $this->processSchedEmail($semailid,$runasynch);
   }


   function checkAndSendGlobalQuick($semailid,$schedid,$tinterval=14400,$ninterval=4500,$runasynch=TRUE){
      $dbLink = new MYSQLaccess;

      $tnow = time();
      $tnow = $tnow - $tinterval;
      
      $query = "SELECT count(semailid) FROM schedemail WHERE ";
      $query .= "status='FINISHED' ";
      $query .= "AND schedid=".$schedid." ";
      $query .= "AND timesent>='".date("Y-m-d H:i:s",$tnow)."' ";
      $query .= ";";
      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." Checking interval for ".$semailid.".  Time: ".$tinterval." Number: ".$ninterval."<br>\nQuery: ".$query."<br>\n";
      $emails = $dbLink->queryGetResults($query);
      //print "<br>query:<br>".$query;
      if ($emails[0]['count(semailid)']>=$ninterval) return FALSE;

      if(!$runasynch) print "<br>".date("Y-m-d H:i:s")." Interval passed: ".$semailid."<br>\n";

      return $this->processSchedEmail($semailid,$runasynch);
   }


   //---------------------------------------------------------------------
   // Schedule this every 10/15 minutes.
   // This picks up lower priority email and those who have not yet been sent
   // Send any emails that are queued in the scheduler in a shorter time slice
   //---------------------------------------------------------------------
   function checkShortEmailJobs($runasynch=TRUE){
      $startTime = time();
      //$maxRunningTime = 5 * 60; //Max time for this script to run is 5 minutes
      $maxRunningTime = 7 * 60; //Max time for this script to run is 5 minutes
      if (!$runasynch) $maxRunningTime = 30;
      $endTime = $startTime+$maxRunningTime;
      if (!$runasynch) print "<br>Values[ startTime: ".date(DATE_ISO8601,$startTime)." endTime: ".date(DATE_ISO8601,$endTime)."] now: ".date(DATE_ISO8601)."<br>";
      // Get all NEW emails (ready to be sent) - get highest priority ones first, among those get the oldest ones first.
      $query = "SELECT e.semailid, s.schedid, s.tinterval1, s.tinterval2, s.ninterval1, s.ninterval2 FROM schedemail e, schedjobs s, useracct u WHERE e.userid=u.userid AND e.schedid=s.schedid AND s.typestr='EMAIL' AND e.status='NEW' ORDER BY e.priority, e.timeadded;";
      $dbLink = new MYSQLaccess;
      if (!$runasynch) print "<br>query: ".$query." now: ".date(DATE_ISO8601)."<br>";
      $emails = $dbLink->queryGetResults($query);
      if (!$runasynch) print "<br>Emails to send: ".count($emails)." now: ".date(DATE_ISO8601)."<br>";
      if ($emails==NULL || count($emails)<1) return FALSE;
      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;
         foreach($emails as $key => $value){
            if ($this->checkAndSend($value['semailid'],$value['schedid'],$value['tinterval1'],$value['tinterval2'],$value['ninterval1'],$value['ninterval2'],$runasynch)){
               if (!$runasynch) print "<br>An email was sent successfully. now: ".date(DATE_ISO8601)."<br>";
               unset($emails[$key]);
            } else {
               if (!$runasynch) print "<br>An email was queued, could not be sent. now: ".date(DATE_ISO8601)."<br>";
               $finished=FALSE;
            }
   
            //Check if the max time is exceeded.
            if (time()>$endTime || count($emails)<1) {
               if (!$runasynch) print "<br>Time is exceeded. now: ".date(DATE_ISO8601)."<br>";
               $finished=TRUE;
               break;
            }
         }
         if (!$finished && $runasynch) sleep(60);
      }
   }

   //---------------------------------------------------------------------
   // Schedule this every half hour.
   // This picks up priority email and those who have not yet been sent
   // Send any emails that are queued in the scheduler in a 30 minute time slice
   //---------------------------------------------------------------------
   function checkEmailJobs(){
      $startTime = time();
      $maxRunningTime = 20 * 60; //Max time for this script to run is 20 minutes
      $endTime = $startTime+$maxRunningTime;

      // Get all NEW emails (ready to be sent) - get highest priority ones first, among those get the oldest ones first.
      $query = "SELECT e.semailid, s.schedid, s.tinterval1, s.tinterval2, s.ninterval1, s.ninterval2 FROM schedemail e, schedjobs s WHERE e.schedid=s.schedid AND s.typestr='EMAIL' AND e.status='NEW' ORDER BY e.priority, e.timeadded;";
      $dbLink = new MYSQLaccess;
      $emails = $dbLink->queryGetResults($query);
      if ($emails==NULL || count($emails)<1) return FALSE;
      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;
         foreach($emails as $key => $value){
            if ($this->checkAndSend($value['semailid'],$value['schedid'],$value['tinterval1'],$value['tinterval2'],$value['ninterval1'],$value['ninterval2'])){
               unset($emails[$key]);
            } else {
               $finished=FALSE;
            }
   
            //Check if the max time is exceeded.
            if (time()>$endTime) {
               $finished=TRUE;
               break;
            }
         }
         if (!$finished) sleep(60);
      }
   }

   //---------------------------------------------------------------------
   // Send any emails that are queued in the scheduler
   // Legacy - this used to wait to send all the emails at once.  (deprecated)
   // Use this for batch processing.
   //---------------------------------------------------------------------
   function former_checkEmailJobs(){
      $startTime = time();
      $maxRunningTime = 40 * 60; //Max time for this script to run is 40 minutes
      $endTime = $startTime+$maxRunningTime;

      $results = $this->getJobByType("EMAIL");
      if ($results==NULL || count($results)<1) return FALSE;
      
      // Get all NEW emails (ready to be sent) - get highest priority ones first, among those get the oldest ones first.
      $query = "SELECT * FROM schedemail WHERE status='NEW' AND schedid=".$results[0]['schedid']." ORDER BY priority, timeadded;";
      $dbLink = new MYSQLaccess;
      $emails = $dbLink->queryGetResults($query);
      if ($emails==NULL || count($emails)<1) return FALSE;

      // Ok, we're ready to go, there are scheduled emails ready to be sent out.
      $emailAddrs = array();
      $emailAddrs2 = array();
      $intervalEnd = time() + $results[0]['tinterval1'];
      $intervalEnd2 = time() + $results[0]['tinterval2'];

      $finished = FALSE;
      while (!$finished) {
         //if the small interval is exceeded, we can refresh email addresses and start sending again.
         if (time()>$intervalEnd) {
            $emailAddrs = NULL;
            $emailAddrs = array();
            $intervalEnd = time() + $results[0]['tinterval1'];
         }

         //iterate through the current list of scheduled emails
         $finished = TRUE;
         $noemailssent = TRUE;
         foreach($emails as $key => $value){
            if (
                  (
                     $emailAddrs[$value['field3']]==NULL || 
                     $emailAddrs[$value['field3']] < $results[0]['ninterval1']
                  ) && (
                     $emailAddrs2[$value['field3']]==NULL || 
                     $emailAddrs2[$value['field3']] < $results[0]['ninterval2']
                  )
               )
            {
               //update the current running counters for email threshold in 2 given intervals of time
               if ($emailAddrs[$value['field3']]==NULL) $emailAddrs[$value['field3']]=1;
               else $emailAddrs[$value['field3']]++;
               if ($emailAddrs2[$value['field3']]==NULL) $emailAddrs2[$value['field3']]=1;
               else $emailAddrs2[$value['field3']]++;

               $this->processSchedEmail($value['semailid']);
               unset($emails[$key]);
               $noemailssent=FALSE;
            } else {
               //We've exceeded the number of emails we can send from a single email address, try the next one
               $finished = FALSE;
            }

            //Check if either we maxed out our running time, or the second interval is exceeded.
            if (time()>$endTime || time()>$intervalEnd2) {
               break(2);
            }

         } //end foreach loop

         // At least 1 email did not go through, sleep the remainder of this small interval and try again
         // Or, if no eamails were sent - and the small interval is not up, we must've exceeded the large email interval
         if (!$finished && time()<$intervalEnd) {
            sleep($intervalEnd-time());
         } else if (!$finished && $noemailssent) {
            $finished=TRUE;
         }

      } //end while loop
      
   } //end function

   function purgeOldJobs() {
      $query = "DELETE FROM schedemail WHERE DATE_ADD(timesent,INTERVAL 1 MONTH)<NOW();";
      $dbLink = new MYSQLaccess;
      $dbLink->delete($query);
   }







   //--------------------------------------------------------------------
   // Use this method to queue up CSV download requests
   //--------------------------------------------------------------------
   function addSchedCSV($sql,$wd_id=NULL,$subject=NULL,$starton=NULL,$priority=10,$rowsatatime=1000,$pathname=NULL,$userid=NULL,$userprops=NULL){
      if ($sql==NULL) return FALSE;
      if ($pathname==NULL) $pathname = "jsfadmin/usercsv/schedcsv_".date("Ymd_His").".csv";
      if ($wd_id==NULL) $wd_id=0;
      if ($userid==NULL) $userid=isLoggedOn();
      if ($userid==NULL) $userid=-1;
      if ($starton==NULL) $starton = date("Y-m-d H:i:s");

      //Make sure the pathname is unique!  If not, use "_n" in the pathname where n is the next integer available
      $counter = 2;
      while(file_exists($GLOBALS['baseDir'].$pathname)){
         $pathname = substr($pathname,0,strlen($pathname)-4)."_".$counter.".csv";
         $counter++;
      }

      $results = $this->getJobByType("CSV");
      $schedid=NULL;
      if ($results==NULL || count($results)<1) {
         $schedid = $this->scheduleJob(NULL,-1,"CSV");
      } else {
         $schedid=$results[0]['schedid'];
      }

      $dbLink = new MYSQLaccess;
      $query = "INSERT DELAYED INTO schedemail";
      $query .= " (schedid,userid,status,timeadded,content,cmsid,subject,priority,starton,field1,field2,field3,field5)";
      $query .= " VALUES (".$schedid.", ".$userid.", 'NEW',NOW(),'".convertString($sql)."',".$wd_id.",'".convertString($subject)."',".$priority.",'".$starton."',0,".$rowsatatime.",'".convertString($userprops)."','".convertString($pathname)."');";

      //print "\n<!--\n";
      //print $query."\n";
      //print "-->\n";

      $dbLink->insert($query);

   }

   //---------------------------------------------------------------------
   // Schedule this every 10/15 minutes.
   // This picks up priority csv jobs and processes priority 1 first, then 2, etc
   // schedule this method with Cron to run CSV jobs
   //---------------------------------------------------------------------
   function checkCSVJobs(){
      $wdObj = new WebsiteData();
      $startTime = time();
      $maxRunningTime = 1 * 60; //Max time for this script to run is 1 minutes
      $endTime = $startTime+$maxRunningTime;

      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;

         // Get a NEW CSV to process
         $query = "SELECT e.* FROM schedemail e, schedjobs s WHERE e.schedid=s.schedid AND s.typestr='CSV' AND e.status='NEW' AND (e.starton IS NULL OR e.starton<NOW()) ORDER BY e.priority, e.timeadded LIMIT 0,1;";
         //print "query1: <br>\n".$query;
         $dbLink = new MYSQLaccess;
         $results = $dbLink->queryGetResults($query);
         //print "<br>\n";
         //print_r($results);
         //print "<br>\n";
         $csv_job = $results[0];
         if ($csv_job!=NULL && $csv_job['semailid']!=NULL) {
            $query = "UPDATE schedemail SET status='RUNNING', field1=".($csv_job['field1']+1)." WHERE semailid=".$csv_job['semailid'];
            $dbLink->update($query);
            //print "query2: <br>\n".$query;
            
            $query = convertBack($csv_job['content']);
            $start_pos = $csv_job['field1']*$csv_job['field2'];
            $query = $query." LIMIT ".$start_pos.", ".$csv_job['field2'];
            //print "query3: <br>\n".$query;
            $results = $dbLink->queryGetResults($query);
            $status = "NEW";
            $addlSetters = "";

            $qs = array();
            if ($csv_job['cmsid']!=NULL && $csv_job['cmsid']>0) {
               $fields = $wdObj->getAllFieldsSystem($csv_job['cmsid']);
               for ($k=0; $k<count($fields); $k++) $qs[$fields[$k]['field_id']] = $fields[$k];
            }

            if ($csv_job['field3']!=NULL) {
               $organization = $wdObj->getWebDataByName(strtolower($csv_job['field3'])." properties");
               $fields = $wdObj->getAllFieldsSystem($organization['cmsid']);
               for ($k=0; $k<count($fields); $k++) $qs[strtolower($csv_job['field3']).$fields[$k]['field_id']] = $fields[$k];
            }

            if ($results!=NULL && count($results)>0) {
               if (count($results)<$csv_job['field2']) {
                  $status = "FINISHED";
                  $addlSetters = ",timesent=NOW()";
               }
               $contents = "";
               $keys = array_keys($results[0]);
      
               for ($j=0; $j<count($results); $j++) {
                  for ($i=0; $i<count($keys); $i++) { 
                     if (isset($qs[$keys[$i]]) && $qs[$keys[$i]]!=NULL && isset($qs[$keys[$i]]['label']) && $qs[$keys[$i]]['label']!=NULL) {
                        $ans = $wdObj->getCSVRow($csv_job['cmsid'],0,$qs[$keys[$i]],$results[$j][$keys[$i]]);
                        $contents .= $ans['content'];
                     } else {
                        $contents .= "\"".csvEncodeDoubleQuotes(convertBack($results[$j][$keys[$i]]))."\",";
                     }
                  }
                  $contents .= "\n";
               }
      
               //If file does not exist, create the header row
               $filename = $GLOBALS['baseDir'].$csv_job['field5'];
               if(!file_exists($filename)){
                  $header = "";
                  for ($i=0; $i<count($keys); $i++) {
                     if (isset($qs[$keys[$i]]) && $qs[$keys[$i]]!=NULL && isset($qs[$keys[$i]]['label']) && $qs[$keys[$i]]['label']!=NULL) {
                      //if ($qs[$keys[$i]]!=NULL && $qs[$keys[$i]]['label']!=NULL) {
                        $ans = $wdObj->getCSVRow($csv_job['cmsid'],0,$qs[$keys[$i]],$results[0][$keys[$i]]);
                        $header .= $ans['header'];                        
                     } else {
                        $header .= "\"".$keys[$i]."\",";
                     }
                  }
                  $contents = $header."\n".$contents;
               }
      
               //Write to the file (append mode adds to the end of an existing file - or creates a new file)
      	      $file = fopen($filename,"a");
               fwrite($file, $contents);
               fclose($file);
            } else {
               $status = "FINISHED";
               $addlSetters = ",timesent=NOW()";
            }
      
            $query = "UPDATE schedemail SET status='".$status."'".$addlSetters." WHERE semailid=".$csv_job['semailid'];
            //print "query4: <br>\n".$query;
            $dbLink->update($query);
   
            if (time()<$endTime) $finished = FALSE;
         }
      }
   }












   //--------------------------------------------------------------------
   // Use this method to queue up user upload requests
   //  pathname should be just the beginning of the full path name (1.csv,2.csv,3.csv, etc. will be added to the pathname)
   //--------------------------------------------------------------------
   function addSchedUsers($pathname,$subject=NULL,$starton=NULL,$priority=10,$rowsatatime=1000,$userid=NULL){
      if ($pathname==NULL) return FALSE;
      if ($userid==NULL) $userid=isLoggedOn();
      if ($userid==NULL) $userid=-1;

      //Make sure the pathname is unique!  If not, use "_n" in the pathname where n is the next integer available
      $counter = 1;
      if(file_exists($GLOBALS['baseDir'].$pathname.$counter.".csv")){
         $results = $this->getJobByType("USERS");
         $schedid=NULL;
         if ($results==NULL || count($results)<1) {
            $schedid = $this->scheduleJob(NULL,-1,"USERS");
         } else {
            $schedid=$results[0]['schedid'];
         }
   
         $dbLink = new MYSQLaccess;
         $query = "INSERT DELAYED INTO schedemail";
         $query .= " (schedid,userid,status,timeadded,content,cmsid,subject,priority,starton,field1)";
         $query .= " VALUES (".$schedid.", ".$userid.", 'NEW',NOW(),'".$pathname."',0,'".convertString($subject)."',".$priority.",NOW(),1);";
   
         //print "\n<!--\n";
         //print $query."\n";
         //print "-->\n";
   
         $dbLink->insert($query);
      }

   }

   //---------------------------------------------------------------------
   // Schedule this every 10/15 minutes.
   // This picks up priority csv jobs and processes priority 1 first, then 2, etc
   // schedule this method with Cron to run CSV jobs
   //---------------------------------------------------------------------
   function checkUsersJobs(){
      ini_set('memory_limit', '512M');
      $startTime = time();
      $maxRunningTime = 2 * 60; //Max time for this script to run is 2 minutes
      $endTime = $startTime+$maxRunningTime;

      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;
         // Get a NEW CSV to process
         $query = "SELECT e.* FROM schedemail e, schedjobs s WHERE e.schedid=s.schedid AND s.typestr='USERS' AND e.status='NEW' AND (e.starton IS NULL OR e.starton<NOW()) ORDER BY e.priority, e.timeadded LIMIT 0,1;";
         //print "query1: <br>\n".$query;

         $dbLink = new MYSQLaccess;
         $results = $dbLink->queryGetResults($query);
         //print "<br>\n";
         //print_r($results);
         //print "<br>\n";
         $csv_job = $results[0];
         if ($csv_job!=NULL && $csv_job['semailid']!=NULL) {
            $query = "UPDATE schedemail SET status='RUNNING', field1=".($csv_job['field1']+1)." WHERE semailid=".$csv_job['semailid'];
            $dbLink->update($query);
            $status = "NEW";
            $addlSetters = "";
            //print "query2: <br>\n".$query;
            $filename = $GLOBALS['baseDir'].$csv_job['content'].$csv_job['field1'].".csv";
            if(file_exists($filename)){
               $template = new Template();
               $ua = new UserAcct();
               $contents = $template->getFileWithoutSub($filename,FALSE);
               $ua->insertContents($contents,getDateForDB()." ".$filename);
               $template->trackItem(NULL,NULL,"cron_users.php finished",$filename);
            } else {
               $status = "FINISHED";
               $addlSetters = ",timesent=NOW()";
            }

            $query = "UPDATE schedemail SET status='".$status."'".$addlSetters." WHERE semailid=".$csv_job['semailid'];
            //print "query4: <br>\n".$query;
            $dbLink->update($query);
            if (time()<$endTime) $finished = FALSE;
         }
      }
   }







   //--------------------------------------------------------------------
   // Use this method to queue up custom jobs
   //--------------------------------------------------------------------
   function addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL,$phpfile=NULL,$userid=NULL,$resched=NULL){
      if ($classname==NULL) return FALSE;
      //if (!class_exists($classname)) return FALSE;

      $results = $this->getJobByType("CUSTOM");
      $schedid=NULL;
      if ($results==NULL || count($results)<1) {
         $schedid = $this->scheduleJob(NULL,-1,"CUSTOM");
      } else {
         $schedid=$results[0]['schedid'];
      }

      if ($userid==NULL) $userid = isLoggedOn();
      if ($userid==NULL) $userid=0;
      
      if ($cmsid==NULL || !is_numeric($cmsid)) $cmsid=0;
      if ($starton==NULL) $starton = "NOW()";
      if ($field1==NULL) $field1=0;
      if ($field2==NULL) $field2=0;
      
      //$objstr = mysql_escape_string(serialize($phpobj));
      if($phpobject==NULL) $phpobject = array();
      $phpobj = mysqli_escape_string(serialize($phpobject));
      
      
      $dbLink = new MYSQLaccess;
      $query = "INSERT DELAYED INTO schedemail";
      $query .= " (schedid,userid,status,timeadded,cmsid,subject,priority,starton,classname,content,field1,field2,field3,field4,field5,field6,field7,phpobj,phpfile,resched)";
      $query .= " VALUES (".$schedid.",".$userid.",'NEW',NOW(),".$cmsid.",'".convertString($subject)."',".$priority.",".$starton.",'".convertString($classname)."','".convertString($content)."',".$field1.",".$field2.",'".convertstring($field3)."','".convertstring($field4)."','".convertstring($field5)."','".convertstring($field6)."'";
      
      if($field7!=NULL) $query .= ",'".convertstring($field7)."'";
      else $query .= ",NULL";
      
      $query .= ",'".$phpobj."','".convertString(trim($phpfile))."','".convertString(trim($resched))."');";

      $dbLink->insert($query);
      print "\n<!-- ***chj*** scheduled job query: ".$query." -->\n\n";
      return TRUE;
   }


   
   
   
   
   
   
   
   
   function copyJob($copyid,$printstuff=FALSE){
      $jobresults = FALSE;
      
      //ini_set('memory_limit', '128M');
      $dbLink = new MYSQLaccess();

      $query =  "SELECT e.*, s.typestr FROM schedemail e, schedjobs s ";
      $query .= " WHERE e.schedid=s.schedid ";
      $query .= " AND e.semailid=".$copyid;
      $results = $dbLink->queryGetResults($query);
      
      if ($printstuff) {
         print "\n<br>Query: ".$query."\n<br>Results:\n<br>";
         print_r($results);
         print "\n<br>";
      }
      
      if ($results!=NULL && $results[0]['semailid']!=NULL && $results[0]['semailid']==$copyid){
         if(0==strcmp($results[0]['typestr'],"CUSTOM")) {
            $phpclassname = $results[0]['classname'];
            if($phpclassname!=NULL && class_exists($phpclassname)) {
               $phpclass = new $phpclassname();
               if(method_exists($phpclass,"copyJob")) {
                  $jobresults = $phpclass->copyJob($copyid);
               }
            }
         } else if(0==strcmp($results[0]['typestr'],"pm_surveycsv")) {
            if(class_exists("ComplexCSV")) {
               $cmplxcsv = new ComplexCSV();
               $jobresults = $cmplxcsv->copyJob($copyid);
            }
         }
      }
      return $jobresults;
   }

   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   

   //---------------------------------------------------------------------
   // Schedule this every 30 minutes.
   // This picks up custom jobs
   //---------------------------------------------------------------------
   function checkCustomJobs($printstuff=FALSE,$maxRunningTime=120,$maxpriority=NULL){
      //ini_set('memory_limit', '128M');
      $dbLink = new MYSQLaccess();
      $startTime = time();
      if($maxRunningTime==NULL || $maxRunningTime<10) $maxRunningTime = 120;
      //$maxRunningTime = 60;
      //$maxRunningTime = 30;
      //$maxRunningTime = 10;

      $endTime = $startTime + $maxRunningTime;

      $query =  "SELECT e.* FROM schedemail e, schedjobs s ";
      $query .= " WHERE e.schedid=s.schedid ";
      $query .= " AND s.typestr='CUSTOM' ";
      $query .= " AND e.status='NEW' ";
      $query .= " AND (e.starton IS NULL OR e.starton<NOW()) ";
      if($maxpriority!=NULL && $maxpriority>0) $query .= " AND e.priority<=".$maxpriority;
      $query .= " ORDER BY e.priority, e.timeadded;";
      $results = $dbLink->queryGetResults($query);
      if ($printstuff) {
         print "\n<br>Query: ".$query."\n<br>Results:\n<br>";
         print_r($results);
         print "\n<br>";
      }
      $jobindex = 0;

      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;

         if ($jobindex>=count($results)) $jobindex=0;
         $customjob = $results[$jobindex];
         
         $customjob['subject'] = convertBack($customjob['subject']);
         $customjob['classname'] = convertBack($customjob['classname']);
         $customjob['content'] = convertBack($customjob['content']);
         $customjob['field3'] = convertBack($customjob['field3']);
         $customjob['field4'] = convertBack($customjob['field4']);
         $customjob['field5'] = convertBack($customjob['field5']);
         $customjob['field6'] = convertBack($customjob['field6']);
         $customjob['field7'] = convertBack($customjob['field7']);
         $resched = convertBack($customjob['resched']);
         
         $customjob['printstuff'] = FALSE;
         if ($printstuff) $customjob['printstuff'] = TRUE;
         $curindex = $jobindex;
         $jobindex++;

         if($customjob['phpfile']!=NULL) {
            $temp_inc = $GLOBALS['baseDir'].convertBack($customjob['phpfile']);
            include_once($temp_inc);
            if ($printstuff) print "\n<br>Attempted to include: ".$temp_inc;
         }
         
         $phpclassname = $customjob['classname'];
         if ($customjob!=NULL && $customjob['semailid']!=NULL && $phpclassname!=NULL && class_exists($phpclassname)) {
               $query = "UPDATE schedemail SET status='RUNNING', timesent=NOW() WHERE semailid=".$customjob['semailid'];
               $dbLink->update($query);
               if ($printstuff) print "\n<br>Update: ".$query;
   
               //call the custom class and the standard "doWork" method
               $phpclass = new $phpclassname();
               $jobresults = $phpclass->doWork($customjob);
               if ($jobresults['cmsid']==NULL) $jobresults['cmsid']=0;
               if ($jobresults['field1']==NULL) $jobresults['field1']=0;
               if ($jobresults['field2']==NULL) $jobresults['field2']=0;
               if ($jobresults['status']==NULL || 0==strcmp($jobresults['status'],"RUNNING")) $jobresults['status'] = "NEW";
   
               $query = "UPDATE schedemail SET status='".$jobresults['status']."', timesent=NOW()";
               $query .= ", content='".convertString($jobresults['content'])."'";
               $query .= ", starton='".$jobresults['starton']."'";
               $query .= ", phpobj='".$jobresults['phpobj']."'";
               $query .= ", cmsid=".$jobresults['cmsid'];
               $query .= ", field1=".$jobresults['field1'];
               $query .= ", field2=".$jobresults['field2'];
               $query .= ", field3='".convertString($jobresults['field3'])."'";
               $query .= ", field4='".convertString($jobresults['field4'])."'";
               $query .= ", field5='".convertString($jobresults['field5'])."'";
               if($jobresults['field7']!=NULL) $query .= ", field7='".$jobresults['field7']."'";
               else $query .= ", field7=NULL";
               $query .= " WHERE semailid=".$customjob['semailid'];
               if ($printstuff) print "\n<br>\n<br><hr>\n<br>*****END of run*****\n<br>Update job query: ".$query."\n<br>";
               $dbLink->update($query);
               $results[$curindex] = $jobresults;
               if (time()<$endTime) $finished = FALSE;

               if ((isset($jobresults['finished']) && $jobresults['finished']) || 0==strcmp($jobresults['status'],"FINISHED")) {
                  $temp_results = array();
                  $temp_index = 0;
                  for ($j=0;$j<count($results);$j++) {
                     if ($j!=$curindex) {
                        $temp_results[$temp_index] = $results[$j];
                        $temp_index++;
                     } else {
                        $jobindex = $temp_index;
                     }
                  }
                  $results = array();
                  $results = $temp_results;
                  
                  //Check if this job should be rescheduled in the future
                  if($resched!=NULL && 0!=strcmp($resched,"never")){
                     if(method_exists($phpclass,"rescheduleJob")) {
                        $starton = date('Y-m-d',strtotime("first day of next month"));
                        if(0==strcmp($resched,"weekly")) $starton = date('Y-m-d',strtotime("next Sunday"));
                        $phpclass->rescheduleJob($customjob['semailid'],$starton);
                     }
                  }
               }
               
         } else if ($customjob['semailid']!=NULL) {
            if ($printstuff) print "<br>\nsemailid is not null<br>\n";
            if ($phpclassname==NULL) print "<br>\nphpclassname is null<br>\n";
            if (!class_exists($phpclassname)) print "<br>\nno class found<br>\n";
            if ($customjob['semailid']==NULL && ($phpclassname==NULL || !class_exists($phpclassname))) {
               $query = "UPDATE schedemail SET status='ERROR' WHERE semailid=".$customjob['semailid'];
               if ($printstuff) print "\n<br>Query: ".$query;
               //print "\n<br>query: ".$query."<br>\n";
               $dbLink->update($query);
            }
         } else {
            if ($printstuff) print "\n<br>No jobs available.\n<br>";
         }
      }
   }






   //--------------------------------------------------------------------
   // Use this method to queue up user upload requests
   //  pathname should be just the beginning of the full path name (1.csv,2.csv,3.csv, etc. will be added to the pathname)
   //--------------------------------------------------------------------
   function addSchedDBRows($pathname,$subject=NULL,$starton=NULL,$priority=10,$userid=NULL){
      if ($pathname==NULL) return FALSE;
      if ($userid==NULL) $userid=isLoggedOn();
      if ($userid==NULL) $userid=-1;

      if(file_exists($GLOBALS['baseDir'].$pathname)){
         $results = $this->getJobByType("DBROWS");
         $schedid=NULL;
         if ($results==NULL || count($results)<1) {
            $schedid = $this->scheduleJob(NULL,-1,"DBROWS");
         } else {
            $schedid=$results[0]['schedid'];
         }
   
         $dbLink = new MYSQLaccess;
         $query = "INSERT DELAYED INTO schedemail";
         $query .= " (schedid,userid,status,timeadded,content,cmsid,subject,priority,starton,field1)";
         $query .= " VALUES (".$schedid.", ".$userid.", 'NEW',NOW(),'".$pathname."',0,'".convertString($subject)."',".$priority.",NOW(),0,0);";
   
         $dbLink->insert($query);
      }

   }

   //---------------------------------------------------------------------
   // Schedule this every 10/15 minutes.
   // This picks up priority csv jobs and processes priority 1 first, then 2, etc
   // schedule this method with Cron to run CSV jobs
   //---------------------------------------------------------------------
   function checkDBRowsJobs($printinfo=FALSE){
      ini_set('memory_limit', '128M');
      $dbLink = new MYSQLaccess;
      $startTime = time();
      $maxRunningTime = 3 * 60;
      if ($printinfo) $maxRunningTime = 30;
      $endTime = $startTime+$maxRunningTime;
      if ($printinfo) print "<BR>starttime: ".$startTime;
      if ($printinfo) print "<BR>endtime: ".$endTime;

      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;
         $query = "SELECT e.* FROM schedemail e, schedjobs s WHERE e.schedid=s.schedid AND s.typestr='DBROWS' AND e.status='NEW' AND (e.starton IS NULL OR e.starton<NOW()) ORDER BY e.priority, e.timeadded LIMIT 0,1;";
         $results = $dbLink->queryGetResults($query);
         if ($printinfo) print "\n<br>query: ".$query."\n<br>";
         $csv_job = $results[0];
         if ($printinfo) print_r($csv_job);
         $doneWithFile = FALSE;
         $error = FALSE;
         if ($csv_job!=NULL && $csv_job['semailid']!=NULL) {
            $field1 = $csv_job['field1'];
            $field2 = $csv_job['field2'];
            if ($field1==NULL) $field1=0;
            if ($field2==NULL) $field2=0;

            $query = "UPDATE schedemail SET status='RUNNING' WHERE semailid=".$csv_job['semailid'];
            $dbLink->update($query);
            if ($printinfo) print "\n<br>query: ".$query;

            $filename = $GLOBALS['baseDir'].$csv_job['content'];
            if ($printinfo) print "\n<br>filename: ".$filename;
            if(file_exists($filename)){
               if (($handle = fopen($filename, "r")) !== FALSE) {
                  $tablename = trim(fgets($handle,4096));
                  $tablename = str_replace(",","",$tablename);
                  $tablename = str_replace("\n","",$tablename);
//print "<BR>table name: ".$tablename;

                  $primarykey = strtolower(trim(fgets($handle,4096)));
                  $primarykey = str_replace(",","",$primarykey);
                  $primarykey = str_replace("\n","",$primarykey);
//print "<BR>primary key: ".$primarykey;

                  $updatestr = strtolower(trim(fgets($handle,4096)));
                  $updatestr = str_replace(",","",$updatestr);
                  $updatestr = str_replace("\n","",$updatestr);
//print "<BR>updatestr: ".$updatestr."<br>";

                  $header = fgets($handle,4096);
                  $header = csvRemoveQuotes($header);
                  //$header = str_replace(","," , ",$header);
                  if ($printinfo) print "<br>\nHeaders row: ".$header."<br>\n";
                  $headers = separateStringBy($header,",");
                  if ($printinfo) print_r($headers);

                  $query = "INSERT INTO ".$tablename." (";
                  for ($i=0; $i<count($headers); $i++) {
                     if ($i>0) $query .= ", ";
                     if ($printinfo) print "<br>\ni: ".$i;
                     if ($printinfo) print "<br>\nkey: ".$headers[$i];
                     $key = strtolower(trim($headers[$i]));
                     if ($printinfo) print "<br>\nkey: ".$key;
                     if (strpos($key,"binarybit")!==FALSE && strpos($key,"binarybit")==0) $query .= substr($key,9);
                     else $query .= $key;
                     if ($printinfo) print "<br>\nquery: ".$query;
                  }
                  $query .= ") VALUES ";
                  if ($printinfo) print "<br>\nBase Insert: ".$query."<br>\n";

                  if ($field2==NULL || $field2<ftell($handle)) $field2 = ftell($handle);
                  if ($printinfo) print "\n<br>field2: ".$field2;

                  fseek($handle,$field2);  
                  $totalLines = 0;
                  $counter = 0;
                  $updates = 0;

                  while (($counter+$updates)<600 && $totalLines<2000 && !$doneWithFile && time()<=$endTime) {
                     $line = fgets($handle,4096);
                     $totalLines++;
//print "<BR>totalLines: ".$totalLines;
//print "<BR>line: ".$line."<br>";
                     $field2 = ftell($handle);
                     if ($printinfo) print "\n<br>field2 (2): ".$field2;
                     if (feof($handle)) $doneWithFile = TRUE;
                     $line = csvRemoveQuotes($line);
                     $line = str_replace(","," , ",$line);

                     $elements = separateStringBy(" ".$line." ",",");
//print_r($elements);
                     if ($line!=NULL && count($elements)>0) {
                        $validRow = TRUE;
                        $updateRow = FALSE;
                        $tempQuery = "";
                        if ($counter>0) $tempQuery .= ", ";
                        $tempQuery .= "(";
                        $updateQuery = "UPDATE ".$tablename." SET ";
                        $primarykeyvalue = NULL;
                        $updatefields = 0;
                        for ($i=0; $i<count($headers); $i++) {
                           $key = strtolower(trim($headers[$i]));
                           $value = trim(convertString($elements[$i]));

//print "<BR>key: ".$key." value: ".$value;
                           if ($i>0) $tempQuery .= ", ";

                           if (strpos($key,"binarybit")==0) {
                              $tempQuery.= pow(2,$value);
                           } else if (strcmp($key,"created")==0 && strlen($value)!=10 && strpos($value,"/")!==FALSE) {
                              $valArr = separateStringBy($value,"/");
                              $valArr2 = separateStringBy($valArr[2]," ");
                              $tempQuery .= "'".str_pad($valArr[0],2," ",STR_PAD_LEFT)."-".str_pad($valArr[1],2," ",STR_PAD_LEFT)."-".str_pad($valArr2[0],4,"20",STR_PAD_LEFT)."'";
                           } else {
                              $tempQuery .= "'".$value."'";
                           }

                           if ($primarykey!=NULL && 0==strcmp($key,$primarykey)){
                              $primarykeyvalue = $value;
                              if ($value===NULL) {
                                 $validRow = FALSE;
                              } else {
                                 $subquery = "SELECT ".$primarykey." FROM ".$tablename." WHERE ".$primarykey."='".$value."';";
//print "<BR>subquery: ".$subquery;

                                 $results = $dbLink->queryGetResults($subquery);
                                 if ($results!=NULL && count($results)>0) {
                                    $validRow = FALSE;
                                    $updateRow = TRUE;
                                 }
                              }
                           } else {
                              if ($updatefields>0) $updateQuery .= ", ";
                              if (strpos($key,"binarybit")==0) {
                                 $shiftpos = pow(2,((int) $value));
//print "<BR>value: ".$value;
//print "<BR>shiftpos: ".$shiftpos;

                                 $updateQuery.= substr($key,9)."=IF((FLOOR(".substr($key,9)."/".$shiftpos.") % 2)=1,".substr($key,9).",(".substr($key,9)."+".$shiftpos."))";
                              } else $updateQuery .= $key."='".$value."'";
                              $updatefields++;
                           }
                        }
                        $tempQuery .= ")";
   
                        if ($validRow) {
                           $query .= $tempQuery;
                           $field1++;
                           $counter++;
                        } else if ($primarykey!=NULL && $updateRow && 0==strcmp($updatestr,"1") && $primarykeyvalue!=NULL) {
                           $updateQuery .= " WHERE ".$primarykey."='".$primarykeyvalue."';";
                           $dbLink->update($updateQuery);
                           if ($printinfo) print "\n<br>query: ".$updateQuery;
                           $field1++;
                           $updates++;
                        }
                     }
                  }
                  if ($counter>0) $dbLink->insert($query);
                  if ($counter>0 && $printinfo) print "\n<br>query: ".$query;
                  fclose($handle);
               } else {
                  $error = TRUE;
               }
            } else {
               $error = TRUE;
            }

            $query = "UPDATE schedemail SET status='NEW', field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
            if ($doneWithFile) {
               $query = "UPDATE schedemail SET status='FINISHED', timesent=NOW(), field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
            } else if ($error) {
               $query = "UPDATE schedemail SET status='ERROR', timesent=NOW(), field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
            }

            $dbLink->update($query);
            if ($printinfo) print "\n<br>query: ".$query;
            if (time()<$endTime) $finished = FALSE;
         }
      }
//print "<BR>nowtime: ".time();
   }














   //---------------------------------------------------------------------
   // Schedule this every 10/15 minutes.
   // This picks up priority csv jobs and processes priority 1 first, then 2, etc
   // schedule this method with Cron to run CSV jobs
   //---------------------------------------------------------------------
   function checkEmailListUpdateJobs(){
      ini_set('memory_limit', '128M');
      $dbLink = new MYSQLaccess;
      $startTime = time();
      $maxRunningTime = 60;
//      $maxRunningTime = 30;
      $endTime = $startTime+$maxRunningTime;
//print "<BR>starttime: ".$startTime;
//print "<BR>endtime: ".$endTime;

      $finished = FALSE;
      while (!$finished) {
         $finished = TRUE;
         $query = "SELECT e.* FROM schedemail e, schedjobs s WHERE e.schedid=s.schedid AND s.typestr='EMLIST' AND e.status='NEW' AND (e.starton IS NULL OR e.starton<NOW()) ORDER BY e.priority, e.timeadded LIMIT 0,1;";
         $results = $dbLink->queryGetResults($query);
         $csv_job = $results[0];
         $doneWithFile = FALSE;
         $error = FALSE;
         if ($csv_job!=NULL && $csv_job['semailid']!=NULL) {
            $field1 = $csv_job['field1'];
            $field2 = $csv_job['field2'];
            if ($field1==NULL) $field1=0;
            if ($field2==NULL) $field2=0;

            $query = "UPDATE schedemail SET status='RUNNING' WHERE semailid=".$csv_job['semailid'];
            $dbLink->update($query);

            $filename = $GLOBALS['baseDir'].$csv_job['content'];
            if(file_exists($filename)){
               if (($handle = fopen($filename, "r")) !== FALSE) {
                  $field4value = fgets($handle,4096);
                  $field4value = trim(csvRemoveQuotes($field4value));
                  $field4value = str_replace("\n","",$field4value);
//print "<BR>field4: ".$field4value;

                  $shiftpos = pow(2,((int) $field4value));
//print "<BR>shiftpos: ".$shiftpos;
                  $updateQuery = "UPDATE useracct SET field4=(field4+".$shiftpos.") WHERE (FLOOR(field4/".$shiftpos.") % 2)=0 AND email in (";

                  if ($field2==NULL || $field2<ftell($handle)) $field2 = ftell($handle);
                  fseek($handle,$field2);  
                  $updates = 0;

                  while ($updates<500 && !$doneWithFile && time()<=$endTime) {
                     $email = fgets($handle,4096);
                     $email = trim(csvRemoveQuotes($email));
                     $email = str_replace("\n","",$email);
//print "<BR>email: ".$email;
                     if ($updates>0) $updateQuery .= ", ";
                     $updateQuery .= "'".$email."'";
                     $updates++;
                     $field1++;
                     $field2 = ftell($handle);
                     if (feof($handle)) $doneWithFile = TRUE;
                  }
                  $updateQuery .= ");";
                  if ($updates>0) $dbLink->update($updateQuery);
//if ($updates>0) print $updateQuery;
                  fclose($handle);
               } else {
                  $error = TRUE;
               }
            } else {
               $error = TRUE;
            }

            $query = "UPDATE schedemail SET status='NEW', field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
            $template = new Template();
            if ($doneWithFile) {
               $query = "UPDATE schedemail SET status='FINISHED', timesent=NOW(), field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
               $template->trackItem(NULL,NULL,"cron_emlist.php finished successfully",$filename);
            } else if ($error) {
               $query = "UPDATE schedemail SET status='ERROR', timesent=NOW(), field1=".$field1.", field2=".$field2." WHERE semailid=".$csv_job['semailid'];
               $template->trackItem(NULL,NULL,"cron_emlist.php caused error",$filename);
            }

            $dbLink->update($query);
            if (time()<$endTime) $finished = FALSE;
         }
      }
//print "<BR>nowtime: ".time();
   }




}



class SchedulerCSVDownload {
   function addSchedCSV($sql,$priority=5,$subj=NULL){
      if ($sql==NULL) return FALSE;
      $pathname = "jsfadmin/usercsv/schedcsv_".date("Ymd_His").".csv";
      $counter = 2;
      while(file_exists($GLOBALS['baseDir'].$pathname)){
         $pathname = substr($pathname,0,strlen($pathname)-4)."_".$counter.".csv";
         $counter++;
      }
      $sched = new Scheduler();
      $sched->addSchedCustom("SchedulerCSVDownload",$subj,$priority,NULL,$sql,0,0,"","",$pathname);
   }

   function doWork($job){
      if($job['field1']==NULL) $job['field1']=0;
      if($job['field2']==NULL) $job['field2']=300;
      if($job['field4']==NULL) $job['field4']="";      
      $job['field4'] .= date("ymdHi").",";
      $job['status'] = "NEW";
   	              
      $query = convertBack($job['content']);
      $start_pos = $job['field1']*$job['field2'];
      $query = $query." LIMIT ".$start_pos.", ".$job['field2'];
      $results = $dbLink->queryGetResults($query);      
      if ($results!=NULL && count($results)>0) {
         if (count($results)<$job['field2']) $job['status'] = "FINISHED";
         
         $contents = "";
         $keys = array_keys($results[0]);
      
         for ($j=0; $j<count($results); $j++) {
           for ($i=0; $i<count($keys); $i++) { 
             $contents .= "\"".csvEncodeDoubleQuotes(convertBack($results[$j][$keys[$i]]))."\",";
           }
           $contents .= "\n";
         }
      
         //If file does not exist, create the header row
         $filename = $GLOBALS['baseDir'].$job['field5'];
         if(!file_exists($filename)){
           $header = "";
           for ($i=0; $i<count($keys); $i++) $header .= "\"".csvEncodeDoubleQuotes($keys[$i])."\",";
           $contents = $header."\n".$contents;
         }
      
         //Write to the file (append mode adds to the end of an existing file or creates a new file)
         $file = fopen($filename,"a");
         fwrite($file, $contents);
         fclose($file);
      } else {
         $job['status'] = "FINISHED";
      }
      
      $job['field1']++;
      return $job;
   }


	
}


?>
