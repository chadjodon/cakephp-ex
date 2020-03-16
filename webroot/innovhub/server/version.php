<?php

// This class is used to display pictures dynamically
class Version {
   //function newFile($dir,$fileName,$extension="",$descr,$search,$owner="",$adminnotes="",$metakw="",$title="",$filetype="TEXT",$privacy=0,$contents=NULL,$tempfilename=NULL,$theme=0) {
   //   return $this->newFileContent($dir,$fileName,$extension,$descr,$search,$owner,$adminnotes,$metakw,$title,NULL,$filetype,$privacy,$contents,$tempfilename,$theme);
   //}

   function newFileContent($dir,$fileName,$extension="",$descr="",$search="",$owner="",$adminnotes="",$metakw="",$title="",$contenttype=NULL,$filetype="TEXT",$privacy=0,$contents=NULL,$tempfilename=NULL,$theme=0,$cachetime=0,$siteid=NULL,$track=NULL) {
      if ($GLOBALS['printstuff']) print "newFile(".$dir.",".$fileName.",".$extension.",...)<BR>";
      $fileName = str_replace(" ","_",$fileName);
      $fileName = str_replace("'","",$fileName);
      $fileName = str_replace("\"","",$fileName);
      $fileName = str_replace("*","",$fileName);
      $fileName = str_replace("&","",$fileName);
      $fileName = str_replace("%","",$fileName);
      $fileName = str_replace("#","",$fileName);
      $fileName = str_replace("$","",$fileName);
      $fileName = str_replace("`","",$fileName);
      $fileName = str_replace("~","",$fileName);
      $f = $this->getFileByShortname($fileName);

      //make sure the shortname (fileName) doesn't already exist
      if ($f!=NULL) {
         return false;
      } else {
         $title = convertString($title);
         $rnd1 = getRandomNum(getSessionEmail());
         $rnd2 = getRandomNum($rnd1);
         $sql = new MYSQLaccess();
         if ($contenttype==NULL) $contenttype="NULL";
         if ($privacy==NULL) $privacy="0";
         if ($cachetime==NULL) $cachetime="0";
         if ($track!=1) $track="0";
         $query = "INSERT INTO cmsfiles (filename, title, extension,dir,filetype,contenttype,privacy,xmp,xmp_full,cachetime,track) VALUES ('".$fileName."','".$title."','".$extension."','".$dir."','".$filetype."',".$contenttype.",".$privacy.",'".$rnd1."','".$rnd2."',".$cachetime.",".$track.");";
         $cmsid = $sql->insertGetValue($query);
         $this->newVersion($cmsid,$owner,$adminnotes,$contents,$tempfilename,$theme,$descr,$search,$metakw,$title,$siteid);
         return $cmsid;
      }
   }

   //function updateFile ($cmsid,$title,$filetype,$privacy=0) {
   //   $this->updateFileContent ($cmsid,$title,$filetype,NULL,$privacy);
   //}

   function updateFileContent ($cmsid,$title,$filetype,$contenttype=NULL,$privacy=0,$cachetime=0,$track=NULL) {
      if ($contenttype==NULL) $contenttype="NULL";
      if ($privacy==NULL) $privacy="0";
      if ($cachetime==NULL) $cachetime="0";
      if ($track==NULL) $track="0";
      $sql = new MYSQLaccess();
      $query = "UPDATE cmsfiles set title='".convertString($title)."', filetype='".$filetype."', contenttype='".$contenttype."', privacy='".$privacy."', cachetime='".$cachetime."', track='".$track."' WHERE cmsid=".$cmsid.";";
      $sql->update($query);
   }

   //This will create a new version of a File in the DB and in the Filesystem
   //  notes:
   //  if tempfilename is set, we assume someone uploaded a file and we save it under the real filename
   //  if this is a textfile, and contents were sent, we save the new file name with the contents
   //  if this is a textfile, and no contents were sent, we save the new file with contents of the active file (if there is one)
   function OLDnewVersion($cmsid,$owner,$adminnotes,$contents=NULL,$tempfilename=NULL,$theme="0",$descr="",$search="",$metakw="",$title="",$siteid=NULL) {
      $version_id = "_jsf_";
      $version = $this->getNextVersionNumber($cmsid);
      $cmsfile = $this->getFileById($cmsid);
      $fileInfo = $cmsfile;
      if ($GLOBALS['printstuff']) print "newVersion() cmsid: ".$cmsid."<BR>";
      if ($tempfilename != NULL) {
         $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$fileInfo['dir'].$fileInfo['filename'].$version_id.$version.$fileInfo['extension'];
         if ($GLOBALS['printstuff']) print " tempfilename: ".$tempfilename." new file name: ".$filename."<BR>";
         copy($tempfilename,$filename);
      } else if (0==strcmp($cmsfile['filetype'],"TEXT")) {
         if ($GLOBALS['printstuff']) print "Text file for new version, trying to get contents of last version.<BR>";
         if ($contents == NULL) {
            $fileInfo = $this->getAsciiFileContents($cmsfile['filename'],$this->getCurrentTheme());
            $contents = $fileInfo['contents'];
         }
      
         $temp = new Template();
         $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$fileInfo['dir'].$fileInfo['filename'].$version_id.$version.$fileInfo['extension'];
         //$temp->saveFile($filename,convertToHtml($contents));
         $temp->saveFile($filename,$contents);
      }

      if ($siteid==NULL) {
         $ctx = new Context();
         $sitearr = $ctx->getSiteContext(); 
         $siteid = $sitearr[0]['siteid'];
      }

      $sql = new MYSQLaccess();
      $query = "INSERT INTO cmsfver (cmsid, siteid, version, search, metakw, metadescr, title, created, status, owner, adminnotes, theme, lastupdate, lastupdateby) VALUES ('".$cmsid."', '".$siteid."', '".$version."', '".convertString($search)."', '".$metakw."', '".convertString($descr)."', '".convertString($title)."',NOW(),'NEW','".$owner."','".convertString($adminnotes)."', '".$theme."',NOW(),'".$owner."');";
      $sql->insert($query);
      return $version;
   }

   function newVersion($cmsid,$owner,$adminnotes,$contents=NULL,$tempfilename=NULL,$theme=NULL,$descr="",$search="",$metakw="",$title="",$siteid=NULL) {
      if ($cmsid==NULL) return NULL;
      $cmsfile = $this->getFileByIdQuick($cmsid);
      if ($cmsfile==NULL) $cmsfile = $this->getFileByIdQuick($cmsid,NULL,TRUE);
      if ($cmsfile==NULL) $cmsfile = $this->getFileById($cmsid);

      $version_id = "_jsf_";
      $version = $this->getNextVersionNumber($cmsid);

      if ($siteid==NULL) {
         $ctx = new Context();
         $sitearr = $ctx->getSiteContext(); 
         $siteid = $sitearr[0]['siteid'];
      }

      $sql = new MYSQLaccess();
      $query = "INSERT INTO cmsfver (cmsid, siteid, version, search, metakw, metadescr, title, created, status, owner, adminnotes, theme, lastupdate, lastupdateby)";
      $query .= " VALUES ('".$cmsid."', '".$siteid."', '".$version."', '".convertString($search)."', '".$metakw."', '".convertString($descr)."', '".convertString($title)."',NOW(),'NEW','".$owner."','".convertString($adminnotes)."', '".$theme."',NOW(),'".$owner."');";
      $sql->insert($query);

      if ($tempfilename != NULL) {
         $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$cmsfile['dir'].$cmsfile['filename'].$version_id.$version.$cmsfile['extension'];
         copy($tempfilename,$filename);
      } else if ($contents != NULL) {
         $temp = new Template();
         $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$cmsfile['dir'].$cmsfile['filename'].$version_id.$version.$cmsfile['extension'];
         $temp->saveFile($filename,$contents);
      } else if($cmsfile['version']!=NULL) {
         $widgetname = $this->getFileTypeObject($cmsfile['filetype']);

         //print "\n<!-- newVersion() copying cmsid: ".$cmsid.".  File to copy:\n";
         //print_r($cmsfile);
         //print "\nto version: ".$version." -->\n";

         $widgetClass = new $widgetname();
         $widgetClass->versionCopy($cmsid,$cmsfile['version'],$cmsid,$version);
      }

      //***chj

      return $version;
   }

   function isVersionEditable($cmsfver){
      $ua = new UserAcct();
      if (0==strcmp($cmsfver['status'],"ACTIVE")) return FALSE;
      else return $this->isVersionHeaderEditable($cmsfver);
   }

   function isVersionHeaderEditable($cmsfver){
      $ua = new UserAcct();
      if (0!=strcmp($cmsfver['owner'],$_SESSION['s_user']['emailAddress']) && !$ua->doesUserHaveAccessToLevel(isLoggedOn(),3) && !$ua->isUserAccessible(isLoggedOn(),"CMS",$cmsfver['cmsid']) ) return FALSE;
      else return TRUE;
   }

   function newDir($curdir,$newdir){
      mkdir($GLOBALS['rootDir'].$GLOBALS['contentDir'].$curdir.$newdir);
   }

   //moves all versioned files from one directory to another
   function moveFile($cmsid,$todir){
      $version_id = "_jsf_";
      if (0==strcmp($todir,"root")) $todir="";
      $fileInfo = $this->getFileById($cmsid);
      if ($fileInfo !== NULL && $todir !== NULL && strcmp($todir,$fileInfo['dir'])!=0) {
         $versions = $this->getAllVersions($cmsid);
         for ($i=0; $i<count($versions); $i++) {
            $cmsfver = $versions[$i];
            $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$fileInfo['dir'].$fileInfo['filename'].$version_id.$cmsfver['version'].$fileInfo['extension'];
            $newfilename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$todir.$fileInfo['filename'].$version_id.$cmsfver['version'].$fileInfo['extension'];
            if (file_exists($filename)) {
               rename($filename,$newfilename);         
            }
         }
         $query = "UPDATE cmsfiles SET dir='".$todir."' WHERE cmsid=".$cmsid.";";
         $sql = new MYSQLaccess();
         $sql->update($query);
      }
   }

   // updates the info for a version, and the fileystem itself if contents or a temporary file are passed in.
   function updateVersion($cmsid,$version,$owner,$adminnotes=NULL,$status=NULL,$contents=NULL,$tempfilename=NULL,$theme=0,$title="",$metadescr="",$search="",$metakw="",$siteid=NULL) {
      //print "updateversion (".$cmsid.",".$version.",".$owner.",".$adminnotes.",".$status.",".$contents.",".$tempfilename.",".$theme.",".$title.",".$metadescr.",".$search.",".$metakw.",".$siteid.")\n<br>";
      $ver = $this->getVersionedFile($cmsid,$version);
      //$ctx = new Context();
      //$sitearr = $ctx->getSiteContext(); 
      //if ($ver['siteid'] == $sitearr[0]['siteid'] && 0==strcmp($ver['owner'],$owner)) {
      if (0==strcmp($ver['owner'],$owner)) {
         $version_id = "_jsf_";

         if ($tempfilename != null) {
            $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$ver['dir'].$ver['filename'].$version_id.$version.$ver['extension'];
            if (file_exists($filename)) {
               rename($filename,$GLOBALS['rootDir'].$GLOBALS['deleteDir']."replaced".getDateForDB().getRandomNum("322").$ver['filename'].$version_id.$version.$ver['extension']);         
            }
            copy($tempfilename,$filename);
         } else if (0==strcmp($ver['filetype'],"TEXT") && $contents!==NULL) {
            $temp = new Template();
            $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$ver['dir'].$ver['filename'].$version_id.$version.$ver['extension'];
            //$temp->saveFile($filename,convertToHtml($contents));
            $temp->saveFile($filename,$contents);
         }
   
         $sql = new MYSQLaccess();
         $query = "UPDATE cmsfver set ";
         if ($title != NULL) $query .= " title='".convertString($title)."',";
         if ($metakw != NULL) $query .= " metakw='".convertString($metakw)."',";
         if ($metadescr != NULL) $query .= " metadescr='".convertString($metadescr)."',";
         if ($search != NULL) $query .= " search='".convertString($search)."',";
         if ($siteid != NULL) $query .= " siteid=".$siteid.",";
         if ($adminnotes != NULL) $query .= " adminnotes='".convertString($adminnotes)."',";
         if ($status != NULL) $query .= " status='".$status."',";
         $query .= " lastupdate=NOW(),";
         $query .= " lastupdateby='".$owner."',";
         $query .= " theme=".$theme;
         $query .= " WHERE cmsid=".$cmsid." AND version=".$version.";";
         $sql->insert($query);
         //print "**chj** query: ".$query;
         return true;
      } else {
         return false;
      }
   }

