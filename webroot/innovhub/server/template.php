<?php

//-----------------------------------------------------------------------------
// Template class
//
//-----------------------------------------------------------------------------
class Template
{
   function doPreLoadWork() {
      $refuserid = getParameter("refuserid");
      if ($refuserid!=NULL) $_SESSION['ssnrem']['refuserid']=$refuserid;

      //$secure = getParameter("secure");
      //if ($secure==1) $_SESSION['secure']=1;
      //else if ($secure==2) unset($_SESSION['secure']);
      //else if (isset($_SERVER['HTTPS'])) $_SESSION['secure']=1;

      if (isset($_SERVER['HTTPS'])) $_SESSION['secure']=1;
      else unset($_SESSION['secure']);

      $action  = getParameter("action");
      $view = getParameter("view");
      if ($action!=null) {
         if (isset($_SESSION["cmsaction"][$action])) $_SESSION["cmsaction"][$action] += 1;
         else $_SESSION["cmsaction"][$action] = 1;
      }
      if ($view!=null) {
         if (isset($_SESSION["cmsview"][$view])) $_SESSION["cmsview"][$view] += 1;
         else $_SESSION["cmsview"][$view] = 1;
      }
    
      $ua=new UserAcct();
      $ua->checkAutoLoginCookie();

      $s_siteid = getParameter("s_siteid");
      $ctx = new Context();
      if ($s_siteid != NULL) { 
         $ctx->setSiteContext($s_siteid);
      } else {
         $ctx->checkSiteCookie();
      }
   }

    function displayPage($title,$sub=null) {
      $this->displayPageExplicit(getParameter("view"),getParameter("page"),$title,$sub);
    }

   function displayViewOnlyExplicit($view) {
        $ss = new Version();
        $results = $ss->getViewShortcut($view);
        if ($results['title'] != null) $title = $results['title'];
        $sub['url'] = $results['url'];
        $sub['view'] = $results['filename'];
        $sub['metaKW'] = $results['metakw'];
        $sub['metaDESCR'] = $results['metadescr'];
        //$this->displayCacheFile($results,$sub);
        $text = $GLOBALS['configDir'] . $results['url'];
        $this->displayFile($text,NULL,NULL,NULL,$sub);
   }

    function displayPageExplicit($view,$page=null,$title=null,$sub=null,$ignoreborders=FALSE) {
      $results['url'] = $page;
      if ($results['url'] == NULL) {
        $ss = new Version();
        $results = $ss->getViewShortcut($view);
        if (!$ss->checkIfUserCanAccess($results['privacy'],isLoggedOn())) {
         $results = $ss->getViewShortcut($ss->getValue("noaccesstemplate"));
        }

        if ($results['title'] != null) $title = $results['title'];
        $sub['url'] = $results['url'];
        $sub['view'] = $results['filename'];
        $sub['metaKW'] = $this->doSubstitutions($results['metakw'],$sub);
        $sub['metaDESCR'] = $this->doSubstitutions($results['metadescr'],$sub);
      }
      
      if ($title!=NULL) $sub['title'] = $title;
      if ($GLOBALS['printstuff']) print "<br><font color=\"red\"><b>View page: ".$text."<br>Title: ".$title."</b></font><br>";
      if(!$ignoreborders) $this->getMainTop($title,$sub,$view);
      $continue=TRUE;
      $this->displayCacheFile($results,$title,$sub);
      if(!$ignoreborders) $this->getMainBottom($sub,$view);
    }
    
