<?php
//error_reporting(E_ALL);
   $template = new Template;
   $ss = new Version();

   $results = $ss->getViewShortcut($vars['view']);
   if (!$ss->checkIfUserCanAccess($results['privacy'],isLoggedOn())) {
      $results = $ss->getViewShortcut($ss->getValue("noaccesstemplate"));
   }

   if ($results['title'] != null) $title = $results['title'];
   $sub['url'] = $results['url'];
   $sub['view'] = $results['filename'];
   $sub['metaKW'] = $results['metakw'];
   $sub['metaDESCR'] = $results['metadescr'];

   
   if ($title!=NULL) $sub['title'] = $title;
   $template->displayCacheFile($results,$title,$sub);
?>