   // updates the info for a version, and the fileystem itself if contents or a temporary file are passed in.
   function updateVersionContents($cmsid,$version,$owner,$contents=NULL,$tempfilename=NULL) {
      $version_id = "_jsf_";
      $ver = $this->getVersionedFile($cmsid,$version);
      //$ctx = new Context();
      //$sitearr = $ctx->getSiteContext(); 
      //if ($ver['siteid'] == $sitearr[0]['siteid'] && 0==strcmp($ver['owner'],$owner)) {
      if (0==strcmp($ver['owner'],$owner)) {
         if ($tempfilename != null) {
            $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$ver['dir'].$ver['filename'].$version_id.$version.$ver['extension'];
            if (file_exists($filename)) {
               rename($filename,$GLOBALS['rootDir'].$GLOBALS['deleteDir']."replaced".getDateForDB().getRandomNum("322").$ver['filename'].$version_id.$version.$ver['extension']);         
            }
            copy($tempfilename,$filename);
         } else if ((0==strcmp($ver['filetype'],"TEXT") || 0==strcmp($ver['filetype'],"DESIGN")) && $contents!==NULL) {
            $temp = new Template();
            $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$ver['dir'].$ver['filename'].$version_id.$version.$ver['extension'];
            //$temp->saveFile($filename,convertToHtml($contents));
            $temp->saveFile($filename,$contents);
         }
   
         $sql = new MYSQLaccess();
         $query = "UPDATE cmsfver set lastupdate=NOW(), lastupdateby='".$owner."' WHERE cmsid=".$cmsid." AND version=".$version.";";
         $sql->insert($query);
         return true;
      } else {
         return false;
      }
   }

   // Removes the existence of a file version in the DB and in the Filesystem
   function removeFileVersion($cmsid,$version) {
      $version_id = "_jsf_";
      //$cmsfile = $this->getFileById($cmsid);
      $cmsfile = $this->getVersionedFile($cmsid,$version);

      if (0==strcmp($cmsfile['owner'],$_SESSION['s_user']['emailAddress']) || isLoggedOn()==1){

         $extension = $cmsfile['extension'];
         $fileName = $cmsfile['filename'];
         $dir = $cmsfile['dir'];
         $sql = new MYSQLaccess();
         //$ctx = new Context();
         //$sitearr = $ctx->getSiteContext(); 
         //if ($cmsfile['siteid'] == $sitearr[0]['siteid']) {
            $query = "DELETE FROM cmsfver WHERE cmsid='".$cmsfile['cmsid']."' AND version='".$version."';";
            $sql->delete($query);
            $query = "DELETE FROM cmsfdes WHERE cmsid='".$cmsfile['cmsid']."' AND version='".$version."';";
            $sql->delete($query);
            $query = "DELETE FROM cmsftemp WHERE cmsid='".$cmsfile['cmsid']."' AND version='".$version."';";
            $sql->delete($query);
            if (file_exists($GLOBALS['rootDir'].$GLOBALS['contentDir'].$dir.$fileName.$version_id.$version.$extension)) {
               rename($GLOBALS['rootDir'].$GLOBALS['contentDir'].$dir.$fileName.$version_id.$version.$extension,$GLOBALS['rootDir'].$GLOBALS['deleteDir']."removed".getDateForDB().getRandomNum("322").$fileName.$version_id.$version.$extension);         
            }
         //}
      }
   }

   // Removes a file reference from teh system if there are no versions of that file in the DB! - will not remove the reference
   //   if there are versions out there... must remove those first to be able to remove the header info
   function removeFileEntirely($cmsid) {
      $sql = new MYSQLaccess();
      $query = "SELECT * FROM cmsfver WHERE cmsid='".$cmsid."';";
      $results = $sql->queryGetResults($query);

      if ($results != NULL && count($results)>0) return FALSE;
      else {
         $query = "DELETE FROM cmsfiles WHERE cmsid='".$cmsid."';";
         $sql->delete($query);

         $query = "DELETE FROM cmsfdes WHERE cmsid='".$cmsid."';";
         $sql->delete($query);

         $query = "DELETE FROM cmsftemp WHERE cmsid='".$cmsid."';";
         $sql->delete($query);

         $query = "DELETE FROM cmsftemp WHERE tempcmsid='".$cmsid."';";
         $sql->delete($query);

         return TRUE;
      }
   }

   function getVersionedFile($cmsid,$version=NULL) {
      $version_id = "_jsf_";
      $cmsfile = $this->getFileById($cmsid);
      $cmsfver = null;
      $query = null;
      
      if ($version == NULL) $query = "SELECT * from cmsfver where cmsid=".$cmsid." AND status='ACTIVE' ORDER BY version DESC;";
      else $query = "SELECT * from cmsfver where cmsid=".$cmsid." AND version=".$version.";";

      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      $cmsfver = $results[0];

      foreach($cmsfver as $key => $value) {
         if ($value!=NULL) $cmsfile[$key]=$value;
      }

      $cmsfile['fqfilename'] = $GLOBALS['rootDir'].$cmsfile['dir'].$cmsfile['filename'].$version_id.$cmsfile['version'].$cmsfile['extension'];
      $cmsfile['urlfilename'] = getBaseURL().$cmsfile['dir'].$cmsfile['filename'].$version_id.$cmsfile['version'].$cmsfile['extension'];
      return $cmsfile;
   }



   function getThemeClause($theme,$name="v.theme") {
      $themesToUse = separateStringBy($theme,",");
      $themeClause = "1=0";
      for ($i=0; $i<count($themesToUse); $i++) {
         $themeClause .= " OR ".$name."=".$themesToUse[$i];
      }
      return $themeClause;
   }

   function getSelectList(){
      return "f.cmsid, f.dir, f.filename, f.htags, f.extension, f.filetype, f.contenttype, f.privacy, f.xmp, f.xmp_full, f.cachetime, f.title as filetitle, f.track, v.adminnotes, v.created, v.status, v.lastupdateby, v.lastupdate, v.owner, v.metakw, v.metadescr, v.title, v.search, v.siteid, v.version, v.theme";
   }

   function getFileQuick($filename,$theme=0,$testing=0) {
      //print "<BR>\nfilename: ".$filename."<br>\n";
      $sql = new MYSQLaccess();
      $ctx = new Context();
      //Check non-default themes
      $query = "SELECT ".$this->getSelectList()." from cmsfiles f, cmsfver v, cmstheme t WHERE f.filename='".$filename."' AND f.cmsid=v.cmsid AND v.theme=t.themeid AND v.status='ACTIVE' AND ".$ctx->getSiteSQL("v.siteid")." AND (".$this->getThemeClause($theme,"v.theme").") ORDER BY t.priority DESC, v.theme DESC, v.version DESC;";
      $results = $sql->queryGetResults($query);
      if($testing==1) print "<br>\ncmsid: ".$filename;
      if ($GLOBALS['printstuff'] || $testing==1) print "version.getFileQuick() non-default query: ".$query."<br>";

      if ($results==NULL || count($results)<1) {
         if(is_numeric($filename)) {
            $query = "SELECT ".$this->getSelectList()." from cmsfiles f, cmsfver v WHERE f.cmsid='".$filename."' AND f.cmsid=v.cmsid AND v.status='ACTIVE' AND ".$ctx->getSiteSQL("v.siteid")." AND (".$this->getThemeClause($theme,"v.theme").") ORDER BY v.version DESC;";
            if($testing==1) print "<br>\nNumeric cmsid: ".$filename." query: ".$query;
            $results = $sql->queryGetResults($query);
         }
        
         if ($results==NULL || count($results)<1) {
           if($testing==1) print "<br>\nlast resort to find file";
           $query = "SELECT ".$this->getSelectList()." from cmsfiles f, cmsfver v WHERE f.filename='".$filename."' AND f.cmsid=v.cmsid AND v.theme=0 AND v.status='ACTIVE' AND ".$ctx->getSiteSQL("v.siteid")." ORDER BY v.version DESC;";
           $results = $sql->queryGetResults($query);
           //print "<br>\nQuery: ".$query."<br>\n";
         }
         if ($GLOBALS['printstuff'] || $testing==1) print "version.getFileQuick() (no values found) default query: ".$query."<br>";
      }
      return $results[0];
   }

   function getFileByIdQuick($cmsid,$theme=NULL,$ignorestatus=FALSE) {
      if ($cmsid==NULL) return NULL;
      $sql = new MYSQLaccess();
      $ctx = new Context();
      //Check non-default themes
      if ($theme===NULL) $theme = $this->getCurrentTheme();
      $query = "SELECT ".$this->getSelectList();
      $query .= " from cmsfiles f, cmsfver v, cmstheme t ";
      $query .= " WHERE f.cmsid='".$cmsid."' ";
      $query .= " AND f.cmsid=v.cmsid ";
      $query .= " AND v.theme=t.themeid ";
      if(!$ignorestatus) $query .= " AND v.status='ACTIVE' ";
      $query .= " AND ".$ctx->getSiteSQL("v.siteid");
      $query .= " AND (".$this->getThemeClause($theme,"v.theme").") ";
      $query .= " ORDER BY t.priority DESC, v.theme DESC, v.version DESC;";
      $results = $sql->queryGetResults($query);
      //print "\n<!-- getfilebyidquick 1. '".$cmsid."' query: ".$query." -->\n";

      if ($results==NULL || count($results)<1) {
         $query = "SELECT ".$this->getSelectList();
         $query .= " from cmsfiles f, cmsfver v ";
         $query .= " WHERE f.cmsid='".$cmsid."' ";
         $query .= " AND f.cmsid=v.cmsid ";
         $query .= " AND v.theme=0 ";
         if(!$ignorestatus) $query .= " AND v.status='ACTIVE' ";
         $query .= " AND ".$ctx->getSiteSQL("v.siteid");
         $query .= " ORDER BY v.version DESC;";
         $results = $sql->queryGetResults($query);
         //print "\n<!-- getfilebyidquick 2. '".$cmsid."' query: ".$query." -->\n";
      }

      return $results[0];
   }

   //method returns contents of the active version for the file
   function getAsciiFileContents($filename,$theme=0,$testing=0) {
      $version_id = "_jsf_";
      $cmsfile = $this->getFileQuick($filename,$theme,$testing);

      $cmsfile['contents'] = " ";
      if ($cmsfile != NULL && $cmsfile['filename'] != NULL) {
         //$temp = new Template();

         $widgetname = $this->getFileTypeObject($cmsfile['filetype']);
         $widgetClass = new $widgetname();
         $cmsfile['contents'] = $widgetClass->getHTML($cmsfile['cmsid'],$cmsfile['version'],NULL,TRUE);
         //$cmsfile['contents'] = $temp->getFileWithoutSub($GLOBALS['rootDir'].$GLOBALS['contentDir'].$cmsfile['dir'].$cmsfile['filename'].$version_id.$cmsfile['version'].$cmsfile['extension']);
      }
      return $cmsfile;
   }

   function sendEmailToTemplate($userid,$from,$shortname,$priority=5,$allowRepeats=FALSE){
      $scheduler = new Scheduler();
      $scheduler->addSchedEmail(NULL,$shortname,NULL,NULL,NULL,$userid,$from,$priority,$allowRepeats);
   }

