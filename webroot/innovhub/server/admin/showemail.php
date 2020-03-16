<?php
   $template = new Template();
   $ua = new UserAcct();
   $scheduler = new Scheduler();
   $semailid = getParameter("semailid");
//print "<!-- ***chj*** start -->\n";
   if ($semailid!=NULL) {
//print "<!-- ***chj*** semailid provided -->\n";
      $results = $scheduler->getScheduledEmails($semailid);
      if ($results['totalJobs']==1) {
//print "<!-- ***chj*** totaljobs = 1 -->\n";
         $version = new Version();
         $value = $results['emails'][0];
         $subject = "";
         $contents = "";
         if ($value['cmsid'] != NULL && $value['cmsid']>0) {
//print "<!-- ***chj*** cmsid -->\n";
            $cmsinfo = $version->getFileById($value['cmsid']);
            $fileinfo = $version->getAsciiFileContents($cmsinfo['filename']);
            $contents = $fileinfo['contents'];
            $subject = $fileinfo['metadescr'];
//print "<!-- ***chj*** subject1: ".$subject." -->\n";
            if ($subject==NULL) $subject = $fileinfo['title'];
//print "<!-- ***chj*** subject2: ".$subject." -->\n";
            if ($subject==NULL) $subject = $fileinfo['filetitle'];
//print "<!-- ***chj*** subject3: ".$subject." -->\n";
         } else {
            $contents = $value['content'];
            $subject = $value['subject'];
         }
   
         $namesArr = explode(",",$value['field4']);
         $valuesArr = explode(",",$value['field5']);
         for ($i=0; $i<count($namesArr); $i++) $_SESSION['params'][$namesArr[$i]] = $valuesArr[$i];
         $touser = $ua->getFullUserInfo($value['userid']);
         $emailtext = $ua->doSubstitutions($contents,$touser);
         $emailtext = $template->doSubstitutions($emailtext);
         $subjecttext = $ua->doSubstitutions($subject,$touser);
         $subjecttext = $template->doSubstitutions($subjecttext);
         print "Subject: ".$subjecttext."<br><br>".$emailtext;
      }
   }
?>