   function displayCacheFile ($cmsfile, $title, $sub) {
      $ss = new Version();
      $widgetname = $ss->getFileTypeObject($cmsfile['filetype']);
      $widgetClass = new $widgetname();

      $text = $GLOBALS['configDir'] . $cmsfile['url'];
      //print "\n<!-- chj***\n";
      //print_r($cmsfile);
      //print "\n-->\n";
      if ($cmsfile['track']==1) $this->trackItem($cmsfile['filename'],NULL,"JSF Content Initiated",$cmsfile['version']);

      //Caching logic if the page caches
      if ($cmsfile['cachetime']>0) {
         //print "\n<!-- cached file: ".$text."-->\n";
         $cachefilename = "cms-".$cmsfile['filename']."-cache.html"; 
         $cachefile = $GLOBALS['cacheFolder'].$cachefilename; 
         if (file_exists($cachefile) && ($cmsfile['cachetime']>=9999999 || ((time()-$cmsfile['cachetime'])<filemtime($cachefile)))) {
            print "\n<!-- cached content -->\n"; 
            include($cachefile); 
         } else { 
            ob_start();

            print $widgetClass->getHTML($cmsfile['cmsid'],$cmsfile['version'],$sub,FALSE);
            //***chj $this->displayFile($text,$title,NULL,NULL,$sub);

            $fp = fopen($cachefile, 'w+'); 
            if($fp==false) die("unable to create file");
            fwrite($fp, ob_get_contents()); 
            fclose($fp); 
            ob_end_flush();
         }
      } else {
         //print "\n<!-- real-time file: ".$text."-->\n";
         //***chj $this->displayFile($text,$title,NULL,NULL,$sub);
         print $widgetClass->getHTML($cmsfile['cmsid'],$cmsfile['version'],$sub,FALSE);
      }
   }

    function getMainTop($title,$sub=null, $view=NULL, $action=NULL) {
      $ss = new Version();
      $shortname = getParameter("topoverride");
      if ($shortname==NULL) {
         if ($view==NULL) $view = $action;
         if ($view==NULL) $view = getParameter("view");
         if ($view == NULL) $view  = getParameter("action");
         $shortname = $ss->getValue("top_".$view);
      }
      if ($shortname==NULL) $shortname = $ss->getValue("toptemplate");
      $results = $ss->getViewShortcut($shortname);
      //$filename = $GLOBALS['rootDir'].$results['url'];
      //if ($GLOBALS['printstuff']) print "getMainTop toptemplate: ".$filename."<BR><BR>";
      //$this->displayTop($title,$filename,$sub);
      $this->displayCacheFile($results,$title,$sub);
      //print "\n<!-- Current Theme: [".$ss->getCurrentTheme()."]. -->\n";
      //print "\n<!-- Current SESSION: \n";
      //print_r($_SESSION);
      //print "\n -->\n";
    }
    
    function getMainBottom($sub=null, $view=NULL, $action=NULL) {
      // get contents of a file into a string
      $ss = new Version();
      $shortname = getParameter("bottomoverride");
      if ($shortname==NULL) {
         if ($view==NULL) $view = $action;
         if ($view==NULL) $view = getParameter("view");
         if ($view == NULL) $view  = getParameter("action");
         $shortname = $ss->getValue("bottom_".$view);
      }
      if ($shortname==NULL) $shortname = $ss->getValue("bottomtemplate");
      $results = $ss->getViewShortcut($shortname);
      //$filename = $GLOBALS['rootDir'].$results['url'];
      //$this->displayBottom($filename,$sub);
      $this->displayCacheFile($results,$sub['title'],$sub);
    }

    function getSurveyTop($title,$sub=null) {
      $ss = new Version();
      $temp_prop = $ss->getValue("topsurvey_".$sub['survey_id']);
      if ($temp_prop == null) $temp_prop = $ss->getValue("topsurvey");
      if ($temp_prop == null) $temp_prop = $ss->getValue("toptemplate");
      $results = $ss->getViewShortcut($temp_prop);
      $filename = $GLOBALS['rootDir'].$results['url'];
      if ($GLOBALS['printstuff']) print "getSurveyTop toptemplate: ".$filename."<BR><BR>";
      $this->displayTop($title,$filename,$sub);
    }
    
    function getSurveyBottom($sub=null) {
      // get contents of a file into a string
       $ss = new Version();
      $temp_prop = $ss->getValue("bottomsurvey_".$sub['survey_id']);
      if ($temp_prop == null) $temp_prop = $ss->getValue("bottomsurvey");
      if ($temp_prop == null) $temp_prop = $ss->getValue("bottomtemplate");
      $results = $ss->getViewShortcut($temp_prop);
      $filename = $GLOBALS['rootDir'].$results['url'];

      $this->displayBottom($filename,$sub);
    }


    function displayTop($title,$filename,$sub=null) {
       // get contents of a file into a string
       $this->displayFile($filename,$title,"",NULL,$sub);

    }

    function displayBottom($filename, $sub=NULL) {
       $this->displayFile($filename,NULL,NULL,NULL,$sub);
    }
    