   function getFileById($cmsid){
      $sql = new MYSQLaccess();
      $query = "SELECT * from cmsfiles where cmsid='".$cmsid."';";
      $results = $sql->queryGetResults($query);
      return $results[0];
   }

   function getHashTags($cmsid){
      $sql = new MYSQLaccess();
      $query = "SELECT htags from cmsfiles where cmsid='".$cmsid."';";
      $results = $sql->queryGetResults($query);
      $htArr = separateStringBy($results[0]['htags']," ");
      return $htArr;
   }

   function addHashTag($cmsid,$hashtag){
      if ($hashtag!=NULL) {
         $hashtag = preg_replace("/[^A-Za-z0-9_-]/",'',$hashtag);
         if (0!=strcmp(substr($hashtag,0,1),"#")) $hashtag = "#".$hashtag;
         $htArr = $this->getHashTags($cmsid);
         $htArr[count($htArr)] = $hashtag;
         $this->setHashTags($cmsid,$htArr);
      }
   }

   function setHashTags($cmsid,$htArr){
      $sql = new MYSQLaccess();
      $query = "update cmsfiles set htags = '";
      foreach($htArr as $key => $val) $query .= trim(strtolower($val))." ";
      $query .= "' where cmsid='".$cmsid."';";
      $sql->update($query);         
   }

   function removeHashTag($cmsid,$hashtag){
      //print "\n<!-- hashtag to remove: ".$hashtag." -->\n";
      if ($hashtag!=NULL) {
         $hashtag = preg_replace("/[^A-Za-z0-9_-]/",'',strtolower($hashtag));
         if (0!=strcmp(substr($hashtag,0,1),"#")) $hashtag = "#".$hashtag;
         $htArr = $this->getHashTags($cmsid);
         $results = array();
         foreach($htArr as $key => $val) if (0!=strcmp($hashtag,$val)) $results[] = $val;
         $this->setHashTags($cmsid,$results);
      }
   }

   function verifyXMP($cmsid,$xmp) {
      $sql = new MYSQLaccess();
      $query = "SELECT * from cmsfiles where cmsid='".$cmsid."' AND xmp='".$xmp."';";
      $results = $sql->queryGetResults($query);
      return $results[0];
   }

   function verifyXMPFull($cmsid,$xmp_full) {
      $sql = new MYSQLaccess();
      $query = "SELECT * from cmsfiles where cmsid='".$cmsid."' AND xmp_full='".$xmp_full."';";
      $results = $sql->queryGetResults($query);
      return $results[0];
   }

   function getFileByShortname($shortname){
      $sql = new MYSQLaccess();
      $query = "SELECT * from cmsfiles where filename='".$shortname."';";
      $results = $sql->queryGetResults($query);
      return $results[0];
   }


   function getExtension($filename){
         $postfix = strtolower(substr($filename,strlen($filename) - strpos(strrev($filename),".")));
         $extension = ".".$postfix;
         return $extension;
   }

   function getFileType($extension) {
         $filetype="TEXT";
         if (0==strcmp($extension,".jpg")|| 0==strcmp($extension,".jpeg") || 0==strcmp($extension,".gif") || 
            0==strcmp($extension,".png") || 0==strcmp($extension,".jng")  || 0==strcmp($extension,".bmp") || 
            0==strcmp($extension,".tiff")|| 0==strcmp($extension,".raw")  || 0==strcmp($extension,".doc") || 
            0==strcmp($extension,".xls") || 0==strcmp($extension,".zip")  || 0==strcmp($extension,".7z")  || 
            0==strcmp($extension,".jar") || 0==strcmp($extension,".cab")  || 0==strcmp($extension,".pdf") || 
            0==strcmp($extension,".psp") || 0==strcmp($extension,".psb")  || 0==strcmp($extension,".ps")  || 
            0==strcmp($extension,".mp3") || 0==strcmp($extension,".wav")  || 0==strcmp($extension,".exe") || 
            0==strcmp($extension,".eps") || 0==strcmp($extension,".mpeg") ) {
               $filetype="BINARY";
         }
         return $filetype;
   }

   function getStatusColor($status) {
      $bgcolor = "#EEEE99";
      if (0==strcmp($status,"ACTIVE")) $bgcolor="#99EE99";
      if (0==strcmp($status,"INACTIVE")) $bgcolor="#EE9999";
      return $bgcolor;
   }

   function getOrderByOptions($selected) {
      $options['Name'] = "filename";
      $options['Type'] = "extension";
      $result = getOptionList("orderby", $options, $selected);
      return $result;
   }



   function getTypeOptions($selected=NULL,$name="filetype",$leaveBlank=FALSE) {
      $types = $this->getFileTypes();
      for ($i=0; $i<count($types); $i++) {
         $options[$types[$i]['displayname']] = $types[$i]['filetype'];
      }
      $result = getOptionList($name, $options, $selected,$leaveBlank);
      return $result;
   }

   function getFileTypes() {
      $query = "SELECT * FROM cmsfwdg ORDER BY widgetid;";
      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function getFileTypeObject($filetype) {
      $query = "SELECT * FROM cmsfwdg WHERE filetype='".$filetype."'";
      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      $obj = trim($results[0]['objectname']);
      if ($obj==NULL) $obj = "UnknownWidget";
      else if (!class_exists($obj)) $obj = "UnknownWidget";

      return $obj;
   }



   function getThemeOptions($selected, $urlstart="", $name="theme", $allElement=false, $extra="") {
      if ($allElement) $options['All'] = $urlstart."-1";
      $options['Default'] = $urlstart."0";
      $themes = $this->getThemes();
      for ($i=0; $i<count($themes); $i++) {
         $theme = $themes[$i];
         $options[$theme['themename']] = $urlstart.$theme['themeid'];
      }
      $result = getOptionList($name, $options, $selected, false, $extra);
      return $result;
   }

   function getDirOptions($selected, $name="dir") {
      $dirList = $this->getDirTree("");
      $options['root/'] = "root";
      for ($i=0; $i<count($dirList); $i++) $options["root/".$dirList[$i]] = $dirList[$i];
      $result = getOptionList($name, $options, $selected);
      return $result;      
   }

   function getDirTree($startFolder){
      $count = -1;
      $results = NULL;
      $template = new Template();
      $dirs = $template->list_dir($GLOBALS['rootDir'].$GLOBALS['contentDir'].$startFolder, false);
      for ($i=0; $i<count($dirs); $i++) {
         $count++;
         $results[$count] = $startFolder.$dirs[$i];
         $recurResults = $this->getDirTree($results[$count]);
         for ($j=0; $j<count($recurResults); $j++) {
            $count++;
            $results[$count] = $recurResults[$j];
         }
      }
      return $results;
   }

   function doVersionSubstitutions($str,$sub=null) {
      $str = $this->doLinkSubs($str,$sub);
      $str = $this->doURLSubs($str,$sub);
      $str = $this->doCMSSubs($str,$sub);
      $str = $this->doSystemPropertySubs($str,$sub);
      $str = $this->doParameterSubs($str,$sub);
      $str = $this->doTitleSubs($str,$sub);
      $str = $this->doPHPSubs($str,$sub);
      return $str;
   }

   function doSystemPropertySubs($str,$sub=null) {
      $tag = $this->getValue("tagprop");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = true;
         else {
            $str = str_replace($tagstart.$shortname.$tagend,$this->getValue($shortname),$str);
         }
      }
      return $str;
   }

