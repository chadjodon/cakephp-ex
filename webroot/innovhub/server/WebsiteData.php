<?php
//error_reporting(E_ALL);
class WebsiteData {
   function getQuestionOptions ($name,$selected,$extra=NULL, $qtypes=NULL){
     if($qtypes==NULL || $qtypes['TEXT']) $options['Text box'] = "TEXT";
     if($qtypes==NULL || $qtypes['INT']) $options['Integer'] = "INT";
     if($qtypes==NULL || $qtypes['DEC']) $options['Decimal'] = "DEC";
     if($qtypes==NULL || $qtypes['MONEY']) $options['Money'] = "MONEY";
     if($qtypes==NULL || $qtypes['TEXTAREA']) $options['Text area'] = "TEXTAREA";
     if($qtypes==NULL || $qtypes['HTML'])  $options['Rich Text'] = "HTML";
     if($qtypes==NULL || $qtypes['CHECKBOX']) $options['Checkboxes'] = "CHECKBOX";
     if($qtypes==NULL || $qtypes['HRZCHKBX'])  $options['Horiz Checkboxes'] = "HRZCHKBX";
     if($qtypes==NULL || $qtypes['NEWCHKBX']) $options['New Checkboxes'] = "NEWCHKBX";
     if($qtypes==NULL || $qtypes['MBL_MC']) $options['Mobile: multi-choice'] = "MBL_MC";
     if($qtypes==NULL || $qtypes['MBL_IMG']) $options['Mobile: images'] = "MBL_IMG";
     if($qtypes==NULL || $qtypes['MBL_UPL']) $options['Mobile: upload'] = "MBL_UPL";
     if($qtypes==NULL || $qtypes['COLOR']) $options['Color'] = "COLOR";
     if($qtypes==NULL || $qtypes['DATE']) $options['Date'] = "DATE";
     if($qtypes==NULL || $qtypes['DATETIME']) $options['Date And Time'] = "DATETIME";
     if($qtypes==NULL || $qtypes['AGE']) $options['Age (Date)'] = "AGE";
     if($qtypes==NULL || $qtypes['SNGLCHKBX']) $options['Single chckbx'] = "SNGLCHKBX";
     if($qtypes==NULL || $qtypes['RADIO']) $options['Radio Button'] = "RADIO";
     if($qtypes==NULL || $qtypes['POLLRADIO']) $options['Poll Radio'] = "POLLRADIO";
     if($qtypes==NULL || $qtypes['DROPDOWN']) $options['Drop Down List'] = "DROPDOWN";
     if($qtypes==NULL || $qtypes['FOREIGN']) $options['Dropdown from another data list'] = "FOREIGN";
     if($qtypes==NULL || $qtypes['FOREIGNCB']) $options['Checkboxes from another data list'] = "FOREIGNCB";
     if($qtypes==NULL || $qtypes['FOREIGNTBL']) $options['Checkboxes from database'] = "FOREIGNTBL";
     if($qtypes==NULL || $qtypes['FOREIGNTDD']) $options['Dropdown from database'] = "FOREIGNTDD";
     if($qtypes==NULL || $qtypes['FOREIGNSRY']) $options['Import Survey Table'] = "FOREIGNSRY";
     if($qtypes==NULL || $qtypes['FOREIGNSCT']) $options['Import Survey Section'] = "FOREIGNSCT";
     if($qtypes==NULL || $qtypes['FOREIGNHYB']) $options['Import Survey Hybrid'] = "FOREIGNHYB";
     if($qtypes==NULL || $qtypes['MANY']) $options['Dynamic List'] = "MANY";
     if($qtypes==NULL || $qtypes['TABLE']) $options['Table'] = "TABLE";
     //$options['Likert Scales'] = "LIKERT";
     if($qtypes==NULL || $qtypes['NEWLIKERT']) $options['Likert Scales'] = "NEWLIKERT";
     if($qtypes==NULL || $qtypes['NEWPRCNT']) $options['Percentage Scale'] = "NEWPRCNT";
     if($qtypes==NULL || $qtypes['INFO']) $options['Information'] = "INFO";
     if($qtypes==NULL || $qtypes['SPACER']) $options['Spacer'] = "SPACER";
     if($qtypes==NULL || $qtypes['FILE']) $options['File Upload'] = "FILE";
     if($qtypes==NULL || $qtypes['IMAGE']) $options['Image Upload'] = "IMAGE";
     if($qtypes==NULL || $qtypes['USERS']) $options['User List'] = "USERS";
     if($qtypes==NULL || $qtypes['USERSRCH']) $options['User Search'] = "USERSRCH";
     if($qtypes==NULL || $qtypes['USERLIST']) $options['Multiple Users'] = "USERLIST";
     if($qtypes==NULL || $qtypes['USERAUTO']) $options['User Auto-complete'] = "USERAUTO";
     if($qtypes==NULL || $qtypes['SITELIST']) $options['Microsites List'] = "SITELIST";
     if($qtypes==NULL || $qtypes['SITEOPT']) $options['Microsites Options'] = "SITEOPT";
     if($qtypes==NULL || $qtypes['REGION']) $options['Regions'] = "REGION";
     if($qtypes==NULL || $qtypes['STATE']) $options['US State'] = "STATE";
     if($qtypes['VOTE']) $options['Ballot Field'] = "VOTE";
     return getOptionList($name, $options, $selected, TRUE, $extra);
   }

   // Find users related to an organization
   function getUsersRelated($webdata,$orgid){
      $ua = new UserAcct();
      $reltype = $webdata['userrel'];
      if ($reltype==NULL) $reltype = "SRVYADMIN";
      $adminids = $ua->getUsersRelated($orgid,"to",$reltype);
      if ($adminids===NULL || count($adminids)<1) {
         if (0!=strcmp($reltype,"SRVYADMIN")) $adminids = $ua->getUsersRelated($orgid,"to","SRVYADMIN");
         if ($adminids===NULL || count($adminids)<1) {
            $adminids = $ua->getUsersRelated($orgid,"to","PUBCNTCT");
         }
      }
      return $adminids;
   }

   // Create a new table
   function newWebData($name, $info, $privatesrvy, $adminemail, $filename, $saveresults, $emailresults,$glossaryid=null, $status=NULL, $externalid=NULL, $field1=NULL, $field2=NULL, $field3=NULL, $field4=NULL, $shortname=NULL, $password=NULL, $captcha=NULL, $userrel=NULL, $esign=0, $usertype=NULL, $rowdisplay=NULL) {
      return $this->updateWebData(NULL,$name,$info,$privatesrvy,$adminemail,$filename,$saveresults,$emailresults,$glossaryid,$status,$externalid,$starttime,$endtime,$field1,$field2,$field3,$field4,$shortname,$password,$captcha,$userrel,$esign,$usertype,$rowdisplay);         
   }
      
   function updateWebData($wd_id=NULL, $name=NULL, $info=NULL, $privatesrvy=2, $adminemail=NULL, $filename=NULL, $saveresults=0, $emailresults=0,$glossaryid=NULL, $status=NULL, $externalid=NULL, $starttime=NULL, $endtime=NULL, $field1=NULL, $field2=NULL, $field3=NULL, $field4=NULL, $shortname=NULL, $password=NULL, $captcha=NULL, $userrel=NULL, $esign=0, $usertype=NULL, $rowdisplay=NULL) {
      $dbi = new MYSQLAccess();
      $name = convertString($name);
      
      if($wd_id==NULL){
         $query1 = "INSERT INTO webdata (createdon, siteid, glossaryid, name) VALUES (NOW(), -1, 0, '".$name."');";
         $wd_id = $dbi->insertGetValue($query1);
      }
      
      $shortname = removeSpecialChars($shortname);
      $info = convertString($info);
      $rowdisplay = convertString($rowdisplay);
      if ($glossaryid==NULL || 0==strcmp(trim($glossaryid),"")) $glossaryid = -1;
      if ($esign!=1) $esign=0;
      if ($captcha==NULL || !is_numeric($captcha) || $captcha!=1) $captcha=0;
      if($name!=NULL) $query .= "name='".$name."', ";
      else $query .= "name=NULL, ";
      if($name!=NULL) $query .= "shortname='".$shortname."', ";
      else $query .= "shortname=NULL, ";
      if($password!=NULL) $query .= "password='".$password."', ";
      else $query .= "password=NULL, ";
      if($info!=NULL) $query .= "info='".$info."', ";
      else $query .= "info=NULL, ";
      if($rowdisplay!=NULL) $query .= "rowdisplay='".$rowdisplay."', ";
      else $query .= "rowdisplay=NULL, ";
      if($field1!=NULL) $query .= "field1='".$field1."', ";
      else $query .= "field1=NULL, ";
      if($field2!=NULL) $query .= "field2='".$field2."', ";
      else $query .= "field2=NULL, ";
      if($field3!=NULL) $query .= "field3='".$field3."', ";
      else $query .= "field3=NULL, ";
      if($field4!=NULL) $query .= "field4='".$field4."', ";
      else $query .= "field4=NULL, ";
      if($privatesrvy!=NULL) $query .= "privatesrvy=".$privatesrvy.", ";
      if($adminemail!=NULL) $query .= "adminemail='".$adminemail."', ";
      else $query .= "adminemail=NULL, ";
      if($filename!=NULL) $query .= "filename='".$filename."', ";
      else $query .= "filename=NULL, ";
      if($userrel!=NULL) $query .= "userrel='".$userrel."', ";
      else $query .= "userrel=NULL, ";
      if($usertype!=NULL) $query .= "usertype='".$usertype."', ";
      else $query .= "usertype=NULL, ";
      if($esign!=NULL) $query .= "esign=".$esign.", ";
      else $query .= "esign=NULL, ";
      if($saveresults!=NULL) $query .= "saveresults=".$saveresults.", ";
      else $query .= "saveresults=NULL, ";
      if($emailresults!=NULL) $query .= "emailresults=".$emailresults.", ";
      else $query .= "emailresults=NULL, ";
      if($glossaryid!=NULL) $query .= "glossaryid=".$glossaryid.", ";
      else $query .= "glossaryid=NULL, ";
      if($captcha!=NULL) $query .= "captcha=".$captcha;
      else $query .= "captcha=NULL";
      if ($status!=NULL) $query .= ", status='".$status."'";
      if ($externalid!=NULL) $query .= ", externalid='".$externalid."'";
      if ($starttime!=NULL) $query .= ", starttime='".$starttime."'";
      if ($endtime!=NULL) $query .= ", endtime='".$endtime."'";
      
      $query1 = "UPDATE webdata SET ";
      $query1 .= $query;
      $query1 .= " WHERE wd_id=".$wd_id.";";
      $dbi->update($query1);
      
      unset($_SESSION['webdata'][$wd_id]);
      unset($_SESSION['webdata'][strtolower(trim($name))]);
      unset($_SESSION['webdata'][strtolower(trim($shortname))]);
      return $wd_id;
   }
   
   function updateWDStatus($wd_id, $status="INACTIVE") {
      $this->setStatus($wd_id, $status);
   }

   function setStatus($wd_id, $status) {
      $this->updateWebDataProperty($wd_id, "status", $status);
   }

   function updateWebDataProperty($wd_id, $name, $value) {
      unset($_SESSION['webdata'][$wd_id]);
      $query = "UPDATE webdata SET ".$name."='".$value."' where wd_id=".$wd_id.";";
      $dbi = new MYSQLAccess();
      $dbi->update($query);
   }

   function removeWebData ($wd_id) {
      $dbi = new MYSQLAccess();
      $query = "DELETE FROM wd_link where wd_id1=".$wd_id.";";
      $dbi->update($query);
      $query = "DELETE FROM wd_link where wd_id2=".$wd_id.";";
      $dbi->update($query);
      $query = "DELETE FROM wd_fields where wd_id=".$wd_id.";";
      $dbi->update($query);
      $query = "DELETE FROM wd_rel where wd_id=".$wd_id.";";
      $dbi->update($query);
      $query = "DELETE from webdata where wd_id=".$wd_id.";";
      $dbi->update($query);
      
      $query = "show tables like 'wd_".$wd_id."';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "DROP TABLE wd_".$wd_id.";";
         $dbi->update($query);
      }

      $query = "show tables like 'wd_".$wd_id."_pub';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "DROP TABLE wd_".$wd_id."_pub;";
         $dbi->update($query);
      }
      
      $query = "show tables like 'wdindex_".$wd_id."';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "DROP TABLE wdindex_".$wd_id.";";
         $dbi->update($query);
      }
      unset($_SESSION['webdata'][$wd_id]);
   }

   function getNextFieldID ($wd_id) {
      $query = "SELECT field_id FROM wd_fields WHERE wd_id=".$wd_id.";";
      $dbi = new MYSQLAccess();
      $next = -1;
      $results = $dbi->queryGetResults($query);
      for ($i=0; $i<count($results); $i++) {
         $val = substr($results[$i]['field_id'],1);
         if ($val > $next) $next = $val;
      }
      return "q".($next + 1);
   }

   function startCloning($wd_id){
      $this->checkAndCreateWD($wd_id,TRUE);
   }

   function checkAndCreateWD($wd_id,$pub=FALSE){
      $dbi = new MYSQLAccess();

      $query = "show tables like 'wd_".$wd_id."';";
      if ($pub) $query = "show tables like 'wd_".$wd_id."_pub';";
      $results = $dbi->queryGetResults($query);

      if ($results == NULL || count($results)<1) {
         $query = "CREATE TABLE wd_".$wd_id." ( ";
         $query .= "wd_row_id int(20) unsigned NOT NULL auto_increment, ";
         if ($pub) {
            $query = "CREATE TABLE wd_".$wd_id."_pub ( ";
            $query .= "wd_row_id int(20) unsigned NOT NULL, ";
         }
         $query .= "dbmode varchar(8) default 'NEW', ";
         $query .= "userid bigint DEFAULT NULL, ";
         $query .= "externalid varchar(128) DEFAULT NULL, ";
         $query .= "origemail varchar(255) default NULL, ";
         $query .= "serialnumber varchar(255) default NULL, ";
         $query .= "complete char(2) default 'N', ";
         $query .= "comments text, ";
         $query .= "datesent date default NULL, ";
         $query .= "lastupdate datetime default NULL, ";
         $query .= "lastupdateby text default NULL, ";
         $query .= "lastupdateby2 text default NULL, ";
         $query .= "created datetime default NULL, ";
         $query .= "esignature varchar(255) default NULL, ";
         $query .= "PRIMARY KEY(wd_row_id));";
         $dbi->update($query);

         if ($pub) {
            $fs = $this->getAllFieldsSystem($wd_id);
            for ($i=0; $i<count($fs); $i++) $this->addField($wd_id, $fs[$i]['parent_s'], $fs[$i]['field_id'], $fs[$i]['label'], $fs[$i]['text'], $fs[$i]['field_type'], $fs[$i]['sequence'], $fs[$i]['privacy'], $fs[$i]['header'], $fs[$i]['defaultval'], $fs[$i]['required'], $fs[$i]['srchfld'],TRUE, $fs[$i]['notes'], $fs[$i]['filterfld'], $fs[$i]['stylecss'], $fs[$i]['map']);
         }
      }
   }
   
   function savedsearch($wd_id,$title=NULL,$querystr=NULL,$descr=NULL,$crud="read"){
      $dbi = new MYSQLAccess();
      $query = "show tables like 'wdsearch_".$wd_id."';";
      $results = $dbi->queryGetResults($query);
      if ($results==NULL || count($results)<1) {
         $query = "CREATE TABLE wdsearch_".$wd_id." ( ";
         $query .= "wds_id int(20) unsigned NOT NULL auto_increment, ";
         $query .= "created datetime default NULL, ";
         $query .= "querystr text default NULL, ";
         $query .= "title varchar(255) default NULL, ";
         $query .= "descr text default NULL, ";
         $query .= "htags text default NULL, ";
         $query .= "nvptype varchar(32) default NULL, ";
         $query .= "PRIMARY KEY(wds_id));";
         $dbi->update($query);
      }
      
      $results = array();
      if($title!=NULL) {
         $query = "SELECT * FROM wdsearch_".$wd_id." ";
         $query .= "WHERE title='".convertString($title)."';";
         $results = $dbi->queryGetResults($query);
      }
      
      $errorstr = "Invalid operation received.";
      if(0==strcmp($crud,"create")) {
         $errorstr = "The title you chose is already in use, please pick another.";
         if(count($results)<1) {
            if($title!=NULL) {
               $errorstr = "";
               $query = "INSERT INTO wdsearch_".$wd_id;
               $query .= " (created,querystr,title,descr) ";
               $query .= "VALUES";
               $query .= " (NOW()";
               if($querystr!=NULL) $query .= ",'".convertString($querystr)."'";
               else $query .= ",NULL";
               $query .= ",'".convertString($title)."'";
               if($descr!=NULL) $query .= ",'".convertString($descr)."'";
               else $query .= ",NULL";
               $query .= ");";
               $dbi->insert($query);
            } else {
               $errorstr = "Please enter a title to save this search.";
            }
         }
      } else if(0==strcmp($crud,"read")) {
         if(count($results)<1) {
            $query = "SELECT * FROM wdsearch_".$wd_id;
            $results = $dbi->queryGetResults($query);
         }
         $errorstr = $results;
      } else if(0==strcmp($crud,"update")) {
         $errorstr = "Could not update - title was not found.";
         if(count($results)==1) {
            $query = "UPDATE wdsearch_".$wd_id." SET ";
            $query .= "querystr='".convertString($querystr)."', ";
            $query .= "descr='".convertString($descr)."' ";
            $query .= "WHERE title='".convertString($title)."';";
            $dbi->update($query);
            $errorstr = "";
         }
      } else if(0==strcmp($crud,"delete")) {
         $errorstr = "Could not delete - title was not found.";
         if(count($results)==1) {
            $query = "DELETE FROM wdsearch_".$wd_id." ";
            $query .= "WHERE title='".convertString($title)."';";
            $dbi->update($query);
            $errorstr = "";
         }
      }
      return $errorstr;
   }
   
   

   //------- SEARCH INDEXING --------------------
   // Use a jdata table to create a "dictionary" to help search
   // Build the efficient index here... (the real table used to search)
   function indexTableWD($wd_id,$debug=FALSE){
      if($debug) print "<br>".date("M/d/Y H:i:s").": Staring general indexing.";      
      $dbi = new MYSQLAccess();

      $multi = $this->getFieldsMultiIndex($wd_id);
      $fs = $multi['allfields'];
      $qs = $multi['bylabel'];

      $query = "show tables like 'wdindex_".$wd_id."';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "DROP TABLE wdindex_".$wd_id.";";
         $dbi->query($query);
      }
      if($debug) print "<br>".date("M/d/Y H:i:s").": Removing all former indexed terms...";      

      
      $query = "CREATE TABLE wdindex_".$wd_id." ( ";
      $query .= "wdi_id int(20) unsigned NOT NULL auto_increment, ";
      $query .= "lastupdate datetime default NULL, ";
      $query .= "autosuggest tinyint DEFAULT 0, ";
      $query .= "word varchar(128) default NULL, ";
      $query .= "origword varchar(128) default NULL, ";
      $query .= "type varchar(32) default NULL, ";
      $query .= "indexval text default NULL, ";
      $query .= "url text default NULL, ";
      
      for ($i=0; $i<count($fs); $i++) {
         if(0!=strcmp($fs[$i]['map'],"word") && 0!=strcmp($fs[$i]['map'],"keywords") && 0!=strcmp($fs[$i]['map'],"type") && 0!=strcmp($fs[$i]['map'],"url") && 0!=strcmp($fs[$i]['map'],"autosuggest") && 0!=strcmp($fs[$i]['map'],"indexval")) {
            $createType = $this->getCreateType($fs[$i]['field_type']);
            $query .= $fs[$i]['field_id']." ".$createType.", ";
         }
      }
      
      $query .= "PRIMARY KEY(wdi_id));";
      $dbi->update($query);
      if($debug) print "<br>".date("M/d/Y H:i:s").": Created INDEXing table.";      

      //Build the base insert
      $insquery = "INSERT INTO wdindex_".$wd_id." (lastupdate,autosuggest,word,origword,type,url";
      for ($i=0; $i<count($fs); $i++) {
         if(0!=strcmp($fs[$i]['map'],"word") && 0!=strcmp($fs[$i]['map'],"keywords") && 0!=strcmp($fs[$i]['map'],"type") && 0!=strcmp($fs[$i]['map'],"url") && 0!=strcmp($fs[$i]['map'],"autosuggest")) {
            $insquery .= ", ".$fs[$i]['field_id'];
         }
      }
      $insquery .= ")";
      $insquery .= " VALUES ";
      
      //Iterate through all words and keywords
      $query = "SELECT * FROM wd_".$wd_id." WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'));";
      $results = $dbi->queryGetResults($query);
      if($debug) print "<br>".date("M/d/Y H:i:s").": ".count($results)." entries to index...";      
      if ($results != NULL && count($results)>0) {
         for ($j=0; $j<count($results); $j++) {
            $query = $insquery;
            
            $as = 0;
            if(0==strcmp(strtolower($results[$j][$qs['autosuggest']]),"yes")) $as = 1;
            
            $query .= "(NOW(),".$as.",'".$results[$j][$qs['word']]."','".$results[$j][$qs['word']]."','".$results[$j][$qs['type']]."','".$results[$j][$qs['url']]."'";
            for ($i=0; $i<count($fs); $i++) {
               if(0!=strcmp($fs[$i]['map'],"word") && 0!=strcmp($fs[$i]['map'],"keywords") && 0!=strcmp($fs[$i]['map'],"type") && 0!=strcmp($fs[$i]['map'],"url") && 0!=strcmp($fs[$i]['map'],"autosuggest")) {
                  $query .= ",'".$results[$j][$fs[$i]['field_id']]."'";
               }
            }
            $query .= ")";
            
            $otherwords = separateStringBy(convertBack($results[$j][$qs['keywords']]),",",NULL,TRUE);
            for($k=0;$k<count($otherwords);$k++){
               $query .= ", (NOW(),".$as.",'".$otherwords[$k]."','".$results[$j][$qs['word']]."','".$results[$j][$qs['type']]."','".$results[$j][$qs['url']]."'";
               for ($i=0; $i<count($fs); $i++) {
                  if(0!=strcmp($fs[$i]['map'],"word") && 0!=strcmp($fs[$i]['map'],"keywords") && 0!=strcmp($fs[$i]['map'],"type") && 0!=strcmp($fs[$i]['map'],"url") && 0!=strcmp($fs[$i]['map'],"autosuggest")) {
                     $query .= ",'".$results[$j][$fs[$i]['field_id']]."'";
                  }
               }
               $query .= ")";               
            }
            $dbi->insert($query);            
         }
      }
      if($debug) print "<br>".date("M/d/Y H:i:s").": Done indexing general entries.";      
   }
   
   // Just specify a field to create an auto-suggest search box
   function indexTableSimpleAutosuggest($wd_id,$keywordsfield,$debug=FALSE){
      if($debug) print "<br>".date("M/d/Y H:i:s").": Staring general indexing.";      
      $dbi = new MYSQLAccess();

      $multi = $this->getFieldsMultiIndex($wd_id);
      $fs = $multi['allfields'];
      $qs = $multi['bylabel'];

      $query = "show tables like 'wdindex_".$wd_id."';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "DROP TABLE wdindex_".$wd_id.";";
         $dbi->query($query);
      }
      if($debug) print "<br>".date("M/d/Y H:i:s").": Removing all former indexed terms...";      
      
      $query = "CREATE TABLE wdindex_".$wd_id." ( ";
      $query .= "wdi_id int(20) unsigned NOT NULL auto_increment, ";
      $query .= "lastupdate datetime default NULL, ";
      $query .= "autosuggest tinyint DEFAULT 0, ";
      $query .= "word varchar(128) default NULL, ";
      $query .= "origword varchar(128) default NULL, ";
      $query .= "type varchar(32) default NULL, ";
      $query .= "indexval text default NULL, ";
      $query .= "url text default NULL, ";
      $query .= "PRIMARY KEY(wdi_id));";
      $dbi->update($query);
      if($debug) print "<br>".date("M/d/Y H:i:s").": Created INDEXing table.";      

      //Build the base insert
      $insquery = "INSERT INTO wdindex_".$wd_id." (lastupdate,autosuggest,word,origword,type,url)";
      $insquery .= " VALUES ";
      
      //Iterate through all words and keywords
      $query = "SELECT * FROM wd_".$wd_id." WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'));";
      $results = $dbi->queryGetResults($query);
      $querycounter = 0;
      
      $allwords = array();
      
      if($debug) print "<br>".date("M/d/Y H:i:s").": ".count($results)." entries to index...";      
      if ($results != NULL && count($results)>0) {
         for ($j=0; $j<count($results); $j++) {
            if($results[$j][$qs[$keywordsfield]]!=NULL) {
               if($querycounter==0) $query = $insquery;
               $otherwords = separateStringBy(convertBack($results[$j][$qs[$keywordsfield]]),",",NULL,TRUE);
               for($k=0;$k<count($otherwords);$k++){
                  if(!isset($allwords[$otherwords[$k]]) || $allwords[$otherwords[$k]]!=1){
                     if($querycounter>0) $query .= ", ";
                     $query .= "(NOW(),1,'".$otherwords[$k]."','".$otherwords[$k]."','".$results[$j][$qs['type']]."','".$results[$j][$qs['url']]."')";
                     $querycounter++;
                     $allwords[$otherwords[$k]] = 1;
                  }
               }
            }
            if($querycounter>0 && ($querycounter>=200 || $j==(count($results) - 1))) {
               $dbi->insert($query);
               $query = "";
               $querycounter = 0;
            }
         }
      }
      if($debug) print "<br>".date("M/d/Y H:i:s").": Done indexing general entries.";      
   }

   

   // Search the pre-created index
   // This is meant to be real-time
   function searchIndexWD($wd_id,$phrase,$excltypes=NULL,$autosuggestonly=TRUE,$testing=FALSE){
      $dbi = new MYSQLAccess();
      $prarr = separateStringBy($phrase," ",NULL,TRUE);
      
      if($testing) {
         print "<br>phase breakdown: ";
         print_r($prarr);
         print "<br>";
      }
      
      if(!is_numeric($wd_id)) {
         $wd = $this->getWebData($wd_id);
         $wd_id = $wd['wd_id'];
      }
      
      $bestresults = NULL;
      
      $i=count($prarr)-1;
      if($testing) print "<br>Starting i: ".$i;
      while($i>=0) {
         $query = "SELECT * FROM wdindex_".$wd_id." WHERE LOWER(word) like ";
         $query .= "'";
         for($j=$i;$j<count($prarr);$j++) {
            if($j>$i) $query .= " ";
            $query .= strtolower($prarr[$j]);
         }
         $query .= "%'";
         
         if($autosuggestonly) $query .= " AND autosuggest=1";
         
         if($excltypes!=NULL) {
            $query .= " AND type NOT IN (";
            $tyarr = separateStringBy($excltypes,",",NULL,TRUE);
            for($k=0;$k<count($tyarr);$k++) {
               if($k>0) $query .= ",";
               $query .= "'".strtolower($tyarr[$k])."'";
            }
            $query .= ")";
         }
         
         $query .= " LIMIT 0,10;";
         
         if($testing) print "<br>query ".$i.": ".$query;
         
         $results = $dbi->queryGetResults($query);
         
         if($testing) {
            print "<br><br>results:<br>\n";
            print_r($results);
            print "<br>\n";
         }
         
         if($results==NULL || count($results)<1) {
            break;
         } else {
            $bestresults = $results;
            $i = $i - 1;
         }
      }
      
      $start = "";
      if($i>=0) {
         for($j=0;$j<=$i;$j++) {
            if($j>0) $start .= " ";
            $start .= $prarr[$j];
         }
      }
      
      $ans = array();
      for($j=0;$j<count($bestresults);$j++) {
         $ans[] = $bestresults[$j]['word'];
         if($j>20) break;
      }
      $results = array();
      $results['start'] = $start;
      $results['ans'] = $ans;
      return $results;
   }

   // given an index, find a phrase that yeilds the most results
   function buildQueryIndexWD($wd_id,$phrase,$printdebug=FALSE){
      $dbi = new MYSQLAccess();
      $prarr = separateStringBy($phrase," ",NULL,TRUE);
      $best_i = 0;
      $best_j = 0;
      $bestresults = NULL;
      $beststr = NULL;
      $str = "";
      
      // i represents the starting position of strings to compare
      for ($i=0;$i<count($prarr);$i++) {
            
         // j represents the number of words (chunks) to include in search
         for($j=(count($prarr)-$i);$j>0;$j--) {
            $str = "";
            
            // k represents position of the total chunks to add to string
            for($k=0;$k<$j;$k++) $str .= $prarr[($i+$k)]." ";
            
            $query = "SELECT * FROM wdindex_".$wd_id." WHERE ";
            $query .= getShortnameSQLStatement("word",$str,TRUE);
            $results = $dbi->queryGetResults($query);
            // Check if anything is found, and if it includes the most words
            if($results!=NULL && count($results)>0) {
               if($j>$best_j) {
                  $best_i = $i;
                  $best_j = $j;
                  $bestresults = $results;
                  $beststr = trim($str);
               }
            }
         }
      }
      
      $searchresults = array();
      if($best_j==0) {
         // not a single word was found in the dictionary
         $temp = array();
         $temp['i'] = 0;
         $temp['j'] = 0;
         $temp['results'] = NULL;
         $temp['phrase'] = $phrase;
         $searchresults[] = $temp;
      } else {
         $temp = array();
         $temp['i'] = $best_i;
         $temp['j'] = $best_j;
         $temp['results'] = $bestresults;
         $temp['phrase'] = $beststr;
         $searchresults[] = $temp;
         
         //Check words at the beginning!
         if($best_i>0) {
            $str = "";
            for($i=0;$i<$best_i;$i++) $str .= $prarr[$i]." ";
            $temp = $this->buildQueryIndexWD($wd_id,$str);
            for($i=0;$i<count($temp);$i++) $searchresults[] = $temp[$i];
         }
         
         //Check words at the end!
         if(($best_j + $best_i) < count($prarr)) {
            $str = "";
            for($i=($best_i+$best_j);$i<count($prarr);$i++) $str .= $prarr[$i]." ";
            $temp = $this->buildQueryIndexWD($wd_id,$str);
            for($i=0;$i<count($temp);$i++) $searchresults[] = $temp[$i];
         }
      }
      
      return $searchresults;
   }
   //------- END - SEARCH INDEXING --------------------
   

   function addField($wd_id, $parent_s, $field_id, $label, $text, $field_type, $sequence, $privacy, $header=NULL, $defaultval=NULL, $required=NULL, $srchfld=NULL, $pub=FALSE, $notes=NULL, $filterfld=NULL, $style=NULL, $map=NULL) {
      $dbi = new MYSQLAccess();
      if ($field_type==NULL) $field_type="SPACER";
      
      $this->checkAndCreateWD($wd_id,$pub);
      $createType = $this->getCreateType($field_type);

      if ($pub) {
         $check_query = "show columns from wd_".$wd_id."_pub like '".$field_id."'";
         $results = $dbi->queryGetResults($check_query);
         if ($results==NULL || count($results)<1) {
            $query = "ALTER TABLE wd_".$wd_id."_pub ADD COLUMN ".$field_id." ".$createType.";";
            $dbi->update($query);
         }
      } else {
         if ($field_id==NULL) $field_id = $this->getNextFieldID($wd_id);
         if ($header==NULL) $header = 0;
         if ($required==NULL) $required = 0;
         if ($srchfld==NULL) $srchfld = 0;
         if ($filterfld==NULL) $filterfld = 0;
         if ($privacy==NULL) $privacy = 0;
         $check_query = "show columns from wd_".$wd_id." like '".$field_id."'";
         $results = $dbi->queryGetResults($check_query);
         if ($results==NULL || count($results)<1) {
            $query = "ALTER TABLE wd_".$wd_id." ADD COLUMN ".$field_id." ".$createType.";";
            $dbi->update($query);
            $query = "INSERT INTO wd_fields (field_id,parent_s,sequence,wd_id,label,field_type,question,privacy,header,defaultval,required,srchfld,notes,filterfld,stylecss,map) VALUES ('".$field_id."', ".$parent_s.", '".$sequence."', ".$wd_id.", '".$label."', '".$field_type."', '".$text."', '".$privacy."',".$header.",'".$defaultval."',".$required.",".$srchfld.",'".convertString($notes)."',".$filterfld.",'".convertString($style)."','".convertString($map)."');";
            $dbi->update($query);
         }

         $query = "show tables like 'wd_".$wd_id."_pub';";
         $results = $dbi->queryGetResults($query);
         if ($results!=NULL && count($results)>0) {
            $this->checkPubTable($wd_id);
         }

      }
      return $field_id;
   }
   
   function getCreateType($field_type) {
      $createType = "TEXT DEFAULT NULL";
      
      if (0==strcmp($field_type,"INT")) $createType = "INT(11) DEFAULT NULL";
      else if (0==strcmp($field_type,"LIKERT")) $createType = "VARCHAR(128) DEFAULT NULL";
      else if (0==strcmp($field_type,"DEC") || 0==strcmp($field_type,"MONEY")) $createType = "FLOAT(16,8) DEFAULT NULL";
      else if (0==strcmp($field_type,"AGE")) $createType = "VARCHAR(16) DEFAULT NULL";
      else if (0==strcmp($field_type,"DATE")) $createType = "VARCHAR(16) DEFAULT NULL";
      else if (0==strcmp($field_type,"DATETIME")) $createType = "VARCHAR(64) DEFAULT NULL";
      else if (0==strcmp($field_type,"SNGLCHKBX")) $createType = "VARCHAR(8) DEFAULT NULL";
      else if (0==strcmp($field_type,"FOREIGNTDD")) $createType = "INT(11) DEFAULT NULL";
      else if (0==strcmp($field_type,"SPACER") || 0==strcmp($field_type,"INFO")) $createType = "VARCHAR(32) DEFAULT NULL";
      
      return $createType;
   }

   function checkPubTable($wd_id){
      $dbi = new MYSQLAccess();

      $this->checkAndCreateWD($wd_id,TRUE);

      $query = "describe wd_".$wd_id;
      $results = $dbi->queryGetResults($query);
      
      $query = "describe wd_".$wd_id."_pub";
      $results_pub = $dbi->queryGetResults($query);
      $fields = array();
      for ($i=0;$i<count($results);$i++) $fields[$results[$i]['Field']] = TRUE;
      for ($i=0;$i<count($results_pub);$i++) $fields[$results_pub[$i]['Field']] = FALSE;

      for ($i=0;$i<count($results);$i++) {
         if ($fields[$results[$i]['Field']]) {
            $query = "ALTER TABLE wd_".$wd_id."_pub ADD COLUMN ".$results[$i]['Field']." ".$results[$i]['Type']." DEFAULT NULL;";
            $dbi->update($query);
         }
      }
   }

   function updateField ($wd_id, $parent_s, $field_id, $label, $text, $type, $sequence, $privacy, $header=0, $defaultval=NULL, $required=0, $srchfld=0, $notes=NULL, $filterfld=0, $style=NULL, $map=NULL, $disa=NULL, $hide=NULL) {
      if ($type==NULL) $type="SPACER";
      if ($wd_id==NULL || $field_id==NULL) return FALSE;
      if ($parent_s==NULL) $parent_s = 0;
      if ($header==NULL) $header = 0;
      if ($required==NULL) $required = 0;
      if ($srchfld==NULL) $srchfld = 0;
      if ($filterfld==NULL) $filterfld = 0;
      if ($sequence==NULL) $sequence=0;
      if ($disa==NULL) $disa=0;
      if ($hide==NULL) $hide=0;
      if ($label==NULL) $label = "label";
      $dbi = new MYSQLAccess();
      
      $query = "UPDATE wd_fields SET label='".$label."'";
      $query .= ", parent_s=".$parent_s;
      $query .= ", field_type='".$type."'";
      $query .= ", question='".$text."'";
      $query .= ", sequence=".$sequence;
      $query .= ", privacy=".$privacy;
      $query .= ", header=".$header;
      $query .= ", required=".$required;
      $query .= ", srchfld=".$srchfld;
      $query .= ", filterfld=".$filterfld;
      $query .= ", defaultval='".$defaultval."'";
      $query .= ", notes='".convertString($notes)."'";
      $query .= ", stylecss='".convertString($style)."'";
      $query .= ", map='".convertString($map)."'";
      $query .= ", disa=".$disa;
      $query .= ", hide=".$hide;
      $query .= " WHERE field_id='".$field_id."' AND wd_id=".$wd_id.";";
      $dbi->update($query);
      
      $createType = $this->getCreateType($type);
      $query = "ALTER TABLE wd_".$wd_id." change ".$field_id." ".$field_id." ".$createType.";";
      $dbi->update($query);

      $query = "show tables like 'wd_".$wd_id."_pub';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query = "ALTER TABLE wd_".$wd_id."_pub change ".$field_id." ".$field_id." ".$createType.";";
         $dbi->update($query);
      }

      return TRUE;
   }

   function updateFieldSequence ($wd_id, $field_id, $sequence) {
      if ($wd_id==NULL || $field_id==NULL) return FALSE;
      if ($sequence==NULL) $sequence=0;
      $dbi = new MYSQLAccess();
      $query = "UPDATE wd_fields SET sequence=".$sequence." WHERE field_id='".$field_id."' AND wd_id=".$wd_id.";";
      $dbi->update($query);
      return TRUE;
   }

   function deleteField ($wd_id, $field_id) {
      if ($wd_id==NULL || $field_id==NULL) return FALSE;
      $dbi = new MYSQLAccess();
      
      $query = "DELETE FROM wd_link WHERE wd_id1=".$wd_id." AND field_id='".$field_id."';";
      $dbi->update($query);

      $query = "DELETE FROM wd_rel WHERE wd_id=".$wd_id." AND (fid1='".$field_id."' OR fid2='".$field_id."');";
      $dbi->update($query);

      $query = "DELETE FROM wd_fields WHERE wd_id=".$wd_id." AND field_id='".$field_id."';";
      $dbi->update($query);

      $query="ALTER TABLE wd_".$wd_id." drop column ".$field_id.";";
      $dbi->update($query);

      $query = "show tables like 'wd_".$wd_id."_pub';";
      $results = $dbi->queryGetResults($query);
      if ($results != NULL && count($results)>0) {
         $query="ALTER TABLE wd_".$wd_id."_pub drop column ".$field_id.";";
         $dbi->update($query);
      }

      return TRUE;
   }
   
   function getFieldPositionGroups($wd_id) {
      $wd = $this->getWebData($wd_id);
      
      $dbi = new MYSQLAccess();
      $query = "SELECT DISTINCT groupname ";
      $query .= " FROM wd_fldpos ";
      $query .= " WHERE wd_id=".$wd['wd_id'];
      $results = $dbi->queryGetResults($query);
      return $results;
   }
   
   function findbestgroupname($wd_id,$groupname) {
      $wd = $this->getWebData($wd_id);
      $gns = $this->getFieldPositionGroups($wd['wd_id']);
      $n = 1;
      $goodname = false;
      $gn = $groupname;
      while(!$goodname) {
         $gn = $groupname;
         if($n>1) $gn .= "_".$n;
         $goodname = true;
         for($i=0;$i<count($gns);$i++) {
           if(0==strcmp(strtolower(trim(convertBack($gns[$i]['groupname']))),strtolower(trim(convertBack($gn))))) {
              $n++;
              $goodname = false;
              break;
           }
         }
      }
      return $gn;
   }
   
   function changeFieldPositionGroupName($wd_id,$from_groupname,$to_groupname=NULL) {
      $wd = $this->getWebData($wd_id);
      if ($to_groupname==NULL) $to_groupname = $from_groupname;
      $gn = $this->findbestgroupname($wd['wd_id'],$to_groupname);
      $query = "UPDATE wd_fldpos ";
      $query .= "SET groupname='".convertString($gn)."' ";
      $query .= "WHERE LOWER(groupname)='".strtolower(trim(convertString($from_groupname)))."' AND wd_id=".$wd['wd_id'].";";
      $dbi = new MYSQLAccess();
      $dbi->update($query);
   }
   
   function copyFieldPositions($wd_id,$from_groupname,$to_groupname=NULL) {
      $wd = $this->getWebData($wd_id);
      
      $dbi = new MYSQLAccess();
      $query = "SELECT * ";
      $query .= " FROM wd_fldpos ";
      $query .= " WHERE wd_id=".$wd['wd_id'];
      $query .= " AND LOWER(groupname)='".strtolower(convertString($from_groupname))."'";
      $results = $dbi->queryGetResults($query);
      if ($to_groupname==NULL) $to_groupname = $from_groupname;
      $gn = $this->findbestgroupname($wd['wd_id'],$to_groupname);
      for($i=0;$i<count($results);$i++){
         $results[$i]['groupname'] = convertBack($gn);
         unset($results[$i]['posid']);
      }
      $insquery = copyRowsInsert($results,'wd_fldpos');
      $dbi->insert($insquery);
   }
   
   function deleteFieldPositions($wd_id,$groupname) {
      $wd = $this->getWebData($wd_id);
      
      $dbi = new MYSQLAccess();
      $query = "DELETE";
      $query .= " FROM wd_fldpos ";
      $query .= " WHERE wd_id=".$wd['wd_id'];
      $query .= " AND LOWER(groupname)='".strtolower(convertString($groupname))."'";
      $results = $dbi->delete($query);
   }
   
   function getFieldPositions($wd_id,$groupname=NULL,$ignoredisabled=TRUE,$indexed=FALSE,$printstuff=FALSE) {
      $wd = $this->getWebData($wd_id);
      
      $dbi = new MYSQLAccess();
      $query = "SELECT p.*";
      $query .= ", f.parent_s";
      $query .= ", f.sequence";
      $query .= ", f.label";
      $query .= ", f.map";
      $query .= ", f.field_type";
      $query .= ", f.required";
      $query .= ", f.question";
      $query .= ", f.defaultval";
      $query .= ", f.required";
      $query .= ", f.stylecss";
      $query .= ", f.hide";
      $query .= ", f.header";
      $query .= ", f.privacy";
      $query .= ", f.filterfld";
      $query .= ", f.disa as disa2";
      $query .= " FROM wd_fldpos p, wd_fields f ";
      $query .= " WHERE p.field_id=f.field_id AND p.wd_id=f.wd_id ";
      $query .= " AND p.wd_id=".$wd['wd_id'];
      
      if($groupname!=NULL) $query .= " AND LOWER(p.groupname)='".strtolower(convertString($groupname))."'";
      else $query .= " AND LOWER(p.groupname) LIKE '%default%'";
      
      if($ignoredisabled) {
         $query .= " AND (p.disa IS NULL OR p.disa=0) ";
         $query .= " AND (f.disa IS NULL OR f.disa=0) ";
      }
      $query .= " ORDER BY f.sequence;";
      if($printstuff) print "<br>\nQuery: ".$query."<br>\n";
      $results = $dbi->queryGetResults($query);
      
      if($results==NULL || count($results)<1) {
         $results = $this->getAllFieldsSystem($wd['wd_id'],NULL,$ignoredisabled);
      }
      
      for($i=0;$i<count($results);$i++){
         $results[$i]['question'] = convertBack($results[$i]['question']);
         //$results[$i]['shortdescr'] = convertBack($results[$i]['shortdescr']);
         $results[$i]['qopts'] = $this->getdropdownoptions($results[$i]);
      }
      $ans = array();
      if($indexed) {
         $ans['allfields'] = $results;
         for($i=0;$i<count($results);$i++){
            $ans[$results[$i]['field_id']] = $results[$i];
            if($results[$i]['map']!=NULL) $ans[trim($results[$i]['map'])] = $results[$i];
            if($results[$i]['label']!=NULL) $ans[strtolower(trim($results[$i]['label']))] = $results[$i];
         }
      } else {
         $ans = $results;
      }
      return $ans;
   }
   
   function setFieldPositions($htmlonly=FALSE,$wd_id=NULL,$groupname=NULL) {
      if($groupname==NULL) $groupname = getParameter("groupname");
      if($wd_id==NULL) $wd_id = getParameter("wd_id");
      
      if($wd_id!=NULL && $groupname!=NULL) {
         $wd = $this->getWebData($wd_id);
         $fs = $this->getAllFieldsSystem($wd['wd_id']);
         
         //First makde changes/updates
         if(!$htmlonly) {
            $pos = $this->getFieldPositions($wd['wd_id'],$groupname,FALSE,TRUE);
            $dbi = new MYSQLAccess();
            $insstmnt = "INSERT INTO wd_fldpos (groupname,field_id,wd_id,leftpos,toppos,rightpos,bottompos,width,height,defval,notes,unit,disptype,params,json,disa,adminresp,statusind,instructions,subname,shortdescr,longdescr) VALUES ";
            $inscount = 0;
            for($i=0;$i<count($fs);$i++) {
               if(0!=strcmp($fs[$i]['field_type'],"SPACER") && 0!=strcmp($fs[$i]['field_type'],"INFO")) {
                  $left = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_left"))));
                  $top = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_top"))));
                  $right = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_right"))));
                  $bottom = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_bottom"))));
                  $width = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_width"))));
                  $height = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_height"))));
                  $unit = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_unit"))));
                  $disa = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_disa"))));
                  $defval = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_defval"))));
                  $notes = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_notes"))));
                  $params = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_params"))));
                  $json = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_json"))));
                  $disptype = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_disptype"))));
                  $adminresp = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_adminresp"))));
                  $statusind = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_statusind"))));
                  $instructions = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_instructions"))));
                  $subname = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_subname"))));
                  $shortdescr = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_shortdescr"))));
                  $longdescr = convertString(trim(convertBack(getParameter($fs[$i]['field_id']."_longdescr"))));
                  if(isset($pos[$fs[$i]['field_id']])) {
                     $query = "UPDATE wd_fldpos SET ";
                     $query .= "leftpos='".$left."'";
                     $query .= ", rightpos='".$right."'";
                     $query .= ", toppos='".$top."'";
                     $query .= ", bottompos='".$bottom."'";
                     $query .= ", width='".$width."'";
                     $query .= ", height='".$height."'";
                     $query .= ", disa='".$disa."'";
                     $query .= ", unit='".$unit."'";
                     $query .= ", disptype='".$disptype."'";
                     $query .= ", params='".$params."'";
                     $query .= ", json='".$json."'";
                     $query .= ", defval='".$defval."'";
                     $query .= ", notes='".$notes."'";
                     $query .= ", adminresp='".$adminresp."'";
                     $query .= ", statusind='".$statusind."'";
                     $query .= ", instructions='".$instructions."'";
                     $query .= ", subname='".$subname."'";
                     $query .= ", shortdescr='".$shortdescr."'";
                     $query .= ", longdescr='".$longdescr."'";
                     $query .= " WHERE posid=".$pos[$fs[$i]['field_id']]['posid'];
                     $dbi->update($query);
                  } else {
                     if($inscount>0) $insstmnt .= ", ";
                     $insstmnt .= " (";
                     $insstmnt .= "'".convertString($groupname)."'";
                     $insstmnt .= ",'".$fs[$i]['field_id']."'";
                     $insstmnt .= ",'".$wd['wd_id']."'";
                     $insstmnt .= ",'".$left."'";
                     $insstmnt .= ",'".$top."'";
                     $insstmnt .= ",'".$right."'";
                     $insstmnt .= ",'".$bottom."'";
                     $insstmnt .= ",'".$width."'";
                     $insstmnt .= ",'".$height."'";
                     $insstmnt .= ",'".$defval."'";
                     $insstmnt .= ",'".$notes."'";
                     $insstmnt .= ",'".$unit."'";
                     $insstmnt .= ",'".$disptype."'";
                     $insstmnt .= ",'".$params."'";
                     $insstmnt .= ",'".$json."'";
                     $insstmnt .= ",'".$disa."'";
                     $insstmnt .= ",'".$adminresp."'";
                     $insstmnt .= ",'".$statusind."'";
                     $insstmnt .= ",'".$instructions."'";
                     $insstmnt .= ",'".$subname."'";
                     $insstmnt .= ",'".$shortdescr."'";
                     $insstmnt .= ",'".$longdescr."'";
                     $insstmnt .= ")";
                     $inscount++;
                     //print "\n<!--".$insstmnt."-->\n";
                  }
               }
            }
            if($inscount>0) $dbi->insert($insstmnt);      
         }
         
         // Secondly, create an html form
         $html = "";
         $html .= "<div id=\"fldpos_groupname_div\">";
         //$html .= "Coordinate Group Name: <input type=\"text\" id=\"fldpos_groupname\" value=\"".$groupname."\">";
         $html .= "<input type=\"hidden\" name=\"wd_id\" id=\"fldpos_wd_id\" value=\"".$wd['wd_id']."\">";
         $html .= "<input type=\"hidden\" name=\"groupname\" id=\"fldpos_groupname\" value=\"".$groupname."\">";
         $html .= "jData Field Coordinates: ".$groupname;
         $html .= "</div>";
         $html .= "<div id=\"fldpos_table_div\">";
         $html .= "<table cellpadding=\"4\" cellspacing=\"1\" id=\"fldpos_table\">";
         $html .= "<tr valign=\"top\" style=\"background-color:#DDDDDD;font-weight:bold;\">";
         $html .= "<td></td>";
         $html .= "<td>Question</td>";
         $html .= "<td>Unit</td>";
         $html .= "<td>Owner</td>";
         $html .= "<td>Status</td>";
         $html .= "<td>Instructions</td>";
         $html .= "<td>Left</td>";
         $html .= "<td>Top</td>";
         //$html .= "<td>Right</td>";
         //$html .= "<td>Bottom</td>";
         $html .= "<td>Width</td>";
         $html .= "<td>Height</td>";
         $html .= "<td>Display Name</td>";
         $html .= "<td>Sub Title</td>";
         $html .= "<td>Type</td>";
         $html .= "<td>Parameters</td>";
         $html .= "<td>JSON</td>";
         $html .= "<td>Default</td>";
         $html .= "<td>Notes</td>";
         $html .= "</tr>";
         
         $pos = $this->getFieldPositions($wd['wd_id'],$groupname,FALSE,TRUE);
         for($i=0;$i<count($fs);$i++) {
            if(0!=strcmp($fs[$i]['field_type'],"SPACER") && 0!=strcmp($fs[$i]['field_type'],"INFO")) {
               $bg = "#FFFFFF";
               if(($i%2)==1) $bg = "#E0E8EE";
               $html .= "<tr valign=\"top\" style=\"background-color:".$bg.";\">";
               
               $discolor = '#88FF88';
               if($pos[$fs[$i]['field_id']]['disa']==1) $discolor = '#FF8888';
               $html .= "<td style=\"background-color:".$discolor.";\">";
               $html .= "<select name=\"".$fs[$i]['field_id']."_disa\" id=\"".$fs[$i]['field_id']."_disa\">";
               $html .= "<option value=\"0\">Enabled</option>";
               $html .= "<option value=\"1\"";
               if($pos[$fs[$i]['field_id']]['disa']==1) $html .= " SELECTED";
               $html .= ">Disabled</option>";
               $html .= "</select>";
               $html .= "</td>";
               $html .= "<td>".$fs[$i]['field_id'];
               if($fs[$i]['map']!=NULL) $html .= " (".$fs[$i]['map'].")";
               else if($fs[$i]['label']!=NULL) $html .= " (".$fs[$i]['label'].")";
               $html .= "</td>";
               $html .= "<td>";
               $html .= "<select name=\"".$fs[$i]['field_id']."_unit\" id=\"".$fs[$i]['field_id']."_unit\">";
               $html .= "<option value=\"in\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['unit'],"in")) $html .= " SELECTED";
               $html .= ">Inches</option>";
               $html .= "<option value=\"pt\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['unit'],"pt")) $html .= " SELECTED";
               $html .= ">Points</option>";
               $html .= "<option value=\"px\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['unit'],"px")) $html .= " SELECTED";
               $html .= ">Pixels</option>";
               $html .= "</select>";
               $html .= "</td>";            
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_adminresp\" id=\"".$fs[$i]['field_id']."_adminresp\" value=\"".$pos[$fs[$i]['field_id']]['adminresp']."\" style=\"font-size:10px;width:70px;\"></td>";
               
               $bgcolor = "#FFFFFF";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"NEW") || $pos[$fs[$i]['field_id']]['statusind']==NULL) $bgcolor = "#99DD99";
               else if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"IP")) $bgcolor = "#9999DD";
               else if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"HOLD")) $bgcolor = "#DDDD99";
               else if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"ATTN")) $bgcolor = "#DD9999";
               
               $html .= "<td style=\"background-color:".$bgcolor.";\">";
               $html .= "<select name=\"".$fs[$i]['field_id']."_statusind\" id=\"".$fs[$i]['field_id']."_statusind\">";
               $html .= "<option value=\"NEW\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"NEW")) $html .= " SELECTED";
               $html .= ">New</option>";
               $html .= "<option value=\"IP\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"IP")) $html .= " SELECTED";
               $html .= ">In Progress</option>";
               $html .= "<option value=\"HOLD\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"HOLD")) $html .= " SELECTED";
               $html .= ">Waiting Feedback</option>";
               $html .= "<option value=\"ATTN\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"ATTN")) $html .= " SELECTED";
               $html .= ">Attention</option>";
               $html .= "<option value=\"FINISHED\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['statusind'],"FINISHED")) $html .= " SELECTED";
               $html .= ">Finished</option>";
               $html .= "</select>";
               $html .= "</td>"; 
               
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_instructions\" id=\"".$fs[$i]['field_id']."_instructions\" style=\"font-size:8px;width:80px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['instructions'])."</textarea></td>";
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_left\" id=\"".$fs[$i]['field_id']."_left\" value=\"".$pos[$fs[$i]['field_id']]['leftpos']."\" style=\"font-size:10px;width:50px;\"></td>";
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_top\" id=\"".$fs[$i]['field_id']."_top\" value=\"".$pos[$fs[$i]['field_id']]['toppos']."\" style=\"font-size:10px;width:50px;\"></td>";
               //$html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_right\" id=\"".$fs[$i]['field_id']."_right\" value=\"".$pos[$fs[$i]['field_id']]['rightpos']."\" style=\"font-size:10px;width:70px;\"></td>";
               //$html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_bottom\" id=\"".$fs[$i]['field_id']."_bottom\" value=\"".$pos[$fs[$i]['field_id']]['bottompos']."\" style=\"font-size:10px;width:70px;\"></td>";
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_width\" id=\"".$fs[$i]['field_id']."_width\" value=\"".$pos[$fs[$i]['field_id']]['width']."\" style=\"font-size:10px;width:50px;\"></td>";
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_height\" id=\"".$fs[$i]['field_id']."_height\" value=\"".$pos[$fs[$i]['field_id']]['height']."\" style=\"font-size:10px;width:50px;\"></td>";
               $html .= "<td><input type=\"text\" name=\"".$fs[$i]['field_id']."_subname\" id=\"".$fs[$i]['field_id']."_subname\" value=\"".convertBack($pos[$fs[$i]['field_id']]['subname'])."\" style=\"font-size:10px;width:70px;\"></td>";
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_shortdescr\" id=\"".$fs[$i]['field_id']."_shortdescr\" style=\"font-size:10px;width:120px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['shortdescr'])."</textarea></td>";
               $html .= "<td>";
               $html .= "<select name=\"".$fs[$i]['field_id']."_disptype\" id=\"".$fs[$i]['field_id']."_disptype\">";
               $html .= "<option value=\"TEXT\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"TEXT")) $html .= " SELECTED";
               $html .= ">Text</option>";
               $html .= "<option value=\"TEXTBLOCK\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"TEXTBLOCK")) $html .= " SELECTED";
               $html .= ">Text Block</option>";
               $html .= "<option value=\"DROPDOWN\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"DROPDOWN")) $html .= " SELECTED";
               $html .= ">Dropdown Inline</option>";
               $html .= "<option value=\"DROPDOWNEXT\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"DROPDOWNEXT")) $html .= " SELECTED";
               $html .= ">Dropdown On Left</option>";
               $html .= "<option value=\"CHECKBOX\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"CHECKBOX")) $html .= " SELECTED";
               $html .= ">Checkboxes</option>";
               $html .= "<option value=\"CHECKBOXLEFT\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"CHECKBOXLEFT")) $html .= " SELECTED";
               $html .= ">Checkboxes (LEFT)</option>";

               $html .= "<option value=\"INFORMATION\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"INFORMATION")) $html .= " SELECTED";
               $html .= ">Information</option>";

               $html .= "<option value=\"GRID\"";
               if(0==strcmp($pos[$fs[$i]['field_id']]['disptype'],"GRID")) $html .= " SELECTED";
               $html .= ">Multiple Images</option>";
               $html .= "</select>";
               $html .= "</td>";
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_params\" id=\"".$fs[$i]['field_id']."_params\" style=\"font-size:8px;width:140px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['params'])."</textarea></td>";
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_json\" id=\"".$fs[$i]['field_id']."_json\" style=\"font-size:8px;width:140px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['json'])."</textarea></td>";
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_defval\" id=\"".$fs[$i]['field_id']."_defval\" style=\"font-size:8px;width:100px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['defval'])."</textarea></td>";
               $html .= "<td><textarea name=\"".$fs[$i]['field_id']."_notes\" id=\"".$fs[$i]['field_id']."_notes\" style=\"font-size:8px;width:100px;height:35px;\">".convertBack($pos[$fs[$i]['field_id']]['notes'])."</textarea></td>";
               $html .= "</tr>";
            }
         }
         $html .= "</table>";
         $html .= "</div>";
      }
      
      return $html;
   }

   function getField($wd_id, $field_id) {
      if ($wd_id == NULL || $field_id==NULL) return NULL;
      if(!is_numeric($wd_id)) {
         $wd = $this->getWebData($wd_id);
         $wd_id = $wd['wd_id'];
      }      
      $query = "SELECT * FROM wd_fields WHERE ";
      $query .= "(field_id='".$field_id."' OR LOWER(map)='".strtolower($field_id)."' OR LOWER(label)='".strtolower($field_id)."') ";
      $query .= "AND wd_id='".$wd_id."';";
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($query);
      if(trim($results[0]['map'])==NULL) $results[0]['map'] = substr(removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[0]['label']))))),0,63);
      return $results[0];
   }

   // Add for field answers affecting visibility of other questions
   function newFieldRel($wd_id,$rel_type,$fid1,$fid2,$f1value){
      if ($wd_id==NULL || $rel_type==NULL || $fid1==NULL) return NULL;
      unset($_SESSION['allrelsindexed_'.$wd_id]);
      $dbi = new MYSQLAccess();
      $sql = "SELECT * FROM wd_rel WHERE wd_id=".$wd_id." AND rel_type='".$rel_type."' AND fid1='".$fid1."' AND fid2='".$fid2."' AND f1value='".$f1value."';";
      $results = $dbi->queryGetResults($sql);
      $retid = NULL;
      if ($results==NULL || count($results)<1) {
          $sql = "INSERT INTO wd_rel (wd_id,rel_type,fid1,fid2,f1value) VALUES (".$wd_id.",'".$rel_type."','".$fid1."','".$fid2."','".$f1value."');";
          $retid = $dbi->insertGetValue($sql);
      }
      return $retid;
   }

   // Add for field answers affecting visibility of other sections
   function newFieldRelSect($wd_id,$fid1,$section,$f1value){
      if ($wd_id==NULL || $fid1==NULL) return NULL;
      unset($_SESSION['allrelsindexed_'.$wd_id]);
      $this->newFieldRel($wd_id,"SECTID",$fid1,$section,$f1value);
   }

   function removeFieldRel($rel_id){
      if ($rel_id==NULL) return FALSE;
      unset($_SESSION['allrelsindexed_'.$wd_id]);
      $sql = "DELETE FROM wd_rel WHERE rel_id=".$rel_id.";";
      $dbi = new MYSQLAccess();
      $dbi->delete($sql);
      return TRUE;
   }

   function getFieldRelsIndexed($wd_id){
      $allrelsindexed = NULL;
      if (isset($_SESSION['allrelsindexed_'.$wd_id])) {
         $allrelsindexed = $_SESSION['allrelsindexed_'.$wd_id];
      } else {
         //***chj*** saving cycles on fieldrels
         $allrels = $this->getAllFieldRels($wd_id);
         $allrelsindexed = array();
         $allrelsindexed['field1'] = array();
         $allrelsindexed['field2'] = array();
         $allrelsindexed['section'] = array();
         $allrelsindexed['n_field1'] = array();
         $allrelsindexed['n_field2'] = array();
         $allrelsindexed['n_section'] = array();
         for ($i=0;$i<count($allrels);$i++) {
            if(0==strcmp($allrels[$i]['rel_type'],"VALUE")) {
               if (!isset($allrelsindexed['field1'][$allrels[$i]['fid1']])) $allrelsindexed['field1'][$allrels[$i]['fid1']] = array();
               $allrelsindexed['field1'][$allrels[$i]['fid1']][] = $allrels[$i];
               if (!isset($allrelsindexed['field2'][$allrels[$i]['fid2']])) $allrelsindexed['field2'][$allrels[$i]['fid2']] = array();
               $allrelsindexed['field2'][$allrels[$i]['fid2']][] = $allrels[$i];
            } else if(0==strcmp($allrels[$i]['rel_type'],"SECTID")) {
               if (!isset($allrelsindexed['section'][$allrels[$i]['fid1']])) $allrelsindexed['section'][$allrels[$i]['fid1']] = array();
               $allrelsindexed['section'][$allrels[$i]['fid1']][] = $allrels[$i];
            } else if(0==strcmp($allrels[$i]['rel_type'],"N_VALUE")) {
               if (!isset($allrelsindexed['n_field1'][$allrels[$i]['fid1']])) $allrelsindexed['n_field1'][$allrels[$i]['fid1']] = array();
               $allrelsindexed['n_field1'][$allrels[$i]['fid1']][] = $allrels[$i];
               if (!isset($allrelsindexed['n_field2'][$allrels[$i]['fid2']])) $allrelsindexed['n_field2'][$allrels[$i]['fid2']] = array();
               $allrelsindexed['n_field2'][$allrels[$i]['fid2']][] = $allrels[$i];
            } else if(0==strcmp($allrels[$i]['rel_type'],"N_SECTID")) {
               if (!isset($allrelsindexed['n_section'][$allrels[$i]['fid1']])) $allrelsindexed['n_section'][$allrels[$i]['fid1']] = array();
               $allrelsindexed['n_section'][$allrels[$i]['fid1']][] = $allrels[$i];
            } else if(0==strcmp($allrels[$i]['rel_type'],"SECTVALUE")) {
               $fields = $this->getFieldsRecur($wd_id,$allrels[$i]['fid2']);
               for ($j=0; $j<count($fields); $j++) {
                  $temprel = array();
                  $temprel['rel_id'] = 0;
                  $temprel['fid1'] = $allrels[$i]['fid1'];
                  $temprel['fid2'] = $fields[$j]['field_id'];
                  $temprel['wd_id'] = $allrels[$i]['wd_id'];
                  $temprel['f1value'] = $allrels[$i]['f1value'];
                  $temprel['rel_type'] = "VALUE";
                  
                  if (!isset($allrelsindexed['field1'][$temprel['fid1']])) $allrelsindexed['field1'][$temprel['fid1']] = array();
                  $allrelsindexed['field1'][$temprel['fid1']][] = $temprel;
                  if (!isset($allrelsindexed['field2'][$temprel['fid2']])) $allrelsindexed['field2'][$temprel['fid2']] = array();
                  $allrelsindexed['field2'][$temprel['fid2']][] = $temprel;
               }
            }
         }
         $_SESSION['allrelsindexed_'.$wd_id] = $allrelsindexed;
      }
      return $allrelsindexed;
   }

   function getAllFieldRels($wd_id){
      $sql = "SELECT * FROM wd_rel WHERE wd_id=".$wd_id.";";
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($sql);
      return $results;         
   }

   function getNakedField1Rel($wd_id,$fid1,$rel_type=NULL,$field2instead=FALSE){
      $sql = "SELECT * FROM wd_rel ";
      $sql .= "WHERE wd_id=".$wd_id;
      
      if($field2instead) $sql .= " AND fid2='".$fid1."' ";
      else $sql .= " AND fid1='".$fid1."' ";
      
      if($rel_type!=NULL) $sql .= "AND rel_type='".$rel_type."' ";
      $sql .= "ORDER BY fid1,f1value,fid2;";
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($sql);
      return $results;         
   }

   function getField1Rel($wd_id,$fid1){
      $results = $this->getSpecificFieldRel($wd_id,$fid1,"field1");
      return $results;         
   }

   function getSectionRel($wd_id,$fid){
      $results = $this->getSpecificFieldRel($wd_id,$fid,"section");
      return $results;         
   }

   function getField2Rel($wd_id,$fid2){
      $results = $this->getSpecificFieldRel($wd_id,$fid2,"field2");
      return $results;         
   }

   function getNegativeField1Rel($wd_id,$fid1){
      $results = $this->getSpecificFieldRel($wd_id,$fid1,"n_field1");
      return $results;         
   }

   function getNegativeSectionRel($wd_id,$fid){
      $results = $this->getSpecificFieldRel($wd_id,$fid,"n_section");
      return $results;         
   }

   function getNegativeField2Rel($wd_id,$fid2){
      $results = $this->getSpecificFieldRel($wd_id,$fid2,"n_field2");
      return $results;         
   }

   function getSpecificFieldRel($wd_id,$fid,$listid){
      $results = NULL;
      $allrels = $this->getFieldRelsIndexed($wd_id);
      if(isset($allrels[$listid]) && isset($allrels[$listid][$fid])) $results = $allrels[$listid][$fid];
      return $results;         
   }

   function addRow($wd_id, $userid=NULL, $serialnumber=NULL, $lastupdateby=NULL, $externalid=NULL) {
      if ($userid==NULL) $userid=0;
      $seed = $userid.date("z");
      $origemail = getRandomNum($seed);
      $temp = null;
      $temp = $this->getCodedRow($wd_id,$origemail);
      while ($temp != null && $temp['wd_row_id']!=NULL) {
         $seed .= date("u").date("s");
         $origemail = getRandomNum($seed);
         $temp = null;
         $temp = $this->getCodedRow($wd_id,$origemail);
      }
      $query = "INSERT INTO wd_".$wd_id." (created, lastupdate, dbmode, userid, origemail";
      if ($serialnumber!=NULL) $query .= ", serialnumber";
      if ($externalid!=NULL) $query .= ", externalid";
      $query .= ", lastupdateby";
      $query .= ") VALUES (NOW(),NOW(),'NEW',".$userid.", '".$origemail."'";
      if ($serialnumber!=NULL) $query .= ", '".$serialnumber."'";
      if ($externalid!=NULL) $query .= ", '".$externalid."'";
      
      if($lastupdateby==NULL) $lastupdateby = substr($_SESSION['s_user']['emailAddress'],0,8);
      if($lastupdateby==NULL) $lastupdateby = "UNKNOWN";
      $lastupdateby .= " ".date("Y-m-d H:i:s");
      $query .= ", '".$lastupdateby."'";
      $query .= ");";
      $dbi = new MYSQLAccess();
      $spid = $dbi->insertGetValue($query);
      return $spid;
   }
   
   function removeRowByName($name,$wd_row_id){
      $wdata = $this->getWebData($name);
      if ($wdata==NULL) return FALSE;
      $this->removeRow($wdata['wd_id'], $wd_row_id);
      return TRUE;
   }
     
   function removeRow($wd_id, $wd_row_id,$forreal=FALSE,$lastupdateby=NULL) {
      $dbi = new MYSQLAccess();
      if ($forreal) {
         $query = "DELETE FROM wd_".$wd_id." WHERE wd_row_id=".$wd_row_id;
         $dbi->delete($query);

         $query = "show tables like 'wd_".$wd_id."_pub';";
         $results = $dbi->queryGetResults($query);
         if ($results != NULL && count($results)>0) {
            $query = "DELETE FROM wd_".$wd_id."_pub WHERE wd_row_id=".$wd_row_id;
            $dbi->delete($query);               
         }
         
         $query = "DELETE FROM wd_link WHERE wd_id1=".$wd_id." AND wd_row_id1=".$wd_row_id.";";
         $dbi->delete($query);
         
         $query = "DELETE FROM wd_link WHERE wd_id2=".$wd_id." AND wd_row_id2=".$wd_row_id.";";
         $dbi->delete($query);
      } else {
        $query = "UPDATE wd_".$wd_id." SET lastupdate=NOW(), dbmode='DELETED'";
        
        if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
        if($lastupdateby==NULL) $lastupdateby = "0";
        $lastupdateby .= " ".date("Y-m-d H:i:s");
        $query .= ", lastupdateby=SUBSTR(CONCAT('".$lastupdateby.", ',IFNULL(lastupdateby,' ')),1,2048)";
        
        $query .= " WHERE wd_row_id=".$wd_row_id.";";
        $dbi->update($query);               
      }
   }

   function updateFieldValue($wd_id,$wd_row_id,$fld,$value,$update=TRUE,$convert=TRUE,$lastupdateby=NULL) {
     if ($wd_row_id==NULL || $wd_id==NULL || $fld==NULL) return FALSE;
     $names = array();
     $values = array();
     $names[] = $fld;
     $values[] = $value;
     return $this->updateMultipleValues($wd_id,$names,$values,$wd_row_id,NULL,$lastupdateby,$convert,$update);
   }
        
   function updateMultipleValues($wd_id,$names,$values,$wd_row_id,$userid=NULL,$lastupdateby=NULL,$convert=TRUE,$update=TRUE) {
      if ($wd_id==NULL || ($wd_row_id==NULL && $userid==NULL)) return FALSE;
      if (count($names)<1 || count($names)!=count($values)) return FALSE;
      
      $obj = $this->getWebData($wd_id);
      $wd_id = $obj['wd_id'];
      $qs = $this->getFieldLabels($wd_id,TRUE,TRUE);
      
      $query1 = "UPDATE wd_".$wd_id." SET lastupdate=NOW()";
      if($update) {
         if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
         if($lastupdateby==NULL) $lastupdateby = "0";
         $lastupdateby .= " ".date("Y-m-d H:i:s");
         
         $query1 .= ", dbmode='UPDATED'";
         $query1 .= ", lastupdateby=SUBSTR(CONCAT('".$lastupdateby.", ',IFNULL(lastupdateby,' ')),1,2048)";
      }
           
      for($i=0;$i<count($names);$i++){
        $name = $qs[$names[$i]];
        if(0==strcmp(substr($names[$i],0,16),"originalwdfield_")) $name=substr($names[$i],16);
        
        $value = $values[$i];
        if ($convert) $value = convertString($values[$i]);
        
        $query1 .= ", ".$name."='".$value."'";
      }
      
      if($wd_row_id!=NULL) $query1 .= " WHERE wd_row_id=".$wd_row_id.";";
      else $query1 .= " WHERE userid=".$userid.";";
      
      $dbi = new MYSQLAccess();
      
      //print "\n\n<!-- ***chj***\nquery:\n".$query1."\n-->\n";
      
      $dbi->update($query1);
      return TRUE;
   }

  function findByExternalId($wd_id, $externalid){
     $resp = $this->getRows($wd_id,NULL,null,null,FALSE,NULL,FALSE,FALSE,FALSE,TRUE,FALSE,NULL,NULL,$externalid);
     return $resp['results'];
  }

   function printShort($survey) {
      print "WebsiteData ".$survey['wd_id'].": ".$survey['name']." - ".$survey['info']." private: ".$survey['privatesrvy'].", adminemail:".$survey['adminemail']."<BR>\n";
   }

   function copySection($wd_id, $section, $newsection=NULL) {
      if($wd_id==NULL) return FALSE;
      
      if(is_numeric($section)) $s = $this->getSection($wd_id,$section);
      else if(is_array($section)) $s = $section;
      else return FALSE;
      
      if($newsection==NULL) $newsection = $s['parent_s'];
      $ns = $this->addSection($wd_id, $newsection, $s['sec_type'], $s['label']." - Copy", ($s['sequence'] + 1), $s['dyna'], $s['question'], $s['param1'], $s['param2'], $s['param3'], $s['param4'], $s['param5'], $s['param6']);

      $questions = $this->getFields($wd_id, $s['section'], $admin);
      for ($j=0; $j<count($questions); $j++) {
         $q = $questions[$j];
         $this->addField($wd_id, $ns, NULL, $q['label'], $q['question'], $q['field_type'], $q['sequence'], $q['privacy'], $q['header'], $q['defaultval'], $q['required'], $q['srchfld'], FALSE, $q['notes'], $q['filterfld'], $q['stylecss'], $q['map']);               
      }

      $sections = $this->getDataSections($wd_id,$s['section']);
      for ($i=0; $i<count($sections); $i++) {
         $this->copySection($wd_id, $sections[$i], $ns);
      }
      
      $flds1 = $this->getFieldsRecur($wd_id,$s['section']);            
      $flds2 = $this->getFieldsRecur($wd_id,$ns);            
      $fldmap = array();
      $fldlist = "";
      for($i=0;$i<count($flds1);$i++) {
         $fldmap[$flds1[$i]['field_id']] = $flds2[$i]['field_id'];
         if($i>0) $fldlist .= ", ";
         $fldlist .= "'".$flds1[$i]['field_id']."'";
      }
      
      $query = "SELECT * FROM wd_rel WHERE wd_id=".$wd_id;
      $query .= " AND fid1 IN (".$fldlist.")";
      $query .= " AND fid2 IN (".$fldlist.")";
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($query);
      
      for($i=0;$i<count($results);$i++){
         $this->newFieldRel($wd_id,$results[$i]['rel_type'],$fldmap[$results[$i]['fid1']],$fldmap[$results[$i]['fid2']],$results[$i]['f1value']);
      }

      return TRUE;
   }

   function addSection($wd_id, $parent_s=NULL, $sec_type=NULL, $label=NULL, $sequence=NULL, $dyna=null, $question=null, $param1=0, $param2=0, $param3=0, $param4=0, $param5=NULL, $param6=NULL) {
     return $this->updateSection($wd_id,NULL,$parent_s,$sec_type,$label,$sequence,$dyna,$question,$param1,$param2,$param3,$param4,$param5,$param6);
   }
        
   function updateSection ($wd_id, $section=NULL, $parent_s=NULL, $sec_type=NULL, $label=NULL, $sequence=1, $dyna=null, $question=null, $param1=0, $param2=0, $param3=0, $param4=0, $param5=NULL, $param6=NULL) {
      if($wd_id==NULL || !is_numeric($wd_id)) return NULL;
      
      $dbi = new MYSQLAccess();
      if ($parent_s==NULL) $parent_s = -1;
      if ($dyna == null) $dyna=0;
      if ($sequence==NULL) $sequence=0;
      if ($param1==NULL || 0==strcmp(trim($param1),"")) $param1 = 0;
      if ($param2==NULL || 0==strcmp(trim($param2),"")) $param2 = 0;
      if ($param3==NULL || 0==strcmp(trim($param3),"")) $param3 = 0;
      if ($param4==NULL || 0==strcmp(trim($param4),"")) $param4 = 0;
      
      $query = "set label='".convertString($label)."', ";
      $query .= "sequence=".$sequence.", ";
      $query .= "dyna=".$dyna.", ";
      $query .= "question='".convertString($question)."', ";
      $query .= "parent_s=".$parent_s.", ";
      $query .= "sec_type='".$sec_type."', ";
      $query .= "param1=".$param1.", ";
      $query .= "param2=".$param2.", ";
      $query .= "param3=".$param3.", ";
      $query .= "param4=".$param4.", ";
      $query .= "param5='".convertString($param5)."', ";
      $query .= "param6='".convertString($param6)."' ";
      
      if($section==NULL) {
        $query = "INSERT INTO wd_sections ".$query.", wd_id=".$wd_id;
        $section = $dbi->insertGetValue($query);
      } else {
        $query = "UPDATE wd_sections ".$query;
        $query .= "WHERE wd_id=".$wd_id." AND section=".$section.";";
        $dbi->update($query);
      }
      return $section;
   }
        

   function updateSectionSequence ($wd_id, $section, $sequence) {
     if ($sequence==NULL) $sequence=0;
     $query = "UPDATE wd_sections set sequence=".$sequence." WHERE wd_id=".wd_id." AND section=".$section.";";
     $dbi = new MYSQLAccess();
     $dbi->update($query);
   }

   function deleteSection ($wd_id, $section) {
     $dbi = new MYSQLAccess();
     $query = "SELECT * from wd_sections WHERE wd_id=".$wd_id." AND parent_s=".$section.";";
     $results1 = $dbi->queryGetResults($query);
     $query = "SELECT * from wd_fields WHERE wd_id=".$wd_id." AND parent_s=".$section.";";
     $results2 = $dbi->queryGetResults($query);
     if (($results1!=NULL && count($results1)>0)||($results2!=NULL && count($results2)>0)) return FALSE;
     else {
        $query = "DELETE from wd_sections WHERE wd_id=".wd_id." AND section=".$section.";";
        $dbi->update($query);
        return TRUE;
     }
   }
   
   function copyWebData($wd_id) {
      return $this->newWebDataFromXML($this->getOutputXML($wd_id,FALSE),NULL,TRUE);
   }

   function resequence($wd_id){
      $survey = $this->getWebData($wd_id);
      if ($survey != null) {
         $index = NULL;
         $fs = $this->getAllFieldsSystem($wd_id);
         for ($i=0; $i<count($fs); $i++) {
            if (0==strcmp(strtolower($fs[$i]['label']),"sequence")) {
               $index = $fs[$i]['field_id'];
               break;
            }
         }
         if ($index != NULL) {
            $currentIndex = 100;
            $incrementIndex = 100;
            $query = "SELECT wd_row_id FROM wd_".$wd_id." ORDER BY ".$index.";";
            $dbi = new MYSQLAccess();
            $results = $dbi->queryGetResults($query);
            for ($i=0; $i<count($results); $i++) {
               //$query = "UPDATE wd_".$wd_id." SET  lastupdate=NOW(), dbmode='UPDATED', ".$index."=".$currentIndex." WHERE wd_row_id=".$results[$i]['wd_row_id'].";";
               $query = "UPDATE wd_".$wd_id." SET ".$index."=".$currentIndex." WHERE wd_row_id=".$results[$i]['wd_row_id'].";";
               $dbi->update($query);
               $currentIndex += $incrementIndex;
            }
         }
      }
   }

   function resequenceStructure($wd_id,$section=-1,$start="10",$increment="10"){
      $fields = $this->getFields($wd_id, $section);
      $sections = $this->getDataSections($wd_id,$section);
      
      $i = 0;
      $j = 0;
      while($i<count($sections) || $j<count($fields)) {
         $usesection = FALSE;
         if($i>=count($sections)) $usesection=FALSE;
         else if($j>=count($fields)) $usesection=TRUE;
         else if(intval($sections[$i]['sequence']) < intval($fields[$j]['sequence'])) $usesection=TRUE;
         
         if($usesection) {
            $this->updateSectionSequence ($wd_id, $sections[$i]['section'], $start);
            $start = $start + $increment;
            $start = $this->resequenceStructure($wd_id,$sections[$i]['section'],$start,$increment);
            $i++;
         } else {
            $this->updateFieldSequence($wd_id, $fields[$j]['field_id'], $start);
            $start = $start + $increment;
            $j++;
         }
      }
      return $start;
   }

   function setReplyStatus($wd_id, $wd_row_id, $complete, $touch=FALSE) {
     if ($wd_id==NULL || $wd_row_id==NULL) return FALSE;
      $dbi = new MYSQLAccess();
      $query = "UPDATE wd_".$wd_id." SET complete='".$complete."'";
      if ($touch) $query .= ", lastupdate=NOW(), dbmode='UPDATED'"; 
      $query .= " WHERE wd_row_id=".$wd_row_id.";";
      $dbi->update($query);
      return TRUE;
   }

   function getSurveyRowsIndexed($name,$parameter_name,$stringformat=FALSE,$debug=FALSE){
      //print "<br>\nname: ".$name." param: ".$parameter_name."<br>\n";
      $answers = array();
      $str1 = "";
      $str2 = "";
      if($debug) $answers['original wdata'] = $name;
      if($debug) $answers['original parameter'] = $parameter_name;
      //if($debug) return $answers;
      $wdata = $this->getWebData($name);
      if ($wdata != NULL) {
         //print "<br>\nWeb Data name: ".$wdata['name']."<br>\n";
         $qs = $this->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
         if ($parameter_name==NULL || !isset($qs[strtolower($parameter_name)])) $parameter_name = "name";
         if (!isset($qs[strtolower($parameter_name)])) $parameter_name = "description";
         if (!isset($qs[strtolower($parameter_name)])) $parameter_name = "shortname";
         if (!isset($qs[strtolower($parameter_name)])) $parameter_name = "wd_row_id";
         $orderby="";
         if (isset($qs['sequence']) && $qs['sequence'] != NULL) $orderby=" ORDER BY ".$qs['sequence'];
         $query = "SELECT * from wd_".$wdata['wd_id']." WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND  dbmode<>'DUP')) ".$orderby;
         if($debug) $answers['original query'] = $query;
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         //print "<br>\n<br>\n";
         //print_r($results);
         //print "<br>\n";
         for ($i=0; $i<count($results); $i++) {
            //print "<br>\n<br>\n".$answers[$results[$i]['wd_row_id']]." ".$results[$i][$qs[strtolower(trim($parameter_name))]]."<br>\n";
            $answers[$results[$i]['wd_row_id']] = $results[$i][$qs[strtolower(trim($parameter_name))]];
            if($i>0) {
               $str1 .= ",";
               $str2 .= ",";
            }
            $str1 .= $results[$i][$qs[strtolower($parameter_name)]];
            $str2 .= $results[$i]['wd_row_id'];
         }
      }
      if($stringformat) return $str1.";".$str2;
      else return $answers;
   }
   
   function runFullWDSQL($wd_id,$recur=TRUE,$fields=NULL,$addlwhere=NULL) {
      $parts = $this->getFullWDSQL($wd_id,NULL,$recur,$fields,$addlwhere);
      $query = "SELECT " .$parts['select']." FROM ".$parts['from']." WHERE ".$parts['where'];
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($query);
      return $results;
   }
   
   // Create parts of an SQL query to get rows and related values
   function getFullWDSQL($wd_id,$prefix=NULL,$recur=TRUE,$fields=NULL,$addlwhere=NULL) {
      //print "getFullWDSQL(".$wd_id.",".$prefix.",".$recur.");\n<br>";
      
      $origprefix = $prefix."_";
      if($prefix==NULL) {
         $prefix = "d";
         $origprefix = "";
      }
      $webdata = $this->getWebData($wd_id);
      
      $resp = array();
      $resp['replacements'] = array();
      $resp['select'] = $prefix.".wd_row_id as ".$origprefix."wd_row_id, ".$prefix.".created as ".$origprefix."created, ".$prefix.".externalid as ".$origprefix."externalid, ".$prefix.".userid as ".$origprefix."userid";
   
      $resp['from'] = "wd_".$webdata['wd_id']." ".$prefix;
      
      $resp['where'] = "(".$prefix.".dbmode is NULL OR (".$prefix.".dbmode<>'DELETED' AND ".$prefix.".dbmode<>'DUP')) ";
      
      if($fields==NULL) $fields = $this->getAllFieldsSystem($webdata['wd_id']);
      for($i=0;$i<count($fields);$i++) {
         if(0==strcmp(strtolower(trim($fields[$i]['label'])),"sequence") || 0==strcmp(strtolower(trim($fields[$i]['map'])),"sequence")) {
            $resp['orderby'] = $prefix.".".$fields[$i]['field_id'];
         }
         if($fields[$i]['map']==NULL) $fields[$i]['map']= substr(str_replace(" ","",strtolower(trim($fields[$i]['label']))),0,64);
         if($fields[$i]['map']!=NULL){
            if(0==strcmp($fields[$i]['field_type'],"FOREIGN") && $recur){
               //print "question: ".$fields[$i]['question']."<br>\n";
               $q_info = separateStringBy(convertBack($fields[$i]['question']),",");
               $q_wd_id = strtolower(trim($q_info[0]));
               $q_resp = $this->getFullWDSQL($q_wd_id,$fields[$i]['map'],FALSE);
               $resp['select'] .= ", ".$q_resp['select'];
               $resp['from'] .= ", ".$q_resp['from'];
               $resp['where'] .= " AND ";
               
               $varname = $prefix.".".$fields[$i]['field_id'];
               
               $resp['where'] .= $varname."=".$fields[$i]['map'].".wd_row_id";
               if(strlen($q_resp['where'])>1) $resp['where'] .= " AND ".$q_resp['where'];
               
               $resp['replacements'][$varname] = $q_resp['select'];
            } else if(0==strcmp($fields[$i]['field_type'],"USERSRCH") && $recur){
               $resp['select'] .= ", ".$fields[$i]['map'].".userid as ".$fields[$i]['map']."_userid";
               $resp['select'] .= ", ".$fields[$i]['map'].".fname as ".$fields[$i]['map']."_fname";
               $resp['select'] .= ", ".$fields[$i]['map'].".lname as ".$fields[$i]['map']."_lname";
               $resp['select'] .= ", ".$fields[$i]['map'].".company as ".$fields[$i]['map']."_company";
               $resp['select'] .= ", ".$fields[$i]['map'].".state as ".$fields[$i]['map']."_state";
               $resp['from'] .= ", useracct ".$fields[$i]['map'];
               $resp['where'] .= " AND ";
               $resp['where'] .= $prefix.".".$fields[$i]['field_id']."=".$fields[$i]['map'].".userid";
            } else {
               $resp['select'] .= ", ".$prefix.".".$fields[$i]['field_id']." as ".$origprefix.$fields[$i]['map'];
            }
         }
      }
      if($resp['orderby']==NULL) $resp['orderby'] = $prefix.".created DESC";
      
      $tblPrefix = $prefix.".";
      $searchobj = $this->getCMSSearchParams($webdata['wd_id'],$tblPrefix);
      //print "<br>\nsearch obj: ";
      //print_r($searchobj);
      //print "<br>\nWhere1: ".$resp['where']."<br>\n";
      if($searchobj['where']!=NULL) $resp['where'] .= " AND ".$searchobj['where'];
      //print "<br>\nWhere2: ".$resp['where']."<br>\n";
      
      return $resp;
   }
   
   function getAverageSQL($wd_id,$avgfld=NULL,$groupby=NULL,$orderby=NULL,$addlselect=NULL) {
      $webdata = $this->getWebData($wd_id);
      $newgroupby = NULL;
      
      $avgfld = strtolower(trim($avgfld));
      $avgfldmap = $avgfld;
      $groupby = strtolower(trim($groupby));
      
      $queryobj = $this->getFullWDSQL($webdata['wd_id']);
      
      $fields = $this->getAllFieldsSystem($webdata['wd_id']);
      
      if($addlselect==NULL) {
         $addlselect = "";
      } else {
         for($i=0;$i<count($fields);$i++) {
            $addlselect = str_replace($fields[$i]['map'],$fields[$i]['field_id'],$addlselect);
            $addlselect = str_replace(strtolower(trim($fields[$i]['label'])),$fields[$i]['field_id'],$addlselect);
         }
      }
      
      if($avgfld!=NULL && $groupby!=NULL) {
         $byarr = separateStringBy(convertBack($groupby),",",NULL,TRUE);
         $newgroupby = "";
         
         for($i=0;$i<count($fields);$i++) {
            if(0==strcmp($avgfld,$fields[$i]['map']) || 0==strcmp($avgfld,strtolower(trim($fields[$i]['label'])))){
               $avgfld = "d.".$fields[$i]['field_id'];
               if($fields[$i]['map']!=NULL) $avgfldmap = $fields[$i]['map'];
            }
            for($j=0;$j<count($byarr);$j++) {
               if(0==strcmp($byarr[$j],$fields[$i]['field_id']) || 0==strcmp($byarr[$j],$fields[$i]['map']) || 0==strcmp($byarr[$j],strtolower(trim($fields[$i]['label'])))){
                  if(strlen($newgroupby)>1) $newgroupby .= ",";
                  $newgroupby .= "d.".$fields[$i]['field_id'];
                  
                  $addlselect .= ", d.".$fields[$i]['field_id'];                  
                  if($fields[$i]['map']!=NULL) $addlselect .= " as ".$fields[$i]['map'];
                  else if($fields[$i]['label']!=NULL) $addlselect .= " as ".str_replace(" ","",strtolower($fields[$i]['label']));
                  
                  if(isset($queryobj['replacements']["d.".$fields[$i]['field_id']])) $addlselect .= ", ".$queryobj['replacements']["d.".$fields[$i]['field_id']];
                  
               }
            }
         }
         $addlselect .= ", AVG(CAST(".$avgfld." AS DECIMAL(10,4))) as avg_".$avgfldmap;
         $addlselect .= ", COUNT(".$avgfld.") as count_".$avgfldmap;
      }
      
      if($orderby!=NULL) {
         for($i=0;$i<count($fields);$i++) {
            $orderby = str_replace($fields[$i]['map'],$fields[$i]['field_id'],$orderby);
            $orderby = str_replace(strtolower(trim($fields[$i]['label'])),$fields[$i]['field_id'],$orderby);
         }
      }
      
      
      if($addlselect!=NULL && strlen($addlselect)>3) $queryobj['select'] = "count(d.wd_row_id)".$addlselect;
      $queryobj['groupby'] = $newgroupby;
      $queryobj['orderby'] = $orderby;
      return $queryobj;
   }
   
        
   function convertForeignWD($question,$value,$debug=FALSE){
        $survey_info = separateStringBy(convertBack($question),",",NULL,TRUE);
        $fld_subs = $this->getSurveyRowsIndexed($survey_info[0],$survey_info[1],FALSE,$debug);
        $ans_arr = separateStringBy(convertBack($value),",",NULL,TRUE);
        $newans = "";
        if($debug) {
           $newans .= "question: ".$question." ";
           $newans .= "a: ".$survey_info[0]." b: ".$survey_info[1]." ";
           $newans .= "Answers: [".implode(", ",$ans_arr)."] ";
           $newans .= "All possible: [".implode(", ",$fld_subs)."] ";
        }
        $totalentries = 0;
        for ($j=0;$j<count($ans_arr);$j++) {
           $temp = trim($ans_arr[$j]);
           if(0!=strcmp($temp,"%E%")) {
              if ($totalentries>0) $newans .= ", ";
              if($fld_subs[$temp]!=NULL) $newans .= convertBack($fld_subs[$temp]);
              else if ($temp!=NULL) $newans .= convertBack($temp);
              $totalentries++;
           }
        }   
        return $newans;
   }
        
   function reverseConvertForeignWD($question,$value){
      $survey_info = separateStringBy(convertBack($question),",",NULL,TRUE);
      $fld_subs = $this->getSurveyRowsIndexed($survey_info[0],$survey_info[1]);
      $ans_arr = separateStringBy(convertBack($value),",",NULL,TRUE);
      $newans = "";
      for ($j=0;$j<count($ans_arr);$j++) {
        if ($j>0) $newans .= ", ";
        $temp = trim($ans_arr[$j]);
        
        foreach($fld_subs as $key => $val) {
           if(strcmp(strtolower($temp),strtolower(trim($val)))==0) {
              $temp = $key;
              break;
           } else if(strcmp(strtolower($temp),strtolower(trim($key)))==0) {
              $temp = $key;
              break;
           }
        }
        $newans .= convertBack($temp);
      }   
      return $newans;
   }
        
   function getSurveyOptionsOnly($name,$parameter_name,$value_name=NULL,$enabledonly=FALSE,$userid=NULL){
      $wdata = $this->getWebData($name);
      if ($wdata == NULL) return NULL;
      
      $parameter_name = strtolower(trim($parameter_name));
      $fs = $this->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
      $fs['wd_row_id'] = "wd_row_id";
      if (!isset($fs[$parameter_name])) return NULL;
      
      $orderby="";
      if (isset($fs['sequence'])) $orderby=" ORDER BY ".$fs['sequence'];
      
      $query = "SELECT * from wd_".$wdata['wd_id'];
      $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
      if ($enabledonly && isset($fs['enabled'])) $orderby=" AND LOWER(".$fs['enabled'].")='yes'";
      if($userid!=NULL) $query .= " AND userid='".$userid."'";
      $query .= $orderby;
      
      $fldval = "wd_row_id";
      if(trim($value_name)!=NULL && isset($fs[trim(strtolower($value_name))])) $fldval = $fs[trim(strtolower($value_name))];
      
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($query);
      $answers = NULL;
      for ($i=0; $i<count($results); $i++) {
         $answers[$results[$i][$fs[$parameter_name]]] = $results[$i][$fldval];
      }
      return $answers;
   }
        
   function getSurveyOptions($name,$parameter_name,$input_name,$default="",$extra="",$label=""){
      $returnstr = NULL;
      $answers = $this->getSurveyOptionsOnly($name,$parameter_name);
      if($answers!=NULL && count($answers)>0) {
         $opts = array();
         $opts[$label] = "";
         foreach($answers as $key => $val) $opts[$key] = $val;
         $returnstr = getOptionList($input_name, $opts, $default, FALSE, $extra, FALSE, 32);
      }
      return $returnstr;
   }
   
   function getSurveyCheckBoxOptions($name,$parameter_name,$input_name,$defaults,$divextra="",$cbextra=""){
      $answers = $this->getSurveyOptionsOnly($name,$parameter_name);
      if($answers==NULL || count($answers)<1) return NULL;
   
      $selects = array();
      $dArr = separateStringBy($defaults,",",NULL,TRUE);
      for ($i=0;$i<count($dArr);$i++) {
         $sel = strtolower(trim($dArr[$i]));
         if (isset($answers[$sel]) && $answers[$sel]!=NULL && !is_numeric($sel)) $selects[$answers[$sel]]=1;
         else $selects[$sel]=1;
      }
      return getCheckboxListDiv($input_name, $answers, $selects, $divextra, $cbextra);
      //return getOptionList($input_name, $answers, $default, TRUE, $extra);
   }
   
   function getTableCheckBoxOptions($name,$parameter_name,$id_name,$input_name,$defaults,$divextra="",$cbextra="",$orderby=NULL){
      $selects = array();
      $dArr = separateStringBy($defaults,",");
      for ($i=0;$i<count($dArr);$i++) $selects[trim($dArr[$i])]=1;
   
      $answers = $this->getTableOptions($name,$parameter_name,$id_name,$orderby);
      return getCheckboxListDiv($input_name, $answers, $selects, $divextra, $cbextra);
      //return getOptionList($input_name, $answers, $default, TRUE, $extra);
   }
   
   function getTableOptions($name,$parameter_name,$id_name,$orderby){
      $query = "SELECT ".$parameter_name.", ".$id_name." from ".$name;
      if($orderby!=NULL) $query .= " ORDER BY ".$orderby;
      $query .= ";";
      $dbi = new MYSQLAccess();
      $results = $dbi->queryGetResults($query);
   
      $answers = array();
      for ($i=0; $i<count($results); $i++) {
         $answers[$results[$i][$parameter_name]] = $results[$i][$id_name];
      }
      
      return $answers;
   }
   
   function getTableDropdownOptions($name,$parameter_name,$id_name,$input_name,$selected,$extra="",$orderby=NULL){
      $selects = array();
      $dArr = separateStringBy($defaults,",");
      for ($i=0;$i<count($dArr);$i++) $selects[trim($dArr[$i])]=1;
      $answers = $this->getTableOptions($name,$parameter_name,$id_name,$orderby);
      return getOptionList($parameter_name, $answers, $selected, TRUE, $extra);
   }
        
        function getDataResult($name,$wd_row_id){
            $wdata = $this->getWebData($name);
            if ($wdata == NULL) return NULL;
            $fields = $this->getAllFields($wdata['wd_id']);
            $fs = NULL;
            $midQuery="";
            for ($i=0; $i<count($fields); $i++){
               if (!(0==strcmp($fields['field_type'],"SPACER") || 
                     0==strcmp($fields['field_type'],"INFO") || 
                     0==strcmp($fields['field_type'],"TABLE") || 
                     0==strcmp($fields['field_type'],"LIKERT") || 
                     0==strcmp($fields['field_type'],"NEWLIKERT") || 
                     0==strcmp($fields['field_type'],"NEWPRCNT") || 
                     0==strcmp($fields['field_type'],"PERCENT"))) {
                  $midQuery = $fields[$i]['field_id']." as ".str_replace(" ", "", strtolower($fields[$i]['label'])).", ";
                  $fs[strtolower($fields[$i]['label'])] = $fields[$i]['field_id'];
               }
            }
            $query = "SELECT ".$midQuery." wd_row_id from wd_".$wdata['wd_id']." WHERE wd_row_id=".$wd_row_id;
            $dbi = new MYSQLAccess();
            $results = $dbi->queryGetResults($query);
            return $results[0];
        }

   function getWebData($wd_id,$fuzzy=FALSE,$printdebug=FALSE,$usehtag=FALSE,$ignorecache=FALSE) {
      if(0==strcmp($wd_id,"clearcache")) {
         $wd_id = NULL;
         unset($_SESSION['webdata']);
      }
      if ($wd_id==NULL) return NULL;
      $wd_id = strtolower(trim($wd_id));
      
      if (!$ignorecache && !$fuzzy && !$usehtag && isset($_SESSION['webdata'][$wd_id])) {
         $wdata = $_SESSION['webdata'][$wd_id];
      } else {   
         $query = "SELECT * FROM webdata WHERE ";
         if (is_numeric($wd_id)) $query .= "wd_id=".$wd_id." OR ";
         
         if($fuzzy) {
            $query .= "LOWER(shortname) LIKE '".$wd_id."' OR ";
            $query .= "LOWER(name) LIKE '".$wd_id."'";
         } else {
            $query .= "LOWER(shortname)='".$wd_id."' OR ";
            $query .= "LOWER(name)='".$wd_id."'";
         }
         
         if($usehtag) {
            $htag = $wd_id;
            if(0!=strcmp(substr($htag,0,1),"#")) $htag = "#".$htag;
            $query .= " OR LOWER(htags) LIKE '%".$htag."'";
            $query .= " OR LOWER(htags) LIKE '%".$htag." %'";
         }
      
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         if ($results==NULL || count($results)<1) return NULL;
         if($printdebug) print "<br>\ngetWebData() query: ".$query."<br>\n";
         
         if($fuzzy || $usehtag) {
            $wdata = $results;
         } else {
            $wdata = $results[0];
            
            if($printdebug) print "\n<br>getWebData() Web Data Object:\n<br>";
            if($printdebug) print_r($wdata);
            if($printdebug) print "\n<br>\n<br>";
            
            $_SESSION['webdata'][$wdata['wd_id']] = $wdata;
            $_SESSION['webdata'][strtolower(trim($wdata['name']))] = $wdata;
            $_SESSION['webdata'][strtolower(trim($wdata['shortname']))] = $wdata;
         
            $query = "show tables like 'wd_".$wdata['wd_id']."';";
            $results = $dbi->queryGetResults($query);
            if ($results == NULL || count($results)<1) {
               $query = "CREATE TABLE wd_".$wdata['wd_id']." ( ";
               $query .= "wd_row_id int(20) unsigned NOT NULL auto_increment, ";
               $query .= "dbmode varchar(8) default 'NEW', ";
               $query .= "userid bigint DEFAULT NULL, ";
               $query .= "externalid varchar(128) DEFAULT NULL, ";
               $query .= "origemail varchar(255) default NULL, ";
               $query .= "serialnumber varchar(255) default NULL, ";
               $query .= "complete char(2) default 'N', ";
               $query .= "comments text, ";
               $query .= "datesent date default NULL, ";
               $query .= "lastupdate datetime default NULL, ";
               $query .= "lastupdateby text default NULL, ";
               $query .= "lastupdateby2 text default NULL, ";
               $query .= "created datetime default NULL, ";
               $query .= "esignature varchar(255) default NULL, ";
               $query .= "PRIMARY KEY(wd_row_id));";
               $dbi->update($query);
            }
         }
      }
      return $wdata;
   }

   function getWebDataByName($name) {
      return $this->getWebData($name);
   }

   function getWebDataByFuzzyName($name=NULL) {
     return $this->getWebData($name,TRUE);           
   }

        function getDataSections($wd_id,$parent_s=-1) {
           if ($wd_id === NULL) return NULL;
           if ($parent_s==NULL || !is_numeric($parent_s)) $parent_s=-1;
           //$query = "SELECT * FROM wd_sections WHERE wd_id=".$wd_id." AND parent_s=".$parent_s." ORDER BY sequence;";
           $query = "SELECT s.* FROM wd_sections s, webdata d WHERE s.wd_id=d.wd_id AND (d.wd_id='".$wd_id."' OR LOWER(d.name)='".strtolower(trim($wd_id))."') AND s.parent_s=".$parent_s." ORDER BY sequence;";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results;
        }

        function getAllDataSections($wd_id) {
           if ($wd_id === NULL) return NULL;
           //$query = "SELECT * FROM wd_sections WHERE wd_id=".$wd_id." ORDER BY sequence;";
           $query = "SELECT s.* FROM wd_sections s, webdata d WHERE s.wd_id=d.wd_id AND (d.wd_id='".$wd_id."' OR LOWER(d.name)='".strtolower(trim($wd_id))."');";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results;
        }
        
        function getOrganizedSections($wd_id,$sects=NULL,$id=NULL){
            if($sects==NULL) $sects = $this->getAllDataSections($wd_id);
            if($id==NULL) $id = -1;
            
            
            //print "<br><br>";
            //print "iterating in getOrganizedSections(), id=".$id."<br>sects:<br>";
            //print_r($sects);
            
            $ans = [];
           
            for($i=0;$i<count($sects);$i++){
               //print "looking at sect: ";
               //print_r($sects[$i]);
               //print "<br>";
               if($sects[$i]['parent_s'] == $id) {
                  $sects[$i]['children'] = $this->getOrganizedSections($wd_id,$sects,$sects[$i]['section']);
                  $ans[] = $sects[$i];
               }
            }
            
            //print "<br><br>";
           
            return $ans;
        }

         function getSectionForField($wd_id,$field_id){
           if ($wd_id === NULL || $field_id===NULL) return NULL;
           $query = "SELECT s.* FROM wd_sections s, wd_fields f WHERE s.wd_id='".$wd_id."' AND f.wd_id=s.wd_id AND s.section=f.parent_s AND f.field_id='".$field_id."';";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results[0];
         }

        function getSection($wd_id,$section) {
           if ($wd_id === NULL || $section===NULL || $section==-1) return NULL;
           $query = "SELECT s.* FROM wd_sections s, webdata d WHERE s.wd_id=d.wd_id AND (d.wd_id='".$wd_id."' OR LOWER(d.name)='".strtolower(trim($wd_id))."') AND section='".$section."';";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results[0];
        }


        function getFields($wd_id=NULL, $section=-1, $admin=0, $showsql=FALSE) {
           if ($wd_id === NULL || $section===NULL) return NULL;
           if ($section==NULL || !is_numeric($section)) $section=-1;
            $ua = new UserAcct();

            $showQuestion ="";
            $temp = NULL;
            if ($admin!=1) $temp = $ua->buildPrivacySQLCheck(isLoggedOn());
            if ($temp != NULL) $showQuestion = " AND (".$temp.")";

           $query = "SELECT f.* FROM wd_fields f, webdata d WHERE f.wd_id=d.wd_id AND f.parent_s='".$section."' AND (d.wd_id='".$wd_id."' OR LOWER(d.name)='".strtolower(trim($wd_id))."') ".$showQuestion." ORDER BY sequence;";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           
           for($i=0;$i<count($results);$i++){
              if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
           }
           
           if($showsql) {
              print "\n<!-- WebsiteData::getFields sql: ".$query."\n";
              //print_r($results);
              print "\n-->\n\n";
           }
           
           return $results;
        }

      function getFieldsRecur($wd_id,$section=-1){
         $fields = $this->getFields($wd_id, $section);   
         $sections = $this->getDataSections($wd_id,$section);
         for ($i=0; $i<count($sections); $i++) {
            $sectfields = $this->getFieldsRecur($wd_id,$sections[$i]['section']);
            if ($sectfields!=NULL && count($sectfields)>0 && $fields!=NULL && count($fields)>0 ) {
               $fields = array_merge($fields,$sectfields);
            } else if ($sectfields!=NULL && count($sectfields)>0) {
               $fields = $sectfields;
            }
         }
         return $fields;         
      }

      function getFieldsVisual($wd_id,$section=-1){
         $sections = $this->getDataSections($wd_id,$section);
         $fields = array();
         $mastercount = 0;
         for ($i=0; $i<count($sections); $i++) {
            $sectfields = $this->getFieldsRecur($wd_id,$sections[$i]['section']);
            $s_count = $sections[$i]['param1'];
            if ($s_count==NULL || $s_count==0 || $s_count>=count($sectfields)) {
               for ($j=0;$j<count($sectfields);$j++) {
                  $fields[$mastercount] = $sectfields[$j];
                  $mastercount++;
               }
            } else if ($s_count<count($sectfields)) {
               $tally = array();
               for ($j=0;$j<count($sectfields);$j++) $tally[$j] = FALSE;
               $rfields = array();
               $k = 0;
               while($k<$s_count) {
                  $indx = mt_rand(0,(count($sectfields)-1));
                  if (!$tally[$indx]) {
                     $fields[$mastercount] = $sectfields[$indx];
                     $mastercount++;
                     $k++;
                     $tally[$indx] = TRUE;
                  }
               }
            }
         }
         return $fields;         
      }

   function getFieldLabels($wd_id,$includemapname=FALSE,$includefieldid=FALSE){
      $fields = $this->getAllFieldsSystem($wd_id);
      $qs = NULL;
      for ($i=0; $i<count($fields); $i++) {
         $qs[strtolower(trim($fields[$i]['label']))] = $fields[$i]['field_id'];
         if($includemapname && trim($fields[$i]['map'])!=NULL) $qs[strtolower(trim($fields[$i]['map']))] = $fields[$i]['field_id'];
         if($includefieldid) $qs[$fields[$i]['field_id']] = $fields[$i]['field_id'];
      }
      return $qs;
   }

   function getFieldNames($wd_id){
      $fields = $this->getAllFieldsSystem($wd_id);
      $qs = NULL;
      for ($i=0; $i<count($fields); $i++) $qs[$fields[$i]['field_id']] = $fields[$i]['label'];
      return $qs;
   }

   function getFieldsIndexed($wd_id){
      $fields = $this->getAllFieldsSystem($wd_id);
      $qs = NULL;
      for ($i=0; $i<count($fields); $i++) $qs[$fields[$i]['field_id']] = $fields[$i];
      return $qs;
   }

   function getHeaderFields($wd_id=NULL) {
     if ($wd_id === NULL) return NULL;
     $query = "SELECT * FROM wd_fields WHERE header=1 AND wd_id=".$wd_id." ORDER BY sequence;";
     $dbi = new MYSQLAccess();
     $results = $dbi->queryGetResults($query);
     for($i=0;$i<count($results);$i++){
        if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
     }
     return $results;
   }
   
   function getSearchFields($wd_id=NULL) {
     if ($wd_id === NULL) return NULL;
     $query = "SELECT * FROM wd_fields WHERE srchfld=1 AND wd_id=".$wd_id." ORDER BY sequence;";
     $dbi = new MYSQLAccess();
     $results = $dbi->queryGetResults($query);
     for($i=0;$i<count($results);$i++){
        if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
     }
     return $results;
   }
   
   function getFilterFields($wd_id=NULL) {
     if ($wd_id === NULL) return NULL;
     $query = "SELECT * FROM wd_fields WHERE filterfld=1 AND wd_id=".$wd_id." ORDER BY sequence;";
     $dbi = new MYSQLAccess();
     $results = $dbi->queryGetResults($query);
     for($i=0;$i<count($results);$i++){
        if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
     }
     return $results;
   }
   
   function getFieldsMultiIndex($wd_id) {
     if ($wd_id === NULL) return NULL;
     $labels = array();
     $names = array();
     $headers = array();
     $filters = array();
     $search = array();
     $indexed = array();
     $results = $this->getAllFieldsSystem($wd_id);
     for($i=0;$i<count($results);$i++) {
        $indexed[$results[$i]['field_id']] = $results[$i];
        $names[$results[$i]['field_id']] = $results[$i]['label'];
        $labels[$results[$i]['field_id']] = $results[$i]['field_id'];
        if(trim($results[$i]['label'])!=NULL) $labels[strtolower(trim($results[$i]['label']))] = $results[$i]['field_id'];
        if(trim($results[$i]['map'])!=NULL) $labels[strtolower(trim($results[$i]['map']))] = $results[$i]['field_id'];              
        if($results[$i]['header']==1) $headers[] = $results[$i];
        if($results[$i]['srchfld']==1) $search[] = $results[$i];
        if($results[$i]['filterfld']==1) $filters[] = $results[$i];
     }
     $ret = array();
     $ret['allfields'] = $results;
     $ret['indexed'] = $indexed;
     $ret['bylabel'] = $labels;
     $ret['byname'] = $names;
     $ret['headers'] = $headers;
     $ret['filters'] = $filters;
     $ret['search'] = $search;
     
     return $ret;
   }

       function getAllFields($wd_id) {
           $ua = new UserAcct();
           $showQuestion ="";
           $temp = $ua->buildPrivacySQLCheck(isLoggedOn());
           if ($temp != NULL) $showQuestion = " AND (".$temp.")";
           $query = "SELECT * FROM wd_fields WHERE wd_id='".$wd_id."' ".$showQuestion." ORDER BY sequence;";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           for($i=0;$i<count($results);$i++){
              if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
           }
           return $results;
       }

       function getAllFieldsSystem($wd_id,$field_type=NULL,$ignoredisabled=FALSE) {
           $query = "SELECT * FROM wd_fields WHERE ";
           $query .= "wd_id='".$wd_id."' ";
           if($field_type!=NULL) $query .= "AND LOWER(field_type)='".strtolower($field_type)."' ";
           if($ignoredisabled) $query .= " AND (disa IS NULL OR disa=0) ";
           $query .= "ORDER BY sequence;";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           //$results = $dbi->queryUseCache($query)
           for($i=0;$i<count($results);$i++){
              if(trim($results[$i]['map'])==NULL) $results[$i]['map'] = removeSpecialChars(strip_tags(strtolower(trim(convertBack($results[$i]['label'])))));
           }
           return $results;
       }

      function getWebTables($userid=NULL,$privatesrvy=NULL, $checkAuth=TRUE, $externalid=NULL, $activetime=NULL, $status=NULL, $asuserid=NULL, $searchtxt=NULL, $limit=NULL) {
         //print "\n<!-- ".$userid.", ".$privatesrvy.", ".$checkAuth.", ".$externalid.", ".$activetime.", ".$status.", ".$asuserid." -->\n";
         $whereclause=" WHERE wd_id>0";

         // Check to make sure we only return the specific types
         if ($privatesrvy!=NULL) {
            $psArr = separateStringBy($privatesrvy,",");
            $whereclause .= " AND (";
            for ($i=0;$i<count($psArr);$i++) {
               if ($i>0) $whereclause .= " OR ";
               $whereclause .= "privatesrvy=".$psArr[$i];
            }
            $whereclause .= ") ";
         }

         // can check mulitple external id's
         if ($externalid!=NULL) {
            //print "\n<br>external id: ".$externalid."\n<br>";
            $psArr = separateStringBy($externalid,",",NULL,TRUE);
            $whereclause .= " AND (";
            for ($i=0;$i<count($psArr);$i++) {
               if ($i>0) $whereclause .= " OR ";
               $whereclause .= "externalid='".$psArr[$i]."'";
            }
            $whereclause .= ") ";
         }
         
         if($searchtxt!=NULL) {
            $strarr = separateStringBySeparators($searchtxt);
            //print "<br>\nsearchtxt: ".$searchtxt."\n<br>";
            //print_r($strarr);
            //print "<br>\n";
            for($i=0;$i<count($strarr);$i++) {
               $htag = trim($strarr[$i]);
               if($htag!=NULL) {
                  $checkname = TRUE;
                  if(0!=strcmp(substr($htag,0,1),"#")) $htag = "#".$htag;
                  else $checkname=FALSE;
                  $whereclause .= " AND (LOWER(htags) LIKE '%".$htag."'";
                  $whereclause .= " OR LOWER(htags) LIKE '%".$htag." %'";
                  if($checkname) {
                     $whereclause .= " OR LOWER(name) LIKE '%".strtolower(trim($strarr[$i]))."%'";
                     $whereclause .= " OR LOWER(shortname) LIKE '%".strtolower(trim($strarr[$i]))."%'";
                  }
                  $whereclause .= ") ";
               }
            }
         }
         
         if ($activetime!=NULL) $whereclause .= " AND starttime<='".$activetime."' AND endtime>='".$activetime."' ";
         if ($status!=NULL) $whereclause .= " AND status='".$status."' ";
         
         $query = "SELECT * FROM webdata ".$whereclause." ORDER BY sequence, createdon DESC";
         if($limit!=NULL) $query .= " LIMIT 0,".$limit;
         $query .= ";";
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         
         //print "<br><br>\n\nQuery: ".$query."\n\n<br><br>";

         if ($checkAuth) {
            $ua = new UserAcct();
            if($userid==NULL) $userid = isLoggedOn();
            if($asuserid==NULL) $asuserid = $userid;
            $results2 = array();
            if ($ua->doesUserHaveAccessToLevel($asuserid,2)) {
               $results2 = $results;
            } else {
               for ($i=0; $i<count($results); $i++) {
                  if ($ua->isUserAccessible($userid,"WDATA",$results[$i]['wd_id'])) {
                     $results2[] = $results[$i];
                  } else if($results[$i]['privatesrvy']==2 || $results[$i]['privatesrvy']==3 || $results[$i]['privatesrvy']==7) {
                     $results2[] = $results[$i];
                  }
               }
            }
            $results = $results2;
         }
         return $results;
      }

      function setAnswer($wd_id,$wd_row_id,$field_id,$answer,$update=TRUE){
         //print "\n<!-- setAnswer(".$wd_id.",".$wd_row_id.",".$field_id.",".$answer."); -->\n";
         if ($wd_id===NULL || $wd_row_id===NULL) return FALSE;
         $dbi = new MYSQLAccess();
         $query = "SELECT * FROM wd_".$wd_id." where wd_row_id='".$wd_row_id."';";
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         if ($results!=NULL && count($results)>0) {
            //$query = "UPDATE wd_".$wd_id." set lastupdate=NOW(), dbmode='UPDATED', ".$field_id."='".$answer."' WHERE wd_row_id=".$wd_row_id.";";
            //print "\n<!-- setAnswer Query: ".$query." -->\n";
            //$dbi->update($query);
            $this->updateFieldValue($wd_id,$wd_row_id,$field_id,$answer,$update,FALSE);

            return TRUE;
         } else {
            return FALSE;
         }
      }

      function getAnswer($wd_id, $wd_row_id, $field_id) {
         if ($wd_row_id == null || $field_id==null) {
            $results['answer'] = "";
         } else {
            //$query = "SELECT ".$field_id." FROM wd_".$wd_id." WHERE wd_row_id=".$wd_row_id;
            $query = "SELECT * FROM wd_".$wd_id." WHERE wd_row_id=".$wd_row_id;
            $dbi = new MYSQLAccess();
            $answer = $dbi->queryGetResults($query);
            $results['answer'] = $answer[0][$field_id];
            $results['row'] = $answer[0];
         }
         return $results;
      }

      function getRowCount ($wd_id, $filterStr) {
         return $this->getDataCount($wd_id);
      }

      function getDataCount ($wd_id, $whereClause="") {
         $query = "SELECT count(*) FROM wd_".$wd_id." WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
         if ($whereClause!=null) $query.= " AND ".$whereClause;
         $query .= ";";
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         return $results[0]['count(*)'];
      }

        function getRowsSurveyOrgAdmin ($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $clearcache=FALSE, $orgParamArray=NULL, $returnresults=TRUE) {
           if ($clearcache) unset($_SESSION['wd']);
           $dbi = new MYSQLAccess();
           $organization = $this->getWebDataByName("org properties");
           $query = "SELECT ";
           if ($countOnly) {
              $query .= "count(*)";
              $query .= " FROM useracct c ";
              $query .= " join wd_".$wd_id." d on c.userid=d.userid ";
              $query .= " join wd_".$organization['wd_id']." o on c.userid=o.userid ";
              $query .= " join userrel r on c.userid=r.userid ";
              $query .= " join useracct a on a.userid=r.reluserid ";
              $query .= " WHERE r.rel_type='SRVYADMIN' ";
           } else {
              $fields = $this->getAllFieldsSystem($wd_id);
              $query .= " c.userid as orgid, ";
              $query .= " c.company, ";
              $query .= " c.addr1, ";
              $query .= " c.addr2, ";
              $query .= " c.city, ";
              $query .= " c.state, ";
              $query .= " c.zip, ";
              $query .= " c.country, ";
              $query .= " c.phonenum, ";
              $query .= " c.phonenum2, ";
              $query .= " c.phonenum3, ";
              $query .= " c.phonenum4, ";
              $query .= " c.fname, ";
              $query .= " c.lname, ";
              $query .= " c.email, ";
              for ($i=0; $i<count($orgParamArray); $i++) {
                 $query .= "o.".$orgParamArray[$i]." as org".$orgParamArray[$i].", ";
              }
              $query .= " c.field5, ";
              $query .= " c.field6, ";
              $query .= " a.fname as admin_firstname, ";
              $query .= " a.lname as admin_lastname, ";
              $query .= " a.addr1 as admin_addr1, ";
              $query .= " a.addr2 as admin_addr2, ";
              $query .= " a.city as admin_city, ";
              $query .= " a.state as admin_state, ";
              $query .= " a.zip as admin_zip, ";
              $query .= " a.email as admin_email, ";
              $query .= " a.phonenum as admin_phonenum, ";
              $query .= " a.phonenum2 as admin_phonenum2, ";
              $query .= " d.* ";
              $query .= " FROM useracct c ";
              $query .= " join wd_".$wd_id." d on c.userid=d.userid ";
              $query .= " join wd_".$organization['wd_id']." o on c.userid=o.userid ";
              $query .= " join userrel r on c.userid=r.userid ";
              $query .= " join useracct a on a.userid=r.reluserid ";
              $query .= " WHERE r.rel_type='SRVYADMIN' ";
           }
           $searchObj = $this->getCMSSearchParams($wd_id,"d.");
           $searchObj2 = $this->getCMSSearchParams($organization['wd_id'],"o.");
           if ($searchObj['where']!=NULL) $query .= " AND ".$searchObj['where'];
           if ($searchObj2['where']!=NULL) $query .= " AND ".$searchObj2['where'];
           if ($filterStr!=NULL) {
              $filterStr = strtolower(convertString($filterStr));
              $query .= " AND ( lower(c.company) LIKE '%".$filterStr."%' OR lower(c.fname) LIKE '%".$filterStr."%' OR lower(c.lname) LIKE '%".$filterStr."%' OR lower(c.email) LIKE '%".$filterStr."%')";
           }

           //***chj***
           $query .= " AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'))";

           if ($orderby != null && !$countOnly) $query .= " ORDER BY ".$orderby;
           $results['query'] = $query;

           if ($limit != null && !$countOnly) $query .= " ".$limit;
           $query .= ";";

           //print "\n<!-- ".date("Y-m-d h:i:s")." Query: ".$query." -->\n";
           if($returnresults) $results['results'] = $dbi->queryGetResults($query);
           $results['params'] = $searchObj['params'];
           $results['orderby'] = $orderby;
           $results['limit'] = $limit;
           return $results;
        }

        function getRowsSurveyAdmin ($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $clearcache=FALSE, $field5=NULL) {
           if ($clearcache) unset($_SESSION['wd']);
           
           //Check if we need to join either useracct or org prop tables
           $use_useracct = FALSE;
           $use_orgprop = FALSE;
           
           if ($filterStr!=NULL) {
              $filterStr = strtolower(convertString($filterStr));
              $use_useracct = TRUE;
              //print "\n<!-- chad chad chad ***1 -->\n";
           }
           
           if ($field5!=NULL) {
              $use_useracct = TRUE;
              //print "\n<!-- chad chad chad ***1a -->\n";
           }
           
           if($orderby!=NULL && strpos($orderby,"c.")!==FALSE) {
              $use_useracct = TRUE;
              //print "\n<!-- chad chad chad ***2 -->\n";
           }
           
           $orgwd = $this->getWebDataByName("org properties");
           $searchObj2 = $this->getCMSSearchParams($orgwd['wd_id'],"p.");
           if($searchObj2!=NULL && strlen($searchObj2['where'])>6 && count($searchObj2['params'])>0) {
              $use_orgprop = TRUE;
              //print "\n<!-- chad chad chad ***3 -->\n";
              //print "\n<!-- obj:\n";
              //print_r($searchObj2);
              //print "\n-->\n";
           }
           if($orderby!=NULL && strpos($orderby,"p.")!==FALSE) {
              $use_orgprop = TRUE;
              //print "\n<!-- chad chad chad ***4 -->\n";
           }
           
           //Start to build the SQL
           $dbi = new MYSQLAccess();
           $query = "SELECT ";
           if ($countOnly) {
              $query .= "count(*)";
           } else {
              $fields = $this->getAllFieldsSystem($wd_id);
              $query .= " d.*";
              
              /*
              if($use_useracct) {
                 $query .= ", c.userid as orgid";
                 $query .= ", c.state";
                 $query .= ", c.field1";
                 $query .= ", c.field5";
                 $query .= ", c.field6";
                 $query .= ", c.company";
                 $query .= ", c.website";
              }
              
              if($use_orgprop) {
                 //for ($i=0; $i<count($orgParamArray); $i++) {
                 //   $query .= ", p.".$orgParamArray[$i]." as org".$orgParamArray[$i];
                 //}
                 $query .= ", p.wd_row_id";
              }
              */
           }
           $query .= " FROM  wd_".$wd_id." d";
           
           if($use_useracct) {
              $query .= " left outer join ";
              $query .= " useracct c ";
              $query .= " on d.userid=c.userid ";
           }
           
           if($use_orgprop) {
              $query .= " left outer join ";
              $query .= " wd_".$orgwd['wd_id']." p ";
              $query .= " on d.userid=p.userid ";
           }
           
           $query .= " WHERE 1=1 ";
           
           $params = array();
           $display = array();
           $searchObj = $this->getCMSSearchParams($wd_id,"d.");
           if ($searchObj['where']!=NULL) {
              $query .= " AND ".$searchObj['where'];
              $params = $searchObj['params'];
              $display = $searchObj['display'];
           }
           
           //if ($searchObj2['where']!=NULL) $query .= " AND ".$searchObj2['where'];
           if ($filterStr!=NULL && $use_useracct) {
              $query .= " AND ( lower(c.company) LIKE '%".$filterStr."%' OR lower(c.fname) LIKE '%".$filterStr."%' OR lower(c.lname) LIKE '%".$filterStr."%' OR lower(c.email) LIKE '%".$filterStr."%')";
           }
           if ($field5!=NULL && $use_useracct) $query .= " AND c.field5=".$field5;

           //Check if there are additional org property parameters
           if ($use_orgprop && $searchObj2['where']!=NULL) {
              $query .= " AND ".$searchObj2['where'];
              $params = array_merge($params,$searchObj2['params']);
              $display = array_merge($display,$searchObj2['display']);
           }

           //***chj***
           $query .= " AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'))";
           $results['rawsql'] = $query;

           if ($orderby != null && !$countOnly) $query .= " ORDER BY ".$orderby;
           if ($limit != null && !$countOnly) $query .= " ".$limit;
           $query .= ";";

           //print "\n<!-- ".date("Y-m-d h:i:s")." Query: ".$query." -->\n";
           $results['results'] = $dbi->queryGetResults($query);
           $results['sql'] = $query;
           $results['params'] = $params;
           $results['display'] = $display;
           $results['orderby'] = $orderby;
           $results['limit'] = $limit;
           return $results;
        }

        function getRowsUser ($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $clearcache=FALSE) {
            if ($clearcache) unset($_SESSION['wd']);
            $dbi = new MYSQLAccess();
            $query = "show tables like 'wd_".$wd_id."';";
            $results = $dbi->queryGetResults($query);
            if ($results == NULL || count($results)<1) return NULL;

           $query = "SELECT ";
           if ($countOnly) {
              $query .= "count(*)";
              $query .= " FROM wd_".$wd_id." d, useracct u ";
              $query .= "WHERE d.userid=u.userid";
           } else {
              $fields = $this->getAllFieldsSystem($wd_id);
              $query .= " d.*, ";
              $query .= " u.fname, ";
              $query .= " u.lname, ";
              $query .= " u.email, ";
              $query .= " u.company ";
              $query .= " FROM wd_".$wd_id." d, useracct u ";
              $query .= "WHERE d.userid=u.userid";
           }
           $searchObj = $this->getCMSSearchParams($wd_id,"d.");
           if ($searchObj['where']!=NULL) $query .= " AND ".$searchObj['where'];
           if ($filterStr!=NULL) {
              $filterStr = strtolower(convertString($filterStr));
              $query .= " AND ( lower(u.company) LIKE '%".$filterStr."%' OR lower(u.fname) LIKE '%".$filterStr."%' OR lower(u.lname) LIKE '%".$filterStr."%' OR lower(u.email) LIKE '%".$filterStr."%')";
           }

           $query .= " AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'))";

           if ($orderby != null && !$countOnly) $query .= " ORDER BY ".$orderby;
           if ($limit != null && !$countOnly) $query .= " ".$limit;
           $query .= ";";
              
           $results['results'] = $dbi->queryGetResults($query);
           $results['params'] = $searchObj['params'];
           $results['orderby'] = $orderby;
           $results['limit'] = $limit;
           return $results;
        }

        function getRows($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $userid=NULL, $forCSV=FALSE, $pub=FALSE, $subforeignfields=FALSE, $ignoreSearchParams=FALSE, $shorteasy=FALSE, $qids=NULL, $page=1, $externalid=NULL, $adduser=FALSE, $printdebug=FALSE) {
           if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: Start function";
           if($page==NULL || !is_numeric($page) || $page<1) $page=1;
           $wdata = $this->getWebData($wd_id);
           $results = NULL;
           
           //See if we need to query the properties...
           $checkuser = FALSE;
           $searchProps = array();
           $wdataprops = NULL;
           if($wdata['usertype']!=NULL) {
              $wdataprops = $this->getWebData($wdata['usertype']." properties");
              $searchProps = $this->getCMSSearchParams($wdataprops['wd_id'],"p.",$printdebug);
              if($searchProps['where']!=NULL && strlen($searchProps['url'])>0) {
                 $checkuser = TRUE;
              }
           }
           
           $dbi = new MYSQLAccess();
           $query = "show tables like 'wd_".$wdata['wd_id']."';";
           if ($pub) $query = "show tables like 'wd_".$wdata['wd_id']."_pub';";
           $results1 = $dbi->queryGetResults($query);
           if ($results1 != NULL && count($results1)>0) {
              if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: table found: ".$wdata['wd_id'];
              
              $multiflds = $this->getFieldsMultiIndex($wdata['wd_id']);
              $qs = $multiflds['bylabel'];
              $qsindx = $multiflds['indexed'];
              $fields = $multiflds['allfields'];
              
              $results = array();
              $results['wd_id'] = $wdata['wd_id'];
              
              $query_sel = "SELECT ";
              $query_from = " FROM ";
              $query_whr = " WHERE 1=1";
              
              if ($countOnly) {
                 $query_sel .= "count(*)";
              } else {
                  if ($forCSV) {
                     $qs2 = array();
                     $counter = 0;
                     $query_sel .= "d.created, d.lastupdate, SUBSTR(d.lastupdateby,1,16) as lastupdateby, d.wd_row_id, d.externalid, d.userid, d.origemail ";
                     for ($i=0; $i<count($fields); $i++) {
                        if (0!=strcmp($fields[$i]['field_type'],"SPACER") && 0!=strcmp($fields[$i]['field_type'],"INFO")) {
                           $query_sel .= ", d.".$fields[$i]['field_id'];
                           $counter++;
                           if ($fields[$i]['header']==1) $qs2[$fields[$i]['label']]=$fields[$i]['field_id'];
                        }
                     }
                     //$results['fields'] = $qs;
                     $results['headers'] = $qs2;
                  } else if ($shorteasy) {
                     $query_sel .= "d.userid, d.origemail, d.created, d.lastupdate, SUBSTR(d.lastupdateby,1,16) as lastupdateby, d.wd_row_id, d.externalid ";
                     for ($i=0; $i<count($fields); $i++) {
                        if (0!=strcmp($fields[$i]['field_type'],"SPACER") && 0!=strcmp($fields[$i]['field_type'],"INFO") && $fields[$i]['header']==1) {
                           $label = $fields[$i]['label'];
                           if(trim($fields[$i]['map'])!=NULL) $label = $fields[$i]['map'];
                           $label = str_replace("\"","",str_replace("'","",str_replace(",","",str_replace(".","",str_replace("%","",str_replace(" ","_",strtolower(trim($label))))))));
                           $query_sel .= ", d.".$fields[$i]['field_id']." as ".$label;
                        }
                     }
                  } else if ($qids!=NULL && count($qids)>0) {
                     $query_sel .= "d.wd_row_id";
                     //$query .= ", d.userid, d.created, d.lastupdate, d.lastupdateby";                     
                     for ($i=0;$i<count($qids);$i++) {
                        $fld = strtolower(trim($qids[$i]));
                        $query_sel .= ", ";
                        if(isset($qs[$fld])) $query_sel .= "d.".$qs[$fld]." as ".str_replace(" ","",$fld);
                        else $query_sel .= "d.".$fld;
                     }
                  } else {
                     $query_sel .= " d.* ";
                  }
              }

              if ($pub) $query_from .= "wd_".$wdata['wd_id']."_pub d";
              else $query_from .= "wd_".$wdata['wd_id']." d";
              
              if($checkuser) {
                 $query_from .= ", wd_".$wdataprops['wd_id']." p, useracct o";
                 $query_whr .= " AND d.userid=p.userid AND d.userid=o.userid";
              }
              
              if ($userid!=NULL) {                 
                $query_whr .= " AND (d.userid=".$userid." ";
                for ($i=0; $i<count($fields); $i++) {
                  //if (0==strcmp($fields[$i]['field_type'],"USERSRCH") && $fields[$i]['header']==1) {
                  if (0==strcmp($fields[$i]['field_type'],"USERSRCH") || 0==strcmp($fields[$i]['field_type'],"USERAUTO")) {
                     $query_whr .= "OR d.".$fields[$i]['field_id']."=".$userid." ";
                  } else if (0==strcmp($fields[$i]['field_type'],"USERLIST")) {
                     $query_whr .= "OR d.".$fields[$i]['field_id']."='".$userid."' ";
                     $query_whr .= "OR REPLACE(d.".$fields[$i]['field_id'].",\"&#44;\",\",\") LIKE '".$userid.",%' ";
                     $query_whr .= "OR REPLACE(d.".$fields[$i]['field_id'].",\"&#44;\",\",\") LIKE '%,".$userid.",%' ";
                     $query_whr .= "OR REPLACE(d.".$fields[$i]['field_id'].",\"&#44;\",\",\") LIKE '%,".$userid."' ";
                  }
                }
                $query_whr .= ") ";
              }
              
               //Old way for foreign rows (o = originating)
               //$o_wd_row_id = getParameter("o_wd_row_id");
               //$o_wd_id = getParameter("o_wd_id");
               //$o_field_id = getParameter("o_field_id");
               //New way to get foreign rows (p = parent)
               $p_wd_row_id = getParameter("o_wd_row_id");
               $p_wd_id = getParameter("o_wd_id");
               $p_field_id = getParameter("o_field_id");
               if($p_wd_id!=NULL && $p_field_id!=NULL && $p_wd_row_id!=NULL) {
                  $tempwd = $this->getWebData($p_wd_id);
                  if($tempwd!=NULL && $tempwd['wd_id']!=NULL){
                     $p_wd_id = $tempwd['wd_id'];
                     $p_qs = $this->getFieldLabels($p_wd_id,TRUE,TRUE);
                     $p_field_id = $p_qs[$p_field_id];
                     if($p_wd_id!=NULL && $p_field_id!=NULL && $p_wd_row_id!=NULL) {
                        // Query only needs 1 of two methods to find related rows
                        // wd_link - more reliable
                        // externalid - more efficient (no joins)
                        
                        $query_from .= ", (SELECT linkid, wd_row_id2 FROM wd_link WHERE ";
                        $query_from .= "wd_id1=".$p_wd_id;
                        $query_from .= " AND wd_id2=".$wdata['wd_id'];
                        $query_from .= " AND wd_row_id1=".$p_wd_row_id;
                        $query_from .= " AND field_id='".$p_field_id."') l";
                        $query_whr .= " AND d.wd_row_id=l.wd_row_id2 ";
                        
                        // NOW, WE USE wd_link 190602
                        //if($externalid==NULL) {
                        //   $externalid = $p_wd_id."_".$p_field_id."_".$p_wd_row_id;
                        //}
                     }
                  }
               }
              
              if ($externalid!=NULL) {
                 $query_whr .= " AND d.externalid='".$externalid."' ";
              }
              
              $searchObj = array();
              if (!$ignoreSearchParams) {
                 $searchObj = $this->getCMSSearchParams($wdata['wd_id'],"d.",$printdebug);                 
                 if ($searchObj['where']!=NULL) $query_whr .= " AND ".$searchObj['where'];
                 if ($checkuser) {
                    $query_whr .= " AND ".$searchProps['where'];
                    $searchObj['params'] = array_merge($searchObj['params'],$searchProps['params']);
                 }
              }

              if ($filterStr!=NULL) {
                  $fltrArr = separateStringBySeparators($filterStr);
                  for ($i=0; $i<count($fltrArr); $i++) {
                     $part = trim($fltrArr[$i]);
                     if ($part!=NULL) {
                        $query_whr .= " AND ( LOWER(d.comments) LIKE '%".strtolower(convertString($part))."%' ";
                        if (0==strcmp($wdata['field2'],"displayupdateby")) $query .= " OR LOWER(d.lastupdateby) LIKE '%".strtolower(convertString($part))."%' ";
                        for ($j=0; $j<count($fields); $j++) {
                           if ($fields[$j]['srchfld']==1) {
                              $query_whr .= " OR LOWER(d.".$fields[$j]['field_id'].") LIKE '%".strtolower(convertString($part))."%' ";
                           }
                        }
                        if($checkuser) {
                           $query_whr .= " OR LOWER(o.company) LIKE '%".strtolower(convertString($part))."%' ";
                        }
                        $query_whr .= ") ";
                     }
                  }

              }
              //$results['query'] = $query;

              //***chj***
              $query_whr .= " AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'))";
              
              $query = $query_sel.$query_from.$query_whr;
   
              // Check if the caller is trying to use a map field to order by
              // if it is a mapped name, check if it's a date field to order by newest entry
              if ($orderby!=null && isset($qs[$orderby]) && 0==strcmp($qsindx[$qs[$orderby]]['field_type'],"DATE")) $query .= " ORDER BY d.".$qs[$orderby]." DESC";
              else if ($orderby!=null && isset($qs[$orderby]) && 0==strcmp($qsindx[$qs[$orderby]]['field_type'],"DATETIME")) $query .= " ORDER BY d.".$qs[$orderby]." DESC";
              else if ($orderby!=null && isset($qs[$orderby])) $query .= " ORDER BY d.".$qs[$orderby];
              else if ($orderby != null && !$countOnly) $query .= " ORDER BY ".$orderby;
              else if ($orderby == null && !$countOnly && isset($qs['recorddate'])) $query .= " ORDER BY d.".$qs['recorddate']." DESC";
              else if ($orderby == null && !$countOnly && isset($qs['sequence'])) $query .= " ORDER BY d.".$qs['sequence'];
              else if (!$countOnly) $query .= " ORDER BY created DESC";
              
              if ($limit != null && !$countOnly) {
                  if (is_numeric($limit)) $query .= " LIMIT ".(($page-1)*$limit).",".$limit;
                  else $query .= " ".$limit;
              }
              $query .= ";";
              //print "<br>\n<br>\n Query: ".$query."  <br>\n<br>\n";
              if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: query is ".$query;
              $results['results'] = $dbi->queryGetResults($query);
              if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: Results returned";
              
              
         //print "\n\n<!-- ***chj*** results:\n";
         //print_r($results['results']);
         //print "\n-->\n\n";
              
              

               $results['fieldsubs'] = array();
               $results['fields'] = array();
               $results['fieldsbyname'] = array();
               //if($subforeignfields) print "\n<!-- ***chj*** getRows() subforeignfields!!! -->\n";
               for ($i=0; $i<count($fields); $i++) {
                  $results['fields'][$fields[$i]['field_id']] = $fields[$i]['label'];
                  $results['fieldsbyname'][strtolower(trim($fields[$i]['label']))] = $fields[$i]['field_id'];
                  if($fields[$i]['map']!=NULL) $results['fieldsbyname'][strtolower(trim($fields[$i]['map']))] = $fields[$i]['field_id'];
                  if (0==strcmp($fields[$i]['field_type'],"FOREIGN") || 0==strcmp($fields[$i]['field_type'],"FOREIGNCB")) {
                     //print "\n<!-- ***chj*** found foreign field: ".$fields[$i]['field_id'].". -->\n";
                     $survey_info = separateStringBy(convertBack($fields[$i]['question']),",");
                     if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                        //print "\n<!-- ***chj*** name: ".$survey_info[0]." param: ".$survey_info[1].". -->\n";
                        $fld_subs = $this->getSurveyRowsIndexed($survey_info[0],$survey_info[1]);
                        $results['fieldsubs'][$fields[$i]['field_id']] = $fld_subs;
                     }
                  } else if (0==strcmp($fields[$i]['field_type'],"FOREIGNTBL") || 0==strcmp($fields[$i]['field_type'],"FOREIGNTDD")) {
                     //print "\n<!-- ***chj*** found foreign table: ".$fields[$i]['field_id'].". -->\n";
                     $survey_info = separateStringBy(convertBack($fields[$i]['question']),",");
                     if ($survey_info[0] != NULL && $survey_info[1] != NULL && $survey_info[2] != NULL) {
                        $query = "SELECT ".$survey_info[1].", ".$survey_info[2]." FROM ".$survey_info[0].";";
                        $tempresults = $dbi->queryGetResults($query);
                        $fld_subs = array();
                        for ($j=0;$j<count($tempresults);$j++) {
                           $fld_subs[$tempresults[$j][$survey_info[2]]] = $tempresults[$j][$survey_info[1]];
                        }
                        $results['fieldsubs'][$fields[$i]['field_id']] = $fld_subs;
                     }
                  }
               }
               
               if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: finished going through foreign fields";
              
               if ($subforeignfields && count($results['fieldsubs'])>0) {
                  for ($i=0;$i<count($results['results']);$i++) {
                     foreach($results['fieldsubs'] as $key => $val){
                        $ans_comma = convertBack($results['results'][$i][$key]);
                        $ans_arr = separateStringBy($ans_comma,",");
                        $newans = "";
                        for ($j=0;$j<count($ans_arr);$j++) {
                           if($val[$ans_arr[$j]]!=NULL) $newans .= $val[$ans_arr[$j]].",";
                           else if($ans_arr[$j]!=NULL && $ans_arr[$j]>=0) $newans .= $ans_arr[$j].",";
                        }
                        $results['results'][$i][$key] = $newans;
                     }
                  }
               }

               if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: after subbing foreighn fields";
                             
               if ($adduser) {
                  if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: adding user information, if any";
                  $ua = new UserAcct();
                  for ($i=0;$i<count($results['results']);$i++) {
                     
                     if($results['results'][$i]['userid']!=NULL && $results['results'][$i]['userid']>0) {
                        $user = $ua->getUser($results['results'][$i]['userid']);
                        if(strpos($user['email'],"dummy")!==FALSE) $user['email'] = "";
                        $arrkey = "userdata";
                        $results['results'][$i][$arrkey] = array();
                        foreach($user as $key=>$val) {
                           if(trim($key)!=NULL && trim($val)!=NULL && strlen($key)<20 && strlen($val)<512 && 0!=strcmp(substr($key,0,1),"q") && 0!=strcmp($key,"password") && 0!=strcmp($key,"password2") && 0!=strcmp($key,"token")){
                              $results['results'][$i][$arrkey][str_replace(" ","",strtolower($key))] = $val;
                           }
                        }                        
                     }
                     
                     for ($j=0; $j<count($fields); $j++) {
                        if (0==strcmp($fields[$j]['field_type'],"USERSRCH") || 0==strcmp($fields[$j]['field_type'],"USERAUTO") || 0==strcmp($fields[$j]['field_type'],"USERLIST")) {
                        //if (0==strcmp($fields[$j]['field_type'],"USERSRCH")) {
                           if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: found userfield: ".$fields[$j]['field_id']." type: ".$fields[$j]['field_type'];
                           if($results['results'][$i][$fields[$j]['field_id']]!=NULL) {
                              if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: value: ".$results['results'][$i][$fields[$j]['field_id']];
                              //$tempparams1 = $_SESSION['params'];
                              //$tempparams2 = $_POST;
                              //$tempparams3 = $_GET;
                              //$_SESSION['params']=array();
                              //$_POST=array();
                              //$_GET=array();
                              
                              $user = array();
                              $userids = separateStringBy(convertBack($results['results'][$i][$fields[$j]['field_id']]),",",NULL,TRUE);
                              if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: trying to add user: ".$userids[0];
                              if(count($userids)>0) $user = $ua->getFullUserInfo($userids[0]);

                              //$user = $ua->getFullUserInfo($results['results'][$i][$fields[$j]['field_id']]);
                              
                              //$_SESSION['params'] = $tempparams1;
                              //$_POST = $tempparams2;
                              //$_GET = $tempparams3;
                              if(strpos($user['email'],"dummy")!==FALSE) $user['email'] = "";
                              $arrkey = $fields[$j]['field_id']."_userdata";
                              $results['results'][$i][$arrkey] = array();
                              foreach($user as $key=>$val) {
                                 if(trim($key)!=NULL && trim($val)!=NULL && strlen($key)<20 && strlen($val)<512 && 0!=strcmp(substr($key,0,1),"q") && 0!=strcmp($key,"password") && 0!=strcmp($key,"password2") && 0!=strcmp($key,"token")){
                                    $results['results'][$i][$arrkey][str_replace(" ","",strtolower($key))] = $val;
                                 }
                              }
                           }
                        }
                     }
                  }
               }
               
               if($printdebug) print "<br>\n".date("Y-m-d H:i:s")." getRows:: after adding users";
               
              //$results['params'] = $searchObj['params'];
              $results['params'] = array();
              if(isset($searchObj['params'])) $results['params'] = $searchObj['params'];
              $results['orderby'] = $orderby;
              $results['limit'] = $limit;
           }
           
         //print "\n\n<!-- ***chj*** results:\n";
         //print_r($results['results']);
         //print "\n-->\n\n";
           
           
           return $results;
        }
        
        function getObjects($userid,$name,$indexed=TRUE){
           $results = NULL;
           $temp = NULL;
           if ($userid!=NULL && $name!=NULL) {
              $ua = new UserAcct();
              $user = $ua->getUser($userid);
              $webdata = $this->getWebDataByName($user['usertype']." objects ".$name);
              if ($webdata != NULL) {
                 $temp = $this->getRows($webdata['wd_id'], NULL, NULL, NULL, FALSE, $userid);
                 $results = $temp['results'];
              }
           }
           
           if ($indexed && $temp!=NULL){
               $results = array();
               $rows = $temp['results'];
               $qs = $temp['fields'];

               for ($i=0;$i<count($rows);$i++) {
                  $line = $rows[$i];
                  $newline = array();
                  foreach($line as $key => $val) {
                     if (isset($qs[$key])) $newline[$qs[$key]] = $val;
                     else $newline[$key] = $val;
                  }
                  $results[] = $newline;
               }
           }
           return $results;
        }
        
        function addObject($userid,$name,$values){
            if ($userid==NULL || $name==NULL) return FALSE;
            $ua = new UserAcct();
            $user = $ua->getUser($userid);
            $webdata = $this->getWebDataByName($user['usertype']." objects ".$name);
            if ($webdata != NULL) {
               $dbLink = new MYSQLaccess;
               $qs = $this->getFieldNames($webdata['wd_id']);
               $query = "INSERT INTO wd_".$webdata['wd_id']." (userid, created, lastupdate";
               $query2 = ") VALUES (".$userid.",NOW(),NOW()";
               foreach($values as $k => $v){
                  if(isset($qs[strtolower(trim($k))])) {
                     $query .= ",".$qs[strtolower(trim($k))];
                     $query2 .= ",'".convertString($v)."'";
                  } else {
                     $query .= ",".strtolower(trim($k));
                     $query2 .= ",'".convertString($v)."'";
                  }
               }
               $query = $query.$query2.");";               
               $dbLink->insert($query);
               return TRUE;
            } else {
               return FALSE;
            }
           
        }

        function updateObject($wd_row_id,$userid,$name,$values){
            if ($userid==NULL || $name==NULL) return FALSE;
            $ua = new UserAcct();
            $user = $ua->getUser($userid);
            $webdata = $this->getWebDataByName($user['usertype']." objects ".$name);
            if ($webdata != NULL) {
               $dbLink = new MYSQLaccess;
               $qs = $this->getFieldNames($webdata['wd_id']);
               $query = "UPDATE wd_".$webdata['wd_id']." SET lastupdate=NOW()";
               foreach($values as $k => $v){
                  if(isset($qs[strtolower(trim($k))])) {
                     $query .= ",".$qs[strtolower(trim($k))]."='".convertString($v)."'";
                  } else {
                     $query .= ",".strtolower(trim($k))."='".convertString($v)."'";
                  }
               }
               $query .= " WHERE wd_row_id=".$wd_row_id." AND userid=".$userid.";";
               $dbLink->update($query);
               return TRUE;
            } else {
               return FALSE;
            }
           
        }

        function doesCompanyExist ($wd_id,$company) {
           $query = "SELECT * FROM wd_".$wd_id." d, useracct u WHERE d.userid=u.userid && u.company='".$company."' AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'));";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return (count($results) > 0);
        }

        function doesEmailExist ($wd_id, $contact_email) {
           $results=$this->getDataBySurveyAndEmail($wd_id, $contact_email);
           return (count($results) > 0);
        }

        function getDataBySurveyAndEmail($wd_id, $email){
           $query = "SELECT * FROM wd_".$wd_id." d, useracct u WHERE d.userid=u.userid AND u.email='".$email."' AND (d.dbmode is NULL OR (d.dbmode<>'DELETED' AND d.dbmode<>'DUP'));";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results;
        }

        function getDataByUserid($wd_id, $userid, $orderby=NULL, $pub=FALSE, $subforeignfields=FALSE, $limit=500){
           //print "\n<!-- ***chj*** in getDataByUserid() -->\n";
           //print "\n<br> ***chj*** in getDataByUserid() <br>\n";
           if ($userid==NULL || $wd_id==NULL) return NULL;
           //if($subforeignfields) print "\n<!-- ***chj*** getDataByUserid() subforeignfields!!! -->\n";
           //print "\n<br> ***chj*** in getDataByUserid() getting rows... <br>\n";
           //function getRows($wd_id, $orderby=null, $limit=null, $filterStr=null, $countOnly=FALSE, $userid=NULL, $forCSV=FALSE, $pub=FALSE, $subforeignfields=FALSE, $ignoreSearchParams=FALSE, $shorteasy=FALSE, $qids=NULL, $page=1, $externalid=NULL, $adduser=FALSE, $printdebug=FALSE) {
           $results = $this->getRows($wd_id, $orderby,$limit, NULL, FALSE, $userid, FALSE, $pub, $subforeignfields, TRUE);
           //print "<br>\nresults:\n<br>";
           //print_r($results);
           //print "<br>\n<br>\n";
           return $results['results'];
        }

        function getDetailsByUniqueField($wd_id,$field_id,$field_value){
           if ($wd_id===NULL || $field_id === NULL || $field_value === NULL) return NULL;
           $query = "SELECT * FROM wd_".$wd_id." WHERE ".$field_id."='".$field_value."' AND (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'));";
           $dbi = new MYSQLAccess();
           $results = $dbi->queryGetResults($query);
           return $results[0];
        }

         function checkSerialNumber($serialnumber,$wd_id) {
            $row = $this->getDetailsByUniqueField($wd_id,"serialnumber",$serialnumber);
            if ($row!=NULL && $row['wd_row_id']>0) return TRUE;
            else return FALSE;
         }

        function getDetails ($wd_id,$wd_row_id,$pub=FALSE) {
           return $this->getRow($wd_id,$wd_row_id,NULL,$pub);
        }
        
        function getRow($wd_id,$wd_row_id=NULL,$origemail=NULL,$pub=FALSE,$printstuff=FALSE) {
           if ($wd_id==NULL || ($wd_row_id==NULL && $origemail==NULL)) return NULL;
           $query = "SELECT * FROM wd_".$wd_id;
           if ($pub) $query .= "_pub";
           $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
           if($wd_row_id!=NULL) $query.= " AND wd_row_id='".$wd_row_id."' ";
           if($origemail!=NULL) $query.= " AND origemail='".$origemail."' ";
           $dbi = new MYSQLAccess();
           if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." getRow query: ".$query."<br>\n";
           $results = $dbi->queryGetResults($query);
           return $results[0];
        }

   // Same as getting row details, but uses readable names instead of q field_id's as indexes
   function getDetailsClear($wd_id,$wd_row_id,$pub=FALSE) {
      $row = $this->getDetails($wd_id,$wd_row_id,$pub);
      $newrow = array();
      $flds = $this->getFieldsIndexed($wd_id);
      foreach($row as $name=>$val){
         //Get it by the field id, the label, and the map name
         $temp = $name;
         if ($temp!=NULL && $val!=NULL) {
            $newrow[$temp] = convertBack($val);
         }
         $temp = str_replace(" ","_",strtolower(trim($flds[$name]['label'])));
         if ($temp!=NULL && $val!=NULL) {
            $newrow[$temp] = convertBack($val);
         }
         $temp = str_replace(" ","_",strtolower(trim($flds[$name]['map'])));
         if ($temp!=NULL && $val!=NULL) {
            $newrow[$temp] = convertBack($val);
         }
      }
      return $newrow;
   }


   // Same as getting row details, but converts all strings back
   function getDetailsDecoded($wd_id,$wd_row_id,$pub=FALSE) {
      $row = $this->getDetails($wd_id,$wd_row_id,$pub);
      foreach($row as $name=>$val){
         $row[$name] = convertBack($val);
      }
      return $row;
   }


      function promoteRow($wd_id, $wd_row_id, $dbmode=NULL, $force=FALSE) {
         //print "\n<!-- websitedata id: ".$wd_id." row id: ".$wd_row_id." dbmode: ".$dbmode." -->\n";
         $dbi = new MYSQLAccess();
         $row = $this->getDetails($wd_id,$wd_row_id);
         if ($row!=NULL) {
            //print "\n<!-- websitedata promote row found row. -->\n";
            if (0==strcmp($row['dbmode'],"DELETED") && 0==strcmp($row['dbmode'],"DUP") && !$force) {
               $this->removeRow($wd_id, $wd_row_id,TRUE);
            } else {
               $this->startCloning($wd_id);
               if ($dbmode==NULL) $dbmode="APPROVED";
               $query = "delete from wd_".$wd_id."_pub WHERE wd_row_id=".$wd_row_id.";";
               $dbi->delete($query);
               $names = "dbmode";
               $values = "'".$dbmode."'";
               foreach($row as $name=>$val){
                  if (0!=strcmp($name,"dbmode") && $val!=NULL) {
                     $names .= ", ".$name;
                     $values .= ", '".$val."'";
                  }
               }

               $query = "INSERT INTO wd_".$wd_id."_pub (";
               $query .= $names;
               $query .= ") VALUES (";
               $query .= $values;
               $query .= ");";
               //print "\n<!-- websitedata promote query: ".$query." -->\n";
               $dbi->insert($query);
            }
         }
      }
        
      function revertRow($wd_id=NULL, $wd_row_id=NULL, $dbmode=NULL, $force=FALSE) {
         if($wd_id!=NULL && $wd_row_id!=NULL && $wd_row_id>0) {
            $dbi = new MYSQLAccess();
            $originalrow = $this->getDetails($wd_id,$wd_row_id);
            $row = $this->getDetails($wd_id,$wd_row_id,TRUE);
            if ($row!=NULL) {
               if ($originalrow==NULL) {
                  $this->copyPublicRow($wd_id,$wd_row_id);
               } else if ($dbmode==NULL && 0==strcmp($originalrow['dbmode'],"APPROVED") && !$force) {
                  $query = "UPDATE wd_".$wd_id." SET dbmode='REJECTED' WHERE wd_row_id=".$wd_row_id.";";
                  $dbi->update($query);
                  $query = "delete from wd_".$wd_id."_pub WHERE wd_row_id=".$wd_row_id.";";
                  $dbi->delete($query);
               } else {
                  if ($dbmode==NULL) $dbmode = $row['dbmode'];
                  $names = "dbmode='".$dbmode."'";
                  foreach($row as $name=>$val){
                     if (0!=strcmp($name,"dbmode") && $val!=NULL) $names .= ", ".$name."='".$val."'";
                     else $names .= ", ".$name."=NULL";
                  }
      
                  $query = "UPDATE wd_".$wd_id." SET ".$names." WHERE wd_row_id=".$wd_row_id.";";
                  $dbi->update($query);
               }
            } else {
               $query = "UPDATE wd_".$wd_id." SET dbmode='REJECTED' WHERE wd_row_id=".$wd_row_id.";";
               $dbi->update($query);
            }
         }
      }
        
      function copyPublicRow($wd_id, $wd_row_id) {
         $dbi = new MYSQLAccess();
         $row = $this->getDetails($wd_id,$wd_row_id,TRUE);

         $query = "DELETE FROM wd_".$wd_id." WHERE wd_row_id=".$wd_row_id.";";
         $dbi->delete($query);

         if ($row!=NULL) {
            $sql_names = "";
            $sql_values = "";
            foreach($row as $name=>$val){
               if($val!=NULL) {
                  if (strlen($sql_names)>0) $sql_names .= ", ";
                  if (strlen($sql_values)>0) $sql_values .= ", ";
                  $sql_names .= $name;
                  $sql_values .= "'".convertString($val)."'";
               }
            }
            $query = "INSERT INTO wd_".$wd_id." (".$sql_names.") VALUES (".$sql_values.");";
            $dbi->insert($query);
         }
      }
        
      function copyRow($wd_id, $wd_row_id, $newuserid=NULL) {
         $dbi = new MYSQLAccess();
         $row = $this->getDetails($wd_id,$wd_row_id,TRUE);

         if ($row!=NULL) {
            $userid = $row['userid'];
            if ($newuserid!=NULL) $userid = $newuserid;
            $new_rowid = $this->addRow($wd_id, $userid);
             
            $sql_str = "";
            foreach($row as $name=>$val){
               if($val!=NULL && 0==strcmp("q",substr($name,0,1)) && is_numeric(substr($name,1,1))) {
                  if (strlen($sql_str)>0) $sql_str .= ", ";
                  $sql_str .= $name."='".convertString($val)."'";
               }
            }
            $sql = "UPDATE wd_".$wd_id." SET ".$sql_str." WHERE wd_row_id=".$new_rowid.";";
            $dbi->update($sql);
         }
      }

      
      
      
      
      
      
      
      
      
      
      
      
      
      
   // -------------------------------------------------
   // -------------------------------------------------
   // Submit a survey and update DB/send email
   // Parameters:
   //    wd_id: identifier to table (can be name, shortname, or numeric identifier)
   //    wd_row_id: identifer to the table entry (if already exists) {optional}
   //    origemail: also identifies table entry in an encoded value {optional}
   //    updateStatus: sets record status to updated and saves time and user info
   //    lastupdateby: overrides logged in user to save user who makes updates
   //    ignorenull: boolean to represent if fields value who are null should be ignored and not updated in the DB to NULL - note: if %%%EMPTY%%% is sent, the field is nulled out in DB
   //    section: default to the full survey, otherwise this will only capture values from a particular section in survey
   //    sendemail: this can override the survey's setting to send an email upon submitting
   //    force: if no values are sent to this function, and you set force to FALSE, no new row will be created and nothing happens upon submission
   // Parameters read in off the URL and POST variables can be in the following formats:
   //    wXXXaYYY  Where XXX is the id of the table and YYY is the field_id of the field (w1aq1, for example)
   //    YYY Where YYY is the field_id of the field (q1, for example)
   //    label name minus special characters in lowercase (firstname, for example)
   //    any of the above with "_append" added - this will concatenate the value to the existing DB value
   // Parameters with special meaning
   //    backup=1 - this will promote the row before saving changes
   //    wXXXpassword - required if there is a password stored for the survey
   //
   function submitSurvey($wd_id=NULL,$wd_row_id=NULL, $updateStatus=true, $lastupdateby=NULL,$ignorenull=FALSE,$section=-1,$sendemail=TRUE,$force=TRUE, $origemail=NULL, $printdebug=FALSE) {
      if($printdebug) print "\n<br>Starting WebsiteData.submitSurvey()\n<br>";
      if ($wd_id==NULL) $wd_id = getParameter("wd_id");
      if ($wd_row_id==NULL) $wd_row_id = getParameter("wd_row_id");
      if ($origemail==NULL) $origemail = getParameter("origemail");
      if ($section==NULL) $section = getParameter("section");
      $serialnumber = getParameter("serialnumber");
      $userid = getParameter("userid");
      
      $errors = "";
      $template = new Template();
      $dbi = new MYSQLAccess();
      $ua = new UserAcct();           
      $webdata = $this->getWebData($wd_id,FALSE,$printdebug);
      if($printdebug) print "\n<br>Web Data Object:\n<br>";
      if($printdebug) print_r($webdata);
      if($printdebug) print "\n<br>\n<br>";
      
      $row = $this->getRow($webdata['wd_id'],$wd_row_id,$origemail);
      
      if($printdebug) print "\n\n\n<br><br><br>Row:\n<br>";
      if($printdebug) print_r($row);
      if($printdebug) print "\n<br>\n<br>\n<br>";
      
      $noupdate=FALSE;
      if (0==strcmp($row['dbmode'],"DELETED") || 0==strcmp($row['dbmode'],"NEW") || 0==strcmp($row['dbmode'],"REJECTED")) $noupdate=TRUE;
      //if($noupdate) print "\n\n<!-- ***chj 1 IGNORE UPDATE -->\n\n";
      
      //Check if we should stop processing this submission
      if ($webdata['privatesrvy']==52 && $serialnumber!=NULL && $this->checkSerialNumber($serialnumber,$webdata['wd_id'])){
         $webdata = NULL;
      }
      
      if ($webdata['password']!=NULL && strlen($webdata['password'])>0) {
         $password = getParameter("w".$wd_id."password");
         if (0!=strcmp($password,$webdata['password'])) $webdata = NULL;
      }
   
      // Either this is a private survey (in which case the entry already exists), or it's a public survey
      if (($webdata['privatesrvy']==1 && $row['wd_row_id']!=null) || ($webdata['privatesrvy']!=1 && $webdata['wd_id']!=null)) {
         if($printdebug) print "\n<br>WebsiteData.submitSurvey() PASSED: can submit a row to this survey!\n<br>";
        
         //Save user info if it's a survey...
         if (($webdata['privatesrvy']<3 || $webdata['privatesrvy']==101) && getParameter("skipuser")!=1) {
            $updateuserid = getParameter("updateuserid");   
            $newuserid = getParameter("newuserid");
            $email = trim(getParameter("email"));
            if ($newuserid==1) {
               if($printdebug) print "\n<br>adding user...<br>\n";
               $userid = $ua->addUserEmailOnly();
               if (!$userid) $errors .= "Please specify a valid email address.";
            //} else {
            } else if($updateuserid==1) {
               if($printdebug) print "\n<br>modifying user...<br>\n";
               $modified = $ua->modifyUser(FALSE);
               if(!$modified) $errors .= "Please specify a valid email address.";
               if($printdebug) print "\n<br>modified user...(".$errors.")<br>\n";
            }
            
            //Ability to submit a second survey within this survey
            $propId = getParameter("prop_wd_id");
            $propRowId = getParameter("prop_wd_row_id");
            if ($propId!=NULL && $propRowId!=NULL) $this->submitSurvey($propId,$propRowId);
         }
         
         //set up query and email contents for a submitted survey
         $emailcontents = $webdata['name']."\n\n";
         
         if($printdebug) print "\n<br>submitting section ".$section."...<br>\n";
         $resp = $this->submitSectionSQL($webdata['wd_id'],$section,$ignorenull,$printdebug);
         if($printdebug) print "\n<br>\n<br>Response from submitSectionSQL:\n<br>";
         if($printdebug) print_r($resp);
         if($printdebug) print "\n<br>\n<br>";
         $emailcontents .= $resp['emailcontents'];
         $query1 = $resp['query1'];
           
         //Sending comments will guarantee that a row is created in survey, even when force=FALSE
         $comments = getParameter("wd".$webdata['wd_id']."_comments");
         if($comments==NULL) $comments = getParameter("comments");
         if ($comments!=NULL) $query1 .= ", comments='".convertString($comments)."'";
               
         //Only if we are keeping results of survey in our DB
         if ($webdata['saveresults']==1) {
            $query = "UPDATE wd_".$webdata['wd_id']." SET lastupdate=NOW()";
            if (!$noupdate) $query .= ", dbmode='UPDATED'";
            //if($noupdate) print "\n\n<!-- ***chj 2 IGNORE UPDATE -->\n\n";
            //else print "\n\n<!-- ***chj 3 added update status -->\n\n";
            
            // Create a new row if it doesn't already exist
            if ($row['wd_row_id']==NULL && ($force || strlen($query1)>0)) {
               if($printdebug) print "\n<br>Adding a row to the table.\n<br>";
               
               // Create the row now... then continue with a follow-up update
               $row['wd_row_id'] = $this->addRow($webdata['wd_id']);
               
               // Reset the main query for dbmode, etc since this is new
               $query = "UPDATE wd_".$webdata['wd_id']." SET dbmode='NEW'";
               if ($userid!=NULL && is_numeric($userid)) $query .= ", userid='".$userid."'";
               if ($serialnumber!=NULL) $query .= ", serialnumber='".$serialnumber."'";
               $noupdate=TRUE;               
            }
            
            if($printdebug) print "\n<br>Looking for foreign links...";
            $externalid = getParameter("externalid");
            $p_wd_row_id = getParameter("o_wd_row_id");
            $p_wd_id = getParameter("o_wd_id");
            $p_field_id = getParameter("o_field_id");
            if($p_wd_id!=NULL && $p_field_id!=NULL && $p_wd_row_id!=NULL) {
               $tempwd = $this->getWebData($p_wd_id);
               if($tempwd!=NULL && $tempwd['wd_id']!=NULL){
                  $p_wd_id = $tempwd['wd_id'];
                  $qs = $this->getFieldLabels($p_wd_id,TRUE,TRUE);
                  $p_field_id = $qs[$p_field_id];
                  if($p_wd_id!=NULL && $p_field_id!=NULL && $p_wd_row_id!=NULL) {
                     if($printdebug) print "\n<br>calling addForeignSurveyLink";
                     // this only adds an entry if there isn't one already
                     $this->addForeignSurveyLink($p_wd_id,$p_field_id,$p_wd_row_id,$webdata['wd_id'],$row['wd_row_id'],$printdebug);
                     
                     // Also set external id for backup
                     if($externalid==NULL) $externalid=$p_wd_id."_".$p_field_id."_".$p_wd_row_id;
                  }
               }
            }
            if ($externalid!=NULL) $query1 .= ", externalid='".$externalid."'";
            
            if(strlen($query1)>0 && isset($row['wd_row_id']) && $row['wd_row_id']!=NULL) {
               if ($updateStatus) $query .= ", complete='Y'";
               if($lastupdateby==NULL) $lastupdateby = isLoggedOn();
               if($lastupdateby==NULL) $lastupdateby = "0";
               $lastupdateby .= " ".date("Y-m-d H:i:s");
               $query .= ", lastupdateby=SUBSTR(CONCAT('".$lastupdateby."',IFNULL(lastupdateby,' ')),1,2048)";
               
               $esign = getParameter("w".$webdata['wd_id']."esign");
               if($esign==NULL) $esign = getParameter("esignature");
               if ($esign!=NULL) $query .= ", esignature='".convertString($esign)."'";
               $query .= $query1;
               
               // take a snapshot if we're told to back this up
               if(getParameter("backup")==1) $this->promoteRow($webdata['wd_id'], $row['wd_row_id']);
               $query .= " WHERE wd_row_id=".$row['wd_row_id'].";";
               $dbi->update($query);
               //print "\n\n<!-- QUERY: ".$query." -->\n\n";
               
               //$row = $this->getRow($wd_id,$row['wd_row_id']);
            }
         }
      
         //only email if they survey is flagged to email
         if ($webdata['emailresults']==1 && $sendemail)  {
            $ver = new Version();
            $sched = new Scheduler();
            $sched->addSchedEmail(NULL,NULL,$emailcontents,$webdata['name'],5,NULL,$ver->getValue("WebsiteContact"),5,TRUE,NULL,NULL,$webdata['adminemail']);
         }
      
      }
   
     return $row['wd_row_id'];
   }
   
   // This function is created only for legacy calls to the former submit section
   function submitSection($wd_row_id, $section, $wd_id, $ignorenull=FALSE, $updateStatus=FALSE){
      if($wd_row_id==NULL) return NULL;
      
      $resp = $this->submitSectionSQL($wd_id,$section,$ignorenull);
      
      $dbi = new MYSQLAccess();
      $query1 = "UPDATE wd_".$wd_id." SET lastupdate=NOW()";
      $lastupdateby = isLoggedOn();
      if($lastupdateby==NULL) $lastupdateby = "0";
      $lastupdateby .= " ".date("Y-m-d H:i:s");
         
      if ($updateStatus) $query1 .= ", complete='Y'";
      $query1 .= ", dbmode='UPDATED'";
      $query1 .= ", lastupdateby=SUBSTR(CONCAT('".$lastupdateby.", ',IFNULL(lastupdateby,' ')),1,2048)";
      $query1 .= $resp['query1'];
      $query1 .= " WHERE wd_row_id=".$wd_row_id.";";
      $dbi->update($query1);
      
      return $resp['emailcontents'];
   }
   
   function submitSectionSQL($wd_id,$section=-1,$ignorenull=FALSE, $printdebug=FALSE) {
      if($printdebug) print "<br>\nIn submitSectionSQL for section ".$section;
      $emailcontents = "";
      $query1 = "";
      $dbi = new MYSQLAccess();
      $webdata = $this->getWebData($wd_id);
      $s = $this->getSection($webdata['wd_id'],$section);
      $emailcontents .= "============================================\n==== ".$s['label']." ====\n";
      
      $questions = $this->getFields($webdata['wd_id'], $section);
      for ($j=0; $j<count($questions); $j++) {
         if($printdebug) print "<br>\nCalling submitQuestionSQL for ".$questions[$j]['field_id'];
         $resp = $this->submitQuestionSQL($webdata['wd_id'],$questions[$j],$ignorenull,$printdebug);
         $emailcontents .= $resp['emailcontents'];
         $query1 .= $resp['query1'];
      }
      
      $sections = $this->getDataSections($webdata['wd_id'], $section);
      for ($j=0; $j<count($sections); $j++) {
         $resp = $this->submitSectionSQL($webdata['wd_id'], $sections[$j]['section'],$ignorenull,$printdebug);
         $emailcontents .= $resp['emailcontents'];
         $query1 .= $resp['query1'];
      }
      
      $resp = array();
      $resp['emailcontents'] = $emailcontents;
      $resp['query1'] = $query1;
      return $resp;
   }
               
   function submitQuestionSQL($wd_id,$q,$ignorenull=FALSE,$printdebug=FALSE){
      if($printdebug) print "<br>\nIn submitQuestionSQL for question ".$q['field_id'];
      
      $query1 = "";
      if (strcmp($q['field_type'],"INFO")!=0 && strcmp($q['field_type'],"SPACER")!=0) {
         $origAnsName = "a".$q['field_id'];
         $answerName = "w".$wd_id.$origAnsName;
         $alternate = $q['field_id'];
         $alternate2 = str_replace("-","",str_replace(".","",str_replace(",","",str_replace("\"","",str_replace("'","",str_replace(" ","_",strtolower(trim($q['label']))))))));
         $alternate3 = strtolower(trim($q['map']));
         
         $appendstr = FALSE;
         $a = getParameter($answerName);
         if ($a==NULL) {
            $a = getParameter($alternate);            
            if ($a==NULL) {
               $a = getParameter($alternate2);
               if ($a==NULL) {
                  $a = getParameter($alternate3);
                  if ($a==NULL) {
                     $a = getParameter($answerName."_append");            
                     if ($a==NULL) {
                        $a = getParameter($alternate."_append");
                        if ($a==NULL) {
                           $a = getParameter($alternate2."_append");
                           if ($a==NULL) {
                              $a = getParameter($alternate3."_append");
                              if ($a!=NULL) $appendstr = TRUE;
                           } else {
                              $appendstr = TRUE;
                           }
                        } else {
                           $appendstr = TRUE;
                        }
                     } else {
                        $appendstr = TRUE;
                     }
                  }
               }
            }
         }
         
         if($printdebug) print "<br>\nChecking ".$q['field_id']." (".$q['field_type'].") [map: ".$q['map']."] found in parameters: ".$a."<br>\n";
         
         $answerSeparator = ", ";
         if (strcmp($q['field_type'],"DATE")==0 || strcmp($q['field_type'],"DATETIME")==0 || strcmp($q['field_type'],"AGE")==0) {
            $dateStr = $a;
            if ($dateStr==NULL) {
               $y = getParameter("w".$wd_id."date_".$q['field_id']."_y");
               $m = getParameter("w".$wd_id."date_".$q['field_id']."_m");
               $d = getParameter("w".$wd_id."date_".$q['field_id']."_d");
               if ($y!=NULL && $m!=NULL && $d!=NULL) {
                  $dateStr .= $y."-".$m."-".$d;
               }
            }

            if ($dateStr!=NULL) {
               $hourStr = getParameter("w".$wd_id."time_".$q['field_id']."_hour");
               $minStr = getParameter("w".$wd_id."time_".$q['field_id']."_min");
               $todStr = getParameter("w".$wd_id."time_".$q['field_id']."_tod");
               
               $temparr = separateStringBy(trim($dateStr)," ");
               $temp = separateStringBy($temparr[0],"/");
               if ($temp!=NULL && count($temp)==3) {
                  $dateStr = "";
                  if(strlen($temp[2])<4) $dateStr .= "20";
                  $dateStr .= $temp[2];
                  $dateStr .= "-";
                  if(strlen($temp[0])<2) $dateStr .= "0";
                  $dateStr .= $temp[0];
                  $dateStr .= "-";
                  if(strlen($temp[1])<2) $dateStr .= "0";
                  $dateStr .= $temp[1];
                  
                  $timestr = separateStringBy($temparr[1],":");
                  if($timestr!=NULL && count($timestr)>1) {
                     $hourStr = $timestr[0];
                     $minStr = $timestr[1];
                     $todStr = "AM";
                     if($hourStr>11) $todStr = "PM";
                  }
               }
               
               if ($hourStr != null && $minStr != null && $todStr != null) {
                  if (0==strcmp($todStr,"PM") && $hourStr<12) $hourStr += 12;
                  else if (0==strcmp($todStr,"AM") && $hourStr==12) $hourStr = "00";
                  $dateStr .= " ".$hourStr.":".$minStr;
               }
            }
            $a = $dateStr;
         } else if (strcmp($q['field_type'],"FILE")==0 || strcmp($q['field_type'],"IMAGE")==0 || strcmp($q['field_type'],"MBL_UPL")==0) {
            if (is_uploaded_file($_FILES[$answerName]['tmp_name'])) {
                  $counter = 0;
                  $fileUpld = "s".$webdata['wd_id']."_".$q['field_id']."_sp".$counter."_".$_FILES[$answerName]['name'];
                  while(file_exists($GLOBALS['srvyDir'].$fileUpld)){
                     $counter++;
                     $fileUpld = "s".$webdata['wd_id']."_q".$q['field_id']."_sp".$counter."_".$_FILES[$answerName]['name'];
                  }
                  move_uploaded_file($_FILES[$answerName]['tmp_name'],$GLOBALS['srvyDir'].$fileUpld);
                  $a = $GLOBALS['srvyURL'].$fileUpld;
                  //if (strcmp($q['field_type'],"IMAGE")==0 || strcmp($q['field_type'],"MBL_UPL")==0) && 0!=strcmp($q['defaultval'],"%%%EMPTY%%%")) {
                  $ext = getExtension($fileUpld);
                  if (strcmp($ext,".jpg")==0 || strcmp($ext,".jpeg")==0 || strcmp($ext,".png")==0) {
                     $jsfi = new JSFImage();
                     $chkld = $jsfi->load($GLOBALS['srvyDir'].$fileUpld);
                     if ($chkld) {
                        $width = 1200;
                        $height = 800;
                        if ($q['defaultval']!=NULL && is_numeric($q['defaultval']) && $q['defaultval']>20) {
                           $width = $q['defaultval'];
                           $height = $q['defaultval'];
                        }
                        if($jsfi->getWidth()>$width || $jsfi->getHeight()>$height) {
                           $jsfi->resizeToRectangle($width,$height);
                           $jsfi->save($GLOBALS['srvyDir'].$fileUpld);
                        }
                        $jsfi->closeimage();
                     } else {
                        $jsfi->closeimage();
                        unlink($GLOBALS['srvyDir'].$fileUpld);
                        $a = "";
                     }
                  }
            } else if (getParameter("w".$wd_id."del_".$origAnsName) == 1) {
               $tmp = getParameter("w".$wd_id."o_".$origAnsName);
               $tmp = str_replace($GLOBALS['srvyURL'],"",$tmp);
               if ($tmp!=NULL) unlink($GLOBALS['srvyDir'].$tmp);
               $a = "";
            } else if (getParameter("w".$wd_id."o_".$origAnsName) != null) {
               $a = getParameter("w".$wd_id."o_".$origAnsName);
            }
         } else if (getParameter("w".$wd_id."m".$q['field_id'])!=NULL && getParameter("w".$wd_id."m".$q['field_id'])==1) {
            $a = "";
            foreach (getParameter($answerName) as $val) {
               $val = convertString($val);
               $a .= $val.$answerSeparator;
            }
            $a = substr($a,0,(strlen($a)-strlen($answerSeparator)));
            //$a = implode(getParameter($answerName));
         } else if (getParameter("w".$wd_id."jsfarray".$q['field_id'])!=NULL && getParameter("w".$wd_id."jsfarray".$q['field_id'])>0) {
            $counter = getParameter("w".$wd_id."jsfarray".$q['field_id']);
            $foundAny = FALSE;
            $a = "";
            for ($k=0; $k<$counter; $k++) {
               $val = convertString(trim(getParameter("w".$wd_id."a".$q['field_id']."_".$k)));
               if ($val != NULL && 0 != strcmp($val,"")) $foundAny=TRUE;
               if (0==strcmp($val,"%E%") || 0==strcmp($val,"%%%EMPTY%%%")) $val="";
               $a .= $val.$answerSeparator;
            }
            $a = substr($a,0,(strlen($a)-strlen($answerSeparator)));
            if (!$foundAny) $a=NULL;
         } else if (is_array($a)) {
            $a = implode(",",$a);
         } else {
            $a = convertString($a);
         }
         
         $dbi = new MYSQLAccess();
         if (0==strcmp($a,"%%%EMPTY%%%") || 0==strcmp($a,"%E%")) $query1 .= ", ".$q['field_id']."=NULL";
         else if($appendstr) $query1 .= ",".$q['field_id']."=CONCAT(IFNULL(".$q['field_id'].",' '),'".$dbi->escape($a)."')";
         else if(!$ignorenull && $a==NULL) $query1 .= ", ".$q['field_id']."=NULL";
         else if($a!=NULL) $query1 .= ", ".$q['field_id']."='".$dbi->escape($a)."'";

         //Write the quesiton to the email even if the answer may be blank
         if ($a == null) $a="<no response>";
         $emailcontents .= $q['label'].": ".$a."\n";
      }
      
      if($printdebug && strlen($query1)>0) print "<br>\nQuestion update query: ".$query1."\n<br>";
      else if($printdebug) print "<br>\nQuestion update query is empty.\n<br>";
      
      $resp = array();
      $resp['emailcontents'] = $emailcontents;
      $resp['query1'] = $query1;
      return $resp;
   }

   // END: Submit a survey and update DB/send email      
   // -------------------------------------------------------------------
   // -------------------------------------------------------------------




   function getAnswersString($wd_id,$wd_row_id){
      $str = "";
      $wd = $this->getWebData($wd_id);
      $str .= convertBack($wd['name'])."\n-----------------------------------------------\n";
      $row = $this->getDetails($wd_id,$wd_row_id);
      return $this->getSectionAnswersString($wd_id,-1,$row,$str);
   }

   function getSectionAnswersString($wd_id,$section,$row,$str){
      $sectionObj = $this->getSection($wd_id,$section);
      $str .= "\n============================================\n";
      if ($sectionObj['label']!=NULL) $str .= "==== ".convertBack($sectionObj['label'])." ====\n";

      $questions = $this->getFields($wd_id, $section);
      for ($j=0; $j<count($questions); $j++) {
         $q = $questions[$j];
         if (strcmp($q['field_type'],"SPACER")==0){
            $str .= "\n";
         } else if (strcmp($q['field_type'],"INFO")==0){
            //$str .= $q['label']."\n";
         } else {
            $str .= convertBack($q['label']).": ".convertBack($row[$q['field_id']])."\n";
         }
      }
      
      $sections = $this->getDataSections($wd_id,$section);
      for ($i=0; $i<count($sections); $i++) {
         $str = $this->getSectionAnswersString($wd_id,$sections[$i]['section'],$row,$str);
      }
      return $str;
   }


        function getCodedRow($wd_id,$origemail) {
           return $this->getRow($wd_id,NULL,$origemail);
        }



        function sendEmail ($wd_id, $wd_row_id) {
            //print "<br>wd_id: ".$wd_id." row: ".$wd_row_id."<br>";
            $ua=new UserAcct();
            $dbi = new MYSQLAccess();
            $webdata = $this->getWebData($wd_id);
            $row = $this->getDetails($wd_id, $wd_row_id);
            $touserid = $row['userid'];
            $from = $webdata['adminemail'];
            if ($from==null) $from = $GLOBALS['defaultEmail'];

            if (class_exists("CompanyInfo")) {
               $ver = new Version();
               $compInfo = new CompanyInfo();
               $fileInfo = $ver->getAsciiFileContents($webdata['filename'],$ver->getCurrentTheme());
               
               //print "<br>\nfileinfo:<br>\n";
               //print_r($fileInfo);
               //print "<br><BR>\n\n";
               
               $contents = str_replace("%%%PARAM_SECURITYCODE_PARAM%%%",$row['origemail'],$fileInfo['contents']);
               $contents = str_replace("%%%PARAM_WD_ID_PARAM%%%",$wd_id,$contents);
               
               $subject = $fileInfo['title'];
               if ($subject==NULL) $subject = $fileInfo['metadescr'];
               if ($subject==NULL) $subject = $fileInfo['filetitle'];
               
               $compInfo->sendCompanyEmail($touserid,$webdata['userrel'],$contents,$subject,$from,$fileInfo['contenttype']);
            } else {
               //$reluser = $ua->getUsersRelated($row['userid'],"to","SRVYADMIN");
               $reluser = $this->getUsersRelated($webdata,$row['userid']);
               if ($reluser!=NULL && $reluser[0]['reluserid']>0) $touserid = $reluser[0]['reluserid'];
               $sched = new Scheduler();
               $sched->addSchedEmail(NULL,$webdata['filename'],NULL, NULL, NULL, $touserid,$from,4,FALSE,"SECURITYCODE,WD_ID",$row['origemail'].",".$wd_id);
            }
            $query = "UPDATE wd_".$wd_id." SET datesent=CURRENT_DATE WHERE wd_row_id=".$wd_row_id.";";
            $dbi->update($query);
        }

        function adminUpdateMultipleSectionsFields($wd_id=NULL){
            if ($wd_id==NULL) {
               $name         = getParameter("wnew-name");
               $info         = getParameter("wnew-info");
               $privatesrvy  = getParameter("wnew-privatesrvy");
               $adminemail   = getParameter("wnew-adminemail");
               $filename     = getParameter("wnew-filename");
               $saveresults  = getParameter("wnew-saveresults");
               $emailresults = getParameter("wnew-emailresults");
               $userid       = getParameter("wnew-userid");
               $externalid   = getParameter("wnew-externalid");
               $shortname    = getParameter("wnew-shortname");
               if ($name!=NULL) {
                  $wd_id = $this->newWebData($name, $info, $privatesrvy, $adminemail, $filename, $saveresults, $emailresults,NULL,NULL,$externalid,NULL,NULL,NULL,NULL,$shortname);
                  if ($userid!=NULL && $wd_id!=NULL) {
                     $ua = new UserAcct;
                     $ua->addUserAccess($userid,"WDATA",$wd_id);
                  }
               }
            } else {
               $webdata = $this->getWebData($wd_id);
               /*
               $name         = getParameter("w".$webdata['wd_id']."-name");
               $shortname    = getParameter("w".$webdata['wd_id']."-shortname");
               $info         = getParameter("w".$webdata['wd_id']."-info");
               $adminemail   = getParameter("w".$webdata['wd_id']."-adminemail");
               $emailresults = getParameter("w".$webdata['wd_id']."-emailresults");
               $saveresults  = getParameter("w".$webdata['wd_id']."-saveresults");
               $privatesrvy  = getParameter("w".$webdata['wd_id']."-privatesrvy");
               $status       = getParameter("w".$webdata['wd_id']."-status");
               if ($name!=NULL)         $webdata['name'] = $name;
               if ($shortname!=NULL)    $webdata['shortname'] = $shortname;
               if ($info!=NULL)         $webdata['info'] = $info;
               if ($adminemail!=NULL)   $webdata['adminemail'] = $adminemail;
               if ($emailresults!=NULL) $webdata['emailresults'] = $emailresults;
               if ($saveresults!=NULL)  $webdata['saveresults'] = $saveresults;
               if ($privatesrvy!=NULL)  $webdata['privatesrvy'] = $privatesrvy;
               if ($status!=NULL)       $webdata['status'] = $status;
               $this->updateWebData($wd_id, $webdata['name'], $webdata['info'], $webdata['privatesrvy'], $webdata['adminemail'], $webdata['filename'], $webdata['saveresults'], $webdata['emailresults'],$webdata['glossaryid'], $webdata['status'], $webdata['externalid'], $webdata['starttime'], $webdata['endtime'], $webdata['field1'], $webdata['field2'], $webdata['field3'], $webdata['field4'], $webdata['shortname'], $webdata['password'], $webdata['captcha'], $webdata['userrel']);
               */
            }

            $fields = $this->getAllFieldsSystem($wd_id);
            $sections = $this->getAllDataSections($wd_id);
            
            for ($i=0;$i<count($fields);$i++) {
               $f = $this->getField($wd_id, $fields[$i]['field_id']);
               $parent_s   = getParameter($f['field_id']."-parent_s");
               $sequence   = getParameter($f['field_id']."-sequence");
               $label      = getParameter($f['field_id']."-label");
               $field_type = getParameter($f['field_id']."-field_type");
               $question   = getParameter($f['field_id']."-question");
               $defaultval = getParameter($f['field_id']."-defaultval");
               $privacy    = getParameter($f['field_id']."-privacy");
               $header     = getParameter($f['field_id']."-header");
               $required   = getParameter($f['field_id']."-required");
               $srchfld    = getParameter($f['field_id']."-srchfld");
               $filterfld  = getParameter($f['field_id']."-filterfld");
               $notes      = getParameter($f['field_id']."-notes");
               $map        = getParameter($f['field_id']."-map");
               $style      = getParameter($f['field_id']."-style");
               $disa       = getParameter($f['field_id']."-disa");
               $hide       = getParameter($f['field_id']."-hide");

               if ($parent_s!==NULL || $sequence!==NULL || $label!==NULL || $field_type!==NULL || $question!==NULL || $defaultval!==NULL || $privacy!==NULL || $header!==NULL || $required!==NULL || $srchfld!==NULL || $filterfld!==NULL || $notes!==NULL || $style!==NULL || $map!==NULL || $disa!==NULL || $hide!==NULL) {
                  if ($parent_s!==NULL)   $f['parent_s'] = $parent_s;
                  if ($sequence!==NULL)   $f['sequence'] = $sequence;
                  if ($label!==NULL)      $f['label'] = $label;
                  if ($field_type!==NULL) $f['field_type'] = $field_type;
                  if ($question!==NULL)   $f['question'] = $question;
                  if ($defaultval!==NULL) $f['defaultval'] = $defaultval;
                  if ($privacy!==NULL)    $f['privacy'] = $privacy;
                  if ($header!==NULL)     $f['header'] = $header;
                  if ($required!==NULL)   $f['required'] = $required;
                  if ($srchfld!==NULL)    $f['srchfld'] = $srchfld;
                  if ($filterfld!==NULL)  $f['filterfld'] = $filterfld;
                  if ($notes!==NULL)      $f['notes'] = $notes;
                  if ($map!==NULL)        $f['map'] = $map;
                  if ($style!==NULL)      $f['stylecss'] = $style;
                  if ($disa!==NULL)       $f['disa'] = $disa;
                  if ($hide!==NULL)       $f['hide'] = $hide;
                  $this->updateField($wd_id,$f['parent_s'],$f['field_id'],$f['label'],$f['question'],$f['field_type'],$f['sequence'],$f['privacy'],$f['header'],$f['defaultval'],$f['required'],$f['srchfld'],$f['notes'],$f['filterfld'],$f['stylecss'],$f['map'],$f['disa'],$f['hide']);
               }
            }

            $label = getParameter("new-label");
            if ($label!==NULL) {
               $this->addField($wd_id, getParameter("new-parent_s"),NULL,$label,getParameter("new-question"),"MBL_MC", getParameter("new-sequence"),0,getParameter("new-header"));
            }

            for ($i=0;$i<count($sections);$i++) {
               $s = $this->getSection($wd_id, $sections[$i]['section']);

               $parent_s   = getParameter("s".$s['section']."-parent_s");
               $sec_type   = getParameter("s".$s['section']."-sec_type");
               $sequence   = getParameter("s".$s['section']."-sequence");
               $label      = getParameter("s".$s['section']."-label");
               $dyna       = getParameter("s".$s['section']."-dyna");
               $question   = getParameter("s".$s['section']."-question");
               $param1     = getParameter("s".$s['section']."-param1");
               $param2     = getParameter("s".$s['section']."-param2");
               $param3     = getParameter("s".$s['section']."-param3");
               $param4     = getParameter("s".$s['section']."-param4");
               $param5     = getParameter("s".$s['section']."-param5");
               $param6     = getParameter("s".$s['section']."-param6");

               if ($parent_s!==NULL || $sec_type!==NULL || $sequence!==NULL || $label!==NULL || $dyna!==NULL || $question!==NULL || $param1!==NULL || $param2!==NULL || $param3!==NULL || $param4!==NULL || $param5!==NULL || $param6!==NULL) {
                  if ($parent_s!==NULL)   $s['parent_s'] = $parent_s;
                  if ($sec_type!==NULL)   $s['sec_type'] = $sec_type;
                  if ($sequence!==NULL)   $s['sequence'] = $sequence;
                  if ($label!==NULL)      $s['label'] = $label;
                  if ($dyna!==NULL)       $s['dyna'] = $dyna;
                  if ($question!==NULL)   $s['question'] = $question;
                  if ($param1!==NULL)     $s['param1'] = $param1;
                  if ($param2!==NULL)     $s['param2'] = $param2;
                  if ($param3!==NULL)     $s['param3'] = $param3;
                  if ($param4!==NULL)     $s['param4'] = $param4;
                  if ($param5!==NULL)     $s['param5'] = $param5;
                  if ($param6!==NULL)     $s['param6'] = $param6;
                  $this->updateSection($wd_id,$s['section'],$s['parent_s'],$s['sec_type'],$s['label'],$s['sequence'],$s['dyna'],$s['question'],$s['param1'],$s['param2'],$s['param3'],$s['param4'],$s['param5'],$s['param6']);
               }
            }

            $sequence = getParameter("snew-sequence");
            if ($sequence!==NULL) {
               $this->addSection ($wd_id,getParameter("snew-parent_s"),"",getParameter("snew-label"),$sequence,NULL,NULL,getParameter("snew-param1"));
            }
            return $wd_id;
        }

        function submitMultipleSurveys($updateStatus=true) {
            $wds = getParameter("wd");
            $rows = getParameter("wd_row_id");
            $userid = getParameter("userid");
            $names = getParameter("name");
            $emailcontents = NULL;
            for ($i=1; $i<=count($wds); $i++) {
               $webdata = $this->getWebData($wds[$i]);
               //print "\n<!-- ***chj*** submitMultipleSurveys webdata: ".$webdata['name']." -->\n";
               //print "\n<!-- ***chj*** submitMultipleSurveys row: ".$rows[$i]." -->\n";
               if ($rows[$i] == null) {
                  if ($webdata['saveresults']==1 && $webdata['privatesrvy'] != 1) {
                     //prevent an empty row from being saved... make sure there's at least one answer to save to the DB
                     $emptyAnswers = true;
                     $fields = $this->getAllFieldsSystem($webdata['wd_id']);
                     for ($j=0; $j<count($fields); $j++) {
                        $ansArr = getParameter("w".$webdata['wd_id']."a".$fields[$j]['field_id']);
                        if ($ansArr[$i]!=NULL) {
                           $emptyAnswers = false;
                           break;
                        }
                     }
                     if ($emptyAnswers) $webdata=null;
                     else {
                        //need to insert a new row into srvy_person table
                        if ($userid == null) $userid = isLoggedOn();
                        $row = $this->getDetails($wds[$i],$this->addRow($webdata['wd_id'],$userid));
                        $emailcontents = "You have received a new submission for ".getDefaultTitle()." ".$webdata['name']."\n\n";
                     }
                  }
               } else {
                  $row = $this->getDetails($wds[$i],$rows[$i]);
               }
               
               if (($webdata['privatesrvy']==1 && $rows[$i] != null) || ($webdata['privatesrvy']!=1 && $webdata != null)) {
                  //Only if we are keeping results of survey in our DB
                  if ($webdata['saveresults']==1) {
                     $count = 0;
                     $fields = $this->getAllFieldsSystem($webdata['wd_id']);
                     for ($j=0; $j<count($fields); $j++) {
                        $field = $fields[$j];
                        if (strcmp($field['field_type'],"INFO")!=0 && strcmp($field['field_type'],"SPACER")!=0) {
                           
                           $multiples = getParameter("w".$webdata['wd_id']."m".$field['field_id']);
                           $arrays = getParameter("w".$webdata['wd_id']."jsfarray".$field['field_id']);
                           $origAnsName = "a".$field['field_id'];
                           $answerName = "w".$webdata['wd_id'].$origAnsName;
                           $answer = getParameter($answerName);
                           $a = "";
                           $answerSeparator = ", ";
                           
                           //print "\n<!-- ***chj*** submitMultipleSurveys (multiples,arrays,ans)\n";
                           //print "\nmultiples:\n";
                           //print_r($multiples);
                           //print "\narrays\n";
                           //print_r($arrays);
                           //print "\norig answername: ".$origAnsName." answername: ".$answerName." answer: ";
                           //print_r($answer);
                           //print "\n-->\n";
                           
                           
                           if (strcmp($field['field_type'],"DATE")==0 || strcmp($field['field_type'],"DATETIME")==0 || strcmp($field['field_type'],"AGE")==0) {
                              $year = getParameter("w".$webdata['wd_id']."date_".$field['field_id']."_y");
                              $month = getParameter("w".$webdata['wd_id']."date_".$field['field_id']."_m");
                              $day = getParameter("w".$webdata['wd_id']."date_".$field['field_id']."_d");
                              $dateStr = "";
                              if ($year[$i] != null && $month[$i]!=null && $day[$i]!=null) {
                                 $dateStr .= $year[$i]."-".$month[$i]."-".$day[$i];
                                 $hourStr = getParameter("w".$webdata['wd_id']."time_".$field['field_id']."_hour");
                                 $minStr = getParameter("w".$webdata['wd_id']."time_".$field['field_id']."_min");
                                 $todStr = getParameter("w".$webdata['wd_id']."time_".$field['field_id']."_tod");
                                 if ($hourStr[$i] != null && $minStr[$i] != null && $todStr[$i] != null) {
                                    if (0==strcmp($todStr[$i],"PM") && $hourStr[$i]<12) $hourStr[$i] += 12;
                                    else if (0==strcmp($todStr[$i],"AM") && $hourStr[$i]==12) $hourStr[$i] = "00";
                                    $dateStr .= " ".$hourStr[$i].":".$minStr[$i];
                                 }
                              } else if ($answer!=NULL && 0!=strcmp(trim($answer),"")) $dateStr = $answer[$i];
                              $a = $dateStr;
                           } else if (strcmp($field['field_type'],"FILE")==0 || strcmp($field['field_type'],"IMAGE")==0 || strcmp($field['field_type'],"MBL_UPL")==0) {
                              $originals = getParameter("w".$webdata['wd_id']."o_".$origAnsName);
                              if (is_uploaded_file($_FILES[$answerName]['tmp_name'][$i])) {
                                    $counter = 0;
                                    $fileUpld = "w".$webdata['wd_id']."_".$field['field_id']."_sp".$row['wd_row_id']."_".$counter."_".$_FILES[$answerName]['name'][$i];
                                    while(file_exists($GLOBALS['srvyDir'].$fileUpld)){
                                       $counter++;
                                       $fileUpld = "w".$webdata['wd_id']."_q".$field['field_id']."_sp".$row['wd_row_id']."_".$counter."_".$_FILES[$answerName]['name'][$i];
                                    }
                                    move_uploaded_file($_FILES[$answerName]['tmp_name'][$i],$GLOBALS['srvyDir'].$fileUpld);
                                    $a = $GLOBALS['srvyURL'].$fileUpld;
                              } else if ($originals[$i] != null) {
                                 $a = $originals[$i];
                              }
                           } else if ($multiples[$i]!=NULL && $multiples[$i]==1) {
                              foreach ($answer[$i] as $val) {
                                 $val = convertString($val);
                                 $a .= $val.$answerSeparator;
                              }
                              $a = substr($a,0,(strlen($a)-strlen($answerSeparator)));
                           } else if ($arrays[$i]!=NULL && $arrays[$i]>0) {
                              $foundAny = FALSE;
                              for ($k=0; $k<$arrays[$i]; $k++) {
                                 $temp = getParameter("w".$webdata['wd_id']."a".$field['field_id']."_".$k);
                                 $val = convertString(trim($temp[$i]));
                                 if ($val != NULL && 0 != strcmp($val,"")) $foundAny=TRUE;
                                 $a .= $val.$answerSeparator;
                              }
                              $a = substr($a,0,(strlen($a)-strlen($answerSeparator)));
                              if (!$foundAny) $a=NULL;
                           } else {
                              $a = convertString($answer[$i]);
                           }

                           if ($a != null && 0!=strcmp($a,"")) {
                              //print "\n<!-- ***chj*** submitMultipleSurveys setting: ".$field['field_id']." with: ".$a." -->\n";
                              $this->setAnswer($wds[$i],$row['wd_row_id'],$field['field_id'],$a);
                           } else {
                              //print "\n<!-- ***chj*** submitMultipleSurveys not setting: ".$field['field_id']." -->\n";
                           }
                           
                           if ($emailcontents != NULL) $emailcontents.=$field['label'].": ".$a."\n";
                        }
                     }
                     if ($webdata['emailresults']==1 && $emailcontents!=NULL) sendEmail($webdata['adminemail'],$webdata['name'],$emailcontents);
                  }
               }// end private check
            }
        }

      function getSearchHTMLAllFields($wdname=NULL, $wd_id=NULL, $exceptions=NULL, $small=FALSE){
         $returnHTML = "";
         $returnJS = "";
         $webdata = NULL;
         if ($wdname!=NULL && 0!=strcmp(trim($wdname),"")) {
            $webdata = $this->getWebDataByName($wdname);
         } else if ($wd_id!=NULL && $wd_id>0) {
            $webdata = $this->getWebData($wd_id);
         }
         if ($webdata!=NULL && $webdata['wd_id']>0) {
            //$questions = $this->getAllFields($webdata['wd_id']);
            $questions = $this->getSearchFields($webdata['wd_id']);
            if (count($questions)<1) $questions = $this->getAllFields($webdata['wd_id']);
            //print "<br>\nquestions:<br>\n";
            //print_r($questions);
            //print "\n<br>\n";
            if ($small) $returnJS .= "\n<script>\nfunction jsfwd_retrunsearchfields() {\nvar fields=[];\n";
            for ($i=0; $i<count($questions); $i++) {
               if (!in_array($questions[$i]['field_id'],$exceptions)) {
                  if ($small) {
                     $resp = $this->getSearchHTMLSmall($questions[$i]);
                     if($resp['html']!=NULL && count($resp['html'])>0) {
                        for($j=0;$j<count($resp['html']);$j++) $returnHTML .= $resp['html'][$j];
                        for($j=0;$j<count($resp['param']);$j++) $returnJS .= " fields.push('".$resp['param'][$j]."');\n";
                     }
                  } else {
                     $returnHTML .= $this->getSearchHTML($questions[$i]);
                  }
               }
            }
            
            if ($small && !in_array("lastupdateby",$exceptions)) {
               $style_label = "float:left;min-width:100px;margin-top:6px;margin-right:4px;";
               $style_value = "float:left;min-width:100px;";
               $style_input = "width:120px;font-size:10px;font-family:verdana;";
               $paramName = "cmszby_w".$webdata['wd_id'];
               $returnHTML .= "<div style=\"clear:both;\"></div>";
               $returnHTML .= "<div style=\"".$style_label."\">Updated by</div>";
               $returnHTML .= "<div style=\"".$style_value."\">";
               $returnHTML .= "<input type=\"text\" id=\"".$paramName."\" name=\"".$paramName."\" value=\"".getParameter($paramName)."\" style=\"".$style_input."\">";
               $returnHTML .= "</div>";
               $returnJS .= " fields.push('".$paramName."');\n";
             }
            
            if ($small) $returnJS .= "return fields;\n}\n</script>\n";
         }
         return $returnHTML.$returnJS;
      }

      function getSearchFilters($wd_id, $questions=NULL, $checkuser=FALSE, $horizontal=TRUE, $includeallfields=FALSE, $autosearch=FALSE){
         $returnobj = array();
         $returnobj['filtercount'] = 0;
         
         $profile = 0;
         if(!$horizontal && ($checkuser || $includeallfields)) $profile = -1;
         
         $sep = NULL;
         if($horizontal) $sep = "<div style=\"float:left;margin-left:10px;width:4px;height:10px;overflow:hidden;\"></div>";

         $filterHTML = "";
         $createJS = "";
         $initJS = "";
         $webdata = $this->getWebData($wd_id);
         if ($webdata!=NULL && $webdata['wd_id']>0) {
            if($questions==NULL || count($questions)<1) {
               if($includeallfields) $questions=$this->getAllFieldsSystem($webdata['wd_id']);
               else $questions = $this->getFilterFields($webdata['wd_id']);
            }
            //print "<br>\nquestions:<br>\n";
            //print_r($questions);
            //print "\n<br>\n";
            if (count($questions)>0) {
               $returnobj['filtercount'] = count($questions);
               // Set the input fields with values form latest search string
               $initJS .= "\nfunction jsfwd_initsearchuri() {\n";
               //$initJS .= "alert('URL to parse: ' + jsfwd_xtraurl);\n";
               $initJS .= "if(Boolean(jsfwd_xtraurl)) {\n";
               $initJS .= "jsfwd_xtraurl.split('&').forEach(function(part) {\n";
               //$initJS .= "  alert('parsing part: ' + part);\n";
               $initJS .= "  if(Boolean(part)){\n";
               $initJS .= "    var item = part.split('=');\n";
               $initJS .= "    if(Boolean(item[0]) && Boolean(item[1]) && jQuery('#' + item[0]).length>0){\n";
               //$initJS .= "      alert('var: ' + item[0] + ' val: ' + decodeURIComponent(item[1]));\n";
               $initJS .= "      jQuery('#' + item[0]).val(decodeURIComponent(item[1]));\n";
               $initJS .= "    }\n";
               $initJS .= "  }\n";
               $initJS .= "});\n";
               $initJS .= "}\n";
               $initJS .= "if(jQuery('#jsfwdfilterstrdiv".$webdata['wd_id']."').length>0 && Boolean(jsfwd_filterstr)){\n";
               $initJS .= "  jQuery('#jsfwdfilterstrdiv".$webdata['wd_id']."').val(jsfwd_filterstr);\n";
               $initJS .= "}\n";
               $initJS .= "}\n";
               
               // Get parameters from filter form and create the search URI string
               $createJS .= "\nfunction jsfwd_getsearchuri() {\n";
               $createJS .= "var temp='';\n";
               $createJS .= "var uri='';\n";
               $createJS .= "jsfwd_filterstr = jQuery('#jsfwdfilterstrdiv".$webdata['wd_id']."').val();\n";
               
               $filterHTML .= "<div class=\"wdsearch_filters\">";
               $filterHTML .= "<div class=\"wdsearch_filters_title\" style=\"margin-bottom:10px;font-weight:bold;color:#989898;\">Search by jdata fields:</div>";
               
               $onchange = "";
               if($autosearch) $onchange = "jsfwd_executefiltersearch();";
               
               for ($i=0; $i<count($questions); $i++) {
                  $resp = $this->getSearchHTMLSmall($questions[$i],$sep,$profile,$onchange);
                  if($resp['html']!=NULL && count($resp['html'])>0) {
                     for($j=0;$j<count($resp['html']);$j++) $filterHTML .= $resp['html'][$j];
                     for($j=0;$j<count($resp['param']);$j++) {
                        $createJS .= " temp = jQuery('#".$resp['param'][$j]."').val();\n";
                        $createJS .= " if(Boolean(temp)) uri += '&".$resp['param'][$j]."=' + encodeURIComponent(temp);\n";
                     }
                  }
               }
               
               $filterHTML .= "<div style=\"clear:both;\"></div>";
               $filterHTML .= "</div>";
               
               if($checkuser && $webdata['usertype']!=NULL) {
                  $udata = $this->getWebData($webdata['usertype']." properties");
                  $questions = array();
                  if($includeallfields) $questions=$this->getAllFieldsSystem($udata['wd_id']);
                  else $questions = $this->getFilterFields($udata['wd_id']);
                  $filterHTML .= "<div class=\"wdsearch_props\">";
                  $filterHTML .= "<div style=\"margin-bottom:10px;font-weight:bold;color:#989898;\">Search by ".$webdata['usertype']." properties:</div>";
                  for ($i=0; $i<count($questions); $i++) {
                     $resp = $this->getSearchHTMLSmall($questions[$i],$sep,$profile);
                     if($resp['html']!=NULL && count($resp['html'])>0) {
                        for($j=0;$j<count($resp['html']);$j++) $filterHTML .= $resp['html'][$j];
                        for($j=0;$j<count($resp['param']);$j++) {
                           $createJS .= " temp = jQuery('#".$resp['param'][$j]."').val();\n";
                           $createJS .= " if(Boolean(temp)) uri += '&".$resp['param'][$j]."=' + encodeURIComponent(temp);\n";
                        }
                     }
                  }
                  $filterHTML .= "<div style=\"clear:both;\"></div>";
                  $filterHTML .= "</div>";
               }
               
               $createJS .= "jsfwd_xtraurl = uri;\n}\n";
            }
         }
         $returnobj['filterhtml'] = $filterHTML;
         $returnobj['filterinit'] = $initJS;
         $returnobj['filterget'] = $createJS;
         return $returnobj;
      }
      
      function getRelatedTables($wd_id){
         //$query1 = "SELECT * FROM webdata WHERE dbmode<>'DELETED' AND (1=0";
         $query1 = "SELECT * FROM webdata WHERE (1=0";
         $cnt1 = 0;
         $flds = $this->getAllFieldsSystem($wd_id);
         for($i=0;$i<count($flds);$i++){
            if(0==strcmp($flds[$i]['field_type'],"FOREIGNSRY") || 0==strcmp($flds[$i]['field_type'],"FOREIGNSCT") || 0==strcmp($flds[$i]['field_type'],"FOREIGNHYB")) {
               $ops = separateStringBy(trim(convertBack($flds[$i]['question'])),";");
               if (is_numeric($ops[0])) $query1 .= " OR wd_id=".trim($ops[0]);
               $query1 .= " OR LOWER(shortname)='".convertString(strtolower(trim($ops[0])))."'";
               $query1 .= " OR LOWER(name)='".convertString(strtolower(trim($ops[0])))."'";
               $cnt1++;
            }
         }
         $query1 .= ");";

         $dbLink = new MYSQLaccess;         
         $resp = array();
         $resp['query1'] = $query1;
         if($cnt1>0) $resp['foreignsry'] = $dbLink->queryGetResults($query1);
         return $resp;
         
      }
      
      // Get foreign WData rows that relate to an external field
      function getForeignSurveyAnswers($wd_id=NULL,$o_wd_id=NULL,$o_field_id=NULL,$o_wd_row_id=NULL,$lookforenabledonly=FALSE){
         if($wd_id==NULL) return NULL;
         $results = array();
         $wd = $this->getWebData($wd_id);
         $wd_id = $wd['wd_id'];
         $qs = $this->getFieldLabels($wd_id,TRUE,TRUE);
         
         if($wd_id!=NULL && $o_wd_row_id!=NULL) {
            $o_wd = $this->getWebData($o_wd_id);
            $o_wd_id = $o_wd['wd_id'];
            if($o_wd_id!=NULL) {
               $o_qs = $this->getFieldLabels($o_wd_id,TRUE,TRUE);
               $o_field_id = $o_qs[$o_field_id];
               
               if($o_field_id!=NULL) {
                  // Original Way to get rows
                  $externalid = $o_wd_id."_".$o_field_id."_".$o_wd_row_id;
                  $results1 = $this->findByExternalId($wd_id, $externalid);
                  
                  // New link table check this too.
                  // Let it Be
                  $query = "SELECT wdsrc.*, l.linkid ";
                  $query .= "FROM wd_link l JOIN wd_".$wd_id." wdsrc on l.wd_row_id2=wdsrc.wd_row_id ";
                  $query .= "WHERE l.wd_id1=".$o_wd_id;
                  $query .= " AND l.wd_row_id1=".$o_wd_row_id;
                  $query .= " AND l.field_id='".$o_field_id."'";
                  $query .= " AND l.wd_id2=".$wd_id;
                  $query .= " AND (wdsrc.dbmode is NULL OR (wdsrc.dbmode<>'DELETED' AND wdsrc.dbmode<>'DUP'))";
                  if(isset($qs['sequence'])) $query .= " ORDER BY wdsrc.".$qs['sequence'];
                  $query .= ";";
                  $dbLink = new MYSQLaccess;         
                  $results2 = $dbLink->queryGetResults($query);
                  
                  // Go thru both lists and make sure only to add entries once
                  $ref = array();
                  
                  // Go thru link rows first to get linkid included
                  for($i=0;$i<count($results2);$i++) {
                     // return enabled rows?  if so, is enabled a field?  if so, is it set to yes
                     if(!$lookforenabledonly || !isset($qs['enabled']) || 0==strcmp(strtolower($results2[$i][$qs['enabled']]),"yes")) {
                        if(!isset($ref[$results2[$i]['wd_row_id']]) || $ref[$results2[$i]['wd_row_id']]!=1) {
                           $ref[$results2[$i]['wd_row_id']] = 1;
                           $results[] = $results2[$i];
                        }
                     }
                  }                  
                  
                  for($i=0;$i<count($results1);$i++) {
                     // return enabled rows?  if so, is enabled a field?  if so, is it set to yes
                     if(!$lookforenabledonly || !isset($qs['enabled']) || 0==strcmp(strtolower($results1[$i][$qs['enabled']]),"yes")) {
                        if(!isset($ref[$results1[$i]['wd_row_id']]) || $ref[$results1[$i]['wd_row_id']]!=1) {
                           $ref[$results1[$i]['wd_row_id']] = 1;
                           $results[] = $results1[$i];
                        }
                     }
                  }                  
               }
            }
         }
         return $results;
      }

      function removeForeignSurveyLink($wd_id,$wd_row_id,$origemail,$linkid) {
         $retval = FALSE;
         if($wd_row_id!=NULL && $origemail!=NULL && $linkid!=NULL) {
            if(!is_numeric($wd_id)) {
               $tempwd = $this->getWebData($wd_id);
               $wd_id = $tempwd['wd_id'];
            }
            if($wd_id!=NULL) {
               $dbi = new MYSQLaccess;
               $query = "SELECT * FROM wd_".$wd_id;
               $query .= " WHERE wd_row_id=".$wd_row_id;
               $query .= " AND origemail='".$origemail."';";
               $results = $dbi->queryGetResults($query);
               
               if($results!=NULL && count($results)>0) {
                  $query = "DELETE FROM wd_link";
                  $query .= " WHERE wd_id2=".$wd_id;
                  $query .= " AND wd_row_id2=".$wd_row_id;
                  $query .= " AND linkid=".$linkid.";";
                  $dbi->delete($query);
                  $retval = TRUE;
               }
            }
         }
         return $retval;
      }
      
      function addForeignSurveyLink($wd_id1,$field_id,$wd_row_id1,$wd_id2,$wd_row_id2,$printdebug=FALSE) {
         if($printdebug) print "<br>\naddForeignSurveyLink(".$wd_id1.",".$field_id.",".$wd_row_id1.",".$wd_id2.",".$wd_row_id2.")";
         $isallgood = FALSE;
         
         //Check to make sure all parameters are set
         if($wd_id1!=NULL && $field_id!=NULL && $wd_row_id1!=NULL && $wd_id2!=NULL && $wd_row_id2!=NULL) {
            if(!is_numeric($wd_id1)) {
               $tempwd = $this->getWebData($wd_id1);
               $wd_id1 = $tempwd['wd_id'];
            }
            if($wd_id1!=NULL) {
               $qs = $this->getFieldLabels($wd_id1,TRUE,TRUE);
               $field_id = $qs[$field_id];
               if($field_id!=NULL) {
                  if(!is_numeric($wd_id2)) {
                     $tempwd = $this->getWebData($wd_id2);
                     $wd_id2 = $tempwd['wd_id'];
                  }
                  if($wd_id2!=NULL) {
                     $dbi = new MYSQLaccess;
                     
                     $query = "SELECT * FROM wd_link WHERE wd_id1=".$wd_id1." AND wd_row_id1=".$wd_row_id1." AND field_id='".$field_id."' AND wd_id2=".$wd_id2." AND wd_row_id2=".$wd_row_id2.";";
                     $results = $dbi->queryGetResults($query);
                     
                     if($results==NULL || count($results)<1){
                        $subquery = "INSERT INTO wd_link (wd_id1,wd_row_id1,field_id,wd_id2,wd_row_id2) ";
                        $subquery .= "VALUES (".$wd_id1.",".$wd_row_id1.",'".$field_id."',".$wd_id2.",".$wd_row_id2.");";
                        if($printdebug) print "<br>\nAdding a link to wd_link: ".$subquery;
                        $dbi->insert($subquery);
                     }
                     $isallgood = TRUE;
                  }
               }
            }
         }
         
         return $isallgood;
      }
      
      
      
      
      
      function structureParentChild($rows,$field,$currentnode=NULL,$depth=0,$printdebug=FALSE) {
         $newlist = array();
         for($i=0;$i<count($rows);$i++) {
            $chk1 = (isset($rows[$i][$field['field_id']]) && $rows[$i][$field['field_id']]==$currentnode);
            $chk2 = (isset($rows[$i][$field['map']]) && $rows[$i][$field['map']]==$currentnode);
            if($chk1 || $chk2) {
               if($printdebug) print "<br>\nnode: ".$currentnode." depth: ".$depth." adding: ".$rows[$i]['wd_row_id']."<br>\n";
               $indx = count($newlist);
               $newlist[$indx] = $rows[$i];
               $newlist[$indx]['structure_depth'] = $depth;
               
               $templist = $this->structureParentChild($rows,$field,$rows[$i]['wd_row_id'],($depth + 1),$printdebug);
               if($templist!=NULL && count($templist)>0){
                  for($j=0;$j<count($templist);$j++) {
                     $indx = count($newlist);
                     $newlist[$indx]=$templist[$j];
                  }
               }
            }
         }
         
         
         if($printdebug) {
            print "<br>\nWebsiteData::structureParentChild end with rows: ";
            print_r($newlist);
            print "<br>\n<br>\n";
         }
         
         return $newlist;
      }
      
      
      
      //--------------------------------------------------------------
      // START - table input display
      // Unlimited # of rows (existing + 1 more)
      // Labeled rows
      // preset # of rows with a dropdown and a max
      function getInnerSurveyDisplay($wd_id,$q=NULL,$field_id=NULL,$wd_row_id=NULL,$divid=NULL,$tableformat=FALSE,$userid=NULL){
         $html = "";
         $js = "";
         $refreshjs = "";
         $function_js = "";
         
         $wd = $this->getWebData($wd_id);
         if($wd==NULL || $wd['wd_id']==NULL) return NULL;
         if($q==NULL) $q = $this->getField($wd['wd_id'], $field_id);

         // Name of parent divid
         $p_var = "";
         $vararr = separateStringBy($divid,"_");
         for($i=0;$i<(count($vararr)-1);$i++){
            if($i>0) $p_var.="_";
            $p_var.=$vararr[$i];
         }
         
         $maxrows = 0;
         $labels = NULL;
         
         // $ops is used to determine delete button below, as well
         $ops = separateStringBy(trim(convertBack($q['question'])),";");
         if($ops[1]!=NULL && is_numeric($ops[1])) $maxrows=$ops[1];
         //else if($ops[1]!=NULL && strpos($ops[1],",")!==FALSE) $labels=separateStringBy($ops[1],",");
         else if($ops[1]!=NULL) $labels=separateStringBy($ops[1],",");
         $wdata = $this->getWebData($ops[0]);

         $js .= "function sm_".$divid."() {\n";
         $js .= "  var url='';\n";
         $js .= "  var field_url='';\n";
         
         $refreshjs .= "function ref_".$divid."(jsondata){\n";
         //$refreshjs .= "  alert(JSON.stringify(jsondata));\n";
         if($tableformat) $refreshjs .= "  var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=displaywdinputrows';\n";
         else $refreshjs .= "  var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=displayinnersurvey';\n";
         $refreshjs .= "  url += '&callback=ret_".$divid."';\n";
         $refreshjs .= "  url += '&divid=".$divid."';\n";
         $refreshjs .= "  url += '&wd_id=".$wd['wd_id']."';\n";
         $refreshjs .= "  url += '&field_id=".$q['field_id']."';\n";
         $refreshjs .= "  url += '&wd_row_id=' + ".$p_var."_wri;\n";
         $refreshjs .= "  url += '&origemail=' + ".$p_var."_oe;\n";
         //$refreshjs .= "  alert(url);\n";
         $refreshjs .= "  jsfwebdata_CallJSONP(url);\n";
         $refreshjs .= "}\n";
         $refreshjs .= "function ret_".$divid."(jsondata){\n";
         //$refreshjs .= "  alert(JSON.stringify(jsondata));\n";
         $refreshjs .= "  if(Boolean(jsondata.html)){\n";
         $refreshjs .= "    jQuery('#".$divid."').html(jsondata.html);\n";
         $refreshjs .= "  }\n";
         $refreshjs .= "}\n";
         
         // ANSWERS: start - Get all rows currently available for this question + 1 empty
         $ans = $this->getForeignSurveyAnswers($wdata['wd_id'],$wd['wd_id'],$q['field_id'],$wd_row_id);
         $qs = $this->getAllFieldsSystem($wdata['wd_id']);
         
         if($tableformat) $html .= "<table cellpadding=\"4\" cellspacing=\"1\">";

         $totaliterations = count($ans) + 1;
         if ($maxrows>0) {
            $totaliterations = $maxrows;
         } else if($labels!=NULL && count($labels)>0) {
            $totaliterations = count($labels);
            
            //Try to match up answers with correct label
            $newans = array();
            for($i=0;$i<count($labels);$i++){
               $newans[$i] = NULL;
               for($j=0;$j<count($ans);$j++){
                  if (0==strcmp(strtolower($labels[$i]),strtolower($ans[$j]['comments']))){
                     $newans[$i] = $ans[$j];
                     array_splice($ans,$j,1);
                     break;
                  }
               }
            }
            for($i=0;$i<count($labels);$i++){
               if(count($ans)>0 && $newans[$i]==NULL) {
                  $newans[$i] = $ans[0];
                  array_splice($ans,0,1);
               }
            }
            $ans = $newans;
         }
         
         
         for($i=0;$i<$totaliterations;$i++) {
         //for($i=0;$i<=count($ans);$i++) {
            $prefix = $divid."r".$i;
            $acthtml="";
            
            if ($maxrows>0 || count($labels)>0) {
               // Specific number of rows are set, so remove buttons.
               $acthtml="";
            } else if($ans[$i]==NULL || $ans[$i]['wd_row_id']==NULL) {
               $acthtml .= "<div ";
               $acthtml .= "style=\"padding:3px;border:1px solid #333333;border-radius:3px;background-color:#DEDEDE;text-align:center;font-family:verdana;font-size:12px;width:60px;margin:3px;cursor:pointer;font-weight:normal;\" ";
               $acthtml .= "id=\"btn_".$prefix."\" ";
               $acthtml .= "onclick=\"sbt_".$prefix."();\" ";
               $acthtml .= ">\n";
               $acthtml .= "Add</div>";
            } else {
               if(isset($ops[2]) && $ops[2]!=NULL && 0==strcmp(strtolower($ops[2]),"remove")) {
                  $acthtml .= "<div ";
                  $acthtml .= "style=\"padding:3px;border:1px solid #333333;border-radius:3px;background-color:#DEDEDE;text-align:center;font-family:verdana;font-size:12px;width:60px;margin:3px;cursor:pointer;font-weight:normal;\" ";
                  $acthtml .= "id=\"btn_".$prefix."\" ";
                  if($tableformat) $acthtml .= "onclick=\"if(confirm('Are you sure you want to remove reference to this row?')) remove_".$prefix."();\" ";
                  else $acthtml .= "onclick=\"if(confirm('Are you sure you want to remove reference to the above section?')) remove_".$prefix."();\" ";
                  $acthtml .= ">\n";
                  $acthtml .= "Remove</div>";
               } else {
                  $acthtml .= "<div ";
                  $acthtml .= "style=\"padding:3px;border:1px solid #333333;border-radius:3px;background-color:#DEDEDE;text-align:center;font-family:verdana;font-size:12px;width:60px;margin:3px;cursor:pointer;font-weight:normal;\" ";
                  $acthtml .= "id=\"btn_".$prefix."\" ";
                  if($tableformat) $acthtml .= "onclick=\"if(confirm('Are you sure you want to permanently delete this row?')) del_".$prefix."();\" ";
                  else $acthtml .= "onclick=\"if(confirm('Are you sure you want to permanently delete the above section?')) del_".$prefix."();\" ";
                  $acthtml .= ">\n";
                  $acthtml .= "Delete</div>";
               }
            }
            
            $returnobj = array();
            if($tableformat){
               $d_js = "";
               if($i==0){
                  $html .= "<tr class=\"".$p_var."inttableheader\">\n";
                  $html .= "<td></td>\n";
                  for ($j=0;$j<count($qs);$j++){
                     if(0!=strcmp($qs[$j]['field_type'],"INFO") && 0!=strcmp($qs[$j]['field_type'],"SPACER")){                        
                        //$html .= "<td>".substr($qs[$j]['label'],0,50)."</td>\n";
                        $html .= "<td>".$qs[$j]['label']."</td>\n";
                     }
                  }         
                  $html .= "<td style=\"background-color:#FFFFFF;\"></td></tr>\n";
               }
               $trowclass = "even";
               if($i%2 == 1) $trowclass = "odd";
               $html .= "<tr class=\"".$p_var."inttable".$trowclass."\">";
               $html .= "<td class=\"".$p_var."inttablelabel\">".$labels[$i]."</td>\n";
               for ($j=0;$j<count($qs);$j++){
                  if(0!=strcmp($qs[$j]['field_type'],"INFO") && 0!=strcmp($qs[$j]['field_type'],"SPACER")){
                     $ret = $this->getTableDisplayField($ans[$i],$qs[$j],$prefix);
                     $html .= "<td align=\"center\">".$ret['str']."</td>\n";
                     $d_js .= $ret['js'];
                  }
               }         
               $html .= "<td>".$acthtml."</td></tr>";
               $returnobj['js'] = $d_js;
            } else {
               //getJSONWebDataSection($wd_id, $section, $row, $prefix=NULL, $printstuff=FALSE, $explicitcss=0, $userelationships=TRUE, $admin=0, $glossary=NULL, $userid=NULL, $displaysect=-1)
               $returnobj = $this->getJSONWebDataSection($wdata['wd_id'], -1, $ans[$i], $prefix, FALSE, 0, TRUE, 0, NULL, $userid);
               $html .= "<div style=\"margin-top:7px;margin-bottom:7px;\">\n";
               $html .= "<div style=\"font-size:14px;font-weight:bold;font-family:verdana;color:#222222;\">".$labels[$i]."</div>\n";            
               $html .= $returnobj['html'];
               $html .= $acthtml;
               $html .= "</div>\n";
            }
            
            
            // Function to create a new row in this (and recursively a parent) table
            // First, check that the parent survey has a row created
            // Second, create a row in this one if necessary
            // Last call the callback sent to the function to perform next step
            $js .= "field_url = url_".$prefix."();\n";
            $js .= "if(Boolean(field_url)) url += '&jsonarr[]=' + encodeURIComponent(field_url);\n";
            $function_js .= "\nvar ".$prefix."_wri='".$ans[$i]['wd_row_id']."';\n";
            $function_js .= "\nvar ".$prefix."_oe='".$ans[$i]['origemail']."';\n";
            $function_js .= "\nvar ".$prefix."_cb;\n";
            $function_js .= "\nvar ".$prefix."formchanges=false;\n";
            $function_js .= "\nvar ".$prefix."chgflds={};\n";
            $function_js .= "\nfunction chk_".$prefix."(cb){\n";
            $function_js .= "  ".$prefix."_cb = cb;\n";
            $function_js .= "  chk_".$p_var."('chk2_".$prefix."');\n";
            $function_js .= "}\n";
            $function_js .= "\nfunction chk2_".$prefix."(jsondata){\n";
            $function_js .= "  if(!Boolean(".$prefix."_wri)){\n";
            $function_js .= "    var url=defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=chkret_".$prefix."';\n";
            $function_js .= "    url += '&action=submitwd';\n";
            $function_js .= "    url += '&skipuser=1';\n";
            $function_js .= "    url += '&token=222_315_2008_32477';\n";
            $function_js .= "    url += '&wd_id=".$wdata['wd_id']."';\n";
            if($labels[$i]!=NULL) $function_js .= "    url += '&comments=' + encodeURIComponent('".strtolower($labels[$i])."');\n";
            else $function_js .= "    url += '&comments=w".$wdata['wd_id']."';\n";
            //$function_js .= "    url += '&externalid=".$wd['wd_id']."_".$q['field_id']."_' + jsondata.wd_row_id;\n";
            $function_js .= "      url += '&o_wd_id=".$wd['wd_id']."';\n";
            $function_js .= "      url += '&o_field_id=".$q['field_id']."';\n";
            $function_js .= "      url += '&o_wd_row_id=' + jsondata.wd_row_id;\n";
            //$function_js .= "    alert('***chj url: ' + url);\n";
            $function_js .= "    jsfwebdata_CallJSONP(url);\n";            
            $function_js .= "  } else {";
            $function_js .= "    jsondata = [];\n";
            $function_js .= "    jsondata.wd_row_id = ".$prefix."_wri;\n";
            $function_js .= "    jsondata.origemail = ".$prefix."_oe;\n";
            $function_js .= "    window[".$prefix."_cb](jsondata);\n";
            $function_js .= "  }\n";
            $function_js .= "}\n";
            $function_js .= "\nfunction chkret_".$prefix."(jsondata){\n";
            $function_js .= "  if(typeof jsfwd_testing !== 'undefined' && Boolean(jsfwd_testing)) alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
            //$function_js .= "  alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
            $function_js .= "  ".$prefix."_wri=jsondata.wd_row_id;\n";
            $function_js .= "  ".$prefix."_oe=jsondata.origemail;\n";
            $function_js .= "  window[".$prefix."_cb](jsondata);\n";
            $function_js .= "}\n";
            $function_js .= "\nfunction sbt_".$prefix."(){\n";
            $function_js .= "  jQuery('#btn_".$prefix."').hide();\n";
            $function_js .= "  jQuery('#btn_".$prefix."').css('border','0').css('background-color','#FFFFFF');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').html('Loading...');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').show();\n";
            $function_js .= "  var cb='sbt2_".$prefix."';\n";
            $function_js .= "  chk_".$p_var."(cb);\n";
            $function_js .= "}\n";
            $function_js .= "function sbt2_".$prefix."(jsondata){\n";
            $function_js .= "    var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=ref_".$divid."';\n";
            $function_js .= "    url += '&action=submitmultiple';\n";
            $function_js .= "    url += sm_".$divid."();\n";
            $function_js .= "    jsfwebdata_CallJSONP(url);\n";            
            $function_js .= "}\n";
            $function_js .= "\nvar ".$prefix."formchanges=false;\n";
            $function_js .= "\nfunction formchange_".$prefix."(fld){\n";
            $function_js .= "  if(Boolean(fld)) ".$prefix."chgflds[fld]=1;\n";
            $function_js .= "  if(!".$prefix."formchanges){\n";
            $function_js .= "    ".$prefix."formchanges=true;\n";
            $function_js .= "    var cb = 'formchange2_".$prefix."';\n";
            $function_js .= "    chk_".$p_var."(cb);\n";
            $function_js .= "  }\n";            
            $function_js .= "}\n";            
            $function_js .= "\nfunction formchange2_".$prefix."(jsondata){\n";
            $function_js .= "  formchange_".$p_var."('".$q['field_id']."');\n";
            $function_js .= "}\n";            
            $function_js .= "function url_".$prefix."(){\n";
            $function_js .= "    var rqderror=false;\n";
            $function_js .= "    var url='';\n";
            $function_js .= "    if(".$prefix."formchanges) {\n";
            $function_js .= "      url += 'action=submitwd';\n";
            $function_js .= "      url += '&skipuser=1';\n";
            $function_js .= "      url += '&token=222_315_2008_32477';\n";
            $function_js .= "      url += '&wd_id=".$wdata['wd_id']."';\n";
            //$function_js .= "      url += '&externalid=".$wd['wd_id']."_".$q['field_id']."_' + ".$p_var."_wri;\n";
            $function_js .= "      url += '&o_wd_id=".$wd['wd_id']."';\n";
            $function_js .= "      url += '&o_field_id=".$q['field_id']."';\n";
            $function_js .= "      url += '&o_wd_row_id=' + ".$p_var."_wri;\n";
            if($labels[$i]!=NULL) $function_js .= "    url += '&comments=' + encodeURIComponent('".strtolower($labels[$i])."');\n";
            $function_js .= "      if(Boolean(".$prefix."_wri)) url += '&wd_row_id=' + ".$prefix."_wri;\n";
            $function_js .= "      if(Boolean(".$prefix."_oe)) url += '&origemail=' + ".$prefix."_oe;\n";
            $function_js .= $returnobj['js'];
            $function_js .= "    }\n";            
            $function_js .= "    return url;\n";            
            $function_js .= "}\n";            
            $function_js .= "function del_".$prefix."(){\n";
            $function_js .= "  jQuery('#btn_".$prefix."').hide();\n";
            $function_js .= "  jQuery('#btn_".$prefix."').css('border','0').css('background-color','#FFFFFF');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').html('Loading...');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').show();\n";
            $function_js .= "  var delurl = 'action=deletesinglewdrow';\n";
            $function_js .= "  delurl += '&wd_id=".$wdata['wd_id']."';\n";
            $function_js .= "  delurl += '&wd_row_id=".$ans[$i]['wd_row_id']."';\n";
            $function_js .= "  delurl += '&origemail=".$ans[$i]['origemail']."';\n";
            $function_js .= "  var url = sm_".$divid."();\n";
            $function_js .= "  url += '&jsonarr[]=' + encodeURIComponent(delurl);\n";
            $function_js .= "  var xurl = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=ref_".$divid."';\n";
            $function_js .= "  xurl += '&action=submitmultiple';\n";
            $function_js .= "  xurl += url;\n";
            $function_js .= "  jsfwebdata_CallJSONP(xurl);\n";
            $function_js .= "}\n";
            $function_js .= "function remove_".$prefix."(){\n";
            $function_js .= "  jQuery('#btn_".$prefix."').hide();\n";
            $function_js .= "  jQuery('#btn_".$prefix."').css('border','0').css('background-color','#FFFFFF');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').html('Loading...');\n";
            $function_js .= "  jQuery('#btn_".$prefix."').show();\n";
            $function_js .= "  var delurl = 'action=removeforeignlink';\n";
            $function_js .= "  delurl += '&wd_id=".$wdata['wd_id']."';\n";
            $function_js .= "  delurl += '&linkid=".$ans[$i]['linkid']."';\n";
            $function_js .= "  delurl += '&wd_row_id=".$ans[$i]['wd_row_id']."';\n";
            $function_js .= "  delurl += '&origemail=".$ans[$i]['origemail']."';\n";
            $function_js .= "  delurl += '&userid=' + encodeURIComponent(jsfwd_userid);\n";
            $function_js .= "  delurl += '&token=' + encodeURIComponent(jsfwd_token);\n";            
            $function_js .= "  var url = sm_".$divid."();\n";
            $function_js .= "  url += '&jsonarr[]=' + encodeURIComponent(delurl);\n";
            $function_js .= "  var xurl = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=ref_".$divid."';\n";
            $function_js .= "  xurl += '&action=submitmultiple';\n";
            $function_js .= "  xurl += url;\n";
            $function_js .= "  jsfwebdata_CallJSONP(xurl);\n";
            $function_js .= "}\n";
            
         }
         // ANSWERS: end
         
         if($tableformat) $html .= "</table>";         
         
         //$js .= "  alert('***chj*** URL IS: ' + url);\n";
         $js .= "  return url;\n";
         $js .= "}\n";
         
         $html .= "\n<script>\n";
         $html .= $function_js;
         $html .= $js;
         $html .= "\n</script>\n";

         $res = array();
         $res['str'] = $html;
         $res['html'] = $html;
         $res['refreshjs'] = $refreshjs;         
         $res['js'] = "sm_".$divid."()";         
         
         return $res;
      }
      
      function getInnerSurveyDisplayHybrid($wd_id,$q=NULL,$field_id=NULL,$wd_row_id=NULL,$divid=NULL,$userid=NULL){
         $wd = $this->getWebData($wd_id);
         if($wd==NULL || $wd['wd_id']==NULL) return NULL;
         if($q==NULL) $q = $this->getField($wd['wd_id'], $field_id);

         // Name of parent divid
         $p_var = "";
         $vararr = separateStringBy($divid,"_");
         for($i=0;$i<(count($vararr)-1);$i++){
            if($i>0) $p_var.="_";
            $p_var.=$vararr[$i];
         }
         
         $ops = separateStringBy(trim(convertBack($q['question'])),",");
         $wdata = $this->getWebData($ops[0]);

         // ANSWERS: start - Get all rows currently available for this question + 1 empty
         $ans = $this->getForeignSurveyAnswers($wdata['wd_id'],$wd['wd_id'],$q['field_id'],$wd_row_id);
         $qs = $this->getAllFieldsSystem($wdata['wd_id']);
         
         if(count($ans)>0) {
            // Print output table of existing related rows
            // Table headers
            $html = "<table cellpadding=\"3\" cellspacing=\"1\" style=\"font-size:12px;\">";
            $html .= "<tr style=\"color:#111111;background-color:#EEEEEE;\">\n";
            for ($j=0;$j<count($qs);$j++){
               if(0!=strcmp($qs[$j]['field_type'],"INFO") && 0!=strcmp($qs[$j]['field_type'],"SPACER")){                        
                  $html .= "<td>".substr($qs[$j]['label'],0,50)."</td>\n";
               }
            }         
            $html .= "<td style=\"background-color:#FFFFFF;\"></td></tr>\n";
            
            // All the rows
            for($i=0;$i<count($ans);$i++) {
               $prefix = $divid."r".$i;
               
               $html .= "<tr>";
               for ($j=0;$j<count($qs);$j++){
                  if(0!=strcmp($qs[$j]['field_type'],"INFO") && 0!=strcmp($qs[$j]['field_type'],"SPACER")){
                     //Print out a friendly version of the data
                     //$html .= "<td>".$ans[$i][$qs[$j]['field_id']]."</td>\n";
                     $obj = $this->getCSVRow($wdata['wd_id'],$ans[$i]['wd_row_id'],$qs[$j],$ans[$i][$qs[$j]['field_id']]);
                     $html .= "<td>".$obj['simple']."</td>\n";
                  }
               }         
               $html .= "<td>";
               $html .= "<span ";
               $html .= "style=\"font-family:verdana;font-size:12px;cursor:pointer;font-weight:bold;color:blue;\" ";
               $html .= "id=\"btn_".$prefix."\" ";
               $html .= "onclick=\"if(confirm('Are you sure you want to permanently delete this row?')) del_".$divid."('".$ans[$i]['wd_row_id']."','".$ans[$i]['origemail']."');\" ";
               $html .= ">\n";
               $html .= "Delete</span>";
               $html .= "</td></tr>";
            }
            $html .= "</table>";
         }
         
         
         //Call for section jsonhtml
         //getJSONWebDataSection($wd_id, $section, $row, $prefix=NULL, $printstuff=FALSE, $explicitcss=0, $userelationships=TRUE, $admin=0, $glossary=NULL, $userid=NULL, $displaysect=-1)
         $returnobj = $this->getJSONWebDataSection($wdata['wd_id'], -1,NULL,$divid,FALSE,0,TRUE,0,NULL,$userid);
         $html .= "<div id=\"hybbg_".$divid."\" style=\"display:none;\"></div>";
         
         $html .= "<div id=\"hyb_".$divid."\" style=\"display:none;\">";
         $html .= $returnobj['html'];
         $html .= "<div style=\"position:relative;margin-top:4px;margin-bottom:5px;\">";
         $html .= "<div onclick=\"sbt_".$divid."();\" style=\"float:left;margin-right:12px;font-size:10px;color:#000000;border:1px solid #000000;background-color:#EDEDED;border-radius:4px;padding:5px;width:80px;cursor:pointer\">Submit</div>";
         $html .= "<div onclick=\"jQuery('#hybbg_".$divid."').hide();jQuery('#hyb_".$divid."').hide();jQuery('#hybtn_".$divid."').show();\" style=\"float:left;margin-right:12px;font-size:10px;color:#000000;border:1px solid #000000;background-color:#EDEDED;border-radius:4px;padding:5px;width:80px;cursor:pointer\">Cancel</div>";
         $html .= "<div style=\"clear:both;\"></div>";
         $html .= "</div>";
         $html .= "</div>";
         
         $link = "Add a new entry";
         if($ops[1]!=NULL) $link = $ops[1];
         $html .= "<div id=\"hybtn_".$divid."\" style=\"position:relative;margin-top:8px;margin-bottom:8px;font-size:10px;color:blue;cursor:pointer;\" onclick=\"showsbt_".$divid."();\">";
         $html .= $link;
         $html .= "</div>";
         

         $html .= "\n<script>\n";
         
         $html .= "\nvar ".$divid."_wri;\n";
         $html .= "\nvar ".$divid."_oe;\n";
         $html .= "\nvar ".$divid."_cb;\n";
         $html .= "\nvar ".$divid."formchanges=false;\n";
         $html .= "\nvar ".$divid."chgflds={};\n";
         
         $html .= "function showsbt_".$divid."(){\n";
         $html .= $returnobj['relationshipjs1'];
         $html .= $returnobj['relationshipjs2'];
         $html .= "jQuery('#hyb_".$divid."').css('position','relative');\n";
         $html .= "jQuery('#hyb_".$divid."').css('margin','10px 5px 10px 5px');\n";
         $html .= "jQuery('#hyb_".$divid."').css('padding','10px');\n";
         $html .= "jQuery('#hyb_".$divid."').css('border-radius','4px');\n";
         $html .= "jQuery('#hyb_".$divid."').css('background-color','#F0F0F0');\n";
         $html .= "jQuery('#hyb_".$divid."').show();\n";
         $html .= "jQuery('#hybtn_".$divid."').hide();\n";
         $html .= "}\n";
         
         // Functions to redraw the table & input
         $html .= "function ref_".$divid."(jsondata){\n";
         //$html .= "  alert(JSON.stringify(jsondata));\n";
         $html .= "  var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp';\n";
         $html .= "  url += '&action=refreshinnerhybrid';\n";
         $html .= "  url += '&callback=ret_".$divid."';\n";
         $html .= "  url += '&divid=".$divid."';\n";
         $html .= "  url += '&wd_id=".$wd['wd_id']."';\n";
         $html .= "  url += '&field_id=".$q['field_id']."';\n";
         $html .= "  url += '&wd_row_id=' + ".$p_var."_wri;\n";
         $html .= "  url += '&origemail=' + ".$p_var."_oe;\n";
         //$html .= "  alert(url);\n";
         $html .= "  jsfwebdata_CallJSONP(url);\n";
         $html .= "}\n";
         $html .= "function ret_".$divid."(jsondata){\n";
         //$html .= "  alert(JSON.stringify(jsondata));\n";
         $html .= "  if(Boolean(jsondata.html)){\n";
         $html .= "    jQuery('#".$divid."').html(jsondata.html);\n";
         $html .= "  }\n";
         $html .= "}\n";
         
         // chk is a method that somebody calls to ensure we have a row
         // cb variable is eventually called once a row is created or verified
         $html .= "\nfunction chk_".$divid."(cb){\n";
         $html .= "  ".$divid."_cb = cb;\n";
         $html .= "  chk_".$p_var."('chk2_".$divid."');\n";
         $html .= "}\n";
         $html .= "\nfunction chk2_".$divid."(jsondata){\n";
         $html .= "  if(!Boolean(".$divid."_wri)){\n";
         $html .= "    var url=defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=chkret_".$divid."';\n";
         $html .= "    url += '&action=submitwd';\n";
         $html .= "    url += '&skipuser=1';\n";
         $html .= "    url += '&token=222_315_2008_32477';\n";
         $html .= "    url += '&wd_id=".$wdata['wd_id']."';\n";
         //$html .= "    url += '&externalid=".$wd['wd_id']."_".$q['field_id']."_' + jsondata.wd_row_id;\n";
         $html .= "    url += '&o_wd_id=".$wd['wd_id']."';\n";
         $html .= "    url += '&o_field_id=".$q['field_id']."';\n";
         $html .= "    url += '&o_wd_row_id=' + jsondata.wd_row_id;\n";
         //$html .= "    alert('***chj url: ' + url);\n";
         $html .= "    jsfwebdata_CallJSONP(url);\n";            
         $html .= "  } else {";
         $html .= "    jsondata = [];\n";
         $html .= "    jsondata.wd_row_id = ".$divid."_wri;\n";
         $html .= "    jsondata.origemail = ".$divid."_oe;\n";
         $html .= "    window[".$divid."_cb](jsondata);\n";
         $html .= "  }\n";
         $html .= "}\n";
         $html .= "\nfunction chkret_".$divid."(jsondata){\n";
         $html .= "  if(typeof jsfwd_testing !== 'undefined' && Boolean(jsfwd_testing)) alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
         //$html .= "  alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
         $html .= "  ".$divid."_wri=jsondata.wd_row_id;\n";
         $html .= "  ".$divid."_oe=jsondata.origemail;\n";
         $html .= "  window[".$divid."_cb](jsondata);\n";
         $html .= "}\n";
         
         
         
         // Entry created by "add" button
         // first make sure there's a row in master to hold us
         // second, submit us
         $html .= "\nfunction sbt_".$divid."(){\n";
         $html .= "  if(".$divid."formchanges){\n";
         $html .= "  jQuery('#btn_".$divid."').hide();\n";
         $html .= "  jQuery('#btn_".$divid."').css('border','0').css('background-color','#FFFFFF');\n";
         $html .= "  jQuery('#btn_".$divid."').html('Loading...');\n";
         $html .= "  jQuery('#btn_".$divid."').show();\n";
         $html .= "  var cb='sbt2_".$divid."';\n";
         $html .= "  chk_".$p_var."(cb);\n";
         $html .= "  }\n";
         $html .= "}\n";
         $html .= "function sbt2_".$divid."(jsondata){\n";
         $html .= "    var rqderror=false;\n";
         $html .= "    var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=ref_".$divid."';\n";
         $html .= "    url += '&action=submitwd';\n";
         $html .= "    url += '&skipuser=1';\n";
         $html .= "    url += '&wd_id=".$wdata['wd_id']."';\n";
         $html .= "    url += '&token=222_315_2008_32477';\n";
         //$html .= "    url += '&externalid=".$wd['wd_id']."_".$q['field_id']."_' + ".$p_var."_wri;\n";
         $html .= "    url += '&o_wd_id=".$wd['wd_id']."';\n";
         $html .= "    url += '&o_field_id=".$q['field_id']."';\n";
         $html .= "    url += '&o_wd_row_id=' + ".$p_var."_wri;\n";
         $html .= "    if(Boolean(".$divid."_wri)) url += '&wd_row_id=' + ".$divid."_wri;\n";
         $html .= "    if(Boolean(".$divid."_oe)) url += '&origemail=' + ".$divid."_oe;\n";
         $html .= $returnobj['js'];
         $html .= "    if(!rqderror) jsfwebdata_CallJSONP(url);\n";            
         $html .= "}\n";
         $html .= "\nfunction formchange_".$divid."(fld){\n";
         $html .= "  if(Boolean(fld)) ".$divid."chgflds[fld]=1;\n";
         $html .= "  if(!".$divid."formchanges){\n";
         $html .= "    ".$divid."formchanges=true;\n";
         $html .= "    var cb = 'formchange2_".$divid."';\n";
         $html .= "    chk_".$p_var."(cb);\n";
         $html .= "  }\n";            
         $html .= "}\n";            
         $html .= "\nfunction formchange2_".$divid."(jsondata){\n";
         $html .= "  formchange_".$p_var."('".$q['field_id']."');\n";
         $html .= "}\n";            
         $html .= "function del_".$divid."(wri,oe){\n";
         $html .= "  jQuery('#btn_".$divid."').hide();\n";
         $html .= "  jQuery('#btn_".$divid."').css('border','0').css('background-color','#FFFFFF');\n";
         $html .= "  jQuery('#btn_".$divid."').html('Loading...');\n";
         $html .= "  jQuery('#btn_".$divid."').show();\n";
         $html .= "  var xurl = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp';\n";
         $html .= "  xurl += '&callback=ref_".$divid."';\n";
         $html .= "  xurl += '&action=deletesinglewdrow';\n";
         $html .= "  xurl += '&wd_id=".$wdata['wd_id']."';\n";
         $html .= "  xurl += '&wd_row_id=' + wri;\n";
         $html .= "  xurl += '&origemail=' + oe;\n";
         $html .= "  jsfwebdata_CallJSONP(xurl);\n";
         $html .= "}\n";
         $html .= "</script>\n";

         $res = array();
         $res['html'] = $html;
         $res['js'] = "sbt_".$divid."();\n";         
         
         return $res;
      }
      
      function getTableDisplayField($row,$q,$prefix,$showlabel=FALSE,$debug=FALSE){
         $dbi = new MYSQLAccess();
         $js = "";
         $helpstr = "";
         
         $name = $prefix."_a".$q['field_id'];
         $columnDescr = $row[$q['field_id']];
                  
         //$columnDescr = $q['field_type'];
         if (0==strcmp($q['field_type'],"TEXT") || 0==strcmp($q['field_type'],"DATETIME") || 0==strcmp($q['field_type'],"DATE") || 0==strcmp($q['field_type'],"INT") || 0==strcmp($q['field_type'],"DEC") || 0==strcmp($q['field_type'],"MONEY")) {
            //$columnDescr = "<input style=\"font-size:10px;font-family:arial;color:#222222;width:80px;\" type=\"text\" name=\"".$name."\" id=\"".$name."\" value=\"".$columnDescr."\" onKeyPress=\"formchange_".$prefix."();\">\n";
            $columnDescr = "<input ";
            $columnDescr .= "type=\"text\" ";
            $columnDescr .= "name=\"".$name."\" ";
            $columnDescr .= "id=\"".$name."\" ";
            $columnDescr .= "onKeyUp=\"if(typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\" ";

            if($showlabel) {
               $columnDescr .= "onblur=\"if(this.value == ''){ this.value = '".$q['label']."'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
               $columnDescr .= "onfocus=\"if(this.value == '".$q['label']."'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
               if($row[$q['field_id']]==NULL) {
                  $columnDescr .= "style=\"font-size:10px;font-style:italic;font-family:arial;color:#BBBBBB;width:80px;\" ";
                  $columnDescr .= "value=\"".$q['label']."\" ";
               } else {
                  $columnDescr .= "style=\"font-size:10px;font-family:arial;color:#222222;width:80px;\" ";
                  $columnDescr .= "value=\"".$row[$q['field_id']]."\" ";
               }
            } else {
               $columnDescr .= "style=\"font-size:10px;font-family:arial;color:#222222;width:80px;\" ";
               $columnDescr .= "value=\"".$row[$q['field_id']]."\" ";
            }
            
            $columnDescr .= ">\n";
            $js = "if(Boolean(jQuery('#".$name."').val()) && jQuery('#".$name."').val()!='".$q['label']."') url += '&".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(jQuery('#".$name."').val()));\n";
         } else if (0==strcmp($q['field_type'],"SNGLCHKBX")) {
            $chkd = "";
            if (0==strcmp(strtolower($columnDescr),"yes")) $chkd = " CHECKED";
            $columnDescr = "<input type=\"checkbox\" name=\"".$name."\" id=\"".$name."\" value=\"YES\"".$chkd." onClick=\"if(typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\">\n";
            $js = "var x='NO';\n";
            if($row==NULL || $row['wd_row_id']==NULL) $js = "var x='';\n";
            $js .= "if(jQuery('#".$name."').is(':checked')) x='YES';\n";
            $js .= "if(Boolean(x)) url += '&".$q['field_id']."=' + x;\n";
         } else if (0==strcmp($q['field_type'],"TEXTAREA")) {
            //$columnDescr ="<textarea style=\"width:80px;height:50px;font-size:10px;font-family:arial;color:#222222;\" name=\"".$name."\" id=\"".$name."\" onKeyPress=\"formchange_".$prefix."();\">".$columnDescr."</textarea>\n";
            $columnDescr ="<textarea style=\"width:80px;height:50px;font-size:10px;font-family:arial;color:#222222;\" name=\"".$name."\" id=\"".$name."\" onKeyUp=\"if(typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\">".$columnDescr."</textarea>\n";
            $js = "if(Boolean(jQuery('#".$name."').val())) url += '&".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(jQuery('#".$name."').val()));\n";
         } else if (0==strcmp($q['field_type'],"FOREIGNTBL")) {
            $names = array();
            $values = array();
            $selarr = separateStringBy(convertBack($columnDescr),",",NULL,TRUE);
            $sel = array();
            for($i=0;$i<count($selarr);$i++) $sel[$selarr[$i]] = 1;
            
            if (0==strcmp($q['field_type'],"FOREIGNTBL")) {
               $optionList = separateStringBy(convertBack($q['question']),",");
               if ($optionList[0] != NULL && $optionList[1] != NULL) {
                  $fldname = strtolower(trim($optionList[1]));
                  $fldval = strtolower(trim($optionList[2]));
                  if($fldval==NULL) $fldval = $fldname;
                  $query = "SELECT ".$fldname;
                  if(0!=strcmp($fldname,$fldval)) $query .= ", ".$fldval;
                  $query .= " from ".trim($optionList[0]);
                  $results = $dbi->queryGetResults($query);
                  for ($i=0; $i<count($results); $i++) {
                     $names[] = $results[$i][$fldname];
                     $values[] = $results[$i][$fldval];
                  }
               }
            }
            for ($a=0; $a<count($names); $a++) {
               if(trim($values[$a])==NULL) $values[$a] = $names[$a];
               $opts[trim($names[$a])] = trim($values[$a]);
            }
            
            
            //$name = $prefix."dd";
            $columnDescr = getCheckboxListDiv($name, $opts, $sel, "style=\"font-size:12px;font-family:arial;color:#222222;\" onchange=\"if(typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\"");
            //$columnDescr = getOptionList($name, $opts, $columnDescr, TRUE, "id=\"".$name."\" style=\"font-size:12px;font-family:arial;color:#222222;\" onchange=\"formchange_".$prefix."();\"");
            //$columnDescr = $q['question'];
            $js .= "     var tmpurl = '';\n";
            $js .= "     var inputs = document.getElementsByName('".$name."[]');\n";
            $js .= "     for (var i=0;i<inputs.length;i++){\n";
            $js .= "       var e = inputs[i];\n";
            $js .= "       if (e.checked) tmpurl = tmpurl + '&w".$q['field_id']."[]=' + encodeURIComponent(e.value);\n";
            $js .= "     }\n";
            $js .= "     if(tmpurl.length>0) url = url + tmpurl;\n";
            
            
            
         } else if (0==strcmp($q['field_type'],"USERAUTO")) {
            $wdname = convertBack($q['question']);
            $wdata = $this->getWebData($wdname);
            $columnDescr = "";
            $columnDescr .= "<input type=\"hidden\" name=\"".$name."\" id=\"".$name."\" value=\"\">";
            $columnDescr .= "<div id=\"wduser_".$name."\" ";
            $columnDescr .= "style=\"position:relative;width:140px;height:22px;font-size:12px;\" ";
            $columnDescr .= "></div>";
            $columnDescr .= "\n<script>\n";
            $columnDescr .= "function jsfwduser_".$name."rem(){\n";
            $columnDescr .= "   if(confirm('Do you want to remove this user?')) {\n";
            $columnDescr .= "      jsfwduser_".$name."ref('%%%EMPTY%%%');\n";
            $columnDescr .= "   }\n";
            $columnDescr .= "}\n";
            $columnDescr .= "function jsfwduser_".$name."ref(userid,nochange) {\n";
            $columnDescr .= "   //alert('userid: ' + userid);\n";
            $columnDescr .= "   jQuery('#".$name."').val(userid);\n";
            $columnDescr .= "   if(Boolean(userid) && userid!='%%%EMPTY%%%') {\n";
            $columnDescr .= "      //get the name of the user\n";
            $columnDescr .= "      var url = jsfsearch_domain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp';\n";
            $columnDescr .= "      url += '&action=searchusers';\n";
            $columnDescr .= "      url += '&callback=jsfwduser_".$name."ref_return';\n";
            $columnDescr .= "      url += '&s_filter=' + userid;\n";
            $columnDescr .= "      //alert('url: ' + url);\n";
            $columnDescr .= "      jsfsearch_CallJSONP(url);\n";
            $columnDescr .= "   } else {\n";
            $columnDescr .= "      //draw an input search box\n";
            $columnDescr .= "      jsfsearch_testinput('wduser_".$name."','".$wdata['wd_id']."','".$q['label']."','jsfwduser_".$name."enter',1,120,12);\n";
            $columnDescr .= "   }\n";
            $columnDescr .= "   if(!Boolean(nochange) && typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\n";
            $columnDescr .= "}\n";            
            $columnDescr .= "function jsfwduser_".$name."ref_return(jsondata) {\n";
            $columnDescr .= "   //alert('results: ' + JSON.stringify(jsondata));\n";
            $columnDescr .= "   if(Boolean(jsondata) && Boolean(jsondata.users) && jsondata.users.length>0) {\n";
            $columnDescr .= "      var str = '';\n";
            $columnDescr .= "      str += jsondata.users[0].fname + ' ' + jsondata.users[0].lname + ' ' + jsondata.users[0].company + ' (' + jsondata.users[0].userid + ')';\n"; 
            $columnDescr .= "      str += '<span style=\"margin-left:10px;cursor:pointer;color:red;font-size:10px;\" onclick=\"jsfwduser_".$name."rem();\">';\n";
            $columnDescr .= "      str += 'Remove</span>';\n";
            $columnDescr .= "      jQuery('#wduser_".$name."').html(str);\n";
            $columnDescr .= "   } else {\n";
            $columnDescr .= "      jsfwduser_".$name."ref('%%%EMPTY%%%');\n";
            $columnDescr .= "   }\n";            
            $columnDescr .= "}\n";            
            $columnDescr .= "function jsfwduser_".$name."enter(divid){\n";
            $columnDescr .= "   //alert('divid: ' + divid);\n";
            $columnDescr .= "   var val=jQuery('#' + divid + '_searchtext').val();\n";
            $columnDescr .= "   if(Boolean(val) && val!='".$q['label']."') {\n";
            $columnDescr .= "      var url = jsfsearch_domain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp';\n";
            $columnDescr .= "      url += '&action=resultswdindex';\n";
            $columnDescr .= "      url += '&callback=jsfwduser_".$name."enter_return';\n";
            $columnDescr .= "      url += '&wd_id=".$wdata['wd_id']."';\n";
            $columnDescr .= "      url += '&phrase=' + encodeURIComponent(val);\n";
            $columnDescr .= "      url += '&divid=' + encodeURIComponent(divid);\n";
            $columnDescr .= "      //alert('url before call: ' + url);\n";
            $columnDescr .= "      jsfsearch_CallJSONP(url);\n";
            $columnDescr .= "   } else {\n";
            $columnDescr .= "      jsfwduser_".$name."enter_return();\n";
            $columnDescr .= "   }\n";
            $columnDescr .= "}\n";
            $columnDescr .= "function jsfwduser_".$name."enter_return(jsondata){\n";
            $columnDescr .= "   //alert('results: ' + JSON.stringify(jsondata));\n";
            $columnDescr .= "   var userid = '%%%EMPTY%%%';\n";
            $columnDescr .= "   if(Boolean(jsondata) && Boolean(jsondata.results) && jsondata.results.length>0 && Boolean(jsondata.results[0].results) && jsondata.results[0].results.length>0) userid = jsondata.results[0].results[0].indexval;\n";
            $columnDescr .= "   //alert('userid: ' + userid);\n";
            $columnDescr .= "   jsfwduser_".$name."ref(userid);\n";
            $columnDescr .= "}\n";
            $columnDescr .= "jsfwduser_".$name."ref('".$row[$q['field_id']]."',true);\n";
            $columnDescr .= "</script>\n";
            
            $js = "if(Boolean(jQuery('#".$name."').val())) url += '&".$q['field_id']."=' + encodeURIComponent(jQuery('#".$name."').val());\n";
            
         } else if (0==strcmp($q['field_type'],"DROPDOWN") || 0==strcmp($q['field_type'],"RADIO") || 0==strcmp($q['field_type'],"FOREIGN") || 0==strcmp($q['field_type'],"FOREIGNTDD") || 0==strcmp($q['field_type'],"USERS") || 0==strcmp($q['field_type'],"USERSRCH") || 0==strcmp($q['field_type'],"USERLIST")) {
            $opts = array();
            //$columnDescr = "# of vals: ".count($names);
            $names = array();
            $values = array();
            
            if (0==strcmp($q['field_type'],"FOREIGN")) {
               $optionList = separateStringBy(convertBack($q['question']),",");
               if ($optionList[0] != NULL && $optionList[1] != NULL) {
                  $fldname = strtolower(trim($optionList[1]));
                  $wdname = strtolower(trim($optionList[0]));
                  $wdata = $this->getWebDataByName($wdname);
                  $qs = $this->getFieldLabels($wdata['wd_id'],TRUE,TRUE);
                  
                  $fldval = strtolower(trim($optionList[2]));
                  if($fldval!=NULL && isset($qs[$fldval])) $fldval = $qs[$fldval];
                  else $fldval = "wd_row_id";
                  $query = "";
                  $query .= "SELECT ".$qs[$fldname].", ".$fldval." from wd_".$wdata['wd_id'];
                  $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
                  if ($qs['sequence'] != NULL) $query .=" ORDER BY ".$qs['sequence'];
                  $dbi = new MYSQLAccess();
                  $results = $dbi->queryGetResults($query);
                  for ($i=0; $i<count($results); $i++) {
                     $names[] = $results[$i][$qs[$fldname]];
                     $values[] = $results[$i][$fldval];
                  }
               }
            } else if (0==strcmp($q['field_type'],"FOREIGNTDD")) {
               $optionList = separateStringBy(convertBack($q['question']),",");
               if ($optionList[0] != NULL && $optionList[1] != NULL) {
                  $fldname = strtolower(trim($optionList[1]));
                  $fldval = strtolower(trim($optionList[2]));
                  if($fldval==NULL) $fldval = $fldname;
                  $query = "SELECT ".$fldname;
                  if(0!=strcmp($fldname,$fldval)) $query .= ", ".$fldval;
                  $query .= " from ".trim($optionList[0]);
                  $results = $dbi->queryGetResults($query);
                  for ($i=0; $i<count($results); $i++) {
                     $names[] = $results[$i][$fldname];
                     $values[] = $results[$i][$fldval];
                  }
               }
            } else if (0==strcmp($q['field_type'],"USERS") || 0==strcmp($q['field_type'],"USERSRCH") || 0==strcmp($q['field_type'],"USERLIST")) {
               $ua = new UserAcct();
               $usersA = $ua->getUsersForSegment(strtolower(trim($q['question'])));
               $users = $usersA['users'];
               $cntr = count($users);
               if($cntr>40) $cntr = 40;
               for ($i=0; $i<$cntr; $i++) {
                  $user = $ua->getUser($users[$i]['userid']);
                  $names[] = $user['fname']." ".$user['lname'];
                  $values[] = $user['userid'];
               }
            } else {
               $optionList = separateStringBy(convertBack($q['question']),";");
               $names = separateStringBy($optionList[0],",");
               $values = array();
               if(isset($optionList[1]) && $optionList[1]!=NULL) $values = separateStringBy($optionList[1],",");
            }
            
            
            for ($a=0; $a<count($names); $a++) {
               if(!isset($values[$a]) || trim($values[$a])==NULL) $values[$a] = $names[$a];
               $opts[trim($names[$a])] = trim($values[$a]);
            }
            
            $displabel = "";
            if($showlabel) $displabel = $q['label'];
            //$name = $prefix."dd";
            $columnDescr = getOptionList($name, $opts, $columnDescr, TRUE, "id=\"".$name."\" style=\"font-size:12px;font-family:arial;color:#222222;\" onchange=\"if(typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\"",FALSE,32,$displabel);
            //$columnDescr = $q['question'];
            $js = "if(Boolean(jQuery('#".$name."').val())) url += '&".$q['field_id']."=' + encodeURIComponent(jQuery('#".$name."').val());\n";
            
            
            
         } else if (0==strcmp($q['field_type'],"MBL_UPL") || 0==strcmp($q['field_type'],"IMAGE")) {
            $columnDescr = "";
            $columnDescr .= "<input type=\"hidden\" name=\"".$name."\" id=\"".$name."\" value=\"".$row[$q['field_id']]."\">";
            $columnDescr .= "<div id=\"wdimg_".$name."\" ";
            $columnDescr .= "style=\"position:relative;width:80px;height:50px;overflow:hidden;\" ";
            $columnDescr .= "></div>";
            $columnDescr .= "\n<script>\n";
            $columnDescr .= "function ".$name."imgdel(){\n";
            $columnDescr .= "   if(confirm('Do you want to permanently delete this image?')) {\n";
            $columnDescr .= "      jsfwdimg_".$name."wa('%%%EMPTY%%%');\n";
            $columnDescr .= "   }\n";
            $columnDescr .= "}\n";
            $columnDescr .= "function jsfwdimg_".$name."wa(fn,nochange) {\n";
            $columnDescr .= "   jQuery('#".$name."').val(fn);\n";
            $columnDescr .= "   var oc='';\n";
            $columnDescr .= "   if(Boolean(fn) && fn!='%%%EMPTY%%%') {\n";
            $columnDescr .= "   oc += '<img src=\\\"' + fn + '\\\" ';\n";
            $columnDescr .= "   oc += 'style=\"z-index:1;height:80px;width:auto;\\\" ';\n";
            $columnDescr .= "   oc += 'onclick=\\\"window.open(\\'' + fn + '\\');\\\" ';\n";
            $columnDescr .= "   oc += '>';\n";
            $columnDescr .= "   oc += '<div onclick=\\\"".$name."imgdel();\\\" ';\n";
            $columnDescr .= "   oc += 'style=\\\"position:absolute;left:3px;top:3px;width:20px;height:20px;z-index:2;overflow:hidden;border-radius:10px;background-color:#FFFFFF;cursor:pointer;\\\">';\n";
            $columnDescr .= "   oc += '<div style=\\\"width:20px;height:14px;margin-top:2px;font-size:12px;color:red;text-align:center;\\\">x</div>';\n";
            $columnDescr .= "   oc += '</div>';\n";
            $columnDescr .= "   } else {\n";
            $columnDescr .= "   oc += '<div style=\\\"background-color:#EEEEEE;color:#000000;font-size:10px;cursor:pointer;padding:3px;margin:5px;text-align:center;border:1px solid #595959;border-radius:4px;\\\" ';\n";
            $columnDescr .= "   oc += 'onclick=\\\"window.open(defaultremotedomain + \\'".$GLOBALS['codeFolder']."uploadimage.php?userid=9&token=9&prefix=".$name."&wd_id=&field_id=\\');\\\">add image</div>';\n";
            $columnDescr .= "   }\n";
            $columnDescr .= "   jQuery('#wdimg_".$name."').html(oc);\n";
            $columnDescr .= "   if(!Boolean(nochange) && typeof formchange_".$prefix." == 'function') formchange_".$prefix."();\n";
            $columnDescr .= "}\n";            
            $columnDescr .= "jsfwdimg_".$name."wa('".$row[$q['field_id']]."',true);\n";
            $columnDescr .= "</script>\n";
            
            $js = "if(Boolean(jQuery('#".$name."').val())) url += '&".$q['field_id']."=' + encodeURIComponent(jQuery('#".$name."').val());\n";
         } else {                  
            if (strlen($columnDescr)>100) $columnDescr = substr($columnDescr,0,97)."...";
         }
         
         $res = array();
         $res['str'] = $columnDescr;
         $res['js'] = $js;
         $res['helpstr'] = $helpstr;
         return $res;         
      }
      
      // END - table input display
      //--------------------------------------------------------------

      
      
      
      
      
      
      
      
      
      
        function getSearchHTML($question){
            $returnHTML="";
            if (0==strcmp($question['field_type'],"RADIO") || 0==strcmp($question['field_type'],"POLLRADIO") || 0==strcmp($question['field_type'],"VOTE") || 0==strcmp($question['field_type'],"DROPDOWN")) {
               $opts = array();
               $optionList = convertBack($question['question']);
               if ($optionList != NULL) {
                  $opts['ALL'] = "";
                  $temp = trim(strtok($optionList,","));
                  while (strcmp($temp,"") != 0) {
                     $opts[$temp] = $temp;
                     $temp = trim(strtok(","));
                  }
                  $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"STATE")) {
               $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".listStates(getParameter($paramName),$paramName,TRUE,"class=\"selectbox\"")."</td></tr>";
            } else if (0==strcmp($question['field_type'],"FOREIGN")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
                  $opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,getParameter($paramName),"class=\"selectbox\"");
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".$opts."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"FOREIGNCB")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  $paramName="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,getParameter($paramName),"class=\"selectbox\"");
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".$opts."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"FOREIGNTBL")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL && $survey_info[2] != NULL) {
                  $paramName="cmscsv_w".$question['wd_id'].$question['field_id'];
                  //$opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,getParameter($paramName),"class=\"selectbox\"");
                  //$returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".$opts."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"TABLE")) {
            } else if (0==strcmp($question['field_type'],"MANY")) {
            } else if (0==strcmp($question['field_type'],"REGION")) {
            } else if (0==strcmp($question['field_type'],"USERS")) {
               $ua = new UserAcct();
               $usersA = $ua->getUsersForSegment(strtolower(trim($question['question'])));
               $users = $usersA['users'];
               $opts = array();
               $opts['ALL'] = "";
               for ($i=0; $i<count($users); $i++) {
                  $user = $ua->getUser($users[$i]['userid']);
                  $opts[$user['fname']." ".$user['lname']." ".$user['company']]=$user['userid'];
               }
               $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
            } else if (0==strcmp($question['field_type'],"SITELIST") || 0==strcmp($question['field_type'],"SITEOPT")) {
               $ctx = new Context();
               $optionList = $ctx->getSiteOptions();
               if ($optionList != NULL) {
                  $optionList['ALL'] = "";
                  $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $optionList, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"CHECKBOX") || 0==strcmp($question['field_type'],"HRZCHKBX")) {
               $opts = array();
               $optionList = convertBack($question['question']);
               if ($optionList != NULL) {
                  $opts['ALL'] = "";
                  $temp = trim(strtok($optionList,","));
                  while (strcmp($temp,"") != 0) {
                     $opts[$temp] = $temp;
                     $temp = trim(strtok(","));
                  }
                  $paramName="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"NEWCHKBX")) {
               $opts = array();
               //$bothnvp = explode(";",trim(convertBack($question['question'])));
               //$names = explode(",",$bothnvp[1]);
               //$values = explode(",",$bothnvp[2]);
               $bothnvp = separateStringBy(trim(convertBack($question['question'])),";");
               $names = separateStringBy($bothnvp[1],",");
               if ($bothnvp[2]==NULL) $bothnvp[2] = $bothnvp[1];
               $values = separateStringBy($bothnvp[2],",");
               if ($names != NULL && count($names)>0) {
                  $opts['ALL'] = "";
                  for ($i=0; $i<count($names); $i++) {
                     if ($values[$i]!=NULL) $opts[$names[$i]]=$values[$i];
                     else $opts[$names[$i]]=$names[$i];
                  }
                  $paramName="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"MBL_MC") || 0==strcmp($question['field_type'],"MBL_IMG")) {
               $opts = array();
               $bothnvp = separateStringBy(trim(convertBack($question['question'])),";");
               $names = separateStringBy($bothnvp[1],",");
               if ($bothnvp[2]==NULL) $bothnvp[2] = $bothnvp[1];
               $values = separateStringBy($bothnvp[2],",");
               if ($names != NULL && count($names)>0) {
                  $opts['ALL'] = "";
                  for ($i=0; $i<count($names); $i++) {
                     if ($values[$i]!=NULL) $opts[$names[$i]]=$values[$i];
                     else $opts[$names[$i]]=$names[$i];
                  }
                  $paramName="cmsz_w".$question['wd_id'].$question['field_id'];
                  $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
               }
            } else if (0==strcmp($question['field_type'],"INT") || 0==strcmp($question['field_type'],"DEC") || 0==strcmp($question['field_type'],"MONEY")) {
               $lParamName="cmsl_w".$question['wd_id'].$question['field_id'];
               $hParamName="cmsh_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($lParamName);
               $low = str_replace("$","",$low);
               $low = str_replace(",","",$low);
               $high = getParameter($hParamName);
               $high = str_replace("$","",$high);
               $high = str_replace(",","",$high);
               $returnHTML .= "<tr class=\"searchfields\"><td>Min ".$question['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$lParamName."\" value=\"".$low."\" size=\"10\"></td></tr>";
               $returnHTML .= "<tr class=\"searchfields\"><td>Max ".$question['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$hParamName."\" value=\"".$high."\" size=\"10\"></td></tr>";
            } else if (0==strcmp($question['field_type'],"DATE") || 0==strcmp($question['field_type'],"DATETIME")) {
               $lParamName="cmsdl_w".$question['wd_id'].$question['field_id'];
               $hParamName="cmsdh_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($lParamName);
               $high = getParameter($hParamName);
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']." After (YYYY-MM-DD)</td><td><input class=\"input\" type=\"text\" name=\"".$lParamName."\" value=\"".$low."\" size=\"10\"></td></tr>";
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']." Before (YYYY-MM-DD)</td><td><input class=\"input\" type=\"text\" name=\"".$hParamName."\" value=\"".$high."\" size=\"10\"></td></tr>";
            } else if (0==strcmp($question['field_type'],"AGE")) {
               $lParamName="cmsal_w".$question['wd_id'].$question['field_id'];
               $hParamName="cmsah_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($lParamName);
               $high = getParameter($hParamName);
               $returnHTML .= "<tr class=\"searchfields\"><td>Min age for ".$question['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$lParamName."\" value=\"".$low."\" size=\"10\"></td></tr>";
               $returnHTML .= "<tr class=\"searchfields\"><td>Max age for ".$question['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$hParamName."\" value=\"".$high."\" size=\"10\"></td></tr>";
            } else if (0==strcmp($question['field_type'],"IMAGE") || 0==strcmp($question['field_type'],"FILE")) {
            } else if (0==strcmp($question['field_type'],"SNGLCHKBX")) {
               $paramName="cmsq_w".$question['wd_id'].$question['field_id'];
               $opts = array();
               $opts[' '] = "";
               $opts['YES'] = "YES";
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td>".getOptionList($paramName, $opts, getParameter($paramName), FALSE, "class=\"selectbox\"")."</td></tr>";
            } else if (0!=strcmp($question['field_type'],"INFO") && 0!=strcmp($question['field_type'],"SPACER")){
               $paramName="cmsz_w".$question['wd_id'].$question['field_id'];
               $returnHTML .= "<tr class=\"searchfields\"><td>".$question['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$paramName."\" value=\"".getParameter($paramName)."\" size=\"10\"></td></tr>";
            }
            return $returnHTML;
        }

        function getSearchHTMLSmall($question,$div_separator=NULL,$profile=0,$onchange=""){
            //print "\n<!-- inside getSearchHTMLSmall() ".$question['field_type']." ".$question['label']." -->\n";
            $returnHTML=array();
            $paramName=array();
            $style_label = "";
            $style_value = "";
            $style_input = "";
            $style_dropd = "";
            if($profile==0) {
               $style_label = "float:left;min-width:100px;margin-top:6px;margin-right:4px;";
               $style_value = "float:left;min-width:40px;";
               $style_input = "width:120px;font-size:10px;font-family:verdana;";
               $style_dropd = "font-size:10px;font-family:verdana;";
            } else if($profile==1) {
               $style_label = "float:left;margin-top:2px;margin-right:4px;";
               $style_value = "float:left;";
               $style_input = "width:35px;font-size:12px;margin-right:4px;font-family:arial;";
               $style_dropd = "font-size:12px;font-family:arial;margin-top:2px;";
            }
            if($div_separator==NULL) $div_separator = "<div class=\"wdsearch_separator\"></div>";

            if (0==strcmp($question['field_type'],"RADIO") || 0==strcmp($question['field_type'],"POLLRADIO") || 0==strcmp($question['field_type'],"VOTE") || 0==strcmp($question['field_type'],"DROPDOWN")) {
               $opts = array();
               $optionList = convertBack($question['question']);
               if ($optionList != NULL) {
                  $bothnvp = explode(";",$optionList);
                  $names = explode(",",$bothnvp[0]);
                  $values = explode(",",$bothnvp[1]);
                  $tot = count($names);
                  if (count($values)>count($names)) $tot = count($values);
                  
                  $opts[$question['label']] = "";
                  for($i=0;$i<$tot;$i++) {
                     if ($values[$i]==NULL) $values[$i]=$names[$i];
                     else if ($names[$i]==NULL) $names[$i]=$values[$i];
                     $opts[$names[$i]] = $values[$i];
                  }
                  $paramName[0] = "cmsq_w".$question['wd_id'].$question['field_id'];
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, "style=\"".$style_dropd."\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"",FALSE,18)."</div>";
               }
            } else if (0==strcmp($question['field_type'],"STATE")) {
               $paramName[0]="cmsq_w".$question['wd_id'].$question['field_id'];
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".listStates(getParameter($paramName[0]),$paramName[0],TRUE," style=\"font-size:10px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
            } else if (0==strcmp($question['field_type'],"FOREIGN")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  $paramName[0]="cmsq_w".$question['wd_id'].$question['field_id'];
                  $opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName[0],getParameter($paramName[0]),"class=\"selectbox\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"",$question['label']);
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".$opts."</div>";
               }
            } else if (0==strcmp($question['field_type'],"FOREIGNCB")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  $paramName[0]="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName[0],getParameter($paramName[0]),"class=\"selectbox\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"",$question['label']);
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".$opts."</div>";
               }
            } else if (0==strcmp($question['field_type'],"FOREIGNTBL") || 0==strcmp($question['field_type'],"FOREIGNTDD")) {
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL && $survey_info[2] != NULL) {
                  $paramName[0]="cmscsv_w".$question['wd_id'].$question['field_id'];
                  //$opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,getParameter($paramName),"class=\"selectbox\"");
                  //$returnHTML .= $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".$opts."</div>";
               }
            } else if (0==strcmp($question['field_type'],"TABLE")) {
            } else if (0==strcmp($question['field_type'],"MANY")) {
            } else if (0==strcmp($question['field_type'],"REGION")) {
            } else if (0==strcmp($question['field_type'],"USERS") || 0==strcmp($question['field_type'],"USERSRCH") || 0==strcmp($question['field_type'],"USERLIST")) {
               $ua = new UserAcct();
               $usersA = $ua->getUsersForSegment(strtolower(trim($question['question'])));
               $users = $usersA['users'];
               $opts = array();
               $opts['ALL'] = "";
               for ($i=0; $i<count($users); $i++) {
                  $user = $ua->getUser($users[$i]['userid']);
                  $opts[$user['fname']." ".$user['lname']." ".$user['company']]=$user['userid'];
               }
               $paramName[0]="cmsq_w".$question['wd_id'].$question['field_id'];
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, "style=\"font-size:10px;font-family:verdana;width:120px;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
            } else if (0==strcmp($question['field_type'],"SITELIST") || 0==strcmp($question['field_type'],"SITEOPT")) {
               $ctx = new Context();
               $optionList = $ctx->getSiteOptions();
               if ($optionList != NULL) {
                  $optionList['ALL'] = "";
                  $paramName[0]="cmsq_w".$question['wd_id'].$question['field_id'];
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $optionList, getParameter($paramName[0]), FALSE, "style=\"font-size:10px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
               }
            } else if (0==strcmp($question['field_type'],"CHECKBOX") || 0==strcmp($question['field_type'],"HRZCHKBX")) {
               $opts = array();
               $optionList = convertBack($question['question']);
               if ($optionList != NULL) {
                  $opts['ALL'] = "";
                  $temp = trim(strtok($optionList,","));
                  while (strcmp($temp,"") != 0) {
                     $opts[$temp] = $temp;
                     $temp = trim(strtok(","));
                  }
                  $paramName[0]="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, "style=\"font-size:8px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"",FALSE,18)."</div>";
               }
            } else if (0==strcmp($question['field_type'],"NEWCHKBX")) {
               $opts = array();
               //$bothnvp = explode(";",trim(convertBack($question['question'])));
               //$names = explode(",",$bothnvp[1]);
               //$values = explode(",",$bothnvp[2]);
               $bothnvp = separateStringBy(trim(convertBack($question['question'])),";");
               $names = separateStringBy($bothnvp[1],",");
               if ($bothnvp[2]==NULL) $bothnvp[2] = $bothnvp[1];
               $values = separateStringBy($bothnvp[2],",");
               if ($names != NULL && count($names)>0) {
                  $opts['ALL'] = "";
                  for ($i=0; $i<count($names); $i++) {
                     if ($values[$i]!=NULL) $opts[$names[$i]]=$values[$i];
                     else $opts[$names[$i]]=$names[$i];
                  }
                  $paramName[0]="cmscsv_w".$question['wd_id'].$question['field_id'];
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, " style=\"font-size:10px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
               }
            } else if (0==strcmp($question['field_type'],"MBL_MC") || 0==strcmp($question['field_type'],"MBL_IMG")) {
               $opts = array();
               //$bothnvp = explode(";",trim(convertBack($question['question'])));
               //$names = explode(",",$bothnvp[1]);
               //$values = explode(",",$bothnvp[2]);
               //print "<br>\nquestion: ".$question['question']."<br>\n";
               $bothnvp = separateStringBy(trim(convertBack($question['question'])),";");
               $names = separateStringBy($bothnvp[0],",");
               if ($bothnvp[1]==NULL) $bothnvp[1] = $bothnvp[0];
               $values = separateStringBy($bothnvp[1],",");
               if ($names != NULL && count($names)>0) {
                  $opts['ALL'] = "";
                  for ($i=0; $i<count($names); $i++) {
                     $tempvalue = $names[$i];
                     if ($values[$i]!=NULL) $tempvalue=$values[$i];
                     if (strlen($tempvalue)>22) $tempvalue = substr($tempvalue,(strlen($tempvalue)-22));
                     //print "<br>\n".$tempvalue;
                     $tempname = $names[$i];
                     if (strlen($tempname)>22) $tempname = substr($tempname,(strlen($tempname)-22));
                     $opts[$tempname]=$tempvalue;
                  }
                  $paramName[0]="cmsz_w".$question['wd_id'].$question['field_id'];
                  $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, "style=\"font-size:10px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
                  
                  //print "<br>\nfields found: ".$paramName."<br>\nhtml: ".convertString($returnHTML)."\n<br>\n<br>";
               }
            } else if (0==strcmp($question['field_type'],"INT") || 0==strcmp($question['field_type'],"DEC") || 0==strcmp($question['field_type'],"MONEY")) {
               $paramName[0]="cmsl_w".$question['wd_id'].$question['field_id'];
               $paramName[1]="cmsh_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($paramName[0]);
               $low = str_replace("$","",$low);
               $low = str_replace(",","",$low);
               $high = getParameter($paramName[1]);
               $high = str_replace("$","",$high);
               $high = str_replace(",","",$high);
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">Min ".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[0]."\" value=\"".$low."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
               $returnHTML[1] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">Max ".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[1]."\" value=\"".$high."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
            } else if (0==strcmp($question['field_type'],"DATE") || 0==strcmp($question['field_type'],"DATETIME")) {
               $paramName[0]="cmsdl_w".$question['wd_id'].$question['field_id'];
               $paramName[1]="cmsdh_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($paramName[0]);
               $high = getParameter($paramName[1]);
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']." After </div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[0]."\" value=\"".$low."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
               $returnHTML[1] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']." Before </div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[1]."\" value=\"".$high."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
            } else if (0==strcmp($question['field_type'],"AGE")) {
               $paramName[0]="cmsal_w".$question['wd_id'].$question['field_id'];
               $paramName[1]="cmsah_w".$question['wd_id'].$question['field_id'];
               $low = getParameter($paramName[0]);
               $high = getParameter($paramName[1]);
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">Min age for ".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[0]."\" value=\"".$low."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
               $returnHTML[1] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">Max age for ".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" name=\"".$paramName[1]."\" value=\"".$high."\" style=\"font-size:10px;font-family:verdana;width:120px;\" onkeyup=\"".$onchange."\"></div>";
            } else if (0==strcmp($question['field_type'],"IMAGE") || 0==strcmp($question['field_type'],"FILE")) {
            } else if (0==strcmp($question['field_type'],"SNGLCHKBX")) {
               $paramName[0]="cmsq_w".$question['wd_id'].$question['field_id'];
               $opts = array();
               $opts[' '] = "";
               $opts['YES'] = "YES";
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\">".getOptionList($paramName[0], $opts, getParameter($paramName[0]), FALSE, "style=\"font-size:10px;font-family:verdana;\" id=\"".$paramName[0]."\" onchange=\"".$onchange."\"")."</div>";
            } else if (0!=strcmp($question['field_type'],"INFO") && 0!=strcmp($question['field_type'],"SPACER")){
               $paramName[0]="cmsz_w".$question['wd_id'].$question['field_id'];
               $returnHTML[0] = $div_separator."<div style=\"".$style_label."\" class=\"wdsearch_label\">".$question['label']."</div><div style=\"".$style_value."\" class=\"wdsearch_value\"><input type=\"text\" id=\"".$paramName[0]."\" name=\"".$paramName[0]."\" value=\"".getParameter($paramName[0])."\" style=\"".$style_input."\" onkeyup=\"".$onchange."\"></div>";
            }
            
            $resp = array();
            $resp['param'] = $paramName;
            $resp['html'] = $returnHTML;
            return $resp;
        }

        function printQuestionHTML($wd_id,$q,$wd_row_id=null,$disabled=false,$longVersion=true,$glossary=null,$color=NULL,$required=FALSE,$postTxt=NULL){
              $answered = $this->getAnswer($wd_id,$wd_row_id, $q['field_id']);
              if ($answered['answer']==NULL) {
                 $temp = getParameter("w".$wd_id."a".$q['field_id']);
                 if (is_array($temp)) $temp = implode(",",$temp);
                 $answered['answer'] = $temp;
              }
              
            //print "\n<!-- printQuestionHTML(): q: ".$q['fieldid']." label: ".$q['label']." answer: ".$answered['answer']." -->\n";
              if ($glossary==null) $glossary = new Glossary(0);
              if ($color != NULL) $colorAttr = " bgcolor=\"#".$color."\"";

              $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
              $rels2 = $this->getField2Rel($wd_id,$q['field_id']);
              $displayStyle="";
              if ($rels2!=NULL && count($rels2)>0) {
                  for ($i=0; $i<count($rels2); $i++) {
                     $neededAns = $this->getAnswer($wd_id,$wd_row_id,$rels2[$i]['fid1']);
                     if ($neededAns['answer']==NULL) {
                        $temp = getParameter("w".$wd_id."a".$rels2[$i]['fid1']);
                        if (is_array($temp)) $temp = implode(",",$temp);
                        $neededAns['answer'] = $temp;
                     }

                     $field1 = $this->getField($wd_id, $rels2[$i]['fid1']);
                     $answers_arr = explode(",",$neededAns['answer']);
                     for ($j=0; $j<count($answers_arr); $j++){
                        //print "<br>needed ans: ".$answers_arr[$j]." value needed by rule: ".$rels2[$i]['f1value']." field1: ".$rels2[$i]['fid1']." q: ".$q['field_id']." default: ".$field1['defaultval']."<BR>\n";
                        if (0==strcmp(trim($answers_arr[$j]),trim($rels2[$i]['f1value']))) {
                           $displayStyle="";
                           break 2;
                        } else if (0==strcmp(trim($neededAns['answer']),"") && 0==strcmp(trim($field1['defaultval']),trim($rels2[$i]['f1value']))) {
                           $displayStyle="";
                           break 2;
                        } else {
                           $displayStyle=" style=\"display: none;\"";
                        }
                     }
                  }
              }

              $q['label'] = convertBack($q['label']);
              if (strcmp($q['field_type'],"TEXT")==0) {
                  $this->printTextHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"FILE")==0 || strcmp($q['field_type'],"MBL_UPL")==0) {
                  $this->printFileHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"IMAGE")==0) {
                  $this->printImageHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"MBL_UPL_OLD")==0) {
                  $this->printMobileUploadHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"INT")==0) {
                  $this->printIntegerHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"DEC")==0 || strcmp($q['field_type'],"MONEY")==0) {
                  $this->printDecimalHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"TEXTAREA")==0) {
                  $this->printTextAreaHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"HTML")==0) {
                  $this->printRTEHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"LIKERT")==0) {
                  $this->printLikertHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"NEWLIKERT")==0) {
                  $this->printNewLikertHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"TABLE")==0) {
                  $this->printTableHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"PERCENT")==0) {
                  $this->printPercentHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"NEWPRCNT")==0) {
                  $this->printNewPercentHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"DATE")==0 || strcmp($q['field_type'],"DATETIME")==0 || strcmp($q['field_type'],"AGE")==0) {
                  $this->printDateHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"INFO")==0) {
                  $this->printInfoHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"SPACER")==0) {
                  $this->printSpacerHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"RADIO")==0 || strcmp($q['field_type'],"VOTE")==0) {
                  $this->printRadioHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"POLLRADIO")==0) {
                  $this->printPollRadioHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"SITEOPT")==0) {
                  $this->printSiteoptHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"USERS")==0) {
                  $this->printUsersHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"USERSRCH")==0 || strcmp($q['field_type'],"USERAUTO")==0) {
                  $this->printUsersSearchHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"USERLIST")==0) {
                  //$this->printUsersListHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
                  $this->printTextHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"SITELIST")==0) {
                  $this->printSitelistHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"DROPDOWN")==0) {
                  $this->printDropdownHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"STATE")==0) {
                  $this->printStateHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"FOREIGN")==0) {
                  $this->printForeignHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"FOREIGNCB")==0) {
                  $this->printForeignCBHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"FOREIGNTBL")==0) {
                  $this->printForeignTBLHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"FOREIGNTDD")==0) {
                  $this->printForeignTBLDDHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"MANY")==0) {
                  $this->printManyHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"SNGLCHKBX")==0) {
                  $this->printSingleCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"CHECKBOX")==0) {
                  $this->printCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"HRZCHKBX")==0) {
                  $this->printHorizCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"NEWCHKBX")==0) {
                  $this->printNewCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"MBL_MC")==0 || strcmp($q['field_type'],"MBL_IMG")==0) {
                  $this->printNewCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              } elseif (strcmp($q['field_type'],"REGION")==0) {
                  $this->printRegionHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
              }

        }

      function printTextHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $ua = new UserAcct();
         $user = $ua->getUser(isLoggedOn());
         $value = $ua->doSubstitutions($value,$user);
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
             <td class="action">
                <input type="text" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" value="<?php echo $value; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" size="25" <?php if($disabled) print "DISABLED"; ?>>
                <?php echo $postTxt; ?>
             </td>
          </tr>
         <?php
      }

      function printFileHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         if ($answered['answer']==NULL || 0==strcmp($answered['answer'],"%%%EMPTY%%%") || 0==strcmp($answered['answer'],"%E%")) {
         ?>
          <tr <?php echo $colorAttr; ?>>
             <td class="label"><?php echo $questionText; ?><?php if($required) echo " *"; ?></td>
             <td class="answer">
               <input name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" type="file" size="40" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>">
             </td>
          </tr>
          <tr style="display:none;" <?php echo $colorAttr; ?>>
             <td class="label">Update <?php echo $questionText; ?> manually</td>
             <td class="answer"><input type="text" name="w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>" value="" size="25" <?php if($disabled) print "DISABLED"; ?>></td>
          </tr>
          
         <?php 
         } else {
         ?>
         
          <tr style="display:none;" <?php echo $colorAttr; ?>>
             <td class="label">Update <?= $questionText ?> manually</td><td><input type="text" id="w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>" name="w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>" value="<?php echo $answered['answer']; ?>" size="25" <?php if($disabled) print "DISABLED"; ?>></td>
          </tr>
          <!--input type="hidden" name="w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>" value="<?php echo $answered['answer']; ?>"-->
          <tr <?php echo $colorAttr; ?>>
             <td class="label"><?= $questionText ?></td>
             <td class="answer">
               <input name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" type="file" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" size="40">
             </td>
          </tr>
          <tr <?php echo $colorAttr; ?>>
             <td class="label">
               Current file
             </td>
             <td id="td_w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>">
             <?php
                $tempurl = $answered['answer'];
                if($tempurl!=NULL) {
                   if(0!=strcmp(substr(strtolower($tempurl),0,4),"http")) $tempurl = $GLOBALS['srvyURL'].$tempurl;
                }
             ?>
               <a href="<?php echo $tempurl; ?>"><?php echo $tempurl; ?></a>
               <span style="margin-left:20px;color:blue;font-size:8px;cursor:pointer;" onclick="if(confirm('Are you sure you would like to permanently delete this file?')) { document.getElementById('w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>').value = '%%%EMPTY%%%';document.getElementById('td_w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>').innerHTML = ''; }">remove</span>
             </td>
          </tr>
         <?php
         }
      }

      function printImageHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         if ($answered['answer']==NULL || 0==strcmp(trim($answered['answer']),"")) {
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?php echo $questionText; ?><?php if($required) echo " *"; ?></td>
             <td class="answer">
               <input name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" type="file" size="20" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>">
             </td>
          </tr>
         <?php 
         } else {
            if(strlen($answered['answer'])>5 && 0!=strcmp(substr($answered['answer'],0,4),"http")) $answered['answer'] = $GLOBALS['srvyURL'].$answered['answer'];
         ?>
          <tr <?php echo $displayStyle; ?> valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?php echo $questionText; ?> (current)</td>
            <td class="answer">
               <table cellpadding="0" cellspacing="0">
               <tr><td>
               <img src="<?php echo $answered['answer']; ?>" style="width:100px;height:auto;">
               </td><td>
               &nbsp; <input type="checkbox" name="w<?php echo $wd_id; ?>del_a<?php echo $q['field_id']; ?>" value="1">Delete this image.
               </td></tr>
               </table>
            </td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>o_a<?php echo $q['field_id']; ?>" value="<?php echo $answered['answer']; ?>">
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label">Update <?= $questionText ?></td>
             <td class="answer">
               <input name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" type="file" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" size="20">
             </td>
          </tr>
         <?php
         }
      }
      
      function OLDprintMobileUploadHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
             <td class="action">
                <?php if($value!=NULL) print "<img src=\"".$value."\" style=\"height:60px;width:auto;\"><br>"; ?>
                <input type="text" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" value="<?php echo $value; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" size="25" <?php if($disabled) print "DISABLED"; ?>>
                <?php echo $postTxt; ?>
             </td>
          </tr>
         <?php
      }
      
      function printIntegerHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
             <td class="answer">
                <input type="text" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="<?php echo $value; ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" size="15" <?php if($disabled) print "DISABLED"; ?>>
                <?php echo $postTxt; ?>
                <span id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>-error" style="display:none"><b>You must enter a valid number</b></span>
                <!-- script>vf.addValidationForField("w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>", "integer", "integer");</script -->
             </td>
          </tr>
         <?php
      }

      function printDecimalHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
             <td class="answer">
                <input type="text" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="<?php echo $value; ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" size="15" <?php if($disabled) print "DISABLED"; ?>>
                <?php echo $postTxt; ?>
                <span id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>-error" style="display:none"><b>You must enter a valid decimal</b></span>
                <!-- script>vf.addValidationForField("w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>", "decimal", "decimal");</script -->
             </td>
          </tr>
         
         <?php
      }

      function printTextAreaHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         $cols="60";
         $rows="3";
         //if ($longVersion) {
         //   $cols="85";
         //   $rows = "8";
         //}
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2" class="label">
               <div><?= $questionText ?><?php if($required) echo " *"; ?></div>
               <textarea name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" COLS="<?php echo $cols; ?>" ROWS="<?php echo $rows; ?>" <?php if($disabled) print "DISABLED"; ?>><?php print $value; ?></textarea>
             </td>
          </tr>
         <?php
      }

      function printRTEHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = trim(convertBack($answered['answer']));
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2" class="label">
               <div><?= $questionText ?><?php if($required) echo " *"; ?></div>
               <script>initRTE('<?php echo convertJavascriptString($value); ?>', '../jsfadmin/rte/html/example.css','w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>','200px');</script>
             </td>
          </tr>
         <?php
      }

      function printLikertHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = $q['label'];
         $temp = separateStringBy(" ".$questionText,",");
         $answers = separateStringBy(" ".convertBack($answered['answer']),",");
         
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2">
                  <table cellpadding="5" cellspacing="3">
                  <tr valign="top">
                      <td align="center">&nbsp;&nbsp;</td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Strongly"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Disagree"></td>
                           </tr>
                           </table>
                      </td>  
                      <td align="center" bgcolor="#DDDDDD"><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Disagree"></td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Neither%20Agree"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=or%20Disagree"></td>
                           </tr>
                           </table>
                      </td>  
                      <td align="center" bgcolor="#DDDDDD"><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Agree"></td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Strongly"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Agree"></td>
                           </tr>
                           </table>
                      </td>  
                  </tr>
         
                  <?php for($i=0; $i<count($temp); $i++) { ?>
                  <tr valign="top">
                      <td class="label"><?php echo $glossary->flagAllTerms(trim($temp[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_1_".$i; ?>" value="-2" <?php if($answers[$i]==-2) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_2_".$i; ?>" value="-1" <?php if($answers[$i]==-1) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_3_".$i; ?>" value="0"  <?php if($answers[$i]==0 && $answers[$i] !== NULL) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_4_".$i; ?>" value="1"  <?php if($answers[$i]==1) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_5_".$i; ?>" value="2"  <?php if($answers[$i]==2) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                  </tr>
                  <?php } ?>
                  <input type="hidden" name="w<?php echo $wd_id; ?>jsfarray<?= $q['field_id'] ?>" value="<?php echo count($temp); ?>">
                  </table>
              </td>
          </tr>
         <?php
      }

      function printNewLikertHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = convertBack($q['question']);
         $temp = separateStringBy(" ".$questionText,",");
         $answers = separateStringBy(" ".convertBack($answered['answer']),",");
         
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2">
                  <?php echo $q['label']; ?>
                  <table cellpadding="5" cellspacing="3">
                  <tr valign="top">
                      <td align="center">&nbsp;&nbsp;</td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Strongly"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Disagree"></td>
                           </tr>
                           </table>
                      </td>  
                      <td align="center" bgcolor="#DDDDDD"><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Disagree"></td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Neither%20Agree"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=or%20Disagree"></td>
                           </tr>
                           </table>
                      </td>  
                      <td align="center" bgcolor="#DDDDDD"><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Agree"></td>  
                      <td align="center" bgcolor="#DDDDDD">
                           <table cellpadding="0" cellspacing="0">
                           <tr>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Strongly"></td>
                           <td><img src="<?php echo getBaseURL(); ?>jsfcode/verticaltext.php?txt=Agree"></td>
                           </tr>
                           </table>
                      </td>  
                  </tr>
         
                  <?php for($i=0; $i<count($temp); $i++) { ?>
                  <tr valign="top">
                      <td class="label"><?php echo $glossary->flagAllTerms(trim($temp[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_1_".$i; ?>" value="-2" <?php if($answers[$i]==-2) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_2_".$i; ?>" value="-1" <?php if($answers[$i]==-1) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_3_".$i; ?>" value="0"  <?php if($answers[$i]==0 && $answers[$i] !== NULL) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_4_".$i; ?>" value="1"  <?php if($answers[$i]==1) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                      <td align="center"><input type="radio" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_".$i; ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']."_5_".$i; ?>" value="2"  <?php if($answers[$i]==2) print "CHECKED=\"checked\""; ?> <?php if($disabled) print "DISABLED"; ?>></td>  
                  </tr>
                  <?php } ?>
                  <input type="hidden" name="w<?php echo $wd_id; ?>jsfarray<?= $q['field_id'] ?>" value="<?php echo count($temp); ?>">
                  </table>
              </td>
          </tr>
         <?php
      }

      function printTableHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = convertBack($q['label']);
         $temp = separateStringBy(" ".$questionText,";");
         $headers = separateStringBy(" ".$temp[0],",");
         $rows = separateStringBy(" ".$temp[1],",");
         $answers = separateStringBy(" ".$answered['answer'],",");
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2">
                  <table cellpadding="2" cellspacing="2">
                  <?php $Acount=0; ?>
                  <tr valign="top">
                  <?php for($i=0; $i<count($headers); $i++) { ?>
                      <td class="toplabel" align="center"><?php echo $glossary->flagAllTerms(trim($headers[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>  
                  <?php } ?>
                  </tr>
         
                  <?php for($i=0; $i<count($rows); $i++) { ?>
                  <tr valign="top">
                      <td class="label"><?php echo $glossary->flagAllTerms(trim($rows[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>
                  <?php for($j=1; $j<count($headers); $j++) { ?>
                      <td align="center"><input type="text" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" value="<?= convertBack(trim($answers[$Acount])) ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" size="13" <?php if($disabled) print "DISABLED"; ?>></td>  
                      <?php $Acount++; ?>
                  <?php } ?>
                  </tr>
                  <?php } ?>
                  <input type="hidden" name="w<?php echo $wd_id; ?>jsfarray<?= $q['field_id'] ?>" value="<?= $Acount ?>">
                  </table>
              </td>
          </tr>
         <?php
      }

      function printPercentHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = convertBack($q['label']);
         $optionList = convertBack($q['question']);
         $answerText = convertBack($answered['answer']);
         $headers = separateStringBy($optionList,",");
         $rows = separateStringBy($questionText,",");
         $answers = separateStringBy(" ".$answerText,",");
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2">
                  <table cellpadding="2" cellspacing="2">
                  <tr>
                     <td id="totalpctg_<?php echo $q['field_id']; ?>"></td>
                     <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     <td id="remainpctg_<?php echo $q['field_id']; ?>"></td>
                  </tr>
                  </table>
                  <table cellpadding="2" cellspacing="2">
                  <?php $Acount=0; ?>
                  <?php $percentjscript=""; ?>
                  <tr valign="top">
                  <td class="toplabel" align="center">&nbsp;</td>  
                  <td class="toplabel" align="center">Points&nbsp;&nbsp;</td>  
                  <?php for($i=0; $i<count($headers); $i++) { ?>
                      <td class="toplabel" align="center"><?php echo $glossary->flagAllTerms(trim($headers[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>  
                  <?php } ?>
                  </tr>
         
                  <?php for($i=0; $i<count($rows); $i++) { ?>
                  <tr valign="top">
                      <td class="label"><?php echo $glossary->flagAllTerms(trim($rows[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>
                      <td align="center"><input type="text" onkeyup="eval_<?php echo $q['field_id']; ?>_pctg();" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" value="<?= convertBack(trim($answers[$Acount])) ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" size="13" <?php if($disabled) print "DISABLED"; ?>></td>  
                      <?php $percentjscript.=" + Number(document.cmssurveyform.w".$wd_id."a".$q['field_id']."_".$Acount.".value)"; ?>
                      <?php $Acount++; ?>
                  <?php for($j=0; $j<count($headers); $j++) { ?>
                      <td align="center"><input type="text" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" value="<?= convertBack(trim($answers[$Acount])) ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" size="13" <?php if($disabled) print "DISABLED"; ?>></td>  
                      <?php $Acount++; ?>
                  <?php } ?>
                  </tr>
                  <?php } ?>
                  <input type="hidden" name="w<?php echo $wd_id; ?>jsfarray<?= $q['field_id'] ?>" value="<?= $Acount ?>">
                  </table>
                  <script type="text/javascript">
                     eval_<?php echo $q['field_id']; ?>_pctg();
                     function eval_<?php echo $q['field_id']; ?>_pctg() {
                        var grandtotal = 0 <?php echo $percentjscript; ?>;
         
                        if (isNaN(grandtotal)) {
                              document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="red"><b>Please Verify that you have only integers in your Points column.</b></font>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = ' '; 
                        } else {
                           if (grandtotal < 100) {
                              document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<b>Total Points Used: '+grandtotal+'</b>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = 'Points remaining: '+(100-grandtotal); 
                           } else {
                              if (grandtotal == 100) {
                                 document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="green"><b>Total Points Used: '+grandtotal+'</b></font>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = 'All Points Allocated'; 
                              } else {
                                 document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="red"><b>Total Points Used: '+grandtotal+'</b></font>'; 
                                 document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = '<b>Please decrease your total points</b>'; 
                              }
                           }
                        }
                     }
                  </script>
              </td>
          </tr>
         <?php
      }

      function printNewPercentHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$questionText = convertBack($q['label']);
         $questionText = "Explanation";
         $optionList = convertBack($q['question']);
         $answerText = $answered['answer'];
         $rows = separateStringBy($optionList,",");
         $headers = separateStringBy($questionText,",");
         $answers = separateStringBy(" ".$answerText,",");
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
              <td colspan="2">
                  <?php echo $q['label']; ?><br>
                  <table cellpadding="2" cellspacing="2">
                  <tr>
                     <td id="totalpctg_<?php echo $q['field_id']; ?>"></td>
                     <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                     <td id="remainpctg_<?php echo $q['field_id']; ?>"></td>
                  </tr>
                  </table>
                  <table cellpadding="2" cellspacing="2">
                  <?php $Acount=0; ?>
                  <?php $percentjscript=""; ?>
                  <tr valign="top">
                  <td class="toplabel" align="center">&nbsp;</td>  
                  <td class="toplabel" align="center">Points&nbsp;&nbsp;</td>  
                  <?php for($i=0; $i<count($headers); $i++) { ?>
                      <td class="toplabel" align="center"><?php echo $glossary->flagAllTerms(trim($headers[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>  
                  <?php } ?>
                  </tr>
         
                  <?php for($i=0; $i<count($rows); $i++) { ?>
                  <tr valign="top">
                      <td class="label"><?php echo $glossary->flagAllTerms(trim($rows[$i]),"#5691c4"); ?>&nbsp;&nbsp;</td>
                      <td align="center"><input type="text" onkeyup="eval_<?php echo $q['field_id']; ?>_pctg();" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" value="<?= convertBack(trim($answers[$Acount])) ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" size="13" <?php if($disabled) print "DISABLED"; ?>></td>  
                      <?php $percentjscript.=" + Number(document.cmssurveyform.w".$wd_id."a".$q['field_id']."_".$Acount.".value)"; ?>
                      <?php $Acount++; ?>
                  <?php for($j=0; $j<count($headers); $j++) { ?>
                      <td align="center"><input type="text" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" value="<?= convertBack(trim($answers[$Acount])) ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= $Acount ?>" size="13" <?php if($disabled) print "DISABLED"; ?>></td>  
                      <?php $Acount++; ?>
                  <?php } ?>
                  </tr>
                  <?php } ?>
                  <input type="hidden" name="w<?php echo $wd_id; ?>jsfarray<?= $q['field_id'] ?>" value="<?= $Acount ?>">
                  </table>
                  <script type="text/javascript">
                     eval_<?php echo $q['field_id']; ?>_pctg();
                     function eval_<?php echo $q['field_id']; ?>_pctg() {
                        var grandtotal = 0 <?php echo $percentjscript; ?>;
         
                        if (isNaN(grandtotal)) {
                              document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="red"><b>Please Verify that you have only integers in your Points column.</b></font>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = ' '; 
                        } else {
                           if (grandtotal < 100) {
                              document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<b>Total Points Used: '+grandtotal+'</b>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = 'Points remaining: '+(100-grandtotal); 
                           } else {
                              if (grandtotal == 100) {
                                 document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="green"><b>Total Points Used: '+grandtotal+'</b></font>'; 
                              document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = 'All Points Allocated'; 
                              } else {
                                 document.getElementById('totalpctg_<?php echo $q['field_id']; ?>').innerHTML = '<font color="red"><b>Total Points Used: '+grandtotal+'</b></font>'; 
                                 document.getElementById('remainpctg_<?php echo $q['field_id']; ?>').innerHTML = '<b>Please decrease your total points</b>'; 
                              }
                           }
                        }
                     }
                  </script>
              </td>
          </tr>
         <?php
      }

      function printDateHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = $q['label'];
         $answerText = $answered['answer'];
         if ($answerText===NULL || 0==strcmp($answerText,"")) $answerText=trim($q['defaultval']);
         if (0==strcmp($answerText,"EMPTY") || 0==strcmp($answerText,"%%%NONE%%%")) $time=NULL;
         else if ($answerText!=null) $time = strtotime($answerText);
         else $time = time();
         
         $dateOpt = getDateSelection(date("d",$time),date("m",$time),date("Y",$time),"w".$wd_id."date_".$q['field_id']);
         if (0==strcmp($q['defaultval'],"EMPTY") || 0==strcmp($q['defaultval'],"%%%NONE%%%")) {
            if (0==strcmp($answerText,"EMPTY") || 0==strcmp($answerText,"%%%NONE%%%")) $dateOpt = getEmptyDateSelection(NULL,NULL,NULL,"w".$wd_id."date_".$q['field_id']);
            else $dateOpt = getEmptyDateSelection(date("d",$time),date("m",$time),date("Y",$time),"w".$wd_id."date_".$q['field_id']);
         }
         if (strcmp($q['field_type'],"DATETIME")==0) {
            $timeOpt = getTimeSelection(date("h",$time),date("i",$time),date("A",$time),"w".$wd_id."time_".$q['field_id']);
            if (0==strcmp($q['defaultval'],"EMPTY")) {
               if (0==strcmp($answerText,"EMPTY")) $timeOpt = getEmptyTimeSelection(NULL,NULL,NULL,"w".$wd_id."time_".$q['field_id']);
               else $timeOpt = getEmptyTimeSelection(date("h",$time),date("i",$time),date("A",$time),"w".$wd_id."time_".$q['field_id']);
            }
         }
         ?>
          <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
             <td class="label"><?php echo $glossary->flagAllTerms($questionText,"#5691c4"); ?><?php if($required) echo " *"; ?></td>
             <td class="answer">
                <table cellpadding="0" cellspacing="0" border="0">
                <tr><td><?php echo $dateOpt; ?></td><td>&nbsp;&nbsp;</td><td><?php echo $timeOpt; ?></td></tr>
                </table>
             </td>
          </tr>
         <?php
      }

      function printInfoHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top">
              <td colspan="2" class="label">
              <?php
               if($q['disa']==1) echo $q['label'];
               else echo $glossary->flagAllTerms($q['label'],"#5691c4"); 
              ?>
              </td>
          </tr>
         <?php
      }

      function printSpacerHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top">
              <td colspan="2"><BR></td>
          </tr>
         <?php
      }

      function printRadioHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <table cellpadding="0" cellspacing="0" border="0">
               <tr><td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td></tr>
         <?php
         $optionList = convertBack($q['question']);
         if ($optionList != NULL) {
           $options = separateStringBy($optionList,",");
           //$temp = trim(strtok($optionList,","));
           //$count2=1;
           for ($a=0; $a<count($options); $a++) {
              $selected="";
              if (strcmp(strtolower(trim($value)),strtolower(trim($options[$a])))==0) $selected="CHECKED";
               ?>
                <tr><td class="qoptions" valign="top"><input type="radio" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= ($a) ?>" value="<?php echo trim($options[$a]); ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $glossary->flagAllTerms($options[$a],"#5691c4"); ?> &nbsp;&nbsp;&nbsp;</td></tr>
               <?php
           }
         }
         ?>
             <tr><td>&nbsp;</td></tr>
             </table>
             </td>
          </tr>
         <?php
      }

      function printPollRadioHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         $width = 400;
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <table cellpadding="1" cellspacing="1" border="0">
               <tr><td colspan="2" class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td></tr>
         <?php
         $optionList = convertBack($q['question']);
         if ($optionList != NULL) {
           $options = separateStringBy($optionList,",");
           $stats = $this->getStats($wd_id);
           $max = ceil($stats['max'] + (0.1 * $stats['max']));
           for ($a=0; $a<count($options); $a++) {
              $selected="";
              if (strcmp(strtolower(trim($value)),strtolower(trim($options[$a])))==0) $selected="CHECKED";
               $barwidth = ceil($width * ($stats['totals'][$q['field_id']][trim($options[$a])] / $max));
               ?>
               <tr>
                  <td class="qoptions" valign="top">
                  <?php if (!$disabled) { ?>
                  <input type="radio" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= ($a) ?>" value="<?php echo trim($options[$a]); ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>>
                  <?php } else if(0==strcmp($selected,"CHECKED")) { ?>
                  * 
                  <?php } ?>
                  </td>
                  <td>
                  <div style="position:relative;width:<?php echo $width; ?>px;height:18px;overflow:hidden;background-color:#333333;">
                  <div style="position:absolute;left:0px;top:0px;width:<?php echo $barwidth; ?>px;height:18px;background-color:RED;"></div>
                  <div style="position:absolute;left:0px;top:0px;width:<?php echo $width; ?>px;height:18px;font-size:14px;font-family:tahoma;color:#FFFFFF;"><?php echo $glossary->flagAllTerms($options[$a],"#5691c4"); ?></div>
                  <div style="position:absolute;right:10px;top:0px;width:<?php echo $width; ?>px;height:18px;text-align:right;font-size:14px;font-family:tahoma;color:#FFFFFF;"><?php echo $stats['totals'][$q['field_id']][trim($options[$a])]; ?></div>
                  </div>
                  </td>
               </tr>
               <?php
           }
         }
         ?>
             </table>
             </td>
          </tr>
         <?php
      }

      function printSiteoptHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $ctx = new Context();
         $optionList = $ctx->getSiteOptions();
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = convertBack($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <table cellpadding="0" cellspacing="0" border="0">
               <tr><td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td></tr>
         <?php
         $count2=1;
         foreach($optionList as $key => $value) {
           $selected = "";
           if ($value != NULL) {
              $answers_arr = separateStringBy($value,",");
              for ($j=0; $j<count($answers_arr); $j++) if (strcmp(trim($answers_arr[$j]),$key)==0) $selected="CHECKED";
           }
               ?>
                <tr><TD class="qoptions"><input type="checkbox" name="w<?php echo $wd_id; ?>a<?= $q['question_id'] ?>[]" id="w<?php echo $wd_id; ?>a<?= $q['question_id'] ?>_<?= ($count2-1) ?>" value="<?= $value ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $glossary->flagAllTerms($key,"#5691c4"); ?> &nbsp;&nbsp;&nbsp;</td></tr>
               <?php
           $count2++;
         }
         ?>
             <tr><td>&nbsp;</td></tr>
             </table>
             </td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['question_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['question_id'] ?>">
         <?php
      }

      function printUsersHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $dispfield = TRUE;
         $ua = new UserAcct();
         $usersA = $ua->getUsersForSegment(strtolower(trim($q['question'])));
         $users = $usersA['users'];
         for ($i=0; $i<count($users); $i++) {
            $user = $ua->getUser($users[$i]['userid']);
            $optionList[$user['userid'].". ".$user['fname']." ".$user['lname']." ".$user['company']]=$user['userid'];
         }
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) {
            if (is_numeric($q['defaultval'])) {
               $value=$q['defaultval'];
            } else if (0==strcmp($q['defaultval'],"loggedonhide")) {
               $value=isLoggedOn();
               $dispfield = $ua->canUserDelete($value);
            } else if (0==strcmp($q['defaultval'],"loggedon")) {
               $value=isLoggedOn();
            }
         }

         if ($dispfield) {
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer">
               <select name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>>
               <option id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_empty" value=""> </option>
               <?php
               if ($optionList != NULL) {
                 $a = 0;
                 foreach ($optionList as $key => $value2) {
                    $selected="";
                    if (strcmp($value,$value2)==0) $selected="selected=\"selected\"";
                     ?>
                      <option id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?php echo $a; ?>" value="<?php echo $value2; ?>" <?php echo $selected; ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $key; ?></option>
                     <?php
                    $a++;
                 }
               }
               ?>
             </select>
             </td>
          </tr>
         <?php } else { ?>
            <input type="hidden" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="<?php echo $value; ?>">
         <?php
         }
      }

      function printUsersSearchHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $ua = new UserAcct();

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=getParameter("w".$wd_id."a".$q['field_id']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $user = NULL;
         if ($value!=NULL && $value>0) $user = $ua->getUser($value);

         $disp = "";
         $empty = FALSE;
         if ($user!=NULL && $user['userid']==$value) $disp = $user['fname']." ".$user['lname']." ".$user['company'];
         else $empty = TRUE;

      ?>
      <script type="text/javascript">
         if (typeof showcmstxtonly == 'undefined') { 
            var e = document.createElement("script");
            e.src = "<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js";
            e.type = "text/javascript";
            document.getElementsByTagName("head")[0].appendChild(e);
         }
      </script>

         <input type="hidden" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="<?php echo $value; ?>">
         <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer"><?php echo $disp; ?></td>
         </tr>
         <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
         <td colspan="2">
            <table cellpadding="1" cellspacing="0">
            <tr><td colspan="4">
            <?php if ($empty) print "Search and select a user to add to this field."; else print "Search and select a user to replace the one above."; ?>
            </td></tr>
            <tr>
            <td style="font-family:arial;font-size:10px;">Search: </td>
            <td><input type="text" style="font-family:arial;font-size:10px;width:100px;" <?php if($disabled) print "DISABLED"; ?> id="ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" name="ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="" size="30"> </td>
            <td><input type="button" style="font-family:arial;font-size:10px;" <?php if($disabled) print "DISABLED"; ?> name="usersearchbtn" value="Go" onClick="var urlaj='<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/ajaxcontroller.php?action=listusers&divname=w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>&search=' + document.getElementById('ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>').value;showcmstxtonly(urlaj,3,'/jsfimages/loading.gif','cmsselw<?php echo $wd_id; ?>a<?= $q['field_id'] ?>');"></td>
            <td id="cmsselw<?php echo $wd_id; ?>a<?= $q['field_id'] ?>"><select name="blah"><option value=""></option></select></td>
            </tr>
            </table>
         </td>
         </tr>
         <?php
      }

      function printUsersListHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $ua = new UserAcct();

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=getParameter("w".$wd_id."a".$q['field_id']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $users = array();
         if ($value!=NULL) {
            $usedids = array();
            $vals = separateStringBy($value,",",NULL,TRUE);
            for($i=0;$i<count($vals);$i++) {
               $val = trim($vals[$i]);
               if(!isset($usedids[$val])) {
                  $user = $ua->getUser($val);
                  if($user!=NULL && $user['userid']!=NULL) {
                     $users[] = $user;
                     $usedids[$val] = 1;
                  }
               }
            }
         }
      ?>
      <script type="text/javascript">
         if (typeof showcmstxtonly == 'undefined') { 
            var e = document.createElement("script");
            e.src = "<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/getcms.js";
            e.type = "text/javascript";
            document.getElementsByTagName("head")[0].appendChild(e);
         }
      </script>

         <input type="hidden" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="<?php echo $value; ?>">
         <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer">
            <?php
               for($i=0;$i<count($users);$i++) {
                  print "<div>";
                  print $users[$i]['userid'].". ".$users[$i]['fname']." ".$users[$i]['lname']." ".$users[$i]['company'];
                  print " <span onclick=\"\" style=\"color:red;font-size:8px;\">remove</span>";
                  print "</div>";
               }
            ?>
            </td>
         </tr>
         <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
         <td colspan="2">
            <table cellpadding="1" cellspacing="0">
            <tr><td colspan="4">Search and select a user to add.</td></tr>
            <tr>
            <td style="font-family:arial;font-size:10px;">Search: </td>
            <td><input type="text" style="font-family:arial;font-size:10px;width:100px;" <?php if($disabled) print "DISABLED"; ?> id="ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" name="ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" value="" size="30"> </td>
            <td><input type="button" style="font-family:arial;font-size:10px;" <?php if($disabled) print "DISABLED"; ?> name="usersearchbtn" value="Go" onClick="var urlaj='<?php echo $GLOBALS['baseURLSSL']; ?>jsfcode/ajaxcontroller.php?action=listusers&divname=w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>&search=' + document.getElementById('ajxusrch_w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>').value;showcmstxtonly(urlaj,3,'/jsfimages/loading.gif','cmsselw<?php echo $wd_id; ?>a<?= $q['field_id'] ?>');"></td>
            <td id="cmsselw<?php echo $wd_id; ?>a<?= $q['field_id'] ?>"><select name="blah"><option value=""></option></select></td>
            </tr>
            </table>
         </td>
         </tr>
         <?php
      }

      function printSitelistHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $ctx = new Context();
         $optionList = $ctx->getSiteOptions();
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = convertBack(trim($answered['answer']));
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer">
               <select name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>>
         <?php
         if ($optionList != NULL) {
           $a = 0;
           foreach ($optionList as $key => $value2) {
              $selected="";
              if (strcmp($value,$value2)==0) $selected="selected=\"selected\"";
               ?>
                <option id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?php echo $a; ?>" value="<?php echo $value2; ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $key; ?></option>
               <?php
              $a++;
           }
         }
         ?>
             </select>
             </td>
          </tr>
         <?php
      }

      function printDropdownHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$rels1 = $this->getField1Rel($wd_id,$q['field_id']);
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            $javascript = " onChange=\"change".$wd_id.$q['field_id']."();\"";
            $javascript2 = "<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "if (document.getElementsByName('w".$wd_id."a".$q['field_id']."')[0].options[document.getElementsByName('w".$wd_id."a".$q['field_id']."')[0].selectedIndex].value=='".$rels1[$i]['f1value']."'){\n";
               $javascript2 .= "   document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= "}\n";
            }
            $javascript2 .= "}\n</script>\n";
         }

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer">
               <?php echo $javascript2; ?>
               <select name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" <?php echo $javascript; ?> <?php if($disabled) print "DISABLED"; ?>>
               <option value="" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_0"></option>
         <?php
         $questionList = trim(convertBack($q['question']));
         $bothnvp = explode(";",$questionList);
         $names = explode(",",$bothnvp[0]);
         $values = explode(",",$bothnvp[1]);
         for ($a=0; $a<count($names); $a++) {
           $selected="";
           $ddl_value = trim($values[$a]);
           if ($ddl_value==NULL) $ddl_value = trim($names[$a]);
           if (strcmp(strtolower($value),strtolower($ddl_value))==0) $selected="selected=\"selected\"";
            ?>
             <option id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?php echo ($a+1); ?>" value="<?php echo $ddl_value; ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $names[$a]; ?></option>
            <?php
         }
         ?>
             </select>
             </td>
          </tr>
         <?php
      }

      function printStateHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$rels1 = $this->getField1Rel($wd_id,$q['field_id']);
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            $javascript = " onChange=\"change".$wd_id.$q['field_id']."();\"";
            $javascript2 = "<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "if (this.cmssurveyform.w".$wd_id."a".$q['field_id'].".options[this.cmssurveyform.w".$wd_id."a".$q['field_id'].".selectedIndex].value=='".$rels1[$i]['f1value']."'){\n";
               $javascript2 .= "   document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= "}\n";
            }
            $javascript2 .= "}\n</script>\n";
         }

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer">
               <?php echo $javascript2; ?>
               <select name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>" <?php echo $javascript; ?> <?php if($disabled) print "DISABLED"; ?>>
               <option value="" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_0"></option>
         <?php
         $optionList = getStateOptionList(TRUE);
         if ($optionList != NULL) {
           $options = array_keys($optionList);
           for ($a=0; $a<count($options); $a++) {
              $selected="";
              if (strcmp($value,$options[$a])==0) $selected="selected=\"selected\"";
               ?>
                <option id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?php echo ($a+1); ?>" value="<?php echo $options[$a]; ?>" <?= $selected ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $options[$a]; ?></option>
               <?php
           }
         }
         ?>
             </select>
             </td>
          </tr>
         <?php
      }

      function printForeignHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $opts = "";
         $survey_info = separateStringBy(convertBack($q['question']),",");
         if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
            $paramName="w".$wd_id."a".$q['field_id'];
            $opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,$value);
         }
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer"><?php echo $opts; ?></td>
          </tr>
         <?php
      }

      function printForeignCBHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $divextra = "class=\"answer\" style=\"padding:3px;margin-right:5px;float:left;\"";
         $cbextra = "class=\"answer\" style=\"margin-right:2px;\"";
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim(convertBack($answered['answer']));
         
         //print "<br><br>***chj*** value: ".$value."<br><br>";
         
         if(0==strcmp(substr($q['defaultval'],0,1),"q") && is_numeric(substr($q['defaultval'],1))){
            print "\n<!-- ***chj*** current val: ".$value." -->\n";
            print "\n<!-- ***chj*** default val: ".$q['defaultval']." -->\n";
            print "\n<!-- ***chj*** calling getAnswer(".$wd_id.", ".$answered['row']['wd_row_id'].", ".$q['defaultval'].") -->\n";
            $temp = $this->getAnswer($wd_id,$answered['row']['wd_row_id'], $q['defaultval']);
            $value .= ",".convertBack(trim($temp['answer']));
            print "\n<!-- ***chj*** new val: ".$value." -->\n";
         } else if ($value==NULL) {
            $value=$q['defaultval'];
         }
         
         $opts = "";
         $survey_info = separateStringBy(convertBack($q['question']),",");
         if ($survey_info[0]!=NULL && $survey_info[1]!=NULL) {
            $paramName="w".$wd_id."a".$q['field_id'];
            $opts = $this->getSurveyCheckBoxOptions($survey_info[0],$survey_info[1],$paramName,$value,$divextra,$cbextra);
            //$opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,$value);
         }
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer"><?php echo $opts; ?></td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }

      function printForeignTBLHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $divextra = "class=\"answer\" style=\"padding:3px;margin-right:5px;float:left;\"";
         $cbextra = "class=\"answer\" style=\"margin-right:2px;\"";
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim(convertBack($answered['answer']));
         if ($value==NULL) $value=$q['defaultval'];
         $opts = "";
         $survey_info = separateStringBy(convertBack($q['question']),",");
         if ($survey_info[0]!=NULL && $survey_info[1]!=NULL && $survey_info[2]!=NULL) {
            $paramName="w".$wd_id."a".$q['field_id'];
            //$opts = $this->getSurveyCheckBoxOptions($survey_info[0],$survey_info[1],$paramName,$value,$divextra,$cbextra);
            //$opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,$value);
            $opts = $this->getTableCheckBoxOptions($survey_info[0],$survey_info[1],$survey_info[2],$paramName,$value,$divextra,$cbextra);
         }
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer"><?php echo $opts; ?></td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }

      function printForeignTBLDDHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $divextra = "class=\"answer\" style=\"padding:3px;margin-right:5px;float:left;\"";
         $cbextra = "class=\"answer\" style=\"margin-right:2px;\"";
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4");
         $value = trim(convertBack($answered['answer']));
         if ($value==NULL) $value=$q['defaultval'];
         $opts = "";
         $survey_info = separateStringBy(convertBack($q['question']),",");
         if ($survey_info[0]!=NULL && $survey_info[1]!=NULL && $survey_info[2]!=NULL) {
            $paramName="w".$wd_id."a".$q['field_id'];
            //$opts = $this->getSurveyCheckBoxOptions($survey_info[0],$survey_info[1],$paramName,$value,$divextra,$cbextra);
            //$opts = $this->getSurveyOptions($survey_info[0],$survey_info[1],$paramName,$value);
            //$opts = $this->getTableCheckBoxOptions($survey_info[0],$survey_info[1],$survey_info[2],$paramName,$value,$extra);
            $opts = $this->getTableDropdownOptions($survey_info[0],$survey_info[1],$survey_info[2],$paramName,$value,$extra);
         }
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>>
            <td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td>
            <td class="answer"><?php echo $opts; ?></td>
          </tr>
         <?php
      }

      function printManyHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         if (class_exists("CustomWebsiteDataType")) {
            $cm = new CustomWebsiteDataType();
            $cm->printManyHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1);
         }
      }

      function getManyInputOnly($wd_id,$q,$answers=NULL,$javascript=NULL,$disabled=FALSE){
         $str = "";
         if (class_exists("CustomWebsiteDataType")) {
            $cm = new CustomWebsiteDataType();
            $str = $cm->getManyInputOnly($wd_id,$q,$answers,$javascript,$disabled);
         }
         return $str;
      }

      function printSingleCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            //$javascript = " onClick=\"change".$wd_id.$q['field_id']."();\"";
            $javascript = " onClick=\"DisplayWait(this);setTimeout(function(){change".$wd_id.$q['field_id']."();HideWait();},20);\"";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            $javascript2 .= "var val='NO';\nif (document.getElementById('w".$wd_id."a".$q['field_id']."').checked) val='YES';\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "   if(val=='".trim($rels1[$i]['f1value'])."') document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
            }
            $javascript2 .= "}\n</script>\n";
         }







         $selected="";
         $value = trim($answered['answer']);
         if ($value==NULL || 0==strcmp($value,"")) $value=strtoupper($q['defaultval']);
         if (strcmp($value,"YES")==0) $selected="CHECKED=\"CHECKED\"";
         ?>
         <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" <?php echo $colorAttr; ?>><td colspan="2" class="qoptions">
         <?php echo $javascript2; ?>
         <input type="checkbox" name="w<?php echo $wd_id; ?>a<?php echo $q['field_id'] ?>" id="w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" value="YES" <?php echo $selected; ?> <?php echo $javascript; ?> <?php if($disabled) print "DISABLED=\"DISABLED\""; ?>> <?php echo $glossary->flagAllTerms($q['label'],"#5691c4"); ?>
         </td></tr>
         <?php         
      }

      function printCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$rels1 = $this->getField1Rel($wd_id,$q['field_id']);
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            $javascript = " onClick=\"DisplayWait(this);setTimeout(function(){change".$wd_id.$q['field_id']."();HideWait();},20);\"";
            $javascript2 = "<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            $javascript2 .= "var inputs = document.getElementsByTagName(\"input\");\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "for (var i=0;i<inputs.length;i++){\n";
               $javascript2 .= "   var e = inputs[i];\n";
               $javascript2 .= "   if ((e.name == 'w".$wd_id."a".$q['field_id']."[]') && (e.value=='".trim($rels1[$i]['f1value'])."') && e.checked)\n";
               $javascript2 .= "      document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= "}\n";
            }
            $javascript2 .= "}\n</script>\n";
         }

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = convertBack(trim($answered['answer']));
         if ($value==NULL || 0==strcmp($value,"")) $value=trim($q['defaultval']);
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <?php echo $javascript2; ?>
               <table cellpadding="0" cellspacing="0" border="0">
               <tr><td class="label"><?= $questionText ?><?php if($required) echo " *"; ?></td></tr>
         <?php
         $optionList = convertBack($q['question']);
         if ($optionList != NULL) {
           $count2=1;
           $options_avl = separateStringBy($optionList,",");
           for ($i=0; $i<count($options_avl); $i++) {
              $temp = trim($options_avl[$i]);
              $selected = "";
              if ($value != NULL) {
                 $answers_arr = separateStringBy($value,",");
                 for ($j=0; $j<count($answers_arr); $j++) if (strcmp(trim($answers_arr[$j]),$temp)==0) $selected="CHECKED";
              }
               ?>
                <tr><TD class="qoptions">
                  <input type="checkbox" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>[]" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= ($count2-1) ?>" value="<?= $temp ?>" <?= $selected ?> <?php echo $javascript; ?> <?php if($disabled) print "DISABLED"; ?>>
                  <?php echo $glossary->flagAllTerms($temp,"#5691c4"); ?>
                  &nbsp;&nbsp;&nbsp;
               </td></tr>
               <?php
              $count2++;
           }
         }
         ?>
             <tr><td>&nbsp;</td></tr>
             </table>
             </td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }

      function printHorizCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$rels1 = $this->getField1Rel($wd_id,$q['field_id']);
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            $javascript = " onClick=\"DisplayWait(this);setTimeout(function(){change".$wd_id.$q['field_id']."();HideWait();},20);\"";
            $javascript2 = "<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            $javascript2 .= "var inputs = document.getElementsByTagName(\"input\");\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "for (var i=0;i<inputs.length;i++){\n";
               $javascript2 .= "   var e = inputs[i];\n";
               $javascript2 .= "   if ((e.name == 'w".$wd_id."a".$q['field_id']."[]') && (e.value=='".trim($rels1[$i]['f1value'])."') && e.checked)\n";
               $javascript2 .= "      document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= "}\n";
            }
            $javascript2 .= "}\n</script>\n";
         }

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = convertBack(trim($answered['answer']));
         if ($value==NULL || 0==strcmp($value,"")) $value=trim($q['defaultval']);
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <?php echo $javascript2; ?>
               <?= $questionText ?><?php if($required) echo " *"; ?><br>
               <table cellpadding="2" cellspacing="0" border="0">
         <?php
         $optionList = convertBack($q['question']);
         if ($optionList != NULL) {
           $count2=1;
           $options_avl = separateStringBy($optionList,",");
           $across = $options_avl[0];
           for ($i=1; $i<count($options_avl); $i++) {
              if (($i%$across)==1) print "<TR>";
              $temp = trim($options_avl[$i]);
              $selected = "";
              if ($value != NULL) {
                 $answers_arr = separateStringBy($value,",");
                 for ($j=0; $j<count($answers_arr); $j++) if (strcmp(trim($answers_arr[$j]),$temp)==0) $selected="CHECKED";
              }
                  ?>
                   <TD class="qoptions"><input type="checkbox" name="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>[]" id="w<?php echo $wd_id; ?>a<?= $q['field_id'] ?>_<?= ($count2-1) ?>" value="<?= $temp ?>" <?= $selected ?> <?php echo $javascript; ?> <?php if($disabled) print "DISABLED"; ?>> <?php echo $glossary->flagAllTerms($temp,"#5691c4"); ?> &nbsp;&nbsp;&nbsp;</td>
                  <?php
              $count2++;
              if (($i%$across)==0) print "</TR>";
           }
           if ((($count2-1)%$across)>0) print "</TR>";
         }
         ?>
             <tr><td>&nbsp;</td></tr>
             </table>
             </td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }

      // formatting is: X;Names;Values
      // X = Number of checkboxes across
      // Names = CSV of display values for each checkbox
      // Values = CSV of corresponding value stored in the DB 
      function printNewCheckboxHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         //$rels1 = $this->getField1Rel($wd_id,$q['field_id']);
         $javascript = "";
         $javascript2 = "";
         if ($rels1!=NULL && count($rels1)>0) {
            $javascript = " onClick=\"DisplayWait(this);setTimeout(function(){change".$wd_id.$q['field_id']."();HideWait();},20);\"";
            $javascript2 = "<script type=\"text/javascript\">\nfunction change".$wd_id.$q['field_id']."(){\n";
            $javascript2 .= "var inputs = document.getElementsByTagName(\"input\");\n";
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
            }
            for ($i=0; $i<count($rels1); $i++) {
               $javascript2 .= "for (var i=0;i<inputs.length;i++){\n";
               $javascript2 .= "   var e = inputs[i];\n";
               $javascript2 .= "   if ((e.name == 'w".$wd_id."a".$q['field_id']."[]') && (e.value=='".trim($rels1[$i]['f1value'])."') && e.checked)\n";
               $javascript2 .= "      document.getElementById('tr_w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= "}\n";
            }
            $javascript2 .= "}\n</script>\n";
         }

         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $answers = convertBack(trim($answered['answer']));
         if ($answers==NULL || 0==strcmp($answers,"")) $answers=trim($q['defaultval']);
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <?php echo $javascript2; ?>
               <?= $questionText ?><?php if($required) echo " *"; ?><br>
               <table cellpadding="2" cellspacing="0" border="0">
         <?php
         $bothnvp = separateStringBy(trim(convertBack($q['question'])),";");
         $across = $bothnvp[0];
         $names = separateStringBy($bothnvp[1],",");
         $values = separateStringBy($bothnvp[2],",");
         if ($bothnvp[1]==NULL && $bothnvp[2]==NULL) {
             $names = separateStringBy($bothnvp[0],",");
             $across = 1;
         } else if (!is_numeric($across)) {
             $across = 1;
             $values = $names;
             $names = separateStringBy($bothnvp[0],",");
         }
                  
         for ($i=0; $i<count($names); $i++) {
            if (($i%$across)==0) print "<TR>\n";
            $temp = trim($names[$i]);
            $temp2 = trim($values[$i]);
            if ($temp!=NULL && $temp2==NULL) $temp2=$temp;
            else if ($temp==NULL && $temp2!=NULL) $temp=$temp2;
            $selected = "";
            if ($answers != NULL) {
               $answers_arr = separateStringBy($answers,",");
               for ($j=0; $j<count($answers_arr); $j++) if (strcmp(trim($answers_arr[$j]),$temp2)==0) $selected="CHECKED";
            }
            print "<TD class=\"qoptions\">";
            print "<input type=\"checkbox\" name=\"w".$wd_id."a".$q['field_id']."[]\" id=\"w".$wd_id."a".$q['field_id']."_".$i."\"";
            print " value=\"";
            print $temp2;
            print "\" ".$selected." ".$javascript;
            if($disabled) print " DISABLED";
            print "> ".$glossary->flagAllTerms($temp,"#5691c4")." &nbsp;&nbsp;&nbsp;</td>\n";
            if ((($i+1)%$across)==0) print "</TR>\n";
         }
         if ((count($names)%$across)>0) print "</TR>\n";
         ?>
             <tr><td>&nbsp;</td></tr>
             </table>
             </td>
          </tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }

      // formatting is: X;Names;Values
      // X = Number of checkboxes across
      // Names = CSV of display values for each checkbox
      // Values = CSV of corresponding value stored in the DB 
      function getNewCheckboxInputOnly($wd_id,$q,$answers=NULL,$javascript=NULL,$disabled=FALSE){
         $str = "";
         $questionText = "";
         //$answers = convertBack(trim($answered['answer']));
         if ($answers==NULL || 0==strcmp($answers,"")) $answers=$q['defaultval'];
         $answers = convertBack(trim($answers));
         $bothnvp = separateStringBy(trim(convertBack($q['question'])),";");
         $across = $bothnvp[0];
         $names = separateStringBy($bothnvp[1],",");
         $values = separateStringBy($bothnvp[2],",");
         if ($bothnvp[1]==NULL && $bothnvp[2]==NULL) {
            $names = separateStringBy($bothnvp[0],",");
            $across = 1;
         }
         $str .= "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n";
         for ($i=0; $i<count($names); $i++) {
            if (($i%$across)==0) $str.="<TR>\n";
            $temp = trim($names[$i]);
            $temp2 = trim($values[$i]);
            if ($temp!=NULL && $temp2==NULL) $temp2=$temp;
            else if ($temp==NULL && $temp2!=NULL) $temp=$temp2;
            $selected = "";
            $answers_arr = separateStringBy($answers,",");
            for ($j=0; $j<count($answers_arr); $j++) {
               $thisans = trim($answers_arr[$j]);
               if (strcmp($thisans,$temp2)==0) {
                  $selected="CHECKED";
                  break;
               }
            }
            $d = "";
            if($disabled) $d=" DISABLED";
            $str .= "<TD class=\"qoptions\">";
            $str .= "<input type=\"checkbox\" name=\"w".$wd_id."a".$q['field_id']."[]\" id=\"w".$wd_id."a".$q['field_id']."_".$i."\"";
            $str .= " value=\"".$temp2."\" ".$selected.$d." ".$javascript.">";
            $str .= $temp." </td>\n";
            if ((($i+1)%$across)==0) $str .= "</TR>\n";
         }
         if ((count($names)%$across)>0) $str .= "</TR>\n";
         $str .= "</table>\n";
         $str .= "<input type=\"hidden\" name=\"w".$wd_id."m".$q['field_id']."\" value=\"1\" id=\"w".$wd_id."m".$q['field_id']."\">\n";
         return $str;
      }

      function printRegionHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
         $questionText = "";
         if ($q['label'] != null) $questionText = $glossary->flagAllTerms($q['label'],"#5691c4")." &nbsp;";
         $value = convertBack(trim($answered['answer']));
         if ($value==NULL || 0==strcmp($value,"")) $value=trim($q['defaultval']);
         ?>
           <tr <?php echo $displayStyle; ?> id="tr_w<?php echo $wd_id; ?>a<?php echo $q['field_id']; ?>" valign="top" <?php echo $colorAttr; ?>><td colspan="2" class="label">
               <?= $questionText ?><?php if($required) echo " *"; ?><br>
         <?php
         $c = array();
         $answers_arr = separateStringBy($value,",");
         for ($j=0; $j<count($answers_arr); $j++) $c[trim($answers_arr[$j])]="CHECKED";
         $this->printRegionSelectionBoxes("LIGHTBLUE",$c,"w".$wd_id."a".$q['field_id']."[]");
         ?>
          </td></tr>
          <input type="hidden" name="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>" value="1" id="w<?php echo $wd_id; ?>m<?= $q['field_id'] ?>">
         <?php
      }


      //TODO:
      function getAnsweredPercentage($wd_id,$wd_row_id) {
         $sci = $this->getDetails($wd_id,$wd_row_id);
         $wd_id = $sci['wd_id'];         
         $countQ = 0;
         $countA = 0;
         $questions = $this->getAllFieldsSystem($wd_id);
         
         for ($j=0; $j<count($questions); $j++) {
            $countQ++;
            $q = $questions[$j];
            $answered = $this->getAnswer($wd_id,$wd_row_id, $q['field_id']);
            if ($answered['answer'] != null) {
               $countA++;
            }
         }
         if ($countQ==0 || $countA==0) $result = 0;
         else $result = (int)(($countA/$countQ)*100.0);

         return $result."%";
      }
   
   function loadContents($contents,$wd_id){
      $rowindex = 0;
      $delimiter=",";
      $newcontents = csvRemoveQuotes($contents);
      //$newcontents = str_replace(","," , ",$newcontents);
      $lines = separateStringBy($newcontents,"\n");

      $wdObj = new WebsiteData();
      $questions = $wdObj->getAllFieldsSystem($wd_id);

      $headers=array();
      while ($questions[$headers[0]]==NULL || 0==strcmp($headers[0],"wd_row_id") || 0==strcmp($headers[0],"useridid")) {
         $headerRow = $lines[$rowindex];
         $headers = separateStringBy(" ".$headerRow." ",$delimiter);
         $rowindex++;
      }
      for ($i=0; $i<count($headers); $i++) $indexTable[strtolower(trim($headers[$i]))] = $i;

      for ($i=$rowindex; $i<count($lines); $i++) {
         $fields = separateStringBy(" ".$lines[$i]." ",$delimiter);
         $userid = trim( $fields[$indexTable['userid']]);
         if ($userid==NULL) $userid=isLoggedOn();
         $wd_row_id = trim($fields[$indexTable['wd_row_id']]);
         $queryN = array();
         $queryV = array();
         $answerFound = FALSE;
         for ($j=0; $j<count($questions); $j++) {
            $propertyName = trim(strtolower($questions[$j]['label']));
            $propertyName2 = trim(strtolower($questions[$j]['field_id']));
            $propertyValue = trim($fields[$indexTable[$propertyName]]);
            $propertyValue2 = trim($fields[$indexTable[$propertyName2]]);
            if ($propertyName != NULL && $propertyValue!=NULL) {
               $queryN[] = $questions[$j]['field_id'];
               $queryV[] = $propertyValue;
               $answerFound = TRUE;
            } else if ($propertyName2 != NULL && $propertyValue2!=NULL) {
               $queryN[] = $questions[$j]['field_id'];
               $queryV[] = $propertyValue2;
               $answerFound = TRUE;
            }
         }
         if ($answerFound) {
            $dbi = new MYSQLAccess();
            if ($wd_row_id==NULL) {
               $query = "INSERT INTO wd_".$wd_id." (userid, dbmode, created, lastupdate, lastupdateby";
               for ($j=0; $j<count($queryN); $j++) $query .= ", ".$queryN[$j];
               $query .= ") VALUES (".$userid.", 'NEW', NOW(), NOW(), 'CSV_Load ".date("Y-m-d H:i:s")."'";
               for ($j=0; $j<count($queryV); $j++) $query .= ", '".$queryV[$j]."'";
               $query .= ");";
               $wd_row_id = $dbi->insertGetValue($query);
            } else {
               $query = "UPDATE wd_".$wd_id." SET lastupdate=NOW(), dbmode='UPDATED', lastupdateby=SUBSTR(CONCAT('CSV ".date("Y-m-d H:i:s").", ',IFNULL(lastupdateby,' ')),1,2048)";
               for ($j=0; $j<count($queryN); $j++) $query .= ", ".$queryN[$j]."='".$queryV[$j]."'";
               $query .= " WHERE wd_row_id=".$wd_row_id.";";
               $dbi->update($query);
            }
         }
      }
   }
   

      function convertCheckboxCSV($questionText,$answerText,$label=NULL,$field_id=NULL,$map=NULL){
         $header_file_m = "";
         $header_file_q = "";
         $header_file = "";
         $content_file = "";
         $simplestr = "";
         
         $answers = separateStringBy(trim(convertBack($answerText)),",",NULL,TRUE);         
         $bothnvp = separateStringBy(trim(convertBack($questionText)),";");
         $across = $bothnvp[0];
         $names = array();
         $values = array();
         if(isset($bothnvp[1])) $names = separateStringBy($bothnvp[1],",");
         if(isset($bothnvp[2])) $values = separateStringBy($bothnvp[2],",");
         if ((!isset($bothnvp[1]) || $bothnvp[1]==NULL) && (!isset($bothnvp[2]) || $bothnvp[2]==NULL)) {
             $names = separateStringBy($bothnvp[0],",");
             $values = $names;
             $across = 1;
         } else if (!is_numeric($across)) {
             $across = 1;
             $values = $names;
             $names = separateStringBy($bothnvp[0],",");
         }
         
         $countcols = count($names);
         if(count($values)>$countcols) $countcols = count($values);
         
         for ($i=0; $i<$countcols; $i++) {
            $n = NULL;
            $v = NULL;
            if(isset($names[$i])) $n = trim($names[$i]);
            if(isset($values[$i])) $v = trim($values[$i]);
            if ($n==NULL) $n = $v;
            else if ($v==NULL) $v = $n;
            $rowAns = " ";
            for($j=0;$j<count($answers);$j++){
               if (0==strcmp($v,$answers[$j]) || 0==strcmp($n,$answers[$j])) {
                  $rowAns = "Yes";
                  if(strlen($simplestr)>0) $simplestr .= ", ";
                  $simplestr .= $n;
                  break;
               }
            }
            $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($label.": ".$n))."\",";
            $header_file_m .= "\"".csvEncodeDoubleQuotes(strip_tags($map.": ".$n))."\",";
            $header_file_q .= "\"".csvEncodeDoubleQuotes(strip_tags($field_id.": ".$n))."\",";
            $content_file .= "\"".$rowAns."\",";
         }
         
         $result = array();
         $result['header_file_m'] = $header_file_m;
         $result['header_file_q'] = $header_file_q;
         $result['header_file'] = $header_file;
         $result['content_file'] = $content_file;
         $result['simple_string'] = $simplestr;
         return $result;
      }
      
      function getCSVRow($wd_id,$wd_row_id=NULL,$q=NULL,$answerText=NULL,$hdr_postfix="",$printstuff=FALSE){
         $printstuff = FALSE;
         
         $header_file_q = "";
         $header_file_m = "";
         $header_file = "";
         $content_file = "";
         $simplestr = "";

         if($q['hide']!=1) {
            if($answerText==NULL && $wd_row_id!=NULL){
               $answered = $this->getAnswer($wd_id,$wd_row_id, $q['field_id']);
               $answerText = $answered['answer'];
               if($printstuff) print "\n<br>answer for: ".$q['field_id']."\n<br>";
               if($printstuff) print_r($answered);
               if($printstuff) print "\n<br>";
            }
            //$answerText = convertBack($answerText);
            if($printstuff) print "\n<br>answer text: ".$answerText."\n<br>\n<br>";
   
   
            if (strcmp($q['field_type'],"MBL_MC")==0 || strcmp($q['field_type'],"MBL_IMG")==0 || strcmp($q['field_type'],"MBL_UPL")==0 || strcmp($q['field_type'],"SNGLCHKBX")==0 || strcmp($q['field_type'],"DATE")==0 || strcmp($q['field_type'],"AGE")==0 || strcmp($q['field_type'],"DATETIME")==0 || strcmp($q['field_type'],"RADIO")==0 || strcmp($q['field_type'],"POLLRADIO")==0 || strcmp($q['field_type'],"VOTE")==0 || strcmp($q['field_type'],"DROPDOWN")==0 || strcmp($q['field_type'],"STATE")==0 || strcmp($q['field_type'],"SITELIST")==0 || strcmp($q['field_type'],"SITEOPT")==0 || strcmp($q['field_type'],"TEXT")==0 || strcmp($q['field_type'],"INT")==0 || strcmp($q['field_type'],"DEC")==0 || strcmp($q['field_type'],"MONEY")==0 || strcmp($q['field_type'],"TEXTAREA")==0 || strcmp($q['field_type'],"HTML")==0) {
               if(0==strcmp(strtolower(trim($q['label'])),"status") && strcmp($q['field_type'],"DROPDOWN")==0){
                  $questionList = trim(convertBack($q['question']));
                  $bothnvp = separateStringBy($questionList,";");
                  $names = separateStringBy($bothnvp[0],",");
                  $values = separateStringBy($bothnvp[1],",");
                  $lookfor = strtolower(trim($answerText));
                  $found = "";
                  if($answerText!=NULL) {
                     for($i=0;$i<count($values);$i++){
                        if (0==strcmp($lookfor,strtolower(trim($values[$i])))) {
                           $found = $names[$i];
                           break;
                        }
                     }
                  }
                  $found = str_replace("%%%EMPTY%%%","",str_replace("%E%","",$found));
                  $content_file .= "\"".csvEncodeDoubleQuotes($found)."\",";
                  $simplestr = $found;
               } else {
                  $answerText = str_replace("%%%EMPTY%%%","",str_replace("%E%","",$answerText));
                  //$content_file .= "\"".csvEncodeDoubleQuotes(convertString($answerText))."\",";
                  $content_file .= "\"".csvEncodeDoubleQuotes($answerText)."\",";
                  //$content_file .= "\"".csvEncodeDoubleQuotes($answerText)."\",";
                  $simplestr = $answerText;
               }
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               $header_file_m .= "\"".csvEncodeDoubleQuotes($q['map']).$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               
            } elseif (strcmp($q['field_type'],"CHECKBOX")==0 || strcmp($q['field_type'],"HRZCHKBX")==0 || strcmp($q['field_type'],"NEWCHKBX")==0) {
               $temp = $this->convertCheckboxCSV($q['question'],$answerText,$q['label'],$q['field_id'],$q['map']);
               $header_file_m .= $temp['header_file_m'];
               $header_file_q .= $temp['header_file_q'];
               $header_file .= $temp['header_file'];
               $content_file .= $temp['content_file'];
               $simplestr .= $temp['simple_string'];
               
               // This saves this data in a different format - means that the data shows up twice in your csv file :/
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               $content_file .= "\"".csvEncodeDoubleQuotes(str_replace("%%%EMPTY%%%,","",str_replace("%E%,","",convertBack($answerText))))."\",";
               $header_file_m .= "\"".csvEncodeDoubleQuotes($q['map']).$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               
            } elseif (strcmp($q['field_type'],"USERS")==0 || strcmp($q['field_type'],"USERSRCH")==0 || strcmp($q['field_type'],"USERAUTO")==0) {
               // Users we create 2 header fields in case a row actually has some data to display to make it a bit easier to read
               $header_file_m .= "\"".csvEncodeDoubleQuotes(strip_tags($q['map'])).$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               
               $ua = new UserAcct();
               $userid = $answerText;
               $content_file .= "\"".csvEncodeDoubleQuotes($userid)."\",";
               
               $header_file_m .= "\"".csvEncodeDoubleQuotes(strip_tags($q['map'])).$hdr_postfix." user info\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix." user info\",";
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix." user info\",";
               
               if ($userid!=NULL && is_numeric($userid)) {
                  $user = $ua->getUser($userid);
                  $content_file .= "\"".csvEncodeDoubleQuotes($user['email'])." - ".csvEncodeDoubleQuotes($user['company'])."\",";
                  $simplestr .= $user['email'];
               } else {
                  $content_file .= "\"\",";
                  $simplestr .= $userid;
               }
            } elseif (strcmp($q['field_type'],"FOREIGNSRY")==0 || strcmp($q['field_type'],"FOREIGNSCT")==0 || strcmp($q['field_type'],"FOREIGNHYB")==0) {
               $temp = separateStringBy(trim(convertBack($q['question'])),";",NULL,TRUE);
               $f_wd = $this->getWebdata($temp[0]);
               //$hdrs = $this->getHeaderFields($f_wd['wd_id']);
               $hdrs = $this->getAllFieldsSystem($f_wd['wd_id']);
               
               if($printstuff) {
                  print "\n<br>";
                  print $q['field_type']." Question ".$q['field_id']." wd: ".$f_wd['wd_id']." Headers:";
                  print "\n<br>";
                  print_r($hdrs);               
                  print "\n<br>";
               }
               
               $total_cols = 10;
               $lables = array();
               if(isset($temp[1]) && $temp[1]!=NULL && is_numeric($temp[1])) {
                  $total_cols=$temp[1];
               } else if(isset($temp[1]) && $temp[1]!=NULL && strpos($temp[1],",")!==FALSE) {
                  $labels=separateStringBy($temp[1],",");
                  $total_cols = count($labels);
               }
               
               if($printstuff) {
                  print "\n<br>";
                  print "Total columns here: ".$total_cols;
                  print "\n<br>";
               }
               
               $ans = $this->getForeignSurveyAnswers($f_wd['wd_id'],$wd_id,$q['field_id'],$wd_row_id);
               
               if($printstuff) {
                  print "\n<br>";
                  print "getForeignSurveyAnsers(".$f_wd['wd_id'].",".$wd_id.",".$q['field_id'].",".$wd_row_id.") Answers:";
                  print "\n<br>";
                  print_r($ans);               
                  print "\n<br>";
               }
               
               for($i=0;$i<$total_cols;$i++){
                  if(!isset($ans[$i])) $ans[$i] = array();
                  if(!isset($ans[$i]['wd_row_id'])) $ans[$i]['wd_row_id'] = NULL;
                  for($j=0;$j<count($hdrs);$j++){
                     $pf = "_".$i;
                     if(isset($labels[$i]) && $labels[$i]!=NULL) $pf = "_".$labels[$i];
                     $f_csvrow = $this->getCSVRow($f_wd['wd_id'],$ans[$i]['wd_row_id'],$hdrs[$j],NULL,$pf);
                     $header_file_m .= $f_csvrow['mheader'];
                     $header_file_q .= $f_csvrow['qheader'];
                     $header_file .= $f_csvrow['header'];
                     $content_file .= $f_csvrow['content'];
                  }
               }
               
            } elseif (strcmp($q['field_type'],"FOREIGNTBL")==0 || strcmp($q['field_type'],"FOREIGNTDD")==0) {
               $survey_info = separateStringBy(convertBack($q['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  if($survey_info[2]==NULL) $survey_info[2] = $survey_info[1];
                  $query = "SELECT * FROM ".$survey_info[0].";";
                  $dbi = new MYSQLAccess();
                  $tempresults = $dbi->queryGetResults($query);
                  $answers = separateStringBy($answerText,",",NULL,TRUE);
                  for($i=0;$i<count($answers);$i++) {
                     for($j=0;$j<count($tempresults);$j++) {
                        if(0==strcmp(trim($answers[$i]),$tempresults[$j][$survey_info[2]])) {
                           if(strlen($simplestr)>0) $simplestr .= ", ";
                           $simplestr .= $tempresults[$j][$survey_info[1]];
                           break;
                        }
                     }
                  }
               }
               $header_file_m .= "\"".csvEncodeDoubleQuotes($q['map']).$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               $content_file .= "\"".csvEncodeDoubleQuotes($simplestr)."\",";
            } elseif (strcmp($q['field_type'],"FOREIGN")==0 || strcmp($q['field_type'],"FOREIGNCB")==0) {
               $survey_info = separateStringBy(convertBack($q['question']),",");
               $fld_subs = $this->getSurveyRowsIndexed($survey_info[0],$survey_info[1],TRUE);
               $temp = $this->convertCheckboxCSV($fld_subs,$answerText,$q['label'],$q['field_id'],$q['map']);
               $header_file_m .= $temp['header_file_m'];
               $header_file_q .= $temp['header_file_q'];
               $header_file .= $temp['header_file'];
               $content_file .= $temp['content_file'];
   
               // This saves this data in a different format - means that the data shows up twice in your csv file :/
               $header_file_m .= "\"".$q['map'].$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               $content_file .= "\"".csvEncodeDoubleQuotes($this->convertForeignWD($q['question'],$answerText))."\",";
               
            } elseif (strcmp($q['field_type'],"FOREIGN")==0) {
               $header_file_m .= "\"".$q['map'].$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].$hdr_postfix."\",";
               $header_file .= "\"".csvEncodeDoubleQuotes(strip_tags($q['label'])).$hdr_postfix."\",";
               $opts = "";
               $survey_info = separateStringBy(convertBack($question['question']),",");
               if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                  $line = $this->getDataResult($survey_info[0],$answerText);
                  $content_file .= "\"".csvEncodeDoubleQuotes($line[str_replace(" ", "",strtolower($survey_info[1]))])."\",";
               } else {
                  $content_file .= "\"".csvEncodeDoubleQuotes($answerText)."\",";
               }
            } elseif (strcmp($q['field_type'],"MANY")==0) {
            } elseif (strcmp($q['field_type'],"TABLE")==0) {
               //print "&nbsp;&nbsp; table<BR>";
               //$questionText = convertBack($q['label']);
               $questionText = convertBack($q['label']);
               $temp = separateStringBy(" ".$questionText,";");
               $headers = separateStringBy(" ".$temp[0],",");
               $rows = separateStringBy(" ".$temp[1],",");
               $answers = separateStringBy(" ".$answerText,",");
   
               $header_file_m .= "\"".$q['map'].":0:0:".$hdr_postfix."\",";
               $header_file_q .= "\"".$q['field_id'].":0:0:".$hdr_postfix."\",";
               $header_file .= "\"".csvEncodeDoubleQuotes($headers[0].":".$rows[0]).$hdr_postfix."\",";
               $content_file .= "\" \",";
               
               for ($m=1; $m<count($headers); $m++) {
                  for ($n=0; $n<count($rows); $n++) {
                     $header_file_m .= "\"".$q['map'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file_q .= "\"".$q['field_id'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file .= "\"".csvEncodeDoubleQuotes($headers[$m].":".$rows[$n]).$hdr_postfix."\",";
                     $answerIndex = $n*(count($headers)-1) + ($m-1);
                     $a = "";
                     if(isset($answers[$answerIndex])) $a = csvEncodeDoubleQuotes($answers[$answerIndex]);
                     $content_file .= "\"".$a."\",";
                  }
               }
            } elseif (strcmp($q['field_type'],"PERCENT")==0) {
              $questionText = convertBack($q['label']);
              $optionList = "Points,".convertBack($q['question']);
              $headers = separateStringBy($optionList,",");
              $rows = separateStringBy($questionText,",");
              $answers = separateStringBy(" ".$answerText,",");
               $acount = 0;
               for ($n=0; $n<count($rows); $n++) {
                  for ($m=0; $m<count($headers); $m++) {
                     $header_file_m .= "\"".$q['map'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file_q .= "\"".$q['field_id'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file .= "\"".csvEncodeDoubleQuotes($headers[$m].":".$rows[$n])."\",";
                     $a = "";
                     if(isset($answers[$acount])) $a = csvEncodeDoubleQuotes($answers[$acount]);
                     $content_file .= "\"".$a."\",";
                     $acount++;
                  }
               }
            } elseif (strcmp($q['field_type'],"NEWPRCNT")==0) {
              $questionText = convertBack($q['question']);
              //$optionList = "Points,".convertBack($q['label']);
              $optionList = "Points,Explanation";
              $headers = separateStringBy($optionList,",");
              $rows = separateStringBy($questionText,",");
              $answers = separateStringBy(" ".$answerText,",");
               $acount = 0;
               for ($n=0; $n<count($rows); $n++) {
                  for ($m=0; $m<count($headers); $m++) {
                     $header_file_m .= "\"".$q['map'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file_q .= "\"".$q['field_id'].":".$m.":".$n.":".$hdr_postfix."\",";
                     $header_file .= "\"".csvEncodeDoubleQuotes($headers[$m].":".$rows[$n]).$hdr_postfix."\",";
                     $content_file .= "\"".csvEncodeDoubleQuotes($answers[$acount])."\",";
                     $acount++;
                  }
               }
            } elseif (strcmp($q['field_type'],"LIKERT")==0) {
               //print "&nbsp;&nbsp; table<BR>";
               $questionText = convertBack($q['label']);
               $temp = separateStringBy(" ".$questionText,",");
               $answers = separateStringBy(" ".$answerText,",");
   
               for ($m=0; $m<count($temp); $m++) {
                     $header_file_m .= "\"".$q['map'].":".$m.":".$hdr_postfix."\",";
                     $header_file_q .= "\"".$q['field_id'].":".$m.":".$hdr_postfix."\",";
                     $header_file .= "\"".csvEncodeDoubleQuotes($temp[$m])."\",";
                     $content_file .= "\"".csvEncodeDoubleQuotes($answers[$m])."\",";
               }
            } elseif (strcmp($q['field_type'],"NEWLIKERT")==0) {
               //print "&nbsp;&nbsp; table<BR>";
               $questionText = convertBack($q['question']);
               $temp = separateStringBy(" ".$questionText,",");
               $answers = separateStringBy(" ".$answerText,",");
   
               for ($m=0; $m<count($temp); $m++) {
                     $header_file_m .= "\"".$q['map'].":".$m.":".$hdr_postfix."\",";
                     $header_file_q .= "\"".$q['field_id'].":".$m.":".$hdr_postfix."\",";
                     $header_file .= "\"".csvEncodeDoubleQuotes($temp[$m])."\",";
                     $content_file .= "\"".csvEncodeDoubleQuotes($answers[$m])."\",";
               }
            }
         }

         $csvRow['mheader'] = $header_file_m;
         $csvRow['qheader'] = $header_file_q;
         $csvRow['header'] = $header_file;
         $csvRow['content'] = $content_file;
         $csvRow['simple'] = $simplestr;
         return $csvRow;
      }

      //TODO:
      function getOutputCSV($wd_id) {
         $results1 = $this->getRows($wd_id);
         $results = $results1['results'];
         
         $header_file = "";
         $content_file = "";
      
         for ($i=0; $i<count($results); $i++) {
            //print "respondant ".$i."<BR>";
            foreach ($results[$i] as $key => $value) {
               if ($i==0) $header_file .= "\"".$key."\",";
               $content_file .= "\"".convertBack($value)."\",";
            }
      
            $fields = $this->getAllFieldsSystem($wd_id);
            for ($k=0; $k<count($fields); $k++) {
               $q = $fields[$k];
               $qCSV = $this->getCSVRow($wd_id,$results[$i]['wd_row_id'], $q);
               if ($i==0) $header_file .= $qCSV['header'];
               $content_file .= $qCSV['content'];
            } //END QUESITONS LOOP
            $content_file .= "\n";
         } 
      
         $entire_file = $header_file."\n".$content_file;
         return $entire_file;
      }

      function getOutputCSVOptions($wd_id,$qids) {
         $ua = new UserAcct();
         $webdata = $this->getWebData($wd_id);
         $adminusers = NULL;
         if ($webdata['privatesrvy']==1) {
            $rows = $this->getRowsSurveyOrgAdmin($wd_id, null, null, $filterStr,FALSE,TRUE);
            $adminusers = NULL;
            $tempusers = $ua->getAdminUsers();
            for ($i=0; $i<count($tempusers); $i++) $adminusers[$tempusers[$i]['userid']]=$tempusers[$i]['email'];
         } else {
            $rows = $this->getRows($wd_id);
         }
         $results = $rows['results'];
         
         $header_file1 = "";
         $header_file2 = "";
         $header_file3 = "";
         $content_file = "";

         $questions = $this->getAllFieldsSystem($wd_id);
         for ($i=0; $i<count($results); $i++) {
            if ($webdata['privatesrvy']==1) {
               $company = $ua->getFullUserInfo($results[$i]['orgid']);
               $adminids = $this->getUsersRelated($webdata,$results[$i]['orgid']);
               $adminuser = NULL;
               if ($adminids!==NULL && count($adminids)>0) {
                  $adminuser = $ua->getFullUserInfo($adminids[0]['reluserid']);
               }
                  if ($i==0) {
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"name\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"contact name\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"contact email\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"contact phone\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"address1\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"address2\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"city\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"state\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"zip\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"country\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"phonenumber\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"fax\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"website\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"priority/years\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"category\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"survey category\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"admin responsible\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"survey notes\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"security code\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"receive email\",";
                     $header_file3 .= "\"\",";

                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"CPIA list\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"APR member\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Responded to PC Recycling Survey\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Primary Org Type\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Secondary Plastic Handling\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Sources PC from\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Total Reclamation Capacity in NA\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"NA Onsite Reclamation Processing Capabilities\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Products Produced\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Last Responded for Survey Year\",";
                     $header_file3 .= "\"\",";
                     $header_file1 .= "\"company\",";
                     $header_file2 .= "\"Categories Last Responded\",";
                     $header_file3 .= "\"\",";

                     $header_file1 .= "\"record\",";
                     $header_file2 .= "\"status\",";
                     $header_file3 .= "\"\",";
                  }
                  $content_file .= "\"".convertBack($company['company'])."\",";               
                  $content_file .= "\"".convertBack($adminuser['fname'])." ".convertBack($adminuser['lname'])."\",";               
                  $content_file .= "\"".convertBack($adminuser['email'])."\",";               
                  $content_file .= "\"".convertBack($adminuser['phonenum'])."\",";               
                  $content_file .= "\"".convertBack($company['addr1'])."\",";               
                  $content_file .= "\"".convertBack($company['addr2'])."\",";
                  $content_file .= "\"".convertBack($company['city'])."\",";
                  $content_file .= "\"".convertBack($company['state'])."\",";               
                  $content_file .= "\"".convertBack($company['zip'])."\",";               
                  $content_file .= "\"".convertBack($company['country'])."\",";               
                  $content_file .= "\"".convertBack($company['phonenum'])."\",";               
                  $content_file .= "\"".convertBack($company['fax'])."\",";
                  $content_file .= "\"".convertBack($company['website'])."\",";
                  $content_file .= "\"".$company['survey priority (years)']."\",";
                  $content_file .= "\"".$company['company type']."\",";
                  $content_file .= "\"".$company['report category']."\",";
                  $content_file .= "\"".$adminusers[$company['admin responsible']]."\",";
                  $content_file .= "\"".convertBack($results[$i]['comments'])."\",";               
                  $content_file .= "\"".$results[$i]['origemail']."\",";               
                  $content_file .= "\"".$company['send survey email correspondence']."\",";

                  $content_file .= "\"".convertBack($company['q38'])."\",";               
                  $content_file .= "\"".convertBack($company['q4'])."\",";               
                  $content_file .= "\"".convertBack($company['q42'])."\",";               
                  $content_file .= "\"".convertBack($company['q47'])."\",";               
                  $content_file .= "\"".convertBack($company['q62'])."\",";               
                  $content_file .= "\"".convertBack($company['q63'])."\",";               
                  $content_file .= "\"".convertBack($company['q44'])."\",";               
                  $content_file .= "\"".convertBack($company['q45'])."\",";               
                  $content_file .= "\"".convertBack($company['q46'])."\",";               
                  $content_file .= "\"".convertBack($company['q93'])."\",";               
                  $content_file .= "\"".convertBack($company['q40'])."\",";               

                  if (0==strcmp($results[$i]['complete'],"Y")) $content_file .= "\"Open\",";               
                  else if (0==strcmp($results[$i]['complete'],"L")) $content_file .= "\"Closed\",";               
                  else if (0==strcmp($results[$i]['complete'],"N")) $content_file .= "\"New\",";               
                  else if (0==strcmp($results[$i]['complete'],"X")) $content_file .= "\"Special\",";               
                  else if (0==strcmp($results[$i]['complete'],"A")) $content_file .= "\"Attention\",";               
                  else $content_file .= "\"N/A\",";               
            }

            foreach ($results[$i] as $key => $value) {
               if (in_array($key,$qids) && 0!=strcmp("q",substr($key,0,1))) {
                  if ($i==0) {
                     $header_file1 .= "\"\",";
                     $header_file2 .= "\"".$key."\",";
                     $header_file3 .= "\"\",";
                  }
                  $content_file .= "\"".convertBack($value)."\",";
               }
            }
      
            for ($k=0; $k<count($questions); $k++) {
               $q = $questions[$k];
               if (in_array($q['field_id'],$qids)) {
                  $answered = $this->getAnswer($wd_id,$results[$i]['wd_row_id'], $q['field_id']);
                  $section_obj = $this->getSectionForField($q['wd_id'],$q['field_id']);
                  $sectionname = strip_tags($section_obj['label']);
                  if (strcmp($q['field_type'],"CHECKBOX")==0 || strcmp($q['field_type'],"HRZCHKBX")==0 || strcmp($q['field_type'],"NEWCHKBX")==0 || strcmp($q['field_type'],"MBL_MC")==0 || strcmp($q['field_type'],"MBL_IMG")==0 || strcmp($q['field_type'],"SNGLCHKBX")==0 || strcmp($q['field_type'],"DATE")==0 || strcmp($q['field_type'],"AGE")==0 || strcmp($q['field_type'],"DATETIME")==0 || strcmp($q['field_type'],"RADIO")==0 || strcmp($q['field_type'],"POLLRADIO")==0 || strcmp($q['field_type'],"VOTE")==0 || strcmp($q['field_type'],"DROPDOWN")==0 || strcmp($q['field_type'],"STATE")==0 || strcmp($q['field_type'],"USERS")==0 || strcmp($q['field_type'],"SITELIST")==0 || strcmp($q['field_type'],"SITEOPT")==0 || strcmp($q['field_type'],"TEXT")==0 || strcmp($q['field_type'],"INT")==0 || strcmp($q['field_type'],"DEC")==0 || strcmp($q['field_type'],"MONEY")==0 || strcmp($q['field_type'],"TEXTAREA")==0 || strcmp($q['field_type'],"HTML")==0 || strcmp($q['field_type'],"FOREIGNCB")==0 || strcmp($q['field_type'],"FOREIGNTBL")==0) {
                     if ($i==0) {
                        $header_file1 .= "\"".$sectionname."\",";
                        $header_file2 .= "\"".$q['label']."\",";
                        $header_file3 .= "\"".$q['field_id']."\",";
                     }
                     $content_file .= "\"".csvEncodeDoubleQuotes(convertBack($answered['answer']))."\",";
                  } else if (strcmp($q['field_type'],"FOREIGN")==0) {
                     if ($i==0) {
                        $header_file1 .= "\"".$sectionname."\",";
                        $header_file2 .= "\"".$q['label']."\",";
                        $header_file3 .= "\"".$q['field_id']."\",";
                     }
                     $opts = "";
                     $survey_info = separateStringBy(convertBack($q['question']),",");
                     if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
                        $line = $this->getDataResult($survey_info[0],$answered['answer']);
                        $content_file .= "\"".csvEncodeDoubleQuotes($line[str_replace(" ", "",strtolower($survey_info[1]))])."\",";
                     } else {
                        $content_file .= "\"".csvEncodeDoubleQuotes(convertBack($answered['answer']))."\",";
                     }
                  } else if (strcmp($q['field_type'],"MANY")==0) {

                  } else if (strcmp($q['field_type'],"LIKERT")==0) {
                     //print "&nbsp;&nbsp; table<BR>";
                     $questionText = $q['label'];
                     $answerText = $answered['answer'];
                     $temp = separateStringBy(" ".$questionText,",");
                     $answers = separateStringBy(" ".$answerText,",");
       
                     for ($m=0; $m<count($temp); $m++) {
                        if ($i==0) {
                           $header_file1 .= "\"".$sectionname."\",";
                           $header_file2 .= "\"".$temp[$m]."\",";
                           $header_file3 .= "\"".$q['field_id']."\",";
                        }
                        $content_file .= "\"".$answers[$m]."\",";
                     }
                  } else if (strcmp($q['field_type'],"NEWLIKERT")==0) {
                     //print "&nbsp;&nbsp; table<BR>";
                     $questionText = convertBack($q['question']);
                     $answerText = $answered['answer'];
                     $temp = separateStringBy(" ".$questionText,",");
                     $answers = separateStringBy(" ".$answerText,",");
       
                     for ($m=0; $m<count($temp); $m++) {
                        if ($i==0) {
                           $header_file1 .= "\"".$sectionname."\",";
                           $header_file2 .= "\"".$temp[$m]."\",";
                           $header_file3 .= "\"".$q['field_id']."\",";
                        }
                        $content_file .= "\"".$answers[$m]."\",";
                     }
                  } else if (strcmp($q['field_type'],"TABLE")==0) {
                     //print "&nbsp;&nbsp; table<BR>";
                     $questionText = convertBack($q['label']);
                     $answerText = $answered['answer'];
                     $temp = separateStringBy(" ".$questionText,";");
                     $headers = separateStringBy(" ".$temp[0],",");
                     $rows = separateStringBy(" ".$temp[1],",");
                     $answers = separateStringBy(" ".$answerText,",");
                     for ($m=1; $m<count($headers); $m++) {
                        for ($n=0; $n<count($rows); $n++) {
                           if ($i==0) {
                              $header_file1 .= "\"".$sectionname."\",";
                              $header_file2 .= "\"".$headers[$m].":".$rows[$n]."\",";
                              $header_file3 .= "\"".$q['field_id']."\",";
                           }
                           $answerIndex = $n*(count($headers)-1) + ($m-1);
                           $content_file .= "\"".convertBack($answers[$answerIndex])."\",";
                        }
                     }
                  } elseif (strcmp($q['field_type'],"PERCENT")==0) {
                    $questionText = $q['label'];
                    $optionList = "Points,".convertBack($q['question']);
                    $answerText = $answered['answer'];
                    $headers = separateStringBy($optionList,",");
                    $rows = separateStringBy($questionText,",");
                    $answers = separateStringBy(" ".$answerText,",");
                     $acount = 0;
                     for ($n=0; $n<count($rows); $n++) {
                        for ($m=0; $m<count($headers); $m++) {
                           if ($i==0) {
                              $header_file1 .= "\"".$sectionname."\",";
                              $header_file2 .= "\"".$headers[$m].":".$rows[$n]."\",";
                              $header_file3 .= "\"".$q['field_id']."\",";
                           }
                           $content_file .= "\"".convertBack($answers[$acount])."\",";
                           $acount++;
                        }
                     }
                  } elseif (strcmp($q['field_type'],"NEWPRCNT")==0) {
                     $questionText = convertBack($q['question']);
                     //$optionList = "Points,".convertBack($q['label']);
                     $optionList = "Points,Explanation";
                    $answerText = $answered['answer'];
                    $headers = separateStringBy($optionList,",");
                    $rows = separateStringBy($questionText,",");
                    $answers = separateStringBy(" ".$answerText,",");
                     $acount = 0;
                     for ($n=0; $n<count($rows); $n++) {
                        for ($m=0; $m<count($headers); $m++) {
                           if ($i==0) {
                              $header_file1 .= "\"".$sectionname."\",";
                              $header_file2 .= "\"".$headers[$m].":".$rows[$n]."\",";
                              $header_file3 .= "\"".$q['field_id']."\",";
                           }
                           $content_file .= "\"".convertBack($answers[$acount])."\",";
                           $acount++;
                        }
                     }
                  }
               } //END if in_array
            } //END QUESITONS LOOP
            $content_file .= "\n";
         } 
         $entire_file = $header_file1."\n".$header_file2."\n".$header_file3."\n".$content_file;
         return $entire_file;
      }

      // This is a saved report (search params) which comes out of a webdata table under the
      // staticly formatted schema as described:
      // name: text - Give this report a name for the graph
      // parameters: text - URL parameters to filter the results
      // wd_id: integer - table (survey) with results
      // field_id: text - list of graphs to generate (individual question ids)
      // frequency: dropdown - None, weekly, monthly, quarterly if you want to automate a report
      // enabled: dropdown - whether or not to allow automated reporting
      // sequence: integer - for displaying reports in order
      function displaySavedStatsList($userid,$userid2=NULL,$wd_id_only=NULL){
         $name = "User Objects Saved Stats";
         $wdata = $this->getWebDataByName($name);

         $rows = NULL;
         $userid2 = $userid.",".$userid2;
         
         $userid2array = separateStringBy($userid2,",",NULL,TRUE);
         if ($userid2array!=NULL && count($userid2array)>0){
             for ($i=0;$i<count($userid2array);$i++) {
                $rows2 = $this->getDataByUserid($wdata['wd_id'], $userid2array[$i], NULL, FALSE, FALSE);
                if($rows2!=NULL && $rows!=NULL) $rows = array_merge($rows,$rows2);
                else if($rows2!=NULL) $rows = $rows2;
             }
         }
             
         $qs = $this->getFieldLabels($wdata['wd_id']);
         $sl_templates = array();
         $used = array();
         for ($i=0;$i<count($rows);$i++){
             if ($wd_id_only==NULL || $wd_id_only==$rows[$i][$qs['wd_id']]) {
                 if (!isset($used[$rows[$i]['wd_row_id']]) || $used[$rows[$i]['wd_row_id']]!=1){
                     $used[$rows[$i]['wd_row_id']] = 1;
                     $statsparams = $rows[$i][$qs['parameters']];
                     $statsparams .= "&wd_id=".$rows[$i][$qs['wd_id']];
                     if($rows[$i][$qs['field_id']] != NULL) $statsparams .= "&field_id=".$rows[$i][$qs['field_id']];
                     if (0==strcmp(strtolower($rows[$i][$qs['frequency']]),"weekly")) {
                         $since = time() - (7 * 24 * 60 * 60);
                         $statsparams .= "&cmsafter=".date("Y-m-d",$since);
                     } else if (0==strcmp(strtolower($rows[$i][$qs['frequency']]),"monthly")) {
                         $since = time() - (31 * 24 * 60 * 60);
                         $statsparams .= "&cmsafter=".date("Y-m-d",$since);
                     } else if (0==strcmp(strtolower($rows[$i][$qs['frequency']]),"quarterly")) {
                         $since = time() - (92 * 24 * 60 * 60);
                         $statsparams .= "&cmsafter=".date("Y-m-d",$since);
                     }
                     
                     $thisrow = array();
                     $thisrow['name'] = $rows[$i][$qs['name']];
                     $fulltitle = $rows[$i][$qs['name']]." (".$rows[$i][$qs['frequency']].")";
                     $thisrow['onclick'] = "jsfwd_title='".$fulltitle."';statsparams='".$statsparams."';showPage('surveystats');";
                     $sl_templates[] = $thisrow;
                  }
             }
         }
         return $sl_templates;
      }   
          
      function addSavedStat($userid,$wd_id,$field_id=NULL,$reportname=NULL,$params=NULL,$enabled='Yes',$sequence=10,$frequency="None"){
         $name = "User Objects Saved Stats";
         $wdata = $this->getWebDataByName($name);
         $qs = $this->getFieldLabels($wdata['wd_id']);
         $query = "INSERT INTO wd_".$wdata['wd_id']." (userid,created,lastupdate,";
         $query .= $qs['wd_id'].",".$qs['field_id'].",".$qs['name'].",";
         $query .= $qs['parameters'].",".$qs['enabled'].",".$qs['sequence'].",";
         $query .= $qs['frequency'].") VALUES (";
         $query .= "'".$userid."',NOW(),NOW(),";
         $query .= "'".$wd_id."',";
         if (is_array($field_id)) $field_id=implode(",",$field_id);
         $query .= "'".convertString($field_id)."',";
         $query .= "'".convertString($reportname)."',";
         $query .= "'".convertString($params)."',";
         $query .= "'".$enabled."',";
         $query .= "'".$sequence."',";
         $query .= "'".convertString($frequency)."');";
         $dbi = new MYSQLAccess();
         $id = $dbi->insertGetValue($query);
         return $id;
      }   
          
      // Prepare stats for display to compare answers
      function getStats($wd_id,$filterStr=NULL,$fldid=NULL){
         $totals = array();
         if ($fldid!=NULL && !is_array($fldid)) $fldid = separateStringBy($fldid,",","",TRUE);
         
         // Set up field values (only ones that we will graph)
         $fields = $this->getAllFieldsSystem($wd_id);
         for ($j=0; $j<count($fields); $j++) {
            //if ($fldid==NULL || 0==strcmp($fldid,$fields[$j]['field_id'])) {
            if ($fldid==NULL || count($fldid)<1 || in_array($fields[$j]['field_id'],$fldid)) {
               $type = $fields[$j]['field_type'];
               if ( 0==strcmp($type,"CHECKBOX") || 0==strcmp($type,"HRZCHKBX") || 
                    0==strcmp($type,"NEWCHKBX") || 0==strcmp($type,"RADIO") || 
                    0==strcmp($type,"POLLRADIO") || 0==strcmp($type,"MBL_MC") || 0==strcmp($type,"MBL_IMG") ||
                    0==strcmp($type,"DROPDOWN") || 0==strcmp($type,"VOTE") ) {
                  $totals[$fields[$j]['field_id']]['totalnumberanswered'] = 0;
                  $max = 0;

                  $bothnvp = separateStringBy(trim(convertBack($fields[$j]['question'])),";");
                  $names = NULL;
                  $values = NULL;
                  if (!is_numeric($bothnvp[0])) {
                     $names = separateStringBy($bothnvp[0],",");
                     $values = separateStringBy($bothnvp[1],",");
                  } else {
                     $names = separateStringBy($bothnvp[1],",");
                     $values = separateStringBy($bothnvp[2],",");
                  }
               
                  if ($values==NULL) $values = $names;
               
                  for ($k=0; $k<count($values); $k++) {
                     $val = trim($values[$k]);
                     if ($val!=NULL) {
                        $totals[$fields[$j]['field_id']][$val] = 0;
                     }
                     $info[$fields[$j]['field_id']] = $fields[$j];
                  }
               } else if (0==strcmp($type,"SNGLCHKBX")) {
               } else if (0==strcmp($type,"NEWLIKERT")) {
               }
            }
         }

         // Now go through all the data results and tally
         $rowsObj = $this->getRows($wd_id, null, null, $filterStr,FALSE);
         $rows = $rowsObj['results'];
         for ($i=0; $i<count($rows); $i++) {
            // Only tally relevant fields
            foreach($totals as $key => $value) {
               // Tally each answer (if there are multiple)
               $ticks = separateStringBy(convertBack($rows[$i][$key]),",");
               for ($j=0; $j<count($ticks); $j++) {
                  $fldVal = trim($ticks[$j]);
                  if ($fldVal!=NULL) {
                     $totals[$key][$fldVal]++;
                     if ($totals[$key][$fldVal]>$max) $max = $totals[$key][$fldVal];
                     $totals[$key]['totalnumberanswered']++;
                  }
               }
            }
         }
         $results['totals'] = $totals;
         $results['info'] = $info;
         $results['max'] = $max;
         return $results;
      }
      
      function printSurveyGraph($wd_id,$gwidth=400,$filterstr=NULL,$field_id=NULL,$title=NULL){
         $str = "";
         if ($title!=NULL) $str = "<div style=\"font-size:18px;font-weight:bold;color:RED;font-family:verdana;padding:10px;\">".$title."</div>";
         $results = $this->getStats($wd_id,$filterstr,$field_id);
         $totals = $results['totals'];
         $info = $results['info'];
         foreach($totals as $key => $value) {
            $str .= "<div style=\"position:relative;padding:8px;border:1px solid #555555;border-radius:6px;margin:4px;\">";
            $twidth = $gwidth - 16 - 8 - 2;
            $str .= "<div style=\"position:relative;width:".$twidth."px;overflow:hidden;margin-top:5px;font-size:12px;font-weight:bold;font-family:arial;color:#000000;background-color:#FFFFFF;\">";
            //$str .= "(".$totals[$key]['totalnumberanswered'].") ";
            $str .= $info[$key]['label'];
            $str .= "</div>";
            foreach($value as $key1 => $value1) {
               if (0!=strcmp($key1,"totalnumberanswered")) {
                  $numpx = round(($value1/$results['max'])*$twidth);
                  $str .= "<div style=\"position:relative;width:".$twidth."px;height:20px;overflow:hidden;margin-top:2px;background-color:#FFFFFF;\">";                     
                  $str .= "<div style=\"position:absolute;left:0px;top:0px;height:20px;width:".$numpx."px;overflow:hidden;background-color:#AACCEE;\">";
                  $str .= "</div>";
                  $str .= "<div style=\"position:absolute;left:2px;top:2px;height:16px;font-size:12px;font-family:arial;color:#000000;\">";
                  $key1arr = separateStringBy($key1,"/");
                  $key2arr = separateStringBy($key1arr[(count($key1arr)-1)],".");
                  $key1 = $key2arr[0];
                  $str .= "(".round(100 * ($value1/$totals[$key]['totalnumberanswered']))."%) ".$key1;
                  $str .= "</div>";                     
                  $str .= "</div>";
               }
            }
            $str .= "</div>";
         }
         return $str;
      }
      
      function getTemplateXML_SearchIndex($name=NULL,$info=NULL) {
         if($name==NULL) $name = "New Search Index ".getDateForDB();
         if($info==NULL) $info = "This JData Table was created using the system template";
         $str = "<webdata><structure>";
         $str .= "<name>".$name."</name>";
         $str .= "<info>".$info."</info>";
         $str .= "<privatesrvy>10</privatesrvy>";
         $str .= "<saveresults>1</saveresults>";
         $str .= "<emailresults>2</emailresults>";
         $str .= "<htags>#searchindex </htags>";
         $str .= "<wd_section>";
         $str .= "<sequence>10</sequence>";
         $str .= "<wd_field>";
         $str .= "<field_id>q0></field_id>";
         $str .= "<sequence>10</sequence>";
         $str .= "<label>Keyword (preferred terminology)</label>";
         $str .= "<field_type>TEXT</field_type>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>word</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q1</field_id>";
         $str .= "<sequence>20</sequence>";
         $str .= "<label>Other Searchable Names (examples of product type)</label>";
         $str .= "<field_type>TEXTAREA</field_type>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>keywords</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q2</field_id>";
         $str .= "<sequence>30</sequence>";
         $str .= "<label>Search return message or URL</label>";
         $str .= "<field_type>TEXTAREA</field_type>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q3</field_id>";
         $str .= "<sequence>40</sequence>";
         $str .= "<label>Search return URL</label>";
         $str .= "<field_type>TEXT</field_type>";
         $str .= "<map>url</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q4</field_id>";
         $str .= "<sequence>50</sequence>";
         $str .= "<label>Use for auto-suggest</label>";
         $str .= "<field_type>SNGLCHKBX</field_type>";
         $str .= "<defaultval>YES</defaultval>";
         $str .= "<map>autosuggest</map>";
         $str .= "</wd_field>";
         $str .= "</wd_section>";
         $str .= "</structure></webdata>";
         return $str;  
      }
      
      function getTemplateXML_Glossary($name=NULL,$info=NULL) {
         if($name==NULL) $name = "New Glossary ".getDateForDB();
         if($info==NULL) $info = "This JData Table was created using the system template";
         $str = "<webdata><structure>";
         $str .= "<name>".$name."</name>";
         $str .= "<info>".$info."</info>";
         $str .= "<privatesrvy>11</privatesrvy>";
         $str .= "<saveresults>1</saveresults>";
         $str .= "<emailresults>2</emailresults>";
         $str .= "<htags>#glossary </htags>";
         $str .= "<wd_section>";
         $str .= "<sequence>10</sequence>";
         $str .= "<wd_field>";
         $str .= "<field_id>q0</field_id>";
         $str .= "<sequence>10</sequence>";
         $str .= "<label>Sequence</label>";
         $str .= "<field_type>INT</field_type>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>sequence</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q1</field_id>";
         $str .= "<sequence>20</sequence>";
         $str .= "<label>Enabled</label>";
         $str .= "<field_type>DROPDOWN</field_type>";
         $str .= "<question>Yes,No</question>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>enabled</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q2</field_id>";
         $str .= "<sequence>30</sequence>";
         $str .= "<label>Keyword (preferred terminology)</label>";
         $str .= "<field_type>TEXT</field_type>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>term</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q3</field_id>";
         $str .= "<sequence>40</sequence>";
         $str .= "<label>Other Variations (case sensitive)</label>";
         $str .= "<field_type>TEXTAREA</field_type>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "<map>alternates</map>";
         $str .= "</wd_field>";
         $str .= "<wd_field>";
         $str .= "<field_id>q4</field_id>";
         $str .= "<sequence>50</sequence>";
         $str .= "<label>Definition</label>";
         $str .= "<field_type>TEXTAREA</field_type>";
         $str .= "<map>definition</map>";
         $str .= "<header>1</header>";
         $str .= "<srchfld>1</srchfld>";
         $str .= "</wd_field>";
         $str .= "</wd_section>";
         $str .= "</structure></webdata>";
         return $str;  
      }

      function newWebDataFromXML($xml,$wd_id=NULL,$copy=FALSE){
         $data = XML_unserialize($xml);
         
         //print "<br><br>\n\n";
         //print "structure:<br>\n";
         //print_r($data);
         //print "<br><br><br>\n\n\n";
         
         $webdata = $data['webdata'];
         $structure = $webdata['structure'];

         //if no id is sent in, see if it's in the xml
         //if($wd_id==NULL) $wd_id = $structure['wd_id'];
         if($wd_id==NULL) {
            if($copy) {
               $structure['name'] .= "Copy";
               $structure['shortname'] .= "Copy";
               $structure['htags'] .= "#copy ";
            }
            $wd_id = $this->newWebData($structure['name'], $structure['info'], $structure['privatesrvy'], $structure['adminemail'],$structure['filename'], $structure['saveresults'], $structure['emailresults'], NULL, $structure['status'], $structure['externalid'], $structure['field1'], $structure['field2'], $structure['field3'], $structure['field4'], $structure['shortname'], $structure['password'], $structure['captcha']);
            if($structure['htags']!=NULL) $this->updateWebDataProperty($wd_id, "htags", $structure['htags']);
            if($structure['status']!=NULL) $this->setStatus($wd_id, $structure['status']);
         }
         
         $mappingarray = $this->newSectionFromXML($wd_id,$structure,-1);

         //wd_rel
         if(is_array($structure['fieldrel']) && array_key_exists(0,$structure['fieldrel'])) $fieldrels = $structure['fieldrel'];
         else if ($structure['fieldrel']!=NULL) $fieldrels[0] = $structure['fieldrel'];
         for ($i=0; $i<count($fieldrels); $i++) {
            $fieldrel = $fieldrels[$i];
            if(isset($mappingarray[$fieldrel['fid2']])) $fieldrel['fid2'] = $mappingarray[$fieldrel['fid2']];
            if ($fieldrel!=NULL) $this->newFieldRel($wd_id,$fieldrel['rel_type'],$fieldrel['fid1'],$fieldrel['fid2'],$fieldrel['f1value']);
         }

         $fldpos = $webdata['fieldpositions'];
         $positions = array();
         if(is_array($fldpos['fieldpos']) && array_key_exists(0,$fldpos['fieldpos'])) $positions = $fldpos['fieldpos'];
         else if ($fldpos['fieldpos']!=NULL) $positions[0] = $fldpos['fieldpos'];
         print "\n\n<!-- ***chj*** Field positions:".count($positions)." -->\n\n";
         $fldcounter = 0;
         $query = "INSERT INTO wd_fldpos (groupname,field_id,wd_id,leftpos,toppos,rightpos,bottompos,width,height,defval,notes,unit,disptype,params,json,disa,adminresp,statusind,instructions) VALUES ";
         for ($i=0; $i<count($positions); $i++) {
            $posrow = $positions[$i];
            
            if($posrow!=NULL && trim($posrow['field_id'])!=NULL) {
               print "<br><br>";
               print_r($posrow);
               print "<br><br>";
               
               $posrow['groupname'] = convertString(trim(convertBack($posrow['groupname'])));
               $posrow['field_id'] = convertString(trim(convertBack($posrow['field_id'])));
               $posrow['leftpos'] = convertString(trim(convertBack($posrow['leftpos'])));
               $posrow['toppos'] = convertString(trim(convertBack($posrow['toppos'])));
               $posrow['rightpos'] = convertString(trim(convertBack($posrow['rightpos'])));
               $posrow['bottompos'] = convertString(trim(convertBack($posrow['bottompos'])));
               $posrow['width'] = convertString(trim(convertBack($posrow['width'])));
               $posrow['height'] = convertString(trim(convertBack($posrow['height'])));
               $posrow['defval'] = convertString(trim(convertBack($posrow['defval'])));
               $posrow['notes'] = convertString(trim(convertBack($posrow['notes'])));
               $posrow['unit'] = convertString(trim(convertBack($posrow['unit'])));
               $posrow['disptype'] = convertString(trim(convertBack($posrow['disptype'])));
               $posrow['params'] = convertString(trim(convertBack($posrow['params'])));
               $posrow['json'] = convertString(trim(convertBack($posrow['json'])));
               $posrow['disa'] = convertString(trim(convertBack($posrow['disa'])));
               if($posrow['disa'] == NULL) $posrow['disa'] = 0;
               $posrow['adminresp'] = convertString(trim(convertBack($posrow['adminresp'])));
               $posrow['statusind'] = convertString(trim(convertBack($posrow['statusind'])));
               $posrow['instructions'] = convertString(trim(convertBack($posrow['instructions'])));
               
               if($fldcounter>0) $query .= ", ";
               $query .= "('".$posrow['groupname']."',";
               $query .= "'".$posrow['field_id']."',";
               $query .= "'".$wd_id."',";
               $query .= "'".$posrow['leftpos']."',";
               $query .= "'".$posrow['toppos']."',";
               $query .= "'".$posrow['rightpos']."',";
               $query .= "'".$posrow['bottompos']."',";
               $query .= "'".$posrow['width']."',";
               $query .= "'".$posrow['height']."',";
               $query .= "'".$posrow['defval']."',";
               $query .= "'".$posrow['notes']."',";
               $query .= "'".$posrow['unit']."',";
               $query .= "'".$posrow['disptype']."',";
               $query .= "'".$posrow['params']."',";
               $query .= "'".$posrow['json']."',";
               $query .= "'".$posrow['disa']."',";
               $query .= "'".$posrow['adminresp']."',";
               $query .= "'".$posrow['statusind']."',";
               $query .= "'".$posrow['instructions']."')";
               $fldcounter++;
            }
         }
         print "\n\n<!-- ***chj*** Field positions entries:\n".$query."\n-->\n\n";
         if($fldcounter>0) {
            $dbi = new MYSQLAccess();
            $dbi->insert($query);
         }
         
         $surveydata = $webdata['data'];
         if(is_array($surveydata['datarow']) && array_key_exists(0,$surveydata['datarow']))$datarows = $surveydata['datarow'];
         else if ($surveydata['datarow']!=NULL) $datarows[0] = $surveydata['datarow'];
         for ($i=0; $i<count($datarows); $i++) {
            $datarow = $datarows[$i];
            if ($datarow != null) {
               $wd_row_id = $this->addRow($wd_id,$datarow['userid']);
               $this->updateFieldValue($wd_id,$wd_row_id,"comments",$datarow['comments']);
               $this->updateFieldValue($wd_id,$wd_row_id,"externalid",$datarow['externalid']);
               $this->setReplyStatus($wd_id,$wd_row_id,$datarow['complete']);
               $questions = $this->getAllFieldsSystem($wd_id);
               for ($j=0; $j<count($questions); $j++) {
                  if ($datarow[$questions[$j]['field_id']] != null) {
                     $this->setAnswer($wd_id,$wd_row_id,$questions[$j]["field_id"],$datarow[$questions[$j]['field_id']]);
                  }
               }
            }
         }
         return $wd_id;
      }

      function newSectionFromXML($wd_id,$sectionStructure,$parent_s=-1,$mappingarray=NULL){
         if($mappingarray == NULL) $mappingarray = array();
         if ($sectionStructure != NULL) {
            $sections = array();
            if(is_array($sectionStructure['wd_section']) && array_key_exists(0,$sectionStructure['wd_section']))$sections = $sectionStructure['wd_section'];
            else if ($sectionStructure['wd_section']!=NULL) $sections[0] = $sectionStructure['wd_section'];
            for ($i=0; $i<count($sections); $i++) {
               $section = $sections[$i];
               if ($section != NULL) {
                  $section_id = $this->addSection($wd_id, $parent_s, $section['sec_type'], $section['label'], $section['sequence'], $section['dyna'], $section['question'], $section['param1'], $section['param2'], $section['param3'], $section['param4'], $section['param5'], $section['param6']);
                  $mappingarray[$section_id] = $section_id;
                  if($section['section']!=NULL) $mappingarray[$section['section']] = $section_id;
                  $mappingarray = $this->newSectionFromXML($wd_id,$section,$section_id,$mappingarray);   
               }
            }

            $questions = array();
            if(is_array($sectionStructure['wd_field']) && array_key_exists(0,$sectionStructure['wd_field']))$questions = $sectionStructure['wd_field'];
            else if ($sectionStructure['wd_field']!=NULL) $questions[0] = $sectionStructure['wd_field'];
            for ($j=0; $j<count($questions); $j++) {
               $question = $questions[$j];
               if ($question != NULL && $question['field_type'] != NULL) {
                  $field_id = $question['field_id'];
                  $this->addField($wd_id, $parent_s, $field_id, $question['label'], $question['question'], $question['field_type'], $question['sequence'], $question['privacy'], $question['header'], $question['defaultval'], $question['required'], $question['srchfld'],FALSE,$question['notes'], $question['filterfld'], $question['stylecss'], $question['map']);
               }
            }
         }
         return $mappingarray;
      }

      function getOutputXML($wd_id,$includeData=TRUE) {
         $xml = new JSFXMLWriter();
         $xml->startDocument('1.0');
         $xml->setIndent(3);
         $survey = $this->getWebData($wd_id);

         $xml->startElement("webdata");
         $xml->startElement("structure");
         foreach($survey as $key => $val) {
            if(trim($key)!=NULL && trim($val)!=NULL) $xml->writeElement($key,$val);
         }
         $this->getOutputXMLFromSection($wd_id,-1,$xml);

         //wd_rel
         $fieldrels = $this->getAllFieldRels($wd_id);
         for ($i=0; $i<count($fieldrels); $i++) {
            $xml->startElement("fieldrel");
            $fr = $fieldrels[$i];
            foreach($fr as $key => $val) $xml->writeElement($key,$val);
            $xml->endElement(); //end fieldrel element
         }

         $xml->endElement(); //end structure element
         
         $xml->startElement("fieldpositions");
         $fldpos = $this->getFieldPositions($wd_id,NULL,FALSE);
         for($i=0;$i<count($fldpos);$i++) {
            $xml->startElement("fieldpos");
            foreach ($fldpos[$i] as $key => $value) $xml->writeElement($key,$value);
            $xml->endElement(); 
         }
         $xml->endElement();
         
         if ($includeData) {
            $xml->startElement("data");
            $results1 = $this->getRows($wd_id);
            $results = $results1['results'];
            for ($i=0; $i<count($results); $i++) {
               $xml->startElement("datarow");
               foreach ($results[$i] as $key => $value) $xml->writeElement($key,$value);
               $xml->endElement(); //end datarow
            } 
            $xml->endElement();  //end data element
         }
         $xml->endElement();  //end webdata element
         $xml->endDocument();
         return $xml->getXml();
      }

      function getSectionOutputXMLForCopy($wd_id,$section) {
         $xml = new JSFXMLWriter();
         $xml->startDocument('1.0');
         $xml->setIndent(3);
         $survey = $this->getWebData($wd_id);

         $xml->startElement("webdata");
         $xml->startElement("structure");
         foreach($survey as $key => $val) {
            if(trim($key)!=NULL && trim($val)!=NULL) $xml->writeElement($key,$val);
         }
         $this->getOutputXMLFromSection($wd_id,$section,$xml);

         //wd_rel
         $fieldrels = $this->getAllFieldRels($wd_id);
         for ($i=0; $i<count($fieldrels); $i++) {
            $xml->startElement("fieldrel");
            $fr = $fieldrels[$i];
            foreach($fr as $key => $val) $xml->writeElement($key,$val);
            $xml->endElement(); //end fieldrel element
         }

         $xml->endElement(); //end structure element
         $xml->endElement();  //end webdata element
         $xml->endDocument();
         return $xml->getXml();
      }

      function getOutputXMLFromSection($wd_id,$section,&$xml){
         $sections = $this->getDataSections($wd_id,$section);
         for ($j=0; $j<count($sections); $j++) {
            $s = $sections[$j];
            $xml->startElement("wd_section");
            foreach($s as $key => $val) if(trim($val)!=NULL) $xml->writeElement($key,$val);
            $this->getOutputXMLFromSection($wd_id,$s['section'],$xml);
            $xml->endElement();
         }

         $questions = $this->getFields($wd_id, $section);
         for ($k=0; $k<count($questions); $k++) {
            $q = $questions[$k];
            $xml->startElement("wd_field");
            foreach($q as $key => $val) if(trim($val)!=NULL) $xml->writeElement($key,$val);
            $xml->endElement();
         }
      }

      function returnSectionsWithFilter($wd_id,$filter){
         $query = "SELECT * FROM wd_sections WHERE wd_id=".$wd_id." AND ( question like '%".$filter."%' OR  label like '%".$filter."%');";
         $dbi = new MYSQLAccess();
         $results = $dbi->queryGetResults($query);
         return $results;
      }

      function newWebDataFromXMLMigrate($xml){
         $data = XML_unserialize($xml);
         $webdata = $data['survey'];
         $structure = $webdata['structure'];
         //print "Name: ".$structure['name'].", info: ".$structure['info'].", private survey: ".$structure['privatesrvy'].", admin email: ".$structure['adminemail'].", save results: ".$structure['saveresults'].", email results: ".$structure['emailresults'];
         $wd_id = $this->newWebData($structure['name'], $structure['info'], $structure['privatesrvy'], $structure['adminemail'],NULL, $structure['saveresults'], $structure['emailresults']);
         $this->setStatus ($wd_id, $structure['status']);
         $this->newSectionFromXMLMigrate($wd_id,$structure,-1);
         return $wd_id;
      }

      function newSectionFromXMLMigrate($wd_id,$sectionStructure,$parent_s=-1){
         if ($sectionStructure != NULL) {
            $sections = array();
            if(is_array($sectionStructure['srvy_section']) && array_key_exists(0,$sectionStructure['srvy_section']))$sections = $sectionStructure['srvy_section'];
            else if ($sectionStructure['srvy_section']!=NULL) $sections[0] = $sectionStructure['srvy_section'];
            for ($i=0; $i<count($sections); $i++) {
               $section = $sections[$i];
               if ($section != NULL) {
                  $section_id = $this->addSection ($wd_id, $parent_s, $section['sec_type'], $section['label'], $section['sequence'], $section['dyna'], $section['question'], $section['param1'], $section['param2'], $section['param3'], $section['param4'], $section['param5'], $section['param6']);
                  $this->newSectionFromXMLMigrate($wd_id,$section,$section_id);   
               }
            }

            $questions = array();
            if(is_array($sectionStructure['srvy_question']) && array_key_exists(0,$sectionStructure['srvy_question']))$questions = $sectionStructure['srvy_question'];
            else if ($sectionStructure['srvy_question']!=NULL) $questions[0] = $sectionStructure['srvy_question'];
            for ($j=0; $j<count($questions); $j++) {
               $question = $questions[$j];
               if ($question != NULL && $question['question_type'] != NULL) {
                  $field_id = $question['question_id'];
                  $this->addField($wd_id, $parent_s, $field_id, $question['label'], $question['question'], $question['question_type'], $question['sequence'], $question['privacy'], $question['header'], $question['defaultval']);
               }
            }
         }
      }

      //TODO:
      // this method answers the question:
      // Are there any sections (identified by the filter phrase) that has an answered question in it
      function sectionsAnswered ($wd_id, $wd_row_id, $filter) {
         $found = FALSE;
         if ($wd_row_id != null) {
            $sections = $this->returnSectionsWithFilter($wd_id,$filter);
            $i = 0;
            while($i<count($sections) && !$found) {
               $s = $sections[$i];
               $questions = $this->getFields($wd_id, $s['section']);
               $j=0;
               while ($j<count($questions) && !$found) {
                  $q = $questions[$j];
                  $answered = $this->getAnswer($wd_row_id, $q['field_id']);
                  if ($answered['answer'] != null) {
                     $found = TRUE;
                  }
                  $j++;
               }
               $i++;
            }
         }
         return $found;
      }

      function getDataTableEntryFields($wd_id,$params,$url=null,$class=null,$showLink=false,$hiddenFields="") {
         $search = $this->getCMSSearchCriteria($wd_id,25,$url);
         $questions = $this->getAllFields($wd_id);
         $qs = NULL;
         $ts = NULL;
         for ($i=0; $i<count($questions); $i++) {
            $qs[strtolower($questions[$i]['label'])] = $questions[$i]['field_id'];
            $ts[strtolower($questions[$i]['label'])] = $questions[$i]['field_type'];
            $ls[strtolower($questions[$i]['label'])] = $questions[$i]['label'];
            $xs[strtolower($questions[$i]['label'])] = $questions[$i]['question'];
         }

         $table = "";
         $table .= "\n<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"2\">\n<tr><td>\n";
         $table .= "\n<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n<tr>\n";
         $table .= "<td>".$search['limittable']."</td><td>".$search['pagetable']."</td>";
         $table .= "</tr>\n</table>\n";
         $cellbg="#CCCCCC";
         $table .= "\n<table width=\"100%\" bgcolor=\"#555555\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" ";
         if ($class!=null) $table .= "class=\"".$class."\"";
         $table .= ">\n";
         $table .= "<form name=\"updaterow\" id=\"updaterow\" action=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."\" method=\"POST\">\n";
         $table .= "<input type=\"hidden\" name=\"updateForm\" value=\"1\">\n";

         $table .= "<tr bgcolor=\"".$cellbg."\">\n";
         if ($showLink) $table.= "<td align=\"center\"><b><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=p.wd_row_id\">ID</a></b></td>\n";
         $table.= "<td align=\"center\"><b><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=complete\">Status</a></b></td>\n";
         $table.= "<td align=\"center\"><b><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=created\">Created</a></b></td>\n";
         $totalcols = 0;
         for ($i=1; $i<count($params); $i++) {
            $table .= "<td align=\"center\"><b><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=".$qs[strtolower($params[$i])]."\">".$params[$i]."</a></b></td>\n";
            $totalcols++;
         }
         $table .= "</tr>\n";

         $results = $search['results'];
         $colspan=2;
         if ($showLink) $colspan=3;
         if ($results==NULL || count($results)<1) $table .= "<tr bgcolor=\"#CCFFFF\"><td align=\"center\" colspan=\"".($totalcols+$colspan)."\"><b>Sorry, this data query is empty.</b></td></tr>";
         for ($i=0; $i<count($results); $i++) {
            $table .= "<input type=\"hidden\" name=\"survey[".($i+1)."]\" value=\"".$wd_id."\">\n";
            $table .= "<input type=\"hidden\" name=\"wd_row_id[".($i+1)."]\" value=\"".$results[$i]['wd_row_id']."\">\n";

            if (strcmp($cellbg,"#FFFFFF")==0) $cellbg="#DDDDDD";
            else $cellbg="#FFFFFF";

            $openlink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&wd_row_id=".$results[$i]['wd_row_id']."&complete=Y\">OK</a>";
            $closelink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&wd_row_id=".$results[$i]['wd_row_id']."&complete=L\">Close</a>";
            $attnlink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&wd_row_id=".$results[$i]['wd_row_id']."&complete=A\">ATTN!</a>";
            $speciallink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&wd_row_id=".$results[$i]['wd_row_id']."&complete=X\">Caution</a>";
            $seplink = "<br>";
   
             if (0==strcmp($results[$i]['complete'],"Y") || 0==strcmp($results[$i]['complete'],"N")) {
                  $link = $closelink.$seplink.$attnlink;
                  $statusbg="#6FFF6F";
                  $status = "OK";
             } else if (0==strcmp($results[$i]['complete'],"L")) {
                  $link = $openlink.$seplink.$attnlink;
                  $statusbg="#DDDDDD";
                  $status = "Closed";
             } else if (0==strcmp($results[$i]['complete'],"N")) {
                  $link = $openlink.$seplink.$closelink.$seplink.$attnlink;
                  $statusbg="#FFFFFF";
                  $status = "New";
             } else if (0==strcmp($results[$i]['complete'],"X")) {
                  $link = $openlink.$seplink.$attnlink;
                  $statusbg="#FDFF5B";
                  $status = "Caution";
             } else if (0==strcmp($results[$i]['complete'],"A")) {
                  $link = $openlink.$seplink.$closelink;
                  $statusbg="#FF4348";
                  $status = "Attention!";
             } else {
                  $link = $openlink.$seplink.$closelink;
                  $statusbg="#FFFFFF";
                  $status = "N/A";
             }

            $table .= "<tr bgcolor=\"".$cellbg."\">\n";
            if ($showLink) $table .= "<td><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&viewsingle=1&wd_row_id=".$results[$i]['wd_row_id']."\">".$results[$i]['wd_row_id']."</a></td>\n";
            $table .= "<td bgcolor=\"".$statusbg."\">".$status."<BR>".$link."</td>\n";
            $table .= "<td>".$results[$i]['created']."</td>\n";
            for ($j=1; $j<count($params); $j++) {
               $field = strtolower($params[$j]);
               $qid = $qs[$field];
               $fldtype = $ts[$field];
               if (0!=strcmp($fldtype,"INFO") && 0!=strcmp($fldtype,"SPACER") && 0!=strcmp($fldtype,"HTML")) {
   
                  $columnDescr = $results[$i][$qid];
                  $descrColor = $cellbg;
                  if (strcmp($field,"enabled")==0) {
                     $enableLink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&field_id=".$qid."&wd_row_id=".$results[$i]['wd_row_id']."&setenable=1&answer=Yes\">Enable</a>";
                     $disableLink = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit'].$search['urlpage'].$search['orderby']."&field_id=".$qid."&wd_row_id=".$results[$i]['wd_row_id']."&setenable=1&answer=No\">Disable</a>";
                     if (strcmp(strtoupper($results[$i][$qid]),"YES")==0) {
                        $columnDescr = "Yes<br>".$disableLink;
                        $descrColor = "#73D975";
                     } else {
                        $columnDescr = "No<br>".$enableLink;
                        $descrColor = "#D97373";
                     }
                  } else if (0==strcmp($fldtype,"IMAGE") || 0==strcmp($fldtype,"MBL_UPL")) {
                     //$info = getHeightProportion ($GLOBALS['srvyDir'].$columnDescr, "60");
                     if(strlen($columnDescr)>5 && 0!=strcmp(substr($columnDescr,0,4),"http")) $columnDescr = $GLOBALS['srvyURL'].$columnDescr;
                     $columnDescr = "<a href=\"".$columnDescr."\" target=\"_new\"><img src=\"".$columnDescr."\" style=\"width:60px;height:auto;\"></a>";
                  } else if (0==strcmp($fldtype,"TEXT") || 0==strcmp($fldtype,"DATE") || 0==strcmp($fldtype,"AGE") || 0==strcmp($fldtype,"DATETIME")) {
                     $columnDescr = "<input class=\"tableinput\" size=\"22\" type=\"text\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                  } else if (0==strcmp($fldtype,"INT") || 0==strcmp($fldtype,"DEC") || 0==strcmp($fldtype,"MONEY")) {
                     $columnDescr = "<input class=\"tableinput\" style=\"width:65px;\" type=\"text\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" value=\"".$columnDescr."\">\n";
                  } else if (0==strcmp($fldtype,"TEXTAREA")) {
                     $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"25\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\">".$columnDescr."</textarea>\n";
                  } else if (0==strcmp($fldtype,"USERS")) {
                     $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
                     $ua = new UserAcct();
                     $usersA = $ua->getUsersForSegment(strtolower(trim($xs[$field])));
                     $users = $usersA['users'];
                     for ($i=0; $i<count($users); $i++) {
                        $user = $ua->getUser($users[$i]['userid']);
                        $optionList[$user['fname']." ".$user['lname']." ".$user['company']]=$user['userid'];
                     }
                     if ($optionList != NULL) {
                       $a = 0;
                       foreach ($optionList as $key => $value) {
                          $selected="";
                          if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                           $temp .= "<option id=\"w".$wd_id."a".$qid."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                          $a++;
                       }
                     }
                     $temp .= "</select>";
                     $columnDescr = $temp;
                  } else if (0==strcmp($fldtype,"SITELIST")) {
                     $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
                     $ctx = new Context();
                     $optionList = $ctx->getSiteOptions();
                     if ($optionList != NULL) {
                       $a = 0;
                       foreach ($optionList as $key => $value) {
                          $selected="";
                          if (strcmp($columnDescr,$value)==0) $selected="selected=\"selected\"";
                           $temp .= "<option id=\"w".$wd_id."a".$qid."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                          $a++;
                       }
                     }
                     $temp .= "</select>";
                     $columnDescr = $temp;
                  } else if (0==strcmp($fldtype,"DROPDOWN") || 0==strcmp($fldtype,"RADIO") || 0==strcmp($fldtype,"POLLRADIO") || 0==strcmp($fldtype,"VOTE")) {
                     //$temp = $columnDescr;
                     $temp = "";
                     $temp .= "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
                     $temp .= "<option value=\"\" id=\"w".$wd_id."a".$qid."_0\"></option>\n";
                     $optionList = convertBack($xs[$field]);
                     if ($optionList != NULL) {
                        $options = separateStringBy($optionList,",");
                        for ($a=0; $a<count($options); $a++) {
                           $selected="";
                           if (strcmp($columnDescr,$options[$a])==0) $selected="selected=\"selected\"";
                           $temp .= "<option id=\"w".$wd_id."a".$qid."_".($a+1)."\" value=\"".$options[$a]."\" ".$selected.">".$options[$a]."</option>\n";
                        }
                     }
                     $temp .= "</select>";
                     $columnDescr = $temp;
                  } else if (0==strcmp($fldtype,"STATE")) {
                     $temp = listStates($columnDescr,"w".$wd_id."a".$qid."[".($i+1)."]",TRUE);
                     $columnDescr = $temp;
                  } else {                  
                     if (strlen($columnDescr)>100) $columnDescr = substr($columnDescr,0,97)."...";
                  }
   
                  $table .= "<td bgcolor=\"".$descrColor."\">".$columnDescr."</td>\n";
               } //end if checking for space/info elements
            } //end looping for all paramters/fields to show in table
            $table .= "</tr>\n";
         }

         //below row in table to add a new entry
         $cellbg="#FFCCCC"; //for new row change color
         $table .= "<input type=\"hidden\" name=\"survey[".(count($results)+1)."]\" value=\"".$wd_id."\">\n";
         $table .= "<tr bgcolor=\"".$cellbg."\">\n";
         $table .= "<td colspan=\"".$colspan."\"><i>Create a new entry here</i></td>\n";
         for ($j=1; $j<count($params); $j++) {
            $field = strtolower($params[$j]);
            $qid = $qs[$field];
            $fldtype = $ts[$field];

            $columnDescr = "";
            $descrColor = $cellbg;
            if (0==strcmp($fldtype,"TEXT")) {
               $columnDescr = "<input class=\"tableinput\" size=\"22\" type=\"text\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\">\n";
            } else if (0==strcmp($fldtype,"INT") || 0==strcmp($fldtype,"DEC") || 0==strcmp($fldtype,"MONEY")) {
               $columnDescr = "<input class=\"tableinput\" style=\"width:65px;\" type=\"text\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\">\n";
            } else if (0==strcmp($fldtype,"TEXTAREA")) {
               $columnDescr ="<textarea class=\"tableinput\" rows=\"3\" cols=\"25\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\"></textarea>\n";
            } else if (0==strcmp($fldtype,"USERS")) {
               $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
               $ua = new UserAcct();
               $usersA = $ua->getUsersForSegment(strtolower(trim($xs[$field])));
               $users = $usersA['users'];
               for ($i=0; $i<count($users); $i++) {
                  $user = $ua->getUser($users[$i]['userid']);
                  $optionList[$user['fname']." ".$user['lname']." ".$user['company']]=$user['userid'];
               }
               if ($optionList != NULL) {
                 $a = 0;
                 foreach ($optionList as $key => $value) {
                    $selected="";
                     $temp .= "<option id=\"w".$wd_id."a".$qid."_".$a."\" value=\"".$value."\" ".$selected.">".$key."</option>\n";
                    $a++;
                 }
               }
               $temp .= "</select>";
               $columnDescr = $temp;
            } else if (0==strcmp($fldtype,"SITELIST")) {
               $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
               $ctx = new Context();
               $optionList = $ctx->getSiteOptions();
               if ($optionList != NULL) {
                 $a = 0;
                 foreach ($optionList as $key => $value) {
                    $selected="";
                     $temp .= "<option id=\"w".$wd_id."a".$qid."_".$a."\" value=\"".$value."\">".$key."</option>\n";
                    $a++;
                 }
               }
               $temp .= "</select>";
               $columnDescr = $temp;
            } else if (0==strcmp($fldtype,"DROPDOWN") || 0==strcmp($fldtype,"RADIO") || 0==strcmp($fldtype,"POLLRADIO") || 0==strcmp($fldtype,"VOTE")) {
               $temp = "<select class=\"tableinput\" name=\"w".$wd_id."a".$qid."[".($i+1)."]\" id=\"w".$wd_id."a".$qid."\">\n";
               $temp .= "<option value=\"\" id=\"w".$wd_id."a".$qid."_0\"></option>\n";
               $optionList = convertBack($xs[$field]);
               if ($optionList != NULL) {
                  $options = separateStringBy($optionList,",");
                  for ($a=0; $a<count($options); $a++) {
                     $temp .= "<option id=\"w".$wd_id."a".$qid."_".($a+1)."\" value=\"".$options[$a]."\">".$options[$a]."</option>\n";
                  }
               }
               $temp .= "</select>";
               $columnDescr = $temp;
            } else if (0==strcmp($fldtype,"STATE")) {
               $temp = listStates(NULL,"w".$wd_id."a".$qid."[".($i+1)."]",TRUE);
               $columnDescr = $temp;
            }

            $table .= "<td bgcolor=\"".$descrColor."\">".$columnDescr."</td>\n";
         } //end looping for all paramters/fields to show in table
         $table .= "</tr>\n";
         $table .="</table>\n";
         $table .="</td></tr>\n";
         $table .="<tr bgcolor=\"#FFFFFF\"><td align=\"left\"><input type=\"submit\" name=\"submit\" value=\"Save Values In Table\"> <input type=\"submit\" name=\"submit\" value=\"Cancel Table Changes\"> <input type=\"button\" onClick=\"window.location='".$url."'\" value=\"Reset Page\"></td></tr>";
         $table .="</form></table>\n";

         return $table;
      }

      function doDataSubstitutions($str,$sub) {
         $str=$this->doSearchFieldSubs($str,$sub);
         $str=$this->doTableSubs($str,$sub);
         $str=$this->doColumnSubs($str,$sub);
         $str=$this->doRssSubs($str,$sub);
         return $str;
      }

      function doRssSubs($str,$sub) {
//print "\n<!-- in doRssSubs -->\n";
         //parameter 1 identifies the template to be used for this rss
         $ss = new Version();
         $tag = $ss->getValue("tagrss");
         $feedtag = $ss->getValue("tagrssfeed");
         $tagbeg = $ss->getValue("tagstarter");
         $tagstart = $tagbeg.$tag."_";
         $tagend = "_".$tag.$tagbeg;
         $finished = false;
         $template = new Template();
         while (!$finished) {
            $shortname = findTagInString($feedtag,$str);
            if ($shortname == NULL) $finished = true;
            else {
               $params = separateStringBy($shortname,$ss->getValue("tagstarter"));
               $url = $params[0];
//print "\n<!-- url: ".$url." -->\n";
               if ($url != null) {
                  $rssObj = new lastRSS();
                  $rssTimeout = $params[4];
                  if ($rssTimeout < 1) $rssTimeout = 14400;
                  $rssArray = $rssObj->Get($url, $rssTimeout);
//print "\n<!-- rss array:\n";
//print_r($rssArray);
//print "\n-->\n";
                  if ($rssArray != null && !empty($rssArray)) {
                     $rssTemp = $params[1];
                     if ($rssTemp == null) $rssTemp="rsstemplate";
                     $contents = $template->doSubstitutions("%%%CMS_".$rssTemp."_CMS%%%",$sub);
                     foreach($rssArray as $key => $value) $contents = str_replace($tagstart.$key.$tagend,$value,$contents);

                     $bTag = $tagstart."BEGINITEMS".$tagend;
                     $eTag = $tagstart."ENDITEMS".$tagend;
                     $btPos = strpos($contents,$bTag);
                     if (strpos($contents,$eTag,$btPos)===false) $finished=true;
                     else {
                        $etPos = strpos($contents,$eTag,$btPos);
                        $middleLength = $etPos-($btPos+strlen($bTag));
                        $first = substr($contents,0,$btPos);
                        $middle = substr($contents,$btPos+strlen($bTag),$middleLength);
                        $last = substr($contents,$etPos+strlen($eTag));
                        $newmiddle = "";
                        $maxCount = $params[2];
                        if ($maxCount==null || $maxCount<1 || $maxCount>count($rssArray['items'])) $maxCount=count($rssArray['items']);
                        for ($i=0; $i<$maxCount; $i++) {
                           $tempmiddle = $middle;
                           foreach($rssArray['items'][$i] as $key => $value) {
                              if (0==strcmp($key,"enclosure")) {
                                 $value=" ";
                                 $finished2 = false;
                                 $c_count = 0;
                                 while (!$finished2 && $c_count<count($rssArray['items'][$i]["enclosure"])) {
                                    if (0==strcmp($rssArray['items'][$i]["enclosure"][$c_count]["type"],"gif/jpeg")) {
                                       $value = "<img src=\"".$rssArray['items'][$i]["enclosure"][$c_count]["url"]."\" border=\"0\">";
                                       $finished2 = true;
                                    }
                                    $c_count++;
                                 }
                                 $tempmiddle = str_replace($tagstart."itemenclosureimage".$tagend,$value,$tempmiddle);
                              }
                              else $tempmiddle = str_replace($tagstart."item".$key.$tagend,$value,$tempmiddle);
                           }
                           $tempmiddle = str_replace($tagstart."itemenclosureimage".$tagend,"",$tempmiddle);
                           $tempmiddle = str_replace($tagstart."itemnumber".$tagend,$i,$tempmiddle);
                           $newmiddle .= $tempmiddle;
                        }
                        
                        $contents = $first.$newmiddle.$last;

                        $rssId = $params[3];
                        if ($rssId == null) $rssId="1";
                        $contents = str_replace($tagstart."rssid".$tagend,$rssId,$contents);
                     }
                     $htmlCommentBegin = ""; //"\n<!--START cmsdata doRssSubs -->\n";
                     $htmlCommentEnd = ""; //"\n<!--END cmsdata doRssSubs -->\n";
                     $str = str_replace($tagbeg.$feedtag."_".$shortname."_".$feedtag.$tagbeg,$htmlCommentBegin.$contents.$htmlCommentEnd,$str);
                  }
                  else {
                     $str = str_replace($tagbeg.$feedtag."_".$shortname."_".$feedtag.$tagbeg,"",$str);
                  }
               }
            }
         }
         return $str;
      }

      // Example: %%%DATA_<survey_name>%%%<param_1>%%%<param_2>%%%<param_n>_DATA%%%
      function doTableSubs($str,$sub){
      $ss = new Version();
      $htmlCommentBegin = "";
      $htmlCommentEnd = "";
      $tag = $ss->getValue("tagdata");
      $tagbeg = $ss->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
         $finished = false;
         while (!$finished) {
            $shortname = findTagInString($tag,$str);
            if ($shortname == NULL) $finished = true;
            else {
               $params = separateStringBy($shortname,$ss->getValue("tagstarter"));
               $survey = $this->getWebDataByName($params[0]);
               if ($survey !=NULL) {
                  $htmlCommentBegin = ""; //"\n<!--START cmsdata doTableSubs -->\n";
                  $htmlCommentEnd = ""; //"\n<!--END cmsdata doTableSubs -->\n";
                  $str = str_replace($tagstart.$shortname.$tagend,$htmlCommentBegin.$this->getDataTable2($survey['wd_id'],$params).$htmlCommentEnd,$str);
               }
            }
         }
         return $str;
      }

      //Example: %%%DATASEARCH_<survey_name>%%%<param_1>%%%<param_2>%%%<param_n>_DATASEARCH%%%
      function doSearchFieldSubs($str,$sub){
         $ss = new Version();
         $tag = $ss->getValue("tagsearch");
         $tagbeg = $ss->getValue("tagstarter");
         $tagstart = $tagbeg.$tag."_";
         $tagend = "_".$tag.$tagbeg;
         $finished = false;
         while (!$finished) {
            $shortname = findTagInString($tag,$str);
            if ($shortname == NULL) $finished = true;
            else {
               $params = separateStringBy($shortname,$ss->getValue("tagstarter"));
               $survey = $this->getWebDataByName($params[0]);
               if ($survey !=NULL) {
                  $searchFields = array();
                  for ($i=1; $i<count($params); $i++) $searchFields[$i-1] = $params[$i];
                  $url = getBaseURL().$GLOBALS['codeFolder']."controller.php?view=".getParameter("view")."&action=".getParameter("action")."&cmslimit=".getParameter("cmslimit")."&cmsorderby=".getParameter("cmsorderby");
                  $str = str_replace($tagstart.$shortname.$tagend,$htmlCommentBegin.$this->getCMSSearchFields($survey['wd_id'],$url,$searchFields).$htmlCommentEnd,$str);
               }
            }
         }
         return $str;
      }

      function doColumnSubs($str,$sub){
         $ss = new Version();
         $tag = $ss->getValue("tagbegindata");
         $tagbeg = $ss->getValue("tagstarter");
         $tagstart = $tagbeg.$tag."_";
         $tagend = "_".$tag.$tagbeg;
         $finished = false;
         while (!$finished) {
            $shortname = findTagInString($tag,$str);
            if ($shortname == NULL) $finished = true;
            else {
               $params = separateStringBy($shortname,$tagbeg);
               $dataname=$params[0];
               $survey = $this->getWebDataByName($dataname);
               if ($survey !=NULL) {
                  $defaultLimit=trim($params[1]);
                  $searchStr=trim($params[2]);
                  $bTag = $tagstart.$shortname.$tagend;
                  $tag2 = $ss->getValue("tagenddata");
                  $tagstart2 = $tagbeg.$tag2."_";
                  $tagend2 = "_".$tag2.$tagbeg;
                  $eTag = $tagstart2.$dataname.$tagend2;
                  $btPos = strpos($str,$bTag);
                  if (strpos($str,$eTag,$btPos)===false) $finished=true;
                  else {
                     $etPos = strpos($str,$eTag,$btPos);
                     $middleLength = $etPos-($btPos+strlen($bTag));
                     $first = substr($str,0,$btPos);
                     $middle = substr($str,$btPos+strlen($bTag),$middleLength);
                     $last = substr($str,$etPos+strlen($eTag));
   
                     $htmlCommentBegin = ""; //"\n<!--START cmsdata doColumnSubs -->\n";
                     $htmlCommentEnd = ""; //"\n<!--END cmsdata doColumnSubs -->\n";
                     $str = $this->doCOLSubs($first.$htmlCommentBegin,$middle,$htmlCommentEnd.$last,$survey['wd_id'],$searchStr,$defaultLimit);
                     //$str .= "<textarea rows=\"6\" cols=\"50\">".$first."</textarea>";
                     //$str .= "<textarea rows=\"6\" cols=\"50\">".$middle."</textarea>";
                     //$str .= "<textarea rows=\"6\" cols=\"50\">".$last."</textarea>";
                     //$str .= "dataname: ".$dataname." searchStr: ".$searchStr." defaultLimit: ".$defaultLimit;
                  }
               }
            }
         }
         return $str;
      }

   function getCMSSearchParams($wd_id,$tblPrefix="",$printdebug=FALSE){
   	  //print "<br>\n get:\n<br>";
   	  //print_r($_GET);
   	  //print "<br>\n post:\n<br>";
   	  //print_r($_POST);
   	  //print "<br>\n session:\n<br>";
   	  //print_r($_SESSION);
   	  //print "<br>\n<br>\n"; 
   	  
   	if($printdebug) print "<br>\nInside getCMSSearchParams";
   	   
      $urlsearch = "";
      $whereClause = "";
      $paramArr = array();
      $paramDispArr = array();
      
      // This will return the object if it's an id or a name.
      $wd = $this->getWebData($wd_id);
      $wd_id = $wd['wd_id'];
      $wdcodename = removeSpecialChars(strip_tags(strtolower(trim(convertBack($wd['name'])))));
      $wdcodename2 = removeSpecialChars(strip_tags(strtolower(trim(convertBack($wd['shortname'])))));

      //$qs = $this->getFieldNames($wd_id);
      //$qs2 = $this->getFieldLabels($wd_id,TRUE);
      //$qs3 = $this->getFieldsIndexed($wd_id);
      $allqs = $this->getFieldsMultiIndex($wd_id);
      $qs = $allqs['byname'];
      $qs2 = $allqs['bylabel'];
      $qs3 = $allqs['indexed'];

      //Log history of this search
      if (isset($_SESSION['ssn_webdata'][$wd_id])) $_SESSION['ssn_webdata'][$wd_id] += 1;
      else $_SESSION['ssn_webdata'][$wd_id] = 1;

      //See if a specific row id was set
      $cmsrowid = getParameter("cmsrowid");
      if ($cmsrowid!=null) {
         if ($whereClause != null) $whereClause .= " AND ";
         $urlsearch .= "&cmsrowid=".$cmsrowid;
         $whereClause .= " ".$tblPrefix."wd_row_id='".$cmsrowid."'";
      }

      //See if a specific row id was enabled
      $cmsenabled = strtolower(trim(getParameter("cmsenabled")));
      if ($cmsenabled!=null && isset($qs2['enabled'])) {
         $urlsearch .= "&cmsenabled=".$cmsenabled;
         if ($whereClause != null) $whereClause .= " AND ";
         if(0==strcmp($cmsenabled,"yes") || 0==strcmp($cmsenabled,"1")) $whereClause .= " LOWER(".$tblPrefix.$qs2['enabled'].")='yes'";
         //else $whereClause .= " LOWER(".$tblPrefix.$qs2['enabled'].")<>'yes'";
      }

      // Fuzzy search on lastupdateby column
      // There can be multiple values in array or semi-colon delimited string
      $key = "cmszby_w".$wd_id;
      $value = getParameter($key);
      if($value!=NULL) {
         if (is_array($value)) $value = implode(";",$value);
         $urlsearch .= "&".$key."=".urlencode($value);
         $paramArr[$key] = $value;
         $paramDispArr[$key] = "Updated by";
   
         if ($whereClause != null) $whereClause .= " AND ";
   
         $fld = "lastupdateby";
         $valarr = separateStringBy($value," ",NULL,TRUE);
         $whereClause .= "( ";
         $wcounter=0;
         for ($i=0;$i<count($valarr);$i++) {
            $curval = trim($valarr[$i]);
            if ($curval!=NULL) {
               if ($wcounter>0) $whereClause .= "OR ";
               $whereClause .= "LOWER(".$tblPrefix.$fld.") LIKE '%".strtolower($curval)."%' ";
               $wcounter++;
            }
         }
         $whereClause .= ") ";
      }

      // Fuzzy search on a specific column format: cmsz_w<wdid><field id>
      // There can be multiple values in array or semi-colon delimited string
      $startswith = "cmsz_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         if (is_array($value)) $value = implode(";",$value);
         $urlsearch .= "&".$key."=".urlencode($value);
         $paramArr[$key] = $value;
         $paramDispArr[$key] = $qs[substr($key,strlen($startswith))];

         if ($whereClause != null) $whereClause .= " AND ";

         $fld = substr($key,strlen($startswith));
         if(!isset($qs[$fld])) {
             if(isset($qs2[$fld])) $fld = $qs2[$fld];
             else $fld=NULL;
         }
         
         if ($fld!=NULL) {
             $valarr = separateStringBy($value,";");
             $whereClause .= "( ";
             $wcounter=0;
             for ($i=0;$i<count($valarr);$i++) {
                $curval = trim($valarr[$i]);
                if ($curval!=NULL) {
                   if ($wcounter>0) $whereClause .= "OR ";
                   $whereClause .= "LOWER(".$tblPrefix.$fld.") LIKE '%".strtolower($curval)."%' ";
                   $wcounter++;
                }
             }
             $whereClause .= ") ";
    
             if (strlen($key)<32) {
                if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
             }
         }
      }

      // Fuzzy search on a specific column format: cmsz_<wdname>_<field id>
      // There can be multiple values in array or semi-colon delimited string
      $startswith = "cmsz_".$wdcodename."_";
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmsz_".$wdcodename2."_";
         $searchArr = getParameters($startswith);
      }
      foreach($searchArr as $key => $value){
         if (is_array($value)) $value = implode(";",$value);
         $urlsearch .= "&".$key."=".urlencode($value);
         $paramArr[$key] = $value;
         $paramDispArr[$key] = $qs[substr($key,strlen($startswith))];

         $fld = substr($key,strlen($startswith));
         if(!isset($qs[$fld])) {
             if(isset($qs2[$fld])) $fld = $qs2[$fld];
             else $fld=NULL;
         }
         
         if ($fld!=NULL) {
            $valarr = separateStringBy($value,";",NULL,TRUE);
            if (count($valarr)>0) {
               if ($whereClause != null) $whereClause .= " AND ";               
               $whereClause .= " ( ";
               $wcounter=0;
               for ($i=0;$i<count($valarr);$i++) {
                  $curval = $valarr[$i];
                  if ($wcounter>0) $whereClause .= " OR ";
                  $whereClause .= " ( ";
                  $valarr2 = separateStringBy($curval,",",NULL,TRUE);
                  $wcounter2=0;
                  for ($j=0;$j<count($valarr2);$j++) {
                     $curval2 = $valarr2[$j];
                     if ($wcounter2>0) $whereClause .= " AND ";
                     $whereClause .= "LOWER(".$tblPrefix.$fld.") LIKE '%".strtolower($curval2)."%' ";
                     $wcounter2++;
                  }
                  $whereClause .= " ) ";
                  $wcounter++;
               }
               $whereClause .= " ) ";
               
               //if (strlen($key)<32) {
               //   if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
               //   else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
               //   $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               //}
            }
         }
      }

      // Search for an exact value with format: cmsq_w<wd id><field id>
      $startswith = "cmsq_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         $value = trim($value);
         if ($value!=NULL) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
             $fld = substr($key,strlen($startswith));
             if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
             }
            
             if($fld!=NULL) {
                // Check that someone is not searching for this question's name (instead of value)
                $value2 = NULL;
                $tmp = separateStringBy(convertBack($qs3[$fld]['question']),";");
                if($tmp[0]!=NULL && $tmp[1]!=NULL) {
                   $tmp1 = separateStringBy($tmp[0],",");
                   $tmp2 = separateStringBy($tmp[1],",");
                   if($tmp1!=NULL && $tmp2!=NULL && count($tmp1)==count($tmp2)) {
                      for($i=0;$i<count($tmp1);$i++){
                         if(strcmp(strtolower(trim($value)),strtolower(trim($tmp1[$i])))==0 || strcmp(strtolower(trim($value)),strtolower(trim($tmp2[$i])))==0) {
                            $value = $tmp2[$i];
                            $value2 = $tmp1[$i];
                            break;
                         }
                      }
                   }
                }
                
                if ($whereClause != null) $whereClause .= " AND ";
                $whereClause.= "(LOWER(".$tblPrefix.$fld.")='".strtolower($value)."'";
                if($value2!=NULL) $whereClause .= " OR LOWER(".$tblPrefix.$fld.")='".strtolower($value2)."'";
                $whereClause.= ")";
                $paramArr[$key] = $value;
                $paramDispArr[$key] = $qs[substr($key,strlen($startswith))];
                if (strlen($key)<32) {
                   if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                   else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                   $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
                }
             }
         }
      }

      // Search for an exact value with format: cmsq_<wdname>_<field id>
      $startswith = "cmsq_".$wdcodename."_";
      //print "Starts with: ".$startswith."<br>\n";
      $searchArr = getParameters($startswith);
      //print_r($searchArr);
      //print "<br>\n<br>\n";
      if(empty($searchArr)) {
         $startswith = "cmsq_".$wdcodename2."_";
         //print "Starts with: ".$startswith."<br>\n";
         $searchArr = getParameters($startswith);
         //print_r($searchArr);
         //print "<br>\n<br>\n";
      }
      foreach($searchArr as $key => $value){
         $value = trim($value);
         if ($value!=NULL) {
            $urlsearch .= "&".$key."=".urlencode($value);
       
             $fld = substr($key,strlen($startswith));
             //print "<br>\nfield1: ".$fld."<br>\n";
             if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
             }
             //print "<br>\nfield2: ".$fld."<br>\n";
             if($fld!=NULL) {
               $valarr = separateStringBy($value,";",NULL,TRUE);
               if (count($valarr)>0) {
                  if ($whereClause != null) $whereClause .= " AND ";               
                  $whereClause .= " ( ";
                  $wcounter=0;
                  for ($i=0;$i<count($valarr);$i++) {
                     $curval = $valarr[$i];
                     if ($wcounter>0) $whereClause .= " OR ";
                     $whereClause .= " ( ";
                     $valarr2 = separateStringBy($curval,",",NULL,TRUE);
                     $wcounter2=0;
                     for ($j=0;$j<count($valarr2);$j++) {
                        $curval2 = $valarr2[$j];
                        if ($wcounter2>0) $whereClause .= " AND ";
                        $whereClause .= "LOWER(".$tblPrefix.$fld.")='".strtolower($curval2)."' ";
                        $wcounter2++;
                     }
                     $whereClause .= " ) ";
                     $wcounter++;
                  }
                  $whereClause .= " ) ";
                  
                  //if (strlen($key)<32) {
                  //   if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  //   else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  //   $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
                  //}
               }
                
                
                
                /*
                if ($whereClause != null) $whereClause .= " AND ";
                $whereClause.= "LOWER(".$tblPrefix.$fld.")='".strtolower($value)."'";
                //print "<br>\nWhere: ".$whereClause."<br>\n";
                $paramArr[$key] = $value;
                $paramDispArr[$key] = $qs[substr($key,strlen($startswith))];
                if (strlen($key)<32) {
                   if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                   else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                   $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
                }
                */
             }
         }
      }

      // Search for an a value in a set with format: cmscsv_w<wd id><field id>
      $startswith = "cmscsv_w".$wd_id;
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmscsv_".$wdcodename."_";
         $searchArr = getParameters($startswith);
         if(empty($searchArr)) {
            $startswith = "cmscsv_".$wdcodename2."_";
            $searchArr = getParameters($startswith);
         }
      }
      foreach($searchArr as $key => $value){
         $value = trim($value);
         if ($value!=NULL) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
                if ($whereClause != null) $whereClause .= " AND ";
                $value2 = str_replace(" ","",strtolower($value));
                $whereClause.= " (";
                $whereClause.= "REPLACE(LOWER(".$tblPrefix.$fld."),' ','')='".$value2."'";
                $whereClause.= " OR REPLACE(LOWER(".$tblPrefix.$fld."),' ','') LIKE '".$value2.",%'";
                $whereClause.= " OR REPLACE(LOWER(".$tblPrefix.$fld."),' ','') LIKE '%,".$value2.",%'";
                $whereClause.= " OR REPLACE(LOWER(".$tblPrefix.$fld."),' ','') LIKE '%,".$value2."'";
                $whereClause.= ") ";
                
                $paramArr[$key] = $value;
                $paramDispArr[$key] = $qs[substr($key,strlen($startswith))];
            }
         }
      }

      // Search for rows less than or equal to this value: cmsh_w<wd id><field id>
      $startswith = "cmsh_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         $value = str_replace("$","",$value);
         $value = str_replace(",","",$value);
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld."<=".$value;
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." less than";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         }
      }

      $startswith = "cmsh_".$wdcodename."_";
      if($printdebug) print "<br>\nLooking for params that start with: ".$startswith;
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmsh_".$wdcodename2."_";
         if($printdebug) print "<br>\nLooking for params that start with: ".$startswith;
         $searchArr = getParameters($startswith);
      }
      if($printdebug) {
         print "<br>\nAnd found:<br>\n";
         print_r($searchArr);
         print "<br>\n<br>\n";
      }
      foreach($searchArr as $key => $value){
         $value = str_replace("$","",$value);
         $value = str_replace(",","",$value);
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld."<=".$value;
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." less than";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         }
      }
      
      
      // Search for rows greater than or equal to this value: cmsl_w<wd id><field id>
      $startswith = "cmsl_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         $value = str_replace("$","",$value);
         $value = str_replace(",","",$value);
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {            
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld.">=".$value;
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." greater than";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         }
      }
      
      $startswith = "cmsl_".$wdcodename."_";
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmsl_".$wdcodename2."_";
         $searchArr = getParameters($startswith);
      }
      foreach($searchArr as $key => $value){
         $value = str_replace("$","",$value);
         $value = str_replace(",","",$value);
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {            
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld.">=".$value;
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." greater than";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         }
      }
      
      
      // Search for ages in years before/after this number: cmsa<l or h>_w<wd id><field id>
      $startswith = "cmsal_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." is before";
               $today = getDateForDB();
               $todayArr = separateStringBy($today,"-");
               $backYear = $todayArr[0] - $value;
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld."<='".$backYear."-".$todayArr[1]."-".$todayArr[2]."'";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }

            }
         }
      }
      
      $startswith = "cmsah_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." is after";
               $today = getDateForDB();
               $todayArr = separateStringBy($today,"-");
               $backYear = $todayArr[0] - $value - 1;
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld.">='".$backYear."-".$todayArr[1]."-".$todayArr[2]."'";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         }
      }

      // Search for rows with a field date before/after this value cmsd<l or h>_w<wd id><field id>
      $startswith = "cmsdl_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         //if(is_numeric($value)) {
            $urlsearch .= "&".$key."=".urlencode($value);
            $fld = substr($key,strlen($startswith));
            if(!isset($qs[$fld])) {
                 if(isset($qs2[$fld])) $fld = $qs2[$fld];
                 else $fld=NULL;
            }
            
            if($fld!=NULL) {
               $paramArr[$key] = $value;
               $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." less than";
               if ($whereClause != null) $whereClause .= " AND ";
               $whereClause.=$tblPrefix.$fld.">='".$value."'";
               if (strlen($key)<32) {
                  if (isset($_SESSION['ssn_webdata'][$wd_id."_".$key])) $_SESSION['ssn_webdata'][$wd_id."_".$key] += 1;
                  else $_SESSION['ssn_webdata'][$wd_id."_".$key] = 1;
                  $_SESSION['ssn_webdata'][$wd_id."_".$key."_value"] = $value;
               }
            }
         //}
      }
      
      $startswith = "cmsdl_".$wdcodename."_";
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmsdl_".$wdcodename2."_";
         $searchArr = getParameters($startswith);
      }
      foreach($searchArr as $key => $value){
         $urlsearch .= "&".$key."=".urlencode($value);
         $fld = substr($key,strlen($startswith));
         if(!isset($qs[$fld])) {
              if(isset($qs2[$fld])) $fld = $qs2[$fld];
              else $fld=NULL;
         }
         
         if($fld!=NULL) {
            $paramArr[$key] = $value;
            $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." less than";
            if ($whereClause != null) $whereClause .= " AND ";
            $whereClause.=$tblPrefix.$fld.">='".$value."'";
         }
      }
      
      $startswith = "cmsdh_w".$wd_id;
      $searchArr = getParameters($startswith);
      foreach($searchArr as $key => $value){
         $urlsearch .= "&".$key."=".urlencode($value);
         $fld = substr($key,strlen($startswith));
         if(!isset($qs[$fld])) {
              if(isset($qs2[$fld])) $fld = $qs2[$fld];
              else $fld=NULL;
         }
         
         if($fld!=NULL) {
            $paramArr[$key] = $value;
            $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." greater than";
            if ($whereClause != null) $whereClause .= " AND";
            $whereClause.= " (";
            $whereClause.=$tblPrefix.$fld."<='".$value."' OR ";
            $whereClause.=$tblPrefix.$fld." is NULL OR ";
            $whereClause.=$tblPrefix.$fld."='')";
         }
      }
      
      $startswith = "cmsdh_".$wdcodename."_";
      $searchArr = getParameters($startswith);
      if(empty($searchArr)) {
         $startswith = "cmsdh_".$wdcodename2."_";
         $searchArr = getParameters($startswith);
      }
      foreach($searchArr as $key => $value){
         $urlsearch .= "&".$key."=".urlencode($value);
         $fld = substr($key,strlen($startswith));
         if(!isset($qs[$fld])) {
              if(isset($qs2[$fld])) $fld = $qs2[$fld];
              else $fld=NULL;
         }
         
         if($fld!=NULL) {
            $paramArr[$key] = $value;
            $paramDispArr[$key] = $qs[substr($key,strlen($startswith))]." greater than";
            if ($whereClause != null) $whereClause .= " AND";
            $whereClause.= " (";
            $whereClause.=$tblPrefix.$fld."<='".$value."' OR ";
            $whereClause.=$tblPrefix.$fld." is NULL OR ";
            $whereClause.=$tblPrefix.$fld."='')";
         }
      }
      

      // Rows created before/after this value
      $cmsbefore = getParameter("cmsbefore");
      if ($cmsbefore!=null) {
        $urlsearch .= "&cmsbefore=".urlencode($cmsbefore);
        $paramArr["cmsbefore"] = $cmsbefore;
        $paramDispArr["cmsbefore"] = "Created before";
        if ($whereClause != null) $whereClause .= " AND ";
        $whereClause.=$tblPrefix."created<'".$cmsbefore."'";
      }
      $cmsafter = getParameter("cmsafter");
      if ($cmsafter!=null) {
        $urlsearch .= "&cmsafter=".urlencode($cmsafter);
        $paramArr["cmsafter"] = $cmsafter;
        $paramDispArr["cmsafter"] = "Created after";
        if ($whereClause != null) $whereClause .= " AND ";
        $whereClause.=$tblPrefix."created>'".$cmsafter."'";
      }


      
      
      $results['wd_id'] = $wd_id;
      $results['url'] = $urlsearch;
      $results['where'] = $whereClause;
      $results['params'] = $paramArr;
      $results['display'] = $paramDispArr;
      //print "\n<!-- ".$results['where']." -->\n";
      return $results;
   }

   function getCMSSearchFields($wd_id,$url,$questionArray=null){
      $questions = $this->getAllFields($wd_id);
      $returnHTML .= "<table cellpadding=\"1\" cellspacing=\"0\" bgcolor=\"#6B6B6B\"><tr><td>";
      $returnHTML .= "<table cellpadding=\"2\" cellspacing=\"0\" bgcolor=\"#DDDDDD\">\n";
      $returnHTML .= "<form name=\"searchSrvy\" action=\"".$url."\" method=\"POST\">\n";
      $returnHTML .= "<TR><TD colspan=\"2\" align=\"center\" background=\"".getBaseURL().$GLOBALS['imagesDir']."tableheaderbg.gif\"><b>Search</b></td></tr>\n";
      if ($questionArray != null) $returnHTML .= "<TR class=\"searchfields\"><TD>Order results by</td><td>".$this->getCMSOrderBy($wd_id,$questionArray)."</td></tr>\n";
      for ($i=0; $i<count($questions); $i++) {
         if ($questionArray == null || in_array($questions[$i]['label'],$questionArray)) {
            $returnHTML .= $this->getSearchHTML($questions[$i]);
         }
         else if (in_array("MIN_".$questions[$i]['label'],$questionArray)) {
               $lParamName="cmsl_w".$wd_id.$questions[$i]['field_id'];
               $low = getParameter($lParamName);
               $low = str_replace("$","",$low);
               $low = str_replace(",","",$low);
               $returnHTML .= "<tr class=\"searchfields\"><td>Min ".$questions[$i]['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$lParamName."\" value=\"".$low."\" size=\"10\"></td></tr>";
         }
         else if (in_array("MAX_".$questions[$i]['label'],$questionArray)) {
               $hParamName="cmsh_w".$wd_id.$questions[$i]['field_id'];
               $high = getParameter($hParamName);
               $high = str_replace("$","",$high);
               $high = str_replace(",","",$high);
               $returnHTML .= "<tr class=\"searchfields\"><td>Max ".$questions[$i]['label']."</td><td><input class=\"input\" type=\"text\" name=\"".$hParamName."\" value=\"".$high."\" size=\"10\"></td></tr>";
         }
      }
      $returnHTML .= "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Search\"></td></tr>";
      $returnHTML .= "</form>";
      $returnHTML .= "</table>";
      $returnHTML .= "</td></tr></table>";
      return $returnHTML;
   }

   function getCMSOrderBy($wd_id,$questionArray=null){
      $questions = $this->getAllFieldsSystem($wd_id);
      $qs = NULL;
      for ($i=0; $i<count($questions); $i++) {
         $qs[strtolower($questions[$i]['label'])] = $questions[$i]['field_id'];
      }
      $options = array();
      for ($i=0; $i<count($questionArray); $i++) {
         $line = trim($questionArray[$i]);
         if ($qs[strtolower($line)] != NULL) $options[$line] = $qs[strtolower($line)];
         else if ($qs[strtolower(substr($line,4))]!= null) $options[substr($line,4)] = $qs[strtolower(substr($line,4))];
      }
      return getOptionList("cmsorderby", $options, getParameter("cmsorderby"), FALSE, "class=\"selectbox\"");
   }


   function getCMSNoSearchCriteria($wd_id) {

         $dbi = new MYSQLAccess();

         //Search for a sequence field - if there is one, order by it
         $orderby = "";
         $query = "SELECT * FROM wd_fields WHERE wd_id=".$wd_id." AND (LOWER(label)='sequence' OR LOWER(map)='sequence');";
         $results = $dbi->queryGetResults($query);
         if ($results != NULL && count($results)>0) $orderby=" ORDER BY ".$results[0]['field_id']." ASC";

         //Search for an enabled field - if there is one, filter by it
         $whereClause = " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
         $query = "SELECT * FROM wd_fields WHERE wd_id=".$wd_id." AND (LOWER(label)='enabled' OR LOWER(map)='enabled');";
         $results = $dbi->queryGetResults($query);
         if ($results != NULL && count($results)>0) $whereClause .= " AND UPPER(".$results[0]['field_id'].")= 'YES'";

         $questions = $this->getAllFieldsSystem($wd_id);
         $qs = NULL;
         for ($i=0; $i<count($questions); $i++) {
            $qs[$questions[$i]['field_id']] = $questions[$i]['label'];
         }

         $ss = new Version();
         $tag = $ss->getValue("tagdatacol");
         $tagbeg = $ss->getValue("tagstarter");
         $tagstart = $tagbeg.$tag."_";
         $tagend = "_".$tag.$tagbeg;

         $query = "select * from wd_".$wd_id.$whereClause.$orderby.";";
         $results = $dbi->queryGetResults($query);
         $search['results'] = $results;
         return $search;
   }

   function getCMSSearchCriteria($wd_id, $defaultLimit=25, $url=null, $pub=FALSE){
         //Possible Search HTTP parameters:
         //  Required to identify the table: view or action
         //  Filtering options: cmsz_*, cmsq_*, cmsh_*, cmsl_*, cmsrowid, cmslimit, cmspage, cmsorderby
         $dbi = new MYSQLAccess();
         $whereClause = "";

         $query = "SELECT * FROM wd_fields WHERE wd_id=".$wd_id." AND (LOWER(label)='enabled' OR LOWER(map)='enabled');";
         $results = $dbi->queryGetResults($query);
         if ($results != NULL && count($results)>0) $whereClause="LOWER(".$results[0]['field_id'].")= 'yes'";

         $searchArr = $this->getCMSSearchParams($wd_id);
         $urlSearch = $searchArr['url'];
         $searchParams = $searchArr['params'];
         if ($whereClause != null && $searchArr['where'] != null) $whereClause.=" AND ";
         $whereClause .= $searchArr['where'];

         $view = getParameter("view");
         $action = getParameter("action");
         $cmspage = getParameter("cmspage");
         if ($cmspage==null) $cmspage=1;
         $cmslimit = getParameter("cmslimit");
         if ($cmslimit == null) $cmslimit = $defaultLimit;

         if ($url==null) {
            $url = getBaseURL().$GLOBALS['codeFolder']."controller.php?view=".$view."&action=".$action;
         }
         $urlpage = "&cmspage=".$cmspage;
         $urllimit = "&cmslimit=".$cmslimit;

         $countResults = $this->getDataCount($wd_id,$whereClause);

         if (0==strcmp($cmslimit,"All")) {
            $limitStmnt = "";
            $totalPages = 0;
         }
         else {
            $totalPages = ceil($countResults/$cmslimit);
            $pageStart = $cmslimit*($cmspage - 1);
            $limitStmnt = " LIMIT " . $pageStart . "," . $cmslimit;
         }

         $questions = $this->getAllFields($wd_id);
         $qs = NULL;
         for ($i=0; $i<count($questions); $i++) {
            $qs[strtolower($questions[$i]['label'])] = $questions[$i]['field_id'];
         }

         $cmsorderby = getParameter("cmsorderby");
         if ($cmsorderby == null) {
            $query = "SELECT * FROM wd_fields WHERE wd_id=".$wd_id." AND (LOWER(label)='sequence' OR LOWER(map)='sequence');";
            $results = $dbi->queryGetResults($query);
            if ($results != NULL && count($results)>0) $cmsorderby = $results[0]['field_id']." ASC";
            else $cmsorderby = $questions[0]['field_id']." ASC";
         }         
         $urlorderby = "&cmsorderby=".$cmsorderby;

         $limitTable = "";
         if ($countResults>10) {
            $limitOpts['All']=$url.$urlSearch.$urlorderby."&cmslimit=All";
            $limitOpts['10'] =$url.$urlSearch.$urlorderby."&cmslimit=10";
            $limitOpts['25'] =$url.$urlSearch.$urlorderby."&cmslimit=25";
            $limitOpts['50'] =$url.$urlSearch.$urlorderby."&cmslimit=50";
            $limitOpts['100'] =$url.$urlSearch.$urlorderby."&cmslimit=100";
            $extra = "onChange=\"window.location.href=this.form.pageLimit.options[this.form.pageLimit.selectedIndex].value;\"";
   
            $limitTable="<table cellpadding=\"0\" cellspacing=\"0\"><tr><form action=\"form\"><TD align=\"left\">".$countResults." results, view  ";
            $limitTable.= getOptionList("pageLimit", $limitOpts, $cmslimit, false, $extra, true);
            $limitTable.=" at a time.</td></form></tr></table>";
         }
         else {
            $limitTable="<table cellpadding=\"0\" cellspacing=\"0\"><tr><TD align=\"left\">".$countResults." results.";
            $limitTable.="</td></form></tr></table>";            
         }

         $pageTable = "";
         if ($cmspage != null && $totalPages != null && $totalPages > 1) {
            $pageTable = "<table align=\"right\"><tr><td>Page: </td>";
            for ($i=1; $i<=$totalPages; $i++) {
               if ($cmspage == $i) $pageTable .= "<td bgcolor=\"#AAAAAA\"><b>".$i."</b></td>";
               else $pageTable .= "<td><a href=\"".$url.$urlSearch.$urlorderby.$urllimit."&cmspage=".$i."\">".$i."</a></td>";
            }
            if ($cmspage>1) $pageTable.="<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"".$url.$urlSearch.$urlorderby.$urllimit."&cmspage=".($cmspage-1)."\"><img src=\"".getBaseURL().$GLOBALS['adminFolder']."images/sprev.gif\" border=\"0\"></a></td>";
            else $pageTable.="<td>&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"".getBaseURL().$GLOBALS['adminFolder']."images/sprev_dis.gif\" broder=0></td>";
            if ($cmspage<$totalPages) $pageTable.="<td>&nbsp;<a href=\"".$url.$urlSearch.$urlorderby.$urllimit."&cmspage=".($cmspage+1)."\"><img src=\"".getBaseURL().$GLOBALS['adminFolder']."images/snext.gif\" border=\"0\"></a></td>";
            else $pageTable.="<td>&nbsp;<img src=\"".getBaseURL().$GLOBALS['adminFolder']."images/snext_dis.gif\" broder=0></td>";
            $pageTable .= "</tr></table>";
         }

         $query = "SELECT * FROM wd_".$wd_id;
         if ($pub) $query .= "_pub";
         $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
         if ($whereClause!=null) $query.= " AND ".$whereClause;
         $query .= " ORDER BY ".$cmsorderby.$limitStmnt.";";
         $results = $dbi->queryGetResults($query);
         $searchResults['results'] = $results;
         $searchResults['limittable'] = $limitTable;
         $searchResults['pagetable'] = $pageTable;
         $searchResults['url'] = $url;
         $searchResults['urlsearch'] = $urlSearch;
         $searchResults['urllimit'] = $urllimit;
         $searchResults['urlpage'] = $urlpage;
         $searchResults['urlorderby'] = $urlorderby;
         return $searchResults;
      }

   function getCMSSearchParamsOnly($wd_id, $defaultLimit=25, $url=null){
         //Possible Search HTTP parameters:
         //  Required to identify the table: view or action
         //  Filtering options: cmsz_*, cmsq_*, cmsh_*, cmsl_*, cmsrowid, cmslimit, cmspage, cmsorderby
         $searchArr = $this->getCMSSearchParams($wd_id);
         if ($url==null) $url = getBaseURL().$GLOBALS['codeFolder']."controller.php?view=".$view."&action=".$action;
         $cmslimit = getParameter("cmslimit");
         if ($cmslimit == null) $cmslimit = $defaultLimit;
         $searchResults['url'] = $url;
         $searchResults['urlsearch'] = $searchArr['url'];
         $searchResults['urllimit'] = "&cmslimit=".$cmslimit;
         $searchResults['urlpage'] = "&cmspage=".getParameter("cmspage");
         $searchResults['urlorderby'] = "&cmsorderby=".getParameter("cmsorderby");
         return $searchResults;
      }

      function getFullTable($wd_id,$url=null,$showEntries=false,$showLink=true,$shorten=true) {
         $questions = $this->getAllFields($wd_id);
         $params = array();
         $params[0] = "wd_row_id";
         for ($i=0; $i<count($questions); $i++) $params[($i+1)] = $questions[$i]['label'];
         if ($showEntries) {
            return $this->getDataTableEntryFields($wd_id,$params,$url,"small_table",$showLink);
         } else {
            return $this->getDataTable2($wd_id,$params,$url,"small_table",$showLink,$shorten);
         }
      }

      function getDataTable2($wd_id,$params,$url=null,$class=null,$showLink=false,$shorten=true) {
         //TODO: remove getDataTable() function call - no longer needed with this one
         $search = $this->getCMSSearchCriteria($wd_id,25,$url);
         $questions = $this->getAllFields($wd_id);
         $qs = NULL;
         $ts = NULL;
         for ($i=0; $i<count($questions); $i++) {
            $qs[strtolower($questions[$i]['label'])] = $questions[$i]['field_id'];
            $ts[strtolower($questions[$i]['label'])] = $questions[$i]['field_type'];
         }

         $table = "";
         $table .= "\n<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n<tr>\n";
         $table .= "<td>".$search['limittable']."</td><td>".$search['pagetable']."</td>";
         $table .= "</tr>\n</table>\n";
         $table .= "\n<table width=\"100%\" bgcolor=\"#888888\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" ";
         if ($class!=null) $table .= "class=\"".$class."\"";
         $table .= ">\n";
         $table .= "<tr bgcolor=\"#CCCCCC\">\n";
         for ($i=1; $i<count($params); $i++) {
            $table .= "<td><b><a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=".$qs[strtolower($params[$i])]."\">".$params[$i]."</a></b></td>\n";
         }
         $table .= "</tr>\n";

         $results = $search['results'];
         for ($i=0; $i<count($results); $i++) {
            $rowURL = $search['url']."&wd_row_id=".$results[$i]['wd_row_id'];
            $table .= "<tr bgcolor=\"#FFFFFF\">\n";
            for ($j=1; $j<count($params); $j++) {
               $table .= "<td>";
               $element = $results[$i][$qs[strtolower($params[$j])]];
               if (0==strcmp($ts[strtolower($params[$j])],"MONEY")) $element ="$".$element;
               else if (0==strcmp($ts[strtolower($params[$j])],"IMAGE")) {
                  $dirFilename = $GLOBALS['baseDir'].$GLOBALS['imagesFolder']."noimage.gif";
                  $urlFilename = getBaseURL().$GLOBALS['imagesFolder']."noimage.gif";
                  if ($element != null) {
                     $dirFilename = $GLOBALS['srvyDir'].$element;
                     $urlFilename = $GLOBALS['srvyURL'].$element;                     
                  }
                  $imageData = getHeightProportion($dirFilename, "100");
                  $element ="<img src=\"".$urlFilename."\" border=\"0\" width=\"".$imageData['width']."\" height=\"".$imageData['height']."\">";
               }
               else {
                  if ($shorten && strlen($element)>64) $element = substr($element,0,61)."...";
               }
               if ($showLink) $table .= "<a href=\"".$rowURL."\">".$element."</a></td>\n";
               else $table .= $element."</td>\n";
            }
            $table .= "</tr>\n";
         }
         $table .="</table>\n";

         return $table;
      }

   function doCOLSubs($first,$str,$last,$wd_id,$searchStr=null,$defaultLimit=25) {
      $result = "";
      $template = new Template();
      $ss = new Version();
      if (0==strcmp($searchStr,"search")) $search = $this->getCMSSearchCriteria($wd_id,$defaultLimit);
      else $search = $this->getCMSNoSearchCriteria($wd_id);
      $questions = $this->getAllFields($wd_id);
      $tag = $ss->getValue("tagtitlecol");
      $tagbeg = $ss->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $rs['wd_row_id'] = "cmsrowid";
      $qs['cmsrowid'] = "wd_row_id";
      $ts['cmsrowid'] = "INT";
      for ($i=0; $i<count($questions); $i++) {
         $rs[$questions[$i]['field_id']] = $questions[$i]['label'];
         $qs[$questions[$i]['label']] = $questions[$i]['field_id'];
         $ts[$questions[$i]['label']] = $questions[$i]['field_type'];
         $value = "<a href=\"".$search['url'].$search['urlsearch'].$search['urllimit']."&cmsorderby=".$questions[$i]['field_id']."\">".$questions[$i]['label']."</a>\n";
         $first = str_replace($tagstart.$questions[$i]['label'].$tagend,$value,$first);
         $last = str_replace($tagstart.$questions[$i]['label'].$tagend,$value,$last);
      }
      $first = str_replace($tagstart."pagetable".$tagend,$search['pagetable'],$first);
      $first = str_replace($tagstart."limittable".$tagend,$search['limittable'],$first);
      $last = str_replace($tagstart."pagetable".$tagend,$search['pagetable'],$last);
      $last = str_replace($tagstart."limittable".$tagend,$search['limittable'],$last);
      $tag = $ss->getValue("tagdatacol");
      $tagbeg = $ss->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $results = $search['results'];
      for ($i=0; $i<count($results); $i++) {
         $temp = $str;
         $value = $i % 2;
         $temp = str_replace($tagstart."classrowid".$tagend,$value,$temp);
         foreach ($results[$i] as $key => $value) {
               $element = $value;
               if (0==strcmp($ts[$rs[$key]],"MONEY")) $element="$".number_format($element,2,".",",");
               else if (0==strcmp($ts[$rs[$key]],"IMAGE")) {
                  $dirFilename = $GLOBALS['baseDir'].$GLOBALS['imagesDir']."noimage.gif";
                  $urlFilename = getBaseURL().$GLOBALS['imagesDir']."noimage.gif";
                  if ($element != null) {
                     $dirFilename = $GLOBALS['srvyDir'].$element;
                     $urlFilename = $GLOBALS['srvyURL'].$element;                     
                  }
                  $imageData = getHeightProportion($dirFilename, "100");
                  $element ="<img src=\"".$urlFilename."\" border=\"0\" width=\"".$imageData['width']."\" height=\"".$imageData['height']."\">";
               }
               $value = $element;
               $temp = str_replace($tagstart.$rs[$key].$tagend,$value,$temp);
         }
         $result .= $temp;
      }
      if ($result==NULL) {
         $result="Sorry, no results were found.";
         //foreach($qs as $key => $value) {            
         //   $result = str_replace($tagstart.$key.$tagend,"",$result);
         //}
         //$result = str_replace($tagstart."classrowid".$tagend,"",$result);
      }
      return $first.$result.$last;
   }

function printAdminSection($wd_id,$section=-1,$url=NULL,$sec_input=TRUE,$qtypes=NULL) {
   if ($url==NULL) $url = getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?action=webdata";
   $sections = $this->getDataSections($wd_id,$section);
   if ($section!=-1) {
      $s = $this->getSection($wd_id,$section);
      $allsections = $this->getAllDataSections($wd_id);
      $allsectionsopts['Main Sect'] = -1;
      for ($i=0; $i<count($allsections); $i++) $allsectionsopts['Sect '.$allsections[$i]['sequence']] = $allsections[$i]['section'];
      $sectsel = getOptionList("parent_s", $allsectionsopts, $s['parent_s']);
      $fields = $this->getFields($wd_id, $section);
      $qids = NULL;
      for ($i=0; $i<count($fields); $i++) $qids[$fields[$i]['field_id']] = $fields[$i]['label'];
      print "<a name=\"section".$section."\"></a>\n";
      print "<table width=\"98%\" cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#444444\"><tr><td>";
      print "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"2\" bgcolor=\"#FFFFFF\">\n";
      $dynamicSection = "";
      if ($s['dyna'] == 1) $dynamicSection = "CHECKED";
      ?>
        <?php if ($sec_input) { ?> 
           <TR bgcolor="#DDDDDD">
           <form action="<?php echo $url; ?>#section<?php echo $s['section']; ?>" name="surveySection<?php echo $s['section']; ?>" method="POST">
           <input type="hidden" name="wd_id" value="<?php echo $s['wd_id']; ?>">
           <input type="hidden" name="section" value="<?php echo $s['section']; ?>">
           <TD colspan="2">Section: <input type="text" size="1" name="sequence" value="<?php echo $s['sequence']; ?>"></td> 
           <td colspan="2">Title: <input type="text" size="40" name="label" value="<?php echo $s['label']; ?>"></td>
           </tr><tr>
           <td colspan="2"><input type="checkbox" name="dyna" value="1" <?php echo $dynamicSection; ?>>Dynamic</td>
           <td colspan="2">Question: <input type="text" name="question" value="<?php echo $s['question']; ?>" size="35"> </td>
           </tr><tr>
           <td colspan="3">Parent Section: <?php echo $sectsel; ?></td><td align="right"><input type="submit" name="Update" value="Update">
            <?php if (count($fields)==0 && count($sections)==0) { ?>
                <input type="submit" name="Delete" value="Delete">
            <?php } ?>
            </td>
           </form>
           </TR>
        <?php } ?>
            <tr><td colspan="4" align="center">
            <table width="96%" cellpadding="2" cellspacing="0" bgcolor="#EEEEEE">
      <?php
      unset($allsectionsopts['Main Sect']);
      $privacyList["Public"] = 0;
      $privacyList["Admin"] = -1;
      $privacyList["Superadmin"] = -2;
      $privacyList["Approved website users level 1"] = 1;
      $privacyList["Approved website users level 2"] = 2;
      $privacyList["Approved website users level 3"] = 3;
      $privacyList["Approved website users level 4"] = 4;
      $privacyList["Approved website users level 5"] = 5;
      $privacyList["Approved website users level 6"] = 6;
      $privacyList["Approved website users level 7"] = 7;
      $privacyList["Approved website users level 8"] = 8;
      $privacyList["Approved website users level 9"] = 9;
      $privacyList["Approved website users level 10"] = 10;
   
      for ($i=0; $i<count($fields); $i++) {
            $q = $fields[$i];
            $selected = NULL;
            $sectionDropDown = getOptionList("parent_s", $allsectionsopts, $q['parent_s']);
            $privacyDropDown = getOptionList("privacy", $privacyList, $q['privacy']);
            $rels = $this->getField1Rel($wd_id,$q['field_id']);
            $extra = " id=\"typeopt_".$q['field_id']."\" onChange=\"changeType('".$q['field_id']."');\"";
      ?>
            <tr bgcolor="#FFFFFF"><td colspan="4"><br></td></tr>
           <?php if ($sec_input) { ?> 
            <tr valign="top" bgcolor="#DDDDDD">
               <td colspan="4" align="center">Field: <?php echo $q['field_id']; ?></td>
            </tr>
           <?php } ?>
            <tr valign="top">
            <form action="<?php echo $url; ?>#section<?php echo $section; ?>" name="surveyQuestion<?php echo $q['field_id']; ?>" method="POST">
            <input type="hidden" name="wd_id" value="<?php echo $q['wd_id']; ?>">
            <input type="hidden" name="field_id" value="<?php echo $q['field_id']; ?>">
            <td colspan="3">
               Sequence: <input type="text" size="1" name="sequence" value="<?php echo $q['sequence']; ?>"> &nbsp; 
               Field label: <input type="text" size="30" name="label" value="<?php echo str_replace("\"","&#34;",convertBack($q['label'])); ?>">
            </td>
            <td>
               <?php if ($sec_input) { ?>
               <input type="checkbox" name="header" value="1" <?php if ($q['header']==1) print "CHECKED"; ?>>Show this field in header
               <?php } else { ?>
               <input type="hidden" name="header" value="<?php echo $q['header']; ?>">
               <?php } ?>
            </td>
            </tr><tr>
            <td colspan="2">Type: <?php echo $this->getQuestionOptions("field_type",$q['field_type'],$extra,$qtypes); ?> </td>
            <td colspan="2">
               <?php if ($sec_input) { ?>
               Privacy: <?php echo $privacyDropDown; ?> 
               <?php } else { ?>
               <input type="hidden" name="privacy" value="0">
               <?php } ?>
            </td>
            </tr><tr>
            <td colspan="4" id="val_label_<?php echo $q['field_id']; ?>" style="display: none;"></td>
            </tr><tr>
            <td colspan="4" id="values_<?php echo $q['field_id']; ?>" style="display: none;">
               <input type="text" size="100" name="question" value="<?php echo convertBack($q['question']); ?>"><br><br> 
            </td>
            </tr><tr>
            <td colspan="2">
               <table cellpadding="0" cellspacing="0"><tr>
               <?php if ($sec_input) { ?> 
                  <td>Parent Section: <?php echo $sectionDropDown; ?>&nbsp;&nbsp;</td>
               <?php } else { ?>
                  <input type="hidden" name="parent_s" value="<?php echo $q['parent_s']; ?>">
               <?php } ?>
               <td id="default_<?php echo $q['field_id']; ?>" style="display: none;">
               Default Value: <input type="text" size="10" name="defaultval" value="<?php echo convertBack($q['defaultval']); ?>"> 
               </td>
               </tr></table>
            </td>
            <td>
               <?php if ($sec_input) { ?>
               <input type="checkbox" name="required" value="1" <?php if ($q['required']==1) print "CHECKED"; ?>>Required Field
               <input type="checkbox" name="srchfld" value="1" <?php if ($q['srchfld']==1) print "CHECKED"; ?>>Search Field
               <input type="checkbox" name="filterfld" value="1" <?php if ($q['filterfld']==1) print "CHECKED"; ?>>Filter-by Field
               <?php } else { ?>
               <input type="hidden" name="required" value="<?php echo $q['required']; ?>">
               <input type="hidden" name="srchfld" value="<?php echo $q['srchfld']; ?>">
               <input type="hidden" name="filterfld" value="<?php echo $q['filterfld']; ?>">
               <?php } ?>
            </td>
            <td align="right"><input type="submit" name="Update" value="Update"> <input type="submit" name="Delete" value="Delete"></td>
            </form>
            </tr>
            <script type="text/javascript">addLoadEvent(changeType('<?php echo $q['field_id']; ?>'));</script>
      <?php for ($j=0; $j<count($rels); $j++) { ?>
            <tr><td colspan="4">
            <?php
               if ($sec_input) {
                  echo "If field [".$rels[$j]['fid1']."] is equal to '".$rels[$j]['f1value']."' then display field [".$rels[$j]['fid2']."]. ";
               } else {
                  echo "If question \"".$qids[$rels[$j]['fid1']]."\" is equal to '".$rels[$j]['f1value']."' then display question \"".$qids[$rels[$j]['fid2']]."\". ";
               }
            ?>
            <a href="<?php echo $url; ?>&wd_id=<?php echo $wd_id; ?>&deleteFieldRel=1&rel_id=<?php echo $rels[$j]['rel_id']; ?>#section<?php echo $q['parent_s']; ?>">Delete this rule</a>
            </td></tr>
      <?php
            }
      }
      print "</table></td></tr>\n";
      print "<tr><td colspan=\"4\" align=\"center\">\n";
   } else {
?>
            <script type="text/javascript">
               function changeType(fid) {
                  el = document.getElementById('typeopt_' + fid);
                  type = el.options[el.selectedIndex].value;
                  document.getElementById('default_' + fid).style.display = "";
                  document.getElementById('relexp_' + fid).style.display = "";
                  document.getElementById('values_' + fid).style.display = "";
                  document.getElementById('val_label_' + fid).style.display = "";
                  if (type=='CHECKBOX' || type=='HRZCHKBX' || type=='NEWCHKBX' || type=='MBL_MC' || type=='MBL_IMG' || type=='DROPDOWN' || type=='RADIO' || type=='POLLRADIO') {
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Selectable Values (separated by commas.  ie: Dogs,Cats,Birds,Cows):";
                  } else if (type=='VOTE') {
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Names of people/items to vote on separated by commas (ie John,Beth,Mike,Mary):";
                  } else if (type=='FOREIGN' || type=='FOREIGNCB' || type=='FOREIGNTBL') {
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Data table name, comma, field name. ie Cities,City Name ";
                  } else if (type=='TABLE') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Columns and rows format: header1,header2,(...);row1,row2,(...) ";
                  } else if (type=='NEWLIKERT') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Likert scale questions (separated by commas)";
                  } else if (type=='NEWPRCNT') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Percentage question rows to be ranked separated by commas. (ie, spent on groceries,spent on car,spent on house,spent on other):";
                  } else if (type=='IMAGE') {
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "<br>Use the default field to specify the max width and height of your image.  This is used to keep upload sizes smaller.";
                  } else if (type=='USERS') {
                     document.getElementById('val_label_' + fid).innerHTML = "<br>User Segment name:";
                  } else if (type=='COLOR' || type=='STATE' || type=='REGION' || type=='DATE' || type=='DATETIME' || type=='AGE' || type=='SNGLCHKBX' || type=='SITEOPT' || type=='SITELIST' || type=='TEXT' || type=='INT' || type=='DEC' || type=='MONEY' || type=='MBL_UPL') {
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "";
                  } else if (type=='INFO') {
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "";
                     document.getElementById('relexp_' + fid).style.display = "none";
                  } else if (type=='SPACER') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "";
                     document.getElementById('relexp_' + fid).style.display = "none";
                  } else if (type=='TEXTAREA' || type=='HTML' || type=='FILE' || type=='TABLE' || type=='NEWPRCNT') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "";
                  } else if (type==' ') {
                     document.getElementById('default_' + fid).style.display = "none";
                     document.getElementById('values_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).style.display = "none";
                     document.getElementById('val_label_' + fid).innerHTML = "";
                     document.getElementById('relexp_' + fid).style.display = "none";
                  } else {
                     document.getElementById('val_label_' + fid).innerHTML = "Values:";
                  }
               }

               function addLoadEvent(func) {
                  var oldonload = window.onload; 
                  if (typeof window.onload != 'function') { 
                     window.onload = func; 
                  } else { 
                     window.onload = function() { 
                        if (oldonload) { 
                           oldonload(); 
                        } 
                        func(); 
                     } 
                  } 
               } 

            </script>
<?php
   }

   //recursively print sections
   for ($i=0; $i<count($sections); $i++) $this->printAdminSection($wd_id,$sections[$i]['section'],$url,$sec_input,$qtypes);

   if ($section!=-1) {
      print "</td></tr></table>\n";
      print "</td></tr></table>\n";
   }
}






function printAdminSectionSmall($wd_id,$section=-1,$url=NULL,$sec_input=TRUE,$qtypes=NULL) {
      $userid = isLoggedOn();
      $ua = new UserAcct();
      $user = $ua->getUser($userid);
?>
      <script type="text/javascript">
         var updateArr = [];
         var updatepending = false;

         function updateStyleValue(id){
            var el = '';
            var stylestr = '';
            
            el = document.getElementById(id + '-st_single');
            if (el.checked) stylestr += 'display:none;';
            el = document.getElementById(id + '-st_bl');
            if (el.checked) stylestr += 'font-weight:bold;';
            el = document.getElementById(id + '-st_in');
            if (el.checked) stylestr += 'margin-left:40px;';
            el = document.getElementById(id + '-st_ab');
            if (el.checked) stylestr += 'margin-top:20px;';
            el = document.getElementById(id + '-st_be');
            if (el.checked) stylestr += 'margin-bottom:20px;';
            if (Boolean(jQuery('#' + id + '-st_cr').val())) stylestr += 'color:' + jQuery('#' + id + '-st_cr').val() + ';';
            if (Boolean(jQuery('#' + id + '-st_fz').val())) stylestr += 'font-size:' + jQuery('#' + id + '-st_fz').val() + ';';
            //alert('style string: ' + stylestr);
            jQuery('#' + id + '-style').val(stylestr);
            
            updateValue(id + '-style');
         }

         function updateValue(key){
            var addkey = true;
            for (var i=0;i<updateArr.length;i++) {
               if (updateArr[i]==key) addkey = false;
            }
            if (addkey) updateArr.push(key);

            if (!updatepending) setPendingUpdates();
            //alert(updateArr);
            //alert(key + ' was updated');
         }

         function updateWebsiteData(){
            var updateURL = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=updatewebdata&wd_id=<?php echo $wd_id; ?>&userid=<?php echo $userid; ?>&token=<?php echo $user["token"]; ?>';
            for (var i=0;i<updateArr.length;i++) {
               var el = document.getElementById(updateArr[i]);
               if(el.type=='checkbox') {
                  if (el.checked) updateURL = updateURL + '&' + encodeURIComponent(updateArr[i]) + '=' + encodeURIComponent($('#' + updateArr[i]).val());
                  else updateURL = updateURL + '&' + encodeURIComponent(updateArr[i]) + '=0';
               } else {
                  var val = $('#' + updateArr[i]).val();
                  if (!Boolean(val)) val='&nbsp;';
                  updateURL = updateURL + '&' + encodeURIComponent(updateArr[i]) + '=' + encodeURIComponent(val);
               }
            }

            //alert(updateURL);
            jsf_json_sendRequest(updateURL,updateWebsiteDataResult);
            //var jsondata = [];
            //jsondata['responsecode']=1;
            //updateWebsiteDataResult(jsondata);
         }

         function updateWebsiteDataResult(jsondata){
            var failed=true;
            if (Boolean(jsondata.responsecode)) {
               if (jsondata.responsecode==1) {
                  setNoPendingUpdates();
                  failed = false;
               }
            }

            if (failed) {
               alert('Your form was not updated.  There was an internal error or a network connectivity problem.');
            } else {
               updateArr = [];
               alert('Your form was updated successfully.');
            }
         }

         function removeFieldRel(frid){
            if (confirm('Are you sure you want to permanently delete this question relationship?')) {
               var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=removewdfieldrel&rel_id=' + frid;
               //alert("url: " + url);
               jsf_json_sendRequest(url,removeFieldRelResult);
            }
         }

         function removeFieldRelResult(jsondata){
            var failed=true;
            if (Boolean(jsondata.responsecode)) {
               if (jsondata.responsecode==1) {
                  if (Boolean(jsondata.rel_id)) {
                     $('#fieldrel' + jsondata.rel_id).html('');
                     failed = false;
                  }
               }
            } 

            if(failed) alert('We could not remove that question relationship.  Please check and try again later.');
         }

         function deleteQuestion(field_id){
            if (confirm('Are you sure you want to permanently delete this question?')) {
               var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=removewdquestion&wd_id=<?php echo $wd_id; ?>&field_id=' + field_id;
               //alert("url: " + url);
               jsf_json_sendRequest(url,deleteQuestionResult);
            }
         }

         function deleteQuestionResult(jsondata){
            var failed=true;
            if (Boolean(jsondata.responsecode)) {
               if (jsondata.responsecode==1) {
                  if (Boolean(jsondata.field_id)) {
                     failed = false;
                     var temparr = [];
                     var quest = jsondata.field_id;
                     $('#' + quest + '_row').hide();
                     $('#' + quest + '_question').hide();
                     $('#' + quest + '_notes').hide();
                     $('#' + quest + '_map').hide();
                     if (Boolean(updateArr)) {
                        for (var i=0;i<updateArr.length;i++) {
                           var spl = updateArr[i].split("-");
                           if (spl[0]!=quest) temparr.push(updateArr[i]);
                        }
                        if (!Boolean(temparr) || temparr.length<1) {
                           updateArr = [];
                           setNoPendingUpdates();
                        } else {
                           updateArr = temparr;
                        }
                     }
                  }
               }
            } 

            if(failed) alert('We could not remove that question.  Please check and try again later.');
         }

         function setPendingUpdates(){
            updatepending = true;
            $('#s-1_outertable').css('background-color','#f41919');
            $('.sectionupdatebutton').show();
            $('.sectioncancelbutton').show();
            //$('.questionupdatebutton').show();            
         }

         function setNoPendingUpdates(){
            updateArr = [];
            updatepending=false;
            $('#s-1_outertable').css('background-color','#444444');
            $('.sectionupdatebutton').hide();
            //$('.questionupdatebutton').hide();            
            $('.sectioncancelbutton').hide();
         }

         function refreshScreen(){
            var loadpage = false;
            if(updatepending) {
               if (confirm('Are you sure you want to cancel your changes?')) loadpage=true;
            } else {
               loadpage=true;
            }

            if (loadpage) location.href='<?php echo getBaseURL(); ?>jsfadmin/admincontroller.php?action=webdata&wd_id=<?php echo $wd_id; ?>';
         }

         function copySection(section){
            if(updatepending) {
               alert('You have pending updates.  Please either save or cancel your changes before trying to copy a section.');
            } else if (confirm('Are you sure you want to copy this entire section and all elements contained within it?')) {
               var url = '<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>admincontroller.php?action=webdata&copySection=1&wd_id=<?php echo $wd_id; ?>&section=' + section;
               location.href = url;
            }
         }

         function deleteSection(section){
            if (confirm('Are you sure you want to permanently delete this section?')) {
               var url = '<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>jsoncontroller.php?action=removewdsection&wd_id=<?php echo wd_id; ?>&section=' + section;
               //alert("url: " + url);
               jsf_json_sendRequest(url,deleteSectionResult);
            }
         }

         function deleteSectionResult(jsondata){
            var failed=true;
            if (Boolean(jsondata.responsecode)) {
               if (jsondata.responsecode==1) {
                  if (Boolean(jsondata.section)) {
                     $('#s' + jsondata.section + '_outertable').html('');
                     failed = false;
                     if (Boolean(updateArr)) {
                        var temparr = [];
                        var sect = 's' + jsondata.section;
                        for (var i=0;i<updateArr.length;i++) {
                           var spl = updateArr[i].split("-");
                           if (spl[0]!=sect) temparr.push(updateArr[i]);
                        }
                        if (!Boolean(temparr) || temparr.length<1) {
                           setNoPendingUpdates();
                        } else {
                           updateArr = temparr;
                        }
                     }
                  }
               }
            } 

            if(failed) alert('We could not remove that section.  Please check and try again later.');
         }

         function changeType(fid) {
            var el = document.getElementById(fid + '-field_type');
            var type = el.options[el.selectedIndex].value;
            var val_lbl = 'Values';
            document.getElementById('label_' + fid).style.display = "";
            document.getElementById('default_' + fid).style.display = "";
            document.getElementById('values_' + fid).style.display = "";
            if (type=='NEWCHKBX' || type=='RADIO') {
               val_lbl = 'Values: [optional # horiz across];[selectable options];[values if diff from option names]';
            } else if (type=='MBL_MC' || type=='MBL_IMG') {
            } else if (type=='CHECKBOX' || type=='HRZCHKBX' || type=='DROPDOWN') {
            } else if (type=='POLLRADIO') {
            } else if (type=='VOTE') {
            } else if (type=='FOREIGNTDD' || type=='FOREIGNTBL') {
               val_lbl = 'Values: [Name of table],[Column for name/label],[Column for value/id]';
            } else if (type=='FOREIGN' || type=='FOREIGNCB') {
               val_lbl = 'Values: [Name/id of jData/survey],[name of field to be used]';
            } else if (type=='FOREIGNSRY' || type=='FOREIGNSCT' || type=='FOREIGNHYB') {
               val_lbl = 'Values: [Name/id of jData/survey];[optional row labels or # rows];[\'Remove\' for soft delete or \'Delete\' for hard delete (default)]';
            } else if (type=='TABLE') {
               document.getElementById('default_' + fid).style.display = "none";
            } else if (type=='NEWLIKERT') {
               document.getElementById('default_' + fid).style.display = "none";
            } else if (type=='NEWPRCNT') {
               document.getElementById('default_' + fid).style.display = "none";
            } else if (type=='IMAGE') {
               document.getElementById('values_' + fid).style.display = "none";
            } else if (type=='USERS') {
            } else if (type=='STATE' || type=='REGION' || type=='DATE' || type=='DATETIME' || type=='AGE' || type=='SNGLCHKBX' || type=='SITEOPT' || type=='SITELIST' || type=='TEXT' || type=='INT' || type=='DEC' || type=='MONEY') {
               document.getElementById('values_' + fid).style.display = "none";
            } else if (type=='INFO') {
               document.getElementById('values_' + fid).style.display = "none";
            } else if (type=='SPACER') {
               document.getElementById('values_' + fid).style.display = "none";
               document.getElementById('label_' + fid).style.display = "none";
            } else if (type=='TEXTAREA') {
               document.getElementById('default_' + fid).style.display = "none";
               val_lbl = 'Value of height in pixels for input box (default 150)';
            } else if (type=='HTML' || type=='FILE' || type=='TABLE' || type=='NEWPRCNT') {
               document.getElementById('default_' + fid).style.display = "none";
               document.getElementById('values_' + fid).style.display = "none";
            } else if (type==' ') {
               document.getElementById('default_' + fid).style.display = "none";
               document.getElementById('values_' + fid).style.display = "none";
            }
            document.getElementById('valueslbl_' + fid).innerHTML = val_lbl;
         }

         function addLoadEvent(func) {
            var oldonload = window.onload; 
            if (typeof window.onload != 'function') { 
               window.onload = func; 
            } else { 
               window.onload = function() { 
                  if (oldonload) { 
                     oldonload(); 
                  } 
                  func(); 
               } 
            } 
         } 

      </script>

<?php
   $this->printAdminSectionSmall_recur($wd_id,$section,$url,$sec_input,$qtypes);
}

function printAdminSectionSmall_recur($wd_id,$section=-1,$url=NULL,$sec_input=TRUE,$qtypes=NULL) {
   if ($url==NULL) $url = getBaseURL().$GLOBALS['adminFolder']."admincontroller.php?action=webdata";
   $sections = $this->getDataSections($wd_id,$section);


      $s = $this->getSection($wd_id,$section);
      $allsections = $this->getAllDataSections($wd_id);
      $allsectionsopts['Main Sect'] = -1;
      for ($i=0; $i<count($allsections); $i++) $allsectionsopts['Sect '.$allsections[$i]['sequence']] = $allsections[$i]['section'];
      $sextra = " style=\"font-size:12px;font-family:arial;\" id=\"s".$s['section']."-parent_s\" onChange=\"updateValue('s".$s['section']."-parent_s');\"";
      $sectsel = getOptionList("parent_s", $allsectionsopts, $s['parent_s'], FALSE, $sextra);
      $fields = $this->getFields($wd_id, $section);
      $qids = NULL;
      for ($i=0; $i<count($fields); $i++) $qids[$fields[$i]['field_id']] = $fields[$i]['label'];
      print "<a name=\"section".$section."\"></a>\n";
      print "<div style=\"height:5px;width:5px;overflow:hidden;\"></div>";
      print "<table cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#444444\" id=\"s".$section."_outertable\"><tr><td>";
      if ($section==-1) {
?>         
               <div style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;" onclick="refreshScreen();">Refresh</div>
         </td></tr><tr><td>
         <script>
         function wd_highlight_reltype(f1,v,f2,ref) {
            var w = '<?php echo $wd_id; ?>';
            var c1 = '';
            if(Boolean(ref)) c1 += 'r';
            c1 += 'cl' + w + f1;
            jQuery('.' + c1).css('font-weight','normal').css('color','#000000');
            if(Boolean(v)) {
               var c2 = c1 + 'v' + v;
               jQuery('.' + c2).css('font-weight','bold').css('color','#FF0000');
            }
            if(Boolean(f2)) {
               var c3 = c1 + 'f' + f2;
               jQuery('.' + c3).css('font-weight','bold').css('color','#FF0000');
            }
         }
         </script>
<?php
      }
      print "<table cellpadding=\"2\" cellspacing=\"2\" bgcolor=\"#FFFFFF\">\n";
      $dynamicSection = "";
      if ($s['dyna'] == 1) $dynamicSection = "CHECKED";
      ?>
        <?php if ($sec_input && $section!=-1) { ?> 
           <TR bgcolor="#b8d0f1" id="s<?php echo $s['section']; ?>_sectionrow">
           <!-- form action="<?php echo $url; ?>#section<?php echo $s['section']; ?>" name="surveySection<?php echo $s['section']; ?>" method="POST" -->
           <!-- input type="hidden" name="wd_id" value="<?php echo $s['wd_id']; ?>" -->
           <!-- input type="hidden" name="section" value="<?php echo $s['section']; ?>" -->
            <TD>Sect: <input type="text" style="width:20px;font-size:10px;" name="sequence" value="<?php echo $s['sequence']; ?>" id="s<?php echo $s['section']; ?>-sequence" onkeyup="updateValue('s<?php echo $s['section']; ?>-sequence');"></td>
            <td>Title: <input type="text" style="width:220px;font-size:10px;" name="label" value="<?php echo $s['label']; ?>" id="s<?php echo $s['section']; ?>-label" onkeyup="updateValue('s<?php echo $s['section']; ?>-label');"></td>
            <td><input type="checkbox" name="dyna" value="1" id="s<?php echo $s['section']; ?>-dyna" onClick="updateValue('s<?php echo $s['section']; ?>-dyna');" <?php echo $dynamicSection; ?>>Dynamic</td>
            <td>Question: <input type="text" name="question" value="<?php echo $s['question']; ?>" style="width:220px;font-size:10px;" id="s<?php echo $s['section']; ?>-question" onkeyup="updateValue('s<?php echo $s['section']; ?>-question');"></td>
            <td>Parent: <?php echo $sectsel; ?></td>
            <TD>Param: <input type="text" style="width:20px;font-size:10px;" name="param1" value="<?php echo $s['param1']; ?>" id="s<?php echo $s['section']; ?>-param1" onkeyup="updateValue('s<?php echo $s['section']; ?>-param1');"></td>
            <td align="right">
               <table cellpadding="0" cellspacing="4"><tr>
               <td>
               <div class="sectionupdatebutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;" onclick="updateWebsiteData();">Update</div>
               <!-- input type="submit" name="Update" value="Update" -->
               </td><td>
               <div class="sectioncancelbutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;" onclick="refreshScreen();">Cancel</div>
               </td>
            <?php if (count($fields)==0 && count($sections)==0) { ?>
               <td>
               <div class="sectiondeletebutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;" onclick="deleteSection('<?php echo $s['section']; ?>');">Delete</div>
                <!-- input type="submit" name="Delete" value="Delete" -->
               </td>
            <?php } else { ?>
               <td>
               <div class="sectioncopybutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;" onclick="copySection('<?php echo $s['section']; ?>');">Copy</div>
               <div style="cursor:pointer;color:blue;font-size:8px;" onclick="jQuery('#s<?php echo $s['section']; ?>_sectionrowxml').show();">view xml</div>
               </td>            
            <?php } ?>
            
               </tr></table>
            </td>
           <!-- /form -->
           </TR>
           <TR bgcolor="#b8d0f1" id="s<?php echo $s['section']; ?>_sectionrowxml" style="display:none;">
           <td colspan="7">
           <textarea style="font-size:10px;width:400px;height:200px;"><?php echo $this->getSectionOutputXMLForCopy($wd_id,$section); ?></textarea>
           </td>
           </TR>
        <?php } ?>

    <?php if (count($fields)>0) { ?>
      <tr><td colspan="10">
      <table cellpadding="1" cellspacing="1" bgcolor="#EEEEEE">
      <tr bgcolor="#CCCCCC" style="font-size:12px;font-family:arial;">
         <?php if ($sec_input) { ?> 
            <td>Field</td>
            <td>Sect</td>
         <?php } ?>
         <td>Sequence</td>
         <!--td>Label</td-->   
         <td>Type</td>
         <?php if ($sec_input) { ?>
            <td>Access</td>
            <td>Header</td>
            <td>Reqd</td>
            <td>Search</td>
            <td>Filter</td>
         <?php } ?>
         <td>Default</td>
         <td></td>
      </tr>

      <?php
         unset($allsectionsopts['Main Sect']);
         $privacyList["Public"] = 0;
         $privacyList["Admin"] = -1;
         $privacyList["Superadmin"] = -2;
         $privacyList["Approved website users level 1"] = 1;
         $privacyList["Approved website users level 2"] = 2;
         $privacyList["Approved website users level 3"] = 3;
         $privacyList["Approved website users level 4"] = 4;
         $privacyList["Approved website users level 5"] = 5;
         $privacyList["Approved website users level 6"] = 6;
         $privacyList["Approved website users level 7"] = 7;
         $privacyList["Approved website users level 8"] = 8;
         $privacyList["Approved website users level 9"] = 9;
         $privacyList["Approved website users level 10"] = 10;

         //$colspan=6;
         //if ($sec_input) $colspan=12;
         $colspan=5;
         if ($sec_input) $colspan=11;
   
         for ($i=0; $i<count($fields); $i++) {
            $q = $fields[$i];
            $selected = NULL;

            $sectionDropDown = getOptionList("parent_s", $allsectionsopts, $q['parent_s'], FALSE, " style=\"font-size:12px;font-family:arial;\" id=\"".$q['field_id']."-parent_s\" onChange=\"updateValue('".$q['field_id']."-parent_s');\"");
            $privacyDropDown = getOptionList("privacy", $privacyList, $q['privacy'], FALSE, " style=\"font-size:12px;font-family:arial;\" id=\"".$q['field_id']."-privacy\" onChange=\"updateValue('".$q['field_id']."-privacy');\"");
            $rels = $this->getNakedField1Rel($wd_id,$q['field_id']);
            $refrels = $this->getNakedField1Rel($wd_id,$q['field_id'],NULL,TRUE);
            //print "<br>\n<br>\n";
            //print_r($refrels);
            //print "<br>\n<br>\n";
            $extra = " style=\"font-size:12px;font-family:arial;\" id=\"".$q['field_id']."-field_type\" onChange=\"changeType('".$q['field_id']."');updateValue('".$q['field_id']."-field_type');\"";
      ?>
            <!-- form action="<?php echo $url; ?>#section<?php echo $section; ?>" name="surveyQuestion<?php echo $q['field_id']; ?>" method="POST" -->
            <!-- input type="hidden" name="wd_id" value="<?php echo $q['wd_id']; ?>" -->
            <!-- input type="hidden" name="field_id" value="<?php echo $q['field_id']; ?>" -->
           
            <tr valign="top" id="<?php echo $q['field_id']; ?>_row" style="font-size:12px;font-family:arial;">

               <?php if ($sec_input) { ?> 
                  <td><?php echo $q['field_id']; ?></td>
                  <td><?php echo $sectionDropDown; ?></td>
               <?php } else { ?>
                  <input type="hidden" name="parent_s" value="<?php echo $q['parent_s']; ?>">
               <?php } ?>
   
               <td><input type="text" name="sequence" value="<?php echo $q['sequence']; ?>" style="font-size:12px;font-family:arial;width:30px;" id="<?php echo $q['field_id']; ?>-sequence" onkeyup="updateValue('<?php echo $q['field_id']; ?>-sequence');"></td>
               <!--td><input type="text" name="label" value="<?php echo str_replace("\"","&#34;",convertBack($q['label'])); ?>" style="font-size:12px;font-family:arial;width:150px;" id="<?php echo $q['field_id']; ?>-label" onkeyup="updateValue('<?php echo $q['field_id']; ?>-label');"></td-->
               <td><?php echo $this->getQuestionOptions("field_type",$q['field_type'],$extra,$qtypes); ?></td>

               <?php if ($sec_input) { ?>
                  <td><?php echo $privacyDropDown; ?></td>
                  <td><input type="checkbox" name="header" value="1" id="<?php echo $q['field_id']; ?>-header" onClick="updateValue('<?php echo $q['field_id']; ?>-header');" <?php if ($q['header']==1) print "CHECKED"; ?>></td>
                  <td><input type="checkbox" name="required" value="1" id="<?php echo $q['field_id']; ?>-required" onClick="updateValue('<?php echo $q['field_id']; ?>-required');" <?php if ($q['required']==1) print "CHECKED"; ?>></td>
                  <td><input type="checkbox" name="srchfld" value="1" id="<?php echo $q['field_id']; ?>-srchfld" onClick="updateValue('<?php echo $q['field_id']; ?>-srchfld');" <?php if ($q['srchfld']==1) print "CHECKED"; ?>></td>
                  <td><input type="checkbox" name="filterfld" value="1" id="<?php echo $q['field_id']; ?>-filterfld" onClick="updateValue('<?php echo $q['field_id']; ?>-filterfld');" <?php if ($q['filterfld']==1) print "CHECKED"; ?>></td>
               <?php } else { ?>
                  <input type="hidden" name="privacy" value="0">
                  <input type="hidden" name="header" value="<?php echo $q['header']; ?>">
                  <input type="hidden" name="required" value="<?php echo $q['required']; ?>">
                  <input type="hidden" name="srchfld" value="<?php echo $q['srchfld']; ?>">
                  <input type="hidden" name="filterfld" value="<?php echo $q['filterfld']; ?>">
               <?php } ?>

               <td>
                  <div id="default_<?php echo $q['field_id']; ?>" style="display: none;">
                  <input type="text" name="defaultval" value="<?php echo convertBack($q['defaultval']); ?>" style="font-size:12px;font-family:arial;width:100px;" id="<?php echo $q['field_id']; ?>-defaultval" onkeyup="updateValue('<?php echo $q['field_id']; ?>-defaultval');">
                  </div>
               </td>

               <td colspan="<?php echo $colspan; ?>" align="right">
                  <table cellpadding="0" cellspacing="1"><tr align="right">
                  <!-- td>
                  <div class="questionupdatebutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;" onclick="updateWebsiteData();">Update</div>
                  </td --><td>
                  <div class="questiondeletebutton" style="cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;" onclick="deleteQuestion('<?php echo $q['field_id']; ?>');">Delete</div>
                  </td>
                  </tr></table>
                  <!-- input type="submit" name="Update" value="Update" style="font-size:12px;font-family:arial;" -->
                  <!-- input type="submit" name="Delete" value="Delete" style="font-size:12px;font-family:arial;" -->
               </td>

            </tr>
            
            <tr valign="top" id="valandlabel_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
            <td></td>
            <td colspan="<?php echo ($colspan-1); ?>">
              <table cellpadding="0" cellspacing="0">
              <tr valign="top">
              <td>
              <span id="label_<?php echo $q['field_id']; ?>" style="margin-right:20px;">
              <div style="background-color:#EEEEEE;font-size:10px;">Label</div>
              <textarea style="font-size:10px;font-family:arial;width:380px;height:60px;" id="<?php echo $q['field_id']; ?>-label" onkeyup="updateValue('<?php echo $q['field_id']; ?>-label');"><?php echo str_replace("\"","&#34;",convertBack($q['label'])); ?></textarea>
              </span>
              </td>
              <td>
              <span id="values_<?php echo $q['field_id']; ?>" style="display:none;">
              <div style="background-color:#EEEEEE;font-size:10px;" id="valueslbl_<?php echo $q['field_id']; ?>">Values</div>
              <textarea style="font-size:10px;font-family:arial;width:380px;height:60px;" id="<?php echo $q['field_id']; ?>-question" onkeyup="updateValue('<?php echo $q['field_id']; ?>-question');"><?php echo convertBack($q['question']); ?></textarea>
              </span>
              </td>
              </tr>
              </table>
            </td>
            </tr>
            
            <!--tr valign="top" id="values_<?php echo $q['field_id']; ?>" style="display:none;font-size:12px;font-family:arial;">
               <td></td>
               <td>Values:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="<?php echo $q['field_id']; ?>_question" style="font-size:12px;font-family:arial;">
               <input type="text" name="question" value="<?php echo convertBack($q['question']); ?>" style="font-size:12px;font-family:arial;width:700px;" id="<?php echo $q['field_id']; ?>-question" onkeyup="updateValue('<?php echo $q['field_id']; ?>-question');">
               </td>
            </tr-->

            <tr valign="top" id="<?php echo $q['field_id']; ?>_exprow" style="font-size:12px;font-family:arial;">
               <td></td>
               <td colspan="<?php echo ($colspan-1); ?>" id="exp_<?php echo $q['field_id']; ?>">
               <script>
               function relexp_<?php echo $q['field_id']; ?>_toggle(){
                  var tdivid='#relexp_<?php echo $q['field_id']; ?>';
                  if(jQuery(tdivid).html()=='+') {
                     jQuery('#<?php echo $q['field_id']; ?>_notes').show();
                     jQuery('#<?php echo $q['field_id']; ?>_map').show();
                     jQuery('#<?php echo $q['field_id']; ?>_strow').show();
                     jQuery('#<?php echo $q['field_id']; ?>_flagrow').show();
                     jQuery('#<?php echo $q['field_id']; ?>_relrow').show();
                     jQuery('.refrel<?php echo $q['field_id']; ?>').show();
                     jQuery(tdivid).html('-');
                  } else {
                     jQuery('#<?php echo $q['field_id']; ?>_notes').hide();
                     jQuery('#<?php echo $q['field_id']; ?>_map').hide();
                     jQuery('#<?php echo $q['field_id']; ?>_strow').hide();
                     jQuery('#<?php echo $q['field_id']; ?>_flagrow').hide();
                     jQuery('#<?php echo $q['field_id']; ?>_relrow').hide();
                     jQuery('.refrel<?php echo $q['field_id']; ?>').hide();
                     jQuery(tdivid).html('+');
                  }
               }
               </script>
               <span id="relexp_<?php echo $q['field_id']; ?>" onclick="relexp_<?php echo $q['field_id']; ?>_toggle();" style="font-size:20px;font-family:arial;cursor:pointer;">+</span>
               <span onclick="relexp_<?php echo $q['field_id']; ?>_toggle();" style="font-size:14px;font-family:arial;cursor:pointer;">Additional Settings</span>
               </td>
            </tr>
            <tr valign="top" id="<?php echo $q['field_id']; ?>_notes" style="font-size:12px;font-family:arial;display:none;">
               <td></td>
               <td>Notes:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="notes_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
               <input type="text" name="notes" value="<?php echo convertBack($q['notes']); ?>" style="font-size:12px;font-family:arial;width:700px;" id="<?php echo $q['field_id']; ?>-notes" onkeyup="updateValue('<?php echo $q['field_id']; ?>-notes');">
               </td>
            </tr>
            
            <tr valign="top" id="<?php echo $q['field_id']; ?>_map" style="font-size:12px;font-family:arial;display:none;">
               <td></td>
               <td>Mapping Name:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="map_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
               <input type="text" name="map" value="<?php echo convertBack($q['map']); ?>" style="font-size:12px;font-family:arial;width:120px;" id="<?php echo $q['field_id']; ?>-map" onkeyup="updateValue('<?php echo $q['field_id']; ?>-map');">
               </td>
            </tr>
            
            <tr valign="top" id="<?php echo $q['field_id']; ?>_strow" style="font-size:12px;font-family:arial;display:none;">
               <td></td>
               <td>Styling:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="style_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
                  <div style="clear:both;">
                  <input type="hidden" name="style" id="<?php echo $q['field_id']; ?>-style" value="">
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="single" id="<?php echo $q['field_id']; ?>-st_single" value="1" onClick="updateStyleValue('<?php echo $q['field_id']; ?>');" <?php if (strpos($q['stylecss'],"display:none;")!==FALSE) print "CHECKED"; ?>> Hide Label</div>
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="bold" id="<?php echo $q['field_id']; ?>-st_bl" value="1" onClick="updateStyleValue('<?php echo $q['field_id']; ?>');" <?php if (strpos($q['stylecss'],"font-weight:bold;")!==FALSE) print "CHECKED"; ?>> Bold</div>
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="indent" id="<?php echo $q['field_id']; ?>-st_in" value="1" onClick="updateStyleValue('<?php echo $q['field_id']; ?>');" <?php if (strpos($q['stylecss'],"margin-left:40px;")!==FALSE) print "CHECKED"; ?>> Indent</div>
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="top" id="<?php echo $q['field_id']; ?>-st_ab" value="1" onClick="updateStyleValue('<?php echo $q['field_id']; ?>');" <?php if (strpos($q['stylecss'],"margin-top:20px;")!==FALSE) print "CHECKED"; ?>> Space Above</div>
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="bot" id="<?php echo $q['field_id']; ?>-st_be" value="1" onClick="updateStyleValue('<?php echo $q['field_id']; ?>');" <?php if (strpos($q['stylecss'],"margin-bottom:20px;")!==FALSE) print "CHECKED"; ?>> Space Below</div>
                  <div style="float:left;margin-right:10px;">
                     <select name="fclr" id="<?php echo $q['field_id']; ?>-st_cr" onchange="updateStyleValue('<?php echo $q['field_id']; ?>');">
                     <option value="#333333" <?php if (strpos($q['stylecss'],"color:#333333;")!==FALSE) print "SELECTED"; ?>>Black font</option>
                     <option value="#AAAAAA" <?php if (strpos($q['stylecss'],"color:#AAAAAA;")!==FALSE) print "SELECTED"; ?>>Grey font</option>
                     <option value="#DD2222" <?php if (strpos($q['stylecss'],"color:#DD2222;")!==FALSE) print "SELECTED"; ?>>Red font</option>
                     <option value="#2222DD" <?php if (strpos($q['stylecss'],"color:#2222DD;")!==FALSE) print "SELECTED"; ?>>Blue font</option>
                     <option value="#009900" <?php if (strpos($q['stylecss'],"color:#009900;")!==FALSE) print "SELECTED"; ?>>Green</option>
                     <option value="#006400" <?php if (strpos($q['stylecss'],"color:#006400;")!==FALSE) print "SELECTED"; ?>>Dark Green</option>
                     <option value="#f89b21" <?php if (strpos($q['stylecss'],"color:#f89b21;")!==FALSE) print "SELECTED"; ?>>Orange</option>
                     <option value="#68bcae" <?php if (strpos($q['stylecss'],"color:#68bcae;")!==FALSE) print "SELECTED"; ?>>Turquoise</option>
                     </select>
                  </div>
                  <div style="float:left;margin-right:10px;">
                     <select name="fnsz" id="<?php echo $q['field_id']; ?>-st_fz" onchange="updateStyleValue('<?php echo $q['field_id']; ?>');">
                     <option value="">Default</option>
                     <option value="10px" <?php if (strpos($q['stylecss'],"font-size:10px;")!==FALSE) print "SELECTED"; ?>>10px</option>
                     <option value="12px" <?php if (strpos($q['stylecss'],"font-size:12px;")!==FALSE) print "SELECTED"; ?>>12px</option>
                     <option value="14px" <?php if (strpos($q['stylecss'],"font-size:14px;")!==FALSE) print "SELECTED"; ?>>14px</option>
                     <option value="16px" <?php if (strpos($q['stylecss'],"font-size:16px;")!==FALSE) print "SELECTED"; ?>>16px</option>
                     <option value="18px" <?php if (strpos($q['stylecss'],"font-size:18px;")!==FALSE) print "SELECTED"; ?>>18px</option>
                     <option value="22px" <?php if (strpos($q['stylecss'],"font-size:22px;")!==FALSE) print "SELECTED"; ?>>22px</option>
                     <option value="30px" <?php if (strpos($q['stylecss'],"font-size:30px;")!==FALSE) print "SELECTED"; ?>>30px</option>
                     </select>
                  </div>
                  <div style="clear:both;"></div>
                  </div>
               </td>
            </tr>

            <tr valign="top" id="<?php echo $q['field_id']; ?>_flagrow" style="font-size:12px;font-family:arial;display:none;">
               <td></td>
               <td>Flags:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="flag_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
                  <div style="clear:both;">
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="disa" id="<?php echo $q['field_id']; ?>-disa" value="1" onClick="updateValue('<?php echo $q['field_id']; ?>-disa');" <?php if ($q['disa']==1) print "CHECKED"; ?>> Read-Only</div>
                  <div style="float:left;margin-right:10px;"><input type="checkbox" name="hide" id="<?php echo $q['field_id']; ?>-hide" value="1" onClick="updateValue('<?php echo $q['field_id']; ?>-hide');" <?php if ($q['hide']==1) print "CHECKED"; ?>> Disable from view</div>
                  <div style="clear:both;"></div>
                  </div>
               </td>
            </tr>

            <tr valign="top" id="<?php echo $q['field_id']; ?>_relrow" style="font-size:12px;font-family:arial;display:none;">
               <td></td>
               <td>New QR:</td>
               <td colspan="<?php echo ($colspan-2); ?>" id="rel_<?php echo $q['field_id']; ?>" style="font-size:12px;font-family:arial;">
                  <script>
                     function submitnewrel_<?php echo $q['field_id']; ?>(){
                        var rt = jQuery('#reltype_<?php echo $q['field_id']; ?>').val();
                        var rtpf = jQuery('#reltypepf_<?php echo $q['field_id']; ?>').val();
                        if(!Boolean(rtpf)) rtpf = '';
                        var tu = '<?php echo $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']; ?>admincontroller.php';
                        tu += '?action=webdata&newFieldRel=1';
                        tu += '&wd_id=<?php echo $wd_id; ?>';
                        tu += '&fid1=<?php echo $q['field_id']; ?>';
                        tu += '&rel_type=' + encodeURIComponent(rtpf + rt);
                        tu += '&f1value=' + encodeURIComponent(jQuery('#f1value_<?php echo $q['field_id']; ?>').val());
                        tu += '&fid2=' + encodeURIComponent(jQuery('#fid2_' + rt + '_<?php echo $q['field_id']; ?>').val());
                        location.href=tu;
                     }
                  </script>
                  <span> 
                  If value 
                  <select id="reltypepf_<?php echo $q['field_id']; ?>">
                  <option value="">equals</option>
                  <option value="N_">does not equal</option>
                  </select>
                  <input type="text" size="10" id="f1value_<?php echo $q['field_id']; ?>">
                  </span>
                  <span>
                   show 
                  <select id="reltype_<?php echo $q['field_id']; ?>" onChange="jQuery('#VALUE_<?php echo $q['field_id']; ?>').hide();jQuery('#SECTID_<?php echo $q['field_id']; ?>').hide();jQuery('#' + jQuery('#reltype_<?php echo $q['field_id']; ?>').val() + '_<?php echo $q['field_id']; ?>').show();">
                  <option value="VALUE">Field</option>
                  <option value="SECTID">Section</option>
                  </select>
                  </span>
                  <span id="VALUE_<?php echo $q['field_id']; ?>"><input type="text" size="10" id="fid2_VALUE_<?php echo $q['field_id']; ?>"></span>
                  <span id="SECTID_<?php echo $q['field_id']; ?>" style="display:none;"> 
                     <select id="fid2_SECTID_<?php echo $q['field_id']; ?>">
                     <?php
                        for ($j=0; $j<count($allsections); $j++) {
                           if($allsections[$j]['section'] != $section) {
                              print "<option value=\"".$allsections[$j]['section']."\">Sect ".$allsections[$j]['sequence']."</option>\n";
                           }
                        }
                     ?>
                     </select>
                  </span>
                  <span style="margin-left:20px;padding:4px;border:1px solid #CCCCCC;border-radius:3px;font-size:10px;font-family:arial;cursor:pointer;" onclick="submitnewrel_<?php echo $q['field_id']; ?>();">Submit QR</span>
               </td>
            </tr>

            <tr style="display:none;" class="refrel<?php echo $q['field_id']; ?>"><td colspan="<?php echo $colspan; ?>"><div style="margin-top:12px;font-size:10px;">References to this field</div></td></tr>               
            <?php for ($j=0; $j<count($refrels); $j++) { ?>
               <tr style="display:none;" class="refrel<?php echo $q['field_id']; ?>"><td colspan="<?php echo $colspan; ?>" style="font-size:10px;font-family:arial;" id="fieldrefrel<?php echo $refrels[$j]['rel_id']; ?>">
               <?php
               
                  $class1 = "rcl".$wd_id.$q['field_id'];
                  $svalue = substr(strtolower(removeSpecialChars($refrels[$j]['f1value'])),0,8);
                  $class2 = $class1."v".$svalue;
                  $class3 = $class1."f".$refrels[$j]['fid2'];
                  
                  if ($sec_input && 0==strcmp($refrels[$j]['rel_type'],"VALUE")) {
                     echo "If field [".$refrels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span>' then display field [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$refrels[$j]['fid2']."</span>]. ";
                  } else if (!$sec_input && 0==strcmp($refrels[$j]['rel_type'],"VALUE")) {
                     echo "If question \"".$qids[$refrels[$j]['fid1']]."\" is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span>' then display question \"<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$qids[$refrels[$j]['fid2']]."</span>\". ";
                  } else if (0==strcmp($refrels[$j]['rel_type'],"SECTVALUE")) {
                     $s = $this->getSection($refrels[$j]['wd_id'],$refrels[$j]['fid2']);
                     echo "If field [".$refrels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span> then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$s['sequence']."</span>]. ";
                  } else if (0==strcmp($refrels[$j]['rel_type'],"SECTID")) {
                     $s = $this->getSection($refrels[$j]['wd_id'],$refrels[$j]['fid2']);
                     echo "If field [".$refrels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span>' then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$s['sequence']."</span>]. ";
                  } else if (0==strcmp($refrels[$j]['rel_type'],"N_VALUE")) {
                     echo "If field [".$refrels[$j]['fid1']."] is not equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span>' then display field [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$refrels[$j]['fid2']."</span>]. ";
                  } else if (0==strcmp($refrels[$j]['rel_type'],"N_SECTID")) {
                     $s = $this->getSection($refrels[$j]['wd_id'],$refrels[$j]['fid2']);
                     echo "If field [".$refrels[$j]['fid1']."] is not equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','".$svalue."','',1);\">".$refrels[$j]['f1value']."</span>' then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$refrels[$j]['fid1']."','','".$refrels[$j]['fid2']."',1);\">".$s['sequence']."</span>]. ";
                  }
               ?>
               <!--a href="<?php echo $url; ?>&wd_id=<?php echo $wd_id; ?>&deleteFieldRel=1&rel_id=<?php echo $refrels[$j]['rel_id']; ?>#section<?php echo $q['parent_s']; ?>" style="font-size:12px;font-family:arial;">Delete rule</a-->
               <!-- span onClick="removeFieldRel('<?php echo $refrels[$j]['rel_id']; ?>');" style="font-size:12px;font-family:arial;color:blue;cursor:pointer;">Delete rule</span -->
               </td></tr>
            <?php } ?>
            <tr style="display:none;" class="refrel<?php echo $q['field_id']; ?>"><td colspan="<?php echo $colspan; ?>"><div style="width:10px;height:10px;overflow:hidden;margin-bottom:12px;"></div></td></tr>               

            <?php for ($j=0; $j<count($rels); $j++) { ?>
               <tr><td colspan="<?php echo $colspan; ?>" style="font-size:12px;font-family:arial;" id="fieldrel<?php echo $rels[$j]['rel_id']; ?>">
               <?php
               
                  $class1 = "cl".$wd_id.$q['field_id'];
                  $svalue = substr(strtolower(removeSpecialChars($rels[$j]['f1value'])),0,8);
                  $class2 = $class1."v".$svalue;
                  $class3 = $class1."f".$rels[$j]['fid2'];
                  
                  if ($sec_input && 0==strcmp($rels[$j]['rel_type'],"VALUE")) {
                     echo "If field [".$rels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span>' then display field [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$rels[$j]['fid2']."</span>]. ";
                  } else if (!$sec_input && 0==strcmp($rels[$j]['rel_type'],"VALUE")) {
                     echo "If question \"".$qids[$rels[$j]['fid1']]."\" is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span>' then display question \"<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$qids[$rels[$j]['fid2']]."</span>\". ";
                  } else if (0==strcmp($rels[$j]['rel_type'],"SECTVALUE")) {
                     $s = $this->getSection($rels[$j]['wd_id'],$rels[$j]['fid2']);
                     echo "If field [".$rels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span> then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$s['sequence']."</span>]. ";
                  } else if (0==strcmp($rels[$j]['rel_type'],"SECTID")) {
                     $s = $this->getSection($rels[$j]['wd_id'],$rels[$j]['fid2']);
                     echo "If field [".$rels[$j]['fid1']."] is equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span>' then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$s['sequence']."</span>]. ";
                  } else if (0==strcmp($rels[$j]['rel_type'],"N_VALUE")) {
                     echo "If field [".$rels[$j]['fid1']."] is not equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span>' then display field [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$rels[$j]['fid2']."</span>]. ";
                  } else if (0==strcmp($rels[$j]['rel_type'],"N_SECTID")) {
                     $s = $this->getSection($rels[$j]['wd_id'],$rels[$j]['fid2']);
                     echo "If field [".$rels[$j]['fid1']."] is not equal to '<span class=\"".$class1." ".$class2."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','".$svalue."');\">".$rels[$j]['f1value']."</span>' then display section [<span class=\"".$class1." ".$class3."\" onclick=\"wd_highlight_reltype('".$rels[$j]['fid1']."','','".$rels[$j]['fid2']."');\">".$s['sequence']."</span>]. ";
                  }
               ?>
               <!--a href="<?php echo $url; ?>&wd_id=<?php echo $wd_id; ?>&deleteFieldRel=1&rel_id=<?php echo $rels[$j]['rel_id']; ?>#section<?php echo $q['parent_s']; ?>" style="font-size:12px;font-family:arial;">Delete rule</a-->
               <span onClick="removeFieldRel('<?php echo $rels[$j]['rel_id']; ?>');" style="font-size:12px;font-family:arial;color:blue;cursor:pointer;">Delete rule</span>
               </td></tr>
            <?php } ?>

            <tr><td colspan="<?php echo $colspan; ?>"><div style="width:10px;height:10px;overflow:hidden;margin-bottom:12px;"></div></td></tr>

            <!-- /form -->
            <script type="text/javascript">addLoadEvent(changeType('<?php echo $q['field_id']; ?>'));</script>

      <?php } ?>
      </table>
      </td></tr>
    <?php } ?>

<?php
   //recursively print sections
   print "<tr><td colspan=\"10\">\n";
   for ($i=0; $i<count($sections); $i++) $this->printAdminSectionSmall_recur($wd_id,$sections[$i]['section'],$url,$sec_input,$qtypes);
   print "</td></tr>\n";

   print "</table>\n";
   print "</td></tr></table>\n";
}










function isFieldChildOf($wd_id,$fid,$sid){
   $foundchild = FALSE;
   $flds = $this->getFields($wd_id, $sid);
   for($i=0;$i<count($flds);$i++) {
      if (0==strcmp($fid,$flds[$i]['field_id'])) {
         $foundchild = TRUE;
         break;
      }
   }
   if(!$foundchild) {
      $sects = $this->getDataSections($wd_id,$sid);
      for($i=0;$i<count($sects);$i++) {
         if($this->isFieldChildOf($wd_id,$fid,$sects[$i]['section'])){
            $foundchild = TRUE;
            break;
         }
      }
   }
   return $foundchild;
}

function checkSectionRels($wd_id) {
   $dbi = new MYSQLAccess();
   $fields = $this->getAllFieldsSystem($wd_id);
   
   $max = 50;
   $relcount = 0;
   
   //$totalremoval = array();
   $totalremoval = "";
   for ($i=0; $i<count($fields); $i++) {
      $removal = array();
      $rels = $this->getNakedField1Rel($wd_id,$fields[$i]['field_id'],"SECTID");
      $rels2 = $this->getNakedField1Rel($wd_id,$fields[$i]['field_id'],"VALUE");
      for ($j=0; $j<count($rels); $j++) {
         for($k=0;$k<count($rels2);$k++) {
            if (0==strcmp($rels[$j]['f1value'],$rels2[$k]['f1value'])) {
               if($this->isFieldChildOf($wd_id,$rels2[$k]['fid2'],$rels[$j]['fid2'])) {
                  $removal[] = $rels2[$k]['rel_id'];
                  //$totalremoval[] = $rels[$k];
                  $totalremoval .= "<br>".$rels2[$k]['fid1'].": Eliminating ".$rels2[$k]['rel_type']." field ".$rels2[$k]['fid2']." (".$rels2[$k]['rel_id'].") because of parent section ".$rels[$j]['fid2']." (".$rels[$j]['rel_id']."). Value was: ".$rels2[$k]['f1value'];
                  $relcount++;
               }
            }
         }
      }
      
      $rels = $this->getNakedField1Rel($wd_id,$fields[$i]['field_id'],"N_SECTID");
      $rels2 = $this->getNakedField1Rel($wd_id,$fields[$i]['field_id'],"N_VALUE");
      for ($j=0; $j<count($rels); $j++) {
         for($k=0;$k<count($rels2);$k++) {
            if (0==strcmp($rels[$j]['f1value'],$rels2[$k]['f1value'])) {
               if($this->isFieldChildOf($wd_id,$rels2[$k]['fid2'],$rels[$j]['fid2'])) {
                  $removal[] = $rels2[$k]['rel_id'];
                  //$totalremoval[] = $rels[$k];
                  $totalremoval .= "<br>".$rels2[$k]['fid1'].": Eliminating ".$rels2[$k]['rel_type']." field ".$rels2[$k]['fid2']." (".$rels2[$k]['rel_id'].") because of parent section ".$rels[$j]['fid2']." (".$rels[$j]['rel_id']."). Value was: ".$rels2[$k]['f1value'];
                  $relcount++;
               }
            }
         }
      }
      
      if(count($removal)>0) {
         $sql = "";
         if(count($removal)>1) {
            $sql = "DELETE FROM wd_rel WHERE rel_id in (".implode(",",$removal).");";
         } else if(count($removal)==1) {
            $sql = "DELETE FROM wd_rel WHERE rel_id=".$removal[0].";";
         }
         $dbi->delete($sql);
      }
      
      if($relcount>$max) {
         $totalremoval .= "<br>Max was reached, you will need to run this function again.";
         break;
      }
   }
   return $totalremoval;
}









function getVisualAdminString_recur($wd_id,$section=-1,$allsectionsopts=NULL) {
   $str = "";
   if ($section==-1) {
      $webdata = $this->getWebData($wd_id);

      $emailops["No email"] = 2;
      $emailops["Send an email when a new record is created"] = 1;
      $sextra = " style=\"font-size:12px;font-family:arial;\" id=\"w".$webdata['wd_id']."-emailresults\" onChange=\"if(jQuery('#w".$webdata['wd_id']."-emailresults').val()==1) jQuery('#wdadminemailaddr').show(); else jQuery('#wdadminemailaddr').hide();jsfwd_updateValue('w".$webdata['wd_id']."-emailresults');\"";
      $emailsel = getOptionList("emailresults", $emailops, $webdata['emailresults'], FALSE, $sextra);

      $statusops["Inactive"] = "INACTIVE";
      $statusops["Active"] = "ACTIVE";
      $statusextra = " style=\"font-size:12px;font-family:arial;\" id=\"w".$webdata['wd_id']."-status\" onChange=\"jsfwd_updateValue('w".$webdata['wd_id']."-status');\"";
      $statussel = getOptionList("status", $statusops, $webdata['status'], FALSE, $statusextra);

      $str .= "<div style=\"background-color:#E1E1FF;margin:10px;padding:10px;\">\n";
      $str .= "<table cellpadding=\"3\" cellspacing=\"1\" style=\"font-size:12px;font-family:arial;\">\n";
      //name, shortname, description, email, sendornot
      $str .= "<tr valign=\"top\"><TD>Survey Name</td><td><input type=\"text\" style=\"width:250px;font-size:12px;\" name=\"name\" value=\"".$webdata['name']."\" id=\"w".$webdata['wd_id']."-name\" onkeyup=\"jsfwd_updateValue('w".$webdata['wd_id']."-name');\"></td></tr>\n";
      $str .= "<tr valign=\"top\"><TD>Shortname</td><td><input type=\"text\" style=\"width:250px;font-size:12px;\" name=\"shortname\" value=\"".$webdata['shortname']."\" id=\"w".$webdata['wd_id']."-shortname\" onkeyup=\"jsfwd_updateValue('w".$webdata['wd_id']."-shortname');\"></td></tr>\n";
      $str .= "<tr valign=\"top\"><td>Description</td><td><textarea style=\"width:250px;height:45px;font-size:12px;\" name=\"info\" id=\"w".$webdata['wd_id']."-info\" onkeyup=\"jsfwd_updateValue('w".$webdata['wd_id']."-info');\">".$webdata['info']."</textarea></td></tr>\n";
      $str .= "<tr valign=\"top\"><td colspan=\"2\">".$statussel."</td></tr>";
      $str .= "<tr valign=\"top\"><td colspan=\"2\">".$emailsel."</td></tr>";
      $str .= "<tr valign=\"top\" id=\"wdadminemailaddr\"";
      if($webdata['emailresults']!=1) $str .= " style=\"display:none;\"";
      $str .= "><td>Email</td><td><input type=\"text\" style=\"width:250px;font-size:12px;\" name=\"adminemail\" value=\"".$webdata['adminemail']."\" id=\"w".$webdata['wd_id']."-adminemail\" onkeyup=\"jsfwd_updateValue('w".$webdata['wd_id']."-adminemail');\"></td></tr>\n";
      $str .= "</table>";
      $str .= "</div>";
   }
   $sections = $this->getDataSections($wd_id,$section);
   $s = $this->getSection($wd_id,$section);

   if ($allsectionsopts==NULL) {
      $allsections = $this->getAllDataSections($wd_id);
      $allsectionsopts['Main Sect'] = -1;
      for ($i=0; $i<count($allsections); $i++) $allsectionsopts['Sect '.$allsections[$i]['sequence']] = $allsections[$i]['section'];
   }
   $temp_allsectopts = $allsectionsopts;
   unset($temp_allsectopts['Main Sect']);

   $sextra = " style=\"font-size:12px;font-family:arial;\" id=\"s".$s['section']."-parent_s\" onChange=\"jsfwd_updateValue('s".$s['section']."-parent_s');\"";
   $sectsel = getOptionList("parent_s", $allsectionsopts, $s['parent_s'], FALSE, $sextra);
   $fields = $this->getFields($wd_id, $section);
   $qids = NULL;
   for ($i=0; $i<count($fields); $i++) $qids[$fields[$i]['field_id']] = $fields[$i]['label'];
   $str .= "<a name=\"section".$section."\"></a>\n";
   $str .= "<div style=\"height:5px;width:5px;overflow:hidden;\"></div>\n";
   $str .= "<table cellpadding=\"2\" cellspacing=\"1\" bgcolor=\"#444444\" id=\"s".$section."_outertable\"><tr><td>\n";
   //if ($section==-1) {
   //   $str .= "<div style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;\" ";
   //   $str .= " onclick=\"jsfwd_refreshScreen('".$wd_id."','',globaluser.userid,globaluser.token);\">Refresh</div>\n";
   //   $str .= "</td></tr><tr><td>\n";
   //}
   $str .= "<table cellpadding=\"2\" cellspacing=\"2\" bgcolor=\"#FFFFFF\" style=\"font-size:12px;font-family:arial;\">\n";
   if ($section!=-1) {
      $str .= "<TR bgcolor=\"#b8d0f1\" id=\"s".$s['section']."_sectionrow\">\n";
      $str .= "<TD>Sect: <input type=\"text\" style=\"width:20px;font-size:10px;\" name=\"sequence\" value=\"".$s['sequence']."\" id=\"s".$s['section']."-sequence\" onkeyup=\"jsfwd_updateValue('s".$s['section']."-sequence');\"></td>\n";
      $str .= "<td>Title: <input type=\"text\" style=\"width:220px;font-size:10px;\" name=\"label\" value=\"".$s['label']."\" id=\"s".$s['section']."-label\" onkeyup=\"jsfwd_updateValue('s".$s['section']."-label');\"></td>\n";
      $str .= "<td>Parent: ".$sectsel."</td>\n";
      $str .= "<TD>Param: <input type=\"text\" style=\"width:20px;font-size:10px;\" name=\"param1\" value=\"".$s['param1']."\" id=\"s".$s['section']."-param1\" onkeyup=\"jsfwd_updateValue('s".$s['section']."-param1');\"></td>\n";
      $str .= "<td align=\"right\">\n";
      $str .= "<table cellpadding=\"0\" cellspacing=\"4\"><tr>\n";
      $str .= "<td>\n";
      $str .= "<div class=\"sectionupdatebutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_updateWebsiteData('".$wd_id."','','',globaluser.userid,globaluser.token);\">Update</div>\n";
      $str .= "</td><td>\n";
      $str .= "<div class=\"sectioncancelbutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_refreshScreen('".$wd_id."','',globaluser.userid,globaluser.token);\">Cancel</div>\n";
      $str .= "</td>\n";
      if (count($fields)==0 && count($sections)==0) {
         $str .= "<td><div class=\"sectiondeletebutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;\" onclick=\"jsfwd_deleteSection('".$wd_id."','".$s['section']."','',globaluser.userid,globaluser.token);\">Delete</div></td>\n";
      }
      $str .= "</tr></table></td></TR>\n";
   }

   if (count($fields)>0) {
      $str .= "<tr><td colspan=\"5\">\n";
      $str .= "<table cellpadding=\"1\" cellspacing=\"1\" bgcolor=\"#EEEEEE\">\n";
      $str .= "<tr bgcolor=\"#CCCCCC\" style=\"font-size:12px;font-family:arial;\">\n";
      $str .= "<!--td>Field</td--><td>Sect</td><td>Sequence</td><td>Question</td><td>Header</td><td></td></tr>\n";

      //$colspan=6;
      $colspan=5;
      for ($i=0; $i<count($fields); $i++) {
         $q = $fields[$i];
         $selected = NULL;

         $sectionDropDown = getOptionList("parent_s", $temp_allsectopts, $q['parent_s'], FALSE, " style=\"font-size:12px;font-family:arial;\" id=\"".$q['field_id']."-parent_s\" onChange=\"jsfwd_updateValue('".$q['field_id']."-parent_s');\"");
         $str .= "<tr valign=\"top\" id=\"".$q['field_id']."_row\" style=\"font-size:12px;font-family:arial;\">\n";
         $str .= "   <!--td>".$q['field_id']."</td-->\n";
         $str .= "   <td>".$sectionDropDown."</td>\n";
         $str .= "   <td><input type=\"text\" name=\"sequence\" value=\"".$q['sequence']."\" style=\"font-size:12px;font-family:arial;width:30px;\" id=\"".$q['field_id']."-sequence\" onkeyup=\"jsfwd_updateValue('".$q['field_id']."-sequence');\"></td>\n";
         $str .= "   <td><input type=\"text\" name=\"label\" value=\"".str_replace("\"","&#34;",convertBack($q['label']))."\" style=\"font-size:12px;font-family:arial;width:270px;\" id=\"".$q['field_id']."-label\" onkeyup=\"jsfwd_updateValue('".$q['field_id']."-label');\"></td>\n";
         $str .= "   <td><input type=\"checkbox\" name=\"header\" value=\"1\" id=\"".$q['field_id']."-header\" onClick=\"jsfwd_updateValue('".$q['field_id']."-header');\" ";
         if ($q['header']==1) $str .= " CHECKED";
         $str .= "></td>\n";
         $str .= "   <td align=\"right\">\n";
         $str .= "      <table cellpadding=\"0\" cellspacing=\"1\"><tr align=\"right\"><td>\n";
         $str .= "      <div class=\"questiondeletebutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;\" onclick=\"jsfwd_deleteQuestion('".$wd_id."','".$q['field_id']."','',globaluser.userid,globaluser.token);\">Delete</div>\n";
         $str .= "      </td></tr></table>\n";
         $str .= "   </td>\n";
         $str .= "</tr>\n";
         $str .= "<tr valign=\"top\" id=\"".$q['field_id']."_row2\"><td colspan=\"".$colspan."\">\n";
         $str .= "   <table cellpadding=\"0\" cellspacing=\"2\"><tr>\n";
         $str .= "   <td>Values:</td>\n";
         $str .= "   <td style=\"font-size:12px;font-family:arial;\">\n";
         $str .= "   <input type=\"text\" name=\"question\" value=\"".convertBack($q['question'])."\" style=\"font-size:12px;font-family:arial;width:320px;\" id=\"".$q['field_id']."-question\" onkeyup=\"jsfwd_updateValue('".$q['field_id']."-question');\">\n";
         $str .= "   </td>\n";
         $str .= "   <td><div style=\"width:10px;height:10px;overflow:hidden;\"></div></td>\n";
         $str .= "   <td>Notes:</td>\n";
         $str .= "   <td style=\"font-size:12px;font-family:arial;\">\n";
         $str .= "   <input type=\"text\" name=\"notes\" value=\"".convertBack($q['notes'])."\" style=\"font-size:12px;font-family:arial;width:200px;\" id=\"".$q['field_id']."-notes\" onkeyup=\"jsfwd_updateValue('".$q['field_id']."-notes');\">\n";
         $str .= "   </td>\n";
         $str .= "   </tr></table>\n";
         $str .= "</td></tr>\n";
         $str .= "<tr id=\"".$q['field_id']."_row3\"><td colspan=\"".$colspan."\"><div style=\"width:10px;height:10px;overflow:hidden;margin-bottom:12px;\"></div></td></tr>\n";
      }
      $str .= "</table>\n";
      $str .= "</td></tr>\n";
   }
   //recursively print sections
   $str .= "<tr><td colspan=\"5\">\n";
   for ($i=0; $i<count($sections); $i++) $str .= $this->getVisualAdminString_recur($wd_id,$sections[$i]['section'],$allsectionsopts);
   $str .= "</td></tr>\n";

   if ($section==-1) {
      //Create new sections/fields - only when we're on the outer table.
      $str .= "<tr><td colspan=\"5\">\n";
      $str .= "<div style=\"margin-top:15px;font-weight:bold;\">Add a new section</div>";
      $str .= "</td></tr>\n";

      $sextra = " style=\"font-size:12px;font-family:arial;\" id=\"snew-parent_s\" onChange=\"jsfwd_updateValue('snew-parent_s');\"";
      $sectsel = getOptionList("parent_s", $allsectionsopts,NULL, FALSE, $sextra);
      $str .= "<TR bgcolor=\"#60ae6c\" id=\"snew_sectionrow\">\n";
      $str .= "<TD>Sect: <input type=\"text\" style=\"width:20px;font-size:10px;\" name=\"sequence\" value=\"\" id=\"snew-sequence\" onkeyup=\"jsfwd_updateValue('snew-sequence');jsfwd_updateValue('snew-parent_s');\"></td>\n";
      $str .= "<td>Title: <input type=\"text\" style=\"width:220px;font-size:10px;\" name=\"label\" value=\"\" id=\"snew-label\" onkeyup=\"jsfwd_updateValue('snew-label');\"></td>\n";
      $str .= "<td>Parent: ".$sectsel."</td>\n";
      //$str .= "<TD>Param: <input type=\"text\" style=\"width:20px;font-size:10px;\" name=\"param1\" value=\"\" id=\"snew-param1\" onkeyup=\"jsfwd_updateValue('snew-param1');\"></td>\n";
      $str .= "<td colspan=\"2\" align=\"right\"></td>\n";
      $str .= "</tr>\n";

      $str .= "<tr><td colspan=\"5\">\n";
      $str .= "<table cellpadding=\"4\" cellspacing=\"4\"><tr>\n";
      $str .= "<td>\n";
      $str .= "<div class=\"sectionupdatebutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_updateWebsiteData('".$wd_id."','','',globaluser.userid,globaluser.token);\">Update</div>\n";
      $str .= "</td><td>\n";
      $str .= "<div class=\"sectioncancelbutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_refreshScreen('".$wd_id."','',globaluser.userid,globaluser.token);\">Cancel</div>\n";
      $str .= "</td></tr></table>\n";
      $str .= "</td></tr>\n";


      if (count($temp_allsectopts)>0) {
         $str .= "<tr><td colspan=\"5\">\n";
         $str .= "<div style=\"margin-top:15px;font-weight:bold;\">Add a new question</div>";
         $str .= "</td></tr>\n";
         $sectionDropDown = getOptionList("parent_s", $temp_allsectopts,NULL,FALSE," style=\"font-size:12px;font-family:arial;\" id=\"new-parent_s\" onChange=\"jsfwd_updateValue('new-parent_s');\"");
         $str .= "<tr><td colspan=\"5\"><table width=\"100%\" cellpadding=\"3\" cellspacing=\"1\" bgcolor=\"#ffffff\">\n";
         $str .= "<tr valign=\"top\" id=\"new_row\" style=\"font-size:12px;font-family:arial;\" bgcolor=\"#60ae6c\">\n";
         $str .= "   <td>".$sectionDropDown."</td>\n";
         $str .= "   <td>Sequence: <input type=\"text\" name=\"sequence\" value=\"\" style=\"font-size:12px;font-family:arial;width:25px;\" id=\"new-sequence\" onkeyup=\"jsfwd_updateValue('new-sequence');jsfwd_updateValue('new-parent_s');\"></td>\n";
         $str .= "   <td>Question: <input type=\"text\" name=\"label\" value=\"\" style=\"font-size:12px;font-family:arial;width:140px;\" id=\"new-label\" onkeyup=\"jsfwd_updateValue('new-label');jsfwd_updateValue('new-parent_s');\"></td>\n";
         //$str .= "   <td><input type=\"checkbox\" name=\"header\" value=\"1\" id=\"new-header\" onClick=\"jsfwd_updateValue('new-header');\"> Header</td>\n";
         //$str .= "   <td align=\"right\">\n";
         //$str .= "   Values: <input type=\"text\" name=\"question\" value=\"\" style=\"font-size:12px;font-family:arial;width:150px;\" id=\"new-question\" onkeyup=\"jsfwd_updateValue('new-question');jsfwd_updateValue('new-parent_s');\">\n";
         //$str .= "   </td>\n";
         $str .= "</tr>\n";
         $str .= "</table></td></tr>\n";
   
         $str .= "<tr><td colspan=\"5\">\n";
         $str .= "<table cellpadding=\"4\" cellspacing=\"4\"><tr>\n";
         $str .= "<td>\n";
         $str .= "<div class=\"sectionupdatebutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_updateWebsiteData('".$wd_id."','','',globaluser.userid,globaluser.token);\">Update</div>\n";
         $str .= "</td><td>\n";
         $str .= "<div class=\"sectioncancelbutton\" style=\"cursor:pointer;padding:3px;text-align:center;font-size:12px;font-family:arial;background-color:#CCCCCC;border:1px solid #000000;display:none;\" onclick=\"jsfwd_refreshScreen('".$wd_id."','',globaluser.userid,globaluser.token);\">Cancel</div>\n";
         $str .= "</td></tr></table>\n";
         $str .= "</td></tr>\n";
      }
   }

   $str .= "</table>\n";
   $str .= "</td></tr></table>\n";
   return $str;
}




   //*********************************************************************************//
   //*** Long method to print out the inner-html of a survey form                  ***//
   // To print an existing row, pass in either the origemail, userid, or wd_row_id
   //*********************************************************************************//
   function printWebData($wd_id, $origemail=NULL, $userid=NULL, $wd_row_id=NULL, $maintenance=NULL, $type=NULL, $phpinclude=NULL, $postToForm=NULL, $includeForm=TRUE,$clr1=NULL,$clr2=NULL,$addreq=FALSE,$extraFields=NULL,$disabled=false,$view=NULL) {
      $ua = new UserAcct;
      $dbi = new MYSQLAccess;     
      
      if ($origemail==NULL) $origemail = getParameter("origemail");
      if ($wd_row_id==NULL) $wd_row_id = getParameter("wd_row_id");
      if ($wd_id==NULL) $wd_id = getParameter("wd_id");
      $webdata = $this->getWebData($wd_id);
      if ($userid==NULL) $userid = trim(getParameter("userid"));
      if ($userid==NULL && $origemail==NULL && $wd_row_id==NULL && $webdata['privatesrvy']<3) $userid = isLoggedOn();
      
      if ($webdata['privatesrvy']==52 && $_COOKIE["survey".$webdata['wd_id']]>0) $disabled=TRUE;
      print "\n<!-- already submitted: ".$_COOKIE["survey".$webdata['wd_id']]." -->\n";
      // get the database row based on the input
      $row = NULL;
      if ($wd_row_id != NULL){
         $row = $this->getDetails($wd_id,$wd_row_id);
      } else if ($wd_row_id == NULL && $origemail != null){
         $row = $this->getCodedRow($wd_id,$origemail);
      } else if ($wd_row_id == NULL && $userid != NULL) {
         $rows = $this->getDataByUserid($wd_id, $userid);
         $row = $rows[0];
      } else if ($webdata['privatesrvy']==52 && $_COOKIE['survey'.$wd_id] > 0) {
         $row = $this->getDetails($wd_id,$_COOKIE['survey'.$wd_id]);
      }
      print "\n<!-- current answers:\n";
      print_r($row);
      print "\n-->\n";

      if ($userid==NULL && $row!=NULL) $userid = $row['userid'];
      $adminuserid = $userid;
      if ($userid!=NULL) {
         //$adminrel = $ua->getUsersRelated($userid,"to","SRVYADMIN");
         $adminrel = $this->getUsersRelated($webdata,$userid);
         if ($adminrel!=NULL && $adminrel[0]['reluserid']>0) {
            $adminuserid = $adminrel[0]['reluserid'];
         }
      }

      // Determine display properties
      if ($postToForm==NULL) $postToForm = getBaseURL().$GLOBALS['codeFolder']."controller.php";
      $bgcolor="#ffffff";
      $longVersion = true;
      $captcha = true;
      if ($type != NULL && strcmp($type,"Short")==0) {
         $longVersion=false;
         $captcha = false;
      } else if ($type != NULL && strcmp($type,"ShortWithCaptcha")==0) {
         $longVersion=false;
      } else if ($type != NULL && strcmp($type,"LongNoCaptcha")==0) {
         $captcha = false;
      }

      //admin view or user view?
      $admin = $ua->isUserAdmin(isLoggedOn());
      if (getParameter("userview") != null && getParameter("userview")==1) $admin = false;
      
      $previouslySubmitted = false;
      if (!$disabled && $row['wd_row_id'] != NULL) {
         if (strcmp($row['complete'],"Y")==0) {
            $disabled=false;
            $previouslySubmitted=true;
         } else if (strcmp($row['complete'],"A")==0) {
            $disabled=false;
            $previouslySubmitted=true;
         } else if (strcmp($row['complete'],"L")==0) {
            $disabled=true;
            $previouslySubmitted=true;
         }
      }

      if ($row['wd_row_id'] == NULL && $webdata['privatesrvy'] == 1 && !$admin) {
         print "<h2>The page you are looking for is not available right now.</h2><br><b>Please check your URL and try again...</b>";
      } else {
         if (($row['wd_row_id'] == NULL && $webdata['privatesrvy'] == 1) || getParameter("viewonly")==1) {
            $disabled=true;
            $previouslySubmitted = false;
         }

         //set up folders used by the wizzy wig HTML editor if needed
         $wwupload = "jsfcode/srvyfiles/wwupload/";
         $_SESSION['imagesdir']= $GLOBALS['baseDir'].$wwupload;
         $_SESSION['imagesurl']= getBaseURL().$wwupload;

         $glossaryid=$webdata['glossaryid'];
         $glossary = new Glossary($glossaryid);
         print $glossary->getjscript();
         ?>
         <div id="LoadBox" style="display:none; position:absolute; border:#000099 solid 1px; padding:10px; color:#000099; background-color:#FFFFFF;"><img src="<?php echo getBaseURL(); ?>jsfimages/loading.gif"></div>
         <script src="<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>rte/js/richtext.js" type="text/javascript" language="javascript"></script>
         <script src="<?php echo getBaseURL().$GLOBALS['adminFolder']; ?>rte/js/config.js" type="text/javascript" language="javascript"></script>
         <!--script src='http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js' type='text/javascript'></script-->
         <!--script src='<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>validation.js' type='text/javascript'></script-->
         <script>
         function expandSection(c,s) {
           if (document.getElementById(c).checked==true) {
               document.getElementById(s).style.display = "";
           } else {
               document.getElementById(s).style.display = "none";
           }
         }

         function DisplayWait(me) {
            load_box_id = document.getElementById('LoadBox');
            //var x = findPosX(me);
            //var y = findPosY(me);
            var x = jsfglsfindPosX(me);
            var y = jsfglsfindPosY(me);
            load_box_id.style.left = String(parseInt(x + 10) + 'px');
            load_box_id.style.top = String(parseInt(y + 10) + 'px');
            load_box_id.style.display = "block";
            //document.getElementById('LoadBox').style.display = "block";
         }
         function HideWait() {
            document.getElementById('LoadBox').style.display = "none";
         }

         </script>
         <!-- script>   var vf = new FormValidation();  </script -->
         <script>
         //$$('input').each(function(inputElem) {
         //$('input').each(function(inputElem) {
         //   inputElem.observe("fv:onInvalid", function() { $(this.identify() + "-error").show();}.bind(inputElem));
         //   inputElem.observe("fv:onValid", function() { $(this.identify() + "-error").hide();}.bind(inputElem));
         //});
         </script>
   
         <?php if ($longVersion) { ?>
            <table width="100%" cellpadding="20" cellspacing="0" border="0"><tr><td>
            <table width="100%" cellpadding="2" cellspacing="0" border="0">
            <tr><td><h2><b><?php echo $webdata['name']; ?></b></h2></td></tr>
            <tr><td><?php echo $glossary->flagAllTerms(convertBack($webdata['info']),"#5691c4"); ?></td></tr>
             <Tr><td valign="top">
               <?php if ($admin && $row['wd_row_id'] != NULL) { ?>
                  
               <?php } else if ($disabled && $previouslySubmitted) { ?>
                 <hr><font color="red" size="+1"><b>You have already submitted this survey and cannot resubmit.</b></font>
               <?php } else if (!$disabled && $previouslySubmitted) { ?>
   
               <?php } ?>
             </td></tr>
             <TR><TD><HR></TD></TR>
             <tr><td>
         <?php } ?>

         <?php if ($includeForm) { ?>
             <table width="100%" align="center" bgcolor="" CELLPADDING="1" CELLSPACING="0" BORDER="0">
             <form name="cmssurveyform" enctype="multipart/form-data" action="<?php echo $postToForm; ?>" method="POST">
             <input type="hidden" name="action" value="submitdata">
             <input type="hidden" name="userid" value="<?php echo $userid; ?>">
             <input type="hidden" name="view" value="<?php echo $view; ?>">
             <input type="hidden" name="newwindow" value="<?php echo getParameter("newwindow"); ?>">

             <?php
               $serialnumber = getRandomNum();
               while ($this->checkSerialNumber($serialnumber,$wd_id)) $serialnumber = getRandomNum();
             ?>
             <input type="hidden" name="serialnumber" value="<?php echo $serialnumber; ?>">
             <?php if ($phpinclude!= NULL ) { ?>
               <input type="hidden" name="phpinclude" value="<?php echo $phpinclude; ?>">
             <?php } ?>
             <tr><td>     
         <?php } ?>

         <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
         <?php echo $extraFields; ?>
         <?php if ($maintenance!= NULL && $maintenance>0 ) { ?>
           <input type="hidden" name="maintenance" value="<?php echo $maintenance; ?>">
         <?php } ?>
         <?php if ($page!= NULL ) { ?>
           <input type="hidden" name="page" value="<?php echo $page; ?>">
         <?php } ?>
         <input type="hidden" name="wd_id" value="<?php echo $wd_id; ?>">
         <input type="hidden" name="wd_row_id" value="<?php echo $row['wd_row_id']; ?>">

         <?php
            //if(FALSE && $admin && ($webdata['privatesrvy']<3 || $webdata['privatesrvy']==101) && $longVersion){
            if($admin && ($webdata['privatesrvy']<3 || $webdata['privatesrvy']==101) && $longVersion){
               $surveyuser = $ua->getUser($userid);
               $adminuser = $ua->getUser($adminuserid);
               //print "<input type=\"hidden\" name=\"skipuser\" value=\"1\">";
               print "<table cellpadding=\"2\" border=\"0\" cellspacing=\"1\">";
               print "<tr>";
               print "<td><b>Company:</b> </td><td><a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$userid."\" target=\"_new\">".$surveyuser['company']."</a></td>";
               print "<td><img src=\"".getBaseURL()."jsfimages/pixel.gif\" width=\"30\" height=\"1\"></td>";
               print "<td><b>Survey Admin:</b> </td><td><a href=\"".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=".$adminuserid."\" target=\"_new\">".$adminuser['fname']." ".$adminuser['lname']."</a></td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['addr1']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['addr1']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['addr2']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['addr2']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['city'].", ".$surveyuser['state']." ".$surveyuser['zip']." ".$surveyuser['country']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['city'].", ".$adminuser['state']." ".$adminuser['zip']." ".$adminuser['country']."</td>";
               print "</tr>";
               
               $website = $surveyuser['website'];
               if($website==NULL) $website = $adminuser['website'];
               if($website!=NULL && strlen($website)>5 && 0!=strcmp(substr(strtolower($website),0,4),"http")) {
                  $webiste = "http://".$website;
               }
               print "<tr>";
               print "<td></td><td><a href=\"".$website."\" target=\"_new\">".$website."</a></td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['email']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['phonenum']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['phonenum']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['phonenum1']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['phonenum1']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td></td><td>".$surveyuser['phonenum2']."</td>";
               print "<td></td>";
               print "<td></td><td>".$adminuser['phonenum2']."</td>";
               print "</tr>";
               print "<tr>";
               print "<td colspan=\"5\"><br></td>";
               print "</tr>";
               print "</table><br><hr>";
            //} else if(($webdata['privatesrvy']<3 || $webdata['privatesrvy']==101) && $longVersion){
               print "<table cellpadding=\"2\" border=\"1\" cellspacing=\"1\"><tr><td>";
               print "Update the survey admin information:<br>";
               print $ua->printUserForm($adminuserid);
               $ua->printUserProperties($userid);
               print "</td></tr></table>";
            }
         ?>

         <?php 
            if ($admin && $longVersion) print "<br><b>Survey Admin Comments:</b><br><textarea name=\"wd".$webdata['wd_id']."_comments\" rows=\"3\" cols=\"40\">".$row['comments']."</textarea>";
         ?>
      
         <?php $this->printWebDataSection($wd_id, -1, $row,$admin,$disabled,$longVersion,$glossary,$clr1,$clr2,$addreq); ?>

         <?php if ($includeForm || $longVersion) { ?>
            </td></tr>
         <?php } ?>

            <?php if(!$disabled || $admin) { ?>

                  <?php if(!$admin && $webdata['password']!=NULL && strlen($webdata['password'])>0) { ?>
                     <tr>
                        <td>
                         <table cellpadding="3" cellspacing="2">
                         <tr>
                           <td class="label">Enter Password</td>
                           <td><input type="password" name="w<?php echo $wd_id; ?>password" value="" id="w<?php echo $wd_id; ?>password" size="25" <?php if($disabled) print "DISABLED"; ?>>
                         </tr>
                         </table>
                        </td>
                     </tr>
                  <?php } else {
                           print "<input type=\"hidden\" name=\"w".$wd_id."password\" value=\"".$webdata['password']."\">\n";
                        }
                  ?>

                  <?php 
                     //if(!$admin && $captcha && $includeForm) { 
                     if(!$admin && $captcha) {
                  ?>
                  <?php if ($includeForm) { ?>
                     <tr valign="top" align="center"><td>
                  <?php } ?>
                         Please enter the security code from the image below to continue.<br>
                         <img src="<?php echo getBaseURL().$GLOBALS['codeFolder']; ?>CaptchaSecurityImages.php" />&nbsp;&nbsp;
                         <input id="security_code" name="security_code" type="text" size="15"/>
                  <?php if ($includeForm) { ?>
                     </td></tr>
                  <?php } ?>
                  <?php 
                     } else {
                        unset($_SESSION['ss_captcha']['security_code']);
                     }
                  ?>
         
                  <?php if ($longVersion && $includeForm) { ?>
                     <TR><TD valign="top" style="padding-left:25px;" align="center">
                        <BR><input type="Submit" name="Submit" value="Submit">
                        &nbsp;<input type="button" value="Cancel Changes/Reset" onClick="window.location.reload()">
                        <br><BR>
                     </TD></TR>
                  <?php } else if ($includeForm) { ?>
                     <TR><TD valign="top" align="left">
                        <input type="Submit" name="Submit" value="Submit">
                     </TD></TR>
                  <?php } ?>
            <?php } ?>
            
            
      <?php if ($includeForm) { ?>
            </form>
            </table>
      <?php } ?>
            
      <?php if ($longVersion) { ?>
            </table>
            </td></tr></table>
      <?php } ?>
         
      <?php
      }// end check to see if user is authorized to view this survey
   }//end function printSurvey()

   function printWebDataSection($wd_id, $section, $row,$admin,$disabled,$longVersion,$glossary,$clr1,$clr2,$addreq) {
      $sectionObj = $this->getSection($wd_id,$section);
      $tableStyle = "";
      if ($sectionObj['dyna'] == 1) {
         $sectionChecked = "";
         $tableStyle="style=\"display: none;\"";
         if ($this->isSectionUsed($wd_id,$section,$row)) {
            $tableStyle="";
            $sectionChecked = "CHECKED";
         }
         print "\n<input type=\"checkbox\" ".$sectionChecked." id=\"c".$sectionObj['section']."\" name=\"check".$sectionObj['section']."\" value=\"1\" onClick=\"expandSection('c".$sectionObj['section']."', 's".$sectionObj['section']."');\">\n";
         print $sectionObj['question']."<BR>";
      }
      else print "\n<input type=\"checkbox\" id=\"c".$sectionObj['section']."\" name=\"check".$sectionObj['section']."\" value=\"1\" style=\"display: none;\">\n";
      
      print "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" id=\"s".$sectionObj['section']."\" ".$tableStyle.">\n";
      if ($longVersion) print "<TR><TD colspan=\"2\"><BR></td></tr>\n";
      if ($sectionObj['label']!=NULL) {
         print "<TR><TD colspan=\"2\"><H2>";
         if ($glossary!=NULL) print $glossary->flagAllTerms($sectionObj['label'],"#5691c4");
         else print $sectionObj['label'];
         print "</H2></td></tr>\n";
      }

      $questions = $this->getFields($wd_id, $section);
      //iterate thru all the questions for this section
      for ($j=0; $j<count($questions); $j++) {
         $q = $questions[$j];
         $color = $clr2;
         if (($j%2)==0) $color=$clr1;
         $this->printQuestionHTML($wd_id,$q,$row['wd_row_id'],(!$admin && $disabled),$longVersion,$glossary,$color,$addreq);
      }

      $sections = $this->getDataSections($wd_id,$section);
      for ($i=0; $i<count($sections); $i++) {
         if ($longVersion) print "<TR><TD colspan=\"2\" valign=\"top\" style=\"padding-left:15px;\">\n";
         else print "<TR><TD colspan=\"2\" valign=\"top\">\n";
         $this->printWebDataSection($wd_id,$sections[$i]['section'],$row,$admin,$disabled,$longVersion,$glossary,$clr1,$clr2,$addreq);
         print "</td></tr>\n";
      }
      print "</table>\n";
   }

   function isSectionUsed($wd_id,$section,$row){
      //print "Checking is section is used: ".$section." <br>";
      $found = FALSE;
      if ($row==NULL) return $found;

      $questions = $this->getFields($wd_id, $section);
      for ($i=0; $i<count($questions); $i++) {
         $q = $questions[$i];
         if ($row[$q['field_id']]!=NULL) {
            $found=TRUE;
            $break;
         }
      }
      if (!$found) {
         $counter = 0;
         $sections = $this->getDataSections($wd_id,$section);
         while (!$found && $counter < count($sections)) {
            $found = $this->isSectionUsed($wd_id,$sections[$counter]['section'],$row);
            $counter++;
         }
      }
      return $found;
   }


   function printRegionSelectionBoxes($bgcolor,$c,$paramname=NULL){
      $topLeft=getBaseURL()."jsfimages/pixel.gif";
      $botLeft=getBaseURL()."jsfimages/pixel.gif";
      $topRight=getBaseURL()."jsfimages/pixel.gif";
      $botRight=getBaseURL()."jsfimages/pixel.gif";
      if ($paramname==NULL) $paramname="region[]";
        //I changed this 2/15/11: Note on this: This code starts a 4 column table and does not close it... make sure you close it with a </table>
  ?>
        
        <script type="text/javascript">
        function DisableRegions(dis) {                                                                    
          for (var i=0;i<document.forms[0].elements.length;i++) {                                                                  
            var e = document.forms[0].elements[i];                           
            if ((e.name == '<?php echo $paramname; ?>') && (e.type=='checkbox') && (e.disabled != dis)) {
              e.checked = false;                
              e.disabled = dis;                  
            }
          }                                                                  
        }

        function SelectAll(ca,st,end){                                                                    
          var checked = false;
          for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
            var e = document.forms[0].elements[i];                           
            if ((e.value == ca) && (e.type=='checkbox'))                
              checked = e.checked;                  
          }                                                                  
          
          for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
            var e = document.forms[0].elements[i];                           
            if ((e.name == '<?php echo $paramname; ?>') && (e.value!= ca) && (e.value <= end) && (e.value >= st) && (e.type=='checkbox'))                
              e.checked = checked;                  
          }                                                                  
        }

        function selectRegions(ca,valArr){
           var checked = false;
           for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
             var e = document.forms[0].elements[i];                           
             if ((e.value == ca) && (e.type=='checkbox'))                
               checked = e.checked;                  
           }                                                                  
           
           for (x in valArr) {
              for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
                var e = document.forms[0].elements[i];                           
                if ((e.name == '<?php echo $paramname; ?>') && (e.value!= ca) && (e.value == valArr[x]) && (e.type=='checkbox'))                
                  e.checked = checked;                  
              }                                                                  
           }
        }
                                                                             
        function CheckAllSelected(ca,par,valArr){                                                                    
          var parentChecked = true;
          var allChecked = true;

          for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
            var e = document.forms[0].elements[i];                       
            if ((e.value != ca) && (e.value != par) && (e.name == '<?php echo $paramname; ?>') && (e.type=='checkbox')){                                                                
              if (e.checked) {

              } else {
                 allChecked = false;
                 for (x in valArr) if (e.value == valArr[x]) parentChecked = false;
                 if (parentChecked != true) break;
              }

            }                                                                
          }                                                                  
        
          for (var i=0;i<document.forms[0].elements.length;i++){                                                                  
            var e = document.forms[0].elements[i];                       
            if ((e.value == ca) && (e.name == '<?php echo $paramname; ?>') && (e.type=='checkbox')){                                                                
               e.checked = allChecked;
            }
            
            if ((e.value == par) && (e.name == '<?php echo $paramname; ?>') && (e.type=='checkbox')){                                                                
               e.checked = parentChecked;
            }
          }
        }                                                                    
        </script>                                                            

             <table width="200"><tr><td>
              <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(25,0,100);" name="<?php echo $paramname; ?>" type="checkbox" value="25" <?= $c[25] ?>/></td><td>All Regions</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>
              </td></tr></table>

                   <table width="700" border="0" cellpadding="1" cellspacing="0">
                   <tr>
                   <!-----COLUMN 1----->
                   <td>

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1">
                              <tr><td style="padding-left:3px;">
                                 <input onClick="CheckAllSelected(25,0,new Array());" name="<?php echo $paramname; ?>" type="checkbox" value="26" <?= $c[26] ?>/></td><td>Alaska</td>
                             </tr>
                      </table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="3" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="../images/spacer.gif" width="1" height="4" alt="Spacer"></td></tr></table>
                   <!--table border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="26" <?= $c[26] ?>/></td><td>Alaska</td></tr></table-->

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" onClick="CheckAllSelected(25,0,new Array());" type="checkbox" value="27" <?= $c[27] ?>/></td><td>California</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="3" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="../images/spacer.gif" width="1" height="4" alt="Spacer"></td></tr></table>
                   <!--table border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="27" <?= $c[27] ?>/></td><td>California</td></tr></table!-->

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,0,new Array());" name="<?php echo $paramname; ?>" type="checkbox" value="28" <?= $c[28] ?>/></td><td>Hawaii</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="3" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="../images/spacer.gif" width="1" height="4" alt="Spacer"></td></tr></table>
                   <!--table border="0" cellpadding="0" cellspacing="0"><tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="28" <?= $c[28] ?>/></td><td>Hawaii</td></tr></table-->

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="selectRegions(21,new Array(31,30,29)); CheckAllSelected(25,0,new Array(31,30,29));" name="<?php echo $paramname; ?>" type="checkbox" value="21" <?= $c[21] ?>/></td><td>Northwest</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,21,new Array(31,30,29));" name="<?php echo $paramname; ?>" type="checkbox" value="31" <?= $c[31] ?>/></td><td>Idaho</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,21,new Array(31,30,29));" name="<?php echo $paramname; ?>" type="checkbox" value="29" <?= $c[29] ?>/></td><td>Montana</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,21,new Array(31,30,29));" name="<?php echo $paramname; ?>" type="checkbox" value="30" <?= $c[30] ?>/></td><td>Wyoming</td></tr>
                   </table>

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="selectRegions(23,new Array(32,33)); CheckAllSelected(25,0,new Array(32,33));"  name="<?php echo $paramname; ?>" type="checkbox" value="23" <?= $c[23] ?>/></td><td>Pacific Northwest</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,23,new Array(32,33));" name="<?php echo $paramname; ?>" type="checkbox" value="32" <?= $c[32] ?>/></td><td>Oregon</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,23,new Array(32,33));" name="<?php echo $paramname; ?>" type="checkbox" value="33" <?= $c[33] ?>/></td><td>Washington</td></tr>
                   </table>

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(15,34,42); CheckAllSelected(25,0,new Array(34,35,36,37,38,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="15" <?= $c[15] ?>/></td><td>Northeast</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="34" <?= $c[34] ?>/></td><td>Connecticut</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="35" <?= $c[35] ?>/></td><td>Maine</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="36" <?= $c[36] ?>/></td><td>Massachusetts</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="37" <?= $c[37] ?>/></td><td>New Hampshire</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="38" <?= $c[38] ?>/></td><td>New Jersey</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="39" <?= $c[39] ?>/></td><td>New York</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="40" <?= $c[40] ?>/></td><td>Pennsylvania</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="41" <?= $c[41] ?>/></td><td>Rhode Island</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));" name="<?php echo $paramname; ?>" type="checkbox" value="42" <?= $c[42] ?>/></td><td>Vermont</td></tr>
                   </table>

                   </td>

                   <!-----COLUMN 2----->
                   <td valign="top">

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1">
                         <tr>
                            <td style="padding-left:3px;"><input onClick="SelectAll(16,43,54); CheckAllSelected(25,0,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="16" <?= $c[16] ?>></td>
                            <td>Midwest</td>
                         </tr>
                      </table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="43" <?= $c[43] ?>/></td><td>Illinois</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="44" <?= $c[44] ?>/></td><td>Indiana</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="45" <?= $c[45] ?>/></td><td>Iowa</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="46" <?= $c[46] ?>/></td><td>Kansas</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="47" <?= $c[47] ?>/></td><td>Michigan</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="48" <?= $c[48] ?>/></td><td>Minnesota</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="49" <?= $c[49] ?>/></td><td>Missouri</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="50" <?= $c[50] ?>/></td><td>Nebraska</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="51" <?= $c[51] ?>/></td><td>North Dakota</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="52" <?= $c[52] ?>/></td><td>Ohio</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="53" <?= $c[53] ?>/></td><td>South Dakota</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));" name="<?php echo $paramname; ?>" type="checkbox" value="54" <?= $c[54] ?>/></td><td>Wisconsin</td></tr>
                   </table>

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(22,55,59); CheckAllSelected(25,0,new Array(56,57,58,59));" name="<?php echo $paramname; ?>" type="checkbox" value="22" <?= $c[22] ?>/></td><td>Mid-Atlantic</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,22,new Array(56,57,58,59));" name="<?php echo $paramname; ?>" type="checkbox" value="55" <?= $c[55] ?>/></td><td>Delaware</td></tr>
                      <tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="56" <?= $c[56] ?>/></td><td>District of Columbia</td></tr>
                      <tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="57" <?= $c[57] ?>/></td><td>Maryland</td></tr>
                      <tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="58" <?= $c[58] ?>/></td><td>Virginia</td></tr>
                      <tr><td style="padding-left:3px;"><input name="<?php echo $paramname; ?>" type="checkbox" value="59" <?= $c[59] ?>/></td><td>West Virginia</td></tr>
                   </table>

                   </td>

                   <!-----COLUMN 3----->
                   <td valign="top">

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(24,60,66); CheckAllSelected(25,0,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="24" <?= $c[24] ?>/></td><td>Southwest</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="60" <?= $c[60] ?>/></td><td>Arizona</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="61" <?= $c[61] ?>/></td><td>Colorado</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="63" <?= $c[63] ?>/></td><td>Nevada</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="62" <?= $c[62] ?>/></td><td>New Mexico</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="64" <?= $c[64] ?>/></td><td>Oklahoma</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="65" <?= $c[65] ?>/></td><td>Texas</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));" name="<?php echo $paramname; ?>" type="checkbox" value="66" <?= $c[66] ?>/></td><td>Utah</td></tr>
                   </table>

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(19,67,76); CheckAllSelected(25,0,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="19" <?= $c[19] ?>/></td><td>Southeast</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="67" <?= $c[67] ?>/></td><td>Alabama</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="68" <?= $c[68] ?>/></td><td>Arkansas</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="69" <?= $c[69] ?>/></td><td>Florida</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="70" <?= $c[70] ?>/></td><td>Georgia</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="71" <?= $c[71] ?>/></td><td>Kentucky</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="72" <?= $c[72] ?>/></td><td>Louisiana</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="73" <?= $c[73] ?>/></td><td>Mississippi</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="74" <?= $c[74] ?>/></td><td>North Carolina</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="75" <?= $c[75] ?>/></td><td>South Carolina</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));" name="<?php echo $paramname; ?>" type="checkbox" value="76" <?= $c[76] ?>/></td><td>Tennessee</td></tr>
                   </table>

                   </td>

                   <!-----COLUMN 4----->
                   <td valign="top">

                   <div class="roundcont" style="background-color:<?= $bgcolor ?>">
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td><img src="<?= $topLeft ?>" width="4" height="4"/></td><td align="right"><img src="<?= $topRight ?>" width="4" height="4" /></td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" class="region1"><tr><td style="padding-left:3px;"><input onClick="SelectAll(14,77,89); CheckAllSelected(25,0,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="14" <?= $c[14] ?>/></td><td>Canada</td></tr></table>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td style="padding:0px;"><img src="<?= $botLeft ?>" width="4" height="4" ></td><td align="right" style="padding:0px;"><img src="<?= $botRight ?>" width="4" height="4"></td></tr></table>
                   </div>

                   <table border="0" cellpadding="0" cellspacing="0" class="region2">
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="77" <?= $c[77] ?>/></td><td>Alberta</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="78" <?= $c[78] ?>/></td><td>British Columbia</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="79" <?= $c[79] ?>/></td><td>Manitoba</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="80" <?= $c[80] ?>/></td><td>New Brunswick</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="81" <?= $c[81] ?>/></td><td>Newfoundland</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="82" <?= $c[82] ?>/></td><td>NW Territories</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="83" <?= $c[83] ?>/></td><td>Nunavut</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="84" <?= $c[84] ?>/></td><td>Nova Scotia</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="85" <?= $c[85] ?>/></td><td>Ontario</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="86" <?= $c[86] ?>/></td><td>Pr. Edward Island</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="87" <?= $c[87] ?>/></td><td>Quebec</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="88" <?= $c[88] ?>/></td><td>Saskatchewan</td></tr>
                      <tr><td style="padding-left:3px;"><input onClick="CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));" name="<?php echo $paramname; ?>" type="checkbox" value="89" <?= $c[89] ?>/></td><td>Yukon</td></tr>
                   </table>
                   </td></tr>
                   </table>
 <?php
   }//end printregions




   
   
   
   
   
   function getRegionSelectionBoxes($selected,$paramname=NULL,$onchg=NULL){
      if ($paramname==NULL) $paramname="region[]";
      
      $c = array();
      if(!is_array($selected)) $selected=separateStringBy($selected,",",NULL,TRUE);
      for($i=0;$i<count($selected);$i++){
         $c[intval($selected[$i])] = "CHECKED";
      }
      
      $str = "";
      
      $str .= "\n<script type=\"text/javascript\">\n";
      $str .= "function wd_DisableRegions(dis) {\n";
      $str .= "  var inputs = document.getElementsByName('".$paramname."');\n";
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.name == '".$paramname."') && (e.type=='checkbox') && (e.disabled != dis)) {\n";
      $str .= "      e.checked = false;\n";                
      $str .= "      e.disabled = dis;\n";
      $str .= "    }\n";
      $str .= "  }\n";                                                                  
      $str .= "}\n";
      $str .= "function wd_SelectAll(ca,st,end){\n";                                                                    
      $str .= "  var checked = false;\n";
      $str .= "  var inputs = document.getElementsByName('".$paramname."');\n";
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.value == ca) && (e.type=='checkbox')) checked = e.checked;\n";                  
      $str .= "  }\n";                                                                  
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.name == '".$paramname."') && (e.value!= ca) && (e.value <= end) && (e.value >= st) && (e.type=='checkbox')) e.checked = checked;\n";                  
      $str .= "  }\n";                                                                  
      $str .= "}\n";
      $str .= "function wd_selectRegions(ca,valArr){\n";
      $str .= "  var checked = false;\n";
      $str .= "  var inputs = document.getElementsByName('".$paramname."');\n";
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.value == ca) && (e.type=='checkbox')) checked = e.checked;\n";                  
      $str .= "  }\n";                                                                  
      $str .= "  for (x in valArr) {\n";
      $str .= "    for (var i=0;i<inputs.length;i++){\n";
      $str .= "      var e = inputs[i];\n";
      $str .= "      if ((e.name == '".$paramname."') && (e.value!= ca) && (e.value == valArr[x]) && (e.type=='checkbox')) e.checked = checked;\n";                  
      $str .= "    }\n";                                                                  
      $str .= "  }\n";
      $str .= "}\n";
      $str .= "function wd_CheckAllSelected(ca,par,valArr){\n";                                                                    
      $str .= "  var parentChecked = true;\n";
      $str .= "  var allChecked = true;\n";
      $str .= "  var inputs = document.getElementsByName('".$paramname."');\n";
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.value != ca) && (e.value != par) && (e.name == '".$paramname."') && (e.type=='checkbox')){\n";                                                                
      $str .= "      if (!e.checked) {\n";
      $str .= "        allChecked = false;\n";
      $str .= "        for (x in valArr) if (e.value == valArr[x]) parentChecked = false;\n";
      $str .= "        if (parentChecked != true) break;\n";
      $str .= "      }\n";
      $str .= "    }\n";                                                                
      $str .= "  }\n";                                                                  
      $str .= "  for (var i=0;i<inputs.length;i++){\n";
      $str .= "    var e = inputs[i];\n";
      $str .= "    if ((e.value == ca) && (e.name == '".$paramname."') && (e.type=='checkbox')) e.checked = allChecked;\n";
      $str .= "    if ((e.value == par) && (e.name == '".$paramname."') && (e.type=='checkbox')) e.checked = parentChecked;\n";
      $str .= "  }\n";
      $str .= "}\n";                                                                    
      $str .= "</script>\n"; 
        
        //outer div - contains pillars
        $str .= "<div style=\"position:relative;\">";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(25,0,100);\" name=\"".$paramname."\" type=\"checkbox\" value=\"25\" ".$c[25]."/> All Regions";
        $str .= "</div>";
        
        //pillar 1 of 4
        $str .= "<div style=\"float:left;margin-right:2px;margin-top:2px;\">";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,0,new Array());\" name=\"".$paramname."\" type=\"checkbox\" value=\"26\" ".$c[26]."/> Alaska";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,0,new Array());\" name=\"".$paramname."\" type=\"checkbox\" value=\"27\" ".$c[27]."/> California";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,0,new Array());\" name=\"".$paramname."\" type=\"checkbox\" value=\"28\" ".$c[28]."/> Hawaii";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_selectRegions(21,new Array(31,30,29)); wd_CheckAllSelected(25,0,new Array(31,30,29));\" name=\"".$paramname."\" type=\"checkbox\" value=\"21\" ".$c[21]."/> Northwest";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,21,new Array(31,30,29));\" name=\"".$paramname."\" type=\"checkbox\" value=\"31\" ".$c[31]."/> Idaho";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,21,new Array(31,30,29));\" name=\"".$paramname."\" type=\"checkbox\" value=\"29\" ".$c[29]."/> Montana";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,21,new Array(31,30,29));\" name=\"".$paramname."\" type=\"checkbox\" value=\"30\" ".$c[30]."/> Wyoming";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_selectRegions(23,new Array(32,33)); wd_CheckAllSelected(25,0,new Array(32,33));\"  name=\"".$paramname."\" type=\"checkbox\" value=\"23\" ".$c[23]."/> Pacific Northwest";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,23,new Array(32,33));\" name=\"".$paramname."\" type=\"checkbox\" value=\"32\" ".$c[32]."/> Oregon";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,23,new Array(32,33));\" name=\"".$paramname."\" type=\"checkbox\" value=\"33\" ".$c[33]."/> Washington";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(15,34,42); wd_CheckAllSelected(25,0,new Array(34,35,36,37,38,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"15\" ".$c[15]."/> Northeast";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"34\" ".$c[34]."/> Connecticut";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"35\" ".$c[35]."/> Maine";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"36\" ".$c[36]."/> Massachusetts";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"37\" ".$c[37]."/> New Hampshire";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"38\" ".$c[38]."/> New Jersey";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"39\" ".$c[39]."/> New York";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"40\" ".$c[40]."/> Pennsylvania";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"41\" ".$c[41]."/> Rhode Island";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,15,new Array(35,36,37,37,39,40,41,42));\" name=\"".$paramname."\" type=\"checkbox\" value=\"42\" ".$c[42]."/> Vermont";
        $str .= "</div>";
        $str .= "</div>";

        //pillar 2 of 4
        $str .= "<div style=\"float:left;margin-right:2px;margin-top:2px;\">";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(16,43,54); wd_CheckAllSelected(25,0,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"16\" ".$c[16]."> Midwest";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"43\" ".$c[43]."> Illinois";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"44\" ".$c[44]."> Indiana";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"45\" ".$c[45]."> Iowa";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"46\" ".$c[46]."> Kansas";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"47\" ".$c[47]."> Michigan";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"48\" ".$c[48]."> Minnesota";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"49\" ".$c[49]."> Missouri";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"50\" ".$c[50]."> Nebraska";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"51\" ".$c[51]."> North Dakota";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"52\" ".$c[52]."> Ohio";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"53\" ".$c[53]."> South Dakota";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,16,new Array(43,44,45,46,47,48,49,50,51,52,53,54));\" name=\"".$paramname."\" type=\"checkbox\" value=\"54\" ".$c[54]."> Wisconsin";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(22,55,59); wd_CheckAllSelected(25,0,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"22\" ".$c[22]."> Mid-Atlantic";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,22,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"55\" ".$c[55]."> Delaware";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,22,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"56\" ".$c[56]."> District of Columbia";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,22,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"57\" ".$c[57]."> Maryland";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,22,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"58\" ".$c[58]."> Virginia";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,22,new Array(56,57,58,59));\" name=\"".$paramname."\" type=\"checkbox\" value=\"59\" ".$c[59]."> West Virginia";
        $str .= "</div>";
        $str .= "</div>";

        //pillar 3 of 4
        $str .= "<div style=\"float:left;margin-right:2px;margin-top:2px;\">";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(24,60,66); wd_CheckAllSelected(25,0,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"24\" ".$c[24]."> Southwest";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"60\" ".$c[60]."> Arizona";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"61\" ".$c[61]."> Colorado";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"63\" ".$c[63]."> Nevada";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"62\" ".$c[62]."> New Mexico";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"64\" ".$c[64]."> Oklahoma";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"65\" ".$c[65]."> Texas";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,24,new Array(60,61,62,63,64,65,66));\" name=\"".$paramname."\" type=\"checkbox\" value=\"66\" ".$c[66]."> Utah";
        $str .= "</div>";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(19,67,76); wd_CheckAllSelected(25,0,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"19\" ".$c[19]."> Southeast";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"67\" ".$c[67]."> Alabama";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"68\" ".$c[68]."> Arkansas";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"69\" ".$c[69]."> Florida";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"70\" ".$c[70]."> Georgia";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"71\" ".$c[71]."> Kentucky";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"72\" ".$c[72]."> Louisiana";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"73\" ".$c[73]."> Mississippi";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"74\" ".$c[74]."> North Carolina";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"75\" ".$c[75]."> South Carolina";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,19,new Array(67,68,69,70,71,72,73,74,75,76));\" name=\"".$paramname."\" type=\"checkbox\" value=\"76\" ".$c[76]."> Tennessee";
        $str .= "</div>";
        $str .= "</div>";

                   
        //pillar 4 of 4
        $str .= "<div style=\"float:left;margin-right:2px;margin-top:2px;\">";
        $str .= "<div style=\"margin-bottom:1px;padding:4px;background-color:#999999;color:#FFFFFF;border-radius:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_SelectAll(14,77,89); wd_CheckAllSelected(25,0,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"14\" ".$c[14]."> Canada";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"77\" ".$c[77]."> Alberta";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"78\" ".$c[78]."> British Columbia";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"79\" ".$c[79]."> Manitoba";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"80\" ".$c[80]."> New Brunswick";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"81\" ".$c[81]."> Newfoundland";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"82\" ".$c[82]."> NW Territories";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"83\" ".$c[83]."> Nunavut";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"84\" ".$c[84]."> Nova Scotia";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"85\" ".$c[85]."> Ontario";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"86\" ".$c[86]."> Pr. Edward Island";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"87\" ".$c[87]."> Quebec";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"88\" ".$c[88]."> Saskatchewan";
        $str .= "</div>";
        $str .= "<div style=\"padding:3px;\">";
        $str .= "<input onClick=\"".$onchg."wd_CheckAllSelected(25,14,new Array(77,78,79,80,81,82,83,84,85,86,87,88,89));\" name=\"".$paramname."\" type=\"checkbox\" value=\"89\" ".$c[89]."> Yukon";
        $str .= "</div>";
        $str .= "</div>";
        
        $str .= "<div style=\"clear:both;\"></div>";
        $str .= "</div>";
        return $str;
   }

   
   function checkSurveyAccess($wd_id,$email,$origemail){
      $resultinguser = NULL;
      $sci = $this->getCodedRow($wd_id,$origemail);
      if($sci!=NULL && $sci['wd_row_id']!=NULL && $sci['userid']!=NULL){
         $cuser = $ua->getUser($sci['userid']);
         $cuser['related'] = FALSE;
         if(0!=strcmp($cuser['email'],$email)){
            $adminrel = $ua->getUsersRelated($sci['userid'],"to","SRVYADMIN");
            $cuser = $ua->getUser($adminrel[0]['reluserid']);
            $cuser['related'] = TRUE;
         }         
         if(0==strcmp($cuser['email'],$email)){
            $resultinguser = $cuser;
         }
      } else {
         $sci = NULL;
      }
      
      $resp = array();
      $resp['row'] = $sci;
      $resp['user'] = $resultinguser;
   }
   
   
   
   

   //-------------------------------------------------------------------------
   //-------------------------------------------------------------------------
   // Print form for json requests
   //-------------------------------------------------------------------------
   //-------------------------------------------------------------------------
   function getJSONForm($wd_id, $wdname=NULL, $origemail=NULL, $userid=NULL, $wd_row_id=NULL, $prefix=NULL, $printstuff=FALSE, $callback=NULL, $paged=FALSE, $page=NULL, $password=NULL, $noemail=0, $explicitcss=0, $userelationships=TRUE, $admin=0, $forcefull=0, $email=NULL) {
      if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." getJSONForm(".$wd_id.",".$wdname.",".$origemail.",".$userid.",".$wd_row_id.",".$prefix.",".$printstuff.",".$callback.",".$paged.",".$page.",".$password.",".$noemail.",".$explicitcss.",".$userelationships.",".$admin.",".$forcefull.")<br>\n";
      $ua = new UserAcct();
      
      $returnobj = array();
      $style = "";
      if ($wd_id==NULL) $wd_id = getParameter("wd_id");
      if ($wd_id==NULL) $wd_id = $wdname;
      if ($wd_id==NULL) $wd_id = getParameter("wdname");
      $webdata = $this->getWebData($wd_id);
      if($prefix==NULL) $prefix="jsfwd";

      if ($printstuff) print "<br>\n".date("m/d/Y H:i:s")." wd_id: ".$webdata['wd_id']."<br>\n";

      if ($webdata['password']!=NULL && 0!=strcmp($password,$webdata['password'])) $webdata=NULL;

      if ($webdata!=NULL && $webdata['wd_id']>0) {
         if ($userid==NULL) $userid = trim(getParameter("userid"));
         if ($origemail==NULL) $origemail = getParameter("origemail");
         if ($email==NULL) $email = getParameter("email");
         if ($wd_row_id==NULL) $wd_row_id = getParameter("wd_row_id");
         
         if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." getRow(".$wd_id.",".$wd_row_id.",".$origemail.");";
         $row = $this->getRow($webdata['wd_id'],$wd_row_id,$origemail,FALSE,$printstuff);
         if ($printstuff) {
            print "\n<br>".date("m/d/Y H:i:s")." Results:<br>\n";
            print_r($row);
            print "\n<br>";
            print "\n<br>";
            print "\n<br>";
         }

         
         $hasacct = FALSE;
         if($webdata['privatesrvy']==1 && $row['userid']>0) $hasacct = TRUE;
         if(!$paged) $page = 1;
         else if ($hasacct && ($page==NULL || !is_numeric($page))) $page = -2;
         else if ($page==NULL || !is_numeric($page)) $page=1;
         
         // Only for private surveys:
         // Users need to be first added to survey, then verified
         $showlogon = FALSE;
         if($webdata['privatesrvy']==1) {
            $showlogon = TRUE;
            if($row['userid']!=NULL && $userid!=NULL && $userid==$row['userid']) {
               $showlogon=FALSE;
            } else if($row['userid']!=NULL) {
               $user = $ua->getUser($email);
               if($user==NULL || $user['userid']==NULL) $user=$ua->getUser($userid);
               if($user['userid']==$row['userid']) {
                  $showlogon=FALSE;
               } else {
                  $adminrel = $ua->getUsersRelated($user['userid'],"both","SRVYADMIN");
                  for($i=0;$i<count($adminrel);$i++) {
                     if($adminrel[$i]['reluserid']==$row['userid'] || $adminrel[$i]['userid']==$row['userid']) {
                        $showlogon=FALSE;
                        break;
                     }
                  }
               }
            }
         }
         if($showlogon) {
            $page = -1;
            $row = NULL;
         }
         

         if ($userid==NULL && $row!=NULL) $userid = $row['userid'];

         if ($webdata['privatesrvy']!=7 || ($forcefull==1)) {
            if ($paged && $callback==NULL) $callback = $prefix."pagedcallback";
            else if ($callback==NULL) $callback = $prefix."callback";
   
            if ($paged) $jsonuri = "&action=submitwebdatapage";
            else $jsonuri = "&action=submitwebdata";
   
            $jsonuri .= "&wd_id=".$webdata['wd_id'];
            $jsonuri .= "&ignorenull=1";
            $jsonuri .= "&forcefull=1";
            $jsonuri .= "&prefix=".$prefix;
            if ($paged) $jsonuri .= "&smpg=".$page;
            if ($userid!=NULL) $jsonuri .= "&userid=".$userid;
            //if ($row['wd_row_id']!=NULL) $jsonuri .= "&wd_row_id=".$row['wd_row_id'];
            if ($noemail==1) $jsonuri .= "&noemail=1";
            if ($explicitcss==1) $jsonuri .= "&explicitcss=1";
            if ($printstuff) $jsonuri .= "&testing=1";
            if ($admin==1) $jsonuri .= "&admin=1";
      
            $html = "";
            $js = "";
            $relationshipjs1 = "";
            $relationshipjs2 = "";
   
            $glossaryid=$webdata['glossaryid'];
            $glossary = NULL;
            if($glossaryid!=NULL) $glossary = new Glossary($glossaryid);
            $html .= $glossary->getjscript();

            if ($webdata['captcha']==1 && $page==1 && $wd_row_id==NULL) {
               $js .= "\nvar ".$prefix."captcha='';\n\n";
               $js .= "\nfunction ".$prefix."InitializeCaptcha(){\n";
               $js .= "   var chars = '2346789abcdefghjkmnprtuvwxyz';\n";
               $js .= "   var tempcaptcha;\n";
               $js .= "   var tempstr = '';\n";
               $js .= "   var tempcnt = 0;\n";
               $js .= "   while (tempcnt < 6) {\n";
               $js .= "      tempcaptcha = Math.floor(Math.random() * chars.length);\n";
               $js .= "      tempstr = tempstr + chars.substring(tempcaptcha,(tempcaptcha+1));\n";
               $js .= "      tempcnt = tempcnt + 1;\n";
               $js .= "   }\n";
               $js .= "   ".$prefix."captcha = tempstr;\n";
               $js .= "   var tempimg = '<img src=\\\"".getBaseURL()."secimage/' + tempstr + '.jpg\\\">';\n";
               $js .= "   jQuery('#".$prefix."captchaimage').html(tempimg);\n";
               $js .= "}\n\n";
               $js .= $prefix."InitializeCaptcha();\n\n";
            }

            $js .= "\nwindow.addEventListener('message', ".$prefix."ReceiveMessage, false);\n";
            $js .= "\nfunction ".$prefix."ReceiveMessage(e){\n";
            $js .= "var databack = e.data;\n";
            $js .= "var databack_a = databack.split(',');\n";
            $js .= "if(databack_a[0]=='esign') {\n";
            $js .= "   ".$prefix."_sendsignatureback(databack_a[1]);\n";
            $js .= "} else {\n";
            $js .= "   var p = databack_a[0];\n";
            $js .= "   var f = databack_a[1];\n";
            $js .= "   var w = databack_a[2];\n";
            $js .= "   var fn = databack_a[3];\n";
            $js .= "   var funct = 'jsfwdimg_' + p + 'w' + w + 'a' + f;\n";
            $js .= "   window[funct](fn);\n";
            $js .= "}\n";
            $js .= "}\n\n";

            $js .= "\nvar ".$prefix."_wri='".$row['wd_row_id']."';\n";
            $js .= "\nvar ".$prefix."_oe='".$row['origemail']."';\n";
            $js .= "\nvar ".$prefix."_e='".$email."';\n";
            $js .= "\nvar ".$prefix."_cb;\n";
            $js .= "\nvar ".$prefix."_nextpg;\n";
            $js .= "\nvar ".$prefix."formchanges=false;\n";
            $js .= "\nvar ".$prefix."chgflds={};\n";
            $js .= "\nfunction formchange_".$prefix."(fld){\n";
            $js .= "  if(Boolean(fld)) ".$prefix."chgflds[fld]=1;\n";
            $js .= "  ".$prefix."formchanges=true;\n";
            $js .= "}\n";            
            $js .= "\nfunction chk_".$prefix."(cb){\n";
            $js .= "  ".$prefix."_cb = cb;\n";
            $js .= "  if(!Boolean(".$prefix."_wri)){\n";
            $js .= "    var url= defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=chkret_".$prefix."';\n";
            $js .= "    url += '&action=submitwd';\n";
            $js .= "    url += '&skipuser=1';\n";
            $js .= "    url += '&token=222_315_2008_32477';\n";
            if ($userid!=NULL) $js .= "url += '&userid=".$userid."';\n";
            $js .= "    url += '&wd_id=".$webdata['wd_id']."';\n";
            $js .= "    url += '&comments=w".$webdata['wd_id']."';\n";
            //$js .= "    alert('***chj*** chk_ url: ' + url);\n";
            $js .= "    jsfwebdata_CallJSONP(url);\n";            
            $js .= "  } else {\n";
            $js .= "    jsondata = [];\n";
            $js .= "    jsondata.wd_row_id = ".$prefix."_wri;\n";
            $js .= "    jsondata.origemail = ".$prefix."_oe;\n";
            $js .= "    jsondata.email = ".$prefix."_e;\n";
            //$js .= "    alert('***chj*** chk_ row was already created.');\n";
            $js .= "    window[".$prefix."_cb](jsondata);\n";
            $js .= "  }\n";
            $js .= "}\n";
            
            $js .= "\nfunction chkret_".$prefix."(jsondata){\n";
            //$js .= "  alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
            $js .= "  if(typeof jsfwd_testing !== 'undefined' && Boolean(jsfwd_testing)) alert('chkret jsondata: ' + JSON.stringify(jsondata));\n";
            $js .= "  if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
            $js .= "  ".$prefix."_wri=jsondata.wd_row_id;\n";
            $js .= "  ".$prefix."_oe=jsondata.origemail;\n";
            $js .= "  ".$prefix."_e=jsondata.email;\n";
            $js .= "   if (typeof(window.localStorage)!='undefined') {\n";
            $js .= "      window.localStorage.setItem('".$webdata['wd_id']."_wri',".$prefix."_wri);\n";
            $js .= "      window.localStorage.setItem('".$webdata['wd_id']."_oe',".$prefix."_oe);\n";
            $js .= "      window.localStorage.setItem('".$webdata['wd_id']."_e',".$prefix."_e);\n";
            $js .= "      window.localStorage.setItem('".$prefix."_e',".$prefix."_e);\n";
            $js .= "   }\n";            
            $js .= "  window[".$prefix."_cb](jsondata);\n";
            $js .= "}\n";
            
            $js .= "\nfunction ".$prefix."SubmitWDForm(nextpg,skiphistory){\n";
            $js .= "  ".$prefix."_nextpg = nextpg;\n";
            if($showlogon) {
               $js .= $prefix."SubmitWD_ret('');\n";
            } else {
               $js .= "  var cb = '".$prefix."SubmitWD_ret';\n";
               $js .= "  chk_".$prefix."(cb);\n";
            }
            $js .= "}\n";
            
            $js .= "\nfunction ".$prefix."SubmitWD_ret(jsondata){\n";
            //$js .= "  alert('***chj*** chkret jsondata: ' + JSON.stringify(jsondata));\n";
            $js .= "   nextpg = ".$prefix."_nextpg;\n";
            $js .= "   var rqderror = false;\n";
            $js .= "   var rqderrorstr = '';\n";
            $js .= "   var c_urls = [];\n";
            $js .= "   var c_url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=".$callback."';\n";
            $js .= "   if(Boolean(".$prefix."_wri)) c_url = c_url + '&wd_row_id=' + ".$prefix."_wri;\n";
            $js .= "   if(Boolean(".$prefix."_oe)) c_url = c_url + '&origemail=' + ".$prefix."_oe;\n";
            $js .= "   if(Boolean(".$prefix."_e)) c_url = c_url + '&email=' + ".$prefix."_e;\n";
            $js .= "   c_url = c_url + '".$jsonuri."';\n";
            $js .= "   if (Boolean(nextpg)) c_url = c_url + '&nextpg=' + nextpg;\n"; 
            $js .= "   var url = c_url;\n";
            $js .= "   var temp='';\n";
      
            $relationshipjs1 .= "function ".$prefix."InitializeWDRelationships(){\n";
            $relationshipjs2 .= "function ".$prefix."CheckWDRelationships(){\n";
            $relationshipjs2 .= "   ".$prefix."InitializeWDRelationships();\n";

            $html .= "<div id=\"".$prefix."area_error\" class=\"".$prefix."area_error\"".$style."></div>";

   
            if ($explicitcss==1) $style=" style=\"position:relative;\"";
            $html .= "<div id=\"".$prefix."wd\" class=\"".$prefix."wd\"".$style.">";
            if ($explicitcss==1) $style=" style=\"font-size:18px;color:#5a5a5a;font-weight:bold;font-family:arial;margin-bottom:10px;\"";
            $html .= "<div id=\"".$prefix."wdlbl\" class=\"".$prefix."wdlbl\"".$style.">";
            $dispname = $webdata['name'];
            //if ($dispname!=null && $glossary!=NULL) $dispname = $glossary->flagAllTerms($dispname,"#5691c4");
            $html .= $dispname;
            $html .= "</div>";
            
            $style = " style=\"display:none;\"";
            if ($explicitcss==1) $style = " style=\"font-size:14px;color:#777777;font-weight:normal;font-family:arial;margin-bottom:5px;display:none;\"";
            $html .= "<div id=\"".$prefix."wdinfo\" class=\"".$prefix."wdinfo\"".$style.">";
            //$dispinfo = $webdata['info'];
            $template = new Template();
            $dispinfo = $contents = $template->doSubstitutions(convertBack($webdata['info']));
            //if ($dispinfo!=null && $glossary!=NULL) $dispinfo = $glossary->flagAllTerms($dispinfo,"#5691c4");
            $html .= $dispinfo;
            $html .= "</div>";
            $style = "";

            $html .= "\n<script>\n";
            $html .= "\nfunction ".$prefix."_showwdinfo(){\n";
            $html .= "jQuery('#".$prefix."wdinfo').show();\n";
            $html .= "jQuery('#".$prefix."wdinfo_show').hide();\n";
            $html .= "jQuery('#".$prefix."wdinfo_hide').show();\n";
            $html .= "}\n";
            $html .= "\nfunction ".$prefix."_hidewdinfo(){\n";
            $html .= "jQuery('#".$prefix."wdinfo').hide();\n";
            $html .= "jQuery('#".$prefix."wdinfo_show').show();\n";
            $html .= "jQuery('#".$prefix."wdinfo_hide').hide();\n";
            $html .= "}\n";
            $html .= "\n</script>\n";
            
            if($webdata['info']!=NULL) {
               $html .= "<div onclick=\"".$prefix."_showwdinfo();\" id=\"".$prefix."wdinfo_show\" style=\"color:blue;font-size:10px;margin-bottom:15px;cursor:pointer;\">";
               $html .= "&#x2295; Show More ";
               $html .= "</div>";
               $html .= "<div onclick=\"".$prefix."_hidewdinfo();\" id=\"".$prefix."wdinfo_hide\" style=\"color:blue;font-size:10px;margin-bottom:15px;cursor:pointer;display:none;\">";
               $html .= "&#x2296; Show Less ";
               $html .= "</div>";
            }

            //$html .= $glossary->getjscript();

            $sect = -1;
            $sections = $this->getDataSections($webdata['wd_id'],-1);
            if($page==-1) {
            //if($showlogon) {
               $returnobj = $this->getJSONWebDataLogin($webdata['wd_id'],$prefix,$email);
               $html .= "\n<script>".$prefix."_showwdinfo();</script>\n";
            } else if($page==-2) {
               $returnobj = $this->getJSONWebAccount($webdata['wd_id'],$prefix,$row['userid']);
            } else {
               if ($paged) {
                  $sect_page = $page - 1;
                  //if($webdata['privatesrvy']==1 && $webdata['userid']>0) $sect_page = $sect_page - 1;
                  if ($printstuff) {
                     print "<br>\n".date("m/d/Y H:i:s")." paged version. Sections:<br>\n";
                     print_r($sections);
                     print "<br>\n<br>\n";
                  }
                  $sect = $sections[$sect_page]['section'];
               }
               //$returnobj = $this->getJSONWebDataSection($webdata['wd_id'], $sect, $row, $prefix, $printstuff, $explicitcss, $userelationships, 0, $glossary, $userid);
               $returnobj = $this->getJSONWebDataSection($webdata['wd_id'], -1, $row, $prefix, $printstuff, $explicitcss, $userelationships, 0, $glossary, $userid, $sect);
            }
   
            if ($printstuff) {
               print "<br>\n".date("m/d/Y H:i:s")." return from getJSONWebDataSection():<br>\n";
               print_r($returnobj);
               print "\n\n<br><br>\n";
            }
            $html .= $returnobj['html'];
            $js .= $returnobj['js'];
            $relationshipjs1 .= $returnobj['relationshipjs1'];
            $relationshipjs2 .= $returnobj['relationshipjs2'];
      
            if($webdata['field2']!=NULL && 0==strcmp($webdata['field2'],"displayupdateby") && $row!=NULL && $row['lastupdateby']!=NULL) {
                $html .= "<div style=\"clear:both;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\">";
                $html .= "Last updated by: ".$row['lastupdateby'];
                //$html .= " on ".date("m/d/Y H:i:s");
                $html .= "</div>";
            }
            
            if($webdata['password']!=NULL && strlen($webdata['password'])>0) {
               if ($explicitcss==1) $style=" style=\"clear:both;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
               $html .= "<div id=\"".$prefix."wdpw\" class=\"".$prefix."wdrow\"".$style.">";
               if ($explicitcss==1) $style=" style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
               $html .= "<div id=\"".$prefix."wdq_pw\" class=\"".$prefix."wdq\"".$style.">";
               $html .= "Enter Password";
               $html .= "</div>";
               if ($explicitcss==1) $style=" style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
               $html .= "<div id=\"".$prefix."wda_pw\" class=\"".$prefix."wda\"".$style.">";         
               if ($explicitcss==1) $style=" style=\"font-size:12px;font-family:arial;width:230px;color:#222222;\"";
               $html .= "<input type=\"password\" name=\"w".$webdata['wd_id']."password\" value=\"\" id=\"".$prefix."w".$webdata['wd_id']."password\" class=\"".$prefix."winput_txt\"".$style.">";
               $html .= "</div>";
               $html .= "</div>";
            }
            if($webdata['captcha']==1 && $page==1 && $wd_row_id==NULL) {
               $js .= "   if (!rqderror) {\n";
               $js .= "   temp = jQuery('#".$prefix."w".$webdata['wd_id']."captcha').val();\n";
               $js .= "   if (!Boolean(temp) || temp.toLowerCase()!=".$prefix."captcha) {\n";
               $js .= "      rqderror = true;\n";
               $js .= "      jQuery('#".$prefix."w".$webdata['wd_id']."captcha').css('border','2px solid RED');\n";
               $js .= "      rqderrorstr = rqderrorstr + 'Please enter the correct security code to continue.';\n";
               $js .= "   }\n";
               $js .= "   }\n";
   
               if ($explicitcss==1) $style=" style=\"clear:both;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
               $html .= "<div id=\"".$prefix."wdcaptcha\" class=\"".$prefix."wdrow\"".$style.">";
               if ($explicitcss==1) $style=" style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
               $html .= "<div id=\"".$prefix."wdq_captcha\" class=\"".$prefix."wdq\"".$style.">";
               $html .= "Please enter the Security Code in the image to continue";
               $html .= "</div>";
               if ($explicitcss==1) $style=" style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
               $html .= "<div id=\"".$prefix."wda_captcha\" class=\"".$prefix."wda ".$prefix."wdatable\"".$style.">";
               if ($explicitcss==1) $style=" style=\"width:auto;margin-bottom:0px;\"";
               $html .= "<table cellpadding=\"3\" cellspacing=\"1\"".$style."><tr>";
               if ($explicitcss==1) $style=" style=\"padding:3px;vertical-align:middle;\"";
               $html .= "<td id=\"".$prefix."captchaimage\"".$style.">";
               $html .= "</td><td".$style.">";
               if ($explicitcss==1) $style=" style=\"font-size:12px;font-family:arial;width:100px;color:#222222;\"";
               $html .= "<input type=\"text\" name=\"w".$webdata['wd_id']."captcha\" value=\"\" id=\"".$prefix."w".$webdata['wd_id']."captcha\" class=\"".$prefix."winput_txtsm\"".$style.">";
               $html .= "</td>";
               $html .= "</tr></table>";
               $html .= "</div>";
               $html .= "</div>";
            }
            
            //$html .= "\n<b>esign is ".$webdata['esign']."</b><br>\n";
            //if ($paged) $html .= "\n<b> ***chj this is paged </b><br>\n";
            //else $html .= "\n<b> ***chj this is not paged </b><br>\n";
            //$html .= "\n<!-- ***chj pages: ".count($sections)." -->\n";
            //$html .= "\n<!-- ***chj page: ".$page." -->\n";
            
            
            $html .= "\n<div style=\"display:none;\">\n";
            $html .= "wd_id: ".$webdata['wd_id']."\n";
            $html .= "esign: ".$webdata['esign']."\n";
            if($paged) $html .= "this is paged\n";
            else $html .= "this is not paged\n";
            $html .= "</div>\n";
            if ($webdata['esign']==1 && (($paged && $page==count($sections)) || !$paged)) {
               //$html .= "Chad chad";
               $js .= "   if (!rqderror) {\n";
               $js .= "   temp = jQuery('#".$prefix."w".$webdata['wd_id']."esign').val();\n";
               $js .= "   if (!Boolean(temp)) {\n";
               $js .= "      rqderror = true;\n";
               $js .= "      jQuery('#".$prefix."w".$webdata['wd_id']."esignbutton').css('border','2px solid RED');\n";
               $js .= "      rqderrorstr = rqderrorstr + 'Please click below to e-sign this document.';\n";
               $js .= "   } else {\n";
               $js .= "      url = url + '&w".$webdata['wd_id']."esign=' + encodeURIComponent(temp);\n";
               $js .= "   }\n";
               $js .= "   }\n";
               
               $html .= "\n<script>\n";
               $html .= "function ".$prefix."_sendsignatureback(imgurl){\n";
               $html .= "jQuery('#".$prefix."w".$webdata['wd_id']."esign').val(imgurl);\n";
               $html .= "jQuery('#".$prefix."w".$webdata['wd_id']."esignbutton').html('Your esignature was submitted successfully.');\n";
               $html .= "}\n";
               $html .= "</script>\n\n";
               $html .= "<input type=\"hidden\" name=\"w".$webdata['wd_id']."esign\" id=\"".$prefix."w".$webdata['wd_id']."esign\" value=\"\">";
               //$html .= "<div onclick=\"window.open('".getBaseURL()."jsfcode/uploadsignature.php?id=".$wd_id."');\" id=\"".$prefix."w".$webdata['wd_id']."esignbutton\" style=\"margin-top:5px;margin-bottom:5px;margin-left:2px;font-size:12px;font-family:verdana;text-align:center;width:100px;background-color:#CCCCCC;padding:8px;border:1px solid #222222;border-radius:5px;\">eSign this document</div>";   
               $html .= "<div id=\"".$prefix."w".$webdata['wd_id']."esignbutton\" style=\"margin:5px;padding:4px;font-size:12px;font-family:verdana;color:green;\">";   
               $html .= "<div onclick=\"window.open('".$GLOBALS['baseURLSSL'].$GLOBALS['codeFolder']."uploadsignature.php?id=".$webdata['wd_id']."&prefix=".$prefix."');\" id=\"".$prefix."w".$webdata['wd_id']."esignbuttondiv\" style=\"margin-top:8px;margin-bottom:10px;margin-left:1px;font-size:12px;font-family:verdana;text-align:center;width:150px;background-color:#DDDDDD;padding:8px;border:1px solid #222222;border-radius:5px;cursor:pointer;\">eSign this document</div>";
               $html .= "</div>";
            }
            
   
            if ($explicitcss==1) $style=" style=\"clear:both;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
            $html .= "<div id=\"".$prefix."wdsubmitload\" class=\"".$prefix."wdrow\" style=\"padding:10px;font-style:italic;font-size:16px;font-family:arial;display:none;\">Submitted.  Loading...</div>";
            $html .= "<div id=\"".$prefix."wdsubmit\" class=\"".$prefix."wdrow\"".$style.">";
      
            if ($paged && $page>1) {
               if ($explicitcss==1) $style=" style=\"float:left;border:1px solid #202020;background-color:#D0D0D0;font-size:13px;font-family:arial;color:black;cursor:pointer;text-align:center;padding:5px;margin:3px;width:180px;\"";
               $html .= "<div id=\"".$prefix."wdsubmitbtn\" class=\"".$prefix."wdsubmitbtn\"".$style." onclick=\"".$prefix."SubmitWDForm(".($page-1).");\">";
               $html .= "Back";
               $html .= "</div>";
            } else if ($paged && $page==1 && $hasacct) {
               if ($explicitcss==1) $style=" style=\"float:left;border:1px solid #202020;background-color:#D0D0D0;font-size:13px;font-family:arial;color:black;cursor:pointer;text-align:center;padding:5px;margin:3px;width:180px;\"";
               $html .= "<div id=\"".$prefix."wdsubmitbtn\" class=\"".$prefix."wdsubmitbtn\"".$style." onclick=\"".$prefix."SubmitWDForm(-2);\">";
               $html .= "Back";
               $html .= "</div>";
            }
      
            $btntxt = "Submit";
            if ($paged && count($sections)>1) {
               //if ($page<count($sections)) $btntxt = "Continue to page ".($page+1);
               if ($page<count($sections)) $btntxt = "Continue";
               else $btntxt = "Complete";
            }
            if ($explicitcss==1) $style=" style=\"float:left;border:1px solid #202020;background-color:#D0D0D0;font-size:13px;font-family:arial;color:black;cursor:pointer;text-align:center;padding:5px;margin:3px;width:180px;\"";
            
            //if($hasacct) $html .= "<b>page: ".$page."</b>";
            
            //Next page is generally 1 plus the printed page, except in login/acct situations
            $dnxtpg = $page + 1;
            if($page<1) $dnxtpg = "";
            
            $html .= "<div data-chad=\"test\" id=\"".$prefix."wdsubmitbtn\" class=\"".$prefix."wdsubmitbtn\"".$style." onclick=\"".$prefix."SubmitWDForm(".$dnxtpg.");\">";
            $html .= $btntxt;
            $html .= "</div>";
      
            $html .= "</div>";
      
            if ($explicitcss==1) $style=" style=\"clear:both;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
            $html .= "<div id=\"".$prefix."wdend\" class=\"".$prefix."wdrow\"".$style."></div>";
      
            $html .= "</div>";
            //$js .= "alert(url);\n";
            $js .= "if (!rqderror) {\n";
            //$js .= "   var loadingstr = '<span style=\\\"padding:10px;font-style:italic;font-size:16px;font-family:arial;\\\" class=\\\"".$prefix."wdsubmitted\\\">Submitted.  Please wait...</span>';\n";
            //$js .= "   jQuery('#".$prefix."wdsubmit').html(loadingstr);\n";
            $js .= "   jQuery('#".$prefix."wdsubmitload').show();\n";
            $js .= "   jQuery('#".$prefix."wdsubmit').hide();\n";
            
            $js .= "   c_urls.push(url + '&chj=' + c_urls.length);\n";
            $js .= "   jsfwebdata_urls = c_urls;\n";
            //$js .= "   alert('***chj*** ' + jsfwebdata_urls.length + ' url(s) to call: ' + JSON.stringify(jsfwebdata_urls));\n";
            $js .= "   url = jsfwebdata_urls.shift();\n";
            
            //$js .= "   alert('***chj*** full url: ' + url);\n";
            $js .= "   jsfwebdata_CallJSONP(url);\n";
            $js .= "} else {\n";
            $js .= "   alert('Please fill in all required fields.  ' + rqderrorstr);\n";
            //$js .= "   if (Boolean(rqderrorid)) jQuery('#' + rqderrorid).css('border','2px solid RED');\n";
            $js .= "}\n";
            $js .= "}\n";
            //$relationshipjs1 .= "   alert('end ".$prefix."InitializeWDRelationships()');\n";
            $relationshipjs1 .= "}\n";
            //$relationshipjs2 .= "   alert('end ".$prefix."CheckWDRelationships()');\n";
            $relationshipjs2 .= "}\n";
            $returnobj['html'] = $html;
            $returnobj['js'] = $js;
            $returnobj['relationshipjs1'] = $relationshipjs1;
            $returnobj['relationshipjs2'] = $relationshipjs2;
            if($paged) {
               $returnobj['pages'] = count($sections);
               $returnobj['page'] = $page;
            }
            if($hasacct) $returnobj['hasuserid'] = 1;
         } else {
            //Single question at a time - displayable on any device.
            if ($callback==NULL) $callback = $prefix."visualcallback";
            //Pick questions (some will be at random based on survey configuration)
            $fields = $this->getFieldsVisual($webdata['wd_id']);

            $answertotals = array();
            $html = "";
            $js = "";
            $js .= "\nfunction jsfwd_vis_clearall(){\n";
            for ($i=0;$i<count($fields);$i++) {
               $q = $fields[$i];

               //Hide most of the questions - only show 1 (the current page/question that is being viewed right now)
               $vis_style = "style=\"display:none;\"";
               if ($i==($page-1)) $vis_style="";

               //Build function to hide all questions (to make it easier to display only 1 after a question is submitted)
               $js .= "jQuery('#".$prefix."_wdvis_pg".$i."').hide();\n";

               //Build DOM structure for this Question/Page
               $html .= "<div id=\"".$prefix."_wdvis_pg".$i."\" class=\"".$prefix."_wdvis\" ".$vis_style.">";
               $html .= "<div class=\"".$prefix."_wdvis_top\"></div>";
               $html .= "<div class=\"".$prefix."_wdvis_q\">";
               $html .= $q['label'];
               $html .= "</div>";
      
               $names = array();
               $values = array();
               $colors = array();
               $questionList = trim(convertBack($q['question']));
               //$bothnvp = explode(";",$questionList);
               //$names = explode(",",$bothnvp[0]);
               //$values = explode(",",$bothnvp[1]);
               //$colors = explode(",",$bothnvp[2]);
               $bothnvp = separateStringBy($questionList,";");
               $names = separateStringBy($bothnvp[0],",");
               $values = separateStringBy($bothnvp[1],",");
               $colors = separateStringBy($bothnvp[2],",");
      
               if (count($names)!= count($values)) $values = $names;

               //Determine the type of question based on name/values for more custom display
               $b_yesno = FALSE;
               $b_numeric = FALSE;
               $b_icon = FALSE;
               $b_short = FALSE;
               if (0==strcmp(strtolower(trim($names[0])),"yes") && 0==strcmp(strtolower(trim($names[1])),"no")) {
                  $b_yesno = TRUE;
                  if ($colors[0]==NULL) {
                     $colors[0] = "#33CC33";
                     $colors[1] = "#CC3333";
                     $colors[2] = "#AAAAAA";
                     $colors[3] = "#AAAAAA";
                     $colors[4] = "#AAAAAA";
                     $colors[5] = "#AAAAAA";
                     $colors[6] = "#AAAAAA";
                  }
               } else if ($values[0]==1 && $values[1]==2 && $values[2]==3 && $values[3]==4 && $values[(count($values)-1)]==count($values)) {
                  $b_numeric = TRUE;
               } else {
                  $b_icon = TRUE;
                  $b_short = TRUE;
                  for ($v=0; $v<count($values); $v++) {
                     $extn = strtolower(substr($values[$v],-4));
                     if (0!=strcmp($extn,".jpg") && 0!=strcmp($extn,".png") && 0!=strcmp($extn,".gif")) $b_icon=FALSE;
                     if (strlen($values[$v])>3) $b_short=FALSE;
                  }
               }
      
               $html .= "<div id=\"".$prefix."_wdvis_a".$i."_a\" class=\"".$prefix."_wdvis_a\">";
               $answertotals[$i] = count($names);

               $classprefix = "";
               $classname = "";
               if ($b_yesno) $classprefix = $prefix."_wdvis_yno";
               else if ($b_numeric) $classprefix = $prefix."_wdvis_num";
               else if ($b_icon) $classprefix = $prefix."_wdvis_pic";
               else if ($b_short) $classprefix = $prefix."_wdvis_sml";
               else $classprefix = $prefix."_wdvis_xxx";

               $html .= "<div id=\"".$classprefix."answers".$i."\" class=\"".$prefix."answersdiv\">";

               $classname = $classprefix.$i;

               for ($a=0; $a<count($names); $a++) {
                  $rowvar = "jsfwd_rowid";
                  if ($row['wd_row_id']!=NULL) {
                     $rowvar = "'".$row['wd_row_id']."'";
                  }
                  $html .= "<div ";
                  $html .= " id=\"".$classname."_outer".$a."\"";
                  $html .= " class=\"".$classname."_outer jsfcursorpointer\" ";

                  //There are two methods (both work) to recieve answers from user:
                  //  1. SubmitWDVisual: Build a full survey response as they click, then submit the full thing once at the end (faster)
                  //  2. SubmitWDNowVisual: Create a response after first answer, then update DB after each subsequent answer (more data)
                  $html .= " onClick=\"".$prefix."SubmitWDVisual(".$rowvar.",".$webdata['wd_id'].",'".$q['field_id']."','".$values[$a]."',".($i+2).");\"";
                  //$html .= " onClick=\"".$prefix."SubmitWDNowVisual(".$rowvar.",".$webdata['wd_id'].",'".$q['field_id']."','".$values[$a]."',".($i+2).");\"";
                  $html .= ">";
                  if ($b_icon) {
                     $html .= "<img src=\"".$names[$a]."\" border=\"0\" class=\"".$classname."_img\">";
                  } else {
                     $html .= "<div ";
                     $html .= " id=\"".$classname."_choice".$a."\"";
                     if ($colors[$a]!=NULL) $html .= " style=\"background-color:".$colors[$a].";\" ";
                     else $html .= " style=\"background-color:#248aec;\" ";
                     $html .= " class=\"".$classname."_choice\"></div>";
                     $html .= "<div ";
                     $html .= " id=\"".$classname."_name".$a."\"";
                     $html .= " class=\"".$classname."_name\"";
                     $html .= ">";
                     if ($b_icon) $html .= "<img src=\"".$names[$a]."\" border=\"0\" class=\"".$classname."_img\">";
                     else $html .= $names[$a];
                     $html .= "</div>";
                  }
                  $html .= "</div>";
               }
               $html .= "<div class=\"".$prefix."_wdvis_finished\"></div>";
               $html .= "</div>";
               $html .= "</div>";
      
               $html .= "</div>";
               
            }

            //Add an extra "page" for the thankyou message...
            $js .= "jQuery('#".$prefix."_wdvis_pg".count($fields)."').hide();\n";
            $html .= "<div id=\"".$prefix."_wdvis_pg".count($fields)."\" class=\"".$prefix."_wdvis\" style=\"display:none;\">";
            if ($webdata['filename']!=NULL) {
               $html .= "<div class=\"fullscrimgdiv\" style=\"position:absolute;top:20px;left:20px;\">";
               $html .= "<img class=\"fullscrimgimg\" src=\"".$webdata['filename']."\">";
               $html .= "</div>";
            } else {
                $html .= "<div class=\"".$prefix."_wdvis_top\"></div>";
                $html .= "<div class=\"".$prefix."_wdvis_q\">";
                $html .= "Thank you for taking the time to answer these questions.";
                $html .= "</div>";
                $html .= "<div class=\"".$prefix."_wdvis_addl\">";
                $html .= "Your feedback will be used to improve our service.";
                $html .= "</div>";
            }
            $html .= "</div>";



            $js .= "}\n";
            $js .= "//Two methods to submit data - one that will be faster, one that will ensure more data is collected.\n";
            $js .= "\nfunction ".$prefix."SubmitWDVisual(rowid,wdid,qid,val,pg){\n";
            $js .= "   jsfwd_vis_clearall();\n";
            $js .= "   jsfwd_visualuri = jsfwd_visualuri + '&w' + wdid + 'a' + encodeURIComponent(qid) + '=' + encodeURIComponent(val);\n";
            $js .= "   if ((pg-1)==jsfwd_total) {\n";
            $js .= "       var url= defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=".$callback."';\n";
            $js .= "       url = url + '&action=submitwebdata';\n";
            $js .= "       url = url + '&prefix=".$prefix."';\n";
            $js .= "       url = url + '&wd_id=' + wdid;\n";
            $js .= "       if (Boolean(rowid)) url = url + '&wd_row_id=' + rowid;\n"; 
            $js .= "       url = url + '&nextpg=' + pg;\n"; 
            $js .= "       url = url + jsfwd_visualuri;\n"; 
            //$js .= "       alert(url);\n"; 
            $js .= "       jsfwebdata_CallJSONP(url,true);\n";
            //$js .= "       setTimeout(function(){jsfwd_pagenum = 1;jsfwd_visualuri='';jsfwd_rowid='';show_surveyhome(true);},10000);\n";
            $js .= "       jsf_viewtimeout = setTimeout(function(){show_surveyhome(true);},10000);\n";
            $js .= "   }\n";
            $js .= "   jQuery('#".$prefix."_wdvis_pg' + (pg-1)).show();\n";
            $js .= "   jsfwd_pagenum=pg;\n";
            $js .= "}\n";
            $js .= "\nfunction ".$prefix."SubmitWDNowVisual(rowid,wdid,qid,val,pg){\n";
            $js .= "   jsfwd_vis_clearall();\n";
            $js .= "   var url= defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&callback=".$callback."';\n";
            $js .= "   url = url + '&action=submitwebdatavisual';\n";
            $js .= "   url = url + '&prefix=".$prefix."';\n";
            $js .= "   url = url + '&wd_id=' + wdid;\n";
            $js .= "   if (Boolean(rowid)) url = url + '&wd_row_id=' + rowid;\n"; 
            $js .= "   url = url + '&qid=' + encodeURIComponent(qid);\n"; 
            $js .= "   url = url + '&val=' + encodeURIComponent(val);\n"; 
            $js .= "   url = url + '&nextpg=' + pg;\n"; 
            //$js .= "   alert(url);\n"; 
            $js .= "   jsfwebdata_CallJSONP(url);\n";
            $js .= "}\n";
            
            $relationshipjs2 = "function ".$prefix."CheckWDRelationships(){\n}\n";
            
            $returnobj['html'] = $html;
            $returnobj['js'] = $js;
            $returnobj['relationshipjs1'] = "";
            $returnobj['relationshipjs2'] = $relationshipjs2;
            $returnobj['total'] = count($fields);
            $returnobj['answertotals'] = $answertotals;

         }
      }
      if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." end getJSONForm()<br>\n";      
      $returnobj['userid'] = $userid;
      $returnobj['wd_id'] = $webdata['wd_id'];
      $returnobj['privatesrvy'] = $webdata['privatesrvy'];
      return $returnobj;
   }

   function getJSONWebDataLogin($wd_id, $prefix=NULL, $email=NULL) {
      $html = "";
      $js = "";
      
      $html .= "<div id=\"".$prefix."sxnlbllogin\" class=\"".$prefix."sxnlbl\">Please Log In</div>";
         
      $html .= "<div id=\"".$prefix."w".$wd_id."aemail\" class=\"".$prefix."wdrow\">";
      $html .= "<div id=\"".$prefix."wdq_email\" class=\"".$prefix."wdq\">";
      $html .= "Email";
      $html .= "</div>";
      $html .= "<div id=\"".$prefix."wda_email\" class=\"".$prefix."wda\">";         
      $html .= "<input type=\"text\" ";
      $html .= "name=\"w".$wd_id."aemail\" ";
      $html .= "value=\"".$email."\" ";
      $html .= "id=\"".$prefix."inputw".$wd_id."aemail\" ";
      $html .= "class=\"".$prefix."winput_txt\">";
      $html .= "</div>";
      $html .= "</div>";
      $html .= "<div id=\"".$prefix."w".$wd_id."apasscode\" class=\"".$prefix."wdrow\">";
      $html .= "<div id=\"".$prefix."wdq_passcode\" class=\"".$prefix."wdq\">";
      $html .= "Passcode";
      $html .= "</div>";
      $html .= "<div id=\"".$prefix."wda_passcode\" class=\"".$prefix."wda\">";         
      $html .= "<input type=\"text\" ";
      $html .= "name=\"w".$wd_id."apasscode\" ";
      $html .= "value=\"".$passcode."\" ";
      $html .= "id=\"".$prefix."inputw".$wd_id."apasscode\" ";
      $html .= "class=\"".$prefix."winput_txt\">";
      $html .= "</div>";
      $html .= "</div>";
      $html .= "\n<script>\n";
      $html .= "var e = window.localStorage.getItem('".$wd_id."_e');\n";
      $html .= "if(Boolean(e)) jQuery('#".$prefix."inputw".$wd_id."aemail').val(e);\n";
      $html .= "\n</script>\n";
      
      $js .= "   url = url + '&w".$wd_id."login=1';\n";
      $js .= "   if (!rqderror) {\n";
      $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."aemail').val();\n";
      $js .= "   if (Boolean(temp)) {\n   url = url + '&w".$wd_id."aemail=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n      jQuery('#".$prefix."inputw".$wd_id."aemail').css('border','1px solid #999999');   }\n";
      $js .= "   else {\n   rqderror = true;\n   rqderrorstr = rqderrorstr + 'Your email was left empty.';\n   jQuery('#".$prefix."inputw".$wd_id."aemail').css('border','2px solid RED');\n   }\n";
      $js .= "   }\n";
      $js .= "   if (!rqderror) {\n";
      $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."apasscode').val();\n";
      $js .= "   if (Boolean(temp)) {\n   url = url + '&w".$wd_id."apasscode=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n      jQuery('#".$prefix."inputw".$wd_id."apasscode').css('border','1px solid #999999');   }\n";
      $js .= "   else {\n   rqderror = true;\n   rqderrorstr = rqderrorstr + 'Your passcode was left empty.';\n   jQuery('#".$prefix."inputw".$wd_id."apasscode').css('border','2px solid RED');\n   }\n";
      $js .= "   }\n";
      
      $returnobj = array();
      $returnobj['html'] = $html;
      $returnobj['js'] = $js;
      $returnobj['relationshipjs1'] = "";
      $returnobj['relationshipjs2'] = "";
      return $returnobj;
   }
   
   function getJSONWebAccount($wd_id, $prefix, $userid) {
      $ua = new UserAcct();
      $companyuser = NULL;
      $adminuser = $ua->getUser($userid);
      if(0==strcmp($adminuser['usertype'],"org")) {
         $companyuser = $adminuser;
         $adminuser = NULL;
         $adminrel = $ua->getUsersRelated($userid,"to","SRVYADMIN");
         if($adminrel!=NULL && count($adminrel)>0) {
            $adminuser = $ua->getFullUserInfo($adminrel[0]['reluserid']);
         }
      }
      
      
      $html = "";
      $js = "";
      
      //$html .= "user: ".$userid;
      
      $js .= "   url = url + '&w".$wd_id."userupdate=1';\n";
      $js .= "   var uchange=false;\n";
      if($companyuser != NULL) $js .= "   url = url + '&w".$wd_id."_o_userid=".$companyuser['userid']."';\n";
      if($adminuser != NULL) $js .= "   url = url + '&w".$wd_id."_u_userid=".$adminuser['userid']."';\n";

      $html .= "\n<script>\n";
      $html .= "var ".$prefix."_w".$wd_id."_uchange = false;\n";
      $html .= "\n</script>\n";
      $html .= "<div id=\"".$prefix."sxnlblaccount\" class=\"".$prefix."sxnlbl\">Update your information</div>";

      if($companyuser != NULL) {
         $html .= "<div id=\"".$prefix."w".$wd_id."acompany\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_company\" class=\"".$prefix."wdq\">";
         $html .= "Company Name";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_company\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_company\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['company']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_company\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_company').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_company=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_o_company').css('border','1px solid #999999');\n";
         $js .= "     } else {\n";
         $js .= "       rqderror = true;\n";
         $js .= "       rqderrorstr = rqderrorstr + 'Your company name was left empty.';\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_o_company').css('border','2px solid RED');\n";
         $js .= "     }\n";
         $js .= "   }\n";
         
         $html .= "<div id=\"".$prefix."w".$wd_id."awebsite\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_website\" class=\"".$prefix."wdq\">";
         $html .= "Website";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_website\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_website\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['website']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_website\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_website').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_website=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";
      }
      
      if($adminuser != NULL) {
         $html .= "<div id=\"".$prefix."w".$wd_id."afname\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_fname\" class=\"".$prefix."wdq\">";
         $html .= "Your First Name";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_fname\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_fname\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['fname']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_fname\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_fname').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_fname=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_fname').css('border','1px solid #999999');\n";
         $js .= "     } else {\n";
         $js .= "       rqderror = true;\n";
         $js .= "       rqderrorstr = rqderrorstr + 'Your first name was left empty.';\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_fname').css('border','2px solid RED');\n";
         $js .= "     }\n";
         $js .= "   }\n";
         
         $html .= "<div id=\"".$prefix."w".$wd_id."alname\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_lname\" class=\"".$prefix."wdq\">";
         $html .= "Your Last Name";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_lname\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_lname\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['lname']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_lname\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_lname').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_lname=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_lname').css('border','1px solid #999999');\n";
         $js .= "     } else {\n";
         $js .= "       rqderror = true;\n";
         $js .= "       rqderrorstr = rqderrorstr + 'Your last name was left empty.';\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_lname').css('border','2px solid RED');\n";
         $js .= "     }\n";
         $js .= "   }\n";
         
         $html .= "<div id=\"".$prefix."w".$wd_id."atitle\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_title\" class=\"".$prefix."wdq\">";
         $html .= "Title";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_title\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_title\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['title']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_title\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_title').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_title=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."aphonenum\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_phonenum\" class=\"".$prefix."wdq\">";
         $html .= "Phone";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_phonenum\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_phonenum\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['phonenum']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_phonenum\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_phonenum').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_phonenum=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."aphonenum3\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_phonenum3\" class=\"".$prefix."wdq\">";
         $html .= "Phone 2";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_phonenum3\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_phonenum3\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['phonenum3']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_phonenum3\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_phonenum3').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_phonenum3=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."aphonenum2\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_phonenum2\" class=\"".$prefix."wdq\">";
         $html .= "Fax";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_phonenum2\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_phonenum2\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['phonenum2']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_phonenum2\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_phonenum3').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_phonenum3=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."aemail\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_email\" class=\"".$prefix."wdq\">";
         $html .= "Email";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_email\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_u_email\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$adminuser['email']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_u_email\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_u_email').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_u_email=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_email').css('border','1px solid #999999');\n";
         $js .= "     } else {\n";
         $js .= "       rqderror = true;\n";
         $js .= "       rqderrorstr = rqderrorstr + 'Your last name was left empty.';\n";
         $js .= "       jQuery('#".$prefix."inputw".$wd_id."a_u_email').css('border','2px solid RED');\n";
         $js .= "     }\n";
         $js .= "   }\n";
      }
      
      if($companyuser != NULL) {
         $html .= "<div id=\"".$prefix."w".$wd_id."aaddr1\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_addr1\" class=\"".$prefix."wdq\">";
         $html .= "Facility Address 1";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_addr1\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_addr1\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['addr1']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_addr1\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_addr1').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_addr1=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."aaddr2\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_addr2\" class=\"".$prefix."wdq\">";
         $html .= "Facility Address 2";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_addr2\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_addr2\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['addr2']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_addr2\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_addr2').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_addr2=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."acity\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_city\" class=\"".$prefix."wdq\">";
         $html .= "City";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_city\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_city\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['city']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_city\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_city').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_city=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";
         
         $adminstateoptionlist = listStates($companyuser['state'],"w".$wd_id."a_o_state",TRUE,"id=\"".$prefix."inputw".$wd_id."a_o_state\" onchange=\"".$prefix."_w".$wd_id."_uchange=true;\"");
         $admincountryoptionlist = listCountries($companyuser['country'],"w".$wd_id."a_o_country",TRUE,"id=\"".$prefix."inputw".$wd_id."a_o_country\" onchange=\"".$prefix."_w".$wd_id."_uchange=true;\"");


         $html .= "<div id=\"".$prefix."w".$wd_id."astate\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_state\" class=\"".$prefix."wdq\">";
         $html .= "State";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_state\" class=\"".$prefix."wda\">";         
         $html .= $adminstateoptionlist;
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_state').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_state=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."azip\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_zip\" class=\"".$prefix."wdq\">";
         $html .= "Zip";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_zip\" class=\"".$prefix."wda\">";         
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a_o_zip\" ";
         $html .= "onkeyup=\"".$prefix."_w".$wd_id."_uchange=true;\" ";
         $html .= "value=\"".$companyuser['zip']."\" ";
         $html .= "id=\"".$prefix."inputw".$wd_id."a_o_zip\" ";
         $html .= "class=\"".$prefix."winput_txt\">";
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_zip').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_zip=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."acountry\" class=\"".$prefix."wdrow\">";
         $html .= "<div id=\"".$prefix."wdq_country\" class=\"".$prefix."wdq\">";
         $html .= "Country";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_country\" class=\"".$prefix."wda\">";         
         $html .= $admincountryoptionlist;
         $html .= "</div>";
         $html .= "</div>";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "     temp = jQuery('#".$prefix."inputw".$wd_id."a_o_country').val();\n";
         $js .= "     if (Boolean(temp)) {\n";
         $js .= "       url = url + '&w".$wd_id."a_o_country=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";
      }
      
      $js .= "if(Boolean(".$prefix."_w".$wd_id."_uchange)) url = url + '&w".$wd_id."a_changes=1';\n";
      //$js .= "if(Boolean(".$prefix."_w".$wd_id."_uchange)) alert('url: ' + url);\n";
      
      $returnobj = array();
      $returnobj['html'] = $html;
      $returnobj['js'] = $js;
      $returnobj['relationshipjs1'] = "";
      $returnobj['relationshipjs2'] = "";
      return $returnobj;
   }
   
   function getJSONWebDataSection($wd_id, $section, $row, $prefix=NULL, $printstuff=FALSE, $explicitcss=0, $userelationships=TRUE, $admin=0, $glossary=NULL, $userid=NULL, $displaysect=-1) {
      //print "\n<!-- wd: ".$wd_id." section: ".$section." row:\n";
      //print_r($row);
      //print "\n-->\n";
      
      $showthissect = FALSE;
      if($section==-1 || $displaysect==-1 || $section==$displaysect){
         if($section==$displaysect) $displaysect = -1;
         $showthissect = TRUE;
      }

      $style="";
      $returnobj = array();
      if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." start getJSONWebDataSection().  wd_id: ".$wd_id." section: ".$section."\n<br>";
      $sectionObj = $this->getSection($wd_id,$section);
      $sectionObj['label'] = convertBack($sectionObj['label']);
      
      $html = "";
      $js = "";
      $relationshipjs1 = "";
      $relationshipjs2 = "";
      if ($explicitcss==1) $style=" style=\"clear:both;border:1px solid #DDDDDD;padding:9px;margin-top:4px;margin-bottom:7px;\"";
      
      $prefix2 = $prefix;
      $origprefix_a = separateStringBy($prefix,"_");
      if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];
      
      if($showthissect && $section==-1) $html .= "<div><div id=\"".$prefix."sxn".$sectionObj['section']."\">";
      else if($showthissect) $html .= "<div><div id=\"".$prefix."sxn".$sectionObj['section']."\" class=\"".$prefix2."sxn\"".$style.">";
      else $html .= "<div style=\"display:none;\"><div id=\"".$prefix."sxn".$sectionObj['section']."\">";
      
      if (trim($sectionObj['label'])!=NULL) {
         if ($explicitcss==1) $style=" style=\"clear:both;font-size:16px;color:#5a5a5a;font-weight:bold;font-family:arial;padding:3px;margin-bottom:5px;\"";
         $html .= "<div id=\"".$prefix."sxnlbl".$sectionObj['section']."\" class=\"".$prefix2."sxnlbl\"".$style.">";
         $dispname = $sectionObj['label'];
         
         // Currently disabling glossary for section headers
         //if ($dispname!=null && $glossary!=NULL) $dispname = $glossary->flagAllTerms($dispname,"#5691c4");
         
         $html .=  trim($dispname);
         $html .= "</div>";
      }

      $tableformat = FALSE;
      if (0==strcmp($sectionObj['question'],"showtableformat")) $tableformat = TRUE;

      
      
      // Mix questions and sections based on sequences
      //   formerly - show all questions in order, then show all sections in order
      $questions = $this->getFields($wd_id, $section, $admin);
      $sections = $this->getDataSections($wd_id,$section);
      
      $i = 0;
      $j = 0;
      while($i<count($sections) || $j<count($questions)) {
         $usesection = FALSE;
         if($i>=count($sections)) $usesection=FALSE;
         else if($j>=count($questions)) $usesection=TRUE;
         else if(intval($sections[$i]['sequence']) < intval($questions[$j]['sequence'])) $usesection=TRUE;
         
         if($usesection) {
            $returnobj = $this->getJSONWebDataSection($wd_id,$sections[$i]['section'],$row,$prefix,$printstuff,$explicitcss,$userelationships,$admin,$glossary,$userid,$displaysect);
            if ($printstuff) {
               print "<br>\n".date("m/d/Y H:i:s")." return from getJSONWebDataSection():<br>\n";
               print_r($returnobj);
               print "\n\n<br><br>\n";
            }
            $html .= $returnobj['html'];
            $js .= $returnobj['js'];
            $relationshipjs1 .= $returnobj['relationshipjs1'];
            $relationshipjs2 .= $returnobj['relationshipjs2'];
            $i++;
         } else {
            $q = $questions[$j];
            $returnobj = $this->getJSONQuestionHTML($wd_id,$q,$row['wd_row_id'],$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$showthissect);
            if ($printstuff) {
               print "<br>\n".date("m/d/Y H:i:s")." return from getJSONQuestionHTML():<br>\n";
               print_r($returnobj);
               print "\n\n<br><br>\n";
            }
            $html .= $returnobj['html'];
            $js .= $returnobj['js'];
            $relationshipjs1 .= $returnobj['relationshipjs1'];
            $relationshipjs2 .= $returnobj['relationshipjs2'];
            $j++;
         }
      }
      
      /*
      for ($j=0; $j<count($questions); $j++) {
         $q = $questions[$j];
         $returnobj = $this->getJSONQuestionHTML($wd_id,$q,$row['wd_row_id'],$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$showthissect);
         if ($printstuff) {
            print "<br>\n".date("m/d/Y H:i:s")." return from getJSONQuestionHTML():<br>\n";
            print_r($returnobj);
            print "\n\n<br><br>\n";
         }
         $html .= $returnobj['html'];
         $js .= $returnobj['js'];
         $relationshipjs1 .= $returnobj['relationshipjs1'];
         $relationshipjs2 .= $returnobj['relationshipjs2'];
      }

      for ($i=0; $i<count($sections); $i++) {
         $returnobj = $this->getJSONWebDataSection($wd_id,$sections[$i]['section'],$row,$prefix,$printstuff,$explicitcss,$userelationships,$admin,$glossary,$userid,$displaysect);
         if ($printstuff) {
            print "<br>\n".date("m/d/Y H:i:s")." return from getJSONWebDataSection():<br>\n";
            print_r($returnobj);
            print "\n\n<br><br>\n";
         }
         $html .= $returnobj['html'];
         $js .= $returnobj['js'];
         $relationshipjs1 .= $returnobj['relationshipjs1'];
         $relationshipjs2 .= $returnobj['relationshipjs2'];
      }
      */

      $html .= "<div style=\"clear:both;\"></div>";
      $html .= "</div></div>";

      $returnobj = array();
      $returnobj['html'] = $html;
      $returnobj['js'] = $js;
      $returnobj['relationshipjs1'] = $relationshipjs1;
      $returnobj['relationshipjs2'] = $relationshipjs2;
      if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." end getJSONWebDataSection()<br>\n";            
      return $returnobj;
   }

   
   
   
   
   function testJSONWebDataSection($wd_id, $section) {
      // Mix questions and sections based on sequences
      //   formerly - show all questions in order, then show all sections in order
      $questions = $this->getFields($wd_id, $section, $admin);
      $sections = $this->getDataSections($wd_id,$section);
      
      $i = 0;
      $j = 0;
      while($i<count($sections) || $j<count($questions)) {
         $usesection = FALSE;
         if($i>=count($sections)) $usesection=FALSE;
         else if($j>=count($questions)) $usesection=TRUE;
         else if(intval($sections[$i]['sequence']) < intval($questions[$j]['sequence'])) $usesection=TRUE;
         
         if(intval($sections[$i]['sequence']) < intval($questions[$j]['sequence'])) print "good to go!!<br>\n";
         
         print "i: ".$i."  j: ".$j."  section: ".$sections[$i]['section']." (".intval($sections[$i]['sequence']).")  question: ".$questions[$j]['field_id']." (".intval($questions[$j]['sequence']).")  usesection: ".$usesection."<br>\n";
         
         if($usesection) {
            print "Showing section: ".$sections[$i]['section']." (".$sections[$i]['sequence'].")<br>\n";
            $this->testJSONWebDataSection($wd_id,$sections[$i]['section']);
            $i++;
         } else {
            print "Showing question: ".$questions[$j]['field_id']." (".$questions[$j]['sequence'].")<br>\n";
            $j++;
         }
      }
   }

   
   
   
   
   
   
   function getJSONQuestionHTML($wd_id,$q,$wd_row_id=null,$prefix=NULL,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
      if ($printstuff) {
         print "<br>\n".date("m/d/Y H:i:s")." in getJSONQuestionHTML() question:<br>\n";
         print_r($q);
         print "\n\n<br><br>\n";
      }
      
      $q['label'] = convertBack($q['label']);
      $q['simplelabel'] = strip_tags($q['label']);

      $answered = $this->getAnswer($wd_id,$wd_row_id, $q['field_id']);
      $returnobj = array();

      if (strcmp($q['field_type'],"TEXT")==0 || strcmp($q['field_type'],"COLOR")==0 || strcmp($q['field_type'],"INT")==0 || strcmp($q['field_type'],"DEC")==0 || strcmp($q['field_type'],"MONEY")==0) {
         $returnobj = $this->getTextJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"TABLE")==0) {
         $returnobj = $this->getTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"TEXTAREA")==0) {
         $returnobj = $this->getTextAreaJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"DATE")==0 || strcmp($q['field_type'],"DATETIME")==0 || strcmp($q['field_type'],"AGE")==0) {
         $returnobj = $this->getDateJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"INFO")==0) {
         $returnobj = $this->getInfoJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"SPACER")==0) {
         $returnobj = $this->getSpacerJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"RADIO")==0 || strcmp($q['field_type'],"VOTE")==0 || strcmp($q['field_type'],"POLLRADIO")==0) {
         $returnobj = $this->getRadioJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"STATE")==0 || strcmp($q['field_type'],"DROPDOWN")==0 || strcmp($q['field_type'],"FOREIGN")==0 || strcmp($q['field_type'],"FOREIGNTDD")==0) {
      //} elseif (strcmp($q['field_type'],"STATE")==0 || strcmp($q['field_type'],"USERS")==0 || strcmp($q['field_type'],"DROPDOWN")==0 || strcmp($q['field_type'],"FOREIGN")==0) {
         $returnobj = $this->getDropdownJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"MANY")==0) {
         $returnobj = $this->getManyJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"NEWCHKBX")==0 || strcmp($q['field_type'],"HRZCHKBX")==0 || strcmp($q['field_type'],"CHECKBOX")==0 || strcmp($q['field_type'],"FOREIGNTBL")==0 || strcmp($q['field_type'],"FOREIGNCB")==0) {
         $returnobj = $this->getNewCheckboxJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"SNGLCHKBX")==0) {
         $returnobj = $this->getSingleCheckboxJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"MBL_MC")==0 || strcmp($q['field_type'],"MBL_IMG")==0) {
         $returnobj = $this->getNewCheckboxJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"MBL_UPL")==0 || strcmp($q['field_type'],"FILE")==0 || strcmp($q['field_type'],"IMAGE")==0) {
         $returnobj = $this->getImageJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"FOREIGNSRY")==0 || strcmp($q['field_type'],"FOREIGNSCT")==0) {
         $returnobj = $this->getWDTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"FOREIGNHYB")==0) {
         $returnobj = $this->getWDHybridTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"OLD_USERSRCH")==0) {
         $returnobj = $this->getUserSearchJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"USERLIST")==0 || strcmp($q['field_type'],"USERS")==0 || strcmp($q['field_type'],"USERSRCH")==0 || strcmp($q['field_type'],"USERAUTO")==0) {
         $returnobj = $this->getUserListJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      } elseif (strcmp($q['field_type'],"REGION")==0 || strcmp($q['field_type'],"USERS")==0 || strcmp($q['field_type'],"USERSRCH")==0) {
         $returnobj = $this->getRegionJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay);
      }
      if ($printstuff) print "\n<br>".date("m/d/Y H:i:s")." end getJSONQuestionHTML()<br>\n";
      
      if($q['hide']==1) {
         $returnobj['html'] = "<div style=\"display:none;\">".$returnobj['html']."</div>";
      }
      
      return $returnobj;
   }

   function getMaxAJAX() {
      return 2000;
   }

   
   
   function getUserSearchJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay){
      //function printUsersSearchHTML($wd_id,$q,$answered,$glossary,$colorAttr,$required,$disabled,$displayStyle,$postTxt,$rels1){
      $ua = new UserAcct();
      $style="";
      $questionText = $q['label'];
      if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
      $value = trim(convertBack($answered['answer']));
      if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
      $user = NULL;
      if ($value!=NULL && $value>0) $user = $ua->getUser($value);
      
      $disp = "";
      if ($user!=NULL && $user['userid']==$value) {
         $disp = $user['userid']." ";
         if($user['fname']!=NULL && strpos($user['fname'],"dummy")===FALSE) $disp .= $user['fname']." ";
         if($user['lname']!=NULL && strpos($user['lname'],"dummy")===FALSE) $disp .= $user['lname']." ";
         if($user['company']!=NULL && strpos($user['company'],"dummy")===FALSE) $disp .= $user['company']." ";
         if($user['email']!=NULL && strpos($user['email'],"dummy")===FALSE) $disp .= "<i>".$user['email']."</i> ";
         if($user['phonenum']!=NULL && strpos($user['phonenum'],"dummy")===FALSE) $disp .= "(".$user['phonenum'].") ";
      }
      
      $html = "";
      $js = "";
      $relationshipjs1 = "";
      $relationshipjs2 = "";
      
      $js .= "   if (!rqderror) {\n";
      $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
      $js .= "   jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');\n";
      
      if ($q['required']==1) {
         $js .= " if(!Boolean(temp)) {\n";
         $js .= "   rqderror = true;\n";
         $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
         $js .= "   jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n";
         $js .= " } else ";
      }
      $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
      $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
      $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
      $js .= "       url = c_url;\n";
      $js .= "     }\n";
      $js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
      $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(temp);\n";
      $js .= "   }\n";
      
      $js .= "   }\n";
      

      $prefix2 = $prefix;
      $origprefix_a = separateStringBy($prefix,"_");
      if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];
      
      $stylerow =  " style=\"clear:both;".$q['stylecss']."\"";
      $class_wdrow = "wdrow";
      $class_wdq = "wdq";
      $class_wda = "wda";      
         
      $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
      $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
      $html .= $questionText;
      if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
      $html .= "</div>";
      $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
      if(trim($disp)!=NULL) $html .= "<div id=\"".$prefix."curuserw".$wd_id."a".$q['field_id']."\" style=\"font-weight:bold;\">".$disp." &nbsp; <span onclick=\"jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val('');formchange_".$prefix."('".$prefix.$q['field_id']."');jQuery('#".$prefix."curuserw".$wd_id."a".$q['field_id']."').html('');\" style=\"cursor:pointer;color:red;font-size:8px;font-family:arial;\">remove</span></div>";
      $html .= "<input type=\"hidden\" ";
      $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" ";
      $html .= "value=\"".$value."\" ";
      $html .= ">";
      $html .= "<input type=\"text\" ";
      $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
      $html .= "value=\"Search\" ";
      $html .= "id=\"".$prefix."searchw".$wd_id."a".$q['field_id']."\" ";
      $html .= "onblur=\"if(this.value == ''){ this.value = 'Search'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
      $html .= "onfocus=\"if(this.value == 'Search'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
      $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";
      $html .= " <span onclick=\"search_".$prefix."w".$wd_id."a".$q['field_id']."();\" style=\"padding:4px;cursor:pointer;text-align:center;color:#222222;font-size:10px;font-family:arial;background-color:#EEEEEE;border:1px solid #222222;border-radius:4px;\">Search</span>";
      $html .= "<div style=\"margin-top:6px;margin-bottom:3px;\" id=\"".$prefix."resultsw".$wd_id."a".$q['field_id']."\"></div>";
      $html .= "<div style=\"margin-top:6px;margin-bottom:3px;\" id=\"".$prefix."newform".$wd_id."a".$q['field_id']."\">";
      $html .= "<div onclick=\"jQuery('#".$prefix."newuser".$wd_id."a".$q['field_id']."').show();\" style=\"font-size:8px;font-family:verdana;cursor:pointer;color:blue;\">&gt; Create New Entry</div>";
      $html .= "<div id=\"".$prefix."newuser".$wd_id."a".$q['field_id']."\" style=\"display:none;padding:5px;border:1px solid #CCCCCC;border-radius:4px;\">";

      $html .= "<select id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newtype\">";
      $html .= "<option value=\"org\">Organization</option>";
      $html .= "<option value=\"user\">Person</option>";
      $html .= "</select>";
      $html .= "<br>";
      
      $html .= "<input type=\"text\" ";
      $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newname\" ";
      $html .= "value=\"New Name\" ";
      $html .= "onblur=\"if(this.value == ''){ this.value = 'New Name'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
      $html .= "onfocus=\"if(this.value == 'New Name'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
      $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
      $html .= "<br>";
      
      $html .= "<input type=\"text\" ";
      $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newemail\" ";
      $html .= "value=\"Email\" ";
      $html .= "onblur=\"if(this.value == ''){ this.value = 'Email'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
      $html .= "onfocus=\"if(this.value == 'Email'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
      $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
      $html .= "<br>";
      
      $html .= "<input type=\"text\" ";
      $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newphone\" ";
      $html .= "value=\"Phone\" ";
      $html .= "onblur=\"if(this.value == ''){ this.value = 'Phone'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
      $html .= "onfocus=\"if(this.value == 'Phone'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
      $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
      $html .= "<br>";
      
      $html .= "<input type=\"text\" ";
      $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newnotes\" ";
      $html .= "value=\"Notes\" ";
      $html .= "onblur=\"if(this.value == ''){ this.value = 'Notes'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
      $html .= "onfocus=\"if(this.value == 'Notes'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
      $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";
      
      $html .= "<div style=\"margin-top:5px;margin-bottom:5px;height:2px;width:1px;overflow:hidden;\"></div>";

      $html .= "<span ";
      $html .= " id=\"".$prefix."w".$wd_id."a".$q['field_id']."_submit\" ";
      $html .= " style=\"cursor:pointer;padding:4px;text-align:center;font-size:10px;font-family:verdana;color:#000000;background-color:#DDDDDD;border:1px solid #888888;border-radius:4px;\" ";
      $html .= ">";
      $html .= "Create Entry";
      $html .= "</span>";
      
      $html .= "<div style=\"margin-top:5px;margin-bottom:5px;height:2px;width:1px;overflow:hidden;\"></div>";
      
      $html .= "</div>";
      $html .= "</div>";
      $html .= "</div>";
      $html .= "</div>";
      
      $html .= "\n<script>\n";
      $html .= "function search_".$prefix."w".$wd_id."a".$q['field_id']."(){\n";
      $html .= "   var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=searchusers&callback=retsearch_".$prefix."w".$wd_id."a".$q['field_id']."';\n";
      $html .= "   url += '&s_searchtxt=' + encodeURIComponent(jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').val());\n";
      if($q['question'] != NULL) $html .= "   url += '&segment=' + encodeURIComponent('".$q['question']."');\n";
      $html .= "   url += '&limit=50';\n";
      $html .= "   jsfwebdata_CallJSONP(url);\n";
      $html .= "}\n";
      $html .= "function retsearch_".$prefix."w".$wd_id."a".$q['field_id']."(jsondata){\n";
      $html .= "   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
      //$html .= "   alert('search results: ' + JSON.stringify(jsondata));\n";
      $html .= "   var str = '';\n";
      $html .= "   if(Boolean(jsondata.users) && jsondata.users.length>0) {\n";
      $html .= "      str += '<select id=\\\"".$prefix."jsw".$wd_id."a".$q['field_id']."\\\" onclick=\\\"jQuery(\\'#".$prefix."inputw".$wd_id."a".$q['field_id']."\\').val(jQuery(\\'#".$prefix."jsw".$wd_id."a".$q['field_id']."\\').val());formchange_".$prefix."('".$prefix.$q['field_id']."');\">';\n";
      $html .= "      str += '<option value=\\\"\\\">Select an Option</option>';\n";
      $html .= "      for(var i=0;i<jsondata.users.length;i++) {\n";
      $html .= "         str += '<option value=\\\"' + jsondata.users[i].userid + '\\\">' + jsondata.users[i].userid + ' ' + jsondata.users[i].fname.substring(0,8) + ' ' + jsondata.users[i].lname.substring(0,8) + ' ' + jsondata.users[i].company.substring(0,8) + '</option>';\n";
      $html .= "      }\n";
      $html .= "      str += '</select>';\n";
      $html .= "   } else {\n";
      $html .= "      str += 'No users could be found.';\n";
      $html .= "   }\n";
      $html .= "   jQuery('#".$prefix."resultsw".$wd_id."a".$q['field_id']."').html(str);\n";
      $html .= "}\n";
      $html .= "function create_".$prefix."w".$wd_id."a".$q['field_id']."(){\n";
      $html .= "   var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=adduser&callback=retcreate_".$prefix."w".$wd_id."a".$q['field_id']."';\n";
      $html .= "   var type = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newtype').val();\n";
      $html .= "   if(type=='org') {\n";
      $html .= "      url += '&usertype=org&company=' + encodeURIComponent(jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newname').val());\n";
      $html .= "   } else {\n";
      $html .= "      url += '&usertype=user&name=' + encodeURIComponent(jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newname').val());\n";
      $html .= "   }\n";
      $html .= "   url += '&email=' + encodeURIComponent(jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newemail').val());\n";
      $html .= "   url += '&phonenum=' + encodeURIComponent(jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newphone').val());\n";
      $html .= "   url += '&notes=' + encodeURIComponent(jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newnotes').val());\n";
      $html .= "   url += '&overrideemail=1';\n";
      $html .= "   jsfwebdata_CallJSONP(url);\n";
      $html .= "}\n";
      $html .= "function retcreate_".$prefix."w".$wd_id."a".$q['field_id']."(jsondata){\n";
      $html .= "   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
      //$html .= "   alert('search results: ' + JSON.stringify(jsondata));\n";
      $html .= "   var str = '';\n";
      $html .= "   if(Boolean(jsondata.responsecode) && jsondata.responsecode==1) {\n";
      $html .= "      jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(jsondata.user.userid);\n";
      $html .= "      formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
      $html .= "      str += jsondata.user.fname + ' ' + jsondata.user.company + ' added successfully.';\n";
      $html .= "   } else {\n";
      $html .= "      str += jsondata.responsetext;\n";
      $html .= "   }\n";
      $html .= "   jQuery('#".$prefix."resultsw".$wd_id."a".$q['field_id']."').html(str);\n";
      $html .= "}\n";
      $html .= "\n</script>\n";
      
      $returnobj = array();
      $returnobj['html'] = $html;
      if($fordisplay) $returnobj['js'] = $js;
      else $returnobj['js'] = "";
      $returnobj['relationshipjs1'] = $relationshipjs1;
      $returnobj['relationshipjs2'] = $relationshipjs2;
      return $returnobj; 
   }

   
   
   
   function getUserListJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary,$userid,$fordisplay){
      $savechanged = FALSE;
      $ua = new UserAcct();
      $style="";
      $questionText = $q['label'];
      if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
      $value = str_replace(",,",",",str_replace(",,",",",trim(convertBack($answered['answer']))));
      if ($value==NULL || 0==strcmp($value,"")) $value=getParameter("w".$wd_id."a".$q['field_id']);
      if (($value==NULL || 0==strcmp($value,"")) && trim($q['map'])!=NULL) $value=getParameter($q['map']);
      if (($value===NULL || 0==strcmp($value,"")) && $q['defaultval']!=NULL) {
         $savechanged = TRUE;
         $value=$q['defaultval'];
      } else if (($value===NULL || 0==strcmp($value,"")) && getParameter("foruserid")!=NULL) {
         $savechanged = TRUE;
         $value=getParameter("foruserid");
      }
      
      $showsearch = TRUE;
      if (0==strcmp(trim($q['defaultval']),"%%%NOSEARCH%%%")) {
         $showsearch=FALSE;
      }
      
      $showcreate = TRUE;
      if (0==strcmp(trim($q['defaultval']),"%%%NOCREATE%%%")) {
         $showcreate=FALSE;
      }
      
      if(0==strcmp(substr($value,0,3),"%%%")) $value = NULL;
      $value = str_replace(",,",",",str_replace(",,",",",$value));

      $html = "";
      $js = "";
      $relationshipjs1 = "";
      $relationshipjs2 = "";
      
      //$js .= "   if (!rqderror) {\n";
      //$js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
      //$js .= "   if (Boolean(temp)) {\n   url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(temp);\n      jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');   }\n";
      //if ($q['required']==1) $js .= "   else {\n   rqderror = true;\n   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n   jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n   }\n";
      //$js .= "   }\n";
      
      // This field shold be marked as changed if a value was added by default
      if($savechanged) $js .= "formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
      $js .= "   if (!rqderror) {\n";
      $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
      $js .= "   jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');\n";
      
      if ($q['required']==1) {
         $js .= " if(!Boolean(temp)) {\n";
         $js .= "   rqderror = true;\n";
         $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
         $js .= "   jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n";
         $js .= " } else ";
      }
      $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
      $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
      $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
      $js .= "       url = c_url;\n";
      $js .= "     }\n";
      $js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
      $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(temp);\n";
      $js .= "   }\n";
      
      $js .= "   }\n";
      
      

      $prefix2 = $prefix;
      $origprefix_a = separateStringBy($prefix,"_");
      if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];
      
      $stylerow =  " style=\"clear:both;".$q['stylecss']."\"";
      $class_wdrow = "wdrow";
      $class_wdq = "wdq";
      $class_wda = "wda";      
         
      $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
      $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
      $html .= $questionText;
      if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
      $html .= "</div>";
      $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
      
      // javascript will print list
      $html .= "<div id=\"".$prefix."curuserw".$wd_id."a".$q['field_id']."\" style=\"\"></div>";
      
      if($value!=NULL && 0==strcmp($q['field_type'],"USERLIST")) $value.=",";
      $html .= "<input type=\"hidden\" ";
      $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" ";
      $html .= "value=\"".$value."\" ";
      $html .= ">";
      
      
      // search-for/add a DB record
      $html .= "<div id=\"".$prefix."addreference".$wd_id."a".$q['field_id']."\" style=\"margin-bottom:20px;\">";
      if($showsearch) {
         $html .= "<input type=\"text\" ";
         $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
         $html .= "value=\"Search\" ";
         $html .= "id=\"".$prefix."searchw".$wd_id."a".$q['field_id']."\" ";
         $html .= "onblur=\"if(this.value == ''){ this.value = 'Search'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
         $html .= "onfocus=\"if(this.value == 'Search'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
         $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";
         $html .= " <span onclick=\"search_".$prefix."w".$wd_id."a".$q['field_id']."();\" style=\"padding:4px;cursor:pointer;text-align:center;color:#222222;font-size:10px;font-family:arial;background-color:#EEEEEE;border:1px solid #222222;border-radius:4px;\">Search</span>";
      }

      $html .= "<div style=\"margin-top:6px;margin-bottom:3px;\" id=\"".$prefix."resultsw".$wd_id."a".$q['field_id']."\"></div>";
   
      if($showsearch) {         
         if($showcreate) {
            //Create a new DB record
            $html .= "<div style=\"margin-top:6px;margin-bottom:3px;\" id=\"".$prefix."newform".$wd_id."a".$q['field_id']."\">";
            $html .= "<div onclick=\"togglenu_".$prefix."w".$wd_id."a".$q['field_id']."();\" style=\"font-size:8px;font-family:verdana;cursor:pointer;color:blue;\">&gt; Create New Entry</div>";
            $html .= "<div id=\"".$prefix."newuser".$wd_id."a".$q['field_id']."\" style=\"display:none;padding:5px;border:1px solid #CCCCCC;border-radius:4px;\">";
            
            $html .= "<select id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newtype\">";
            $html .= "<option value=\"org\">Organization</option>";
            $html .= "<option value=\"user\"";
            if(strpos(strtolower($q['label']),"person")!==FALSE || strpos(strtolower($q['label']),"contact")!==FALSE || strpos(strtolower($q['label']),"staff")!==FALSE) $html.= " SELECTED";
            $html .= ">Person</option>";
            $html .= "</select>";
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newname\" ";
            $html .= "value=\"New Name\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'New Name'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'New Name'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newemail\" ";
            $html .= "value=\"Email\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Email'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Email'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newphone\" ";
            $html .= "value=\"Phone\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Phone'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Phone'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newfax\" ";
            $html .= "value=\"Fax\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Fax'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Fax'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newaddr1\" ";
            $html .= "value=\"Address 1\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Address 1'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Address 1'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newaddr2\" ";
            $html .= "value=\"Address 2\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Address 2'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Address 2'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newcity\" ";
            $html .= "value=\"City\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'City'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'City'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:140px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newstate\" ";
            $html .= "value=\"State\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'State'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'State'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:70px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newzip\" ";
            $html .= "value=\"Postal Code\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Postal Code'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Postal Code'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:150px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newurl\" ";
            $html .= "value=\"Website\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Website'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Website'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<input type=\"text\" ";
            $html .= "id=\"".$prefix."w".$wd_id."a".$q['field_id']."_newnotes\" ";
            $html .= "value=\"Notes\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = 'Notes'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == 'Notes'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:230px;font-family:verdana;font-size:14px;border:1px solid #DDDDDD;border-radius:2px;font-style:italic;color:#BBBBBB;\">";      
            $html .= "<br>";
            
            $html .= "<div style=\"margin-top:5px;margin-bottom:5px;height:2px;width:1px;overflow:hidden;\"></div>";
      
            $html .= "<span ";
            $html .= " id=\"".$prefix."w".$wd_id."a".$q['field_id']."_submit\" ";
            $html .= " style=\"cursor:pointer;padding:4px;text-align:center;font-size:10px;font-family:verdana;color:#000000;background-color:#DDDDDD;border:1px solid #888888;border-radius:4px;\" ";
            $html .= " onclick=\"create_".$prefix."w".$wd_id."a".$q['field_id']."();\" ";
            $html .= ">";
            $html .= "Create Entry";
            $html .= "</span>";
            
            $html .= "<div style=\"margin-top:5px;margin-bottom:5px;height:2px;width:1px;overflow:hidden;\"></div>";
            
            $html .= "</div>";
            // END new record
            
            $html .= "</div>";
            // END toggle new record
         }
      }
      $html .= "</div>";
      // END search-for/add new record
      
      
      
      
      $html .= "</div>";
      $html .= "</div>";
     
      $html .= "\n<script>\n";
      //$html .= "alert('".$value."-".$value1."-".$value2."-".$value3."-".$value4."');\n";
      $html .= "function togglenu_".$prefix."w".$wd_id."a".$q['field_id']."(forceclose){\n";
      $html .= "  if(Boolean(forceclose) || jQuery('#".$prefix."newuser".$wd_id."a".$q['field_id']."').is(':visible')){\n";
      $html .= "    jQuery('#".$prefix."newuser".$wd_id."a".$q['field_id']."').hide();\n";
      $html .= "  } else {\n";
      $html .= "    jQuery('#".$prefix."newuser".$wd_id."a".$q['field_id']."').show();\n";
      $html .= "  }\n";
      $html .= "}\n";
      $html .= "function rmuser_".$prefix."w".$wd_id."a".$q['field_id']."(uid){\n";
      $html .= "  if(Boolean(uid)) {\n";
      $html .= "    var val = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
      $html .= "    var uids = val.split(',');\n";
      $html .= "    var str = '';\n";
      $html .= "    for(var i=0;i<uids.length;i++) {\n";
      $html .= "      if(Boolean(uids[i]) && uid!=uids[i]){\n";
      $html .= "        if(i>0) str += ',';\n";
      $html .= "        str += uids[i];\n";
      $html .= "      }\n";
      $html .= "    }\n";
      $html .= "    jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(str);\n";
      $html .= "    formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
      $html .= "    ulist_".$prefix."w".$wd_id."a".$q['field_id']."();\n";
      $html .= "  }\n";
      $html .= "}\n";
      $html .= "function adduser_".$prefix."w".$wd_id."a".$q['field_id']."(uid){\n";
      $html .= "  if(Boolean(uid)) {\n";
      $html .= "    var str = '';\n";
      if(0!=strcmp($q['field_type'],"USERSRCH") && 0!=strcmp($q['field_type'],"USERS") && 0!=strcmp($q['field_type'],"USERAUTO")) {
         $html .= "  var val = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         $html .= "  var uids = val.split(',');\n";
         $html .= "  for(var i=0;i<uids.length;i++) {\n";
         $html .= "    if(Boolean(uids[i]) && uid!=uids[i]){\n";
         $html .= "      if(Boolean(str) && str.length>0) str += ',';\n";
         $html .= "      str += uids[i];\n";
         $html .= "    }\n";
         $html .= "  }\n";
         $html .= "  if(Boolean(str) && str.length>0) str += ',';\n";
      }
      $html .= "    str += uid;\n";
      $html .= "    jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(str);\n";
      $html .= "    formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
      $html .= "    ulist_".$prefix."w".$wd_id."a".$q['field_id']."();\n";
      $html .= "  }\n";
      $html .= "}\n";
      $html .= "function ulist_".$prefix."w".$wd_id."a".$q['field_id']."(){\n";
      $html .= "   var uids = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
      $html .= "   if(Boolean(uids)){\n";
      $html .= "     jQuery('#".$prefix."curuserw".$wd_id."a".$q['field_id']."').html('Loading user list...');\n";
      $html .= "     var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=searchusers&callback=retulist_".$prefix."w".$wd_id."a".$q['field_id']."';\n";
      $html .= "     url += '&s_filter=' + encodeURIComponent(uids);\n";
      $html .= "     url += '&limit=100';\n";
      $html .= "     jsfwebdata_CallJSONP(url);\n";
      $html .= "   } else {\n";
      $html .= "     jQuery('#".$prefix."curuserw".$wd_id."a".$q['field_id']."').html('');\n";
      $html .= "     jQuery('#".$prefix."addreference".$wd_id."a".$q['field_id']."').show();\n";
      $html .= "   }\n";
      $html .= "}\n";
      $html .= "function retulist_".$prefix."w".$wd_id."a".$q['field_id']."(jsondata){\n";
      $html .= "   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
      $html .= "   var str = '';\n";
      $html .= "   var showaddreference = true;\n";
      $html .= "   if(Boolean(jsondata.users) && jsondata.users.length>0) {\n";
      $html .= "      for(var i=0;i<jsondata.users.length;i++) {\n";
      $html .= "         str += '<div style=\\\"position:relative;margin-top:2px;margin-bottom:2px;padding-bottom:2px;border-bottom:1px solid #DDDDDD;width:300px;\\\">';\n";
      $html .= "         str += '<div style=\\\"float:left;width:250px;overflow:hidden;';\n";
      if($ua->isUserAdmin($userid) || (isLoggedOn() && $ua->isUserAdmin(isLoggedOn()))) {
         $html .= "str += 'cursor:pointer;\\\" onclick=\\\"window.open(\\'".getBaseURL()."jsfadmin/admincontroller.php?action=usermodcloning&userid=' + jsondata.users[i].userid + '\\');';\n";
      }
      $html .= "         str += '\\\">';\n";
      $html .= "         str += jsondata.users[i].userid + '. ';\n";
      $html .= "         str += jsondata.users[i].fname + ' ';\n";
      $html .= "         str += jsondata.users[i].lname + ' ';\n";
      $html .= "         str += jsondata.users[i].company + ' ';\n";
      $html .= "         str += '</div>';\n";
      $html .= "         str += '<div style=\\\"float:left;margin-left:8px;width:40px;overflow:hidden;\\\">';\n";
      $html .= "         str += '<span style=\\\"color:red;font-size:8px;cursor:pointer;\\\" onclick=\\\"if(confirm(\\'Remove Record?\\')) rmuser_".$prefix."w".$wd_id."a".$q['field_id']."(' + jsondata.users[i].userid + ');\\\">';\n";
      $html .= "         str += 'remove';\n";
      $html .= "         str += '</span>';\n";
      $html .= "         str += '</div>';\n";
      $html .= "         str += '<div style=\\\"clear:both;\\\"></div>';\n";
      $html .= "         str += '</div>';\n";
      $html .= "      }\n";
      $html .= "      str += '<div style=\\\"margin-top:2px;margin-bottom:10px;width:100%;height:1px;overflow:hidden;\\\"></div>';\n";
      if(0==strcmp($q['field_type'],"USERSRCH") || 0==strcmp($q['field_type'],"USERAUTO") || 0==strcmp($q['field_type'],"USERS")) $html .= " showaddreference = false;\n";
      $html .= "   }\n";
      $html .= "   jQuery('#".$prefix."curuserw".$wd_id."a".$q['field_id']."').html(str);\n";
      $html .= "   if(showaddreference) jQuery('#".$prefix."addreference".$wd_id."a".$q['field_id']."').show();\n";
      $html .= "   else jQuery('#".$prefix."addreference".$wd_id."a".$q['field_id']."').hide();\n";
      $html .= "}\n";
      $html .= "function search_".$prefix."w".$wd_id."a".$q['field_id']."(){\n";
      $html .= "   var filterstr = jQuery('#".$prefix."searchw".$wd_id."a".$q['field_id']."').val();\n";
      $html .= "   if(!Boolean(filterstr) || filterstr=='Search') filterstr = '';\n";
      $html .= "   var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=searchusers&callback=retsearch_".$prefix."w".$wd_id."a".$q['field_id']."';\n";
      $html .= "   url += '&s_searchtxt=' + encodeURIComponent(filterstr);\n";
      if(trim(convertBack($q['question']))!=NULL) $html .= "   url += '&segment=' + encodeURIComponent('".trim(convertBack($q['question']))."');\n";
      $html .= "   url += '&limit=50';\n";
      $html .= "   jsfwebdata_CallJSONP(url);\n";
      $html .= "}\n";
      $html .= "function retsearch_".$prefix."w".$wd_id."a".$q['field_id']."(jsondata){\n";
      $html .= "   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
      //$html .= "   alert('search results: ' + JSON.stringify(jsondata));\n";
      $html .= "   var str = '';\n";
      $html .= "   if(Boolean(jsondata.users) && jsondata.users.length>0) {\n";
      $html .= "      str += '<select id=\\\"".$prefix."jsw".$wd_id."a".$q['field_id']."\\\" ';\n";
      $html .= "      str += ' onchange=\\\"';\n";
      //if(0==strcmp($q['field_type'],"USERSRCH") || 0==strcmp($q['field_type'],"USERS")) $html .= "str += 'jQuery(\\'#".$prefix."inputw".$wd_id."a".$q['field_id']."\\').val(jQuery(\\'#".$prefix."jsw".$wd_id."a".$q['field_id']."\\').val());';\n";
      //else $html .= " str += 'jQuery(\\'#".$prefix."inputw".$wd_id."a".$q['field_id']."\\').val(jQuery(\\'#".$prefix."inputw".$wd_id."a".$q['field_id']."\\').val() + jQuery(\\'#".$prefix."jsw".$wd_id."a".$q['field_id']."\\').val() + \\',\\');';\n";
      $html .= "      str += 'adduser_".$prefix."w".$wd_id."a".$q['field_id']."(jQuery(\\'#".$prefix."jsw".$wd_id."a".$q['field_id']."\\').val());';\n";
      //$html .= "      str += 'formchange_".$prefix."(\\'".$prefix.$q['field_id']."\\');ulist_".$prefix."w".$wd_id."a".$q['field_id']."();';\n";
      $html .= "      str += '\\\">';\n";
      $html .= "      str += '<option value=\\\"\\\">Select an Option</option>';\n";
      $html .= "      for(var i=0;i<jsondata.users.length;i++) {\n";
      $html .= "         str += '<option value=\\\"' + jsondata.users[i].userid + '\\\">' + jsondata.users[i].userid + ' ' + jsondata.users[i].fname.substring(0,8) + ' ' + jsondata.users[i].lname.substring(0,8) + ' ' + jsondata.users[i].company.substring(0,8) + '</option>';\n";
      $html .= "      }\n";
      $html .= "      str += '</select>';\n";
      $html .= "   } else {\n";
      $html .= "      str += 'No users could be found.';\n";
      $html .= "   }\n";
      $html .= "   jQuery('#".$prefix."resultsw".$wd_id."a".$q['field_id']."').html(str);\n";
      $html .= "}\n";
      $html .= "function create_".$prefix."w".$wd_id."a".$q['field_id']."(){\n";
      $html .= "   var newname = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newname').val();\n";
      $html .= "   if(Boolean(newname) && newname!='New Name') {\n";
      $html .= "     var newemail = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newemail').val();\n";
      $html .= "     if(newemail=='Email') newemail = '';\n";
      $html .= "     var newphone = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newphone').val();\n";
      $html .= "     if(newphone=='Phone') newphone = '';\n";
      $html .= "     var newfax = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newfax').val();\n";
      $html .= "     if(newfax=='Fax') newfax = '';\n";
      
      $html .= "     var newaddr1 = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newaddr1').val();\n";
      $html .= "     if(newaddr1=='Address 1') newaddr1 = '';\n";
      $html .= "     var newaddr2 = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newaddr2').val();\n";
      $html .= "     if(newaddr2=='Address 2') newaddr2 = '';\n";
      $html .= "     var newcity = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newcity').val();\n";
      $html .= "     if(newcity=='City') newcity = '';\n";
      $html .= "     var newstate = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newstate').val();\n";
      $html .= "     if(newstate=='State') newstate = '';\n";
      $html .= "     var newzip = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newzip').val();\n";
      $html .= "     if(newzip=='Postal Code') newzip = '';\n";
      
      $html .= "     var newurl = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newurl').val();\n";
      $html .= "     if(newurl=='Website') newurl = '';\n";
      $html .= "     var newnotes = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newnotes').val();\n";
      $html .= "     if(newnotes=='Notes') newnotes = '';\n";
      $html .= "     var url = defaultremotedomain + '".$GLOBALS['codeFolder']."jsoncontroller.php?format=jsonp&action=adduser&callback=retcreate_".$prefix."w".$wd_id."a".$q['field_id']."';\n";
      $html .= "     var type = jQuery('#".$prefix."w".$wd_id."a".$q['field_id']."_newtype').val();\n";
      $html .= "     if(type=='org') {\n";
      $html .= "        url += '&usertype=org&company=' + encodeURIComponent(newname);\n";
      $html .= "     } else {\n";
      $html .= "        url += '&usertype=user&name=' + encodeURIComponent(newname);\n";
      $html .= "     }\n";
      $html .= "     url += '&email=' + encodeURIComponent(newemail);\n";
      $html .= "     url += '&phonenum=' + encodeURIComponent(newphone);\n";
      $html .= "     url += '&phonenum2=' + encodeURIComponent(newfax);\n";
      $html .= "     url += '&addr1=' + encodeURIComponent(newaddr1);\n";
      $html .= "     url += '&addr2=' + encodeURIComponent(newaddr2);\n";
      $html .= "     url += '&city=' + encodeURIComponent(newcity);\n";
      $html .= "     url += '&state=' + encodeURIComponent(newstate);\n";
      $html .= "     url += '&zip=' + encodeURIComponent(newzip);\n";
      $html .= "     url += '&website=' + encodeURIComponent(newurl);\n";
      $html .= "     url += '&notes=' + encodeURIComponent(newnotes);\n";
      $html .= "     url += '&overrideemail=1';\n";
      $html .= "     url += '&password=123456';\n";
      $html .= "     url += '&cpassword=123456';\n";
      $html .= "     jsfwebdata_CallJSONP(url);\n";
      $html .= "   } else {\n";
      $html .= "     alert('Please enter a name to create a new database record.');\n";
      $html .= "   }\n";
      $html .= "}\n";
      $html .= "function retcreate_".$prefix."w".$wd_id."a".$q['field_id']."(jsondata){\n";
      $html .= "   if (typeof jsf_endjsoning == 'function') jsf_endjsoning();\n";
      //$html .= "   alert('search results: ' + JSON.stringify(jsondata));\n";
      $html .= "   var str = '';\n";
      $html .= "   if(Boolean(jsondata.responsecode) && jsondata.responsecode==1) {\n";
      if(0==strcmp($q['field_type'],"USERSRCH") || 0==strcmp($q['field_type'],"USERAUTO") || 0==strcmp($q['field_type'],"USERS")) $html .= " jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(jsondata.user.userid);\n";
      else $html .= " jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val() + jsondata.user.userid + ',');\n";
      $html .= "      formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
      $html .= "      ulist_".$prefix."w".$wd_id."a".$q['field_id']."();\n";
      $html .= "      togglenu_".$prefix."w".$wd_id."a".$q['field_id']."(true);\n";
      $html .= "   } else {\n";
      $html .= "      alert(jsondata.responsetext);\n";
      $html .= "   }\n";
      $html .= "}\n";
      $html .= "ulist_".$prefix."w".$wd_id."a".$q['field_id']."();\n";
      if(trim(convertBack($q['question']))!=NULL) $html .= "search_".$prefix."w".$wd_id."a".$q['field_id']."();\n";

      $html .= "\n</script>\n";

      $returnobj = array();
      $returnobj['html'] = $html;
      if($fordisplay) $returnobj['js'] = $js;
      else $returnobj['js'] = "";
      $returnobj['relationshipjs1'] = $relationshipjs1;
      $returnobj['relationshipjs2'] = $relationshipjs2;
      return $returnobj; 
   }

   
   
   
   
   
   
   
      function getTextJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $style="";
         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         }
         $javascript = "";
         $okujavascript = "";
         $okdjavascript = "";
         $javascript2 = "";
         
         //if ($rels1!=NULL && count($rels1)>0) {
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            //$javascript = " onKeyUp=\"".$prefix."change".$wd_id.$q['field_id']."();\"";
            $okujavascript = $prefix."change".$wd_id.$q['field_id']."();";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
            
            $rem_flds = array();
            $rem_nflds = array();
            
            for ($i=0; $i<count($rels1); $i++) {
               if(!in_array($rels1[$i]['fid2'],$rem_flds)) {
                  $rem_flds[] = $rels1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').hide();\n";
                  $javascript2 .= $temp;
                  $relationshipjs1 .= $temp;
               }
            }
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$rels1[$i]['f1value']."'){\n";
               $temp .= "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').show();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            for ($i=0; $i<count($nrels1); $i++) {
               //$relationshipjs1 .= "   document.getElementById('".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').style.display='';\n";
               if(!in_array($nrels1[$i]['fid2'],$rem_nflds)) {
                  $rem_nflds[] = $nrels1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').show();\n";
                  $javascript2 .= $temp;
                  $relationshipjs1 .= $temp;
               }
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$nrels1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').hide();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            for ($i=0; $i<count($scts1); $i++) {
               if(!in_array($scts1[$i]['fid2'],$rem_flds)) {
                  $rem_flds[] = $scts1[$i]['fid2'];
                  $temp = "   jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').hide();\n";
                  $javascript2 .= $temp;
                  $relationshipjs1 .= $temp;
               }
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$scts1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').show();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            for ($i=0; $i<count($nscts1); $i++) {
               if(!in_array($nscts1[$i]['fid2'],$rem_nflds)) {
                  $rem_nflds[] = $nscts1[$i]['fid2'];
                  $temp = "   jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').show();\n";
                  $javascript2 .= $temp;
                  $relationshipjs1 .= $temp;
               }
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$nscts1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').hide();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            $javascript2 .= "}\n</script>\n";
         }
         //$javascript .= " onkeypress=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         //$javascript .= " onkeyup=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         $okujavascript .= "formchange_".$prefix."('".$prefix.$q['field_id']."');";
         if (strcmp($q['field_type'],"INT")==0) {
            $okdjavascript .= "var x=event.which || event.keyCode;if(x!=188 && x!=8 && x!=46 && x!=45 && x!=37 && x!=39 && (x<48 || x>57)) event.preventDefault();";
            //$javascript .= "var x=event.which || event.keyCode;if(x!=8 && x!=46 && x!=45 && x!=37 && x!=39 && (x<48 || x>57)) return false;";
            //$javascript .= " onkeypress=\"var x=event.which || event.keyCode;alert('code: ' + x);\"";
         }
         $javascript .= " onkeyup=\"".$okujavascript."\"";
         $javascript .= " onkeydown=\"".$okdjavascript."\"";

         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if(strlen(strip_tags($q['label']))>22) {
            $class_wdq = "wdqrow";
            $class_wda = "wdarow";
         }
         
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }

         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         if (strcmp($q['field_type'],"INT")==0 || strcmp($q['field_type'],"DEC")==0) {
            $js .= "   temp = jsfwebdata_replaceAll(',','',temp);\n";
         }
         $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');\n";
         
         if ($q['required']==1) {
            $js .= " if(!Boolean(temp)) {\n";
            $js .= "   rqderror = true;\n";
            $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
            $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n";
            $js .= " } else ";
         }
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     if(!Boolean(temp)) temp='%E%';\n";
         $js .= "     url += '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "   }\n";
         
         $js .= "   }\n";
         
         
         $html .= $javascript2;
         
         
         if(!$fordisplay) {
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\">";
            $html .= "<input type=\"hidden\" ";
            $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
            $html .= "value=\"".$value."\" ";
            $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\">";
            $html .= "</div>";
         } else if(FALSE===strpos($stylerow,"display:none;")) {
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
            $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
            $html .= $questionText;
            if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
            $html .= "</div>";
            $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         
            if ($explicitcss==1) $style=" style=\"font-size:12px;font-family:arial;width:230px;color:#222222;\"";
            
            if($q['disa']==1) {
               if(strlen(trim($value)) > 0) $html .= convertString($value);
               else $html .= "<i>Empty</i>";
               $html .= "<input type=\"hidden\" ";
               $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
               $html .= "value=\"".$value."\" ";
               $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\"".$style." ";
               $html .= ">";
            } else {
               $html .= "<input type=\"text\" ";
               $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
               
               if (strcmp($q['field_type'],"INT")==0) $html .= "value=\"".formatNumberCommas($value)."\" ";
               else $html .= "value=\"".$value."\" ";
               
               $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\"".$style." ";
               $html .= "class=\"".$prefix2."winput_txt\"".$javascript.">";
            }
            
            if(0==strcmp($q['field_type'],"COLOR")) {
               $html .= "<div id=\"plt_".$prefix."inputw".$wd_id."a".$q['field_id']."\" style=\"\"></div>";
               $html .= "\n<script>\njsfwd_createSpectrum('".$prefix."inputw".$wd_id."a".$q['field_id']."','formchange_".$prefix."(\'".$prefix.$q['field_id']."\');');\n</script>\n";
            }
            $html .= "</div>";
            $html .= "</div>";
         } else {
            $tempstyle = "color:#222222;font-style:normal;";
            if($value==NULL) {
               $value=$questionText;
               $tempstyle = "color:#BBBBBB;font-style:italic;";
            }
            
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\">";
            $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         
            $html .= "<input type=\"text\" ";
            $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
            $html .= "value=\"".$value."\" ";
            $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\"".$style." ";
            $html .= "onblur=\"if(this.value == ''){ this.value = '".$questionText."'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == '".$questionText."'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "style=\"width:200px;font-family:verdana;font-size:16px;border:1px solid #DDDDDD;border-radius:2px;".$tempstyle."\"".$javascript.">";
            $html .= "</div>";
            $html .= "</div>";            
         }
         
         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      function getTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $questionText = convertBack($q['label']);
         //if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $temp = separateStringBy($questionText,";");
         $headers = separateStringBy($temp[0],",");
         $rows = separateStringBy($temp[1],",");
         $answers = separateStringBy($answered['answer'],",");
         if($answers==NULL || count($answers)<1) $answers = separateStringBy($q['defaultval'],",");
         
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix."_tablediv\">";
         $html .= "<div style=\"position:relative;width:100%;overflow:auto;".$q['stylecss']."\">";
         
         $wd1 = 220;
         $wd2 = 120;
         
         if($fordisplay) {
            $html .= "<table cellpadding=\"3\" cellspacing=\"1\" style=\"font-size:12px;font-family:arial;font-weight:normal;\">";         
            $html .= "<tbody class=\"jsfwdtablescroll\">";
            $html .= "<tr bgcolor=\"#EEEEEE\">";
            $html .= "<td><div style=\"width:".$wd1."px;overflow:hidden;text-align:center;\">";
            if (trim($headers[0])!=null && $glossary!=NULL) $html .= $glossary->flagAllTerms(trim($headers[0]),"#5691c4");
            else $html .= trim($headers[0]);
            $html .= "</div></td>";
            for($i=1; $i<count($headers); $i++) {
               $html .= "<td><div style=\"width:".$wd2."px;overflow:hidden;text-align:center;\">";
               if (trim($headers[$i])!=null && $glossary!=NULL) $html .= $glossary->flagAllTerms(trim($headers[$i]),"#5691c4");
               else $html .= trim($headers[$i]);
               $html .= "</div></td>";
            }
            $html .= "</tr>";
            $html .= "</tbody>";
            $html .= "</table>";
            $html .= "<table cellpadding=\"3\" cellspacing=\"1\" style=\"font-size:12px;font-family:arial;font-weight:normal;\">";         
            $html .= "<tbody class=\"jsfwdtablescroll\">";
         }
         
         $acount = 0;
         $js .= "   if (!rqderror && Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         for($i=0; $i<count($rows); $i++) {
            $bgc = "#FFFFFF";
            if(($i%2) == 1) $bgc="#F8F8F8";
            if($fordisplay) {
               $html .= "<tr bgcolor=\"".$bgc."\">";
               $html .= "<td bgcolor=\"#EEEEEE\"><div style=\"width:".$wd1."px;overflow:hidden;\">";
               if (trim($rows[$i])!=null && $glossary!=NULL) $html .= $glossary->flagAllTerms(trim($rows[$i]),"#5691c4");
               else $html .= trim($rows[$i]);
               $html .= "</div></td>";
            }
            
            for($j=1; $j<count($headers); $j++) {
               //$js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."_".$acount."').val();\n";
               //$js .= "   if(!Boolean(temp)) temp='%E%';\n";
               //$js .= "   url = url + '&w".$wd_id."a".$q['field_id']."_".$acount."=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
               
               if($fordisplay) $html .= "<td align=\"center\"><div style=\"width:".$wd2."px;overflow:hidden;\">";
               if($q['disa']==1) {
                  $html .= "<input ";
                  $html .= "type=\"hidden\" ";
                  $html .= "name=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$acount."\" ";
                  $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$acount."\" ";
                  $html .= "value=\"".trim($answers[$acount])."\" ";
                  $html .= ">";
                  $html .= trim($answers[$acount]);
               } else {
                  $html .= "<input ";
                  $html .= "type=\"text\" ";
                  //$html .= "onkeypress=\"formchange_".$prefix."('".$prefix.$q['field_id']."');\" ";
                  $html .= "onkeyup=\"formchange_".$prefix."('".$prefix.$q['field_id']."');\" ";
                  $html .= "style=\"width:".($wd2 - 30)."px;font-size:12px;font-family:arial;\" ";
                  //$html .= "style=\"width:110px;font-size:12px;font-family:arial;\" ";
                  $html .= "name=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$acount."\" ";
                  $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$acount."\" ";
                  $html .= "value=\"".trim($answers[$acount])."\" ";
                  $html .= ">";
               }
               if($fordisplay) $html .= "</div></td>";
               $acount++;
            }
            
            if($fordisplay) $html .= "</tr>";
         }
         $js .= "url=jsfwebdata_tableurl('w".$wd_id."a".$q['field_id']."','".$prefix."inputw".$wd_id."a".$q['field_id']."','".$acount."',url);\n";
         $js .= "url += '&w".$wd_id."jsfarray".$q['field_id']."=".$acount."';\n";
         $js .= "if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "  c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "  url = c_url;\n";
         $js .= "}\n";
         $js .= "}\n";
         
         if($fordisplay) $html .= "</tbody>";
         if($fordisplay) $html .= "</table>";
         
         $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }
      
      // FOREIGNSRY
      // label displayed.
      // questionText: wd table name;<comma-separated row names> or <# of rows selectable,label for dropdown> or <nothing, allowing ability for user to add as many rows as they wish>
      function getWDTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];         
         
         $divid = $prefix."_w".$wd_id.$q['field_id'];
         
         $tableformat = FALSE;
         if(strcmp($q['field_type'],"FOREIGNSRY")==0) $tableformat=TRUE;
         $res = $this->getInnerSurveyDisplay($wd_id,$q,NULL,$answered['row']['wd_row_id'],$divid,$tableformat);         
         //$res = $this->getTableDisplay($wd_id,$q,NULL,$answered['row']['wd_row_id'],$divid);
         if($res!=NULL) {
            $html .= "<script>\n";
            $html .= $res['refreshjs'];
            $html .= "\n</script>\n";
            if($q['stylecss']==NULL) $q['stylecss'] = "margin-top:2px;margin-bottom:8px;font-weight:bold;";
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2."wdrow ".$prefix2."inttableoutter\" style=\"".$q['stylecss']."\">";
            $html .= trim(convertBack($q['label']));
            $html .= "<div style=\"padding:2px;font-weight:normal;width:100%;overflow:auto;\" id=\"".$divid."\">";
            $html .= $res['html'];
            $html .= "</div>";
            $html .= "</div>";

            $js .= "if (!rqderror) {\n";
            $js .= "   if(url.length>".$this->getMaxAJAX().") {\n";
            $js .= "     c_urls.push(url + '&chj=' + c_urls.length);\n";
            $js .= "     url = c_url;\n";
            $js .= "   }\n";
            $js .= "   url = url + ".$res['js'].";\n";
            $js .= "}\n";
         }
         
         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }
      
      
      function getWDHybridTableJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];         
         
         $divid = $prefix."_w".$wd_id.$q['field_id'];
         
         $tableformat = FALSE;
         $res = $this->getInnerSurveyDisplayHybrid($wd_id,$q,NULL,$answered['row']['wd_row_id'],$divid);         
         if($res!=NULL) {
            if($q['stylecss']==NULL) $q['stylecss'] = "margin-top:2px;margin-bottom:8px;font-weight:bold;";
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2."wdrow\" style=\"".$q['stylecss']."padding:5px;border:1px solid #707070;border-radius:5px;font-size:14px;font-family:verdana;color:#111111;\">";
            $html .= trim(convertBack($q['label']));
            $html .= "<div style=\"padding:2px;font-weight:normal;width:100%;overflow:auto;\" id=\"".$divid."\">";
            $html .= $res['html'];
            $html .= "</div>";
            $html .= "</div>";

            $js .= "if (!rqderror) {\n";
            $js .= "   if(url.length>".$this->getMaxAJAX().") {\n";
            $js .= "     c_urls.push(url + '&chj=' + c_urls.length);\n";
            $js .= "     url = c_url;\n";
            $js .= "   }\n";
            $js .= $res['js'];
            $js .= "}\n";
         }
         
         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }
      
      
      function getTextAreaJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) $value=$q['defaultval'];
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         
         if ($q['required']==1) {
            $js .= " if(!Boolean(temp)) {\n";
            $js .= "   rqderror = true;\n";
            $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
            $js .= " } else ";
         }
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         //$js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
         $js .= "     if(!Boolean(temp)) temp='%E%';\n";
         $js .= "     if((url.length + temp.length) > ".$this->getMaxAJAX().") {\n";
         $js .= "       var totalc = 0;\n";
         $js .= "       while(totalc<temp.length) {\n";
         $js .= "         c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "         url = c_url;\n";
         $js .= "         var l = ".$this->getMaxAJAX()." - url.length;\n";
         $js .= "         if(l>1400) l = 1400;\n";
         $js .= "         var p = '&w".$wd_id."a".$q['field_id']."';\n";
         $js .= "         if(totalc>0) p += '_append';\n";
         $js .= "         url = url + p + '=' + encodeURIComponent(jsfwebdata_convertstring(temp.substr(totalc,l)));\n";
         $js .= "         totalc = totalc + l;\n";
         $js .= "       }\n";
         $js .= "     } else {\n";
         $js .= "       url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "     }\n";
         $js .= "   }\n";
         
         $js .= "   }\n";
         
         $style="";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         $class_ta = "winput_ta";
         //if(strlen(strip_tags($q['label']))>22) {
         if(strlen(strip_tags($q['label']))>1) {
            $class_wdq = "wdqrow";
            $class_wda = "wdarow";
            $class_ta = "winput_tarow";
         }
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;padding:3px;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }
         
         if(FALSE===strpos($stylerow,"display:none;")) {
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
            $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
            $html .= $questionText;
            if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
            $html .= "</div>";
            $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         
            if ($explicitcss==1) {
               $style=" style=\"font-size:12px;font-family:arial;width:230px;height:150px;color:#222222;\"";
            } else {
               $height = trim(convertBack($q['question']));
               if ($height!=NULL && is_numeric($height)) $style=" style=\"height:".$height."px;\"";
            }
            
            if($q['disa']==1) {
               $tv = convertString($value);
               if(strlen(trim($value))<1) $tv = "<i>Empty</i>";
               
               $html .= $tv."\n";
               $html .= "<input type=\"hidden\" ";
               $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
               $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" ";
               $html .= "value=\"".convertString($value)."\">\n";
            } else {
               $html .= "<textarea ";
               $html .= "onkeyup=\"formchange_".$prefix."('".$prefix.$q['field_id']."');\" ";
               $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
               $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" ";
               $html .= "class=\"".$prefix2.$class_ta."\" ";
               $html .= $style.">".convertBack($value)."</textarea>\n";
            }
            $html .= "</div>";
            $html .= "</div>";
         } else {
            $tempstyle = "color:#222222;font-style:normal;";
            if($value==NULL) {
               $value=$questionText;
               $tempstyle = "color:#BBBBBB;font-style:italic;";
            }
            
            $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\">";
            $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";

            $html .= "<textarea ";
            $html .= "onkeyup=\"formchange_".$prefix."('".$prefix.$q['field_id']."');\" ";
            $html .= "onblur=\"if(this.value == ''){ this.value = '".$questionText."'; this.style.fontStyle='italic'; this.style.color='#BBBBBB';}\" ";
            $html .= "onfocus=\"if(this.value == '".$questionText."'){ this.value = ''; this.style.fontStyle='normal'; this.style.color='#222222';}\" ";
            $html .= "name=\"w".$wd_id."a".$q['field_id']."\" ";
            $html .= "id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" ";
            $html .= "style=\"width:200px;height:100px;font-family:verdana;font-size:16px;border:1px solid #DDDDDD;border-radius:2px;".$tempstyle."\" ";
            $html .= ">".convertBack($value)."</textarea>\n";
            
            $html .= "</div>";
            $html .= "</div>";            
         }

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      //Note: there is a calendar.js created for this new Date object.  Need to make sure it is referenced
      // on the page if this is to work correctly.
      //   <script type='text/javascript'>
      //      var str = showCalendarInput('name2');
      //      document.write(str);
      //   </script>
      function getDateJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $questionText = $q["label"];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $answerText = trim(convertBack($answered["answer"]));
         if ($answerText===NULL || 0==strcmp($answerText,"")) $answerText=trim($q["defaultval"]);
         if (0==strcmp($answerText,"NOW")) $answerText=getDateForDB();
         if (0==strcmp($answerText,"EMPTY") || 0==strcmp($answerText,"%%%NONE%%%") || 0==strcmp($answerText,"%%%EMPTY%%%") || 0==strcmp($answerText,"%E%")) $answerText=NULL;
         if ($answerText!=NULL){
            $temparr = separateStringBy($answerText," ");
            $temp = separateStringBy($temparr[0],"-");
            if($temp!=NULL && count($temp)==3) {
               $answerText = $temp[1]."/".$temp[2]."/".$temp[0];
            }
            
            $temp = separateStringBy($temparr[1],":");
            if($temp!=NULL && count($temp)>1) {
               $answerText .= " ".$temp[0].":".$temp[1];
            }
         }

         $questionText = $q["label"];
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $style="";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }
         
         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');\n";
         
         if ($q['required']==1) {
            $js .= " if(!Boolean(temp)) {\n";
            $js .= "   rqderror = true;\n";
            $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
            $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n";
            $js .= " } else ";
         }
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         $js .= "   }\n";
         
         $js .= "   }\n";

         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q["field_id"]."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         $html .= "<div id=\"".$prefix."wdq_".$q["field_id"]."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
         $html .= $questionText;
         if ($q["required"]==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
         
         $html .= "</div>";
         $html .= "</div>";

         $html .= "\n<script type=\"text/javascript\">\n";
         $html .= "var str = '';\n";
         $html .= "var temponchange = 'formchange_".$prefix."(\\'".$prefix.$q['field_id']."\\');';\n";
         $html .= "if (typeof showCalendarInput === 'function') str += showCalendarInput('".$prefix."inputw".$wd_id."a".$q['field_id']."','".$answerText."',temponchange,0);\n";
         $html .= "else str += '<input onkeyup=\\\"' + temponchange + '\\\" type=\\\"text\\\" id=\\\"".$prefix."inputw".$wd_id."a".$q['field_id']."\\\" value=\\\"".$answerText."\\\">';\n";
         $html .= "jQuery('#".$prefix."wda_".$q['field_id']."').html(str);\n";
         
         $html .= "function datechange_".$prefix."_".$q['field_id']."(){\n";
         $html .= " if(typeof formchange_".$prefix." === 'function') {\n";
         $html .= "   formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
         $html .= " } else {\n";
         $html .= "   setTimeout(datechange_".$prefix."_".$q['field_id'].",400);\n";
         $html .= " }\n";
         $html .= "}\n";
         $html .= "datechange_".$prefix."_".$q['field_id']."();\n";
         
         $html .= "</script>\n";
         
         
         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      function getInfoJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $questionText = convertBack($q['label']);
         if ($q['disa']!=1 && $questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $html = "";
         
         $row = $answered['row'];
         foreach($row as $n => $v){
            $str = "%%%".$n."%%%";
            $questionText = str_replace($str,$v,$questionText);
         }
         
         if($row['userid']!=NULL){
            $ua = new UserAcct();
            $user = $ua->getUser($row['userid']);
            unset($user['password']);
            unset($user['password2']);
            foreach($user as $n => $v) {
               $str = "%%%USER_".strtoupper($n)."%%%";
               $questionText = str_replace($str,$v,$questionText);
            }
         }

         $str = "%%%wd_row_id%%%";
         $questionText = str_replace($str,$row['wd_row_id'],$questionText);
         $str = "%%%short_id%%%";
         $questionText = str_replace($str,base_convert($row['wd_row_id'], 10, 36),$questionText);
         
         $template = new Template();
         $questionText = $template->doSubstitutions($questionText);
         
         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $style="";
         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         $class_wdrow = "wdrow";
         if ($tableformat) {
            $class_wdrow = "wdcell";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
         }


         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2."winput_info";
         if (trim(strtolower($q['defaultval'])) != NULL) $html.=strtolower($q['defaultval']);
         $html .= "\">".$questionText."</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = "";
         $returnobj['relationshipjs2'] = "";
         return $returnobj;
      }

      function getSpacerJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $class_wdrow = "wdrow";
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\">";
         $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2."winput_spacer\"></div>";
         $html .= "</div>";
         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      
      
      
      
      function getRadioJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $answer = convertBack(trim($answered['answer']));
         if ($answer==NULL || 0==strcmp($answer,"")) $answer=trim($q['defaultval']);
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         }

         $javascript2 = "";
         $javascript = " onClick=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            $javascript .= $prefix."change".$wd_id.$q['field_id']."();";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
            $temp = "   var inputs = document.getElementsByName('w".$wd_id."a".$q['field_id']."');\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;

            for ($i=0; $i<count($rels1); $i++) {
               $temp = "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "   document.getElementById('".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "   document.getElementById('".$prefix."sxn".$nscts1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }

            $temp = "   for (var i=0;i<inputs.length;i++){\n";
            $temp .= "      var e = inputs[i];\n";
            for ($i=0; $i<count($rels1); $i++) {
               $temp .= "      if (e.value=='".trim($rels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "         document.getElementById('".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp .= "      if (e.value=='".trim($scts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "         document.getElementById('".$prefix."sxn".$scts1[$i]['fid2']."').style.display='';\n";
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp .= "      if (e.value=='".trim($nrels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "         jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').hide();\n";
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp .= "      if (e.value=='".trim($nscts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "         document.getElementById('".$prefix."sxn".$nscts1[$i]['fid2']."').style.display='none';\n";
            }
            $temp .= "   }\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;

            $javascript2 .= "}\n</script>\n";
         }
         $javascript .= "\"";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];         
         
         $style="";
         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }

         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = '';\n";
         $js .= "   var inputs = document.getElementsByName('w".$wd_id."a".$q['field_id']."');\n";
         //$js .= "   alert('inputs: ' + JSON.stringify(inputs));\n";
         $js .= "   for (var i=0;i<inputs.length;i++){\n";
         $js .= "      var e = inputs[i];\n";
         $js .= "      if (e.checked) {\n";
         $js .= "         temp = e.value;\n";
         //$js .= "         break;\n";
         $js .= "      }\n";
         $js .= "   }\n";
         
         if ($q['required']==1) {
            $js .= " if(!Boolean(temp)) {\n";
            $js .= "   rqderror = true;\n";
            $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
            $js .= " } else ";
         }
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
         //$js .= "     alert('temp: ' + temp);\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(jsfwebdata_convertstring(temp));\n";
         //$js .= "     alert('temp: ' + url);\n";
         $js .= "   }\n";
         
         $js .= "   }\n";
         
         $html .= $javascript2;
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         
         $qlength = strlen(strip_tags(trim(convertBack($questionText))));
         if($qlength>90 || 0==strcmp($q['field_type'],"POLLRADIO")){
            $class_wdq = "wdrow";
            $class_wda = "wdrow";
         }
         
         $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
         $html .= $questionText;
         if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         

         
         
         $bothnvp = separateStringBy(trim(convertBack($q['question'])),";");
         $across = $bothnvp[0];
         $names = separateStringBy($bothnvp[1],",");
         $values = separateStringBy($bothnvp[2],",");
         if ($bothnvp[1]==NULL && $bothnvp[2]==NULL) {
             $names = separateStringBy($bothnvp[0],",");
             $values = $names;
             $across = 1;
         } else if (!is_numeric($across)) {
             $across = 1;
             $values = $names;
             $names = separateStringBy($bothnvp[0],",");
         }
         
         
         $html .= "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
         if(0!=strcmp($q['field_type'],"POLLRADIO")){         
            for ($i=0; $i<count($names); $i++) {
               if (($i%$across)==0) $html .= "<TR>";
               $temp = trim($names[$i]);
               $temp2 = trim($values[$i]);
               if ($temp!=NULL && $temp2==NULL) $temp2=$temp;
               else if ($temp==NULL && $temp2!=NULL) $temp=$temp2;
               $selected = "";
               if (strcmp($answer,$temp2)==0) $selected="CHECKED";
               $html .= "<TD>";
               $html .= "<input class=\"".$prefix2."winput_radio\" type=\"radio\" name=\"w".$wd_id."a".$q['field_id']."\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$i."\"";
               $html .= " value=\"".$temp2."\" ".$selected.$javascript.">";
               if ($temp!=null && $glossary!=NULL) $temp = $glossary->flagAllTerms($temp,"#5691c4");
               $html .= $temp."</td>";
               if ((($i+1)%$across)==0 || (($i+1)==count($names))) $html .= "</TR>";
            }
         } else {
            $width = 290;
            $stats = $this->getStats($wd_id);
            $max = ceil($stats['max'] + (0.1 * $stats['max']));
            for ($i=0; $i<count($names); $i++) {               
               $temp = trim($names[$i]);
               $temp2 = trim($values[$i]);
               if ($temp!=NULL && $temp2==NULL) $temp2=$temp;
               else if ($temp==NULL && $temp2!=NULL) $temp=$temp2;
               $selected = "";
               if (strcmp($answer,$temp2)==0) $selected="CHECKED";
               $barwidth = ceil($width * ($stats['totals'][$q['field_id']][$temp2] / $max));
               
               $html .= "<TR>";
               $html .= "<TD>";
               $html .= "<input class=\"".$prefix2."winput_radio\" type=\"radio\" name=\"w".$wd_id."a".$q['field_id']."\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$i."\"";
               $html .= " value=\"".$temp2."\" ".$selected.$javascript.">";
               $html .= "</td>";
               $html .= "<TD>";
               $html .= "<div style=\"position:relative;width:".$width."px;height:18px;overflow:hidden;background-color:#333333;\">";
               $html .= "<div style=\"position:absolute;left:0px;top:0px;width:".$barwidth."px;height:18px;background-color:RED;\"></div>";
               $html .= "<div style=\"position:absolute;left:0px;top:0px;width:".$width."px;height:18px;font-size:14px;font-family:tahoma;color:#FFFFFF;\">".$temp."</div>";
               $html .= "<div style=\"position:absolute;right:5px;top:0px;width:".$width."px;height:18px;text-align:right;font-size:14px;font-family:tahoma;color:#FFFFFF;\">".$stats['totals'][$q['field_id']][$temp2]."</div>";
               $html .= "</div>";
               $html .= "</td>";
               $html .= "</TR>";
            }
         }
         $html .= "</table>";
         
         $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }
      
      function getdropdownoptions($q,$userid=NULL) {
         $names = array();
         $values = array();
         $descr = array();
         
         //print "\n\n<br><br>\n\n";
         //print_r($q);
         //print "\n\n<br><br>\n\n";

         if (strcmp($q['field_type'],"USERS")==0){
            $ua = new UserAcct();
            $usersA = $ua->getUsersForSegment(strtolower(trim($q['question'])));
            $users = $usersA['users'];
            for ($i=0; $i<count($users); $i++) {
               $user = $ua->getUser($users[$i]['userid']);
               $names[] = $user['userid'].". ".$user['fname']." ".$user['lname']." ".$user['company'];
               $descr[] = $user['userid'].". ".$user['fname']." ".$user['lname']." ".$user['company'];
               $values[] = $user['userid'];
            }
            
         } else if (strcmp($q['field_type'],"STATE")==0) {
            $states = getStateOptionList(TRUE);
            foreach($states as $n => $v){
               $names[] = $n;
               $descr[] = $n;
               $values[] = $v;
            }
            
         } else if (strcmp($q['field_type'],"FOREIGN")==0 || strcmp($q['field_type'],"FOREIGNCB")==0) {
            //defaultval tells us if we select all rows, or just userid rows - ignore if it's the userid indicator
            if(0==strcmp($value,"userid")) $value = "";
            $survey_info = separateStringBy(convertBack($q['question']),",");
            if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
               $paramName="w".$wd_id."a".$q['field_id'];
               $fldname = strtolower(trim($survey_info[1]));
               $wdname = strtolower(trim($survey_info[0]));
               $wdata = $this->getWebDataByName($wdname);
               $qs = $this->getFieldLabels($wdata['wd_id'],TRUE);
               //print "\n\n<br><br>\n\n";
               //print_r($qs);
               //print "\n\n<br><br>\n\n";
               $fldval = NULL;
               if(trim($survey_info[2])!=NULL) $fldval = $qs[strtolower(trim($survey_info[2]))];
               if($fldval==NULL) $fldval = "wd_row_id";
               $query = "SELECT * from wd_".$wdata['wd_id'];
               $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
               if (0==strcmp($q['defaultval'],"userid") && $userid!=NULL) $query .=" AND userid=".$userid;
               if ($qs['sequence'] != NULL) $query .=" ORDER BY ".$qs['sequence'];
               $dbi = new MYSQLAccess();
               $results = $dbi->queryGetResults($query);
               //print "\n\n<br><br>\n\n";
               //print_r($results);
               //print "\n\n<br><br>\n\n";
               for ($i=0; $i<count($results); $i++) {
                  $names[] = $results[$i][$qs[$fldname]];
                  $values[] = $results[$i][$fldval];
                  //print("<br>\n\nq: ".$qs['descr'].", ".$results[$i]['q4']);
                  if(isset($qs['descr']) && isset($results[$i][$qs['descr']])) $descr[] = $results[$i][$qs['descr']];
                  else $descr[] = $results[$i][$qs[$fldname]];
               }
            }
            //print "<br>\n<br>\n";
            //print_r($descr);
            //print "<br>\n<br>\n";
         } else if (0==strcmp($q['field_type'],"FOREIGNTDD")) {
            //defaultval tells us if we select all rows, or just userid rows - ignore if it's the userid indicator
            $survey_info = separateStringBy(convertBack($q['question']),",");
            if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
               $tbname = trim($survey_info[0]);
               $fldname = trim($survey_info[1]);
               $fldval = trim($survey_info[2]);
               $flddescr = trim($survey_info[3]);
               if($fldval==NULL) $fldval = $fldname;
               if($flddescr==NULL) $flddescr = $fldname;
               $dbi = new MYSQLAccess();
               $query = "SELECT * from ".$tbname;
               $results = $dbi->queryGetResults($query);
               for ($i=0; $i<count($results); $i++) {
                  $names[$i] = $results[$i][$fldname];
                  $values[$i] = $results[$i][$fldval];
                  $descr[$i] = $results[$i][$flddescr];
               }
            }
            
         } else if (strcmp($q['field_type'],"DROPDOWN")==0 || strcmp($q['field_type'],"CHECKBOX")==0 || strcmp($q['field_type'],"NEWCHKBX")==0 || strcmp($q['field_type'],"HRZCHKBX")==0 || strcmp($q['field_type'],"RADIO")==0) {
            $questionList = trim(convertBack($q['question']));
            $bothnvp = explode(";",$questionList);
            $narray = $bothnvp[0];
            $varray = $bothnvp[1];
            if(is_numeric($bothnvp[0])) {
               $narray = $bothnvp[1];
               $varray = $bothnvp[2];
            }
            if($varray==NULL) $varray = $narray;
            $names = explode(",",$narray);
            $values = explode(",",$varray);
            $descr = $names;
         }
         
         $resp = array();
         $resp['names'] = $names;
         $resp['values'] = $values;
         $resp['descr'] = $descr;
         //print "<br>\n<br>\n";
         //print_r($resp);
         //print "<br>\n<br>\n";
         return $resp;
      }

      function getDropdownJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         } else {
            //$html .= "\n<script>\n";
            //$html .= "alert('not using relationships');\n";
            //$html .= "</script>\n";
         }

         $javascript2 = "";
         $javascript = " onChange=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            $javascript .= $prefix."change".$wd_id.$q['field_id']."();";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
            
            //Hide all fields that this question has a QR, then unhide those that qualify
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$rels1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').show();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            //Hide all sections that this field refers to, then unhide qualified ones
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "   jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$scts1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').show();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "   jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').show();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$nrels1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').hide();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "   jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').show();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "   if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val()=='".$nscts1[$i]['f1value']."'){\n";
               $temp .= "      jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').hide();\n";
               $temp .= "   }\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            $javascript2 .= "}\n</script>\n";
         }
         $javascript .= "\"";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $style="";
         //$stylerow = "";
         
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";
         $labelinside = FALSE;
         if(FALSE!==strpos($stylerow,"display:none;")) {
            $stylerow = str_replace("display:none;","",$stylerow);
            $labelinside = TRUE;
         }         
         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if(strlen(strip_tags($q['label']))>22) {
            $class_wdq = "wdqrow";
            $class_wda = "wdarow";
         }         
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }

         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $value = trim(convertBack($answered['answer']));
         if ($value===NULL || 0==strcmp($value,"")) {
            $value=$q['defaultval'];
            $js .= "formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
         }
         
         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','1px solid #999999');\n";
         
         if ($q['required']==1) {
            $js .= " if(!Boolean(temp)) {\n";
            $js .= "   rqderror = true;\n";
            $js .= "   rqderrorstr = rqderrorstr + '[".$q['simplelabel']."] was left empty.';\n";
            $js .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').css('border','2px solid RED');\n";
            $js .= " } else ";
         }
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         //$js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
         $js .= "     if(!Boolean(temp)) temp='%E%';\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(temp);\n";
         $js .= "   }\n";
         
         $js .= "   }\n";
         
         
         
         $html .= $javascript2;
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         if(!$labelinside) {
            $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
            $html .= $questionText;
            if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
            $html .= "</div>";
         }
         $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
         $html .= "<select ";
         if($labelinside) $html .= "style=\"font-size:16px;color:#555555;\" ";
         $html .= "class=\"".$prefix2."winput_select\" name=\"w".$wd_id."a".$q['field_id']."\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" class=\"".$prefix2."winput_txt\"".$javascript.">";
         $html .= "<option value=\"\" id=\"w".$wd_id."a".$q['field_id']."_0\" style=\"color:#BBBBBB;font-style:italic;font-size:16px;\">";
         if($labelinside) $html .= $questionText;
         $html .= "</option>";

         $qopts = $this->getdropdownoptions($q,$userid);
         $names = $qopts['names'];
         $values = $qopts['values'];

         /*
         $names = array();
         $values = array();
         
         if (strcmp($q['field_type'],"USERS")==0){
            $ua = new UserAcct();
            $usersA = $ua->getUsersForSegment(strtolower(trim($q['question'])));
            $users = $usersA['users'];
            for ($i=0; $i<count($users); $i++) {
               $user = $ua->getUser($users[$i]['userid']);
               $names[] = $user['userid'].". ".$user['fname']." ".$user['lname']." ".$user['company'];
               $values[] = $user['userid'];
            }
            
         } else if (strcmp($q['field_type'],"STATE")==0) {
            $states = getStateOptionList(TRUE);
            foreach($states as $n => $v){
               $names[] = $n;
               $values[] = $v;
            }
            
         } else if (strcmp($q['field_type'],"FOREIGN")==0) {
            //defaultval tells us if we select all rows, or just userid rows - ignore if it's the userid indicator
            if(0==strcmp($value,"userid")) $value = "";
            $survey_info = separateStringBy(convertBack($q['question']),",");
            if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
               $paramName="w".$wd_id."a".$q['field_id'];
               $fldname = strtolower(trim($survey_info[1]));
               $wdname = strtolower(trim($survey_info[0]));
               $wdata = $this->getWebDataByName($wdname);
               $qs = $this->getFieldLabels($wdata['wd_id'],TRUE);
               $fldval = NULL;
               if(trim($survey_info[2])!=NULL) $fldval = $qs[strtolower(trim($survey_info[2]))];
               if($fldval==NULL) $fldval = "wd_row_id";
               $query = "SELECT * from wd_".$wdata['wd_id'];
               $query .= " WHERE dbmode<>'DELETED' AND dbmode<>'DUP'";
               if (0==strcmp($q['defaultval'],"userid") && $userid!=NULL) $query .=" AND userid=".$userid;
               if ($qs['sequence'] != NULL) $query .=" ORDER BY ".$qs['sequence'];
               $dbi = new MYSQLAccess();
               $results = $dbi->queryGetResults($query);
               for ($i=0; $i<count($results); $i++) {
                  $names[] = $results[$i][$qs[$fldname]];
                  $values[] = $results[$i][$fldval];
               }
            }
            
         } else if (0==strcmp($q['field_type'],"FOREIGNTDD")) {
            //defaultval tells us if we select all rows, or just userid rows - ignore if it's the userid indicator
            $survey_info = separateStringBy(convertBack($q['question']),",");
            if ($survey_info[0] != NULL && $survey_info[1] != NULL) {
               $tbname = trim($survey_info[0]);
               $fldname = trim($survey_info[1]);
               $fldval = trim($survey_info[2]);
               if($fldval==NULL) $fldval = $fldname;
               $dbi = new MYSQLAccess();
               $query = "SELECT * from ".$tbname;
               $results = $dbi->queryGetResults($query);
               for ($i=0; $i<count($results); $i++) {
                  $names[$i] = $results[$i][$fldname];
                  $values[$i] = $results[$i][$fldval];
               }
            }
            
         } else if (strcmp($q['field_type'],"DROPDOWN")==0) {
            $questionList = trim(convertBack($q['question']));
            $bothnvp = explode(";",$questionList);
            $names = explode(",",$bothnvp[0]);
            $values = explode(",",$bothnvp[1]);
         }
         */

         for ($a=0; $a<count($names); $a++) {
           if(trim($values[$a])==NULL) $values[$a] = $names[$a];
           $selected="";
           $ddl_value = trim($values[$a]);
           if ($ddl_value==NULL) $ddl_value = trim($names[$a]);
           if (strcmp(strtolower($value),strtolower(trim($values[$a])))==0 || strcmp(strtolower($value),strtolower(trim($names[$a])))==0) $selected="selected=\"selected\"";
           $html .= "<option style=\"font-size:16px;color:#222222;\" id=\"w".$wd_id."a".$q['field_id']."_".($a+1)."\" value=\"".$ddl_value."\" ".$selected.">".$names[$a]."</option>";
         }
         $html .= "</select>";

         $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      function getManyJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         if (class_exists("CustomWebsiteDataType")) {
            $cm = new CustomWebsiteDataType();
            $cm->getManyJSONHTML($wd_id,$q,$answered,$prefix,$printstuff,$tableformat,$explicitcss,$userelationships,$glossary);
         }

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      function getImageJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $html = "\n<!-- getImageJSONHTML() -->\n";
         $js = "";

         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $answers = convertBack(trim($answered['answer']));
         if ($answers==NULL || 0==strcmp($answers,"")) $answers=trim($q['defaultval']);

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if(strlen(strip_tags($q['label']))>22) {
            $class_wdq = "wdqrow";
            $class_wda = "wdarow";
         }
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }

         $js .= "   if (!rqderror) {\n";
         $js .= "   temp = jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val();\n";
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     if(!Boolean(temp)) temp='%%%EMPTY%%%';\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."=' + encodeURIComponent(temp);\n";
         $js .= "   }\n";
         
         $js .= "   }\n";
         

         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
         $html .= $questionText;
         if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
         $html .= "</div>";
         $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         
         $html .= "<input type=\"hidden\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" value=\"".$answers."\">";
         $html .= "\n<script>\n";
         $html .= "function ".$prefix."wda_".$q['field_id']."imgdel(){\n";
         $html .= "   if(confirm('Do you want to permanently delete this image?')) {\n";
         $html .= "      formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
         $html .= "      jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val('%%%EMPTY%%%');\n";
         $html .= "      jQuery('#".$prefix."wda_".$q['field_id']."img').html('');\n";
         $html .= "   }\n";
         $html .= "}\n";
         $html .= "</script>\n";
         $html .= "<div id=\"".$prefix."wda_".$q['field_id']."img\" style=\"position:relative;padding:5px;\">";
         $html .= "</div>";         

         $html .= "\n<script>\n";
         $html .= "function jsfwdimg_".$prefix."w".$wd_id."a".$q['field_id']."(fn) {\n";
         $html .= "   jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').val(fn);\n";
         $html .= "   var str = '';\n";
         $html .= "   str += '<div style=\\\"margin-top:5px;margin-bottom:8px;\\\">';\n";
         $html .= "   str += '<a href=\\\"' + fn + '\\\" target=\\\"_new\\\">';\n";
         $html .= "   var show = '<img src=\"' + fn + '\" style=\"max-width:200px;max-height:120px;width:auto;height:auto;\" border=\"0\">';\n";
         $html .= "   if(fn.toLowerCase().endsWith('doc')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_doc.jpg\\\"> word doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('docx')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_doc.jpg\\\"> word doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('rtf')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_doc.jpg\\\"> word doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('html')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_html.jpg\\\"> HTML doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('htm')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_html.jpg\\\"> HTML doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('js')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_html.jpg\\\"> Web doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('css')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_html.jpg\\\"> Web doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('php')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_html.jpg\\\"> Web doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('pdf')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_pdf.jpg\\\"> pdf doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('txt')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_txt.jpg\\\"> text doc uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('zip')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_zip.jpg\\\"> zip file uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('ppt')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_ppt.jpg\\\"> Powerpoint file uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('pptx')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_ppt.jpg\\\"> Powerpoint file uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('xlsx')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_xl.jpg\\\"> Spreadsheet uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('xls')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_xl.jpg\\\"> Spreadsheet uploaded';\n";
         $html .= "   else if(fn.toLowerCase().endsWith('csv')) show = '<img src=\\\"' + defaultremotedomain + 'jsfimages/icon_xl.jpg\\\"> Spreadsheet uploaded';\n";
         $html .= "   str += show;\n";
         $html .= "   str += '</a>';\n";
         $html .= "   str += '<div onclick=\\\"".$prefix."wda_".$q['field_id']."imgdel();\\\" style=\\\"position:absolute;cursor:pointer;left:1px;top:1px;width:20px;height:20px;overflow:hidden;z-index:100;border:1px solid #777777;border-radius:10px;background-color:#FFFFFF;opacity:0.8;\\\">';\n";
         $html .= "   str += '<div style=\\\"position:relative;width:20px;height:14px;margin-top:2px;font-family:arial;color:#AA3333;text-align:center;\\\">';\n";
         $html .= "   str += 'x</div>';\n";
         $html .= "   str += '</div>';\n";
         $html .= "   str += '</div>';\n";
         $html .= "   jQuery('#".$prefix."wda_".$q['field_id']."img').html(str);\n";
         $html .= "}\n";
         if ($answers!=NULL) {
            $html .= "jsfwdimg_".$prefix."w".$wd_id."a".$q['field_id']."('".$answers."');\n";
         }
         $html .= "</script>\n";

         $html .= "<div ";
         $html .= "style=\"font-size:12px;font-weight:normal;color:#000000;cursor:pointer;background-color:#EEEEEE;padding:4px;border:1px solid #000000;border-radius:4px;margin-right:3px;margin-top:2px;margin-bottom:1px;text-align:center;max-width:140px;\" ";
         $html .= "id=\"imgupl_".$prefix."w".$wd_id."q".$q['field_id']."\" ";
         $html .= "onclick=\"formchange_".$prefix."('".$prefix.$q['field_id']."');window.open(defaultremotedomain + '".$GLOBALS['codeFolder']."uploadimage.php?userid=9&token=9&prefix=".$prefix."&wd_id=".$wd_id."&field_id=".$q['field_id']."');\"";
         $html .= ">Upload</div>";
         
         $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = "";
         $returnobj['relationshipjs2'] = "";
         return $returnobj;
      }

      // formatting is: X;Names;Values
      // X = Number of checkboxes across
      // Names = CSV of display values for each checkbox
      // Values = CSV of corresponding value stored in the DB 
      function getNewCheckboxJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         //$html = "\n<!-- getNewCheckboxJSONHTML() -->\n";
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         }
         $javascript2 = "";
         $javascript = " onClick=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         //if ($rels1!=NULL && count($rels1)>0) {
         //if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0)) {
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            $javascript .= $prefix."change".$wd_id.$q['field_id']."();";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
            //$javascript2 .= "alert('in checkbox change.');\n";
            $temp = "var inputs = document.getElementsByName('w".$wd_id."a".$q['field_id']."[]');\n";
            //$temp .= "alert('inputs: ' + JSON.stringify(inputs));\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;
            
            $rem_flds = array();
            $rem_nflds = array();
            
            // Initialize fields/sections to be shown/hidden
            for ($i=0; $i<count($rels1); $i++) {
               //$relationshipjs1 .= "document.getElementById('".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
               if(!in_array($rels1[$i]['fid2'],$rem_flds)) {
                  $rem_flds[] = $rels1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').hide();\n";
                  $relationshipjs1 .= $temp;
                  $javascript2 .= $temp;
               }
            }
            for ($i=0; $i<count($scts1); $i++) {
               //$relationshipjs1 .= "document.getElementById('".$prefix."sxn".$scts1[$i]['fid2']."').style.display='none';\n";
               if(!in_array($scts1[$i]['fid2'],$rem_flds)) {
                  $rem_flds[] = $scts1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').hide();\n";
                  $relationshipjs1 .= $temp;
                  $javascript2 .= $temp;
               }
            }
            for ($i=0; $i<count($nrels1); $i++) {
               //$relationshipjs1 .= "document.getElementById('".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').style.display='';\n";
               if(!in_array($nrels1[$i]['fid2'],$rem_nflds)) {
                  $rem_nflds[] = $nrels1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').show();\n";
                  $relationshipjs1 .= $temp;
                  $javascript2 .= $temp;
               }
            }
            for ($i=0; $i<count($nscts1); $i++) {
               //$relationshipjs1 .= "document.getElementById('".$prefix."sxn".$nscts1[$i]['fid2']."').style.display='';\n";
               if(!in_array($nscts1[$i]['fid2'],$rem_nflds)) {
                  $rem_nflds[] = $nscts1[$i]['fid2'];
                  $temp = "jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').show();\n";
                  $relationshipjs1 .= $temp;
                  $javascript2 .= $temp;
               }
            }
            
            // determine what to do with fields/sections
            $temp = "   for (var i=0;i<inputs.length;i++){\n";
            $temp .= "      var e = inputs[i];\n";
            for ($i=0; $i<count($rels1); $i++) {
               $temp .= "      if (e.value=='".trim($rels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "        jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').show();\n";
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp .= "      if (e.value=='".trim($scts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "        jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').show();\n";
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp .= "      if (e.value=='".trim($nrels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "        jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').hide();\n";
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp .= "      if (e.value=='".trim($nscts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "        jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').hide();\n";
            }
            $temp .= "   }\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;
            //$javascript2 .= "alert('end checkbox change.');\n";
            $javascript2 .= "}\n</script>\n";
         }
         $javascript .= "\"";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $style="";
         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdqrow";
         $class_wda = "wdarow";
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }
         
         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $html .= $javascript2;
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         if($fordisplay) $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\" class=\"".$prefix2.$class_wdq."\"".$styleq.">";
         if($fordisplay) $html .= $questionText;
         if ($q['required']==1 && $fordisplay) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
         if($fordisplay) $html .= "</div>";
         if($fordisplay) $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\" class=\"".$prefix2.$class_wda."\"".$stylea.">";         
         if($fordisplay) $html .= "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" class=\"".$prefix2.$class_wda."\"".$stylea.">";
         
         $answerText = convertBack(trim($answered['answer']));
         
         if(0==strcmp(substr($q['defaultval'],0,1),"q") && is_numeric(substr($q['defaultval'],1))){
            $temp = $this->getAnswer($wd_id,$answered['row']['wd_row_id'], $q['defaultval']);
            $answerText .= ",".convertBack(trim($temp['answer']));
         } else if ($answerText==NULL) {
            $answerText=$q['defaultval'];
         }

         $answers = separateStringBy($answerText,",",NULL,TRUE);            
         $bothnvp = separateStringBy(trim(convertBack($q['question'])),";");
         $across = $bothnvp[0];
         $names = separateStringBy($bothnvp[1],",");
         $values = separateStringBy($bothnvp[2],",");
         if (0==strcmp($q['field_type'],"FOREIGNCB")) {
            $tblparams = separateStringBy($across,",");
            $tu = NULL;
            if(0==strcmp($q['defaultval'],"userid")) $tu = $userid;
            $opts = $this->getSurveyOptionsOnly($tblparams[0],$tblparams[1],$tblparams[2],$tblparams[4],$tu);
            $names = array();
            $values = array();
            foreach($opts as $key => $val) {
               $names[] = $key;
               $values[] = $val;
            }
            $across = 3;
            if($tblparams[3]!=NULL && is_numeric($tblparams[3])) $across = $tblparams[3];
         } else if (0==strcmp($q['field_type'],"FOREIGNTBL")) {
            $tblparams = separateStringBy($across,",");
            $opts = $this->getTableOptions($tblparams[0],$tblparams[1],$tblparams[2],$tblparams[4]);
            $names = array();
            $values = array();
            foreach($opts as $key => $val) {
               $names[] = $key;
               $values[] = $val;
            }
            $across = 3;
            if($tblparams[3]!=NULL && is_numeric($tblparams[3])) $across = $tblparams[3];
         } else if ($bothnvp[1]==NULL && $bothnvp[2]==NULL) {
             $names = separateStringBy($bothnvp[0],",");
             $values = $names;
             $across = 1;
         } else if (!is_numeric($across)) {
             $across = 1;
             $values = $names;
             $names = separateStringBy($bothnvp[0],",");
         }
         
         $countcols = count($names);
         if(count($values)>$countcols) $countcols = count($values);
         
         $js .= "";
         $js .= "   if (!rqderror) {\n";
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     url = url + '&w".$wd_id."m".$q['field_id']."=1';\n";
         //$js .= "     url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent('%%%EMPTY%%%');\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent('%E%');\n";
         
         for ($i=0; $i<$countcols; $i++) {
            $n = trim($names[$i]);
            $v = trim($values[$i]);
            if ($n==NULL) $n = $v;
            else if ($v==NULL) $v = $n;

            $rowAns = "";
            for($j=0;$j<count($answers);$j++){
               if (0==strcmp($v,trim($answers[$j])) || 0==strcmp($n,trim($answers[$j]))) {
                  $rowAns = "CHECKED";
                  break;
               }
            }
         
         
            if (($i%$across)==0 && $fordisplay) $html .= "<TR>";
            if($fordisplay) $html .= "<TD>";
            $js .= "if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."_".$i."').prop('checked')) url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent(jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."_".$i."').val());\n";
            $html .= "<input class=\"".$prefix2."winput_check\" type=\"checkbox\" name=\"w".$wd_id."a".$q['field_id']."[]\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."_".$i."\"";
            $html .= " value=\"".$v."\" ".$rowAns." ".$javascript.">";
            if ($n!=null && $glossary!=NULL && $fordisplay) $n = $glossary->flagAllTerms($n,"#5691c4");
            if($fordisplay) $html .= $n."</td>";
            if ((($i+1)%$across)==0 && $fordisplay) $html .= "</TR>";
         }
         $js .= "    }\n";
         $js .= "  }\n";
         
         
         if (($countcols%$across)>0 && $fordisplay) $html .= "</TR>";
         if($fordisplay) $html .= "</table>";
         $html .= "<input type=\"hidden\" name=\"w".$wd_id."m".$q['field_id']."\" value=\"1\" id=\"w".$wd_id."m".$q['field_id']."\">";

         if($fordisplay) $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

      
      function getRegionJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         //$html = "\n<!-- getNewCheckboxJSONHTML() -->\n";
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";
         
         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         }
         $javascript = $prefix."change".$wd_id.$q['field_id']."();";
         $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
         $javascript2 .= "formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            $temp = "var inputs = document.getElementsByName('w".$wd_id."a".$q['field_id']."[]');\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;
            
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').hide();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').show();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').show();\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            
            $temp = "   for (var i=0;i<inputs.length;i++){\n";
            $temp .= "      var e = inputs[i];\n";
            for ($i=0; $i<count($rels1); $i++) {
               $temp .= "      if (e.value=='".trim($rels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "jQuery('#".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').show();\n";
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp .= "      if (e.value=='".trim($scts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "jQuery('#".$prefix."sxn".$scts1[$i]['fid2']."').show();\n";
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp .= "      if (e.value=='".trim($nrels1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "jQuery('#".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').hide();\n";
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp .= "      if (e.value=='".trim($nscts1[$i]['f1value'])."' && e.checked)\n";
               $temp .= "jQuery('#".$prefix."sxn".$nscts1[$i]['fid2']."').hide();\n";
            }
            $temp .= "   }\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;
            
         }
         $javascript2 .= "}\n</script>\n";

         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         
         // Get label ready (glossary pass)
         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         
         
         // Get answer from row, another question, or default
         $answerText = convertBack(trim($answered['answer']));
         if(0==strcmp(substr($q['defaultval'],0,1),"q") && is_numeric(substr($q['defaultval'],1))){
            $temp = $this->getAnswer($wd_id,$answered['row']['wd_row_id'], $q['defaultval']);
            $answerText .= ",".convertBack(trim($temp['answer']));
         } else if ($answerText==NULL) {
            $answerText=convertBack(trim($q['defaultval']));
         }

         // create javascript to capture the anwers for this field
         $js .= "   if (!rqderror) {\n";
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     url = url + '&w".$wd_id."m".$q['field_id']."=1';\n";
         //$js .= "     url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent('%%%EMPTY%%%');\n";
         $js .= "     url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent('%E%');\n";
         $js .= "     var inputs = document.getElementsByName('w".$wd_id."a".$q['field_id']."[]');\n";
         $js .= "     for (var i=0;i<inputs.length;i++){\n";
         $js .= "       var e = inputs[i];\n";
         $js .= "       if (e.checked) {\n";
         $js .= "         url = url + '&w".$wd_id."a".$q['field_id']."[]=' + encodeURIComponent(e.value);\n";
         $js .= "       }\n";
         $js .= "     }\n";
         $js .= "   }\n";
         $js .= "   }\n";
         
         // start by putting a relationship checker function in html
         $html .= $javascript2;
         
         // Regular labels, required marker, etc
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\">";
         if($fordisplay) $html .= "<div id=\"".$prefix."wdq_".$q['field_id']."\">";
         if($fordisplay) $html .= $questionText;
         if ($q['required']==1 && $fordisplay) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
         if($fordisplay) $html .= "</div>";
         if($fordisplay) $html .= "<div id=\"".$prefix."wda_".$q['field_id']."\">";
         $html .= $this->getRegionSelectionBoxes($answerText,"w".$wd_id."a".$q['field_id']."[]",$javascript);
         $html .= "<input type=\"hidden\" name=\"w".$wd_id."m".$q['field_id']."\" value=\"1\" id=\"w".$wd_id."m".$q['field_id']."\">";
         if($fordisplay) $html .= "</div>";
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }
      
      
      // formatting is: X;Names;Values
      // X = Number of checkboxes across
      // Names = CSV of display values for each checkbox
      // Values = CSV of corresponding value stored in the DB 
      function getSingleCheckboxJSONHTML($wd_id,$q,$answered,$prefix,$printstuff=FALSE,$tableformat=FALSE,$explicitcss=0,$userelationships=TRUE,$glossary=NULL,$userid=NULL,$fordisplay=TRUE){
         $html = "";
         $js = "";
         $relationshipjs1 = "";
         $relationshipjs2 = "";

         $rels1 = NULL;
         $scts1 = NULL;
         $nrels1 = NULL;
         $nscts1 = NULL;
         if ($userelationships) {
            $rels1 = $this->getField1Rel($wd_id,$q['field_id']);
            $scts1 = $this->getSectionRel($wd_id,$q['field_id']);
            $nrels1 = $this->getNegativeField1Rel($wd_id,$q['field_id']);
            $nscts1 = $this->getNegativeSectionRel($wd_id,$q['field_id']);
         }

         $javascript2 = "";
         $javascript = " onClick=\"formchange_".$prefix."('".$prefix.$q['field_id']."');";
         //if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0)) {
         if (($rels1!=NULL && count($rels1)>0) || ($scts1!=NULL && count($scts1)>0) || ($nrels1!=NULL && count($nrels1)>0) || ($nscts1!=NULL && count($nscts1)>0)) {
            //***chj103*** 190705 $javascript .= $prefix."change".$wd_id.$q['field_id']."();";
            $javascript .= "jsfwdCheckWDRelationships();";
            $javascript2 = "\n<script type=\"text/javascript\">\nfunction ".$prefix."change".$wd_id.$q['field_id']."(){\n";
            $temp = "var val='NO';\nif (document.getElementById('".$prefix."inputw".$wd_id."a".$q['field_id']."').checked) val='YES';\n";
            $javascript2 .= $temp;
            $relationshipjs2 .= $temp;
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "document.getElementById('".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').style.display='none';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($rels1); $i++) {
               $temp = "   if(val=='".trim($rels1[$i]['f1value'])."') document.getElementById('".$prefix."w".$wd_id."a".$rels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "document.getElementById('".$prefix."sxn".$scts1[$i]['fid2']."').style.display='none';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($scts1); $i++) {
               $temp = "   if(val=='".trim($scts1[$i]['f1value'])."') document.getElementById('".$prefix."sxn".$scts1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "document.getElementById('".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nrels1); $i++) {
               $temp = "   if(val=='".trim($nrels1[$i]['f1value'])."') document.getElementById('".$prefix."w".$wd_id."a".$nrels1[$i]['fid2']."').style.display='none';\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "document.getElementById('".$prefix."sxn".$nscts1[$i]['fid2']."').style.display='';\n";
               $javascript2 .= $temp;
               $relationshipjs1 .= $temp;
            }
            for ($i=0; $i<count($nscts1); $i++) {
               $temp = "   if(val=='".trim($nscts1[$i]['f1value'])."') document.getElementById('".$prefix."sxn".$nscts1[$i]['fid2']."').style.display='none';\n";
               $javascript2 .= $temp;
               $relationshipjs2 .= $temp;
            }
            $javascript2 .= "}\n</script>\n";
         }
         $javascript .= "\"";


         $prefix2 = $prefix;
         $origprefix_a = separateStringBy($prefix,"_");
         if($origprefix_a[0]!=NULL) $prefix2 = $origprefix_a[0];

         $style="";
         //$stylerow = "";
         $stylerow = " style=\"clear:both;".$q['stylecss']."\"";         
         $styleq = "";
         $stylea = "";
         if ($explicitcss==1) $stylerow = " style=\"clear:both;padding:3px;font-size:14px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:5px;\"";
         if ($explicitcss==1) $stylea = " style=\"float:left;width:240px;font-size:12px;font-family:arial;color:#404040;\"";
         if ($explicitcss==1) $styleq = " style=\"float:left;width:170px;font-size:12px;font-family:arial;color:#404040;\"";
         $class_wdrow = "wdrow";
         $class_wdq = "wdq";
         $class_wda = "wda";
         if ($tableformat) {
            $class_wdrow = "wdcell";
            $class_wdq = "wdcellq";
            $class_wda = "wdcella";
            if ($explicitcss==1) $stylerow = " style=\"float:left;font-size:12px;color:#404040;font-weight:normal;font-family:arial;margin-bottom:3px;\"";
            if ($explicitcss==1) $stylea = " style=\"float:left;width:150px;font-size:12px;font-family:arial;color:#404040;\"";
            if ($explicitcss==1) $styleq = " style=\"float:left;width:100px;font-size:12px;font-family:arial;color:#404040;\"";
         }

         $questionText = $q['label'];
         if ($questionText!=null && $glossary!=NULL) $questionText = $glossary->flagAllTerms($questionText,"#5691c4");
         $answer = convertBack(trim($answered['answer']));
         if (trim($answer)==NULL && trim($q['defaultval'])!=NULL) {
            $answer=trim($q['defaultval']);
            $js .= "formchange_".$prefix."('".$prefix.$q['field_id']."');\n";
         }

         $html .= $javascript2;
         $html .= "<div id=\"".$prefix."w".$wd_id."a".$q['field_id']."\" class=\"".$prefix2.$class_wdrow."\"".$stylerow.">";
         
         
         //$js .= "if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').prop('checked')) url = url + '&w".$wd_id."a".$q['field_id']."=YES';\n";
         //$js .= "else url = url + '&w".$wd_id."a".$q['field_id']."=NO';\n";
         
         $js .= "   if (!rqderror) {\n";
         $js .= "   if (Boolean(".$prefix."chgflds) && Boolean(".$prefix."chgflds['".$prefix.$q['field_id']."'])) {\n";
         $js .= "     if(url.length>".$this->getMaxAJAX().") {\n";
         $js .= "       c_urls.push(url + '&chj=' + c_urls.length);\n";
         $js .= "       url = c_url;\n";
         $js .= "     }\n";
         $js .= "     if (jQuery('#".$prefix."inputw".$wd_id."a".$q['field_id']."').prop('checked')) url = url + '&w".$wd_id."a".$q['field_id']."=YES';\n";
         $js .= "     else url = url + '&w".$wd_id."a".$q['field_id']."=NO';\n";
         $js .= "   }\n";
         $js .= "   }\n";

         
         
         $selected = "";
         if ($answer!=NULL && 0==strcmp($answer,"YES"))  $selected="CHECKED";

         if($q['disa']==1) {
            if(0==strcmp($selected,"CHECKED")) {
               $html .= "<span  class=\"".$prefix2."winput_check\">&#10003;".$questionText."</span>";
               $html .= "<input type=\"hidden\" name=\"w".$wd_id."a".$q['field_id']."\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\" value=\"YES\">";
            }
         } else {
            $html .= "<input class=\"".$prefix2."winput_check\" type=\"checkbox\" name=\"w".$wd_id."a".$q['field_id']."\" id=\"".$prefix."inputw".$wd_id."a".$q['field_id']."\"";
            $html .= " value=\"YES\" ".$selected." ".$javascript.">";
            if ($q['required']==1) $html .= " <span style=\"color:RED;font-weight:bold;\">*</span>";
            $html .= "<span  class=\"".$prefix2."winput_check\">".$questionText."</span>";
         }
         $html .= "</div>";

         $returnobj = array();
         $returnobj['html'] = $html;
         if($fordisplay) $returnobj['js'] = $js;
         else $returnobj['js'] = "";
         $returnobj['relationshipjs1'] = $relationshipjs1;
         $returnobj['relationshipjs2'] = $relationshipjs2;
         return $returnobj;
      }

}





Class LoadWebData {
   function doWork($job){
   	if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
      
      $filename = $job['content'];
      //$answer = unserialize($job['phpobj']);
      //if ($answer==NULL) $answer=array();

      $sql = new MYSQLaccess();
      $wd = new WebsiteData();

      if (($handle = fopen($filename, "r")) === FALSE) {
         $job['status'] = "ERROR";
      } else {
         $header = fgets($handle,4096);
         while(strpos($header,"\n")===FALSE && !feof($handle)) $header .= fgets($handle,4096);
         
         if ($job['printstuff']) print "\n<br>------------------------------------\n<br>header: ".$header."\n<br>------------------------------------<br><br>\n";
         $header = csvRemoveQuotes($header);
         $headers = separateStringBy($header,",");
         $indexTable = array();
         for ($i=0; $i<count($headers); $i++) {
            $t = removeSpecialChars(strtolower(trim($headers[$i])));
            $t_orig = $t;
            if($t!=NULL) {
               $i2 = 1;
               while(isset($indexTable[$t])) {
                  $t = $i2."_".$t_orig;
                  $i2++;
               }
               if ($job['printstuff']) print "<br>\n *****Header ".$i.": ".$t;
               $indexTable[$t] = $i;
            }
         }

         if ($job['printstuff']) {
            print "\n<br>\n<br>-------------------Index Table------------------------";
            print "\n<br>index table: <br>";
            print_r($indexTable);
            print "\n<hr><br>";
         }

         if ($job['field2']==NULL || $job['field2']<ftell($handle)) $job['field2'] = ftell($handle);
         fseek($handle,$job['field2']);
         
         $qs = $wd->getFieldsIndexed($job['cmsid']);

         $totalLines = 0;
         $doneWithFile = FALSE;
         //while ($totalLines<300 && !$doneWithFile) {
         while ($totalLines<20 && !$doneWithFile) {
            $skip = FALSE;
            $update = FALSE;
            
            $line = fgets($handle,4096);
            while(strpos($line,"\n")===FALSE && !feof($handle)) $line .= fgets($handle,4096);
            
            $totalLines++;
            $job['field1']++;
            $job['field2'] = ftell($handle);
            if (feof($handle)) $doneWithFile = TRUE;
            
            if ($job['printstuff']) print "\n\n<br><br>---------LINE BEFORE CONVERSION-------\n<br>".$line."\n<br>----------------------------------\n\n<br><br>";
            
            $line = csvRemoveQuotes($line);

            if ($job['printstuff']) print "\n\n<br><br>---------LINE AFTER CONVERSION-------\n<br>".$line."\n<br>----------------------------------\n\n<br><br>";
            
            $fields = separateStringBy($line,",");
            
            if ($job['printstuff']) {
               for($i=0;$i<count($fields);$i++){
                  print "\n<br>*******record field ".$i.": ".$fields[$i];
               }
            }
            $wd_row_id=$fields[$indexTable['wd_row_id']];
            
            if($wd_row_id==NULL || $wd_row_id<1) {
               $userid=$fields[$indexTable['userid']];
               $wd_row_id = $wd->addRow($job['cmsid'],$userid,NULL,"CSV_UPLOAD");
            }
            
            $query = "UPDATE wd_".$job['cmsid']." SET lastupdateby=SUBSTR(CONCAT('CSV ".date("Y-m-d H:i:s").", ',IFNULL(lastupdateby,' ')),1,2048), lastupdate=NOW()";
            
            //Go through questions to see if you can find specific ones.
            $used_h = array();
            foreach($qs as $key => $q){
               $questionText = convertBack($q['label']);
               
               if(0==strcmp($q['field_type'],"TABLE")) {
                  $foundq = FALSE;
                  $seta = "";
                  $temp = separateStringBy($questionText,";");
                  if (!isset($temp[1]) && $job['printstuff']) print "\n<br>Table question without any rows: ".$q['field_id'];
                  $hs = array();
                  $rows = array();
                  
                  if(isset($temp[0])) $hs = separateStringBy($temp[0],",");
                  if(isset($temp[1])) $rows = separateStringBy($temp[1],",");
      
                  for ($n=0; $n<count($rows); $n++) {
                     for ($m=1; $m<count($hs); $m++) {
                        $indx = removeSpecialChars(strtolower(trim($hs[$m].":".$rows[$n])));
                        if(!isset($indexTable[$indx])) {
                           $indx = removeSpecialChars(strtolower(trim($q['field_id'].":".$hs[$m].":".$rows[$n])));
                        }
                        
                        $t_orig = $indx;
                        $i2 = 1;
                        while(isset($used_h[$indx])) {
                           $indx = $i2."_".$t_orig;
                           $i2++;
                        }
                        $used_h[$indx] = TRUE;
                        if ($job['printstuff']) print "<br>\n ******Used (".$indexTable[$indx]."): ".$indx." [".$fields[$indexTable[$indx]]."]";
                        if(isset($indexTable[$indx])) $foundq=TRUE;
                        $seta .= convertString($fields[$indexTable[$indx]]).",";
                     }
                  }
                  if($foundq) $query .= ", ".$q['field_id']."='".$seta."'";
                  if ($job['printstuff'] && $foundq) print "\n<br>******Found Table update: <b>".$q['field_id']."='".$seta."'</b>\n<br>";
               } else if(0==strcmp($q['field_type'],"NEWCHKBX")) {
                  if ($job['printstuff']) print "\n<br>***chj***New Checkboxes: <b>".$q['field_id']."  :::  ".$q['label']."   :::   ".$q['question']."</b>\n<br>";
                  
                  $foundq = FALSE;
                  $questionStr = convertBack($q['question']);
                  $seta = "";
                  $temp = separateStringBy($questionStr,",");
                  for ($n=0; $n<count($temp); $n++) {
                     $indx = removeSpecialChars(strtolower(trim($questionText.":".$temp[$n])));
                     if(isset($indexTable[$indx]) && 0==strcmp(strtolower(trim($fields[$indexTable[$indx]])),"yes")) {
                        $seta .= $temp[$n].",";
                        $foundq=TRUE;
                     }
                     $indx = removeSpecialChars(strtolower(trim($q['field_id'].":".$questionText.":".$temp[$n])));
                     if(isset($indexTable[$indx]) && 0==strcmp(strtolower(trim($fields[$indexTable[$indx]])),"yes")) {
                        $seta .= $temp[$n].",";
                        $foundq=TRUE;
                     }
                  }
                  if($foundq) $query .= ", ".$q['field_id']."='".convertString($seta)."'";
                  if ($job['printstuff'] && $foundq) print "\n<br>******Found NewCheckbox update: <b>".$q['field_id']."='".$seta."'</b>\n<br>";
               } else {
                  if(isset($indexTable[removeSpecialChars(strtolower(trim($questionText)))])) $query .= ", ".$q['field_id']."='".convertString($fields[$indexTable[removeSpecialChars(strtolower(trim($questionText)))]])."'";
                  else if(isset($indexTable[removeSpecialChars(strtolower(trim($q['map'])))])) $query .= ", ".$q['field_id']."='".convertString($fields[$indexTable[removeSpecialChars(strtolower(trim($q['map'])))]])."'";
                  else if(isset($indexTable[removeSpecialChars(strtolower(trim($q['field_id'].":".$questionText)))])) $query .= ", ".$q['field_id']."='".convertString($fields[$indexTable[removeSpecialChars(strtolower(trim($q['field_id'].":".$questionText)))]])."'";
                  //else if(isset($indexTable[removeSpecialChars(strtolower(trim($q['field_id'])))])) $query .= ", ".$q['field_id']."='".convertString($fields[$indexTable[removeSpecialChars(strtolower(trim($q['field_id'])))]])."'";
               }
               
               if(isset($indexTable[$q['field_id']])) $query .= ", ".$q['field_id']."='".convertString($fields[$indexTable[$q['field_id']]])."'";
            }
            
            if ($job['printstuff']) {
               print "\n<br>\n<br>---------------------ROW Used Indices----------------------";
               print "\n<br>used: <br>";
               print_r($used_h);
               print "\n<hr><br>";
            }
            
            
            if(isset($indexTable["dbmode"])) $query .= ", dbmode='".$fields[$indexTable["dbmode"]]."'"; 
            
            //for ($i=0; $i<count($headers); $i++) {
            //   $t = strtolower(trim($headers[$i]));
            //   if($t!=NULL && 0!=strcmp($t,"wd_row_id")) $query .= ", ".$t."='".convertString($fields[$i])."'";
            //}
            $query .= " WHERE wd_row_id=".$wd_row_id.";";
            $sql->update($query);
            if ($job['printstuff']) print "\n<br>update query: ".$query;

         }
         //$job['phpobj'] = mysql_escape_string(serialize($answer));
         if ($doneWithFile) $job['status'] = "FINISHED";
         else $job['status'] = "NEW";
      }
      return $job;
   }

   function startjob($wd_id,$fn) {
      $subj = "Uploading JData CSV: ".date("m/d/Y H:i:s");
      $sched = new Scheduler();
      $sched->addSchedCustom("LoadWebData",$subj,4,NULL,$fn,0,0,NULL,NULL,NULL,NULL,NULL,$wd_id);
   }
   
   function copyJob($copyid=NULL) {
      $jobresults = FALSE;
      if($copyid!=NULL){
         $dbLink = new MYSQLaccess;         
         $query = "SELECT * FROM schedemail WHERE semailid=".$copyid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            $sched = new Scheduler();
            $sched->addSchedCustom("LoadWebData",$results[0]['subject'],4,NULL,$results[0]['content'],0,0,NULL,NULL,NULL,NULL,NULL,$results[0]['cmsid']);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
   
}



Class DownloadWebData {
   function doWork($job){
   	if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
      $hdr_postfix = "";
      $wd_id = $job['cmsid'];
      $qids = unserialize($job['phpobj']);
      
      if($job['content']==NULL) $job['content'] = "jdata_".$wd_id."_".date("YmdHis").".csv";
      $filename = $job['content'];
      
      // field1 represents the number of processed rows (eventually this will represent page for getRows())
      // field2 represents the amount per page/iteration (limit)
      if($job['field1']==NULL) $job['field1']=0;
      
      if($job['printstuff']) $job['field2'] = 100;
      else if($job['field2']==NULL || $job['field2']<50) $job['field2']=100;
      
      $job['field3'] = "Last ran: ".date("m/d/Y H:i:s");
      
      $sql = new MYSQLaccess();
      $wd = new WebsiteData();
      $ua = new UserAcct();
      
      // get header info.  need this to see if user/org data needed
      $webdata = $wd->getWebData($wd_id);
      $orgwd = NULL;
      $orgqs = NULL;
      $ty = "org";
      if($webdata['privatesrvy']==1) {
         if(trim($webdata['usertype'])!=NULL) $ty = trim($webdata['usertype']);
         $orgwd = $wd->getWebData($ty." Properties");
         $orgqs = $wd->getHeaderFields($orgwd['wd_id']);
      } else if(trim($webdata['usertype'])!=NULL) {
         $ty = trim($webdata['usertype']);
         $orgwd = $wd->getWebData($ty." Properties");
         $orgqs = $wd->getHeaderFields($orgwd['wd_id']);
      }
      
      //$qs = $wd->getFieldsIndexed($wd_id);
      $fields = $wd->getAllFieldsSystem($wd_id);
      $fields_ordered = array();
      
      // eventually, we'd like to use getrows() function - for now, there's no way to pass paramters.  NOTE: field1 would need to be converted to page number      
      //$wd->getRows($wd_id,"d.wd_row_id",$job['field2'], $filterStr=null,FALSE, $userid=NULL,FALSE,FALSE, $subforeignfields=FALSE,FALSE,FALSE,$qids,$job['field1']);

      $query = "SELECT ";
      if($qids==NULL || count($qids)<1){
         $query .= "*";
         $fields_ordered = $fields;
      } else {
         $query .= implode($qids,",");
         $query .= ",wd_row_id,userid,created,lastupdate,lastupdateby,comments";

         for($i=0;$i<count($fields);$i++){
            for($j=0;$j<count($qids);$j++){
               if(0==strcmp($fields[$i]['field_id'],$qids[$j])){
                  $fields_ordered[] = $fields[$i];
                  break 1;
               }
            }
         }
         
      }
      $query .= " FROM wd_".$wd_id;
      $query .= " WHERE (dbmode is NULL OR (dbmode<>'DELETED' AND dbmode<>'DUP'))";
      $query .= " ORDER BY wd_row_id";
      $query .= " LIMIT ".$job['field1'].",".$job['field2'];
            
      $mheader = "";
      $qheader = "";
      $header = "";
      $content = "";
      $results = $sql->queryGetResults($query);
      for ($i=0;$i<count($results);$i++) {
         // Row header data - no questions
         foreach($results[$i] as $key => $val){
            if (0!=strcmp(substr($key,0,1),"q")) {
               if($i==0) {
                  $mheader .= "\"".csvEncodeDoubleQuotes(strip_tags($key)).$hdr_postfix."\",";
                  $qheader .= "\"".csvEncodeDoubleQuotes(strip_tags($key)).$hdr_postfix."\",";
                  $header .= "\"".csvEncodeDoubleQuotes(strip_tags($key)).$hdr_postfix."\",";
               }
               $content .= "\"".csvEncodeDoubleQuotes($val)."\",";
            }
         }
         
         // Private surveys are for organizations with administrators
         // try to print org and admin headers/data before real data
         if($orgwd!=NULL && $orgwd['wd_id']>0) {
            // try getting user/org and survey admin
            if($i==0) {
               $h = "";
               $h .= "\"fname_admin\",";
               $h .= "\"lname_admin\",";
               $h .= "\"email_admin\",";
               $h .= "\"phonenum_admin\",";
               $h .= "\"userid_admin\",";
               $h .= "\"company_".$ty."\",";
               $h .= "\"addr1_".$ty."\",";
               $h .= "\"addr2_".$ty."\",";
               $h .= "\"city_".$ty."\",";
               $h .= "\"state_".$ty."\",";
               $h .= "\"zip_".$ty."\",";
               $h .= "\"country_".$ty."\",";
               $h .= "\"phonenum_".$ty."\",";
               $h .= "\"userid_".$ty."\",";
               $h .= "\"adminresp_".$ty."\",";
               $mheader .= $h;
               $qheader .= $h;
               $header .= $h;
               
               for($j=0;$j<count($orgqs);$j++) {
                  $csvr = $wd->getCSVRow($orgwd['wd_id'],NULL,$orgqs[$j],NULL,"_".$ty,$job['printstuff']);
                  $mheader .= $csvr['mheader'];
                  $qheader .= $csvr['qheader'];
                  $header .= $csvr['header'];
               }
            }
            
            $o = array();
            $op = array();
            $u = array();
            if($results[$i]['userid']>0) {
               $o = $ua->getUser($results[$i]['userid']);
               if(0==strcmp($o['usertype'],"user")) {
                  $u = $o;
                  $adminrel = $ua->getUsersRelated($u['userid'],"from","SRVYADMIN");
                  $o = $ua->getUser($adminrel[0]['userid']);
               } else {               
                  $adminrel = $ua->getUsersRelated($o['userid'],"to","SRVYADMIN");
                  $u = $ua->getUser($adminrel[0]['reluserid']);
               }
               
               if($o['userid']==NULL && $u['userid']>0) $o = $u;
               else if($u['userid']==NULL && $o['userid']>0) $u = $o;
                              
               $content .= "\"".csvEncodeDoubleQuotes($u['fname'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($u['lname'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($u['email'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($u['phonenum'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($u['userid'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['company'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['addr1'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['addr2'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['city'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['state'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['zip'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['country'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['phonenum'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['userid'])."\","; 
               $content .= "\"".csvEncodeDoubleQuotes($o['field5'])."\","; 
               
               $op = $wd->getRows($orgwd['wd_id'],null,1,null,FALSE,$results[$i]['userid'],FALSE,FALSE,FALSE,TRUE,FALSE,NULL,1,NULL,FALSE,$job['printstuff']);
               for($j=0;$j<count($orgqs);$j++) {
                  $csvr = $wd->getCSVRow($orgwd['wd_id'],$op['results'][0]['wd_row_id'],$orgqs[$j],$op['results'][0][$orgqs[$j]['field_id']],"_".$ty,$job['printstuff']);
                  $content .= $csvr['content'];
               }
            }
         }
         
         // Fields ordered...
         for($j=0;$j<count($fields_ordered);$j++){
            if ($fields_ordered[$j]['hide']!=1) {
               $ans = $wd->getCSVRow($wd_id,$results[$i]['wd_row_id'],$fields_ordered[$j],$results[$i][$fields_ordered[$j]['field_id']],"",$job['printstuff']);
               if($i==0) {
                  $mheader .= $ans['mheader'];
                  $qheader .= $ans['qheader'];
                  $header .= $ans['header'];
               }
               $content .= $ans['content'];
            }
         }
         
         $content .= "\n";
      }
      
      $fulltext = "";
      if($job['field1']==0) $fulltext .= $qheader."\n".$mheader."\n".$header."\n";
      $fulltext .= $content;
      
      $job['field1'] = $job['field1'] + count($results);
      //$job['field1'] = $job['field1'] + $job['field2'];
      
      //When we use wd->getRows(), call this instead:
      //$job['field1']++;
      
      $job['field5'] = "jsfadmin/usercsv/".$filename;
      $file = fopen($GLOBALS['baseDir'].$job['field5'],"a");
      fwrite($file, $fulltext);
      fclose($file);
      
      if(count($results)<$job['field2']) $job['status'] = "FINISHED";
      else $job['status'] = "NEW";      
      
      return $job;
   }

   function startjob($wd_id,$qids=NULL,$subj="",$resched=NULL) {
      $subj .= " [JData ".$wd_id." ".date("m/d/Y H:i:s")."]";
      $sched = new Scheduler();
      $fn = "jdata_".$wd_id."_".date("YmdHis").".csv";
      //function addSchedCustom($classname,$subject=NULL,$priority=10,$starton=NULL,$content=NULL,$field1=0,$field2=0,$field3=NULL,$field4=NULL,$field5=NULL,$field6=NULL,$field7=NULL,$cmsid=0,$phpobject=NULL){
      $sched->addSchedCustom("DownloadWebData",$subj,4,NULL,$fn,0,0,NULL,NULL,NULL,NULL,NULL,$wd_id,$qids,NULL,NULL,$resched);
   }
   
   function copyJob($copyid=NULL) {
      $jobresults = FALSE;
      if($copyid!=NULL){
         $dbLink = new MYSQLaccess;
         
         $query = "SELECT * FROM schedemail WHERE semailid=".$copyid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            $sched = new Scheduler();
            $sched->addSchedCustom($results[0]['classname'],$results[0]['subject'],4,NULL,"",0,0,NULL,NULL,NULL,NULL,NULL,$results[0]['cmsid'],$results[0]['phpobject']);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
   
   function rescheduleJob($jobid=NULL,$starton=NULL) {
      $jobresults = FALSE;
      if($jobid!=NULL){
         $dbLink = new MYSQLaccess;
         
         $query = "SELECT * FROM schedemail WHERE semailid=".$jobid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            if(0!=strcmp($starton,"NOW()")) $starton = "'".$starton."'";
            $sched = new Scheduler();
            $sched->addSchedCustom($results[0]['classname'],$results[0]['subject'],$results[0]['priority'],$starton,"",0,0,NULL,NULL,NULL,NULL,NULL,$results[0]['cmsid'],$results[0]['phpobject'],$results[0]['phpfile'],$results[0]['userid'],$results[0]['resched']);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
}


Class EmailSurveyRecipients {
   function doWork($job){
   	if ($job['printstuff']) error_reporting(E_ALL);
      if ($job['printstuff']) print "\n<br>job:<br>\n";
      if ($job['printstuff']) print_r($job);
      if ($job['printstuff']) print "<br>\n";
      
      $sql = new MYSQLaccess();
      $wd = new WebsiteData();
      
      if($job['field1']==NULL || $job['field1']<1) $job['field1'] = 0;
      if($job['field2']==NULL || $job['field2']<1) $job['field2'] = 50;
      $job['field3'] = "Last ran: ".date("m/d/Y H:i:s");      

      $query = convertBack($job['content']);
      $query .= " ORDER BY d.wd_row_id";
      $query .= " LIMIT ".$job['field1'].",".$job['field2'];
      $results = $sql->queryGetResults($query);
      for($i=0;$i<count($results);$i++) {
         $wd->sendEmail($job['cmsid'],$results[$i]['wd_row_id']);
         $job['field1']++;
      }
      
      if(count($results)<$job['field2']) $job['status'] = "FINISHED";
      else $job['status'] = "NEW";      

      return $job;
   }

   function startjob($wd_id,$sql) {
      $subj = "Sending email for ".$wd_id." on ".date("m/d/Y H:i:s");
      $sched = new Scheduler();
      $sched->addSchedCustom("EmailSurveyRecipients",$subj,2,NULL,$sql,0,0,NULL,NULL,NULL,NULL,NULL,$wd_id);
   }
   
   function copyJob($copyid=NULL) {
      $jobresults = FALSE;
      if($copyid!=NULL){
         $dbLink = new MYSQLaccess;
         
         $query = "SELECT * FROM schedemail WHERE semailid=".$copyid.";";
         $results = $dbLink->queryGetResults($query);
         if ($results!=NULL && count($results)==1){
            $sched = new Scheduler();
            $sched->addSchedCustom("EmailSurveyRecipients",$results[0]['subject'],2,NULL,$results[0]['content'],0,0,NULL,NULL,NULL,NULL,NULL,$results[0]['cmsid']);
            $jobresults = TRUE;
         }
      }
      return $jobresults;      
   }
}

?>