    function displayFile($filename,$title,$hidden,$extra, $sub=null) {
      $outputText = $this->getFile($filename,$title,$hidden,$extra, $sub);
      print $outputText;
    }
    
    function getFile($filename, $title, $hidden, $extra, $sub) {
      if ($title!=null) $sub['title']= $title;
      $sub['hidden']=$hidden;
      $sub['extra']=$extra;
      $content = $this->getFileWithoutSub($filename);
      $content = $this->doSubstitutions($content,$sub);
      return $content;
    }
    
    function getFileWithoutSub($filename, $returnError=TRUE) {
        if ( $filename == NULL) {
           if ($returnError) {
            $ss = new Version();
            //$filename= $GLOBALS['configDir'] . $ss->getValue("errorpage");
            $results = $ss->getViewShortcut($ss->getValue("errortemplate"));
            $filename = $GLOBALS['rootDir'].$results['url'];
           }
           else return NULL;
        }
        else {
          if (!(is_file($filename))) {
             if ($returnError) {
               $ss = new Version();
               //$filename = $GLOBALS['configDir'] . $ss->getValue("errorpage");
               $results = $ss->getViewShortcut($ss->getValue("errortemplate"));
               $filename = $GLOBALS['rootDir'].$results['url'];
             }
             else return NULL;
          }
        }
        
        if ($GLOBALS['printstuff']) print "getfilewithoutsub.. Filename: ".$filename."<BR><BR>";
        $fd = fopen ($filename, "r");
        $contents = fread ($fd, filesize ($filename));
        fclose ($fd);
        
        return $contents;
    }

    function getMenu(){
       $ss = new Version();
       $filename = $GLOBALS['configDir'].$ss->getValue("menuHTML");
       $fileContents = $this->getFileWithoutSub($filename,FALSE);
       return $this->doBasicSubstitutions($fileContents);
    }