   function doParameterSubs($str,$sub=null) {
      $tag = $this->getValue("tagparam");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = true;
         else {
            $str = str_replace($tagstart.$shortname.$tagend,getParameter($shortname),$str);
         }
      }
      return $str;
   }

   function doLinkSubs($str,$sub=null) {
      $tag = $this->getValue("tagcmslink");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = true;
         else {
            $cmsfver = $this->getFileQuick($shortname,$this->getCurrentTheme());
            $url = $this->createURL($cmsfver);
            if ($cmsfver['extension']==NULL || 0==strcmp($cmsfver['extension'],".html") || 0==strcmp($cmsfver['extension'],".htm")) {
               $link = "<a href=\"".getBaseURL().$GLOBALS['codeFolder']."controller.php?view=".$cmsfver['filename']."\">".$cmsfver['title']."</a>";
            }
            else {
               $link = "<a href=\"".getBaseURL().$url."\">".$cmsfver['title']."</a>";
            }
            $str = str_replace($tagstart.$shortname.$tagend,$link,$str);
         }
      }
      return $str;
   }

   function doURLSubs($str,$sub=null) {
      $tag = $this->getValue("tagurl");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = true;
         else {
            $cmsfver = $this->getFileQuick($shortname,$this->getCurrentTheme());
            $url = $this->createURL($cmsfver);
            $str = str_replace($tagstart.$shortname.$tagend,getBaseURL().$url,$str);
         }
      }
      return $str;
   }

   function doTitleSubs($str,$sub) {
      $tag = $this->getValue("tagtitle");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = true;
         else {
            $cmsfver = $this->getFileQuick($shortname,$this->getCurrentTheme());
            $url = $this->createURL($cmsfver);
            $str = str_replace($tagstart.$shortname.$tagend,$cmsfver['title'],$str);
         }
      }
      return $str;
   }

   function doPHPSubs($str,$sub) {
      $tag = $this->getValue("tagphp");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         //print "\n<!-- dophpsubs, shortname: ".$shortname." -->\n";
         if ($shortname == NULL) $finished = true;
         else {
            $params = separateStringBy($shortname,$this->getValue("tagstarter"));
            $requiredPHPFile = $GLOBALS['rootDir'].$params[0];
            //print "\n<!-- dophpsubs, including once: ".$requiredPHPFile." -->\n";
            include_once $requiredPHPFile;
            //print "\n<!-- dophpsubs, new impl class: ".$params[1]."() -->\n";
            $implClass = new $params[1]();
            $args = array();
            for ($i=2; $i<count($params); $i++) {
               //print "\n<!-- dophpsubs, args[".($i-2)."]: ".$params[$i]." -->\n";
               $args[$i-2]=$params[$i];
            }
            $htmlCommentBegin = ""; //"\n<!--START version doPHPSubs -->\n";
            $htmlCommentEnd = ""; //"\n<!--END version doPHPSubs -->\n";
            $newHTML = $implClass->doWork($args);
            $str = str_replace($tagstart.$shortname.$tagend,$htmlCommentBegin.$newHTML.$htmlCommentEnd,$str);
         }
      }
      return $str;
   }

   // privacy of -1 is any admin user
   // privacy of -2 is a super admin user
   // privacy of 0 is anybody
   // privacy 1 thru 10 are website levels of access - 10 being the most access
   function checkIfUserCanAccess($privacy,$userid){
      $maxLevel = 10;
      $ua = new UserAcct();
      if ($privacy == 0) {
         return true;
      } else if ($privacy == -1 && $ua->isUserAdmin($userid)) {
         return true;
      } else if ($privacy == -2 && $ua->doesUserHaveAccessToLevel($userid,12)) {
         return true;
      } else if (($privacy > 0) && $ua->isUserAdmin($userid)) {
         return true;
      } else if ($privacy > 0) {
         $allowed = false;
         $levels = $ua->getUsersAccessPointsFor($userid,"WEBSITE");
         if (count($levels)>0) {
            $highestLvl = $levels[count($levels)-1]['id'];
            if ($privacy <= $highestLvl) $allowed = true;
         }
         return $allowed;
      } else {
         return false;
      }

   }

   function doCMSSubs($str,$sub=null) {
      $ua = new UserAcct();
      $tag = $this->getValue("tagcms");
      $tagbeg = $this->getValue("tagstarter");
      $tagstart = $tagbeg.$tag."_";
      $tagend = "_".$tag.$tagbeg;
      $finished = false;
      while (!$finished) {
         $shortname = findTagInString($tag,$str);
         if ($shortname == NULL) $finished = TRUE;
         else {
            $cmsfver = $this->getFileQuick($shortname,$this->getCurrentTheme());
            if ($cmsfver==NULL || $cmsfver['cmsid']==NULL) {
               $str = str_replace($tagstart.$shortname.$tagend,"<!-- cms shortname: ".$shortname." was not found. -->",$str);
            } else {
               if ($this->checkIfUserCanAccess($cmsfver['privacy'],isLoggedOn())) {
                  $widgetname = $this->getFileTypeObject($cmsfver['filetype']);
                  $widgetClass = new $widgetname();
                  //print "\n<!-- doCMSSubs() shortname: ".$shortname." -->\n";
                  $contents = $widgetClass->getHTML($cmsfver['cmsid'],$cmsfver['version'],$sub);
                  $str = str_replace($tagstart.$shortname.$tagend,$contents,$str);
                  $temp = new Template();
                  if ($cmsfver['track']==1) $temp->trackItem($cmsfver['filename'],NULL,"JSF Content Substitution Initiated",$cmsfver['version']);
               } else {
                  $str = str_replace($tagstart.$shortname.$tagend,"<!-- portion of this page is not available to this user -->",$str);
               }
            }
         }
      }
      return $str;
   }

   function searchFiles($dir=NULL,$searchtxt=NULL,$filetype=NULL,$contenttype=NULL,$srchType="AND",$orderby=NULL,$limit=0,$page=1,$filename=NULL,$extension=NULL,$htags=NULL) {
      $whereclause = "";
      if ($page==NULL || $page<1) $page = 1;

      if (0==strcmp(strtoupper($srchType),"AND")) $whereclause = "1=1 ";
      else $whereclause = "1=0 ";

      if ($htags!=NULL) {
         $whereclause.= $srchType." (1=1 ";
         $htArr = separateStringBy($htags,",");
         for ($i=0;$i<count($htArr);$i++) {
            $t = trim($htArr[$i]);
            if ($t!=NULL) {
               $htArr2 = separateStringBy($t," ");
               for ($j=0;$j<count($htArr2);$j++) {
                  $t2 = trim($htArr2[$j]);
                  if ($t2!=NULL) {
                     if (0!=strcmp(substr($t2,0,1),"#")) $t2 = "#".$t2;
                     $whereclause .= "AND htags LIKE '%".$t2." %' ";
                  }
               }
            }
         }
         $whereclause.= " ) ";
      }

      if ($filetype != NULL) $whereclause.=  $srchType." UPPER(filetype)='".strtoupper($filetype)."' ";
      if ($contenttype != NULL) $whereclause.=  $srchType." contenttype=".$contenttype." ";
      if ($dir !== NULL) $whereclause.=  $srchType." UPPER(dir)='".strtoupper($dir)."' ";
      if ($searchtxt != NULL) {
         $whereclause.= $srchType." ( UPPER(filename) like '%".strtoupper($searchtxt)."%' ";
         $whereclause.= " OR UPPER(extension) like '%".strtoupper($searchtxt)."%' ";
         $whereclause.= " OR UPPER(title) like '%".strtoupper($searchtxt)."%' ";
         $whereclause.= " OR UPPER(htags) like '%".strtoupper($searchtxt)."%' ";
         $whereclause.= " OR UPPER(dir) like '%".strtoupper($searchtxt)."%' ) ";
      }
      if ($filename != NULL) $whereclause.=  $srchType." LOWER(filename) like '%".strtolower($filename)."%' ";
      if ($extension != NULL) $whereclause.=  $srchType." LOWER(extension) like '%".strtolower($extension)."%' ";
      
      if ($orderby == NULL) $whereclause.= " ORDER BY filename ASC";
      else $whereclause .= " ORDER BY ".$orderby;
   
      if ($limit!=NULL && $limit>0) $whereclause .= " LIMIT ".(($page-1)*$limit).",".$limit;

      $sql = new MYSQLaccess();
      $query = "SELECT * from cmsfiles where ".$whereclause.";";
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function advancedSearchFiles($orderby=0,$dir=NULL,$filename=NULL,$extension=NULL,$descr=NULL,$search=NULL,$metakw=NULL,$title=NULL,$theme=NULL,$adminnotes=NULL,$srchType="AND") {
      $whereclause = NULL;
      if ($filename != NULL) $whereclause.=   " UPPER(f.filename) like '%".strtoupper($filename)."%' ".$srchType;
      if ($extension != NULL) $whereclause.=  " UPPER(f.extension) like '%".strtoupper($extension)."%' ".$srchType;
      if ($descr != NULL) $whereclause.=      " UPPER(v.metadescr) like '%".strtoupper($descr)."%' ".$srchType;
      if ($search != NULL) $whereclause.=     " UPPER(v.search) like '%".strtoupper($search)."%' ".$srchType;
      if ($metakw != NULL) $whereclause.=     " UPPER(v.metakw) like '%".strtoupper($metakw)."%' ".$srchType;
      if ($title != NULL) $whereclause.=      " UPPER(v.title) like '%".strtoupper($title)."%' ".$srchType;
      if ($theme != NULL) $whereclause.=      " v.theme=".$theme." ".$srchType;
      if ($adminnotes != NULL) $whereclause.= " UPPER(v.adminnotes) like '%".strtoupper($adminnotes)."%' ".$srchType;

      if ($whereclause == NULL) {
         $whereclause= "f.cmsid=v.cmsid";
         if ($dir != NULL) $whereclause.= " AND UPPER(f.dir) like '%".strtoupper($dir)."%'";
      } else {
         $tempwhereclause= "f.cmsid=v.cmsid";
         if ($dir != NULL) $tempwhereclause.= " AND UPPER(f.dir) like '%".strtoupper($dir)."%'";
         $endingClause = " 1=1";
         if (0==strcmp($srchType,"OR")) $endingClause=" 1=0";
         $whereclause = $tempwhereclause." AND (".$whereclause.$endingClause.")";
      }

      if ($orderby == 1) $whereclause.= " ORDER BY f.filename DESC";
      else if ($orderby == 2) $whereclause.= " ORDER BY f.extension ASC";
      else if ($orderby == 3) $whereclause.= " ORDER BY f.extension DESC";
      else if ($orderby == 4) $whereclause.= " ORDER BY f.title ASC";
      else if ($orderby == 5) $whereclause.= " ORDER BY f.title DESC";
      else if ($orderby == 6) $whereclause.= " ORDER BY f.dir ASC";
      else if ($orderby == 7) $whereclause.= " ORDER BY f.dir DESC";
      else if ($orderby == 8) $whereclause.= " ORDER BY v.theme ASC, f.filename ASC";
      else if ($orderby == 9) $whereclause.= " ORDER BY v.lastupdate DESC";
      else $whereclause.= " ORDER BY f.filename ASC";

      $sql = new MYSQLaccess();                                                                       
      $query = "SELECT DISTINCT f.cmsid, f.filename, f.extension, f.dir, f.filetype, f.title ";
      $query .= " FROM cmsfiles f, cmsfver v WHERE ".$whereclause.";";
      $results = $sql->queryGetResults($query);
      return $results;
   }


   function getNextVersionNumber($cmsid) {
      $sql = new MYSQLaccess();
      $query = "SELECT MAX(version) FROM cmsfver WHERE cmsid=".$cmsid.";";
      $results = $sql->queryGetResults($query);
      
      return ($results[0]['MAX(version)']+1);
   }

   function getAllVersions($cmsid) {
      $sql = new MYSQLaccess();
      $query = "SELECT ".$this->getSelectList()." from cmsfver v, cmsfiles f where v.cmsid=f.cmsid AND v.cmsid=".$cmsid." ORDER BY v.version ASC;";
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function getFileVersion($cmsid,$version){
      $query = "SELECT ".$this->getSelectList()." from cmsfver v, cmsfiles f where f.cmsid=v.cmsid AND v.cmsid=".$cmsid." AND v.version=".$version.";";
      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      return $results[0];
   }

   function getVersionByShortname($shortname,$version){
      $query = "SELECT ".$this->getSelectList()." from cmsfver v, cmsfiles f where f.cmsid=v.cmsid AND f.filename='".$shortname."' AND v.version=".$version.";";
      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      $cmsfile = $results[0];
      $version_id = "_jsf_";
      $cmsfile['contents'] = " ";
      if ($cmsfile != NULL && $cmsfile['filename'] != NULL) {
         //$temp = new Template();
         //$cmsfile['contents'] = $temp->getFileWithoutSub($GLOBALS['rootDir'].$GLOBALS['contentDir'].$cmsfile['dir'].$cmsfile['filename'].$version_id.$cmsfile['version'].$cmsfile['extension']);
         $widgetname = $this->getFileTypeObject($cmsfile['filetype']);
         $widgetClass = new $widgetname();
         $cmsfile['contents'] = $widgetClass->getHTML($cmsfile['cmsid'],$cmsfile['version'],NULL,TRUE);

      }
      return $cmsfile;
   }

   function getIcon($ext="") {
      $ext = strtolower($ext);
      $icon = "icon_unknown.jpg";
      if (0==strcmp($ext,".jpg") || 0==strcmp($ext,".gif") || 0==strcmp($ext,".tif") || 0==strcmp($ext,".png")) $icon="icon_jpg.jpg";
      else if (0==strcmp($ext,".doc") || 0==strcmp($ext,".docx")) $icon="icon_doc.jpg";
      else if (0==strcmp($ext,".pdf")) $icon="icon_pdf.jpg";
      else if (0==strcmp($ext,".txt") || 0==strcmp($ext,".log")) $icon="icon_txt.jpg";
      else if (0==strcmp($ext,".xls") || 0==strcmp($ext,".xlsx") || 0==strcmp($ext,".csv")) $icon="icon_xl.jpg";
      else if (0==strcmp($ext,".htm") || 0==strcmp($ext,".html")) $icon="icon_html.jpg";
      else if (0==strcmp($ext,".ppt") || 0==strcmp($ext,".pptx")) $icon="icon_ppt.jpg";
      else if (0==strcmp($ext,".zip") || 0==strcmp($ext,".jar") || 0==strcmp($ext,".tar") || 0==strcmp($ext,".7z")) $icon="icon_zip.jpg";
      $img_icon .= "<img src=\"".getBaseURL().$GLOBALS['imagesDir'].$icon."\" border=\"0\">";
      return $img_icon;
   }

   function setVersionStatus($cmsid,$version,$status){
      //Update the status in the DB
      $sql = new MYSQLaccess();
      $query = "UPDATE cmsfver set status='".$status."' where version=".$version." AND cmsid=".$cmsid.";";
      $sql->update($query);

      //Flush the cache out...
      $cmsfile = $this->getFileById($cmsid);
      $cachefilename = "cms-".$cmsfile['filename']."-cache.html"; 
      $cachefile = $GLOBALS['cacheFolder'].$cachefilename; 
      if (file_exists($cachefile)) unlink($cachefile);
   }


//-----------------------------------------------------------------------------
// Global Database system settings
// This class simply sets name-value pairs needed by the system
//-----------------------------------------------------------------------------
  
  function getDefaultTitle() {    
    return $this->getValue('defaultTitle');
  }

  function getSiteEmail() {
    return $this->getValue('siteemail');
  }

  function setSiteEmail($siteEmail,$theme) {
    $valid = new Validator;
    if ($valid->validemail($siteEmail)) {
      $this->setValueTheme('siteemail',$siteEmail,$theme);
      return TRUE;
    }
    else return FALSE;
  }

  function setDefaultTitle($title,$theme) {
      $this->setValueTheme('defaultTitle',$title,$theme);
      return TRUE;
  }

  function newThemeBased($theme,$newTheme) {
     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM globals where theme='".$theme."';";
     $results = $dbLink->queryGetResults($query);
     
     for ($i=0; $i<count($results); $i++) {
       $line = $results[$i];
       $this->setValueTheme(convertString($line['name']),convertString($line['value']),$newTheme);
     }

      $this->newTheme($newTheme);
  }

   function newTheme($themename, $priority=1, $startday=1, $endday=372, $status="INACTIVE") {
      $sql = new MYSQLAccess();     
      $query = "INSERT INTO cmstheme (themename,priority,startday,endday,status) VALUES('".$themename."',".$priority.",".$startday.",".$endday.",'".$status."');";
      return $sql->insertGetValue($query);
      $this->clearSessionCache();
   }

   function newAdspace($adspcname, $adspctype, $status="INACTIVE") {
      $sql = new MYSQLAccess();     
      $query = "INSERT INTO cmsadspc (adspcname,adspctype,status) VALUES('".$adspcname."','".$adspctype."','".$status."');";
      return $sql->insertGetValue($query);
   }

   function newAdspaceDef($adspaceid, $cmsid, $percent, $priority=1, $status="INACTIVE") {
      $sql = new MYSQLAccess();     
      $query = "INSERT INTO cmsasdef (adspaceid,cmsid,percent,priority,status) VALUES('".$adspaceid."','".$cmsid."','".$percent."','".$priority."','".$status."');";
      return $sql->insertGetValue($query);
   }

   function updateTheme($id, $themename, $priority=1, $startday=1, $endday=372, $status="INACTIVE") {
      $sql = new MYSQLAccess();     
      $query = "UPDATE cmstheme set themename='".$themename."',priority=".$priority.",startday=".$startday.",endday=".$endday.",status='".$status."' WHERE themeid=".$id.";";
      if ($GLOBALS['printstuff']) print $query."<BR>";
      $sql->update($query);
      $this->clearSessionCache();
   }

   function updateAdspace($adspaceid, $adspcname, $adspctype, $status) {
      $sql = new MYSQLAccess();     
      $query = "UPDATE cmsadspc set adspcname='".$adspcname."',adspctype='".$adspctype."',status='".$status."' WHERE adspaceid=".$adspaceid.";";
      $sql->update($query);
   }

   function updateAdspaceDef($adspacedefid,$adspaceid, $cmsid, $percent, $priority=1, $status="INACTIVE") {
      $sql = new MYSQLAccess();     
      $query = "UPDATE cmsasdef set adspaceid='".$adspaceid."',cmsid='".$cmsid."',percent='".$percent."',priority='".$priority."',status='".$status."' WHERE adspacedefid=".$adspacedefid.";";
      $sql->update($query);
   }   

   function removeTheme($themeid) {
     $dbLink = new MYSQLAccess;     
     $query = "DELETE FROM cmstheme where themeid=".$themeid.";";
     $dbLink->delete($query);

     $query = "DELETE FROM cmsrules where themeid=".$themeid.";";
     $dbLink->delete($query); 

      $this->clearSessionCache();
   }
   
   function removeAdspace($adspaceid) {
     $dbLink = new MYSQLAccess;
     $asds = $this->getAdspaceDefs($adspaceid);
     for ($i=0; $i<count($asds); $i++) {
      $query = "DELETE FROM cmsrules where asdefid='".$asds[$i]['asdefid']."';";
      $dbLink->delete($query);
     }

     $query = "DELETE FROM cmsasdef where adspaceid=".$adspaceid.";";
     $dbLink->delete($query);

     $query = "DELETE FROM cmsadspc where adspaceid=".$adspaceid.";";
     $dbLink->delete($query);
   }

   function removeAdspaceDef($asdefid){
      $dbLink = new MYSQLAccess;
      $query = "DELETE FROM cmsrules where asdefid='".$asdefid."';";
      $dbLink->delete($query);

      $query = "DELETE FROM cmsasdef where asdefid='".$asdefid."';";
      $dbLink->delete($query);
   }

   function newThemeRule($themeid,$asdefid, $ruletype,$field1="",$field2="",$field3="",$field4="",$field5=""){
      $sql = new MYSQLAccess();     
      $query = "INSERT INTO cmsrules (themeid,asdefid,ruletype,field1,field2,field3,field4,field5) VALUES('".$themeid."','".$asdefid."','".$ruletype."','".$field1."','".$field2."','".$field3."','".$field4."','".$field5."');";
      return $sql->insertGetValue($query);      
   }

   function removeThemeRule($ruleid){
      $sql = new MYSQLAccess();     
      $query = "DELETE FROM cmsrules where ruleid='".$ruleid."';";
      $sql->delete($query); 
   }

   function getThemeRules($themeid){
      if ($themeid==NULL || $themeid==0) return NULL;
      $sql = new MYSQLAccess();     
      $query = "SELECT * from cmsrules where themeid='".$themeid."';";
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function getAdspaces(){
      $sql = new MYSQLAccess();     
      $query = "SELECT * from cmsadspc;";
      $results = $sql->queryGetResults($query);
      return $results;      
   }

   function getAdspaceDefs($adspaceid) {
      $sql = new MYSQLAccess();     
      $query = "SELECT * from cmsasdef where adspaceid='".$adspaceid."';";
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function getAdspaceRules($asdefid){
      $sql = new MYSQLAccess();     
      $query = "SELECT * from cmsrules where asdefid='".$asdefid."';";
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function checkPrivacyRule ($line) {
      $passed=true;
      //field1 represents which privacy setting to search for
      //  0 checks that the user is logged in at all
      //  x checks that they are approved for the website at level x
      //  -1 checks that they are an administrator
      //  -2 checks that they are a superadministrator
      //field2 represents whether or not this is a positive
      //  or a negative check.  ie, if field1=0, field2=1: 
      //  check that the user is logged in.  if field1=0,
      //  field2=2, check that no user is logged in.

      if ($line["field1"] == 0) $passed=isLoggedOn();
      else $passed = $this->checkIfUserCanAccess($line["field1"],isLoggedOn());

      if ($line['field2']==2) $passed = !$passed;
      return $passed;
   }

   function checkSessionRule ($line) {
      $passed=false;
      //field1 negates
      //field2 checks if a parameter is set in the session
      //field3 (if set) checks the value of that session key

      if ($line["field2"]!=null && isset($_SESSION[$line["field2"]])) {
         if ($line['field3'] == null) $passed=true;
         else if (0==strcmp($_SESSION[$line["field2"]],$line['field3'])) $passed=true;
      }

      if ($line['field1'] == 2) $passed = !$passed;

      return $passed;
   }

   function checkViewRule ($line) {
      $passed=false;
      //field1 1=view, 2=action
      //field2 view/action name
      //field3 number of times to be viewed
      //field4 negates

      if ($line["field1"]==2) {
         if ($_SESSION['cmsaction'][$line['field2']]>=$line['field3']) $passed=true;
      }
      else {
         if ($_SESSION['cmsview'][$line['field2']]>=$line['field3']) $passed=true;
      }

      if ($line['field4'] == 2) $passed = !$passed;

      return $passed;
   }

   function checkSearchRule ($line) {
      $passed=true;
      //print "<br>made it here!  field1: ".$line["field1"]." field2: ".$line["field2"]." field3: ".$line["field3"]." field4: ".$line["field4"]."<br>";
      //field1 represents survey_id
      //field2 represents search param
      //field3 represents search value
      //field4 represents relationship (< > == !=)
      if ($line["field1"] != NULL) {
         if ($_SESSION['ssn_survey'][$line["field1"]]>0) {
            if ($line["field2"] != NULL) {
               if ($_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]]>0) {
                  if ($line["field3"] != NULL) {
                     if (0==strcmp($line["field4"],"==") &&
                         0==strcmp($_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]."_value"],$line["field3"])){
                        $passed=true;
                     }
                     else if (0==strcmp($line["field4"],"!=") &&
                         0!=strcmp($_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]."_value"],$line["field3"])) {
                        $passed=true;
                     }
                     else if (0==strcmp($line["field4"],">") &&
                         $_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]."_value"] > $line["field3"]) {
                        $passed=true;
                     }
                     else if (0==strcmp($line["field4"],"<") &&
                         $_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]."_value"] <= $line["field3"]) {
                        $passed=true;
                     }
                     else if (0==strcmp($line["field4"],"contains") &&
                         strpos($_SESSION['ssn_survey'][$line["field1"]."_".$line["field2"]."_value"],$line["field3"])!== false) {
                        $passed=true;
                     }
                     else $passed=false;
                  }
               }
               else $passed = false;
            }
         }
         else $passed = false;
      }
      return $passed;
   }

   function checkThemeRules($theme){
      $themeArr = explode(",",$theme);
      $newTheme = "";
      for ($i=0; $i<count($themeArr); $i++) {
         if ($theme!=0) {
            $rules = $this->getThemeRules($themeArr[$i]);
            if ($rules==NULL || count($rules)<1) $newTheme .=",".$themeArr[$i];
            else {
               $passed=true;
               for ($j=0; $j<count($rules); $j++) {
                  $line = $rules[$j];
                  if (0==strcmp($line['ruletype'],"SEARCH")) {
                     if (!$this->checkSearchRule($line)) {
                        $passed=false;
                        break;
                     }
                  }
                  else if (0==strcmp($line['ruletype'],"SESSION")) {
                     if (!$this->checkSessionRule($line)) {
                        $passed=false;
                        break;
                     }
                  }
                  else if (0==strcmp($line['ruletype'],"PRIVACY")) {
                     if (!$this->checkPrivacyRule($line)) {
                        $passed=false;
                        break;
                     }
                  }
                  else if (0==strcmp($line['ruletype'],"VIEW")) {
                     if (!$this->checkViewRule($line)) {
                        $passed=false;
                        break;
                     }
                  }
               }
               if ($passed) $newTheme .=",".$themeArr[$i];
            }
         }
      }
      return substr($newTheme,1);
   }

  function getThemes() {
     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM cmstheme;";
     $results = $dbLink->queryGetResults($query);
     return $results;
  }

   function getThemeById($themeid) {
      if ($themeid == 0){
         $result['themename'] = "Default";
         $result['priority'] = "0";
         $result['themeid'] = "0";
         $result['startday'] = "1";
         $result['endday'] = "383";
         $result['status'] = "ACTIVE";
         return $result;
      }

     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM cmstheme where themeid='".$themeid."';";
     $results = $dbLink->queryGetResults($query);
     return $results[0];
   }

  function getCurrentTheme() {
     $theme = 0;
      //Clear the cache if the parameter is sent in, or a certain amount of time has passed in seconds
      if (getParameter("overridetheme")!=NULL || getParameter("clearcache")==1) $this->clearSessionCache();
      else if (isset($_SESSION['CurrentThemeTime']) &&(time() - $_SESSION['CurrentThemeTime']) > (7*60)) $this->clearSessionCache();

     if (isset($_SESSION['CurrentTheme'])) {
        $theme = $_SESSION['CurrentTheme'];
     } else {
        $dbLink = new MYSQLAccess;
        $currentd = date("j");
        $currentm = date("n");
        $currentday = ($currentm-1)*32 + $currentd;

        $query = "SELECT * FROM cmstheme WHERE status='ACTIVE' AND (";
        $query .= $currentday."=startday OR ".$currentday."=endday ";
        $query .= "OR (startday < ".$currentday." AND ".$currentday." < endday) ";
        $query .= "OR (".$currentday." < startday AND ".$currentday." < endday AND endday < startday) ";
        $query .= ") ORDER BY priority ASC;";
        $results = $dbLink->queryGetResults($query);
        $theme = "0";
        for ($i=0; $i<count($results); $i++) $theme = $results[$i]['themeid'].",".$theme;
        if (getParameter("overridetheme")!=NULL) $theme = getParameter("overridetheme").",".$theme;
        $_SESSION['CurrentTheme'] = $theme;
        $_SESSION['CurrentThemeTime'] = time();
     }
      //check rules for the list of themes...
      if ((!isset($_SESSION['CurrentThemeAfterRules'])) || (isset($_SESSION['CurrentThemeAfterRulesTime']) && (time() - $_SESSION['CurrentThemeAfterRulesTime']) > -1)) {
         $_SESSION['CurrentThemeAfterRulesTime'] = time();
         $theme = $this->checkThemeRules($theme);
         if (!isset($_SESSION['CurrentThemeAfterRules']) || 0!=strcmp($_SESSION['CurrentThemeAfterRules'],$theme)) { 
            $_SESSION['CurrentThemeAfterRules'] = $theme;
         }
      } else {
         $theme = $_SESSION['CurrentThemeAfterRules'];
      }

     //if ($GLOBALS['printstuff']) print "getCurrentTheme() Current theme: ".$theme."<BR>";
     //print "\n<!-- getCurrentTheme() Current theme: ".$theme." -->\n";
     return $theme;
  }

  function findNames($searchtxt){
      $dbLink = new MYSQLAccess;     
      $query = "SELECT name FROM globals WHERE LOWER(name) LIKE '%".strtolower($searchtxt)."%' ORDER BY name;";
      $results = $dbLink->queryGetResults($query);
      return $results;
  }

  function getValue ($name) {
     $theme = $this->getCurrentTheme();
     if (!isset($_SESSION['CurrentTheme.sys_prop'][$name][$theme])) {
         $value = $this->getValueTheme($name,$theme);
         $_SESSION['CurrentTheme.sys_prop'][$name][$theme] = $value;
     }
     return $_SESSION['CurrentTheme.sys_prop'][$name][$theme];
  }
  
  function getValueTheme($name, $theme) {
     $ctx = new Context();
     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM globals g, cmstheme t WHERE g.name='$name' AND ".$ctx->getSiteSQL("g.siteid")." AND g.themeid=t.themeid AND (".$this->getThemeClause($theme,"g.themeid").") ORDER BY t.priority DESC;";
     //if ($GLOBALS['printstuff']) print "getValueTheme query: ".$query."<BR>";
     $results = $dbLink->queryGetResults($query);
      if ($results==NULL || count($results)<1) {
         $query = "SELECT * from globals WHERE name='".$name."' AND ".$ctx->getSiteSQL()." AND themeid=0;";
         $results = $dbLink->queryGetResults($query);
         //print "<br><br>version query: ".$query."<br><br>";
      }
      $val = NULL;
      if($results!=NULL && count($results)>0) $val = $results[0]['value'];
     return $val;
  }

   function getSystemProperties ($themeid=-1,$sortby="name") {
     $ctx = new Context();
     $sitearr = $ctx->getSiteContext(); 
      $whereclause = " WHERE siteid=".$sitearr[0]['siteid'];
      if ($themeid != -1) $whereclause .= " AND themeid=".$themeid;
      $orderby = " ORDER BY ".$sortby;
      $query = "SELECT * from globals".$whereclause.$orderby.";";
      $dbLink = new MYSQLAccess;     
      $results = $dbLink->queryGetResults($query);
      return $results;
   }

  function removeValue ($name, $themeid) {
     $ctx = new Context();
     $sitearr = $ctx->getSiteContext(); 

     $dbLink = new MYSQLAccess;     
     $query = "DELETE FROM globals WHERE siteid='".$sitearr[0]['siteid']."' AND name='".$name."' AND themeid=".$themeid.";";
      $dbLink->delete($query);
  }

  function setValueTheme ($name, $value, $theme) {
     if ($GLOBALS['printstuff']) print "name: ".$name." value: ".$value." theme: ".$theme."<BR>";
     
     $ctx = new Context();
     $sitearr = $ctx->getSiteContext(); 

     $runUpdate = FALSE;
     $dbLink = new MYSQLAccess;     
     $query = "SELECT * FROM globals WHERE siteid='".$sitearr[0]['siteid']."' AND name='$name' and themeid='".$theme."';";
     $results = $dbLink->queryGetResults($query);
     if ($results!=NULL && count($results)> 0) $runUpdate=TRUE;
     if ($runUpdate) {
        $query = "UPDATE globals SET value='$value' WHERE siteid='".$sitearr[0]['siteid']."' AND name='$name' AND themeid='$theme';";
         if ($GLOBALS['printstuff']) print "query: ".$query."<BR>";
        $dbLink->update($query);
     } else {
        $query = "INSERT INTO globals (name, value, themeid, siteid) VALUES ('$name','$value','$theme','".$sitearr[0]['siteid']."');";
         if ($GLOBALS['printstuff']) print "query: ".$query."<BR>";
        $dbLink->insert($query);
     }
      $_SESSION['CurrentTheme.sys_prop'][$name][$theme] = $value;
  }

   function clearSessionCache(){
      $prefix = "CurrentTheme";
      foreach($_SESSION as $key => $value) {
         if (0==strcmp($prefix,substr($key,0,strlen($prefix)))) unset($_SESSION[$key]);
      }
   }
  
  //---------- shortcuts ----------------------

   function createURL($cmsfver) {
      $version_id = "_jsf_";
      $url = $GLOBALS['contentDir'].$cmsfver['dir'].$cmsfver['filename'].$version_id.$cmsfver['version'].$cmsfver['extension'];
      return $url;
   }

  function getViewShortcut($name) {
     if (isset($_SESSION["CurrentTheme.sc.".$name.".filename"])) {
        $obj = $this->getAllSCSessionValues("CurrentTheme.sc.".$name.".");
     }
     else {
         $obj = $this->getFileQuick($name,$this->getCurrentTheme());
         $obj['url'] = $this->createURL($obj);
         $this->setAllSCSessionValues($obj,"CurrentTheme.sc.".$name.".");
     }
     return $obj;
  }

  function getAllShortcuts($contenttype=1){
      $query = "SELECT * FROM cmsfiles WHERE contenttype='".$contenttype."' ORDER BY filename;";
      $dbLink = new MYSQLAccess;     
      $results = $dbLink->queryGetResults($query);
      return $results;
  }

  function setAllSCSessionValues($obj,$sVal) {
     $_SESSION[$sVal."cmsid"]       = $obj['cmsid']    ;
     $_SESSION[$sVal."url"]         = $obj['url']      ;
     $_SESSION[$sVal."dir"]         = $obj['dir']      ;
     $_SESSION[$sVal."filename"]    = $obj['filename'] ;
     $_SESSION[$sVal."extension"]   = $obj['extension'];
     $_SESSION[$sVal."filetype"]    = $obj['filetype'] ;
     $_SESSION[$sVal."privacy"]     = $obj['privacy'] ;
     $_SESSION[$sVal."metakw"]      = $obj['metakw']   ;
     $_SESSION[$sVal."metadescr"]   = $obj['metadescr'];
     $_SESSION[$sVal."track"]       = $obj['track']    ;
     $_SESSION[$sVal."title"]       = $obj['title']    ;
     $_SESSION[$sVal."search"]      = $obj['search']   ;
     $_SESSION[$sVal."version"]     = $obj['version']  ;
     $_SESSION[$sVal."siteid"]      = $obj['siteid']   ;
     $_SESSION[$sVal."theme"]       = $obj['theme']    ;
     $_SESSION[$sVal."cachetime"]   = $obj['cachetime'];
  }

  function getAllSCSessionValues($sVal) {
     $obj['cmsid']       = $_SESSION[$sVal."cmsid"]    ;
     $obj['url']         = $_SESSION[$sVal."url"]      ;
     $obj['dir']         = $_SESSION[$sVal."dir"]      ;
     $obj['filename']    = $_SESSION[$sVal."filename"] ;
     $obj['extension']   = $_SESSION[$sVal."extension"];
     $obj['filetype']    = $_SESSION[$sVal."filetype"] ;
     $obj['privacy']     = $_SESSION[$sVal."privacy"] ;
     $obj['metakw']      = $_SESSION[$sVal."metakw"]   ;
     $obj['metadescr']   = $_SESSION[$sVal."metadescr"];
     $obj['track']       = $_SESSION[$sVal."track"]    ;
     $obj['title']       = $_SESSION[$sVal."title"]    ;
     $obj['search']      = $_SESSION[$sVal."search"]   ;
     $obj['version']     = $_SESSION[$sVal."version"]  ;
     $obj['siteid']      = $_SESSION[$sVal."siteid"]   ;
     $obj['theme']       = $_SESSION[$sVal."theme"]    ;
     $obj['cachetime']   = $_SESSION[$sVal."cachetime"];
     return $obj;
  }
  
}


