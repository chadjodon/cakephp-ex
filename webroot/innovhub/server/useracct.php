<?php

//--------------------------------------------------------------------------------
// UserAcct class
// This class represents the accounts users use to log into the website
// test 123
//--------------------------------------------------------------------------------

class UserAcct
{
   function track($method,$notes,$action="Update Account",$userid=NULL,$lastupdateby=NULL){
      $ver = new Version();
      if ($ver->getValue("trackactivity")==1) {
         $template = new Template();
         $jsftrack2 = "By user: ".isLoggedOn();
         if($lastupdateby==NULL) $lastupdateby = isLoggedOn()."-".date("Y-m-d H:i:s");
         $template->trackItem("Update Account","useracct:".$method,$notes,$jsftrack2,$lastupdateby,NULL,NULL,NULL,NULL,$userid);
      }
   }


   // Edit the following list to rename the user account access levels
   function getLevels () {
      $levels["Access to Admin"] = 1;
      $levels["View Survey/Data"] = 2;
      $levels["Content Activate"] = 3;
      $levels["Website Themes"] = 4;
      $levels["System Props"] = 5;
      $levels["Approve Users"] = 6;
      $levels["Extract Data"] = 7;
      $levels["TBD 8"] = 8;
      $levels["Custom Options"] = 9;
      $levels["E-Commerce"] = 10;
      $levels["User management"] = 11;
      $levels["Delete records"] = 12;
      return $levels;
   }

   function getUAPointLevels () {
      $levels[1] = "GENADMIN";
      $levels[2] = "SURVEY";
      $levels[3] = "CMS";
      $levels[4] = "THEME";
      $levels[5] = "SYSPROP";
      $levels[6] = "USERS";
      $levels[7] = "EXTRACT";
      $levels[8] = "t8";
      $levels[9] = "CUSTOM";
      $levels[10] = "ECOMM";
      $levels[11] = "USER";
      $levels[12] = "SUADMIN";
      for ($i=1; $i<=12; $i++) $levels[$levels[$i]]=$i;
      return $levels;
   }

   function addUserEmailOnly($addLevel1=NULL,$overrideemail=FALSE){
      $ans = $this->addUser($addLevel1,$overrideemail, TRUE);
      if($ans>0) return $ans;
      else return FALSE;
      
      /*
      $ss = new Version();
      if ($addLevel1==NULL) {
         $addLevel1=FALSE;
         if ($ss->getValue("AddLevel1ToNewUsers")==1) $addLevel1=TRUE;
      }

      
      $refsrc    = convertString(trim(strtolower(getParameter("refsrc"))));
      $email     = convertString(trim(strtolower(getParameter("email"))));
      $lt = strpos($email,"<");
      $gt = strpos($email,">");
      if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) $email = substr($email,$lt+1,$gt-$lt-1);

      $username     = convertString(trim(getParameter("username")));

      $email2     = convertString(trim(strtolower(getParameter("email2"))));
      $lt = strpos($email2,"<");
      $gt = strpos($email2,">");
      if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) $email2 = substr($email2,$lt+1,$gt-$lt-1);

      $fname     = convertString(trim(getParameter("fname")));
      $lname     = convertString(trim(getParameter("lname")));
      $name     = convertString(trim(getParameter("name")));
      if ($fname==NULL && $lname==NULL && $name!=NULL) {
         $namearr = separateStringBy($name," ");
         $fname = $namearr[0];
         $lname = $namearr[1];
      }
      $company   = convertString(trim(getParameter("company")));
      $title   = convertString(trim(getParameter("title")));
      $alive     = trim(getParameter("alive"));
      $siteid  = trim(getParameter("siteid"));
      $ownersite  = trim(getParameter("ownersite"));
      $parentid  = trim(getParameter("parentid"));
      $parentid2  = trim(getParameter("parentid2"));
      $website   = trim(getParameter("website"));
      $usertype  = trim(getParameter("usertype"));
      $phonenum  = trim(getParameter("phonenum"));
      $phonenum2  = trim(getParameter("phonenum2"));
      $phonenum3  = trim(getParameter("phonenum3"));
      $phonenum4  = trim(getParameter("phonenum4"));
      $addr1     = convertString(trim(getParameter("addr1")));
      $addr2     = convertString(trim(getParameter("addr2")));
      $city      = trim(getParameter("city"));
      $state     = trim(getParameter("state"));
      $country     = trim(getParameter("country"));
      $zip       = trim(getParameter("zip"));
      if ($zip == NULL) $zip="00000";
      
      $field1    = trim(getParameter("field1"));
      $field2    = trim(getParameter("field2"));
      $field3    = trim(getParameter("field3"));
      $field4    = trim(getParameter("field4"));
      $field5    = trim(getParameter("field5"));
      $field6    = trim(getParameter("field6"));

      $age       = trim(getParameter("age"));
      $gender    = trim(getParameter("gender"));
      $marital   = trim(getParameter("marital"));
      $edu       = trim(getParameter("edu"));

      $notes     = convertString(trim(strtolower(getParameter("notes"))));

      $password="";
 
      
      
     $valid = new Validator;
      if (($valid->validemail($email) && !$this->userExists($email)) || $overrideemail){
        $userid = $this->addAccount($email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $age, $gender, $marital, $edu, $nletter, FALSE, $addLevel1, $notes, $usertype, $company, $parentid, $website, $parentid2, $alive, $refsrc, $country, $title, $overrideemail, NULL, $ownersite, $siteid, $email2, $username, $field1, $field2, $field3, $field4, $field5, $field6);
        return $userid;
      } else {
         return FALSE;
      }
      */
   }

    function addUserViaAdmin($addLevel1=NULL) {
       return $this->addUser($addLevel1,TRUE,TRUE);
    }