    function doSubstitutions($str,$sub=NULL) {
      //print "\n<!-- Subs: \n";
      //print_r($sub);
      //print "\n-->\n";

      //Dynamic substitutions
      //$storeUtil = new StoreUtil();
      //$sub_2 = $storeUtil->getSubstituteItems($str);
      //$adSpaces = $storeUtil->getAdSpaces($str);
      
      //$menu = new Menu;
      //$menuJS = $menu->getMenuHTML();
      //$breadcrumb = $menu->getBreadCrumb(getBaseURL().$GLOBALS['htaccessDir'].$sub['view'].".html");
      //Static substitutions
      
      $wd = new WebsiteData();
      $str = $wd->doDataSubstitutions($str,$sub);
      //$survey = new Survey();
      //$str = $survey->doDataSubstitutions($str,$sub);
      
      //print "\n<!-- dosubstitutions, Size of Content: ".strlen($str)." -->\n";
      $ss = new Version();
      $str = $ss->doVersionSubstitutions($str,$sub);
      //print "\n<!-- dosubstitutions, Size of Content: ".strlen($str)." -->\n";
      $bannerText = $ss->getValue("bannerText");
      $bgImage = $ss->getValue("bgImage");
      $bannerImage = $ss->getValue("bannerImage");
      $menuBg = $ss->getValue("menuBg");
      $tmpclr1 = $ss->getValue("tmpclr1");
      $tmpclr2 = $ss->getValue("tmpclr2");
      $tmpclr3 = $ss->getValue("tmpclr3");
      
      $title="";
      if (isset($sub['title'])) $title = $sub['title'];
      $fname="";
      if (isset($sub['fname'])) $fname = $sub['fname'];
      $lname="";
      if (isset($sub['lname'])) $lname = $sub['lname'];
      $fromname = "";
      if (isset($sub['fromname'])) $fromname = $sub['fromname'];
      $ordernum="";
      if (isset($sub['ordernum'])) $ordernum = $sub['ordernum'];
      $email="";
      if (isset($sub['email'])) $email = $sub['email'];
      $userid="";
      if (isset($sub['userid'])) $userid = $sub['userid'];
      $hidden="";
      if (isset($sub['hidden'])) $hidden = $sub['hidden'];
      $password="";
      if (isset($sub['password'])) $password = $sub['password'];
      $extra="";
      if (isset($sub['extra'])) $extra = $sub['extra'];

      $metaKW = "";
      if (isset($sub['metaKW'])) $metaKW = convertBack($sub['metaKW']);
      $metaDESCR = "";
      if (isset($sub['metaDESCR'])) $metaDESCR = convertBack($sub['metaDESCR']);
      $view_repl = "";
      if (isset($sub['view'])) $view_repl = $sub['view'];

      $error="";
      //if ($ss->getValue("messagesinbox")==1 && isset($sub['error'])) $error = "<div id=\"cms_jsf_error\" style=\"padding:6px;color:red;background-color:#FFBBBB;border:1px solid RED;font-family:arial;font-size:12px;\">".$sub['error']." &nbsp; <span style=\"text-decoration:underline;font-size:10px;color:blue;cursor:pointer;\" onclick=\"$('#cms_jsf_error').fadeOut(500);\">close</span></div>";
      if ($ss->getValue("messagesinbox")==1 && isset($sub['error'])) $error = "<div id=\"cms_jsf_error\" style=\"padding:6px;color:red;background-color:#FFCCCC;border:1px solid RED;font-family:arial;font-size:12px;\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\"><tr><td style=\"color:RED;font-family:arial;font-size:12px;\">".$sub['error']." </td><td style=\"width:14px;\" align=\"right\"><div style=\"cursor:pointer;width:14px;height:14px;\" onclick=\"$('#cms_jsf_error').fadeOut(500);\"><img src=\"".getBaseURL()."jsfimages/close.png\" border=\"0\"></div></td></tr></table></div>";
      else if (isset($sub['error'])) $error = "<BR><center><font color=\"red\"><b>".$sub['error']."</b></font></center>";
      $msg = "";
      if ($ss->getValue("messagesinbox")==1 && isset($sub['msg'])) $msg = "<div id=\"cms_jsf_msg\" style=\"padding:6px;color:GREEN;background-color:#CCFFCC;border:1px solid GREEN;font-family:arial;font-size:12px;\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\"><tr><td style=\"color:GREEN;font-family:arial;font-size:12px;\">".$sub['msg']." </td><td style=\"width:14px;\" align=\"right\"><div style=\"cursor:pointer;width:14px;height:14px;\" onclick=\"$('#cms_jsf_msg').fadeOut(500);\"><img src=\"".getBaseURL()."jsfimages/close.png\" border=\"0\"></div></td></tr></table></div>";
      else if (isset($sub['msg'])) $msg = "<br><center><font color=\"green\"><b>".$sub['msg']."</b></font></center>";
      
      if ($title == NULL) $title = getDefaultTitle();
      
      $usingSSL = getBaseURL();
      
      if ($title == NULL) $title=getDefaultTitle();

      $fullname = $fname." ".$lname;
      if ($fname == NULL) $fname="Customer"; 
      if ($lname == NULL) $lname=" ";
      if ($ordernum==NULL) $ordernum='?';
      if ($email == NULL) $email=" ";
      if ($userid == NULL) $userid=" ";
      if ($password == NULL) $password=" ";

      $ua = new UserAcct();
      $user = $ua->getFullUserInfo(isLoggedOn());
      $str = $ua->doSubstitutions($str,$user);

      //foreach($adSpaces as $key => $value) {
      //   $str = str_replace($key,$value,$str);
      //}
      
      //foreach($sub_2 as $key => $value) {
      //   $str = str_replace($key,$value,$str);
      //}

      $str = str_replace("%%%TITLE%%%",$title,$str);
      $str = str_replace("%%%METAKEYWORDS%%%",$metaKW,$str);
      $str = str_replace("%%%METADESCRIPTION%%%",$metaDESCR,$str);
      $str = str_replace("%%%VIEWNAME%%%",$view_repl,$str);
      $str = str_replace("%%%ERROR%%%",$error,$str);
      $str = str_replace("%%%MESSAGE%%%",$msg,$str);
      $str = str_replace("%%%BAREERROR%%%",$sub['error'],$str);
      $str = str_replace("%%%BAREMESSAGE%%%",$sub['msg'],$str);
      $str = str_replace("%%%SITEEMAIL%%%",$ss->getSiteEmail(),$str); 
      $str = str_replace("%%%UNSURESSLURL%%%",$usingSSL,$str); 
      $str = str_replace("%%%STATELIST%%%",listStates("BL","state"),$str);
      $str = str_replace("%%%STATELIST2%%%",listStates(),$str);
      $str = str_replace("%%%EXTRALINKS%%%",$extra,$str);
      $str = str_replace("%%%FULLNAME%%%",$fullname,$str);
      $str = str_replace("%%%FIRSTNAME%%%",$fname,$str);
      $str = str_replace("%%%LASTNAME%%%",$lname,$str);
      $str = str_replace("%%%FROMNAME%%%",$fromname,$str);
      $str = str_replace("%%%EMAIL%%%",$email,$str);
      $str = str_replace("%%%USERID%%%",$userid,$str);
      $str = str_replace("%%%PASSWORD%%%",$password,$str);
      $str = str_replace("%%%HIDDEN%%%",$hidden,$str);
      //print "\n<!-- dosubstitutions, Size of Content: ".strlen($str)." -->\n";
      
      $str = $this->doBasicSubstitutions($str);
      return $str;
    }