// 4 functions required:
// controller(): to read input of admin request to manipulate an object
// getAdminPHPInclude(): to get the admin screen for manipulating an object
// getHTML(cmsid,version): to get the HTML rendered for this object
// versionCopy(cmsid,version,tocmsid,toversion) copies contents of one object to another
class HTMLWidget {
   function controller($vars=NULL){
      $ss = new Version();
      $cmsid = $vars['cmsid'];
      if ($cmsid==NULL) $cmsid = getParameter("cmsid");
      $vars['cmsid'] = $cmsid;
      $version = $vars['version'];
      if ($version==NULL) $version = getParameter("version");
      $vars['version'] = $version;
      $owner = $_SESSION['s_user']['emailAddress'];
      $vars['owner'] = $owner;
      $contents = getParameter("contents");

      $tempfilename=NULL;
      if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
         $tempfilename=$_FILES['userfile']['tmp_name'];
         $contents = NULL;
         $extension = $ss->getExtension($_FILES['userfile']['name']);
      }

      if (getParameter("editcontents")==1) {
         if (!$ss->updateVersionContents($cmsid,$version,$owner,$contents,$tempfilename)) {
            $vars['error'] = "You cannot update a file version owned by another user.  Please create a new version.<br>";
         } else {
            $vars['msg'] = "File updated successfully.<br>";
            $vars['update'] = 1;
         }
      }
      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_html.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      //print "\n<!-- getting html cmsid: ".$cmsid." version: ".$version." -->\n";
      $template = new Template();
      $ss = new Version();
      $str = "";

