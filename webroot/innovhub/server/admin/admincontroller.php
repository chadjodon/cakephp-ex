<?php
//print "\n<!-- in admincontroller.php, including: Classes,CustomCMS_Admin -->\n";
include_once "../Classes.php";
//error_reporting(E_ALL);
//include_once $GLOBALS['rootDir'].$GLOBALS['customCodeFolder']."CustomCMS_Admin.php";

//print "<br>\n<br>\n";
//print_r($_SERVER);
//print "<br>\n<br>\n";


//print "<br>\nSSL url: ".$GLOBALS['baseURLSSL'];
//print "<br>\nscript uri: ".$_SERVER['SCRIPT_URI'];
//print "<br>\nsubstring script uri: ".substr($_SERVER['SCRIPT_URI'],0,strlen($GLOBALS['baseURLSSL']));

if(FALSE && 0!=strcmp(substr($_SERVER['SCRIPT_URI'],0,strlen($GLOBALS['baseURLSSL'])),$GLOBALS['baseURLSSL'])) {
   //If this instance requires SSL, redirect when not exactly the URL of choice
   // This will help page/login cookies/storage needed...
   $newheaderlocation = $GLOBALS['baseURLSSL'].substr($_SERVER['REQUEST_URI'],1);
?>

<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="0; url=<?php echo $newheaderlocation; ?>">
        <script type="text/javascript">
            window.location.href = "<?php echo $newheaderlocation; ?>"
        </script>
        <title>Page Redirection</title>
    </head>
    <body>
        If you are not redirected automatically, follow this <a href='<?php echo $newheaderlocation; ?>'>link</a>.
    </body>
</html>

<?php
} else {
   $_SESSION['admindir'] = "server/admin/";
   
   //print_r($_SESSION);
   if(!isset($_SESSION['params']['overridetheme'])) unset($_SESSION['params']);
   
   $template = new Template;
   $ss = new Version;
   $ctx = new Context();
   $ua=new UserAcct;
   //$menu = new Menu;
   //$pubcont = new PubCont;
   //$surveyObj = new Survey;
   $wdObj = new WebsiteData;
   
   $pagejs = $ua->checkAutoLoginCookie();
   
   $vars['removecmsid']=0;
   $vars['forgottenPW'] = getParameter("forgottenPW");
   $vars['defaultTitle'] = $ss->getValue("defaultTitle");
   
   $basedir = $GLOBALS['configDir']."main/";
   $baseurl = "main/";
   
   $vars['topbottomincluded'] = true;
   $toppage = "top.php";
   $bottompage = "bottom.php";
   $welcomepage = "welcome.php";
   $customTop = $ss->getValue("adminTop");
   print "<!-- customtop: ".$customTop." -->\n";
   $customBottom = $ss->getValue("adminBottom");
   //print "<!-- custombottom: ".$customBottom." -->\n";
   $customWelcome = $ss->getValue("adminWelcome");
   if ($customTop!=NULL && $customBottom!=NULL) {
      $toppage = $GLOBALS['baseDir'].$customTop;
      $bottompage = $GLOBALS['baseDir'].$customBottom;
   }
   if ($customWelcome!=NULL) $welcomepage = $GLOBALS['baseDir'].$customWelcome;
   
   print "<!-- top: ".$toppage." bottom: ".$bottompage." welcome: ".$customWelcome." -->\n";
   
   $action = getParameter("action");
   if ($action == NULL) $action="welcome";
   
     //print "\n<!-- ***chj*** action 1: ".$action." -->\n";
   
   
   
   $showdebug = getParameter("showdebug");
   $_SESSION['showdebug']=FALSE;
   if ($showdebug==1) $_SESSION['showdebug']=TRUE;
   
   if($_SESSION['showdebug']) print "\n<!-- ".date("Y-m-d H:i:s")." admincontroller.php start -->\n";
   
   
   //print "\n<!-- ***chj*** before login userid: ".isLoggedOn()." -->\n";
   
   if (0 == strcmp($action,"login")) {
      if (!$ua->logInUser()){
         $vars['error'] .="Your user id or password is incorrect.<BR>";
         $action="showlogon";
      } else {
   
   //print "\n<!-- ***chj*** after login userid: ".isLoggedOn()." -->\n";
   
         $_SESSION['params'] = $_SESSION['lastparams'];
         $action = getParameter("action");
         
         if ($action == NULL || 0==strcmp($action,"login")) $action="welcome";
      }
   } else if (0 == strcmp($action,"resetpw")) {
      $vars['email'] = getParameter("email");
      if (!$ua->sendForgottenEmail($vars['email'])) {
         $vars['error']="The password was not reset successfully, we could not find a user account for that email.  Please check the email spelling and try again.<BR>";
         $vars['forgottenPW'] = 1;
         $action="showlogon";
      }
      else {
         $vars['msg'] = "Your password was reset successfully.  You will receive an email shortly with your new password.<BR>";
         $action="showlogon";
      }
   }
   
   //print "\n<!-- ***chj*** before admin userid: ".isLoggedOn()." -->\n";
   
   
   if (!$ua->isUserAdmin(isLoggedOn())) {
      saveParameters();
      $action="showlogon";	
      $vars['admin'] = false;
      
      $ctx->clearSiteContext();
      $ua->logout();
      
   } else {
      $vars['admin'] = true;
   
      $s_siteid = getParameter("s_siteid");
      if ($s_siteid != NULL) {
         $results = $ua->canAdminUserAccessSite(isLoggedOn(),$s_siteid);
         if ($results['canaccess']) {
            $ctx->setSiteContext($s_siteid);
         } else {
            $ctx->setSiteContext($results['defaultsiteid']);
         }
      } else {
         $sitearr = $ctx->getSiteContext();
         $results = $ua->canAdminUserAccessSite(isLoggedOn(),$sitearr[0]['siteid']);
         if (!$results['canaccess']) {
            $ctx->setSiteContext($results['defaultsiteid']);
         }
      }
   }
   
   //print "Action: ".$action."<BR><BR>\n";
   // ======================================================
   // === Start admin actions
   // === 
   // === 
   // ======================================================
   
   
   //------------------------------------------------------
   // View the logon screen
   //------------------------------------------------------
   if (0 == strcmp($action,"showlogon")) {
      $page="showlogon.php";
   }
   //------------------------------------------------------
   // View a particular web page from the site
   //------------------------------------------------------
   else if (0 == strcmp($action,"logout")) {
     //print "\n<!-- ***chj*** logout action caught.  userid: ".isLoggedOn()." -->\n";
     $pagejs = NULL; 
     $ctx->clearSiteContext();
     $ua->logout();
     $page = "showlogon.php";
     
     unset($_SESSION['s_user']);
     unset($_SESSION['params']);
     unset($_SESSION['lastparams']);
     
     $_SESSION['s_user'] = array();
     $_SESSION['params'] = array();
     $_SESSION['lastparams'] = array();
   }
   //------------------------------------------------------
   // run a raw query
   //------------------------------------------------------
   else if (0 == strcmp($action,"sqlquery")) {
      if($ua->canUserDelete(isLoggedOn())){
         $vars['query'] = getParameter("query");
         $page = "sqlquery.php";
   
         if(getParameter("csv")==1) {
            $obj = new ScheduledSQLCSV();
            $obj->createJob(getParameter("content"),getParameter("subject"),getParameter("field1"));
            $vars['msg'] = "Your job was schedled.<br>";
         }
     } else {
      $vars['error'] = "Sorry, you do not have the access rights necessary to perform that operation.<br>";
      $page = $welcomepage;
     }
   }
   //------------------------------------------------------
   // test out functions...
   //------------------------------------------------------
   else if (0 == strcmp($action,"custompage")) {
      $page = $GLOBALS['rootDir'].$GLOBALS['customCodeFolder'].getParameter("page").".php";
   }
   
   else if (0 == strcmp($action,"test")) {
     $parameters[0] = "working";
     $parameters[1] = isLoggedOn();
      $temp = $ua->getUser(isLoggedOn());
     $parameters[2] = $temp['ulevel'];
     $parameters[3] = $ua->doesUserHaveAccessToLevel(isLoggedOn(),1);
     $parameters[4] = $_SESSION['s_user']['emailAddress'];
      $access = $ua->getUserAccessLevels(isLoggedOn());
      for ($i=0; $i<count($access); $i++) {
         $parameters[(5+$i)] = $access[($i+1)];
      }
   
     $page = "test.php";
   }
   else if (0 == strcmp($action,"xmltest")) {
      //error_reporting(8191);
      print "<html><body>Testing successfully.";
      $xmlp = getParameter("xmlp");
     $data = XML_unserialize($xmlp);
     print "\n<br><br><br>\n";
     print "<textarea rows=\"20\" cols=\"140\">\n";
     print_r($data);
     print "</textarea>\n";
   
     print "<br><br><br>\n";
      print "<form action=\"".$GLOBALS['baseURLSSL'].$_SESSION['admindir']."admincontroller.php\" method=\"post\">\n";
      print "<input type=\"hidden\" name=\"action\" value=\"xmltest\">\n";
      print "<textarea rows=\"20\" cols=\"140\" name=\"xmlp\">\n".$xmlp."</textarea>\n";
      print "<input type=\"submit\" name=\"submit\" value=\"submit\">\n";
     print "</form>\n";
     $page = "test.php";
   }
   //------------------------------------------------------
   // View a particular web page from the site
   //------------------------------------------------------
   else if (0 == strcmp($action,"showview")) {
     $vars['view'] = getParameter("view");
     $page = "showview.php";
   }
   //------------------------------------------------------
   // View a particular web page from the site
   //------------------------------------------------------
   else if (0 == strcmp($action,"showshortcut")) {
     $vars['view'] = getParameter("view");
     $page = "showshortcut.php";
   }
   //------------------------------------------------------
   // View the list of web pages on the site
   //------------------------------------------------------
   else if (0 == strcmp($action,"showallshortcuts")) {
        $page = "showallshortcuts.php";
   }
   //------------------------------------------------------
   // edit a web page on the site
   //------------------------------------------------------
   else if (0 == strcmp($action,"editview")) {
      $vars['view'] = getParameter("view");
      $title = getParameter("title");
      $descr = getParameter("descr");
      $keywords = getParameter("keywords");
      $contents = getParameter("contents");
   
      if ($vars['view'] == null) {
         $vars['error'] .= "The save was not successful.  Please fill in the view name before saving.<BR>";
      }
      else {
         $url = $baseurl.$vars['view'].".html";
         $filename = $basedir.$vars['view'].".html";
          $contents = $template->getTextBetween($contents, "<!--%%%BEGINHTML%%%-->", "<!--%%%ENDHTML%%%-->");
          $contents = $template->getTextBetween($contents, "<body>", "</body>");
          $contents = $template->reverseSubstitutions($contents);
         if ($contents != null) $template->saveFile($filename,$contents);
         //$ss->saveViewShortcut($vars['view'],$url,$keywords,$descr,$title,$other);
         $vars['msg'] .= "Saved successfully.<BR>";
      }
      $page = "showshortcut.php";
   
   }
   //------------------------------------------------------
   // Upload a dream weaver edited file
   //------------------------------------------------------
   else if (0==strcmp($action,"uploadDW")) {
        $vars['view'] = getParameter("view");
        
        if (is_uploaded_file($_FILES['userfile']['tmp_name']) && $vars['view'] != NULL) {
           if (file_exists($GLOBALS['dwuploadDir'].$vars['view'].".html")) {
              $count = 0;
              $filename = $GLOBALS['dwuploadDir']."dw".$count."_".$vars['view'].".html";
              while (file_exists($filename)) {
                 $count ++;
                 $filename = $GLOBALS['dwuploadDir']."dw".$count."_".$vars['view'].".html";
              }
              rename($GLOBALS['dwuploadDir'].$vars['view'].".html",$filename);
           }
           if (copy($_FILES['userfile']['tmp_name'],$GLOBALS['dwuploadDir'].$vars['view'].".html"))
           {
                $filename= $GLOBALS['dwuploadDir'].$vars['view'].".html";
                $contents = $template->getFileWithoutSub($filename,FALSE);
                $contents = $template->getTextBetween($contents, "<!--%%%BEGINHTML%%%-->", "<!--%%%ENDHTML%%%-->");
                $contents = $template->getTextBetween($contents, "<body>", "</body>");
                $contents = $template->reverseSubstitutions($contents);
                if ($contents != null) $template->saveFile($filename,$contents);
   
                $vars['msg'] .= $vars['view'].".html uploaded to server.<BR>";
           }
           else $vars['error'] .= "Upload failed.  Please make sure the file is not too large and exists on your system.";
        }
   
        else $vars['error'] .= "Upload failed.";
   
        $page = "showshortcut.php";
   
   }
   //------------------------------------------------------
   // Upload a dream weaver edited file
   //------------------------------------------------------
   else if (0==strcmp($action,"uploadFile")) {
        $conversion = getParameter("conversion");
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
           if (file_exists($GLOBALS['dwuploadDir'].$_FILES['userfile']['name'])) {
              $count = 0;
              $filename = $GLOBALS['dwuploadDir']."dw".$count."_".$_FILES['userfile']['name'];
              while (file_exists($filename)) {
                 $count ++;
                 $filename = $GLOBALS['dwuploadDir']."dw".$count."_".$_FILES['userfile']['name'];
              }
              rename($GLOBALS['dwuploadDir'].$_FILES['userfile']['name'],$filename);
           }
           if (copy($_FILES['userfile']['tmp_name'],$GLOBALS['dwuploadDir'].$_FILES['userfile']['name']))
           {
                $filename= $GLOBALS['dwuploadDir'].$_FILES['userfile']['name'];
                $contents = $template->getFileWithoutSub($filename,FALSE);
                if (0==strcmp($conversion,"csv")) $newcontents = csvRemoveQuotes($contents);
                else if (0==strcmp($conversion,"latin")) $newcontents = convertLatinString($contents);
                else $newcontents = $contents;
                $template->saveFile($filename,$newcontents);
                $vars['msg'] .= "uploaded to server.  Format: ".$conversion."<BR>";
                //print csvRemoveQuotes("love you yooo\",space,space\"hello world\"i'm,right here");
           }
           else $vars['error'] .= "Upload failed.  Please make sure the file is not too large and exists on your system.";
        }
        else $vars['error'] .= "Upload failed.";
   
        $page = "chad.php";
   
   }
   //------------------------------------------------------
   // Upload a dream weaver edited file
   //------------------------------------------------------
   else if (0==strcmp($action,"chad")) {
        $page = "chad.php";
   }
   //------------------------------------------------------
   // View the file listing screen
   //------------------------------------------------------
   else if (0 == strcmp($action,"listfiles")) {
      $vars['dir'] = getParameter("dir");
      if ($vars['dir'] == null) $vars['dir'] = "main/";
      $page="listfiles.php";
   }
   //------------------------------------------------------
   // View the file/shortcuts listing screen
   //------------------------------------------------------
   else if (0 == strcmp($action,"showurls")) {
      $vars['dir'] = getParameter("dir");
      if ($vars['dir'] == null) $vars['dir'] = "main/";
      $page="showurls.php";
   }
   
   //------------------------------------------------------
   // View ad campaigns
   //------------------------------------------------------
    elseif (0 == strcmp($action,'campaigns')) {
       $adSpace = new AdSpace;
       $cname = getParameter('cname');
       
       if ($cname!=null) {
          $startdate=getParameter("startdate");
          $expire = getParameter("expire");
          $cstate = getParameter("cstate");
          if (0==strcmp($subaction,"new")) {
             if ( $adSpace->addCampaign($startdate, $expire, $cstate, $cname) ) print "<font color=\"red\">Campaign Added Successfully.</font>\n<BR><Br>";
             else print "<font color=\"red\">Error: Pleae pick a campaign name that is not already created.</font>\n<BR><BR>";
          }
          elseif (0==strcmp($subaction,"update")) {
             $adSpace->updateCampaign($startdate, $expire, $cstate, $cname);
             $vars['msg'] = "Campaign Updated Successfully<Br>";
          }
          elseif (0==strcmp($subaction,"delete")) {
             if ($adSpace->removeCampaign($cname)) $vars['msg'] = "Campaign deleted Successfully.<Br>";
             else $vars['error'] ="Please make sure there are no adspaces in your campaign before deleting.<BR>";
          }
       }
      $page="campaigns.php";    
    }
    // ---------------------------------------------------------------------
    // list and edit ad spaces
    // ---------------------------------------------------------------------
    elseif (0 == strcmp($action,'adspaces')) {
       $adnum = getParameter('adnum');
       $cname = getParameter('cname');
       $vars['cname'] = $cname;
       $adSpace = new AdSpace;
   
       $deleteAd = getParameter('deleteAd');
       if ($deleteAd == 1 & $adnum != NULL) {
          $adSpace->removeAd($adnum);
          $adnum=NULL;
       }
       $createAd = getParameter('createAd');
       if ($createAd == 1) {
          $adnum = $adSpace->AddAdSpace("", getDateForDB(), getDateForDB(1,0), "DISABLED", $cname);
       }
   
       if ($adnum == NULL) {
         $modifyAd = getParameter('modifyAd');
          if ($modifyAd == 1) {
             $listadnum = getParameter('listadnum');
             $adstate = getParameter('adstate');
             $adSpace->changeAdStatus($listadnum,$adstate);
             $vars['msg'] = "Ad Space Updated<br>";
          }
          $page = "adspaces.php";
       }
       else {
          $vars['adnum'] = $adnum;
          $modifyAd = getParameter('modifyAd');
          if ($modifyAd == 1) {
             $startdate = getParameter('startdate');
             $expire = getParameter('expire');
             $ad = getParameter('ad');
             $adstate = getParameter('adstate');
             $extra = getParameter('extra');
             $whereclause = getParameter('whereclause');
   
             $adSpace->modifyAd($adnum,$startdate,$expire,$ad,$adstate,$extra,$cname,$whereclause);
             $vars['msg']="Ad Space Updated<br>";
          }
         $page="adspacedetail.php";
       }
   
    }
   
   //------------------------------------------------------
   // View the welcome screen
   //------------------------------------------------------
   else if (0 == strcmp($action,"welcome")) {
      $vars['lastlogin'] = $_SESSION['s_user']['lastlogin'];
      $vars['email'] = $_SESSION['s_user']['emailAddress'];
      $page=$welcomepage;
   }
   
   
   //------------------------------------------------------
   // Dashboard of social interaction/todo/notes
   //------------------------------------------------------
   else if (0 == strcmp($action,"dashboard")) {
      $vars['lastlogin'] = $_SESSION['s_user']['lastlogin'];
      $vars['email'] = $_SESSION['s_user']['emailAddress'];
      $subaction = getParameter("subaction");
      if (0==strcmp($subaction,"deletecomment")) {
         $up = new UserPost();
         $up->deletePost(getParameter("postid"));
      } else if (0==strcmp($subaction,"addcomment")) {
         $up = new UserPost();
         $up->addPost(getParameter("posttype"),getParameter("userid"),getParameter("title"),getParameter("content"),1,getParameter("status"),getParameter("refid"),getParameter("category"));
      }
      $page="dashboard.php";
   }
   
   
   else if (0 == strcmp($action,"testjob")) {
      //error_reporting(E_ALL);
   
      $priority = getParameter("priority");
      if($priority==NULL) $priority=1;
      
      $subj = "Tester of custom jobs added: ".date("m/d/Y H:i:s");
      $sched = new Scheduler();
      $sched->addSchedCustom("CustomSchedJobTest",$subj,$priority);
      $page="dashboard.php";
   }
   
   //------------------------------------------------------
   // run email scheduled job
   //------------------------------------------------------
   else if (0 == strcmp($action,"sendemailsout")) {
      $scheduler = new Scheduler();
      $scheduler->checkShortEmailJobs();
      $page="dashboard.php";
   }
   //------------------------------------------------------
   // View the system users
   //------------------------------------------------------
   else if (0 == strcmp($action,"listusers")) {
      //$vars['segmentid'] = trim(getParameter("segmentid"));
      //if ($vars['segmentid'] != NULL) $ua->setUserSearchParams($vars['segmentid']);
      ini_set('memory_limit', '128M');
      $page="listusers.php";
   }
   
   //------------------------------------------------------
   // Search system users
   //------------------------------------------------------
   else if (0 == strcmp($action,"searchusers")) {
      $page="usersadvsearch.php";
   }
   
   else if (0 == strcmp($action,"listuserscloning")) {
      //error_reporting(E_ALL);
   
      $page="listuserscloning.php";
      $userid = getParameter('userid');
      $submit = getParameter('subaction');
   
      if ($userid!=NULL && is_array($userid)) {
         $useraccess = getParameter('privacy');
         $emailtext = $template->doSubstitutions(getParameter('emailtext'));
         $subjecttext = $template->doSubstitutions(getParameter('subjecttext'));
         $reason = getParameter("reason");
   
         if (0==strcmp($submit,"Approve")) {
             //error_reporting(E_ALL);
             $ua->promoteManyAccounts($userid);
         } else {
             for ($i=0; $i<count($userid); $i++) {
                $u = $userid[$i];
                if (0==strcmp($submit,"Delete")) {
                   if ($u!=isLoggedOn()) $ua->deleteUserAcct($u);
                } else if (0==strcmp($submit,"Approve")) {
                   //error_reporting(E_ALL);
                   
                   /*
                   $ua->promoteAccount($u);
                   //$rels = $ua->getUsersRelated($u);
                   $rels = $ua->getUsersRelated($u,"both");
                   $fin = array();
                   for ($i=0;$i<count($rels);$i++) {
                      if (!isset($fin[$rels[$i]['xuserid']])) {
                         $ua->promoteAccount($rels[$i]['xuserid']);
                         $fin[$rels[$i]['xuserid']] = 1;
                      }
                   }
                   */
                   
                   //$ua->promoteManyAccounts($useridarray);
                } else if (0==strcmp($submit,"Reject")) {
                   if (0==strcmp($reason,"Reason")) $reason="";
                   $ua->revertAccount($u,NULL,$reason);
                   //$rels = $ua->getUsersRelated($u);
                   //for ($i=0;$i<count($rels);$i++) $ua->revertAccount($rels[$i]['xuserid']);
                } else if (0==strcmp($submit,"Remove Users From List")) {
                   $segmentid = getParameter('segmentid');
                   if ($ua->removeUserFromList($segmentid,$u)) $vars['msg'] = "Users removed from user segment successfully.<br>";
                   else $vars['error']="Users were not removed from list, please make sure you select checkboxes next to the users you wish to remove.<br>";
                } else if (0==strcmp($submit,"Update User Access")) {
                   $ua->setWebsiteAccess($u,$useraccess);
                } else if (0==strcmp($submit,"Add Users To List")) {
                   $asegmentid = getParameter('a_segmentid');
                   if ($ua->addUserToList($asegmentid,$u)) $vars['msg']="Users were added to your user segment successfully.<br>";
                   else $vars['error']="No users were added to the list, please make sure you select a user segment.<br>";
                } else if ($emailtext != NULL) {
                   $fromuser = getParameter("from");
                   $type = getParameter("type");
                   if ($fromuser==NULL) $fromuser = $ss->getValue("WebsiteContact");
                   if ($type==NULL) $vars['error'] = "No Messages Were Sent.  Please indicate the type of message to send (email/internal message/both).";
                   else {
                      if (0==strcmp($type,"email") || 0==strcmp($type,"both")) {
                         $emailAddr = $ua->sendEmailTo($u,$subjecttext,$emailtext,$fromuser);
                      }
                      if (0==strcmp($type,"shortusmg") || 0==strcmp($type,"usmg") || 0==strcmp($type,"both")) {
                         $uMsg = new UserMessages();
                         $uMsg->newMessage($u,$subjecttext,$fromuser,$emailtext);
                         $usero = $ua->getUser($u);
                         $emailAddr = $usero['email'];
                         if (0==strcmp($type,"shortusmg")) {
                            $shortMsg = $usero['fname'].",\nYou have just received a new message from ".getDefaultTitle().".\n";
                            $shortMsg .= "You can read all your ".getDefaultTitle()." messages by logging in and clicking on the 'Messages' tab:\n".$GLOBALS['baseURL'].".\n\nThanks!";
                            $ua->sendEmailTo($u,"New ".getDefaultTitle()." Message",$shortMsg,$fromuser);
                         }
                      }
          
                      if ($vars['msg']==NULL) $vars['msg'] = "Messages were sent to: ".$emailAddr;
                      else $vars['msg'] .= ", ".$emailAddr;
                   }
                }
             }
         }
      }
   
   
      if (getParameter("refreshcache")==1) {
         $dbLink = new MYSQLaccess;
         $dbLink->deleteCache();
         //$query = "DELETE FROM dbcache;";
         //$dbLink->delete($query);
         $url = "http://www.plasticfilmrecycling.org/jsfcode/jsoncontroller.php?action=refreshcache";
         requestJSON($url,FALSE,TRUE);
      }
   
   
   
   }
   
   //------------------------------------------------------
   // list users' posts
   //------------------------------------------------------
   else if (0 == strcmp($action,"userposts")) {
   
      $postids = getParameter("postid");
      if (is_array($postids)) {
         $up = new UserPost();
         $submit = strtolower(trim(getParameter("submit")));
         for ($i=0; $i<count($postids); $i++) {
            if (0==strcmp($submit,"enable")) {
               $up->updatePostVisibility($postids[$i],1);
            } else if (0==strcmp($submit,"disable")) {
               $up->updatePostVisibility($postids[$i],0);
            } else if (0==strcmp($submit,"delete")) {
               $post = $up->getPost($postids[$i]);
               if ($ua->canUserDelete(isLoggedOn()) && $post!=NULL && ($post['visibility']==0 || $post['visibility']==NULL)) {
                  $up->deletePost($postids[$i],FALSE);
               }
            }
         }
      }
   
      $page="userposts.php";
   }
   
   
   else if (0==strcmp($action,"emaillist")) {
      $page = "listusers.php";
      $emaillist = getParameter("emaillist");
      if($emaillist != NULL) {
         $ua = new UserAcct();
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
            if (!$ua->userExists($emailsArr[$i])) {
               $_SESSION['params']['email'] = $emailsArr[$i];
               $userid = $ua->addUserEmailOnly();
               if ($userid!=NULL && $userid!==FALSE) {
                  $ua->addNotes($userid, "(".getDateForDB().") ADMIN EMAILLIST: ".isLoggedOn());
               }
            }
         }
         $vars['msg'] = "Email(s) added to the system successfully.<br>";
         unset($_SESSION['params']);
      } else {
         $vars['error'] = "No email(s) were added to the sytem.  Your list was empty.<br>";
      }
   }
   
   
   
   
   //------------------------------------------------------
   // Download a csv of users
   //------------------------------------------------------
   else if (0==strcmp($action,"dluserscsv")) {
      ini_set('memory_limit', '128M');
      //$vars['segmentid'] = trim(getParameter("segmentid"));
      //if ($vars['segmentid'] != NULL) $ua->setUserSearchParams($vars['segmentid']);
      $filename = "usercsv/users_".getParameter("segmentid")."_".date("ymd",time()).".csv";
      $entire_file = $ua->getUserCSV(getParameter("segmentid"));
      $template->saveFile($filename,$entire_file);
      $page = "download.php";
      $_SESSION['params']['filename'] = $filename;
   }
   
   else if (0==strcmp($action,"dluserscloningcsv")) {
      $segmentid = getParameter("segmentid");
      $responses = $ua->searchUsersSQLBySegment($segmentid);
      $fromTables = $responses['fromTables'];
      $baseWhere = $responses['baseWhere'];
      $whereClause = $responses['whereClause'];
      $getParams = $responses['getParams'];
      $hiddenFields = $responses['hiddenFields'];
      
      $usertype = getParameter("s_usertype");
      if($usertype==NULL) $usertype = "user";
      $wdname = $usertype." properties";
      $wd = $wdObj->getWebData($wdname);
      $field3 = $wd['wd_id'];
      $field4 = "YES";
      
      $sql = "SELECT DISTINCT u.userid FROM useracct u".$fromTables." WHERE 1=1 ".$baseWhere;
      if ($whereClause!=NULL) $sql .= " AND ".$whereClause;
      //print "\n<!-- user csv query: ".$sql." -->\n";
      $cud = new DownloadUserData();
      $cud->addJob($sql,getParameter("subject"),$field3,$field4);
      $vars['msg'] = "Your user CSV file is scheduled.  Please check the list of CSV files for the status of your file.<br>";
      $page="listuserscloning.php";   
   }
   
   else if (0==strcmp($action,"dlactiveuseremails")) {
      ini_set('memory_limit', '128M');
      //$vars['segmentid'] = trim(getParameter("segmentid"));
      //if ($vars['segmentid'] != NULL) $ua->setUserSearchParams($vars['segmentid']);
      $filename = "usercsv/activeemails_".date("ymd",time()).".csv";
      $entire_file = $ua->getActiveUserCSV();
      $template->saveFile($filename,$entire_file);
      $page = "download.php";
      $_SESSION['params']['filename'] = $filename;
   }
   
   else if (0==strcmp($action,"dlemailsandzips")) {
      ini_set('memory_limit', '128M');
      //$vars['segmentid'] = trim(getParameter("segmentid"));
      //if ($vars['segmentid'] != NULL) $ua->setUserSearchParams($vars['segmentid']);
      $filename = "usercsv/activeemails_".date("ymd",time()).".csv";
      $entire_file = $ua->getUserAndZipCSV();
      $template->saveFile($filename,$entire_file);
      $page = "download.php";
      $_SESSION['params']['filename'] = $filename;
   }
   
   else if (0==strcmp($action,"uploaduserscsv")) {
      ini_set('memory_limit', '256M');
      $filename = saveUploadedFile("usercsv",$GLOBALS['rootDir'].$GLOBALS['csvuploadDir'],"");
      $contents = $template->getFileWithoutSub($GLOBALS['rootDir'].$GLOBALS['csvuploadDir'].$filename,FALSE);
      $ua = new UserAcct();
      $vars['results'] = $ua->loadContents($contents,getDateForDB()." ".$filename);
      $page="loadusersresult.php";
   }
   
   else if (0==strcmp($action,"uploaduserscloning")) {
      if(getParameter("upload")==1) {
         $filename = saveUploadedFile("usercsv",$GLOBALS['rootDir'].$GLOBALS['csvuploadDir'],"");
         $fqfn = $GLOBALS['rootDir'].$GLOBALS['csvuploadDir'].$filename;
         $lud = new LoadUserData();
         $lud->startjob($fqfn);
         $vars['msg'] = "Your file was scheduled to upload";
      }
      $page="uploaduserscloning.php";   
   }
   
   else if (0==strcmp($action,"usersegment")) {
      $page="usersegments.php";
      $ua = new UserAcct();
      $segmentid = trim(getParameter("segmentid"));
      $name = trim(getParameter("name"));
      $descr = trim(getParameter("descr"));
      
      if (getParameter("updatesegment")==1) {
         $userSearchObj = $ua->searchUsersSQL();
         //print "\n\n<!-- ***chj*** current params:\n\n";
         //print_r($userSearchObj);
         //print "\n-->\n\n";
   
         $ua->updateSegment($segmentid,$name,$descr,$userSearchObj['getParams'],getParameter("seggroupid"),getParameter("dropdown"));
      } else if (getParameter("newsegment")==1) {
         $userSearchObj = $ua->searchUsersSQL();
         $ua->newSegment($name,$descr,$userSearchObj['getParams'],getParameter("uselist"),getParameter("seggroupid"),getParameter("dropdown"));
      } else if (getParameter("deletesegment")==1) {
         $delsegmentid = trim(getParameter("delsegmentid"));
         $ua->deleteSegment($delsegmentid);
      } else if (getParameter("scheduleemails")==1) {
         $shortname = trim(getParameter("shortname"));
         $fromemails = explode(",",trim(getParameter("fromemails")));
         $numOfEmails = count($fromemails);
         
         if ($segmentid!=NULL && $segmentid>0 && $numOfEmails>0 && $shortname!=NULL && $fromemails[0]!=NULL) {
            $type = trim(getParameter("type"));
            $priority = trim(getParameter("priority"));
            $scheduler = new Scheduler();
            $scheduler->sendGroupMessage($segmentid,$type,$fromemails,$shortname,$priority);
            $vars['msg'] = "Emails are successfully scheduled for the segment you selected.<br>";
         } else {
            $vars['error'] = "Emails were not scheduled.  Please make sure you select an email template, and you indicate email address(es) to send email from.<br>";
         }
      } else if (getParameter("removesegmentid")==1) {
         if ($ua->removeInclSegmentId($segmentid,trim(getParameter("inclsegmentid")))) $vars['msg']="Segment was removed from your list of referenced segments<br>";
         else $vars['error'] = "Segment could not be removed for your referenced segments.<br>";
      } else if (getParameter("addsegmentid")==1) {
         if ($ua->addInclSegmentId($segmentid,trim(getParameter("inclsegmentid")))) $vars['msg']="Segment was added to your list of referenced segments.<br>";
         else $vars['error'] = "Segment could not be added to your referenced segments.<br>";
      } else if (getParameter("addsegmentcondition")==1) {
         if ($ua->addSegmentCondition ($segmentid,trim(getParameter("inclsegmentcondition")))) $vars['msg']="Segment condition updated.<br>";
         else $vars['error'] = "Segment condition could not be changed.<br>";
      } else if (getParameter("newseggroup")==1) {
         $ua->newSegmentGroup(getParameter("name"),getParameter("parentid"));
         $vars['msg']="User segment folder added.<br>";
      } else if (getParameter("updateseggroup")==1) {
         $seggroupid = getParameter("seggroupid");
         $parentid = getParameter("parentid");
         if ($segroupid==$parentid) {
            $vars['error']="Cannot change the parent folder to this value.<br>";
         } else {
            $ua->updateSegmentGroup($seggroupid,getParameter("name"),$parentid);
            $vars['msg']="User segment folder updated.<br>";
         }
      } else if (getParameter("addusers")==1) {
         $ua->addEmailsToSegment($segmentid,getParameter("emaillist"));
         $vars['msg'] = "Users were added - please list segment users to verify.";
      }
      $ua->setUserSearchParams($segmentid);
   }
   
   //------------------------------------------------------
   // Scheduled jobs...
   //------------------------------------------------------
   else if (0 == strcmp($action,"scheduledemails")) {
      $scheduler = new Scheduler();
      $page="viewscheduledemails.php";
   
      $submit = getParameter("submit");
      $emailids = getParameter("a_semailid");
      if (is_array($emailids)) {
         for ($i=0; $i<count($emailids); $i++) {
            if (0==strcmp($submit,"Delete")) {
               //$semailobj = $scheduler->getScheduledEmails($emailids[$i]);
               $scheduler->removeSchedEmail($emailids[$i]);
            } else if (0==strcmp($submit,"Pause")) {
               $semailobj = $scheduler->getScheduledEmails($emailids[$i],"NEW");
               $scheduler->updateEmailJob($semailobj['emails'][0]['semailid'],"PAUSED");
            } else if (0==strcmp($submit,"Unpause")) {
               $semailobj = $scheduler->getScheduledEmails($emailids[$i],"PAUSED");
               $scheduler->updateEmailJob($semailobj['emails'][0]['semailid'],"NEW");
            } else if (0==strcmp($submit,"Process Now")) {
               if ($scheduler->processSchedEmail($emailids[$i])) $vars['msg']="Email was sent successfully<br>";
               else $vars['error'] = "Job was not processed.  Please make sure it is in 'NEW' status.<br>";
            } else if (0==strcmp($submit,"Re-send Email")) {
               $scheduler->copyEmailJob($emailids[$i]);
            }
         }
      }
   }
   
   else if (0 == strcmp($action,"showemail")) {
      $page="showemail.php";
   }
   
   //------------------------------------------------------
   // add a user to the system
   //------------------------------------------------------
   else if (0 == strcmp($action,"adduser")) {
      ini_set('memory_limit', '128M');
      $page="listusers.php";
      $newuserid = $ua->addUser(NULL,TRUE,TRUE);
      if ($newuserid>0) {
         //$ua->grantAdminAccess($newuserid);
         $vars['userid'] = $newuserid;
         $page = "usermod.php";
         $vars['msg'].="User Created Successfully.<BR>";
      } else if ($newuserid==-1) {
         $vars['error'] .= "Please enter a valid email address.<BR>";
      } else if ($newuserid==-2) {
         $vars['error'] .= "Your email addresses do not match.  Please verify that you have entered the same email address twice.<BR>";
      } else if ($newuserid==-3) {
         $vars['error'] .= "That email address already has an existing user account.  Try resetting you password.<BR>";
         $vars['showforgotpw'] = 1;
      } else if ($newuserid==-4) {
         $vars['error'] .= "Please verify that your passwords match and that it is at least 6 characters in length.<BR>";
      } else {
         $vars['error'] .= "Make sure your passwords are the same, and the email is valid.  This email address may already have an account.<BR>";
      }
   }
   
   else if (0 == strcmp($action,"adduserform")) {
      $page="addusercloning.php";
   }
   
   else if (0 == strcmp($action,"addusercloning")) {
      ini_set('memory_limit', '128M');
      $page="addusercloning.php";
      $newuserid = $ua->addUser(NULL,TRUE,TRUE);
      if ($newuserid>0) {
         //$ua->grantAdminAccess($newuserid);
         $vars['userid'] = $newuserid;
         $page = "usermodcloning.php";
         $vars['msg'].="User Created Successfully.<BR>";
      } else if ($newuserid==-1) {
         $vars['error'] .= "Please enter a valid email address.<BR>";
      } else if ($newuserid==-2) {
         $vars['error'] .= "Your email addresses do not match.  Please verify that you have entered the same email address twice.<BR>";
      } else if ($newuserid==-3) {
         $vars['error'] .= "That email address already has an existing user account.  Try resetting you password.<BR>";
         $vars['showforgotpw'] = 1;
      } else if ($newuserid==-4) {
         $vars['error'] .= "Please verify that your passwords match and that it is at least 6 characters in length.<BR>";
      } else {
         $vars['error'] .= "Make sure your passwords are the same, and the email is valid.  This email address may already have an account.<BR>";
      }
   }
   
   //------------------------------------------------------
   // add a user to the system and create a relationship
   //------------------------------------------------------
   else if (0 == strcmp($action,"adduserandrelation")) {
      ini_set('memory_limit', '128M');
      $page="usermod.php";
      $newuserid = $ua->addUser(NULL,TRUE,TRUE);
      if ($newuserid>0) {
         //add the relationship...
         $rel_type = getParameter("rel_type");
         $vars['userid'] = getParameter("userid");
         $ua->addUserRelationship($vars['userid'],$newuserid,$rel_type);
         $vars['msg'].="User Contact for this organization Created Successfully.<BR>";
      } else if ($newuserid==-1) {
         $vars['error'] .= "Please enter a valid email address.<BR>";
      } else if ($newuserid==-2) {
         $vars['error'] .= "Your email addresses do not match.  Please verify that you have entered the same email address twice.<BR>";
      } else if ($newuserid==-3) {
         $vars['error'] .= "That email address already has an existing user account.  Try resetting you password.<BR>";
         $vars['showforgotpw'] = 1;
      } else if ($newuserid==-4) {
         $vars['error'] .= "Please verify that your passwords match and that it is at least 6 characters in length.<BR>";
      } else {
         $vars['error'] .= "Make sure your passwords are the same, and the email is valid.  This email address may already have an account.<BR>";
      }
   }
   else if (0 == strcmp($action,"adduserandrelationcloning")) {
      ini_set('memory_limit', '128M');
      $page="usermodcloning.php";
      $newuserid = $ua->addUser(NULL,TRUE,TRUE);
      if ($newuserid>0) {
         //add the relationship...
         $rel_type = getParameter("rel_type");
         $vars['userid'] = getParameter("userid");
         $ua->addUserRelationship($vars['userid'],$newuserid,$rel_type);
         $vars['msg'].="User Contact for this organization Created Successfully.<BR>";
      } else if ($newuserid==-1) {
         $vars['error'] .= "Please enter a valid email address.<BR>";
      } else if ($newuserid==-2) {
         $vars['error'] .= "Your email addresses do not match.  Please verify that you have entered the same email address twice.<BR>";
      } else if ($newuserid==-3) {
         $vars['error'] .= "That email address already has an existing user account.  Try resetting you password.<BR>";
         $vars['showforgotpw'] = 1;
      } else if ($newuserid==-4) {
         $vars['error'] .= "Please verify that your passwords match and that it is at least 6 characters in length.<BR>";
      } else {
         $vars['error'] .= "Make sure your passwords are the same, and the email is valid.  This email address may already have an account.<BR>";
      }
   }
   //------------------------------------------------------
   // delete a user from the system
   //------------------------------------------------------
   else if (0 == strcmp($action,"deleteUserAcct")) {
        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
           if (0==strcmp(isLoggedOn(),getParameter('userid'))){
            $vars['error'] .= "You cannot delete yourself.<BR>";
           }
           else {
             if ($ua->deleteUserAcct(getParameter('userid')))
            $vars['msg'] .= "User was deleted successfully.<BR>";
             else
            $vars['error'] .= "User Could not be deleted.  Please try again.<BR>";
           }
        }
        else {
         $vars['error'] .= "You do not have the authority to delete users.<BR>";	  
        }
   
       $page="listusers.php";
   }
   //------------------------------------------------------
   // delete a user from the system
   //------------------------------------------------------
   else if (0 == strcmp($action,"userlistaction")) {
         //error_reporting(E_ALL);
        $page = getParameter("phpinclude");
        if ($page==null) $page="listusers.php";
   
        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
           $userid = getParameter('userid');
           $submit = getParameter('subaction');
           $useraccess = getParameter('privacy');
           $emailtext = $template->doSubstitutions(getParameter('emailtext'));
           $subjecttext = $template->doSubstitutions(getParameter('subjecttext'));
           if (is_array($userid)) {
               for ($i=0; $i<count($userid); $i++) {
                  $u = $userid[$i];
                  if (0==strcmp($submit,"Delete Selected Users")) {
                     //print "\n<!-- delete account 1.... -->\n";
                     if ($u!=isLoggedOn()) $ua->deleteUserAcct($u);
                  } else if (0==strcmp($submit,"Remove Users From Segment")) {
                     $segmentid = getParameter('segmentid');
                     if ($ua->removeUserFromList($segmentid,$u)) $vars['msg'] = "Users removed from user segment successfully.<br>";
                     else $vars['error']="Users were not removed from list, please make sure you select checkboxes next to the users you wish to remove.<br>";
                  } else if (0==strcmp($submit,"Update User Access")) {
                     $ua->setWebsiteAccess($u,$useraccess);
                  } else if (0==strcmp($submit,"Add Users To Segment")) {
                     $asegmentid = getParameter('a_segmentid');
                     if ($ua->addUserToList($asegmentid,$u)) $vars['msg']="Users were added to your user segment successfully.<br>";
                     else $vars['error']="No users were added to the list, please make sure you select a user segment.<br>";
                  } else if ($emailtext != NULL) {
                     $fromuser = getParameter("from");
                     $type = getParameter("type");
                     if ($fromuser==NULL) $fromuser = $ss->getValue("WebsiteContact");
                     if ($type==NULL) $vars['error'] = "No Messages Were Sent.  Please indicate the type of message to send (email/internal message/both).";
                     else {
                        if (0==strcmp($type,"email") || 0==strcmp($type,"both")) {
                           $emailAddr = $ua->sendEmailTo($u,$subjecttext,$emailtext,$fromuser);
                        }
                        if (0==strcmp($type,"shortusmg") || 0==strcmp($type,"usmg") || 0==strcmp($type,"both")) {
                           $uMsg = new UserMessages();
                           $uMsg->newMessage($u,$subjecttext,$fromuser,$emailtext);
                           $usero = $ua->getUser($u);
                           $emailAddr = $usero['email'];
                           if (0==strcmp($type,"shortusmg")) {
                              $shortMsg = $usero['fname'].",\nYou have just received a new message from ".getDefaultTitle().".\n";
                              $shortMsg .= "You can read all your ".getDefaultTitle()." messages by logging in and clicking on the 'Messages' tab:\n".$GLOBALS['baseURL'].".\n\nThanks!";
                              $ua->sendEmailTo($u,"New ".getDefaultTitle()." Message",$shortMsg,$fromuser);
                           }
                        }
      
                        if ($vars['msg']==NULL) $vars['msg'] = "Messages were sent to: ".$emailAddr;
                        else $vars['msg'] .= ", ".$emailAddr;
                     }
                  }
               }
           } else {
               $vars['error'] .= "Please select users before you try that action.<BR>";	  
           }
        }
        else {
           $vars['error'] .= "You do not have the authority to change the user list.<BR>";	  
        }
   
   }
   
   //------------------------------------------------------
   // Change a user's password
   //------------------------------------------------------
   else if (0 == strcmp($action,"useroverride")) {
      $page = $GLOBALS['baseURL'];
      $vars['redirect']=1;
      $vars['userid'] = getParameter('userid');
      $ua->addUserToSession($ua->getUser($vars['userid']));
   }
   
   //------------------------------------------------------
   // Change a user's password
   //------------------------------------------------------
   else if (0 == strcmp($action,"modifypassword")) {
      //ini_set('memory_limit', '128M');
      $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
      $vars['p_userid'] = getParameter('p_userid');
      //print "\n<!-- CHECKING PERMISSIONS MODIFYPASSWORD -->\n";
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) || $vars['p_userid']==isLoggedOn()) {
         //print "\n<!-- CALLING MODIFYPASSWORD -->\n";
         if ($ua->modifyPassword(getParameter('password'), getParameter('cpassword'), getParameter('oldpassword'), $vars['p_userid'], TRUE)) {
            $vars['msg'] .= "User password was updated.<BR>";
         } else {
            $vars['error'] .= "Password could not be changed.  Pleas make sure you entered the correct password.<BR>";
         }
      } else {
         $vars['error'] .= "You do not have the authority to change passwords.<BR>";	  
      }
      $page="usermod.php";
   }
   else if (0 == strcmp($action,"modifypasswordcloning")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['p_userid'] = getParameter('p_userid');
        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) || $vars['p_userid']==isLoggedOn()) {
             if ($ua->modifyPassword(getParameter('password'), getParameter('cpassword'), getParameter('oldpassword'), $vars['p_userid'], TRUE)) {
                $vars['msg'] .= "User password was updated.<BR>";
             }
             else {
            $vars['error'] .= "Password could not be changed.  Pleas make sure you entered the correct password.<BR>";
             }
        }
        else {
         $vars['error'] .= "You do not have the authority to change passwords.<BR>";	  
        }
       $page="usermodcloning.php";
   }
   //------------------------------------------------------
   // Change a user's info
   //------------------------------------------------------
   else if (0 == strcmp($action,"modifyuser")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
             if ($ua->modifyUser(($vars['userid']==isLoggedOn()))) $vars['msg'] .= "User info was updated.<BR>";
             else $vars['error'] .= "Please make sure you entered all the required data.<BR>";
        } else {
           if ($vars['userid']==isLoggedOn()) {
              $_POST['ulevel'] = $_SESSION['s_user']['ulevel'];
              if ($ua->modifyUser(TRUE)) $vars['msg'] .= "User info was updated.<BR>";
              else $vars['error'] .= "Please make sure you entered all the required data.<BR>";
           } else {
              $vars['error'] .= "You do not have the authority to change user information.<BR>";
           }
        }
       $page="usermod.php";
   }
   else if (0 == strcmp($action,"modifyusercloning")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
        if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
             if ($ua->modifyUser(($vars['userid']==isLoggedOn()))) {
                $vars['msg'] .= "User info was updated.<BR>";
                if (0==strcmp(getParameter("submit"),"Save and Approve")) $ua->promoteManyAccounts($vars['userid']);
             } else {
                $vars['error'] .= "Please make sure you entered all the required data.<BR>";
             }
        } else {
           if ($vars['userid']==isLoggedOn()) {
              $_POST['ulevel'] = $_SESSION['s_user']['ulevel'];
              if ($ua->modifyUser(TRUE)) $vars['msg'] .= "User info was updated.<BR>";
              else $vars['error'] .= "Please make sure you entered all the required data.<BR>";
           } else {
              $vars['error'] .= "You do not have the authority to change user information.<BR>";
           }
        }
       $page="usermodcloning.php";
   }
   else if (0 == strcmp($action,"activateuser")) {
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter('userid');
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
         if ($ua->activateAccount($vars['userid'])) $vars['msg'] = "User activated successfully.<br>";
         else $vars['msg'] = "User could not be activated.<br>";
      } else {
         $vars['error'] .= "You do not have the authority to change user information.<BR>";
      }
      $page="usermod.php";
   }
   else if (0 == strcmp($action,"deactivateuser")) {
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter('userid');
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
         if ($ua->deactivateAccount($vars['userid'])) $vars['msg'] = "User deactivated successfully.<br>";
         else $vars['msg'] = "User could not be deactivated.<br>";
      } else {
         $vars['error'] .= "You do not have the authority to change user information.<BR>";
      }
      $page="usermod.php";
   }
   else if (0 == strcmp($action,"activateusercloning")) {
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter('userid');
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
         if ($ua->activateAccount($vars['userid'])) $vars['msg'] = "User activated successfully.<br>";
         else $vars['msg'] = "User could not be activated.<br>";
      } else {
         $vars['error'] .= "You do not have the authority to change user information.<BR>";
      }
      $page="usermodcloning.php";
   }
   else if (0 == strcmp($action,"deactivateusercloning")) {
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter('userid');
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
         if ($ua->deactivateAccount($vars['userid'])) $vars['msg'] = "User deactivated successfully.<br>";
         else $vars['msg'] = "User could not be deactivated.<br>";
      } else {
         $vars['error'] .= "You do not have the authority to change user information.<BR>";
      }
      $page="usermodcloning.php";
   }
   //------------------------------------------------------
   // Change a user's authority/access level
   //------------------------------------------------------
   else if (0 == strcmp($action,"changeuseraccess")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
   
       if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
         $ua->setUserAccessLevels($vars['userid'],getParameter('useraccess'));
         $vars['msg'] .= "User authority access successfully updated.<BR>";
       }
       else {
         $vars['error'] .= "You do not have the authority to change user authority level.<BR>";
       }
   
       $page="usermod.php";
   }
   else if (0 == strcmp($action,"changeuseraccesscloning")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
   
       if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
         $ua->setUserAccessLevels($vars['userid'],getParameter('useraccess'));
         $vars['msg'] .= "User authority access successfully updated.<BR>";
       }
       else {
         $vars['error'] .= "You do not have the authority to change user authority level.<BR>";
       }
   
       $page="usermodcloning.php";
   }
   
   
   else if (0 == strcmp($action,"jsfstats")) {
       $page="jsfstats.php";
   }
   
   
   else if (0 == strcmp($action,"jsfreports")) {
       $page="jsfreports.php";
   }
   
   
   
   //------------------------------------------------------
   // Show the details for a user
   //------------------------------------------------------
   else if (0 == strcmp($action,"usermod")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
       $page="usermod.php";
   }
   
   else if (0 == strcmp($action,"usermodcloning")) {
      ini_set('memory_limit', '128M');
       $vars['email'] = convertString(strtolower(trim(getParameter('email'))));
       $vars['userid'] = getParameter('userid');
   
      if (getParameter("refreshgeo")==1) {
         $ua->getUserGeoCode($vars['userid'],TRUE,NULL,TRUE);  
      }
      if (getParameter("approveuser")==1) {
         //$ua->promoteAccount($vars['userid']);
         //$rels = $ua->getUsersRelated($vars['userid']);
         //for ($i=0;$i<count($rels);$i++) $ua->promoteAccount($rels[$i]['xuserid']);
         $ua->promoteManyAccounts($vars['userid']);  
      }
      if (getParameter("markupdated")==1) {
         $ua->setLastUpdated($vars['userid']);
      }
      if (getParameter("verifyuser")==1) {
         $ua->updateField($vars['userid'],"lastverified",getDateForDB(),FALSE,FALSE);
      }
      if (getParameter("copyuser")==1) {
          //error_reporting(E_ALL);
          $vars['userid'] = $ua->copyAccount($vars['userid']);
      }
      if (getParameter("rejectuser")==1) {
         $ua->revertAccount($vars['userid']);
         //$rels = $ua->getUsersRelated($vars['userid']);
         //for ($i=0;$i<count($rels);$i++) $ua->revertAccount($rels[$i]['xuserid']);
      }
      if (getParameter("removeparentid")==1) {
         $ua->removeParentID($vars['userid']);
      }
      if (getParameter("addparentid")==1) {
         $ua->addParentID($vars['userid'],getParameter("parentid"));
      }
      if (0==strcmp(getParameter("subaction"),"addchildren")) {
         if($ua->addMultipleParentIDs($vars['userid'],getParameter("userids"))) {
            $vars['msg'] = "Children added successfully.";
         } else {
            $vars['error'] = "Children could not be added.  Please check that you are adding a numeric list of comma-separated user ids.";
         }
      }
      if (0==strcmp(getParameter("subaction"),"manualgeo")) {
         $names = array();
         $names[] = "lat";
         $names[] = "lng";
         $values = array();
         $values[] = getParameter("lat");
         $values[] = getParameter("lng");
         $ua->updateMultipleFields($vars['userid'],$names,$values);
      }
   
       $page="usermodcloning.php";
   }
   
   else if (0 == strcmp($action,"usermodupdatepropscloning")) {
      $vars['wd_row_id'] = getParameter("wd_row_id");
      $vars['wd_id'] = getParameter("wd_id");
      $vars['wd_row_id'] = $wdObj->submitSurvey($vars['wd_id'], $vars['wd_row_id'], true);
      $vars['userid'] = getParameter("userid");
      
      $results = $wdObj->getRows($vars['wd_id'],NULL,1,NULL,FALSE,$vars['userid']);
      if(0==strcmp($results['results'][0]['dbmode'],"UPDATED")) $ua->setLastUpdated($vars['userid']);
      if (0==strcmp(getParameter("submit"),"Save and Approve")) $ua->promoteManyAccounts($vars['userid']);
      $page="usermodcloning.php";
   }
   
   
   else if (0 == strcmp($action,"usermodrelupdatecloning")) {
       $vars['userid'] = getParameter('userid');
   
      if (getParameter("updatereluser")==1) {
         $prfx = getParameter("prefix");
         $vars['updateuserid'] = getParameter($prfx."updateuserid");
         
         $names = array();
         $values = array();
         $names[] = "fname";
         $values[] = getParameter($prfx."fname");
         $names[] = "lname";
         $values[] = getParameter($prfx."lname");
         $names[] = "company";
         $values[] = getParameter($prfx."company");
         $names[] = "title";
         $values[] = getParameter($prfx."title");
         $names[] = "phonenum";
         $values[] = getParameter($prfx."phonenum");
         $names[] = "addr1";
         $values[] = getParameter($prfx."addr1");
         $names[] = "addr2";
         $values[] = getParameter($prfx."addr2");
         $names[] = "city";
         $values[] = getParameter($prfx."city");
         $names[] = "state";
         $values[] = getParameter($prfx."state");
         $names[] = "zip";
         $values[] = getParameter($prfx."zip");
         $names[] = "country";
         $values[] = getParameter($prfx."country");
         $names[] = "website";
         $values[] = getParameter($prfx."website");
         $ua->updateMultipleFields($vars['updateuserid'],$names,$values);
         $ua->setLastUpdated($vars['userid'],$_SESSION['s_user']['emailAddress']);
         $ua->setLastUpdated($vars['updateuserid'],$_SESSION['s_user']['emailAddress']);
         
         if (0==strcmp(getParameter("submit"),"Save and Approve")) $ua->promoteManyAccounts($vars['userid']);
      }
   
       $page="usermodcloning.php";
   }
   
   else if (0 == strcmp($action,"usercomparecloning")) {
       //error_reporting(E_ALL);
       $vars['userid'] = getParameter('userid');
       $page="usercomparecloning.php";
       $subaction = getParameter('subaction');
       if (0==strcmp($subaction,"approvesinglefield")) {
         $field = getParameter('field');
         $value = getParameter('value');
         $ua->updateField($vars['userid'],$field, $value,TRUE);
       } else if (0==strcmp($subaction,"rejectsinglefield")) {
         $field = getParameter('field');
         $value = getParameter('value');
         $ua->updateField($vars['userid'],$field, $value,FALSE);
       } else if (0==strcmp($subaction,"approve")) {
         $ua->promoteManyAccounts($vars['userid']);  
         //$ua->promoteAccount($vars['userid']);
         //$rels = $ua->getUsersRelated($vars['userid']);
         //for ($i=0;$i<count($rels);$i++) $ua->promoteAccount($rels[$i]['xuserid']);
       } else if (0==strcmp($subaction,"reject")) {
         $ua->revertAccount($vars['userid']);
         //$rels = $ua->getUsersRelated($vars['userid']);
         //for ($i=0;$i<count($rels);$i++) $ua->revertAccount($rels[$i]['xuserid']);
       }
   }
   
   //------GLOSSARY CODE START------------------------------------------------
   else if (0 == strcmp($action,"displayglossaries")) {
      $page = "displayglossaries.php";
   }
   else if (0 == strcmp($action,"editglossary")) {
      $vars['glossid'] = getParameter('glossid');
      $glossary = new Glossary($vars['glossid']);
      $subaction = getParameter("subaction");
      if (0==strcmp($subaction,"Update")) {
         if ($glossary->editGlossary(getParameter("glosstitle"), getParameter("descr"))){
            $vars['msg']="Glossary Updated Successfully";
         } else $vars['error']="Error occured, your glossary could not be updated.";
      } else if (0==strcmp($subaction,"Delete")) {
         if ($glossary->removeGlossary()){
            $vars['msg']="Glossary Deleted Successfully";
         } else $vars['error']="Please remove all glossary terms before trying to delete the glossary.";
      }
      $page = "displayglossaries.php";
   }
   else if (0 == strcmp($action,"addglossary")) {
      $glossary = new Glossary();
      $glossary->newGlossary(getParameter("glosstitle"), getParameter("descr"));
      $page = "displayglossaries.php";
   }
   else if (0 == strcmp($action,"displayglossaryterms")) {
      $vars['glossid'] = getParameter('glossid');
      $page = "displayglossaryterms.php";
   }
   else if (0 == strcmp($action,"editglossaryterm")) {
      $vars['glossaryid'] = getParameter('glossaryid');
      $vars['glossid'] = getParameter('glossaryid');
      $glossary = new Glossary($vars['glossaryid']);
      $vars['term'] = getParameter('term');
      $subaction = getParameter("subaction");
      if (0==strcmp($subaction,"Update")) {
         $glossary->removeTerm($vars['term']);
         $glossary->insertTerm($vars['term'], getParameter("definition"), getParameter("alternates"));
         $vars['msg']="Glossary Term Updated Successfully";
      } else if (0==strcmp($subaction,"Delete")) {
         if ($glossary->removeTerm($vars['term'])){
            $vars['msg']="Glossary Term Deleted Successfully";
         } else $vars['error']="Unable to delete that glossary term.  Please try again later.";
      }
      $page = "displayglossaryterms.php";
   }
   else if (0 == strcmp($action,"addglossaryterm")) {
      $vars['glossaryid'] = getParameter('glossaryid');
      $vars['glossid'] = getParameter('glossaryid');
      $glossary = new Glossary($vars['glossaryid']);
      $glossary->insertTerm(getParameter("term"), getParameter("definition"), getParameter("alternates"));
      $page = "displayglossaryterms.php";
   }
   
   //------GLOSSARY CODE END------------------------------------------------
   
   
   
   
   else if (0 == strcmp($action,"buildsearchindex")) {
      $page = "buildsearchindex.php";
   }
   
   
   
   //------MENU CODE START------------------------------------------------
   else if (0 == strcmp($action,"displayallmenus")) {
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),3)) {
         $page = "displayallmenus.php";
      } else $vars['error']="You must have the authority to activate/deactivate content to access site Menus.";
   
   }
   else if (0 == strcmp($action,"editmenu")) {
      $vars['menuid'] = getParameter('menuid');
      $subaction = getParameter("subaction");
      if (0==strcmp($subaction,"Update")) {
         if ($menu->editMenu($vars['menuid'], getParameter("menutitle"), getParameter("descr"), getParameter("fs"), getParameter("fc"), getParameter("fch"), getParameter("bgc"), getParameter("bgch"), getParameter("leftimg"), getParameter("rightimg"), getParameter("menubg"))){
            $vars['msg']="Menu Updated Successfully";
         } else $vars['error']="Error occured, Menu could not be updated.";
      } else if (0==strcmp($subaction,"Delete")) {
         if ($menu->removeMenu($vars['menuid'])){
            $vars['msg']="Menu Deleted Successfully";
         } else $vars['error']="Please remove all menu items before trying to delete the menu.";
      }
      $page = "displayallmenus.php";
   }
   else if (0 == strcmp($action,"addmenu")) {
      $vars['menuid'] = getParameter('menuid');
      $menu->newMenu(getParameter("menutitle"), getParameter("descr"), getParameter("fs"), getParameter("fc"), getParameter("fch"), getParameter("bgc"), getParameter("bgch"), getParameter("leftimg"), getParameter("rightimg"), getParameter("menubg"));
      $page = "displayallmenus.php";
   }
   //------------------------------------------------------
   // View the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"displaysitemenu")) {
       $vars['menuid'] = getParameter('menuid');
      $page = "displaysitemenu.php";
   }
   //------------------------------------------------------
   // Add to the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"displayaddmenuitem")) {
       $vars['menuid'] = getParameter('menuid');
      $vars['parent'] = getParameter("itemid");
      $page = "displayaddmenuitem.php";
   }
   //------------------------------------------------------
   // Edit an item from the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"displayeditmenuitem")) {
      $vars['menuid'] = getParameter('menuid');
      $vars['itemid'] = getParameter("itemid");
      $mi = $menu->getMenuItem($vars['itemid'],$vars['menuid']);
      $vars['parent'] = $mi['parent'];
      $page = "displayaddmenuitem.php";
   }
   //------------------------------------------------------
   // Add an item to the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"addmenuitem")){
      $vars['fn'] = saveUploadedFile("icon",$GLOBALS['srvyDir'],"mi_");
      $vars['menuid'] = getParameter('menuid');
      $itemid = $menu->addMenuItem(getParameter("parent"),convertString(getParameter("name")),convertString(getParameter("menuname")),getParameter("width"),getParameter("url"),getParameter("sequence"),getParameter("status"),$vars['menuid'],getParameter("privacy"),getParameter("adminprivacy"),$vars['fn'],getParameter("onlinest"));
      $vars['msg'] .= "Menu item ".$itemid." was successfully added.<BR>";
      //if (0==strcmp(getParameter("submit"),"Save and Edit Page")) {
      //   $vars['view'] = $ss->getVeiwFromURL(getParameter("url"));
      //   $page = "showshortcut.php";
      //}
      //else {
         $page = "displaysitemenu.php";
      //}
   }
   //------------------------------------------------------
   // Edit an item from the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"editmenuitem")){
      $vars['fn'] = saveUploadedFile("icon",$GLOBALS['srvyDir'],"mi_");
      $vars['menuid'] = getParameter('menuid');
      $vars['itemid'] = getParameter("itemid");
      $vars['parent'] = getParameter("parent");
      $menu->editMenuItem($vars['itemid'],$vars['parent'],convertString(getParameter("name")),convertString(getParameter("menuname")),getParameter("width"),getParameter("url"),getParameter("sequence"),getParameter("status"),$vars['menuid'],getParameter("privacy"),getParameter("adminprivacy"),$vars['fn'],getParameter("onlinest"));
      $vars['msg'] .= "Menu item was successfully changed.<BR>";
      $page = "displayaddmenuitem.php";
   }
   //------------------------------------------------------
   // Remove an item from the site menu
   //------------------------------------------------------
   else if (0 == strcmp($action,"removemenuitem")){
      $vars['menuid'] = getParameter('menuid');
      $vars['itemid'] = getParameter("itemid");
      if ($menu->removeMenuItem($vars['itemid'],$vars['menuid'])) {
         $vars['msg'] .= "Menu item deleted successfully.<BR>";
      }
      else {
         $vars['error'] .= "Please remove any menu children before deleting a menu item.<BR>";
      }
      $page = "displaysitemenu.php";
   }
   //---END OF MENU CODE---------------------------------------------------
   
   
   //------------------------------------------------------
   // View the public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"displaypubcontent")) {
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   //------------------------------------------------------
   // Edit public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"editpubcont")) {
      $publishdate = getParameter("publishdate_y")."-".getParameter("publishdate_m")."-".getParameter("publishdate_d");
      $pubcont->updateContent(getParameter("contid"), $publishdate, getParameter("datedisp"), getParameter("ctype"), getParameter("title"), getParameter("other"), getParameter("contdata"), getParameter("link1url"), getParameter("link1display"), getParameter("link2url"), getParameter("link2display"));
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   //------------------------------------------------------
   // Remove public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"removepubcont")) {
      $pubcont->removeContent(getParameter("contid"));
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   //------------------------------------------------------
   // Add public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"addpubcont")) {
   
      $publishdate = getParameter("publishdate_y")."-".getParameter("publishdate_m")."-".getParameter("publishdate_d");
      $pubcont->addContent($publishdate, getParameter("datedisp"), getParameter("ctype"), getParameter("title"), getParameter("other"), getParameter("contdata"), getParameter("link1url"), getParameter("link1display"), getParameter("link2url"), getParameter("link2display"));
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   //------------------------------------------------------
   // Disable public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"disablepubcont")) {
      $pubcont->disablepubcont(getParameter("contid"));
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   //------------------------------------------------------
   // Enable public content
   //------------------------------------------------------
   else if (0 == strcmp($action,"enablepubcont")) {
      $pubcont->enablepubcont(getParameter("contid"));
      $vars['ctype'] = getParameter("dispctype");
      $page = "displaypubcontent.php";
   }
   
   //------------------------------------------------------
   // cms themes
   //------------------------------------------------------
   else if (0 == strcmp($action,"viewthemes")) {
   
      $themeid = getParameter("themeid");
      $themename = convertString(trim(getParameter("themename")));
      $priority = getParameter("priority");
      $status = getParameter("status");
      $startd = getParameter("startd");
      $startm = getParameter("startm");
      $endd = getParameter("endd");
      $endm = getParameter("endm");
      $startd = ($startm-1)*32+$startd;
      $endd = ($endm-1)*32+$endd;
   
      $add = getParameter("add");
      $update = getParameter("update");
      
      $page="listthemes.php";
   
      if ($add == 1 && $ua->doesUserHaveAccessToLevel(isLoggedOn(),4)) $ss->newTheme($themename,$priority,$startd,$endd,$status);
      else if ($update ==1) {
         $submit = getParameter("submit");
         if (0==strcmp($submit,"Update") && $ua->doesUserHaveAccessToLevel(isLoggedOn(),4)) {
            $ss->updateTheme($themeid,$themename,$priority,$startd,$endd,$status);
            $vars['msg']="Theme '".$themename."' was updated successfully.<br>";
         } else if (0==strcmp($submit,"Delete") && $ua->doesUserHaveAccessToLevel(isLoggedOn(),4) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ss->removeTheme($themeid);
         } else if (0==strcmp($submit,"View Info") && $ua->doesUserHaveAccessToLevel(isLoggedOn(),4)) {
   
            if (getParameter("newrule")==1) {
               $ruletype = getParameter('ruletype');
               $field1 = getParameter("field1");
               $field2 = getParameter("field2");
               $field3 = getParameter("field3");
               $field4 = getParameter("field4");
               $field5 = getParameter("field5");
   
               if (0==strcmp($ruletype,"SEARCH")) {
                  $questions = $surveyObj->getAllQuestions($field1);
                  for ($i=0; $i<count($questions); $i++) {
                     if (0==strcmp($questions[$i]['label'],$field2)) {
                        if (0==strcmp($questions[$i]['question_type'],"RADIO") || 0==strcmp($questions[$i]['question_type'],"CHECKBOX")) {
                           $field2="cmsq_".$questions[$i]['question_id'];
                           break;
                        }
                        else if (0!=strcmp($questions[$i]['question_type'],"INFO") && 0!=strcmp($questions[$i]['question_type'],"SPACER")){
                           $field2="cmsz_".$questions[$i]['question_id'];
                           break;
                        }
                     }
                     else if (0==strcmp("MIN_".$questions[$i]['label'],$field2)) {
                        $field2="cmsl_".$questions[$i]['question_id'];
                        break;
                     }
                     else if (0==strcmp("MAX_".$questions[$i]['label'],$field2)) {
                        $field2="cmsh_".$questions[$i]['question_id'];
                        break;
                     }
                  }
               }
   
               $ss->newThemeRule($themeid,$adspaceid,$ruletype,$field1,$field2,$field3,$field4,$field5);
            }
            if (getParameter("removerule")==1) {
               $ss->removeThemeRule(getParameter('ruleid'));
            }
   
            $vars['themeid'] = $themeid;
            $page="themedetail.php";
         }
         else $vars['error'] = "Sorry, you do not have the proper authorization to perform that task.<br>";
      }
      else if ($add == 1) $vars['error'] = "You do not have the proper authorization to perform that task.<br>";
   
   }
   
   //------------------------------------------------------
   // cms system properties
   //------------------------------------------------------
   else if (0 == strcmp($action,"viewsystemproperties")) {
      $page="viewsystemproperties.php";
   
      $name = getParameter("name");
      $value = getParameter("value");
      
      if (isParameterSet("theme")) $theme = getParameter("theme");
      else $theme = 0;
   
      $submit = getParameter("submit");
   
      $updateprop = getParameter("updateprop");
      if ($updateprop==1) {
         if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),5)){
            if (0==strcmp($submit,"Remove")) {
               if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),12))$ss->removeValue ($name, $theme);
               else $vars['error'] = "Sorry, you do not have the proper authorization to perform that task.<br>";
            }
            else $ss->setValueTheme ($name, convertString($value), $theme);
         }
         else $vars['error'] = "Sorry, you do not have the proper authorization to perform that task.<br>";
      }
   
   }
   
   //------------------------------------------------------
   // additional user access rights
   //------------------------------------------------------
   else if (0 == strcmp($action,"useraccesspoints")){
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter("userid");
      if (getParameter("remove")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ua->removeUserAccess($vars['userid'],getParameter("sys"),getParameter("id"));
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      if (getParameter("add")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
            $ua->addUserAccess($vars['userid'],getParameter("sys"),getParameter("id"));
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      
      $page="usermod.php";
   }
   else if (0 == strcmp($action,"useraccesspointscloning")){
      ini_set('memory_limit', '128M');
      $vars['userid'] = getParameter("userid");
      if (getParameter("remove")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ua->removeUserAccess($vars['userid'],getParameter("sys"),getParameter("id"));
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      if (getParameter("add")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
            $ua->addUserAccess($vars['userid'],getParameter("sys"),getParameter("id"));
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      
      $page="usermodcloning.php";
   }
   
   //------------------------------------------------------
   // user relationships
   //------------------------------------------------------
   else if (0 == strcmp($action,"userrelation")){
      ini_set('memory_limit', '128M');
      if (getParameter("add")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
            $reluserid = getParameter("reluserid");
            $userid = getParameter("userid");
            $rel_type = getParameter("rel_type");
            if ($rel_type!=NULL && $userid!=NULL && $reluserid!=NULL) {
               $ua->addUserRelationship($userid,$reluserid,$rel_type);
            } else {
               $vars['error'] = "Please choose a user and relationship type to add a related user.<br>";
            }
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      if (getParameter("remove")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ua->removeUserRelationship(getParameter("userrel_id"));
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      
      $page="usermod.php";
   }
   
   else if (0 == strcmp($action,"userrelationcloning")){
      ini_set('memory_limit', '128M');
      $userid = getParameter("userid");
      if (getParameter("add")==1) {
         if($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
            $reluserid = getParameter("reluserid");
            $rel_type = getParameter("rel_type");
            if ($rel_type!=NULL && $userid!=NULL && $reluserid!=NULL) {
               $ua->addUserRelationship($userid,$reluserid,$rel_type);
               $ua->setLastUpdated($userid,$_SESSION['s_user']['emailAddress']);            
            } else {
               $vars['error'] = "Please choose a user and relationship type to add a related user.<br>";
            }
         }
         else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      if (getParameter("update")==1) {
         $submit = strtolower(getParameter("submit"));
         if (0==strcmp($submit,"remove") && $ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ua->removeUserRelationship(getParameter("userrel_id"));
            $ua->setLastUpdated($userid,$_SESSION['s_user']['emailAddress']);            
         } else if (0==strcmp($submit,"update") && $ua->doesUserHaveAccessToLevel(isLoggedOn(),11) && $ua->doesUserHaveAccessToLevel(isLoggedOn(),12)) {
            $ua->updateUserRelationship(getParameter("userrel_id"),getParameter("rel_type"));
            $ua->setLastUpdated($userid,$_SESSION['s_user']['emailAddress']);            
         } else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      }
      
      $page="usermodcloning.php";
   }
   
   //------------------------------------------------------
   // approve user for all website function
   //------------------------------------------------------
   else if (0 == strcmp($action,"approvewebsiteuser")){
      $vars['userid'] = getParameter("userid");
      $privacy = getParameter("privacy");
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),11)) {
         $ua->setWebsiteAccess($vars['userid'],$privacy);
      }
      else $vars['error'] = "Sorry, you do not have the authority to perform that action.<br>";
      
      $page="listusers.php";
   }
   
   //------------------------------------------------------
   // cms interface UI
   //------------------------------------------------------
   else if (0 == strcmp($action,"previewcontent")) {
      $page = "previewcontent.php";
   }
   
   else if (0 == strcmp($action,"showversionfiles")) {
      ini_set('memory_limit', '128M');
      $page="showversionfiles.php";
   
      $cmsid = getParameter("cmsid");
      $curdir = getParameter("curdir");
      if ($GLOBALS['printstuff']) print "curdir in admincontroller 'showversionfiles': ".$curdir."<BR>";
   
      $version = getParameter("version");
      $vars['version'] = $version;
      $filename = getParameter("filename");
      $filetype = getParameter("filetype");
      $contenttype = getParameter("contenttype");
      $track = getParameter("track");
      $cachetime = getParameter("cachetime");
      $privacy = getParameter("privacy");
      $status = getParameter("status");
      $title = getParameter("title");
      $metadescr = getParameter("metadescr");
      $metakw = getParameter("metakw");
      $vsiteid = getParameter("vsiteid");
      $adminnotes = getParameter("adminnotes");
      $search = getParameter("search");
      $owner = $_SESSION['s_user']['emailAddress'];
      $theme = getParameter("theme");
      if ($theme==NULL) $theme = 0;
      //$dir = $GLOBALS['contentDir'];
      $dir = getParameter("curdir");
      $extension = getParameter("extension");
      $contents = getParameter("contents");
      $newdir = getParameter("newdir");
   
      $newfile = getParameter("newfile");
      $statusupdate = getParameter("statusupdate");
      $deleteversion = getParameter("deleteversion");
      $editversion = getParameter("editversion");
      $newversion = getParameter("newversion");
      $updatefile = getParameter("updatefile");
      $newdirectory = getParameter("newdirectory");
      $addaccess = getParameter("addaccess");
      $removeaccess = getParameter("removeaccess");
      $deletedirectory = getParameter("deletedirectory");
   
   
   
   
      //$editcontents = getParameter("editcontents");
   
   
   
   
      if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
         $tempfilename=$_FILES['userfile']['tmp_name'];
         $contents = NULL;
         $extension = $ss->getExtension($_FILES['userfile']['name']);
      }
      else $tempfilename=NULL;
   
   
   
      if ($newfile==1) {
         $filetype = $ss->getFileType($extension);
         $cmsid = $ss->newFileContent($dir,$filename,$extension,$metadescr,$search,$owner,$adminnotes,$metakw,$title,$contenttype,$filetype,$privacy,$contents,$tempfilename,$theme,$cachetime,$vsiteid,$track);
         $vars['cmsid'] = $cmsid;
         if (!$cmsid) {
            $cmsid = NULL;
            $vars['error'] = "Error: Please make sure the shortname you use is unique<br/>";
         }
      } else if ($statusupdate!=NULL) {
         $ss->setVersionStatus($cmsid,$version,$statusupdate);
      } else if ($editversion==1) {
         if (!$ss->updateVersion($cmsid,$version,$owner,$adminnotes,$status,$contents,$tempfilename,$theme,$title,$metadescr,$search,$metakw,$vsiteid)) $vars['error'] = "You cannot update a version owned by another user.  Please create a new version.<br>";
      } else if ($newversion==1) {
         $vars['version'] = $ss->newVersion($cmsid,$owner,$adminnotes,$contents,$tempfilename,$theme,$metadescr,$search,$metakw,$title,$vsiteid);
      } else if ($deleteversion==1) {
         $ss->removeFileVersion($cmsid,$version);
      } else if ($updatefile==1) {
         $updatestr = getParameter("updatestr");
         if (0==strcmp($updatestr,"Delete This File")) {
            if ($ss->removeFileEntirely($cmsid)) {
               $vars['removecmsid']=1;
            }
            else $vars['error'] = "Please remove all versions of this content before trying to remove the content reference.<BR>";
         }
         else {
            $ss->updateFileContent($cmsid,$title,$filetype,$contenttype,$privacy,$cachetime,$track);
            $ss->moveFile($cmsid,getParameter("movedir"));
         }
      } else if ($newdirectory==1) {
         $ss->newDir($dir,$newdir);
      } else if ($addaccess==1) {
         $ua->addUserAccess(getParameter("userid"),"CMS",$cmsid);
         $vars['defaultusersection'] = "CHECKED";
      } else if ($removeaccess==1) {
         $ua->removeUserAccess(getParameter("userid"),"CMS",$cmsid);
         $vars['defaultusersection'] = "CHECKED";
      } else if ($deletedirectory==1) {
         $dirname = $GLOBALS['rootDir'].$GLOBALS['contentDir'].getParameter("deldir");
         $dirname = substr($dirname,0,strlen($dirname)-1);
         $directories = $template->list_dir($dirname,false);
         $files = $template->list_dir($dirname,true);
         if (empty($files) && empty($directories) ) {
            if (rmdir($dirname)) $vars['msg'] = "Directory removed.<BR>";
            else $vars['error'] = "Problem deleting directory: ".$dirname.".<BR>";
            chdir($old); // Restore the old working directory    
         }
         else {
            $vars['error'] = "Directory '".getParameter("deldir")."' must be empty if you wish to delete it.<BR>";
         }
      }
   
      if ($cmsid!=NULL) {
         $cmsfile = $ss->getFileById($cmsid);
         $widgetname = $ss->getFileTypeObject($cmsfile['filetype']);
         $widgetClass = new $widgetname();
         $vars['cmsid'] = $cmsid;
         $vars = $widgetClass->controller($vars);
      }
   
   }
   
   
   
   else if (0 == strcmp($action,"managefiles")) {
      ini_set('memory_limit', '128M');
      $page="managefiles.php";
   
      $cmsid = getParameter("cmsid");
      $curdir = getParameter("curdir");
   
      $version = getParameter("version");
      $vars['version'] = $version;
      $filename = getParameter("filename");
      $filetype = getParameter("filetype");
      $contenttype = getParameter("contenttype");
      $track = getParameter("track");
      $cachetime = getParameter("cachetime");
      $privacy = getParameter("privacy");
      $status = getParameter("status");
      $title = getParameter("title");
      $filetitle = getParameter("filetitle");
      if ($filetitle==NULL) $filetitle=$title;
      $metadescr = getParameter("metadescr");
      $metakw = getParameter("metakw");
      $vsiteid = getParameter("vsiteid");
      $adminnotes = getParameter("adminnotes");
      $search = getParameter("search");
      $owner = $_SESSION['s_user']['emailAddress'];
      $theme = getParameter("theme");
      if ($theme==NULL) $theme = 0;
      $dir = getParameter("curdir");
      $extension = getParameter("extension");
      $contents = getParameter("contents");
      $newdir = getParameter("newdir");
   
      $newfile = getParameter("newfile");
      $statusupdate = getParameter("statusupdate");
      $deleteversion = getParameter("deleteversion");
      $editversion = getParameter("editversion");
      $newversion = getParameter("newversion");
      $updatefile = getParameter("updatefile");
      $newdirectory = getParameter("newdirectory");
      $addaccess = getParameter("addaccess");
      $removeaccess = getParameter("removeaccess");
      $deletedirectory = getParameter("deletedirectory");
   
      $cmsaddhtag = getParameter("cmsaddhtag");
      $cmsdelhtag = getParameter("cmsdelhtag");
      $hashtag = preg_replace("/[^A-Za-z0-9_-]/",'',getParameter("hashtag"));
      //print "\n<!-- htag:".$hashtag." -->\n";
      
      print "\n\n<!-- ***chj*** new file: ".$_FILES['userfile']['name']." -->\n\n";
      print "<!-- files contents: \n";
      print_r($_FILES);
      print "\n-->\n";
      $tempfilename=NULL;
      if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
         $tempfilename=$_FILES['userfile']['tmp_name'];
         $contents = NULL;
         $extension = $ss->getExtension($_FILES['userfile']['name']);
         print "\n\n<!-- ***chj*** new file uploaded: ".$_FILES['userfile']['name']." extension: ".$extension." -->\n\n";
      }
   
      if ($newfile==1) {
         //if ($filetype==NULL) $filetype = $ss->getFileType($extension);
         $filetype = $ss->getFileType($extension);
         $cmsid = $ss->newFileContent($dir,$filename,$extension,$metadescr,$search,$owner,$adminnotes,$metakw,$title,$contenttype,$filetype,$privacy,$contents,$tempfilename,$theme,$cachetime,$vsiteid,$track);
         $vars['cmsid'] = $cmsid;
         if (!$cmsid) {
            $cmsid = NULL;
            $vars['error'] = "Error: Please make sure the shortname you use is unique<br/>";
         }
      } else if ($statusupdate!=NULL) {
         $ss->setVersionStatus($cmsid,$version,$statusupdate);
      } else if ($newversion==1) {
         $vars['version'] = $ss->newVersion($cmsid,$owner,$adminnotes,$contents,$tempfilename,$theme,$metadescr,$search,$metakw,$title,$vsiteid);
      } else if ($deleteversion==1) {
         $ss->removeFileVersion($cmsid,$version);
      } else if ($addaccess==1) {
         $ua->addUserAccess(getParameter("userid"),"CMS",$cmsid);
         $vars['defaultusersection'] = "CHECKED";
      } else if ($removeaccess==1) {
         $ua->removeUserAccess(getParameter("userid"),"CMS",$cmsid);
         $vars['defaultusersection'] = "CHECKED";
      }
      if ($editversion==1) {
         if (!$ss->updateVersion($cmsid,$version,$owner,$adminnotes,$status,$contents,$tempfilename,$theme,$title,$metadescr,$search,$metakw,$vsiteid)) $vars['error'] .= "You cannot update a version owned by another user.  Please create a new version.<br>";
      }
      if ($updatefile==1) {
         $updatestr = getParameter("updatestr");
         if (0==strcmp($updatestr,"Delete This File")) {
            if ($ss->removeFileEntirely($cmsid)) $vars['removecmsid']=1;
            else $vars['error'] .= "Please remove all versions of this content before trying to remove the content reference.<BR>";
         } else {
            $ss->updateFileContent($cmsid,$filetitle,$filetype,$contenttype,$privacy,$cachetime,$track);
            //$ss->moveFile($cmsid,getParameter("movedir"));
         }
      }
   
      if ($cmsaddhtag==1) $ss->addHashTag($cmsid,$hashtag);
      if ($cmsdelhtag==1) $ss->removeHashTag($cmsid,$hashtag);
   
      if ($cmsid!=NULL) {
         $cmsfile = $ss->getFileById($cmsid);
         $widgetname = $ss->getFileTypeObject($cmsfile['filetype']);
         $widgetClass = new $widgetname();
         $vars['cmsid'] = $cmsid;
         $vars = $widgetClass->controller($vars);
      }
   
   }
   
   
   else if (0 == strcmp($action,"manageimages")) {
      $page="manageimages.php";
   }
   
   else if (0 == strcmp($action,"tracking")) {
      //error_reporting(E_ALL);
      $submit = getParameter("submit");
      if (0==strcmp(strtolower($submit),"delete rows")) {
         $tracker = new TrackerArchive();
         $trkids = getParameter("trkids");
         $table = getParameter("table");
         for ($i=0;$i<count($trkids);$i++) {
            $tracker->deleteTracking($trkids[$i],$table);
         }
      }
      $page="tracking.php";
   }
   
   //------------------------------------------------------
   //------------------------------------------------------
   // site id functions
   //------------------------------------------------------
   //------------------------------------------------------
   else if (0 == strcmp($action,"sitemanagement")) {
      $page="sitemanagement.php";
   }
   else if (0 == strcmp($action,"siteadd")) {
      $vars['priority'] = getParameter("priority");
      $vars['name'] = convertString(trim(getParameter("name")));
      $vars['metadescr'] = convertString(trim(getParameter("metadescr")));
      $vars['keywords'] = convertString(trim(getParameter("keywords")));
      $vars['shortname'] = convertString(str_replace(" ","",getParameter("shortname")));
      $vars['alternates'] = convertString(str_replace(" ","",getParameter("alternates")));
      $vars['shortdescr'] = convertString(trim(getParameter("shortdescr")));
      $vars['descr'] = convertString(trim(getParameter("descr")));
      $vars['site_url'] = getParameter("site_url");
      $vars['site_type'] = getParameter("site_type");
      $vars['parent'] = getParameter("parent");
      $vars['siteid'] = getParameter("siteid");
      $vars['image1'] = saveUploadedFile("image1",$GLOBALS['srvyDir'],"siteimg1_");
      $vars['image2'] = saveUploadedFile("image2",$GLOBALS['srvyDir'],"siteimg2_");
      $vars['image3'] = saveUploadedFile("image3",$GLOBALS['srvyDir'],"siteimg3_");
      $vars['image4'] = saveUploadedFile("image4",$GLOBALS['srvyDir'],"siteimg4_");
      $vars['image5'] = saveUploadedFile("image5",$GLOBALS['srvyDir'],"siteimg5_");
      if ($vars['siteid'] != NULL) {
         if ($vars['name'] != NULL && $vars['parent'] != NULL) {
            $ctx->updateSite($vars['siteid'],$vars['priority'],$vars['name'],$vars['shortname'],$vars['shortdescr'],$vars['descr'],$vars['site_url'],$vars['site_type'],$vars['image1'],$vars['image2'],$vars['image3'],$vars['image4'],$vars['image5'],$vars['alternates'],$vars['metadescr'],$vars['keywords']);
            $page="sitemanagement.php";
         } else {
            if (getParameter("makechanges")==1) $vars['error'] = "Please make sure to include the site name.<br>";
            if ($vars['siteid'] == NULL) $page="sitemanagement.php";
            else {
               $vars = $ctx->getSiteInfo($vars['siteid']);
               $vars['title'] = "Modify Micro Site";
               $page="siteadd.php";
            }
         }
      } else {
         if ($vars['name'] != NULL && $vars['parent'] != NULL) {
            $ctx->addSite($vars['priority'],$vars['name'],$vars['shortname'],$vars['shortdescr'],$vars['descr'],$vars['site_url'],$vars['site_type'],$vars['parent'],$vars['image1'],$vars['image2'],$vars['image3'],$vars['image4'],$vars['image5'],$vars['alternates'],$vars['metadescr'],$vars['keywords']);
            $vars['msg'] = "Site Added Successfully.";
            $page="sitemanagement.php";
         } else {
            if (getParameter("makechanges")==1) $vars['error'] = "Please make sure to include the site name.<br>";
            $vars['title'] = "New Micro Site";
            $page="siteadd.php";
         }
      }
   }
   else if (0 == strcmp($action,"siteremove")) {
      if ($ctx->deleteSite(getParameter(siteid))) $vars['msg']="Site removed successfully.<br>";
      else $vars['error'] = "Please remove any child sites before trying to delete a parent.  You cannot delete the Default site.<br>";
      $page="sitemanagement.php";
   }
   
   
   else if (0==strcmp($action,"jsftools")) {
      $page = "jsftools.php";
   }
   
   
   //------------------------------------------------------
   // survey functions
   //------------------------------------------------------
   else if (0 == strcmp($action,"survey")) {
      
      $survey_id = getParameter("survey_id");
      //print "survey id: ".$survey_id;
   
      $newFeed = getParameter("newFeed");
      $title = getParameter("title");
      $description = getParameter("description");
      $webMaster = getParameter("webMaster");
      $image_url = getParameter("image_url");
      $image_link = getParameter("image_link");
      $image_title = getParameter("image_title");
      $image_width = getParameter("image_width");
      $image_height = getParameter("image_height");
      $copyright = getParameter("copyright");
      $managingEditor = getParameter("managingEditor");
      $link = getParameter("link");
      $max = getParameter("max");
      $viewtype = getParameter("viewtype");
   
      $sname = getParameter("sname");
      $sinfo = getParameter("sinfo");
      $privatesrvy = getParameter("privatesrvy");
      $adminemail = convertString(strtolower(trim(getParameter("adminemail"))));
      $saveresults = getParameter("saveresults");
      $emailresults = getParameter("emailresults");
      $glossaryid = getParameter("glossaryid");
      $xml = getParameter("xml");
   
      $page = "adminsurvey.php";
      if ($viewtype==6) $page = "newrssfeed.php";
   
      if ($survey_id == NULL) {
         if ($sname != NULL) $survey_id = $surveyObj->newSurvey($sname, $sinfo, $privatesrvy, $adminemail, $saveresults, $emailresults, $glossaryid);
         else if ($xml != NULL) $survey_id = $surveyObj->newSurveyFromXML($xml);
         else if ($newFeed==1) {
            $survey_id = $surveyObj->newSurvey($title, $description, 6, $webMaster, 1, 0);
            $surveyObj->updateRss($survey_id,$link,$image_url,$image_title,$image_link,$image_width,$image_height,$copyright,$managingEditor,$max);
            $section = $surveyObj->addSection($survey_id, "RSS Item Fields", 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "enabled", "Yes,No", "RADIO", 10, 2);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "sequence", "", "INT", 20, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "category", "", "TEXT", 25, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_title", "", "TEXT", 30, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_description", "", "TEXTAREA", 40, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_pubDate", "", "DATETIME", 55, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "", "", "SPACER", 60, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "Optional Items", "", "INFO", 70, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_comment", "", "TEXT", 80, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "Image", "", "IMAGE", 85, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_author", "", "TEXT", 90, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_enclosure_type", "gif/jpeg,audio/mpeg,", "RADIO", 100, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_enclosure_url", "", "TEXT", 110, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "item_enclosure_length", "", "TEXT", 120, 0);
            $qid = $surveyObj->getNextQID ($survey_id, $section);
            $surveyObj->addQuestion ($survey_id, $section, $qid, "story", "", "HTML", 130, 0);
      
            $page = "listsurveys.php";
         }
      }
      else {
   
         $survey = $surveyObj->getSurvey($survey_id);
         if ($survey['privatesrvy']==6) $page = "newrssfeed.php";
   
         $subaction = getParameter("subaction");
         if (0==strcmp($subaction,"makecopy")) {
            $surveyObj->copySurvey(getParameter("survey_id"));
            $page = "listsurveys.php";
         }
   
   
         if (0==strcmp($subaction,"addaccess")) {
            $ua->addUserAccess(getParameter("userid"),"SURVEY",$survey_id);
         }
         if (0==strcmp($subaction,"removeaccess")) {
            $ua->removeUserAccess(getParameter("userid"),"SURVEY",$survey_id);
         }
   
         if (0==strcmp($subaction,"remove")) {
            $surveyObj->removeSurvey(getParameter("survey_id"));
            $page = "listsurveys.php";
         }
         else {
            $section = getParameter("section");
            $sequence = getParameter("sequence");
            $label = getParameter("label");
            $question_type = getParameter("question_type");
            $question = getParameter("question");
            $question_id = getParameter("question_id");
            $newSection = getParameter("newSection");
            $newQuestion = getParameter("newQuestion");
            $deleteQuestion = getParameter("deleteQuestion");
            $deleteSection = getParameter("deleteSection");
            $dyna = getParameter("dyna");
            $privacy = getParameter("privacy");
            $updateFeed = getParameter("updateFeed");
            
            if ($updateFeed == 1) {
               $surveyObj->updateSurvey($survey_id, $title, $description, 6, $webMaster, 1, 0);
               $surveyObj->updateRss($survey_id,$link,$image_url,$image_title,$image_link,$image_width,$image_height,$copyright,$managingEditor,$max);
               $page = "listsurveys.php";
               //$page = "newrssfeed.php";
            } else if ($sname != NULL) {
               $surveyObj->updateSurvey($survey_id, $sname, $sinfo, $privatesrvy, $adminemail, $saveresults, $emailresults, $glossaryid);
            } else if ($deleteSection == 1) {
            } else if ($deleteQuestion == 1) {
               $surveyObj->deleteQuestion($survey_id, $question_id);
            } else if ($newSection == 1) {
               $surveyObj->addSection($survey_id, convertString($label), 0, $dyna, convertString($question));
            } else if ($newQuestion == 1) {
               $qid = $surveyObj->getNextQID ($survey_id, $section);
               $surveyObj->addQuestion ($survey_id, $section, $qid, convertApostrophes($label), convertApostrophes($question), $question_type, $sequence, $privacy);
            } else if ($question_id != NULL) {
               $surveyObj->updateQuestion($survey_id, $section, $question_id, convertApostrophes($label), convertApostrophes($question), $question_type, $sequence, $privacy);
            } else if ($section != NULL) {
               $deleteSection = getParameter("Delete");
               if (0==strcmp($deleteSection,"Delete")) $surveyObj->deleteSection($survey_id, $section);
               else $surveyObj->updateSection($survey_id, $section, $sequence, $label, $dyna, $question);
            }
   
         }
      }
   
   }
   
   //------------------------------------------------------
   // show survey xml
   //------------------------------------------------------
   else if (0==strcmp($action,"surveyxml")) {
      //error_reporting(E_ALL);
      $vars['survey_id'] = getParameter("survey_id");
      $vars['xml'] = $surveyObj->getOutputXML($vars['survey_id']);
      $page = "surveyxml.php";
   }
   
   //------------------------------------------------------
   // List Surveys
   //------------------------------------------------------
   else if (0 == strcmp($action,"listsurveys")) {
      $page = "listsurveys.php";
   }
   //------------------------------------------------------
   // List Emails
   //------------------------------------------------------
   else if (0 == strcmp($action,"listemails")) {
      $complete = getParameter("complete");
      $fax = getParameter("fax");
      $updateAnswer = getParameter("updateAnswer");
      $updateForm = getParameter("updateForm");
      $submit = getParameter("submit");
      if ($complete != null) $surveyObj->setReplyStatus(getParameter("srvy_person_id"),$complete);
      if ($fax != null) $surveyObj->setFax(getParameter("srvy_person_id"),$fax);
      if ($updateAnswer == 1) $surveyObj->setAnswer(getParameter("survey_id"),getParameter("srvy_person_id"),getParameter("question_id"),getParameter("answer"));
      if ($updateForm == 1 && 0==strcmp($submit,"Delete Selected Rows")) {
         $del_srvy_person_id = getParameter("del_srvy_person_id");
         if (is_array($del_srvy_person_id)) {
            for ($i=0; $i<count($del_srvy_person_id); $i++) $surveyObj->removeEmail($del_srvy_person_id[$i]);
         }
      }
      if ($updateForm == 1 && 0==strcmp($submit,"Save Values In Table")) $surveyObj->submitMultipleSurveys();
   
      $survey_id = getParameter("survey_id");
      $survey = $surveyObj->getSurvey($survey_id);
   
      $page = "listemails.php";
      if ($survey['privatesrvy']>99) {
         $customCode = new AdminUI();
         $page = $customCode->getCustomSurveyPage($survey['privatesrvy']);
      }
   }
   else if (0 == strcmp($action,"srvysearchresults")) {
      $vars['survey_id'] = getParameter("survey_id");
      $vars['newentry'] = getParameter("newentry");
      $vars['srvy_person_id'] = getParameter("srvy_person_id");
      if ($vars['newentry'] == 1) $page = "SrvySearchDetail.php";
      else if ($vars['srvy_person_id'] == null || 0==strcmp($vars['srvy_person_id'],"")) $page = "SrvySearchResults.php";
      else $page = "SrvySearchDetail.php";
   }
   //------------------------------------------------------
   // New survey entry and submitting a survey
   //------------------------------------------------------
   else if (0 == strcmp($action,"viewsurvey")) {
      $vars['survey_id'] = getParameter("survey_id");
      $vars['srvy_person_id'] = getParameter("srvy_person_id");
      $page = "adminviewsurvey.php";
   }
   else if (0 == strcmp($action,"ss")) {
      $vars['srvy_person_id'] = getParameter("srvy_person_id");
      $vars['survey_id'] = getParameter("survey_id");
      $vars['email'] = getParameter("contact_email");
      $vars['name'] = getParameter("contact_name");
      $vars['srvy_person_id'] = $surveyObj->submitSurvey($vars['srvy_person_id'], $vars['survey_id'], $vars['email'], $vars['name'], false);
      $page = getParameter("phpinclude");
      if ($page==null) $page = "listemails.php";
   }
   
   //------------------------------------------------------
   // Re-sequence the entries in the table.  use the existing sequence
   // only start with 10 and update the sequence field by 10's
   //------------------------------------------------------
   else if (0 == strcmp($action,"resequence")) {
      $survey_id = getParameter("survey_id");
      $vars['survey_id'] = $survey_id;
      $surveyObj->resequence($survey_id);
      $page = "listemails.php";
   }
   
   //------------------------------------------------------
   // Update Survey Person
   //------------------------------------------------------
   else if (0 == strcmp($action,"updatesurveyperson")) {
         $survey_id = getParameter("survey_id");
         $contact_email = getParameter("contact_email");
         $srvy_person_id = getParameter("srvy_person_id");
         
         if (getParameter("newcompany")==1) {
            $srvy_person_id = $surveyObj->addEmail ($survey_id, $contact_email);
            $surveyObj->updatePerson($srvy_person_id);
   
            if (0 == strcmp(getParameter("AddCompany"),"Add Company Only")) {
               //do nothing if we're ONLY adding a company
               $surveyObj->setReplyStatus($srvy_person_id,"X");
            }
            else {
               $surveyObj->sendEmail($srvy_person_id);
               $surveyObj->setReplyStatus($srvy_person_id,"N");
            }
         }
         else if (getParameter("resend") != null && strcmp(getParameter("resend"),"1")==0) {
            $surveyObj->sendEmail($srvy_person_id);
            if (getParameter("setstatus")!=null) $surveyObj->setReplyStatus($srvy_person_id,getParameter("setstatus"));
         }
         else if (getParameter("delete") != null && strcmp(getParameter("delete"),"1")==0) {
            $surveyObj->removeEmail($srvy_person_id);
         }
         else if (getParameter("updatecomments") != null && strcmp(getParameter("updatecomments"),"1")==0) {
            $surveyObj->updatePerson($srvy_person_id);
         }
         
         if (getParameter("emailbody") != null || getParameter("emailsubject") != null) {
            $surveyObj->setEmailBody($survey_id,getParameter('emailbody'),getParameter("emailsubject"),getParameter("adminemail"));
         }
   
         if (is_array($srvy_person_id)) {
            for ($i=0; $i<count($srvy_person_id); $i++) {
               $submit = getParameter("submit");
               if (0==strcmp($submit,"Delete selected rows")) {
                  $surveyObj->removeEmail($srvy_person_id[$i]);
               }
               else if (0==strcmp($submit,"Send email to selected rows")) {
                  $surveyObj->sendEmail($srvy_person_id[$i]);
               }
            }
         }
   
         $survey = $surveyObj->getSurvey($survey_id);   
         $page = "listemails.php";
         if ($survey['privatesrvy']>99) {
            $customCode = new AdminUI();
            $page = $customCode->getCustomSurveyPage($survey['privatesrvy']);
         }
   }
   //------------------------------------------------------
   // Upload a csv of companies
   //------------------------------------------------------
   else if (0==strcmp($action,"uploadSurveyCSV")) {
        $survey_id = getParameter("survey_id");
   
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
           $count = 0;
           $filename = $GLOBALS['rootDir'].$GLOBALS['csvuploadDir']."srvy_".$survey_id."_".$count."_".$_FILES['userfile']['name']; 
           while (file_exists($filename)) {
              $count ++;
              $filename = $GLOBALS['rootDir'].$GLOBALS['csvuploadDir']."srvy_".$survey_id."_".$count."_".$_FILES['userfile']['name']; 
           }
           if (copy($_FILES['userfile']['tmp_name'],$filename))
           {
                $contents = $template->getFileWithoutSub($filename,FALSE);
                $surveyObj->loadContents($survey_id,$contents);
                $vars['msg'] .= "CSV ".$_FILES['userfile']['name']." uploaded successfully.<BR>";
           }
           else $vars['error'] .= "Upload failed.  Please make sure the file is not too large and exists on your system.";
        }
        else $vars['error'] .= "Upload failed.";
   
        $page = "listemails.php";
   }
   
   else if (0==strcmp($action,"uploadwdcsv")) {
      ini_set('memory_limit', '256M');
      $filename = saveUploadedFile("wdcsv",$GLOBALS['rootDir'].$GLOBALS['csvuploadDir'],"webdata_");
      $wd_id = getParameter("wd_id");
      
      $small = getParameter("small");
      if($small==1) {
         // OLD WAY - load all rows now
         $contents = $template->getFileWithoutSub($GLOBALS['rootDir'].$GLOBALS['csvuploadDir'].$filename,FALSE);
         $wdObj->loadContents($contents,$wd_id);
         $vars['msg'] = "Your csv file was loaded.";
      } else {
         $lwd = new LoadWebData();
         $lwd->startjob($wd_id,$GLOBALS['rootDir'].$GLOBALS['csvuploadDir'].$filename);
         $vars['msg'] = "Your csv file was scheduled to be loaded.";
      }
      
      $webdata = $wdObj->getWebData($wd_id);
      $page = "wd_listrows.php";
      //if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrows.php";
      if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrowsopti.php";
      if ($webdata['privatesrvy']>99) {
         $customCode = new AdminUI();
         $temppage = $customCode->getCustomSurveyPage($webdata['privatesrvy']);
         if ($temppage!=NULL) $page = $temppage;
      }
   }
   
   
   //------------------------------------------------------
   // Download a csv of recipients
   //------------------------------------------------------
   else if (0==strcmp($action,"dlcsv")) {
      ini_set('memory_limit', '128M');
      $survey_id = getParameter("survey_id");
      $qids = getParameter("qids");
      $entire_file = $surveyObj->getOutputCSVOptions($survey_id,$qids);
      $output_file_name = 'surveyresponses.csv'; 
      $template->saveFile($output_file_name,$entire_file);
      $page = "download.php";
   }
   
   //------------------------------------------------------
   // Download a csv of recipients
   //------------------------------------------------------
   else if (0==strcmp($action,"dlwdcsv")) {
      //error_reporting(E_ALL);
      $wd_id = getParameter("wd_id");
      $qids = getParameter("qids");
      $subj = getParameter("subject");
      $dlwd = new DownloadWebData();
      $dlwd->startjob($wd_id,$qids,$subj);
      $page = "wd_listrows.php";
      $vars['msg'] = "Your download is scheduled - please check CSVs for progress.";
      
      
      
      //ini_set('memory_limit', '128M');
      //$wd_id = getParameter("wd_id");
      //$qids = getParameter("qids");
      //$entire_file = $wdObj->getOutputCSVOptions($wd_id,$qids);
      //$output_file_name = 'surveyresponses.csv'; 
      //$template->saveFile($output_file_name,$entire_file);
      //$page = "download.php";
   }
   
   //------------------------------------------------------
   // Debug a csv of recipients
   //------------------------------------------------------
   else if (0==strcmp($action,"dlcsvdebug")) {
      $survey_id = getParameter("survey_id");
      $entire_file = $surveyObj->getOutputCSV($survey_id);
      $page = "listemails.php";
   }
   //**************************************************************************************************************************************
   //**************************************************************************************************************************************
   
   //------------------------------------------------------
   // WebSite Data functions
   //------------------------------------------------------
   else if (0 == strcmp($action,"wd_fieldposition")) {
      //error_reporting(E_ALL);
      $page = "wd_fieldposition.php";
   }
   
   else if (0 == strcmp($action,"webdata")) {
      //error_reporting(E_ALL);
      $wd_id = getParameter("wd_id");
      $xml = getParameter("xml");
      $migratexml = getParameter("migratexml");
      $sname = convertString(getParameter("sname"));
      $shortname = getParameter("shortname");
      $password = getParameter("password");
      $sinfo = convertString(getParameter("sinfo"));
      $privatesrvy = getParameter("privatesrvy");
      $adminemail = convertString(strtolower(trim(getParameter("adminemail"))));
      $filename = trim(getParameter("filename"));
      $userrel = trim(getParameter("userrel"));
      $usertype = trim(getParameter("usertype"));
      $saveresults = getParameter("saveresults");
      $emailresults = getParameter("emailresults");
      $glossaryid = getParameter("glossaryid");
      $captcha = getParameter("captcha");
      $esign = getParameter("esign");
   
      $title = convertString(getParameter("title"));
      $description = convertString(getParameter("description"));
      $webMaster = getParameter("webMaster");
      $viewtype = getParameter("viewtype");
   
   
      $page = "wd_admin.php";
      $subaction = getParameter("subaction");
      
      print "\n<!-- subaction: ".$subaction." -->\n";
      
      if ($wd_id == NULL) {
         print "\n<!-- wd_id is null -->\n";
         if ($sname != NULL) {
            $wd_id = $wdObj->newWebData($sname, $sinfo, $privatesrvy, $adminemail, $filename, $saveresults, $emailresults, $glossaryid, NULL, NULL, NULL, NULL, NULL, NULL, $shortname, $password, $captcha, $userrel, $esign, $usertype);
            //$page = "wd_listwebdata.php";
            $page = "wd_listwebdata_htags.php";
         } else if ($xml != NULL) {
            $wd_id = $wdObj->newWebDataFromXML($xml);
            $page = "wd_listwebdata_htags.php";
         } else if ($migratexml != NULL) {
            $wd_id = $wdObj->newWebDataFromXMLMigrate($migratexml);
            $page = "wd_listwebdata_htags.php";
         } else if (0==strcmp($subaction,"newglossary")) {
            $wd_id = $wdObj->newWebDataFromXML($wdObj->getTemplateXML_Glossary());
            $vars['wd_id'] = $wd_id;
         } else if (0==strcmp($subaction,"newsearchindex")) {
            $wd_id = $wdObj->newWebDataFromXML($wdObj->getTemplateXML_SearchIndex());
            $vars['wd_id'] = $wd_id;
         }
      } else {
         print "\n<!-- wd_id is not null: ".$wd_id." -->\n";
         $webdata = $wdObj->getWebData($wd_id);
         $wd_id = $webdata['wd_id'];
         if (0==strcmp($subaction,"makecopy")) {
            //error_reporting(E_ALL);
            $wdObj->copyWebData($wd_id);
            $page = "websitedata.php";
            $page = "wd_listwebdata_htags.php";
         } else if (0==strcmp($subaction,"addaccess")) {
            $ua->addUserAccess(getParameter("userid"),"WDATA",$wd_id);
         } else if (0==strcmp($subaction,"removeaccess")) {
            $ua->removeUserAccess(getParameter("userid"),"WDATA",$wd_id);
         } else if (0==strcmp($subaction,"remove")) {
            $wdObj->removeWebData($wd_id);
            $page = "wd_listwebdata_htags.php";
         } else {
            print "\n<!-- no subaction detected -->\n";
            $field_id = getParameter("field_id");
            $parent_s = getParameter("parent_s");
            $sec_type = getParameter("sec_type");
            $sequence = getParameter("sequence");
            $dyna = getParameter("dyna");
            $question = convertString(getParameter("question"));
            $param1 = getParameter("param1");
            $param2 = getParameter("param2");
            $param3 = getParameter("param3");
            $param4 = getParameter("param4");
            $param5 = convertString(getParameter("param5"));
            $param6 = convertString(getParameter("param6"));
            $newField = getParameter("newField");
            $newFieldRel = getParameter("newFieldRel");
            $newFieldRelSect = getParameter("newFieldRelSect");
            $deleteFieldRel = getParameter("deleteFieldRel");
            $section = getParameter("section");
            $header = getParameter("header");
            $required = getParameter("required");
            $srchfld = getParameter("srchfld");
            $filterfld = getParameter("filterfld");
            $notes = convertString(getParameter("notes"));
            $field_type = getParameter("field_type");
            $deleteField = getParameter("deleteField");
            $newSection = getParameter("newSection");
            $copySection = getParameter("copySection");
            $copySectionFromXML = getParameter("copySectionFromXML");
            $label = convertString(getParameter("label"));
            $privacy = getParameter("privacy");
            $defaultval = convertString(getParameter("defaultval"));
            $resequence = getParameter("resequence");
            $refinerels = getParameter("refinerels");
            $rowdisplay = getParameter("rowdisplay");
            
            if ($sname != NULL) {
               print "\n<!-- updating webdata, sname detected -->\n";
               $wdObj->updateWebData($wd_id, $sname, $sinfo, $privatesrvy, $adminemail, $filename, $saveresults, $emailresults, $glossaryid, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $shortname, $password, $captcha, $userrel, $esign, $usertype, $rowdisplay);
            } else if ($newSection == 1) {
               print "\n<!-- updating webdata, new section -->\n";
               $wdObj->addSection($wd_id, $parent_s, $sec_type, $label, $sequence, $dyna, $question, $param1, $param2, $param3, $param4, $param5, $param6);
            } else if ($copySection == 1) {
               print "\n<!-- updating webdata, copy section -->\n";
               $wdObj->copySection($wd_id, $section);
            } else if ($copySectionFromXML == 1) {
               print "\n<!-- new webdata from xml -->\n";
               $wdObj->newWebDataFromXML($xml,$wd_id);
            } else if ($deleteFieldRel == 1) {
               $rel_id = getParameter("rel_id");
               $wdObj->removeFieldRel($rel_id);
            } else if ($newFieldRel == 1) {
               print "\n<!-- updating webdata, new field rel -->\n";
               $rel_type = getParameter("rel_type");
               $fid1 = getParameter("fid1");
               $fid2 = getParameter("fid2");
               $f1value = getParameter("f1value");
               if(!is_array($fid2)){
                  //print "\n<!-- not array -->\n";
                  $fidarr = separateStringBySeparators($fid2);
                  $fid2 = $fidarr;
                  //print "\n<!-- new array:\n";
                  //print_r($fid2);
                  //print "\n-->\n";
               }
               for($i=0;$i<count($fid2);$i++) {
                  if(trim($fid2[$i])!=NULL) {
                     //print "\n<!-- new field rel: ".$wd_id.",".$rel_type.",".$fid1.",".$fid2[$i].",".$f1value." -->\n";
                     $wdObj->newFieldRel($wd_id,$rel_type,$fid1,trim($fid2[$i]),$f1value);
                  }
               }
            } else if ($newFieldRelSect == 1) {
               $fid1 = getParameter("fid1");
               $parent_s = getParameter("parent_s");
               $f1value = getParameter("f1value");
               $wdObj->newFieldRelSect($wd_id,$fid1,$parent_s,$f1value);
            } else if ($resequence == 1) {
               $wdObj->resequenceStructure($wd_id);
            } else if ($refinerels == 1) {
               $vars['commandoutput'] = $wdObj->checkSectionRels($wd_id);
            } else if ($newField == 1) {
               $fid = $wdObj->getNextFieldID ($wd_id);
               $wdObj->addField($wd_id, $parent_s, $fid, convertApostrophes($label), convertApostrophes($question), $field_type, $sequence, $privacy, $header, $defaultval, $required, $srchfld, FALSE, $notes, $filterfld);
            } else if ($field_id != NULL) {
               $deleteThisField = getParameter("Delete");
               if (0==strcmp($deleteThisField,"Delete")) $wdObj->deleteField($wd_id, $field_id);
               else $wdObj->updateField($wd_id, $parent_s, $field_id, $label, $question, $field_type, $sequence, $privacy, $header,$defaultval,$required, $srchfld, $notes, $filterfld);
            } else if ($section != NULL) {
               $deleteSection = getParameter("Delete");
               if (0==strcmp($deleteSection,"Delete")) $wdObj->deleteSection($wd_id, $section);
               else $wdObj->updateSection($wd_id, $section, $parent_s, $sec_type, $label, $sequence, $dyna, $question, $param1, $param2, $param3, $param4, $param5, $param6);
            }
         }
      }
   }
   
   else if (0==strcmp($action,"addusertowd")) {
      $userid = getParameter("userid");
      $wd_id = getParameter("wd_id");
      
      $count_new = 0;
      $count_existing = 0;
      $count_error = 0;
      
      if (!is_array($userid)) {
         $userid = array();
         $userid[0] = getParameter("userid");
      }
      for ($i=0; $i<count($userid); $i++) {
         $tempuser = $ua->getUser($userid[$i]);         
         if (0==strcmp($tempuser['usertype'],"user")) {
            $reluser = $ua->getUsersRelated($tempuser['userid'],"from","SRVYADMIN");
            if ($reluser!=NULL && count($reluser)>0 && $reluser[0]['userid']!=NULL) {
               $tempuser = $ua->getUser($reluser[0]['userid']);
            }
         } else {
            $reluser = $ua->getUsersRelated($tempuser['userid'],"to","SRVYADMIN");
            if ($reluser==NULL) $tempuser=NULL;
         }
         if ($tempuser!=NULL) {
            $exists = $wdObj->getDataByUserid($wd_id, $tempuser['userid']);
            if ($exists==NULL || $exists[0]['userid']==NULL) {
                print "\n<!-- adding row for user: ".$tempuser['userid']." (".$tempuser['company'].") -->\n";
               $wdObj->addRow($wd_id, $tempuser['userid']);
               $count_new++;
            } else {
                print "\n<!-- user already existed: ".$tempuser['userid']." (".$tempuser['company'].") -->\n";
               $count_existing++;
            }
         } else {
            $count_error++;
         }
      }
      $vars['count_new'] = $count_new;
      $vars['count_existing'] = $count_existing;
      $vars['count_error'] = $count_error;
      $vars['msg'] = "You have added ".$count_new." organizations to the survey.  ".$count_existing." organizations were already included in this survey.  ".$count_error." organizations could not be added (make sure they have an administrator).";
      $page = "listusers.php";
   }
   
   else if (0==strcmp($action,"addusertowdcloning")) {
      $userid = getParameter("userid");
      $wd_id = getParameter("wd_id");
      
      $count_new = 0;
      $count_existing = 0;
      $count_error = 0;
      
      if (!is_array($userid)) {
         $userid = array();
         $userid[0] = getParameter("userid");
         print "\n<!-- ***chj*** userid parameter was not an array -->\n";
      }
      print "<!-- ***chj*** userids: ".count($userid)." -->\n";   
      for ($i=0; $i<count($userid); $i++) {
         print "<!-- ***chj*** userid: ".$userid[$i]." -->\n";   
         $tempuser = $ua->getUser($userid[$i]);         
         if (0==strcmp($tempuser['usertype'],"user")) {
            $reluser = $ua->getUsersRelated($tempuser['userid'],"from","SRVYADMIN");
            if ($reluser!=NULL && count($reluser)>0 && $reluser[0]['userid']!=NULL) {
               $tempuser = $ua->getUser($reluser[0]['userid']);
            }
         } else {
            $reluser = $ua->getUsersRelated($tempuser['userid'],"to","SRVYADMIN");
            if ($reluser==NULL) $tempuser=NULL;
         }
         if ($tempuser!=NULL) {
            $exists = $wdObj->getDataByUserid($wd_id, $tempuser['userid']);
            if ($exists==NULL || $exists[0]['userid']==NULL) {
                print "\n<!-- adding row for user: ".$tempuser['userid']." (".$tempuser['company'].") -->\n";
               $wdObj->addRow($wd_id, $tempuser['userid']);
               $count_new++;
            } else {
                print "\n<!-- user already existed: ".$tempuser['userid']." (".$tempuser['company'].") -->\n";
               $count_existing++;
            }
         } else {
            print "\n<!-- user could not be added: ".$userid[$i]." -->\n";
            $count_error++;
         }
      }
      $vars['count_new'] = $count_new;
      $vars['count_existing'] = $count_existing;
      $vars['count_error'] = $count_error;
      $vars['msg'] = "You have added ".$count_new." organizations to the survey.  ".$count_existing." organizations were already included in this survey.  ".$count_error." organizations could not be added (make sure they have an administrator).";
      $page = "listuserscloning.php";
   }
   
   //------------------------------------------------------
   // show survey xml
   //------------------------------------------------------
   else if (0==strcmp($action,"webdataxml")) {
      $vars['wd_id'] = getParameter("wd_id");
      $structureonly = getParameter("structureonly");
      $showData = ($structureonly!=1);
      $vars['xml'] = $wdObj->getOutputXML($vars['wd_id'],$showData);
      $page = "webdataxml.php";
   }
   
   //------------------------------------------------------
   // List WebData Tables
   //------------------------------------------------------
   else if (0 == strcmp($action,"wd_listwebdata")) {
      //$page = "wd_listwebdata.php";
      $page = "wd_listwebdata_htags.php";
   }
   //------------------------------------------------------
   // List Emails
   //------------------------------------------------------
   else if (0 == strcmp($action,"wd_listrows")) {
      //error_reporting(E_ALL);
      $complete = getParameter("complete");
      $wd_id = getParameter("wd_id");
      $updateAnswer = getParameter("updateAnswer");
      $updateForm = getParameter("updateForm");
      $submit = getParameter("submit");
      if ($complete != null) $wdObj->setReplyStatus($wd_id,getParameter("wd_row_id"),$complete);
      if ($fax != null) $wdObj->setFax(getParameter("srvy_person_id"),$fax);
      if ($updateAnswer == 1) $wdObj->setAnswer(getParameter("wd_id"),getParameter("wd_row_id"),getParameter("field_id"),getParameter("answer"));
      if ($updateForm == 1 && 0==strcmp($submit,"Delete Selected Rows")) {
         $del_wd_row_id = getParameter("del_wd_row_id");
         if (is_array($del_wd_row_id)) {
            for ($i=0; $i<count($del_wd_row_id); $i++) $wdObj->removeRow($wd_id,$del_wd_row_id[$i]);
         }
      }
      if ($updateForm == 1 && 0==strcmp($submit,"Save Values In Table")) $wdObj->submitMultipleSurveys();
      
      if(0==strcmp($submit,"Download CSV")) {
      //if(0==strcmp($submit,"dlwdcsv")) {
         $qids = getParameter("qids");
         $subj = getParameter("subject");
         $resched = getParameter("resched");
         $dlwd = new DownloadWebData();
         $dlwd->startjob($wd_id,$qids,$subj,$resched);
         $vars['msg'] = "Your download is scheduled - please check CSVs for progress.";
      }
      
      
      
   
      $webdata = $wdObj->getWebData($wd_id);
      $page = "wd_listrows.php";
      //if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrows.php";
      if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrowsopti.php";
      if ($webdata['privatesrvy']>99) {
         $customCode = new AdminUI();
         $temppage = $customCode->getCustomSurveyPage($webdata['privatesrvy']);
         if ($temppage!=NULL) $page = $temppage;
      }
   
   //print "<br>private survey: ".$webdata['privatesrvy'];
   //print "<br>page: ".$page;
   
   }
   
   // alertnative to "wd_listrows
   else if (0 == strcmp($action,"wd_showtable")) {
      $page = "wd_showtable.php";
      
   }
   
   
   //------------------------------------------------------
   // Scheduled jobs...
   //------------------------------------------------------
   else if (0 == strcmp($action,"scheduledcsvs")) {
      //error_reporting(E_ALL);
      $scheduler = new Scheduler();
      $page="viewscheduledcsvs.php";
   
      $submit = getParameter("submit");
      $emailids = getParameter("a_semailid");
      if (is_array($emailids)) {
         $copyerror = FALSE;
   
         for ($i=0; $i<count($emailids); $i++) {
            if (0==strcmp($submit,"Delete")) {
               //$semailobj = $scheduler->getScheduledEmails($emailids[$i]);
               $scheduler->removeSchedEmail($emailids[$i]);
            } else if (0==strcmp($submit,"Pause")) {
               $semailobj = $scheduler->getScheduledEmails($emailids[$i],"NEW");
               $scheduler->updateEmailJob($emailids[$i],"PAUSED");
            } else if (0==strcmp($submit,"Unpause")) {
               $semailobj = $scheduler->getScheduledEmails($emailids[$i],"PAUSED");
               $scheduler->updateEmailJob($emailids[$i],"NEW");
            } else if (0==strcmp($submit,"Prioritize")) {
               $scheduler->updateEmailJob($emailids[$i],NULL,1);            
            } else if (0==strcmp($submit,"Redo")) {
               if(!$scheduler->copyJob($emailids[$i])) $copyerror=TRUE;           
            }
         }
         
         if($copyerror) $vars['error'] = "There was a problem with copying at least 1 of your jobs.";
         else $vars['msg'] = "Completed Successfully!";
      }
   
      if (getParameter("scheduleSurveyCSV")==1) {
         $orgParams = array();
         //$orgParams[] = "q35";
         $orgParams[] = "q47";
         //$orgParams[] = "q8";
         //$orgParams[] = "q7";
         $orgParams[] = "q42";
         $orgParams[] = "q9";
         $orgParams[] = "q62";
         $orgParams[] = "q95";
         $orgParams[] = "q44";
         $orgParams[] = "q46";
         $orgParams[] = "q60";
         $orgParams[] = "q78";
         $orgParams[] = "q39";
         $orgParams[] = "q40";
         $orgParams[] = "q23";
         $orgParams[] = "q69";
         $orgParams[] = "q70";
         $results = $wdObj->getRowsSurveyOrgAdmin(getParameter("wd_id"), NULL, NULL, getParameter("filterStr"), FALSE, FALSE, $orgParams,FALSE);
   
         $dateStr = NULL;
         $y = getParameter("start_y");
         $m = getParameter("start_m");
         $d = getParameter("start_d");
         if ($y!=NULL && $m!=NULL && $d!=NULL) {
            $dateStr .= $y."-".$m."-".$d;
   
            $hourStr = getParameter("start_hour");
            $minStr = getParameter("start_min");
            $todStr = getParameter("start_tod");
            if ($hourStr != null && $minStr != null && $todStr != null) {
               if (0==strcmp($todStr,"PM") && $hourStr<12) $hourStr += 12;
               else if (0==strcmp($todStr,"AM") && $hourStr==12) $hourStr = "00";
               $dateStr .= " ".$hourStr.":".$minStr;
            }
            //print "\n<!-- date string: ".$dateStr." -->\n";
         }
   
         $sched = new Scheduler();
         $sched->addSchedCSV($results['query'],getParameter("wd_id"),getParameter("subject"),$dateStr,10,1000,NULL,NULL,'org');
         //print "\n<!--\n";
         //print $results['query']."\n";
         //print getParameter("wd_id")."\n";
         //print getParameter("subject")."\n";
         //print "-->\n";
         $vars['msg'] = "Your CSV file was scheduled.  Please check back later for a downloadable file.";
      }
   
   
   }
   
   
   else if (0 == strcmp($action,"submitwdcsv")) {
      //error_reporting(E_ALL);
      $page="viewscheduledcsvs.php";
   
      $wd_id = getParameter("wd_id");
      $results = $wdObj->getRows($wd_id,getParameter("orderby"),10,getParameter("filterStr"), FALSE,NULL,TRUE);
      $wd = $wdObj->getWebData($wd_id);
      $subject = getParameter("subject");
      if ($subject==NULL) $subject = date("m/d/Y H:i - \"").$wd['name']."\" CSV download";
      $sched = new Scheduler();
      $sched->addSchedCSV($results['query'],$wd_id,$subject,NULL,10,1000,NULL,NULL,NULL);
      $vars['msg'] = "Your CSV file was scheduled.  Please check back later for a downloadable file.";
   }
   
   
   
   else if (0 == strcmp($action,"wd_search")) {
      $vars['wd_id'] = getParameter("wd_id");
      $page = "wd_search.php";
   }
   
   else if (0 == strcmp($action,"wd_search2")) {
      $vars['wd_id'] = getParameter("wd_id");
      $page = "wd_search2.php";
   }
   
   else if (0 == strcmp($action,"wd_schedulecsv")) {
      $vars['wd_id'] = getParameter("wd_id");
      $page = "wd_schedulecsv.php";
   }
   
   //------------------------------------------------------
   // New survey entry and submitting a survey
   //------------------------------------------------------
   else if (0 == strcmp($action,"viewwd")) {
      $vars['wd_id'] = getParameter("wd_id");
      $vars['wd_row_id'] = getParameter("wd_row_id");
      $page = "wd_viewwd.php";
   }
   else if (0 == strcmp($action,"submitdata")) {
      $vars['wd_row_id'] = getParameter("wd_row_id");
      $vars['wd_id'] = getParameter("wd_id");
      $vars['wd_row_id'] = $wdObj->submitSurvey($vars['wd_id'], $vars['wd_row_id'], false);
      $page = getParameter("phpinclude");
      if ($page==null) $page = "wd_listrows.php";
   }
   
   //------------------------------------------------------
   // Re-sequence the entries in the table.  use the existing sequence
   // only start with 10 and update the sequence field by 10's
   //------------------------------------------------------
   else if (0 == strcmp($action,"resequence")) {
      $wd_id = getParameter("wd_id");
      $vars['wd_id'] = $wd_id;
      $wdObj->resequence($wd_id);
      $page = "wd_listrows.php";
   }
   
   else if (0 == strcmp($action,"wd_emailrows")) {
      // sql is sent in - just run it and email all
      $page = "wd_emailrows.php";
   }
   //------------------------------------------------------
   // Update Survey Person
   //------------------------------------------------------
   else if (0 == strcmp($action,"wd_updaterow")) {
         $wd_id = getParameter("wd_id");
         $wd_row_id = getParameter("wd_row_id");
         $wd_row_id_CB = getParameter("wd_row_id_CB");
   
         $complete = getParameter("complete");
         $submit = getParameter("submit");
         $updateAnswer = getParameter("updateAnswer");
         $updateForm = getParameter("updateForm");
   
         if (getParameter("resend") != null && strcmp(getParameter("resend"),"1")==0) {
            $wdObj->sendEmail($wd_id,$wd_row_id);
            if (getParameter("setstatus")!=null) $wdObj->setReplyStatus($wd_id,$wd_row_id,getParameter("setstatus"));
         } else if (getParameter("delete") != null && strcmp(getParameter("delete"),"1")==0) {
            $wdObj->removeRow($wd_id,$wd_row_id);
         } else if (getParameter("updatecomments")==1) {
            $comments = convertString(getParameter("comments"));
            //print "\n<!-- ***chj***\ncomments: ".$comments."\n\n";
            //print "\nwd_id: ".$wd_id;
            //print "\nwd_row_id: ".$wd_row_id;
            //print "\n-->\n";
            $wdObj->updateFieldValue($wd_id,$wd_row_id,"originalwdfield_comments",$comments,FALSE);
            //$wdObj->updateFieldValue($wd_id,$wd_row_id,"comments",convertString(getParameter("comments")),FALSE);
         }
         
         if (is_array($wd_row_id_CB)) {
            for ($i=0; $i<count($wd_row_id_CB); $i++) {
               if (0==strcmp($submit,"Delete selected rows")) {
                  $wdObj->removeRow($wd_id,$wd_row_id_CB[$i]);
               } else if (0==strcmp($submit,"Send email to selected rows")) {
                  $wdObj->sendEmail($wd_id,$wd_row_id_CB[$i]);
               }
            }
         }
   
         if ($complete != null) $wdObj->setReplyStatus($wd_id,$wd_row_id,$complete);
         if ($updateAnswer == 1) $wdObj->setAnswer($wd_id,$wd_row_id,getParameter("field_id"),getParameter("answer"));
         if ($updateForm == 1 && 0==strcmp($submit,"Delete Selected Rows")) {
            $del_wd_row_id = getParameter("del_wd_row_id");
            if (is_array($del_wd_row_id)) {
               for ($i=0; $i<count($del_wd_row_id); $i++) $wdObj->removeRow($wd_id,$del_wd_row_id[$i]);
            }
         }
         if ($updateForm == 1 && 0==strcmp($submit,"Save Values Above")) $wdObj->submitMultipleSurveys();
   
         $page = getParameter("page");
         if ($page==NULL) {
            $webdata = $wdObj->getWebData($wd_id);
            $page = "wd_listrows.php";
            //if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrows.php";
            if ($webdata['privatesrvy']<3) $page = "wd_listsurveyrowsopti.php";
            if ($webdata['privatesrvy']>99) {
               $customCode = new AdminUI();
               $temppage = $customCode->getCustomSurveyPage($webdata['privatesrvy']);
               if ($temppage!=NULL) $page = $temppage;
            }
         }
   }
   //------------------------------------------------------
   // Upload a csv of companies
   //------------------------------------------------------
   else if (0==strcmp($action,"uploadSurveyCSV")) {
        $wd_id = getParameter("survey_id");
   
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
           $count = 0;
           $filename = $GLOBALS['rootDir'].$GLOBALS['csvuploadDir']."srvy_".$wd_id."_".$count."_".$_FILES['userfile']['name']; 
           while (file_exists($filename)) {
              $count ++;
              $filename = $GLOBALS['rootDir'].$GLOBALS['csvuploadDir']."srvy_".$wd_id."_".$count."_".$_FILES['userfile']['name']; 
           }
           if (copy($_FILES['userfile']['tmp_name'],$filename))
           {
                $contents = $template->getFileWithoutSub($filename,FALSE);
                $wdObj->loadContents($wd_id,$contents);
                $vars['msg'] .= "CSV ".$_FILES['userfile']['name']." uploaded successfully.<BR>";
           }
           else $vars['error'] .= "Upload failed.  Please make sure the file is not too large and exists on your system.";
        }
        else $vars['error'] .= "Upload failed.";
   
        $page = "listemails.php";
   }
   
   //------------------------------------------------------
   // Download a csv of recipients
   //------------------------------------------------------
   else if (0==strcmp($action,"dlcsvwd")) {
   //print "***";
      ini_set('memory_limit', '128M');
      $wd_id = getParameter("wd_id");
      $qids = getParameter("qids");
      $entire_file = $wdObj->getOutputCSVOptions($wd_id,$qids);
      $output_file_name = 'surveyresponses.csv'; 
      $template->saveFile($output_file_name,$entire_file);
      $page = "download.php";
   }
   
   //**************************************************************************************************************************************
   //**************************************************************************************************************************************
   
   
   
   //------------------------------------------------------
   // eCommerce Action
   //------------------------------------------------------
   else if (0==strcmp(substr($action,0,3),"ec_")) {
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),10)) {
         $ecUI = new EcommerceAdminUI();
         $result = $ecUI->controller();
         $vars = array_merge($vars,$result['vars']);
         $page = $result['page'];
      } else {
         $vars['error'] .= "You do not have the authority to access E-Commerce, contact your administrator.<BR>";	  
      }
   }
   
   
   //------------------------------------------------------
   // Custom Action
   //------------------------------------------------------
   else if (0==strcmp($action,"custom")) {
      ini_set('memory_limit', '128M');
      $page="dashboard.php";
      if ($ua->doesUserHaveAccessToLevel(isLoggedOn(),9) || $ua->doesUserHaveAccessToLevel(isLoggedOn(),2)) {
         $vars['source'] = "admin";
         $customCode = new AdminUI();
         $result = $customCode->controller();
            //print "<br><br>VARS: ";
            //print_r ($vars);
            //print "<br><br>custom VARS: ";
            //print_r ($result['vars']);
         $vars = array_merge($vars,$result['vars']);
         $page = $result['page'];
      } else {
         $vars['error'] .= "You do not have the authority to access this menu option, contact your administrator.<BR>";	  
      }
   }
   
   
   //------------------------------------------------------
   // error
   //------------------------------------------------------
   else if ($action != null) {
      //print "Action: ".$action."<BR>";
      $page = "error.php";
   }
   
   
   
   
   // ======================================================
   // === Display the page
   // === 
   // === 
   // ======================================================
   
   //print "\n<!-- page: ".$page." -->\n";
   if ($vars['redirect']==1) {
      //print "\n<!-- redirect -->\n";
      header("Location: ".$page);
   } else if (getParameter("noMenu")==1) {
      //print "\n<!-- nomenu -->\n";
      //print "<HTML><BODY>";
      //print "<font color=\"red\">".$vars['error']."</font><font color=\"green\">".$vars['msg']."</font>";
      include $page;
      //print "</BODY></html>";
   } else if (getParameter("noPrint")==1) {
      //print "\n<!-- noprint -->\n";
      include $page;
   } else {
      //print "\n<!-- regular -->\n";
      include $toppage;
      if($pagejs != NULL) print "\n".$pagejs."\n";
      //print "\n\n<!-- page: ".$page." -->\n";
      include $page;
      //print "\n<!-- end page: ".$page." -->\n\n";
      include $bottompage;
   }
}
?>