    function doBasicSubstitutions($str) {
      $userFName = "";
      if (isset($_SESSION['s_user']['fname'])) {
         $userFName = $_SESSION['s_user']['fname'];
      }

       $separator = NULL;
       if (isset($GLOBALS['loginseparator'])) $separator = $GLOBALS['loginseparator'];
       if ($separator == null) $separator = " | ";

       $logURL = $GLOBALS['baseURLSSL'].$GLOBALS['htaccessAction']."viewlogin.html";
       $logAction = "<a href=\"".$logURL."\">Log In | Join</a>";
       
       if (isLoggedOn()) {
         $logURL = $GLOBALS['baseURLSSL'].$GLOBALS['htaccessAction']."logout.html";
         $logAction = "Welcome ".$_SESSION['s_user']['emailAddress']."<BR>";
         $logAction .= "<a href=\"".$logURL."\">Log Out</a>".$separator;
         $logAction .= "<a href=\"" . $GLOBALS['baseURLSSL'].$GLOBALS['htaccessAction']."account.html\">Account</a>";
       }

      $newwindow = getParameter("newwindow");
      $linktarget = "";
      if ($newwindow==1) $linktarget=" target=\"_newtab\" ";

       $str = str_replace("%%%FORMSREWRITE%%%",$GLOBALS['formsrewrite'],$str);
       $str = str_replace("%%%HTACCESSDIR%%%",$GLOBALS['htaccessDir'],$str);
       $str = str_replace("%%%HTACCESSEXPORTDIR%%%",$GLOBALS['htaccessExportDir'],$str);
       $str = str_replace("%%%USERFNAME%%%",$userFName,$str);
       $str = str_replace("%%%LOGACTION%%%",$logAction,$str);
       $str = str_replace("%%%IMAGESDIR%%%",$GLOBALS['imagesDir'],$str); 
       $str = str_replace("%%%CODEFOLDER%%%",$GLOBALS['codeFolder'],$str); 
       $str = str_replace("%%%STYLEDIR%%%",$GLOBALS['styleDir'],$str); 
       $str = str_replace("%%%VIEWDIR%%%",$GLOBALS['htaccessView'],$str); 
       $str = str_replace("%%%ACTIONDIR%%%",$GLOBALS['htaccessAction'],$str); 
       $str = str_replace("%%%BASEURL%%%",getBaseURL(),$str); 
       $str = str_replace("%%%ROOTDIR%%%",$GLOBALS['baseDir'],$str); 
       $str = str_replace("%%%BASEURLSSL%%%",$GLOBALS['baseURLSSL'],$str);
       $str = str_replace("%%%ADMINURLSSL%%%",$GLOBALS['baseURLSSL'],$str);
       $str = str_replace("%%%SURVEYFILELOCATION%%%",$GLOBALS['srvyURL'],$str);
       $str = str_replace("%%%DEFAULTTITLE%%%",getDefaultTitle(),$str);
       $str = str_replace("%%%STAFFNAME%%%",getDefaultTitle()." staff",$str);
       $str = str_replace("%%%SHORTDATE%%%",date("m/d/Y"),$str);
       $str = str_replace("%%%LONGDATE%%%",date("F j, Y"),$str);
       $str = str_replace("%%%YEAR%%%",date("Y"),$str);
       $str = str_replace("%%%COPYRIGHT%%%","&copy;",$str);
       $str = str_replace("%%%NONBREAKSPACE%%%","&nbsp;",$str);
       $str = str_replace("%%%INTTIMESTAMP%%%",time(),$str);
       $str = str_replace("%%%LINKTARGET%%%",$linktarget,$str);
      //print "\n<!-- dobasicsubstitutions, Size of Content: ".strlen($str)." -->\n";

       return $str;
    }