      $cmsfver = $ss->getVersionedFile($cmsid,$version);
      $url = $ss->createURL($cmsfver);
      //Caching logic if the page caches
      if ($cmsfver['cachetime']>0 && !$withoutsubs) {
         $cachefilename = "cms-".$cmsfver['filename']."-cache.html"; 
         $cachefile = $GLOBALS['cacheFolder'].$cachefilename; 
         if (file_exists($cachefile) && ($results['cachetime']>=9999999 || ((time()-$cmsfver['cachetime'])<filemtime($cachefile)))) { 
            //print "\n<!-- getting cached html: ".$cachefile." -->\n";
            $fd = fopen ($cachefile, "r");
            $str = fread ($fd, filesize ($cachefile));
            fclose ($fd);
         } else { 
            $str = $template->getFileWithoutSub($GLOBALS['rootDir'].$url);
            $str = $template->doSubstitutions($str,$sub);
            $fp = fopen($cachefile, 'w+');
            if($fp==false) die("unable to create file");
            fwrite($fp, $str); 
            fclose($fp); 
         }
      } else {
         $str = $template->getFileWithoutSub($GLOBALS['rootDir'].$url);
         if(!$withoutsubs) $str = $template->doSubstitutions($str,$sub);
      }
      

      return $str;
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){
      $version_id = "_jsf_";
      $ss = new Version();
      $cmsfile = $ss->getFileById($cmsid);
      $fileInfo = $ss->getVersionByShortname($cmsfile['filename'],$version);
      $contents = $fileInfo['contents'];
   
      $temp = new Template();
      $filename = $GLOBALS['rootDir'].$GLOBALS['contentDir'].$fileInfo['dir'].$fileInfo['filename'].$version_id.$toversion.$fileInfo['extension'];
      $temp->saveFile($filename,$contents);
   }
}