    function addUser($addLevel1=NULL,$overrideemail=FALSE, $emailonly=FALSE) {
        if ($addLevel1==NULL) {
            $addLevel1=FALSE;
            $ss = new Version();
            if ($ss->getValue("AddLevel1ToNewUsers")==1) $addLevel1=TRUE;
        }

        $valid = new Validator;

        $notes    = convertString(trim(getParameter("notes")));
        $refsrc    = convertString(trim(strtolower(getParameter("refsrc"))));
        $confirmemail = trim(getParameter("confirmemail"));
        $cemail    = trim(strtolower(getParameter("cemail")));
        $email     = trim(strtolower(getParameter("email")));
         $lt = strpos($email,"<");
         $gt = strpos($email,">");
         if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) $email = substr($email,$lt+1,$gt-$lt-1);
         $email = convertString($email);
        $email2    = trim(strtolower(getParameter("email2")));
         $lt = strpos($email2,"<");
         $gt = strpos($email2,">");
         if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) $email2 = substr($email2,$lt+1,$gt-$lt-1);
         $email2 = convertString($email2);
         
        $fname     = convertString(trim(getParameter("fname")));
        $lname     = convertString(trim(getParameter("lname")));
        $name     = convertString(trim(getParameter("name")));
        if ($fname==NULL && $lname==NULL && $name!=NULL) {
           $namearr = separateStringBy($name," ",NULL,TRUE);
           $fname = $namearr[0];
           $lname = $namearr[1];
        }

        $username  = convertString(trim(getParameter("username")));
        $company   = convertString(trim(getParameter("company")));
        $title     = convertString(trim(getParameter("title")));
        $alive     = trim(getParameter("alive"));
       if ($alive===NULL || 0==strcmp(trim($alive),"") || $alive!=0) $alive=1;
       else $alive=0;

        $siteid  = trim(getParameter("siteid"));
        $ownersite  = trim(getParameter("ownersite"));
        $parentid  = trim(getParameter("parentid"));
        $parentid2  = trim(getParameter("parentid2"));
        $website   = trim(getParameter("website"));
        $usertype  = trim(getParameter("usertype"));
        $phonenum  = trim(getParameter("phonenum"));
        $phonenum2  = trim(getParameter("phonenum2"));
        $phonenum3  = trim(getParameter("phonenum3"));
        $phonenum4  = trim(getParameter("phonenum4"));
        $addr1     = convertString(trim(getParameter("addr1")));
        $addr2     = convertString(trim(getParameter("addr2")));
        $city      = convertString(trim(getParameter("city")));
        $state     = trim(getParameter("state"));
        $zip       = trim(getParameter("zip"));
        $country   = trim(getParameter("country"));

         $field1    = trim(getParameter("field1"));
         $field2    = trim(getParameter("field2"));
         $field3    = trim(getParameter("field3"));
         $field4    = trim(getParameter("field4"));
         $field5    = trim(getParameter("field5"));
         $field6    = trim(getParameter("field6"));
        
        $age       = trim(getParameter("age"));
        $gender    = trim(getParameter("gender"));
        $marital   = trim(getParameter("marital"));
        $edu       = trim(getParameter("edu"));
        
        if ($zip == NULL) $zip="00000";

        $password  = trim(getParameter("password"));
        $cpassword = trim(getParameter("cpassword"));
        if($emailonly) {
           if($password==NULL) $password = "emailonly123";
           if($cpassword==NULL) $cpassword = $password;
        }

        $nletter   = trim(getParameter("nletter"));

        if (0!=strcmp($nletter,"YES")) $nletter="NO";
         if (!$overrideemail && !$valid->validemail($email)) {
            //print "\n<!-- Not a valid email.  UserAcct:addUser() -->\n";
            return -1;
         } else if ($confirmemail==1 && 0!=strcmp($email,$cemail)) {
            //print "\n<!-- Email/confirm email not successful.  UserAcct:addUser() -->\n";
            return -2;
         } else if ($this->userProfileExists($email)) {
            //print "\n<!-- User already has account.  UserAcct:addUser() -->\n";
            return -3;
         } else if ($emailOnly && $this->userExists($email)) {
            //print "\n<!-- Email already exists -->\n";
            return -5;
         } else if (!$valid->validpassword($password,$cpassword)) {
            //print "\n<!-- Problem with password validation.  UserAcct:addUser() -->\n";
            return -4;
         } else {
      	  if ($this->userExists($email)){
            $u = $this->getUserByEmail($email);
            $userid = $u['userid'];
            $this->modifyUserExplicit($userid,$email,$fname,$lname,$age,$gender,$marital,$edu,$nletter,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$usertype,$company,$website,$alive, $country, $title, NULL,NULL, NULL, $ownersite, $siteid, $email2, $username, $field1, $field2, $field3, $field4, $field5, $field6);
            $this->modifyPassword($password, $cpassword, "", $userid, TRUE);
           } else {
            $userid = $this->addAccount($email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $age, $gender, $marital, $edu, $nletter, TRUE, $addLevel1,$notes,$usertype,$company,$parentid,$website,$parentid2,$alive,$refsrc, $country, $title, $overrideemail, NULL, $ownersite, $siteid, $email2, $username, $field1, $field2, $field3, $field4, $field5, $field6);
           }
            //print "\n<!-- userid: ".$userid." -->\n";
            return $userid;
        }
    }

    function grantAdminAccess($userid, $ulevel=1) {
       $dbLink = new MYSQLaccess;
       //$query = "UPDATE useracct SET ulevel=".$ulevel." WHERE userid=".$userid.";";
       //$dbLink->update($query);

      //new method...
      $sql = "DELETE FROM useraccess WHERE userid=".$userid." AND sys='ADMIN';";
      $dbLink->delete($sql);
      if ($ulevel>0) $this->addUserAccess($userid,'ADMIN',$ulevel);
    }

   function addNotes($userid, $notes) {
      $names = array();
      $values = array();
      
      $names[] = "notes";
      $values[] = $notes;
      
      $this->updateMultipleFields($userid,$names,$values,NULL,"notes");      
   }

    function updateSingleField($userid,$field,$value,$lastupdateby=NULL){
      $this->updateField($userid,$field,$value,FALSE,TRUE,TRUE,$lastupdateby);
    }

   function updateField($userid, $field, $value, $pub=FALSE, $checkprops=TRUE, $updateflag=TRUE, $lastupdateby=NULL) {
      $names = array();
      $values = array();
      $names[] = $field;
      $values[] = $value;
      $this->updateMultipleFields($userid,$names,$values,$lastupdateby,"f",$pub,$checkprops,$updateflag);
   }


   function setLastUpdated($userid,$lastupdateby=NULL,$source=NULL){
      //print "\n***chj*** setLastUpdated: ".$lastupdateby."\n";
      $names = array();
      $values= array();
      $this->updateMultipleFields($userid,$names,$values,$lastupdateby,$source);
   }

   function updateMultipleFields($userid,$names,$values,$lastupdateby=NULL,$source=NULL,$pub=FALSE, $checkprops=TRUE, $updateflag=TRUE, $checkuseracct=TRUE){
      //if ($names==NULL || count($names)<1) return NULL;
      if ($userid==NULL) return NULL;
      $rnotes = implode(",",$names).";".implode(",",$values);
      
      $user = $this->getUser($userid);
      
      $unames = $names;
      $uvalues = $values;
      
      $dbmode = $user['dbmode'];
      if($dbmode==NULL || 0==strcmp($dbmode,"APPROVED")) $dbmode = "UPDATED";

      if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
      if($lastupdateby==NULL) $lastupdateby = "0";
      $lastupdateby .= "-".date("Ymd");
      if($source!=NULL) $lastupdateby .= "-".$source;
      
      $dbLink = new MYSQLaccess;
      
      if ($checkprops) {
         $wdObj = new WebsiteData();
         $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
         if ($webdata != NULL) {
            $qs = $wdObj->getFieldLabels($webdata['wd_id'],TRUE,TRUE);
            
            // Make sure there is an entry for this user
            $results = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
            if ($results==NULL || count($results)<1 || $results[0]['wd_row_id']==NULL) {
               $wdObj->addRow($webdata['wd_id'], $userid);
            }
            
            // Pick the correct table name (could be public)
            $query = "UPDATE wd_".$webdata['wd_id'];
            if ($pub) {
               // The row must already be in the pub table of this does nothing
               $wdObj->startCloning($webdata['wd_id']);
               $query .= "_pub";
            }
            
            $query .= " SET ";
            $setquery = "";

            //Make sure we capture any fields not set in jdata
            $unames = array();
            $uvalues = array();
            
            for ($i=0; $i<count($names); $i++) {
               if(isset($qs[strtolower(trim($names[$i]))])) {
                  $value = $values[$i];
                  $qfieldid = $qs[strtolower(trim($names[$i]))];
                  $fld = $wdObj->getField($webdata['wd_id'], $qfieldid);
                  
                  // Convert the value if necessary
                  if(strcmp($fld['field_type'],"FOREIGN")==0 || strcmp($fld['field_type'],"FOREIGNCB")==0) {
                     $value = $wdObj->reverseConvertForeignWD($fld['question'],$value);
                  } else if(strcmp($fld['field_type'],"DROPDOWN")==0 && $value==1 && strpos(strtolower($fld['question']),"yes")!==FALSE) {
                     $value = "YES";
                  } else if(strcmp($fld['field_type'],"SNGLCHKBX")==0 && $value==1) {
                     $value = "YES";
                  }
                  
                  if(strlen($setquery)>2) $setquery .= ", ";
                  $value = convertString(trim($value));
                  if($value!=NULL) $setquery .= $qfieldid."='".$value."'";
                  else $setquery .= $qfieldid."=NULL";
               } else {
                  // Not found in properties, must be in useracct table
                  $unames[] = $names[$i];
                  $uvalues[] = $values[$i];
               }
            }
            
            //Update status and remember this event
            if($updateflag) {
               if(strlen($setquery)>2) $setquery .= ", ";
               $setquery .= "dbmode='".$dbmode."'";
               $setquery .= ", lastupdate=NOW()";
               $setquery .= ", lastupdateby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdateby,' ')),1,2048)";
            }

            //Only do an update if there's anything to set
            if(strlen($setquery)>2) {
               $query .= $setquery;
               $query .= " WHERE userid=".$userid.";";
               $dbLink->update($query);
            }
         }
      }

      // UPDATE useracct table
      if($checkuseracct) {
         $query = "UPDATE useracct";
         if ($pub) $query .= "_pub";
         $query .= " SET ";
         $setquery = "";
         
         for ($i=0; $i<count($unames); $i++) {
            $value = convertString(trim($uvalues[$i]));
            if(strlen($setquery)>2) $setquery .= ", ";
            if($value!=NULL) $setquery .= $unames[$i]."='".$value."'";
            else $setquery .= $unames[$i]."=NULL";
         }
         
         if($updateflag) {
            if(strlen($setquery)>2) $setquery .= ", ";
            $setquery .= "dbmode='".$dbmode."'";
            $setquery .= ", lastupdated='".getDateForDB()."'";
            $setquery .= ", lastupdatedby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdatedby,' ')),1,2048)";
         }
         
         if(strlen($setquery)>2) {
            $query .= $setquery;
            $query .= " WHERE userid=".$userid.";";
            $dbLink->update($query);
         }
      }
      
      // Remember that we updated this
      $this->track("updateMultipleFields",$rnotes,"Update Account",$userid,$lastupdateby);
   }

   function setBinaryBitField($userid, $field, $position){
      if ($userid==NULL) return NULL;
      if ($field==NULL) return NULL;
      if ($position===NULL || !is_numeric($position)) return NULL;
      
      $dbmode = $this->getUserField($userid,"dbmode");
      
      $dbLink = new MYSQLaccess;
      $shiftpos = pow(2,((int) $position));
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."'";
      if(0==strcmp($dbmode,"APPROVED")) $query .= ", dbmode='UPDATED'";
      $query .= ", ".$field."=(".$field."+".$shiftpos.") WHERE (FLOOR(".$field."/".$shiftpos.") % 2)=0 AND userid=".$userid;
      $dbLink->update($query);

      $jsftrack1 = "Updating user bit field: ".$userid;
      $this->track("setBinaryBitField",$jsftrack1,"Update Account",$userid);
   }

   function unsetBinaryBitField($userid, $field, $position){
      if ($userid==NULL) return NULL;
      if ($field==NULL) return NULL;
      if ($position===NULL || !is_numeric($position)) return NULL;
      
      $dbmode = $this->getUserField($userid,"dbmode");
      
      $dbLink = new MYSQLaccess;
      $shiftpos = pow(2,((int) $position));
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."'";
      if(0==strcmp($dbmode,"APPROVED")) $query .= ", dbmode='UPDATED'";
      $query .= ", ".$field."=(".$field."-".$shiftpos.") WHERE (FLOOR(".$field."/".$shiftpos.") % 2)=1 AND userid=".$userid;
      $dbLink->update($query);

      $jsftrack1 = "Updating user bit field: ".$userid;
      $this->track("unsetBinaryBitField",$jsftrack1,"Update Account",$userid);
   }

   //------------------------------
   // User Access is handled in a leveled system.  The ulevel field of the useracct table holds an integer.
   // Inside this integer is a binary number with up to 12 bits.  A bit can be 0 or 1 indicating if that
   // level is on or off.  Every level is independent and they do not need to grow on each other.  This means
   // that user with access to level 3 does not neccessarily have (nor need) access to level 2 - they can be
   // two separate parts of the system.  
   // For example (assuming right most bit is least significant), 000001010011
   // would equate to the decimal number 83, which is stored in the ulevel field and represents the
   // following level access: 1,2,5, and 7 since those bits are 1's.  canUserDelete() method assumes that
   // level 12 is the bit that indicates a user has the ability to delete a record in your system.
   //------------------------------

   function getUserAccessLevels($userid) {
      $curr_level_arr = $this->getUsersAccessPointsFor($userid,'ADMIN');
      $curr_level = $curr_level_arr[0]['id'];
      return $this->breakOutLevelsFromInt($curr_level);
   }

   function breakOutLevelsFromInt($curr_ulevel=0) {
      $access = array();
      if ($curr_ulevel == NULL || $curr_ulevel==0) return $access;

      for ($i=0; $i<12; $i++) {
         if (($curr_ulevel % 2) > 0) {
            $access[($i+1)] = 1;
            $curr_ulevel--;
         }
         else $access[($i+1)] = 0;
         $curr_ulevel = $curr_ulevel / 2;
      }
      return $access;
   }

   function setUserAccessLevels($userid,$levels) {
      $curr_ulevel = 0;
      foreach($levels as $key => $value) {
         if ($value > 0) {
            $exponent = $value-1;
            $power = pow(2,$exponent);
            $curr_ulevel = $curr_ulevel + $power;
         }
      }
      $this->grantAdminAccess($userid, $curr_ulevel);
   }

   function doesUserHaveAccessToLevel($userid,$level) {
      $levels = $this->getUserAccessLevels($userid);
      if (!isset($levels[$level])) return FALSE;
      else return ($levels[$level]==1);
   }

    function canUserDelete($userid) {
      return $this->doesUserHaveAccessToLevel($userid,12);
    }

    function getOrganizations($table="useracct") {
       $dbLink = new MYSQLaccess;
       $query = "SELECT u.userid, u.email, u.fname, u.lname, u.company, u.addrid, u.password, u.nletter, u.other, u.orgid, u.age, u.gender, u.edu, u.marital, u.created, u.login, u.lastlogin FROM ".$table." u WHERE u.usertype='org' ORDER BY u.fname, u.lname;";
       //print $query;
       $users = $dbLink->queryGetResults($query);
       return $users;
    }

    function isOrganization($userid,$table="useracct") {
      if ($userid==NULL) return FALSE;

      $dbLink = new MYSQLaccess;
      $query = "SELECT u.usertype FROM ".$table." u WHERE u.usertype='org' AND u.userid=".$userid.";";
      //print $query;
      $users = $dbLink->queryGetResults($query);
      return (count($users)>0);
    }

    function getAdminUsers($table="useracct") {
       $dbLink = new MYSQLaccess;
       //$query = "SELECT * FROM useracct WHERE (ulevel IS NOT NULL) AND (ulevel > 0) ORDER BY email;";
       $query = "SELECT u.userid, u.email, u.fname, u.lname,u.password, u.nletter, u.other, u.orgid, u.age, u.gender, u.edu, u.marital, u.created, u.login, u.lastlogin FROM ".$table." u, useraccess a WHERE (u.userid=a.userid) AND (a.sys='ADMIN') AND (a.id > 0) ORDER BY u.email;";
       //print $query;
       $users = $dbLink->queryGetResults($query);
       return $users;
    }

    function isUserAdmin($userid) {
      if ($userid == null) return FALSE;
      $curr_level_arr = $this->getUsersAccessPointsFor($userid,'ADMIN');
      
      //print "\n<!-- ***chj*** access points:\n";
      //print_r($curr_level);
      //print "\n-->\n";
      
      $curr_level = $curr_level_arr[0]['id'];      
      if ($curr_level != NULL && intval($curr_level) > 0) return TRUE;
      else return FALSE;
    }

   //----------------------------
   // User accessibility is also generalized per a particular record within a system.  For example, if you
   // do not want to give a user access to an entire system using the levels above (ie full delete access
   // to all records) you can assign access to particular records within that system.
   // To keep the user account subcomponent decoupled from the rest of the subsystems, the access system
   // and record ids can be arbitrary.  This gives an unlimited number of subsystems access to this system.
   //----------------------------
   function getUsersAccessPoints($userid){
      if ($userid==NULL) return NULL;
      $query = "SELECT * from useraccess where userid=".$userid." order by sys";
       $dbLink = new MYSQLaccess;
       $points = $dbLink->queryGetResults($query);
       return $points;
   }

   function getUsersAccessPointsFor($userid,$sys){
      if ($userid==NULL) return NULL;
      $query = "SELECT * from useraccess where sys='".$sys."' AND userid=".$userid." ORDER BY id;";
      //print "\n<!-- query: ".$query." -->\n";
       $dbLink = new MYSQLaccess;
       $points = $dbLink->queryGetResults($query);
       return $points;
   }
   
   function internalJSONaccess() {
      $query = "SELECT u.userid, u.token from useraccess a, useracct u where a.sys='ADMIN' AND CAST(id AS SIGNED)>1 LIMIT 0,1;";
      //print "\n<!-- query: ".$query." -->\n";
       $dbLink = new MYSQLaccess;
       $points = $dbLink->queryGetResults($query);
       return $points[0];
   }

   function canAdminUserAccessSite($userid,$siteid) {
      $canaccess = FALSE;
      $defaultsiteid = NULL;
      $ctx = new Context();
      $siteid = $ctx->transformSiteid($siteid);
      $sites = $this->getUsersAccessPointsFor($userid,"ADMINSITEID");
      if ($sites!=NULL && count($sites>0)) {
         $i = 0;
         while ($i<count($sites) && !$canaccess) {
            $tempsiteid = $ctx->transformSiteid($sites[$i]['id']);
            if ($i==0) $defaultsiteid = $tempsiteid;
            if ($tempsiteid==-1) $canaccess=TRUE;
            if ($tempsiteid==$siteid) $canaccess=TRUE;
            $i++;
         }
      } else {
         $canaccess = TRUE;
         $defaultsiteid = -1;
      }
      $results['canaccess'] = $canaccess;
      $results['defaultsiteid'] = $defaultsiteid;
      return $results;
   }

   function buildPrivacySQLCheck($userid,$adminprivacy=FALSE){
      $addlSQL = "";
      $ua = new UserAcct();

      if ($ua->canUserDelete($userid)) return NULL;
      else if ($ua->isUserAdmin($userid)) {
         if ($adminprivacy) {
            $apoints = $this->getUAPointLevels();
            $addlSQL = "privacy>=0";
            $lvls = $this->getUserAccessLevels($userid);
            for ($i=1; $i<count($lvls); $i++) {
               if ($lvls[$i]==1) $addlSQL .= " OR (privacy=-1 AND adminprivacy=".$i.")";
               else $addlSQL .= " OR (privacy=-1 AND EXISTS(select * from useraccess where userid=".$userid." AND sys='".$apoints[$i]."') AND adminprivacy=".$i.")";
            }
            return $addlSQL; 

         } else {
            return "privacy>=-1";
         }
      }
      else {
         $addlSQL = "privacy=0";
         $levels = $this->getUsersAccessPointsFor($userid,"WEBSITE");
         if (count($levels)>0) {
            $highestLvl = $levels[count($levels)-1]['id'];
            for ($i=1; $i<=$highestLvl; $i++) {
               $addlSQL .= " OR privacy=".$i;
            }
         }
         return $addlSQL; 
      }
   }

   function canUserAccessWebsiteLevel($userid,$level) {
      $answer = false;
      if ($this->canUserDelete($userid)) $answer=true;
      else if ($this->isUserAdmin($userid) && $level>=-1) $answer=true;
      else {
         $levels = $this->getUsersAccessPointsFor($userid,"WEBSITE");
         if (count($levels)>0) {
            $highestLvl = $levels[count($levels)-1]['id'];
            $answer = ($highestLvl>=$level);
         }
      }
      return $answer; 
   }

   function isUserAccessible($userid,$sys,$id){
      //print "\n<!-- UserAcct::isUserAccessible(".$userid.", ".$sys.", ".$id.") -->\n";
      if ($userid==NULL) return NULL;
      $query = "SELECT * from useraccess where sys='".$sys."' AND id='".$id."' AND userid=".$userid;
       $dbLink = new MYSQLaccess;
       $users = $dbLink->queryGetResults($query);
       //print "\n<!-- UserAcct::isUserAccessible query: '".$query."' -->\n";
      if ($users == NULL || count($users)<1) {
         //print "\n<!-- UserAcct::isUserAccessible returning FALSE -->\n";
         return FALSE;
      } else {
         //print "\n<!-- UserAcct::isUserAccessible returning TRUE -->\n";
         return TRUE;
      }
   }

   function usersAccessible($sys,$id){
      $query = "SELECT u.email, u.userid from useracct u where u.userid in (SELECT a.userid from useraccess a where a.userid=u.userid AND sys='".$sys."' AND id='".$id."')";
       $dbLink = new MYSQLaccess;
       $users = $dbLink->queryGetResults($query);
      return $users;
   }

   function usersNotAccessible($sys,$id){
      $query = "SELECT u.email, u.userid from useracct u where u.userid not in (SELECT a.userid from useraccess a WHERE a.userid=u.userid AND sys='".$sys."' AND id='".$id."')";
       $dbLink = new MYSQLaccess;
       $users = $dbLink->queryGetResults($query);
      return $users;
   }

   function addUserAccess($userid,$sys,$id) {
      //print "\n<!-- UserAcct::addUserAccess(".$userid.", ".$sys.", ".$id.") -->\n";
      if (!$this->isUserAccessible($userid,$sys,$id)) {
         $sql = "INSERT INTO useraccess (userid,sys,id) VALUES (".$userid.",'".$sys."','".$id."')";
         $dbLink = new MYSQLaccess;
         $dbLink->insert($sql);
         //print "\n<!-- UserAcct::addUserAccess query:".$sql." -->\n";
         $jsftrack1 = "Add user access: ".$userid." sys:".$sys." id:".$id;
         $this->track("addUserAccess",$jsftrack1,"Update Account",$userid);
      }
   }

   function setWebsiteAccess($userid,$level) {
         $dbLink = new MYSQLaccess;
         $sql = "DELETE FROM useraccess WHERE userid=".$userid." AND sys='WEBSITE';";
         $dbLink->delete($sql);
         if ($level>0) $this->addUserAccess($userid,'WEBSITE',$level);
   }

   function removeUserAccess($userid,$sys,$id) {
      if ($this->isUserAccessible($userid,$sys,$id)) {
         $sql = "DELETE from useraccess WHERE userid=".$userid." AND sys='".$sys."' AND id='".$id."'";
         $dbLink = new MYSQLaccess;
         $dbLink->delete($sql);

         $jsftrack1 = "Remove user access: ".$userid." sys:".$sys." id:".$id;
         $this->track("removeUserAccess",$jsftrack1,"Update Account",$userid);
      }
   }

   function addAccount($email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $age=null, $gender=null, $marital=null, $edu=null, $nletter=null, $encryptedPW=TRUE, $addLevel1=FALSE, $notes=NULL, $usertype=NULL, $company=NULL, $parentid=NULL, $website=NULL, $parentid2=NULL, $alive="1", $refsrc=NULL, $country="US", $title=NULL, $overrideemail=FALSE, $password2=NULL, $ownersite=NULL, $siteid=NULL, $email2=NULL, $username=NULL, $field1=NULL, $field2=NULL, $field3=NULL, $field4=NULL, $field5=NULL, $field6=NULL, $createdOverride=NULL, $token=NULL, $lastupdateby=NULL) {
       //print "\n<!-- email: ".$email." password: ".$password." fname: ".$fname." lname: ".$lname." phonenum: ".$phonenum." phone2: ".$phonenum2;
       //print " phone3: ".$phonenum3." phone4: ".$phonenum4." addr1: ".$addr1." addr2: ".$addr2." city: ".$city." state: ".$state." zip: ".$zip;
       //print " age: ".$age." gender: ".$gender." marital: ".$marital." edu: ".$edu." nletter: ".$nletter." encPW: ".$encryptedPW;
       //print " addlvl1: ".$addLevel1." notes: ".$notes." type: ".$usertype." company: ".$company." parent: ".$parentid." website: ".$website;
       //print " parent2: ".$parentid2." alive: ".$alive." refsrc: ".$refsrc." country: ".$country." title: ".$title." override: ".$overrideemail;
       //print " pw2: ".$password2." ownersite: ".$ownersite." siteid: ".$siteid." email2: ".$email2." username: ".$username." -->\n";

      $insFields = "";
      $insVals = "";
      if ($token!=NULL) {
         $insFields .= ", token";
         $insVals .= ",'".convertString($token)."'";
      } else {
         $insFields .= ", token";
         $insVals .= ",'".getRandomNum()."'";
      }
      if ($field1!=NULL) {
         $insFields .= ", field1";
         $insVals .= ",'".convertString($field1)."'";
      }
      if ($field2!=NULL) {
         $insFields .= ", field2";
         $insVals .= ",'".convertString($field2)."'";
      }
      if ($field3!=NULL) {
         $insFields .= ", field3";
         $insVals .= ",'".convertString($field3)."'";
      }
      if ($field4!=NULL && is_numeric($field4)) {
         $insFields .= ", field4";
         $insVals .= ",".$field4;
      }
      if ($field5!=NULL && is_numeric($field5)) {
         $insFields .= ", field5";
         $insVals .= ",".$field5;
      }
      if ($field6!=NULL && is_numeric($field6)) {
         $insFields .= ", field6";
         $insVals .= ",".$field6;
      }

       $tempaccount = FALSE;
       if ($email==NULL && !$overrideemail) {
          return NULL;
       } else if ($email==NULL && $overrideemail){
         $email = "sys".getRandomNum(date("l").date("s"))."@jstorefrontdummy.com";
         while ($this->userExists($email)) $email = "sys".getRandomNum(date("l").date("s"))."@jstorefrontdummy.com";
         $tempaccount = TRUE;
       }

      if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
      if($lastupdateby==NULL) $lastupdateby = "0";
      $lastupdateby .= "-".date("Ymd");
      $lastupdateby .= "-n";
      $insFields .= ",lastupdatedby";
      $insVals .= ",'".convertString(trim($lastupdateby))."'";
       
       //if ($username==NULL) $username=$email;
       $fname = trim(convertString($fname));
       $lname = trim(convertString($lname));
       if ($fname!=NULL) $fname = strtoupper(substr($fname,0,1)).substr($fname,1);
       if ($lname!=NULL) $lname = strtoupper(substr($lname,0,1)).substr($lname,1);
       $insFields .= ",fname";
       $insVals .= ",'".convertString(trim($fname))."'";
       $insFields .= ",lname";
       $insVals .= ",'".convertString(trim($lname))."'";

       if ($usertype==NULL) $usertype="user";

       if ($siteid==NULL) {
         $ss = new Version();
         if ($ss->getValue("multisitesuseremails")==1) {
            $ctx = new Context();
            $sitearr = $ctx->getSiteContext();
            $siteid = $sitearr[0]['siteid'];
         } else $siteid=-1;
       }

       if ($parentid==NULL) $parentid=-1;
       if ($parentid2==NULL) $parentid2=-1;
       if ($alive===NULL || 0==strcmp(trim($alive),"") || $alive!=0) $alive=1;
       else $alive=0;

       $dbLink = new MYSQLaccess;

       $userCreated = getDateForDB();
       if ($createdOverride!=NULL) {
         $datearraytemp = separateStringBy($createdOverride," ");
         $datearray = separateStringBy($datearraytemp[0],"/");
         if ($datearray!=NULL && count($datearray)>2) {
            $m = (int) $datearray[0];
            $d = (int) $datearray[1];
            $y = (int) $datearray[2];
            if ($m<10) $m = "0".$m;
            if ($d<10) $d = "0".$d;
            if ($y<80) $y = "20".$y;
            else if ($y<100) $y = "19".$y;
            $userCreated = $y."-".$m."-".$d;
         }
       }

       if ($encryptedPW) $password = md5($password);
       if ($encryptedPW) $password2 = md5($password2);

       $insFields .= ",email";
       $insVals .= ",'".convertString(strtolower(trim($email)))."'";
       $insFields .= ",email2";
       $insVals .= ",'".convertString(strtolower(trim($email2)))."'";
       $insFields .= ",username";
       $insVals .= ",'".convertString(trim($username))."'";
       $insFields .= ",ownersite";
       $insVals .= ",'".convertString(trim($ownersite))."'";
       $insFields .= ",phonenum";
       $insVals .= ",'".convertString(trim($phonenum))."'";
       $insFields .= ",phonenum2";
       $insVals .= ",'".convertString(trim($phonenum2))."'";
       $insFields .= ",phonenum3";
       $insVals .= ",'".convertString(trim($phonenum3))."'";
       $insFields .= ",phonenum4";
       $insVals .= ",'".convertString(trim($phonenum4))."'";
       $insFields .= ",addr1";
       $insVals .= ",'".convertString(trim($addr1))."'";
       $insFields .= ",addr2";
       $insVals .= ",'".convertString(trim($addr2))."'";
       $insFields .= ",city";
       $insVals .= ",'".convertString(trim($city))."'";
       $insFields .= ",state";
       $insVals .= ",'".convertString(trim($state))."'";
       
       if(trim($zip)!=NULL) {
          if(strpos(trim($zip),"-")==4) $zip = "0".trim($zip);
          if(strlen(trim($zip))==4) $zip = "0".trim($zip);
          $insFields .= ",zip";
          $insVals .= ",'".convertString(trim($zip))."'";
       }
       
       $insFields .= ",website";
       $insVals .= ",'".convertString(trim($website))."'";
       $insFields .= ",country";
       $insVals .= ",'".convertString(trim($country))."'";
       $insFields .= ",notes";
       $insVals .= ",'".convertString(trim($notes))."'";
       $insFields .= ",company";
       $insVals .= ",'".convertString(trim($company))."'";

       $query = "INSERT INTO useracct (dbmode, password, password2, age, gender, marital, edu, login, created, lastupdated, nletter, usertype, title, parentid, parentid2, alive, refsrc, siteid".$insFields.") VALUES ";
       $query .= "('NEW', '".$password."', '".$password2."', '$age', '$gender', '$marital', '$edu', '$userCreated', '$userCreated', '$userCreated', '$nletter', '$usertype','$title',$parentid,$parentid2,$alive,'$refsrc',$siteid".$insVals.");";

       $userid = $dbLink->insertGetValue($query);

      $jsftrack1 = "Adding user account: ".$userid;
      $this->track("addAccount",$jsftrack1,"Add Account",$userid);

       if ($alive==1) $this->addReferral($_SESSION['ssnrem']['refuserid'],$userid,"New User");
       //print "\n<!-- query: ".$query." -->\n";
       $addrid = $this->addAddress($userid,convertString(strtolower(trim($email))),$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$website, $country);
       $this->setPrimaryAddress($userid,$addrid);
       if ($addLevel1) $this->setWebsiteAccess($userid,1);
       
      $wdObj = new WebsiteData();
      $wd = $wdObj->getWebData($usertype." properties");
      if($wd!=NULL && $wd['wd_id']>0) {
         $query1 = "INSERT INTO wd_".$wd['wd_id']." SET ";
         $query1 .= "lastupdate=NOW(), dbmode='NEW', ";
         $query1 .= "lastupdateby='".$lastupdateby."', ";
         $query1 .= "origemail='".getRandomNum()."', ";
         $query1 .= "userid=".$userid.";";
         $dbLink->insert($query1);
         //print "\n<!-- ***chj*** query: ".$query1." -->\n";
      }
       
       
       $this->remoteAddUpdate($email, $email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $gender, $usertype, $company, $website, $alive, $username);
       return $userid;
    }

   function addReferral($refuserid,$newuserid,$adminnotes=NULL){
      if ($refuserid==NULL || $newuserid==NULL || $newuserid<1) return NULL;
      $query .= "INSERT INTO referral (refuserid,newuserid,created,adminnotes) VALUES (".$refuserid.",".$newuserid.",NOW(),'".convertString($adminnotes)."');";
      $dbLink = new MYSQLAccess;     
      return $dbLink->insertGetValue($query);
   }

   function remoteAddUpdate($email, $newemail, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $gender=null, $usertype=NULL, $company=NULL, $website=NULL, $alive=1, $username=NULL) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($email)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=addupdateuser&email=".$email."&enc_password=".encrypt($GLOBALS['masterkey'],$password);
         $url .= "&username=".convertString(trim($username));
         $url .= "&newemail=".convertString(trim($newemail));
         $url .= "&fname=".convertString(trim($fname));
         $url .= "&lname=".convertString(trim($lname));
         $url .= "&phonenum=".convertString(trim($phonenum));
         $url .= "&phonenum2=".convertString(trim($phonenum2));
         $url .= "&phonenum3=".convertString(trim($phonenum3));
         $url .= "&phonenum4=".convertString(trim($phonenum4));
         $url .= "&addr1=".convertString(trim($addr1));
         $url .= "&addr2=".convertString(trim($addr2));
         $url .= "&city=".convertString(trim($city));
         $url .= "&state=".convertString(trim($state));
         $url .= "&zip=".convertString(trim($zip));
         $url .= "&gender=".convertString(trim($gender));
         $url .= "&usertype=".convertString(trim($usertype));
         $url .= "&company=".convertString(trim($company));
         $url .= "&website=".convertString(trim($website));
         $url .= "&alive=".convertString(trim($alive));
         $url .= "&refsrc=".convertString(trim(getBaseURL()));
         $url = str_replace(" ","%20",$url);
         //print "URL: ".$url."<BR>\n";
         $result = $reader->Parse($url);
         $stuff = $result['feed']['entry'];

         if (0==strcmp($stuff['content'],"FAIL")) return FALSE;
         else {
            $user = array();
            for ($i=0; $i<count($stuff); $i++) $user[$stuff[$i]['title']]=$stuff[$i]['content'];
            $localuser = $this->getUserByEmail($email);
            $this->updateField($localuser['userid'],"token",$user['token']);
            return $user['token'];
         }
      } else {
         return FALSE;
      }
   }

   function setPrimaryAddress($userid,$addrid){
      $jsftrack1 = "Updating user primary address: ".$userid;
      $this->track("setPrimaryAddress",$jsftrack1,"Update Account",$userid);

       if ($userid==NULL || $userid<1 || $addrid==NULL || $addrid<1) {
          return NULL;
       } else {
          $dbLink = new MYSQLaccess;
          $query = "UPDATE useracct set addrid=".$addrid." WHERE userid=".$userid.";";
          $dbLink->update($query);
          return $addrid;
       }
   }

   function activateAccount($userid, $updatestatus=TRUE){
      if ($userid==NULL) return FALSE;
            
      $dbmode = $this->getUserField($userid,"dbmode");
      
      $dbLink = new MYSQLaccess;
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', activated=1";
      if(0==strcmp($dbmode,"APPROVED") && $updatestatus) $query .= ", dbmode='UPDATED'";
      $query .= " WHERE userid=".$userid.";";
      $dbLink->update($query);

      //$jsftrack1 = "Updating user activated field: ".$userid;
      //$this->track("activateAccount",$jsftrack1);

      return TRUE;
   }
   
   function deactivateAccount($userid, $activatedstr="", $updatestatus=TRUE){
      if ($userid==NULL) return FALSE;
            
      $dbmode = $this->getUserField($userid,"dbmode");
      

      $dbLink = new MYSQLaccess;
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', activated=0, activatedstr='".convertString($activatedstr)."'";
      if(0==strcmp($dbmode,"APPROVED") && $updatestatus) $query .= ", dbmode='UPDATED'";
      
      $lastupdateby = isLoggedOn();
      if($lastupdateby==NULL) $lastupdateby = "0";
      $lastupdateby .= "-".date("Ymd");
      $lastupdateby .= "-da";
      $query .= ", lastupdatedby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdatedby,' ')),1,2048) ";
      
      $query .= " WHERE userid=".$userid.";";
      $dbLink->update($query);

      $jsftrack1 = "Deactivate account: ".$userid;
      $this->track("deactivateAccount",$jsftrack1,"Update Account",$userid);

      return TRUE;
   }
   
   function getUserActivated($userid,$table="useracct"){
      if ($userid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      $query = "SELECT activated FROM ".$table." WHERE userid=".$userid.";";
      $results = $dbLink->queryGetResults($query);
      return $results[0]['activated'];
   }

   function addAddress($userid,$email,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$website,$country){
      if ($userid==NULL) $userid = "-1";
       $dbLink = new MYSQLaccess;
       $query = "INSERT INTO addr (userid, email, phonenum, phonenum2, phonenum3, phonenum4, addr1, addr2, city, state, zip, country, website) VALUES "; 
       $query .= "($userid, '".convertString(strtolower(trim($email)))."', '$phonenum', '$phonenum2', '$phonenum3', '$phonenum4', '".convertString(trim($addr1))."', '".convertString(trim($addr2))."', '".convertString(trim($city))."', '$state', '$zip', '$country', '".convertString(trim($website))."');";
       $addrid = $dbLink->insertGetValue($query);
       return $addrid;
   }

    function swapPrimaryEmail($userid, $addToSession=TRUE) {
       if ($userid == NULL) return FALSE;
       $user = $this->getUser($userid);
       if ($user['email2'] == NULL || 0==strcmp("",trim($user['email2']))) return FALSE;
       $dbLink = new MYSQLaccess;
       $query = "SELECT userid FROM useracct WHERE email='".$user['email2']."' AND userid!=".$user['userid'].";";
       $results = $dbLink->queryGetResults($query);

       if ($results!=NULL && count($results)>0) return FALSE;

       $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', dbmode='UPDATED', email='".$user['email2']."', email2='".$user['email']."' WHERE userid=".$user['userid'].";";

       if ($addToSession) $this->addUserToSession($this->getUser($userid));

      $jsftrack1 = "Swapping user email: ".$userid;
      $this->track("swapPrimaryemail",$jsftrack1,"Update Account",$userid);

    }

    function modifyUser($addToSession=TRUE,$defaults=NULL) {
        $valid = new Validator;
        $userid = trim(getParameter("updateuserid"));
        if ($userid==NULL) $userid = trim(getParameter("userid"));
        
        //print "\n<!-- userid: ".$userid." -->\n";
        if ($userid == NULL) return FALSE;
        
        $propertiesonly   = trim(getParameter("propertiesonly"));
        $email     = convertString(strtolower(trim(getParameter("email"))));
        if ($email==NULL) $email=$defaults['email'];
        $email2     = convertString(strtolower(trim(getParameter("email2"))));
        if ($email2==NULL) $email2=$defaults['email2'];
        $fname     = convertString(trim(getParameter("fname")));
        if ($fname==NULL) $fname=$defaults['fname'];
        $lname     = convertString(trim(getParameter("lname")));
        if ($lname==NULL) $lname=$defaults['lname'];
        $company   = convertString(trim(getParameter("company")));
        if ($company==NULL) $company=$defaults['company'];
        $title     = convertString(trim(getParameter("title")));
        if ($title==NULL) $title=$defaults['title'];
        $alive     = trim(getParameter("alive"));

        $username  = convertString(trim(getParameter("username")));
        if ($username==NULL) $username=$defaults['username'];
        //else if(0==strmcp($username,"%%%EMPTY%%%")) $username = "";

        $parentid = NULL;
        $parentid2 = NULL;
        if ($this->isUserAdmin(isLoggedOn())){
           $parentid  = trim(getParameter("parentid"));
           $parentid2  = trim(getParameter("parentid2"));
        }
        $website   = trim(getParameter("website"));
        $usertype  = trim(getParameter("usertype"));
        $siteid  = trim(getParameter("siteid"));
        if ($siteid==NULL) $siteid=$defaults['siteid'];
        $ownersite  = trim(getParameter("ownersite"));
        if ($ownersite==NULL) $ownersite=$defaults['ownersite'];
        $phonenum  = trim(getParameter("phonenum"));
        if ($phonenum==NULL) $phonenum=$defaults['phonenum'];
        $phonenum2  = trim(getParameter("phonenum2"));
        if ($phonenum2==NULL) $phonenum2=$defaults['phonenum2'];
        $phonenum3  = trim(getParameter("phonenum3"));
        if ($phonenum3==NULL) $phonenum3=$defaults['phonenum3'];
        $phonenum4  = trim(getParameter("phonenum4"));
        if ($phonenum4==NULL) $phonenum4=$defaults['phonenum4'];
        $addr1     = convertString(trim(getParameter("addr1")));
        $addr2     = convertString(trim(getParameter("addr2")));
        $city      = convertString(trim(getParameter("city")));
        if ($city==NULL) $city=$defaults['city'];
        $state     = trim(getParameter("state"));
        if ($state==NULL) $state=$defaults['state'];
        $zip       = trim(getParameter("zip"));
        if ($zip==NULL) $zip=$defaults['zip'];
        $country   = trim(getParameter("country"));
        if ($addr1==NULL) {
           $addr1     = $defaults['addr1'];
           $addr2     = $defaults['addr2'];
        }

         $emailflag = trim(getParameter("emailflag"));
         if ($emailflag==NULL) $emailflag=$defaults['emailflag'];
         
         $field1    = trim(getParameter("field1"));
         if ($field1==NULL) $field1=$defaults['field1'];
         $field2    = trim(getParameter("field2"));
         if ($field2==NULL) $field2=$defaults['field2'];
         $field3    = trim(getParameter("field3"));
         if ($field3==NULL) $field3=$defaults['field3'];
         $field4    = trim(getParameter("field4"));
         if ($field4==NULL) $field4=$defaults['field4'];
         $field5    = trim(getParameter("field5"));
         if ($field5==NULL) $field5=$defaults['field5'];
         $field6    = trim(getParameter("field6"));
         if ($field6==NULL) $field6=$defaults['field6'];

        $age       = trim(getParameter("age"));
         if ($age==NULL) $age=$defaults['age'];
        $gender    = trim(getParameter("gender"));
        if ($gender==NULL) $gender=$defaults['gender'];
        $marital   = trim(getParameter("marital"));
         if ($marital==NULL) $marital=$defaults['marital'];
        $edu       = trim(getParameter("edu"));
         if ($edu==NULL) $edu=$defaults['edu'];
        $nletter   = trim(getParameter("nletter"));
         if ($nletter===NULL) $nletter=$defaults['nletter'];

        $ulevel    = trim(getParameter("ulevel"));
	     if ($ulevel == null) $ulevel="";
        else $ulevel = ", ulevel=".$ulevel;

        if (0!=strcmp($nletter,"YES")) $nletter="NO";

        $user = $this->getUserByEmail($email);
        //if ( !$valid->validname($fname,$lname) || ($email!=NULL && $this->userExists($email) && $user['userid']!=$userid))
        if ($email!=NULL && $this->userExists($email) && $user['userid']!=$userid) {
          //print "\n<!-- user exists, or email null, userid!=userid -->\n";

          return False;
        } else {
            if ($propertiesonly != 1) {
               $this->modifyUserExplicit($userid,$email,$fname,$lname,$age,$gender,$marital,$edu,$nletter,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$usertype,$company,$website,$alive, $country, $title,$parentid,$parentid2, NULL, $ownersite, $siteid, $email2, $username, $field1, $field2, $field3, $field4, $field5, $field6, NULL, FALSE, NULL, $emailflag);
               $notes = trim(getParameter("notes"));
               if($notes!=NULL) $this->addNotes($userid, convertString($notes));
            }
            if ($addToSession) $this->addUserToSession($this->getUser($userid));
            return True;
        }

    }
    
    function addParentID($userid,$parentid){
      $this->updateField($userid, "parentid", $parentid, FALSE, FALSE, TRUE);
      
      $puser = $this->getUser($parentid);
      if($puser['parentid']==NULL || $puser['parentid']==-1) {
         $this->updateField($parentid, "parentid", "-1001", FALSE, FALSE, FALSE);
      }
    }
    
    function addMultipleParentIDs($parentid,$userids) {
       $userid_arr = separateStringBy($userids,",",NULL,TRUE);
       $foundproblem = FALSE;
       for($i=0;$i<count($userid_arr);$i++) {
          if(!is_numeric($userid_arr[$i])) {
             $foundproblem = TRUE;
             break;
          }
       }
       
       if(!$foundproblem) {
          $instmnt = implode(", ",$userid_arr);   	 
          $query = "UPDATE useracct SET dbmode='UPDATED', lastupdated='".getDateForDB()."'";
          
         $lastupdateby = isLoggedOn();
         if($lastupdateby==NULL) $lastupdateby = "0";
         $lastupdateby .= "-".date("Ymd");
         $lastupdateby .= "-p";

         $query .= ", lastupdatedby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdatedby,' ')),1,2048) ";            
          
          $query .= ", parentid='".$parentid."' WHERE userid IN (".$instmnt.");";
          $sql = new MYSQLaccess;
          $sql->update($query);
          
         $wdObj = new WebsiteData();
         $wd = $wdObj->getWebData("org properties");
         if($wd!=NULL && $wd['wd_id']>0) {
            $query1 = "UPDATE wd_".$wd['wd_id']." SET ";
            $query1 .= "lastupdate=NOW(), dbmode='UPDATED', ";
            $query1 .= "lastupdateby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdateby,' ')),1,2048) ";            
            $query1 .= " WHERE userid IN (".$instmnt.");";
            $sql->update($query1);
         }

       }
       
       return !$foundproblem;
    }

    function removeParentID($userid){
      $user = $this->getUser($userid);
      if($user['parentid']>0) {
         $this->updateField($userid, "parentid", "-1", FALSE, FALSE, TRUE);
         
         $puser = $this->getUser($user['parentid']);
         if($puser['parentid']==NULL || $puser['parentid']<1) {
            $query = "SELECT userid FROM useracct where parentid=".$user['parentid']." OR parentid2=".$user['parentid']." LIMIT 0,1";
            $dbLink = new MYSQLaccess;
            $results = $dbLink->queryGetResults($query);
            if(count($results)==0) {
               $this->updateField($user['parentid'], "parentid", "-1", FALSE, FALSE, FALSE);
            } else {
               $this->updateField($user['parentid'], "parentid", "-1001", FALSE, FALSE, FALSE);
            }
         }
      }
    }

   function modifyUserExplicit($userid,$email,$fname,$lname,$age,$gender,$marital,$edu,$nletter,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1=NULL,$addr2=NULL,$city=NULL,$state=NULL,$zip=NULL,$usertype=NULL,$company=NULL,$website=NULL,$alive=NULL, $country="US", $title=NULL,$parentid=NULL,$parentid2=NULL, $password2=NULL, $ownersite=NULL, $siteid=NULL, $email2=NULL, $username=NULL, $field1=NULL, $field2=NULL, $field3=NULL, $field4=NULL, $field5=NULL, $field6=NULL, $token=NULL, $localonly=FALSE, $lastupdateby=NULL, $emailflag=NULL){
      if ($userid==NULL) return NULL;
      $user = $this->getUser($userid);
      if ($user==NULL) return NULL;
      if ($fname!=NULL) $fname = strtoupper(substr($fname,0,1)).substr($fname,1);
      else $fname = $user['fname'];
      if ($lname!=NULL) $lname = strtoupper(substr($lname,0,1)).substr($lname,1);
      else $lname = $user['lname'];

      $dbLink = new MYSQLaccess;
      $setEmailStr = "";
      if ($email!=NULL) $setEmailStr .= " email='".convertString(strtolower(trim($email)))."',";
      
      if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
      if($lastupdateby==NULL) $lastupdateby = "0";
      $lastupdateby .= "-".date("Ymd");
      $lastupdateby .= "-u";
      $setEmailStr .= " lastupdatedby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdatedby,' ')),1,2048),";

      if ($username!=NULL) {
         if(!$this->usernameExists($username,$userid)) $setEmailStr .= " username='".convertString(trim($username))."',";
      }

      if ($email2!=NULL) $setEmailStr .= " email2='".convertString(strtolower(trim($email2)))."',";
      
      if ($usertype==NULL) $usertype = $user['usertype'];
      if ($usertype==NULL) $usertype = "user";
      $setEmailStr .= " usertype='".$usertype."',";
      
      if ($alive!==NULL && 0!=strcmp(trim($alive),"") && ($alive==1 || $alive==0)) {
         $setEmailStr .= " alive=".$alive.",";
         if ($alive==1 && $user['alive']==0) $this->addReferral($_SESSION['ssnrem']['refuserid'],$userid,"Modify user");
      }
      if ($siteid!=NULL) $setEmailStr .= " siteid=".$siteid.",";
      if ($parentid!=NULL) $setEmailStr .= " parentid=".$parentid.",";
      if ($parentid2!=NULL) $setEmailStr .= " parentid2=".$parentid2.",";
      if ($password2!=NULL) $setEmailStr .= " password2='".md5($password2)."',";
      if ($ownersite!=NULL) $setEmailStr .= " ownersite='".$ownersite."',";
      if ($token!=NULL) $setEmailStr .= " token='".convertString($token)."',";
      
      if ($emailflag!=NULL) $setEmailStr .= " emailflag='".convertString(trim($emailflag))."',";
      else $setEmailStr .= " emailflag='0',";
      
      $setEmailStr .= " field1='".convertString($field1)."',";
      $setEmailStr .= " field2='".convertString($field2)."',";
      $setEmailStr .= " field3='".convertString($field3)."',";

      if ($field4!=NULL && is_numeric($field4)) $setEmailStr .= " field4=".$field4.",";
      else $setEmailStr .= " field4=NULL,";

      if ($field5!=NULL && is_numeric($field5)) $setEmailStr .= " field5=".$field5.",";
      else $setEmailStr .= " field5=NULL,";

      if ($field6!=NULL && is_numeric($field6)) $setEmailStr .= " field6=".$field6.",";
      else $setEmailStr .= " field6=NULL,";

      $company = convertString($company);
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', ".$setEmailStr." fname='".convertString(trim($fname))."'";

      //if (0!=strcmp($user['dbmode'],"NEW") && 0!=strcmp($user['dbmode'],"REJECTED") && 0!=strcmp($user['dbmode'],"DELETED")) $query .= ", dbmode='UPDATED'";
      if (0==strcmp($user['dbmode'],"APPROVED")) $query .= ", dbmode='UPDATED'";
      //print "\n\n<!-- query so far: ".$query." -->\n\n";

      $query .= ", lname='".convertString(trim($lname))."', company='".convertString(trim($company))."', title='".convertString(trim($title))."', age='".$age."'";
      $query .= ", gender='".$gender."', marital='".$marital."', edu='".$edu."'";
      if ($phonenum!=NULL) $query .= ", phonenum='".convertString(trim($phonenum))."'";
      if ($phonenum2!=NULL) $query .= " , phonenum2='".convertString(trim($phonenum2))."'";
      if ($phonenum3!=NULL) $query .= " , phonenum3='".convertString(trim($phonenum3))."'";
      if ($phonenum4!=NULL) $query .= " , phonenum4='".convertString(trim($phonenum4))."'";
      if ($website!=NULL) $query .= ", website='".convertString(trim($website))."'";
      if ($addr1!=NULL) $query .= ", addr1='".convertString(trim($addr1))."'";
      $query .= ", addr2='".convertString(trim($addr2))."'";
      if ($city!=NULL) $query .= ", city='".convertString(trim($city))."'";
      if ($state!=NULL) $query .= ", state='".convertString(trim($state))."'";
      
       if(trim($zip)!=NULL) {
          if(strpos(trim($zip),"-")==4) $zip = "0".trim($zip);
          if(strlen(trim($zip))==4) $zip = "0".trim($zip);
          $query .= ", zip='".convertString(trim($zip))."'";
       }
      
      if ($country!=NULL) $query .= ", country='".convertString(trim($country))."'";

      $query .= ", nletter='".$nletter."'".$ulevel." WHERE userid=".$userid.";";
      $dbLink->update($query);

      $jsftrack1 = "Updating user account: ".$userid;
      $this->track("modifyUserExplicit",$jsftrack1,"Update Account",$userid);

      //print "\n<!-- modify user query1: ".$query." -->\n";
      /*
      $query = "SELECT addrid FROM useracct WHERE userid=".$userid.";";
      $results = $dbLink->queryGetResults($query);
      if ($results==NULL || $results[0]['addrid']==NULL) {
         $query = "INSERT INTO addr SET userid=".$userid.", email='".convertString(strtolower(trim($email)))."', phonenum='$phonenum', phonenum2='$phonenum2', phonenum3='$phonenum3', phonenum4='$phonenum4', addr1='".convertString(trim($addr1))."', addr2='".convertString(trim($addr2))."', city='".convertString(trim($city))."', state='$state', zip='$zip', country='$country', website='$website';";
         $addrid = $dbLink->insertGetValue($query);
         $this->setPrimaryAddress($userid,$addrid);
      } else {
         $query = "UPDATE addr SET lat=null, lng=null";
         if ($email!=NULL) $query .= ", email='".convertString(strtolower(trim($email)))."'";
         if ($phonenum!=NULL) $query .= ", phonenum='$phonenum'";
         if ($phonenum2!=NULL) $query .= " , phonenum2='$phonenum2'";
         if ($phonenum3!=NULL) $query .= " , phonenum3='$phonenum3'";
         if ($phonenum4!=NULL) $query .= " , phonenum4='$phonenum4'";
         if ($addr1!=NULL) {
            $query .= ", addr1='".convertString(trim($addr1))."'";
            $query .= ", addr2='".convertString(trim($addr2))."'";
         }
         if ($city!=NULL) $query .= ", city='".convertString(trim($city))."'";
         if ($state!=NULL) $query .= ", state='$state'";
         if ($zip!=NULL) $query .= ", zip='$zip'";
         if ($country!=NULL) $query .= ", country='$country'";
         if ($website!=NULL) $query .= ", website='$website'";
         $query .= " WHERE addrid=".$results[0]['addrid'].";";
         $dbLink->update($query);
      }
      */
      
      
      $wdObj = new WebsiteData();
      $wd = $wdObj->getWebData($usertype." properties");
      //print "<br><b>User type:</b>".$usertype."<br><b>WD: </b>".$wd['wd_id']."<br>";
      if($wd!=NULL && $wd['wd_id']>0) {
         $query1 = "UPDATE wd_".$wd['wd_id']." SET ";
         $query1 .= "lastupdate=NOW(), dbmode='UPDATED', ";
         $query1 .= "lastupdateby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdateby,' ')),1,2048) ";
         $query1 .= "WHERE userid=".$userid.";";
         $dbLink->update($query1);
         //print "\n<!-- ***chj*** query: ".$query1." -->\n";
         //print "\n<br><b> query: ".$query1." </b><br>\n";
      }

      if(!$localonly) $this->remoteAddUpdate($user['email'],$email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $gender, $usertype, $company, $website, $alive);
      //print "<BR> query2: ".$query."<BR>";
   }
    
   function modifyWebSite($addrid,$website){
      if ($addrid==NULL) return NULL;
      $dbLink = new MYSQLaccess;
      $query = "UPDATE addr SET website='".$website."'";
      $query .= " WHERE addrid=".$addrid.";";
      $dbLink->update($query);
   }
    
   function makeAlive($user) {
      if ($user==null || $user['userid']==null) return FALSE;
      if ($user['alive']==0) {
               
         $dbmode = $this->getUserField($userid,"dbmode");
      
         $dbLink = new MYSQLaccess;
         $query = "UPDATE useracct SET lastupdated='".getDateForDB()."'";
         if(0==strcmp($dbmode,"APPROVED")) $query .= ", dbmode='UPDATED'";
         $query .= ", alive=1 WHERE userid=".$user['userid'];
         $dbLink->update($query);
         $this->addReferral($_SESSION['ssnrem']['refuserid'],$user['userid'],"Making user alive");

         $jsftrack1 = "Updating user alive: ".$user['userid'];
         $this->track("makeAlive",$jsftrack1,"Update Account",$userid);
      }
      return TRUE;
   }

   function updateAddress($addrid,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$website=NULL,$country="US",$email=NULL){
      if ($addrid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      $query = "UPDATE addr SET ";
      $query .= "lat=null, lng=null, phonenum='$phonenum', phonenum2='$phonenum2', phonenum3='$phonenum3', phonenum4='$phonenum4', addr1='".convertString(trim($addr1))."', addr2='".convertString(trim($addr2))."', city='".convertString(trim($city))."', state='$state', zip='$zip', country='$country', website='$website'";
      if ($email!=NULL) $query .= ", email='".convertString(strtolower(trim($email)))."'";
      $query .= " WHERE addrid=".$addrid.";";
      $dbLink->update($query);
   }

   function updateUserAddress($userid,$phonenum,$phonenum2,$phonenum3,$phonenum4,$addr1,$addr2,$city,$state,$zip,$website=NULL,$country=NULL,$dbmode=NULL,$lastupdateby=NULL){
      if ($userid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      
      if($dbmode==NULL) $dbmode="UPDATED";
      if($country==NULL) $country="US";
      
      $user = $this->getUser($userid);
      
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', dbmode='".$dbmode."', lat=null, lng=null";
      $query .= ", phonenum='".convertString(trim($phonenum))."', phonenum2='".convertString(trim($phonenum2))."'";
      $query .= ", phonenum3='".convertString(trim($phonenum3))."', phonenum4='".convertString(trim($phonenum4))."'";
      $query .= ", addr1='".convertString(trim($addr1))."', addr2='".convertString(trim($addr2))."'";
      $query .= ", city='".convertString(trim($city))."', state='".convertString(trim($state))."'";
      $query .= ", zip='".convertString(trim($zip))."', country='".convertString(trim($country))."'";
      if($website!=NULL) $query .= ", website='".convertString(trim($website))."'";
      $query .= " WHERE userid=".$userid.";";
      $dbLink->update($query);
      

      
      $wdObj = new WebsiteData();
      $wd = $wdObj->getWebData($user['usertype']." properties");
      if($wd!=NULL && $wd['wd_id']>0) {
         $query1 = "UPDATE wd_".$wd['wd_id']." SET ";
         $query1 .= "lastupdate=NOW(), dbmode='UPDATED', ";
         if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
         if($lastupdateby==NULL) $lastupdateby = "0";
         $lastupdateby .= "-".date("Ymd");
         $query1 .= "lastupdateby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdateby,' ')),1,2048) ";
         $query1 .= "WHERE userid=".$userid.";";
         $dbLink->update($query1);
         //print "\n<!-- ***chj*** query: ".$query1." -->\n";
      }
      

      $jsftrack1 = "Updating user address: ".$userid;
      $this->track("updateUserAddress",$jsftrack1,"Update Account",$userid);
   }

   function setGeoCode($addrid,$lat,$lng) {
      if ($addrid==NULL) return FALSE;
      if ($lat==NULL || $lng==NULL) return FALSE;

      $dbLink = new MYSQLaccess;
      $query = "UPDATE addr SET lat=".$lat.", lng=".$lng." WHERE addrid=".$addrid.";";
      $dbLink->update($query);
      return TRUE;
   }

   function setUserGeoCode($userid,$lat,$lng) {
      if ($userid==NULL) return FALSE;
      if ($lat==NULL || $lng==NULL) return FALSE;
      
      $dbmode = $this->getUserField($userid,"dbmode");
      
      $dbLink = new MYSQLaccess;
      $query = "UPDATE useracct SET lastupdated='".getDateForDB()."'";
      if(0==strcmp($dbmode,"APPROVED")) $query .= ", dbmode='UPDATED'";
      $query .= ", lat=".$lat.", lng=".$lng." WHERE userid=".$userid.";";
      $dbLink->update($query);

      $jsftrack1 = "Updating user geocode: ".$userid;
      $this->track("setUserGeoCode",$jsftrack1,"Update Account",$userid);

      return TRUE;
   }

   function getGeoCodeExplicit($addr,$showInfo=FALSE){
      //$showInfo=TRUE;
      $resp = array();
      if (($addr['city']!=NULL && $addr['state']!=NULL) || $addr['zip']!=NULL) {         
         if($addr['addr1']==NULL && $addr['addr2']==NULL) {
            $dbLink = new MYSQLaccess;
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
               if ($showInfo) print "\n<!-- found table e911zip for geocode -->\n";
               $query = NULL;
               $query = "SELECT * FROM e911zip ";
               $query .= "WHERE ";
               
               if($addr['zip']!=NULL) {
                  $z = str_replace("-","",str_replace(" ","",strtolower($addr['zip'])));
                  $query .= "LOWER(ZipCode) like '".$z."%' ";
                  $resp['accuracy'] = "zip";
               } else if($addr['city']!=NULL && $addr['state']!=NULL) {
                  $query .= "LOWER(City)='".strtolower($addr['city'])."' AND LOWER(State)='".strtolower($addr['state'])."' ";
                  $resp['accuracy'] = "city";
               } else if($addr['state']!=NULL) {
                  $query .= "LOWER(State)='".strtolower($addr['state'])."' ";
                  $resp['accuracy'] = "state";
               }
               $query .= "ORDER BY Population DESC LIMIT 0,1;";                  
               $results = $dbLink->queryGetResults($query);
               if($results!=NULL && count($results)>0) {
                  $resp['query'] = $query;
                  $resp['latitude'] = $results[0]['Latitude'];
                  $resp['longitude'] = $results[0]['Longitude'];
               }
            }
         }
            
         if ($resp['latitude'] == NULL || $resp['longitude']==NULL) {
            $url = "https://maps.googleapis.com/maps/api/geocode/json";
            $address = "";
            if (isset($addr['addr1'])) $address .= $addr['addr1']." ";
            if (isset($addr['addr2'])) $address .= $addr['addr2']." ";
            if (isset($addr['city'])) $address .= $addr['city'].", ";
            if (isset($addr['state'])) $address .= $addr['state']." ";
            if (isset($addr['zip'])) $address .= $addr['zip'];
            
            $gmapsURL = $url."?address=".urlencode(trim($address))."&sensor=false";
            
            $ss = new Version();
            $apikey = $ss->getValue('GoogleMapsKey');
            if($apikey!=NULL) $gmapsURL .= "&key=".$apikey;
            
            if ($showInfo) print "\n<!-- getGeoCodeExplicit gmaps url: ".$gmapsURL." -->\n";
            //print "\n<!-- gmaps url: ".$gmapsURL." -->\n";
            
            //Method 1 to get content:
            $ch2 = curl_init();
            curl_setopt($ch2,CURLOPT_URL,$gmapsURL);
            //curl_setopt($ch2,CURLOPT_POST, 1);
            curl_setopt($ch2,CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch2);
            curl_close($ch2);
            if ($showInfo) {
               print "\n<!-- getGeoCodeExplicit method1 content:\n";
               print $content;
               print "\n-->\n";
            }
            $data = objectToArray(json_decode_jsf($content));      
            $resp['latitude'] = $data['results'][0]['geometry']['location']['lat'];
            $resp['longitude'] = $data['results'][0]['geometry']['location']['lng'];
            $components = $data['results'][0]['address_components'];
            for ($i=0;$i<count($components);$i++){
                $types = $components[$i]['types'];
                for ($j=0;$j<count($types);$j++){
                    if (0==strcmp($types[$j],"locality")){
                        $resp['locality'] = $components[$i]['short_name'];
                        break;
                    } else if (0==strcmp($types[$j],"administrative_area_level_1")){
                        $resp['administrative_area_level_1'] = $components[$i]['short_name'];
                        break;
                    } else if (0==strcmp($types[$j],"country")){
                        $resp['country'] = $components[$i]['short_name'];
                        break;
                    }
                }
            }
   
            if ($showInfo) {
               print "\n<!--\n";
               print "getGeoCodeExplicit method 1 response obj:";
               print "\n";
               print_r($resp);
               print "\n -->\n";
            }
         }

         if ($resp['latitude'] == NULL || $resp['longitude']==NULL) {
            //Method 2 to get content:
            $content=file_get_contents($gmapsURL);
            if ($showInfo) {
               print "\n<!-- getGeoCodeExplicit method2:\n";
               print $content;
               print "\n-->\n";
            }
            $data = objectToArray(json_decode_jsf($content));      
            $resp['latitude'] = $data['results'][0]['geometry']['location']['lat'];
            $resp['longitude'] = $data['results'][0]['geometry']['location']['lng'];

             $components = $data['results'][0]['address_components'];
             for ($i=0;$i<count($components);$i++){
                 $types = $components[$i]['types'];
                 for ($j=0;$j<count($types);$j++){
                     if (0==strcmp($types[$j],"locality")){
                         $resp['locality'] = $components[$i]['short_name'];
                         break;
                     } else if (0==strcmp($types[$j],"administrative_area_level_1")){
                         $resp['administrative_area_level_1'] = $components[$i]['short_name'];
                         break;
                     } else if (0==strcmp($types[$j],"country")){
                         $resp['country'] = $components[$i]['short_name'];
                         break;
                     }
                 }
             }
             
            if ($showInfo) {
               print "\n<!--\n";
               print "getGeoCodeExplicit method 2 response obj:";
               print "\n";
               print_r($resp);
               print "\n -->\n";
            }
         }
         
         if ($resp['latitude'] == NULL || $resp['longitude']==NULL) return NULL;

         return $resp;
      } else {
         return NULL;
      }

   }

   function getGeoCode($addrid,$showInfo=FALSE) {
      $resp = NULL;
      if ($addrid!=NULL && $addrid>0) {
         $dbLink = new MYSQLaccess;
         $query = "SELECT addr1,addr2,city,state,zip,country,lat,lng FROM addr WHERE addrid=".$addrid.";";
         $addrs = $dbLink->queryGetResults($query);
         if ($showInfo) {
            print "\n<!--\n";
            print "query: ".$query;
            print "\n";
            print_r($addrs);
            print "\n -->\n";
         }
         $addr = $addrs[0];
         if ($addr['lat']==NULL || $addr['lng']==NULL) {
            //$resp = $this->getGeoCodeExplicit($addr);
            $resp = $this->getGeoCodeExplicit($addr,$showInfo);
            $this->setGeoCode($addrid,$resp['latitude'],$resp['longitude']);
         } else {
            $resp['latitude'] = $addr['lat'];
            $resp['longitude'] = $addr['lng'];
         }
      }
      return $resp;
   }

   function getUserGeoCode($userid,$showInfo=FALSE,$table=NULL,$force=FALSE) {
      if($table==NULL) $table = "useracct";
      $resp = NULL;
      if ($userid!=NULL && $userid>0) {
         $dbLink = new MYSQLaccess;
         $query = "SELECT addr1,addr2,city,state,zip,country,lat,lng FROM ".$table." WHERE userid=".$userid.";";
         $addrs = $dbLink->queryGetResults($query);
         if ($showInfo) {
            print "\n<!--\n";
            print "query: ".$query;
            print "\n";
            print_r($addrs);
            print "\n -->\n";
         }
         $addr = $addrs[0];
         if ($addr['lat']==NULL || $addr['lng']==NULL || (0==strcmp(substr($addr['lat'],0,4),"0.00") && 0==strcmp(substr($addr['lng'],4),"0.00")) || $force) {
            $resp = $this->getGeoCodeExplicit($addr,$showInfo);
            if ($showInfo) {
               print "\n<!--\n";
               print "response: ";
               print "\n";
               print_r($resp);
               print "\n -->\n";
            }
            $suc = $this->setUserGeoCode($userid,$resp['latitude'],$resp['longitude']);
            if($suc && $showInfo) print "\n<!-- succeeded. -->\n";
            else if(!$suc && $showInfo) print "\n<!-- failed. -->\n";
         } else {
            $resp['latitude'] = $addr['lat'];
            $resp['longitude'] = $addr['lng'];
         }
      }
      return $resp;
   }


    function checkIfModifiable() {
      return TRUE;
    }
    
    function modifyUserPassword() {
        $password  = trim(getParameter("password"));
        $cpassword = trim(getParameter("cpassword"));
        $oldpassword = trim(getParameter("oldpassword"));
        $userid = $_SESSION['s_user']['userid'];
        return $this->modifyPassword($password, $cpassword, $oldpassword, $userid);
    }

   function modifyPassword($password, $cpassword, $oldpassword, $userid, $force=FALSE) {
      //print "\n<!-- modifyPassword(".$password.",".$cpassword.",".$oldpassword.",".$userid.",".$force." -->\n";
      $success = FALSE;
      $dbLink = new MYSQLaccess;
      $user = $this->getUser($userid);
      $authKey['email'] = convertString(strtolower(trim($user['email'])));
      if ($this->remoteModifyPassword($authKey['email'],$password,$cpassword,$oldpassword)){
         //$query = "UPDATE useracct SET lastupdated='".getDateForDB()."', dbmode='UPDATED', password='".md5($password)."', password2='".md5($oldpassword)."' WHERE userid=".$userid.";";
         $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', password='".md5($password)."', password2='".md5($oldpassword)."' WHERE userid=".$userid.";";
         $dbLink->update($query);
         $success = TRUE;
      }
      $valid = new Validator;
      $authKey['password'] = $oldpassword;
      
      if (
       $userid != NULL &&
       $valid->validpassword($password,$cpassword) &&
       ($this->userAuthenticate($authKey) || $force)
       )
      {
         $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', password='".md5($password)."', password2='".md5($oldpassword)."' WHERE userid=".$userid.";";
         //print "\n<!-- query: ".$query." -->\n";
         //print "\n<!-- password: ".$password." -->\n";
         $dbLink->update($query);
         //$this->addUserToSession($this->getUser($userid));
         $success = TRUE;
      }
      
      if ($success) {
         $jsftrack1 = "Updating user password: ".$userid;
         $this->track("modifyPassword",$jsftrack1,"Update Account",$userid);
      }
      
      return $success;
    }

   function remoteModifyPassword($email,$password,$cpassword,$oldpassword) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($email)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=newpassword&email=".$email;
         $url .= "&password=".encrypt($GLOBALS['masterkey'],$password);
         $url .= "&cpassword=".encrypt($GLOBALS['masterkey'],$cpassword);
         $url .= "&oldpassword=".encrypt($GLOBALS['masterkey'],$oldpassword);
         $url = str_replace(" ","%20",$url);
         //print "URL: ".$url."<BR>\n";
         $result = $reader->Parse($url);
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FAIL")) return FALSE;
         else return TRUE;
      } else {
         return FALSE;
      }
   }

    function modifyUserEmail() {
      $valid = new Validator;

      $oldemail  = $_SESSION['s_user']['emailAddress'];
      $email     = convertString(strtolower(trim(getParameter("email"))));
      $password  = trim(getParameter("password"));
      $userid  = trim(getParameter("userid"));
      if ($userid==NULL) return NULL;

      $authKey['email']=$oldemail;
      $authKey['password']=$password;

      if (
           !$valid->validemail($email)   |
           $this->userExists($email) |
           !$this->userAuthenticate($authKey)
         )
      {
        return False;
      } else {
         $dbmode = $this->getUserField($userid,"dbmode");
         
         $dbLink = new MYSQLaccess;
         $query = "UPDATE useracct SET lastupdated='".getDateForDB()."'";
         if(0==strcmp($dbmode,"APPROVED")) $query .= ", dbmode='UPDATED'";
         $query .= ", email='$email' WHERE userid=".$userid.";";
         $dbLink->update($query);
         $this->addUserToSession($this->getUser($userid));

         $jsftrack1 = "Updating user email: ".$userid;
         $this->track("modifyUserEmail",$jsftrack1,"Update Account",$userid);

          return True;
      }
    }


   function usernameExists($username,$userid=NULL) {
      $ss = new Version();
      $dbLink = new MYSQLaccess;

      $queryUsername = convertString(strtolower(trim($username)));
      $query = "SELECT userid FROM useracct WHERE (dbmode is NULL OR (dbmode<>'DELETED')) AND (LOWER(username)='".$queryUsername."' OR LOWER(email)='".$queryUsername."')";
      if ($userid!=NULL) $query .= " AND userid<>".$userid;
      if ($ss->getValue("multisitesuseremails")==1) {
         $ctx = new Context();
         $query .= " AND ".$ctx->getSiteSQL();
      }

      $values = $dbLink->queryGetResults($query);

      if (count($values) < 1) {
         if ($this->remoteUsernameExists($username)) {
            $query = "INSERT INTO useracct (username) VALUES ('".convertString($username)."');";
            $userid = $dbLink->insertGetValue($query);
            //$query = "INSERT INTO addr (userid) VALUES (".$userid.");";
            //$addrid = $dbLink->insertGetValue($query);
            //$query = "UPDATE useracct set addrid=".$addrid." where userid=".$userid.";";
            //$dbLink->update($query);
            return TRUE;
         } else return FALSE;
         
      } else return TRUE;
   }

   function userExists($userEmail=NULL) {
      if($userEmail==NULL) return FALSE;
      
      $ss = new Version();
      $dbLink = new MYSQLaccess;

      $queryEmail = convertString(strtolower(trim($userEmail)));
      $query = "SELECT userid FROM useracct WHERE (dbmode is NULL OR (dbmode<>'DELETED')) AND LOWER(email)='".$queryEmail."'";
      if ($ss->getValue("multisitesuseremails")==1) {
         $ctx = new Context();
         $query .= " AND ".$ctx->getSiteSQL();
      }
      $values = $dbLink->queryGetResults($query);

      if (count($values) < 1) {
         if ($this->remoteUserExists($userEmail)) {
            $query = "INSERT INTO useracct (email) VALUES ('".convertString(strtolower($userEmail))."');";
            $userid = $dbLink->insertGetValue($query);
            //$query = "INSERT INTO addr (email,userid) VALUES ('".convertString(strtolower($userEmail))."',".$userid.");";
            //$addrid = $dbLink->insertGetValue($query);
            //$query = "UPDATE useracct set addrid=".$addrid." where userid=".$userid.";";
            //$dbLink->update($query);
            return TRUE;
         } else return FALSE;
         
      } else return TRUE;
   }

   function userProfileExists($userEmail) {
      $ss = new Version();
      $dbLink = new MYSQLaccess;

      $queryEmail = convertString(strtolower(trim($userEmail)));
      $query = "SELECT userid FROM useracct WHERE (dbmode is NULL OR (dbmode<>'DELETED')) AND alive=1 AND LOWER(email)='".$queryEmail."' AND NOT(password='".md5("")."') AND NOT(password='') AND NOT(password IS NULL)";

      if ($ss->getValue("multisitesuseremails")==1) {
         $ctx = new Context();
         $query .= " AND ".$ctx->getSiteSQL();
      }

      $values = $dbLink->queryGetResults($query);

      if (count($values) < 1) {
         //print "\n<!-- no local profile found.  checking remotely -->\n";
         //return $this->remoteUserProfileExists($userEmail);

         if ($this->remoteUserProfileExists($userEmail)) {
            $query = "INSERT INTO useracct (email,alive,password) VALUES ('".convertString(strtolower($userEmail))."',1,'1234');";
            $userid = $dbLink->insertGetValue($query);
            //$query = "INSERT INTO addr (email,userid) VALUES ('".convertString(strtolower($userEmail))."',".$userid.");";
            //$addrid = $dbLink->insertGetValue($query);
            //$query = "UPDATE useracct set addrid=".$addrid." where userid=".$userid.";";
            //$dbLink->update($query);
            return TRUE;
         } else return FALSE;
         
      } else return TRUE;

   }

   function remoteUsernameExists($username) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $username     = convertString(strtolower(trim($username)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=usernameexists&username=".encrypt($GLOBALS['masterkey'],$username);
         $url = str_replace(" ","%20",$url);
         //print "\n<!-- URL: ".$url." -->\n";
         $result = $reader->Parse($url);
         //print "\n<!-- result: ";
         //print_r($result);
         //print " -->\n";
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FALSE")) return FALSE;
         else return TRUE;
      } else {
         return FALSE;
      }
   }

   function remoteUserExists($email) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($email)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=emailexists&email=".encrypt($GLOBALS['masterkey'],$email);
         $url = str_replace(" ","%20",$url);
         //print "\n<!-- URL: ".$url." -->\n";
         $result = $reader->Parse($url);
         //print "\n<!-- result: ";
         //print_r($result);
         //print " -->\n";
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FALSE")) return FALSE;
         else return TRUE;
      } else {
         return FALSE;
      }
   }

   function remoteUserProfileExists($email) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($email)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=profileexists&email=".encrypt($GLOBALS['masterkey'],$email);
         $url = str_replace(" ","%20",$url);
         //print "\n<!-- URL: ".$url." -->\n";
         $result = $reader->Parse($url);
         //print "\n<!-- result: ";
         //print_r($result);
         //print " -->\n";
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FALSE")) return FALSE;
         else return TRUE;
      } else {
         return FALSE;
      }
   }

    function getAllUsers($orderby="email",$table="useracct") {
      $query = "SELECT * FROM ".$table." ORDER BY ".$orderby.";";
      $dbLink = new MYSQLaccess;
      $values = $dbLink->queryGetResults($query);
      return $values;
    }

   function getUserAndZipCSV($table="useracct"){
      $csvresult = "";
      $query = "SELECT u.email, a.zip FROM ".$table." u, addr a WHERE u.activated=1 AND u.addrid=a.addrid AND a.zip IS NOT NULL AND TRIM(a.zip)!='' ORDER BY u.created;";
      $dbLink = new MYSQLaccess;
      $results = $dbLink->queryGetResults($query);

      for ($i=0; $i<count($results); $i++) {
         $csvresult .= $results[$i]['email'].",".$results[$i]['zip']."\n";
      }

      return $csvresult;
   }

   function getActiveUserCSV($table="useracct"){
      $csvresult = "";
      $query = "SELECT email FROM ".$table." WHERE activated=1 ORDER BY created;";
      $dbLink = new MYSQLaccess;
      $results = $dbLink->queryGetResults($query);

      for ($i=0; $i<count($results); $i++) {
         $csvresult .= $results[$i]['email']."\n";
      }

      return $csvresult;
   }

   function getUserCSV($segmentid=NULL, $includeProps=TRUE, $fieldsArray=NULL){
      $csvresult = "";
      $csvresulthdr = "";
      //$results = $this->searchUsers();
      $results = $this->getUsersForSegment(NULL, $segmentid);
      $simple = TRUE;
      if ($fieldsArray==NULL) $simple = FALSE;
      else {
         for ($i=0; $i<count($fieldsArray); $i++) {
            if (0!=strcmp($fieldsArray[$i],"created") && 0!=strcmp($fieldsArray[$i],"alive") && 0!=strcmp($fieldsArray[$i],"userid") && 0!=strcmp($fieldsArray[$i],"email")) {
               $simple = FALSE;
               break;
            }
         }
      }

      for ($i=0; $i<count($results['users']); $i++) {
         if ($simple) $line = $results['users'][$i];
         else if ($includeProps) $line = $this->getFullUserInfo($results['users'][$i]['userid']);
         else $line = $this->getUser($results['users'][$i]['userid']);
         foreach($line as $key => $value) {
            if (($fieldsArray==NULL && 0!=strcmp("q",substr($key,0,1)) && !is_numeric(substr($key,1,1))) || in_array($key,$fieldsArray)) {
               if ($i==0) $csvresulthdr .= $key.",";
               $csvresult .= "\"".csvEncodeDoubleQuotes(trim(convertBack($value)))."\",";
            }
         }

         $csvresult .= "\n";
      }

      return $csvresulthdr."\n".$csvresult;
   }
   
   //-------------------------------
   // Load new useraccts, update existing accounts, delete accounts, or first try to search for account and update if similar found
   // flags: delete, overrideemail, search, ignore, add, approve, reject
   // search=1 is required if deleting a record without a userid.
   // search=1 without delete flag will try to update a record - if not found, it will insert the record
   function loadContents($contents,$refsrc_param=NULL,$overrideemail=TRUE, $printstuff=FALSE){
      $sql = new MYSQLaccess();
      $existingcount = 0;
      $overridecount = 0;
      $deletecount = 0;
      $newcount = 0;
      $emptycount = 0;
      $results = array();
      $indexTable = array();

      $delimiter=",";
      $newcontents = csvRemoveQuotes($contents);
      //$newcontents = str_replace("\r","\n",$newcontents);
      //$newcontents = str_replace("\n\n","\n",$newcontents);
      $lines = separateStringBy($newcontents,"\n");
      $headerRow = removenonasciichars($lines[0]);
      $headers = separateStringBy($headerRow,$delimiter);
      for ($i=0; $i<count($headers); $i++) $indexTable[strtolower(trim($headers[$i]))] = $i;
      
      if($printstuff) {
         print "<br>\nHeaders for this file:\n<br>";
         print_r($indexTable);
         print "<br>\n<br>\n";
      }
      
      $approveaccounts = array();
      
      for ($i=1; $i<count($lines); $i++) {
         if(strlen($lines[$i])>3) {

            $lines[$i] = removenonasciichars($lines[$i]);            
            if($printstuff) print "Line ".$i.":".$lines[$i]."\n<br>";
            
            $lineinfo = array();
            $lineok = TRUE;
            
            
            $fields = separateStringBy($lines[$i],$delimiter);
            $csvnames = NULL;
            $csvvalues = NULL;
            for ($j=0; $j<count($fields); $j++) {
               $csvnames[$j] = convertString(trim($headers[$j]));
               $csvvalues[$j] = convertString(trim($fields[$j]));
            }
            $ignore        = strtolower(trim( $fields[$indexTable['ignore']]));
            if(0==strcmp($ignore,"yes")) $ignore=1;
            if($ignore==1) $lineok=FALSE;
            $delete        = strtolower(trim( $fields[$indexTable['delete']])); 
            if(0==strcmp($delete,"yes")) $delete=1;
            $add           = strtolower(trim( $fields[$indexTable['add']])); 
            if(0==strcmp($add,"yes")) $add=1;
            $search        = strtolower(trim( $fields[$indexTable['search']]));
            if(0==strcmp($search,"yes")) $search=1;
            $approve       = strtolower(trim( $fields[$indexTable['approve']]));
            if(0==strcmp($approve,"yes")) $approve=1;
            $reject        = strtolower(trim( $fields[$indexTable['reject']]));
            if(0==strcmp($reject,"yes")) $reject=1;
            
            //only for update/delete
            $userid        = convertString(trim( $fields[$indexTable['userid']]));
            if($userid==NULL) $userid = convertString(trim( $fields[$indexTable['id']]));
            if($userid==NULL) $userid = convertString(trim( $fields[$indexTable['company_id']]));
            
            if($printstuff) print $i.". ".$userid." approve: ".$approve." reject: ".$reject." search: ".$search." add: ".$add." delete: ".$delete." ignore: ".$ignore."<br>\n";
   
            $notes         = convertString(trim( $fields[$indexTable['notes']]));
            if($notes==NULL) $notes = $refsrc_param;
            if($notes==NULL) $notes = getDateForDB()." User Load";
   
            $fname         = convertString(trim( $fields[$indexTable['fname']])); 
            $lname         = convertString(trim( $fields[$indexTable['lname']])); 
            $addr1         = convertString(trim( $fields[$indexTable['addr1']]));
            $addr2         = convertString(trim( $fields[$indexTable['addr2']]));
            if($addr1==NULL) {
               $addr1      = convertString(trim( $fields[$indexTable['address1']]));
               $addr2      = convertString(trim( $fields[$indexTable['address2']]));
            }
            
            $city          = convertString(trim( $fields[$indexTable['city']]));
            $state         = convertString(trim( $fields[$indexTable['state']]));
            $country       = convertString(trim( $fields[$indexTable['country']]));
            $zip           = convertString(trim( $fields[$indexTable['zip']]));
            $phonenum      = convertString(trim( $fields[$indexTable['phonenum']]));
            if($phonenum==NULL) $phonenum = convertString(trim( $fields[$indexTable['phone']]));
            $phonenum2     = convertString(trim( $fields[$indexTable['phonenum2']]));
            if($phonenum2==NULL) $phonenum2 = convertString(trim( $fields[$indexTable['fax']]));
            $phonenum3     = convertString(trim( $fields[$indexTable['phonenum3']]));
            if($phonenum3==NULL) $phonenum3 = convertString(trim( $fields[$indexTable['phone2']]));
            $phonenum4     = convertString(trim( $fields[$indexTable['phonenum4']]));
            $username      = convertString(trim( $fields[$indexTable['username']]));
            $email2        = convertString(trim( $fields[$indexTable['email2']]));
            $email         = convertString(trim( $fields[$indexTable['email']]));
            $lt = strpos($email,"<");
            $gt = strpos($email,">");
            if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) {
               $email = substr($email,$lt+1,$gt-$lt-1);
               //try to get fname and lname?
            }
   
            $gender        = convertString(trim( $fields[$indexTable['gender']])); 
            $password      = convertString(trim( $fields[$indexTable['password']])); 
            $password_enc  = convertString(trim( $fields[$indexTable['password_enc']])); 
            $usertype      = convertString(trim( $fields[$indexTable['usertype']]));
            
            
            if($printstuff) print "Header company index: ".$indexTable['company']." field: ".$fields[$indexTable['company']]."<br>\n";
            $company       = convertString(trim( $fields[$indexTable['company']]));
            if($company==NULL) $company = convertString(trim( $fields[$indexTable['name']]));
            
            
            $title         = convertString(trim( $fields[$indexTable['title']]));
            $siteid        = trim( $fields[$indexTable['siteid']]);
            $ownersite     = trim( $fields[$indexTable['ownersite']]);
            $parentid      = trim( $fields[$indexTable['parentid']]);
            $website       = trim( $fields[$indexTable['website']]);
            if($website==NULL) $website = trim( $fields[$indexTable['url']]);
            //if($printstuff) print $userid." website after: ".$website."<br>\n";
            $parentid2     = trim( $fields[$indexTable['parentid2']]);
   
            $created       = trim( $fields[$indexTable['created']]);
            $lastverified  = trim( $fields[$indexTable['lastverified']]);
            $field1        = trim( $fields[$indexTable['field1']]);
            if($field1==NULL) $field1 = trim($fields[$indexTable['srvycontact']]);
            $field2        = trim( $fields[$indexTable['field2']]); 
            $field3        = trim( $fields[$indexTable['field3']]); 
            $field4        = trim( $fields[$indexTable['field4']]); 
            $field5        = trim( $fields[$indexTable['field5']]); 
            $field6        = trim( $fields[$indexTable['field6']]); 
   
            $lat           = trim( $fields[$indexTable['lat']]);
            $lng           = trim( $fields[$indexTable['lng']]);
   
            $refsrc_csv    = trim( $fields[$indexTable['refsrc']]);
            if ($refsrc_csv!=NULL) $refsrc = $refsrc_csv;
            else if ($refsrc_param != NULL) $refsrc=$refsrc_param;
            else $refsrc = getDateForDB()." User Load";
   
            $alive         = trim( $fields[$indexTable['alive']]);
            if ($usertype==NULL) $usertype= "org";
            //if ($usertype==NULL) $usertype= "user";
            if ($alive!=0) $alive= "1";
            
            $lineinfo['search'] = $search;
            $lineinfo['delete'] = $delete;
            $lineinfo['add'] = $add;
            if($email!=NULL) $lineinfo['email'] = $email;
            if($fname!=NULL || $lname!=NULL) $lineinfo['username'] = $fname." ".$lname;
            if($company!=NULL) $lineinfo['company'] = $company;
            $lineinfo['addr'] = $addr1." ".$city.", ".$state;;
            if($phonenum!=NULL) $lineinfo['phonenum'] = $phonenum;
            
            
            $public_email = convertString(trim( $fields[$indexTable['public_email']]));
            $public_userid = convertString(trim( $fields[$indexTable['public_userid']]));
            $public_usertype  = convertString(trim( $fields[$indexTable['public_usertype']]));
            if($public_usertype==NULL) $public_usertype = "user";
            $public_fname     = convertString(trim( $fields[$indexTable['public_fname']])); 
            $public_lname     = convertString(trim( $fields[$indexTable['public_lname']])); 
            $public_title     = convertString(trim( $fields[$indexTable['public_title']])); 
            $public_addr1     = convertString(trim( $fields[$indexTable['public_addr1']]));
            $public_addr2     = convertString(trim( $fields[$indexTable['public_addr2']]));
            $public_city      = convertString(trim( $fields[$indexTable['public_city']]));
            $public_state     = convertString(trim( $fields[$indexTable['public_state']]));
            $public_country   = convertString(trim( $fields[$indexTable['public_country']]));
            $public_zip       = convertString(trim( $fields[$indexTable['public_zip']]));
            $public_phonenum  = convertString(trim( $fields[$indexTable['public_phonenum']]));
            $public_phonenum2 = convertString(trim( $fields[$indexTable['public_phonenum2']]));
            $public_notes     = convertString(trim( $fields[$indexTable['public_notes']]));
            
            $admin_email = convertString(trim( $fields[$indexTable['admin_email']]));
            $admin_userid = convertString(trim( $fields[$indexTable['admin_userid']]));
            $admin_usertype  = convertString(trim( $fields[$indexTable['admin_usertype']]));
            if($admin_usertype==NULL) $admin_usertype = "user";
            $admin_fname     = convertString(trim( $fields[$indexTable['admin_fname']])); 
            $admin_lname     = convertString(trim( $fields[$indexTable['admin_lname']])); 
            $admin_title     = convertString(trim( $fields[$indexTable['admin_title']])); 
            $admin_addr1     = convertString(trim( $fields[$indexTable['admin_addr1']]));
            $admin_addr2     = convertString(trim( $fields[$indexTable['admin_addr2']]));
            $admin_city      = convertString(trim( $fields[$indexTable['admin_city']]));
            $admin_state     = convertString(trim( $fields[$indexTable['admin_state']]));
            $admin_country   = convertString(trim( $fields[$indexTable['admin_country']]));
            $admin_zip       = convertString(trim( $fields[$indexTable['admin_zip']]));
            $admin_phonenum  = convertString(trim( $fields[$indexTable['admin_phonenum']]));
            $admin_phonenum2 = convertString(trim( $fields[$indexTable['admin_phonenum2']]));
            $admin_notes     = convertString(trim( $fields[$indexTable['admin_notes']])); 
            
            
   
            // Only perform this if searching, not ignoring, no userid specified, and not adding
            if($search==1 && $lineok && $userid==NULL && $add!=1){
               if($printstuff) print "Trying a search...<br>\n";
               // see if we can first find if the record already exists
               $temp = $this->quickfind($email,$fname,$lname,$company,$addr1,$city,$state,$zip,$phonenum,$printstuff);            
               $lineok = $temp['lineok'];
               $userid = $temp['userid'];
               $lineinfo['searchstatus'] = $temp['searchstatus'];
               $lineinfo['searchnote'] = $temp['searchnote'];
            } else if($search==1 && $add==1) {
               $lineinfo['searchstatus'] = "SKIPPED";
               $lineinfo['searchnote'] = "add flag was set to force new record";
            }
      
            //Add this record if the right conditions are met
            if ($lineok && $delete!=1 && $userid==NULL && (($overrideemail && $email==NULL) || !$this->userExists($email))) {
               // create a new user account
               if($printstuff) print "Adding ".$usertype.": ".$company." ".$fname." ".$lname." ...<br>\n";
               $encryptedPW = ($password_enc==NULL || $password_enc!=1);
               $userid = $this->addAccount($email, $password, $fname, $lname, $phonenum, $phonenum2, $phonenum3, $phonenum4, $addr1, $addr2, $city, $state, $zip, $age, $gender, $marital, $edu, $nletter, $encryptedPW, TRUE, $notes,$usertype,$company,$parentid,$website,$parentid2,$alive,$refsrc, $country, $title,$overrideemail, NULL, $ownersite, $siteid,$email2,$username, $field1, $field2, $field3, $field4, $field5, $field6, $created);
               //if ($userid>0 && $lat!=NULL && $lng!=NULL) {
               //   $user = $this->getUser($userid);
               //   $this->setGeoCode($user['addrid'],$lat,$lng);
               //   $this->setUserGeoCode($user['userid'],$lat,$lng);
               //}
               $this->updateMultipleUserProperties($userid,$csvnames,$csvvalues,$printstuff);
   
               $lineinfo['status'] = "NEW";
               $lineinfo['note'] = $userid;
               
               if($lastverified!=NULL) $this->updateField($userid,"lastverified",$lastverified,FALSE,FALSE);
               
               //Add 1 or 2 related users via same line
               if($public_userid==NULL && $public_email!=NULL && !$this->userExists($public_email)) {
                  $public_userid = $this->addAccount($public_email, $password, $public_fname, $public_lname, $public_phonenum, $public_phonenum2, NULL, NULL, $public_addr1, $public_addr2, $public_city, $public_state, $public_zip,NULL,NULL,NULL,NULL,NULL, $encryptedPW, TRUE, $public_notes,$public_usertype,$company,$userid,NULL,NULL,$alive,$refsrc, $public_country, $public_title);
               } else if($public_userid!=NULL || ($public_email!=NULL && $this->userExists($public_email))) {
                  if($public_userid==NULL) {
                     $tu = $this->getUserByEmail($public_email);
                     $public_userid = $tu['userid'];
                  }
                  $this->modifyUserExplicit($public_userid,$public_email,$public_fname,$public_lname,NULL,NULL,NULL,NULL,NULL,$public_phonenum,$public_phonenum2,NULL,NULL,$public_addr1, $public_addr2, $public_city, $public_state, $public_zip,$public_usertype,$company,NULL,NULL, $public_country);
               }
               if($public_userid>0) $this->addUserRelationship($userid,$public_userid,"PUBCNTCT");
               
               if(0==strcmp($admin_email,$public_email)) {
                  $admin_userid = $public_userid;
               } else if($admin_userid==NULL && $admin_email!=NULL && !$this->userExists($admin_email)) {
                  $admin_userid = $this->addAccount($admin_email, $password, $admin_fname, $admin_lname, $admin_phonenum, $admin_phonenum2, NULL, NULL, $admin_addr1, $admin_addr2, $admin_city, $admin_state, $admin_zip,NULL,NULL,NULL,NULL,NULL, $encryptedPW, TRUE, $admin_notes,$admin_usertype,$company,$userid,NULL,NULL,$alive,$refsrc, $admin_country, $admin_title);
               } else if($admin_userid!=NULL || ($admin_email!=NULL && $this->userExists($admin_email))) {
                  if($admin_userid==NULL) {
                     $tu = $this->getUserByEmail($admin_email);
                     $admin_userid = $tu['userid'];
                  }
                  $this->modifyUserExplicit($admin_userid,$admin_email,$admin_fname,$admin_lname,NULL,NULL,NULL,NULL,NULL,$admin_phonenum,$admin_phonenum2,NULL,NULL,$admin_addr1, $admin_addr2, $admin_city, $admin_state, $admin_zip,$admin_usertype,$company,NULL,NULL, $admin_country);
               }
               if($admin_userid>0) $this->addUserRelationship($userid,$admin_userid,"PUBCNTCT");
               
            } else if($add==1) {
               $lineinfo['status'] = "NO ACTION";
               $lineinfo['note'] = "Could not force add - email address already exists";            
            } else if($lineok && $delete!=1 && $userid!=NULL) {
               // update an existing record by way of email address or userid, default to original values
               $lineinfo['status'] = "UPDATE";
               $lineinfo['note'] = $userid;
               
               $user = NULL;
               $user = $this->getUser($userid);
   
               if ($usertype!=NULL) $user['usertype']=$usertype;
               if ($fname!=NULL) $user['fname']=$fname;
               if ($lname!=NULL) $user['lname']=$lname;
               if ($company!=NULL) $user['company']=$company;
               if ($siteid!=NULL) $user['siteid']=$siteid;
               if ($ownersite!=NULL) $user['ownersite']=$ownersite;
               if ($age!=NULL) $user['age']=$age;
               if ($gender!=NULL) $user['gender']=$gender;
               if ($marital!=NULL) $user['marital']=$marital;
               if ($edu!=NULL) $user['edu']=$edu;
               if ($nletter!=NULL) $user['nletter']=$nletter;
               if ($phonenum!=NULL) $user['phonenum']=$phonenum;
               if ($phonenum2!=NULL) $user['phonenum2']=$phonenum2;
               if ($phonenum3!=NULL) $user['phonenum3']=$phonenum3;
               if ($phonenum4!=NULL) $user['phonenum4']=$phonenum4;
               if ($addr1!=NULL) $user['addr1']=$addr1;
               if ($addr2!=NULL) $user['addr2']=$addr2;
               if ($city!=NULL) $user['city']=$city;
               if ($state!=NULL) $user['state']=$state;
               if ($zip!=NULL) $user['zip']=$zip;
               if ($country!=NULL) $user['country']=$country;
               if ($alive!=NULL) $user['alive']=$alive;
               if ($parentid!=NULL) $user['parentid']=$parentid;
               if ($parentid2!=NULL) $user['parentid2']=$parentid2;
               if ($email2!=NULL) $user['email2']=$email2;
               if ($username!=NULL) $user['username']=$username;
               if ($field1!=NULL)  $user['field1'] = $field1;
               if ($field2!=NULL)  $user['field2'] = $field2;
               if ($field3!=NULL)  $user['field3'] = $field3;
               if ($field4!=NULL)  $user['field4'] = $field4;
               if ($field5!=NULL)  $user['field5'] = $field5;
               if ($field6!=NULL)  $user['field6'] = $field6;
               if ($website!=NULL)  $user['website'] = $website;
               $this->modifyUserExplicit($user['userid'],$user['email'],$user['fname'],$user['lname'],$user['age'],$user['gender'],$user['marital'],$user['edu'],$user['nletter'],$user['phonenum'],$user['phonenum2'],$user['phonenum3'],$user['phonenum4'],$user['addr1'],$user['addr2'],$user['city'],$user['state'],$user['zip'],$user['usertype'],$user['company'],$user['website'],$user['alive'], $user['country'], $user['title'], $user['parentid'], $user['parentid2'], NULL, $user['ownersite'], $user['siteid'], $user['email2'], $user['username'], $user['field1'], $user['field2'], $user['field3'], $user['field4'], $user['field5'], $user['field6']);
               if($notes!=NULL) $this->addNotes($user['userid'], $user['notes']."; ".date("Y-m-d").": ".$notes);
               $this->updateMultipleUserProperties($user['userid'],$csvnames,$csvvalues,$printstuff);
               if($lastverified!=NULL) $this->updateField($user['userid'],"lastverified",$lastverified,FALSE,FALSE);
               
               //Add 1 or 2 related users via same line
               if($public_userid==NULL && $public_email!=NULL && !$this->userExists($public_email)) {
                  $public_userid = $this->addAccount($public_email, $password, $public_fname, $public_lname, $public_phonenum, $public_phonenum2, NULL, NULL, $public_addr1, $public_addr2, $public_city, $public_state, $public_zip,NULL,NULL,NULL,NULL,NULL, $encryptedPW, TRUE, $public_notes,$public_usertype,$company,$userid,NULL,NULL,$alive,$refsrc, $public_country, $public_title);
               } else if($public_userid!=NULL || ($public_email!=NULL && $this->userExists($public_email))) {
                  if($public_userid==NULL) {
                     $tu = $this->getUserByEmail($public_email);
                     $public_userid = $tu['userid'];
                  }
                  $this->modifyUserExplicit($public_userid,$public_email,$public_fname,$public_lname,NULL,NULL,NULL,NULL,NULL,$public_phonenum,$public_phonenum2,NULL,NULL,$public_addr1, $public_addr2, $public_city, $public_state, $public_zip,$public_usertype,$company,NULL,NULL, $public_country);
               }
               if($public_userid>0) $this->addUserRelationship($userid,$public_userid,"PUBCNTCT");
               
               if(0==strcmp($admin_email,$public_email)) {
                  $admin_userid = $public_userid;
               } else if($admin_userid==NULL && $admin_email!=NULL && !$this->userExists($admin_email)) {
                  $admin_userid = $this->addAccount($admin_email, $password, $admin_fname, $admin_lname, $admin_phonenum, $admin_phonenum2, NULL, NULL, $admin_addr1, $admin_addr2, $admin_city, $admin_state, $admin_zip,NULL,NULL,NULL,NULL,NULL, $encryptedPW, TRUE, $admin_notes,$admin_usertype,$company,$userid,NULL,NULL,$alive,$refsrc, $admin_country, $admin_title);
               } else if($admin_userid!=NULL || ($admin_email!=NULL && $this->userExists($admin_email))) {
                  if($admin_userid==NULL) {
                     $tu = $this->getUserByEmail($admin_email);
                     $admin_userid = $tu['userid'];
                  }
                  $this->modifyUserExplicit($admin_userid,$admin_email,$admin_fname,$admin_lname,NULL,NULL,NULL,NULL,NULL,$admin_phonenum,$admin_phonenum2,NULL,NULL,$admin_addr1, $admin_addr2, $admin_city, $admin_state, $admin_zip,$admin_usertype,$company,NULL,NULL, $admin_country);
               }
               if($admin_userid>0) $this->addUserRelationship($userid,$admin_userid,"PUBCNTCT");
               
               
            } else if($lineok && $delete==1 && $userid!=NULL) {
               $lineinfo['status'] = "DELETE";
               $lineinfo['note'] = $userid;
   
               $user = NULL;
               $user = $this->getUser($userid);
               $this->deleteUserAcct($user['userid']);
            } else if ($lineok && $email==NULL && $userid==NULL) {
               $lineinfo['status'] = "NO ACTION";
               $lineinfo['note'] = "no email or userid";
            } else if ($lineok) {
               $lineinfo['status'] = "NO ACTION";
               $lineinfo['note'] = "email exists but no search enabled";
               // user's email already exists - can't do anything with it because override flag was not set
            } else {
               $lineinfo['status'] = "NO ACTION";
               $lineinfo['note'] = "unknown reason";
            }
            
            $lineinfo['userid'] = $userid;
            
            if($approve==1 && $userid!=NULL) $approveaccounts[] = $userid;
            else if($reject==1 && $userid!=NULL) $this->revertAccount($userid,getParameter("dbmode"),getParameter("reason"));
            
            $results[] = $lineinfo;
         }
      }
      if(count($approveaccounts)>0) $this->promoteManyAccounts($approveaccounts);
      return $results;
   }

   function quickfind($email=NULL,$fname=NULL,$lname=NULL,$company=NULL,$addr1=NULL,$city=NULL,$state=NULL,$zip=NULL,$phonenum=NULL,$debug=FALSE) {
      $lineinfo = array();
      $lineinfo['lineok'] = TRUE;
      $lineinfo['searchstatus'] = "NOT FOUND";
      $lineinfo['searchnote'] = "";
         
      if($email!=NULL && $this->userExists($email)){
         $temp = $this->getUserByEmail($email);
         $lineinfo['userid'] = $temp['userid'];
         $lineinfo['searchstatus'] = "FOUND";
         $lineinfo['searchnote'] = "found by email";
      }
      
      if($lineinfo['userid']==NULL) {
         // try a combination of ways to find a record (first/last name, company, address, etc)
         $trysearch = TRUE;
         $query = "SELECT userid FROM useracct where ";
         
         if($fname!=NULL && $lname!=NULL) {
            $query .= getSQLStatementLike("fname",$fname,5);
            $query .= getSQLStatementLike("lname",$lname,5);
         } else if($company!=NULL) {
            $query .= getSQLStatementLike("company",$company,7);
         } else {
            if($debug) print "Not trying search because fname, lname, or company not specified.<br>\n";
            $trysearch = FALSE;
         }
               
         $addrcount = 0;
         $loccount = 0;
         $addarr = separateStringBy(convertBack($addr1)," ",NULL,TRUE);
         $addr = "";
         $addr2 = "";
         $addrnum = NULL;

         for($j=0;$j<count($addarr);$j++){
            $el = removeAmbiguity($addarr[$j],TRUE);
            if(is_numeric($el) && $addrnum==NULL){
               $addrnum = $el;
            } else {
               if (strlen($el)>strlen($addr)) {
                  $addr2 = $addr;
                  $addr = $el;
               }
            }
         }
         if ($addrnum!=NULL) {
            $tempstr = getSQLStatementLike("addr1",$addrnum,7);
            if($tempstr!=NULL) {
               $query .= " AND ".$tempstr;
               $addrcount++;
            }
         }
         if ($addr!=NULL && strlen($addr)>2) {
            $tempstr = getSQLStatementLike("addr1",$addr,7,TRUE);
            if($tempstr!=NULL) {
               $query .= " AND ".$tempstr;
               $addrcount++;
            }
         }
         if ($addr2!=NULL && strlen($addr2)>2) {
            $tempstr = getSQLStatementLike("addr1",$addr2,7,TRUE);
            if($tempstr!=NULL) {
               $query .= " AND ".$tempstr;
               $addrcount++;
            }
         }
               
         if ($city!=NULL) {
            $query .= " AND ".getSQLStatementLike("city",$city,6);
            $loccount++;
         }
         if ($state!=NULL) {
            $query .= " AND LOWER(state)='".strtolower($state)."'";
            $loccount++;
         }
         if ($zip!=NULL) {
            $query .= " AND ".getSQLStatementLike("zip",$zip,4);
            $loccount = $loccount + 2;
         }
               
         if(($addrcount<2 || $loccount<2) && $trysearch && $phonenum!=NULL) {
            $query .= " AND ".getSQLStatementLike("phonenum",$phonenum,10);
            $addrcount++;
         } else if($addrcount<2 || $loccount<2) {
            $trysearch=FALSE;
            $lineinfo['searchnote'] = "not enough info to search";
         }
               
         if($trysearch) {
            $sql = new MYSQLaccess();
            $query .= " AND (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'REJECTED'))";
            $matches = $sql->queryGetResults($query);
            if($debug) {
               print "User Search Query: ".$query."<br>\nResults<br>\n";
               print_r($matches);
               print "<br><br>\n\n";
            }
            if ($matches!=NULL && count($matches)==1) {
               $lineinfo['userid'] = $matches[0]['userid'];
               $lineinfo['searchstatus'] = "FOUND";
               $lineinfo['searchnote'] = "found by address/phone";
            } else if($matches!=NULL && count($matches)>1) {
               $lineinfo['lineok'] = FALSE;
               $lineinfo['searchstatus'] = "DUPLICATES";
               $lineinfo['searchnote'] = "";
               for($j=0;$j<count($matches);$j++) {
                  $lineinfo['searchnote'] .= $matches[$j]['userid'].";";
               }
            } else {
               $lineinfo['searchnote'] = "search came up empty";
            }
         }
      }
      return $lineinfo;
   }
   
   function mergeaccounts($userid1,$userid2){
      $rnotes = "Merging accounts: ".$userid1." and ".$userid2;
      $this->track("mergeaccounts",$rnotes,"mergeaccounts");
      
      $sql = new MYSQLaccess();
      
      $user1 = $this->getUser($userid1);
      $user2 = $this->getUser($userid2);
      //$user1 = $this->getFullUserInfo($userid1);
      //$user2 = $this->getFullUserInfo($userid2);
      if($user1['lastupdated']==NULL) $user1['lastupdated'] = $user1['created'];
      if($user2['lastupdated']==NULL) $user2['lastupdated'] = $user2['created'];
      
      $time1 = strtotime($user1['lastupdated']);
      $time2 = strtotime($user2['lastupdated']);
      if($time1>$time2) {
         $temp = $user1;
         $user1 = $user2;
         $user2 = $temp;
      }
      
      $notes = $user2['notes'].";duplicate merged: ".$user1['userid'];
      if($user1['notes']!=NULL) $notes.="   ;;;  from duplicate: ".$user1['notes'];
      $user2['notes'] = NULL;
      $user1['notes'] = $notes;
      
      $query = "";
      foreach($user2 as $key=>$val){
         $tempval = trim(convertBack($user1[$key]));
         if(trim(convertBack($val))==NULL && $tempval!=NULL) {
            $query .= ", ".$key."='".convertString($tempval)."'";
         }
      }
      
      $query = "UPDATE useracct SET dbmode='UPDATED'".$query;
      $query .= " WHERE userid=".$user2['userid'];
      $sql->update($query);
      $query = "UPDATE useracct SET dbmode='DUP' ";
      $query .= " WHERE userid=".$user1['userid'];
      $sql->update($query);
      
      if(0==strcmp($user1['usertype'],$user2['usertype'])){      
         $wdObj = new WebsiteData();
         
         $webdata = $wdObj->getWebDataByName($user2['usertype']." Properties");
         if ($webdata != NULL) {
            $fields = $wdObj->getAllFieldsSystem($webdata['wd_id']);
            //print "<br>\nuseracct:getfulluserinfo wd: ".$webdata['wd_id']."<br>\n";
            $results = $wdObj->getDataByUserid($webdata['wd_id'], $user1['userid']);
            $user1props = $results[0];
            $results = $wdObj->getDataByUserid($webdata['wd_id'], $user2['userid']);
            $user2props = $results[0];
            //print "<br>\nuseracct:getfulluserinfo q26: ".$sci['q26']."<br>\n";
            
            $query = "";
            for($i=0;$i<count($fields);$i++){
               if($user2props[$fields[$i]['field_id']]==NULL){
                  $query .= ", ".$fields[$i]['field_id']."='".$user1props[$fields[$i]['field_id']]."'";
               }
            }
            
            if($query!=NULL && strlen($query)>0) {
               $query = "UPDATE wd_".$webdata['wd_id']." SET dbmode='UPDATED'".$query;
               $query .= " WHERE wd_row_id=".$user2props['wd_row_id'];
               $sql->update($query);
            }
         }
         
         $foundtables = array();
         $results = $wdObj->getWebData("#associatedtodb",FALSE,FALSE,TRUE);
         for($i=0;$i<count($results);$i++) {
            $rows = $wdObj->getRows($results[$i]['wd_id'],NULL,NULL,NULL,FALSE,$user1['userid']);
         }
         $results = $wdObj->getWebData($user2['usertype']." Objects%",TRUE,FALSE,FALSE);
      }
      
      if (class_exists("CustomUserPromote")) {
         $customObj = new CustomUserPromote();
         $customObj->promoteAccount($user1['userid']);
      }

   }
   
   function insertContents($contents,$refsrc_param=NULL){
      $sql = new MYSQLaccess();
      $newcount = 0;
      $totalcount = 0;

      $startquery = "INSERT INTO useracct (";
      $startquery .= "dbmode, ";
      $startquery .= "email, ";
      $startquery .= "fname, ";
      $startquery .= "lname, ";
      $startquery .= "company, ";
      $startquery .= "addr1, ";
      $startquery .= "addr2, ";
      $startquery .= "city, ";
      $startquery .= "state, ";
      $startquery .= "zip, ";
      $startquery .= "phonenum, ";
      $startquery .= "phonenum2, ";
      $startquery .= "phonenum3, ";
      $startquery .= "phonenum4, ";
      $startquery .= "username, ";
      $startquery .= "email2, ";
      $startquery .= "password, ";
      $startquery .= "ownersite, ";
      $startquery .= "gender, ";
      $startquery .= "created, ";
      $startquery .= "usertype, ";
      $startquery .= "title, ";
      $startquery .= "parentid, ";
      $startquery .= "parentid2, ";
      $startquery .= "alive, ";
      $startquery .= "other, ";
      $startquery .= "website, ";
      $startquery .= "field1, ";
      $startquery .= "field2, ";
      $startquery .= "field3, ";
      $startquery .= "field4, ";
      $startquery .= "field5, ";
      $startquery .= "field6, ";
      $startquery .= "refsrc";
      $startquery .= ") VALUES ";
      $query = $startquery;

      $delimiter=",";
      $newcontents = csvRemoveQuotes($contents);
      $newcontents = str_replace(","," , ",$newcontents);
      $lines = separateStringBy($newcontents,"\n");
      $headerRow = $lines[0];
      $headers = separateStringBy(" ".$headerRow." ",$delimiter);
      for ($i=0; $i<count($headers); $i++) $indexTable[strtolower(trim($headers[$i]))] = $i;
      
      for ($i=1; $i<count($lines); $i++) {
         $fields = separateStringBy(" ".$lines[$i]." ",$delimiter);
         $csvnames = NULL;
         $csvvalues = NULL;
         for ($j=0; $j<count($fields); $j++) {
            $csvnames[$j] = convertString(trim($headers[$j]));
            $csvvalues[$j] = convertString(trim($fields[$j]));
         }

         $notes         = convertString(trim( $fields[$indexTable['notes']])); 
         $fname         = convertString(trim( $fields[$indexTable['fname']])); 
         $lname         = convertString(trim( $fields[$indexTable['lname']])); 
         $addr1         = convertString(trim( $fields[$indexTable['addr1']]));
         $addr2         = convertString(trim( $fields[$indexTable['addr2']]));
         $city          = convertString(trim( $fields[$indexTable['city']]));
         $state         = convertString(trim( $fields[$indexTable['state']]));
         $country       = convertString(trim( $fields[$indexTable['country']]));
         $zip           = convertString(trim( $fields[$indexTable['zip']]));
         $phonenum      = convertString(trim( $fields[$indexTable['phonenum']]));
         $phonenum2     = convertString(trim( $fields[$indexTable['phonenum2']]));
         $phonenum3     = convertString(trim( $fields[$indexTable['phonenum3']]));
         $phonenum4     = convertString(trim( $fields[$indexTable['phonenum4']]));
         $username      = convertString(trim( $fields[$indexTable['username']]));
         $email2        = convertString(trim( $fields[$indexTable['email2']]));
         $email         = convertString(trim( $fields[$indexTable['email']]));
         $lt = strpos($email,"<");
         $gt = strpos($email,">");
         if ($lt!==FALSE && $gt!==FALSE && ($gt>($lt+3))) $email = substr($email,$lt+1,$gt-$lt-1);

         $other         = convertString(trim( $fields[$indexTable['other']])); 
         $gender        = convertString(trim( $fields[$indexTable['gender']])); 
         $password      = convertString(trim( $fields[$indexTable['password']])); 
         $password_enc  = convertString(trim( $fields[$indexTable['password_enc']])); 
         $usertype      = convertString(trim( $fields[$indexTable['usertype']])); 
         $company       = convertString(trim( $fields[$indexTable['company']]));
         $title         = convertString(trim( $fields[$indexTable['title']]));
         $ownersite     = trim( $fields[$indexTable['ownersite']]);
         $parentid      = trim( $fields[$indexTable['parentid']]);
         $website       = trim( $fields[$indexTable['website']]);
         $parentid2     = trim( $fields[$indexTable['parentid2']]);

         $created       = trim( $fields[$indexTable['created']]); 
         if ($created!=NULL) {
            $datearraytemp = separateStringBy($created," ");
            $datearray = separateStringBy($datearraytemp[0],"/");
            if ($datearray!=NULL && count($datearray)>2) {
               $m = (int) $datearray[0];
               $d = (int) $datearray[1];
               $y = (int) $datearray[2];
               if ($m<10) $m = "0".$m;
               if ($d<10) $d = "0".$d;
               if ($y<80) $y = "20".$y;
               else if ($y<100) $y = "19".$y;
               $created = $y."-".$m."-".$d;
            }
         } else {
            $created = getDateForDB();
         }

         $field1        = trim( $fields[$indexTable['field1']]); 
         $field2        = trim( $fields[$indexTable['field2']]); 
         $field3        = trim( $fields[$indexTable['field3']]); 
         $field4        = trim( $fields[$indexTable['field4']]); 
         $field5        = trim( $fields[$indexTable['field5']]); 
         $field6        = trim( $fields[$indexTable['field6']]);
         $lat           = trim( $fields[$indexTable['lat']]);
         $lng           = trim( $fields[$indexTable['lng']]);
         if ($field4==NULL) $field4="0";
         if ($field5==NULL) $field5="0";
         if ($field6==NULL) $field6="0";
         if ($lat==NULL) $lat="0.0";
         if ($lng==NULL) $lng="0.0";

         $overrideemail = trim( $fields[$indexTable['overrideemail']]);
         if ($overrideemail==1) $overrideemail = TRUE;
         else if (0==strcmp(strtoupper($overrideemail),"TRUE")) $overrideemail = TRUE;
         else $overrideemail = FALSE;

         $refsrc_csv    = trim( $fields[$indexTable['refsrc']]);
         if ($refsrc_csv!=NULL) $refsrc = $refsrc_csv;
         else if ($refsrc_param != NULL) $refsrc=$refsrc_param;
         else $refsrc = getDateForDB()." User Load";

         $alive         = trim( $fields[$indexTable['alive']]);
         if ($usertype==NULL) $usertype= "user";
         if ($alive!=0) $alive= "1";

         if ($email==NULL) $email = "jsfdummy".getRandomNum."@dummy.com";
         $query .= "('NEW', ";
         $query .= "'".convertString($email)."', ";
         $query .= "'".convertString($fname)."', ";
         $query .= "'".convertString($lname)."', ";
         $query .= "'".convertString($company)."', ";
         $query .= "'".convertString($addr1)."', ";
         $query .= "'".convertString($addr2)."', ";
         $query .= "'".convertString($city)."', ";
         $query .= "'".convertString($state)."', ";
         $query .= "'".convertString($zip)."', ";
         $query .= "'".convertString($phonenum)."', ";
         $query .= "'".convertString($phonenum2)."', ";
         $query .= "'".convertString($phonenum3)."', ";
         $query .= "'".convertString($phonenum4)."', ";
         $query .= "'".convertString($username)."', ";
         $query .= "'".convertString($email2)."', ";
         $query .= "'".convertString($password)."', ";
         $query .= "'".convertString($ownersite)."', ";
         $query .= "'".convertString($gender)."', ";
         $query .= "'".convertString($created)."', ";
         $query .= "'".convertString($usertype)."', ";
         $query .= "'".convertString($title)."', ";
         $query .= "'".convertString($parentid)."', ";
         $query .= "'".convertString($parentid2)."', ";
         $query .= "'".convertString($alive)."', ";
         $query .= "'".convertString($other)."', ";
         $query .= "'".convertString($website)."', ";
         $query .= "'".convertString($field1)."', ";
         $query .= "'".convertString($field2)."', ";
         $query .= "'".convertString($field3)."', ";
         $query .= "'".convertString($field4)."', ";
         $query .= "'".convertString($field5)."', ";
         $query .= "'".convertString($field6)."', ";
         $query .= "'".convertString($refsrc)."')";

         $newcount++;
         $totalcount++;
         if ($newcount==1000 || $i==(count($lines)-1)) {
            $query .= ";";
            $sql->insert($query);
            $query = $startquery;
            $newcount = 0;
         } else {
            $query .= ", ";
         }
      }

      $jsftrack1 = "Loading user accounts: ".$totalcount;
      $this->track("insertContents",$jsftrack1,"Add Account");
   }

   function getUsersForSegment($segment=NULL, $segmentid=NULL, $orderby=NULL, $page=NULL, $limit=NULL, $justCount=FALSE, $table="useracct"){
      //if ($segment==NULL && $segmentid==NULL) return FALSE;
      if ($segment!=NULL) $segmentid=$this->getSegmentIdByName($segment);

      $responses = $this->searchUsersSQLBySegment($segmentid);
      $dbLink = new MYSQLaccess;

      $orderby = "u.userid";
      if ($orderby==NULL || 0==strcmp($orderby,"")) {
         //$orderby = "u.lname";
         $orderby = "u.userid";
      }

      $fromTables = $responses['fromTables'];
      $baseWhere = $responses['baseWhere'];
      $whereClause = $responses['whereClause'];
      $getParams = $responses['getParams'];
      $hiddenFields = $responses['hiddenFields'];

      if ($justCount) {
         $sql = "SELECT COUNT(DISTINCT u.userid) AS totalnumber FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         if ($whereClause!=NULL) $sql .= " AND ".$whereClause;
         //print "\n\n<!--  ".$sql."  -->\n\n";
         $values = $dbLink->queryGetResults($sql);
         return $values[0]['totalnumber'];
      } else {
         $sql = "SELECT DISTINCT u.userid, u.email, u.alive, u.created, u.dbmode FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         //$sql = "SELECT DISTINCT u.userid, u.email, u.fname, u.lname, u.company, u.alive, u.created, u.dbmode FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         if ($whereClause!=NULL) $sql .= " AND ".$whereClause;
         $sql .= " ORDER BY ".$orderby;
         if ($limit!=NULL) {
            if ($page == NULL || $page<1) $page=1;
            $sql .= " LIMIT ".(($page-1)*$limit).", ".$limit;
         }
   
         //print "\n\n<!--  getusersforsegment sql: ".$sql."  -->\n\n";
         $values = $dbLink->queryGetResults($sql);
         $returnObj['users'] = $values;
         if ($this->isUserAdmin(isLoggedOn())) $returnObj['sql'] = $sql;
         $returnObj['hiddenFields'] = $hiddenFields;
         $returnObj['getParams'] = $getParams;
         $returnObj['parentsegment']=$segmentid;
         
         return $returnObj;
      }
   }

   function searchUsersSQLBySegment($segmentid=NULL,$fromTables=NULL,$baseWhere=NULL) {
      $segmentCount = 0;
      $segments = array();
      $segmentCondition = "AND";
      $sql = "";
      $hiddenFields = "";
      //unset($_SESSION['params']);
      if ($segmentid==NULL) $segmentid = getParameter("segmentid");
      $uSeg = $this->getUserSegment($segmentid);
      if ($segmentid != NULL && $uSeg != NULL) {
         for ($i=0; $i<count($uSeg['getParams']); $i++) {
            $name = $uSeg['getParams'][$i]['name'];
            $value = $uSeg['getParams'][$i]['value'];
            if (0==strcmp($name,"SEGMENTID")) {
               $segments[$segmentCount] = $value;
               $segmentCount++;
            } else if (0==strcmp($name,"SEGMENTCONDITION")) {
               $segmentCondition = $value;
            } else {
               $_SESSION['params'][$name] = $value;
            }
         }
      }
      $returnObj = $this->searchUsersSQL($fromTables,$baseWhere);
      $hiddenFields = $returnObj['hiddenFields'];
      $getParams = $returnObj['getParams'];
      
      unset($_SESSION['params']);
      if ($returnObj['whereClause']!=NULL) {
         $sql = "( ".$returnObj['whereClause']." )";
         $fromTables = $returnObj['fromTables'];
         $baseWhere = $returnObj['baseWhere'];
      }
      if (count($segments)>0) {
         $seg_sql= "";
         for ($i=0; $i<count($segments); $i++) {
            $tempObj = $this->searchUsersSQLBySegment($segments[$i],$fromTables,$baseWhere);
            if ($tempObj['whereClause']!=NULL) {
               if (0==strcmp($segmentCondition,"OR") && strlen($seg_sql)>2) $seg_sql .= " OR ";
               else if (strlen($seg_sql)>2) $seg_sql .= " AND ";
  
               if (0==strcmp($segmentCondition,"NOT")) $seg_sql .= " NOT";

               $seg_sql .= "( ".$tempObj['whereClause']." )";
               $fromTables = $tempObj['fromTables'];
               $baseWhere = $tempObj['baseWhere'];
            }
         }
         $sql .= " AND (".$seg_sql.")";
      }
      
      $responses['whereClause']=$sql;
      $responses['fromTables']=$fromTables;
      $responses['baseWhere']=$baseWhere;
      $responses['hiddenFields']=$hiddenFields;
      $responses['getParams']=$getParams;
      $responses['parentsegment']=$segmentid;

      return $responses;
   }

   //-------------------------------------------------------------------   
   // Build user SQL where clause and from statement from paramters
   //-------------------------------------------------------------------   
   function getLimitSearchStr($param,$english){
      $returnobj = NULL;
      $getParamsCount = count($getParams);
      $value = strtolower(trim(getParameter("s_".$param)));
      if ($value != NULL) {
         $wcounter = 0;
         if (is_array($value)) $value = implode(";",$value);

         $valarr = separateStringBy($value,";");
         $returnobj['whereClause'] = " AND ( ";
         for ($i=0;$i<count($valarr);$i++) {
            $curval = convertString(strtolower(trim($valarr[$i])));
            if ($curval!=NULL) {
               if ($wcounter>0) $returnobj['whereClause'] .= "OR ";
               $returnobj['whereClause'] .= "LOWER(u.".$param.") LIKE '%".$curval."%' ";
               $wcounter++;
            }
         }
         $returnobj['whereClause'] .= ") ";

         $returnobj['hiddenFields'] = "<input type=\"hidden\" name=\"s_".$param."\" value=\"".$value."\">\n";
         $returnobj['name'] = "s_".$param;
         $returnobj['value'] = $value;
         $returnobj['display'] = $english.": ".$value;
         if ($wcounter<1) $returnobj = NULL;
      }
      return $returnobj;
   }

   function searchUsersSQL($fromTables=NULL,$baseWhere=NULL) {
      $hiddenFields = "";
      $getParamsCount = 0;
      $getParams = NULL;

      $whereClause = "1=1 ";

      $alive = strtolower(trim(getParameter("s_alive")));
      if ($alive != NULL && 0==strcmp($alive,"no")) {
         $whereClause .= " AND u.alive=0";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_alive\" value=\"no\">\n";
         $getParams[$getParamsCount]['name'] = "s_alive";
         $getParams[$getParamsCount]['value'] = "no";
         $getParams[$getParamsCount]['display'] = "Alive: no";
         $getParamsCount += 1;
      } else if ($alive != NULL && 0==strcmp($alive,"both")) {
         $hiddenFields .= "<input type=\"hidden\" name=\"s_alive\" value=\"both\">\n";
         $getParams[$getParamsCount]['name'] = "s_alive";
         $getParams[$getParamsCount]['value'] = "both";
         $getParams[$getParamsCount]['display'] = "Alive: both";
         $getParamsCount += 1;
      } else {
         $whereClause .= " AND u.alive=1";
      }

      $createdbefore = strtolower(trim(getParameter("s_createdbefore")));
      if ($createdbefore != NULL) {
         $whereClause .= " AND u.created<='".$createdbefore."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_createdbefore\" value=\"".$createdbefore."\">\n";
         $getParams[$getParamsCount]['name'] = "s_createdbefore";
         $getParams[$getParamsCount]['value'] = $createdbefore;
         $getParams[$getParamsCount]['display'] = "Created before: ".$createdbefore;
         $getParamsCount += 1;
      }
      $createdafter = strtolower(trim(getParameter("s_createdafter")));
      if ($createdafter != NULL) {
         $whereClause .= " AND u.created>='".$createdafter."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_createdafter\" value=\"".$createdafter."\">\n";
         $getParams[$getParamsCount]['name'] = "s_createdafter";
         $getParams[$getParamsCount]['value'] = $createdafter;
         $getParams[$getParamsCount]['display'] = "Created after: ".$createdafter;
         $getParamsCount += 1;
      }
      $lastupdatedbefore = strtolower(trim(getParameter("s_lastupdatedbefore")));
      if ($lastupdatedbefore != NULL) {
         $whereClause .= " AND u.lastupdated<='".$lastupdatedbefore."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_lastupdatedbefore\" value=\"".$lastupdatedbefore."\">\n";
         $getParams[$getParamsCount]['name'] = "s_lastupdatedbefore";
         $getParams[$getParamsCount]['value'] = $lastupdatedbefore;
         $getParams[$getParamsCount]['display'] = "Updated before: ".$lastupdatedbefore;
         $getParamsCount += 1;
      }
      $lastupdatedafter = strtolower(trim(getParameter("s_lastupdatedafter")));
      if ($lastupdatedafter != NULL) {
         $whereClause .= " AND u.lastupdated>='".$lastupdatedafter."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_lastupdatedafter\" value=\"".$lastupdatedafter."\">\n";
         $getParams[$getParamsCount]['name'] = "s_lastupdatedafter";
         $getParams[$getParamsCount]['value'] = $lastupdatedafter;
         $getParams[$getParamsCount]['display'] = "Updated after: ".$lastupdatedafter;
         $getParamsCount += 1;
      }
      $usertype = strtolower(trim(getParameter("s_usertype")));
      if ($usertype != NULL) {
         $tempWhere = NULL;
         $utArr = separateStringBy($usertype,",");
         for ($i=0; $i<count($utArr); $i++) {
            if ($tempWhere==NULL) $tempWhere=" AND (LOWER(u.usertype)='".trim($utArr[$i])."'";
            else $tempWhere .= " OR LOWER(u.usertype)='".trim($utArr[$i])."'";
         }
         $whereClause .= $tempWhere.")";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_usertype\" value=\"".$usertype."\">\n";
         $getParams[$getParamsCount]['name'] = "s_usertype";
         $getParams[$getParamsCount]['value'] = $usertype;
         $getParams[$getParamsCount]['display'] = "Account: ".$usertype;
         $getParamsCount += 1;
      }
      
      $parentid = strtolower(trim(getParameter("s_parentid")));
      if ($parentid == NULL || 0==strcmp($parentid,"exclude")) {
         $whereClause .= " AND (u.parentid is NULL OR u.parentid > -100)";
      } else if (0==strcmp($parentid,"ignore")) {
         $hiddenFields .= "<input type=\"hidden\" name=\"s_parentid\" value=\"ignore\">\n";
         $getParams[$getParamsCount]['name'] = "s_parentid";
         $getParams[$getParamsCount]['value'] = "ignore";
         $getParams[$getParamsCount]['display'] = "Accounts and Parents";
         $getParamsCount += 1;
      } else {
         $whereClause .= " AND (u.parentid=".$parentid." OR u.parentid2=".$parentid.")";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_parentid\" value=\"".$parentid."\">\n";
         $getParams[$getParamsCount]['name'] = "s_parentid";
         $getParams[$getParamsCount]['value'] = $parentid;
         if($parentid == -1001) $getParams[$getParamsCount]['display'] = "Parents Only";
         else $getParams[$getParamsCount]['display'] = "Parent ID: ".$parentid;
         $getParamsCount += 1;
      }

      $dbmode = strtolower(trim(getParameter("s_dbmode")));
      if ($dbmode != NULL) {
         $dbmodeArr = separateStringBy($dbmode,",");
         $whereClause .= " AND (";
         for ($i=0;$i<count($dbmodeArr);$i++) {
            if ($i>0) $whereClause .= " OR ";
            $whereClause .= "u.dbmode='".strtoupper($dbmodeArr[$i])."'";
         }
         $whereClause .= ")";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_dbmode\" value=\"".$dbmode."\">\n";
         $getParams[$getParamsCount]['name'] = "s_dbmode";
         $getParams[$getParamsCount]['value'] = $dbmode;
         $getParams[$getParamsCount]['display'] = "Status: ".$dbmode;
         $getParamsCount += 1;
      } else {
         $whereClause .= " AND (u.dbmode is NULL OR (u.dbmode<>'DELETED'))";
      }

      $filter = trim(strtolower(trim(getParameter("s_filter"))));
      if ($filter != NULL) {
         $whereClause .= " AND ( 1=0";
         
         $filArr = separateStringBy(trim(strtolower($filter)),",",NULL,TRUE);
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),";",NULL,TRUE);
            for ($m=0; $m<count($filArr1);$m++) {
               // commas or semi-colors represent an OR relationship
               $whereClause .= " OR ( 1=1";
               
               $filArr2 = separateStringBy(trim($filArr1[$m])," ",NULL,TRUE);
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"*",NULL,TRUE);
                  
                  // anything else represents individual OR sequence
                  for ($k=0; $k<count($filArr3);$k++) {
                     $whereClause .= " AND ( 1=0";
                     $filArr3[$k] = trim(convertString($filArr3[$k]));
                     if (is_numeric($filArr3[$k])) $whereClause .= " OR u.userid=".$filArr3[$k];
                     else {
                        $whereClause .= " OR LOWER(u.fname) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.lname) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.email) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.company) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.title) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.username) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.phonenum) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.addr1) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.city) LIKE '%".$filArr3[$k]."%'";
                     }
                     $whereClause .= ")";
                  }
               }
               $whereClause .= ")";
               
            }
         }
         $whereClause .= ")";
         
         $hiddenFields .= "<input type=\"hidden\" name=\"s_filter\" value=\"".$filter."\">\n";
         $getParams[$getParamsCount]['name'] = "s_filter";
         $getParams[$getParamsCount]['value'] = $filter;
         $getParams[$getParamsCount]['display'] = "Search: \"".$filter."\"";
         $getParamsCount += 1;
      }

      $searchtxt = trim(strtolower(trim(getParameter("s_searchtxt"))));
      if ($searchtxt != NULL) {
         $whereClause .= " AND ( 1=0";
         
         $filArr = separateStringBy(trim(strtolower($searchtxt)),",",NULL,TRUE);
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),";",NULL,TRUE);
            for ($m=0; $m<count($filArr1);$m++) {
               // commas or semi-colors represent an OR relationship
               $whereClause .= " OR ( 1=1";
               
               $filArr2 = separateStringBy(trim($filArr1[$m])," ",NULL,TRUE);
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"*",NULL,TRUE);
                  
                  // anything else represents individual OR sequence
                  for ($k=0; $k<count($filArr3);$k++) {
                     $whereClause .= " AND ( 1=0";
                     $filArr3[$k] = trim(convertString($filArr3[$k]));
                     if (is_numeric($filArr3[$k])) $whereClause .= " OR u.userid=".$filArr3[$k];
                     else {
                        $whereClause .= " OR LOWER(u.fname) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.lname) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.email) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.company) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.title) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.username) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.phonenum) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.addr1) LIKE '%".$filArr3[$k]."%'";
                        $whereClause .= " OR LOWER(u.city) LIKE '%".$filArr3[$k]."%'";
                     }
                     $whereClause .= ")";
                  }
               }
               $whereClause .= ")";
               
            }
         }
         $whereClause .= ")";
         
         $hiddenFields .= "<input type=\"hidden\" name=\"s_filter\" value=\"".$searchtxt."\">\n";
         $getParams[$getParamsCount]['name'] = "s_searchtxt";
         $getParams[$getParamsCount]['value'] = $searchtxt;
         $getParams[$getParamsCount]['display'] = "Search: \"".$searchtxt."\"";
         $getParamsCount += 1;
      }

      $ctx = new Context();
      $sitearr = $ctx->getSiteContext();
      $siteid = strtolower(trim(getParameter("search_siteid")));
      if ($ctx->isSiteLeaf($sitearr[0]['siteid'])) {
         $whereClause .= " AND u.siteid=".$sitearr[0]['siteid'];
         $hiddenFields .= "<input type=\"hidden\" name=\"search_siteid\" value=\"".$sitearr[0]['siteid']."\">\n";
         $getParams[$getParamsCount]['name'] = "search_siteid";
         $getParams[$getParamsCount]['value'] = $sitearr[0]['siteid'];
         $getParams[$getParamsCount]['display'] = "Site: ".$sitearr[0]['siteid'];
         $getParamsCount += 1;
      } else if ($siteid != NULL) {
         $whereClause .= " AND u.siteid=".$siteid;
         $hiddenFields .= "<input type=\"hidden\" name=\"search_siteid\" value=\"".$siteid."\">\n";
         $getParams[$getParamsCount]['name'] = "search_siteid";
         $getParams[$getParamsCount]['value'] = $siteid;
         $getParams[$getParamsCount]['display'] = "Site: ".$siteid;
         $getParamsCount += 1;
      }
      $refsrc = strtolower(trim(getParameter("s_refsrc")));
      if ($refsrc != NULL) {
         $whereClause .= " AND LOWER(u.refsrc) LIKE '%".$refsrc."%'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_refsrc\" value=\"".$refsrc."\">\n";
         $getParams[$getParamsCount]['name'] = "s_refsrc";
         $getParams[$getParamsCount]['value'] = $refsrc;
         $getParams[$getParamsCount]['display'] = "Reference: ".$refsrc;
         $getParamsCount += 1;
      }
      $nletter = strtolower(trim(getParameter("s_nletter")));
      if ($nletter != NULL) {
         $whereClause .= " AND LOWER(u.nletter) LIKE '%".$nletter."%'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_nletter\" value=\"".$nletter."\">\n";
         $getParams[$getParamsCount]['name'] = "s_nletter";
         $getParams[$getParamsCount]['value'] = $nletter;
         $getParams[$getParamsCount]['display'] = "Newsletter: ".$nletter;
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("ownersite","Owner site");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("field1","Field1");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("field2","Field2");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("field3","Field3");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("title","Title");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("fname","First name");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("lname","Last name");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("username","Username");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("company","Company");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("email","Email");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }


      $gender = strtolower(trim(getParameter("s_gender")));
      if ($gender != NULL) {
         $whereClause .= " AND LOWER(u.gender)='".$gender."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_gender\" value=\"".$gender."\">\n";
         $getParams[$getParamsCount]['name'] = "s_gender";
         $getParams[$getParamsCount]['value'] = $gender;
         $getParams[$getParamsCount]['display'] = "Gender: ".$gender;
         $getParamsCount += 1;
      }

      $addr1 = strtolower(trim(getParameter("s_addr1")));
      if ($addr1 != NULL) {
         $whereClause .= " AND (LOWER(u.addr1) LIKE '%".$addr1."%' OR LOWER(u.addr2) LIKE '%".$addr1."%')";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_addr1\" value=\"".$addr1."\">\n";
         $getParams[$getParamsCount]['name'] = "s_addr1";
         $getParams[$getParamsCount]['value'] = $addr1;
         $getParams[$getParamsCount]['display'] = "Address: ".$addr1;
         $getParamsCount += 1;
      }

      $temp = $this->getLimitSearchStr("city","City");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }

      $temp = $this->getLimitSearchStr("state","State");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }
      $temp = $this->getLimitSearchStr("zip","Postal");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }

      $country = strtolower(trim(getParameter("s_country")));
      if ($country != NULL && 0!=strcmp($country,"xx")) {
         $whereClause .= " AND LOWER(u.country) LIKE '%".$country."%'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_country\" value=\"".$country."\">\n";
         $getParams[$getParamsCount]['name'] = "s_country";
         $getParams[$getParamsCount]['value'] = $country;
         $getParams[$getParamsCount]['display'] = "Country: ".$country;
         $getParamsCount += 1;
      }

      $phonenumber = strtolower(trim(getParameter("s_phonenumber")));
      if ($phonenumber != NULL) {
         $whereClause .= " AND (LOWER(u.phonenum) LIKE '%".$phonenumber."%'";
         $whereClause .= " OR LOWER(u.phonenum2) LIKE '%".$phonenumber."%'";
         $whereClause .= " OR LOWER(u.phonenum3) LIKE '%".$phonenumber."%'";
         $whereClause .= " OR LOWER(u.phonenum4) LIKE '%".$phonenumber."%')";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_phonenumber\" value=\"".$phonenumber."\">\n";
         $getParams[$getParamsCount]['name'] = "s_phonenumber";
         $getParams[$getParamsCount]['value'] = $phonenumber;
         $getParams[$getParamsCount]['display'] = "Phone: ".$phonenum;
         $getParamsCount += 1;
      }

      $temp = $this->getLimitSearchStr("website","Website");
      if ($temp!=NULL) {
         $whereClause .= $temp['whereClause'];
         $hiddenFields .= $temp['hiddenFields'];
         $getParams[$getParamsCount]['name'] = $temp['name'];
         $getParams[$getParamsCount]['value'] = $temp['value'];
         $getParams[$getParamsCount]['display'] = $temp['display'];
         $getParamsCount += 1;
      }

      $notes = strtolower(trim(getParameter("s_notes")));
      if ($notes != NULL) {
         $whereClause .= " AND LOWER(u.notes) LIKE '%".$notes."%'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_notes\" value=\"".$notes."\">\n";
         $getParams[$getParamsCount]['name'] = "s_notes";
         $getParams[$getParamsCount]['value'] = $notes;
         $getParams[$getParamsCount]['display'] = "Notes: ".$notes;
         $getParamsCount += 1;
      }

      $activatedstr = strtolower(trim(getParameter("s_activatedstr")));
      if ($activatedstr != NULL) {
         $whereClause .= " AND LOWER(u.activatedstr) LIKE '%".$activatedstr."%'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_activatedstr\" value=\"".$activatedstr."\">\n";
         $getParams[$getParamsCount]['name'] = "s_activatedstr";
         $getParams[$getParamsCount]['value'] = $activatedstr;
         $getParams[$getParamsCount]['display'] = "Reject reason: ".$activatedstr;
         $getParamsCount += 1;
      }

      $binaryfield4 = trim(getParameter("s_binaryfield4"));
      if ($binaryfield4!==NULL && is_numeric($binaryfield4)) {
         $whereClause .= " AND (FLOOR(u.field4/POW(2,".$binaryfield4.")) % 2)=1";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_binaryfield4\" value=\"".$binaryfield4."\">\n";
         $getParams[$getParamsCount]['name'] = "s_binaryfield4";
         $getParams[$getParamsCount]['value'] = $binaryfield4;
         $getParams[$getParamsCount]['display'] = "Field4: ".$binaryfield4;
         $getParamsCount += 1;         
      }

      $privacy = trim(getParameter("s_privacy"));
      if ($privacy != NULL) {
         if (strpos($fromTables,", useraccess ")===FALSE) {
            $fromTables .= ", useraccess x";
            $baseWhere .= " AND u.userid=x.userid";
         }

         if ($privacy<0) {
            $whereClause .= " AND x.sys='ADMIN' AND x.id>0";
         } else if ($privacy==0 || $privacy==NULL) {
            $whereClause .= " AND NOT(x.sys='WEBSITE' AND x.id>0) AND NOT(x.sys='ADMIN' AND x.id>0)";
         } else {
            $whereClause .= " AND x.sys='WEBSITE' AND x.id=".$privacy;
         }
         $hiddenFields .= "<input type=\"hidden\" name=\"s_privacy\" value=\"".$privacy."\">\n";
         $getParams[$getParamsCount]['name'] = "s_privacy";
         $getParams[$getParamsCount]['value'] = $privacy;
         $getParams[$getParamsCount]['display'] = "Access: ".$privacy;
         $getParamsCount += 1;
      }

      $userrelto = trim(getParameter("s_userrelto"));
      if ($userrelto != NULL) {
         if (strpos($fromTables,", userrel ")===FALSE) {
            $fromTables .= ", userrel ur";
            $baseWhere .= " AND u.userid=ur.reluserid";
         }

         $whereClause .= " AND ur.userid=".$userrelto;
         $hiddenFields .= "<input type=\"hidden\" name=\"s_userrelto\" value=\"".$userrelto."\">\n";
         $getParams[$getParamsCount]['name'] = "s_userrelto";
         $getParams[$getParamsCount]['value'] = $userrelto;
         $getParams[$getParamsCount]['display'] = "Related to: ".$userrelto;
         $getParamsCount += 1;
      }
      $userreltotype = trim(getParameter("s_userreltotype"));
      if ($userreltotype != NULL) {
         if (strpos($fromTables,", userrel ")===FALSE) {
            $fromTables .= ", userrel ur";
            $baseWhere .= " AND u.userid=ur.reluserid";
         }

         $whereClause .= " AND ur.rel_type='".$userreltotype."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_userreltotype\" value=\"".$userreltotype."\">\n";
         $getParams[$getParamsCount]['name'] = "s_userreltotype";
         $getParams[$getParamsCount]['value'] = $userreltotype;
         $getParams[$getParamsCount]['display'] = "Is a: ".$userreltotype;
         $getParamsCount += 1;
      }
      $userrelfrom = trim(getParameter("s_userrelfrom"));
      if ($userrelfrom != NULL) {
         if (strpos($fromTables,", userrel ")===FALSE) {
            $fromTables .= ", userrel ur";
            $baseWhere .= " AND u.userid=ur.reluserid";
         }

         $whereClause .= " AND ur.userid=".$userrelfrom;
         $hiddenFields .= "<input type=\"hidden\" name=\"s_userrelfrom\" value=\"".$userrelfrom."\">\n";
         $getParams[$getParamsCount]['name'] = "s_userrelfrom";
         $getParams[$getParamsCount]['value'] = $userrelfrom;
         $getParams[$getParamsCount]['display'] = "Referenced by: ".$userrelfrom;
         $getParamsCount += 1;
      }
      $userrelfromtype = trim(getParameter("s_userrelfromtype"));
      if ($userrelfromtype != NULL) {
         if (strpos($fromTables,", userrel ")===FALSE) {
            $fromTables .= ", userrel ur";
            $baseWhere .= " AND u.userid=ur.reluserid";
         }

         $whereClause .= " AND ur.rel_type='".$userrelfromtype."'";
         $hiddenFields .= "<input type=\"hidden\" name=\"s_userrelfromtype\" value=\"".$userrelfromtype."\">\n";
         $getParams[$getParamsCount]['name'] = "s_userrelfromtype";
         $getParams[$getParamsCount]['value'] = $userrelfromtype;
         $getParams[$getParamsCount]['display'] = "Is a: ".$userrelfromtype;
         $getParamsCount += 1;
      }
      if (class_exists("CustomUserSegment")) {
         $customObj = new CustomUserSegment();
         $returnObj = $customObj->searchUsers();
         if ($returnObj != NULL && count($returnObj['nvp'])>0) {
            for ($i=0; $i<count($returnObj['nvp']); $i++) {
               $name = $returnObj['nvp'][$i]["name"];
               $value = $returnObj['nvp'][$i]["value"];
               $hiddenFields .= "<input type=\"hidden\" name=\"".$name."\" value=\"".$value."\">\n";
               $getParams[$getParamsCount]['name'] = $name;
               $getParams[$getParamsCount]['value'] = $value;
               $getParams[$getParamsCount]['display'] = $returnObj['nvp'][$i]["display"].": ".$value;
               $getParamsCount += 1;            
            }
            $baseWhere .= $returnObj['baseWhere'];
            $whereClause .= $returnObj['whereClause'];
            $fromTables .= $returnObj['fromTables'];
         }
      }


      if ($usertype==NULL) $usertype = "user";
      $surveyObj = new Survey();
      $srvyName = $usertype." Properties";
      $survey = $surveyObj->getSurveyByName($srvyName);
      if ($survey!==NULL && $survey['survey_id']>0) {
         $responses['surveyid'] = $survey['survey_id'];
         $searchParams = $surveyObj->getCMSSearchParams($survey['survey_id'],"s.");   
         foreach ($searchParams['params'] as $key => $value){
            $hiddenFields .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">\n";
            $getParams[$getParamsCount]['name'] = $key;
            $getParams[$getParamsCount]['value'] = $value;
            $getParamsCount += 1;
         }
         if ($survey != NULL && $searchParams['where'] != NULL) {
            if (strpos($fromTables,", srvy_person p")===FALSE) {
               $fromTables .= ", survey".$survey['survey_id']." s";
               $fromTables .= ", srvy_person p";
               $baseWhere .= " AND u.userid=p.userid AND p.srvy_person_id=s.srvy_person_id";
            }
            $whereClause .= " AND ".$searchParams['where'];
         }
      } else {
         $wdObj = new WebsiteData();
         $webdata = $wdObj->getWebDataByName($srvyName);
         if ($webdata!==NULL && $webdata['wd_id']>0) {
            $responses['websitedataid'] = $webdata['wd_id'];
            $searchParams = $wdObj->getCMSSearchParams($webdata['wd_id'],"p.");   
            foreach ($searchParams['params'] as $key => $value){
               $hiddenFields .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">\n";
               $getParams[$getParamsCount]['name'] = $key;
               $getParams[$getParamsCount]['value'] = $value;
               $getParams[$getParamsCount]['display'] = $searchParams['display'][$key].": ".$value;
               $getParamsCount += 1;
            }
            if ($webdata != NULL && $searchParams['where'] != NULL) {
               if (strpos($fromTables,", wd_".$webdata['wd_id']." p")===FALSE) {
                  $fromTables .= ", wd_".$webdata['wd_id']." p";
                  $baseWhere .= " AND u.userid=p.userid AND (p.dbmode is NULL OR (p.dbmode<>'DELETED' AND p.dbmode<>'DUP')) ";
               }
               $whereClause .= " AND ".$searchParams['where'];
            } else {
               $includeproperties = strtolower(trim(getParameter("s_includeproperties")));
               if ($includeproperties==1) {
                  $hiddenFields .= "<input type=\"hidden\" name=\"s_includeproperties\" value=\"1\">\n";
                  if (strpos($fromTables,", wd_".$webdata['wd_id']." p")===FALSE) {
                     $fromTables .= ", wd_".$webdata['wd_id']." p";
                     $baseWhere .= " AND u.userid=p.userid AND (p.dbmode is NULL OR (p.dbmode<>'DELETED' AND p.dbmode<>'DUP')) ";
                  }
               }
            }
         }
      }


      $userlist = getParameter("s_userlist");
      $usersWhere = NULL;
      if ($userlist != NULL) {
         $dbLink = new MYSQLaccess;
         $query = "SELECT v.*, u.name as segmentname FROM usersegnvp v, usersegment s WHERE v.segmentid=u.segmentid && v.segmentid=".$userlist." AND v.name='userid';";
         $userids = $dbLink->queryGetResults($query);
         for ($i=0; $i<count($userids); $i++) {
            if ($i==0) $usersWhere = "u.userid=".$userids[$i]['value'];
            else $usersWhere .= " OR u.userid=".$userids[$i]['value'];
         }
         $hiddenFields .= "<input type=\"hidden\" name=\"s_userlist\" value=\"".$userlist."\">\n";
         $getParams[$getParamsCount]['name'] = "s_userlist";
         $getParams[$getParamsCount]['value'] = $userlist;
         $getParams[$getParamsCount]['display'] = "Saved search: ".$userids['segmentname'];
         $getParamsCount += 1;
         if ($usersWhere!=NULL) $whereClause = "( ".$whereClause." )  AND (".$usersWhere.")";
      }

      //print "\n<!-- **chj** where clause: ".$whereClause." -->\n";
      //print "\n\n<!-- ***chj*** current params:\n\n";
      //print_r($getParams);
      //print "\n-->\n\n";

      
      
      $responses['whereClause'] = $whereClause;
      $responses['fromTables'] = $fromTables;
      $responses['baseWhere'] = $baseWhere;
      $responses['getParams'] = $getParams;
      $responses['hiddenFields'] = $hiddenFields;
      return $responses;
   }

   function getUserTypes(){
      $utOpts ['User'] = "user";
      $utOpts ['Org'] = "org";
      //$utOpts ['System'] = "SYS";
      //$utOpts ['Admin'] = "ADMIN";
      $wdObj = new WebsiteData();
      $webdata = $wdObj->getWebDataByName("user types");
      if ($webdata!=NULL && $webdata['wd_id']>0) {
         $fields = $wdObj->getAllFieldsSystem($webdata['wd_id']);
         $qs = NULL;
         for ($i=0; $i<count($fields); $i++) $qs[strtolower($fields[$i]['label'])] = $fields[$i]['field_id'];
         $results = $wdObj->getRows($webdata['wd_id'],NULL,NULL,NULL,FALSE,NULL,FALSE,FALSE,FALSE,TRUE);
         $rows = $results['results'];
         for ($i=0; $i<count($rows); $i++) $utOpts[$rows[$i][$qs['user type description']]] = $rows[$i][$qs['user type code']];
      }
      return $utOpts;
   }

   function getSearchHTML($usertypes="user,org",$includeForm=TRUE,$selected=NULL,$postToForm=NULL,$exceptions=NULL,$renames=NULL,$includeCustom=FALSE){
      //print "<br>\nStart getSearchHTML()\n<br>";
      $str = "";
      $qdivs = "";
      if ($includeForm) {
         if ($postToForm==NULL) $postToForm = $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listusers";
         $str .= "<form action=\"".$postToForm."\" method=\"post\">";
         //$str .= "<input type=\"hidden\" name=\"action\" value=\"listusers\">\n";
         $str .= "<table border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
         $str .= "<TR><TD colspan=\"2\"><h2>Search For Users</h2></td></tr>\n";
      }

      if ($renames['refsrc']==NULL) $renames['refsrc'] = "Reference";
      if ($renames['company']==NULL) $renames['company'] = "Company";
      if ($renames['ownersite']==NULL) $renames['ownersite'] = "Orig Site";
      if ($renames['title']==NULL) $renames['title'] = "Title";
      if ($renames['field1']==NULL) $renames['field1'] = "Field 1";
      if ($renames['field2']==NULL) $renames['field2'] = "Field 2";
      if ($renames['field3']==NULL) $renames['field3'] = "Field 3";
      if ($renames['nletter']==NULL) $renames['nletter'] = "News Letter";
      if ($renames['notes']==NULL) $renames['notes'] = "Notes";
      if ($renames['dbmode']==NULL) $renames['dbmode'] = "Record status";

      $dbmodeopts = array();
      $dbmodeopts['New'] = "NEW";
      $dbmodeopts['Approved'] = "APPROVED";
      $dbmodeopts['Updated'] = "UPDATED";
      $dbmodeopts['Rejected'] = "REJECTED";
      $dbmodeopts['Duplicate'] = "DUP";
      $dbmodeopts['New and Updated'] = "NEW,UPDATED";
      $dbmodeopts['New and Approved'] = "NEW,APPROVED";
      $dbmodeopts['Approved and Updated'] = "APPROVED,UPDATED";
      $dbmodeopts['New/Approved/Updated'] = "NEW,APPROVED,UPDATED";
      $dbmodeopts['Deleted'] = "DELETED";

      $userArr = separateStringBy($usertypes,",");
      $surveyObj = new Survey();
      $wdObj = new WebsiteData();

      if (!in_array("usertype",$exceptions)) {
         $qdivs .= "<tr><td colspan=\"2\">\n";
         $str .= "<tr><td colspan=\"2\">\n";
         for ($i=0; $i<count($userArr); $i++) {
            $usertype = trim($userArr[$i]);
            if ($usertype!=NULL) {
               $str .= "<input type=\"radio\" name=\"s_usertype\" value=\"".$usertype."\" onClick=\"";
               for ($j=0; $j<count($userArr); $j++) {
                  if ($i==$j) $str .= "document.getElementById('usertype_sect_".trim($userArr[$j])."').style.display='';";
                  else $str .= "document.getElementById('usertype_sect_".trim($userArr[$j])."').style.display='none';";
               }
               $str .= "\"";
               $styleStr = "style=\"display:none;\"";
               if (strcmp($selected,$usertype)==0) {
                  $str .= " CHECKED";
                  $styleStr = "";
               }
               $str .= ">".$usertype." &nbsp; \n";
               $qdivs .= "<div id=\"usertype_sect_".$usertype."\" ".$styleStr.">\n<table cellpadding=\"1\" cellspacing=\"1\">\n";
               $survey = $surveyObj->getSurveyByName($usertype." Properties");
               if ($survey!=NULL && $survey['survey_id']>0) {
                  $questions = $surveyObj->getAllQuestions($survey['survey_id']);
                  for ($j=0; $j<count($questions); $j++) {
                     $qdivs .= $surveyObj->getSearchHTML($questions[$j]);
                  }
               } else {
                  $qdivs .= $wdObj->getSearchHTMLAllFields($usertype." Properties",NULL,$exceptions);
               }
               $qdivs .="\n</table>\n</div>\n";
            }
         }
         $qdivs .= "</td></tr>\n";
         $str .= "</td></tr>\n";
      }

      $str .= "<TR><TD colspan=\"2\">General search (list of users,names, email addresses) &nbsp; <input type=\"text\" name=\"s_filter\" value=\"".getParameter('s_filter')."\"></td></tr>\n";
      if (!in_array("created",$exceptions)) {
         $str .= "<TR><TD>Created After <font size=\"-2\">YYYY-MM-DD</font></td><TD><input type=\"text\" name=\"s_createdafter\" value=\"".getParameter('s_createdafter')."\"></td></tr>\n";
         $str .= "<TR><TD nowrap>Created Before <font size=\"-2\">YYYY-MM-DD</font></td><TD><input type=\"text\" name=\"s_createdbefore\" value=\"".getParameter('s_createdbefore')."\"></td></tr>\n";
      }

      if (!in_array("lastupdated",$exceptions)) {
         $str .= "<TR><TD>Updated After <font size=\"-2\">YYYY-MM-DD</font></td><TD><input type=\"text\" name=\"s_lastupdatedafter\" value=\"".getParameter('s_lastupdatedafter')."\"></td></tr>\n";
         $str .= "<TR><TD nowrap>Updated Before <font size=\"-2\">YYYY-MM-DD</font></td><TD><input type=\"text\" name=\"s_lastupdatedbefore\" value=\"".getParameter('s_lastupdatedbefore')."\"></td></tr>\n";
      }

      if (!in_array("fname",$exceptions)) $str .= "<TR><TD>First Name</td><TD><input type=\"text\" name=\"s_fname\" value=\"".getParameter('s_fname')."\"></td></tr>\n";
      if (!in_array("lname",$exceptions)) $str .= "<TR><TD>Last Name</td><TD><input type=\"text\" name=\"s_lname\" value=\"".getParameter('s_lname')."\"></td></tr>\n";
      if (!in_array("company",$exceptions)) $str .= "<TR><TD>".$renames['company']."</td><TD><input type=\"text\" name=\"s_company\" value=\"".getParameter('s_company')."\"></td></tr>\n";
      if (!in_array("title",$exceptions)) $str .= "<TR><TD>".$renames['title']."</td><TD><input type=\"text\" name=\"s_title\" value=\"".getParameter('s_title')."\"></td></tr>\n";
      if (!in_array("parentid",$exceptions)) $str .= "<TR><TD>Parent User ID</td><TD><input type=\"text\" name=\"s_parentid\" value=\"".getParameter('s_parentid')."\"></td></tr>\n";
      if (!in_array("alive",$exceptions)) {
         $str .= "<tr><td>Activity </td><td>\n";
         $tempchecked = "";
         if (getParameter('s_alive')==NULL) $tempchecked="CHECKED";
         $str .= "<input type=\"radio\" name=\"s_alive\" value=\"\" ".$tempchecked.">Active &nbsp; &nbsp; \n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_alive')),"no")) $tempchecked="CHECKED";
         $str .= "<input type=\"radio\" name=\"s_alive\" value=\"NO\" ".$tempchecked.">Dormant &nbsp; &nbsp; \n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_alive')),"no")) $tempchecked="CHECKED";
         $str .= "<input type=\"radio\" name=\"s_alive\" value=\"BOTH\" ".$tempchecked.">All &nbsp; &nbsp; \n";
         $str .= "</td></tr>\n";
      }
      if (!in_array("gender",$exceptions)) {
         $str .= "<TR><TD>Gender</td><TD>\n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_gender')),"m")) $tempchecked="CHECKED";
         $str .= "<input type=\"radio\" name=\"s_gender\" value=\"M\" ".$tempchecked.">Male &nbsp;&nbsp; \n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_gender')),"f")) $tempchecked="CHECKED";
         $str .= "<input type=\"radio\" name=\"s_gender\" value=\"F\" ".$tempchecked.">Female\n";
         $str .= "</td></tr>\n";
      }
      if (!in_array("addr1",$exceptions)) $str .= "<TR><TD>Address 1</td><TD><input type=\"text\" name=\"s_addr1\" value=\"".getParameter('s_addr1')."\"></td></tr>\n";
      if (!in_array("city",$exceptions)) $str .= "<TR><TD>City</td><TD><input type=\"text\" name=\"s_city\" value=\"".getParameter('s_city')."\"></td></tr>\n";
      if (!in_array("state",$exceptions)) $str .= "<TR><TD>State</td><TD><input type=\"text\" name=\"s_state\" value=\"".getParameter('s_state')."\"></td></tr>\n";
      if (!in_array("zip",$exceptions)) $str .= "<TR><TD>Postal Code</td><TD><input type=\"text\" name=\"s_zip\" value=\"".getParameter('s_zip')."\"></td></tr>\n";
      if (!in_array("country",$exceptions)) $str .= "<TR><TD>Country</td><TD>".listCountries(getParameter('s_country'),"s_country",TRUE)."</td></tr>\n";
      if (!in_array("phonenumber",$exceptions)) $str .= "<TR><TD>Phone Number</td><TD><input type=\"text\" name=\"s_phonenumber\" value=\"".getParameter('s_phonenumber')."\"></td></tr>\n";
      if (!in_array("email",$exceptions)) $str .= "<TR><TD>Email</td><TD><input type=\"text\" name=\"s_email\" value=\"".getParameter('s_email')."\"></td></tr>\n";
      if (!in_array("refsrc",$exceptions)) $str .= "<TR><TD>".$renames['refsrc']."</td><TD><input type=\"text\" name=\"s_refsrc\" value=\"".getParameter('s_refsrc')."\"></td></tr>\n";
      if (!in_array("ownersite",$exceptions)) $str .= "<TR><TD>".$renames['ownersite']."</td><TD><input type=\"text\" name=\"s_ownersite\" value=\"".getParameter('s_ownersite')."\"></td></tr>\n";
      if (!in_array("field1",$exceptions)) $str .= "<TR><TD>".$renames['field1']."</td><TD><input type=\"text\" name=\"s_field1\" value=\"".getParameter('s_field1')."\"></td></tr>\n";
      if (!in_array("field2",$exceptions)) $str .= "<TR><TD>".$renames['field2']."</td><TD><input type=\"text\" name=\"s_field2\" value=\"".getParameter('s_field2')."\"></td></tr>\n";
      if (!in_array("field3",$exceptions)) $str .= "<TR><TD>".$renames['field3']."</td><TD><input type=\"text\" name=\"s_field3\" value=\"".getParameter('s_field3')."\"></td></tr>\n";
      if (!in_array("nletter",$exceptions)) $str .= "<TR><TD>".$renames['nletter']."</td><TD><input type=\"text\" name=\"s_nletter\" value=\"".getParameter('s_nletter')."\"></td></tr>\n";
      if (!in_array("notes",$exceptions)) $str .= "<TR><TD>".$renames['notes']."</td><TD><input type=\"text\" name=\"s_notes\" value=\"".getParameter('s_notes')."\"></td></tr>\n";
      if (!in_array("dbmode",$exceptions)) $str .= "<tr><td>".$renames['dbmode']."</td><td>".getOptionList("s_dbmode",$dbmodeopts,convertBack(strtoupper(getParameter('s_dbmode'))),TRUE,"style=\"font-size:10px;font-family:verdana;\"")."</td></tr>\n";
      if (!in_array("activatedstr",$exceptions)) $str .= "<TR><TD>".$renames['activatedstr']."</td><TD><input type=\"text\" name=\"s_activatedstr\" value=\"".getParameter('s_activatedstr')."\"></td></tr>\n";

      if (!in_array("siteid",$exceptions)) {
         $ctx = new Context();
         $sitearr = $ctx->getSiteContext();
         if ($ctx->isSiteLeaf($sitearr[0]['siteid'])) {
            $str .= "<input type=\"hidden\" name=\"search_siteid\" value=\"".$sitearr[0]['siteid']."\">\n";
         } else {
            $sitelist = $ctx->getSiteOptions(-1,0,NULL,TRUE);
            $str .= "<TR><TD>Site</td><TD>".getOptionList("search_siteid",$sitelist,getParameter('search_siteid'),TRUE)."</td></tr>\n";
         }
      }


      if (!in_array("privacy",$exceptions)) {
         $privacyOpts = array();
         $privacyOpts['Administrator']=-1;
         $privacyOpts['No Approval']=0;
         for ($j=1; $j<=10; $j++) $privacyOpts['Website level '.$j]=$j;
         $privacySearch = getOptionList("s_privacy", $privacyOpts, getParameter("s_privacy"), TRUE);
         $str .= "<tr><td>Access Level:</td><td>".$privacySearch."</td></tr>\n";
      }
      $str .= $qdivs;

      if ($includeCustom && class_exists("CustomUserSegment")) {
         $customObj = new CustomUserSegment();
         $str .= $customObj->getSearchParamsHTML();
      }

      if ($includeForm) {
         $str .= "<TR><TD colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Search\"></TD></TR>\n";
         $str .= "</table>";
         $str .= "</form>\n";
      }
      //print "<br>\nEnd getSearchHTML()\n<br>";
      return $str;
   }


   function getSearchHTMLSmall($usertypes="user,org",$selected=NULL,$exceptions=NULL,$renames=NULL,$includeCustom=FALSE){
      $str = "";
      $qdivs = "";
      $style_label = "float:left;min-width:130px;margin-top:6px;";
      $style_value = "float:left;min-width:130px;";
      $style_input = "width:120px;font-size:10px;font-family:verdana;";
      $div_separator = "<div style=\"clear:both;\"></div>";

      if ($renames['refsrc']==NULL) $renames['refsrc'] = "Reference";
      if ($renames['company']==NULL) $renames['company'] = "Company";
      if ($renames['ownersite']==NULL) $renames['ownersite'] = "Orig Site";
      if ($renames['title']==NULL) $renames['title'] = "Title";
      if ($renames['field1']==NULL) $renames['field1'] = "Field 1";
      if ($renames['field2']==NULL) $renames['field2'] = "Field 2";
      if ($renames['field3']==NULL) $renames['field3'] = "Field 3";
      if ($renames['nletter']==NULL) $renames['nletter'] = "News Letter";
      if ($renames['notes']==NULL) $renames['notes'] = "Notes";
      if ($renames['dbmode']==NULL) $renames['dbmode'] = "Record status";
      if ($renames['activatedstr']==NULL) $renames['activatedstr'] = "Reject reason";

      $dbmodeopts = array();
      $dbmodeopts['New'] = "NEW";
      $dbmodeopts['Approved'] = "APPROVED";
      $dbmodeopts['Updated'] = "UPDATED";
      $dbmodeopts['Rejected'] = "REJECTED";
      $dbmodeopts['Duplicate'] = "DUP";
      $dbmodeopts['New and Updated'] = "NEW,UPDATED";
      $dbmodeopts['New and Approved'] = "NEW,APPROVED";
      $dbmodeopts['Approved and Updated'] = "APPROVED,UPDATED";
      $dbmodeopts['New/Approved/Updated'] = "NEW,APPROVED,UPDATED";
      $dbmodeopts['Deleted'] = "DELETED";
      $dbmodeopts['Rejected/Deleted'] = "REJECTED,DELETED";

      $userArr = separateStringBy($usertypes,",",NULL,TRUE);
      $wdObj = new WebsiteData();

      if (!in_array("usertype",$exceptions)) {
         $str .= "<div style=\"clear:both;\"></div>";
         for ($i=0; $i<count($userArr); $i++) {
            $usertype = trim($userArr[$i]);
            if ($usertype!=NULL) {
               $str .= "<div style=\"float:left;min-width:100px;\"><input type=\"radio\" name=\"s_usertype\" value=\"".$usertype."\" onClick=\"";
               for ($j=0; $j<count($userArr); $j++) {
                  if ($i==$j) $str .= "document.getElementById('usertype_sect_".trim($userArr[$j])."').style.display='';";
                  else $str .= "document.getElementById('usertype_sect_".trim($userArr[$j])."').style.display='none';";
               }
               $str .= "\"";
               $styleStr = "style=\"display:none;\"";
               if (strcmp($selected,$usertype)==0) {
                  $str .= " CHECKED";
                  $styleStr = "";
               }
               $str .= ">".$usertype."</div>\n";
               $str .= $div_separator;
               //$str .= "<div style=\"clear:both;\"></div>";
               $qdivs .= "<div id=\"usertype_sect_".$usertype."\" ".$styleStr.">\n";
               $qdivs .= $wdObj->getSearchHTMLAllFields($usertype." Properties",NULL,$exceptions,TRUE);

               if (class_exists("CustomUserSegment")) {
                  $customObj = new CustomUserSegment();
                  $qdivs .= $customObj->getSearchParamsHTML(TRUE,$usertype);
               }

               $qdivs .="\n</div>\n";
            }
         }
         $str .= "<div style=\"clear:both;\"></div>";
      }

      $str .= $div_separator;
      $str .= "<div style=\"".$style_label."\">Filter</div><div style=\"".$style_value."\"><input type=\"text\" name=\"s_filter\" value=\"".getParameter('s_filter')."\" style=\"".$style_input."\"></div>\n";
      $str .= $div_separator;
      if (!in_array("created",$exceptions)) {
         $str .= $div_separator;
         $str .= "<div style=\"".$style_label."\">Created After</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_createdafter\" value=\"".getParameter('s_createdafter')."\"></div>\n";
         $str .= $div_separator;
         $str .= "<div style=\"".$style_label."\">Created Before</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_createdbefore\" value=\"".getParameter('s_createdbefore')."\"></div>\n";
      }

      if (!in_array("lastupdated",$exceptions)) {
         $str .= $div_separator;
         $str .= "<div style=\"".$style_label."\">Updated After</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_lastupdatedafter\" value=\"".getParameter('s_lastupdatedafter')."\"></div>\n";
         $str .= $div_separator;
         $str .= "<div style=\"".$style_label."\">Updated Before</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_lastupdatedbefore\" value=\"".getParameter('s_lastupdatedbefore')."\"></div>\n";
      }
      
      if (!in_array("fname",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">First Name</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_fname\" value=\"".getParameter('s_fname')."\"></div>\n";
      if (!in_array("lname",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Last Name</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_lname\" value=\"".getParameter('s_lname')."\"></div>\n";
      if (!in_array("company",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['company']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_company\" value=\"".getParameter('s_company')."\"></div>\n";
      if (!in_array("title",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['title']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_title\" value=\"".getParameter('s_title')."\"></div>\n";
//print "\n\n<!-- ***chj*** parentid check\nExceptions:\n";
//print_r($exceptions);
//print "\n\nin_array: ".in_array("parentid",$exceptions);
//print "\n-->\n\n";
      if (!in_array("parentid",$exceptions)) {
         $str .= $div_separator;
         
         $pid_sel = array();
         $pid_sel[1] = "";
         $pid_sel[2] = "";
         $pid_sel[3] = "";
         $pid_sel[4] = "";
         $pid_css = "display:none;";
         if(getParameter('s_parentid')==NULL || 0==strcmp(getParameter('s_parentid'),"exclude")) $pid_sel[1] = "SELECTED";
         else if(0==strcmp(getParameter('s_parentid'),"ignore")) $pid_sel[2] = "SELECTED";
         else if(getParameter('s_parentid')==-1001) $pid_sel[3] = "SELECTED";
         else {
            $pid_sel[4] = "SELECTED";
            $pid_css = "";
         }
         
         $str .= "<div style=\"width:1px;height:8px;overflow:hidden;\"></div>\n";
         $str .= "<div style=\"margin-top:5px;\" id=\"advsrch_parentid_sel\">\n";
         $str .= "<select id=\"advsrch_parentid_opt\" onchange=\"jQuery('#s_parentid').val('');jQuery('#advsrch_parentid').hide();var x=parseInt(jQuery('#advsrch_parentid_opt').val());if(x==1) jQuery('#s_parentid').val('exclude');if(x==2) jQuery('#s_parentid').val('ignore');if(x==3) jQuery('#s_parentid').val('-1001');if(x==4) jQuery('#advsrch_parentid').show();\">\n";
         $str .= "<option value=\"1\" ".$pid_sel[1].">Exclude Parents</option>\n";
         $str .= "<option value=\"2\" ".$pid_sel[2].">Include Parents</option>\n";
         $str .= "<option value=\"3\" ".$pid_sel[3].">Only parents</option>\n";
         $str .= "<option value=\"4\" ".$pid_sel[4].">Children of</option>\n";
         $str .= "</select>\n";
         $str .= "</div>\n";
         $str .= "<div id=\"advsrch_parentid\" style=\"".$pid_css."\">\n";
         $str .= "<div style=\"".$style_label."\">Parent User ID</div>";
         $str .= "<div style=\"".$style_value."\">";
         $str .= "<input style=\"".$style_input."\" type=\"text\" id=\"s_parentid\" name=\"s_parentid\" value=\"".getParameter('s_parentid')."\">";
         $str .= "</div>\n";
         $str .= "<div style=\"clear:both;\"></div>\n";
         $str .= "</div>\n";
         $str .= "<div style=\"width:1px;height:8px;overflow:hidden;\"></div>\n";
      }
      if (!in_array("gender",$exceptions)) {
         $str .= $div_separator;
         $str .= "<div style=\"".$style_label."\">Gender</div>\n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_gender')),"m")) $tempchecked="CHECKED";
         $str .= "<div style=\"".$style_value."\"><input type=\"radio\" name=\"s_gender\" value=\"M\" ".$tempchecked.">Male</div>\n";
         $tempchecked = "";
         if (0==strcmp(strtolower(getParameter('s_gender')),"f")) $tempchecked="CHECKED";
         $str .= "<div style=\"".$style_value."\"><input type=\"radio\" name=\"s_gender\" value=\"F\" ".$tempchecked.">Female</div>\n";
      }
      if (!in_array("addr1",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Address 1</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_addr1\" value=\"".getParameter('s_addr1')."\"></div>\n";
      if (!in_array("city",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">City</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_city\" value=\"".getParameter('s_city')."\"></div>\n";
      if (!in_array("state",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">State</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_state\" value=\"".getParameter('s_state')."\"></div>\n";
      if (!in_array("zip",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Postal Code</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_zip\" value=\"".getParameter('s_zip')."\"></div>\n";
      if (!in_array("country",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Country</div><div style=\"".$style_value."\">".listCountries(getParameter('s_country'),"s_country",TRUE)."</div>\n";
      if (!in_array("phonenumber",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Phone Number</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_phonenumber\" value=\"".getParameter('s_phonenumber')."\"></div>\n";
      if (!in_array("email",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">Email</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_email\" value=\"".getParameter('s_email')."\"></div>\n";
      if (!in_array("refsrc",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['refsrc']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_refsrc\" value=\"".getParameter('s_refsrc')."\"></div>\n";
      if (!in_array("ownersite",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['ownersite']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_ownersite\" value=\"".getParameter('s_ownersite')."\"></div>\n";
      if (!in_array("field1",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['field1']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_field1\" value=\"".getParameter('s_field1')."\"></div>\n";
      if (!in_array("field2",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['field2']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_field2\" value=\"".getParameter('s_field2')."\"></div>\n";
      if (!in_array("field3",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['field3']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_field3\" value=\"".getParameter('s_field3')."\"></div>\n";
      if (!in_array("nletter",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['nletter']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_nletter\" value=\"".getParameter('s_nletter')."\"></div>\n";
      if (!in_array("notes",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['notes']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_notes\" value=\"".getParameter('s_notes')."\"></div>\n";
      if (!in_array("dbmode",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['dbmode']."</div><div style=\"".$style_value."\">".getOptionList("s_dbmode",$dbmodeopts,convertBack(strtoupper(getParameter('s_dbmode'))),TRUE,"style=\"font-size:10px;font-family:verdana;\"")."</div>\n";
      if (!in_array("activatedstr",$exceptions)) $str .= $div_separator."<div style=\"".$style_label."\">".$renames['activatedstr']."</div><div style=\"".$style_value."\"><input style=\"".$style_input."\" type=\"text\" name=\"s_activatedstr\" value=\"".getParameter('s_activatedstr')."\"></div>\n";

      if (!in_array("siteid",$exceptions)) {
         $ctx = new Context();
         $sitearr = $ctx->getSiteContext();
         if ($ctx->isSiteLeaf($sitearr[0]['siteid'])) {
            $str .= "<input type=\"hidden\" name=\"search_siteid\" value=\"".$sitearr[0]['siteid']."\">\n";
         } else {
            $sitelist = $ctx->getSiteOptions(-1,0,NULL,TRUE);
            $str .= $div_separator."<div style=\"".$style_label."\">Site</div><div style=\"".$style_value."\">".getOptionList("search_siteid",$sitelist,getParameter('search_siteid'),TRUE)."</div>\n";
         }
      }


      if (!in_array("privacy",$exceptions)) {
         $privacyOpts = array();
         $privacyOpts['Administrator']=-1;
         $privacyOpts['No Approval']=0;
         for ($j=1; $j<=10; $j++) $privacyOpts['Website level '.$j]=$j;
         $privacySearch = getOptionList("s_privacy", $privacyOpts, getParameter("s_privacy"), TRUE);
         $str .= $div_separator."<div style=\"".$style_label."\">Access Level:</div><div style=\"".$style_value."\">".$privacySearch."</div>\n";
      }



      if (!in_array("limit",$exceptions)) {
         $limitopts = array();
         $limitopts['25'] = 25;
         $limitopts['50'] = 50;
         $limitopts['100'] = 100;
         $limitopts['200'] = 200;
         $limitopts['500'] = 500;
         $limit = getParameter("limit");
         if ($limit == NULL) $limit = 50;
         //if ($limit == NULL) $limit = 100;
         $limitsearch = getOptionList("limit", $limitopts,$limit);
         $str .= $div_separator."<div style=\"".$style_label."\">Users per page</div><div style=\"".$style_value."\">".$limitsearch."</div>\n";
      }


      $str .= $qdivs;

      if ($includeCustom && class_exists("CustomUserSegment")) {
         $customObj = new CustomUserSegment();
         $str .= $customObj->getSearchParamsHTML(TRUE);
      }

      $str .= $div_separator;
      //return "<div style=\"max-width:150px;\">".$str."</div>";
      return "<div style=\"max-width:200px;\">".$str."</div>";
   }


    function simplesearchUsers($filter=NULL, $name=NULL, $email=NULL, $company=NULL, $addr=NULL, $nameReqd=FALSE, $orderby=NULL, $justCount=FALSE, $limit=NULL, $page=1, $usertype="user", $ignoreDummies=TRUE, $table="useracct") {
      $dbLink = new MYSQLaccess;
      $query = "SELECT ";
      if ($justCount) $query .= "count(*)";
      else $query .= "u.*";

      $query .= " FROM ".$table." u WHERE usertype='".$usertype."'";

      if ($ignoreDummies) $query .= " AND email NOT LIKE '%dummy%' ";
      if ($nameReqd) {
         $query .= " AND fname IS NOT NULL AND fname<>'' ";
         $query .= " AND lname IS NOT NULL AND lname<>'' ";
      }

      if ($name != NULL) {
         $filArr = separateStringBy(trim(strtolower($name))," ");
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),"*");
            for ($m=0; $m<count($filArr1);$m++) {
               $filArr2 = separateStringBy(trim($filArr1[$m]),",");
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"%");
                  for ($k=0; $k<count($filArr3);$k++) {
                        $query .= " AND (LOWER(u.fname) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.lname) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.username) LIKE '%".trim($filArr3[$k])."%') ";
                  }
               }
            }
         }
      }

      if ($email != NULL) {
         $filArr = separateStringBy(trim(strtolower($email))," ");
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),"*");
            for ($m=0; $m<count($filArr1);$m++) {
               $filArr2 = separateStringBy(trim($filArr1[$m]),",");
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"%");
                  for ($k=0; $k<count($filArr3);$k++) {
                        $query .= " AND (LOWER(u.email) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.email2) LIKE '%".trim($filArr3[$k])."%') ";
                  }
               }
            }
         }
      }

      if ($company != NULL) {
         $filArr = separateStringBy(trim(strtolower($company))," ");
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),"*");
            for ($m=0; $m<count($filArr1);$m++) {
               $filArr2 = separateStringBy(trim($filArr1[$m]),",");
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"%");
                  for ($k=0; $k<count($filArr3);$k++) {
                     $query .= " AND LOWER(u.company) LIKE '%".trim($filArr3[$k])."%' ";
                  }
               }
            }
         }
      }

      if ($addr != NULL) {
         $filArr = separateStringBy(trim(strtolower($addr))," ");
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),"*");
            for ($m=0; $m<count($filArr1);$m++) {
               $filArr2 = separateStringBy(trim($filArr1[$m]),",");
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"%");
                  for ($k=0; $k<count($filArr3);$k++) {
                     $query .= " AND (LOWER(u.addr1) LIKE '%".trim($filArr3[$k])."%'";
                     $query .= " OR LOWER(u.addr2) LIKE '%".trim($filArr3[$k])."%'";
                     $query .= " OR LOWER(u.city) LIKE '%".trim($filArr3[$k])."%'";
                     $query .= " OR LOWER(u.state) LIKE '%".trim($filArr3[$k])."%') ";
                  }
               }
            }
         }
      }

      if ($filter != NULL) {
         $filArr = separateStringBy(trim(strtolower($filter))," ");
         for ($i=0; $i<count($filArr);$i++) {
            $filArr1 = separateStringBy(trim($filArr[$i]),"*");
            for ($m=0; $m<count($filArr1);$m++) {
               $filArr2 = separateStringBy(trim($filArr1[$m]),",");
               for ($j=0; $j<count($filArr2);$j++) {
                  $filArr3 = separateStringBy(trim($filArr2[$j]),"%");
                  for ($k=0; $k<count($filArr3);$k++) {
                     //if (is_numeric($filArr3[$k])) $query .= " OR u.userid=".trim($filArr3[$k]);
                     //else {
                        $query .= " AND (LOWER(u.fname) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.lname) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.email) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.company) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.title) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.notes) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.username) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.phonenum) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.phonenum2) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.addr1) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.addr2) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.city) LIKE '%".trim($filArr3[$k])."%'";
                        $query .= " OR LOWER(u.state) LIKE '%".trim($filArr3[$k])."%') ";
                     //}
                  }
               }
            }
         }
      }

      if (!$justCount) {
         $query .= " ORDER BY ";
         if ($orderby!=NULL) $query .= $orderby;
         else $query .= "u.lname";
   
         if ($limit!=NULL && is_numeric($limit)) {
            $start = 0;
            if ($page>1) $start = ($page - 1) * $limit;
            $query .= " LIMIT ".$start.",".$limit;
         }
      }
      //print "\n<!-- simple search users: ".$sql." -->\n";
      $values = $dbLink->queryGetResults($query);
      return $values;
    }

    function searchUsers($orderby=NULL,$justCount=FALSE, $limit=NULL, $page=1, $table="useracct") {


      //return $this->getUsersForSegment(NULL,NULL,$orderby,$page,$limit,$justCount,$table);


      $dbLink = new MYSQLaccess;
      $responses = $this->searchUsersSQL();
      $fromTables = $responses['fromTables'];
      $baseWhere = $responses['baseWhere'];
      $whereClause = $responses['whereClause'];
      $getParams = $responses['getParams'];
      $hiddenFields = $responses['hiddenFields'];

      if ($justCount) {
         $sql = "SELECT COUNT(DISTINCT u.userid) AS totalnumber FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         if ($whereClause!=NULL) $sql .= " AND ".$whereClause;
      } else {
         $orderby = "u.userid";
         if ($orderby==NULL || 0==strcmp($orderby,"")) $orderby = "u.lname, u.company";
         //$sql = "SELECT DISTINCT u.userid, u.email, u.fname, u.lname, u.company, u.alive, u.created, u.dbmode FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         $sql = "SELECT DISTINCT u.userid, u.email FROM ".$table." u".$fromTables." WHERE 1=1 ".$baseWhere;
         if ($whereClause != NULL) $sql .= " AND ".$whereClause;
         $sql .= " ORDER BY ".$orderby;

         if ($limit!=NULL) $sql .= " LIMIT ".(($page-1)*$limit).",".$limit;

         $sql .= ";";
      }
      $values = $dbLink->queryGetResults($sql);
      //print "\n\n<!-- **chj** user search query: ".$sql."  -->\n\n";
      $returnObj['users'] = $values;
      $returnObj['hiddenFields'] = $hiddenFields;
      $returnObj['getParams'] = $getParams;
      return $returnObj;
    }

   function setUserSearchParams($segmentid=NULL) {
      unset($_SESSION['params']);
      if ($segmentid==NULL) $segmentid = getParameter("segmentid");
      $uSeg = $this->getUserSegment($segmentid);
      if ($segmentid != NULL && $uSeg != NULL) {
         for ($i=0; $i<count($uSeg['getParams']); $i++) {
            $name = $uSeg['getParams'][$i]['name'];
            $value = $uSeg['getParams'][$i]['value'];
            $_SESSION['params'][$name] = $value;
         }
      }
   }

   function getUserListSegments(){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT s.segmentid, s.name FROM usersegment s, usersegnvp p WHERE s.segmentid=p.segmentid AND p.name='s_userlist' ORDER BY s.name;";
      $segments = $dbLink->queryGetResults($sql);
      $results = array();
      $results['Select List']=0;
      for ($i=0; $i<count($segments); $i++) $results[$segments[$i]['name']] = $segments[$i]['segmentid'];
      return $results;
   }

   function isUserListSegment($segmentid){
      if ($segmentid==NULL) return FALSE;
      $found = TRUE;
      $dbLink = new MYSQLaccess;
      $sql = "SELECT s.segmentid FROM usersegment s, usersegnvp p WHERE s.segmentid=p.segmentid AND s.segmentid=".$segmentid." AND p.name='s_userlist';";
      $segments = $dbLink->queryGetResults($sql);
      if ($segments==NULL || count($segments)<1) $found = FALSE;
      return $found;
   }




   function getAllUserSegments(){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM usersegment ORDER BY name;";
      $segments = $dbLink->queryGetResults($sql);
      return $segments;
   }

   function getAllDropdownSegments(){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM usersegment WHERE dropdown=1 ORDER BY name;";
      $segments = $dbLink->queryGetResults($sql);
      return $segments;
   }

   function getUserSegmentsFor($seggroupid=-1){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM usersegment WHERE seggroupid=".$seggroupid." ORDER BY name;";
      $segments = $dbLink->queryGetResults($sql);
      return $segments;
   }

   function getSegGroupList($seggroupid=-1,$previousPath="root",$grpArray=NULL){
      $grpArray[$previousPath]=$seggroupid;
      $groups = $this->getSegmentGroupsFor($seggroupid);
      if ($groups==NULL || count($groups)<1) {
         return $grpArray;
      } else {
         for ($i=0; $i<count($groups); $i++) {
            $grpArray = $this->getSegGroupList($groups[$i]['seggroupid'],$previousPath."/".$groups[$i]['name'],$grpArray);
         }
         return $grpArray;
      }
   }

   function newSegmentGroup($name,$parentid=-1){
      $dbLink = new MYSQLaccess;
      $sql = "INSERT INTO userseggroup (parentid,name) VALUES (".$parentid.", '".convertString(trim($name))."');";
      return $dbLink->insertGetValue($sql);
   }
   
   function updateSegmentGroup($seggroupid,$name,$parentid=-1){
      $dbLink = new MYSQLaccess;
      $sql = "UPDATE userseggroup set parentid=".$parentid.", name='".convertString(trim($name))."' WHERE seggroupid=".$seggroupid.";";
      $dbLink->insert($sql);
   }
   
   function removeSegmentGroup($seggroupid){
      if (!$this->isSegGroupEmpty($seggroupid)) return FALSE;
      else {
         $dbLink = new MYSQLaccess;
         $sql = "DELETE FROM userseggroup WHERE seggroupid=".$seggroupid." ORDER BY name;";
         $dbLink->delete($sql);
         return TRUE;
      }
   }

   function isSegGroupEmpty($seggroupid){
      $groups = $this->getSegmentGroupsFor($seggroupid);
      if ($groups!=NULL && count($groups)>0) return FALSE;
      else {
         $segments = $this->getUserSegmentsFor($seggroupid);
         if ($segments!=NULL && count($segments)>0) return FALSE;
         else return TRUE;      
      }
   }

   function getAllSegmentGroups(){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM userseggroup ORDER BY name;";
      $segments = $dbLink->queryGetResults($sql);
      return $segments;
   }

   function getSegmentGroupsFor($parentid=-1){
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM userseggroup WHERE parentid=".$parentid." ORDER BY name;";
      $segments = $dbLink->queryGetResults($sql);
      return $segments;
   }

   function getSegmentGroup($seggroupid) {
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM userseggroup WHERE seggroupid=".$seggroupid.";";
      $segments = $dbLink->queryGetResults($sql);
      return $segments[0];
   }

   function getSegmentIdByName($segmentName=NULL){
      if ($segmentName==NULL) return NULL;
      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM usersegment WHERE LOWER(name)='".strtolower($segmentName)."';";
      $segments = $dbLink->queryGetResults($sql);
      $returnObj = $segments[0]['segmentid'];
      return $returnObj;
   }

   function getUserSegmentName($segmentid=NULL) {
      if ($segmentid==NULL) return NULL;

      $dbLink = new MYSQLaccess;
      $sql = "SELECT name FROM usersegment WHERE segmentid=".$segmentid.";";
      $segments = $dbLink->queryGetResults($sql);
      $returnObj = $segments[0]['name'];
      return $returnObj;
   }

   function getUserSegment($segmentid=NULL) {
      if ($segmentid==NULL) return NULL;

      $dbLink = new MYSQLaccess;
      $sql = "SELECT * FROM usersegment WHERE segmentid=".$segmentid.";";
      $segments = $dbLink->queryGetResults($sql);
      $returnObj = $segments[0];
      $sql = "SELECT * FROM usersegnvp WHERE segmentid=".$segmentid.";";
      $segmentnvps = $dbLink->queryGetResults($sql);
      $returnObj['getParams']= $segmentnvps;
      return $returnObj;
   }

   function newSegment($name,$descr,$getParams,$uselist,$seggroupid=NULL,$dropdown=NULL){
      if ($seggroupid==NULL) $seggroupid=-1;
      if ($dropdown==NULL) $dropdown=0;
      $dbLink = new MYSQLaccess;
      $sql = "INSERT INTO usersegment (name,descr,seggroupid,dropdown) VALUES ('".$name."','".$descr."',".$seggroupid.",".$dropdown.");";
      $segmentid = $dbLink->insertGetValue($sql);
      for ($i=0; $i<count($getParams); $i++) {
         $sql = "INSERT INTO usersegnvp (segmentid,name,value) VALUES (".$segmentid.",'".$getParams[$i]['name']."','".$getParams[$i]['value']."');";
         $dbLink->insert($sql);
      }
      if ($uselist==1) {
         $sql = "INSERT INTO usersegnvp (segmentid,name,value) VALUES (".$segmentid.",'s_userlist','".$segmentid."');";
         $dbLink->insert($sql);
      }
   }

   function addUserToList($segmentid,$userid) {
      if ($segmentid!=NULL && $segmentid>0 && $userid!=NULL) {
         $dbLink = new MYSQLaccess;
         $sql = "SELECT * FROM usersegnvp WHERE segmentid=".$segmentid." AND name='userid' AND value='".$userid."';";
         $values = $dbLink->queryGetResults($sql);
         if ($values==NULL || count($values)<1) {
            $sql = "INSERT INTO usersegnvp (segmentid,name,value) VALUES (".$segmentid.",'userid','".$userid."');";
            $dbLink->insert($sql);
         }
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function addEmailsToSegment($segmentid,$emaillist){
      $emailsArr = array();
      $emailsArr1 = separateStringBy(trim($emaillist),",");
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
      for ($i=0; $i<count($emailsArr); $i++) {
         if ($emailsArr[$i]!=NULL && $this->userExists($emailsArr[$i])) {
            $user = $this->getUserByEmail($emailsArr[$i]);
            $this->addUserToList($segmentid,$user['userid']);
         }
      }
   }

   function removeUserFromList($segmentid,$userid) {
      //print "<BR>remove user from list: param1: ".$segmentid." param2: ".$userid."<br>";
      if ($segmentid!=NULL && $segmentid>0 && $userid!=NULL) {
         $dbLink = new MYSQLaccess;
         $sql = "DELETE FROM usersegnvp WHERE segmentid=".$segmentid." AND name='userid' AND value='".$userid."';";
         $dbLink->delete($sql);
         return TRUE;
      } else {
         return FALSE;
      }      
   }

   function addSegmentCondition ($segmentid=NULL,$condition="AND"){
      if ($segmentid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      $sql = "DELETE FROM usersegnvp WHERE segmentid=".$segmentid." AND name='SEGMENTCONDITION';";
      $dbLink->delete($sql);
      $sql = "INSERT INTO usersegnvp (segmentid, name, value) VALUES (".$segmentid.",'SEGMENTCONDITION','".$condition."');";
      $dbLink->insert($sql);
      return TRUE;
   }

   function addInclSegmentId ($segmentid=NULL,$inclsegmentid=NULL){
      if ($segmentid==NULL || $inclsegmentid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      //$sql = "DELETE FROM usersegnvp WHERE segmentid=".$segmentid." AND name='SEGMENTID';";
      //$dbLink->delete($sql);
      $sql = "INSERT INTO usersegnvp (segmentid, name, value) VALUES (".$segmentid.",'SEGMENTID','".$inclsegmentid."');";
      $dbLink->insert($sql);
      return TRUE;
   }

   function removeInclSegmentId ($segmentid=NULL,$inclsegmentid=NULL){
      if ($segmentid==NULL || $inclsegmentid==NULL) return FALSE;
      $dbLink = new MYSQLaccess;
      $sql = "DELETE FROM usersegnvp WHERE segmentid=".$segmentid." AND name='SEGMENTID' AND value='".$inclsegmentid."';";
      $dbLink->delete($sql);
      return TRUE;
   }

   function updateSegment($segmentid,$name,$descr,$getParams,$seggroupid=NULL,$dropdown=NULL){
      //print "\n\n<!-- ***chj*** current params:\n\n";
      //print_r($getParams);
      //print "\n-->\n\n";
   	 
      if ($seggroupid==NULL) $seggroupid=-1;
      if ($dropdown==NULL) $dropdown=0;
      $dbLink = new MYSQLaccess;
      $sql = "UPDATE usersegment set name='".$name."',descr='".$descr."',seggroupid=".$seggroupid.", dropdown=".$dropdown." WHERE segmentid=".$segmentid.";";
      $dbLink->update($sql);
      //print "<br>query: ".$sql."<BR>\n";
      $sql = "DELETE FROM usersegnvp WHERE name!='SEGMENTCONDITION' AND name!='userid' AND name!='SEGMENTID' AND segmentid=".$segmentid.";";
      $dbLink->delete($sql);
      //print "<br>query: ".$sql."<BR>\n";
      for ($i=0; $i<count($getParams); $i++) {
         $sql = "INSERT INTO usersegnvp (segmentid,name,value) VALUES (".$segmentid.",'".$getParams[$i]['name']."','".$getParams[$i]['value']."');";
         $dbLink->insert($sql);
         //print "\n<!-- query: ".$sql." -->\n";
      }
   }

   function deleteSegment($segmentid){
      $dbLink = new MYSQLaccess;
      $sql = "DELETE FROM usersegment WHERE segmentid=".$segmentid.";";
      $dbLink->delete($sql);
      $sql = "DELETE FROM usersegnvp WHERE segmentid=".$segmentid.";";
      $dbLink->delete($sql);
   }




    function getUserField($userid,$fieldname,$table="useracct") {
      if ($userid==NULL || $fieldname==NULL) return NULL;
      $dbLink = new MYSQLaccess;
      $query = "SELECT ".$fieldname." FROM ".$table." WHERE userid=".$userid.";";
      $lines = $dbLink->queryGetResults($query);
      return $lines[0][$fieldname];
    }

    function getUser($userid,$refresh=FALSE,$table="useracct",$ignoreParent=FALSE) {
      if ($userid==NULL) return NULL;

      $dbLink = new MYSQLaccess;
      $query = "SELECT * FROM ".$table." WHERE userid=".$userid.";";
      if(!is_numeric($userid)) $query = "SELECT * FROM ".$table." WHERE (email='".$userid."' OR username='".$userid."');";
      $lines = $dbLink->queryGetResults($query);
      $user = $lines[0];
      if ($user==NULL) return NULL;

      if ($user['addr1']==NULL && $user['city']==NULL) {
         if ($user['addrid']!=NULL && $user['addrid']>0) {
            $query = "SELECT * FROM addr WHERE addrid=".$user['addrid'].";";
         } else {
            $query = "SELECT * FROM addr WHERE userid=".$user['userid']." ORDER BY addrid DESC;";
         }
         $lines = $dbLink->queryGetResults($query);
         if($lines!=NULL && count($lines)>0) {
            $result = $lines[0];            
            foreach($result as $key => $value) if (0!=strcmp($key,"email") && 0!=strcmp($key,"userid") && $result[$key]!=NULL) $user[$key]=$result[$key];
         }
      }

      if ($refresh && $GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl'];
         $url .= "jsfcode/atomcontroller.php?action=refreshlogin";
         $url .= "&email=".$user['email'];
         $url .= "&token=".$user['token'];
         $url = str_replace(" ","%20",$url);
         //print "\n<!-- getuser refresh url: ".$url." -->\n";
         $result = $reader->Parse($url);
         $stuff = $result['feed']['entry'];
         if (0!=strcmp($stuff['content'],"FAIL") && 0!=strcmp($stuff[0]['content'],"FAIL")) {
            $query = NULL;
            for ($i=0; $i<count($stuff); $i++) {
               $name = $stuff[$i]['title'];
               if (0!=strcmp($name,"refreshlogin") && 0!=strcmp($name,"userid") && 0!=strcmp($name,"email") && 0!=strcmp($name,"email2") && 0!=strcmp($name,"password") && 0!=strcmp($name,"password2") && 0!=strcmp($name,"activated") && 0!=strcmp($name,"alive") && 0!=strcmp($name,"login") && 0!=strcmp($name,"lastlogin") && 0!=strcmp($name,"created") && 0!=strcmp($name,"lastupdated")) {
                  $value = $stuff[$i]['content'];
                  $user[$name]=$value;
                  if ($query!=NULL) $query .= ", ";
                  else $query = "";
                  $query .= $name."='".convertString($value)."'";
               }
            }
            if ($query!=NULL) {
               //print "\n<!-- getuser refresh query: ".$query." -->\n";
               $query = "UPDATE useracct SET ".$query." WHERE userid=".$user['userid'].";";
               $dbLink->update($query);
            }
         }
      }
      
      if(!$ignoreParent && $user['parentid']>0){
         $puser = $this->getUser($user['parentid'],$refresh,$table,TRUE);
         foreach($user as $key=>$val) {
            if($user[$key]==NULL && $puser[$key]!=NULL) $user[$key]=$puser[$key];
         }
      }

      return $user;
    }

    function getFullUserInfo($userid,$pub=FALSE,$ignoreParent=FALSE,$forthepublic=FALSE){
      $table="useracct";
      if ($pub) $table="useracct_pub";
      $user = $this->getUser($userid,FALSE,$table,$ignoreParent);
      if ($user==NULL) return NULL;

      //$surveyObj = new Survey();
      //$survey = $surveyObj->getSurveyByName($user['usertype']." Properties");
      //if ($survey != NULL) {
      //   $questions = $surveyObj->getAllQuestionsSystem($survey['survey_id']);
      //   $qs = NULL;
      //   for ($i=0; $i<count($questions); $i++) $qs[strtolower($questions[$i]['label'])] = $questions[$i]['question_id'];
      //
      //   $results = $surveyObj->getDataBySurveyAndUserid($survey['survey_id'], $user['userid']);
      //   $sci = $results[0];
      //   foreach ($qs as $key => $value) $user[$key] = $sci[$value];
      //   $user['srvy_person_id'] = $sci['srvy_person_id'];
      //   $user['survey_id'] = $survey['survey_id'];
      //} else {
         $wdObj = new WebsiteData();
         $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
         if ($webdata != NULL) {
            //print "<br>\nuseracct:getfulluserinfo wd: ".$webdata['wd_id']."<br>\n";
            $results = $wdObj->getDataByUserid($webdata['wd_id'], $user['userid'], NULL, $pub);
            $sci = $results[0];
            $questions = $wdObj->getAllFieldsSystem($webdata['wd_id']);
            $qs = NULL;
            for ($i=0; $i<count($questions); $i++) {
               $l = trim(strtolower($questions[$i]['label']));
               $m = trim(strtolower($questions[$i]['map']));
               if($l!=NULL) $qs[$l] = $questions[$i]['field_id'];
               if($m!=NULL) $qs[$m] = $questions[$i]['field_id'];
               $user[$questions[$i]['field_id']] = $sci[$questions[$i]['field_id']];
            }
            foreach ($qs as $key => $value) $user[$key] = $sci[$value];
            $user['srvy_person_id'] = $sci['wd_row_id'];
            //$user['srvy_person_id'] = $user['userid'].$sci['wd_id'].$sci['wd_row_id'];
            $user['wd_row_id'] = $sci['wd_row_id'];
            $user['wd_id'] = $webdata['wd_id'];
            $user['lastupdateby'] = $sci['lastupdateby'];
         }
      //}
      

      $user2 = $user;
      if($forthepublic) {
         $user2 = array();
         foreach($user as $key => $value) {
            //if (0!=strcmp($key,"password") && 0!=strcmp($key,"token") && 0!=strcmp(substr($key,0,1),"q")) {
            if (0!=strcmp($key,"password") && 0!=strcmp($key,"password2") && 0!=strcmp(substr($key,0,1),"q")) {
               $user2[$key]=$value;
            }
            if ($user2['token']==NULL) {
               $user2['token'] = getRandomNum();
               $this->updateField($user2['userid'],"token",$user2['token']);
            }
            if ($this->isUserAdmin($user['userid'])) $resp['user']['isadmin']=1;
         }
      }
      

      return $user2;
    }

    function getFullUserInfoByEmail($email){
      return $this->getFullUserInfo($email);
    }

    function getFullUserInfoByUsername($username){
      return $this->getFullUserInfo($username);
    }

    function getUsersOfType($type,$orderby=NULL,$table="useracct"){
      if ($orderby==NULL) $orderby = "u.lname, u.fname, u.company";
      $dbLink = new MYSQLaccess;

      $select = "SELECT u.*";
      $from = "FROM ".$table." u";
      $where = "WHERE u.usertype='".$type."'";

      $qs = array();
      $wdObj = new WebsiteData();
      $webdata = $wdObj->getWebDataByName($type." Properties");
      if ($webdata != NULL) {
         $qs = $wdObj->getFieldNames($webdata['wd_id']);
         foreach($qs as $key => $val) $select .= ", w.".$key;
         $from .= ", wd_".$webdata['wd_id']." w";
         $where .= " AND u.userid=w.userid";
      }

      $query = $select." ".$from." ".$where." ORDER BY ".$orderby.";";
      $users = $dbLink->queryGetResults($query);
      if ($qs!=NULL && count($qs)>0) {
         for ($i=0;$i<count($users);$i++) {
            foreach($qs as $key => $val) $users[$i][$val] = $users[$i][$key];
         }
      }
      return $users;
    }

    function updateUserProperty($userid,$name,$value,$skipstatus=FALSE){
      $rnotes = $namew.";".$value;
      $this->track("updateUserProperty",$rnotes,"updateUserProperty",$userid);
      
      $user = $this->getUser($userid);
      $surveyObj = new Survey();
      $survey = $surveyObj->getSurveyByName($user['usertype']." Properties");
      if ($survey != NULL) {
         $questions = $surveyObj->getAllQuestionsSystem($survey['survey_id']);
         $qs = NULL;
         for ($i=0; $i<count($questions); $i++) $qs[strtolower($questions[$i]['label'])] = $questions[$i]['question_id'];

         $results = $surveyObj->getDataBySurveyAndUserid($survey['survey_id'], $userid);
         $sci = $results[0];
         if ($sci==NULL || $sci['spi']==NULL) {
            $srvy_person_id = $surveyObj->addEmail($survey['survey_id'], $user['email']);
            $surveyObj->updatePersonExplicit($srvy_person_id,$userid,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
         } else {
            $srvy_person_id = $sci['spi'];
         }
         $qid = $qs[strtolower($name)];
         if ($srvy_person_id!=NULL && $qid!=NULL) {
            $dbLink = new MYSQLaccess;
            $query = "UPDATE survey".$survey['survey_id']." SET ".$qid."='".convertString(trim($value))."' WHERE srvy_person_id=".$srvy_person_id.";";
            $dbLink->update($query);
            if(!$skipstatus) $this->setLastUpdated($userid);
         }
      } else{
         $wdObj = new WebsiteData();
         $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
         if ($webdata != NULL) {
            $qs = $wdObj->getFieldLabels($webdata['wd_id']);
   
            $results = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
            $sci = $results[0];
            if ($sci==NULL || $sci['wd_row_id']==NULL) {
               $wd_row_id = $wdObj->addRow($webdata['wd_id'], $user['userid']);
            } else {
               $wd_row_id = $sci['wd_row_id'];
            }
            $qid = $qs[strtolower($name)];
            if ($qid==NULL && in_array($name,$qs)) $qid = $name;
            if ($wd_row_id!=NULL && $qid!=NULL && 0==strcmp(substr($qid,0,1),"q") && is_numeric(substr($qid,1))) {
               $dbLink = new MYSQLaccess;
               $query = "UPDATE wd_".$webdata['wd_id']." SET ".$qid."='".convertString(trim($value))."' WHERE wd_row_id=".$wd_row_id.";";
               $dbLink->update($query);
               if(!$skipstatus) $this->setLastUpdated($userid);
            }
         }
      }
    }

    function updateMultipleUserProperties($userid,$names,$values,$printdebug=FALSE){
       if($printdebug) {
          print "<br>\n";
          print "<br>\n";
          print "useracct::updateMultipleUserProperties for ".$userid."<br>\nNames:<br>\n";
          print_r($names);
          print "<br>\nValues:<br>\n";
          print_r($values);
          print "<br>\n";
          print "<br>\n";
       }
       
       // Do NOT update the status fields
       // Do NOT update the useracct table (only properties)
       // updateMultipleFields($userid,$names,$values,$lastupdateby=NULL,$source=NULL,$pub=FALSE, $checkprops=TRUE, $updateflag=TRUE, $checkuseracct=TRUE)
       $this->updateMultipleFields($userid,$names,$values,NULL,NULL,FALSE,TRUE,FALSE,FALSE);
    }
    
    function OLD_updateMultipleUserProperties($userid,$names,$values,$printdebug=FALSE){
      $rnotes = implode(",",$names).";".implode(",",$values);
      $this->track("updateMultipleUserProperties",$rnotes,"updateMultipleUserProperties",$userid);
      
      $dbLink = new MYSQLaccess;
      $user = $this->getUser($userid);
      $wdObj = new WebsiteData();
      $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
      if ($webdata != NULL) {
         $qs = $wdObj->getFieldLabels($webdata['wd_id'],TRUE,TRUE);
         $results = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
         $sci = $results[0];
         if ($sci==NULL || $sci['wd_row_id']==NULL) {
            $wd_row_id = $wdObj->addRow($webdata['wd_id'], $user['userid']);
         } else {
            $wd_row_id = $sci['wd_row_id'];
         }
         $setterStr = "";
         for ($i=0; $i<count($names); $i++) {
            $name = $names[$i];
            $value = $values[$i];
            $qid = NULL;
            if(isset($qs[strtolower(trim($name))])) $qid = $qs[strtolower(trim($name))];
            if ($qid==NULL && in_array($name,$qs)) $qid = $name;
            if ($wd_row_id!=NULL && $qid!=NULL) {
               if (strlen($setterStr)>2) $setterStr .= ", ";
               
               //if value is in string format, convert it to wd_row_id format
               $fld = $wdObj->getField($webdata['wd_id'], $qid);
               if(strcmp($fld['field_type'],"FOREIGN")==0 || strcmp($fld['field_type'],"FOREIGNCB")==0) {
                  $value = $wdObj->reverseConvertForeignWD($fld['question'],$value);
               } else if(strcmp($fld['field_type'],"old_CHECKBOX")==0 || strcmp($fld['field_type'],"old_HRZCHKBX")==0 || strcmp($fld['field_type'],"old_NEWCHKBX")==0) {
                  $cb_answers = separateStringBy($value,",",NULL,TRUE);            
                  $cb_bothnvp = separateStringBy(trim(convertBack($fld['question'])),";");
                  $cb_across = $cb_bothnvp[0];
                  $cb_names = separateStringBy($cb_bothnvp[1],",");
                  $cb_values = separateStringBy($cb_bothnvp[2],",");
                  if ($cb_bothnvp[1]==NULL && $cb_bothnvp[2]==NULL) {
                      $cb_names = separateStringBy($cb_bothnvp[0],",");
                  } else if (!is_numeric($cb_across)) {
                      $cb_values = $cb_names;
                      $cb_names = separateStringBy($cb_bothnvp[0],",");
                  }
                  if($cb_values==NULL || count($cb_values)<1) $cb_values = $cb_names;
                  $value = "";
                  for ($j=0; $j<count($cb_names); $j++) {
                     for($k=0;$k<count($cb_answers);$k++) {
                        if(0==strcmp(strtolower($cb_answers[$k]),strtolower($cb_values[$j]))) {
                           $value .= $cb_values[$j].",";
                           break;
                        } else if(0==strcmp(strtolower($cb_answers[$k]),strtolower($cb_names[$j]))) {
                           if($cb_values[$j]!=NULL) $value .= $cb_values[$j].",";
                           else $value .= $cb_names[$j].",";
                           break;                              
                        }
                     }
                  }
                  
               } else if(strcmp($fld['field_type'],"DROPDOWN")==0 && $value==1 && strpos(strtolower($fld['question']),"yes")!==FALSE) {
                  $value = "YES";
               } else if(strcmp($fld['field_type'],"SNGLCHKBX")==0 && $value==1) {
                  $value = "YES";
               }
               $tempval = convertString(trim($value));
               if($tempval!=NULL) $setterStr .= $qid."='".$tempval."'";
               else $setterStr .= $qid."=NULL";
               if($printdebug) print "<br>\nadditional setter: ".$setterStr."<br>\n";
            }
         }
         if (strlen($setterStr)>2) {
            $query = "UPDATE wd_".$webdata['wd_id']." SET ".$setterStr." WHERE wd_row_id=".$wd_row_id.";";
            $dbLink->update($query);
            if($printdebug) print "<br>\nquery: ".$query."<br>\n";
         }

      }
    }

    //-----------------------------------------------------------------
    // 'to' direction represents from the parents' perspective
    // 'from' direction represents from the child's persepctive
    //-----------------------------------------------------------------
    function getUsersRelated($userid,$direction="to",$rel_type=NULL,$usertype=NULL,$table="useracct",$limit=NULL,$countonly=FALSE){
      $results = array();
      $rel_table = "userrel";
      if ($table==NULL) {
         $table="useracct";
      } else if (strpos($table,"_pub")!==FALSE) {
         $rel_table = "userrel_pub";
      }
      if ($userid!=NULL) {
         if (0==strcmp($direction,"both")) {
            $arr1 = $this->getUsersRelated($userid,"to",$rel_type,$usertype,$table,$limit,$countonly);
            if ($arr1==NULL || count($arr1)<1) $arr1 = array();
            $arr2 = $this->getUsersRelated($userid,"from",$rel_type,$usertype,$table,$limit,$countonly);
            if ($arr2==NULL || count($arr2)<1) $arr2 = array();
            if ($countonly) {
               $results[0]['total'] = $arr1[0]['total'] + $arr2[0]['total'];
            } else {
               $results = array_merge($arr1,$arr2);
            }
         } else {
            $dbLink = new MYSQLaccess;
            $query = "SELECT r.*, u.email, u.fname, u.lname, u.company, u.addrid, u.addr1, u.addr2, u.phonenum, u.city, u.state, u.usertype, u.userid as xuserid FROM ".$rel_table." r, ".$table." u WHERE ";
            if ($countonly) $query = "SELECT count(u.userid) as total FROM ".$rel_table." r, ".$table." u WHERE ";
            if (0==strcmp($direction,"to")) {
               $query .= "r.userid=".$userid." AND r.reluserid=u.userid ";
            } else {
               $query .= "r.reluserid=".$userid." AND r.userid=u.userid ";
            }
            if ($rel_type!=NULL) $query .= " AND r.rel_type='".$rel_type."'";
            if ($usertype!=NULL) $query .= " AND u.usertype='".$usertype."'";
            //if (!$countonly) $query .= " ORDER BY u.lname, u.fname, u.company";
            if (!$countonly) $query .= " ORDER BY r.rel_type, u.lname, u.fname, u.company";
            if (!$countonly && $limit!=NULL && $limit>0) $query .= " LIMIT 0,".$limit;
            $query .= ";";
            //print "query: ".$query;
            $results = $dbLink->queryGetResults($query);
         }
      }
//print "<br>Query: ".$query."<BR>";
//print_r($results);
//print "<br>";
      return $results;
    }

    //-----------------------------------------------------------------
    // 'to' direction represents from the parents' perspective
    // 'from' direction represents from the child's persepctive
    //-----------------------------------------------------------------
    function getUsersRelatedList($userids,$direction="to",$rel_type=NULL,$table="useracct"){
      if ($userids==NULL || count($userids)<1) return NULL;
      $dbLink = new MYSQLaccess;
      $relatedlist = array();
      $counter = 0;

      $comparefield = "r.reluserid";
      if (0==strcmp($direction,"to")) $comparefield = "r.userid";

      while ($counter<count($userids)) {
         $subcounter = 0;
         $addlquery = "";
         for ($i=0;$i<100;$i++) {
            if (isset($userids[$counter]) && $userids[$counter]['userid']!=NULL) {
               $addlquery .= " OR ".$comparefield."=".$userids[$counter]['userid'];
               $subcounter++;
               $counter++;
            } else {
               break;
            }
         }
         if ($subcounter>0) {
            $query = "SELECT r.*, u.email FROM userrel r, ".$table." u WHERE ";
            if (0==strcmp($direction,"to")) $query .= "r.reluserid=u.userid ";
            else $query .= "r.userid=u.userid ";
            if ($rel_type!=NULL) $query .= " AND r.rel_type='".$rel_type."'";
            $query .= " AND (1=0 ".$addlquery.")";
            $query .= ";";
            $results = $dbLink->queryGetResults($query);
            $relatedlist = array_merge($relatedlist,$results);
         }
      }

      return $relatedlist;
    }


    function isUserRelated($userid1,$userid2,$rel_type=NULL){
      if ($userid1==NULL || $userid2==NULL) return NULL;
      $dbLink = new MYSQLaccess;
      $query = "SELECT * FROM userrel WHERE ((userid=".$userid1." AND reluserid=".$userid2.") OR (userid=".$userid2." AND reluserid=".$userid1."))";
      if ($rel_type!=NULL) $query .= " AND rel_type='".$rel_type."'";
      $query .= ";";
      $results = $dbLink->queryGetResults($query);
      if ($results==NULL || count($results)<1) return FALSE;
      else return TRUE;
    }

   function getUserRelations(){
      $relTypeOpt['Survey Administrator'] = "SRVYADMIN";
      $relTypeOpt['Alt Survey Contact'] = "SRVYALT";
      $relTypeOpt['Administrator'] = "ADMIN";
      $relTypeOpt['Public Contact'] = "PUBCNTCT";
      $relTypeOpt['Employee'] = "EMPLOYEE";
      $relTypeOpt['Former Employee'] = "FORMEREMP";
      $relTypeOpt['Contractor'] = "CONTRACT";
      //$relTypeOpt['Parent/Child'] = "PARENT";
      return $relTypeOpt;
   }

   function getRelTypeString($rel_type){
      $relTypeOpt = $this->getUserRelations();
      $str = "";
      foreach($relTypeOpt as $key => $value) {
         if (0==strcmp($rel_type,$value)) $str = $key;
      }
      return $str;
   }

    function addUserRelationship($userid,$reluserid,$rel_type,$field1=NULL,$field2=NULL){
      $dbLink = new MYSQLaccess;
      $query = "INSERT INTO userrel (userid,reluserid,rel_type,field1,field2) VALUES (".$userid.",".$reluserid.",'".$rel_type."','".$field1."','".$field2."');";
      if (!$this->isUserRelated($userid,$reluserid,$rel_type)) return $dbLink->insertGetValue($query);
      else return NULL;
    }

   function removeUserRelationship($userrel_id){
      $dbLink = new MYSQLaccess;
      $query = "DELETE FROM userrel WHERE userrel_id=".$userrel_id.";";
      $dbLink->delete($query);
   }

   function updateUserRelationship($userrel_id,$rel_type){
      $dbLink = new MYSQLaccess;
      $query = "UPDATE userrel set rel_type='".$rel_type."' WHERE userrel_id=".$userrel_id.";";
      $dbLink->update($query);
   }

    function doSubstitutions($str,$userInfo) {
      if ($userInfo!=NULL) {
         //print "\n<!-- ";
         //print_r($userInfo);
         //print " -->\n";
         foreach ($userInfo as $key => $value) {
            //print "\n<!-- ".$value." -->\n";
            $str = str_replace("%%%USER_".strtoupper($key)."%%%",$value,$str);
         }
      }

      $finished = false;
      while (!$finished) {
         $shortname = findInternalString("%%%USER_","%%%",$str);
         if ($shortname == NULL) $finished = true;
         else {
            $str = str_replace("%%%USER_".$shortname."%%%","",$str);
            //print "\n<!-- ".$shortname." -->\n";
         }
      }

      return $str;
    }
                    
    function getUserByUsername($username,$table="useracct") {
      $dbLink = new MYSQLaccess;
      $query = "SELECT * FROM ".$table." WHERE LOWER(username)='".convertString(strtolower(trim($username)))."' OR LOWER(email)='".convertString(strtolower(trim($username)))."';";
      $lines = $dbLink->queryGetResults($query);
      if ($lines != NULL && count($lines)>0) {
         $user = $lines[0];

         if ($user['addr1']==NULL && $user['city']==NULL) {
            if ($user['addrid']!=NULL && $user['addrid']>0) {
               $query = "SELECT * FROM addr WHERE addrid=".$user['addrid'].";";
            } else {
               $query = "SELECT * FROM addr WHERE userid=".$user['userid']." ORDER BY addrid DESC;";
            }
   
           $lines = $dbLink->queryGetResults($query);
           $result = $lines[0];
         
            foreach($result as $key => $value) {
               if (0!=strcmp($key,"email") && 0!=strcmp($key,"userid") && $result[$key]!=NULL) $user[$key]=$result[$key];
            }
         }

         return $user;
      } else {
         return null;
      }
    }

    function getUserByEmail($email,$table="useracct") {
      $dbLink = new MYSQLaccess;
      //$query = "SELECT * FROM useracct WHERE email='".convertString(strtolower(trim($email)))."' OR email2='".convertString(strtolower(trim($email2)))."';";
      $query = "SELECT * FROM ".$table." WHERE email='".convertString(strtolower(trim($email)))."' AND (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'REJECTED'));";
      $lines = $dbLink->queryGetResults($query);
      if ($lines != NULL && count($lines)>0) {
         $user = $lines[0];

         if ($user['addr1']==NULL && $user['city']==NULL) {
            if ($user['addrid']!=NULL && $user['addrid']>0) {
               $query = "SELECT * FROM addr WHERE addrid=".$user['addrid'].";";
            } else {
               $query = "SELECT * FROM addr WHERE userid=".$user['userid']." ORDER BY addrid DESC;";
            }
   
            $lines = $dbLink->queryGetResults($query);
            $result = $lines[0];
         
            foreach($result as $key => $value) {
               if (0!=strcmp($key,"email") && 0!=strcmp($key,"userid") && $result[$key]!=NULL) $user[$key]=$result[$key];
            }
         }
         return $user;
      } else {
         return null;
      }
    }

    function getUserByField1($field1,$table="useracct") {
      $dbLink = new MYSQLaccess;
      $query = "SELECT * FROM ".$table." WHERE LOWER(field1)='".convertString(strtolower(trim($field1)))."' AND (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'REJECTED'));";
      $lines = $dbLink->queryGetResults($query);
      $user = NULL;
      if ($lines != NULL && count($lines)>0) {
         $user = $lines[0];
      }
      return $user;
    }

    function deleteUserAcct($userid, $removeAcct=TRUE, $force=FALSE) {
       $dbLink = new MYSQLaccess;

       if ($userid ==NULL) {
         return False;
       } else {
         $user = $this->getUser($userid);
         if (0==strcmp($user['dbmode'],"DELETED") || $force) {
   
            $jsftrack1 = "Deleting user: ".$userid." email: ".$user['email']." Name: ".$user['fname']." ".$user['lname']." ".$user['company'];
            $this->track("deleteUserAcct",$jsftrack1,"Remove Account",$userid);
   
            $query = "DELETE FROM useraccess WHERE userid=".$userid.";";
            $dbLink->delete($query);
            if ($removeAcct) {
               $query = "DELETE FROM addr WHERE userid=".$userid.";";
               $dbLink->delete($query);
               $query = "DELETE FROM useracct WHERE userid=".$userid.";";
               $dbLink->delete($query);
               $query = "DELETE FROM useracct_pub WHERE userid=".$userid.";";
               $dbLink->delete($query);
               $query = "DELETE FROM usermsg WHERE userid=".$userid.";";
               $dbLink->delete($query);
               $query = "DELETE FROM userpost WHERE userid=".$userid.";";
               $dbLink->delete($query);
               $query = "DELETE FROM userpost WHERE refid='".$userid."' AND posttype='userpost';";
               $dbLink->delete($query);
               
               
               $query = "SELECT survey_id, srvy_person_id FROM srvy_person WHERE userid=".$userid;
               $lines = $dbLink->queryGetResults($query);
               for ($i=0; $i<count($lines); $i++) {
                  $query = "DELETE FROM survey".$lines[$i]['survey_id']." WHERE srvy_person_id=".$lines[$i]['srvy_person_id'].";";
                  $dbLink->delete($query);               
               }
               $query = "DELETE FROM srvy_person WHERE userid=".$userid;
               $dbLink->update($query);
               
               $query = "SELECT wd_id FROM webdata;";
               $lines = $dbLink->queryGetResults($query);
               for ($i=0; $i<count($lines); $i++) {
                  $query = "DELETE FROM wd_".$lines[$i]['wd_id']." WHERE userid=".$userid.";";
                  $dbLink->delete($query);

                  $query = "show tables like 'wd_".$lines[$i]['wd_id']."_pub';";
                  $results = $dbLink->queryGetResults($query);
                  if ($results != NULL && count($results)>0) {
                     $query = "DELETE FROM wd_".$lines[$i]['wd_id']."_pub WHERE userid=".$userid.";";
                     $dbLink->update($query);
                  }
               }
            }
            $query = "DELETE FROM userrel WHERE userid=".$userid." OR reluserid=".$userid.";";
            $dbLink->delete($query);
            
            $query = "DELETE FROM userrel_pub WHERE userid=".$userid." OR reluserid=".$userid.";";
            $dbLink->delete($query);
            
            $query = "DELETE FROM usersegnvp WHERE name='userid' AND value='".$userid."';";
            $dbLink->delete($query);
         } else {
            $query = "UPDATE useracct SET dbmode='DELETED', lastupdated=NOW() where userid=".$userid.";";
            $dbLink->update($query);
            $query = "DELETE FROM useracct_pub where userid=".$userid.";";
            $dbLink->update($query);
         }

         if (class_exists("CustomUserPromote")) {
            $customObj = new CustomUserPromote();
            $customObj->promoteAccount($user['userid']);
         }

         return TRUE;
       }
    }

    function updateLastLogin($userid) {
      if ($userid==NULL) return NULL;
      $user = $this->getUser($userid);
      $query = "UPDATE useracct SET ";
      if($user['login']!=NULL) $query .= "lastlogin='".$user['login']."', ";
      $query .= "login='".getDateForDB()."' WHERE userid=".$user['userid'].";";
      $dblink = new MYSQLaccess;
      $dblink->update($query);
    }

    function logInUser() {
      $email     = convertString(strtolower(trim(getParameter("email"))));
      $username  = convertString(strtolower(trim(getParameter("username"))));
      $password  = trim(getParameter("password"));

      if (($email==NULL && $username==NULL) || $password==NULL || 0==strcmp($password,"")) return FALSE;

      //print "\n<!-- ***chj*** login: ".$email."/".$password." -->\n";
      
      $authKey['username'] = $username;
      $authKey['email'] = $email;
      $authKey['password'] = $password;
      if ($this->userAuthenticate($authKey)) {
        $luser = NULL;
        if ($username!=NULL) {
            $luser = $this->getUserByUsername($username);
            if ($luser==NULL || $luser['userid']==NULL) $luser = $this->getUserByEmail($username);
        } else $luser = $this->getUserByEmail($email);
        
        //print "\n<!-- ***chj*** logged in user: \n";
        //print_r($luser);
        //print "\n-->\n";
        
        $this->updateLastLogin($luser['userid']);
        $this->addUserToSession($luser);
         //setcookie("email",$luser['email'], time()+60*60*24*120,'/');
         //setcookie("loggedin","1", time()+60*60*2,'/');
         $ckDomain = getCookieDomain();
         //setcookie("userid_t",$luser['userid'], time()+60*60*24*365,'/',$ckDomain,TRUE,TRUE);
         //setcookie("userid_t",$luser['userid'], time()+60*60*24*365,'/',$ckDomain);
         //print "<br>\n".$_COOKIE['userid_t']."<br>\n";
        if (getParameter("setcookie")!=NULL && (strcmp(strtoupper(trim(getParameter("setcookie"))),"TRUE")==0 || getParameter("setcookie")==1)) {
            //setcookie("autologin",$luser['userid'], time()+60*60*24*180,'/',$ckDomain,TRUE,TRUE);
            //setcookie("autologin",$luser['userid'], time()+60*60*24*180,'/',$ckDomain);
            setcookie("userid_t",$luser['userid'], time()+60*60*24*365,'/',$ckDomain);
            //print "\n<!-- autologin cookie set: ".$ckDomain." user set: ".$_COOKIE['autologin']." -->\n";
            print "\n<!-- userid_t cookie set: ".$ckDomain." user set: ".$_COOKIE['userid_t']." -->\n";
        }        
        return True;
      }
      else return False;
    }

    function sendForgottenEmail($email,$from=NULL,$title=NULL,$url=NULL,$testing=0) {
      $email = convertString(strtolower(trim($email)));
      if ($email==NULL) return FALSE;

      //First try to reset the remote password (if there is one)
      if ($this->remoteResetPassword($email)) return TRUE;

      if (!$this->userProfileExists($email)) {
         if($testing==1) print "<br>\nProfile does not exist for ".$email.".";
         return FALSE;
      }

      $luser = $this->getUserByEmail($email);
      if ( $luser != null) {
         $dblink = new MYSQLaccess;
         $password = getRandomNum();
         $token = getRandomNum();
         $query = "UPDATE useracct SET lastupdated='".getDateForDB()."', password='".md5($password)."', token='".$token."', password2='needsreset_".substr(md5($password),0,32)."' WHERE userid=".$luser['userid'].";";
         $dblink->update($query);
         $subject = "Forgotten password";
         $toname = $luser['fname']." ".$luser['lname'];
         if($luser['fname']==NULL && $luser['lname']==NULL) $toname = $luser['email'];
         if($title==NULL) $title = getDefaultTitle();
         $content = "Hello.\nYou have recently requested to have your (".$toname.") password reset on ".$title.".\nYour new temporary password is: \n".$password;
         $content .= "\n\nPlease log in and change this temporary password as soon as possible for security purposes.\n\nThank you!\n";
         if($url==NULL) $url = $GLOBALS['baseURL'];
         $content .= $url;
         $ss = new Version();
         if($from==NULL) $from = $ss->getValue("WebsiteContact");
         $scheduler = new Scheduler();
         $scheduler->addSchedEmail(NULL,NULL,$content,$subject,5,$luser['userid'],$from,1,TRUE);

         $jsftrack1 = "Send forgotton email: ".$luser['userid'];
         $this->track("sendForgottenEmail",$jsftrack1,"Update Account",$userid);

         return True;
      } else {
         if($testing==1) print "<br>\nUser not retreived from DB: ".$email.".";
         
         return False;
      }
    }

   function remoteResetPassword($email) {
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($email)));
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=resetpassword&email=".$email;
         $url = str_replace(" ","%20",$url);
         //print "URL: ".$url."<BR>\n";
         $result = $reader->Parse($url);
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FAIL")) return FALSE;
         else return TRUE;
      } else {
         return FALSE;
      }
   }

    function userAuthenticate($authKey,$usertype=NULL) {
      $dblink = new MYSQLaccess;
      if ($usertype==NULL) $usertype="user";
      $email     = convertString(strtolower(trim($authKey['email'])));
      $username  = convertString(strtolower(trim($authKey['username'])));
      $password  = trim($authKey['password']);
      if (($email==NULL && $username==NULL) || $password==NULL || 0==strcmp($password,"")) return FALSE;
      $query = "";
      if ($username!=NULL) $query = "SELECT * FROM useracct WHERE alive=1 AND (LOWER(username)='".$username."' OR email='".$username."') AND (password='".md5($password)."' OR password2='".md5($password)."')";
      else if ($email!=NULL) $query = "SELECT * FROM useracct WHERE alive=1 AND email='".$email."' AND (password='".md5($password)."' OR password2='".md5($password)."')";
      else return FALSE;

      $ss = new Version();
      if ($ss->getValue("multisitesuseremails")==1) {
         $ctx = new Context();
         $query .= " AND ".$ctx->getSiteSQL();
      }

      $results = $dblink->queryGetResults($query);
      //print "\n<!-- query: ".$query." -->\n";
      if ($results==NULL || count($results)<1) {
         return $this->checkRemoteAuthenticate($authKey);
      } else {
         if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
            $reader = new ATOMReader();
            $url = $GLOBALS['remotejsfauthenticationurl'];
            $url .= "jsfcode/atomcontroller.php?action=".urlencode(encrypt($GLOBALS['masterkey'],"logintoken"));
            $url .= "&email=".urlencode($email);
            //print "\n<!-- getuser refresh url: ".$url." -->\n";
            $result = $reader->Parse($url);
            //print "\n<!-- getuser refresh return:\n";
            //print_r($result);
            //print "\n-->\n";
            
            $stuff = $result['feed']['entry'];
            //print "\n<!-- getuser refresh return:\n";
            //print_r($stuff);
            //print "\n-->\n";

            if (0==strcmp($stuff['title'],"token")) {
               $token = $stuff['content'];
               if ($token!=NULL) $this->updateField($results[0]['userid'],"token",$token);
            }
         }
         return TRUE;
      }
    }

   function checkRemoteAuthenticate($authKey){
      if ($GLOBALS['remotejsfauthenticationurl']!=NULL) {
         $email     = convertString(strtolower(trim($authKey['email'])));
         $username  = convertString(strtolower(trim($authKey['username'])));
         $password  = $authKey['password'];
         $reader = new ATOMReader();
         $url = $GLOBALS['remotejsfauthenticationurl']."jsfcode/atomcontroller.php?action=validatelogin&email=".$email."&username=".$username."&enc_password=".encrypt($GLOBALS['masterkey'],$password);
         $url = str_replace(" ","%20",$url);
         //print "URL: ".$url."<BR>\n";
         $result = $reader->Parse($url);
         $stuff = $result['feed']['entry'];
         if (0==strcmp($stuff['content'],"FAIL")) return FALSE;
         else {
            $user = array();
            for ($i=0; $i<count($stuff); $i++) $user[$stuff[$i]['title']]=$stuff[$i]['content'];
            if ($username!=NULL) $localuser = $this->getUserByUsername($username);
            else $localuser = $this->getUserByEmail($email);
            if ($localuser != NULL) {
               $this->modifyUserExplicit($localuser['userid'],$user['email'], $user['fname'], $user['lname'], $user['age'], $user['gender'], $user['marital'], $user['edu'], $user['nletter'], $user['phonenum'], $user['phonenum2'], $user['phonenum3'], $user['phonenum4'], $user['addr1'], $user['addr2'], $user['city'], $user['state'], $user['zip'], $user['usertype'], $user['company'], $user['website'], $user['alive'], $user['country'], $user['title'], $user['parentid'], $user['parentid2'], $password, $GLOBALS['remotejsfauthenticationurl'], $user['siteid'], $user['email2'], $user['username'], $user['field1'], $user['field2'], $user['field3'], $user['field4'], $user['field5'], $user['field6'], $user['token']);
            } else {
               $this->addAccount($user['email'], NULL, $user['fname'], $user['lname'], $user['phonenum'], $user['phonenum2'], $user['phonenum3'], $user['phonenum4'], $user['addr1'], $user['addr2'], $user['city'], $user['state'], $user['zip'], $user['age'], $user['gender'], $user['marital'], $user['edu'], $user['nletter'], TRUE, NULL, $user['notes'], $user['usertype'], $user['company'], $user['parentid'], $user['website'], $user['parentid2'], $user['alive'], $user['refsrc'], $user['country'], $user['title'], FALSE, $password, $GLOBALS['remotejsfauthenticationurl'], $user['siteid'], $user['email2'], $user['username'], $user['field1'], $user['field2'], $user['field3'], $user['field4'], $user['field5'], $user['field6'], NULL, $user['token']);
            }
            return TRUE;
         }
      } else {
         return FALSE;
      }
   }

    function addUserToSession($userInfo) {
       $_SESSION["secure"]=1;
       $_SESSION['s_user']['emailAddress'] = $userInfo['email'];
       
       foreach($userInfo as $key => $value) {
         $_SESSION['s_user'][$key]=$value;
      }      
    }

   function logout() {
      //if(isLoggedOn()) print "\n<!-- entering logout() function logged in.  userid: ".isLoggedOn()." -->\n";
      
      $_SESSION['s_user']['emailAddress'] = NULL;
      $_SESSION['s_user']['userid'] = NULL;
      unset($_SESSION['s_user']['emailAddress']);
      unset($_SESSION['s_user']['userid']);
      unset($_SESSION['s_user']);
      unset($_SESSION["secure"]);
      //setcookie("autologin","",0,'/',getCookieDomain());
      //setcookie("autologin","",0,'/');
      setcookie("userid_t","",0,'/',getCookieDomain());
      setcookie("userid_t","",0,'/');
      session_unset();
      //unset($_COOKIE['PHPSESSID']);
      //unset($_COOKIE['autologin']);
      unset($_COOKIE['PHPSESSID']);
      unset($_COOKIE['userid_t']);
      
      //print "\n<!-- ***chj*** logged out -->\n";
      //if(isLoggedOn()) print "\n<!-- Error occurred.  userid: ".isLoggedOn()." -->\n";
   }

   function checkAutoLoginCookie() {
      //print "\n<!-- checkAutoLoginCookie() -->\n";
      $userid = NULL;
      $pagejs = "";
      //if (!isLoggedOn() && isset($_COOKIE["autologin"])) {
      if (!isLoggedOn() && isset($_COOKIE["userid_t"])) {
         //print "\n\n<!-- Userid: ".$_COOKIE["userid_t"]."-->\n";

         $userid = $_COOKIE["userid_t"];
         if ($userid != null && $userid>0) {
            $user = $this->getUser($userid);
            //print "\n<!-- user: ";
            //print_r($user);
            //print " -->\n";
            $this->addUserToSession($user);
            //print "\n<!-- user: ";
            //print_r($_SESSION);
            //print " -->\n";
         }
      } else {
         //print "\n<!-- user is logged on -->\n";
         $userid = isLoggedOn();        
      }

      if (!isset($_COOKIE["userid_t"]) && is_numeric($userid) && $userid>0) {
         $pagejs = "\n<script>\n";
         $pagejs .= "var jsfadmin_d = new Date();\n";
         $pagejs .= "jsfadmin_d.setTime(jsfadmin_d.getTime() + (365 * 24 * 60 * 60 * 1000));\n";
         $pagejs .= "var jsfadmin_expires = 'expires=' + jsfadmin_d.toUTCString();\n";
         $pagejs .= "document.cookie = 'userid_t=".$userid.";' + jsfadmin_expires + ';path=/; domain=".getCookieDomain()."';\n";         
         $pagejs .= "</script>\n";
         setcookie("userid_t",$userid, time()+60*60*24*365,'/',getCookieDomain(),TRUE,TRUE);
         //print "\n<!-- Setting userid cookie: ".$userid." -->\n";
      }
      
      return $pagejs;
   }

   function sendEmailTo($userid,$subj,$contents,$fromuser,$contenttype=5,$toemail=NULL,$debug=FALSE){
      //print "\n<!-- sendEmailTo - userid: ".$userid." subj: ".$subj." contents: ".$contents." -->\n";
      //print "<br>".date("Y-m-d H:i:s")." 1<BR>";
      $template = new Template();
      $emailtext = $contents;
      $subjecttext = $subj;

      $touser = array();
      if ($userid>0) {
         $touser = $this->getFullUserInfo($userid);
         $emailtext = $this->doSubstitutions($emailtext,$touser);
         $subjecttext = $this->doSubstitutions($subjecttext,$touser);
         if(strpos($touser['email'],"_dummy@facebook.com")!==FALSE) {
            $touser['email'] = $touser['field2'];
         }
         if($debug) print "<br>\nuser found: ".$tuuser['userid'].": ".$touser['email']." nletter: ".$touser['nletter']."<br>\n";

      } else {
         $touser['email'] = convertBack($toemail);
      }

      if (strpos($touser['email'],"dummy")===FALSE && 0!=strcmp($touser['nletter'],"unsubscribe")) {
         if($debug) print "<br>\nemail validated, ok to send<br>\n";

         $emailtext = $template->doSubstitutions($emailtext);
         $subjecttext = convertBack($template->doSubstitutions($subjecttext));
   
         if ($GLOBALS['usesmtp']==1 && class_exists("PHPMailer")) {
            if($debug) print "<br>\nin smtp block<br>\n";
            $fromuserobj = $this->getUserByEmail($fromuser);
            $fromuserobj = $this->getFullUserInfo($fromuserobj['userid']);
            if ($contenttype==5) $emailtext = convertString($emailtext);
            //print "<br>".date("Y-m-d H:i:s")." 10<BR>";
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Host = $GLOBALS['smtphost'];
            //$mail->SMTPDebug  = 1;
            $mail->SMTPAuth   = true;
            $mail->Port       = $GLOBALS['smtpport'];
   
            $smtpuser = $GLOBALS['smtpuser'];
            $smtppassword = $GLOBALS['smtppassword'];
            $smtpemail = $GLOBALS['smtpemail'];
            $smtpname = $GLOBALS['smtpname'];
            if ($fromuserobj['smtppassword'] != NULL && $fromuserobj['smtpuser'] != NULL) {
               $smtpuser = $fromuserobj['smtpuser'];
               $smtppassword = $fromuserobj['smtppassword'];
               $smtpemail = $fromuserobj['email'];
               $smtpname = $fromuserobj['fname']." ".$fromuserobj['lname'];
            }
            //print "<!-- mailing to: ".$smtpuser.", ".$smtppassword.", ".$smtpemail.", ".$smtpname." -->\n";
   
            $mail->Username   = $smtpuser;
            $mail->Password   = $smtppassword;
            $mail->SetFrom($smtpemail, $smtpname);
            $mail->AddReplyTo($smtpemail,$smtpname);
   
            $mail->Subject    = $subjecttext;
            $mail->AltBody    = strip_tags(convertBack($emailtext));
            $mail->MsgHTML($emailtext);
            if (0!=strcmp(trim($touser['fname']),"") && 0!=strcmp(trim($touser['lname']),"")) {
               $mail->AddAddress($touser['email'], $touser['fname']." ".$touser['lname']);
            } else {
               $mail->AddAddress($touser['email']);
            }
            $mail->Send();
         } else {
            if($debug) print "<br>\nin regular email block<br>\n";

            $headers = getEmailHeaders($fromuser);
            if ($contenttype==6) $headers = getHTMLEmailHeaders($fromuser);
            if (0==strcmp(substr($touser['email'],-7),".rr.com")) $headers = str_replace("\r\n","\n",$headers);
            else if (0==strcmp(strtolower(substr($touser['email'],-9)),"cisco.com")) $headers = str_replace("\r\n","\n",$headers);
            else if (0==strcmp(strtolower(substr($touser['email'],-13)),"itxchange.com")) $headers = str_replace("\r\n","\n",$headers);
            else if (0==strcmp(strtolower(substr($touser['email'],-17)),"scottdeweycpa.com")) $headers = str_replace("\r\n","\n",$headers);
            else if (0==strcmp(strtolower(substr($touser['email'],-7)),"unc.edu")) $headers = str_replace("\r\n","\n",$headers);
            else if (0==strcmp(strtolower(substr($touser['email'],-7)),"dexone.com")) $headers = str_replace("\r\n","\n",$headers);
            $return_path = $fromuser;
            //if ($contenttype==6) {
            //   $headers = getMultipartHeaders($fromuser,$emailtext);
            //   $emailtext = "";
            //}
            mail($touser['email'], $subjecttext, $emailtext, $headers, "-f ".$return_path);
            //print "mail(".$touser['email'].", ".$subjecttext.", ".$emailtext.", ".$headers.");";
         }
         return $touser['email'];
      } else {
         if($debug) print "<br>\nemail was not validated, not able to send<br>\n";
         return FALSE;
      }
   }

   function printUserForm($userid=NULL,$exceptions=NULL,$renames=NULL,$prefix=NULL,$across=1){
      $user = array();
      $str = "";
      $js = "";
      $js .= "var temp;\n";
      $js .= "var temp2;\n";
      $js .= "var err=false;\n";
      $js .= "var errstr='';\n";
      if ($userid != NULL) {
         $user = $this->getUser($userid);
         $str .= "<input type=\"hidden\" name=\"".$prefix."updateuserid\" id=\"".$prefix."updateuserid\" value=\"".$user['userid']."\">\n";
      } else {
         $str .= "<input type=\"hidden\" name=\"".$prefix."newuserid\" id=\"".$prefix."newuserid\" value=\"1\">\n";
         $user['email'] = getParameter("email");
         $user['fname'] = getParameter("fname");
         $user['lname'] = getParameter("lname");
         $user['company'] = getParameter("company");
         $user['title'] = getParameter("title");
         $user['website'] = getParameter("website");
         $user['addr1'] = getParameter("addr1");
         $user['addr2'] = getParameter("addr2");
         $user['city'] = getParameter("city");
         $user['state'] = getParameter("state");
         $user['zip'] = getParameter("zip");
         $user['country'] = getParameter("country");
         $user['phonenum'] = getParameter("phonenum");
         $user['phonenum2'] = getParameter("phonenum2");
         $user['phonenum3'] = getParameter("phonenum3");
      }
      $str .= "<div style=\"position:relative;clear:both;\">\n";
      //$str .= "<table cellpadding=\"2\" cellspacing=\"0\">\n";
      $countFields = 0;

      if (!in_array("email",$exceptions)) {
         if($renames['email']==NULL) $renames['email']="Email";
         //$str .= "<tr><td class=\"label\">".$renames['email']."</td><td><input type=\"text\" name=\"".$prefix."email\" id=\"".$prefix."profileemail\" size=\"25\" value=\"".$user['email']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['email']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."email\" id=\"".$prefix."profileemail\" style=\"width:210px;\" value=\"".$user['email']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
         $js .= "if (!err) {\n";
         $js .= "temp=document.getElementById('profileemail').value;\n";
         $js .= "if (!Boolean(temp)) {\n";
         $js .= "err = true;\n";
         $js .= "errstr = 'Please enter your email address before continuing.';\n";
         $js .= "document.getElementById('profileemail').focus();\n";
         $js .= "}\n";
         $js .= "}\n";
			 if (!in_array("cemail",$exceptions)) {
				 if($renames['cemail']==NULL) $renames['cemail']="Confirm Email";
				 //$str .= "<tr><td class=\"label\">".$renames['cemail']."</td><td><input type=\"text\" name=\"".$prefix."cemail\" id=\"".$prefix."profilecemail\" size=\"25\" value=\"".getParameter("cemail")."\"></td></tr>";
				 $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['cemail']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."cemail\" id=\"".$prefix."profilecemail\" style=\"width:210px;\" value=\"\"></div>";
				 $countFields++;
				 if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
				 				 
				 $js .= "if (!err) {\n";
				 $js .= "temp=document.getElementById('profileemail').value;\n";
				 $js .= "temp2=document.getElementById('profilecemail').value;\n";
				 $js .= "if (temp!=temp2) {\n";
				 $js .= "err = true;\n";
				 $js .= "errstr = 'Your email addresses do not match, please try again.';\n";
				 $js .= "document.getElementById('profilecemail').focus();\n";
				 $js .= "}\n";
				 $js .= "}\n";
			 }
      }
      if (!in_array("fname",$exceptions)) {
         if($renames['fname']==NULL) $renames['fname']="First Name";
         //$str .= "<tr><td class=\"label\">".$renames['fname']."</td><td><input type=\"text\" name=\"".$prefix."fname\" id=\"".$prefix."profilefname\" size=\"25\" value=\"".$user['fname']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\" id=\"".$prefix."fname_div1\">".$renames['fname']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\" id=\"".$prefix."fname_div2\"><input type=\"text\" name=\"".$prefix."fname\" id=\"".$prefix."profilefname\" style=\"width:210px;\" value=\"".$user['fname']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
         
         $js .= "if (!err) {\n";
         $js .= "temp=document.getElementById('profilefname').value;\n";
         $js .= "if (!Boolean(temp)) {\n";
         $js .= "err = true;\n";
         $js .= "errstr = 'Please enter your first name before continuing.';\n";
         $js .= "document.getElementById('profilefname').focus();\n";
         $js .= "}\n";
         $js .= "}\n";
      }
      if (!in_array("lname",$exceptions)) {
         if($renames['lname']==NULL) $renames['lname']="Last Name";
         //$str .= "<tr><td class=\"label\">".$renames['lname']."</td><td><input type=\"text\" name=\"".$prefix."lname\" id=\"".$prefix."profilelname\" size=\"25\" value=\"".$user['lname']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\" id=\"".$prefix."lname_div1\">".$renames['lname']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\" id=\"".$prefix."lname_div2\"><input type=\"text\" name=\"".$prefix."lname\" id=\"".$prefix."profilelname\" style=\"width:210px;\" value=\"".$user['lname']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("company",$exceptions)) {
         if($renames['company']==NULL) $renames['company']="Organization";
         //$str .= "<tr><td class=\"label\">".$renames['company']."</td><td><input type=\"text\" name=\"".$prefix."company\" id=\"".$prefix."profilecompany\" size=\"25\" value=\"".$user['company']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['company']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."company\" id=\"".$prefix."profilecompany\" style=\"width:210px;\" value=\"".$user['company']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("title",$exceptions)) {
         if($renames['title']==NULL) $renames['title']="Title";
         //$str .= "<tr><td class=\"label\">".$renames['title']."</td><td><input type=\"text\" name=\"".$prefix."title\" id=\"".$prefix."profiletitle\" size=\"25\" value=\"".$user['title']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['title']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."title\" id=\"".$prefix."profiletitle\" style=\"width:210px;\" value=\"".$user['title']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("addr1",$exceptions)) {
         if($renames['addr1']==NULL) $renames['addr1']="Address";
         //$str .= "<tr><td class=\"label\">".$renames['addr1']."</td><td><input type=\"text\" name=\"".$prefix."addr1\" id=\"".$prefix."profileaddr1\" size=\"25\" value=\"".$user['addr1']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['addr1']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."addr1\" id=\"".$prefix."profileaddr1\" style=\"width:210px;\" value=\"".$user['addr1']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("addr2",$exceptions)) {
         if($renames['addr2']==NULL) $renames['addr2']="Address 2";
         //$str .= "<tr><td></td><td><input type=\"text\" name=\"".$prefix."addr2\" id=\"".$prefix."profileaddr2\" size=\"25\" value=\"".$user['addr2']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['addr2']." </div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."addr2\" id=\"".$prefix."profileaddr2\" style=\"width:210px;\" value=\"".$user['addr2']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("city",$exceptions)) {
         if($renames['city']==NULL) $renames['city']="City";
         //$str .= "<tr><td class=\"label\">".$renames['city']."</td><td><input type=\"text\" name=\"".$prefix."city\" id=\"".$prefix."profilecity\" size=\"25\" value=\"".$user['city']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['city']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."city\" id=\"".$prefix."profilecity\" style=\"width:210px;\" value=\"".$user['city']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("state",$exceptions)) {
         //$str .= "<tr><td class=\"label\">State</td><td class=\"label\">".listStates($user['state'],$prefix."state",FALSE,"id=\"".$prefix."profilestate\"")." Zip Code <input type=\"text\" name=\"".$prefix."zip\" id=\"".$prefix."profilezip\" id=\"profilezip\" size=\"10\" value=\"".$user['zip']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">State</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".listStates($user['state'],$prefix."state",FALSE,"id=\"".$prefix."profilestate\"")." Zip Code <input type=\"text\" name=\"".$prefix."zip\" id=\"".$prefix."profilezip\" id=\"profilezip\" style=\"width:90px;\" value=\"".$user['zip']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
         $js .= "if (!err) {\n";
         $js .= "temp=document.getElementById('profilezip').value;\n";
         $js .= "if (!Boolean(temp)) {\n";
         $js .= "err = true;\n";
         $js .= "errstr = 'Please enter your zip code before continuing.';\n";
         $js .= "document.getElementById('profilezip').focus();\n";
         $js .= "}\n";
         $js .= "}\n";
      }
      if (!in_array("country",$exceptions)) {
         //$str .= "<tr><td class=\"label\">Country</td><td class=\"label\">".listCountries($user['country'],$prefix."country",TRUE,"id=\"".$prefix."profilecountry\"")."</td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">Country</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".listCountries($user['country'],$prefix."country",TRUE,"id=\"".$prefix."profilecountry\"")."</div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("website",$exceptions)) {
         
         $website = $user['website'];
         if($website!=NULL && strlen($website)>5 && 0!=strcmp(substr(strtolower($website),0,4),"http")) {
            $webiste = "http://".$website;
         }
         
         if($renames['website']==NULL) $renames['website']="Website";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['website']."</div>";
         $str .= "<div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">";
         $str .= "<input type=\"text\" name=\"".$prefix."website\" id=\"".$prefix."profilewebsite\" style=\"width:180px;\" value=\"".$website."\">";
         if($website!=NULL && strlen($website)>12) {
            $str .= "<span onclick=\"window.open('".$website."');\" style=\"margin-left:5px;font-size:8px;font-family:arial;padding:4px;background-color:#F2F2F2;border:1px solid #888888;border-radius:3px;cursor:pointer;\">Go</span>";
         }                  
         $str .= "</div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("phonenum",$exceptions)) {
         if($renames['phonenum']==NULL) $renames['phonenum']="Phone Number";
         //$str .= "<tr><td class=\"label\">".$renames['phonenum']."</td><td><input type=\"text\" name=\"".$prefix."phonenum\" id=\"".$prefix."profilephonenum\" size=\"25\" value=\"".$user['phonenum']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['phonenum']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."phonenum\" id=\"".$prefix."profilephonenum\" style=\"width:210px;\" value=\"".$user['phonenum']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("phonenum2",$exceptions)) {
         if($renames['phonenum2']==NULL) $renames['phonenum2']="Fax";
         //$str .= "<tr><td class=\"label\">".$renames['phonenum2']."</td><td><input type=\"text\" name=\"".$prefix."phonenum2\" id=\"".$prefix."profilephonenum2\" size=\"25\" value=\"".$user['phonenum2']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['phonenum2']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."phonenum2\" id=\"".$prefix."profilephonenum2\" style=\"width:210px;\" value=\"".$user['phonenum2']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }
      if (!in_array("phonenum3",$exceptions)) {
         if($renames['phonenum3']==NULL) $renames['phonenum3']="Alternate Phone";
         //$str .= "<tr><td class=\"label\">".$renames['phonenum3']."</td><td><input type=\"text\" name=\"".$prefix."phonenum3\" id=\"".$prefix."profilephonenum3\" size=\"25\" value=\"".$user['phonenum3']."\"></td></tr>";
         $str .= "<div style=\"width:135px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\">".$renames['phonenum3']."</div><div style=\"width:235px;float:left;font-size:12px;font-family:verdana;margin-right:8px;margin-bottom:5px;\"><input type=\"text\" name=\"".$prefix."phonenum3\" id=\"".$prefix."profilephonenum3\" style=\"width:210px;\" value=\"".$user['phonenum3']."\"></div>";
         $countFields++;
         if ($across==NULL || $across==1 || ($across==2 && (($countFields%2)==0))) $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      }

      //$str .= "</table>\n";
      $str .= "<div style=\"width:1px;height:1px;clear:both;\"></div>";
      $str .= "</div>\n";

      
      $str .= "<script type=\"text/javascript\">\n";
      $str .= "function validateUserProfileFields() {\n";
      $str .= $js;
      $str .= "if(err) {\nalert(errstr);\nreturn false;\n} else {\nreturn true;\n}\n";
      $str .= "}\n";
      $str .= "</script>\n";
      return $str;
   }

   function printUserProperties($userid=NULL, $printWDFields=FALSE, $usertype=NULL, $printJS=FALSE){
      $user = NULL;
      if ($userid != NULL && $userid>0) $user = $this->getUser($userid);
      if ($user['usertype']==NULL) $user['usertype'] = $usertype;

      $wdObj = new WebSiteData();
      $webdata = $wdObj->getWebDataByName($user['usertype']." Properties");
      if ($webdata != NULL) {
         print "<input type=\"hidden\" name=\"prop_wd_id\" value=\"".$webdata['wd_id']."\">\n";
         if ($userid!=NULL) {
            $rows = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
            print "<input type=\"hidden\" name=\"prop_wd_row_id\" value=\"".$rows[0]['wd_row_id']."\">\n";
         }
         if ($printWDFields) {
            print "<input type=\"hidden\" name=\"wd_id\" value=\"".$webdata['wd_id']."\">\n";
            if ($userid!=NULL) print "<input type=\"hidden\" name=\"wd_row_id\" value=\"".$rows[0]['wd_row_id']."\">\n";
         }
         if ($printJS) {
            //$wdObj->printWebData($webdata['wd_id'],NULL,isLoggedOn(),$rows[0]['wd_row_id'],NULL, "ShortWithCaptcha",NULL,NULL,FALSE);
            $wdObj->printWebData($webdata['wd_id'],NULL,isLoggedOn(),$rows[0]['wd_row_id'],NULL, "ShortWithCaptcha",NULL,NULL,FALSE);
         } else {
            $wdObj->printWebDataSection($webdata['wd_id'], -1, $rows[0],$this->isUserAdmin(isLoggedOn()),FALSE,FALSE,NULL,NULL,NULL,FALSE);
         }
      }
   }
   
   function printusercomment($str,$userid){
      $user = $this->getUser($userid);
      print "\n\n<!-- ".$str." user ".$userid.": \n";
      if($user!=NULL){
         print "   userid: ".$user['userid']."\n";
         print "   dbmode: ".$user['dbmode']."\n";
         print "   fname: ".$user['fname']."\n";         
         print "   lname: ".$user['lname']."\n";         
         print "   company: ".$user['company']."\n";         
         print "   email: ".$user['email']."\n";         
      }
      print "\n-->\n\n";
   }

   function getValidationJS(){
      $javascript = "";
      $javascript .= "      if (fname.value==null || fname.value==\"\") {                                                                                       \n";
      $javascript .= "         fname.className='errorinput';                                                                                                    \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please enter your first name.';                                            \n";
      $javascript .= "         fname.focus();return false;                                                                                                      \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         fname.className='';                                                                                                              \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (lname.value==null || lname.value==\"\") {                                                                                       \n";
      $javascript .= "         lname.className='errorinput';                                                                                                    \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please enter your last name.';                                             \n";
      $javascript .= "         lname.focus();return false;                                                                                                      \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         lname.className='';                                                                                                              \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "                                                                                                                                          \n";
      $javascript .= "      if (lname.value.length<2) {                                                                                                         \n";
      $javascript .= "         lname.className='errorinput';                                                                                                    \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please enter your last name.';                                             \n";
      $javascript .= "         lname.focus();return false;                                                                                                      \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         lname.className='';                                                                                                              \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "                                                                                                                                          \n";
      $javascript .= "      if (email.value==null || email.value==\"\") {                                                                                       \n";
      $javascript .= "         email.className='errorinput';                                                                                                    \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please enter your email address.';                                         \n";
      $javascript .= "         email.focus();return false;                                                                                                      \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         email.className='';                                                                                                              \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (cemail.value==null || cemail.value==\"\") {                                                                                     \n";
      $javascript .= "         cemail.className='errorinput';                                                                                                   \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please confirm your email address.';                                       \n";
      $javascript .= "         cemail.focus();return false;                                                                                                     \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         cemail.className='';                                                                                                             \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if ( cemail.value != email.value ) {                                                                                                \n";
      $javascript .= "         email.className='errorinput';                                                                                                    \n";
      $javascript .= "         cemail.className='errorinput';                                                                                                   \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Your email addresses do not match, please verify your entry.';             \n";
      $javascript .= "         email.focus();return false;                                                                                                      \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         email.className='';                                                                                                              \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (password.value==null || password.value==\"\") {                                                                                 \n";
      $javascript .= "         password.className='errorinput';                                                                                                 \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please enter a password.';                                                 \n";
      $javascript .= "         password.focus();return false;                                                                                                   \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         password.className='';                                                                                                           \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (password.value.length<6) {                                                                                                      \n";
      $javascript .= "         password.className='errorinput';                                                                                                 \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please create a password of at least 6 characters in length.';             \n";
      $javascript .= "         password.focus();return false;                                                                                                   \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         password.className='';                                                                                                           \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (cpassword.value==null || cpassword.value==\"\") {                                                                               \n";
      $javascript .= "         password.className='errorinput';                                                                                                 \n";
      $javascript .= "         cpassword.className='errorinput';                                                                                                \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please confirm your password.';                                            \n";
      $javascript .= "         cpassword.focus();return false;                                                                                                  \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         cpassword.className='';                                                                                                          \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if ( password.value != cpassword.value ) {                                                                                          \n";
      $javascript .= "         password.className='errorinput';                                                                                                 \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Your passwords do not match, please retry.';                               \n";
      $javascript .= "         password.focus();return false;                                                                                                   \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         password.className='';                                                                                                           \n";
      $javascript .= "      }                                                                                                                                   \n";
      $javascript .= "      if (agree.checked) {                                                                                                                \n";
      $javascript .= "         agree.className='';                                                                                                              \n";
      $javascript .= "      } else {                                                                                                                            \n";
      $javascript .= "         agree.className='errorinput';                                                                                                    \n";
      $javascript .= "         document.getElementById('errormessages').innerHTML = 'Please accept our terms of use and privacy policy to create your account.';\n";
      $javascript .= "         agree.focus();return false;                                                                                                      \n";
      $javascript .= "      }                                                                                                                                   \n";
      return $javascript;
   }








   function promoteManyAccounts($useridarray,$ignoreusers=NULL) {
      //$_SESSION['showdebug'] = TRUE;
      if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts start method -->\n";

   	 $sql = new MYSQLaccess();
   	 $wdObj = new WebsiteData();

   	 if($ignoreusers==NULL) $ignoreusers = array();
   	 
   	 //Make sure the data structure is an array
   	 if (!is_array($useridarray)) {
   	 	$userid = $useridarray;
   	   $useridarray = array();
   	   $useridarray[] = $userid;
   	 }
   	 
   	 //Make sure we convert comma-separated elements
   	 $temp = $useridarray;
 	    $useridarray = array();
 	    for ($i=0;$i<count($temp);$i++){
 	       $temparr = separateStringBy($temp[$i],",",NULL,TRUE);
 	       for ($j=0;$j<count($temparr);$j++) {
 	          $useridarray[] = $temparr[$j];
 	       }
 	    }
 	    
      if($_SESSION['showdebug']) {
         print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts userids:\n";
         print_r($useridarray);
         print "\n-->\n";
      }

      $rnotes = "Promote accounts: ".implode(",",$useridarray);
      $this->track("promoteManyAccounts",$rnotes,"PromoteManyAccounts");

      
 	    if($useridarray==NULL || count($useridarray)<1) {
 	       // do nothing.
 	    } else if(count($useridarray)>50) {
 	       if($_SESSION['showdebug']) print "<br>\nCreating promote user job.<br>\n";
 	       $pl = new PromoteUserList();
 	       $pl->addJob($useridarray,$ignoreusers); 	       
 	    } else {
 	       if($_SESSION['showdebug']) print "<!--\nPromoting users.\n-->\n";
 	       
          $results = array();
          $instmnt = "";
          $query2 = "";
          $query_new = "";
          $query_old = "";
          
          // get results if there are any userids to look up
          // also get their old values - so we know whether or not
          // to retreive the new location coordinates
          $query1 = "SELECT userid,addr1,addr2,city,state,zip ";
          if (count($useridarray)==1) {
             //$query = "SELECT userid FROM useracct WHERE userid=".$useridarray[0]." AND dbmode<>'APPROVED';";
             $query2 = "WHERE userid=".$useridarray[0].";";
          } else if (count($useridarray)>1) {
             $instmnt = implode(", ",$useridarray);   	 
             //$query = "SELECT userid FROM useracct WHERE userid IN (".$instmnt.") AND dbmode<>'APPROVED';";
             $query2 = "WHERE userid IN (".$instmnt.");";
          }
          $query_new = $query1."FROM useracct ".$query2;
          $query_old = $query1."FROM useracct_pub ".$query2;
          $results = $sql->queryGetResults($query_new);
          $results_former = $sql->queryGetResults($query_old);
          
          // If the address 1 field has changed, force a geo lookup
          // Must do this before copying updated info over to public
          $pastaddr = array();
          for($i=0;$i<count($results_former);$i++){
             $pastaddr[$results_former[$i]['userid']] = $results_former[$i];
          }
          for($i=0;$i<count($results);$i++){
             $caddr1 = strtolower(trim($results[$i]['addr1']));
             $caddr2 = strtolower(trim($pastaddr[$results[$i]['userid']]['addr1']));
             if(0!=strcmp($caddr1,$caddr2)) {
                $this->getUserGeoCode($results[$i]['userid'],FALSE,NULL,TRUE);
             }
          }
          
         if($_SESSION['showdebug']) {
            print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts query: ".$query."\nresults:\n";
            print_r($results);
            print "\n-->\n";
         }
          
          $instmnt = "";
          $newuseridarray = array();
          for ($i=0;$i<count($results);$i++){
              if ($i>0) $instmnt .= ", ";
              $instmnt .= $results[$i]['userid'];
              $newuseridarray[] = $results[$i]['userid'];
              $ignoreusers[] = $results[$i]['userid'];
          }
          
          if ($newuseridarray!=NULL && count($newuseridarray)>0 && strlen(trim($instmnt))>0) {
              if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts users: (".$instmnt.") -->\n";
   
              $query = "DELETE FROM useracct_pub WHERE userid IN (".$instmnt.");";
              $sql->delete($query);
              
              $query = "UPDATE useracct SET dbmode='APPROVED' WHERE userid IN (".$instmnt.");";
              $sql->update($query);
              
              $query = "SELECT * FROM useracct WHERE userid IN (".$instmnt.");";
              $results = $sql->queryGetResults($query);
              
              $query = copyRowsInsert($results,"useracct_pub");
              if ($query != NULL) $sql->insert($query);
              
              if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts user insert: ".$query." -->\n";
              
              $webdata_arr1 = $wdObj->getWebDataByFuzzyName("% Objects%");
              $webdata_arr2 = $wdObj->getWebDataByFuzzyName("% Properties");
              $webdata_arr = array_merge($webdata_arr1,$webdata_arr2);
              //print "\n\n<!-- ***chj JDATAs found:\n";
              //print_r($webdata_arr);
              //print "\n-->\n\n";
              if ($webdata_arr != NULL && count($webdata_arr)>0) {
                 for ($i=0; $i<count($webdata_arr); $i++) {
                    $query = "show tables like 'wd_".$webdata_arr[$i]['wd_id']."_pub';";
                    $results = $sql->queryGetResults($query);
                    if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser wd objects: ".$query." -->\n";
                    if ($results != NULL && count($results)>0) {
                       $query = "DELETE FROM wd_".$webdata_arr[$i]['wd_id']."_pub WHERE userid IN (".$instmnt.");";
                       $sql->delete($query);
                       
                       //$query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set dbmode='APPROVED' WHERE userid IN (".$instmnt.");"; 
                       //$sql->update($query);
                       
                        // new: first backup lastupdateby data, then empty it
                        $query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set lastupdateby2=lastupdateby, dbmode='APPROVED' WHERE userid IN (".$instmnt.");"; 
                        $sql->update($query);
                        
                        $query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set lastupdateby='' WHERE userid IN (".$instmnt.");"; 
                        $sql->update($query);
                       
                       
                       $query = "SELECT * FROM wd_".$webdata_arr[$i]['wd_id']." WHERE userid IN (".$instmnt.");";
                       $results = $sql->queryGetResults($query);
                       
                       if(count($results)>0) {
                        $query = "DELETE FROM wd_".$webdata_arr[$i]['wd_id']."_pub WHERE ";
                        for($j=0;$j<count($results);$j++) {
                           if($j>0) $query .= " OR ";
                           $query .= "wd_row_id=".$results[$j]['wd_row_id'];
                        }
                        $query .= ";";
                        $sql->delete($query);
                       }
								
                       if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts copying rows for wd_".$webdata_arr[$i]['wd_id']."_pub -->\n";
                       $query = copyRowsInsert($results,"wd_".$webdata_arr[$i]['wd_id']."_pub");
                       if ($query != NULL) $sql->insert($query);
                       if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts inserts: ".$query." -->\n";
                    }
                }
             }
                                
              //$query = "DELETE FROM userrel_pub WHERE userid IN (".$instmnt.") OR reluserid IN (".$instmnt.");";
              $query = "DELETE FROM userrel_pub WHERE userid IN (".$instmnt.");";
              $sql->delete($query);
              
              //$query = "SELECT * FROM userrel WHERE userid IN (".$instmnt.") OR reluserid IN (".$instmnt.");";
              $query = "SELECT * FROM userrel WHERE userid IN (".$instmnt.");";
              $results = $sql->queryGetResults($query);
              
              $query = copyRowsInsert($results,"userrel_pub");
              if ($query != NULL) $sql->insert($query);
              if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts copying rows for userrel_pub: ".$query." -->\n";

              
              $addluserids = array();
              // OLD: $ignoreusers = $useridarray;
              for ($i=0;$i<count($results);$i++){
                 $ignore = FALSE;
                 for ($j=0;$j<count($ignoreusers);$j++) {
                     if ($ignoreusers[$j]==$results[$i]['userid']) {
                         $ignore = TRUE;
                         break;
                     }
                 }
                 if (!$ignore) {
                     $addluserids[] = $results[$i]['userid'];
                     $ignoreusers[] = $results[$i]['userid'];
                 }
                 
                 $ignore = FALSE;
                 for ($j=0;$j<count($ignoreusers);$j++) {
                     if ($ignoreusers[$j]==$results[$i]['reluserid']) {
                         $ignore = TRUE;
                         break;
                     }
                 }
                 if (!$ignore) {
                     $addluserids[] = $results[$i]['reluserid'];
                     $ignoreusers[] = $results[$i]['reluserid'];
                 }
              }
              
              if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts promoting additional users (recursive) -->\n";
              if($_SESSION['showdebug']) {
                 print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts";
                 print "\npromoting additional users:\n";
                 print_r($addluserids);
                 print "\nignoring users:\n";
                 print_r($ignoreusers);
                 print "\n-->\n";
              }
              $this->promoteManyAccounts($addluserids,$ignoreusers);
              
              if (class_exists("CustomUserPromote")) {
                 if($_SESSION['showdebug']) {
                    print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts custom user promote start:\n";
                    print_r($newuseridarray);
                    print "\n-->\n";
                 }
                 $customObj = new CustomUserPromote();
                 $customObj->promoteManyAccounts($newuseridarray);
                 if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts custom user promote end -->\n";
              } else {
                 if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts custom user promote was not found -->\n";
              }
          }
      }
      if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts end method -->\n";		 
   }


      function promoteAccount($userid) {
         if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser userid: ".$userid." -->\n";
         
         $rnotes = "promoteAccount: ".$userid;
         $this->track("promoteAccount",$rnotes,"PromoteAccount",$userid);
         
         $dbi = new MYSQLAccess();
         $row = $this->getUser($userid);
         if ($row!=NULL) {
            if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser user found: ".$userid." -->\n";
            if (0==strcmp($row['dbmode'],"DELETED???")) {
               if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser user is DELETED -->\n";
               //$this->deleteUserAcct($userid);
            } else if ($row['dbmode']==NULL || 0==strcmp($row['dbmode'],"APPROVED") || 0==strcmp($row['dbmode'],"UPDATED") || 0==strcmp($row['dbmode'],"NEW") || 0==strcmp($row['dbmode'],"REJECTED") || 0==strcmp($row['dbmode'],"DELETED") || 0==strcmp($row['dbmode'],"DUP")) {
            	 
            	// update all useracct tables 
               if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser user is NEW or UPDATED -->\n";
               $query = "DELETE FROM useracct_pub WHERE userid=".$userid.";";
               $dbi->delete($query);

               $names = "dbmode, lastupdated";
               $values = "'APPROVED', NOW()";
               foreach($row as $name=>$val){
                  if (0!=strcmp($name,"dbmode") && 0!=strcmp($name,"lastupdated")) {
                     $names .= ", ".$name;
                     $values .= ", '".$val."'";
                  }
               }
               $query = "INSERT INTO useracct_pub (".$names.") VALUES (".$values.");";
               $dbi->update($query);
               if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser ins query: ".$query." -->\n";

               $query = "UPDATE useracct SET dbmode='APPROVED' WHERE userid=".$userid.";";
               $dbi->update($query);
               if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser query: ".$query." -->\n";
               
               
               //update and WD info for user
               $wdObj = new WebsiteData();
					$webdata_arr1 = $wdObj->getWebDataByFuzzyName("% Objects%");
					$webdata_arr2 = $wdObj->getWebDataByFuzzyName("% Properties");
					$webdata_arr = array_merge($webdata_arr1,$webdata_arr2);
					if ($webdata_arr != NULL && count($webdata_arr)>0) {
						for ($i=0; $i<count($webdata_arr); $i++) {
							$query = "show tables like 'wd_".$webdata_arr[$i]['wd_id']."_pub';";
							$results = $dbi->queryGetResults($query);
							if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteaccount wd objects: ".$query." -->\n";
							if ($results != NULL && count($results)>0) {
								$query = "DELETE FROM wd_".$webdata_arr[$i]['wd_id']."_pub WHERE userid=".$userid.";";
								$dbi->delete($query);
								//$query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set dbmode='APPROVED' WHERE userid=".$userid.";";
								// new: first backup lastupdateby data, then empty it
								$query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set lastupdateby2=lastupdateby, dbmode='APPROVED' WHERE userid=".$userid.";"; 
								$dbi->update($query);
								
								$query = "UPDATE wd_".$webdata_arr[$i]['wd_id']." set lastupdateby='' WHERE userid=".$userid.";"; 
								$dbi->update($query);
								
								$query = "SELECT * FROM wd_".$webdata_arr[$i]['wd_id']." WHERE userid=".$userid.";";
								$results = $dbi->queryGetResults($query);
								
								$query = "DELETE FROM wd_".$webdata_arr[$i]['wd_id']."_pub WHERE ";
								for($j=0;$j<count($results);$j++) {
								   if($j>0) $query .= " OR ";
								   $query .= "wd_row_id=".$results[$j]['wd_row_id'];
								}
								$query .= ";";
								$dbi->delete($query);
								
								$query = copyRowsInsert($results,"wd_".$webdata_arr[$i]['wd_id']."_pub");
								if ($query != NULL) $dbi->insert($query);
							}
					  }
				  }
				  
				   // Update all users related to this account
					$query = "DELETE FROM userrel_pub WHERE userid=".$userid." OR reluserid=".$userid.";";
					$dbi->delete($query);
					
					$query = "SELECT * FROM userrel WHERE userid=".$userid." OR reluserid=".$userid.";";
					$results = $dbi->queryGetResults($query);
					
					$query = copyRowsInsert($results,"userrel_pub");
					if ($query != NULL) $dbi->insert($query);
					
					$addluserids = array();
					$ignoreusers = array();
					$ignoreusers[] = $userid;
					for ($i=0;$i<count($results);$i++){
						$ignore = FALSE;
						for ($j=0;$j<count($ignoreusers);$j++) {
							 if ($ignoreusers[$j]==$results[$i]['userid']) {
								  $ignore = TRUE;
								  break;
							 }
						}
						if (!$ignore) {
							 $addluserids[] = $results[$i]['userid'];
							 $ignoreusers[] = $results[$i]['userid'];
						}
						
						$ignore = FALSE;
						for ($j=0;$j<count($ignoreusers);$j++) {
							 if ($ignoreusers[$j]==$results[$i]['reluserid']) {
								  $ignore = TRUE;
								  break;
							 }
						}
						if (!$ignore) {
							 $addluserids[] = $results[$i]['reluserid'];
							 $ignoreusers[] = $results[$i]['reluserid'];
						}
					}
					
					if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promotemanyaccounts promoting additional users (recursive) -->\n";
					$this->promoteManyAccounts($addluserids);

					
				   //Update custom properties
               if (class_exists("CustomUserPromote")) {
                  if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser custom user promote start -->\n";
                  $customObj = new CustomUserPromote();
                  $customObj->promoteAccount($userid);
                  if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." useracct:promoteuser custom user promote end -->\n";
               }

            } else {
               //print "\n<!-- useracct:promoteuser user is unknown: ".$row['dbmode']." -->\n";
            }
         } else {
            //print "\n<!-- useracct:promoteuser user not found! -->\n";
         }
      }
        
      function revertAccount($userid,$dbmode=NULL,$reason=NULL) {
         $rnotes = "Revert ".$userid." to ".$dbmode." because: ".$reason;
         $this->track("revertAccount",$rnotes,"Revert Account",$userid);

         $dbi = new MYSQLAccess();
         $originalrow = $this->getUser($userid);
         $row = $this->getUser($userid,FALSE,"useracct_pub");
         if ($row!=NULL) {
            if (($dbmode==NULL || 0==strcmp($dbmode,"REJECTED")) && 0==strcmp($originalrow['dbmode'],"APPROVED")) {
               $query = "UPDATE useracct SET dbmode='REJECTED'";
               if ($reason!=NULL) $query .= ", activatedstr='".convertString($reason)."'";

               // this is changing from approved to rejected, save the update
               $lastupdateby = isLoggedOn();
               if($lastupdateby==NULL) $lastupdateby = "0";
               $lastupdateby .= "-".date("Ymd")."-REJECTED";
               $query .= ", lastupdated='".getDateForDB()."'";
               $query .= ", lastupdatedby=SUBSTR(CONCAT('".$lastupdateby.",',IFNULL(lastupdatedby,' ')),1,2048)";
               
               $query .= " WHERE userid=".$userid.";";
               $dbi->update($query);
               $query = "delete from useracct_pub WHERE userid=".$userid.";";
               $dbi->delete($query);
               $query = "DELETE FROM userrel_pub WHERE userid=".$userid.";";
               $dbi->delete($query);
            } else if (($dbmode==NULL || 0==strcmp($dbmode,"DELETED")) && (0==strcmp($originalrow['dbmode'],"REJECTED") || 0==strcmp($originalrow['dbmode'],"DELETED") || 0==strcmp($originalrow['dbmode'],"DUP") || 0==strcmp($originalrow['dbmode'],"NEW"))) {
               $this->deleteUserAcct($userid);
            } else {
               if ($dbmode==NULL) $dbmode = $row['dbmode'];
               $names = "dbmode='".$dbmode."'";
               foreach($row as $name=>$val){
                  if (0!=strcmp($name,"dbmode") && 0!=strcmp($name,"activatedstr") && 0!=strcmp($name,"userid")) {
                     if($val!=NULL) {
                        $names .= ", ".$name."='".$val."'";
                     } else {
                        if (0==strcmp($name,"activated")) $names .= ", activated=0";
                        else if (0==strcmp($name,"parentid")) $names .= ", parentid=-1";
                        else if (0==strcmp($name,"parentid2")) $names .= ", parentid2=-1";
                        else if (0==strcmp($name,"alive")) $names .= ", alive=1";
                        else $names .= ", ".$name."=NULL";
                     }
                  }
               }
   
               $query = "UPDATE useracct SET ".$names;
               if ($reason!=NULL) $query .= ", activatedstr='".convertString($reason)."' ";
               $query .= " WHERE userid=".$userid.";";
               $dbi->update($query);               

               $wdObj = new WebsiteData();
               $webdata = $wdObj->getWebDataByName($originalrow['usertype']." Properties");
               if ($webdata != NULL) {
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
                  $wdObj->revertRow($webdata['wd_id'], $results[0]['wd_row_id'],NULL,TRUE);
               }

               $webdata_arr = $wdObj->getWebDataByFuzzyName($originalrow['usertype']." Objects%");
               if ($webdata_arr != NULL && count($webdata_arr)>0) {
                  for ($i=0; $i<count($webdata_arr); $i++) {
                     $query = "DELETE FROM wd_".$webdata_arr[$i]['wd_id']." WHERE userid=".$userid.";";
                     $dbi->delete($query);
                     $query = "show tables like 'wd_".$webdata_arr[$i]['wd_id']."_pub';";
                     $results = $dbi->queryGetResults($query);
                     if ($results != NULL && count($results)>0) {
                        $results = $wdObj->getDataByUserid($webdata_arr[$i]['wd_id'], $userid, NULL, TRUE);
                        for ($j=0;$j<count($results);$j++) $wdObj->copyPublicRow($webdata_arr[$i]['wd_id'], $results[$j]['wd_row_id']);
                     }
                  }
               }

               $query = "DELETE FROM userrel WHERE userid=".$userid.";";
               $dbi->delete($query);
               $results = $dbi->queryGetResults("SELECT * FROM userrel_pub WHERE userid=".$userid.";");
               for ($i=0;$i<count($results);$i++) {
                  $query = "INSERT INTO userrel (userrel_id,userid,reluserid,field1,field2,rel_type)";
                  $query .= " VALUES ('".$results[$i]['userrel_id']."','".$results[$i]['userid']."','".$results[$i]['reluserid']."','".$results[$i]['field1']."','".$results[$i]['field2']."','".$results[$i]['rel_type']."');";
                  $dbi->insert($query);
               }


            }
         } else {
            $query = "UPDATE useracct SET dbmode='DELETED', lastupdated=NOW()";
            if ($reason!=NULL) $query .= ", activatedstr='".convertString($reason)."' ";
            $query .= " WHERE userid=".$userid.";";
            $dbi->update($query);
         }
         
         if (class_exists("CustomUserPromote")) {
			 $customObj = new CustomUserPromote();
			 $customObj->promoteAccount($userid);
         }
         
      }



      function copyAccount($userid) {
      	$newuserid = $userid;
         $row = $this->getUser($userid);
         if ($row!=NULL) {
         	 	$row['email'] = str_replace("@","_copy@",$row['email']);
               $names = "dbmode, created, lastupdated";
               $values = "'NEW', NOW(), NOW()";
               foreach($row as $name=>$val){
                  if (0!=strcmp($name,"dbmode") && 0!=strcmp($name,"lastupdated") && 0!=strcmp($name,"created") && 0!=strcmp($name,"userid") && 0!=strcmp($name,"addrid")) {
                     $names .= ", ".$name;
                     $values .= ", '".$val."'";
                  }
               }
               $dbi = new MYSQLAccess();
               $query = "INSERT INTO useracct (".$names.") VALUES (".$values.");";
               $newuserid = $dbi->insertGetValue($query);

               $wdObj = new WebsiteData();
               $webdata = $wdObj->getWebDataByName($row['usertype']." Properties");
               if ($webdata != NULL) {
                  $results = $wdObj->getDataByUserid($webdata['wd_id'], $userid);
               	$wdObj->copyRow($webdata['wd_id'], $results['wd_row_id'], $newuserid); 
               }
         }
         return $newuserid;
      }
        

      
      
      
  
}
//--------------------------------------------------------------------------------
// End of UserAcct class
//--------------------------------------------------------------------------------


Class LoadUserData {
   function doWork($job){
      ini_set('auto_detect_line_endings',true);
      if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>LoadUserData job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
      
      $doneWithFile = FALSE;
      $filename = $job['content'];
      if ($job['field1']==NULL) $job['field1']=0;
      
      /*
      if ($job['field1']==NULL || $job['field1']==0) {
         $job['field1'] = 1;
         $contents = file_get_contents($filename);
         $contents = str_replace("\r\n","\n",$contents);
         $contents = str_replace("\r","\n",$contents);
         $contents = str_replace("\n\n","\n",$contents);
         file_put_contents($filename,$contents);
         if ($job['printstuff']) print "Run 1: Converted carriage returns and re-saved file: ".$filename."<br>\n";
         
         $i = strpos($contents,"phonenum");
         if($i===FALSE && $job['printstuff']) {
            print "no phonenum column found.<br>\n";
         } else if ($job['printstuff']) {
            print "phonenum found at: ".$i."<br>\n";
            print "Reference LF: ".ord("\n")." CR: ".ord("\r")."<br>\n";
            $section = substr($contents,$i,32);
            for($j=0;$j<32;$j++) {
               $c = substr($section,$j,1);
               print "Character ".$j." is ".$c." [".ord($c)."]<br>\n";
            }
            
         }
         
      } else {
      */
         $ua = new UserAcct();
         $loadresults = unserialize($job['phpobj']);
         
         if (($handle = fopen($filename, "r")) === FALSE) {
            $job['status'] = "ERROR";
         } else {
            $contents = fgets($handle,4096);
            if(0!=strcmp(substr($contents,(strlen($contents)-1)),"\n")) $contents .= "\n";
            
            if ($job['field2']==NULL || $job['field2']<ftell($handle)) $job['field2'] = ftell($handle);
            fseek($handle,$job['field2']);
   
            $totalLines = 0;
            while ($totalLines<50 && !$doneWithFile) {
               $skip = FALSE;
               $update = FALSE;
               $line = fgets($handle,4096);
               if(strlen($line)>3) {
                  if(0!=strcmp(substr($line,(strlen($line)-1)),"\n")) $line .= "\n";
                  $contents .= $line;
                  $totalLines++;
                  $job['field1']++;
               }
               $job['field2'] = ftell($handle);
               if (feof($handle)) $doneWithFile = TRUE;
            }
   
            $tempresults = $ua->loadContents($contents,$filename,TRUE,$job['printstuff']);
            
            $newresults = array();
            if(is_array($tempresults) && is_array($loadresults)) $newresults = array_merge($loadresults,$tempresults);
            else if(is_array($tempresults)) $newresults = $tempresults;
            else if(is_array($loadresults)) $newresults = $loadresults;
            
            $job['phpobj'] = mysqli_escape_string(serialize($newresults));
         }
      /*   
      }
      */
      
      $job['status'] = "NEW";
      if ($doneWithFile) {
         $job['status'] = "FINISHED";
         //$gcul = new GeoCodeUserList();
         //$gcul->addJob("Check geocodes for new ORGs");
      }
      
      return $job;
   }

   function startjob($fn) {
      $subj = "Uploading UserAcct CSV: ".date("m/d/Y H:i:s");
      $sched = new Scheduler();
      $sched->addSchedCustom("LoadUserData",$subj,2,NULL,$fn);
   }
}


Class DownloadUserData {
   function getUserDownloadHTML(){
      $str = "";

      $str .= "<div id=\"csvnoshow\">\n";
      $str .= "<div style=\"margin-bottom:15px;font-size:10px;font-family:arial;color:#555555;cursor:pointer;\" onclick=\"$('#csvnoshow').hide();$('#csvshow').show();\">+ Download CSV</div>\n";
      $str .= "</div>\n";
      $str .= "<div id=\"csvshow\" style=\"display:none;\">\n";
      $str .= "<div style=\"font-size:10px;font-family:arial;color:#555555;cursor:pointer;\" onclick=\"$('#csvshow').hide();$('#csvnoshow').show();\">- Hide</div>\n";

      $str .= "<form action=\"".getBaseURL()."jsfadmin/admincontroller.php\" method=\"POST\">\n";
      $str .= "<input type=\"hidden\" name=\"action\" value=\"dluserscloningcsv\">\n";
      $str .= "<input type=\"hidden\" name=\"segmentid\" value=\"".getParameter("segmentid")."\">\n";
      //$str .= "<input type=\"hidden\" name=\"s_includeproperties\" value=\"1\">\n";
      $str .= "%%%HIDDENFIELDS%%%\n";
      $str .= "<table cellpadding=\"2\" cellspacing=\"0\" style=\"font-size:14px;font-family:verdana;\"><tr>\n";
      $str .= "<td><b>Download CSV</b> &nbsp; </td>\n";
      $str .= "<td> Subject:</td>\n";
      $str .= "<td><input type=\"text\" name=\"subject\" value=\"\"> &nbsp; </td>\n";
      //$str .= "<td><input type=\"checkbox\" name=\"allcolumns\" value=\"YES\"> All columns </td>\n";
      $str .= "<td> &nbsp; <input type=\"submit\" name=\"submit\" value=\"submit\"></td>\n";
      $str .= "</tr></table>\n";
      $str .= "</form><br>\n";
      
      $str .= "</div>\n";
      return $str;
   }

   function addJob($sql,$subject=NULL,$field3=NULL,$field4="YES"){
      $sched = new Scheduler();
      $pathname = "jsfadmin/usercsv/useracct_".date("Ymd_His").".csv";
      $sched->addSchedCustom("DownloadUserData",$subject,4,NULL,$sql,0,0,$field3,$field4,$pathname);
   }

   function doWork($job){
      //error_reporting(E_ALL);

      //$userrels = array();
      //if (isset($job['phpobj'])) {
      //   $userrels = unserialize($job['phpobj']);
      //}
      
      
      if (!isset($job['field1']) || $job['field1']==NULL || $job['field1']<1) $job['field1']=1;
      if (!isset($job['field2']) || $job['field2']==NULL || $job['field2']<1) $job['field2']=0;
      $csv = "";
      $header1 = "";
      $header2 = "";
      $header3 = "";
      $useheader = FALSE;

      $limit = 250;
      $start = ($job['field1']-1) * $limit;
      $job['field1']++;
      
      $sql = new MYSQLaccess();

      $query = convertBack($job['content'])." LIMIT ".$start.",".$limit;
      if($job['printstuff']) print "\n<br>Query: ".$query."<br>\n";
      $results = $sql->queryGetResults($query);

      $job['status'] = "NEW";

      if ($results==NULL || count($results)<1) {
         $job['finished'] = TRUE;
         $job['status'] = "FINISHED";
         //$job['field3'] = "Job finished";
      } else {
         $ua = new UserAcct();
                  
         if ($job['field2']==0) $useheader = TRUE;

         $fields = array();
         $fields_wd = array();
         if(isset($job['phpobj']) && $job['phpobj']!=NULL) {
            $wdlist = unserialize($job['phpobj']);
            $fields = $wdlist['fields'];
            $fields_wd = $wdlist['fields_wd'];
         } else if($job['field3']!=NULL) {
            $wd = new WebsiteData();
            $fld3arr = separateStringBy($job['field3'],",",NULL,TRUE);
            for($i=0;$i<count($fld3arr);$i++) {
               if($fld3arr[$i]!=NULL && $fld3arr[$i]>0) {
                  if (0==strcmp(trim(strtolower($job['field4'])),"yes")) $flds = $wd->getAllFieldsSystem($fld3arr[$i]);
                  else $flds = $wd->getHeaderFields($fld3arr[$i]);
                  $fields[] = $flds;
                  $fields_wd[] = $fld3arr[$i];
               }
            }
            $wdlist = array();
            $wdlist['fields'] = $fields;
            $wdlist['fields_wd'] = $fields_wd;
            $job['phpobj'] = mysqli_escape_string(serialize($wdlist));            
         }
         
         if($job['printstuff']) print "\n<br>Iterating thru users:<br>\n";
         for ($i=0;$i<count($results);$i++) {
            // first get relevant useracct fields
            $user = $ua->getUser($results[$i]['userid']);
            if($job['printstuff']) print "\n<br>".$i." ";
            foreach ($user as $key => $value){
               if(0!=strcmp($key,"password") && 0!=strcmp($key,"password2") && 0!=strcmp($key,"token") && 0!=strcmp($key,"addrid") && 0!=strcmp($key,"nletter") && 0!=strcmp($key,"orgid") && 0!=strcmp($key,"age") && 0!=strcmp($key,"gender") && 0!=strcmp($key,"edu") && 0!=strcmp($key,"marital") && 0!=strcmp($key,"ownersite")) {
                  $key = trim($key);
                  $value = trim($value);
                  if($job['printstuff']) print "(".$key.",".$value.") ";
                  if($i==0 && $useheader) {
                     $header1 .= "\"".csvEncodeDoubleQuotes($key)."\",";
                     $header2 .= "\"".csvEncodeDoubleQuotes($key)."\",";
                     $header3 .= "\"".csvEncodeDoubleQuotes($key)."\",";
                  }
                  $csv .= "\"".csvEncodeDoubleQuotes($value)."\",";
               }
            }

            if($job['printstuff']) print "<br>\nGetting jdata...";
            // Attach jdata fields
            for($k=0;$k<count($fields_wd);$k++){
               $flds = $fields[$k];
               if($flds!=NULL && count($flds)>0){
                  $rows = $wd->getDataByUserid($fields_wd[$k],$results[$i]['userid']);
                  $row = $rows[0];
                  for ($j=0;$j<count($flds);$j++){
                     //$csvrow = $wd->getCSVRow($fields_wd[$k],$row['wd_row_id'],$flds[$j],$row[$flds[$j]['field_id']]);            	 
                     $csvrow = $wd->getCSVRow($fields_wd[$k],$row['wd_row_id'],$flds[$j]);            	 
                     if($i==0 && $useheader) {
                        $header1 .= $csvrow['header'];
                        $header2 .= $csvrow['qheader'];
                        $header3 .= $csvrow['mheader'];
                     }
                     $csv .= $csvrow['content'];            	 
                  }
               }
            }
            
            if (0==strcmp(trim(strtolower($job['field4'])),"yes")) {
               if($job['printstuff']) print "<br>\nGetting related users...";
               // Add related users for admin/contact
               $relfields = array();
               $relfields[] = "userid";
               $relfields[] = "fname";
               $relfields[] = "lname";
               $relfields[] = "email";
               $relfields[] = "title";
               $relfields[] = "addr1";
               $relfields[] = "addr2";
               $relfields[] = "city";
               $relfields[] = "state";
               $relfields[] = "zip";
               $relfields[] = "phonenum";
               $adminuser = NULL;
               $contactuser = NULL;
               $userrels = $ua->getUsersRelated($user['userid'],"to");
               for ($j=0; $j<count($userrels); $j++) {
                  if (0==strcmp($userrels[$j]['rel_type'],"SRVYADMIN")) $adminuser = $ua->getUser($userrels[$j]['reluserid']);
                  else if (0==strcmp($userrels[$j]['rel_type'],"PUBCNTCT")) $contactuser = $ua->getUser($userrels[$j]['reluserid']);
               }
               if($i==0 && $useheader) {
                  $header = "";
                  for($k=0;$k<count($relfields);$k++) $header .= "\"admin_".$relfields[$k]."\",";
                  $header1 .= $header;
                  $header2 .= $header;
                  $header3 .= $header;
                  $header = "";
                  for($k=0;$k<count($relfields);$k++) $header .= "\"public_".$relfields[$k]."\",";
                  $header1 .= $header;
                  $header2 .= $header;
                  $header3 .= $header;
               }
               for($k=0;$k<count($relfields);$k++) $csv .= "\"".csvEncodeDoubleQuotes($adminuser[$relfields[$k]])."\",";
               for($k=0;$k<count($relfields);$k++) $csv .= "\"".csvEncodeDoubleQuotes($contactuser[$relfields[$k]])."\",";
            }
            
            $csv .= "\n";
            $job['field2']++;
         }
         
         //Append (or create) the csv file
         if ($useheader) $csv = $header1."\n".$header2."\n".$header3."\n".$csv;
         $filename = $GLOBALS['baseDir'].$job['field5'];
         $file = fopen($filename,"a");
         fwrite($file, $csv);
         fclose($file);
      }

      //$job['phpobj'] = mysql_escape_string(serialize($userrels));
      
      return $job;
   }
   
   function copyJob($copyid=NULL) {
      $jobresults = FALSE;
      if($copyid!=NULL){
         $dbLink = new MYSQLaccess;
         
         $query = "SELECT * FROM schedemail WHERE semailid=".$copyid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            $pathname = "jsfadmin/usercsv/useracct_".date("Ymd_His").".csv";
            $sched = new Scheduler();
            $sched->addSchedCustom("DownloadUserData",$results[0]['subject'],4,NULL,$results[0]['sql'],0,0,$results[0]['field3'],$results[0]['field4'],$pathname);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
}








Class PromoteUserList {
   function addJob($useridlist,$ignorelist=NULL){
      if(!is_array($useridlist) && strpos($useridlist,",")===FALSE) {
         $temp = $useridlist;
         $useridlist = array();
         $useridlist[] = $temp;
      } else if(!is_array($useridlist) && strpos($useridlist,",")!==FALSE){
         $useridlist = separateStringBy($useridlist,",",NULL,TRUE);
      }
      
      $dbLink = new MYSQLaccess();
      $query =  "SELECT content,semailid FROM schedemail WHERE classname='PromoteUserList' and status='NEW' LIMIT 0,1;";
      $results = $dbLink->queryGetResults($query);
      if($results!=NULL && count($results)>0) {
         $useridlist_o = separateStringBy($results[0]['content'],",",NULL,TRUE);
         $useridlist = array_merge($useridlist,$useridlist_o);         
         $useridlist = array_merge(array_unique($useridlist));
         $query="UPDATE schedemail SET content='".implode(",",$useridlist)."' WHERE semailid=".$results[0]['semailid'];
         $dbLink->update($query);
      } else {
         $useridlist = array_merge(array_unique($useridlist));
         //addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL,$phpfile=NULL){
         $sched = new Scheduler();
         $sched->addSchedCustom("PromoteUserList",NULL,2,NULL,implode(",",$useridlist),0,0,NULL,NULL,NULL,NULL,NULL,0,$ignorelist);
      }
   }

   function doWork($job){
      $ignoreusers = unserialize($job['phpobj']);
      $useridlist_o = separateStringBy($job['content'],",",NULL,TRUE);
      $total_it = count($useridlist_o);
      if(count($useridlist_o)>100) {
         $newuseridlist = array();
         for($i=100;$i<count($useridist_o);$i++){
            $newuseridlist[] = $useridlist_o[$i];
         }
         $job['content'] = implode(",",$newuseridlist);
         $job['status'] = "NEW";
         $total_it = 100;
      } else {
         $job['status'] = "FINISHED";
         $job['content'] = "";
      }
      
      if($total_it>0) {
         $promote_id = array();
         for($i=0;$i<$total_it;$i++){
            $promote_id[] = $userlist_o[$i];
         }
         
         $ua = new UserAcct();
         $ua->promoteManyAccounts($promote_id,$ignoreusers);
      }      
      return $job;
   }
}

Class GeoCodeUserList {
   function addJob($subj=NULL){
      $sched = new Scheduler();
      
      $query = "SELECT userid FROM useracct WHERE usertype='org' AND dbmode='APPROVED'";
      $query .= " AND (addr1 is NOT NULL AND addr1<>'')";
      $query .= " AND (city is NOT NULL AND city<>'')";
      $query .= " AND (state is NOT NULL AND state<>'')";
      $query .= " AND (zip is NOT NULL AND zip<>'')";
      $query .= " AND (lat is NULL OR lat='' OR (lat>-0.01 AND lat<0.01))";
      $query .= " AND (lng is NULL OR (lng>-0.01 AND lng<0.01))";
      $query .= " AND created>DATE_SUB( CURDATE( ) ,INTERVAL 45 DAY )";
      $query .= " ORDER BY created DESC";
      
      //addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL,$phpfile=NULL){
      $sched->addSchedCustom("GeoCodeUserList",$subj,2,NULL,$query);
   }

   function doWork($job){
      $maxrecords = 50;
      if($job['field1'] == NULL) $job['field1'] = 0;
      if($job['field2'] == NULL) $job['field2'] = 0;
      
      $sql = new MYSQLaccess();
      $ua = new UserAcct();
      $query = convertBack($job['content']);
      $query .= " LIMIT 0,".$maxrecords.";";
      if($job['printstuff']) print "<br>\nQuery: ".$query."<br>\n";
      $results = $sql->queryGetResults($query);
      $job['field1']++;
      $approveaccounts = array();
      
      for($i=0;$i<count($results);$i++) {
         $info = $ua->getUserGeoCode($results[$i]['userid']);
         $approveaccounts[] = $results[$i]['userid'];
         $job['field2']++;
         if($job['printstuff']) print "Userid: ".$results[$i]['userid']." - ";
         if($job['printstuff']) print_r($info);
         if($job['printstuff']) print "<br>\n";
      }

      $ua->promoteManyAccounts($approveaccounts);
      
      $job['status'] = "NEW";
      if(count($results)<$maxrecords) $job['status'] = "FINISHED";
      
      return $job;
   }
}

?>