    function reverseSubstitutions($str) {
       
       $str = str_replace($GLOBALS['htaccessDir'],"%%%HTACCESSDIR%%%",$str);
       $str = str_replace($GLOBALS['htaccessExportDir'],"%%%HTACCESSEXPORTDIR%%%",$str);
       $str = str_replace(getBaseURL(),"%%%BASEURL%%%",$str); 
       $str = str_replace($GLOBALS['baseURLSSL'],"%%%BASEURLSSL%%%",$str);
       $str = str_replace($GLOBALS['baseURLSSL'],"%%%ADMINURLSSL%%%",$str);
       $str = str_replace("src=\"images","src=\"%%%BASEURL%%%images",$str);
       $str = str_replace("src=\"/images","src=\"%%%BASEURL%%%images",$str);

       return $str;
    }

   function getTextBetween($contents, $beginTxt, $endTxt) {
       if (strpos($contents, $beginTxt) === false || strpos($contents, $endTxt)===false) {
         return $contents;
       }

       $start = strpos($contents, $beginTxt)+strlen($beginTxt);
       $length = strlen($contents) - $start - strlen(strstr($contents,$endTxt));
       $newcontents = substr($contents,$start,$length);
       return $newcontents;
   }

    function is_dir_ex($dirname) 
    { 
        $handle=opendir($dirname); 
        if(0==strcmp(readdir($handle),".")) 
            $result=true; 
        else 
            $result=false; 
        closedir($handle); 
        return $result; 
    } 
    
   function getFiles($directory,$startCSV=NULL,$endCSV=NULL) {
      $result_array = array();
      $files = $this->list_dir($directory);

      $startArr = array();
      $endArr = array();

      if($startCSV!=NULL) $startArr = separateStringBy($startCSV,",");
      else $startArr[0] = "";

      if($endCSV!=NULL) $endArr = separateStringBy($endCSV,",");
      else $endArr[0] = "";

      foreach ($files as $value) {
         $found = FALSE;
         foreach ($startArr as $x) {
            $start = strtolower(trim($x));
            if ($start==NULL || 0==strcmp(substr($value,0,strlen($start)),$start)) {
               foreach ($endArr as $y) {
                  $end = strtolower(trim($y));
                  if ($end==NULL || 0==strcmp(substr($value,-(strlen($end))),$end)) {
                     $result_array[] = $value;
                     break 2;
                  }
               }
            }
         }
      }
      return $result_array;
   }
    
    //List the contents of a directory...
    // if TRUE is passed in, list the files
    // if FALSE is passed in, list the directories
    function list_dir($dirname, $files=TRUE, $printstuff=FALSE) { 
      if($dirname[strlen($dirname)-1]!='/') $dirname.='/'; 
      $result_array=array(); 
      $handle=opendir($dirname); 
      if ($printstuff && $files) print "\n<!-- list_dir() finding files for directory: ".$dirname." -->\n";
      else if ($printstuff && !$files) print "\n<!-- list_dir() finding directories for directory: ".$dirname." -->\n";
      
      while ($file = readdir($handle)) {
         if ($printstuff) print "\n<!-- list_dir element: ".$file." -->\n";
         if(0!=strcmp($file,".") && 0!=strcmp($file,"..")) {
            if ($printstuff) print "\n<!-- list_dir non-root file: ".$file." -->\n";
            if(is_dir($dirname.$file) && !$files) {
               $result_array[]=$file."/";
               if ($printstuff) print "\n<!-- list_dir directory: ".$file." -->\n";
            } else if (is_file($dirname.$file) && $files) {
               $result_array[]=$file;
               if ($printstuff) print "\n<!-- list_dir file: ".$file." -->\n";
            }
         }
      } 
      closedir($handle);
      sort($result_array);
      return $result_array;
    }