class BinaryWidget {
   function controller($vars=NULL){

      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_binary.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      $template = new Template();
      $str = "";
      $ss = new Version();
      $cmsfver = $ss->getVersionedFile($cmsid,$version);
      $url = $ss->createURL($cmsfver);
      if (0==strcmp($cmsfver['extension'],".jpg") || 0==strcmp($cmsfver['extension'],".jpeg") || 0==strcmp($cmsfver['extension'],".gif") || 0==strcmp($cmsfver['extension'],".png")) {
         $str = "<img src=\"".getBaseURL().$url."\" alt=\"".$cmsfver['title']."\" border=\"0\">";
      } else {
         $str = "<a href=\"".getBaseURL().$url."\">".$cmsfver['title']."</a>";
      }

      return $str;
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){

   }
}

class SurveyWidget {
   function controller($vars=NULL){

      $wdObj = new WebsiteData();
      if (getParameter("updatestatus")==1) {
         $wdObj->updateWDStatus(getParameter("update_wd_id"), getParameter("status"));
      }
      if (getParameter("updatesurvey")==1) {
         $wdObj->updateWebData(getParameter("wd_id"),getParameter("name"),getParameter("info"), getParameter("privatesrvy"), getParameter("adminemail"), NULL, 1, getParameter("emailresults"),NULL,getParameter("status"),NULL,$starttime,$endtime,$field1,convertString(getParameter("field2")),convertString(getParameter("field3")),convertString(getParameter("field4")),convertString(getParameter("shortname")),convertString(getParameter("password")));
      }
      if (getParameter("addquestion")==1) {
         $wd_id = getParameter("wd_id");
         $parent_s = $wdObj->getDataSections($wd_id);
         $wdObj->addField($wd_id, $parent_s[0]['section'], NULL, getParameter("label"), getParameter("question"), getParameter("field_type"), getParameter("sequence"), getParameter("privacy"), getParameter("vheader"), getParameter("defaultval"));
      }
      if (getParameter("newFieldRel")==1) {
         $wd_id = getParameter("wd_id");
         $rel_type = getParameter("rel_type");
         $fid1 = getParameter("fid1");
         $fid2 = getParameter("fid2");
         $f1value = getParameter("f1value");
         $wdObj->newFieldRel($wd_id,$rel_type,$fid1,$fid2,$f1value);
      }
      if (getParameter("deleteFieldRel")==1) {
         $rel_id = getParameter("rel_id");
         $wdObj->removeFieldRel($rel_id);
      }

      $field_id = getParameter("field_id");
      if ($field_id != NULL) {
         $wd_id = getParameter("wd_id");
         $parent_s = getParameter("parent_s");
         $sequence = getParameter("sequence");
         $question = convertString(getParameter("question"));
         $field_type = getParameter("field_type");
         $label = convertString(getParameter("label"));
         $privacy = getParameter("privacy");
         $defaultval = convertString(getParameter("defaultval"));

         $deleteThisField = getParameter("Delete");
         if (0==strcmp($deleteThisField,"Delete")) $wdObj->deleteField($wd_id, $field_id);
         else $wdObj->updateField($wd_id, $parent_s, $field_id, $label, $question, $field_type, $sequence, $privacy, $header,$defaultval);
      }

      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_survey.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      //error_reporting(E_ALL);
      $template = new Template();
      $str = "";
      $wdObj = new WebsiteData();
      $webdata = $wdObj->getWebDataByName("CMS_Survey_".$cmsid."_".$version);
      if ($webdata['wd_id']!=NULL) {
         $extraFields = "";
         ob_start();
         $wdObj->printWebData($webdata['wd_id'], NULL, isLoggedOn(), NULL, NULL, "Short", NULL, getBaseURL()."jsfcode/controller.php?view=".getParameter("view"), TRUE,NULL,NULL,FALSE,$extraFields);
         $str = ob_get_contents();
         ob_end_clean();
      }
      return $str;
      //return "**chj";
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){
      $wdObj = new WebsiteData();
      $webdata = $wdObj->getWebDataByName("CMS_Survey_".$tocmsid."_".$toversion);
      if ($webdata['wd_id']==NULL) {
         $webdata = $wdObj->getWebDataByName("CMS_Survey_".$cmsid."_".$version);
         $wd_id = $webdata['wd_id'];
         $new_wd_id = $wdObj->copyWebData($wd_id);
         $wdObj->updateWebData($new_wd_id, "CMS_Survey_".$tocmsid."_".$toversion, $webdata['info'], $webdata['privatesrvy'], $webdata['adminemail'], $webdata['filename'], $webdata['saveresults'], $webdata['emailresults'],$webdata['glossaryid'], $webdata['status'], $tocmsid);
      }
   }
}

class TemplateWidget {
   function controller($vars=NULL){
      if (getParameter("editcontents")==1) {
         $divids = getParameter("divids");
         if ($divids!=NULL && is_array($divids) && count($divids)>0) {
            for ($i=0; $i<count($divids); $i++) {
               $bgcolor = getParameter("div".$divids[$i]."_bgcolor");
               $bgcolor2 = getParameter("div".$divids[$i]."_bgcolor2");
               if ($bgcolor2!=NULL && 0==strcmp($bgcolor,$bgcolor2)) $bgcolor=NULL;
               $bgimage = getParameter("div".$divids[$i]."_bgimage");
               $bgimage2 = getParameter("div".$divids[$i]."_bgimage2");
               if ($bgimage2!=NULL && 0==strcmp($bgimage,$bgimage2)) $bgimage=NULL;
               $contentref = getParameter("div".$divids[$i]."_contentref");
               $contentref2 = getParameter("div".$divids[$i]."_contentref2");
               if ($contentref2!=NULL && 0==strcmp($contentref,$contentref2)) $contentref=NULL;
               $url = getParameter("div".$divids[$i]."_url");
               $url2 = getParameter("div".$divids[$i]."_url2");
               if ($url2!=NULL && 0==strcmp($url,$url2)) $url=NULL;
               print "\n<!-- controller() updating div iteration: ".$i." divid: ".$divids[$i]." bgcolor: ".$bgcolor." bgcolor2: ".$bgcolor2." bgimage: ".$bgimage." bgimage2: ".$bgimage2." contentref: ".$contentref." contentref2: ".$contentref2." url: ".$url." url2: ".$url2." -->\n";
               $this->updateTemplateDesignDiv($divids[$i],getParameter("cmsid"),getParameter("version"),$bgcolor,$bgimage,$contentref,$url);
            }
         }
      } else if (getParameter("setparent")==1) {
         $this->addTemplateDesignDiv(-1,getParameter("cmsid"),-1,getParameter("tempcmsid"));
      }
      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_template.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      $template = new Template();
      $str = "";
      $divs = $this->getTemplateDesignDivs($cmsid,$version);
      $totalwidth = 0;
      $totalheight = 0;
      for ($i=0; $i<count($divs); $i++) {
         $tempw = $divs[$i]['divleft'] + $divs[$i]['divwidth'];
         $temph = $divs[$i]['divtop'] + $divs[$i]['divheight'];
         if ($totalwidth<$tempw) $totalwidth = $tempw;
         if ($totalheight<$temph) $totalheight = $temph;
         $str .=  "<div class=\"draggable\" data-divid=\"".$divs[$i]['divid']."\" data-index=\"".($i+1)."\" ";
         $divpostfix = $divs[$i]['label'];
         if ($divpostfix==NULL) $divpostfix=$divs[$i]['divid'];
         $str .= "id=\"cms".$divs[$i]['filename']."_".$divpostfix."\" ";
         $str .=  "style=\"";
         if ($divs[$i]['style']!=NULL) {
            $str .= $divs[$i]['style'];
         } else {
            $str .=  "position:absolute;";

            if ($divs[$i]['fixed']==1) $str .=  "overflow:hidden;";
            else $str .=  "overflow:auto;";

            $str .=  "top:".$divs[$i]['divtop']."px;";
            $str .=  "left:".$divs[$i]['divleft']."px;";
            $str .=  "width:".$divs[$i]['divwidth']."px;";
            $str .=  "height:".$divs[$i]['divheight']."px;";
            if ($divs[$i]['borderw']==NULL) $divs[$i]['borderw']=0; 
            if ($divs[$i]['borderc']==NULL) $divs[$i]['borderc']="#FFFFFF"; 
            $str .=  "border:".$divs[$i]['borderw']."px solid ".$divs[$i]['borderc'].";";
            if ($divs[$i]['bgcolor']!=NULL) $str .=  "background-color:".$divs[$i]['bgcolor'].";";
            if ($divs[$i]['bgimage']!=NULL) $str .=  "background-image: URL(".$template->doSubstitutions($divs[$i]['bgimage'],$sub).");";
            if ($divs[$i]['zindex']==NULL) $divs[$i]['zindex']=1; 
            $str .=  "z-index:".$divs[$i]['zindex'].";";
            if ($divs[$i]['url']!=NULL) $str .= "cursor:pointer;";
         }
         $str .=  "\" ";

         if ($divs[$i]['url']!=NULL) {
            $newwindow = getParameter("newwindow");
            $url = $template->doSubstitutions($divs[$i]['url'],$sub);
            $action = "window.location.href='".$url."';";
            if ($newwindow==1) $action="window.open('".$url."','_newtab');";
            $str .= "onClick=\"".$action."\" ";
         }

         $str .= ">\n";

         $shortname = trim($divs[$i]['contentref']);
         if ($shortname!=NULL && strpos($shortname,"%%%")===FALSE) {
            $shortname = "%%%CMS_".$shortname."_CMS%%%";
         }
         $str .= $template->doSubstitutions($shortname,$sub);

         $str .= "</div>";
      }
      return "<div style=\"position:relative;text-align:left;height:".$totalheight."px;width:".$totalwidth."px;\">".$str."</div>";
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){
      $sql = new MYSQLaccess();
      $query = "SELECT * FROM cmsftemp WHERE cmsid=".$cmsid." AND version=".$version.";";
      $divs = $sql->queryGetResults($query);

      for ($i=0; $i<count($divs); $i++) {
         $this->addTemplateDesignDiv($divs[$i]['divid'],$tocmsid,$toversion,$divs[$i]['tempcmsid']);
         $this->updateTemplateDesignDiv($divs[$i]['divid'],$tocmsid,$toversion,$divs[$i]['bgcolor'],$divs[$i]['bgimage'],$divs[$i]['contentref'],$divs[$i]['url']);
      }
   }


   //--------------------------------------------
   // Utility methods below...
   //--------------------------------------------
   function getParent($cmsid){
      if ($cmsid==NULL) return NULL;
      $sql = new MYSQLaccess();
      $query = "SELECT tempcmsid FROM cmsftemp WHERE cmsid=".$cmsid." AND divid=-1;";
      $results = $sql->queryGetResults($query);
      $parent = $results[0]['tempcmsid'];
      return $parent;
   }

   function getTemplateDesignDivs($cmsid,$version){
      //print "\n<!-- getTemplateDesigndivs(cmsid= ".$cmsid.", version=".$version.") -->\n";
      $sql = new MYSQLaccess();
      $ss = new Version();

      $parent = $this->getParent($cmsid);
      $cmsfile = $ss->getFileByIdQuick($parent);
      $wdg = new DesignWidget();
      $divs = $wdg->getDesignDivs($cmsfile['cmsid'],$cmsfile['version']);
      for ($i=0; $i<count($divs); $i++) {
         $origdivid = $divs[$i]['origdivid'];
         if ($origdivid==-1 || $origdivid==NULL) $origdivid = $divs[$i]['divid'];
         $query = "SELECT t.*, f.filename FROM cmsftemp t, cmsfiles f WHERE t.cmsid=f.cmsid AND t.divid=".$origdivid." AND t.cmsid=".$cmsid." AND t.version=".$version.";";
         $results = $sql->queryGetResults($query);
         if ($results==NULL || count($results)<1 || $results[0]['divid']==NULL) {
            $this->addTemplateDesignDiv($origdivid,$cmsid,$version,$parent);
         } else {
            if ($results[0]['bgimage']!=NULL) {
               $divs[$i]['bgimage2'] = $divs[$i]['bgimage'];
               $divs[$i]['bgimage'] = $results[0]['bgimage'];
            }
            if ($results[0]['bgcolor']!=NULL) {
               $divs[$i]['bgcolor2'] = $divs[$i]['bgcolor'];
               $divs[$i]['bgcolor'] = $results[0]['bgcolor'];
            }
            if ($results[0]['contentref']!=NULL) {
               $divs[$i]['contentref2'] = $divs[$i]['contentref'];
               $divs[$i]['contentref'] = $results[0]['contentref'];
            }
            if ($results[0]['url']!=NULL) {
               $divs[$i]['url2'] = $divs[$i]['url'];
               $divs[$i]['url'] = $results[0]['url'];
            }
            if ($results[0]['filename']!=NULL) {
               $divs[$i]['filename2'] = $divs[$i]['filename'];
               $divs[$i]['filename'] = $results[0]['filename'];
            }
         }
         $divs[$i]['divid'] = $origdivid;
         $divs[$i]['cmsid'] = $cmsid;
         $divs[$i]['version'] = $version;
      }

      return $divs;
   }

