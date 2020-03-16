<?php
   include_once "Classes.php";
   if(getParameter("testing")==1) error_reporting(E_ALL);
   if(getParameter("testing")==1) print "\n\n<br><br><hr><br>TESTING TURNED ON<br><hr><br><br>\n\n";
   // http://www.plasticsmarkets.org/jsfcode/jsoncontroller.php?action=getwebdatapage&callback=jsfwebdata_display&wdname=Test%20Survey&prefix=jsfwd&page=1
   //include_once "WDataSPA.php";
   include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS.php";

   if (isset($_SERVER['HTTPS'])) $_SESSION['secure']=1;
   else unset($_SESSION['secure']);

   $action  = getParameter("action");
   //print "<br>action: ".$action." id: ".getParameter("id")."<br>\n";
   
   $json = "";
   $resp = array();
   $resp['responsecode'] = 0;
   $resp['requestdatetime'] = date("y-m-d H:i:s");
   $resp['divid'] = getParameter("divid");
   
   
   //****************************************************
   //*************** OPTIONAL SECURITY ******************
   
   // Who is requesting access?
   function jsf_parsedomain($ref) {
      $url = NULL;
      $ref = str_replace("http://","",$ref);
      $ref = str_replace("https://","",$ref);
   
      $urlArr = separateStringBy($ref,"/");
      $ref = $urlArr[0];
   
      $urlArr = separateStringBy($ref,":");
      $ref = $urlArr[0];
   
      if($ref!=NULL) {
         $urlArr = separateStringBy($ref,".");
         $url = $urlArr[(count($urlArr)-2)].".".$urlArr[(count($urlArr)-1)];
         if(count($urlArr)==4 && is_numeric($urlArr[0]) && is_numeric($urlArr[1]) && is_numeric($urlArr[2]) && is_numeric($urlArr[3])) {
            $url = $urlArr[0].".".$urlArr[1].".".$urlArr[2].".".$urlArr[3];
         }
      }
      return $url;
   }
   
   function jsf_checkpermission($domain,$token=NULL,$action=NULL){
      // only follow through if "API Security" jdata table exists
      $allow = FALSE;
      
      if($domain!=NULL) {
         $wdo = new WebsiteData();
         $wdata = $wdo->getWebData("API Security");
         if($wdata!=NULL && $wdata['wd_id']!=NULL) {
            $qs = $wdo->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
            
            $query = "SELECT * FROM wd_".$wdata['wd_id']." WHERE ";
            $query .= "(LOWER(".$qs['alldomains'].")='yes'";
            if($domain!=NULL) $query .= " OR LOWER(".$qs['domains'].") LIKE '%".$domain."%'";
            $query .= ")";
            $query .= " AND ";
            $query .= "(LOWER(".$qs['allactions'].")='yes'";
            if($action!=NULL) $query .= " OR LOWER(".$qs['actions'].") LIKE '%".$action."%'";
            $query .= ")";
            $query .= " AND ";
            $query .= "(LOWER(".$qs['notokenrqd'].")='yes'";
            if($token!=NULL) $query .= " OR ".$qs['domaintoken']."='".$token."'";
            $query .= ")";
            $query .= " AND ";
            $query .= "LOWER(".$qs['enabled'].")='yes'";
            $query .= " ORDER BY ".$qs['sequence'].";";
            
            $dbLink = new MYSQLaccess;
            $results = $dbLink->queryGetResults($query);
            
            if($results!=NULL && count($results)>0) {
               // check any additional fields here (tracking, etc)
               if(0==strcmp(strtolower(trim($results[0][$qs['track']])),"yes")) {
                  $temp = new Template();
                  $temp->trackItem("JSON API Request",$domain.": ".$action,substr($_SERVER['REQUEST_URI'],0,254),substr($_SERVER['REQUEST_URI'],254,254));
               }
               
               $allow = TRUE;
            }
         } else {
            //continue if no security table exists...
            $allow = TRUE;
         }
      }
      return $allow;
   }



   function jsf_RESTSecurity() {
      // use url parameter to check domain, use domaintoken for auth
      $url1 = jsf_parsedomain($_SERVER['HTTP_REFERER']);
      if($url1==NULL) $url1 = jsf_parsedomain($_SERVER['REMOTE_ADDR']);
      $url2 = jsf_parsedomain(getBaseURL());
      $allowAPI = FALSE;
      
      if(0==strcmp($url1,$url2)) {
         $allowAPI = TRUE;
      } else {
         $domaintoken = getParameter("domaintoken");
         $action = getParameter("action");
         $allowAPI = jsf_checkpermission($url1,$domaintoken,$action);
      }
      return $allowAPI;
   }
   
   //**************** SECURITY **************************
   //****************************************************
   
   $allow = jsf_RESTSecurity();
   
   if($allow) {
      if (0==strcmp($action,"test1234")) {
         $resp['id'] = 1234;
         $resp['city'] = "Raleigh / Durham";
         $resp['title'] = "$22 for $50 Worth of Food at Oliver Twist";
         $resp['details'] = "Come to Oliver Twist for a great experience.";
         $resp['stipulations'] = "Used only on week days.  Not valid towards food";
         $resp['company'] = "Oliver Twist<br>777 Creedmoor Ave<br>Suite 444<br>Raleigh, NC  22222<br>(919)-444-4444<br>http://www.ssssssssss.com";
         $resp['img'] = getBaseURL()."jsfimages/noimage.jpg";
         $resp['responsecode'] = 1;
         $resp['available'] = 1;
         $resp['value'] = 50;
         $resp['price'] = 22;
         $resp['endtime'] = "2011-05-23 00:00:00";
   
      } else if (0==strcmp($action,"available")) {
         $resp['responsecode'] = 1;
      // Validate a user logging in from an application.  If so, issue a temporary
      // "valet key" to retreive data, ie, token    
      } else if (0==strcmp($action,"login")) {
         $email = getParameter("email");
         $password = getParameter("password");
         $authKey['email'] = $email;
         $authKey['password'] = $password;
         $ua = new UserAcct();
         if ($ua->userAuthenticate($authKey)) {
            $resp['responsecode'] = 1;
            $fulluser = $ua->getFullUserInfoByEmail($email);
            
            if($fulluser['token']==NULL) {
               $token = getRandomNum($email);
               $ua->updateField($fulluser['userid'],"token",$token);
               $fulluser['token'] = $token;
            }
            //$token = getRandomNum($email);
            //***chj** remove using field1 when zsd app goes out of prod
            //$ua->updateField($fulluser['userid'],"field1",$token);
            //$fulluser['field1'] = $token;
            //$ua->updateField($fulluser['userid'],"token",$token);
            //$fulluser['token'] = $token;
            //$resp['token'] = $token;
            foreach($fulluser as $key => $value) {
               if (0!=strcmp($key,"password") && 0!=strcmp(substr($key,0,1),"q")) {
                  $resp['user'][$key]=$value;
               }
            }
            if ($ua->isUserAdmin($fulluser['userid'])) $resp['user']['isadmin']=1;
            if (getParameter("includerels")==1){
               $relto = $ua->getUsersRelated($fulluser['userid'],"to");
               if ($relto!=NULL && count($relto)>0) $resp['rels_to'] = $relto;
               $relfrom = $ua->getUsersRelated($fulluser['userid'],"from");
               if ($relfrom!=NULL && count($relfrom)>0) $resp['rels_from'] = $relfrom;
            }
         } else {
            $resp['responsecode'] = 0;
         }
   
      //---------------------------------------------------------------------------------
      // Create user account if one doesn't already exist.  Update it otherwise
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"logout")) {
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['token'],$token)) {
            $token = getRandomNum($user['email']);
            $ua->updateField($user['userid'],"token",$token);
            $resp['token'] = $token;
         }
   
      // Send in userid and token (field1) to see if the application has
      // valid claim to the user information
      } else if (0==strcmp($action,"requestjsoncsv")) {
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token) ) {
            $json = getParameter("json");
            $subj = getParameter("subj");
            $limit = getParameter("limit");
            $page = getParameter("page");
            $results = getParameter("results");
            $field1 = 100;
            $sch = new ScheduledJSONCSV();
            $resp['id'] = $sch->createJob($json,$subj,$limit,$page,$results,$field1,$userid);
            $resp['responsecode'] = 1;
            $resp['json'] = $json;
            $resp['subj'] = $subj;
            //$resp['divid'] = getParameter("divid");
         }
      } else if (0==strcmp($action,"acctinfo")) {
         $getuserid = getParameter("getuserid");
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getFullUserInfo($userid);
         if (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token) ) {
            if ($getuserid!=NULL && $getuserid!=$userid) $user = $ua->getFullUserInfo($getuserid);
            if ($user['token']==NULL) {
               $user['token'] = getRandomNum();
               $ua->updateField($user['userid'],"token",$user['token']);
            }
            $resp['responsecode'] = 1;
            foreach($user as $key => $value) {
               //if (0!=strcmp($key,"password") && 0!=strcmp($key,"token") && 0!=strcmp(substr($key,0,1),"q")) {
               if (0!=strcmp($key,"password") && 0!=strcmp(substr($key,0,1),"q")) {
                  $resp['user'][$key]=$value;
               }
            }
            if ($ua->isUserAdmin($user['userid'])) $resp['user']['isadmin']=1;
   
            if (getParameter("includerels")==1){
               $relto = $ua->getUsersRelated($user['userid'],"to");
               if ($relto!=NULL && count($relto)>0) $resp['rels_to'] = $relto;
               $relfrom = $ua->getUsersRelated($user['userid'],"from");
               if ($relfrom!=NULL && count($relfrom)>0) $resp['rels_from'] = $relfrom;
            }
         } else {
            $resp['responsecode'] = 0;
         }
   
      } else if (0==strcmp($action,"facebookacctinfo")) {
         $field1 = getParameter("facebookid");
         $field2 = getParameter("fbemail");
         $email = getParameter("email");
         $userid = NULL;
         $ua = new UserAcct();
         $user = NULL;
         if($email != NULL) $user = $ua->getUserByEmail($email);
         else $user = $ua->getUserByField1($field1);
         
         // Should we attempt to get the user by facebook email?
         // This seems like a security risk
         /////// if($user==NULL) $user = $ua->getUserByEmail($field2);
         
         //print "<br>\n";
         //print_r($user);
         //print "<br>\n";
         $validate1 = encrypt($field1,$field2);
         $validate2 = getParameter("validate");
         if(0==strcmp($validate1,$validate2)) {
            if($user==NULL || $user['userid']==NULL){
               $fname = getParameter("fname");
               $lname = getParameter("lname");
               $notes = getParameter("notes");
               $refsrc = getParameter("refsrc");
               $usertype = getParameter("usertype");
               if($email==NULL) $email = $field1."_dummy@facebook.com";
               $userid = $ua->addAccount($email,getRandomNum(),$fname, $lname,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL, TRUE,FALSE,$notes,$usertype,NULL,NULL,NULL,NULL,NULL,$refsrc,NULL,NULL,TRUE,NULL,NULL,NULL,NULL,NULL, $field1, $field2);
               $resp['newuser'] = 1;
            } else {
               //if($field2==NULL || 0==strcmp($field2,$user['field2'])) $userid = $user['userid'];
               $userid = $user['userid'];
               $resp['newuser'] = 0;
            }
         }
         
         if($userid!=NULL) {
            $user = $ua->getFullUserInfo($userid);
            if ($user['token']==NULL) {
               $user['token'] = getRandomNum();
               $ua->updateField($user['userid'],"token",$user['token']);
            }
            $resp['responsecode'] = 1;
            foreach($user as $key => $value) {
               //if (0!=strcmp($key,"password") && 0!=strcmp($key,"token") && 0!=strcmp(substr($key,0,1),"q")) {
               if (0!=strcmp($key,"password") && 0!=strcmp(substr($key,0,1),"q")) {
                  $resp['user'][$key]=$value;
               }
            }
            if ($ua->isUserAdmin($user['userid'])) $resp['user']['isadmin']=1;
         } else {
            $resp['responsecode'] = 0;
         }
         
      } else if (0==strcmp($action,"removeuserrelationship")) {
         $resp['responsecode'] = 0;
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getFullUserInfo($userid);
         if (0==strcmp($user['token'],$token)) {
            $ua->removeUserRelationship(getParameter('relid'));
            $resp['responsecode'] = 1;
         }
      } else if (0==strcmp($action,"getorgwdid")) {
         $resp['responsecode'] = 0;
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getFullUserInfo($userid);
         $resp['webdata'] = NULL;
         if (0==strcmp($user['token'],$token) ) {
            $resp['responsecode'] = 1;
            $wd = new WebsiteData();
            $results = $wd->getWebTables($userid,7,TRUE,getParameter("organizationid"),NULL,"ACTIVE",$userid);
            $resp['webdata'] = $results[0];
         }
   
      //---------------------------------------------------------------------------------
      // Create user account if one doesn't already exist.  Update it otherwise
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"adduser")) {
         $resp['testing']=getParameter("testing");
         $ua = new UserAcct();
         $email = getParameter("email");
         $password = getParameter("password");
         $cpassword = getParameter("cpassword");
         $overrideemail = (getParameter("overrideemail") == 1);
   
         $userid = $ua->addUser(NULL,$overrideemail);
         if($resp['testing']==1) print "<br>adduser... userid: ".$userid."<BR>";
         if ($userid>0) {
            $resp['userid'] = $userid;
            $fulluser = $ua->getFullUserInfo($userid);
            $token = getRandomNum($email);
            //***chj** remove using field1 when zsd app goes out of prod
            //$ua->updateField($fulluser['userid'],"field1",$token);
            //$fulluser['field1'] = $token;
            $ua->updateField($fulluser['userid'],"token",$token);
            $fulluser['token'] = $token;
            $resp['token'] = $token;
            foreach($fulluser as $key => $value) {
               if (0!=strcmp($key,"password") && 0!=strcmp(substr($key,0,1),"q")) {
                  $resp['user'][$key]=$value;
               }
            }
            if ($ua->isUserAdmin($fulluser['userid'])) $resp['user']['isadmin']=1;
            
            $userrelid = getParameter("userrelid");
            $userreltype = getParameter("userreltype");
            if($userrelid!=NULL && $userreltype!=NULL) {
               $ua->addUserRelationship($userid,$userrelid,$userreltype);
            }
            
            $resp['responsecode'] = 1;      
            $resp['newuser'] = 1;   
            
            //check/submit properties
            $wdname = $fulluser['usertype']." Properties";
            $wdo = new WebsiteData();
            if($resp['testing']==1) print "<br>adduser... looking for wdname: ".$wdname."<BR>";
            $wdata = $wdo->getWebData($wdname);
            if($wdata!=NULL) {
               $rows = $wdo->getDataByUserid($wdata['wd_id'], $fulluser['userid']);
               if($resp['testing']==1) print "<br>adduser found properties... wdname: ".$wdname." row: ".$rows[0]['wd_row_id']." wd_id: ".$wdata['wd_id']." oe: ".$rows[0]['origemail']."<BR>";
               $resp['wd_row_id'] = $wdo->submitSurvey($wdata['wd_id'],$rows[0]['wd_row_id'],TRUE,NULL,TRUE,-1,FALSE,TRUE,$rows[0]['origemail'],($resp['testing']==1));
               $resp['wd_id'] = $wdata['wd_id'];
               $resp['origemail'] = $rows[0]['origemail'];
            }
         } else {
            $resp['responsecode'] = 0;
            $resp['response'] = $userid;
            if ($userid==-1) $resp['responsetext'] = "Invalid email address.";
            else if ($userid==-2) $resp['responsetext'] = "Please make sure you type the same email address twice.";
            else if ($userid==-3) $resp['responsetext'] = "Email address already has an account in our system.";
            else if ($userid==-4) $resp['responsetext'] = "Please make sure your password is at least 6 characters and you typed the same password twice.";
            else $resp['responsetext'] = "Unable to create your account at this time.  You may have a weak internet connection.  Please try again later.";
         }
   
      //---------------------------------------------------------------------------------
      // Create user account if one doesn't already exist.  Update it otherwise
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"addnotesuser")) {
         $ua = new UserAcct();
         $ua->addNotes(getParameter("userid"),getParameter("notes"));
         $resp['responsecode'] = 1;               
      } else if (0==strcmp($action,"updateuserfield")) {
         $resp['responsecode'] = 0;               
         $getuserid = getParameter("getuserid");
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token)) {
            if ($getuserid!=NULL && $getuserid!=$userid) {
               $user = $ua->getUser($getuserid);
            }
            
            $ua->updateField($user['userid'], getParameter("field"), getParameter("value"));
            $resp['responsecode'] = 1;         
         }
      } else if (0==strcmp($action,"updateuser")) {
         $getuserid = getParameter("getuserid");
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getFullUserInfo($userid);
         if (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token)) {
            if ($getuserid!=NULL && $getuserid!=$userid) {
               $user = $ua->getFullUserInfo($getuserid);
            }
            $_SESSION['params']['updateuserid'] = $user['userid'];
            if (getParameter("changepassword")==1) {
               $password = getParameter("password");
               $cpassword = getParameter("cpassword");
               $ua->modifyPassword($password, $cpassword, NULL, $user['userid'], TRUE);
            }
            if ($ua->modifyUser(FALSE,$user)) {
               $fulluser = $ua->getFullUserInfo($user['userid']);
               foreach($fulluser as $key => $value) {
                  if (0!=strcmp($key,"password") && 0!=strcmp($key,"password2") && 0!=strcmp(substr($key,0,1),"q")) {
                     $resp['user'][$key]=$value;
                  }
               }
               if (getParameter("includerels")==1){
                  $relto = $ua->getUsersRelated($fulluser['userid'],"to");
                  if ($relto!=NULL && count($relto)>0) $resp['rels_to'] = $relto;
                  $relfrom = $ua->getUsersRelated($fulluser['userid'],"from");
                  if ($relfrom!=NULL && count($relfrom)>0) $resp['rels_from'] = $relfrom;
               }
               if ($ua->isUserAdmin($fulluser['userid'])) $resp['user']['isadmin']=1;
               $resp['responsecode'] = 1;
               
               $tempun = getParameter("username");
               if($tempun!=NULL && 0!=strcmp($tempun,$fulluser['username'])) {
                  $resp['responsetxt'] = "Your account was updated - but the username you tried to save is already in use, please try a different username.";
               }
            } else {
               $resp['responsecode'] = 0;
            }
            unset($_SESSION['params']['updateuserid']);
         } else {
            $resp['responsecode'] = 0;
         }
   
   
      } else if (0==strcmp($action,"refreshcache")) {
         session_unset();
         $dbLink = new MYSQLaccess();
         $dbLink->deleteCache();      
         $resp['responsecode'] = 1;      
         
      //---------------------------------------------------------------------------------
      // Search Users...
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"searchusers")) {
         $resp = NULL;
         $ua = new UserAcct();
         $wdObj = new WebsiteData();
         
         //getUsersForSegment($segment=NULL, $segmentid=NULL, $orderby=NULL, $page=NULL, $limit=NULL, $justCount=FALSE, $table="useracct"){
         $resp = $ua->getUsersForSegment(getParameter("segment"), getParameter("segmentid"), getParameter("orderby"), getParameter("page"), getParameter("limit"), (getParameter("justCount")==1));
         $resp['divid'] = getParameter("divid");
         //print_r($resp);
         if(getParameter("addcount")==1) {
            $resp['totalcount']  = $ua->getUsersForSegment(getParameter("segment"), getParameter("segmentid"), NULL, NULL, NULL, TRUE);
         }
         
         //unset($resp['hiddenFields']);
         //unset($resp['parentsegment']);
         //unset($resp['getParams']);
         
         for($i=0;$i<count($resp['users']);$i++){
            $resp['users'][$i] = $ua->getUser($resp['users'][$i]['userid']);
            if(getParameter("fulluser")==1) {
               //$resp['users'][$i] = $ua->getFullUserInfo($resp['users'][$i]['userid']);
               
               $webdata = $wdObj->getWebData($resp['users'][$i]['usertype']." Properties");
               if($webdata!=NULL && $webdata['wd_id']>0) {
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $resp['users'][$i]['userid']);
                  $sci = $results[0];
                  if($sci!=NULL && $sci['wd_row_id']>0) {
                     $questions = $wdObj->getHeaderFields($webdata['wd_id']);
                     for ($j=0; $j<count($questions); $j++) {
                        $prop = str_replace(":","",str_replace("\"","",str_replace("'","",str_replace(",","",str_replace(".","",str_replace(" ","",strtolower($questions[$j]['label'])))))));
                        $resp['users'][$i][$prop] = $sci[$questions[$j]['field_id']];
                     }
                  }
               }
   
            }
            unset($resp['users'][$i]['password']);
            unset($resp['users'][$i]['token']);
            if(strpos($resp['users'][$i]['email'],"dummy")!==FALSE) $resp['users'][$i]['email'] = "";
         }
         $resp['responsecode'] = 1;      
   
      } else if (0==strcmp($action,"searchusersrelated")) {
         $resp = NULL;
         $ua = new UserAcct();
         $userobj = $ua->getUsersForSegment(getParameter("segment"), getParameter("segmentid"), getParameter("orderby"), getParameter("page"), getParameter("limit"), (getParameter("justCount")==1));
         $resp['users'] = $ua->getUsersRelatedList($userobj['users'],getParameter("direction"),getParameter("rel_type"));
         $resp['responsecode'] = 1;      
   
      } else if (0==strcmp($action,"usersegments")) {
         $resp = NULL;
         $ua = new UserAcct();
         $resp = $ua->getAllDropdownSegments();
         $resp['responsecode'] = 1;      
   
      //---------------------------------------------------------------------------------
      // WebsiteData functions
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"submitwebdatavisual")) {
         $wdObj = new WebsiteData;
         $wd_id = getParameter("wd_id");
   
         if ($wd_id==NULL) {
            $resp['responsecode'] = 0;
            $resp['error'] = "There is an issue with your request, please contact the site administrator.";
         } else {
            $qid = getParameter("qid");
            $wd_row_id = getParameter("wd_row_id");
            if ($wd_row_id==NULL) $wd_row_id=$wdObj->addRow($wd_id);   
            if ($wd_row_id!=NULL && $wd_row_id>0) {
               $wdObj->setAnswer($wd_id,$wd_row_id,$qid,getParameter("val"));
               $resp['responsecode'] = 1;
               $resp['privatesrvy'] = 7;
               $resp['wd_row_id'] = $wd_row_id;
               $resp['wd_id'] = $wd_id;
               $resp['explicitcss']=getParameter("explicitcss");
               $resp['noemail']=getParameter("noemail");
               $resp['testing']=getParameter("testing");
               $resp['prefix'] = getParameter("prefix");
               $resp['nextpg'] = getParameter("nextpg");
            } else {
               $resp['responsecode'] = 0;
               $resp['error'] = "There was an error processing this request.  Please try again later.";
            }
         }
      } else if (0==strcmp($action,"submitwebdatapage")) {
         //error_reporting(E_ALL);
         $wdObj = new WebsiteData;
         $wd_id = getParameter("wd_id");
         $wd_row_id = getParameter("wd_row_id");
         $smpg = getParameter("smpg");
         if ($smpg==NULL || !is_numeric($smpg)) $smpg=1;
   
         $sections = $wdObj->getDataSections($wd_id,-1);
         if (getParameter("w".$wd_id."login")==1) {
            $email = getParameter("w".$wd_id."aemail");
            $passcode = getParameter("w".$wd_id."apasscode");
            $sci = $wdObj->getCodedRow($wd_id,$passcode);
            //print_r($sci);
            $weregood=FALSE;
            if($sci!=NULL && $sci['userid']>0) {
               $ua = new UserAcct();
               $user = $ua->getUser($email);
               if($user['userid']==$sci['userid']) {
                  $weregood=TRUE;
               } else {
                  $adminrel = $ua->getUsersRelated($user['userid'],"both","SRVYADMIN");
                  //print_r($adminrel);
                  for($i=0;$i<count($adminrel);$i++) {
                     if($adminrel[$i]['reluserid']==$sci['userid'] || $adminrel[$i]['userid']==$sci['userid']) {
                        $weregood=TRUE;
                        break;
                     }
                  }
               }
            }
            if($weregood){
               $resp['responsecode'] = 1;
               $resp['row'] = $sci;
               $resp['userid'] = $sci['userid'];
               $resp['wd_row_id'] = $sci['wd_row_id'];
               $resp['origemail'] = $sci['origemail'];
               $resp['email'] = $email;
               $resp['wd_id'] = $wd_id;
               $resp['explicitcss']=getParameter("explicitcss");
               $resp['noemail']=getParameter("noemail");
               $resp['testing']=getParameter("testing");
               //$resp['nextpg'] = 1;            
            } else {
               $resp['responsecode'] = 0;
               $resp['error'] = "Sorry, your credentials are incorrect.  Please check your passcode and try again.";
            }
         } else if (getParameter("w".$wd_id."userupdate")==1) {
            $ua = new UserAcct();
            $o_userid = getParameter("w".$wd_id."_o_userid");
            $u_userid = getParameter("w".$wd_id."_u_userid");
            $uchange = getParameter("w".$wd_id."a_changes");
            if($uchange==1 && $o_userid!=NULL && $o_userid>0) {
               $ouser = $ua->getUser($o_userid);
               //if(getParameter("w".$wd_id."a_o_company")!=NULL) $ouser['company'] = getParameter("w".$wd_id."a_o_company");
               $ouser['company'] = getParameter("w".$wd_id."a_o_company");
               $ouser['website'] = getParameter("w".$wd_id."a_o_website");
               $ouser['addr1'] = getParameter("w".$wd_id."a_o_addr1");
               $ouser['addr2'] = getParameter("w".$wd_id."a_o_addr2");
               $ouser['city'] = getParameter("w".$wd_id."a_o_city");
               $ouser['state'] = getParameter("w".$wd_id."a_o_state");
               $ouser['zip'] = getParameter("w".$wd_id."a_o_zip");
               $ouser['country'] = getParameter("w".$wd_id."a_o_country");
               $ua->modifyUserExplicit($ouser['userid'],$ouser['email'],$ouser['fname'],$ouser['lname'],$ouser['age'],$ouser['gender'],$ouser['marital'],$ouser['edu'],$ouser['nletter'],$ouser['phonenum'],$ouser['phonenum2'],$ouser['phonenum3'],$ouser['phonenum4'],$ouser['addr1'],$ouser['addr2'],$ouser['city'],$ouser['state'],$ouser['zip'],$ouser['usertype'],$ouser['company'],$ouser['website'],$ouser['alive'], $ouser['country'], $ouser['title']);
            }
            if($uchange==1 && $u_userid!=NULL && $u_userid>0) {
               $uuser = $ua->getUser($u_userid);
               //if(getParameter("w".$wd_id."a_o_company")!=NULL) $uuser['company'] = getParameter("w".$wd_id."a_o_company");
               $uuser['fname'] = getParameter("w".$wd_id."a_u_fname");
               $uuser['lname'] = getParameter("w".$wd_id."a_u_lname");
               $uuser['title'] = getParameter("w".$wd_id."a_u_title");
               $uuser['phonenum'] = getParameter("w".$wd_id."a_u_phonenum");
               $uuser['phonenum2'] = getParameter("w".$wd_id."a_u_phonenum2");
               $uuser['phonenum3'] = getParameter("w".$wd_id."a_u_phonenum3");
               $uuser['email'] = getParameter("w".$wd_id."a_u_email");
               $ua->modifyUserExplicit($uuser['userid'],$uuser['email'],$uuser['fname'],$uuser['lname'],$uuser['age'],$uuser['gender'],$uuser['marital'],$uuser['edu'],$uuser['nletter'],$uuser['phonenum'],$uuser['phonenum2'],$uuser['phonenum3'],$uuser['phonenum4'],$uuser['addr1'],$uuser['addr2'],$uuser['city'],$uuser['state'],$uuser['zip'],$uuser['usertype'],$uuser['company'],$uuser['website'],$uuser['alive'], $uuser['country'], $uuser['title']);
            }
            $resp['row'] = $wdObj->getDetailsClear($wd_id,$wd_row_id);
            $resp['responsecode'] = 1;
            $resp['userid'] = $resp['row']['userid'];
            $resp['wd_row_id'] = $resp['row']['wd_row_id'];
            $resp['origemail'] = $resp['row']['origemail'];
            $resp['email']=getParameter("email");
            $resp['wd_id'] = $wd_id;
            $resp['explicitcss']=getParameter("explicitcss");
            $resp['noemail']=getParameter("noemail");
            $resp['testing']=getParameter("testing");
            $resp['nextpg'] = getParameter("nextpg");
            if($resp['nextpg']==NULL) $resp['nextpg'] = 1;
         } else if ($wd_id==NULL || $smpg>count($sections)) {
            $resp['responsecode'] = 0;
            $resp['error'] = "There is an issue with your request, please contact the site administrator.";
         } else {
            $sect_index = $smpg - 1;
            if ($wd_row_id==NULL) {
               $wd_row_id=$wdObj->addRow($wd_id);
               $resp['newrecord'] = 1;
            }
            $wdObj->submitSection($wd_row_id, $sections[$sect_index]['section'],$wd_id,(getParameter("ignorenull")==1),TRUE);
            
            //table within a table will submit the json needed to save each row.
            $wdtablejsonarr = getParameter("jsonarr");
            if($wdtablejsonarr!=NULL && is_array($wdtablejsonarr)){
               $rowscounted = 0;
               $sqlupdates = array();
               for($i=0;$i<count($wdtablejsonarr);$i++){
                  //$t_params = parseURLParams($wdtablejsonarr[$i]);               
                  //$recurl = getBaseURL()."jsfcode/jsoncontroller.php?action=submitsinglewdrow&i=".date("YmdHis").getRandomNum();
                  //$recurl = getBaseURL()."jsfcode/jsoncontroller.php?action=submitsinglewdrow";
                  $recurl = getBaseURL()."jsfcode/jsoncontroller.php?";
                  $recurl .= $wdtablejsonarr[$i];
                  if($resp['newrecord']==1) $recurl .= "&o_wd_row_id=".$wd_row_id;
                  //print "<br>\nJSON request row:<br>\n".$recurl."<br>\n<br>\n";
                  $recurl_result = requestJSON($recurl,FALSE,TRUE);
               }
            }
            
            $resp['row'] = $wdObj->getDetailsClear($wd_id,$wd_row_id);
      
            if ($wd_row_id>0) {
               if ($smpg==count($sections)) {
                  $wd = $wdObj->getWebData($wd_id);
                  if ($wd['emailresults']==1 && $wd['adminemail']!=NULL && getParameter('noemail')!=1) {
                     $ver = new Version();
                     $fromemail = $ver->getValue("WebsiteContact");
   
                     if(trim($wd['filename'])==NULL) {
                        $emailcontents = $wdObj->getAnswersString($wd_id,$wd_row_id);
                     } else {
                        $emailcontents_o = $ver->getAsciiFileContents(trim($wd['filename']));
                        $emailcontents = $emailcontents_o['contents'];
                     }
                     
                     $emailcontents = str_replace("%%%WD_ID%%%",$wd['wd_id'],$emailcontents);
                     $emailcontents = str_replace("%%%WD_ROW_ID%%%",$wd_row_id,$emailcontents);
                     
                     $sched = new Scheduler();
                     $sched->addSchedEmail(NULL,NULL,$emailcontents,$wd['name'],5,NULL,$fromemail,5,TRUE,NULL,NULL,$wd['adminemail']);
                  }
                  
                  $esign = getParameter("w".$wd['wd_id']."esign");
                  if ($esign!=NULL) {
                     $wdObj->updateFieldValue($wd['wd_id'],$wd_row_id,"esignature",$esign,FALSE);
                     $template = new Template();
                     $template->trackItem("esignature",$wd['name'],$wd['wd_id']."_".$wd_row_id,$esign,"eSignature captured online");
                  }  
               }
   
               $resp['responsecode'] = 1;
               $resp['wd_row_id'] = $wd_row_id;
               $resp['origemail'] = $resp['row']['origemail'];
               $resp['wd_id'] = $wd_id;
               $resp['explicitcss']=getParameter("explicitcss");
               $resp['noemail']=getParameter("noemail");
               $resp['email']=getParameter("email");
               $resp['testing']=getParameter("testing");
               $nextpg = getParameter("nextpg");
               if ($nextpg==NULL && $smpg<1) $nextpg = 1;
               else if ($nextpg==NULL) $nextpg = $smpg + 1;
               if ($nextpg > count($sections)) $nextpg=-1;
               $resp['nextpg'] = $nextpg;            
            } else {
               $resp['responsecode'] = 0;
               $resp['error'] = "There was an error processing this request.  Please try again later.";
            }
         }
      
      } else if (0==strcmp($action,"urlshortener")) {
         $ua = new UserAcct();
         $wdObj = new WebsiteData();
         $wd_id = getParameter("wd_id");
         $resp['url'] = "error.html";
         if ($wd_id == NULL) $wd_id = getParameter("wdname");
         $wdata = $wdObj->getWebDataByName($wd_id);
         $wd_id = $wdata['wd_id'];
         if($wd_id!=NULL && $wdata['privatesrvy']==3){
            $wd_row_id = getParameter("wd_row_id");
            $shortname = getParameter("shortname");
            $username = getParameter("username");
            if($username!=NULL) {
               $user = $ua->getUserByUsername($username);
               if($user!=NULL && $user['userid']!=NULL){
                  $qs = $wdObj->getFieldLabels($wd_id);
                  $dbLink = new MYSQLaccess();
                  $query = "SELECT ".$qs['url']." as url FROM wd_".$wd_id." WHERE ";
                  $query .= "userid=".$user['userid'];
                  $query .= " AND ".$qs['shortname']."='".$shortname."'";
                  $query .= " ORDER BY created DESC LIMIT 0,1;";
                  $results = $dbLink->queryGetResults($query);
                  $resp['url'] = $results[0]['url'];
               }
            } else if ($wd_row_id!=NULL) {
               $qs = $wdObj->getFieldLabels($wd_id);
               $dbLink = new MYSQLaccess();
               $query = "SELECT ".$qs['url']." as url FROM wd_".$wd_id." WHERE ";
               $query .= "wd_row_id=".$wd_row_id.";";
               $results = $dbLink->queryGetResults($query);
               $resp['url'] = $results[0]['url'];
            }
         }
         
      } else if (0==strcmp($action,"retrievesecurewdrow")) {
         $wd = new WebsiteData();
         $resp['prefix'] = getParameter("prefix");
         $resp['wdname'] = getParameter("wdname");
         if($resp['wdname']==NULL) $resp['wdname'] = getParameter("wd_id");
         $wdata = $wd->getWebData($resp['wdname']);
         $resp['origemail'] = getParameter("origemail");
         $resp['wd_id'] = $wdata['wd_id'];
         //print_r($wdata);
         $trow = $wd->getRow($resp['wd_id'],NULL,$resp['origemail']);
         $resp['row'] = $trow;
         if($trow!=NULL && $trow['wd_row_id']>0) {
            $resp['responsecode'] = 1;
         }
   
      } else if (0==strcmp($action,"removeforeignlink")) {
         // Need all of the following input to verify the row before the linkage can be broken
         // This will not remove the row, but rather the linkage only
         $wd = new WebsiteData();
         $ua = new UserAcct();
         
         $resp['linkid'] = getParameter("linkid");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $resp['origemail'] = getParameter("origemail");
         //$resp['userid'] = getParameter("userid");
         //$resp['token'] = getParameter("token");
         
         //$user = $ua->getUser($resp['userid']);
         //if (0==strcmp($user['token'],$resp['token'])) {
            $success = $wd->removeForeignSurveyLink($resp['wd_id'],$resp['wd_row_id'],$resp['origemail'],$resp['linkid']);
            if($success) {
               //Remove its externalid as well
               $wd->updateFieldValue($resp['wd_id'],$resp['wd_row_id'],"originalwdfield_externalid","",TRUE,TRUE,$resp['userid']);
               $resp['responsecode'] = 1;
            }
         //}
      } else if (0==strcmp($action,"submitwd")) {
         $printdebug = (getParameter("testing")==1);
         
         // 160909: latest submit to utilize all the latest changes in submit survey function
         //   function submitSurvey($wd_id=NULL,$wd_row_id=NULL, $updateStatus=true, $lastupdateby=NULL,$ignorenull=FALSE,$section=-1,$sendemail=TRUE,$force=TRUE, $origemail=NULL, $printdebug=FALSE) {
         $wd = new WebsiteData();
         $resp['responsecode'] = 0;
         
         $resp['wdname'] = getParameter("wdname");
         if($resp['wdname']==NULL) $resp['wdname'] = getParameter("wd_id");
         $wdata = $wd->getWebData($resp['wdname']);
         $resp['wd_id'] = $wdata['wd_id'];
         
         if($printdebug) print "<br>\nwd_id: ".$resp['wd_id'];
         
         if($wdata!=NULL && $wdata['wd_id']>0) {
            $ua = new UserAcct();
            $resp['userid'] = getParameter("userid");
            $resp['token'] = getParameter("token");
            $resp['origemail'] = getParameter("origemail");
            $resp['wd_row_id'] = getParameter("wd_row_id");
            if($printdebug) print "<br>\nuserid: ".$resp['userid']." token: ".$resp['token']." origemail: ".$resp['origemail']." wd_row_id: ".$resp['wd_row_id'];
                  
            $allowaccess = FALSE;
            $resp['wd_id'] = $wdata['wd_id'];
            
            if ($resp['wd_row_id']==NULL && $resp['token']!=NULL && 0==strcmp("222_315_2008_32477",$resp['token'])) {
               $allowaccess=TRUE;
            } else if($resp['userid']!=NULL && $resp['token']!=NULL){
               $user = $ua->getUser($resp['userid']);
               if (0==strcmp($user['token'],$resp['token'])) $allowaccess=TRUE;
            } else if($resp['origemail']!=NULL && $resp['wd_row_id']!=NULL){
               $trow = $wd->getRow($resp['wd_id'],NULL,$resp['origemail']);
               if($trow!=NULL && 0==strcmp($resp['wd_row_id'],$trow['wd_row_id'])) $allowaccess=TRUE;
            }
            
            if($allowaccess) {
               $resp['helpstr'] = "allowed access.";
               $force = (getParameter("force")==1);
               if($printdebug) print "<br>\n<br>\n============CALLING submitSurvey==============<br>\n<br>\n";
               $resp['wd_row_id'] = $wd->submitSurvey($resp['wd_id'],$resp['wd_row_id'],TRUE,NULL,TRUE,-1,FALSE,$force,$resp['origemail'],$printdebug);
               if($printdebug) print "<br>\n<br>\n============DONE CALLING submitSurvey==============<br>\n<br>\n";
               $trow = $wd->getRow($resp['wd_id'],$resp['wd_row_id']);
               $resp['origemail'] = $trow['origemail'];
               $resp['responsecode'] = 1;
               
               // Could be inner surveys wanting to submit their entries - if so, submit them
               $jsonarr = getParameter("jsonarr");
               if($jsonarr!=NULL && is_array($jsonarr)){
                  $resp['jsonresponses'] = array();
                  for($i=0;$i<count($jsonarr);$i++){
                     $recurl = getBaseURL()."jsfcode/jsoncontroller.php?";
                     $recurl .= $jsonarr[$i];
                     if($printdebug) print "<br>\n<br>\n============CALLING additional jsonarr[] submitSurvey==============<br>\nurl:".$recurl."<br>\n<br>\n";
                     $resp['jsonresponses'][$i] = requestJSON($recurl,FALSE,TRUE);
                     if($printdebug) print "<br>\n<br>\n============DONE CALLING additional jsonarr[] submitSurvey==============<br>\n<br>\n";
                  }
               }
               //print_r($resp);
            }
         }
   
      
      } else if (0==strcmp($action,"submitwebdata")) {
         //error_reporting(E_ALL);
         $ua = new UserAcct();
         $wdObj = new WebsiteData();
         $resp['admin'] = getParameter("admin");
         $resp['prefix'] = getParameter("prefix");
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $resp['origemail'] = getParameter("origemail");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['testing'] = getParameter("testing");
         if($resp['testing']==1) print "<br>wd_id: ".$resp['wd_id']." row: ".$resp['wd_row_id']." oe: ".$resp['origemail']."<BR>";
         if ($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
         if ($resp['wd_id']!=NULL) {
            $wdata = $wdObj->getWebData($resp['wd_id']);
            $resp['wd_id'] = $wdata['wd_id'];
         }
         
         $resp['userid'] = getParameter("userid");
         $user = array();
         if ($resp['userid']!=NULL) $user = $ua->getUser($resp['userid']);
         if($resp['wd_row_id']==NULL) $resp['newrecord'] = 1;
         //submitSurvey($wd_id=NULL,$wd_row_id=NULL, $updateStatus=true, $lastupdateby=NULL,$ignorenull=FALSE,$section=-1,$sendemail=TRUE,$force=TRUE, $origemail=NULL, $printdebug=FALSE)
         if($resp['testing']==1) print "<br>submitting survey...<BR>";
         $resp['wd_row_id'] = $wdObj->submitSurvey($resp['wd_id'],$resp['wd_row_id'],($resp['admin']!=1),$user['email'],(getParameter("ignorenull")==1),-1,TRUE,TRUE,NULL,(getParameter("testing")==1));
         if($resp['testing']==1) print "<br>returned from submitting survey...<BR>";
         if ($resp['wd_row_id']>0) {
            $resp['responsecode'] = 1;
            $wdtablejsonarr = getParameter("jsonarr");
            if($wdtablejsonarr!=NULL && is_array($wdtablejsonarr)){
               for($i=0;$i<count($wdtablejsonarr);$i++){
                  $recurl = getBaseURL()."jsfcode/jsoncontroller.php?";
                  $recurl .= $wdtablejsonarr[$i];
                  if($resp['newrecord']==1) $recurl .= "&o_wd_row_id=".$resp['wd_row_id'];
                  if($resp['testing']==1) print "<br>\nCalling URL:\n<br>".$recurl."<br>\n";
                  $recurl_result = requestJSON($recurl,FALSE,TRUE);
               }
            }
         } else if ($resp['wd_row_id']==-1) {
            $resp['responsecode'] = 0;
            $resp['error'] = "The password you entered was incorrect.";
         } else {
            $resp['responsecode'] = 0;
            $resp['error'] = "There was an error processing this request.  Please try again later.";
         }
         
      } else if (0==strcmp($action,"quicksubmitwebdata")) {      
         $resp['responsecode'] = 0;
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp("222_315_2008_32477",$token) || 0==strcmp($user['token'],$token)) {
            $wdObj = new WebsiteData();
            $resp['wdname'] = getParameter("wdname");
            if($resp['wdname']==NULL) $resp['wdname'] = getParameter("wd_id");
            $wdata = $wdObj->getWebDataByName($resp['wdname']);
            $resp['wd_id'] = $wdata['wd_id'];
            $resp['wd_row_id'] = getParameter("wd_row_id");
            $qs = $wdObj->getFieldLabels($wdata['wd_id'],TRUE);
            $query = "";
            foreach($qs as $key=>$val){
               $param = str_replace("\"","",str_replace("'","",str_replace(" ","_",strtolower(trim($key)))));
               $temp = getParameter($param);
               if($temp!=NULL) {
                  $query .= ",".$val."='".convertString($temp)."'";
               } else {
                  $temp = getParameter($param."_append");
                  if($temp!=NULL) {
                     $query .= ",".$val."=CONCAT(".$val.",'".convertString($temp)."')";
                  } else {
                     $temp = getParameter($val);
                     if($temp!=NULL) {
                        $query .= ",".$val."='".convertString($temp)."'";
                     } else {
                        $temp = getParameter($val."_append");
                        if($temp!=NULL) {
                           $query .= ",".$val."=CONCAT(".$val.",'".convertString($temp)."')";
                        }
                     }
                  }
               }
            }
            if(0!=strcmp($query,"")) {
               $resp['responsecode'] = 1;            
               $dbi = new MYSQLAccess();
               $dbquery = "";
               if($resp['wd_row_id']==NULL) {
                  $dbquery = "INSERT INTO wd_".$wdata['wd_id']." SET userid=".$userid.", created=NOW(), lastupdate=NOW()";
                  $dbquery .= $query;
                  $resp['wd_row_id'] = $dbi->insert($dbquery);
               } else {
                  if(getParameter("backup")==1) $wdObj->promoteRow($wdata['wd_id'], $resp['wd_row_id']);
                  
                  $dbquery = "UPDATE wd_".$wdata['wd_id']." SET lastupdate=NOW()";
                  $dbquery .= $query;
                  $dbquery .= " WHERE wd_row_id=".$resp['wd_row_id'];
                  $dbi->update($dbquery);
               }
            }
            //$resp['row'] = $wdObj->getDetailsClear($resp['wd_id'],$resp['wd_row_id']);
            $resp['row'] = $wdObj->getDetailsDecoded($resp['wd_id'],$resp['wd_row_id']);
         }
      } else if (0==strcmp($action,"submitwdfield")) {
         $wd_id = getParameter("wd_id");
         $wd_row_id = getParameter("wd_row_id");
         $origemail = getParameter("origemail");
         $userid = getParameter("userid");
         $token = getParameter("token");
         $field = getParameter("field");
         $value = getParameter("value");
         if($wd_id!=NULL && $wd_row_id!=NULL && $field!=NULL && $userid!=NULL && ($token!=NULL || $origemail!=NULL)) {
            if(getParameter("testing")==1) print "<br>\nAll parameters are good.<br>\n";
            $wdObj = new WebsiteData();
            $allowupdate = FALSE;
            $row = $wdObj->getRow($wd_id,$wd_row_id,$origemail);         
            if($token!=NULL) {
               $ua = new UserAcct();
               $user = $ua->getUser($userid);
               if(getParameter("testing")==1) {
                  print "<br>\ntoken specified: ".$token." userid: ".$userid."<br>\n";
                  print_r($user);
                  print "<br>\n";
               }
               if (0==strcmp($user['token'],$token) && ($ua->isUserAdmin($userid) || $userid==$row['userid'])) {
                  $allowupdate = TRUE;
               }
            } else {
               if(getParameter("testing")==1) print "<br>\origemail: ".$origemail."<br>\n";
               if($row['userid']!=NULL && $row['userid']==$userid) {
                  $allowupdate = TRUE;
               }
            }
            
            if($allowupdate) {
               if(getParameter("testing")==1) print "<br>\nAccess is good.<br>\n";
               $wdObj->updateFieldValue($wd_id,$wd_row_id,$field,$value);
               $resp['responsecode'] = 1;
            } else if(getParameter("testing")==1) {
               print "<br>\nAccess is not good.<br>\n";
            }
         }
      } else if (0==strcmp($action,"getwebdata")) {
         $ua = new UserAcct();
         $wd_id = getParameter("wd_id");
         $wdname = getParameter("wdname");
         $wdmsg = getParameter("wdmsg");
         //print "\n\n<br><br>resp wdmsg: ".$wdmsg']."<br><br>\n\n";
         $wd_row_id = getParameter("wd_row_id");
         $origemail = getParameter("origemail");
         
         $userid = getParameter("userid");
         $admin = getParameter("admin");
         if($admin==1 && $userid!=NULL) {
            $token = getParameter("token");
            $user = $ua->getUser($userid);
            if (0!=strcmp($user['token'],$token) && $ua->isUserAdmin($userid)) {
               $userid = NULL;
               $admin = 0;
               unset($_GET['userid']);
            }
         }
   
         $prefix = getParameter("prefix");
         $forcefull = getParameter("forcefull");
         if ($wd_id!=NULL || $wdname!=NULL) {
            $wdObj = new WebsiteData();
            //  http://www.plasticsmarkets.org/jsfcode/jsonpcontroller.php?action=getwebdata&callback=jsfwebdata_display&wdname=2015%2F16%20National%20Access%20Study&prefix=jsfwd&userid=1&wd_id=68&wd_row_id=5&forcefull=1
            if(getParameter("testing")==1) print "\n getting wdata form: getJSONForm(wd_id: ".$wd_id.", wdname: ".$wdname.", origemail: ".$origemail.", userid: ".$userid.", wd_row_id: ".$wd_row_id.", prefix: ".$prefix.",FALSE,NULL,FALSE,NULL,NULL,0,0,TRUE, admin: ".$admin.", forcefull: ".$forcefull.");\n\n";
            $resp = $wdObj->getJSONForm($wd_id,$wdname,$origemail,$userid,$wd_row_id,$prefix,(getParameter("testing")==1),NULL,FALSE,NULL,NULL,0,0,TRUE,$admin,$forcefull);
            //if(getParameter("testing")==1) {
            //   print "\n results from getting wdata form:\n";
            //   print_r($resp);
            //   print "\n\n";
            //}
            $resp['responsecode'] = 1;
            $resp['wd_row_id'] = $wd_row_id;
            $resp['wdname'] = $wdname;
            $resp['wdmsg'] = $wdmsg;
            $resp['prefix'] = $prefix;
         } else {
            $resp['responsecode'] = 0;
         }
      } else if (0==strcmp($action,"getwebdatasimple")) {
         $wd_id = getParameter("wd_id");
         $wdname = getParameter("wdname");
         $wd_row_id = getParameter("wd_row_id");
         $origemail = getParameter("origemail");
         $userid = getParameter("userid");
         $prefix = getParameter("prefix");
         $forcefull = getParameter("forcefull");
         $admin = getParameter("admin");
         if ($wd_id!=NULL || $wdname!=NULL) {
            $wdObj = new WebsiteData;
            $resp['responsecode'] = 1;
            $resp = $wdObj->getJSONForm($wd_id,$wdname,$origemail,$userid,$wd_row_id,$prefix,FALSE,NULL,FALSE,NULL,NULL,0,0,FALSE,0,$forcefull);
            $resp['prefix'] = $prefix;
         } else {
            $resp['responsecode'] = 0;
         }
      } else if (0==strcmp($action,"getwebdatapage")) {
         //error_reporting(E_ALL);
         $wd_id = getParameter("wd_id");
         $wdname = getParameter("wdname");
         $testing = (getParameter("testing")==1); 
         if ($testing) {
            //error_reporting(E_ALL);
            print "<br>\n";
            print date("m/d/Y H:i:s")." Start jsoncontroller API: getwebdatapage ";
            print "wd_id: ".$wd_id." wdname: ".$wdname." page: ".$page." prefix: ".$prefix;
            print "<br>\n";
         }
         if ($wd_id!=NULL || $wdname!=NULL) {
            $page = getParameter("page");
            $wd_row_id = getParameter("wd_row_id");
            $origemail = getParameter("origemail");
            $userid = getParameter("userid");
            $prefix = getParameter("prefix");
            $noemail = getParameter("noemail");
            $email = getParameter("email");
            $explicitcss = getParameter("explicitcss"); 
            $password = getParameter("password");
            $forcefull = getParameter("forcefull");
            $wdObj = new WebsiteData;
            $resp = $wdObj->getJSONForm($wd_id,$wdname,$origemail,$userid,$wd_row_id,$prefix,$testing,NULL,TRUE,$page,$password,$noemail,$explicitcss,TRUE,0,$forcefull,$email);
            if($wd_id!=NULL) $resp['wd_id'] = $wd_id;
            if($wd_row_id!=NULL) $resp['wd_row_id'] = $wd_row_id;
            if($origemail!=NULL) $resp['origemail'] = $origemail;
            if($email!=NULL) $resp['email'] = $email;
            $resp['responsecode'] = 1;
            $resp['prefix'] = $prefix;
         } else {
            $resp['responsecode'] = 0;
         }
         if ($testing) print "<br>\n".date("m/d/Y H:i:s")." END jsoncontroller API: getwebdatapage <br>\n";
      } else if (0==strcmp($action,"getwebdataspa")) {
         //error_reporting(E_ALL);
         $wdname = getParameter("wdname");
         if ($wdname!=NULL) {
            $userid = getParameter("userid");
            $prefix = getParameter("prefix");
            if($prefix==NULL) $prefix = "jsfwd";
            //disabled this for now...
            //$wdspaObj = new WDSinglePageApp();
            $resp['responsecode'] = 1;
            //$resp = $wdspaObj->getSPAJSONForm($wdname, $prefix, NULL, $userid);
            $resp['prefix'] = $prefix;
         } else {
            $resp['responsecode'] = 0;
         }
      } else if (0==strcmp($action,"getwebdatatables")) {
         $wd = new WebsiteData();
         $ua = new UserAcct();
         
         $resp['privatesrvy'] = getParameter("privatesrvy");
         $resp['userid'] = getParameter("userid");
         $token = getParameter("token");
         $user = NULL;
         if($resp['userid']!=NULL) $user = $ua->getUser($resp['userid']);
         
         $resp['searchtxt'] = getParameter("searchtxt");
         $resp['foruserid'] = getParameter("foruserid");
         $resp['limit'] = getParameter("limit");
         
         if ($user!=NULL && (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token))) {
            //print "\n<br>userid, token were fine\n<br>";
            $resp['credentials'] = "OK";
            $resp['isadmin'] = 0;
            if ($ua->isUserAdmin($user['userid'])) $resp['isadmin']=1;
         } else {
            $resp['credentials'] = "NONE";
            $resp['userid'] = NULL;
            $resp['foruserid'] = NULL;
         }
         
         $resp['responsecode'] = 1;
         // hashtags can be used/searched too below
         $resp['results'] = $wd->getWebTables($resp['userid'],$resp['privatesrvy'],TRUE,getParameter("externalid"),NULL,NULL,$resp['userid'],$resp['searchtxt'],$resp['limit']);
      } else if (0==strcmp($action,"searchwdrows")) {
         //error_reporting(E_ALL);
         $wd = new WebsiteData();
         //        function getRows($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $userid=NULL, $forCSV=FALSE, $pub=FALSE, $subforeignfields=FALSE, $ignoreSearchParams=FALSE, $shorteasy=FALSE, $qids=NULL, $page=1, $externalid=NULL, $adduser=FALSE) {
         $resp = $wd->getRows(getParameter("wd_id"), getParameter("orderby"), getParameter("limit"), getParameter("filterstr"),FALSE,getParameter("userid"),TRUE,FALSE,FALSE,FALSE,FALSE,NULL,1,getParameter("externalid"));
         unset($resp['query']);
         if ($resp['results']==NULL) unset($resp['results']);
         //print "\n<!-- response is: \n";
         //print_r($resp);
         //print " -->\n";
         
   
      } else if (0==strcmp($action,"removewdrow")) {
         $resp['wd_id'] = getParameter("wd_id");
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $wd = new WebsiteData();
         //print "<br>\n<br>\nwd_id: ".$resp['wd_id']." row: ".$resp['wd_row_id']."<br>\n<br>\n";
         $wd->removeRow($resp['wd_id'],$resp['wd_row_id']);
   
         $resp['enabledonly'] = getParameter("enabledonly");
         $resp['orderby'] = getParameter("orderby");
         $resp['limit'] = getParameter("limit");
         $resp['filterstr'] = getParameter("filterstr");
         $resp['maxcol'] = getParameter("maxcol");
         if ($resp['maxcol']==NULL) $resp['maxcol']=3;
   
         $wdata = $wd->getWebData($resp['wd_id']);
         $resp['responsecode'] = 1;
         //$qs = $wd->getFieldLabels($wdata['wd_id']);
         $qs = $wd->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
         $headers = $wd->getHeaderFields($wdata['wd_id']);
         if ($resp['enabledonly']==1 && isset($qs['enabled'])) {
            $paramname = "cmsz_w".$wdata['wd_id'].$qs['enabled'];
            $_SESSION['params'][$paramname] = "yes";
         }
         if ($resp['orderby']==NULL && isset($qs['sequence'])) $resp['orderby'] = "d.".$qs['sequence']." ASC";
         $results = $wd->getRows($wdata['wd_id'], $resp['orderby'], $resp['limit'], $resp['filterstr'],FALSE,NULL,TRUE);
         $rows = $results['results'];
         $resp['wd_id'] = $wdata['wd_id'];
         $resp['info'] = $wdata['info'];
         //$resp['qs'] = $qs;
         $resp['rows'] = array();
         for ($i=0;$i<count($rows); $i++) {
            $resp['rows'][$i] = array();
            foreach($qs as $key=>$value) {
               $key = str_replace(" ","",strtolower($key));
               $resp['rows'][$i][$key] = $rows[$i][$value];
            }
            $hdrcounter = 0;
            while($hdrcounter<$resp['maxcol']) {
               $fld = $headers[$hdrcounter]['field_id'];
               if ($fld!=NULL) $resp['rows'][$i]['display'] = "<div class=\"jsfwdtabledisp".$hdrcounter."\">".$rows[$i][$fld]."</div>";
               $hdrcounter++;
            }
            $resp['rows'][$i]['wd_row_id'] = $rows[$i]['wd_row_id'];
            $resp['rows'][$i]['userid'] = $rows[$i]['userid'];
         }
   //print "<br><br>\nwd rows:\n<br>";
   //print_r($resp['rows']);
   //print "\n\n<br><br>";
   
      } else if (0==strcmp($action,"getwdsearch")) {
         $wd = new WebsiteData();
         $resp['responsecode'] = 1;
         $resp['wd_id'] = getParameter("wd_id");
         //$resp['wdname'] = getParameter("wdname");
         //$resp['html'] = $wd->getSearchHTMLAllFields($resp['wdname'], $resp['wd_id'],NULL,TRUE);      
         $resp['html'] = $wd->getSearchHTMLAllFields(NULL, $resp['wd_id'],NULL,TRUE);      
         $resp['fields'] = $wd->getSearchFields($resp['wd_id']);
   
      } else if (0==strcmp($action,"getwdsavedstats")) {
         $wd = new WebsiteData();
   
         //$resp['width'] = getParameter("width");
         //if ($resp['width']==NULL) $resp['width']=300;
         $resp['userid'] = getParameter("userid");
         $resp['orgid'] = getParameter("orgid");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['responsecode'] = 1;
         $resp['statlist'] = $wd->displaySavedStatsList($resp['userid'],$resp['orgid'],$resp['wd_id']);
         
      } else if (0==strcmp($action,"getdynamicreportdata")) {
         $ua = new UserAcct();
         $sql = new MYSQLaccess();
         $wd = new WebsiteData();
         
         $resp['testing'] = getParameter("testing");
         if($resp['testing']==1) print "<br>\ngetdynamicreportdata();";
         
         $accesstokenrequired = TRUE;
         
         // report id should indicate which report
         $reportid = getParameter("reportid");
         
         // data id should indicate which specific saved search
         $dataid = getParameter("dataid");
         if($resp['testing']==1) print "<br>\nreportid: ".$reportid." dataid: ".$dataid;
         
         $token = getParameter("token");
         $userid = getParameter("userid");
         if($token != NULL) {
            if($userid!=NULL && $ua->isUserAdmin($userid)) {
               $user = $ua->getUser($userid);
               if (0==strcmp($user['token'],$token)) {
                  $accesstokenrequired = FALSE;
               }
            }
                  
            $reportswd = $wd->getWebData("Tools and Widgets Dynamic Reports");
            $reportsqs = $wd->getFieldLabels($reportswd['wd_id'],TRUE,TRUE);
            if($resp['testing']==1) {
               print "<br>\n";
               print "Report Questions:<br>\n";
               print_r($reportsqs);
               print "<br>\n";
               print "<br>\n";
            }
            $query = "SELECT * FROM wd_".$reportswd['wd_id']." WHERE ";
            if(is_numeric($reportid)) {
               $query .= "(wd_row_id=".$reportid." OR ".$reportsqs['reportid']."='".$reportid."') ";
            } else {
               $query .= $reportsqs['reportid']."='".$reportid."' ";
            }
            $query .= " AND LOWER(".$reportsqs['enabled'].")='yes' ";
            
            if($accesstokenrequired) {
               if(strlen($token)>7) $query .= " AND ".$reportsqs['accesstokens']." LIKE '%".$token."%' ";
               else $query .= " AND 1=0 ";
            }
            
            $query .= " ORDER BY ".$reportsqs['sequence']." LIMIT 0,1;";
            
            if($resp['testing']==1) print "<br>\nquery: ".$query;
            
            $results = $sql->queryGetResults($query);
            if($results!=NULL && count($results)>0) {
               $reportrow = $results[0];
               $params = "";
               if($dataid!=NULL) {
                  if($resp['testing']==1) print "<br>\na saved search was specified.";
                  // At this point report found, find specific saved search
                  $searchwd = $wd->getWebData("Tools and Widgets Dynamic Reports Saved");
                  $searchqs = $wd->getFieldLabels($searchwd['wd_id'],TRUE,TRUE);
                  $results2 = $wd->getForeignSurveyAnswers($searchwd['wd_id'],$reportswd['wd_id'],$reportsqs['savedsearch'],$reportrow['wd_row_id'],TRUE);
                  //$query = "SELECT w.*, l.linkid FROM wd_".$searchwd['wd_id']." w";
                  //$query .= ", (SELECT linkid,wd_row_id2 FROM wd_link WHERE wd_id1=".$reportswd['wd_id']." AND wd_row_id1=".$reportrow['wd_row_id']." AND field_id='".$reportsqs['savedsearch']."' AND wd_id2=".$searchwd['wd_id'].") l ";
                  //$query .= " WHERE ";
                  //$query .= " w.wd_row_id=l.wd_row_id2";
                  ////$query .= " w.externalid='".$reportswd['wd_id']."_".$reportsqs['savedsearch']."_".$reportrow['wd_row_id']."' ";
                  //$query .= " AND w.".$searchqs['dataid']."='".$dataid."' ";
                  //$query .= " AND LOWER(w.".$searchqs['enabled'].")='yes' ";
                  //$query .= " ORDER BY w.".$searchqs['sequence'];
                  //$query .= " LIMIT 0,1;";
                  //$results2 = $sql->queryGetResults($query);
                  if($results2!=NULL && count($results2)>0) {
                     $params = $results2[0][$seachqs['parameters']];
                  }
               }
               
               if(0==strcmp($reportrow[$reportsqs['type']],"Database") || 0==strcmp($reportrow[$reportsqs['type']],"Simple Data")) {
                  if($resp['testing']==1) print "<br>\nDatabase or Simple Data found...";
                  $searchwd = $wd->getWebData("Tools and Widgets Dynamic Report Search");
                  $searchqs = $wd->getFieldLabels($searchwd['wd_id'],TRUE,TRUE);
                  $results2 = $wd->getForeignSurveyAnswers($searchwd['wd_id'],$reportswd['wd_id'],$reportsqs['parameters'],$reportrow['wd_row_id'],TRUE);
                  
                  //$query = "SELECT w.*, l.linkid FROM wd_".$searchwd['wd_id']." w";
                  //$query .= ", (SELECT linkid,wd_row_id2 FROM wd_link WHERE wd_id1=".$reportswd['wd_id']." AND wd_row_id1=".$reportrow['wd_row_id']." AND field_id='".$reportsqs['parameters']."' AND wd_id2=".$searchwd['wd_id'].") l ";
                  //$query .= " WHERE ";
                  ////$query .= "externalid='".$reportswd['wd_id']."_".$reportsqs['parameters']."_".$reportrow['wd_row_id']."' ";
                  //$query .= " w.wd_row_id=l.wd_row_id2";
                  //
                  //if(isset($searchqs['enabled'])) $query .= " AND LOWER(".$searchqs['enabled'].")='yes' ";
                  //$query .= " ORDER BY ".$searchqs['sequence'].";";
                  //$results2 = $sql->queryGetResults($query);
                  
                  $statements = "";
                  $paramarr = separateStringBy($params,",",NULL,TRUE);
                  for($j=0;$j<count($paramarr);$j++) {
                     $keyval = separateStringBy($paramarr[$j],"=");
                     $key = trim($keyval[0]);
                     $val = trim(urldecode($keyval[1]));
                     for($i=0;$i<count($results2);$i++) {
                        $lookfor = "_".str_replace(".","_",str_replace(" ","",str_replace("#","",$results2[$i][$searchqs['param']])));
                        if(strpos($key,$lookfor)!==FALSE) {
                           if(0==strcmp($results2[$i][$searchqs['type']],"text")) {
                              $statements .= " AND LOWER(".$results2[$i][$searchqs['param']].") LIKE '%".strtolower($results2[$i][$searchqs['prefix']].$val)."%' ";
                           } else if(0==strcmp($results2[$i][$searchqs['type']],"opts") || 0==strcmp($results2[$i][$searchqs['type']],"json opts")) {
                              $tempopts = separateStringBy($val,";",NULL,TRUE);
                              $statements .= " AND (";
                              for($k=0;$k<count($tempopts);$k++){
                                 if($k>0) $statemnts .= " OR ";
                                 $statements .= $results2[$i][$searchqs['param']]."='".$tempopts[$k]."'";
                              }
                              $statements .= ") ";
                           } else if(0==strcmp($results2[$i][$searchqs['type']],"date")) {
                              if(strpos($key,"_end")!==FALSE) {
                                 $statements .= " AND ".$results2[$i][$searchqs['param']]."<='".$val."' ";
                              } else {
                                 $statements .= " AND ".$results2[$i][$searchqs['param']].">='".$val."' ";
                              }
                           } else {
                              $statements .= " AND LOWER(".$results2[$i][$searchqs['param']].")='".strtolower($results2[$i][$searchqs['prefix']].$val)."' ";
                           }
                        }
                     }
                  }
                  
                  
                  $query = "";
                  if(0==strcmp($reportrow[$reportsqs['type']],"Simple Data")) {
                     if($resp['testing']==1) print "<br>\nMore specifically, Simple Data found...";
                     $datawd = $wd->getWebData("Tools and Widgets Dynamic Reports Data");
                     $dataqs = $wd->getFieldLabels($datawd['wd_id'],TRUE,TRUE);
                     $query = "SELECT w.".$dataqs['ydisp']." as ydisp";
                     $query .= ", w.".$dataqs['xdisp']." as xdisp";
                     $query .= ", w.".$dataqs['val']." as val";
                     if(0==strcmp(strtolower($reportrow[$reportsqs['countonly']]),"yes")) {
                        $query = "SELECT count(*)";
                     }
                     $query .= " FROM wd_".$datawd['wd_id']." w";
                     $query .= ", (SELECT linkid,wd_row_id2 FROM wd_link WHERE wd_id1=".$reportswd['wd_id']." AND wd_row_id1=".$reportrow['wd_row_id']." AND field_id='".$reportsqs['simpledata']."' AND wd_id2=".$datawd['wd_id'].") l ";
                     $query .= " WHERE ";
                     //$query .= " externalid='".$reportswd['wd_id']."_".$reportsqs['simpledata']."_".$reportrow['wd_row_id']."' ";
                     $query .= " w.wd_row_id=l.wd_row_id2";
                     $query .= " AND LOWER(w.".$dataqs['enabled'].")='yes' ";
                     $query .= " AND w.dbmode<>'DELETED' ";
                     $query .= " ORDER BY w.".$dataqs['sequence'];
                  } else {
                     $query = str_replace(";","",convertBack($reportrow[$reportsqs['sql']]));
                  }
                  
                  $tquery = strtolower($query);
                  $tindexl = strrpos($tquery," limit ");
                  $tindexo = strrpos($tquery," order by ");
                  $tindexg = strrpos($tquery," group by ");
   
                  $tstart = $query;
                  $tend = "";
                  if($tindexg>=0) {
                     $tstart = substr($query,0,$tindexg);
                     $tend = substr($query,$tindexg);
                  } else if($tindexo>=0) {
                     $tstart = substr($query,0,$tindexo);
                     $tend = substr($query,$tindexo);
                  } else if ($tindexl>=0) {
                     $tstart = substr($query,0,$tindexl);
                     $tend = substr($query,$tindexl);
                  }
                  $runquery = str_replace("\n"," ",str_replace("<br>"," ",str_replace("<BR>"," ",$tstart." ".$statements." ".$tend)));
                  if($resp['testing']==1) print "<br>\ndb query: ".$runquery;
                  $finalresults = $sql->queryGetResults($runquery);
                  $resp['rows'] = $finalresults;
               } else {
                  if(0==strcmp($reportrow[$reportsqs['type']],"Website Data")) {
                     $reportrow[$reportsqs['json']] = "jsfcode/jsoncontroller.php?action=getwdreport";
                     $reportrow[$reportsqs['json']] .= "&wd_id=".urlencode($reportrow[$reportsqs['wdparam']]);
                     if($reportrow[$reportsqs['groupparam']]!=NULL) $reportrow[$reportsqs['json']] .= "&groupby=".urlencode($reportrow[$reportsqs['groupparam']]);
                     if($reportrow[$reportsqs['avgfld']]!=NULL) $reportrow[$reportsqs['json']] .= "&avgfld=".urlencode($reportrow[$reportsqs['avgfld']]);
                     if($reportrow[$reportsqs['orderparam']]!=NULL) $reportrow[$reportsqs['json']] .= "&orderparam=".urlencode($reportrow[$reportsqs['orderparam']]);
                     if($reportrow[$reportsqs['addlwhere']]!=NULL) $reportrow[$reportsqs['json']] .= "&addlwhere=".urlencode($reportrow[$reportsqs['addlwhere']]);
                     if($reportrow[$reportsqs['addlselect']]!=NULL) $reportrow[$reportsqs['json']] .= "&addlselect=".urlencode($reportrow[$reportsqs['addlselect']]);
                  }
                  if(0==strcmp(strtolower($reportrow[$reportsqs['countonly']]),"yes")) $reportrow[$reportsqs['json']] .= "&countonly=1";
                  $jsonurl = getBaseURL().$reportrow[$reportsqs['json']];
                  $uaccess = $ua->internalJSONaccess();
                  $jsonurl .= "&userid=".$uaccess['userid'];
                  $jsonurl .= "&token=".$uaccess['token'];
                  
                  $finalresults = requestJSON($jsonurl);
                  foreach($finalresults as $key => $val){
                     if(0!=strcmp($key,"userid") && 0!=strcmp($key,"token") && 0!=strcmp($key,"sql") && 0!=strcmp($key,"query")) {
                        $resp[$key] = $val;
                     }
                  }
               }
            
               $resp['responsecode'] = 1;
            }
         }
         
      } else if (0==strcmp($action,"newwdsavedstat")) {
         $wd = new WebsiteData();
         $resp['userid'] = getParameter("userid");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['field_id'] = getParameter("field_id");
         $resp['reportname'] = getParameter("reportname");
         $resp['params'] = getParameter("params");
         $resp['enabled'] = getParameter("enabled");
         $resp['sequence'] = getParameter("sequence");
         $resp['frequency'] = getParameter("frequency");
         $resp['responsecode'] = 1;
         $resp['id'] = $wd->addSavedStat($resp['userid'],$resp['wd_id'],$resp['field_id'],$resp['reportname'],$resp['params'],$resp['enabled'],$resp['sequence'],$resp['frequency']);		
         
      } else if (0==strcmp($action,"getwdstats")) {
         $ua = new UserAcct();
         $wd = new WebsiteData();
   
         $resp['responsecode'] = 0;
         $resp['width'] = getParameter("width");
         if ($resp['width']==NULL) $resp['width']=300;
         $resp['field_id'] = getParameter("field_id");
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $resp['title'] = getParameter("title");
         
         $resp['wd_id'] = getParameter("wd_id");
         if($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
         $wdata = NULL;
         $wdata = $wd->getWebData($resp['wd_id']);
         $resp['wd_id'] = $wdata['wd_id'];
         $resp['wdname'] = $wdata['name'];
   
         //first make sure it's a valid table
         if ($wdata!=NULL && $wdata['wd_id']!=NULL) {
            $allowaccess = TRUE;
   
            // if it's not a public table, we need to make sure the user is authenticated
            if ($wdata['privatesrvy']!=3) {
               $user = $ua->getUser($resp['userid']);
               if (0!=strcmp($user['token'],$resp['token'])) $allowaccess=FALSE;
            }
   
            if ($allowaccess) {
               // if it's not a public table, we need to authorize the user by passing the foruserid
               if ($wdata['privatesrvy']!=3 && $wdata['privatesrvy']!=7 && $wdata['privatesrvy']!=8 && !$ua->isUserAdmin($resp['userid'])) $resp['foruserid'] = $resp['userid'];
   
               $resp['filterstr'] = getParameter("filterstr");      
               $resp['responsecode'] = 1;
               $resp['wd_id'] = $wdata['wd_id'];
               $resp['name'] = $wdata['name'];
               $resp['info'] = $wdata['info'];
               
               $resp['stathtml'] = $wd->printSurveyGraph($resp['wd_id'],$resp['width'],$resp['filterstr'],$resp['field_id'],$resp['title']);
            } else {
               $resp['error'] = "You do not have authority to this table.";
            }
         } else {
            $resp['error'] = "The table you're trying to display is not available.";
         }
         unset($_SESSION['params']);
   
      } else if (0==strcmp($action,"getsinglewdrow")) {
         $ua = new UserAcct();
         $wd = new WebsiteData();
         $resp['responsecode'] = 0;
         $resp['wdname'] = getParameter("wdname");
         if($resp['wdname']==NULL) $resp['wdname'] = getParameter("wd_id");
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $user = array();
         if($resp['userid'] != NULL) $user = $ua->getUser($resp['userid']);
         if (0==strcmp("222_315_2008_32477",$resp['token']) || 0==strcmp($user['token'],$resp['token'])) {
            $wdata = $wd->getWebDataByName($resp['wdname']);
            $resp['wd_id'] = $wdata['wd_id'];
      
            //first make sure it's a valid table
            if ($wdata!=NULL && $wdata['wd_id']!=NULL) {
               $resp['wd_row_id'] = getParameter("wd_row_id");
               $resp['row'] = $wd->getDetailsClear($resp['wd_id'],$resp['wd_row_id']);
               $resp['responsecode'] = 1;
            }
         }
         
      } else if (0==strcmp($action,"getwdrows")) {
         $ua = new UserAcct();
         $wd = new WebsiteData();
         $resp['responsecode'] = 0;
         $resp['wdname'] = getParameter("wdname");
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $user = $ua->getUser($resp['userid']);
         if (0==strcmp($user['token'],$resp['token'])) {
            $wdata = $wd->getWebDataByName($resp['wdname']);
            $resp['wd_id'] = $wdata['wd_id'];
      
            //first make sure it's a valid table
            if ($wdata!=NULL && $wdata['wd_id']!=NULL) {
               $resp['foruserid'] = getParameter("foruserid");
               $resp['filterstr'] = getParameter("filterstr");
               $resp['orderby'] = getParameter("orderby");
               $resp['limit'] = getParameter("limit");
               $resp['countonly'] = getParameter("countonly");
               $resp['qids'] = getParameter("qids");
               $resp['page'] = getParameter("page");
               //getRows($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $userid=NULL, $forCSV=FALSE, $pub=FALSE, $subforeignfields=FALSE, $ignoreSearchParams=FALSE, $shorteasy=FALSE, $qids=NULL, $page=1)
               $results = $wd->getRows($wdata['wd_id'],$resp['orderby'],$resp['limit'],$resp['filterstr'],($resp['countonly']==1),$resp['foruserid'],FALSE,FALSE,FALSE,FALSE,FALSE,$resp['qids'],$resp['page']);
               $resp['responsecode'] = 1;
               $resp['rows'] = $results['results'];
            }
         }
         
      } else if (0==strcmp($action,"submitmultiple")) {      
         $jsonarr = getParameter("jsonarr");
         $resp['responses'] = array();
         if($jsonarr!=NULL && is_array($jsonarr)){
            $rowscounted = 0;
            $sqlupdates = array();
            for($i=0;$i<count($jsonarr);$i++){
               $recurl = getBaseURL()."jsfcode/jsoncontroller.php?";
               $recurl .= $jsonarr[$i];
               
               //specifically for submitting wd tables
               if(isset($resp['new_wd_row_id'])) $recurl .= "&o_wd_row_id=".$resp['new_wd_row_id'];
               
               if (getParameter("testing")==1) print "<br>\n".date("m/d/Y H:i:s")." SENDING URL: ".$recurl;
               //print "<br>\n".$recurl."\n<br>";
               $resp['responses'][$i] = requestJSON($recurl,(getParameter("testing")==1),TRUE);
               
               //if a new row was added to the outer survey, pass it along (also specific for wdtable submissions)
               if(isset($resp['responses'][$i]['new_wd_row_id'])) $resp['new_wd_row_id']=$resp['responses'][$i]['new_wd_row_id'];
            }
         }
         //print_r($resp);
         $resp['responsecode'] = 1;
         
      } else if (0==strcmp($action,"submitsinglewdrow")) {      
         $wd = new WebsiteData();
         $resp['responsecode'] = 0;
         
         $resp['wdname'] = getParameter("wdname");
         $resp['wd_id'] = getParameter("wd_id");
         if($resp['wdname']==NULL) $resp['wdname'] = $resp['wd_id'];
         $wdata = $wd->getWebData($resp['wdname']);
         
         if($wdata!=NULL && $wdata['wd_id']>0) {
            $ua = new UserAcct();
            $resp['userid'] = getParameter("userid");
            $resp['token'] = getParameter("token");
            $resp['origemail'] = getParameter("origemail");
            $resp['wd_row_id'] = getParameter("wd_row_id");
                  
            $allowaccess = FALSE;
            $resp['wd_id'] = $wdata['wd_id'];
            
            if ($resp['wd_row_id']==NULL && $resp['token']!=NULL && 0==strcmp("222_315_2008_32477",$resp['token'])) {
               $allowaccess=TRUE;
            } else if($resp['userid']!=NULL && $resp['token']!=NULL){
               $user = $ua->getUser($resp['userid']);
               if (0==strcmp($user['token'],$resp['token'])) $allowaccess=TRUE;
            } else if($resp['origemail']!=NULL && $resp['wd_row_id']!=NULL){
               $trow = $wd->getCodedRow($resp['wd_id'],$resp['origemail']);
               if($trow!=NULL && 0==strcmp($resp['wd_row_id'],$trow['wd_row_id'])) $allowaccess=TRUE;
            }
            
            if($allowaccess) {
               //print "allowed access.";
               $resp['wd_id'] = $wdata['wd_id'];
               //$resp['wd_row_id'] = getParameter("wd_row_id");
               $qs = $wd->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
                           
               $query = "";
               foreach($qs as $key=>$val){
                  //print "\n\n<br><br>";
                  //print $key.": ".$val;
                  //print "\n\n<br><br>";
                  $param = str_replace("\"","",str_replace("'","",str_replace(" ","_",strtolower(trim($key)))));
                  $temp = getParameter($param);
                  if($temp==NULL) $temp = getParameter($val);
                  if($temp!=NULL) {
                     $query .= ",".$val."='".convertString($temp)."'";
                  } else {
                     $temp = getParameter($param."_append");
                     if($temp==NULL) $temp = getParameter($val."_append");
                     if($temp!=NULL) {
                        $query .= ",".$val."=CONCAT(IFNULL(".$val.",' '),'".convertString($temp)."')";
                     } else {
                        $temp = getParameter("w".$wdata['wd_id']."a".$param);
                        if($temp==NULL) $temp = getParameter("w".$wdata['wd_id']."a".$val);
                        if($temp!=NULL) {
                           $query .= ",".$val."='".convertString($temp)."'";
                        }
                     }
                  }
                  //print "\n\n<br><br>";
                  //print $query;
                  //print "\n\n<br><br>";
               }
               
               $temp = getParameter("comments");
               if($temp!=NULL) $query .= ",comments='".convertString(trim($temp))."'";
               
               //print "#2<br>\n";
               //print_r($resp);
      
               if(trim($query)!=NULL && 0!=strcmp($query,"") && strlen($query)>4) {
                  
                  // If the row hasn't been created yet, do it now
                  if($resp['wd_row_id']==NULL) {
                     $resp['wd_row_id'] = $wd->addRow($wdata['wd_id']);
                     $resp['added'] = $resp['wd_row_id'];
                     
                     $resp['origemail'] = getRandomNum();
                     $query .= ",origemail='".$resp['origemail']."'";
                  } else {
                     $query .= ",dbmode='UPDATED'";
                  }
                  
                  if($resp['userid']!=NULL) $query .= ",userid=".$resp['userid'];
                  
                  $temp = getParameter("externalid");         
                  $o_wd_id = getParameter("o_wd_id");
                  $o_field_id = getParameter("o_field_id");
                  $o_wd_row_id = getParameter("o_wd_row_id");
                  if(getParameter("wd_externalsurvey")==1 && $o_wd_id!=NULL && $o_field_id!=NULL) {
                     $o_wdata = $wd->getWebData($o_wd_id);
                     $o_wd_id = $o_wdata['wd_id'];
                     $tqs = $wd->getFieldLabels($o_wd_id,TRUE,TRUE);
                     $o_field_id = $tqs[$o_field_id];
                     if($o_wd_row_id==NULL){
                        //create a new row, and return the row id in response
                        $o_wd_row_id = $wd->addRow($o_wd_id);
                        $resp['new_wd_row_id'] = $o_wd_row_id;
                     }
                     if($temp==NULL) $temp = $o_wd_id."_".$o_field_id."_".$o_wd_row_id;
                     $wd->addForeignSurveyLink($o_wd_id,$o_field_id,$o_wd_row_id,$wdata['wd_id'],$resp['wd_row_id']);
                  }
                  if($temp!=NULL) $query .= ",externalid='".convertString($temp)."'";
   
                  
                  $resp['responsecode'] = 1;            
                  $dbi = new MYSQLAccess();
                  $dbquery = "";
                  if(getParameter("backup")==1) $wd->promoteRow($wdata['wd_id'], $resp['wd_row_id']);
                     
                  $dbquery = "UPDATE wd_".$wdata['wd_id']." SET lastupdate=NOW()";
                  $dbquery .= $query;
                  $dbquery .= " WHERE dbmode<>'DELETED' AND wd_row_id=".$resp['wd_row_id'];
                  $dbi->update($dbquery);
                  $resp['updated'] = $resp['wd_row_id'];
                  
                  $resp['query'] = $dbquery;
               }
      
               //print "#4<br>\n";
               //print_r($resp);
      
               //$resp['row'] = $wd->getDetailsClear($resp['wd_id'],$resp['wd_row_id']);
               //$resp['row'] = $wd->getDetailsDecoded($resp['wd_id'],$resp['wd_row_id']);
            }
         }   
      
      } else if (0==strcmp($action,"deletesinglewdrow")) {
         $ua = new UserAcct();
         $wd = new WebsiteData();
         $resp['responsecode'] = 0;
         $resp['testing'] = getParameter("testing");
         $resp['wdname'] = getParameter("wdname");
         if($resp['wdname']==NULL) $resp['wdname'] = getParameter("wd_id");
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $resp['origemail'] = getParameter("origemail");
         $resp['wd_row_id'] = getParameter("wd_row_id");
         
         $allowaccess = FALSE;
         $wdata = $wd->getWebDataByName($resp['wdname']);
         $resp['wd_id'] = $wdata['wd_id'];
         
         if($resp['testing']==1) {
            print "\n<br>Before check:<br>\n";
            print_r($resp);
            print "\n<br>";
         }
         
         if ($resp['token']!=NULL && 0==strcmp("222_315_2008_32477",$resp['token'])) {
            $allowaccess=TRUE;
            if($resp['testing']==1) print "<br>\nspecial token found, allowing access<br>\n";
         } else if($resp['userid']!=NULL && $resp['token']!=NULL){
            $user = $ua->getUser($resp['userid']);
            if (0==strcmp($user['token'],$resp['token'])) {
               $allowaccess=TRUE;
               if($resp['testing']==1) print "<br>\nuser and token match, we are good to allow access<br>\n";
            } else {
               if($resp['testing']==1) print "<br>\nuser and token do not match, not allowing access<br>\n";
            }
         } else if($resp['origemail']!=NULL && $resp['wd_row_id']!=NULL){
            $trow = $wd->getCodedRow($resp['wd_id'],$resp['origemail']);
            if($trow!=NULL && 0==strcmp($resp['wd_row_id'],$trow['wd_row_id'])) {
               $allowaccess=TRUE;
               if($resp['testing']==1) print "<br>\norigemail match, we are good to allow access<br>\n";
            } else {
               if($resp['testing']==1) print "<br>\norigemail does not match, not allowing access<br>\n";
            }
         }
         
         if($allowaccess && $wdata!=NULL && $wdata['wd_id']!=NULL && $resp['wd_row_id']!=NULL) {
            $wd->removeRow($resp['wd_id'],$resp['wd_row_id']);
            $resp['responsecode'] = 1;
            $resp['removed'] = $resp['wd_row_id'];
         }
         
      } else if (0==strcmp($action,"refreshinnerhybrid")) {
         //error_reporting(E_ALL);
         $wd = new WebsiteData();
         $resp['html'] = "";
         $resp['responsecode'] = 1;
         $resp['wd_id'] = getParameter("wd_id");
         if($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
         $wdata = $wd->getWebData($resp['wd_id']);
         
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $resp['field_id'] = getParameter("field_id");
         //$resp['divid'] = getParameter("divid");
   
         $temp = $wd->getInnerSurveyDisplayHybrid($wdata['wd_id'],NULL,$resp['field_id'],$resp['wd_row_id'],$resp['divid']);
         if($temp!=NULL) $resp['html'] = $temp['html'];
         
      } else if (0==strcmp($action,"displayinnersurvey")) {
         $wd = new WebsiteData();
         $resp['html'] = "";
         $resp['responsecode'] = 1;
         $resp['wd_id'] = getParameter("wd_id");
         if($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
         $wdata = $wd->getWebData($resp['wd_id']);
         
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $resp['field_id'] = getParameter("field_id");
         //$resp['divid'] = getParameter("divid");
   
         $temp = $wd->getInnerSurveyDisplay($wdata['wd_id'],NULL,$resp['field_id'],$resp['wd_row_id'],$resp['divid'],FALSE);
         if($temp!=NULL) $resp['html'] = $temp['str'];
         
      } else if (0==strcmp($action,"displaywdinputrows")) {
         $wd = new WebsiteData();
         $resp['html'] = "";
         $resp['responsecode'] = 1;
         $resp['wd_id'] = getParameter("wd_id");
         if($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
         $wdata = $wd->getWebData($resp['wd_id']);
         
         $resp['wd_row_id'] = getParameter("wd_row_id");
         $resp['field_id'] = getParameter("field_id");
         //$resp['divid'] = getParameter("divid");
         
         //$resp['rows_count'] = getParameter("rows_count");
         //$resp['rows_ids'] = getParameter("rows_ids");
         
         //$resp['ans'] = getParameter("ans");
   
         //$resp['calling_wd_id'] = getParameter("calling_wd_id");
         //$resp['calling_row_id'] = getParameter("calling_row_id");
         //$resp['calling_field_id'] = getParameter("calling_field_id");
         //$prefix = "";
         
         //$resp['html'] = $wd->getTableDisplayBody($wdata['wd_id'],$resp['ans'],$prefix,$labels=NULL,$maxrows=0);
         
         //$temp = $wd->getTableDisplay($wdata['wd_id'],NULL,$resp['field_id'],$resp['wd_row_id'],$resp['divid']);
         $temp = $wd->getInnerSurveyDisplay($wdata['wd_id'],NULL,$resp['field_id'],$resp['wd_row_id'],$resp['divid'],TRUE,(getParameter("spacesaver")==1));
         if($temp!=NULL) $resp['html'] = $temp['str'];
         
      } else if (0==strcmp($action,"getwdreport")) {
         $wd_id = getParameter("wd_id");
         $resp['wd_id'] = $wd_id;
         $avgfld = getParameter("avgfld");
         $countonly = getParameter("countonly");
         $groupby = getParameter("groupby");
         $orderby = getParameter("orderby");
         $addlwhere = getParameter("addlwhere");
         $addlselect = getParameter("addlselect");
         $wd = new WebsiteData();
         $wdresp = $wd->getAverageSQL($wd_id,$avgfld,$groupby,$orderby,$addlselect);
         
         $query = "SELECT ".$wdresp['select'];
         if($countonly==1) $query = "SELECT count(*)";
         $query .= " FROM ".$wdresp['from'];
         $query .= " WHERE ".$wdresp['where'];
         if($addlwhere!=NULL) $query .= " ".$addlwhere;
         if($wdresp['groupby']!=NULL) $query .= " GROUP BY ".$wdresp['groupby'];
         if($countonly!=1 && $wdresp['orderby']!=NULL) $query .= " ORDER BY ".$wdresp['orderby'];
         
         $dbLink = new MYSQLaccess();
         $results = $dbLink->queryGetResults($query);
         
         $resp['count'] = count($results);
         if($countonly==1) $resp['count'] = $results[0]['count(*)'];
         $resp['results'] = $results;
         $resp['query'] = $query;
         
      } else if (0==strcmp($action,"getqopts")) {
         $wd_id = getParameter("wd_id");
         $field = getParameter("field");
         if($field==NULL) $field = getParameter("field_id");
         $resp['wd_id'] = $wd_id;
         $resp['field'] = $field;
         $wd = new WebsiteData();
         $wdata = $wd->getWebData($wd_id);
         $resp['webdata'] = $wdata;
         $mf = $wd->getFieldsMultiIndex($wdata['wd_id']);
         $q = $mf['indexed'][$mf['bylabel'][$field]];
         $resp['field'] = $q;
         $temp = $wd->getdropdownoptions($q);
         $rows = array();
         $indexed = array();
         for($i=0;$i<count($temp['names']);$i++) {
            $tobj = array();
            $tobj['name'] = $temp['names'][$i];
            $tobj['value'] = $temp['values'][$i];
            $tobj['descr'] = $temp['descr'][$i];
            $rows[] = $tobj;
            $indexed[$temp['values'][$i]] = $temp['names'][$i];
         }
         $resp['rows'] = $rows;
         $resp['indexed'] = $indexed;
         $resp['resonpsecode'] = 1;
         
      } else if (0==strcmp($action,"getnvpuniquenames")) {
         $resp['resonpsecode'] = 0;
         
         $wd_id = getParameter("wd_id");
         $namefilter = getParameter("namefilter");
         $nobackups = getParameter("nobackups");
         $reducebackups = getParameter("reducebackups");
         $onlybackups = getParameter("onlybackups");
         
         $resp['wd_id'] = $wd_id;
         $resp['namefilter'] = $namefilter;
         $resp['orderby'] = getParameter("orderby");
         
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if ($ua->isUserAdmin($user['userid']) && 0==strcmp($user['token'],$token) ) {
            $wd = new WebsiteData();
            $wdata = $wd->getWebData($wd_id);
            
            if($wdata!=NULL && $wdata['wd_id']!=NULL) {
               $qs = $wd->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
               
               $orderby = "ORDER BY ".$qs['name'];
               
               $query = "";
               $query .= "SELECT DISTINCT ".$qs['name']." as name ";
               $query .= "FROM wd_".$wdata['wd_id']." ";
               $query .= "WHERE LOWER(".$qs['enabled'].")='yes'";
               if($namefilter!=NULL) {
                  $query .= " AND LOWER(".$qs['name'].") ";
                  $query .= " LIKE '%".strtolower(trim($namefilter))."%'";
               }
               
               if($onlybackups==1) {
                  $t = getDateForDB();
                  $query .= " AND ".$qs['name']." LIKE '%\_________backup' ";
               } else if($nobackups==1) {
                  $t = getDateForDB();
                  $query .= " AND ".$qs['name']." NOT LIKE '%\_________backup' ";
               } else if($reducebackups==1) {
                  $t = getDateForDB();
                  $query .= " AND (";
                  $query .= $qs['name']." NOT LIKE '%\_________backup' ";
                  $query .= "OR ".$qs['name']." LIKE '%\_".substr($t,0,4).substr($t,5,2)."__backup'";
                  $query .= ")";
               }
               
               if(0==strcmp($resp['orderby'],"createddesc") || 0==strcmp($resp['orderby'],"created")) {
                  $orderby = "ORDER BY created DESC";
               } else if(0==strcmp($resp['orderby'],"createdasc")) {
                  $orderby = "ORDER BY created ASC";
               }
               
               $query .= " ".$orderby.";";
               
               $dbi = new MYSQLAccess();
               $results = $dbi->queryGetResults($query);
               
               $newresults = array();
               for($i=0;$i<count($results);$i++) {
                  $newresults[] = $results[$i]['name'];
               }
               
               $resp['responsecode'] = 1;
               $resp['results'] = $newresults;
            }
         }
      } else if (0==strcmp($action,"getwdandrows")) {
         $time_start = microtime(true);
         
         //error_reporting(E_ALL);
         $ua = new UserAcct();
         $wd = new WebsiteData();
   
         //$resp['responsecode'] = 0;
         $resp['addfiltering'] = getParameter("addfiltering");
         $resp['autosearch'] = getParameter("autosearch");
         $resp['addrowdisplay'] = getParameter("addrowdisplay");
         $resp['testing'] = getParameter("testing");
         if($resp['testing']==1) error_reporting(E_ALL);
         $resp['wd_id'] = getParameter("wd_id");
         $resp['wdname'] = getParameter("wdname");
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $resp['adduser'] = getParameter("adduser");
         //$resp['divid'] = getParameter("divid");
   
         // Saving this info just so we pass it back to requester
         $resp['o_wd_row_id'] = getParameter("o_wd_row_id");
         $resp['o_wd_id'] = getParameter("o_wd_id");
         $resp['o_field_id'] = getParameter("o_field_id");
         
         if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: Attempting to get webdata";
         
         $wdata = NULL;
         if ($resp['wd_id']==NULL) $resp['wd_id'] = $resp['wdname'];
         if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: BEFORE: wd_id: ".$resp['wd_id']." wdname: ".$resp['wdname']." userid: ".$resp['userid']." token: ".$resp['token']."<br>\n";
         $wdata = $wd->getWebData($resp['wd_id'],FALSE,FALSE,FALSE,TRUE);
         
         //print "<BR><BR>\nwebdata:<br>\n";
         //print_r($wdata);
         //print "<BR><BR>\n\n";
         
         $resp['wdname'] = $wdata['name'];
         $resp['wd_id'] = $wdata['wd_id'];
         $resp['shortname'] = $wdata['shortname'];
         if($resp['shortname']==NULL) $resp['shortname'] = strtolower(removeSpecialChars($wdata['name']));
   
         if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: AFTERWARDS: wd_id: ".$resp['wd_id']." wdname: ".$resp['wdname']." userid: ".$resp['userid']." token: ".$resp['token']."<br>\n";
         
         //first make sure it's a valid table
         if ($wdata!=NULL && $wdata['wd_id']!=NULL) {
            $allowaccess = TRUE;
   
            // if it's not a public table, we need to make sure the user is authenticated
            if ($wdata['privatesrvy']!=3) {
               $user = $ua->getUser($resp['userid']);
               if (0!=strcmp($user['token'],$resp['token'])) $allowaccess=FALSE;
            }
   
            if ($allowaccess) {
               if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: Allowing access to webdata";
               // if it's not a public table, we need to authorize the user by passing the foruserid
               if ($wdata['privatesrvy']!=3 && $wdata['privatesrvy']!=7 && $wdata['privatesrvy']!=8 && !$ua->isUserAdmin($resp['userid'])) $resp['foruserid'] = $resp['userid'];
               else $resp['foruserid'] = getParameter("foruserid");
               
               if ($resp['userid']!=NULL && $ua->isUserAdmin($resp['userid'])) $resp['isadmin']=1;
   
               $multiflds = $wd->getFieldsMultiIndex($wdata['wd_id']);
               $qs = $multiflds['bylabel'];
               $headers = $multiflds['headers'];
               
               if($resp['addfiltering']==1) {
                  //print "<br>\n Adding filtering";
                  $filterfields = $wd->getSearchFilters($wdata['wd_id'],$multiflds['filters'],FALSE,TRUE,FALSE,($resp['autosearch']==1));
                  if ($filterfields!=NULL && $filterfields['filterhtml']!=NULL) {
                     $resp['filterhtml'] = $filterfields['filterhtml'];
                     $resp['filterinit'] = $filterfields['filterinit'];
                     $resp['filterget'] = $filterfields['filterget'];
                     $resp['filtercount'] = $filterfields['filtercount'];
                  }
                  //print "<br>\n<br>\nFilters:<br>\n";
                  //print_r($resp['filterhtml']);
                  //print "<br>\n<br>\nFilters init:<br>\n";
                  //print_r($resp['filterinit']);
                  //print "<br>\n<br>\nFilter get:<br>\n";
                  //print_r($resp['filterget']);
                  //print "<br>\n<br>\nFilter Count:<br>\n";
                  //print_r($resp['filtercount']);
                  //print "<br>\n<br>\n";
               }
               if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: after filter fields retreived";
   
               $resp['countonly'] = getParameter("countonly");
               $resp['enabledonly'] = getParameter("enabledonly");
               $resp['orderby'] = getParameter("orderby");
               $resp['limit'] = getParameter("limit");
               $resp['page'] = getParameter("page");
               $resp['random'] = getParameter("random");
               $resp['filterstr'] = getParameter("filterstr");
               $resp['maxcol'] = getParameter("maxcol");
               if ($resp['maxcol']==NULL) $resp['maxcol']=3;
               
               if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: limit: ".$resp['limit'];
         
               $resp['responsecode'] = 1;
               //$qs = $wd->getFieldLabels($wdata['wd_id'],true);
               //$headers = $wd->getHeaderFields($wdata['wd_id']);
               if ($resp['enabledonly']==1 && isset($qs['enabled'])) {
                  $paramname = "cmsz_w".$wdata['wd_id'].$qs['enabled'];
                  $_SESSION['params'][$paramname] = "yes";
               }
               
               /* This is now being handled by the server, not the controller
               if ($resp['orderby']==NULL && isset($qs['sequence'])) $resp['orderby'] = "d.".$qs['sequence']." ASC";
               else if ($resp['orderby']==NULL && isset($qs['recorddate'])) $resp['orderby'] = "d.".$qs['recorddate']." DESC";
               else if ($resp['orderby']==NULL) $resp['orderby'] = "d.created DESC";
               */
               
               $countresults = NULL;
               if($resp['countonly']!=1 && $resp['limit']==1 && $resp['random']!=1) {
                  $resp['totalrows'] = 1;
               } else {
                  $countresults = $wd->getRows($wdata['wd_id'], NULL, NULL, $resp['filterstr'],TRUE,$resp['foruserid'],TRUE);
                  $resp['totalrows'] = $countresults['results'][0]['count(*)'];
               }
               
               if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: # of rows: ".$resp['totalrows'];
               
               if($resp['page']==NULL) $resp['page'] = 1;
               if($resp['random']==1) {
                  $temp = ceil($resp['totalrows'] / $resp['limit']);
                  $resp['page'] = rand(1,$temp);
               }
               
               if($resp['countonly']!=1) {
                  //function getRows($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $userid=NULL, $forCSV=FALSE, $pub=FALSE, $subforeignfields=FALSE, $ignoreSearchParams=FALSE, $shorteasy=FALSE, $qids=NULL, $page=1, $externalid=NULL, $adduser=FALSE, $printdebug=FALSE) {
                  $results = $wd->getRows($wdata['wd_id'], $resp['orderby'], $resp['limit'], $resp['filterstr'],FALSE,$resp['foruserid'],TRUE,FALSE,FALSE,FALSE,FALSE,NULL,$resp['page'],NULL,($resp['adduser']==1),($resp['testing']==1));
                  $rows = $results['results'];
                  $resp['fieldsubs'] = $results['fieldsubs'];
                  $resp['fields'] = $results['fields'];
                  $resp['fieldsbyname'] = $results['fieldsbyname'];
               }
               if($resp['testing']==1) {
                  print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." results obtained.";
                  print "<br>\nOrder by: ".$resp['orderby'];
                  print "<br>\nLimit: ".$resp['limit'];
                  //print "<br>Results:<br>\n\n";
                  //print_r($results);
                  print "<br><br>\n\n";
               }
               $resp['wd_id'] = $wdata['wd_id'];
               $resp['info'] = $wdata['info'];
               $resp['htags'] = $wdata['htags'];
               //$resp['qs'] = $qs;
               //$resp['query'] = $results['query'];
               $resp['rows'] = array();
               $inputrowdisplayjs = "";
               $resp['inputdisplayrow'] = "";
               for ($i=0;$i<count($rows); $i++) {
                  $resp['rows'][$i] = array();
                  if($rows[$i]['userid']==NULL || $rows[$i]['userid']==$resp['userid']) $resp['rows'][$i]['origemail'] = $rows[$i]['origemail'];
                  $resp['rows'][$i]['wd_row_id'] = $rows[$i]['wd_row_id'];
                  $resp['rows'][$i]['userid'] = $rows[$i]['userid'];
                  $resp['rows'][$i]['created'] = $rows[$i]['created'];
                  if(isset($rows[$i]['complete'])) $resp['rows'][$i]['complete'] = $rows[$i]['complete'];               
                  foreach($qs as $key=>$value) {
                     $key = str_replace(" ","",strtolower(strip_tags($key)));
                     if(isset($rows[$i][$value."_userdata"]) && is_array($rows[$i][$value."_userdata"])) $resp['rows'][$i][$key."userdata"] = $rows[$i][$value."_userdata"];
                     $resp['rows'][$i][$key] = convertBack($rows[$i][$value]);
                  }
                  if(isset($rows[$i]['userdata'])) $resp['rows'][$i]['userdata'] = $rows[$i]['userdata'];
                  
                  if($resp['addrowdisplay']==1) {
                     if($wdata['rowdisplay']==NULL) {
                        //print "<br><br>chad was here 1<br><br>";
                        
                        $hdrcounter = 0;
                        $resp['rows'][$i]['display'] = "<div class=\"jsfwdtablerowdisp\">";
                        $tempdisplay = "";
                        while($hdrcounter<$resp['maxcol'] && $hdrcounter<count($headers)) {
                           if (0==strcmp(strtolower($headers[$hdrcounter]['label']),"status")) {
                               $fld = $headers[$hdrcounter]['field_id'];
                               if ($fld!=NULL) {
                                 $showcolor = strtolower(trim($rows[$i][$fld]));
                                 $questionList = trim(convertBack($headers[$hdrcounter]['question']));
                                 $bothnvp = explode(";",$questionList);
                                 $names = explode(",",$bothnvp[0]);
                                 $values = explode(",",$bothnvp[1]);
                                 for($j=0;$j<count($names);$j++) {
                                    if(0==strcmp($showcolor,strtolower(trim($names[$j])))) {
                                       if($values[$j]!=NULL) $showcolor = strtolower(trim($values[$j]));
                                    }
                                 }
                                  
                                 $tempdisplay = "<div style=\"float:left;margin-top:5px;margin-left:8px;margin-right:8px;width:12px;height:12px;border:1px solid #CCCCCC;border-radius:6px;background-color:".$showcolor.";overflow:hidden;\"></div>".$tempdisplay;
                               }
                           } else {
                               $fld = $headers[$hdrcounter]['field_id'];
                               if ($fld!=NULL) {
                                  $disp = $rows[$i][$fld];
                                  if(0==strcmp($headers[$hdrcounter]['field_type'],"FOREIGN") || 0==strcmp($headers[$hdrcounter]['field_type'],"FOREIGNCB")) {
                                     $disp = $wd->convertForeignWD($headers[$hdrcounter]['question'],$disp);
                                  }
                                  if(0==strcmp(strtolower(trim($headers[$hdrcounter]['label'])),"url") || 0==strcmp(strtolower(trim($headers[$hdrcounter]['map'])),"url")) {
                                     $disp = "<div style=\"cursor:pointer;color:blue;\" onclick=\"event.stopPropagation();window.open('".$disp."');\">".$disp."</div>";
                                  } else if (0==strcmp(substr(strtolower(trim($disp)),0,4),"http")) {
                                     $disp = "<div style=\"cursor:pointer;color:blue;\" onclick=\"event.stopPropagation();window.open('".$disp."');\">".$disp."</div>";
                                  }
                                  $tempdisplay .= "<div class=\"jsfwdtabledisp".$hdrcounter."\">".$disp."</div>";
                               }
                           }
                           $hdrcounter++;
                        }
                        $resp['rows'][$i]['display'] .= $tempdisplay."<div class=\"jsfwdtableexposedeletebtn2\">%%%DELETE%%%</div><div class=\"jsfwdtabledispend\"></div></div>";
                     } else {
                        //print "<br><br>chad was here 2<br><br>";
                        $resp['rows'][$i]['display'] = convertBack($wdata['rowdisplay']);
                        
                        //Initialize input row for this display
                        if($i==0) {
                           /*
                           $resp['inputdisplayrow'] = convertBack($wdata['rowdisplay']);
                           $wc = 0;
                           while($wc<50 && strpos($resp['inputdisplayrow'],"width:")!==FALSE) {
                              $st = strpos($resp['inputdisplayrow'],"width:");
                              $en = strpos($resp['inputdisplayrow'],";",$st);
                              if($st!==FALSE && $en!==FALSE && $st<$en) {
                                 $temp = substr($resp['inputdisplayrow'],0,$st).substr($resp['inputdisplayrow'],$en);
                                 $resp['inputdisplayrow'] = $temp;
                              } else {
                                 break;
                              }
                              $wc++;
                           }
                           */
                           $resp['inputdisplayrow'] = "<div style=\"margin:8px 1px 8px 2px;\">";
                           
                           $inputrowdisplayjs = "\n<script>\n";
                           $inputrowdisplayjs .= "function jsfwebdata_returnjsfchjprefixsub(jsondata){\n";
                           $inputrowdisplayjs .= "  //alert('Result of new row: ' + JSON.stringify(jsondata));\n";
                           $inputrowdisplayjs .= "  jsfwd_executefiltersearch();\n";
                           $inputrowdisplayjs .= "}\n";
                           $inputrowdisplayjs .= "function jsfwebdata_submitjsfchjprefixsub(){\n";
                           $inputrowdisplayjs .= "var url = '';\n";
                           $inputrowdisplayjs .= "url += defaultremotedomain + 'jsfcode/jsonpcontroller.php';\n";
                           $inputrowdisplayjs .= "url += '?action=submitwd';\n";
                           $inputrowdisplayjs .= "url += '&userid=' + jsfwd_userid;\n";
                           $inputrowdisplayjs .= "url += '&token=' + jsfwd_token;\n";
                           $inputrowdisplayjs .= "url += '&wd_id=".$resp['wd_id']."';\n";
                           $inputrowdisplayjs .= "url += '&callback=jsfwebdata_returnjsfchjprefixsub';\n";
                        }
                        
                        //Do work for common fields
                        $resp['rows'][$i]['display'] = str_replace("%%%userdata_fname%%%",$resp['rows'][$i]['userdata']['fname'],$resp['rows'][$i]['display']);
                        $resp['rows'][$i]['display'] = str_replace("%%%userdata_lname%%%",$resp['rows'][$i]['userdata']['lname'],$resp['rows'][$i]['display']);
                        $resp['rows'][$i]['display'] = str_replace("%%%userdata_email%%%",$resp['rows'][$i]['userdata']['email'],$resp['rows'][$i]['display']);
                        $resp['rows'][$i]['display'] = str_replace("%%%userdata_company%%%",$resp['rows'][$i]['userdata']['company'],$resp['rows'][$i]['display']);
   
                        //$resp['rows'][$i]['display'] = str_replace("%%%created%%%",$resp['rows'][$i]['created'],$resp['rows'][$i]['display']);
                        $resp['rows'][$i]['display'] = str_replace("%%%created%%%",date("m/d/Y",strtotime($resp['rows'][$i]['created'])),$resp['rows'][$i]['display']);
                        $resp['rows'][$i]['display'] = str_replace("%%%createdtime%%%",date("m/d/Y H:i:s",strtotime($resp['rows'][$i]['created'])),$resp['rows'][$i]['display']);
                        
                        //Do work for each field found in the row display string
                        foreach($qs as $key => $val) {
                           $sub = "%%%".strtolower(trim($key))."%%%";
                           if(strpos($resp['rows'][$i]['display'],$sub)!==FALSE) {
                              //save an input row
                              if($i==0) {                              
                                 $inputresp = $wd->getTableDisplayField(NULL,$multiflds['indexed'][$val],'jsfchjprefixsub',TRUE);
                                 //$resp['inputdisplayrow'] = str_replace($sub,$inputresp['str'],$resp['inputdisplayrow']);
                                 $resp['inputdisplayrow'] .= "<div style=\"float:left;margin-right:20px;\">".$inputresp['str']."</div>";
                                 $resp['inputdisplayrow'] .= "\n<script>\n";
                                 $resp['inputdisplayrow'] .= "if(jQuery('#cmsq_w".$wdata['wd_id'].$val."').length>0 && Boolean(jQuery('#cmsq_w".$wdata['wd_id'].$val."').val())) {\n";
                                 $resp['inputdisplayrow'] .= "   jQuery('#jsfchjprefixsub_a".$val."').val(jQuery('#cmsq_w".$wdata['wd_id'].$val."').val());\n";
                                 $resp['inputdisplayrow'] .= "}\n";
                                 $resp['inputdisplayrow'] .= "\n</script>\n";
                                 $inputrowdisplayjs .= $inputresp['js'];
                              }
                              
                              $disp = $resp['rows'][$i][$val];
                              //$disp = $multiflds['indexed'][$val]['field_type'];
                              if(0==strcmp($multiflds['indexed'][$val]['field_type'],"FOREIGN") || 0==strcmp($multiflds['indexed'][$val]['field_type'],"FOREIGNCB")) {
                                 //$disp = "chad: ".$disp;
                                 $disp = $wd->convertForeignWD($multiflds['indexed'][$val]['question'],$disp,FALSE);
                              } else if(0==strcmp($multiflds['indexed'][$val]['field_type'],"DROPDOWN") && 0==strcmp($multiflds['indexed'][$val]['map'],"status") && FALSE) {
                                 $disparr = separateStringBy(convertBack($multiflds['indexed'][$val]['question']),",",NULL,TRUE);
                                 $curri = -1;
                                 for($i=0;$i<count($disparr);$i++) {
                                    if(0==strcmp(strtolower(trim($disparr[$i])),strtolower(trim($disp)))) {
                                       $curri = $i + 1;
                                       break;
                                    }
                                 }
                                 if($curri<count($disparr)) {
                                    $disp = "<span onclick=\"\" style=\"color:blue\">";
                                    $disp .= "Move to ".$disparr[$curri];
                                    $disp .= "</span>";
                                 }
                              }
                              $resp['rows'][$i]['display'] = str_replace($sub,$disp,$resp['rows'][$i]['display']);
                           } else if ((0==strcmp($multiflds['indexed'][$val]['field_type'],'USERSRCH') || 0==strcmp($multiflds['indexed'][$val]['field_type'],'USERAUTO')) && strpos($resp['rows'][$i]['display'],"%%%".$key."_")!==FALSE) {
                              $resp['rows'][$i]['display'] = str_replace("%%%".$key."_fname%%%",$resp['rows'][$i][$val.'userdata']['fname'],$resp['rows'][$i]['display']);
                              $resp['rows'][$i]['display'] = str_replace("%%%".$key."_lname%%%",$resp['rows'][$i][$val.'userdata']['lname'],$resp['rows'][$i]['display']);
                              $resp['rows'][$i]['display'] = str_replace("%%%".$key."_email%%%",$resp['rows'][$i][$val.'userdata']['email'],$resp['rows'][$i]['display']);
                              $resp['rows'][$i]['display'] = str_replace("%%%".$key."_company%%%",$resp['rows'][$i][$val.'userdata']['company'],$resp['rows'][$i]['display']);
                              
                              // Add input for user info (adding a new record)
                              if($i==0) {                              
                                 $inputresp = $wd->getTableDisplayField(NULL,$multiflds['indexed'][$val],'jsfchjprefixsub',TRUE);
                                 
                                 /*
                                 $sub = "%%%".$key."_fname%%%";
                                 if(strpos($resp['rows'][$i]['display'],$sub)===FALSE) {
                                    $sub = "%%%".$key."_lname%%%";
                                    if(strpos($resp['rows'][$i]['display'],$sub)===FALSE) {
                                       $sub = "%%%".$key."_email%%%";
                                    }
                                 }
                                 $resp['inputdisplayrow'] = str_replace($sub,$inputresp['str'],$resp['inputdisplayrow']);
                                 $resp['inputdisplayrow'] = str_replace("%%%".$key."_fname%%%","",$resp['inputdisplayrow']);
                                 $resp['inputdisplayrow'] = str_replace("%%%".$key."_lname%%%","",$resp['inputdisplayrow']);
                                 $resp['inputdisplayrow'] = str_replace("%%%".$key."_email%%%","",$resp['inputdisplayrow']);
                                 */
                                 $resp['inputdisplayrow'] .= "<div style=\"float:left;margin-right:20px;\">".$inputresp['str']."</div>";
                                 $resp['inputdisplayrow'] .= "\n<script>\n";
                                 if($resp['userid']!=NULL) {
                                    $resp['inputdisplayrow'] .= "jQuery('#jsfchjprefixsub_a".$val."').val('".$resp['userid']."');\n";
                                 }
                                 $resp['inputdisplayrow'] .= "if(jQuery('#cmsq_w".$wdata['wd_id'].$val."').length>0 && Boolean(jQuery('#cmsq_w".$wdata['wd_id'].$val."').val())) {\n";
                                 $resp['inputdisplayrow'] .= "   jQuery('#jsfchjprefixsub_a".$val."').val(jQuery('#cmsq_w".$wdata['wd_id'].$val."').val());\n";
                                 $resp['inputdisplayrow'] .= "}\n";
                                 $resp['inputdisplayrow'] .= "\n</script>\n";
                                 $inputrowdisplayjs .= $inputresp['js'];
                              }
                           }
                        }
                     }
                  }
               }
               
               //finish the input/functions for a new record if we created those assets
               if(strlen($resp['inputdisplayrow'])>0 && strlen($inputrowdisplayjs)>0) {
                  $inputrowdisplayjs .= "if (jsfwd_testing) alert('URL: ' + url);\n";
                  $inputrowdisplayjs .= "jsfwebdata_CallJSONP(url);\n";
                  $inputrowdisplayjs .= "}\n</script>\n";
                  $resp['inputdisplayrow'] .= "<div style=\"float:left;margin:1px 8px 4px 8px;padding:3px;text-align:center;width:105px;border:1px solid #343434;border-radius:3px;cursor:pointer;font-size:12px;font-family:arial;\" onclick=\"jsfwebdata_submitjsfchjprefixsub();\">Submit</div>";
                  $resp['inputdisplayrow'] .= "<div style=\"clear:both;\"></div>";
                  $resp['inputdisplayrow'] .= "</div>";
                  $resp['inputdisplayrow'] .= $inputrowdisplayjs;
               }
            } else {
               $resp['error'] = "You do not have authority to this table.";
            }
         } else {
            $resp['error'] = "The table you're trying to display is not available.";
         }
         unset($_SESSION['params']);
         if($resp['testing']==1) print "<br>\n".date("Y-m-d H:i:s")." ms: ".(round(1000 * (microtime(true) - $time_start)))." getwdandrows:: END, returning";
         if($resp['testing']==1) print "<br>\n";
   
      } else if(0==strcmp($action,"getfieldpositions")) {
         $wdobj = new WebsiteData();
         $resp['responsecode'] = 0;
         $resp['wd_id'] = getParameter("wd_id");
         $resp['groupname'] = getParameter("groupname");
         $resp['testing'] = getParameter("testing");
         $webdata = $wdobj->getWebData($resp['wd_id']);
         if($webdata!=NULL) {
            $resp['results'] = $wdobj->getFieldPositions($webdata['wd_id'],$resp['groupname'],TRUE,FALSE,($resp['testing']==1));
            $resp['webdata'] = $webdata;
            $resp['responsecode'] = 1;
            $resp['sections'] = $wdobj->getOrganizedSections($webdata['wd_id']);
            $resp['fieldrels'] = $wdobj->getFieldRelsIndexed($webdata['wd_id']);
         }
         
         
      } else if (0==strcmp($action,"adminwebdata")) {
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['token'],$token) && $ua->isUserAdmin($user['userid'])) {
            //Do not allow anybody to just get access to this...
            $wdobj = new WebsiteData();
            $wdata = NULL;
            $resp['wd_id'] = getParameter("wd_id");
            if ($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wdname");
            if ($resp['wd_id']==NULL) $resp['wd_id'] = getParameter("wd_name");
            
            if ($resp['wd_id']!=NULL) {
               $resp['responsecode'] = 1;
               $resp['html'] = $wdobj->getVisualAdminString_recur($resp['wd_id']);
            } else {
               $resp['responsestr'] = "Could not find the webdata";
               $resp['responsecode'] = 0;
            }
         } else {
            $resp['responsestr'] = "User not authenticated";
            $resp['responsecode'] = 0;
         }
   
      } else if (0==strcmp($action,"getwebdataheader")) {
         $resp['responsecode'] = 0;
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['token'],$token) && $ua->isUserAdmin($user['userid'])) {
            $wd = new WebsiteData();
            $wd_id = getParameter("wd_id");
            $resp['wd'] = $wd->getWebData($wd_id);
            $resp['responsecode'] = 1;
         }
      } else if (0==strcmp($action,"getsections")) {
         $resp['responsecode'] = 0;
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         //if (0==strcmp($user['token'],$token) && $ua->isUserAdmin($user['userid'])) {
         if (0==strcmp($user['token'],$token)) {
            $wd = new WebsiteData();
            $wd_id = getParameter("wd_id");
            $resp['sections'] = $wd->getDataSections($wd_id,getParameter("section"));
            for ($i=0;$i<count($resp['sections']);$i++) {
               $questions = $wd->getFields($wd_id,$resp['sections'][$i]['section']);
               for ($j=0;$j<count($questions);$j++) {
                  $questions[$j]['question'] = convertBack($questions[$j]['question']);   
                  $questions[$j]['label'] = convertBack($questions[$j]['label']);   
               }
               $resp['sections'][$i]['questions'] = $questions;
            }
            $resp['responsecode'] = 1;
         }
         
      } else if (0==strcmp($action,"getrelatedwdtables")) {
         $resp['responsecode'] = 0;
         $wd_id = getParameter("wd_id");
         if($wd_id!=NULL){
            $wd = new WebsiteData();
            //$origemail = getParameter("origemail");
            $wdata = $wd->getWebData($wd_id);
            //if(0==strcmp($origemail,$wdata['origemail'])) {
            $userid = getParameter("userid");
            $token = getParameter("token");
            $ua = new UserAcct();
            $user = $ua->getUser($userid);
            //if (0==strcmp($user['token'],$token) && $ua->isUserAdmin($user['userid'])) {
            if (0==strcmp($user['token'],$token) && $wdata!=NULL) {         
               $resp = $wd->getRelatedTables($wdata['wd_id']);
               $resp['webdata'] = $wdata;
               $resp['responsecode'] = 1;
            }
         }
   
      } else if (0==strcmp($action,"updatewebdata")) {
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['token'],$token) && $ua->isUserAdmin($user['userid'])) {
            $wdobj = new WebsiteData();
            $wd_id = getParameter("wd_id");
            $resp['wd_id'] = $wdobj->adminUpdateMultipleSectionsFields($wd_id);
            $wdata = $wdobj->getWebData($resp['wd_id']);
            $resp['wdname'] = $wdata['name'];
            $resp['responsecode'] = 1;
            $resp['userid'] = $userid;
            $resp['token'] = $token;
         } else {
            $resp['responsecode'] = 0;
         }
   
      } else if (0==strcmp($action,"removewdfieldrel")) {
         $wdobj = new WebsiteData();
         $rel_id = getParameter("rel_id");
         $wdobj->removeFieldRel($rel_id);
         $resp['responsecode'] = 1;      
         $resp['rel_id'] = $rel_id;      
   
   
      } else if (0==strcmp($action,"removewdquestion")) {
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['field_id'] = getParameter("field_id");      
         $wdobj = new WebsiteData();
         $wdobj->deleteField($resp['wd_id'], $resp['field_id']);
         $resp['responsecode'] = 1;
   
      } else if (0==strcmp($action,"removewdsection")) {
         $resp['userid'] = getParameter("userid");
         $resp['token'] = getParameter("token");
         $resp['wd_id'] = getParameter("wd_id");
         $resp['section'] = getParameter("section");
         $wdobj = new WebsiteData();
         $wdobj->deleteSection($resp['wd_id'], $resp['section']);
         $resp['responsecode'] = 1;      
   
      } else if (0==strcmp($action,"getcmscontent")) {
         $organizationid = getParameter("organizationid");
         $shortname = getParameter("shortname");
         $resp['shortname'] = $shortname;
         //$divid = getParameter("divid");
         //if($divid!=NULL) $resp['divid'] = $divid;
   
   //print "orgid: ".$organizationid."<br>\n";
   //print "sn: ".$shortname."<br>\n";
   
         //$template = new Template();
         //$contentstr = "%%%CMS_".$shortname."_CMS%%%";
         //$resp['responsecode'] = 1;      
         //$content = $template->doSubstitutions($contentstr);
   
         $ss = new Version();
         $orgarr = separateStringBy($organizationid,",");
         $i=0;
         $finished = FALSE;
         $content = NULL;
         while($i<count($orgarr) && !$finished) {
            $sn = $shortname."_".$orgarr[$i];
            //print "sn".$i.": ".$sn."<br>\n";
            $fileobj = $ss->getAsciiFileContents($sn);
            $temp = trim($fileobj['contents']);
            if ($temp!=NULL) {
               $finished = TRUE;
               $content = $temp;
            }
            $i++;
         }
         if ($content==NULL) {
            //print "none yet, calling shortname...<br>\n";
            $fileobj = $ss->getAsciiFileContents($shortname);
            $content = trim($fileobj['contents']);
         }
   
         $content = convertBack($content);
         //print "<br>content : ".$content."<br>\n";
   
         $template = new Template();
   
         $resp['content'] = $template->doSubstitutions($content);
   
         
      } else if (0==strcmp($action,"emailsegment")) {
         
         $userid = getParameter("userid");
         $token = getParameter("token");
         
         $ua = new UserAcct();
         $user = $ua->getUser($userid);
         if (0==strcmp($user['token'],$token)) {
            $fromemail = getParameter("fromemail");
            $from = $ua->getUserByEmail($fromemail);
            if(strpos($from['notes'],"sendemail")!==FALSE){
               $emails = getParameter("emails");
               $sendtoemails = separateStringBySeparators($emails);
               $content = getParameter("content");
               $subject = getParameter("subject");
               $sched = new Scheduler();
               for($i=0;$i<count($sendtoemails);$i++) {
                  $sched->addSchedEmail(NULL,NULL,$content, $subject, 6, NULL,$from['email'],5,FALSE,NULL,NULL,$sendtoemails[$i]);
               }
            }
         }
         
         
      //---------------------------------------------------------------------------------
      // Forgotten email...
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"forgottenpw")) {
         $email = getParameter("email");
         $ua = new UserAcct();
         if (!$ua->sendForgottenEmail($email,getParameter("fromemail"),getParameter("title"),getParameter("url"),getParameter("testing"))) {
         //if (!$ua->sendForgottenEmail($email)) {
            $resp['responsecode'] = 0;      
         } else {
            $resp['responsecode'] = 1;      
         }
      //---------------------------------------------------------------------------------
      // Modify Password...
      // parameters needed: userid,token,password,cpassword,oldpassword
      //---------------------------------------------------------------------------------
      } else if (0==strcmp($action,"modifypw")) {
         $userid = getParameter("userid");
         $token = getParameter("token");
         $ua = new UserAcct();
         $user = $ua->getFullUserInfo($userid);
         if (0==strcmp($user['field1'],$token) || 0==strcmp($user['token'],$token)) {
            if ($ua->modifyPassword(getParameter("password"),getParameter("cpassword"),getParameter("oldpassword"), $userid)) $resp['responsecode'] = 1;
            else $resp['responsecode'] = 0;
         }   
   
      } else if (0==strcmp($action,"geocode")) {
         /*
         $dbLink = new MYSQLaccess;
         
         $foundloc = FALSE;
         $foundTbl = FALSE;
         
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
            //print "found table <br>\n";
            $searchtxt = substituteStateName(trim(getParameter("zip")));
            //print "searchtxt: ".$searchtxt."<br>\n";
            
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
         */
         $resp = findCoords(getParameter("zip"),TRUE);
         if ($resp['latitude']!=NULL && $resp['longitude']!=NULL) {
            $resp['responsecode'] = 1;
         } else {
            $resp['responsecode'] = 0;
         }
   
      } else if (0==strcmp($action,"reversegeocode")) {
         //error_reporting(E_ALL);
         $ver = new Version();
         $extra = "";
         $msg = "We cannot accept requests from your domain.";
         $domainok = FALSE;
         $allowdomains = $ver->getValue("allowdomains");
         $d = array();
         $d[] = getBaseURL();
         if($allowdomains!=NULL) {
            $d = separateStringBy($allowdomains,",",NULL,TRUE);
         }
         $cstr = $_SERVER['HTTP_HOST'];
         $cstr = str_replace("/","",str_replace(".","",str_replace("http:","",str_replace("https:","",str_replace("www","",strtolower($cstr))))));
         for($i=0;$i<count($d);$i++) {
            $c2str = str_replace("/","",str_replace(".","",str_replace("http:","",str_replace("https:","",str_replace("www","",strtolower($d[$i]))))));
            $extra .= "  ".$cstr.",".$c2str;
            if(0==strcmp($cstr,$c2str)) {
               $domainok = TRUE;
               break;
            }
         }
         
         $authtoken = getParameter("authtoken");
         if($domainok) {
            $msg = "You need to send a valid authentication token.";
            if($authtoken!=NULL) {
               $aarr = separateStringBy($authtoken,"%%%");
               $dbLink = new MYSQLaccess();
               $query = "SELECT * from useracct WHERE userid='".$aarr[0]."' AND token='".$aarr[1]."';";
               $results = $dbLink->queryGetResults($query);
               if ($results != NULL && count($results)>0) {
                  $msg = "You need to specify latitude and longitude.";
                  $lat = getParameter("latitude");
                  $lng = getParameter("longitude");
                  
                  if($lat!=NULL && $lng!=NULL) {
                     $msg = "Please specify system GoogleMapsAPI Key";
                     
                     $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=false";
                     
                     $gmapskey = $ver->getValue("GoogleMapsKey");
                     if($gmapskey!=NULL) {
                        $msg = "OK";
                        $url .= "&key=".$gmapskey;
                        
                        //$resp = requestJSON($url,TRUE,TRUE);
                        $resp = requestJSON($url);
                        
                        //$resp['originalurl'] = $url;
                        
                        if(getParameter("simple")==1){
                           $city = NULL;
                           $state = NULL;
                           $postal = NULL;
                           for($i=0;$i<count($resp['results']);$i++) {
                              $foundType = FALSE;
                              for($j=0;$j<count($resp['results'][$i]['types']);$j++) {
                                 if(0==strcmp($resp['results'][$i]['types'][$j],"street_address")) {
                                    $foundType=TRUE;
                                    break;
                                 }
                              }
                              if($foundType) {
                                 for($j=0;$j<count($resp['results'][$i]['address_components']);$j++) {
                                    for($k=0;$k<count($resp['results'][$i]['address_components'][$j]['types']);$k++) {
                                       if(0==strcmp($resp['results'][$i]['address_components'][$j]['types'][$k],"locality")) {
                                          $city = $resp['results'][$i]['address_components'][$j]['short_name'];
                                          break;
                                       } else if(0==strcmp($resp['results'][$i]['address_components'][$j]['types'][$k],"administrative_area_level_1")) {
                                          $state = $resp['results'][$i]['address_components'][$j]['short_name'];
                                          break;
                                       } else if(0==strcmp($resp['results'][$i]['address_components'][$j]['types'][$k],"postal_code")) {
                                          $postal = $resp['results'][$i]['address_components'][$j]['short_name'];
                                          break;
                                       }
                                    }
                                    if($city!=NULL && $state!=NULL && $postal!=NULL) break;
                                 }
                                 break;
                              }
                           }
                           
                           $resp = array();
                           $resp['city'] = $city;
                           $resp['state'] = $state;
                           $resp['postal'] = $postal;
                        }
                        $resp['responsecode'] = 1;
                        //$resp['domain'] = getBaseURL();
                        //$resp['request'] = $_SERVER['HTTP_HOST'];
                     }
                  }
               }
            }
         }
         $resp['msg'] = $msg;
         $resp['extra'] = $extra;
         
      } else if (0==strcmp($action,"jsfnewsignup")) {
         //error_reporting(E_ALL);
         $resp['domain'] = getBaseURL();
         $resp['responsecode'] = 0;
         $resp['userid'] = 0;
         $rnd = getParameter("rnd");
         $sessionid = getParameter("sessionid");
         $validate = getParameter("validate");
         $validate_new = encrypt($rnd,substr($sessionid,0,4));   
         if (0==strcmp($validate,$validate_new)) {
            $template = new Template();
            $ua = new UserAcct();
            $view = getParameter("view");
            $jsftrack1 = trim(getParameter("jsftrack1"));
            $jsftrack2 = getParameter("jsftrack2");
            $clearbits = getParameter("clearbits");
            $user = $ua->getUserByEmail($jsftrack1);
            $currentValue = 0;
            if ($user==NULL || $user['userid']==NULL) {
               $_SESSION['params']['email'] = $jsftrack1;
               $userid = $ua->addUserEmailOnly();
               $_SESSION['params']['email'] = NULL;
               if ($userid>0) $template->trackItem('NewUser',$action,$jsftrack1,$jsftrack2,$jsftrack3,NULL,NULL,NULL,$sessionid);
               else $template->trackItem('FailedNewUser',$action,$jsftrack1,$jsftrack2,$jsftrack3,NULL,NULL,NULL,$sessionid);
               $jsftrack1 = $userid;
               $jsftrack3 = "";
            } else {
               $jsftrack1 = $user['userid'];
               $jsftrack3 = getBitsSet($user['field4']);
               if ($clearbits==1) $ua->updateField($jsftrack1, "field4", 0);
               else $currentValue = $user['field4'];
            }
            $lists = separateStringBy($jsftrack2,",");
            for ($i=0; $i<count($lists); $i++) {
               $currentValue = setBitNumber($currentValue,$lists[$i]);
               //$ua->setBinaryBitField($jsftrack1, "field4", $lists[$i]);
            }
            $ua->updateField($jsftrack1, "field4", $currentValue);
   
            $template->trackItem($view,$action,$jsftrack1,$jsftrack2,$jsftrack3,NULL,NULL,NULL,$sessionid);
            $resp['userid'] = $jsftrack1;
            $resp['responsecode'] = 1;
         }
   
      } else if (0==strcmp($action,"trackitem") || 0==strcmp($action,"atrackitem") || 0==strcmp($action,"jsontrackitem")) {
         $rnd = getParameter("rnd");
         $sessionid = getParameter("sessionid");
         $validate = getParameter("validate");
         $validate_new = encrypt($rnd,substr($sessionid,0,4));   
         if (0==strcmp($validate,$validate_new)) {
            $template = new Template();
            $view = getParameter("view");
            $foraction = getParameter("foraction");
            if($foraction==NULL) $foraction = $action;
            $jsftrack1 = getParameter("jsftrack1");
            $jsftrack2 = getParameter("jsftrack2");
            $jsftrack3 = getParameter("jsftrack3");
            $referer = getParameter("referer");
            if($referer==NULL) $referer = getParameter("referrer");
            $ipaddr = getParameter("ipaddr");
            $agent = getParameter("agent");
            $userid = getParameter("userid");
            $country = getParameter("country");
            $region = getParameter("region");
            $city = getParameter("city");
            $zipcode = getParameter("zipcode");
            $lat = getParameter("lat");
            $lng = getParameter("lng");
            $template->trackItem($view,$foraction,$jsftrack1,$jsftrack2,$jsftrack3,$referer,$ipaddr,$agent,$sessionid,$userid,$country,$region,$city,$lat,$lng,$zipcode);
            $resp['responsecode'] = 1;
            $resp['domain'] = getBaseURL();
         }
   
      } else if (0==strcmp($action,"jsontrackgeocode")) {
         $rnd = getParameter("rnd");
         $sessionid = getParameter("sessionid");
         $validate = getParameter("validate");
         $validate_new = encrypt($rnd,substr($sessionid,0,4));   
         if (0==strcmp($validate,$validate_new)) {
            $template = new Template();
            $view = getParameter("view");
            $action = getParameter("action");
            $jsftrack1 = getParameter("jsftrack1");
            $jsftrack2 = getParameter("jsftrack2");
            $jsftrack3 = getParameter("jsftrack3");
            $referer = getParameter("referer");
            $ipaddr = getParameter("ipaddr");
            $agent = getParameter("agent");
            $userid = getParameter("userid");
            $country = getParameter("country");
            $region = getParameter("region");
            $city = getParameter("city");
            $zipcode = getParameter("zipcode");
            $lat = getParameter("lat");
            $lng = getParameter("lng");
            $template->trackItem($view,$action,$jsftrack1,$jsftrack2,$jsftrack3,$referer,$ipaddr,$agent,$sessionid,$userid,$country,$region,$city,$lat,$lng,$zipcode);
            $resp['responsecode'] = 1;
            $resp['domain'] = getBaseURL();
         }
      } else if (0==strcmp($action,"dosubstitutions")) {
         $str = getParameter("str");
         $template = new Template();
         $resp = $template->doSubstitutions($str);
   
   
      } else if (0==strcmp($action,"imagezoom") || 0==strcmp($action,"imagefull")) {
         //print "hello world";
         $resp = NULL;
         $img = getParameter("imageuri");
         $local = TRUE;
         $external = FALSE;
         $extURL = "";
         if (0==strcmp("http://",substr($img,0,7)) || 0==strcmp("https://",substr($img,0,8))) {
            $urllength = strlen(getBaseURL());
            if (0==strcmp(getBaseURL(),substr($img,0,$urllength))) {
               $img = substr($img,$urllength);
            } else {
               $local = FALSE;
               $urlend = strpos($img,"/",9);
               if ($urlend!==FALSE) {
                  $external = TRUE;
                  $extURL = substr($img,0,($urlend+1));
                  $img = substr($img,($urlend+1));
               }
            }
         }
         if ($local) {
            $ji = new JSFImage();
            $ji->load(str_replace("//","/",$GLOBALS['baseDir'].$img));
            $resp['html'] = '';
            if (0==strcmp($action,"imagezoom")) {
               $resp['html'] = $ji->getZoomToRectangle(getParameter("width"),getParameter("height"),getParameter("extrastyle"));
            } else {
               $resp['html'] = $ji->getFullToRectangle(getParameter("width"),getParameter("height"),getParameter("extrastyle"));
            }
            $resp['extra'] = getParameter("extra");
         } else if ($external) {
            $url = $extURL."jsfcode/jsoncontroller.php?action=imagezoom&width=".getParameter("width")."&height=".getParameter("height")."&extrastyle=".urlencode(getParameter("extrastyle"))."&extra=".urlencode(getParameter("extra"))."&imageuri=".urlencode($img);
            $resp = requestJSON($url);
         }
   
      } else if (0==strcmp($action,"getfilesfromdir")) {
   
      } else if (0==strcmp($action,"adduserpost")) {
         $up = new UserPost();
         $resp = array();
         $resp['answer'] = "-1";
         $resp['passthru1'] = getParameter("passthru1");
         $resp['passthru2'] = getParameter("passthru2");
         $posttype = getParameter("posttype");
         $resp['params'] = array();
         $resp['params']['posttype'] = getParameter("posttype");
         $resp['params']['refid'] = getParameter("refid");
         $resp['params']['userid'] = getParameter("userid");
         $resp['params']['title'] = convertString(getParameter("title"));
         $resp['params']['content'] = convertString(getParameter("content"));
         $resp['params']['category'] = convertString(getParameter("category"));
         $resp['params']['quickie'] = getParameter("quickie");
         $resp['params']['field1'] = convertString(getParameter("field1"));
         $resp['params']['field2'] = convertString(getParameter("field2"));
         $resp['params']['field3'] = convertString(getParameter("field3"));
         $resp['params']['field4'] = convertString(getParameter("field4"));
         $resp['params']['norepeat'] = convertString(getParameter("norepeat"));
         $resp['params']['emailtemplate'] = convertString(getParameter("emailtemplate"));
         $resp['params']['emailtouserid'] = convertString(getParameter("emailtouserid"));
         $makeItHappen = TRUE;
         if ($resp['params']['norepeat']==1) {  
            //getPostsFor($userid=NULL,$posttype=NULL,$refid=NULL,$status=NULL,$limit=NULL,$publicOnly=TRUE,$category=NULL,$searchstr=NULL,$orderby=NULL,$field1_lt=NULL,$field1_gt=NULL,$field2_lt=NULL,$field2_gt=NULL,$field3=NULL,$field4=NULL,$countOnly=FALSE){
            $post_count = $up->getPostsFor($resp['params']['userid'],$resp['params']['posttype'],$resp['params']['refid'],NULL,NULL,FALSE,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
            if ($post_count[0]['count(*)']>0) $makeItHappen=FALSE;
         }
         if ($makeItHappen) {
            $resp['answer'] = $up->addPost($resp['params']['posttype'],$resp['params']['userid'],$resp['params']['title'],$resp['params']['content'],1,"ACTIVE",$resp['params']['refid'],$resp['params']['category'],$resp['params']['quickie'],TRUE,$resp['params']['field1'],$resp['params']['field2'],$resp['params']['field3'],$resp['params']['field4']);
            if ($resp['params']['emailtemplate']!=NULL && $resp['params']['emailtouserid']!=NULL && $resp['params']['emailtouserid']>0) {
               $ua = new UserAcct();
               $tempuser = $ua->getUser($resp['params']['emailtouserid']);
               if (strpos($tempuser['email'],"dummy")===FALSE) {
                  $sched = new Scheduler();
                  $version = new Version();
                  $names = array();
                  $values = array();
                  $sched->sendMessage($resp['params']['emailtouserid'],$version->getValue("WebsiteContact"),$resp['params']['emailtemplate'],NULL,NULL,NULL,5,"email",$names,$values);
               }
            }
         }
   
   
      } else if (0==strcmp($action,"checkuserpost")) {
         $up = new UserPost();
         $resp = array();
         $resp['responsecode'] = "1";
         $resp['passthru1'] = getParameter("passthru1");
         $resp['passthru2'] = getParameter("passthru2");
         
         $resp['params'] = array();
         $resp['params']['posttype'] = getParameter("posttype");
         $resp['params']['refid'] = getParameter("refid");
         $resp['params']['userid'] = getParameter("userid");
         
         //getPostsFor($userid=NULL,$posttype=NULL,$refid=NULL,$status=NULL,$limit=NULL,$publicOnly=TRUE,$category=NULL,$searchstr=NULL,$orderby=NULL,$field1_lt=NULL,$field1_gt=NULL,$field2_lt=NULL,$field2_gt=NULL,$field3=NULL,$field4=NULL,$countOnly=FALSE,$email=NULL){
         if($resp['params']['userid']!=NULL) {
            $post_count = $up->getPostsFor($resp['params']['userid'],$resp['params']['posttype'],$resp['params']['refid'],NULL,NULL,FALSE,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
            $resp['user'] = $post_count[0]['count(*)'];
         }
         $post_count = $up->getPostsFor(NULL,$resp['params']['posttype'],$resp['params']['refid'],NULL,NULL,FALSE,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
         $resp['total'] = $post_count[0]['count(*)'];
         
      } else if (0==strcmp($action,"checkmultipleuserpost")) {
         $up = new UserPost();
         $resp = array();
         $resp['responsecode'] = "1";
         $resp['passthru1'] = getParameter("passthru1");
         $resp['passthru2'] = getParameter("passthru2");
         
         $resp['params'] = array();
         $resp['params']['posttype'] = getParameter("posttype");
         $resp['params']['refidarray'] = getParameter("refidarray");
         $resp['params']['userid'] = getParameter("userid");
         
         for($i=0;$i<count($resp['params']['refidarray']);$i++) {
            $resp['ref'.$resp['params']['refidarray'][$i]] = array();
            //getPostsFor($userid=NULL,$posttype=NULL,$refid=NULL,$status=NULL,$limit=NULL,$publicOnly=TRUE,$category=NULL,$searchstr=NULL,$orderby=NULL,$field1_lt=NULL,$field1_gt=NULL,$field2_lt=NULL,$field2_gt=NULL,$field3=NULL,$field4=NULL,$countOnly=FALSE,$email=NULL){
            if($resp['params']['userid']!=NULL) {
               $post_count = $up->getPostsFor($resp['params']['userid'],$resp['params']['posttype'],$resp['params']['refidarray'][$i],NULL,NULL,FALSE,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
               $resp['ref'.$resp['params']['refidarray'][$i]]['user'] = $post_count[0]['count(*)'];
            }
            $post_count = $up->getPostsFor(NULL,$resp['params']['posttype'],$resp['params']['refidarray'][$i],NULL,NULL,FALSE,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,TRUE);
            $resp['ref'.$resp['params']['refidarray'][$i]]['total'] = $post_count[0]['count(*)'];
         }
         
      } else if (0==strcmp($action,"rotateimage")) {
         //error_reporting(E_ALL);
         $resp['responsecode'] = 0;
         $fn = getParameter("fn");
         $resp['fn'] = $fn;
         $resp['passthru1'] = getParameter("passthru1");
         $resp['passthru2'] = getParameter("passthru2");
         $degrees = getParameter("degrees");
         
         if($fn!=NULL) {
            if(0==strcmp("http",substr($fn,0,4))) {
               $fn = str_replace($GLOBALS['baseURL'],"",$fn);
               $fn = str_replace($GLOBALS['baseURLSSL'],"",$fn);
            }
            $jsfi = new JSFImage();
            $chkld = $jsfi->load($GLOBALS['baseDir'].$fn);
            if ($chkld) {
               $jsfi->resizeToRectangle(900,800);
               //if(file_exists($GLOBALS['baseDir']."backup".$fn)) unlink($GLOBALS['baseDir']."backup".$fn);
               //$jsfi->save($GLOBALS['baseDir']."backup".$fn,NULL,75,755);
               if(file_exists($GLOBALS['baseDir'].$fn)) unlink($GLOBALS['baseDir'].$fn);
               if($degrees!=NULL && $degrees!=0) $jsfi->rotateImage($degrees);
               $jsfi->save($GLOBALS['baseDir'].$fn,NULL,80);
               $jsfi->closeimage();
               $resp['responsecode'] = 1;
            }
         }      
   
      } else if (0==strcmp($action,"jsfnvp")) {
         $nvp = new NVPHelper();
         
         $dbi = new MYSQLAccess();
         $wdObj = new WebsiteData();
         $subaction = getParameter("subaction");
         $wd_id = getParameter("wd_id");
         $webdata = $wdObj->getWebData($wd_id);
         $qs = $wdObj->getFieldLabels($webdata['wd_id']); 
         $name = getParameter("name");
         $version = strtolower(trim(getParameter("version")));
         $byuser = getParameter("byuser");
         $singleversion = getParameter("singleversion");
         $limit = getParameter("limit");
         $lookupuser = getParameter("lookupuser");
         $adduser = getParameter("adduser");
         
         if(0==strcmp($subaction,"get")){
            $obj = $nvp->searchNVP($wd_id,$name,$version,$byuser,$singleversion,$limit,$adduser,$lookupuser);
            $rows = $obj["rows"];
            $resp['rows'] = $rows;
            if(count($rows)==1) {
               foreach($rows[0] as $key => $val) {
                  $resp[$key] = $val;
               }
            }
            if(isset($obj['user'])) $resp['user'] = $obj['user'];
            $resp['wd_id'] = $obj['wd_id'];
            $resp['wdname'] = $obj['wdname'];
            $resp['responsecode'] = 1;
            
         } else if(0==strcmp($subaction,"update")){
            $obj = $nvp->searchNVP($wd_id,$name,$version,$byuser,$singleversion,$limit,$adduser,$lookupuser);
            $field = getParameter("field");
            $value = getParameter("value");
            
            $qs = $obj['qs'];
            
            if($obj['rows']!=NULL && count($obj['rows'])>0 && isset($qs[$field])) {
               $query = "UPDATE wd_".$obj['wd_id']." SET ";
               $query .= $qs[$field]."='".convertString($value)."'";
               $query .= " WHERE ";
               
               for($i=0;$i<count($obj['rows']);$i++) {
                  if($i>0) $query .= " OR ";
                  $query .= "wd_row_id=".$obj['rows'][$i]['wd_row_id'];
               }
               
               $dbi = new MYSQLAccess();
               $dbi->update($query);
            }
         } else if(0==strcmp($subaction,"checkandset") || 0==strcmp($subaction,"set")){
            $obj = $nvp->searchNVP($wd_id,$name,$version,$byuser,$singleversion,$limit,$adduser,$lookupuser);
            $webdata = $obj['webdata'];
            $qs = $obj['qs'];
            $rows = $obj["rows"];
            
            $check = getParameter("check");
            $value = getParameter("value");
            $userid = getParameter("userid");
            
            $resp['responsecode'] = 1;
            if($rows!=NULL && count($rows)>0) {
               if($check==NULL || $rows[0]['value']==NULL || 0==strcmp($rows[0]['value'],$check)) {
                  $query = "UPDATE wd_".$webdata['wd_id'];
                  $query .= " SET lastupdate=NOW()";
                  $query .= ", ".$qs['value']."='".trim(convertString($value))."'";
                  $query .= " WHERE wd_row_id=".$rows[0]['wd_row_id'];
                  $dbi->update($query);
               } else {
                  if(count($rows)==1) {
                     foreach($rows[0] as $key => $val) {
                        $resp[$key] = $val;
                     }
                  }
                  $resp['responsecode'] = 0;
                  $resp['wd_id'] = $obj['wd_id'];
                  $resp['user'] = $obj['user'];
               }
            } else {
               if($version==NULL) $version = 1;
               $query = "INSERT INTO wd_".$webdata['wd_id']." (";
               $query .= "dbmode";
               $query .= ",created";
               $query .= ",lastupdate";
               $query .= ",".$qs['name'];
               $query .= ",".$qs['value'];
               if(isset($qs['enabled'])) $query .= ",".$qs['enabled'];
               if(isset($qs['version'])) $query .= ",".$qs['version'];
               if(isset($qs['verstatus'])) $query .= ",".$qs['verstatus'];
               if($userid!=NULL) $query .= ",userid";
               $query .= ") VALUES (";
               $query .= "'NEW'";
               $query .= ",NOW()";
               $query .= ",NOW()";
               $query .= ",'".convertString($name)."'";
               $query .= ",'".trim(convertString($value))."'";
               if(isset($qs['enabled'])) $query .= ",'YES'";
               if(isset($qs['version'])) $query .= ",'".$version."'";
               if(isset($qs['verstatus'])) $query .= ",'ACTIVE'";
               if($userid!=NULL) $query .= ",".$userid;
               $query .= ")";
               $wd_row_id = $dbi->insertGetValue($query);
            }
         }
         
      } else if (0==strcmp($action,"pagebuilder")) {
         //error_reporting(E_ALL);
         $resp['responsecode'] = 0;
         $subaction = getParameter("subaction");
         if(0==strcmp($subaction,"singlepage")){
            $resp['wd_id'] = getParameter("wd_id");
            $resp['responsecode'] = 0;
            $pagename = strtolower(trim(getParameter("pagename")));
            if(0!=strcmp(substr($pagename,0,6),"page: ")) $pagename = "page: ".$pagename;
            $name = $pagename."%";
            $notname = $pagename."_jsflock";
            $version = strtolower(trim(getParameter("version")));
            $nvp = new NVPHelper();
            $obj = $nvp->searchNVP($resp['wd_id'],$name,$version,NULL,1,500,NULL,NULL,$notname,(getParameter("testing")==1));
            if(getParameter("testing")==1) $resp['query'] = $obj['query'];
            $results = $obj['rows'];
            if($results!=NULL && count($results)>0) {
               $pageobj = array();
               $pgcnt = 0;
               $cont = true;
               while($cont){
                  $cont = false;
                  for($j=0;$j<count($results);$j++){
                     $nm = $pagename;
                     if($pgcnt>0) $nm .= "_jsf".$pgcnt;
                     if(0==strcmp($nm,strtolower($results[$j]['name']))) {
                        $cont = true;
                        $t = json_decode(convertBack($results[$j]['value']), true);
                        if($pgcnt==0) {
                           $pageobj = $t;
                           $pageobj['version'] = $results[$j]['version'];
                           $pageobj['verstatus'] = $results[$j]['verstatus'];
                        }
                        else $pageobj['rows'] = array_merge($pageobj['rows'],$t['rows']);
                        $pgcnt++;
                        if(count($pageobj['rows']) >= $pageobj['rowcount']) break;
                     }
                  }
                  if(count($pageobj['rows']) >= $pageobj['rowcount']) break;
               }
               $resp['responsecode'] = 1;
               $resp['page'] = $pageobj;
               //$resp['divid'] = getParameter("divid");
               $resp['width'] = getParameter("width");
            }
            
            
            
            
            
            
            
            
            
            /*
            $resp['wd_id'] = getParameter("wd_id");
            $pagename = strtolower(trim(getParameter("pagename")));
            $wdObj = new WebsiteData();
            $webdata = $wdObj->getWebData($resp['wd_id']);
            if($webdata!=NULL && $webdata['wd_id']>0) {
               $qs = $wdObj->getFieldLabels($webdata['wd_id'],true,true);
               
               if(getParameter("testing")==1) {
                  print "<br>\nqs:<br>\n";
                  print_r($qs);
                  print "<br>\n";
               }
               
               if(0!=strcmp(substr($pagename,0,6),"page: ")) $pagename = "page: ".$pagename;
               $query = "SELECT ".$qs['name']." as name";
               $query .= ", ".$qs['value']." as value";
               $query .= ", ".$qs['image']." as image";
               if(isset($qs['version']) && isset($qs['verstatus'])) {
						$query .= ", ".$qs['version']." as version";
						$query .= ", ".$qs['verstatus']." as verstatus";
               }
               $query .= " FROM wd_".$webdata['wd_id']." ";
               $query .= "WHERE LOWER(".$qs['name'].") LIKE '".$pagename."%' ";
               $query .= "AND LOWER(".$qs['name'].")<>'".$pagename."_jsflock' ";
               
               // If this particular pagebuilder has versions...
               if(isset($qs['version']) && isset($qs['verstatus'])) {
                  $version = strtolower(trim(getParameter("version")));
                  if($version==NULL || 0==strcmp($version,'active')) $query .= "AND ".$qs['verstatus']."='ACTIVE' ";
                  else if(is_numeric($version)) $query .= "AND ".$qs['version']."=".$version." ";
               }
               
               $query .= "AND dbmode<>'DELETED' ";
               $query .= "AND LOWER(".$qs['enabled'].")='yes'";

               if(isset($qs['version']) && isset($qs['verstatus'])) {
               	$query .= " ORDER BY ".$qs['version']." DESC";
               }
               
               if(getParameter("testing")==1) print "<br>\nquery: ".$query."<br>\n";
               $dbi = new MYSQLAccess();
               $results = $dbi->queryGetResults($query);
               if($results!=NULL && count($results)>0) {
                  $pageobj = array();
                  $pgcnt = 0;
                  $cont = true;
                  $workingver = NULL;
                  while($cont){
                  	$cont = false;
                     for($j=0;$j<count($results);$j++){
                        $nm = $pagename;
                        if($pgcnt>0) $nm .= "_jsf".$pgcnt;
                        if(0==strcmp($nm,strtolower($results[$j]['name']))) {
                        	if($workingver==NULL || $workingver==$results[$j]['version']) {
										$cont = true;
										$t = json_decode(convertBack($results[$j]['value']), true);
										if($pgcnt==0) {
											$pageobj = $t;
											$pageobj['version'] = $results[$j]['version'];
											$pageobj['verstatus'] = $results[$j]['verstatus'];
											$workingver = $results[$j]['version'];
										}
										else $pageobj['rows'] = array_merge($pageobj['rows'],$t['rows']);
										$pgcnt++;
										if(count($pageobj['rows']) >= $pageobj['rowcount']) break;
									}
                        }
                     }
                     if(count($pageobj['rows']) >= $pageobj['rowcount']) break;
                  }
                  $resp['responsecode'] = 1;
                  $resp['page'] = $pageobj;
                  //$resp['divid'] = getParameter("divid");
                  $resp['width'] = getParameter("width");
                  //$resp['rows'] = $results;
               }
            }
            */
         }
         
      //Makes it easy to add/update/search based on hashtags in any table
      } else if (0==strcmp($action,"jsfhashtag")) {
         
         // Parameters:
         // userid or email/token for security
         // tb is the name of the table if applicable
         // wd_id/wdname to identify a jdata table if applicable
         // col is the name of the column which contains the CSL of hashtags
         // prk/prv is a private key and value
         $userid = getParameter("userid");
         $email = getParameter("email");
         $token = getParameter("token");
         $tb = getParameter("tb");
         $wd_id = getParameter("wd_id");
         if($wd_id==NULL) $wd_id = getParameter("wdname");
         $htaction = getParameter("htaction");
         $ua = new UserAcct();
         $user = NULL;
         if ($userid!=NULL) $user = $ua->getUser($userid);
         else $user = $ua->getUserByEmail($email);
         if ($htaction!=NULL && ($tb!=NULL || $wd_id!=NULL) && 0==strcmp($user['token'],$token)) {
            $resp['responsecode'] = 1;
   
            $col = getParameter("col");
            if ($col==NULL) $col = "hashtags";
            $orderby = getParameter("orderby");
            $prk = getParameter("prk");
            $prv = getParameter("prv");
            $searchcols = getParameter("searchcols");
   
            if ($wd_id!=NULL) {
               $wdObj = new WebsiteData();
               $wd = $wdObj->getWebDataByName($wd_id);
               $resp['wd_id'] = $wd['wd_id'];
               $qs = $wdObj->getFieldLabels($wd['wd_id']);
               if (isset($qs[$col])) $col = $qs[$col];
               if (isset($qs[$prk])) $prk = $qs[$prk];
               $tb = "wd_".$wd['wd_id'];
               $colarr = separateStringBy($searchcols,",",NULL,TRUE);
               $temp = "";
               for($i=0;$i<count($colarr);$i++){
                  if(isset($qs[$colarr[$i]])) $colarr[$i]=$qs[$colarr[$i]];
                  if($i>0) $temp .= ", ";
                  $temp .= $colarr[$i];
               }
               $searchcols = $temp;
            }
   
            $curht = NULL;
            $dbi = new MYSQLAccess();
            if ($prk!=NULL && $prv!=NULL) {
               $query = "SELECT ".$col." FROM ".$tb." WHERE ".$prk."='".$prv."';";
               $results = $dbi->queryGetResults($query);
               $curht = $results[0][$col];
            }
            
            $ht = trim(getParameter("ht"));
            $resp['originalht'] = $ht;
            if ($ht!=NULL) {
               //if (0==strcmp(substr($ht,0,1),"#")) $ht = substr($ht,1);
               //$ht = preg_replace('/[^A-Za-z0-9_-:]/','',$ht);
               $ht = convertHashtag($ht);
            }
            $resp['ht'] = $ht;
            
            if (0==strcmp($htaction,"get")) {
               //$resp['hashtags'] = separateStringBy($curht," ");
            } else if (0==strcmp($htaction,"add")) {
               $resp['htaction'] = "add";            
               if ($ht!=NULL && strpos($curht,"#".$ht." ")===FALSE) {
                  $curht = $curht."#".$ht." ";
                  $query = "UPDATE ".$tb." SET ".$col."='".$curht."' WHERE ".$prk."='".$prv."';";
                  $resp['query'] = $query;
                  $dbi->update($query);
                  unset($_SESSION['webdata']);               
               }
            } else if (0==strcmp($htaction,"delete")) {
               if ($ht!=NULL && strpos($curht,"#".$ht." ")!==FALSE) {
                  $curht = str_replace("#".$ht." ","",$curht);
                  $query = "UPDATE ".$tb." SET ".$col."='".$curht."' WHERE ".$prk."='".$prv."';";
                  $dbi->update($query);
                  unset($_SESSION['webdata']);               
               }
            } else if (0==strcmp($htaction,"search")) {
               $searchht = getParameter("searchht");
               $excludeht = getParameter("excludeht");
               if($searchht==NULL) $searchht = $ht;
               if($searchht!=NULL && $excludeht!=NULL && strpos($searchht,$excludeht)!==FALSE) $excludeht=NULL;
               if(!is_array($searchht)) $searchht=separateStringBy($searchht,",",NULL,TRUE);
               $using = array();
               if($searchcols==NULL) $searchcols="*";
               $query = "SELECT ".$searchcols." FROM ".$tb." WHERE 1=1";
               for($i=0;$i<count($searchht);$i++){
                  $temp = preg_replace("/[^A-Za-z0-9_:-]/",'',$searchht[$i]);
                  $query .= " AND LOWER(".$col.") like '%#".strtolower($temp)." %'";
                  $using[] = $temp;
               }
               
               if($excludeht!=NULL) {
                  $temp = preg_replace("/[^A-Za-z0-9_:-]/",'',$excludeht);
                  $query .= " AND (";
                  $query .= " ".$col." IS NULL OR ";
                  $query .= " LOWER(".$col.") not like '%#".strtolower($temp)." %'";
                  $query .= ")";
               }
               
               $searchtxt = strtolower(trim(getParameter("searchtxt")));
               $resp['searchtxt'] = $searchtxt;
               if($searchtxt!=NULL) {
                  if($searchcols==NULL) $searchcols = $col;
                  $query .= " AND(";
                  $colarr = separateStringBy($searchcols,",",NULL,TRUE);
                  for($i=0;$i<count($colarr);$i++){
                     if($i>0) $query .= " OR ";
                     $query .= "LOWER(".$colarr[$i].") LIKE '%".$searchtxt."%'";
                  }
                  $query .= ")";
               }
               
               if($orderby!=NULL) {
                  $query .= " ORDER BY ".$orderby." LIMIT 0,50;";
                  $resp['orderby'] = $orderby;
               } else {
                  $query .= " ORDER BY ".$prk." DESC LIMIT 0,50;";
               }
               $results = $dbi->queryGetResults($query);
               //$results = $dbi->queryGetResults($query." ORDER BY ".$prk." DESC LIMIT 0,50;");
               $htremaining = array();
               for($i=0;$i<count($results);$i++){
                  $hts = $results[$i][$col];
                  $htarr = separateStringBy($hts," ");
                  for($j=0;$j<count($htarr);$j++) {
                     $n = substr($htarr[$j],1);
                     $proceed = true;
                     for($k=0;$k<count($using);$k++){
                        if(0==strcmp($n,$using[$k])) $proceed = false;
                     }
                     if($proceed) {
                        if(isset($htremaining[$n])) $htremaining[$n]++;
                        else $htremaining[$n] = 1;
                     }
                  }
                  foreach($results[$i] as $key=>$val){
                     if(0!=strcmp($col,$key)){
                        if(strlen($val)>100) $results[$i][$key] = substr($val,0,100);
                     }
                  }
               }
               $resp['results'] = $results;
               //arsort($htremaining);
               $htr = array();
               foreach($htremaining as $key => $val) $htr[] = $key;
               
               /*
               // Sort $htr by largest first
               for($i=0;$i<count($htr);$i++) {
                  for($j=($i+1);$j<count($htr);$j++) {
                     if($htremaining[$htr[$j]]>$htremaining[$htr[$i]]) {
                        $temp = $htr[$i];
                        $htr[$i] = $htr[$j];
                        $htr[$j] = $temp;
                     }
                  }
               }
               */
               
               
               
               $resp['rehashtags'] = $htr;
               $resp['ushashtags'] = $using;
            } else {
               $resp['responsecode'] = 0;
            }
            $resp['curht'] = $curht;
            if ($curht!=NULL) {
               $htarr = separateStringBy($curht," ");
               for ($i=0;$i<count($htarr);$i++) $htarr[$i] = preg_replace("/[^A-Za-z0-9_:-]/",'',$htarr[$i]);
               $resp['hashtags'] = $htarr;
            }
         } else {
            $resp['responsecode'] = 0;
            $resp['error'] = "Unaccessible right now.";
         }
         
      } else if (0==strcmp($action,"searchwdindex")) {
         // Get auto-complete recommendations to display   
         $wd_id = getParameter("wd_id");
         $phrase = getParameter("phrase");
         $resp['wd_id'] = $wd_id;
         $resp['phrase'] = $phrase;
         if($wd_id==-1) {
            $resp['responsecode'] = 1;
            $phrase = strtolower(trim($phrase));
            if(strlen($phrase)>2) {
               $query = "SELECT name,wd_id ";
               $query .= "FROM webdata ";
               $query .= "WHERE LOWER(name) LIKE '%".$phrase."%' ORDER BY name LIMIT 0,10;";
               $resp['query'] = $query;
               $dbLink = new MYSQLaccess;
               $results = $dbLink->queryGetResults($query);
               $resp['ans'] = array();
               for($j=0;$j<count($results);$j++) $resp['ans'][] = $results[$j]['name'];
            }
         } else {
            $wdObj = new WebsiteData();
            $resp = $wdObj->searchIndexWD($wd_id,$phrase,NULL,TRUE,(getParameter("testing")==1));
            $resp['divid'] = getParameter("divid");
            $resp['responsecode'] = 1;
         }
         
      } else if (0==strcmp($action,"resultswdindex")) {
         // Get results from the index to build a query given a phrase   
         $wd_id = getParameter("wd_id");
         $phrase = getParameter("phrase");
         
         $wdObj = new WebsiteData();
         $results = $wdObj->buildQueryIndexWD($wd_id,$phrase,(getParameter("testing")==1));
         $resp['results'] = $results;
   
         
      // Custom responses for json request
      } else if (class_exists("CustomString")) {
         $customObj = new CustomString();
         $resp_t = $customObj->customAction($action);
         
         //print "<br><br>\n\n".$resp_t."<br><br>\n\n";
         
         if($resp_t!=NULL) $resp = $resp_t;
         //print "\n\n<br><br>\n\n".$resp."\n\n<br><br>\n\n";
         
      }
      
      //print "\n<br>\n".$resp."\n<br>\n";
      $resp['jsonsaveval'] = getParameter("jsonsaveval");
      //print "\n<br>\n".$resp."\n<br>\n";
   
      //print "\n<!-- chj response is: \n";
      //print_r($resp);
      //print " -->\n";
   
      //print "\n<!-- chj response is: \n";
      //print_r(json_encode_jsf($resp));
      //print " -->\n";
   
      //print "\n<br>\n".$resp."\n<br>\n";
      //$json = json_encode_jsf($resp);
      //print "<br><br>\n\n";
      //print_r($resp);
      //print "<br><br>\n\n";
      
   } else {
      $resp['failed'] = "Request not secure";
   }

   $json = json_encode($resp);
   $callback = getParameter("callback");
   $format = getParameter("format");      
   if($format!=NULL && $callback!=NULL && 0==strcmp($format,"jsonp")) echo $callback."(".$json.");";
   else echo $json;
   
   class NVPHelper {
      function searchNVP($wd_id,$name,$version=NULL,$byuser=NULL,$singleversion=NULL,$limit=100,$adduser=NULL,$lookupuser=NULL,$notname=NULL,$testing=FALSE) {
         if($testing) print "<br>\nsearchNVP: ".$wd_id.", ".$name."<br>\n";
         $resp = array();
         
         $dbi = new MYSQLAccess();
         $wdObj = new WebsiteData();
         $webdata = $wdObj->getWebData($wd_id);
         $qs = $wdObj->getFieldLabels($webdata['wd_id'],TRUE,TRUE);
         
         $resp['qs'] = $qs;
         $resp['webdata'] = $webdata;
         
         // This could be 1 if user only expects 1 to be returned
         if($limit==NULL || !is_numeric($limit)) $limit = 100;
         
         $query = "SELECT ";
         $query .= $qs['name']." as name";
         $query .= ", ".$qs['value']." as value";
         if(isset($qs['version'])) $query .= ", ".$qs['version']." as version";
         if(isset($qs['verstatus'])) $query .= ", ".$qs['verstatus']." as verstatus";
         if(isset($qs['sequence'])) $query .= ", ".$qs['sequence']." as sequence";
         if(isset($qs['enabled'])) $query .= ", ".$qs['enabled']." as enabled";
         if(isset($qs['image'])) $query .= ", ".$qs['image']." as image";
         $query .= ", created";
         $query .= ", lastupdate";
         $query .= ", wd_row_id";
         $query .= ", userid";
         $query .= " FROM wd_".$webdata['wd_id']." ";
         //$query .= " WHERE ".$qs['name']."='".$name."'";
         
         $query .= " WHERE dbmode<>'DELETED'";
         
         // it's assumed the caller will include % where needed in the name variable
         if($name!=NULL) $query .= " AND LOWER(".$qs['name'].") LIKE '".strtolower(convertString($name))."'";
         if($notname!=NULL) $query .= " AND LOWER(".$qs['name'].") NOT LIKE '".strtolower(convertString($notname))."'";
         if($byuserid!=NULL) $query .= " AND userid=".$byuserid;
         
         if(isset($qs['enabled'])) $query .= " AND LOWER(".$qs['enabled'].")='yes'";
         
         // version can be a specific number, "active", or "latest" for the most recent
         if(isset($qs['version']) && $version!=NULL && is_numeric($version)) $query .= " AND ".$qs['version']."=".$version;
         else if(isset($qs['verstatus']) && $version!=NULL && 0==strcmp(strtolower(trim($version)),"active")) $query .= " AND ".$qs['verstatus']."='ACTIVE'";

         
         $obcount = 0;
         $query .= " ORDER BY ";
         if(isset($qs['version'])) {
            if($obcount>0) $query .= ", ";
            $query .= $qs['version']." DESC";
            $obcount++;
         }
         if(isset($qs['sequence'])) {
            if($obcount>0) $query .= ", ";
            $query .= $qs['sequence']." ASC";
            $obcount++;
         }
         
         if($obcount>0) $query .= ", ";
         $query .= "created DESC";
         $obcount++;
         
         $query .= " LIMIT 0,".$limit.";";
         $resp['query'] = $query;
         
         if($testing) print "<br>\nsearchNVP query: ".$query."<br>\n";

         $rows = $dbi->queryGetResults($query);
         
         if($rows!=NULL && count($rows)>0) {
            // Check if only a single version is requested
            $newrows = array();
            for($i=0;$i<count($rows);$i++) {
               $addrow = TRUE;
               
               if($singleversion!=NULL && $singleversion==1 && isset($qs['version'])){
                  if($rows[0]['version'] != $rows[$i]['version']) $addrow = FALSE;
               }
               
               if($addrow) $newrows[] = $rows[$i];
            }
            $resp['rows'] = $newrows;
            $resp['wd_id'] = $webdata['wd_id'];
            $resp['wdname'] = $webdata['name'];
            
            if($lookupuser==1 && $rows[0]['value']!=NULL && is_numeric($rows[0]['value'])) {
               // assumes value is a userid
               $ua = new UserAcct();
               $resp['user'] = $ua->getUser($rows[0]['value']);
            } else if($adduser==1 && $rows[0]['userid']!=NULL && is_numeric($rows[0]['userid'])) {
               // only returns for first response
               $ua = new UserAcct();
               $resp['user'] = $ua->getUser($rows[0]['userid']);
            }
         }
         return $resp;
      }
      
   }
?>