    function list_dir_both($dirname, $printstuff=FALSE) { 
      if($dirname[strlen($dirname)-1]!='/') $dirname.='/'; 
      $file_array=array(); 
      $dir_array=array(); 
      $handle=opendir($dirname); 
      if ($printstuff) print "\n<!-- list_dir() finding files/dirs for directory: ".$dirname." -->\n";
      
      while ($file = readdir($handle)) {
         if ($printstuff) print "\n<!-- list_dir element: ".$file." -->\n";
         if(0!=strcmp($file,".") && 0!=strcmp($file,"..")) {
            if ($printstuff) print "\n<!-- list_dir non-root file: ".$file." -->\n";
            if(is_dir($dirname.$file)) {
               $dir_array[]=$file."/";
               if ($printstuff) print "\n<!-- list_dir directory: ".$file." -->\n";
            } else if (is_file($dirname.$file)) {
               $file_array[]=$file;
               if ($printstuff) print "\n<!-- list_dir file: ".$file." -->\n";
            }
         }
      } 
      closedir($handle);
      sort($dir_array);
      sort($file_array);

      $result_array = array();
      $result_array['files'] = $file_array;
      $result_array['directories'] = $dir_array;
      return $result_array;
    }

    function createDir($newDir) {
      if ($this->is_dir_ex($newDir)) return TRUE;
      else {
        if (mkdir($newDir, 0755)) return TRUE;
        else return FALSE;
      }
    }

    
    function saveFile ($filename,$contents) {
       $contents = str_replace("\\\"", "\"",$contents);
       $contents = str_replace("\'", "'",$contents);

	    $file = fopen($filename, "w+");
       if($file==false) die("unable to create file");
	    fwrite($file, $contents);
	    fclose($file);
    }

   
   // This is for simple tracking within our own infrastructure for keeping a count.    
   function trackItem($view=NULL,$action=NULL,$jsftrack1=NULL,$jsftrack2=NULL,$jsftrack3=NULL,$referer=NULL,$ipaddr=NULL,$agent=NULL,$sessionid=NULL,$userid=NULL,$country=NULL,$region=NULL,$city=NULL,$lat=NULL,$lng=NULL,$postal=NULL,$skipBots=FALSE){
      
      if($GLOBALS['notracking']==NULL || $GLOBALS['notracking']!=1) {
      
         //print "\n<!-- Track item: ".$view.", ".$action.", ".$jsftrack1.", ".$jsftrack2." -->\n";
         $trkid=NULL;
   
         if ($agent == NULL) $agent = $_SERVER['HTTP_USER_AGENT'];
         $la = trim(strtolower($agent));
   
         if (!$skipBots || ($agent!=NULL && strpos($la,"googlebot")===FALSE && strpos($la,"bingbot")===FALSE && strpos($la,"ahrefsbot")===FALSE && strpos($la,"msnbot")===FALSE && strpos($la,"careerbot")===FALSE && strpos($la,"bot.php")===FALSE)) {
            $q1 = "created";
            $q2 = "NOW()";
   
            if ($view!=NULL) {
               $q1 .= ",view";
               $q2 .= ",'".convertString($view)."'";
            }
      
            if ($action!=NULL) {
               $q1 .= ",action";
               $q2 .= ",'".convertString($action)."'";
            }
      
            if ($jsftrack1!=NULL) {
               $q1 .= ",jsftrack1";
               $q2 .= ",'".substr(convertString($jsftrack1),0,254)."'";
            }
      
            if ($jsftrack2!=NULL) {
               $q1 .= ",jsftrack2";
               $q2 .= ",'".substr(convertString($jsftrack2),0,254)."'";
            }
      
            if ($jsftrack3!=NULL) {
               $q1 .= ",jsftrack3";
               $q2 .= ",'".substr(convertString($jsftrack3),0,254)."'";
            }
      
            if ($country!=NULL) {
               $q1 .= ",country";
               $q2 .= ",'".convertString($country)."'";
            }
      
            if ($region!=NULL) {
               $q1 .= ",region";
               $q2 .= ",'".convertString($region)."'";
            }
      
            if ($city!=NULL) {
               $q1 .= ",city";
               $q2 .= ",'".convertString($city)."'";
            }
      
            if ($postal!=NULL) {
               $q1 .= ",postal";
               $q2 .= ",'".convertString($postal)."'";
            }
      
            if ($lat!=NULL) {
               $q1 .= ",lat";
               $q2 .= ",".$lat;
            }
      
            if ($lng!=NULL) {
               $q1 .= ",lng";
               $q2 .= ",".$lng;
            }
      
            if ($userid!=NULL) {
               $q1 .= ",userid";
               $q2 .= ",".$userid;
            } else if (isLoggedOn()) {
               $q1 .= ",userid";
               $q2 .= ",".isLoggedOn();
            } else {
               $q1 .= ",userid";
               $q2 .= ",-1";
            }
      
            $q1 .= ",referer";
            if ($referer!=NULL) $q2 .= ",'".convertString($referer)."'";
            else if(isset($_SERVER["HTTP_REFERER"])) $q2 .= ",'".convertString($_SERVER["HTTP_REFERER"])."'";
            else $q2 .= ",''";
            
            $q1 .= ",ipaddr";
            if ($ipaddr!=NULL) $q2 .= ",'".convertString($ipaddr)."'";
            else $q2 .= ",'".convertString($_SERVER["REMOTE_ADDR"])."'";
      
            $q1 .= ",agent";
            $q2 .= ",'".convertString($agent)."'";
      
            $q1 .= ",sessionid";
            if ($sessionid!=NULL) $q2 .= ",'".convertString($sessionid)."'";
            else $q2 .= ",'".convertString(session_id())."'";
      
            $dbLink = new MYSQLAccess;
            $query = "INSERT DELAYED INTO tracker (".$q1.") VALUES (".$q2.");";
            $trkid = $dbLink->insertGetValue($query);
            //print "\n<!-- ".$query." -->\n";
         }
         return $trkid;
      } else {
         return NULL;
      }
   }