   function addTemplateDesignDiv($divid,$cmsid,$version,$tempcmsid) {
      if ($cmsid==NULL || $version==NULL) return NULL;
      $query = "INSERT INTO cmsftemp (divid,cmsid,version,created,tempcmsid) VALUES (".$divid.", ".$cmsid.",".$version.",NOW(),".$tempcmsid.");";
      $sql = new MYSQLaccess();
      $sql->insert($query);
   }

   function updateTemplateDesignDiv($divid,$cmsid,$version,$bgcolor=NULL,$bgimage=NULL,$contentref=NULL,$url=NULL){
      //print "\n<!-- divid: ".$divid." cmsid: ".$cmsid." version: ".$version." bgcolor: ".$bgcolor." bgimage: ".$bgimage." contentref: ".$contentref." url: ".$url." -->\n";
      if ($divid==NULL) return NULL;
      $sql = new MYSQLaccess();
      //$query = "SELECT * FROM cmsftemp WHERE divid=".$divid." AND cmsid=".$cmsid." AND version=".$version.";";
      //$results = $sql->queryGetResults($query);
      //if ($results==NULL || count($results)<1) {
      //   $query = "INSERT INTO cmsftemp (divid, cmsid, version) VALUES (".$divid.", ".$cmsid.", ".$version.");";
      //   $sql->insert($query);
      //}

      $query = "UPDATE cmsftemp SET ";
      $query .= "bgcolor='".$bgcolor."', ";
      $query .= "bgimage='".$bgimage."', ";
      $query .= "url='".$url."', ";
      $query .= "contentref='".convertString($contentref)."'";
      $query .= " WHERE divid=".$divid." AND cmsid=".$cmsid." AND version=".$version.";";
      //print "\n<!-- Query: ".$query." -->\n";
      $sql->update($query);
   }

}

class DesignWidget {
   function controller($vars=NULL){
      if (getParameter("deletediv")==1) {
         $this->removeDesignDiv(getParameter("divid"));
      } else if (getParameter("editcontents")==1) {
         if (0==strcmp(getParameter("designsubmit"),"New section")) $this->addDesignDiv($vars['cmsid'],$vars['version']);
         $divids = getParameter("divids");
         if ($divids!=NULL && is_array($divids) && count($divids)>0) {
            for ($i=0; $i<count($divids); $i++) {
               $this->updateDesignDiv($divids[$i],getParameter("div".$divids[$i]."_divtop"),getParameter("div".$divids[$i]."_divleft"),getParameter("div".$divids[$i]."_divwidth"),getParameter("div".$divids[$i]."_divheight"),getParameter("div".$divids[$i]."_bgcolor"),getParameter("div".$divids[$i]."_bgimage"),getParameter("div".$divids[$i]."_borderw"),getParameter("div".$divids[$i]."_borderc"),getParameter("div".$divids[$i]."_zindex"),getParameter("div".$divids[$i]."_contentref"),getParameter("div".$divids[$i]."_url"),getParameter("div".$divids[$i]."_label"),getParameter("div".$divids[$i]."_status"),getParameter("div".$divids[$i]."_fixed"));
            }
         }
      } else if (getParameter("editdivstyle")==1) {
         $divid = trim(getParameter("divid"));
         $style = trim(getParameter("style"));
         //print "divid: ".$divid." style: ".$style;
         $this->updatestyleDesignDiv($divid,$style);
      }
      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_layout.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      $template = new Template();
      $str = "";
      $divs = $this->getDesignDivs($cmsid,$version,1);
      $totalwidth = 0;
      $totalheight = 0;
      for ($i=0; $i<count($divs); $i++) {
         $tempw = $divs[$i]['divleft'] + $divs[$i]['divwidth'];
         $temph = $divs[$i]['divtop'] + $divs[$i]['divheight'];
         if ($totalwidth<$tempw) $totalwidth = $tempw;
         if ($totalheight<$temph) $totalheight = $temph;
         $divpostfix = $divs[$i]['label'];
         if ($divpostfix==NULL) $divpostfix=$divs[$i]['divid'];
         $str .=  "<div class=\"draggable\" data-divid=\"".$divs[$i]['divid']."\" data-index=\"".($i+1)."\" id=\"cms".$divs[$i]['filename']."_".$divpostfix."\" ";
         $str .=  "style=\"";
         if ($divs[$i]['style']!=NULL) {
            $str .= $divs[$i]['style'];
         } else {
            $str .=  "position:absolute;";

            if ($divs[$i]['fixed']==1) $str .=  "overflow:hidden;";
            else $str .=  "overflow:auto;";

            $str .=  "top:".$divs[$i]['divtop']."px;";
            $str .=  "left:".$divs[$i]['divleft']."px;";
            $str .=  "width:".$divs[$i]['divwidth']."px;";
            $str .=  "height:".$divs[$i]['divheight']."px;";
            if ($divs[$i]['borderw']==NULL) $divs[$i]['borderw']=0; 
            if ($divs[$i]['borderc']==NULL) $divs[$i]['borderc']="#FFFFFF"; 
            $str .=  "border:".$divs[$i]['borderw']."px solid ".$divs[$i]['borderc'].";";
            if ($divs[$i]['bgcolor']!=NULL) $str .=  "background-color:".$divs[$i]['bgcolor'].";";
            if ($divs[$i]['bgimage']!=NULL) $str .=  "background-image: URL(".$template->doSubstitutions($divs[$i]['bgimage'],$sub).");";
            if ($divs[$i]['zindex']==NULL) $divs[$i]['zindex']=1; 
            $str .=  "z-index:".$divs[$i]['zindex'].";";
            if ($divs[$i]['url']!=NULL) $str .= "cursor:pointer;";
         }
         $str .=  "\" ";
         if ($divs[$i]['url']!=NULL) {
            $newwindow = getParameter("newwindow");
            $url = $template->doSubstitutions($divs[$i]['url'],$sub);
            $action = "window.location.href='".$url."';";
            if ($newwindow==1) $action="window.open('".$url."','_newtab');";
            $str .= "onClick=\"".$action."\" ";
         }
         $str .= ">\n";

         $shortname = trim($divs[$i]['contentref']);
         if ($shortname!=NULL && strpos($shortname,"%%%")===FALSE) {
            $shortname = "%%%CMS_".$shortname."_CMS%%%";
         }
         $str .= $template->doSubstitutions($shortname,$sub);

         $str .= "</div>";
      }
      return "<div style=\"position:relative;text-align:left;height:".$totalheight."px;width:".$totalwidth."px;\">".$str."</div>";
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){
      $divs = $this->getDesignDivs($cmsid,$version);
      for ($i=0; $i<count($divs); $i++) {
         $origdivid = $divs[$i]['divid'];
         if ($divs[$i]['origdivid']!=NULL && $divs[$i]['origdivid']>0) $origdivid = $divs[$i]['origdivid'];
         $divid = $this->addDesignDiv($tocmsid,$toversion,$origdivid);
         $this->updateDesignDiv($divid,$divs[$i]['divtop'],$divs[$i]['divleft'],$divs[$i]['divwidth'],$divs[$i]['divheight'],$divs[$i]['bgcolor'],$divs[$i]['bgimage'],$divs[$i]['borderw'],$divs[$i]['borderc'],$divs[$i]['zindex'],$divs[$i]['contentref'],$divs[$i]['url'],$divs[$i]['label'],$divs[$i]['status'],$divs[$i]['fixed']);
      }
   }


   //--------------------------------------------
   // Utility methods below...
   //--------------------------------------------

   function getDesignDivs($cmsid,$version,$status=NULL){
      if ($cmsid==NULL || $version==NULL) return NULL;
      $query = "SELECT d.*, f.filename FROM cmsfdes d, cmsfiles f WHERE d.cmsid=f.cmsid AND d.cmsid=".$cmsid." AND d.version=".$version;
      if ($status!==NULL) $query .= " AND d.status=".$status;
      //$query .= " ORDER BY d.zindex DESC;";
      $query .= " ORDER BY d.divtop;";
      $sql = new MYSQLaccess();
      $results = $sql->queryGetResults($query);
      return $results;
   }

   function addDesignDiv($cmsid,$version,$origdivid=NULL) {
      if ($cmsid==NULL || $version==NULL) return NULL;
      if ($origdivid==NULL) $origdivid=-1;
      $query = "INSERT INTO cmsfdes (cmsid,version,created,borderw,borderc,bgcolor,divwidth,divheight,origdivid) VALUES (".$cmsid.",".$version.",NOW(),1,'#000000','#FFFFFF',200,200,".$origdivid.");";
      $sql = new MYSQLaccess();
      return $sql->insertGetValue($query);
   }

   function removeDesignDiv($divid) {
      if ($divid==NULL) return NULL;
      $query = "DELETE FROM cmsfdes WHERE divid=".$divid;
      $sql = new MYSQLaccess();
      $sql->delete($query);
   }

   function updateDesignDiv($divid,$divtop=0,$divleft=0,$divwidth=0,$divheight=0,$bgcolor=NULL,$bgimage=NULL,$borderw=0,$borderc=NULL,$zindex=1,$contentref=NULL,$url=NULL,$label=NULL,$status=NULL,$fixed=NULL){
      if ($divid==NULL) return NULL;
      $sql = new MYSQLaccess();
      if ($zindex==NULL) $zindex="1";
      if ($borderw==NULL) $borderw="0";
      if ($fixed==NULL) $fixed="0";
      if ($status===NULL || !is_numeric($status)) $status=0;
      $query = "UPDATE cmsfdes SET ";
      $query .= "divtop=".$divtop.", ";
      $query .= "divleft=".$divleft.", ";
      $query .= "divwidth=".$divwidth.", ";
      $query .= "divheight=".$divheight.", ";
      $query .= "bgcolor='".$bgcolor."', ";
      $query .= "bgimage='".$bgimage."', ";
      $query .= "borderw=".$borderw.", ";
      $query .= "borderc='".$borderc."', ";
      $query .= "url='".$url."', ";
      $query .= "label='".convertString($label)."', ";
      $query .= "status=".$status.", ";
      $query .= "zindex=".$zindex.", ";
      $query .= "fixed=".$fixed.", ";
      $query .= "contentref='".convertString($contentref)."'";
      $query .= " WHERE divid=".$divid.";";
      $sql->update($query);
   }

   function updatestyleDesignDiv($divid,$style=NULL){
      if ($divid==NULL) return NULL;
      $sql = new MYSQLaccess();
      $query = "UPDATE cmsfdes SET ";
      $query .= "style='".$style."'";
      $query .= " WHERE divid=".$divid.";";
      $sql->update($query);
   }

}

class UnknownWidget {
   function controller($vars=NULL){
      return $vars;
   }
   
   function getAdminPHPInclude(){
      return "showversionwidget_unkown.php";
   }

   function getHTML($cmsid,$version,$sub=NULL,$withoutsubs=FALSE){
      $str = "";
      return $str;
   }

   function versionCopy($cmsid,$version,$tocmsid,$toversion){
   }
}
?>
