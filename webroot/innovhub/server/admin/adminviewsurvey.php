<?php 
   if ($vars['error']!= null) print "<font size=\"+1\" color=\"red\"><b>".$vars['error']."</b></font>";
   if ($vars['msg']!= null) print "<font size=\"+1\" color=\"grey\"><b>".$vars['msg']."</b></font>";

   
   $surveyOBJ = new Survey();
   if ($vars['survey_id'] == NULL) {
      $sci = $surveyOBJ->getDetails($vars['srvy_person_id']);
      $vars['survey_id'] = $sci['survey_id'];
   }
   print "<span class=\"button01\"><a href=\"".$GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php?action=listemails&survey_id=".$vars['survey_id']."\">Cancel, Return to data table...</a></span><br><br>";

   $surveyOBJ->printSurvey(NULL, $vars['srvy_person_id'], $vars['survey_id'], NULL, NULL, NULL, $GLOBALS['baseURLSSL'].$GLOBALS['adminFolder']."admincontroller.php");
?>