   function getTracking($searchstr=NULL,$orderby=NULL,$limit=100,$page=1,$countonly=FALSE,$table=NULL) {
      return $this->searchTracking($searchstr,NULL,NULL,NULL,NULL,NULL,$orderby,$limit,$page,$countonly,$table);
   }

   function searchTracking($searchstr=NULL,$ipaddr=NULL,$referer=NULL,$sessionid=NULL,$createdafter=NULL,$createdbefore=NULL,$orderby=NULL,$limit=100,$page=1,$countonly=FALSE,$table=NULL) {
      if ($orderby==NULL) $orderby = "p.created DESC";
      if ($table==NULL) $table = "tracker";
      $dbLink = new MYSQLAccess;
      $query = "SELECT * ";
      if ($countonly) $query = "SELECT count(*) ";

      $query .= " FROM ".$table." p ";
      $query .= " WHERE 1=1 ";

      if ($ipaddr!=NULL) $query .= "AND LOWER(ipaddr) LIKE '%".strtolower($ipaddr)."%' ";
      if ($referer!=NULL) $query .= "AND LOWER(referer) LIKE '%".strtolower($referer)."%' ";
      if ($sessionid!=NULL) $query .= "AND LOWER(sessionid) LIKE '%".strtolower($sessionid)."%' ";
      if ($createdafter!=NULL) $query .= "AND created >= '".$createdafter."' ";
      if ($createdbefore!=NULL) $query .= "AND created <= '".$createdbefore."' ";
      
      if ($searchstr!=NULL) {
         $query .= "AND (";
         $query .= " LOWER(referer) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(ipaddr) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(sessionid) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(agent) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(jsftrack1) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(jsftrack2) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(jsftrack3) LIKE '%".strtolower($searchstr)."%' OR ";
         $query .= " LOWER(created) LIKE '%".strtolower($searchstr)."%') ";
      }

      if (!$countonly) {
         $start = ($page-1)*$limit;
         $query .= " ORDER BY ".$orderby." ";
         $query .= " LIMIT ".$start.",".$limit.";";
      }
      $results = $dbLink->queryGetResults($query); 
      return $results;
   }

   function deleteTracking($trkid){
      $dbLink = new MYSQLAccess;
      $query = "DELETE FROM tracker WHERE trkid=".$trkid.";";
      $dbLink->delete($query);
   }
   
}


?>
